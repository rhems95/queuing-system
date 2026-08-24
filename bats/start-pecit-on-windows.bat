@echo off
setlocal EnableExtensions
title PECIT Queuing System — Windows Startup

REM ============================================================
REM  Boot helper for Windows login / Startup folder
REM  1) Start XAMPP Apache + MySQL
REM  2) Wait until the kiosk URL responds
REM  3) Launch kiosk (Chrome silent print)
REM  4) Optional: launch public display (Edge)
REM ============================================================

set "XAMPP_DIR=C:\xampp"
set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
set "KIOSK_BAT=%BAT_DIR%start-kiosk-chrome.bat"
set "DISPLAY_BAT=%BAT_DIR%start-display-edge.bat"
set "KIOSK_URL=http://localhost/queue-system/public/kiosk"
set "START_DISPLAY=0"

echo.
echo ========================================
echo   PECIT Queuing System startup
echo ========================================
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" | find /I "mysqld.exe" >nul
if errorlevel 1 (
    echo Starting MySQL...
    if exist "%XAMPP_DIR%\mysql_start.bat" (
        start "XAMPP MySQL" /MIN cmd /c ""%XAMPP_DIR%\mysql_start.bat""
    ) else if exist "%XAMPP_DIR%\mysql\bin\mysqld.exe" (
        start "XAMPP MySQL" /MIN "%XAMPP_DIR%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP_DIR%\mysql\bin\my.ini"
    ) else (
        echo WARNING: MySQL starter not found in %XAMPP_DIR%
    )
) else (
    echo MySQL already running.
)

tasklist /FI "IMAGENAME eq httpd.exe" | find /I "httpd.exe" >nul
if errorlevel 1 (
    echo Starting Apache...
    if exist "%XAMPP_DIR%\apache_start.bat" (
        start "XAMPP Apache" /MIN cmd /c ""%XAMPP_DIR%\apache_start.bat""
    ) else if exist "%XAMPP_DIR%\apache\bin\httpd.exe" (
        start "XAMPP Apache" /MIN "%XAMPP_DIR%\apache\bin\httpd.exe"
    ) else (
        echo WARNING: Apache starter not found in %XAMPP_DIR%
    )
) else (
    echo Apache already running.
)

echo.
echo Waiting for kiosk URL to become ready...
echo   %KIOSK_URL%
echo.

set /a TRIES=0
:wait_loop
set /a TRIES+=1
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "try { $r=Invoke-WebRequest -Uri '%KIOSK_URL%' -UseBasicParsing -TimeoutSec 3; if($r.StatusCode -ge 200 -and $r.StatusCode -lt 500){ exit 0 } else { exit 1 } } catch { exit 1 }"
if not errorlevel 1 goto ready

if %TRIES% GEQ 40 (
    echo.
    echo Timed out waiting for Apache/kiosk.
    echo Start Apache + MySQL in XAMPP Control Panel, then re-run this bat.
    echo.
    pause
    exit /b 1
)

ping -n 3 127.0.0.1 >nul
goto wait_loop

:ready
echo Kiosk URL is ready.
echo.

if not exist "%KIOSK_BAT%" (
    echo Missing kiosk launcher:
    echo   %KIOSK_BAT%
    pause
    exit /b 1
)

echo Launching kiosk...
start "PECIT Kiosk Launcher" cmd /c ""%KIOSK_BAT%""

if "%START_DISPLAY%"=="1" if exist "%DISPLAY_BAT%" (
    echo Launching display...
    start "PECIT Display Launcher" cmd /c ""%DISPLAY_BAT%""
)

echo.
echo Startup sequence finished.
echo This window will close in 5 seconds...
ping -n 6 127.0.0.1 >nul
endlocal
exit /b 0
