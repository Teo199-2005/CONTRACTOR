<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanupSectionsSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Delete duplicate/incorrect sections
        $sectionsToDelete = [
            'Grade 7 - Einstein',
            'Grade 7 - Newton', 
            'Grade 8 - Einstein',
            'Grade 9 - Galileo',
            'Grade 10 - Darwin'
        ];
        
        foreach ($sectionsToDelete as $sectionName) {
            $deleted = $db->table('sections')
                ->where('section_name', $sectionName)
                ->delete();
            
            if ($deleted) {
                echo "Deleted section: $sectionName\n";
            }
        }
        
        echo "Section cleanup completed!\n";
        
        // Show remaining sections
        $sections = $db->table('sections')
            ->orderBy('grade_level', 'ASC')
            ->orderBy('section_name', 'ASC')
            ->get()
            ->getResultArray();
            
        echo "\nRemaining sections:\n";
        foreach ($sections as $section) {
            echo "Grade {$section['grade_level']}: {$section['section_name']} ({$section['current_enrollment']}/{$section['max_capacity']})\n";
        }
    }
}