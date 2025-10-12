<?php

$host = 'localhost';
$dbname = 'lphs_sms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Fixing section enrollment counts...\n\n";
    
    // Update section enrollment counts based on actual student count
    $stmt = $pdo->query("
        SELECT s.id, s.section_name, COUNT(st.id) as actual_count, s.current_enrollment
        FROM sections s
        LEFT JOIN students st ON s.id = st.section_id AND st.enrollment_status = 'enrolled'
        WHERE s.is_active = 1
        GROUP BY s.id
    ");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sections as $section) {
        if ($section['actual_count'] != $section['current_enrollment']) {
            echo "Updating {$section['section_name']}: {$section['current_enrollment']} -> {$section['actual_count']}\n";
            
            $stmt = $pdo->prepare("UPDATE sections SET current_enrollment = ? WHERE id = ?");
            $stmt->execute([$section['actual_count'], $section['id']]);
        }
    }
    
    echo "\n✅ Section counts updated!\n";
    echo "Teacher analytics should now show correct data.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}