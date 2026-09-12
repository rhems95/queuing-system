@echo off
set "BAT_DIR=%~dp0"
set "TARGET=%BAT_DIR%start-staff-float.bat"

if not exist "%TARGET%" (
    echo start-staff-float.bat was not found next to this installer.
    pause
    exit /b 1
)

REM Lets the staff dashboard "Open System Float" button start THIS PC's bat
REM (not the server). Run once per staff PC. No admin required.
reg add "HKCU\Software\Classes\pecit-float" /ve /d "URL:PECIT Staff Float" /f >nul
reg add "HKCU\Software\Classes\pecit-float" /v "URL Protocol" /d "" /f >nul
reg add "HKCU\Software\Classes\pecit-float\shell\open\command" /ve /d "\"%TARGET%\" %%1" /f >nul

echo.
echo Open System Float will now run:
echo   %TARGET%
echo.
echo Other staff PCs: set bats\staff-float-url.txt to
echo   http://192.168.2.100/queue-system/public/window/float
echo (use your server LAN IP if it is different.)
echo.
pause
exit /b 0
