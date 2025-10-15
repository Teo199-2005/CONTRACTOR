<?php
// Fix student password for LRN 100000000001
$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get student info
    $stmt = $pdo->prepare("SELECT * FROM students WHERE lrn = '100000000001'");
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "Found student: {$student['first_name']} {$student['last_name']}\n";
        echo "User ID: {$student['user_id']}\n";
        
        // Set a proper password
        $newPassword = 'student123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update or insert auth identity
        $stmt2 = $pdo->prepare("SELECT * FROM auth_identities WHERE user_id = ?");
        $stmt2->execute([$student['user_id']]);
        $auth = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($auth) {
            // Update existing
            $stmt3 = $pdo->prepare("UPDATE auth_identities SET name = ?, secret = ? WHERE user_id = ?");
            $stmt3->execute([$student['lrn'], $hashedPassword, $student['user_id']]);
            echo "Updated existing auth identity\n";
        } else {
            // Insert new
            $stmt3 = $pdo->prepare("INSERT INTO auth_identities (user_id, type, name, secret, created_at, updated_at) VALUES (?, 'email_password', ?, ?, NOW(), NOW())");
            $stmt3->execute([$student['user_id'], $student['lrn'], $hashedPassword]);
            echo "Created new auth identity\n";
        }
        
        // Also update the user email if needed
        $stmt4 = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt4->execute([$student['lrn'] . '@lphs.edu', $student['user_id']]);
        
        echo "\n=== LOGIN CREDENTIALS ===\n";
        echo "Username: {$student['lrn']}\n";
        echo "Password: $newPassword\n";
        echo "\nAlternatively, try:\n";
        echo "Username: {$student['lrn']}@lphs.edu\n";
        echo "Password: $newPassword\n";
        
    } else {
        echo "Student not found.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>