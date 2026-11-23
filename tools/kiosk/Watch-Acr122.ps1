# Poll ACS ACR122U (PC/SC) and write the last card UID for the kiosk.
param(
    [string]$OutFile = "",
    [int]$IntervalMs = 350
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
    $OutFile = Join-Path $PSScriptRoot "..\..\storage\app\kiosk-card.json"
}
$OutFile = [System.IO.Path]::GetFullPath($OutFile)
$dir = Split-Path $OutFile -Parent
if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir | Out-Null }

Add-Type -TypeDefinition @"
using System;
using System.Runtime.InteropServices;

public static class PecitWinSCard {
    [DllImport("winscard.dll")]
    public static extern int SCardEstablishContext(int dwScope, IntPtr r1, IntPtr r2, out IntPtr ctx);

    [DllImport("winscard.dll")]
    public static extern int SCardReleaseContext(IntPtr ctx);

    [DllImport("winscard.dll", CharSet = CharSet.Unicode)]
    public static extern int SCardListReaders(IntPtr ctx, string groups, char[] readers, ref int size);

    [DllImport("winscard.dll", CharSet = CharSet.Unicode)]
    public static extern int SCardConnect(IntPtr ctx, string reader, int share, int proto, out IntPtr card, out int active);

    [DllImport("winscard.dll")]
    public static extern int SCardDisconnect(IntPtr card, int disp);

    [DllImport("winscard.dll")]
    public static extern int SCardTransmit(IntPtr card, IntPtr sendPci, byte[] send, int sendLen, IntPtr recvPci, byte[] recv, ref int recvLen);

    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Unicode)]
    public struct ReaderState {
        [MarshalAs(UnmanagedType.LPWStr)]
        public string szReader;
        public IntPtr pvUserData;
        public uint dwCurrentState;
        public uint dwEventState;
        public uint cbAtr;
        [MarshalAs(UnmanagedType.ByValArray, SizeConst = 36)]
        public byte[] rgbAtr;
    }

    [DllImport("winscard.dll", CharSet = CharSet.Unicode)]
    public static extern int SCardGetStatusChange(IntPtr ctx, int timeout, ref ReaderState state, int count);

    public const int Timeout = unchecked((int)0x8010000A);
    public const uint StatePresent = 0x20;

    public static int Poll(IntPtr ctx, string reader, int timeoutMs, uint current, out uint nextCurrent, out bool present) {
        ReaderState st = new ReaderState();
        st.szReader = reader;
        st.dwCurrentState = current;
        st.rgbAtr = new byte[36];
        int rc = SCardGetStatusChange(ctx, timeoutMs, ref st, 1);
        if (rc != 0) {
            nextCurrent = current;
            present = false;
            return rc;
        }
        nextCurrent = st.dwEventState & ~2u;
        present = (st.dwEventState & StatePresent) != 0;
        return 0;
    }

    [DllImport("kernel32.dll", CharSet = CharSet.Ansi)]
    public static extern IntPtr GetProcAddress(IntPtr h, string name);

    [DllImport("kernel32.dll", CharSet = CharSet.Unicode)]
    public static extern IntPtr GetModuleHandle(string name);

    public static IntPtr PciT0() {
        IntPtr h = GetModuleHandle("winscard.dll");
        return h == IntPtr.Zero ? IntPtr.Zero : GetProcAddress(h, "g_rgSCardT0Pci");
    }

    public static IntPtr PciT1() {
        IntPtr h = GetModuleHandle("winscard.dll");
        return h == IntPtr.Zero ? IntPtr.Zero : GetProcAddress(h, "g_rgSCardT1Pci");
    }
}
"@

function Get-Readers([IntPtr]$ctx) {
    $size = 0
    [void][PecitWinSCard]::SCardListReaders($ctx, $null, $null, [ref]$size)
    if ($size -le 2) { return @() }
    $chars = New-Object char[] $size
    $rc = [PecitWinSCard]::SCardListReaders($ctx, $null, $chars, [ref]$size)
    if ($rc -ne 0) { return @() }
    $raw = -join $chars
    return @($raw.Split([char]0) | Where-Object { $_ -and $_.Trim() -ne "" })
}

function Get-Uid([IntPtr]$ctx, [string]$reader) {
    $card = [IntPtr]::Zero
    $proto = 0
    $rc = [PecitWinSCard]::SCardConnect($ctx, $reader, 2, 3, [ref]$card, [ref]$proto)
    if ($rc -ne 0 -or $card -eq [IntPtr]::Zero) { return $null }
    try {
        $pci = if ($proto -eq 2) { [PecitWinSCard]::PciT1() } else { [PecitWinSCard]::PciT0() }
        if ($pci -eq [IntPtr]::Zero) { $pci = [PecitWinSCard]::PciT0() }
        $send = [byte[]](0xFF, 0xCA, 0x00, 0x00, 0x00)
        $recv = New-Object byte[] 32
        $recvLen = $recv.Length
        $trc = [PecitWinSCard]::SCardTransmit($card, $pci, $send, $send.Length, [IntPtr]::Zero, $recv, [ref]$recvLen)
        if ($trc -ne 0 -or $recvLen -lt 3) { return $null }
        $sw1 = $recv[$recvLen - 2]
        $sw2 = $recv[$recvLen - 1]
        if ($sw1 -ne 0x90 -or $sw2 -ne 0x00) { return $null }
        $n = $recvLen - 3
        if ($n -lt 2) { return $null }
        return -join ($recv[0..$n] | ForEach-Object { $_.ToString("X2") })
    } finally {
        [void][PecitWinSCard]::SCardDisconnect($card, 0)
    }
}

function Write-Tap([string]$uid) {
    $payload = @{
        uid = $uid
        seen_at = (Get-Date).ToString("o")
    } | ConvertTo-Json -Compress
    $tmp = "$OutFile.tmp"
    [System.IO.File]::WriteAllText($tmp, $payload)
    Move-Item -LiteralPath $tmp -Destination $OutFile -Force
}

function Pick-Reader([IntPtr]$ctx) {
    $readers = @(Get-Readers $ctx)
    foreach ($name in $readers) {
        if ($name -match 'ACR122|ACS ') { return $name }
    }
    if ($readers.Count -gt 0) { return $readers[0] }
    return $null
}

Write-Host "ACR122 watcher -> $OutFile"
while ($true) {
    $ctx = [IntPtr]::Zero
    try {
        $rc = [PecitWinSCard]::SCardEstablishContext(2, [IntPtr]::Zero, [IntPtr]::Zero, [ref]$ctx)
        if ($rc -ne 0 -or $ctx -eq [IntPtr]::Zero) {
            Start-Sleep -Milliseconds 800
            continue
        }

        $reader = Pick-Reader $ctx
        if (-not $reader) {
            [void][PecitWinSCard]::SCardReleaseContext($ctx)
            $ctx = [IntPtr]::Zero
            Start-Sleep -Milliseconds 800
            continue
        }

        $current = [uint32]0
        $wasPresent = $false
        while ($true) {
            $next = [uint32]0
            $isPresent = $false
            $prc = [PecitWinSCard]::Poll($ctx, $reader, 400, $current, [ref]$next, [ref]$isPresent)
            if ($prc -eq [PecitWinSCard]::Timeout) {
                continue
            }
            if ($prc -ne 0) { break }

            $current = $next
            if ($isPresent -and -not $wasPresent) {
                $uid = $null
                foreach ($try in 1..8) {
                    Start-Sleep -Milliseconds 70
                    $uid = Get-Uid $ctx $reader
                    if ($uid) { break }
                }
                if ($uid) {
                    Write-Tap $uid
                    Write-Host "tap $uid"
                }
            }
            $wasPresent = $isPresent
        }
    } catch {
        Start-Sleep -Milliseconds 800
    } finally {
        if ($ctx -ne [IntPtr]::Zero) { [void][PecitWinSCard]::SCardReleaseContext($ctx) }
    }
}
