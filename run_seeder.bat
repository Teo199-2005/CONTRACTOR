@echo off
echo Running Complete Data Seeder for LPHS SMS...
echo.

cd /d "%~dp0"

php run_seeder.php

echo.
echo Press any key to exit...
pause >nul



