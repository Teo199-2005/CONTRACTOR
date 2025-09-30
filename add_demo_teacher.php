<?php
// Direct database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lphs_sms';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Find existing demo teacher user from auth_identities
$result = $mysqli->query("SELECT user_id FROM auth_identities WHERE secret = 'demo.teacher@lphs.edu'");
$identity = $result->fetch_assoc();

if ($identity) {
    $userId = $identity['user_id'];
    
    // Check if teacher record exists
    $result = $mysqli->query("SELECT id FROM teachers WHERE user_id = $userId");
    $teacher = $result->fetch_assoc();
    
    if (!$teacher) {
        // Create teacher record
        $mysqli->query("INSERT INTO teachers (user_id, first_name, last_name, gender, email, employment_status, created_at, updated_at) VALUES ($userId, 'Demo', 'Teacher', 'Male', 'demo.teacher@lphs.edu', 'active', NOW(), NOW())");
        echo "Demo teacher record created successfully!\n";
    } else {
        echo "Demo teacher record already exists.\n";
    }
} else {
    echo "Demo teacher identity not found.\n";
}

$mysqli->close();