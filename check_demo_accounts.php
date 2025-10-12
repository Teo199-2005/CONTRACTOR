<?php

// Simple script to check demo accounts
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lphs_sms';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CHECKING DEMO ACCOUNTS ===\n\n";

// Check users with groups
$sql = "SELECT u.id, u.email, u.active, agu.group 
        FROM users u 
        LEFT JOIN auth_groups_users agu ON u.id = agu.user_id 
        WHERE u.email LIKE '%demo%' OR u.email LIKE '%admin%' OR u.email LIKE '%maria%'
        ORDER BY u.email";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Email: " . $row['email'] . " | Group: " . ($row['group'] ?? 'None') . " | Active: " . $row['active'] . "\n";
    }
} else {
    echo "No demo accounts found\n";
}

echo "\n=== CHECKING ADMIN ACCOUNTS ===\n";

// Check for any admin accounts
$sql = "SELECT u.id, u.email, u.active 
        FROM users u 
        JOIN auth_groups_users agu ON u.id = agu.user_id 
        WHERE agu.group = 'admin'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Admin: " . $row['email'] . " | Active: " . $row['active'] . "\n";
    }
} else {
    echo "No admin accounts found\n";
}

$conn->close();