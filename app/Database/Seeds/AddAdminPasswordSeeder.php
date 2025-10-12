<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAdminPasswordSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Find admin user by username
        $admin = $db->table('users')->where('username', 'admin')->get()->getRow();
        if ($admin) {
            // Check if admin has password
            $identity = $db->table('auth_identities')->where('user_id', $admin->id)->get()->getRow();
            if (!$identity) {
                $db->table('auth_identities')->insert([
                    'user_id' => $admin->id,
                    'type' => 'email_password',
                    'name' => $admin->email ?: 'admin@lphs.edu',
                    'secret' => password_hash('admin123', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                echo "Added password for admin user ID: {$admin->id}\n";
            } else {
                echo "Admin already has password\n";
            }
            
            // Update email if empty
            if (empty($admin->email)) {
                $db->table('users')->where('id', $admin->id)->update(['email' => 'admin@lphs.edu']);
                echo "Updated admin email\n";
            }
        } else {
            echo "No admin user found\n";
        }
    }
}