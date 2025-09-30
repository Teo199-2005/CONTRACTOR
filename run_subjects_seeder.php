<?php

// Simple script to run the subjects seeder
require_once 'vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$seeder = \Config\Database::seeder();
$seeder->call('SubjectsSeeder');

echo "Subjects seeder completed successfully!\n";