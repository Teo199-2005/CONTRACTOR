<?php
require_once 'vendor/autoload.php';
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\TeacherModel;

$teacherModel = new TeacherModel();

// Check if any teachers exist
$teachers = $teacherModel->findAll();
echo "Total teachers: " . count($teachers) . "\n";

if (count($teachers) > 0) {
    echo "First teacher:\n";
    print_r($teachers[0]);
    
    $teacherId = $teachers[0]['id'];
    echo "\nTesting query with ID: $teacherId\n";
    
    // Test the exact query from editForm
    $teacher = $teacherModel->select('teachers.*, users.email')
        ->join('users', 'users.id = teachers.user_id', 'inner')
        ->where('teachers.id', $teacherId)
        ->first();
    
    if ($teacher) {
        echo "✅ Query successful!\n";
        print_r($teacher);
    } else {
        echo "❌ Query failed\n";
        echo "Last query: " . $teacherModel->getLastQuery() . "\n";
    }
} else {
    echo "No teachers found in database\n";
}