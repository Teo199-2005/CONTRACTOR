<?php

// Bootstrap CodeIgniter
require_once 'vendor/autoload.php';

// Load the path config
$pathsConfig = require_once 'app/Config/Paths.php';

// Set up the environment
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', $pathsConfig->systemDirectory);
define('APPPATH', $pathsConfig->appDirectory);
define('WRITEPATH', $pathsConfig->writableDirectory);
define('TESTPATH', $pathsConfig->testsDirectory);

// Load the framework bootstrap
require_once SYSTEMPATH . 'bootstrap.php';

// Get database connection
$db = \Config\Database::connect();

try {
    echo "Adding sample data for teacher analytics...\n";

    // First, let's check if we have a teacher user
    $teacherUser = $db->table('users')
        ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
        ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
        ->where('auth_groups.name', 'teacher')
        ->get()
        ->getFirstRow();

    if (!$teacherUser) {
        echo "No teacher user found. Creating one...\n";
        
        // Create teacher user
        $userData = [
            'username' => 'teacher1',
            'email' => 'teacher1@lphs.edu',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $db->table('users')->insert($userData);
        $teacherUserId = $db->insertID();
        
        // Add to teacher group
        $teacherGroupId = $db->table('auth_groups')->where('name', 'teacher')->get()->getFirstRow()->id;
        $db->table('auth_groups_users')->insert([
            'user_id' => $teacherUserId,
            'group_id' => $teacherGroupId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "Created teacher user with ID: $teacherUserId\n";
    } else {
        $teacherUserId = $teacherUser->user_id;
        echo "Found existing teacher user with ID: $teacherUserId\n";
    }

    // Check if teacher record exists
    $teacherRecord = $db->table('teachers')->where('user_id', $teacherUserId)->get()->getFirstRow();
    
    if (!$teacherRecord) {
        echo "Creating teacher record...\n";
        
        $teacherData = [
            'user_id' => $teacherUserId,
            'employee_id' => 'T001',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => 'Cruz',
            'email' => 'teacher1@lphs.edu',
            'phone' => '09123456789',
            'address' => '123 Teacher St, City',
            'date_of_birth' => '1985-05-15',
            'gender' => 'Female',
            'employment_status' => 'active',
            'hire_date' => '2020-06-01',
            'department' => 'Mathematics',
            'position' => 'Senior High School Teacher',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $db->table('teachers')->insert($teacherData);
        $teacherId = $db->insertID();
        echo "Created teacher record with ID: $teacherId\n";
    } else {
        $teacherId = $teacherRecord->id;
        echo "Found existing teacher record with ID: $teacherId\n";
    }

    // Create sections and assign teacher as adviser
    echo "Creating sections...\n";
    
    $sections = [
        ['section_name' => 'Grade 7 - Einstein', 'grade_level' => 7, 'max_capacity' => 40, 'current_enrollment' => 0, 'adviser_id' => $teacherId],
        ['section_name' => 'Grade 8 - Newton', 'grade_level' => 8, 'max_capacity' => 40, 'current_enrollment' => 0, 'adviser_id' => null],
    ];
    
    $sectionIds = [];
    foreach ($sections as $sectionData) {
        $sectionData['school_year'] = '2024-2025';
        $sectionData['is_active'] = true;
        $sectionData['created_at'] = date('Y-m-d H:i:s');
        $sectionData['updated_at'] = date('Y-m-d H:i:s');
        
        // Check if section already exists
        $existingSection = $db->table('sections')
            ->where('section_name', $sectionData['section_name'])
            ->where('school_year', $sectionData['school_year'])
            ->get()
            ->getFirstRow();
            
        if (!$existingSection) {
            $db->table('sections')->insert($sectionData);
            $sectionIds[] = $db->insertID();
            echo "Created section: {$sectionData['section_name']}\n";
        } else {
            $sectionIds[] = $existingSection->id;
            echo "Found existing section: {$sectionData['section_name']}\n";
        }
    }

    // Create subjects
    echo "Creating subjects...\n";
    
    $subjects = [
        ['subject_name' => 'Mathematics 7', 'subject_code' => 'MATH7', 'grade_level' => 7, 'is_active' => true],
        ['subject_name' => 'Science 7', 'subject_code' => 'SCI7', 'grade_level' => 7, 'is_active' => true],
        ['subject_name' => 'English 7', 'subject_code' => 'ENG7', 'grade_level' => 7, 'is_active' => true],
        ['subject_name' => 'Filipino 7', 'subject_code' => 'FIL7', 'grade_level' => 7, 'is_active' => true],
        ['subject_name' => 'Mathematics 8', 'subject_code' => 'MATH8', 'grade_level' => 8, 'is_active' => true],
        ['subject_name' => 'Science 8', 'subject_code' => 'SCI8', 'grade_level' => 8, 'is_active' => true],
    ];
    
    $subjectIds = [];
    foreach ($subjects as $subjectData) {
        $subjectData['created_at'] = date('Y-m-d H:i:s');
        $subjectData['updated_at'] = date('Y-m-d H:i:s');
        
        $existingSubject = $db->table('subjects')
            ->where('subject_code', $subjectData['subject_code'])
            ->get()
            ->getFirstRow();
            
        if (!$existingSubject) {
            $db->table('subjects')->insert($subjectData);
            $subjectIds[] = $db->insertID();
            echo "Created subject: {$subjectData['subject_name']}\n";
        } else {
            $subjectIds[] = $existingSubject->id;
            echo "Found existing subject: {$subjectData['subject_name']}\n";
        }
    }

    // Create sample students
    echo "Creating sample students...\n";
    
    $students = [
        ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'grade_level' => 7, 'gender' => 'Male'],
        ['first_name' => 'Maria', 'last_name' => 'Santos', 'grade_level' => 7, 'gender' => 'Female'],
        ['first_name' => 'Pedro', 'last_name' => 'Garcia', 'grade_level' => 7, 'gender' => 'Male'],
        ['first_name' => 'Ana', 'last_name' => 'Rodriguez', 'grade_level' => 7, 'gender' => 'Female'],
        ['first_name' => 'Jose', 'last_name' => 'Martinez', 'grade_level' => 7, 'gender' => 'Male'],
        ['first_name' => 'Carmen', 'last_name' => 'Lopez', 'grade_level' => 7, 'gender' => 'Female'],
        ['first_name' => 'Miguel', 'last_name' => 'Hernandez', 'grade_level' => 7, 'gender' => 'Male'],
        ['first_name' => 'Sofia', 'last_name' => 'Gonzalez', 'grade_level' => 7, 'gender' => 'Female'],
        ['first_name' => 'Carlos', 'last_name' => 'Perez', 'grade_level' => 7, 'gender' => 'Male'],
        ['first_name' => 'Isabella', 'last_name' => 'Torres', 'grade_level' => 7, 'gender' => 'Female'],
    ];
    
    $studentIds = [];
    foreach ($students as $index => $studentData) {
        $studentData['student_id'] = 'STU' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        $studentData['section_id'] = $sectionIds[0]; // Assign to Grade 7 section
        $studentData['enrollment_status'] = 'enrolled';
        $studentData['school_year'] = '2024-2025';
        $studentData['date_of_birth'] = '2010-01-01';
        $studentData['address'] = '123 Student St, City';
        $studentData['contact_number'] = '09123456789';
        $studentData['email'] = strtolower($studentData['first_name'] . '.' . $studentData['last_name']) . '@student.lphs.edu';
        $studentData['created_at'] = date('Y-m-d H:i:s');
        $studentData['updated_at'] = date('Y-m-d H:i:s');
        
        $existingStudent = $db->table('students')
            ->where('student_id', $studentData['student_id'])
            ->get()
            ->getFirstRow();
            
        if (!$existingStudent) {
            $db->table('students')->insert($studentData);
            $studentIds[] = $db->insertID();
            echo "Created student: {$studentData['first_name']} {$studentData['last_name']}\n";
        } else {
            $studentIds[] = $existingStudent->id;
            echo "Found existing student: {$studentData['first_name']} {$studentData['last_name']}\n";
        }
    }

    // Update section enrollment count
    $db->table('sections')->where('id', $sectionIds[0])->update(['current_enrollment' => count($studentIds)]);

    // Create sample grades
    echo "Creating sample grades...\n";
    
    $gradeSubjects = array_slice($subjectIds, 0, 4); // First 4 subjects for Grade 7
    
    foreach ($studentIds as $studentId) {
        foreach ($gradeSubjects as $subjectId) {
            // Generate realistic grades (70-95 range with some variation)
            $baseGrade = rand(75, 90);
            $variation = rand(-5, 10);
            $grade = max(70, min(95, $baseGrade + $variation));
            
            $gradeData = [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'grade' => $grade,
                'quarter' => 1,
                'school_year' => '2024-2025',
                'remarks' => $grade >= 85 ? 'Excellent' : ($grade >= 75 ? 'Good' : 'Needs Improvement'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $existingGrade = $db->table('grades')
                ->where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('quarter', 1)
                ->where('school_year', '2024-2025')
                ->get()
                ->getFirstRow();
                
            if (!$existingGrade) {
                $db->table('grades')->insert($gradeData);
            }
        }
    }
    
    echo "Sample grades created successfully!\n";
    echo "\nSample data setup complete!\n";
    echo "You can now log in as:\n";
    echo "Username: teacher1\n";
    echo "Password: password123\n";
    echo "Then go to Teacher Portal > Analytics to see the data.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}