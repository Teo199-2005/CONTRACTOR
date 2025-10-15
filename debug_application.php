<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "=== DEBUG: Next Year Application Submission ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $result = $db->query("SELECT 1 as test")->getRow();
    echo "   ✓ Database connection successful\n\n";

    // Check if next_year_applications table exists
    echo "2. Checking next_year_applications table...\n";
    $tableExists = $db->tableExists('next_year_applications');
    if ($tableExists) {
        echo "   ✓ Table exists\n";
        
        // Show table structure
        $fields = $db->getFieldData('next_year_applications');
        echo "   Table structure:\n";
        foreach ($fields as $field) {
            echo "     - {$field->name} ({$field->type})\n";
        }
    } else {
        echo "   ✗ Table does not exist\n";
        echo "   Creating table...\n";
        
        $sql = "CREATE TABLE next_year_applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            current_grade_level INT NOT NULL,
            next_grade_level INT NOT NULL,
            gwa DECIMAL(5,2) NOT NULL,
            school_year VARCHAR(20) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            applied_at DATETIME NOT NULL,
            reviewed_at DATETIME NULL,
            reviewed_by INT NULL,
            notes TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
        )";
        
        $db->query($sql);
        echo "   ✓ Table created successfully\n";
    }
    echo "\n";

    // Test finding student by LRN
    echo "3. Testing student lookup...\n";
    $student = $db->table('students')
        ->where('lrn', '100000000001')
        ->get()->getRowArray();
    
    if ($student) {
        echo "   ✓ Found student: {$student['first_name']} {$student['last_name']} (ID: {$student['id']})\n";
        echo "   Grade Level: {$student['grade_level']}\n";
    } else {
        echo "   ✗ Student with LRN 100000000001 not found\n";
        
        // Try to find any student
        $anyStudent = $db->table('students')->limit(1)->get()->getRowArray();
        if ($anyStudent) {
            echo "   Found sample student: {$anyStudent['first_name']} {$anyStudent['last_name']} (LRN: {$anyStudent['lrn']})\n";
        }
    }
    echo "\n";

    // Test application insertion
    echo "4. Testing application insertion...\n";
    if ($student) {
        // Check if application already exists
        $existing = $db->table('next_year_applications')
            ->where('student_id', $student['id'])
            ->where('school_year', '2026-2027')
            ->get()->getRow();
        
        if ($existing) {
            echo "   ! Application already exists (ID: {$existing->id}, Status: {$existing->status})\n";
        } else {
            $applicationData = [
                'student_id' => $student['id'],
                'current_grade_level' => $student['grade_level'],
                'next_grade_level' => $student['grade_level'] + 1,
                'gwa' => 85.50,
                'school_year' => '2026-2027',
                'status' => 'pending',
                'applied_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->table('next_year_applications')->insert($applicationData);
            if ($result) {
                $insertId = $db->insertID();
                echo "   ✓ Test application inserted successfully (ID: $insertId)\n";
                
                // Clean up test data
                $db->table('next_year_applications')->delete($insertId);
                echo "   ✓ Test data cleaned up\n";
            } else {
                echo "   ✗ Failed to insert test application\n";
                echo "   Error: " . $db->error()['message'] . "\n";
            }
        }
    }
    echo "\n";

    // Test JSON response format
    echo "5. Testing JSON response format...\n";
    $testResponse = [
        'success' => true,
        'message' => 'Application submitted successfully! The admin will review your application for Grade 8.'
    ];
    echo "   Sample success response: " . json_encode($testResponse) . "\n";
    
    $testErrorResponse = [
        'success' => false,
        'error' => 'Student record not found'
    ];
    echo "   Sample error response: " . json_encode($testErrorResponse) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>