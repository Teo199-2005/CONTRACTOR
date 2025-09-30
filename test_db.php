<?php

// Simple database connection test
// Run this to check if your database is working

echo "Testing Database Connection...\n";
echo "==============================\n\n";

try {
    // Include CodeIgniter bootstrap
    require_once 'preload.php';
    
    // Test database connection
    $db = \Config\Database::connect();
    
    if ($db->connect(false)) {
        echo "✅ Database connection successful!\n";
        echo "Database: " . $db->database . "\n";
        echo "Host: " . $db->hostname . "\n\n";
        
        // Check if tables exist
        echo "Checking Tables:\n";
        $tables = $db->listTables();
        
        if (empty($tables)) {
            echo "❌ No tables found in database!\n";
            echo "You may need to run migrations first.\n";
        } else {
            echo "✅ Found " . count($tables) . " tables:\n";
            foreach ($tables as $table) {
                echo "  - " . $table . "\n";
            }
            
            // Check specific tables
            echo "\nChecking Key Tables:\n";
            $keyTables = ['students', 'teachers', 'sections', 'subjects', 'announcements'];
            
            foreach ($keyTables as $table) {
                if (in_array($table, $tables)) {
                    $count = $db->table($table)->countAllResults();
                    echo "  ✅ " . $table . ": " . $count . " records\n";
                } else {
                    echo "  ❌ " . $table . ": Table not found\n";
                }
            }
        }
        
    } else {
        echo "❌ Database connection failed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n==============================\n";
echo "Test completed.\n";



