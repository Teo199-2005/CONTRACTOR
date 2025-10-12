<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinalDemoTeacherSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Get demo teacher
        $teacher = $db->table('teachers')->where('user_id', 27)->get()->getRowArray();
        if (!$teacher) {
            echo "Demo teacher not found\n";
            return;
        }

        // Clear existing data
        $db->table('students')->where('section_id >', 0)->delete();
        $db->table('sections')->where('id >', 0)->delete();

        // Create section
        $sectionData = [
            'section_name' => 'Grade 10 - Aristotle',
            'grade_level' => 10,
            'adviser_id' => $teacher['id'],
            'school_year' => '2024-2025',
            'max_capacity' => 40,
            'current_enrollment' => 8,
            'is_active' => 1
        ];
        $db->table('sections')->insert($sectionData);
        $sectionId = $db->insertID();

        // Create students with unique student_id
        $students = [
            ['student_id' => 'DEMO-001', 'first_name' => 'Juan', 'last_name' => 'Cruz', 'gender' => 'Male'],
            ['student_id' => 'DEMO-002', 'first_name' => 'Maria', 'last_name' => 'Santos', 'gender' => 'Female'],
            ['student_id' => 'DEMO-003', 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'gender' => 'Male'],
            ['student_id' => 'DEMO-004', 'first_name' => 'Ana', 'last_name' => 'Lopez', 'gender' => 'Female'],
            ['student_id' => 'DEMO-005', 'first_name' => 'Jose', 'last_name' => 'Martinez', 'gender' => 'Male'],
            ['student_id' => 'DEMO-006', 'first_name' => 'Carmen', 'last_name' => 'Rodriguez', 'gender' => 'Female'],
            ['student_id' => 'DEMO-007', 'first_name' => 'Miguel', 'last_name' => 'Hernandez', 'gender' => 'Male'],
            ['student_id' => 'DEMO-008', 'first_name' => 'Sofia', 'last_name' => 'Gonzalez', 'gender' => 'Female'],
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

        echo "Created 8 students for demo teacher\n";
    }
}