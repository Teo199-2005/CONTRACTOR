<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixDemoTeacherSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Find demo teacher user by email
        $user = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRowArray();
        if (!$user) {
            echo "Demo teacher user not found. Creating user...\n";
            // Create user manually
            $db->table('users')->insert([
                'email' => 'demo.teacher@lphs.edu',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $userId = $db->insertID();
            
            // Add to teacher group
            $db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group' => 'teacher',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $userId = $user['id'];
            echo "Found demo teacher user with ID: " . $userId . "\n";
        }

        // Create or update teacher record
        $teacher = $db->table('teachers')->where('user_id', $userId)->get()->getRowArray();
        $teacherData = [
            'user_id' => $userId,
            'teacher_id' => 'DEMO-T001',
            'first_name' => 'Demo',
            'last_name' => 'Teacher',
            'gender' => 'Male',
            'email' => 'demo.teacher@lphs.edu',
            'employment_status' => 'active',
            'position' => 'Teacher',
            'department' => 'Academic'
        ];

        if ($teacher) {
            $db->table('teachers')->where('id', $teacher['id'])->update($teacherData);
            $teacherId = $teacher['id'];
            echo "Updated existing teacher record\n";
        } else {
            $db->table('teachers')->insert($teacherData);
            $teacherId = $db->insertID();
            echo "Created new teacher record with ID: " . $teacherId . "\n";
        }

        // Create section and students
        $sectionData = [
            'section_name' => 'Grade 10 - Aristotle',
            'grade_level' => 10,
            'adviser_id' => $teacherId,
            'school_year' => '2024-2025',
            'max_capacity' => 40,
            'is_active' => 1
        ];

        $section = $db->table('sections')->where('section_name', 'Grade 10 - Aristotle')->get()->getRowArray();
        if ($section) {
            $db->table('sections')->where('id', $section['id'])->update($sectionData);
            $sectionId = $section['id'];
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
            if (!$existing) {
                $db->table('students')->insert($fullStudentData);
            }
        }

        echo "Demo teacher setup complete with 8 students in Grade 10 - Aristotle section\n";
    }
}