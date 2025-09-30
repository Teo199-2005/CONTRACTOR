<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class AuthSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $users = model(UserModel::class);
        $email = 'admin@lphs.edu';
        $existing = $users->where('email', $email)->first();
        if (! $existing) {
            $user = new User([
                'username' => 'admin',
                'email'    => $email,
                'password' => 'ChangeMe123!',
            ]);
            $users->save($user);
            $userId = (int) $users->getInsertID();
            // Assign group directly in groups_users table
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group'   => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}

