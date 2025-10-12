<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'codeigniter4' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
define('APPPATH', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('WRITEPATH', __DIR__ . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR);
define('TESTPATH', __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);

require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$paths = new \Config\Paths();
$bootstrap = \CodeIgniter\Config\Services::codeigniter();
$bootstrap->initialize();
$bootstrap->setContext('web');

// Get database connection
$db = \Config\Database::connect();

// Direct database operations since we're in a standalone script
$builder = $db->table('students');

// Philippine enrollment pattern data for testing
$enrollmentPattern = [
    2023 => [
        'Jan' => 2, 'Feb' => 1, 'Mar' => 0, 'Apr' => 1, 'May' => 2, 'Jun' => 15,
        'Jul' => 12, 'Aug' => 8, 'Sep' => 3, 'Oct' => 1, 'Nov' => 1, 'Dec' => 0
    ],
    2024 => [
        'Jan' => 3, 'Feb' => 2, 'Mar' => 1, 'Apr' => 2, 'May' => 3, 'Jun' => 18,
        'Jul' => 15, 'Aug' => 10, 'Sep' => 4, 'Oct' => 2, 'Nov' => 1, 'Dec' => 0
    ]
];

echo "Adding enrollment test data based on Philippine school patterns...\n";

foreach ($enrollmentPattern as $year => $months) {
    foreach ($months as $month => $count) {
        for ($i = 0; $i < $count; $i++) {
            $monthNum = date('m', strtotime($month));
            $day = rand(1, 28);
            $createdAt = "$year-$monthNum-$day 10:00:00";
            
            $data = [
                'first_name' => 'Test' . rand(1000, 9999),
                'last_name' => 'Student' . rand(1000, 9999),
                'gender' => rand(0, 1) ? 'Male' : 'Female',
                'date_of_birth' => ($year - 16) . '-01-01',
                'enrollment_status' => 'enrolled',
                'grade_level' => rand(7, 10),
                'school_year' => $year . '-' . ($year + 1),
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ];
            
            try {
                $builder->insert($data);
                echo "Added student for $month $year\n";
            } catch (Exception $e) {
                echo "Error adding student: " . $e->getMessage() . "\n";
            }
        }
    }
}

echo "Test data added successfully!\n";
echo "Total students added: " . array_sum(array_merge(...array_values($enrollmentPattern))) . "\n";
?>