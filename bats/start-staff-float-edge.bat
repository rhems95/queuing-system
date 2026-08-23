@echo off
set "BAT_DIR=%~dp0"
for %%I in ("%BAT_DIR%..") do set "ROOT_DIR=%%~fI"
cd /d "%ROOT_DIR%"
powershell -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%ROOT_DIR%\tools\staff-float\Start-StaffFloat.ps1" -Browser edge
exit /b 0
