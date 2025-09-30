<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PhilippineSectionsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Clear existing sections first
        $db->table('sections')->truncate();
        
        // LPHS Section Names
        $sectionNames = [
            // Grade 7 Sections
            7 => [
                'Aphrodite',
                'Belus'
            ],
            
            // Grade 8 Sections
            8 => [
                'Aesop',
                'Bower'
            ],
            
            // Grade 9 Sections
            9 => [
                'Argon',
                'Beryllium'
            ],
            
            // Grade 10 Sections
            10 => [
                'Aristotle',
                'Bartley'
            ],
            
            // Grade 11 Sections
            11 => [
                'Garnet',
                'Tanzanite'
            ],
            
            // Grade 12 Sections
            12 => [
                'Gold',
                'Tangerine'
            ]
        ];
        
        $sectionsData = [];
        $currentYear = date('Y');
        $schoolYear = $currentYear . '-' . ($currentYear + 1);
        
        foreach ($sectionNames as $gradeLevel => $sections) {
            foreach ($sections as $sectionName) {
                $sectionsData[] = [
                    'section_name' => $sectionName,
                    'grade_level' => $gradeLevel,
                    'school_year' => $schoolYear,
                    'adviser_id' => null, // Will be assigned later when teachers are added
                    'max_capacity' => 40, // Standard Philippine classroom capacity
                    'current_enrollment' => 0,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        // Insert all sections
        $db->table('sections')->insertBatch($sectionsData);
        
        echo "Philippine school sections created successfully!\n";
        echo "Created " . count($sectionsData) . " sections across 4 grade levels.\n";
        
        // Display created sections
        echo "\nSections created:\n";
        foreach ($sectionNames as $gradeLevel => $sections) {
            echo "Grade $gradeLevel:\n";
            foreach ($sections as $sectionName) {
                echo "  - $sectionName\n";
            }
        }
    }
}
