<?php
// Simple debug script to test application submission
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = require_once dirname(__DIR__) . '/app/Config/Paths.php';
$app = new \CodeIgniter\CodeIgniter($app);
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "<h2>Debug: Next Year Application Submission</h2>";

try {
    // Test database connection
    echo "<h3>1. Database Connection</h3>";
    $result = $db->query("SELECT 1 as test")->getRow();
    echo "✓ Database connection successful<br><br>";

    // Check table
    echo "<h3>2. Table Check</h3>";
    $tableExists = $db->tableExists('next_year_applications');
    if ($tableExists) {
        echo "✓ next_year_applications table exists<br>";
        
        // Show existing applications
        $applications = $db->table('next_year_applications')->get()->getResultArray();
        echo "Current applications: " . count($applications) . "<br>";
        foreach ($applications as $app) {
            echo "- Student ID {$app['student_id']}: Grade {$app['current_grade_level']} → {$app['next_grade_level']} ({$app['status']})<br>";
        }
    } else {
        echo "✗ Table does not exist<br>";
    }
    echo "<br>";

    // Test student lookup
    echo "<h3>3. Student Lookup</h3>";
    $student = $db->table('students')
        ->where('lrn', '100000000001')
        ->get()->getRowArray();
    
    if ($student) {
        echo "✓ Found student: {$student['first_name']} {$student['last_name']} (ID: {$student['id']})<br>";
        echo "Grade Level: {$student['grade_level']}<br>";
        
        // Test application submission
        echo "<h3>4. Test Application Submission</h3>";
        
        // Check if already applied
        $existing = $db->table('next_year_applications')
            ->where('student_id', $student['id'])
            ->where('school_year', '2026-2027')
            ->get()->getRow();
        
        if ($existing) {
            echo "! Application already exists (Status: {$existing->status})<br>";
            echo "Applied at: {$existing->applied_at}<br>";
        } else {
            echo "No existing application found. Ready to submit.<br>";
        }
        
    } else {
        echo "✗ Student with LRN 100000000001 not found<br>";
        
        // Show available students
        $students = $db->table('students')->limit(5)->get()->getResultArray();
        echo "Available students:<br>";
        foreach ($students as $s) {
            echo "- {$s['first_name']} {$s['last_name']} (LRN: {$s['lrn']}, Grade: {$s['grade_level']})<br>";
        }
    }

} catch (Exception $e) {
    echo "<h3>ERROR</h3>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>