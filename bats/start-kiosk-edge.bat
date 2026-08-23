@echo off
setlocal EnableExtensions
title PECIT Kiosk Silent Print (Edge)

REM ============================================================
REM  Silent print for Xprinter XP-58 XP-Q90EC — Edge
REM ============================================================

set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
set "SET_PRINTER=%ROOT_DIR%\tools\kiosk\Set-DefaultPrinter.ps1"
set "PROFILE=%LOCALAPPDATA%\PECIT-Kiosk-Edge"
set "KIOSK_URL=http://localhost/queue-system/public/kiosk"
set "PRINTER_MATCH=XP-Q90EC"

echo.
echo Waiting 10 seconds before starting kiosk...
ping -n 11 127.0.0.1 >nul

set "EDGE="
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if not defined EDGE if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

if not defined EDGE (
    echo.
    echo Microsoft Edge not found.
    echo.
    pause
    exit /b 1
)

echo.
if exist "%SET_PRINTER%" (
    echo Setting default printer matching %PRINTER_MATCH% ...
    powershell -NoProfile -ExecutionPolicy Bypass -File "%SET_PRINTER%" -Match "%PRINTER_MATCH%"
    if errorlevel 1 (
        echo WARNING: Could not set default printer. Continuing to open kiosk...
    )
)

echo.
echo Closing existing Edge processes...
taskkill /F /IM msedge.exe /T >nul 2>&1
ping -n 3 127.0.0.1 >nul

if not exist "%PROFILE%" mkdir "%PROFILE%" >nul 2>&1

echo Starting kiosk...
echo URL: %KIOSK_URL%
echo.

start "PECIT Kiosk" "%EDGE%" --user-data-dir="%PROFILE%" --kiosk --kiosk-printing --no-first-run --no-default-browser-check --disable-session-crashed-bubble --hide-crash-restore-bubble --disable-infobars "%KIOSK_URL%"

ping -n 3 127.0.0.1 >nul
tasklist /FI "IMAGENAME eq msedge.exe" | find /I "msedge.exe" >nul
if errorlevel 1 (
    echo Edge did not start. Opening maximized window instead...
    start "PECIT Kiosk" "%EDGE%" --user-data-dir="%PROFILE%" --start-maximized --no-first-run "%KIOSK_URL%"
    echo.
    pause
)

endlocal
exit /b 0
