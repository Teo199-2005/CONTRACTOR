<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking all demo teacher accounts...\n\n";
    
    // Find all users with teacher group
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.email, u.first_name, u.last_name, agu.group
        FROM users u 
        JOIN auth_groups_users agu ON u.id = agu.user_id 
        WHERE agu.group = 'teacher'
    ");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($teachers) . " teacher users:\n";
    foreach ($teachers as $teacher) {
        echo "- ID: {$teacher['id']}, Username: {$teacher['username']}, Name: {$teacher['first_name']} {$teacher['last_name']}\n";
        
        // Check if teacher record exists
        $stmt = $pdo->prepare("SELECT id FROM teachers WHERE user_id = ?");
        $stmt->execute([$teacher['id']]);
        $teacherRecord = $stmt->fetch();
        
        if ($teacherRecord) {
            echo "  ✓ Teacher record exists (ID: {$teacherRecord['id']})\n";
        } else {
            echo "  ✗ No teacher record found\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}