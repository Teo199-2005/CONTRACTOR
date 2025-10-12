<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Auth_identities table structure:\n";
    $stmt = $pdo->query("DESCRIBE auth_identities");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nAuth_groups_users table structure:\n";
    $stmt = $pdo->query("DESCRIBE auth_groups_users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nSample auth_identities:\n";
    $stmt = $pdo->query("SELECT user_id, type, name FROM auth_identities LIMIT 5");
    $identities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($identities as $identity) {
        echo "- User ID: {$identity['user_id']}, Type: {$identity['type']}, Name: {$identity['name']}\n";
    }
    
    echo "\nSample auth_groups_users:\n";
    $stmt = $pdo->query("SELECT user_id, group_id FROM auth_groups_users LIMIT 5");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($groups as $group) {
        echo "- User ID: {$group['user_id']}, Group ID: {$group['group_id']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}