<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";

    // Check if we have any users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Found $userCount users in database\n";

    // Create teacher user if none exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'teacher1'");
    $stmt->execute();
    $teacherUser = $stmt->fetch();

    if (!$teacherUser) {
        echo "Creating teacher user...\n";
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, active, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())");
        $stmt->execute(['teacher1', 'teacher1@lphs.edu', password_hash('password123', PASSWORD_DEFAULT)]);
        $teacherUserId = $pdo->lastInsertId();
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
            INSERT INTO teachers (user_id, employee_id, first_name, last_name, middle_name, email, phone, address, date_of_birth, gender, employment_status, hire_date, department, position, created_at, updated_at) 
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

    // Create students
    echo "Creating students...\n";
    $students = [
        ['Juan', 'Dela Cruz', 'Male'],
        ['Maria', 'Santos', 'Female'],
        ['Pedro', 'Garcia', 'Male'],
        ['Ana', 'Rodriguez', 'Female'],
        ['Jose', 'Martinez', 'Male'],
        ['Carmen', 'Lopez', 'Female'],
        ['Miguel', 'Hernandez', 'Male'],
        ['Sofia', 'Gonzalez', 'Female'],
        ['Carlos', 'Perez', 'Male'],
        ['Isabella', 'Torres', 'Female'],
        ['Luis', 'Morales', 'Male'],
        ['Elena', 'Vargas', 'Female'],
    ];
    
    $studentIds = [];
    foreach ($students as $index => $student) {
        $studentId = 'STU' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $existingStudent = $stmt->fetch();
        
        if (!$existingStudent) {
            $stmt = $pdo->prepare("
                INSERT INTO students (student_id, first_name, last_name, grade_level, gender, section_id, enrollment_status, school_year, date_of_birth, address, contact_number, email, created_at, updated_at) 
                VALUES (?, ?, ?, 7, ?, ?, 'enrolled', '2024-2025', '2010-01-01', '123 Student St, City', '09123456789', ?, NOW(), NOW())
            ");
            $email = strtolower($student[0] . '.' . $student[1]) . '@student.lphs.edu';
            $stmt->execute([$studentId, $student[0], $student[1], $student[2], $sectionId, $email]);
            $studentIds[] = $pdo->lastInsertId();
            echo "Created student: {$student[0]} {$student[1]}\n";
        } else {
            $studentIds[] = $existingStudent['id'];
            echo "Found existing student: {$student[0]} {$student[1]}\n";
        }
    }

    // Update section enrollment count
    $stmt = $pdo->prepare("UPDATE sections SET current_enrollment = ? WHERE id = ?");
    $stmt->execute([count($studentIds), $sectionId]);

    // Create grades for Quarter 1
    echo "Creating grades for Quarter 1...\n";
    foreach ($studentIds as $studentDbId) {
        foreach ($subjectIds as $subjectId) {
            $stmt = $pdo->prepare("
                SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND quarter = 1 AND school_year = '2024-2025'
            ");
            $stmt->execute([$studentDbId, $subjectId]);
            $existingGrade = $stmt->fetch();
            
            if (!$existingGrade) {
                // Generate realistic grades with some variety
                $baseGrade = rand(75, 92);
                $variation = rand(-8, 8);
                $grade = max(65, min(98, $baseGrade + $variation));
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 1, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentDbId, $subjectId, $teacherId, $grade, $remarks]);
            }
        }
    }

    // Create some grades for Quarter 2 (partial)
    echo "Creating partial grades for Quarter 2...\n";
    $q2Count = 0;
    foreach (array_slice($studentIds, 0, 8) as $studentDbId) { // Only first 8 students
        foreach (array_slice($subjectIds, 0, 3) as $subjectId) { // Only first 3 subjects
            $stmt = $pdo->prepare("
                SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND quarter = 2 AND school_year = '2024-2025'
            ");
            $stmt->execute([$studentDbId, $subjectId]);
            $existingGrade = $stmt->fetch();
            
            if (!$existingGrade) {
                $baseGrade = rand(78, 94);
                $variation = rand(-6, 6);
                $grade = max(70, min(96, $baseGrade + $variation));
                $remarks = $grade >= 90 ? 'Excellent' : ($grade >= 85 ? 'Very Good' : ($grade >= 80 ? 'Good' : ($grade >= 75 ? 'Fair' : 'Needs Improvement')));
                
                $stmt = $pdo->prepare("
                    INSERT INTO grades (student_id, subject_id, teacher_id, grade, quarter, school_year, remarks, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 2, '2024-2025', ?, NOW(), NOW())
                ");
                $stmt->execute([$studentDbId, $subjectId, $teacherId, $grade, $remarks]);
                $q2Count++;
            }
        }
    }
    
    echo "Created $q2Count Quarter 2 grades\n";
    echo "\nSample data setup complete!\n";
    echo "Teacher Analytics Data Summary:\n";
    echo "- Teacher: Maria Santos (teacher1)\n";
    echo "- Section: Grade 7 - Einstein (" . count($studentIds) . " students)\n";
    echo "- Subjects: " . count($subjectIds) . " subjects\n";
    echo "- Grades: Full Q1 data, partial Q2 data\n\n";
    echo "You can now log in as:\n";
    echo "Username: teacher1\n";
    echo "Password: password123\n";
    echo "Then go to Teacher Portal > Analytics to see the data and charts!\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}