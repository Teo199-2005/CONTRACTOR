<?php
// Debug student email issue
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lphs_sms';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $lrn = '999419249897';
    
    echo "Debugging student with LRN: $lrn\n\n";
    
    // Get student record
    $stmt = $pdo->prepare("SELECT * FROM students WHERE lrn = ?");
    $stmt->execute([$lrn]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo "Student found:\n";
        echo "ID: " . $student['id'] . "\n";
        echo "User ID: " . $student['user_id'] . "\n";
        echo "Email in students table: " . ($student['email'] ?? 'NULL') . "\n\n";
        
        // Get user record
        if ($student['user_id']) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$student['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "User found:\n";
                echo "Email in users table: " . ($user['email'] ?? 'NULL') . "\n\n";
            } else {
                echo "No user found with ID: " . $student['user_id'] . "\n\n";
            }
        }
        
        // Test the join query
        $stmt = $pdo->prepare("
            SELECT students.*, users.email as user_email 
            FROM students 
            LEFT JOIN users ON users.id = students.user_id 
            WHERE students.lrn = ?
        ");
        $stmt->execute([$lrn]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Join query result:\n";
        echo "Student email: " . ($result['email'] ?? 'NULL') . "\n";
        echo "User email: " . ($result['user_email'] ?? 'NULL') . "\n";
        
    } else {
        echo "No student found with LRN: $lrn\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>