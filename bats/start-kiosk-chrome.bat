@echo off
setlocal EnableExtensions
title PECIT Kiosk Silent Print

REM ============================================================
REM  Silent print for Xprinter XP-58 XP-Q90EC — Chrome
REM ============================================================

set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
set "SET_PRINTER=%ROOT_DIR%\tools\kiosk\Set-DefaultPrinter.ps1"
set "CLEAR_PRINT=%ROOT_DIR%\tools\kiosk\Clear-ChromePrintSticky.ps1"
set "PROFILE=%LOCALAPPDATA%\PECIT-Kiosk-Chrome"
set "KIOSK_URL=http://localhost/queue-system/public/kiosk"
REM Match only — do not put parentheses in echoed/set values used by cmd parsing.
set "PRINTER_MATCH=XP-Q90EC"

echo.
echo Waiting 10 seconds before starting kiosk...
ping -n 11 127.0.0.1 >nul

set "CHROME="
if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if not defined CHROME if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
if not defined CHROME if exist "%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe" set "CHROME=%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"

if not defined CHROME (
    echo.
    echo Google Chrome not found.
    echo Install Chrome or edit this .bat with the correct chrome.exe path.
    echo.
    pause
    exit /b 1
)

echo.
echo Using Chrome:
echo   %CHROME%
echo.

if exist "%SET_PRINTER%" (
    echo Setting Windows default printer matching %PRINTER_MATCH% ...
    powershell -NoProfile -ExecutionPolicy Bypass -File "%SET_PRINTER%" -Match "%PRINTER_MATCH%"
    if errorlevel 1 (
        echo WARNING: Could not set default printer. Continuing to open kiosk...
    )
) else (
    echo WARNING: Missing printer helper:
    echo   %SET_PRINTER%
)

echo.
echo Closing existing Chrome processes...
taskkill /F /IM chrome.exe /T >nul 2>&1
ping -n 3 127.0.0.1 >nul

if not exist "%PROFILE%" mkdir "%PROFILE%" >nul 2>&1

if exist "%CLEAR_PRINT%" (
    echo Clearing Chrome saved printer sticky settings...
    powershell -NoProfile -ExecutionPolicy Bypass -File "%CLEAR_PRINT%" -ProfileDir "%PROFILE%" -PreferredPrinter "%PRINTER_MATCH%"
)

set "ACR_LAUNCH=%ROOT_DIR%\tools\kiosk\Start-Acr122-Hidden.vbs"
if exist "%ACR_LAUNCH%" (
    echo Starting ACR122U card watcher...
    wscript.exe //nologo "%ACR_LAUNCH%"
)

set "PAPER_LAUNCH=%ROOT_DIR%\tools\kiosk\Start-PrinterPaper-Hidden.vbs"
if exist "%PAPER_LAUNCH%" (
    echo Starting printer paper watcher...
    wscript.exe //nologo "%PAPER_LAUNCH%"
)

set "GSM_LAUNCH=%ROOT_DIR%\tools\kiosk\Start-GsmSms-Hidden.vbs"
if exist "%GSM_LAUNCH%" (
    echo Starting GSM SMS watcher...
    wscript.exe //nologo "%GSM_LAUNCH%"
)

echo Starting kiosk...
echo URL: %KIOSK_URL%
echo.

start "PECIT Kiosk" "%CHROME%" --user-data-dir="%PROFILE%" --kiosk --kiosk-printing --no-first-run --no-default-browser-check --disable-session-crashed-bubble --hide-crash-restore-bubble --disable-infobars "%KIOSK_URL%"

ping -n 3 127.0.0.1 >nul
tasklist /FI "IMAGENAME eq chrome.exe" | find /I "chrome.exe" >nul
if errorlevel 1 (
    echo Chrome did not start. Opening maximized window instead...
    start "PECIT Kiosk" "%CHROME%" --user-data-dir="%PROFILE%" --start-maximized --no-first-run "%KIOSK_URL%"
    echo.
    pause
)

endlocal
exit /b 0
