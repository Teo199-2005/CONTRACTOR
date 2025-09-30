<?php
// Complete Data Setup - Adds ALL missing data
echo "LPHS SMS Complete Data Setup\n";
echo "=============================\n\n";

// Database configuration - update these values
$host = 'localhost';
$username = 'root';  // Change if different
$password = '';      // Change if different
$database = 'lphs_sms'; // Change if different

function tableExists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return (bool) $stmt->fetchColumn();
}

function tableHasColumn(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        return (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully!\n\n";

    // Detect users schema (Shield vs simple)
    $hasUsers = tableExists($pdo, 'users');
    $usersHasPassword = $hasUsers && tableHasColumn($pdo, 'users', 'password');

    // Create missing domain tables if they don't exist (do NOT touch users schema)
    echo "Creating missing tables...\n";

    // Teachers table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS teachers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            teacher_id VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            gender ENUM('Male','Female') NOT NULL,
            email VARCHAR(255) NOT NULL,
            employment_status ENUM('active','inactive','resigned','terminated') DEFAULT 'active',
            specialization VARCHAR(100) NULL,
            contact_number VARCHAR(20) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Parents table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS parents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            contact_number VARCHAR(20) NULL,
            address TEXT NULL,
            occupation VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Students table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            gender ENUM('Male','Female') NOT NULL,
            date_of_birth DATE NOT NULL,
            email VARCHAR(255) NULL,
            enrollment_status ENUM('pending','approved','rejected','enrolled','graduated','dropped') DEFAULT 'pending',
            grade_level INT NULL,
            school_year VARCHAR(20) NULL,
            student_id VARCHAR(20) UNIQUE NULL,
            section_id INT NULL,
            address TEXT NULL,
            contact_number VARCHAR(20) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Student-Parent relationships
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS student_parents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            parent_id INT NOT NULL,
            relationship VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_student_parent (student_id, parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Grades table (ensure minimal fields)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grades (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            subject_id INT NOT NULL,
            teacher_id INT NOT NULL,
            school_year VARCHAR(20) NOT NULL,
            quarter INT NOT NULL,
            grade DECIMAL(5,2) NULL,
            remarks VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Ensure minimal subjects exist for grades and charts
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subjects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subject_code VARCHAR(20) NOT NULL,
            subject_name VARCHAR(100) NOT NULL,
            grade_level INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "✅ All tables created/verified!\n\n";

    // Add sample data
    echo "Adding comprehensive sample data...\n\n";

    // Helper: create user if simple users schema (with password) exists
    $createUser = function(string $email, string $plainPassword) use ($pdo, $usersHasPassword): ?int {
        if (! $usersHasPassword) {
            return null; // Shield or different schema: skip users
        }
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password, active) VALUES (?, ?, 1)");
        $stmt->execute([$email, password_hash($plainPassword, PASSWORD_DEFAULT)]);
        $id = (int) $pdo->lastInsertId();
        // If user already existed, fetch id
        if ($id === 0) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['id'] : null;
        }
        return $id;
    };

    $subjects = [
        ['ENG7','English 7',7],['MATH7','Mathematics 7',7],['SCI7','Science 7',7],
        ['ENG8','English 8',8],['MATH8','Mathematics 8',8],['SCI8','Science 8',8]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO subjects (subject_code, subject_name, grade_level) VALUES (?, ?, ?)");
    foreach ($subjects as $s) { $stmt->execute($s); }

    // 1. Teachers (user_id may be NULL)
    echo "1. Creating teachers...\n";
    $teachers = [
        ['teacher1@lphs.edu', 'DemoPass123!', 'T001', 'Maria', 'Santos', 'Female', 'Mathematics'],
        ['teacher2@lphs.edu', 'DemoPass123!', 'T002', 'Juan', 'Cruz', 'Male', 'Science'],
        ['teacher3@lphs.edu', 'DemoPass123!', 'T003', 'Ana', 'Reyes', 'Female', 'English'],
        ['teacher4@lphs.edu', 'DemoPass123!', 'T004', 'Roberto', 'Garcia', 'Male', 'History'],
        ['teacher5@lphs.edu', 'DemoPass123!', 'T005', 'Carmen', 'Lopez', 'Female', 'Physical Education']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO teachers (user_id, teacher_id, first_name, last_name, gender, email, specialization) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($teachers as $t) {
        $userId = $createUser($t[0], $t[1]);
        $stmt->execute([$userId, $t[2], $t[3], $t[4], $t[5], $t[0], $t[6]]);
    }
    echo "✅ Added 5 teachers\n";

    // 2. Students (user_id may be NULL)
    echo "2. Creating students...\n";
    $students = [
        // Enrolled students
        ['student1@lphs.edu', 'DemoPass123!', '2024-001', 'Pedro', 'Garcia', 'Male', '2010-03-15', 'enrolled', 7, 1],
        ['student2@lphs.edu', 'DemoPass123!', '2024-002', 'Maria', 'Lopez', 'Female', '2010-07-22', 'enrolled', 7, 1],
        ['student3@lphs.edu', 'DemoPass123!', '2024-003', 'Jose', 'Martinez', 'Male', '2009-11-08', 'enrolled', 8, 4],
        ['student4@lphs.edu', 'DemoPass123!', '2024-004', 'Carmen', 'Rodriguez', 'Female', '2009-05-12', 'enrolled', 8, 4],
        ['student5@lphs.edu', 'DemoPass123!', '2024-005', 'Antonio', 'Hernandez', 'Male', '2008-09-30', 'enrolled', 9, 7],
        ['student6@lphs.edu', 'DemoPass123!', '2024-006', 'Isabella', 'Santos', 'Female', '2008-12-03', 'enrolled', 9, 7],
        ['student7@lphs.edu', 'DemoPass123!', '2024-007', 'Miguel', 'Cruz', 'Male', '2007-06-18', 'enrolled', 10, 10],
        ['student8@lphs.edu', 'DemoPass123!', '2024-008', 'Sofia', 'Reyes', 'Female', '2007-04-25', 'enrolled', 10, 10],
        // Pending
        ['student9@lphs.edu', 'DemoPass123!', '2024-009', 'Carlos', 'Torres', 'Male', '2010-01-10', 'pending', 7, 2],
        ['student10@lphs.edu', 'DemoPass123!', '2024-010', 'Elena', 'Morales', 'Female', '2010-08-15', 'pending', 7, 2],
        ['student11@lphs.edu', 'DemoPass123!', '2024-011', 'Diego', 'Flores', 'Male', '2009-03-20', 'pending', 8, 5],
        ['student12@lphs.edu', 'DemoPass123!', '2024-012', 'Valentina', 'Gutierrez', 'Female', '2009-10-05', 'pending', 8, 5]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO students (user_id, student_id, first_name, last_name, gender, date_of_birth, email, enrollment_status, grade_level, section_id, school_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '2024-2025')");
    foreach ($students as $s) {
        $userId = $createUser($s[0], $s[1]);
        $stmt->execute([$userId, $s[2], $s[3], $s[4], $s[5], $s[6], $s[0], $s[7], $s[8], $s[9]]);
    }
    echo "✅ Added 12 students (8 enrolled, 4 pending)\n";

    // 3. Parents (user_id may be NULL)
    echo "3. Creating parents...\n";
    $parents = [
        ['parent1@lphs.edu', 'DemoPass123!', 'Roberto', 'Garcia', 'Engineer'],
        ['parent2@lphs.edu', 'DemoPass123!', 'Elena', 'Lopez', 'Teacher'],
        ['parent3@lphs.edu', 'DemoPass123!', 'Carlos', 'Martinez', 'Doctor'],
        ['parent4@lphs.edu', 'DemoPass123!', 'Ana', 'Rodriguez', 'Nurse'],
        ['parent5@lphs.edu', 'DemoPass123!', 'Miguel', 'Hernandez', 'Lawyer']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO parents (user_id, first_name, last_name, email, occupation) VALUES (?, ?, ?, ?, ?)");
    foreach ($parents as $p) {
        $userId = $createUser($p[0], $p[1]);
        $stmt->execute([$userId, $p[2], $p[3], $p[0], $p[4]]);
    }
    echo "✅ Added 5 parents\n";

    // 4. Relationships (use existing ids 1..12 heuristically)
    echo "4. Linking students to parents...\n";
    $links = [
        [1,1,'Father'],[2,2,'Mother'],[3,3,'Father'],[4,4,'Mother'],[5,5,'Father'],
        [6,1,'Father'],[7,2,'Mother'],[8,3,'Father'],[9,4,'Mother'],[10,5,'Father']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO student_parents (student_id, parent_id, relationship) VALUES (?, ?, ?)");
    foreach ($links as $rel) { $stmt->execute($rel); }
    echo "✅ Linked students and parents\n";

    // 5. Grades (ensure teacher_id and school_year present)
    echo "5. Adding grades...\n";
    $grades = [
        // student_id, subject_id, teacher_id, school_year, quarter, grade, remarks
        [1,1,1,'2024-2025',1,85,'Passed'],[1,2,1,'2024-2025',1,88,'Passed'],[1,3,1,'2024-2025',1,82,'Passed'],
        [2,1,1,'2024-2025',1,90,'Passed'],[2,2,1,'2024-2025',1,87,'Passed'],[2,3,1,'2024-2025',1,89,'Passed'],
        [3,4,2,'2024-2025',1,83,'Passed'],[3,5,2,'2024-2025',1,86,'Passed'],[3,6,2,'2024-2025',1,81,'Passed'],
        [4,4,2,'2024-2025',1,88,'Passed'],[4,5,2,'2024-2025',1,85,'Passed'],[4,6,2,'2024-2025',1,87,'Passed']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO grades (student_id, subject_id, teacher_id, school_year, quarter, grade, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($grades as $g) { $stmt->execute($g); }
    echo "✅ Added 12 grade records\n";

    // 6. Enrollment documents (handle schema variants)
    echo "6. Adding enrollment documents...\n";
    $hasDocName = tableHasColumn($pdo, 'enrollment_documents', 'document_name');
    $hasFileNameCol = tableHasColumn($pdo, 'enrollment_documents', 'file_name');
    $hasIsVerified = tableHasColumn($pdo, 'enrollment_documents', 'is_verified');
    $hasStatus = tableHasColumn($pdo, 'enrollment_documents', 'status');
    $hasFileSize = tableHasColumn($pdo, 'enrollment_documents', 'file_size');
    $hasMimeType = tableHasColumn($pdo, 'enrollment_documents', 'mime_type');

    $docs = [
        [1,'birth_certificate','birth_cert_pedro_garcia.pdf','uploads/documents/birth_cert_pedro_garcia.pdf', true],
        [1,'report_card','report_card_pedro_garcia.pdf','uploads/documents/report_card_pedro_garcia.pdf', true],
        [2,'birth_certificate','birth_cert_maria_lopez.pdf','uploads/documents/birth_cert_maria_lopez.pdf', true],
        [2,'report_card','report_card_maria_lopez.pdf','uploads/documents/report_card_maria_lopez.pdf', false],
        [9,'birth_certificate','birth_cert_carlos_torres.pdf','uploads/documents/birth_cert_carlos_torres.pdf', false]
    ];

    foreach ($docs as $d) {
        [$studentId, $type, $name, $path, $verified] = $d;
        // Build dynamic insert based on columns
        $cols = ['student_id','document_type','file_path'];
        $params = [$studentId, $type, $path];
        if ($hasDocName) { $cols[] = 'document_name'; $params[] = $name; }
        if ($hasFileNameCol && ! $hasDocName) { $cols[] = 'file_name'; $params[] = $name; }
        if ($hasFileSize) { $cols[] = 'file_size'; $params[] = 123456; }
        if ($hasMimeType) { $cols[] = 'mime_type'; $params[] = 'application/pdf'; }
        if ($hasIsVerified) { $cols[] = 'is_verified'; $params[] = $verified ? 1 : 0; }
        if ($hasStatus && ! $hasIsVerified) { $cols[] = 'status'; $params[] = $verified ? 'approved' : 'pending'; }

        $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
        $sql = 'INSERT IGNORE INTO enrollment_documents (' . implode(',', $cols) . ') VALUES (' . $placeholders . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    echo "✅ Added enrollment documents\n";

    // Final counts
    echo "\nFinal Database Status:\n";
    echo "======================\n";
    $count = $pdo->query("SELECT COUNT(*) FROM teachers WHERE employment_status = 'active'")->fetchColumn();
    echo "Active Teachers: $count\n";
    $count = $pdo->query("SELECT COUNT(*) FROM students WHERE enrollment_status = 'enrolled'")->fetchColumn();
    echo "Enrolled Students: $count\n";
    $count = $pdo->query("SELECT COUNT(*) FROM students WHERE enrollment_status = 'pending'")->fetchColumn();
    echo "Pending Enrollments: $count\n";

    echo "\nEnrollment by Grade Level:\n";
    $stmt = $pdo->query("SELECT grade_level, COUNT(*) AS total FROM students GROUP BY grade_level ORDER BY grade_level");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Grade {$row['grade_level']}: {$row['total']}\n";
    }

    echo "\n✅ Complete setup finished successfully!\n";
    echo "Dashboard widgets and chart should now show data.\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "\nPlease check your database configuration:\n";
    echo "- Host: $host\n";
    echo "- Database: $database\n";
    echo "- Username: $username\n";
    echo "- Password: [hidden]\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
