@echo off
setlocal
title PECIT Kiosk Silent Print (Edge)

REM ============================================================
REM  Silent print for Xprinter XP-58 (no Print dialog) — Microsoft Edge
REM
REM  IMPORTANT:
REM  - Edge must start FRESH with --kiosk-printing
REM  - If Edge is already open, the dialog WILL still appear
REM  - This script closes Edge, then relaunches in kiosk mode
REM ============================================================

set "EDGE="
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

set "PROFILE=%LOCALAPPDATA%\PECIT-Kiosk-Edge"
set "KIOSK_URL=http://localhost/queue-system/public/kiosk"

if "%EDGE%"=="" (
    echo.
    echo Microsoft Edge not found.
    echo Install Edge or edit this file with the correct msedge.exe path.
    echo.
    pause
    exit /b 1
)

echo.
echo Closing existing Edge windows so silent print can work...
taskkill /F /IM msedge.exe >nul 2>&1
timeout /t 2 /nobreak >nul

if not exist "%PROFILE%" mkdir "%PROFILE%" >nul 2>&1

echo Starting kiosk with silent printing (Edge)...
echo URL: %KIOSK_URL%
echo.
echo Tips:
echo  1) Set "Xprinter XP-58" as Windows DEFAULT printer
echo  2) Do NOT open Edge manually afterward
echo  3) Use only this launcher for the kiosk PC
echo.

start "" "%EDGE%" ^
  --user-data-dir="%PROFILE%" ^
  --kiosk ^
  --kiosk-printing ^
  --no-first-run ^
  --disable-session-crashed-bubble ^
  --disable-infobars ^
  "%KIOSK_URL%"

endlocal
