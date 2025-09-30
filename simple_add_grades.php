<?php

// Simple script to add sample grades directly to database
// Run this from your project root directory

echo "Adding sample grades directly to database...\n";

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'lphs_sms';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Find a student (preferably our new student)
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? OR email = ? LIMIT 1");
    $stmt->execute(['new.student@lphs.edu', 'demo.student@lphs.edu']);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo "No student found. Creating a test student...\n";
        
        // Create a simple test student
        $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, student_id, grade_level, enrollment_status, school_year) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Test', 'Student', 'test.student@lphs.edu', 'TEST-001', 10, 'enrolled', '2024-2025']);
        $studentId = $pdo->lastInsertId();
        
        echo "Created test student with ID: $studentId\n";
    } else {
        $studentId = $student['id'];
        echo "Using existing student: {$student['first_name']} {$student['last_name']} (ID: $studentId)\n";
    }
    
    // Create some basic subjects if they don't exist
    $subjects = [
        ['Mathematics', 'MATH10', 3, 10],
        ['English', 'ENG10', 3, 10],
        ['Science', 'SCI10', 3, 10],
        ['Filipino', 'FIL10', 3, 10],
        ['Physical Education', 'PE10', 2, 10]
    ];
    
    $subjectIds = [];
    foreach ($subjects as $subject) {
        // Check if subject exists
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
        $stmt->execute([$subject[1]]);
        $existingSubject = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSubject) {
            $subjectIds[] = $existingSubject['id'];
            echo "Subject {$subject[1]} already exists.\n";
        } else {
            // Create subject
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, subject_code, units, grade_level) VALUES (?, ?, ?, ?)");
            $stmt->execute($subject);
            $subjectIds[] = $pdo->lastInsertId();
            echo "Created subject: {$subject[0]} ({$subject[1]})\n";
        }
    }
    
    // Add grades for current quarter
    $currentMonth = (int) date('n');
    $currentQuarter = 1; // Default to Q1
    
    if ($currentMonth >= 6 && $currentMonth <= 8) {
        $currentQuarter = 1;
    } elseif ($currentMonth >= 9 && $currentMonth <= 11) {
        $currentQuarter = 2;
    } elseif ($currentMonth >= 12 || $currentMonth <= 2) {
        $currentQuarter = 3;
    } else {
        $currentQuarter = 4;
    }
    
    echo "Adding grades for Quarter $currentQuarter...\n";
    
    $sampleGrades = [88.5, 92.0, 85.0, 90.5, 95.0];
    $schoolYear = '2024-2025';
    
    for ($i = 0; $i < count($subjectIds) && $i < count($sampleGrades); $i++) {
        // Check if grade already exists
        $stmt = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND quarter = ? AND school_year = ?");
        $stmt->execute([$studentId, $subjectIds[$i], $currentQuarter, $schoolYear]);
        $existingGrade = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingGrade) {
            echo "Grade already exists for subject ID {$subjectIds[$i]}, Quarter $currentQuarter\n";
        } else {
            // Insert grade
            $grade = $sampleGrades[$i];
            $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : 'Good');
            
            $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, quarter, school_year, grade, remarks, date_recorded) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$studentId, $subjectIds[$i], $currentQuarter, $schoolYear, $grade, $remarks]);
            
            echo "Added grade $grade for subject ID {$subjectIds[$i]}, Quarter $currentQuarter\n";
        }
    }
    
    // Calculate and display average
    $stmt = $pdo->prepare("SELECT AVG(grade) as average FROM grades WHERE student_id = ? AND quarter = ? AND school_year = ?");
    $stmt->execute([$studentId, $currentQuarter, $schoolYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $average = $result['average'];
    
    echo "\n=== Summary ===\n";
    echo "Student ID: $studentId\n";
    echo "Quarter $currentQuarter Average: " . ($average ? number_format($average, 2) : 'N/A') . "\n";
    echo "School Year: $schoolYear\n";
    
    echo "\n✅ Sample grades added successfully!\n";
    echo "The dashboard should now show dynamic data instead of static 85.5%\n";
    
} catch (PDOException $e) {
    echo "\n❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

echo "\nScript completed.\n";
