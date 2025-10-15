<?php
// Fix Juan's section assignment
$host = 'localhost';
$database = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Updating Juan Dela Cruz section assignment...\n";
    
    $stmt = $pdo->prepare("UPDATE students SET section_id = NULL WHERE lrn = ?");
    $result = $stmt->execute(['100000000001']);
    
    if ($result) {
        echo "✓ Juan Dela Cruz section assignment cleared successfully.\n";
        echo "He is now 'Not assigned' to any section.\n";
    } else {
        echo "✗ Failed to update section assignment.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>