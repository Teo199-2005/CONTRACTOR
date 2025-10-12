<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";

    // Check if teacher user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'teacher1'");
    $stmt->execute();
    $teacherUser = $stmt->fetch();

    if (!$teacherUser) {
        echo "Creating teacher user...\n";
        
        // Create user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, first_name, last_name, active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
        $stmt->execute(['teacher1', 'teacher1@lphs.edu', 'Maria', 'Santos']);
        $teacherUserId = $pdo->lastInsertId();
        
        // Create auth identity (password)
        $stmt = $pdo->prepare("INSERT INTO auth_identities (user_id, type, name, secret, created_at, updated_at) VALUES (?, 'email_password', ?, ?, NOW(), NOW())");
        $stmt->execute([$teacherUserId, 'teacher1@lphs.edu', password_hash('password123', PASSWORD_DEFAULT)]);
        
        // Add to teacher group
        $stmt = $pdo->prepare("INSERT INTO auth_groups_users (user_id, `group`, created_at) VALUES (?, 'teacher', NOW())");
        $stmt->execute([$teacherUserId]);
        
        echo "Created teacher user with ID: $teacherUserId\n";
    } else {
        $teacherUserId = $teacherUser['id'];
        echo "Found existing teacher user with ID: $teacherUserId\n";
    }

    // Check if teacher record exists
    $stmt = $pdo->prepare("SELECT id FROM teachers WHERE user_id = ?");
    $stmt->execute([$teacherUserId]);
    $teacherRecord = $stmt->fetch();
    
    if (!$teacherRecord) {
        echo "Creating teacher record...\n";
        $stmt = $pdo->prepare("
            INSERT INTO teachers (user_id, teacher_id, first_name, last_name, middle_name, email, contact_number, address, date_of_birth, gender, employment_status, date_hired, department, position, created_at, updated_at) 
            VALUES (?, 'T001', 'Maria', 'Santos', 'Cruz', 'teacher1@lphs.edu', '09123456789', '123 Teacher St, City', '1985-05-15', 'Female', 'active', '2020-06-01', 'Mathematics', 'Senior High School Teacher', NOW(), NOW())
        ");
        $stmt->execute([$teacherUserId]);
        $teacherId = $pdo->lastInsertId();
        echo "Created teacher record with ID: $teacherId\n";
    } else {
        $teacherId = $teacherRecord['id'];
        echo "Found existing teacher record with ID: $teacherId\n";
    }

    // Create section
    echo "Creating section...\n";
    $stmt = $pdo->prepare("SELECT id FROM sections WHERE section_name = 'Grade 7 - Einstein' AND school_year = '2024-2025'");
    $stmt->execute();
    $existingSection = $stmt->fetch();
    
    if (!$existingSection) {
        $stmt = $pdo->prepare("
            INSERT INTO sections (section_name, grade_level, max_capacity, current_enrollment, adviser_id, school_year, is_active, created_at, updated_at) 
            VALUES ('Grade 7 - Einstein', 7, 40, 0, ?, '2024-2025', 1, NOW(), NOW())
        ");
        $stmt->execute([$teacherId]);
        $sectionId = $pdo->lastInsertId();
        echo "Created section with ID: $sectionId\n";
    } else {
        $sectionId = $existingSection['id'];
        // Update adviser if not set
        $stmt = $pdo->prepare("UPDATE sections SET adviser_id = ? WHERE id = ? AND adviser_id IS NULL");
        $stmt->execute([$teacherId, $sectionId]);
        echo "Found existing section with ID: $sectionId\n";
    }

    // Create subjects
    echo "Creating subjects...\n";
    $subjects = [
        ['Mathematics 7', 'MATH7', 7],
        ['Science 7', 'SCI7', 7],
        ['English 7', 'ENG7', 7],
        ['Filipino 7', 'FIL7', 7],
        ['Araling Panlipunan 7', 'AP7', 7],
        ['MAPEH 7', 'MAPEH7', 7],
    ];
    
    $subjectIds = [];
    foreach ($subjects as $subject) {
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
        $stmt->execute([$subject[1]]);
        $existingSubject = $stmt->fetch();
        
        if (!$existingSubject) {
            $stmt = $pdo->prepare("
                INSERT INTO subjects (subject_name, subject_code, grade_level, is_active, created_at, updated_at) 
                VALUES (?, ?, ?, 1, NOW(), NOW())
            ");
            $stmt->execute($subject);
            $subjectIds[] = $pdo->lastInsertId();
            echo "Created subject: {$subject[0]}\n";
        } else {
            $subjectIds[] = $existingSubject['id'];
            echo "Found existing subject: {$subject[0]}\n";
        }
    }

    // Create students with varied performance levels
    echo "Creating students...\n";
    $students = [
        ['Juan', 'Dela Cruz', 'Male', 'high'],      // High performer
        ['Maria', 'Santos', 'Female', 'high'],     // High performer
        ['Pedro', 'Garcia', 'Male', 'medium'],     // Medium performer
        ['Ana', 'Rodriguez', 'Female', 'high'],    // High performer
        ['Jose', 'Martinez', 'Male', 'low'],       // Low performer
        ['Carmen', 'Lopez', 'Female', 'medium'],   // Medium performer
        ['Miguel', 'Hernandez', 'Male', 'medium'], // Medium performer
        ['Sofia', 'Gonzalez', 'Female', 'high'],   // High performer
        ['Carlos', 'Perez', 'Male', 'low'],        // Low performer
        ['Isabella', 'Torres', 'Female', 'medium'], // Medium performer
        ['Luis', 'Morales', 'Male', 'high'],       // High performer
        ['Elena', 'Vargas', 'Female', 'low'],      // Low performer
        ['Diego', 'Ramos', 'Male', 'medium'],      // Medium performer
        ['Lucia', 'Jimenez', 'Female', 'high'],    // High performer
        ['Roberto', 'Castillo', 'Male', 'medium'], // Medium performer
    ];
    
    $studentIds = [];
    foreach ($students as $index => $student) {
        $lrn = '1234567890' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("SELECT id FROM students WHERE lrn = ?");
        $stmt->execute([$lrn]);
        $existingStudent = $stmt->fetch();
        
        if (!$existingStudent) {
            $stmt = $pdo->prepare("
                INSERT INTO students (lrn, first_name, last_name, grade_level, gender, section_id, enrollment_status, school_year, date_of_birth, address, contact_number, email, created_at, updated_at) 
                VALUES (?, ?, ?, 7, ?, ?, 'enrolled', '2024-2025', '2010-01-01', '123 Student St, City', '09123456789', ?, NOW(), NOW())
            ");
            $email = strtolower($student[0] . '.' . $student[1]) . '@student.lphs.edu';
            $stmt->execute([$lrn, $student[0], $student[1], $student[2], $sectionId, $email]);
            $studentIds[] = ['id' => $pdo->lastInsertId(), 'level' => $student[3]];
            echo "Created student: {$student[0]} {$student[1]} ({$student[3]} performer)\n";
        } else {
            $studentIds[] = ['id' => $existingStudent['id'], 'level' => $student[3]];
            echo "Found existing student: {$student[0]} {$student[1]}\n";
        }
    }

    // Update section enrollment count
    $stmt = $pdo->prepare("UPDATE sections SET current_enrollment = ? WHERE id = ?");
    $stmt->execute([count($studentIds), $sectionId]);

    // Create realistic grades based on performance levels
    echo "Creating realistic grades...\n";
    
    $gradeRanges = [
        'high' => [85, 95],    // High performers: 85-95
        'medium' => [75, 84],  // Medium performers: 75-84
        'low' => [65, 74]      // Low performers: 65-74
    ];
    
    foreach ($studentIds as $student) {
        $studentDbId = $student['id'];
        $level = $student['level'];
        $range = $gradeRanges[$level];
        
        foreach ($subjectIds as $subjectId) {
            // Quarter 1 grades
            $stmt = $pdo->prepare("
                SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND quarter = 1 AND school_year = '2024-2025'
            ");
            $stmt->execute([$studentDbId, $subjectId]);
            $existingGrade = $stmt->fetch();
            
            if (!$existingGrade) {
                $grade = rand($range[0], $range[1]);
                // Add some variation
                $grade += rand(-3, 3);
                $grade = max(60, min(98, $grade));
                
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentDbId, $subjectId, $teacherId, $grade, $remarks]);
            }
            
            // Quarter 2 grades (with slight improvement/decline)
            $stmt = $pdo->prepare("
                SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND quarter = 2 AND school_year = '2024-2025'
            ");
            $stmt->execute([$studentDbId, $subjectId]);
            $existingGrade = $stmt->fetch();
            
            if (!$existingGrade) {
                $grade = rand($range[0], $range[1]);
                // Add trend (slight improvement for most students)
                $trend = rand(-2, 4); // Slight positive bias
                $grade += $trend;
                $grade = max(60, min(98, $grade));
                
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 2, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentDbId, $subjectId, $teacherId, $grade, $remarks]);
            }
        }
    }
    
    echo "\n=== TEACHER ANALYTICS DATA SETUP COMPLETE ===\n";
    echo "Teacher: Maria Santos (teacher1)\n";
    echo "Section: Grade 7 - Einstein (" . count($studentIds) . " students)\n";
    echo "Subjects: " . count($subjectIds) . " subjects\n";
    echo "Performance Distribution:\n";
    echo "- High Performers: " . count(array_filter($studentIds, fn($s) => $s['level'] === 'high')) . " students\n";
    echo "- Medium Performers: " . count(array_filter($studentIds, fn($s) => $s['level'] === 'medium')) . " students\n";
    echo "- Low Performers: " . count(array_filter($studentIds, fn($s) => $s['level'] === 'low')) . " students\n";
    echo "Grades: Complete Q1 and Q2 data with realistic performance trends\n\n";
    echo "LOGIN CREDENTIALS:\n";
    echo "Username: teacher1\n";
    echo "Password: password123\n\n";
    echo "Go to: Teacher Portal > Analytics\n";
    echo "- View comprehensive analytics dashboard\n";
    echo "- Export PDF reports\n";
    echo "- See working charts and data visualizations\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}