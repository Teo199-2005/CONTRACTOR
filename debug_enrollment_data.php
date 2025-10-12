<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

// Check actual enrollment data by month and year
echo "=== ENROLLMENT DATA DEBUG ===\n\n";

// Check students table structure
$fields = $db->getFieldData('students');
echo "Students table fields:\n";
foreach ($fields as $field) {
    echo "- {$field->name} ({$field->type})\n";
}

echo "\n=== ENROLLMENT BY MONTH/YEAR ===\n";

// Get enrollment data by month for current year
$currentYear = date('Y');
$query = "
    SELECT 
        YEAR(created_at) as year,
        MONTH(created_at) as month,
        COUNT(*) as count,
        enrollment_status
    FROM students 
    WHERE deleted_at IS NULL 
    GROUP BY YEAR(created_at), MONTH(created_at), enrollment_status
    ORDER BY year DESC, month ASC
";

$results = $db->query($query)->getResultArray();

foreach ($results as $row) {
    $monthName = date('F', mktime(0, 0, 0, $row['month'], 1));
    echo "Year: {$row['year']}, Month: {$monthName} ({$row['month']}), Status: {$row['enrollment_status']}, Count: {$row['count']}\n";
}

echo "\n=== TOTAL ENROLLED BY MONTH (2024) ===\n";

for ($month = 1; $month <= 12; $month++) {
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $count = $db->query("
        SELECT COUNT(*) as count 
        FROM students 
        WHERE YEAR(created_at) = 2024 
        AND MONTH(created_at) = ? 
        AND deleted_at IS NULL
    ", [$month])->getRow()->count;
    
    echo "{$monthName}: {$count}\n";
}

echo "\n=== SAMPLE STUDENT RECORDS ===\n";
$samples = $db->query("
    SELECT id, first_name, last_name, created_at, enrollment_status 
    FROM students 
    WHERE deleted_at IS NULL 
    ORDER BY created_at DESC 
    LIMIT 10
")->getResultArray();

foreach ($samples as $student) {
    echo "ID: {$student['id']}, Name: {$student['first_name']} {$student['last_name']}, Created: {$student['created_at']}, Status: {$student['enrollment_status']}\n";
}
?>