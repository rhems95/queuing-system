# PECIT Staff Float — always-on-top system window
# Opens a small browser app window (if needed), pins it on top, and keeps it topmost.

param(
    [string]$Url = "http://localhost/queue-system/public/window/float",
    [ValidateSet("chrome", "edge")]
    [string]$Browser = "chrome"
)

$ErrorActionPreference = "Stop"

if ($Browser -eq "edge") {
    $browserCandidates = @(
        "${env:ProgramFiles(x86)}\Microsoft\Edge\Application\msedge.exe",
        "$env:ProgramFiles\Microsoft\Edge\Application\msedge.exe"
    )
    $profileName = "PECIT-Staff-Float-Edge"
    $browserLabel = "Microsoft Edge"
} else {
    $browserCandidates = @(
        "$env:ProgramFiles\Google\Chrome\Application\chrome.exe",
        "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe",
        "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe"
    )
    $profileName = "PECIT-Staff-Float-Chrome"
    $browserLabel = "Google Chrome"
}

$browserExe = $browserCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1

if (-not $browserExe) {
    Write-Host "$browserLabel not found. Install it or edit this script." -ForegroundColor Red
    exit 1
}

$profileDir = Join-Path $env:LOCALAPPDATA $profileName
New-Item -ItemType Directory -Force -Path $profileDir | Out-Null

$windowWidth = 260
$windowHeight = 290

Add-Type @"
using System;
using System.Text;
using System.Collections.Generic;
using System.Runtime.InteropServices;
public class PecitWin {
    public delegate bool EnumProc(IntPtr hWnd, IntPtr lParam);
    [DllImport("user32.dll")] public static extern bool EnumWindows(EnumProc lpEnumFunc, IntPtr lParam);
    [DllImport("user32.dll")] public static extern int GetWindowText(IntPtr hWnd, StringBuilder lpString, int nMaxCount);
    [DllImport("user32.dll")] public static extern bool IsWindowVisible(IntPtr hWnd);
    [DllImport("user32.dll")] public static extern bool SetWindowPos(IntPtr hWnd, IntPtr hWndInsertAfter, int X, int Y, int cx, int cy, uint uFlags);
    public static readonly IntPtr HWND_TOPMOST = new IntPtr(-1);
    public const uint SWP_NOMOVE = 0x0002;
    public const uint SWP_NOSIZE = 0x0001;
    public const uint SWP_SHOWWINDOW = 0x0040;
}
"@

function Get-CandidateWindows {
    $list = New-Object System.Collections.Generic.List[IntPtr]
    $callback = [PecitWin+EnumProc]{
        param([IntPtr]$hWnd, [IntPtr]$lParam)
        if (-not [PecitWin]::IsWindowVisible($hWnd)) { return $true }
        $sb = New-Object System.Text.StringBuilder 512
        [void][PecitWin]::GetWindowText($hWnd, $sb, $sb.Capacity)
        $title = $sb.ToString()
        if ($title -like "*PECIT Staff Float*") {
            $list.Add($hWnd) | Out-Null
        }
        return $true
    }
    [void][PecitWin]::EnumWindows($callback, [IntPtr]::Zero)
    return $list
}

function Set-TopMost([IntPtr]$hwnd, [bool]$forceSize) {
    if ($hwnd -eq [IntPtr]::Zero) { return }
    if ($forceSize) {
        [void][PecitWin]::SetWindowPos(
            $hwnd,
            [PecitWin]::HWND_TOPMOST,
            40, 80, $windowWidth, $windowHeight,
            [PecitWin]::SWP_SHOWWINDOW
        )
        return
    }
    [void][PecitWin]::SetWindowPos(
        $hwnd,
        [PecitWin]::HWND_TOPMOST,
        0, 0, 0, 0,
        ([PecitWin]::SWP_NOMOVE -bor [PecitWin]::SWP_NOSIZE -bor [PecitWin]::SWP_SHOWWINDOW)
    )
}

function Start-FloatBrowser {
    Start-Process -FilePath $browserExe -ArgumentList @(
        "--user-data-dir=`"$profileDir`"",
        "--app=$Url",
        "--window-size=$windowWidth,$windowHeight",
        "--window-position=40,80",
        "--disable-extensions",
        "--disable-infobars",
        "--hide-crash-restore-bubble",
        "--no-first-run",
        "--no-default-browser-check"
    )
}

$existing = Get-CandidateWindows
$launchedBrowser = $false
if ($existing.Count -eq 0) {
    Start-FloatBrowser
    $launchedBrowser = $true
}

# Pin once we see the window, then keep re-applying TopMost so it stays above other apps.
$deadline = (Get-Date).AddHours(12)
$firstPin = $true
$seen = $false
while ((Get-Date) -lt $deadline) {
    $windows = Get-CandidateWindows
    if ($windows.Count -gt 0) {
        $seen = $true
        foreach ($hwnd in $windows) {
            Set-TopMost $hwnd $firstPin
        }
        $firstPin = $false
    } elseif ($seen) {
        # Staff closed the float window.
        break
    } elseif (-not $launchedBrowser) {
        Start-FloatBrowser
        $launchedBrowser = $true
    }
    Start-Sleep -Milliseconds 1500
}

exit 0
