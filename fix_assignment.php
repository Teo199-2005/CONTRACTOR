<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    // Assign Demo Teacher (ID: 11) to Aristotle section (ID: 7)
    $result = $db->prepare("UPDATE sections SET adviser_id = ?, updated_at = NOW() WHERE id = ?");
    $success = $result->execute([11, 7]);
    
    echo "Assignment result: " . ($success ? "SUCCESS" : "FAILED") . "\n";
    
    // Verify
    $check = $db->prepare("SELECT id, section_name, adviser_id FROM sections WHERE id = 7");
    $check->execute();
    $section = $check->fetch(PDO::FETCH_ASSOC);
    
    echo "Aristotle section after update:\n";
    echo "ID: " . $section['id'] . ", Name: " . $section['section_name'] . ", Adviser ID: " . ($section['adviser_id'] ?? 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>