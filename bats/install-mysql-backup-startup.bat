@echo off
setlocal EnableExtensions
title Install PECIT MySQL backup startup shortcut

REM Creates a Startup-folder shortcut so MySQL is dumped at Windows login.

set "BAT_DIR=%~dp0"
set "TARGET=%BAT_DIR%backup-mysql-on-startup.bat"
set "STARTUP=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup"
set "SHORTCUT=%STARTUP%\PECIT MySQL Backup.lnk"

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
  "$sc.Description='Dump XAMPP MySQL databases at Windows login';" ^
  "$sc.Save();" ^
  "Write-Host ('Installed: %SHORTCUT%')"

if errorlevel 1 (
    echo Failed to create Startup shortcut.
    pause
    exit /b 1
)

echo.
echo Done. MySQL will be backed up when this Windows user signs in.
echo The dump waits until MySQL is running, so it is safe alongside
echo the PECIT Queuing System startup shortcut.
echo.
echo Backups are stored in:
echo   C:\xampp\mysql\backups
echo.
echo To remove later, delete:
echo   %SHORTCUT%
echo.
pause
endlocal
exit /b 0
