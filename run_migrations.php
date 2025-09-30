<?php

// Run Database Migrations
// This should be run before the seeder

echo "Running Database Migrations...\n";
echo "==============================\n\n";

try {
    // Include CodeIgniter bootstrap
    require_once 'preload.php';
    
    // Get the migration instance
    $migrate = \Config\Services::migrations();
    
    echo "Checking current migration status...\n";
    
    // Get current migration status
    $currentVersion = $migrate->getVersion();
    echo "Current migration version: " . $currentVersion . "\n\n";
    
    // Run migrations
    echo "Running migrations...\n";
    $result = $migrate->latest();
    
    if ($result === false) {
        echo "❌ Migration failed!\n";
    } else {
        echo "✅ Migrations completed successfully!\n";
        echo "New migration version: " . $migrate->getVersion() . "\n\n";
        
        // Show what tables were created
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        
        echo "Tables in database:\n";
        foreach ($tables as $table) {
            echo "  - " . $table . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n==============================\n";
echo "Migration process completed.\n";



