<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Grade 7
            ['subject_code' => 'ENG7', 'subject_name' => 'English 7', 'grade_level' => 7, 'units' => 3.0],
            ['subject_code' => 'MATH7', 'subject_name' => 'Mathematics 7', 'grade_level' => 7, 'units' => 3.0],
            ['subject_code' => 'SCI7', 'subject_name' => 'Science 7', 'grade_level' => 7, 'units' => 3.0],
            ['subject_code' => 'FIL7', 'subject_name' => 'Filipino 7', 'grade_level' => 7, 'units' => 3.0],
            ['subject_code' => 'AP7', 'subject_name' => 'Araling Panlipunan 7', 'grade_level' => 7, 'units' => 3.0],
            ['subject_code' => 'PE7', 'subject_name' => 'Physical Education 7', 'grade_level' => 7, 'units' => 2.0],
            
            // Grade 8
            ['subject_code' => 'ENG8', 'subject_name' => 'English 8', 'grade_level' => 8, 'units' => 3.0],
            ['subject_code' => 'MATH8', 'subject_name' => 'Mathematics 8', 'grade_level' => 8, 'units' => 3.0],
            ['subject_code' => 'SCI8', 'subject_name' => 'Science 8', 'grade_level' => 8, 'units' => 3.0],
            ['subject_code' => 'FIL8', 'subject_name' => 'Filipino 8', 'grade_level' => 8, 'units' => 3.0],
            ['subject_code' => 'AP8', 'subject_name' => 'Araling Panlipunan 8', 'grade_level' => 8, 'units' => 3.0],
            ['subject_code' => 'PE8', 'subject_name' => 'Physical Education 8', 'grade_level' => 8, 'units' => 2.0],
            
            // Grade 9
            ['subject_code' => 'ENG9', 'subject_name' => 'English 9', 'grade_level' => 9, 'units' => 3.0],
            ['subject_code' => 'MATH9', 'subject_name' => 'Mathematics 9', 'grade_level' => 9, 'units' => 3.0],
            ['subject_code' => 'SCI9', 'subject_name' => 'Science 9', 'grade_level' => 9, 'units' => 3.0],
            ['subject_code' => 'FIL9', 'subject_name' => 'Filipino 9', 'grade_level' => 9, 'units' => 3.0],
            ['subject_code' => 'AP9', 'subject_name' => 'Araling Panlipunan 9', 'grade_level' => 9, 'units' => 3.0],
            ['subject_code' => 'PE9', 'subject_name' => 'Physical Education 9', 'grade_level' => 9, 'units' => 2.0],
            
            // Grade 10
            ['subject_code' => 'ENG10', 'subject_name' => 'English 10', 'grade_level' => 10, 'units' => 3.0],
            ['subject_code' => 'MATH10', 'subject_name' => 'Mathematics 10', 'grade_level' => 10, 'units' => 3.0],
            ['subject_code' => 'SCI10', 'subject_name' => 'Science 10', 'grade_level' => 10, 'units' => 3.0],
            ['subject_code' => 'FIL10', 'subject_name' => 'Filipino 10', 'grade_level' => 10, 'units' => 3.0],
            ['subject_code' => 'AP10', 'subject_name' => 'Araling Panlipunan 10', 'grade_level' => 10, 'units' => 3.0],
            ['subject_code' => 'PE10', 'subject_name' => 'Physical Education 10', 'grade_level' => 10, 'units' => 2.0],
        ];

        foreach ($subjects as $subject) {
            $this->db->table('subjects')->insert($subject);
        }
    }
}