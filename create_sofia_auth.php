<?php
require_once 'vendor/autoload.php';

// Create database connection
$db = \Config\Database::connect();

// Hash the password
$password = 'Demo9005!';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert auth_identity record for Sofia (user_id 128)
$inserted = $db->table('auth_identities')->insert([
    'user_id' => 128,
    'type' => 'email_password',
    'name' => '',
    'secret' => 'sofia.aguilar@lphs.edu',
    'secret2' => $hashedPassword,
    'expires' => null,
    'extra' => null,
    'force_reset' => 0,
    'last_used_at' => null,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);

if ($inserted) {
    echo "Auth identity created successfully for Sofia Aguilar\n";
    echo "Email: sofia.aguilar@lphs.edu\n";
    echo "Password: Demo9005!\n";
} else {
    echo "Failed to create auth identity\n";
}
?>