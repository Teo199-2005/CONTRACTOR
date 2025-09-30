<?php

// Script to add sample grades for testing
// Run this from your project root directory

// Include CodeIgniter bootstrap
require_once 'preload.php';

use App\Models\GradeModel;
use App\Models\StudentModel;
use App\Models\SubjectModel;

echo "Adding sample grades for testing...\n";

try {
    $gradeModel = new GradeModel();
    $studentModel = new StudentModel();
    $subjectModel = new SubjectModel();
    
    // Find a student (preferably our new student)
    $student = $studentModel->where('email', 'new.student@lphs.edu')->first();
    if (!$student) {
        // Fallback to any student
        $student = $studentModel->first();
    }
    
    if (!$student) {
        echo "No student found. Please create a student first.\n";
        exit;
    }
    
    echo "Adding grades for student: {$student['first_name']} {$student['last_name']} (ID: {$student['id']})\n";
    
    // Sample subjects for Grade 8
    $subjects = [
        ['subject_name' => 'Mathematics', 'subject_code' => 'MATH8', 'units' => 3, 'grade_level' => 8],
        ['subject_name' => 'English', 'subject_code' => 'ENG8', 'units' => 3, 'grade_level' => 8],
        ['subject_name' => 'Science', 'subject_code' => 'SCI8', 'units' => 3, 'grade_level' => 8],
        ['subject_name' => 'Filipino', 'subject_code' => 'FIL8', 'units' => 3, 'grade_level' => 8],
        ['subject_name' => 'Araling Panlipunan', 'subject_code' => 'AP8', 'units' => 3, 'grade_level' => 8],
        ['subject_name' => 'Physical Education', 'subject_code' => 'PE8', 'units' => 2, 'grade_level' => 8],
        ['subject_name' => 'Music', 'subject_code' => 'MUS8', 'units' => 1, 'grade_level' => 8],
        ['subject_name' => 'Arts', 'subject_code' => 'ART8', 'units' => 1, 'grade_level' => 8],
    ];
    
    // Create subjects if they don't exist
    $subjectIds = [];
    foreach ($subjects as $subjectData) {
        $existing = $subjectModel->where('subject_code', $subjectData['subject_code'])->first();
        if ($existing) {
            $subjectIds[] = $existing['id'];
            echo "Subject {$subjectData['subject_code']} already exists.\n";
        } else {
            $subjectId = $subjectModel->insert($subjectData);
            $subjectIds[] = $subjectId;
            echo "Created subject: {$subjectData['subject_name']} ({$subjectData['subject_code']})\n";
        }
    }
    
    // Sample grades for quarters 1-3 (quarter 4 will be empty)
    $sampleGrades = [
        1 => [88.5, 92.0, 85.0, 90.5, 87.0, 95.0, 93.0, 89.0], // Quarter 1
        2 => [90.0, 89.5, 87.5, 92.0, 88.5, 94.0, 91.0, 90.5], // Quarter 2  
        3 => [85.0, 88.0, 82.0, 89.0, 85.5, 92.0, 88.0, 87.0], // Quarter 3
    ];
    
    $schoolYear = '2024-2025';
    
    // Insert grades for each quarter
    foreach ($sampleGrades as $quarter => $grades) {
        echo "\nAdding Quarter $quarter grades...\n";
        
        for ($i = 0; $i < count($subjectIds) && $i < count($grades); $i++) {
            $gradeData = [
                'student_id' => $student['id'],
                'subject_id' => $subjectIds[$i],
                'teacher_id' => 1, // Assuming teacher ID 1 exists
                'school_year' => $schoolYear,
                'quarter' => $quarter,
                'grade' => $grades[$i],
                'remarks' => $grades[$i] >= 90 ? 'Excellent' : ($grades[$i] >= 85 ? 'Very Good' : ($grades[$i] >= 80 ? 'Good' : 'Fair')),
                'date_recorded' => date('Y-m-d H:i:s')
            ];
            
            // Check if grade already exists
            $existing = $gradeModel->where('student_id', $student['id'])
                ->where('subject_id', $subjectIds[$i])
                ->where('school_year', $schoolYear)
                ->where('quarter', $quarter)
                ->first();
                
            if ($existing) {
                echo "Grade already exists for subject ID {$subjectIds[$i]}, Quarter $quarter\n";
            } else {
                $gradeModel->insert($gradeData);
                echo "Added grade {$grades[$i]} for subject ID {$subjectIds[$i]}, Quarter $quarter\n";
            }
        }
    }
    
    // Calculate and display averages
    echo "\n=== Grade Summary ===\n";
    for ($q = 1; $q <= 4; $q++) {
        $average = $gradeModel->getQuarterAverage($student['id'], $schoolYear, $q);
        echo "Quarter $q Average: " . ($average !== null ? number_format($average, 2) : 'N/A') . "\n";
    }
    
    $gwa = $gradeModel->getFinalAverage($student['id'], $schoolYear);
    echo "GWA (General Weighted Average): " . ($gwa !== null ? number_format($gwa, 2) : 'N/A') . "\n";
    
    $canEnroll = ($gwa !== null && $gwa >= 75.0);
    echo "Can enroll for next semester: " . ($canEnroll ? 'YES' : 'NO') . "\n";
    
    echo "\n✅ Sample grades added successfully!\n";
    echo "You can now view the grades at: student/grades\n";
    
} catch (\Throwable $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nScript completed.\n";
