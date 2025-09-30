@echo off
echo Adding Sample Grades for LPHS SMS...
echo.

cd /d "%~dp0"

php add_sample_grades.php

echo.
echo Press any key to exit...
pause >nul
