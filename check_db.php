<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    echo "SECTIONS TABLE STRUCTURE:\n";
    $result = $db->query('DESCRIBE sections');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    echo "\nTEACHERS TABLE STRUCTURE:\n";
    $result = $db->query('DESCRIBE teachers');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    echo "\nSAMPLE SECTIONS DATA:\n";
    $result = $db->query('SELECT id, section_name, adviser_id FROM sections LIMIT 3');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Section: " . $row['section_name'] . ", adviser_id: " . ($row['adviser_id'] ?? 'NULL') . "\n";
    }
    
    echo "\nSAMPLE TEACHERS DATA:\n";
    $result = $db->query('SELECT id, first_name, last_name, license_number FROM teachers LIMIT 3');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . ", License: " . ($row['license_number'] ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>