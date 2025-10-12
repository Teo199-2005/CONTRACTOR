<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding grades for demo teacher with 8 students...\n\n";
    
    // Find teacher with exactly 8 students
    $stmt = $pdo->query("
        SELECT t.id as teacher_id, t.first_name, t.last_name, t.user_id,
               s.id as section_id, s.section_name, s.grade_level,
               COUNT(st.id) as student_count
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        JOIN students st ON s.id = st.section_id AND st.enrollment_status = 'enrolled'
        WHERE s.is_active = 1
        GROUP BY t.id
        HAVING student_count = 8
    ");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($teachers as $teacher) {
        echo "Processing {$teacher['first_name']} {$teacher['last_name']} - {$teacher['section_name']}\n";
        
        // Get students
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name FROM students 
            WHERE section_id = ? AND enrollment_status = 'enrolled'
        ");
        $stmt->execute([$teacher['section_id']]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get subjects for this grade
        $stmt = $pdo->prepare("SELECT id, subject_name FROM subjects WHERE grade_level = ? AND is_active = 1");
        $stmt->execute([$teacher['grade_level']]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "  Adding grades for " . count($subjects) . " subjects...\n";
        
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                // Q1 grades
                $stmt = $pdo->prepare("
                    SELECT id FROM grades 
                    WHERE student_id = ? AND subject_id = ? AND quarter = 1 AND school_year = '2024-2025'
                ");
                $stmt->execute([$student['id'], $subject['id']]);
                
                if (!$stmt->fetch()) {
                    $grade = rand(75, 95);
                    $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : 'Fair'));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$student['id'], $subject['id'], $teacher['teacher_id'], $grade, $remarks]);
                }
                
                // Q2 grades
                $stmt = $pdo->prepare("
                    SELECT id FROM grades 
                    WHERE student_id = ? AND subject_id = ? AND quarter = 2 AND school_year = '2024-2025'
                ");
                $stmt->execute([$student['id'], $subject['id']]);
                
                if (!$stmt->fetch()) {
                    $grade = rand(78, 93);
                    $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : 'Fair'));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 2, '2024-2025', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$student['id'], $subject['id'], $teacher['teacher_id'], $grade, $remarks]);
                }
            }
        }
        
        echo "  ✓ Grades added successfully\n\n";
    }
    
    echo "✅ Demo teacher grades added!\n";
    echo "Refresh the analytics page to see the data.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}