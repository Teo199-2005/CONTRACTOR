<?php

// Script to create a new approved student account using DemoAccountsSeeder
// Run this from your project root directory

// Include CodeIgniter bootstrap
require_once 'preload.php';

echo "Creating new approved student account...\n";
echo "Running DemoAccountsSeeder to create demo accounts including new student...\n\n";

try {
    // Get the seeder instance
    $seeder = new \App\Database\Seeds\DemoAccountsSeeder();

    // Run the seeder
    $seeder->run();

    echo "\n✅ Demo accounts created successfully!\n";
    echo "\n=== Available Student Accounts ===\n";
    echo "1. Demo Student (Original):\n";
    echo "   Email: demo.student@lphs.edu\n";
    echo "   Password: DemoPass123!\n";
    echo "   Status: Enrolled\n";
    echo "   Grade Level: 7\n\n";

    echo "2. New Student (Approved by Admin):\n";
    echo "   Email: new.student@lphs.edu\n";
    echo "   Password: DemoPass123!\n";
    echo "   Status: Approved by Admin\n";
    echo "   Grade Level: 8\n";
    echo "   Name: John Doe\n\n";

    echo "You can now login with either of these student accounts!\n";
    echo "The new student account is already approved and ready to use.\n";

} catch (\Throwable $e) {
    echo "\n❌ Error during account creation: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nScript completed.\n";
