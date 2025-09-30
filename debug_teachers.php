<?php
require_once 'vendor/autoload.php';

$db = \Config\Database::connect();

// Check teachers table
echo "Teachers table:\n";
$teachers = $db->query("SELECT id, first_name, last_name, user_id FROM teachers LIMIT 3")->getResultArray();
foreach ($teachers as $teacher) {
    echo "ID: {$teacher['id']}, Name: {$teacher['first_name']} {$teacher['last_name']}, user_id: " . ($teacher['user_id'] ?? 'NULL') . "\n";
}

echo "\nUsers table:\n";
$users = $db->query("SELECT id, email FROM users WHERE email LIKE '%@lphs.edu.ph' LIMIT 3")->getResultArray();
foreach ($users as $user) {
    echo "ID: {$user['id']}, Email: {$user['email']}\n";
}

echo "\nJoin test:\n";
$result = $db->query("SELECT t.first_name, t.last_name, t.user_id, u.email FROM teachers t LEFT JOIN users u ON u.id = t.user_id LIMIT 3")->getResultArray();
foreach ($result as $row) {
    echo "Name: {$row['first_name']} {$row['last_name']}, user_id: " . ($row['user_id'] ?? 'NULL') . ", Email: " . ($row['email'] ?? 'NULL') . "\n";
}
?>