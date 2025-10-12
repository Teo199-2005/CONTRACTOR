<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Fixing all teacher accounts...\n\n";
    
    // Update users with proper usernames and names
    $teacherUpdates = [
        1 => ['username' => 'teacher2', 'first_name' => 'John', 'last_name' => 'Smith'],
        2 => ['username' => 'teacher3', 'first_name' => 'Jane', 'last_name' => 'Doe'],
        3 => ['username' => 'teacher4', 'first_name' => 'Robert', 'last_name' => 'Johnson'],
        14 => ['username' => 'teacher5', 'first_name' => 'Emily', 'last_name' => 'Davis']
    ];
    
    foreach ($teacherUpdates as $userId => $data) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ? WHERE id = ?");
        $stmt->execute([$data['username'], $data['first_name'], $data['last_name'], $userId]);
        echo "Updated user ID $userId: {$data['username']}\n";
    }
    
    // Assign sections to teachers
    $sectionAssignments = [
        7 => 14,  // John Smith -> Grade 7 Newton
        8 => 15,  // Jane Doe -> Grade 8 Einstein  
        9 => 16,  // Robert Johnson -> Grade 9 Galileo
        10 => 17  // Emily Davis -> Grade 10 Darwin
    ];
    
    foreach ($sectionAssignments as $teacherId => $sectionId) {
        $stmt = $pdo->prepare("UPDATE sections SET adviser_id = ? WHERE id = ?");
        $stmt->execute([$teacherId, $sectionId]);
        
        $stmt = $pdo->prepare("SELECT section_name FROM sections WHERE id = ?");
        $stmt->execute([$sectionId]);
        $sectionName = $stmt->fetchColumn();
        
        echo "Assigned teacher ID $teacherId to section: $sectionName\n";
    }
    
    echo "\n✅ All teacher accounts fixed!\n";
    echo "Login credentials:\n";
    echo "- teacher1 / password123 (Maria Santos - 22 students)\n";
    echo "- teacher2 / password123 (John Smith)\n";
    echo "- teacher3 / password123 (Jane Doe)\n";
    echo "- teacher4 / password123 (Robert Johnson)\n";
    echo "- teacher5 / password123 (Emily Davis)\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}