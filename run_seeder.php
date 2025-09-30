<?php

// Simple script to run the CompleteDataSeeder
// Run this from your project root directory

// Include CodeIgniter bootstrap
require_once 'preload.php';

// Get the seeder instance
$seeder = new \App\Database\Seeds\CompleteDataSeeder();

echo "Starting Complete Data Seeder...\n";
echo "This will add sample data to all tables.\n\n";

try {
    // Run the seeder
    $seeder->run();
    echo "\n✅ Seeding completed successfully!\n";
    echo "\nSample accounts created:\n";
    echo "Teachers:\n";
    echo "- teacher1@lphs.edu (Password: DemoPass123!)\n";
    echo "- teacher2@lphs.edu (Password: DemoPass123!)\n";
    echo "- teacher3@lphs.edu (Password: DemoPass123!)\n";
    echo "\nStudents:\n";
    echo "- student1@lphs.edu (Password: DemoPass123!)\n";
    echo "- student2@lphs.edu (Password: DemoPass123!)\n";
    echo "- student3@lphs.edu (Password: DemoPass123!)\n";
    echo "- student4@lphs.edu (Password: DemoPass123!)\n";
    echo "- student5@lphs.edu (Password: DemoPass123!)\n";
    echo "\nParents:\n";
    echo "- parent1@lphs.edu (Password: DemoPass123!)\n";
    echo "- parent2@lphs.edu (Password: DemoPass123!)\n";
    echo "- parent3@lphs.edu (Password: DemoPass123!)\n";
    echo "\nAdmin:\n";
    echo "- demo.admin@lphs.edu (Password: DemoPass123!)\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during seeding: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}



