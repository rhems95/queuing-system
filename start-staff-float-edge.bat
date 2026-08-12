@echo off
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%~dp0tools\staff-float\Start-StaffFloat.ps1" -Browser edge
exit /b 0
