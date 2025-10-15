<?php
// Simple script to create the next_year_applications table

// Database connection (adjust these values as needed)
$host = 'localhost';
$database = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'next_year_applications'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "Table 'next_year_applications' already exists.\n";
        
        // Show existing records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM next_year_applications");
        $count = $stmt->fetch()['count'];
        echo "Current records: $count\n";
        
    } else {
        echo "Creating 'next_year_applications' table...\n";
        
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
            INDEX idx_student_id (student_id),
            INDEX idx_school_year (school_year),
            INDEX idx_status (status)
        )";
        
        $pdo->exec($sql);
        echo "Table created successfully!\n";
    }
    
    // Test student lookup
    echo "\nTesting student lookup...\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, lrn, grade_level FROM students WHERE lrn = ?");
    $stmt->execute(['100000000001']);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "Found student: {$student['first_name']} {$student['last_name']} (ID: {$student['id']}, Grade: {$student['grade_level']})\n";
    } else {
        echo "Student with LRN 100000000001 not found.\n";
        
        // Show available students
        $stmt = $pdo->query("SELECT id, first_name, last_name, lrn, grade_level FROM students LIMIT 3");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Available students:\n";
        foreach ($students as $s) {
            echo "- {$s['first_name']} {$s['last_name']} (LRN: {$s['lrn']}, Grade: {$s['grade_level']})\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection settings.\n";
}
?>