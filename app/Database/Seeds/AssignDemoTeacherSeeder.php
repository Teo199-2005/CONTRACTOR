<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AssignDemoTeacherSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Get demo teacher
        $teacher = $db->table('teachers')->where('teacher_id', 'DEMO-T001')->get()->getRowArray();
        if (!$teacher) {
            $teacher = $db->table('teachers')->where('first_name', 'Demo')->where('last_name', 'Teacher')->get()->getRowArray();
        }
        if (!$teacher) {
            echo "Demo teacher not found\n";
            return;
        }

        // Create/update section for demo teacher
        $sectionData = [
            'section_name' => 'Grade 10 - Aristotle',
            'grade_level' => 'Grade 10',
            'adviser_id' => $teacher['id'],
            'school_year' => '2024-2025',
            'max_students' => 40,
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

        // Create sample students for this section
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
            $existing = $db->table('students')->where('lrn', $studentData['lrn'])->get()->getRowArray();
            
            $fullStudentData = array_merge($studentData, [
                'section_id' => $sectionId,
                'grade_level' => 'Grade 10',
                'enrollment_status' => 'enrolled',
                'school_year' => '2024-2025',
                'date_of_birth' => '2009-01-01',
                'address' => 'Sample Address',
                'contact_number' => '09123456789',
                'emergency_contact_name' => 'Parent Name',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent'
            ]);

            if ($existing) {
                $db->table('students')->where('id', $existing['id'])->update($fullStudentData);
            } else {
                $db->table('students')->insert($fullStudentData);
            }
        }

        echo "Demo teacher assigned to section with " . count($students) . " students\n";
    }
}