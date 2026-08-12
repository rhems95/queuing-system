@echo off
setlocal
title PECIT Kiosk Silent Print

REM ============================================================
REM  Silent print for Xprinter XP-58 (no Print dialog)
REM
REM  IMPORTANT:
REM  - Chrome must start FRESH with --kiosk-printing
REM  - If Chrome is already open, the dialog WILL still appear
REM  - This script closes Chrome, then relaunches in kiosk mode
REM ============================================================

set "CHROME=C:\Program Files\Google\Chrome\Application\chrome.exe"
set "PROFILE=%LOCALAPPDATA%\PECIT-Kiosk-Chrome"
set "KIOSK_URL=http://localhost/queue-system/public/kiosk"

if not exist "%CHROME%" (
    echo.
    echo Chrome not found:
    echo   %CHROME%
    echo.
    pause
    exit /b 1
)

echo.
echo Closing existing Chrome windows so silent print can work...
taskkill /F /IM chrome.exe >nul 2>&1
timeout /t 2 /nobreak >nul

if not exist "%PROFILE%" mkdir "%PROFILE%" >nul 2>&1

echo Starting kiosk with silent printing...
echo URL: %KIOSK_URL%
echo.
echo Tips:
echo  1) Set "Xprinter XP-58" as Windows DEFAULT printer
echo  2) Do NOT open Chrome manually afterward
echo  3) Use only this launcher for the kiosk PC
echo.

start "" "%CHROME%" ^
  --user-data-dir="%PROFILE%" ^
  --kiosk ^
  --kiosk-printing ^
  --no-first-run ^
  --disable-session-crashed-bubble ^
  --disable-infobars ^
  "%KIOSK_URL%"

endlocal
