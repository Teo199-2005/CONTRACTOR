@echo off
echo ========================================
echo    LPHS SMS Complete Setup Script
echo ========================================
echo.

cd /d "%~dp0"

echo Step 1: Testing Database Connection...
echo ----------------------------------------
php test_db.php
echo.

echo Step 2: Running Database Migrations...
echo ----------------------------------------
php run_migrations.php
echo.

echo Step 3: Seeding Sample Data...
echo ----------------------------------------
php run_seeder.php
echo.

echo ========================================
echo    Setup Complete!
echo ========================================
echo.
echo Now you can:
echo 1. Login to your system
echo 2. Check the admin dashboard for data
echo 3. Test all the features
echo.
echo Press any key to exit...
pause >nul



