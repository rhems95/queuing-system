@echo off
set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
cd /d "%ROOT_DIR%"

REM Float page URL:
REM   Kiosk / server PC can use localhost.
REM   Other staff PCs must use the server LAN IP (edit bats\staff-float-url.txt).
REM   Example: http://192.168.2.100/queue-system/public/window/float
set "FLOAT_URL="
set "ARG=%~1"
if /i "%ARG:~0,4%"=="http" set "FLOAT_URL=%ARG%"
if "%FLOAT_URL%"=="" if exist "%BAT_DIR%staff-float-url.txt" (
    for /f "usebackq tokens=* delims=" %%U in ("%BAT_DIR%staff-float-url.txt") do (
        if not "%%U"=="" if "%FLOAT_URL%"=="" set "FLOAT_URL=%%U"
    )
)
if "%FLOAT_URL%"=="" set "FLOAT_URL=http://localhost/queue-system/public/window/float"

REM Start the helper hidden, then close this console.
start "" powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%ROOT_DIR%\tools\staff-float\Start-StaffFloat.ps1" -Browser chrome -Url "%FLOAT_URL%"
exit
