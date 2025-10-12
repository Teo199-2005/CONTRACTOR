<?php
// Simple test script to verify teacher edit functionality
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\TeacherModel;
use CodeIgniter\Shield\Models\UserModel;

echo "Testing Teacher Edit Fix...\n\n";

$teacherModel = new TeacherModel();
$userModel = new UserModel();

// Check if we have any teachers
$teachers = $teacherModel->findAll();
echo "Found " . count($teachers) . " teachers in database.\n";

if (count($teachers) > 0) {
    $teacher = $teachers[0];
    echo "Testing with teacher ID: " . $teacher['id'] . "\n";
    echo "Teacher ID: " . ($teacher['teacher_id'] ?? 'NULL') . "\n";
    echo "Name: " . $teacher['first_name'] . ' ' . $teacher['last_name'] . "\n";
    echo "Email: " . ($teacher['email'] ?? 'NULL') . "\n";
    echo "Date Hired: " . ($teacher['date_hired'] ?? 'NULL') . "\n";
    echo "Employment Status: " . ($teacher['employment_status'] ?? 'NULL') . "\n";
    
    // Test the join query that was failing
    $teacherWithUser = $teacherModel->select('teachers.*, users.email')
        ->join('users', 'users.id = teachers.user_id', 'inner')
        ->find($teacher['id']);
    
    if ($teacherWithUser) {
        echo "\n✅ JOIN query successful!\n";
        echo "Teacher found with user email: " . $teacherWithUser['email'] . "\n";
    } else {
        echo "\n❌ JOIN query failed - teacher not found\n";
    }
} else {
    echo "No teachers found. Creating a test teacher...\n";
    
    // Create a test user first
    $userData = [
        'email' => 'test.teacher@lphs.edu.ph',
        'password' => 'password123',
        'active' => 1
    ];
    
    if ($userModel->save($userData)) {
        $userId = $userModel->getInsertID();
        echo "Created test user with ID: $userId\n";
        
        // Add user to teacher group
        $userEntity = $userModel->find($userId);
        $userEntity->addGroup('teacher');
        
        // Create teacher record
        $teacherData = [
            'teacher_id' => '2024-0001',
            'user_id' => $userId,
            'first_name' => 'Test',
            'last_name' => 'Teacher',
            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',
            'email' => 'test.teacher@lphs.edu.ph',
            'department' => 'Mathematics',
            'position' => 'Teacher',
            'date_hired' => '2024-01-01',
            'employment_status' => 'active'
        ];
        
        if ($teacherModel->save($teacherData)) {
            $teacherId = $teacherModel->getInsertID();
            echo "✅ Created test teacher with ID: $teacherId\n";
            
            // Test the problematic query
            $teacherWithUser = $teacherModel->select('teachers.*, users.email')
                ->join('users', 'users.id = teachers.user_id', 'inner')
                ->find($teacherId);
            
            if ($teacherWithUser) {
                echo "✅ Teacher edit query should now work!\n";
                echo "Access: http://localhost/admin/teachers/edit-form/$teacherId\n";
            } else {
                echo "❌ Still having issues with the query\n";
            }
        } else {
            echo "❌ Failed to create teacher record\n";
            print_r($teacherModel->errors());
        }
    } else {
        echo "❌ Failed to create user account\n";
        print_r($userModel->errors());
    }
}

echo "\nTest completed.\n";