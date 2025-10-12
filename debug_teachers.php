<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=lphs_sms', 'root', '');
    
    echo "ALL TEACHERS:\n";
    $result = $db->query('SELECT id, first_name, last_name, email, employment_status, deleted_at FROM teachers');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . 
             ", Status: " . $row['employment_status'] . ", Deleted: " . ($row['deleted_at'] ?? 'NULL') . "\n";
    }
    
    echo "\nACTIVE TEACHERS ONLY:\n";
    $result = $db->query("SELECT id, first_name, last_name, email FROM teachers WHERE employment_status = 'active' AND deleted_at IS NULL");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>