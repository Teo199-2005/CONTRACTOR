<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompleteDemoTeacherSetupSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Get demo teacher
        $teacher = $db->table('teachers')->where('teacher_id', 'DEMO-T001')->get()->getRowArray();
        if (!$teacher) {
            echo "Demo teacher not found\n";
            return;
        }

        echo "Found teacher ID: " . $teacher['id'] . "\n";

        // Update section to ensure correct adviser_id
        $db->table('sections')
            ->where('section_name', 'Grade 10 - Aristotle')
            ->update(['adviser_id' => $teacher['id']]);

        // Create subjects for Grade 10
        $subjects = [
            ['subject_code' => 'ENG10', 'subject_name' => 'English 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'FIL10', 'subject_name' => 'Filipino 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'MATH10', 'subject_name' => 'Mathematics 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'SCI10', 'subject_name' => 'Science 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'AP10', 'subject_name' => 'Araling Panlipunan 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'MAPEH10', 'subject_name' => 'MAPEH 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'TLE10', 'subject_name' => 'Technology and Livelihood Education 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
            ['subject_code' => 'ESP10', 'subject_name' => 'Edukasyon sa Pagpapakatao 10', 'grade_level' => 10, 'units' => 1, 'is_active' => 1],
        ];

        foreach ($subjects as $subject) {
            $existing = $db->table('subjects')
                ->where('subject_code', $subject['subject_code'])
                ->where('grade_level', $subject['grade_level'])
                ->get()->getRowArray();
            
            if (!$existing) {
                $db->table('subjects')->insert($subject);
            }
        }

        // Verify students are in the section
        $section = $db->table('sections')->where('adviser_id', $teacher['id'])->get()->getRowArray();
        if ($section) {
            $studentCount = $db->table('students')
                ->where('section_id', $section['id'])
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
            
            echo "Section ID: " . $section['id'] . " has " . $studentCount . " students\n";
        }

        echo "Demo teacher setup completed\n";
    }
}