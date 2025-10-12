<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding grades for all teachers...\n\n";
    
    // Get all teachers with their sections and students
    $stmt = $pdo->query("
        SELECT t.id as teacher_id, t.first_name, t.last_name,
               s.id as section_id, s.section_name, s.grade_level,
               COUNT(st.id) as student_count
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        LEFT JOIN students st ON s.id = st.section_id AND st.enrollment_status = 'enrolled'
        WHERE s.is_active = 1
        GROUP BY t.id, s.id
    ");
    $teacherSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get subjects for each grade level
    $stmt = $pdo->query("SELECT id, subject_name, grade_level FROM subjects WHERE is_active = 1");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $subjectsByGrade = [];
    foreach ($subjects as $subject) {
        $subjectsByGrade[$subject['grade_level']][] = $subject;
    }
    
    foreach ($teacherSections as $ts) {
        echo "Processing {$ts['first_name']} {$ts['last_name']} - {$ts['section_name']} ({$ts['student_count']} students)\n";
        
        if ($ts['student_count'] == 0) {
            echo "  No students found, skipping...\n";
            continue;
        }
        
        // Get students in this section
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name FROM students 
            WHERE section_id = ? AND enrollment_status = 'enrolled'
        ");
        $stmt->execute([$ts['section_id']]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get subjects for this grade level
        $gradeSubjects = $subjectsByGrade[$ts['grade_level']] ?? [];
        
        if (empty($gradeSubjects)) {
            echo "  No subjects found for grade {$ts['grade_level']}, skipping...\n";
            continue;
        }
        
        echo "  Adding grades for " . count($gradeSubjects) . " subjects...\n";
        
        foreach ($students as $student) {
            foreach ($gradeSubjects as $subject) {
                // Check if grade already exists
                $stmt = $pdo->prepare("
                    SELECT id FROM grades 
                    WHERE student_id = ? AND subject_id = ? AND quarter = 1 AND school_year = '2024-2025'
                ");
                $stmt->execute([$student['id'], $subject['id']]);
                $existingGrade = $stmt->fetch();
                
                if (!$existingGrade) {
                    // Generate realistic grade based on student performance level
                    $baseGrade = rand(75, 92);
                    $variation = rand(-8, 8);
                    $grade = max(65, min(98, $baseGrade + $variation));
                    $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$student['id'], $subject['id'], $ts['teacher_id'], $grade, $remarks]);
                }
                
                // Add Quarter 2 grades (partial)
                $stmt = $pdo->prepare("
                    SELECT id FROM grades 
                    WHERE student_id = ? AND subject_id = ? AND quarter = 2 AND school_year = '2024-2025'
                ");
                $stmt->execute([$student['id'], $subject['id']]);
                $existingGrade = $stmt->fetch();
                
                if (!$existingGrade && rand(1, 100) <= 70) { // 70% chance of having Q2 grade
                    $baseGrade = rand(78, 94);
                    $variation = rand(-6, 6);
                    $grade = max(70, min(96, $baseGrade + $variation));
                    $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 2, '2024-2025', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$student['id'], $subject['id'], $ts['teacher_id'], $grade, $remarks]);
                }
            }
        }
        
        echo "  ✓ Grades added successfully\n";
    }
    
    echo "\n✅ All grades added successfully!\n";
    echo "Teacher analytics should now show data for all teachers.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}