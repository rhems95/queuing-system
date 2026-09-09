@echo off
setlocal EnableExtensions EnableDelayedExpansion
title PECIT MySQL backup

REM ============================================================
REM  Daily (login) dump of XAMPP MySQL user databases.
REM  Waits for mysqld so this can run from the Windows Startup
REM  folder at the same time as start-pecit-on-windows.bat.
REM ============================================================

set "XAMPP_DIR=C:\xampp"
set "MYSQL=%XAMPP_DIR%\mysql\bin\mysql.exe"
set "MYSQLDUMP=%XAMPP_DIR%\mysql\bin\mysqldump.exe"
set "MYSQLADMIN=%XAMPP_DIR%\mysql\bin\mysqladmin.exe"
set "BACKUP_DIR=%XAMPP_DIR%\mysql\backups"
set "KEEP_DAYS=14"
set "MYSQL_USER=root"

echo.
echo ========================================
echo   PECIT MySQL backup
echo ========================================
echo.

if not exist "%MYSQL%" (
    echo ERROR: mysql.exe not found:
    echo   %MYSQL%
    goto fail
)
if not exist "%MYSQLDUMP%" (
    echo ERROR: mysqldump.exe not found:
    echo   %MYSQLDUMP%
    goto fail
)

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "%BACKUP_DIR%" (
    echo ERROR: Could not create backup folder:
    echo   %BACKUP_DIR%
    goto fail
)

set "LOG=%BACKUP_DIR%\pecit-mysql-backup.log"

echo Waiting for MySQL to accept connections...
set /a TRIES=0
:wait_mysql
set /a TRIES+=1
"%MYSQLADMIN%" -u %MYSQL_USER% ping >nul 2>&1
if not errorlevel 1 goto mysql_ready

if %TRIES% GEQ 40 (
    echo.
    echo Timed out waiting for MySQL.
    echo Start MySQL in XAMPP, then re-run this bat.
    call :log "FAIL timeout waiting for MySQL"
    goto fail
)

ping -n 4 127.0.0.1 >nul
goto wait_mysql

:mysql_ready
echo MySQL is up.
echo.

for /f "usebackq delims=" %%T in (`powershell -NoProfile -Command "Get-Date -Format 'yyyy-MM-dd_HHmmss'"`) do set "STAMP=%%T"
set "OUTFILE=%BACKUP_DIR%\pecit-mysql-%STAMP%.sql"
set "LATEST=%BACKUP_DIR%\pecit-mysql-latest.sql"

set "DBLIST_FILE=%TEMP%\pecit-mysql-dblist.txt"
"%MYSQL%" -u %MYSQL_USER% -N -e "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('information_schema','performance_schema','mysql','sys','phpmyadmin','test') ORDER BY schema_name" > "%DBLIST_FILE%"
if errorlevel 1 (
    echo ERROR: Could not list databases.
    call :log "FAIL SHOW DATABASES"
    goto fail
)

set "DBLIST="
for /f "usebackq delims=" %%D in ("%DBLIST_FILE%") do (
    set "DBLIST=!DBLIST! %%D"
)

if "!DBLIST!"=="" (
    echo ERROR: No user databases found to dump.
    call :log "FAIL no user databases"
    goto fail
)

echo Dumping:!DBLIST!
echo   -^> %OUTFILE%
echo.

"%MYSQLDUMP%" -u %MYSQL_USER% --single-transaction --routines --events --add-drop-database --databases !DBLIST! --result-file="%OUTFILE%"
if errorlevel 1 (
    echo ERROR: mysqldump failed.
    call :log "FAIL mysqldump"
    goto fail
)

if not exist "%OUTFILE%" (
    echo ERROR: Dump file was not created.
    call :log "FAIL missing dump file"
    goto fail
)

for %%S in ("%OUTFILE%") do set "SIZE=%%~zS"
if "%SIZE%"=="0" (
    echo ERROR: Dump file is empty.
    call :log "FAIL empty dump"
    goto fail
)

copy /Y "%OUTFILE%" "%LATEST%" >nul
echo Saved %SIZE% bytes.
echo Latest copy: %LATEST%
echo.

echo Removing dumps older than %KEEP_DAYS% days...
forfiles /p "%BACKUP_DIR%" /m pecit-mysql-20*.sql /d -%KEEP_DAYS% /c "cmd /c del /q @path" >nul 2>&1

call :log "OK %OUTFILE% (%SIZE% bytes) dbs:!DBLIST!"
echo Backup finished.
echo This window will close in 6 seconds...
ping -n 7 127.0.0.1 >nul
endlocal
exit /b 0

:fail
echo.
echo Backup failed. See:
echo   %LOG%
echo.
echo This window will close in 15 seconds...
ping -n 16 127.0.0.1 >nul
endlocal
exit /b 1

:log
echo %DATE% %TIME% %~1>>"%LOG%"
exit /b 0
