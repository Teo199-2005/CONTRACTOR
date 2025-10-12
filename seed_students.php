<?php
require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\StudentModel;
use CodeIgniter\Shield\Models\UserModel;

$studentModel = new StudentModel();
$userModel = new UserModel();

$students = [
    ['Juan', 'Dela Cruz', 'Male', '2009-03-15', 7, 'juan.delacruz@gmail.com', '09123456789', 'Maria Dela Cruz', '09987654321', 'Mother'],
    ['Maria', 'Santos', 'Female', '2008-07-22', 8, 'maria.santos@gmail.com', '09234567890', 'Jose Santos', '09876543210', 'Father'],
    ['Pedro', 'Garcia', 'Male', '2007-11-08', 9, 'pedro.garcia@gmail.com', '09345678901', 'Ana Garcia', '09765432109', 'Mother'],
    ['Ana', 'Rodriguez', 'Female', '2006-05-12', 10, 'ana.rodriguez@gmail.com', '09456789012', 'Carlos Rodriguez', '09654321098', 'Father'],
    ['Jose', 'Martinez', 'Male', '2009-09-30', 7, 'jose.martinez@gmail.com', '09567890123', 'Elena Martinez', '09543210987', 'Mother'],
    ['Carmen', 'Lopez', 'Female', '2008-01-18', 8, 'carmen.lopez@gmail.com', '09678901234', 'Miguel Lopez', '09432109876', 'Father'],
    ['Miguel', 'Hernandez', 'Male', '2007-04-25', 9, 'miguel.hernandez@gmail.com', '09789012345', 'Rosa Hernandez', '09321098765', 'Mother'],
    ['Rosa', 'Gonzalez', 'Female', '2006-12-03', 10, 'rosa.gonzalez@gmail.com', '09890123456', 'Luis Gonzalez', '09210987654', 'Father'],
    ['Luis', 'Perez', 'Male', '2009-06-14', 7, 'luis.perez@gmail.com', '09901234567', 'Sofia Perez', '09109876543', 'Mother'],
    ['Sofia', 'Torres', 'Female', '2008-10-27', 8, 'sofia.torres@gmail.com', '09012345678', 'Diego Torres', '09098765432', 'Father']
];

echo "Seeding students...\n";

foreach ($students as $index => $student) {
    // Create user account
    $userData = [
        'email' => $student[4],
        'password' => 'student123',
        'active' => 1
    ];
    
    if ($userModel->save($userData)) {
        $userId = $userModel->getInsertID();
        $userEntity = $userModel->find($userId);
        $userEntity->addGroup('student');
        
        // Create student record
        $studentData = [
            'user_id' => $userId,
            'student_id' => '2025-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
            'first_name' => $student[0],
            'last_name' => $student[1],
            'gender' => $student[2],
            'date_of_birth' => $student[3],
            'grade_level' => $student[4],
            'email' => $student[5],
            'contact_number' => $student[6],
            'emergency_contact_name' => $student[7],
            'emergency_contact_number' => $student[8],
            'emergency_contact_relationship' => $student[9],
            'enrollment_status' => 'enrolled',
            'school_year' => '2025-2026',
            'address' => 'Sample Address, City',
            'nationality' => 'Filipino'
        ];
        
        if ($studentModel->save($studentData)) {
            echo "✓ Created student: {$student[0]} {$student[1]} (Grade {$student[4]})\n";
        } else {
            echo "✗ Failed to create student: {$student[0]} {$student[1]}\n";
        }
    } else {
        echo "✗ Failed to create user for: {$student[0]} {$student[1]}\n";
    }
}

echo "\nStudent seeding completed!\n";