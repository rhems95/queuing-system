# Poll the XP-Q90EC paper sensor and write storage/app/kiosk-printer.json for staff alerts.
param(
    [string]$OutFile = "",
    [int]$IntervalMs = 1500
)

$ErrorActionPreference = "Continue"

if (-not ("Pecit.HideConsole" -as [type])) {
    Add-Type -Namespace Pecit -Name HideConsole -MemberDefinition @"
        [DllImport("kernel32.dll")] public static extern IntPtr GetConsoleWindow();
        [DllImport("user32.dll")] public static extern bool ShowWindow(IntPtr hWnd, int nCmdShow);
"@
}
$console = [Pecit.HideConsole]::GetConsoleWindow()
if ($console -ne [IntPtr]::Zero) {
    [void][Pecit.HideConsole]::ShowWindow($console, 0)
}

if (-not $OutFile) {
    $OutFile = Join-Path $PSScriptRoot "..\..\storage\app\kiosk-printer.json"
}
$OutFile = [System.IO.Path]::GetFullPath($OutFile)
$dir = Split-Path $OutFile -Parent
if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir | Out-Null }

Add-Type -TypeDefinition @"
using System;
using System.Runtime.InteropServices;
using System.Threading;

public static class PecitPrinterPaper {
    [StructLayout(LayoutKind.Sequential)]
    public struct DevIf {
        public int cbSize;
        public Guid InterfaceClassGuid;
        public int Flags;
        public IntPtr Reserved;
    }

    [DllImport("setupapi.dll", CharSet = CharSet.Unicode, SetLastError = true)]
    public static extern IntPtr SetupDiGetClassDevs(ref Guid classGuid, string enumerator, IntPtr parent, uint flags);
    [DllImport("setupapi.dll", CharSet = CharSet.Unicode, SetLastError = true)]
    public static extern bool SetupDiEnumDeviceInterfaces(IntPtr set, IntPtr dev, ref Guid classGuid, uint index, ref DevIf data);
    [DllImport("setupapi.dll", CharSet = CharSet.Unicode, SetLastError = true)]
    public static extern bool SetupDiGetDeviceInterfaceDetail(IntPtr set, ref DevIf data, IntPtr detail, uint size, out uint required, IntPtr devInfo);
    [DllImport("setupapi.dll")]
    public static extern bool SetupDiDestroyDeviceInfoList(IntPtr set);
    [StructLayout(LayoutKind.Sequential)]
    public struct OVERLAPPED {
        public IntPtr Internal;
        public IntPtr InternalHigh;
        public uint Offset;
        public uint OffsetHigh;
        public IntPtr hEvent;
    }

    [DllImport("kernel32.dll", CharSet = CharSet.Unicode, SetLastError = true)]
    public static extern IntPtr CreateFile(string name, uint access, uint share, IntPtr sec, uint disp, uint flags, IntPtr template);
    [DllImport("kernel32.dll", SetLastError = true, EntryPoint = "WriteFile")]
    public static extern bool WriteFileSimple(IntPtr h, byte[] buf, uint n, out uint written, IntPtr ov);
    [DllImport("kernel32.dll", SetLastError = true, EntryPoint = "ReadFile")]
    public static extern bool ReadFileSimple(IntPtr h, byte[] buf, uint n, out uint read, IntPtr ov);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool WriteFile(IntPtr h, byte[] buf, uint n, out uint written, ref OVERLAPPED ov);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool ReadFile(IntPtr h, byte[] buf, uint n, out uint read, ref OVERLAPPED ov);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool GetOverlappedResult(IntPtr h, ref OVERLAPPED ov, out uint transferred, bool wait);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool CloseHandle(IntPtr h);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool CancelIoEx(IntPtr h, IntPtr ov);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern IntPtr CreateEvent(IntPtr sec, bool manual, bool initial, string name);
    [DllImport("kernel32.dll", SetLastError = true)]
    public static extern bool ResetEvent(IntPtr h);
    [DllImport("kernel32.dll")]
    public static extern uint WaitForSingleObject(IntPtr h, uint ms);

    static string DevicePath() {
        Guid g = new Guid("28d78fad-5a12-11d1-ae5b-0000f803a8c2");
        IntPtr set = SetupDiGetClassDevs(ref g, null, IntPtr.Zero, 0x12);
        if (set == IntPtr.Zero || set == new IntPtr(-1)) return null;
        try {
            DevIf data = new DevIf();
            data.cbSize = Marshal.SizeOf(typeof(DevIf));
            if (!SetupDiEnumDeviceInterfaces(set, IntPtr.Zero, ref g, 0, ref data)) return null;
            uint needed;
            SetupDiGetDeviceInterfaceDetail(set, ref data, IntPtr.Zero, 0, out needed, IntPtr.Zero);
            if (needed < 8) return null;
            IntPtr detail = Marshal.AllocHGlobal((int)needed);
            try {
                Marshal.WriteInt32(detail, IntPtr.Size == 8 ? 8 : 6);
                uint needed2;
                if (!SetupDiGetDeviceInterfaceDetail(set, ref data, detail, needed, out needed2, IntPtr.Zero)) return null;
                return Marshal.PtrToStringUni(new IntPtr(detail.ToInt64() + 4));
            } finally {
                Marshal.FreeHGlobal(detail);
            }
        } finally {
            SetupDiDestroyDeviceInfoList(set);
        }
    }

    static bool FinishIo(IntPtr h, ref OVERLAPPED ov, IntPtr ev, bool started, int winErr, ref uint transferred, int timeoutMs) {
        if (started) return transferred > 0;
        if (winErr != 997) return false;
        if (WaitForSingleObject(ev, (uint)timeoutMs) != 0) {
            CancelIoEx(h, IntPtr.Zero);
            WaitForSingleObject(ev, 200);
            return false;
        }
        return GetOverlappedResult(h, ref ov, out transferred, false);
    }

    // DLE EOT 4. Returns -1 if the sensor did not answer. Otherwise the status byte.
    // Paper end is bits 5 and 6 (0x60). With paper loaded the XP-Q90EC returns 0x12.
    // A print job can leave the status read stuck; cancel it and do not wait forever.
    public static int ReadPaperByte() {
        string path = DevicePath();
        if (string.IsNullOrEmpty(path)) return -1;
        uint access = 0x80000000u | 0x40000000u;
        IntPtr h = CreateFile(path, access, 3, IntPtr.Zero, 3, 0, IntPtr.Zero);
        if (h == IntPtr.Zero || h == new IntPtr(-1)) return -1;
        bool stuck = false;
        try {
            byte[] cmd = new byte[] { 0x10, 0x04, 0x04 };
            uint written;
            if (!WriteFileSimple(h, cmd, 3, out written, IntPtr.Zero) || written != 3) return -1;
            byte[] buf = new byte[8];
            uint read = 0;
            bool ok = false;
            var t = new Thread(() => { ok = ReadFileSimple(h, buf, 8, out read, IntPtr.Zero); });
            t.IsBackground = true;
            t.Start();
            if (!t.Join(800)) {
                CancelIoEx(h, IntPtr.Zero);
                if (!t.Join(400)) {
                    stuck = true;
                    return -1;
                }
            }
            if (!ok || read < 1) return -1;
            return buf[0];
        } finally {
            if (!stuck && h != IntPtr.Zero && h != new IntPtr(-1)) CloseHandle(h);
        }
    }
}
"@

function Test-StuckTicket {
    # A ticket that stays "Printing" is how this XP-Q90EC reports no paper.
    # Windows does not set DetectedErrorState, and the USB port is busy while the job is held.
    $cutoff = (Get-Date).AddSeconds(-12)
    $names = @("Xprinter XP-58(XP-Q90EC)")
    try {
        $jobs = @(Get-CimInstance -ClassName Win32_PrintJob -ErrorAction Stop)
    } catch {
        $jobs = @()
    }
    foreach ($job in $jobs) {
        $name = [string]$job.Name
        $mine = $false
        foreach ($printer in $names) {
            if ($name.StartsWith($printer)) { $mine = $true }
        }
        if (-not $mine) { continue }
        $status = [string]$job.JobStatus
        $submitted = $job.TimeSubmitted
        if ($submitted -and $submitted -lt $cutoff -and $status -match 'Printing|Error|Paper|Blocked|Restart|Retained') {
            return $true
        }
    }
    return $false
}

function Write-Paper([bool]$paperOut, [int]$status) {
    $payload = @{
        paper_out = $paperOut
        status = $status
        seen_at = (Get-Date).ToString("o")
    } | ConvertTo-Json -Compress
    $tmp = "$OutFile.tmp"
    [System.IO.File]::WriteAllText($tmp, $payload)
    Move-Item -LiteralPath $tmp -Destination $OutFile -Force
}

$paperOut = $false
if (Test-Path $OutFile) {
    try {
        $prev = Get-Content -LiteralPath $OutFile -Raw | ConvertFrom-Json
        if ($prev.paper_out -eq $true) { $paperOut = $true }
    } catch {}
}
while ($true) {
    # Check the spooler before the USB sensor. A retained ticket owns the port,
    # and a sensor read then blocks forever and never updates the staff alert.
    if (Test-StuckTicket) {
        $paperOut = $true
        Write-Paper $true -2
        Start-Sleep -Milliseconds $IntervalMs
        continue
    }
    $byte = [PecitPrinterPaper]::ReadPaperByte()
    if ($byte -ge 0) {
        $paperOut = (($byte -band 0x60) -eq 0x60)
    }
    Write-Paper $paperOut $byte
    Start-Sleep -Milliseconds $IntervalMs
}
