<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DebugLogin extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:login';
    protected $description = 'Debug login issues';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $lrn = $params[0] ?? '740239857139';
        
        CLI::write("=== DEBUGGING LOGIN FOR LRN: {$lrn} ===", 'yellow');
        
        // Check if student exists
        $student = $db->query("SELECT * FROM students WHERE lrn = ?", [$lrn])->getRow();
        if ($student) {
            CLI::write("✓ Student found: ID {$student->id}, Name: {$student->first_name} {$student->last_name}");
            CLI::write("  User ID: {$student->user_id}");
            CLI::write("  Status: {$student->enrollment_status}");
            
            if ($student->user_id) {
                // Check user account
                $user = $db->query("SELECT * FROM users WHERE id = ?", [$student->user_id])->getRow();
                if ($user) {
                    CLI::write("✓ User account found: {$user->email}");
                    CLI::write("  Active: " . ($user->active ? 'Yes' : 'No'));
                    
                    // Check auth identity
                    $identity = $db->query("SELECT * FROM auth_identities WHERE user_id = ?", [$student->user_id])->getRow();
                    if ($identity) {
                        CLI::write("✓ Auth identity found");
                        CLI::write("  Type: {$identity->type}");
                        CLI::write("  Name: {$identity->name}");
                        CLI::write("  Has secret: " . (!empty($identity->secret) ? 'Yes' : 'No'));
                    } else {
                        CLI::write("✗ No auth identity found");
                    }
                    
                    // Check group assignment
                    $group = $db->query("SELECT * FROM auth_groups_users WHERE user_id = ?", [$student->user_id])->getRow();
                    if ($group) {
                        CLI::write("✓ Group assignment: {$group->group}");
                    } else {
                        CLI::write("✗ No group assignment found");
                    }
                } else {
                    CLI::write("✗ User account not found for user_id: {$student->user_id}");
                }
            } else {
                CLI::write("✗ Student has no user_id");
            }
        } else {
            CLI::write("✗ Student not found with LRN: {$lrn}");
            
            // Show all students
            CLI::write("\n=== ALL STUDENTS ===", 'yellow');
            $allStudents = $db->query("SELECT id, lrn, first_name, last_name, user_id FROM students LIMIT 10")->getResult();
            foreach ($allStudents as $s) {
                CLI::write("ID: {$s->id}, LRN: {$s->lrn}, Name: {$s->first_name} {$s->last_name}, User ID: {$s->user_id}");
            }
        }
    }
}