<?php
// Simple web-based script to add sample grades
// Access this via: http://localhost/add_sample_data.php

echo "<h2>Adding Sample Grades for Testing</h2>";

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'lphs_sms';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Connected to database successfully.</p>";
    
    // Find a student
    $stmt = $pdo->prepare("SELECT * FROM students LIMIT 1");
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo "<p>❌ No student found in database.</p>";
        exit;
    }
    
    $studentId = $student['id'];
    echo "<p>📚 Using student: {$student['first_name']} {$student['last_name']} (ID: $studentId)</p>";
    
    // Create basic subjects
    $subjects = [
        ['Mathematics', 'MATH10', 3, 10],
        ['English', 'ENG10', 3, 10],
        ['Science', 'SCI10', 3, 10],
        ['Filipino', 'FIL10', 3, 10],
        ['Physical Education', 'PE10', 2, 10]
    ];
    
    $subjectIds = [];
    foreach ($subjects as $subject) {
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
        $stmt->execute([$subject[1]]);
        $existingSubject = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSubject) {
            $subjectIds[] = $existingSubject['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, subject_code, units, grade_level) VALUES (?, ?, ?, ?)");
            $stmt->execute($subject);
            $subjectIds[] = $pdo->lastInsertId();
            echo "<p>➕ Created subject: {$subject[0]}</p>";
        }
    }
    
    // Determine current quarter
    $currentMonth = (int) date('n');
    $currentQuarter = 1;
    
    if ($currentMonth >= 6 && $currentMonth <= 8) {
        $currentQuarter = 1;
    } elseif ($currentMonth >= 9 && $currentMonth <= 11) {
        $currentQuarter = 2;
    } elseif ($currentMonth >= 12 || $currentMonth <= 2) {
        $currentQuarter = 3;
    } else {
        $currentQuarter = 4;
    }
    
    echo "<p>📅 Adding grades for Quarter $currentQuarter (based on current month: $currentMonth)</p>";
    
    // Sample grades
    $sampleGrades = [88.5, 92.0, 85.0, 90.5, 95.0];
    $schoolYear = '2024-2025';
    
    // Clear existing grades for this quarter first
    $stmt = $pdo->prepare("DELETE FROM grades WHERE student_id = ? AND quarter = ? AND school_year = ?");
    $stmt->execute([$studentId, $currentQuarter, $schoolYear]);
    
    // Add new grades
    for ($i = 0; $i < count($subjectIds) && $i < count($sampleGrades); $i++) {
        $grade = $sampleGrades[$i];
        $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : 'Good');
        
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, quarter, school_year, grade, remarks, date_recorded) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$studentId, $subjectIds[$i], $currentQuarter, $schoolYear, $grade, $remarks]);
        
        echo "<p>📊 Added grade: $grade for subject ID {$subjectIds[$i]}</p>";
    }
    
    // Calculate average
    $stmt = $pdo->prepare("SELECT AVG(grade) as average FROM grades WHERE student_id = ? AND quarter = ? AND school_year = ?");
    $stmt->execute([$studentId, $currentQuarter, $schoolYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $average = $result['average'];
    
    echo "<h3>📈 Results:</h3>";
    echo "<p><strong>Student ID:</strong> $studentId</p>";
    echo "<p><strong>Quarter $currentQuarter Average:</strong> " . number_format($average, 2) . "</p>";
    echo "<p><strong>School Year:</strong> $schoolYear</p>";
    
    echo "<h3>✅ Success!</h3>";
    echo "<p>Sample grades have been added. The dashboard should now show <strong>" . number_format($average, 2) . "%</strong> instead of the static 85.5%</p>";
    echo "<p><a href='../student/dashboard'>Go to Student Dashboard</a> to see the dynamic data!</p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
