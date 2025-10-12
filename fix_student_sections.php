<?php

require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

// Get all students without sections or with invalid sections
$students = $db->table('students')
    ->select('students.*, sections.id as valid_section_id')
    ->join('sections', 'sections.grade_level = students.grade_level AND sections.school_year = students.school_year', 'left')
    ->where('students.enrollment_status', 'enrolled')
    ->where('(students.section_id IS NULL OR students.section_id NOT IN (SELECT id FROM sections))')
    ->get()
    ->getResultArray();

echo "Found " . count($students) . " students needing section assignment.\n";

// Group students by grade level
$studentsByGrade = [];
foreach ($students as $student) {
    $grade = $student['grade_level'];
    if (!isset($studentsByGrade[$grade])) {
        $studentsByGrade[$grade] = [];
    }
    $studentsByGrade[$grade][] = $student;
}

// Assign students to sections
foreach ($studentsByGrade as $gradeLevel => $gradeStudents) {
    echo "\nProcessing Grade $gradeLevel students...\n";
    
    // Get available sections for this grade level
    $sections = $db->table('sections')
        ->where('grade_level', $gradeLevel)
        ->where('school_year', '2025-2026')
        ->where('is_active', true)
        ->orderBy('section_name', 'ASC')
        ->get()
        ->getResultArray();
    
    if (empty($sections)) {
        echo "No sections found for Grade $gradeLevel\n";
        continue;
    }
    
    echo "Available sections: " . implode(', ', array_column($sections, 'section_name')) . "\n";
    
    // Distribute students evenly across sections
    $sectionIndex = 0;
    $studentsPerSection = ceil(count($gradeStudents) / count($sections));
    $currentSectionCount = 0;
    
    foreach ($gradeStudents as $student) {
        $currentSection = $sections[$sectionIndex];
        
        // Update student's section
        $db->table('students')
            ->where('id', $student['id'])
            ->update([
                'section_id' => $currentSection['id'],
                'school_year' => '2025-2026'
            ]);
        
        echo "Assigned {$student['first_name']} {$student['last_name']} to {$currentSection['section_name']}\n";
        
        $currentSectionCount++;
        
        // Move to next section if current one is full
        if ($currentSectionCount >= $studentsPerSection && $sectionIndex < count($sections) - 1) {
            $sectionIndex++;
            $currentSectionCount = 0;
        }
    }
}

// Update section enrollment counts
echo "\nUpdating section enrollment counts...\n";
$sections = $db->table('sections')->get()->getResultArray();

foreach ($sections as $section) {
    $count = $db->table('students')
        ->where('section_id', $section['id'])
        ->where('enrollment_status', 'enrolled')
        ->countAllResults();
    
    $db->table('sections')
        ->where('id', $section['id'])
        ->update(['current_enrollment' => $count]);
    
    echo "Section {$section['section_name']} (Grade {$section['grade_level']}): $count students\n";
}

echo "\nStudent-section assignment completed!\n";