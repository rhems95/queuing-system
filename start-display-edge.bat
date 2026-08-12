@echo off
setlocal
title PECIT Public Display (Edge)

REM ============================================================
REM  Open the public display page fullscreen in Microsoft Edge
REM ============================================================

set "EDGE="
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

set "PROFILE=%LOCALAPPDATA%\PECIT-Display-Edge"
set "DISPLAY_URL=http://localhost/queue-system/public/display"

if "%EDGE%"=="" (
    echo.
    echo Microsoft Edge not found.
    echo Install Edge or edit this file with the correct msedge.exe path.
    echo.
    pause
    exit /b 1
)

echo.
echo Starting PECIT public display (Edge fullscreen)...
echo URL: %DISPLAY_URL%
echo.

if not exist "%PROFILE%" mkdir "%PROFILE%" >nul 2>&1

start "" "%EDGE%" ^
  --user-data-dir="%PROFILE%" ^
  --kiosk ^
  --no-first-run ^
  --disable-session-crashed-bubble ^
  --disable-infobars ^
  --autoplay-policy=no-user-gesture-required ^
  "%DISPLAY_URL%"

endlocal
