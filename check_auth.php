<?php
// Check auth_identities table directly
$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get student info first
    $stmt = $pdo->prepare("SELECT * FROM students WHERE lrn = '100000000001'");
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "Student: {$student['first_name']} {$student['last_name']}\n";
        echo "User ID: {$student['user_id']}\n\n";
        
        // Check auth_identities for this user
        $stmt2 = $pdo->prepare("SELECT * FROM auth_identities WHERE user_id = ?");
        $stmt2->execute([$student['user_id']]);
        $auth = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($auth) {
            echo "Auth Identity Found:\n";
            echo "Type: {$auth['type']}\n";
            echo "Name: {$auth['name']}\n";
            echo "Secret (first 50 chars): " . substr($auth['secret'], 0, 50) . "...\n\n";
            
            // Try more passwords
            $passwords = [
                'student123',
                'Demo123!',
                'Student123!',
                '123456',
                'password',
                'juan123',
                'Juan123',
                'delacruz123',
                'student',
                'demo',
                '100000000001',
                'lphs123',
                'LPHS123'
            ];
            
            echo "Testing passwords:\n";
            foreach ($passwords as $pass) {
                if (password_verify($pass, $auth['secret'])) {
                    echo "✓ FOUND: Password is '$pass'\n";
                    echo "Login with:\n";
                    echo "Username: {$auth['name']}\n";
                    echo "Password: $pass\n";
                    exit;
                }
            }
            echo "None of the common passwords worked.\n";
            
            // Check if it's a plain text password (shouldn't be, but let's check)
            if (strlen($auth['secret']) < 60) {
                echo "Possible plain text password: {$auth['secret']}\n";
            }
            
        } else {
            echo "No auth identity found for this user.\n";
        }
    } else {
        echo "Student not found.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>