# PECIT Staff Float — always-on-top system window
# Opens a small browser app window, pins it on top, then exits.

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
$windowHeight = 270

Start-Process -FilePath $browserExe -ArgumentList @(
    "--user-data-dir=`"$profileDir`"",
    "--app=$Url",
    "--window-size=$windowWidth,$windowHeight",
    "--window-position=40,80",
    "--disable-extensions",
    "--no-first-run"
)

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
        if (
            $title -like "*PECIT Staff Float*" -or
            $title -like "*PECIT Queuing System*" -or
            $title -eq "Login"
        ) {
            $list.Add($hWnd) | Out-Null
        }
        return $true
    }
    [void][PecitWin]::EnumWindows($callback, [IntPtr]::Zero)
    return $list
}

function Set-TopMost([IntPtr]$hwnd) {
    if ($hwnd -eq [IntPtr]::Zero) { return }
    # Force size in case the browser restored a previous window size from the profile.
    [void][PecitWin]::SetWindowPos(
        $hwnd,
        [PecitWin]::HWND_TOPMOST,
        40, 80, $windowWidth, $windowHeight,
        [PecitWin]::SWP_SHOWWINDOW
    )
}

# Wait briefly for the float window, pin once, then exit.
$pinned = $false
for ($i = 0; $i -lt 25; $i++) {
    Start-Sleep -Milliseconds 300
    $windows = Get-CandidateWindows
    if ($windows.Count -gt 0) {
        foreach ($hwnd in $windows) {
            Set-TopMost $hwnd
        }
        $pinned = $true
        break
    }
}

exit $(if ($pinned) { 0 } else { 0 })
