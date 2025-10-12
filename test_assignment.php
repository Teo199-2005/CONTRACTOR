<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    // Test direct assignment
    echo "Testing direct assignment...\n";
    $sectionId = 1; // First section
    $teacherId = 11; // Demo Teacher ID
    
    $result = $db->prepare("UPDATE sections SET adviser_id = ?, updated_at = NOW() WHERE id = ?");
    $success = $result->execute([$teacherId, $sectionId]);
    
    echo "Update result: " . ($success ? "SUCCESS" : "FAILED") . "\n";
    
    // Check if it worked
    $check = $db->prepare("SELECT id, section_name, adviser_id FROM sections WHERE id = ?");
    $check->execute([$sectionId]);
    $section = $check->fetch(PDO::FETCH_ASSOC);
    
    echo "Section after update:\n";
    echo "ID: " . $section['id'] . "\n";
    echo "Name: " . $section['section_name'] . "\n";
    echo "Adviser ID: " . ($section['adviser_id'] ?? 'NULL') . "\n";
    
    // Now test the join query
    echo "\nTesting join query:\n";
    $joinQuery = $db->query("
        SELECT s.id, s.section_name, s.adviser_id,
               t.first_name, t.last_name,
               CONCAT(t.first_name, ' ', t.last_name) as adviser_name
        FROM sections s
        LEFT JOIN teachers t ON t.id = s.adviser_id
        WHERE s.id = 1
    ");
    
    $joinResult = $joinQuery->fetch(PDO::FETCH_ASSOC);
    echo "Join result:\n";
    print_r($joinResult);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>