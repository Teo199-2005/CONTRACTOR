<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    echo "ALL SECTIONS WITH ADVISER INFO:\n";
    $result = $db->query("
        SELECT s.id, s.section_name, s.grade_level, s.adviser_id,
               t.first_name, t.last_name,
               CONCAT(t.first_name, ' ', t.last_name) as adviser_name
        FROM sections s
        LEFT JOIN teachers t ON t.id = s.adviser_id
        ORDER BY s.grade_level, s.section_name
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . 
             ", Name: " . $row['section_name'] . 
             ", Grade: " . $row['grade_level'] . 
             ", Adviser ID: " . ($row['adviser_id'] ?? 'NULL') . 
             ", Adviser: " . ($row['adviser_name'] ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>