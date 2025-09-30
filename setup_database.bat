@echo off
echo Setting up LPHS SMS Database...

REM Import database structure
mysql -u root -p -e "SOURCE database_setup.sql;"

REM Import sample data
mysql -u root -p -e "SOURCE sample_data.sql;"

echo Database setup complete!
pause