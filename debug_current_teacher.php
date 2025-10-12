<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING TEACHER LOGIN ISSUE ===\n\n";
    
    // Check all teacher users and their records
    echo "ALL TEACHER USERS:\n";
    $stmt = $pdo->query("
        SELECT u.id as user_id, u.username, u.first_name, u.last_name,
               t.id as teacher_id, t.first_name as t_first, t.last_name as t_last
        FROM users u
        JOIN auth_groups_users agu ON u.id = agu.user_id
        LEFT JOIN teachers t ON u.id = t.user_id
        WHERE agu.group = 'teacher'
        ORDER BY u.id
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "- User ID {$user['user_id']}: {$user['username']} ({$user['first_name']} {$user['last_name']})\n";
        if ($user['teacher_id']) {
            echo "  ✓ Teacher record: ID {$user['teacher_id']} ({$user['t_first']} {$user['t_last']})\n";
            
            // Check if this teacher has a section
            $stmt = $pdo->prepare("
                SELECT s.id, s.section_name, s.current_enrollment
                FROM sections s 
                WHERE s.adviser_id = ? AND s.is_active = 1
            ");
            $stmt->execute([$user['teacher_id']]);
            $section = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($section) {
                echo "  ✓ Section: {$section['section_name']} ({$section['current_enrollment']} students)\n";
            } else {
                echo "  ✗ No section assigned\n";
            }
        } else {
            echo "  ✗ No teacher record\n";
        }
        echo "\n";
    }
    
    // Show which teacher has the most data
    echo "TEACHER WITH MOST STUDENTS (for testing):\n";
    $stmt = $pdo->query("
        SELECT t.id, t.first_name, t.last_name, t.user_id,
               s.section_name, s.current_enrollment,
               u.username
        FROM teachers t
        JOIN sections s ON t.id = s.adviser_id
        JOIN users u ON t.user_id = u.id
        WHERE s.is_active = 1
        ORDER BY s.current_enrollment DESC
        LIMIT 1
    ");
    $bestTeacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($bestTeacher) {
        echo "- {$bestTeacher['first_name']} {$bestTeacher['last_name']}\n";
        echo "- Username: {$bestTeacher['username']}\n";
        echo "- Section: {$bestTeacher['section_name']} ({$bestTeacher['current_enrollment']} students)\n";
        echo "- User ID: {$bestTeacher['user_id']}\n";
        echo "- Teacher ID: {$bestTeacher['id']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}