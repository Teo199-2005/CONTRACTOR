<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DirectFixDemoTeacherSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Get the demo teacher user
        $user = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRowArray();
        if (!$user) {
            echo "Demo teacher user not found\n";
            return;
        }

        echo "Found user ID: " . $user['id'] . "\n";

        // Get or create teacher record
        $teacher = $db->table('teachers')->where('user_id', $user['id'])->get()->getRowArray();
        if (!$teacher) {
            // Create teacher record
            $teacherData = [
                'user_id' => $user['id'],
                'teacher_id' => 'DEMO-T001',
                'first_name' => 'Demo',
                'last_name' => 'Teacher',
                'gender' => 'Male',
                'email' => 'demo.teacher@lphs.edu',
                'employment_status' => 'active',
                'position' => 'Teacher',
                'department' => 'Academic'
            ];
            $db->table('teachers')->insert($teacherData);
            $teacherId = $db->insertID();
            echo "Created teacher record with ID: " . $teacherId . "\n";
        } else {
            $teacherId = $teacher['id'];
            echo "Found teacher ID: " . $teacherId . "\n";
        }

        // Delete existing section and students to start fresh
        $db->table('students')->where('section_id', 18)->delete();
        $db->table('sections')->where('id', 18)->delete();

        // Create new section
        $sectionData = [
            'section_name' => 'Grade 10 - Aristotle',
            'grade_level' => 10,
            'adviser_id' => $teacherId,
            'school_year' => '2024-2025',
            'max_capacity' => 40,
            'current_enrollment' => 8,
            'is_active' => 1
        ];
        $db->table('sections')->insert($sectionData);
        $sectionId = $db->insertID();
        echo "Created section with ID: " . $sectionId . "\n";

        // Create students
        $students = [
            ['first_name' => 'Juan', 'last_name' => 'Cruz', 'gender' => 'Male'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'gender' => 'Female'],
            ['first_name' => 'Pedro', 'last_name' => 'Garcia', 'gender' => 'Male'],
            ['first_name' => 'Ana', 'last_name' => 'Lopez', 'gender' => 'Female'],
            ['first_name' => 'Jose', 'last_name' => 'Martinez', 'gender' => 'Male'],
            ['first_name' => 'Carmen', 'last_name' => 'Rodriguez', 'gender' => 'Female'],
            ['first_name' => 'Miguel', 'last_name' => 'Hernandez', 'gender' => 'Male'],
            ['first_name' => 'Sofia', 'last_name' => 'Gonzalez', 'gender' => 'Female'],
        ];

        foreach ($students as $studentData) {
            $fullStudentData = array_merge($studentData, [
                'section_id' => $sectionId,
                'grade_level' => 10,
                'enrollment_status' => 'enrolled',
                'school_year' => '2024-2025',
                'date_of_birth' => '2009-01-01',
                'address' => 'Sample Address',
                'contact_number' => '09123456789',
                'emergency_contact_name' => 'Parent Name',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent'
            ]);
            $db->table('students')->insert($fullStudentData);
        }

        echo "Created 8 students in section " . $sectionId . "\n";

        // Verify the setup
        $studentCount = $db->table('students')
            ->join('sections', 'sections.id = students.section_id')
            ->where('sections.adviser_id', $teacherId)
            ->where('students.enrollment_status', 'enrolled')
            ->countAllResults();

        echo "Verification: Teacher " . $teacherId . " has " . $studentCount . " students\n";
        echo "Demo teacher setup completed successfully\n";
    }
}