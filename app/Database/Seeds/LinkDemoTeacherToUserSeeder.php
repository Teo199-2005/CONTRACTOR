<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LinkDemoTeacherToUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Get demo teacher user
        $user = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRowArray();
        if (!$user) {
            echo "Demo teacher user not found\n";
            return;
        }

        // Get or create demo teacher record
        $teacher = $db->table('teachers')->where('teacher_id', 'DEMO-T001')->get()->getRowArray();
        if ($teacher) {
            // Update existing teacher with user_id
            $db->table('teachers')->where('id', $teacher['id'])->update(['user_id' => $user['id']]);
            $teacherId = $teacher['id'];
        } else {
            // Create new teacher record
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
        }

        // Create/update section
        $sectionData = [
            'section_name' => 'Grade 10 - Aristotle',
            'grade_level' => 10,
            'adviser_id' => $teacherId,
            'school_year' => '2024-2025',
            'max_capacity' => 40,
            'is_active' => 1
        ];

        $existingSection = $db->table('sections')->where('section_name', 'Grade 10 - Aristotle')->get()->getRowArray();
        if ($existingSection) {
            $db->table('sections')->where('id', $existingSection['id'])->update($sectionData);
            $sectionId = $existingSection['id'];
        } else {
            $db->table('sections')->insert($sectionData);
            $sectionId = $db->insertID();
        }

        // Create students
        $students = [
            ['lrn' => '123456789001', 'first_name' => 'Juan', 'last_name' => 'Cruz', 'gender' => 'Male'],
            ['lrn' => '123456789002', 'first_name' => 'Maria', 'last_name' => 'Santos', 'gender' => 'Female'],
            ['lrn' => '123456789003', 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'gender' => 'Male'],
            ['lrn' => '123456789004', 'first_name' => 'Ana', 'last_name' => 'Lopez', 'gender' => 'Female'],
            ['lrn' => '123456789005', 'first_name' => 'Jose', 'last_name' => 'Martinez', 'gender' => 'Male'],
            ['lrn' => '123456789006', 'first_name' => 'Carmen', 'last_name' => 'Rodriguez', 'gender' => 'Female'],
            ['lrn' => '123456789007', 'first_name' => 'Miguel', 'last_name' => 'Hernandez', 'gender' => 'Male'],
            ['lrn' => '123456789008', 'first_name' => 'Sofia', 'last_name' => 'Gonzalez', 'gender' => 'Female'],
        ];

        $createdStudents = 0;
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

            $existing = $db->table('students')->where('lrn', $studentData['lrn'])->get()->getRowArray();
            if ($existing) {
                $db->table('students')->where('id', $existing['id'])->update($fullStudentData);
            } else {
                $db->table('students')->insert($fullStudentData);
                $createdStudents++;
            }
        }

        echo "Demo teacher linked to user account and assigned to section with " . count($students) . " students\n";
    }
}