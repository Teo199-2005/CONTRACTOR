<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddGrade11And12StudentsSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Get Grade 11 and 12 sections
        $grade11Sections = $db->table('sections')->where('grade_level', 11)->get()->getResultArray();
        $grade12Sections = $db->table('sections')->where('grade_level', 12)->get()->getResultArray();
        
        // Sample student names for Grade 11 and 12
        $seniorHighStudents = [
            ['first_name' => 'Alexandra', 'last_name' => 'Santos', 'gender' => 'Female'],
            ['first_name' => 'Benjamin', 'last_name' => 'Cruz', 'gender' => 'Male'],
            ['first_name' => 'Catherine', 'last_name' => 'Reyes', 'gender' => 'Female'],
            ['first_name' => 'Daniel', 'last_name' => 'Garcia', 'gender' => 'Male'],
            ['first_name' => 'Evangeline', 'last_name' => 'Lopez', 'gender' => 'Female'],
            ['first_name' => 'Francis', 'last_name' => 'Martinez', 'gender' => 'Male'],
            ['first_name' => 'Gabrielle', 'last_name' => 'Hernandez', 'gender' => 'Female'],
            ['first_name' => 'Harrison', 'last_name' => 'Gonzalez', 'gender' => 'Male'],
            ['first_name' => 'Isabella', 'last_name' => 'Torres', 'gender' => 'Female'],
            ['first_name' => 'Jonathan', 'last_name' => 'Morales', 'gender' => 'Male'],
            ['first_name' => 'Katherine', 'last_name' => 'Vargas', 'gender' => 'Female'],
            ['first_name' => 'Leonardo', 'last_name' => 'Ramos', 'gender' => 'Male'],
            ['first_name' => 'Marianne', 'last_name' => 'Jimenez', 'gender' => 'Female'],
            ['first_name' => 'Nicholas', 'last_name' => 'Castillo', 'gender' => 'Male'],
            ['first_name' => 'Olivia', 'last_name' => 'Rivera', 'gender' => 'Female'],
            ['first_name' => 'Patrick', 'last_name' => 'Mendoza', 'gender' => 'Male'],
            ['first_name' => 'Queenie', 'last_name' => 'Flores', 'gender' => 'Female'],
            ['first_name' => 'Ricardo', 'last_name' => 'Valdez', 'gender' => 'Male'],
            ['first_name' => 'Stephanie', 'last_name' => 'Aguilar', 'gender' => 'Female'],
            ['first_name' => 'Theodore', 'last_name' => 'Medina', 'gender' => 'Male']
        ];
        
        // Get last LRN to continue sequence
        $lastStudent = $db->table('students')->select('lrn')->orderBy('lrn', 'DESC')->get()->getRowArray();
        $nextLrn = $lastStudent ? intval($lastStudent['lrn']) + 1 : 100000000001;
        
        $studentsData = [];
        
        // Add Grade 11 students
        if (!empty($grade11Sections)) {
            $sectionIndex = 0;
            $studentsPerSection = 10; // 10 students per section
            $currentSectionCount = 0;
            
            for ($i = 0; $i < 20; $i++) { // 20 Grade 11 students
                $student = $seniorHighStudents[$i % count($seniorHighStudents)];
                $currentSection = $grade11Sections[$sectionIndex];
                
                $studentsData[] = [
                    'lrn' => (string)$nextLrn,
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'gender' => $student['gender'],
                    'date_of_birth' => '2007-' . rand(1, 12) . '-' . rand(1, 28),
                    'grade_level' => 11,
                    'section_id' => $currentSection['id'],
                    'enrollment_status' => 'enrolled',
                    'school_year' => '2025-2026',
                    'emergency_contact_name' => 'Parent/Guardian',
                    'emergency_contact_number' => '09' . rand(100000000, 999999999),
                    'emergency_contact_relationship' => 'Parent',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $nextLrn++;
                $currentSectionCount++;
                
                if ($currentSectionCount >= $studentsPerSection && $sectionIndex < count($grade11Sections) - 1) {
                    $sectionIndex++;
                    $currentSectionCount = 0;
                }
            }
        }
        
        // Add Grade 12 students
        if (!empty($grade12Sections)) {
            $sectionIndex = 0;
            $studentsPerSection = 10; // 10 students per section
            $currentSectionCount = 0;
            
            for ($i = 0; $i < 20; $i++) { // 20 Grade 12 students
                $student = $seniorHighStudents[$i % count($seniorHighStudents)];
                $currentSection = $grade12Sections[$sectionIndex];
                
                $studentsData[] = [
                    'lrn' => (string)$nextLrn,
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'gender' => $student['gender'],
                    'date_of_birth' => '2006-' . rand(1, 12) . '-' . rand(1, 28),
                    'grade_level' => 12,
                    'section_id' => $currentSection['id'],
                    'enrollment_status' => 'enrolled',
                    'school_year' => '2025-2026',
                    'emergency_contact_name' => 'Parent/Guardian',
                    'emergency_contact_number' => '09' . rand(100000000, 999999999),
                    'emergency_contact_relationship' => 'Parent',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $nextLrn++;
                $currentSectionCount++;
                
                if ($currentSectionCount >= $studentsPerSection && $sectionIndex < count($grade12Sections) - 1) {
                    $sectionIndex++;
                    $currentSectionCount = 0;
                }
            }
        }
        
        // Insert all students
        if (!empty($studentsData)) {
            $db->table('students')->insertBatch($studentsData);
            echo "Added " . count($studentsData) . " senior high school students.\n";
        }
        
        // Update section enrollment counts
        foreach (array_merge($grade11Sections, $grade12Sections) as $section) {
            $count = $db->table('students')
                ->where('section_id', $section['id'])
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
            
            $db->table('sections')
                ->where('id', $section['id'])
                ->update(['current_enrollment' => $count]);
            
            echo "Section {$section['section_name']} (Grade {$section['grade_level']}): $count students\n";
        }
        
        echo "Grade 11 and 12 students added successfully!\n";
    }
}