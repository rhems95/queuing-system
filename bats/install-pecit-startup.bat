@echo off
setlocal EnableExtensions
title Install PECIT startup shortcut

REM Creates a Startup-folder shortcut so the system launches at Windows login.

set "BAT_DIR=%~dp0"
set "TARGET=%BAT_DIR%start-pecit-on-windows.bat"
set "STARTUP=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup"
set "SHORTCUT=%STARTUP%\PECIT Queuing System.lnk"

if not exist "%TARGET%" (
    echo Missing:
    echo   %TARGET%
    pause
    exit /b 1
)

if not exist "%STARTUP%" mkdir "%STARTUP%" >nul 2>&1

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ws=New-Object -ComObject WScript.Shell;" ^
  "$sc=$ws.CreateShortcut('%SHORTCUT%');" ^
  "$sc.TargetPath='%TARGET%';" ^
  "$sc.WorkingDirectory='%BAT_DIR%';" ^
  "$sc.WindowStyle=7;" ^
  "$sc.Description='Start PECIT Queuing System (XAMPP + Kiosk)';" ^
  "$sc.Save();" ^
  "Write-Host ('Installed: %SHORTCUT%')"

if errorlevel 1 (
    echo Failed to create Startup shortcut.
    pause
    exit /b 1
)

echo.
echo Done. PECIT will start when this Windows user signs in.
echo.
echo To remove later, delete:
echo   %SHORTCUT%
echo.
pause
endlocal
exit /b 0
