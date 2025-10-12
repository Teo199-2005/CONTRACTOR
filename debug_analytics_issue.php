<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING ANALYTICS ISSUE ===\n\n";
    
    // Find the demo teacher with 8 students
    $stmt = $pdo->query("
        SELECT t.id as teacher_id, t.user_id, t.first_name, t.last_name,
               s.id as section_id, s.section_name, s.grade_level,
               COUNT(st.id) as student_count
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        LEFT JOIN students st ON s.id = st.section_id AND st.enrollment_status = 'enrolled'
        WHERE s.is_active = 1
        GROUP BY t.id, s.id
        HAVING student_count = 8
        LIMIT 1
    ");
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher) {
        echo "No teacher with exactly 8 students found!\n";
        exit;
    }
    
    echo "Found teacher: {$teacher['first_name']} {$teacher['last_name']}\n";
    echo "Teacher ID: {$teacher['teacher_id']}\n";
    echo "User ID: {$teacher['user_id']}\n";
    echo "Section: {$teacher['section_name']}\n";
    echo "Students: {$teacher['student_count']}\n\n";
    
    // Check grades for this teacher
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as grade_count, AVG(grade) as avg_grade
        FROM grades g
        JOIN students s ON g.student_id = s.id
        WHERE s.section_id = ? AND g.school_year = '2024-2025'
    ");
    $stmt->execute([$teacher['section_id']]);
    $gradeInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Grades found: {$gradeInfo['grade_count']}\n";
    echo "Average grade: " . number_format($gradeInfo['avg_grade'] ?? 0, 1) . "%\n\n";
    
    // Check subjects
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT g.subject_id) as subject_count
        FROM grades g
        JOIN students s ON g.student_id = s.id
        WHERE s.section_id = ? AND g.school_year = '2024-2025'
    ");
    $stmt->execute([$teacher['section_id']]);
    $subjectCount = $stmt->fetchColumn();
    
    echo "Subjects with grades: $subjectCount\n\n";
    
    if ($gradeInfo['grade_count'] == 0) {
        echo "❌ NO GRADES FOUND - Adding grades now...\n";
        
        // Get students
        $stmt = $pdo->prepare("SELECT id FROM students WHERE section_id = ? AND enrollment_status = 'enrolled'");
        $stmt->execute([$teacher['section_id']]);
        $students = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get subjects
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE grade_level = ? AND is_active = 1");
        $stmt->execute([$teacher['grade_level']]);
        $subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($students as $studentId) {
            foreach ($subjects as $subjectId) {
                $grade = rand(75, 95);
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : 'Good');
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentId, $subjectId, $teacher['teacher_id'], $grade, $remarks]);
            }
        }
        
        echo "✅ Grades added! Refresh analytics page.\n";
    } else {
        echo "✅ Grades exist - analytics should work!\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}