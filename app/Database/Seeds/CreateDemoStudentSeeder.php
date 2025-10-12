<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class CreateDemoStudentSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $userModel = new UserModel();
        
        // Get a Grade 10 section
        $grade10Section = $db->table('sections')
            ->where('grade_level', 10)
            ->where('is_active', true)
            ->get()
            ->getRowArray();
        
        if (!$grade10Section) {
            echo "No Grade 10 section found!\n";
            return;
        }
        
        // Create user account
        $userData = [
            'email' => 'mariasantos67@hotmail.com',
            'password' => 'student123',
            'active' => 1
        ];
        
        // Check if user already exists
        $existingUser = $userModel->where('email', 'mariasantos67@hotmail.com')->first();
        
        if ($existingUser) {
            echo "Demo student user already exists. Updating student record...\n";
            $userId = $existingUser->id;
        } else {
            $userModel->save($userData);
            $userId = $userModel->getInsertID();
            
            // Add user to student group
            $user = $userModel->find($userId);
            $user->addGroup('student');
            echo "Created demo student user account.\n";
        }
        
        // Generate LRN
        $lastStudent = $db->table('students')->select('lrn')->orderBy('lrn', 'DESC')->get()->getRowArray();
        $nextLrn = $lastStudent ? (string)(intval($lastStudent['lrn']) + 1) : '100000000001';
        
        // Check if student record already exists
        $existingStudent = $db->table('students')->where('user_id', $userId)->get()->getRowArray();
        
        if ($existingStudent) {
            // Update existing student record
            $db->table('students')
                ->where('id', $existingStudent['id'])
                ->update([
                    'grade_level' => 10,
                    'section_id' => $grade10Section['id'],
                    'enrollment_status' => 'enrolled',
                    'school_year' => '2025-2026',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            echo "Updated existing demo student record.\n";
        } else {
            // Create new student record
            $studentData = [
                'user_id' => $userId,
                'lrn' => $nextLrn,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'gender' => 'Female',
                'date_of_birth' => '2008-03-15',
                'place_of_birth' => 'Panglao, Bohol',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'contact_number' => '09123456789',
                'address' => 'Barangay Lourdes, Panglao, Bohol',
                'emergency_contact_name' => 'Rosa Santos',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Mother',
                'grade_level' => 10,
                'section_id' => $grade10Section['id'],
                'enrollment_status' => 'enrolled',
                'school_year' => '2025-2026',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('students')->insert($studentData);
            echo "Created new demo student record.\n";
        }
        
        // Update section enrollment count
        $count = $db->table('students')
            ->where('section_id', $grade10Section['id'])
            ->where('enrollment_status', 'enrolled')
            ->countAllResults();
        
        $db->table('sections')
            ->where('id', $grade10Section['id'])
            ->update(['current_enrollment' => $count]);
        
        echo "Demo student created successfully!\n";
        echo "Email: mariasantos67@hotmail.com\n";
        echo "Password: student123\n";
        echo "Grade: 10\n";
        echo "Section: {$grade10Section['section_name']}\n";
        echo "LRN: $nextLrn\n";
    }
}