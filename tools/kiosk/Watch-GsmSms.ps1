# Send queued PECIT ticket texts through a SIM7600-style GSM modem.
param(
    [string]$OutDir = "",
    [int]$IntervalMs = 1500
)

$ErrorActionPreference = "Continue"

if (-not ("Pecit.HideConsoleGsm" -as [type])) {
    Add-Type -Namespace Pecit -Name HideConsoleGsm -MemberDefinition @"
        [DllImport("kernel32.dll")] public static extern IntPtr GetConsoleWindow();
        [DllImport("user32.dll")] public static extern bool ShowWindow(IntPtr hWnd, int nCmdShow);
"@
}
$console = [Pecit.HideConsoleGsm]::GetConsoleWindow()
if ($console -ne [IntPtr]::Zero) {
    [void][Pecit.HideConsoleGsm]::ShowWindow($console, 0)
}

if (-not $OutDir) {
    $OutDir = Join-Path $PSScriptRoot "..\..\storage\app\sms-outbox"
}
$OutDir = [System.IO.Path]::GetFullPath($OutDir)
if (-not (Test-Path $OutDir)) { New-Item -ItemType Directory -Path $OutDir | Out-Null }
$sentDir = Join-Path (Split-Path $OutDir -Parent) "sms-sent"
if (-not (Test-Path $sentDir)) { New-Item -ItemType Directory -Path $sentDir | Out-Null }
$statusFile = Join-Path (Split-Path $OutDir -Parent) "kiosk-gsm.json"

$script:PortName = $null

function Write-GsmStatus([string]$state, [string]$detail) {
    $payload = @{
        state = $state
        detail = $detail
        port = $script:PortName
        seen_at = (Get-Date).ToString("o")
    } | ConvertTo-Json -Compress
    [System.IO.File]::WriteAllText($statusFile, $payload)
}

function Read-AtReply($port, [int]$timeoutMs) {
    $sw = [System.Diagnostics.Stopwatch]::StartNew()
    $buf = ""
    while ($sw.ElapsedMilliseconds -lt $timeoutMs) {
        $buf += $port.ReadExisting()
        if ($buf -match 'OK|ERROR|>') { break }
        Start-Sleep -Milliseconds 60
    }
    return $buf
}

function Send-At($port, [string]$cmd, [int]$timeoutMs) {
    $port.Write($cmd + "`r")
    return Read-AtReply $port $timeoutMs
}

function Test-GsmPort([string]$name) {
    foreach ($baud in @(115200, 9600)) {
        $port = $null
        try {
            $port = New-Object System.IO.Ports.SerialPort $name, $baud, 'None', 8, 'One'
            $port.NewLine = "`r"
            $port.ReadTimeout = 400
            $port.WriteTimeout = 1500
            $port.DtrEnable = $true
            $port.RtsEnable = $true
            $port.Open()
            Start-Sleep -Milliseconds 200
            $port.DiscardInBuffer()
            $reply = Send-At $port "AT" 1200
            if ($reply -match 'OK') {
                $port.Close()
                return $true
            }
        } catch {
        } finally {
            if ($port -and $port.IsOpen) { $port.Close() }
            if ($port) { $port.Dispose() }
        }
    }
    return $false
}

function Get-PortOrder {
    $names = @()
    try { $names = @([System.IO.Ports.SerialPort]::GetPortNames()) } catch { $names = @() }
    $names = @($names | Select-Object -Unique)
    $caption = @{}
    try {
        Get-CimInstance Win32_PnPEntity -ErrorAction SilentlyContinue | ForEach-Object {
            if ($_.Name -match '\((COM\d+)\)') { $caption[$Matches[1]] = $_.Name }
        }
    } catch {}
    $at = @(); $other = @()
    foreach ($name in $names) {
        $label = [string]$caption[$name]
        if ($label -match 'Audio|NMEA|Diagnostic') { continue }
        if ($label -match 'AT') { $at += $name } else { $other += $name }
    }
    return @($at + $other)
}

function Find-GsmPort {
    foreach ($name in (Get-PortOrder)) {
        if (Test-GsmPort $name) { return $name }
    }
    return $null
}

function Send-GsmText([string]$portName, [string]$number, [string]$text) {
    $port = $null
    try {
        $port = New-Object System.IO.Ports.SerialPort $portName, 115200, 'None', 8, 'One'
        $port.ReadTimeout = 500
        $port.WriteTimeout = 2000
        $port.DtrEnable = $true
        $port.RtsEnable = $true
        $port.Open()
        Start-Sleep -Milliseconds 150
        $hello = Send-At $port "AT" 1500
        if ($hello -notmatch 'OK') {
            $port.Close()
            $port.Dispose()
            $port = New-Object System.IO.Ports.SerialPort $portName, 9600, 'None', 8, 'One'
            $port.ReadTimeout = 500
            $port.WriteTimeout = 2000
            $port.DtrEnable = $true
            $port.RtsEnable = $true
            $port.Open()
            $hello = Send-At $port "AT" 1500
            if ($hello -notmatch 'OK') { return $false }
        }
        $mode = Send-At $port "AT+CMGF=1" 2000
        if ($mode -notmatch 'OK') { return $false }
        [void](Send-At $port 'AT+CSCS="GSM"' 1500)
        $port.DiscardInBuffer()
        $port.Write("AT+CMGS=`"$number`"`r")
        $prompt = Read-AtReply $port 8000
        if ($prompt -notmatch '>') { return $false }
        $port.Write($text)
        $port.Write([char]26)
        $done = Read-AtReply $port 30000
        return ($done -match '\+CMGS:' -and $done -notmatch 'ERROR')
    } catch {
        return $false
    } finally {
        if ($port -and $port.IsOpen) { $port.Close() }
        if ($port) { $port.Dispose() }
    }
}

Write-GsmStatus "starting" "Looking for the GSM modem"
while ($true) {
    if (-not $script:PortName -or -not (Test-GsmPort $script:PortName)) {
        $script:PortName = Find-GsmPort
    }
    $jobs = @(Get-ChildItem -LiteralPath $OutDir -Filter "*.json" -File -ErrorAction SilentlyContinue)
    if (-not $script:PortName) {
        Write-GsmStatus "no-port" ("Waiting for the modem. Queued texts: " + $jobs.Count)
    } else {
        Write-GsmStatus "ready" ("Modem on " + $script:PortName)
        foreach ($job in $jobs) {
            try {
                $item = Get-Content -LiteralPath $job.FullName -Raw | ConvertFrom-Json
            } catch {
                continue
            }
            $ok = Send-GsmText $script:PortName ([string]$item.to) ([string]$item.text)
            if ($ok) {
                $dest = Join-Path $sentDir $job.Name
                Move-Item -LiteralPath $job.FullName -Destination $dest -Force
                Write-GsmStatus "sent" ("Sent to " + $item.to)
            } else {
                Write-GsmStatus "send-failed" ("Could not send to " + $item.to)
                $script:PortName = $null
                break
            }
        }
    }
    Start-Sleep -Milliseconds $IntervalMs
}
