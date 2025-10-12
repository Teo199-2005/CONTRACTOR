<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateDemoTeacherUserLinkSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Find user by checking both email formats
        $user = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRowArray();
        if (!$user) {
            // Try alternative email format
            $user = $db->table('users')->like('email', 'demo.teacher')->get()->getRowArray();
        }

        if (!$user) {
            echo "No demo teacher user found\n";
            return;
        }

        echo "Found user ID: " . $user['id'] . " with email: " . $user['email'] . "\n";

        // Update teacher record to link to user
        $teacher = $db->table('teachers')->where('teacher_id', 'DEMO-T001')->get()->getRowArray();
        if ($teacher) {
            $db->table('teachers')->where('id', $teacher['id'])->update(['user_id' => $user['id']]);
            echo "Updated teacher record to link to user ID: " . $user['id'] . "\n";
        } else {
            echo "Teacher record not found\n";
        }

        // Ensure user is in teacher group
        $groupExists = $db->table('auth_groups_users')
            ->where('user_id', $user['id'])
            ->where('group', 'teacher')
            ->get()->getRowArray();

        if (!$groupExists) {
            $db->table('auth_groups_users')->insert([
                'user_id' => $user['id'],
                'group' => 'teacher',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            echo "Added user to teacher group\n";
        }

        echo "Demo teacher user link updated successfully\n";
    }
}