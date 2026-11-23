@echo off
setlocal EnableExtensions
set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
set "WATCH=%ROOT_DIR%\tools\kiosk\Watch-Acr122.ps1"
set "LAUNCH=%ROOT_DIR%\tools\kiosk\Start-Acr122-Hidden.vbs"

if not exist "%WATCH%" (
    echo Missing reader helper:
    echo   %WATCH%
    pause
    exit /b 1
)

if not exist "%LAUNCH%" (
    echo Missing hidden launcher:
    echo   %LAUNCH%
    pause
    exit /b 1
)

REM wscript has no console. The VBS starts the watcher with ShowWindow=0
REM so Windows Terminal cannot keep a PowerShell window open.
wscript.exe //nologo "%LAUNCH%"
endlocal
exit
