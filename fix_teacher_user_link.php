<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    // Find the demo teacher user
    $userQuery = $db->query("SELECT id, email FROM users WHERE email = 'demo.teacher@lphs.edu'");
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Found user: ID " . $user['id'] . ", Email: " . $user['email'] . "\n";
        
        // Update the Demo Teacher record to link to this user
        $updateQuery = $db->prepare("UPDATE teachers SET user_id = ? WHERE first_name = 'Demo' AND last_name = 'Teacher'");
        $result = $updateQuery->execute([$user['id']]);
        
        echo "Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
        
        // Verify the update
        $checkQuery = $db->query("SELECT id, user_id, first_name, last_name FROM teachers WHERE first_name = 'Demo' AND last_name = 'Teacher'");
        $teacher = $checkQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            echo "Teacher record: ID " . $teacher['id'] . ", User ID: " . ($teacher['user_id'] ?? 'NULL') . 
                 ", Name: " . $teacher['first_name'] . " " . $teacher['last_name'] . "\n";
        }
    } else {
        echo "Demo teacher user not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>