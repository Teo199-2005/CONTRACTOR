<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Fixing all demo teacher accounts...\n\n";
    
    // Find all users with teacher group that don't have teacher records
    $stmt = $pdo->query("
        SELECT DISTINCT u.id, u.username, u.email, u.first_name, u.last_name
        FROM users u 
        JOIN auth_groups_users agu ON u.id = agu.user_id 
        LEFT JOIN teachers t ON u.id = t.user_id
        WHERE agu.group = 'teacher' AND t.id IS NULL
    ");
    $teachersToFix = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($teachersToFix) . " teacher users without teacher records:\n\n";
    
    $teacherNames = [
        'John Smith', 'Jane Doe', 'Robert Johnson', 'Emily Davis', 
        'Michael Brown', 'Sarah Wilson', 'David Miller', 'Lisa Garcia'
    ];
    
    $departments = ['Mathematics', 'Science', 'English', 'Filipino', 'Social Studies', 'MAPEH', 'TLE', 'Values Education'];
    
    foreach ($teachersToFix as $index => $user) {
        $nameIndex = $index % count($teacherNames);
        $nameParts = explode(' ', $teacherNames[$nameIndex]);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1];
        $department = $departments[$index % count($departments)];
        $teacherId = 'T' . str_pad($index + 2, 3, '0', STR_PAD_LEFT); // Start from T002
        
        echo "Creating teacher record for User ID {$user['id']}...\n";
        echo "- Name: $firstName $lastName\n";
        echo "- Department: $department\n";
        echo "- Teacher ID: $teacherId\n";
        
        $stmt = $pdo->prepare("
            INSERT INTO teachers (
                user_id, teacher_id, first_name, last_name, 
                email, contact_number, address, date_of_birth, 
                gender, employment_status, date_hired, department, 
                position, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, 
                ?, '09123456789', '123 Teacher St, City', '1985-01-01', 
                'Male', 'active', '2020-06-01', ?, 
                'High School Teacher', NOW(), NOW()
            )
        ");
        
        $email = strtolower($firstName . '.' . $lastName) . '@lphs.edu';
        
        $stmt->execute([
            $user['id'], $teacherId, $firstName, $lastName,
            $email, $department
        ]);
        
        $newTeacherId = $pdo->lastInsertId();
        echo "✓ Created teacher record with ID: $newTeacherId\n\n";
    }
    
    // Also create sections for some teachers
    echo "Creating sample sections...\n";
    
    $sections = [
        ['Grade 7 - Newton', 7],
        ['Grade 8 - Einstein', 8], 
        ['Grade 9 - Galileo', 9],
        ['Grade 10 - Darwin', 10]
    ];
    
    // Get some teacher IDs
    $stmt = $pdo->query("SELECT id FROM teachers ORDER BY id LIMIT 4");
    $teacherIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($sections as $index => $section) {
        $adviserId = $teacherIds[$index] ?? null;
        
        $stmt = $pdo->prepare("
            SELECT id FROM sections WHERE section_name = ? AND school_year = '2024-2025'
        ");
        $stmt->execute([$section[0]]);
        $existingSection = $stmt->fetch();
        
        if (!$existingSection && $adviserId) {
            $stmt = $pdo->prepare("
                INSERT INTO sections (
                    section_name, grade_level, max_capacity, 
                    current_enrollment, adviser_id, school_year, 
                    is_active, created_at, updated_at
                ) VALUES (?, ?, 40, 0, ?, '2024-2025', 1, NOW(), NOW())
            ");
            $stmt->execute([$section[0], $section[1], $adviserId]);
            echo "✓ Created section: {$section[0]} (Adviser ID: $adviserId)\n";
        }
    }
    
    echo "\n=== ALL DEMO TEACHERS FIXED ===\n";
    echo "All teacher users now have proper teacher records.\n";
    echo "Teacher analytics should work for all demo accounts.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}