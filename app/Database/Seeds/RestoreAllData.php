<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RestoreAllData extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Add more teachers with license numbers
        $moreTeachers = [
            ['teacher_id' => '2025-0006', 'license_number' => '6789012', 'first_name' => 'Elisa', 'last_name' => 'Ereno', 'email' => 'elisa.ereno@lphs.edu.ph', 'department' => 'Mathematics', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0007', 'license_number' => '7890123', 'first_name' => 'Joven', 'last_name' => 'Labilles', 'email' => 'joven.labilles@lphs.edu.ph', 'department' => 'Science', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0008', 'license_number' => '8901234', 'first_name' => 'Laila', 'last_name' => 'Salvadora', 'email' => 'laila.salvadora@lphs.edu.ph', 'department' => 'MAPEH', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0009', 'license_number' => '9012345', 'first_name' => 'Maricar', 'last_name' => 'Sapugay', 'email' => 'maricar.sapugay@lphs.edu.ph', 'department' => 'Mathematics', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0010', 'license_number' => '0123456', 'first_name' => 'Midlyn', 'last_name' => 'Castillo', 'email' => 'midlyn.castillo@lphs.edu.ph', 'department' => 'Filipino', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0011', 'license_number' => '1357924', 'first_name' => 'Nathan David', 'last_name' => 'Dolorical', 'email' => 'nathan.dolorical@lphs.edu.ph', 'department' => 'Social Studies', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0012', 'license_number' => '2468135', 'first_name' => 'Roselle', 'last_name' => 'Plotado', 'email' => 'roselle.plotado@lphs.edu.ph', 'department' => 'Science', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0013', 'license_number' => '9876543', 'first_name' => 'Jeanette', 'last_name' => 'Rodriguez', 'email' => 'jeanette.rodriguez@lphs.edu.ph', 'department' => 'Mathematics', 'position' => 'Teacher', 'employment_status' => 'active'],
        ];
        
        foreach ($teachers as $teacher) {
            $teacher['created_at'] = date('Y-m-d H:i:s');
            $teacher['updated_at'] = date('Y-m-d H:i:s');
            $db->table('teachers')->ignore(true)->insert($teacher);
        }
        
        // Add sample students
        $students = [
            ['student_id' => '2024-001', 'first_name' => 'Demo', 'last_name' => 'Student', 'email' => 'demo.student@lphs.edu', 'grade_level' => 7, 'enrollment_status' => 'enrolled', 'gender' => 'Male'],
            ['student_id' => '2024-002', 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@lphs.edu', 'grade_level' => 8, 'enrollment_status' => 'enrolled', 'gender' => 'Male'],
            ['student_id' => '2024-003', 'first_name' => 'Jose', 'last_name' => 'Martinez', 'email' => 'jose.martinez@lphs.edu', 'grade_level' => 9, 'enrollment_status' => 'enrolled', 'gender' => 'Male'],
            ['student_id' => '2024-004', 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'email' => 'pedro.garcia@lphs.edu', 'grade_level' => 8, 'enrollment_status' => 'enrolled', 'gender' => 'Male'],
            ['student_id' => '2024-005', 'first_name' => 'Valentina', 'last_name' => 'Gutierrez', 'email' => 'valentina.gutierrez@lphs.edu', 'grade_level' => 8, 'enrollment_status' => 'enrolled', 'gender' => 'Female'],
            ['student_id' => '2024-006', 'first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'maria.santos@lphs.edu', 'grade_level' => 7, 'enrollment_status' => 'pending', 'gender' => 'Female'],
            ['student_id' => '2024-007', 'first_name' => 'Carlos', 'last_name' => 'Lopez', 'email' => 'carlos.lopez@lphs.edu', 'grade_level' => 9, 'enrollment_status' => 'pending', 'gender' => 'Male'],
        ];
        
        foreach ($students as $student) {
            $student['school_year'] = '2024-2025';
            $student['created_at'] = date('Y-m-d H:i:s');
            $student['updated_at'] = date('Y-m-d H:i:s');
            $db->table('students')->ignore(true)->insert($student);
        }
        
        echo "All data restored: Teachers with license numbers and students!\n";
    }
}