<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;
use App\Models\TeacherModel;
use App\Models\StudentModel;
use App\Models\ParentModel;

class DemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $users = model(UserModel::class);
        $password = 'DemoPass123!';

        $created = [];

        // Admin
        $created['admin'] = $this->createUserIfMissing($users, 'demo.admin@lphs.edu', $password, 'admin');

        // Teacher
        $teacherUserId = $this->createUserIfMissing($users, 'demo.teacher@lphs.edu', $password, 'teacher');
        if ($teacherUserId) {
            $tm = new TeacherModel();
            $exists = $tm->where('user_id', $teacherUserId)->first();
            if (! $exists) {
                $tm->insert([
                    'user_id' => $teacherUserId,
                    'teacher_id' => 'DEMO-T001',
                    'first_name' => 'Demo',
                    'last_name' => 'Teacher',
                    'gender' => 'Male',
                    'email' => 'demo.teacher@lphs.edu',
                    'employment_status' => 'active',
                ]);
            }
        }

        // Student
        $studentUserId = $this->createUserIfMissing($users, 'demo.student@lphs.edu', $password, 'student');
        if ($studentUserId) {
            $sm = new StudentModel();
            $exists = $sm->where('user_id', $studentUserId)->first();
            if (! $exists) {
                $sm->insert([
                    'user_id' => $studentUserId,
                    'first_name' => 'Demo',
                    'last_name' => 'Student',
                    'gender' => 'Male',
                    'date_of_birth' => '2010-01-01',
                    'email' => 'demo.student@lphs.edu',
                    'enrollment_status' => 'enrolled',
                    'grade_level' => 7,
                    'school_year' => '2024-2025',
                    'student_id' => $sm->createUniqueStudentId(),
                ]);
            }
        }

        // New Approved Student
        $newStudentUserId = $this->createUserIfMissing($users, 'new.student@lphs.edu', $password, 'student');
        if ($newStudentUserId) {
            $sm = new StudentModel();
            $exists = $sm->where('user_id', $newStudentUserId)->first();
            if (! $exists) {
                $sm->insert([
                    'user_id' => $newStudentUserId,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'gender' => 'Male',
                    'date_of_birth' => '2009-05-15',
                    'email' => 'new.student@lphs.edu',
                    'enrollment_status' => 'approved',
                    'grade_level' => 8,
                    'school_year' => '2024-2025',
                    'student_id' => $sm->createUniqueStudentId(),
                    'address' => '123 Main Street, City',
                    'contact_number' => '09123456789',
                    'emergency_contact_name' => 'Jane Doe',
                    'emergency_contact_number' => '09987654321',
                    'emergency_contact_relationship' => 'Mother',
                ]);
            }
        }

        // Parent
        $parentUserId = $this->createUserIfMissing($users, 'demo.parent@lphs.edu', $password, 'parent');
        if ($parentUserId) {
            $pm = new ParentModel();
            $exists = $pm->where('user_id', $parentUserId)->first();
            if (! $exists) {
                $pm->insert([
                    'user_id' => $parentUserId,
                    'first_name' => 'Demo',
                    'last_name' => 'Parent',
                    'email' => 'demo.parent@lphs.edu',
                    'contact_number' => '09123456789',
                    'address' => 'Demo Address',
                ]);
            }
        }
    }

    private function createUserIfMissing(UserModel $users, string $email, string $password, string $group): ?int
    {
        $db = \Config\Database::connect();
        
        // Check by email in users table
        $existing = $db->table('users')->where('email', $email)->get()->getRowArray();
        if ($existing) {
            // Ensure group assignment
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $existing['id'],
                'group'   => $group,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return (int) $existing['id'];
        }
        
        $user = new User([
            'email'    => $email,
            'password' => $password,
            'active'   => 1,
        ]);
        $users->save($user);
        $id = (int) $users->getInsertID();
        
        // Link to group
        $db->table('auth_groups_users')->ignore(true)->insert([
            'user_id' => $id,
            'group'   => $group,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $id;
    }
}


