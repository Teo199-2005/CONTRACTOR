<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking teacher1 login setup...\n\n";
    
    // Check user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'teacher1'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found: ID {$user['id']}, Username: {$user['username']}\n";
        
        // Check auth identity
        $stmt = $pdo->prepare("SELECT * FROM auth_identities WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $identity = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($identity) {
            echo "Auth identity found: Type {$identity['type']}\n";
        } else {
            echo "No auth identity found!\n";
        }
        
        // Check group membership
        $stmt = $pdo->prepare("SELECT * FROM auth_groups_users WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($group) {
            echo "Group membership: {$group['group']}\n";
        } else {
            echo "No group membership found!\n";
        }
        
        // Check teacher record
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            echo "Teacher record found: ID {$teacher['id']}, Name: {$teacher['first_name']} {$teacher['last_name']}\n";
        } else {
            echo "No teacher record found!\n";
        }
        
    } else {
        echo "User not found!\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}