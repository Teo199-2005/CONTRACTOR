<?php
// Simple database connection to check student password
$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query for student with LRN 100000000001
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.lrn,
            s.first_name,
            s.last_name,
            s.user_id,
            s.temp_password,
            u.email,
            ai.name as auth_name,
            ai.secret as password_hash
        FROM students s
        LEFT JOIN users u ON s.user_id = u.id
        LEFT JOIN auth_identities ai ON u.id = ai.user_id AND ai.type = 'email_password'
        WHERE s.lrn = '100000000001'
    ");
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "=== Student Found ===\n";
        echo "ID: " . $result['id'] . "\n";
        echo "LRN: " . $result['lrn'] . "\n";
        echo "Name: " . $result['first_name'] . " " . $result['last_name'] . "\n";
        echo "User ID: " . ($result['user_id'] ?: 'Not set') . "\n";
        echo "Email: " . ($result['email'] ?: 'Not set') . "\n";
        echo "Auth Name: " . ($result['auth_name'] ?: 'Not set') . "\n";
        echo "Temp Password: " . ($result['temp_password'] ?: 'Not set') . "\n";
        echo "Password Hash: " . ($result['password_hash'] ? 'Set' : 'Not set') . "\n";
        
        // Check if temp_password exists and what it is
        if ($result['temp_password']) {
            echo "\n=== LOGIN CREDENTIALS ===\n";
            echo "Username/Email: " . ($result['email'] ?: $result['lrn']) . "\n";
            echo "Password: " . $result['temp_password'] . "\n";
        } else {
            echo "\n=== CHECKING COMMON PASSWORDS ===\n";
            $commonPasswords = ['student123', 'Demo123!', 'Student123!', '123456', 'password'];
            
            if ($result['password_hash']) {
                foreach ($commonPasswords as $password) {
                    if (password_verify($password, $result['password_hash'])) {
                        echo "✓ Password found: " . $password . "\n";
                        echo "Username/Email: " . ($result['email'] ?: $result['lrn']) . "\n";
                        break;
                    }
                }
            } else {
                echo "No password hash found in database.\n";
            }
        }
    } else {
        echo "Student with LRN 100000000001 not found in database.\n";
    }
    
    echo "\n=== All Students with similar LRN ===\n";
    $stmt2 = $pdo->prepare("
        SELECT 
            s.lrn,
            s.first_name,
            s.last_name,
            s.temp_password,
            u.email
        FROM students s
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.lrn LIKE '100000000001%' OR s.lrn = '100000000001'
        ORDER BY s.lrn
    ");
    
    $stmt2->execute();
    $allResults = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($allResults as $student) {
        echo "LRN: {$student['lrn']} | Name: {$student['first_name']} {$student['last_name']} | Email: " . ($student['email'] ?: 'None') . " | Temp Pass: " . ($student['temp_password'] ?: 'None') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database credentials.\n";
}
?>