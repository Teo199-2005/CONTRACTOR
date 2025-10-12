<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixDemoTeacherIdSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Update demo teacher to have teacher_id
        $db->table('teachers')
            ->where('email', 'demo.teacher@lphs.edu')
            ->update(['teacher_id' => 'DEMO-T001']);

        echo "Fixed demo teacher teacher_id\n";
    }
}