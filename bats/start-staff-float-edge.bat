@echo off
set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
cd /d "%ROOT_DIR%"

REM Same URL rules as start-staff-float.bat — edit bats\staff-float-url.txt for other PCs.
set "FLOAT_URL="
set "ARG=%~1"
if /i "%ARG:~0,4%"=="http" set "FLOAT_URL=%ARG%"
if "%FLOAT_URL%"=="" if exist "%BAT_DIR%staff-float-url.txt" (
    for /f "usebackq tokens=* delims=" %%U in ("%BAT_DIR%staff-float-url.txt") do (
        if not "%%U"=="" if "%FLOAT_URL%"=="" set "FLOAT_URL=%%U"
    )
)
if "%FLOAT_URL%"=="" set "FLOAT_URL=http://localhost/queue-system/public/window/float"

start "" powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%ROOT_DIR%\tools\staff-float\Start-StaffFloat.ps1" -Browser edge -Url "%FLOAT_URL%"
exit
