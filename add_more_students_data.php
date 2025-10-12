<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding more students and data...\n\n";
    
    // Get all active sections
    $stmt = $pdo->query("
        SELECT s.id, s.section_name, s.grade_level, s.adviser_id, s.current_enrollment,
               t.first_name, t.last_name
        FROM sections s
        JOIN teachers t ON s.adviser_id = t.id
        WHERE s.is_active = 1
    ");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $studentNames = [
        ['Alex', 'Rivera'], ['Sophia', 'Chen'], ['Marcus', 'Johnson'], ['Isabella', 'Garcia'],
        ['Ethan', 'Williams'], ['Olivia', 'Brown'], ['Noah', 'Davis'], ['Emma', 'Miller'],
        ['Liam', 'Wilson'], ['Ava', 'Moore'], ['Mason', 'Taylor'], ['Mia', 'Anderson'],
        ['Lucas', 'Thomas'], ['Charlotte', 'Jackson'], ['Oliver', 'White'], ['Amelia', 'Harris'],
        ['Elijah', 'Martin'], ['Harper', 'Thompson'], ['James', 'Garcia'], ['Evelyn', 'Martinez']
    ];
    
    foreach ($sections as $section) {
        echo "Processing {$section['section_name']} ({$section['first_name']} {$section['last_name']})...\n";
        
        // Add 5-8 more students to each section
        $newStudents = rand(5, 8);
        
        for ($i = 0; $i < $newStudents; $i++) {
            $name = $studentNames[($section['id'] * 10 + $i) % count($studentNames)];
            $lrn = '2024' . $section['grade_level'] . str_pad($section['id'], 2, '0', STR_PAD_LEFT) . str_pad($i + 20, 2, '0', STR_PAD_LEFT);
            
            // Check if student exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE lrn = ?");
            $stmt->execute([$lrn]);
            if ($stmt->fetch()) continue;
            
            $gender = ($i % 2 == 0) ? 'Male' : 'Female';
            $email = strtolower($name[0] . '.' . $name[1] . $i) . '@student.lphs.edu';
            
            $stmt = $pdo->prepare("
                INSERT INTO students (
                    lrn, first_name, last_name, grade_level, gender, 
                    section_id, enrollment_status, school_year, 
                    date_of_birth, address, contact_number, email, 
                    created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, 'enrolled', '2024-2025', 
                    '2010-01-01', '123 Student St', '09123456789', ?, 
                    NOW(), NOW()
                )
            ");
            $stmt->execute([
                $lrn, $name[0], $name[1], $section['grade_level'], 
                $gender, $section['id'], $email
            ]);
            
            $studentId = $pdo->lastInsertId();
            
            // Add grades for this student
            $stmt = $pdo->prepare("SELECT id FROM subjects WHERE grade_level = ? AND is_active = 1");
            $stmt->execute([$section['grade_level']]);
            $subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($subjects as $subjectId) {
                // Q1 grade
                $grade = rand(70, 98);
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : 'Fair'));
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentId, $subjectId, $section['adviser_id'], $grade, $remarks]);
                
                // Q2 grade (70% chance)
                if (rand(1, 100) <= 70) {
                    $grade = rand(72, 96);
                    $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : 'Fair'));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 2, '2024-2025', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$studentId, $subjectId, $section['adviser_id'], $grade, $remarks]);
                }
            }
        }
        
        // Update section enrollment count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM students 
            WHERE section_id = ? AND enrollment_status = 'enrolled'
        ");
        $stmt->execute([$section['id']]);
        $totalStudents = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE sections SET current_enrollment = ? WHERE id = ?");
        $stmt->execute([$totalStudents, $section['id']]);
        
        echo "  ✓ Added $newStudents students (Total: $totalStudents)\n";
    }
    
    echo "\n✅ More students and data added successfully!\n";
    echo "Refresh the analytics page to see updated data.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}