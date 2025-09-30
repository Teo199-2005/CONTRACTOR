@echo off
echo Creating New Approved Student Account for LPHS SMS...
echo.

cd /d "%~dp0"

php create_new_student.php

echo.
echo Press any key to exit...
pause >nul
