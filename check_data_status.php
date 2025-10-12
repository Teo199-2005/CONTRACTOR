<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATA STATUS CHECK ===\n\n";
    
    // Check teachers with sections
    echo "TEACHERS WITH SECTIONS:\n";
    $stmt = $pdo->query("
        SELECT t.first_name, t.last_name, s.section_name, s.grade_level, s.current_enrollment
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        WHERE s.is_active = 1
        ORDER BY s.grade_level
    ");
    $teacherSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($teacherSections as $ts) {
        echo "- {$ts['first_name']} {$ts['last_name']}: {$ts['section_name']} ({$ts['current_enrollment']} students)\n";
    }
    
    // Check subjects by grade
    echo "\nSUBJECTS BY GRADE:\n";
    $stmt = $pdo->query("
        SELECT grade_level, COUNT(*) as subject_count, GROUP_CONCAT(subject_name SEPARATOR ', ') as subjects
        FROM subjects 
        WHERE is_active = 1 
        GROUP BY grade_level 
        ORDER BY grade_level
    ");
    $subjectsByGrade = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($subjectsByGrade as $sg) {
        echo "- Grade {$sg['grade_level']}: {$sg['subject_count']} subjects\n";
        echo "  {$sg['subjects']}\n";
    }
    
    // Check grades data
    echo "\nGRADES DATA:\n";
    $stmt = $pdo->query("
        SELECT school_year, quarter, COUNT(*) as grade_count
        FROM grades 
        GROUP BY school_year, quarter 
        ORDER BY school_year, quarter
    ");
    $gradesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($gradesData as $gd) {
        echo "- {$gd['school_year']} Q{$gd['quarter']}: {$gd['grade_count']} grades\n";
    }
    
    // Check sample teacher analytics
    echo "\nSAMPLE TEACHER ANALYTICS (Maria Santos):\n";
    $stmt = $pdo->query("
        SELECT t.first_name, t.last_name, 
               COUNT(DISTINCT st.id) as student_count,
               COUNT(DISTINCT g.id) as grade_count,
               AVG(g.grade) as avg_grade
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        JOIN students st ON s.id = st.section_id
        LEFT JOIN grades g ON st.id = g.student_id AND g.teacher_id = t.id
        WHERE t.first_name = 'Maria' AND t.last_name = 'Santos'
        GROUP BY t.id
    ");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sample) {
        echo "- Students: {$sample['student_count']}\n";
        echo "- Grades: {$sample['grade_count']}\n";
        echo "- Average: " . number_format($sample['avg_grade'] ?? 0, 1) . "%\n";
    }
    
    echo "\n✅ Data check complete!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}