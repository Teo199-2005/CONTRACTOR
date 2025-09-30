<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;
use App\Models\TeacherModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\SectionModel;
use App\Models\SubjectModel;
use App\Models\GradeModel;
use App\Models\NotificationModel;
use App\Models\AnnouncementModel;
use App\Models\EnrollmentDocumentModel;

class CompleteDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Clear existing data first (optional - comment out if you want to keep existing data)
        // $this->clearExistingData();
        
        // Seed all tables with comprehensive data
        $this->seedUsers();
        $this->seedTeachers();
        $this->seedStudents();
        $this->seedParents();
        $this->seedStudentParents();
        $this->seedGrades();
        $this->seedNotifications();
        $this->seedEnrollmentDocuments();
        $this->seedAdditionalAnnouncements();
        
        echo "Complete data seeding finished!\n";
    }
    
    private function seedUsers()
    {
        $users = model(UserModel::class);
        $password = 'DemoPass123!';
        
        // Create additional users for testing
        $userData = [
            ['email' => 'teacher1@lphs.edu', 'group' => 'teacher'],
            ['email' => 'teacher2@lphs.edu', 'group' => 'teacher'],
            ['email' => 'teacher3@lphs.edu', 'group' => 'teacher'],
            ['email' => 'student1@lphs.edu', 'group' => 'student'],
            ['email' => 'student2@lphs.edu', 'group' => 'student'],
            ['email' => 'student3@lphs.edu', 'group' => 'student'],
            ['email' => 'student4@lphs.edu', 'group' => 'student'],
            ['email' => 'student5@lphs.edu', 'group' => 'student'],
            ['email' => 'parent1@lphs.edu', 'group' => 'parent'],
            ['email' => 'parent2@lphs.edu', 'group' => 'parent'],
            ['email' => 'parent3@lphs.edu', 'group' => 'parent'],
        ];
        
        foreach ($userData as $userInfo) {
            $this->createUserIfMissing($users, $userInfo['email'], $password, $userInfo['group']);
        }
    }
    
    private function seedTeachers()
    {
        $tm = new TeacherModel();
        
        $teachers = [
            [
                'user_id' => $this->getUserIdByEmail('teacher1@lphs.edu'),
                'teacher_id' => 'T001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'gender' => 'Female',
                'email' => 'teacher1@lphs.edu',
                'employment_status' => 'active',
                'specialization' => 'Mathematics',
                'contact_number' => '09123456789',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('teacher2@lphs.edu'),
                'teacher_id' => 'T002',
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'gender' => 'Male',
                'email' => 'teacher2@lphs.edu',
                'employment_status' => 'active',
                'specialization' => 'Science',
                'contact_number' => '09123456790',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('teacher3@lphs.edu'),
                'teacher_id' => 'T003',
                'first_name' => 'Ana',
                'last_name' => 'Reyes',
                'gender' => 'Female',
                'email' => 'teacher3@lphs.edu',
                'employment_status' => 'active',
                'specialization' => 'English',
                'contact_number' => '09123456791',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($teachers as $teacher) {
            if ($teacher['user_id']) {
                $exists = $tm->where('user_id', $teacher['user_id'])->first();
                if (!$exists) {
                    $tm->insert($teacher);
                }
            }
        }
    }
    
    private function seedStudents()
    {
        $sm = new StudentModel();
        
        $students = [
            [
                'user_id' => $this->getUserIdByEmail('student1@lphs.edu'),
                'first_name' => 'Pedro',
                'last_name' => 'Garcia',
                'gender' => 'Male',
                'date_of_birth' => '2010-03-15',
                'email' => 'student1@lphs.edu',
                'enrollment_status' => 'enrolled',
                'grade_level' => 7,
                'school_year' => '2024-2025',
                'student_id' => '2024-001',
                'section_id' => 1,
                'address' => '123 Main St, City',
                'contact_number' => '09123456792',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('student2@lphs.edu'),
                'first_name' => 'Maria',
                'last_name' => 'Lopez',
                'gender' => 'Female',
                'date_of_birth' => '2010-07-22',
                'email' => 'student2@lphs.edu',
                'enrollment_status' => 'enrolled',
                'grade_level' => 7,
                'school_year' => '2024-2025',
                'student_id' => '2024-002',
                'section_id' => 1,
                'address' => '456 Oak Ave, City',
                'contact_number' => '09123456793',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('student3@lphs.edu'),
                'first_name' => 'Jose',
                'last_name' => 'Martinez',
                'gender' => 'Male',
                'date_of_birth' => '2009-11-08',
                'email' => 'student3@lphs.edu',
                'enrollment_status' => 'enrolled',
                'grade_level' => 8,
                'school_year' => '2024-2025',
                'student_id' => '2024-003',
                'section_id' => 4,
                'address' => '789 Pine St, City',
                'contact_number' => '09123456794',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('student4@lphs.edu'),
                'first_name' => 'Carmen',
                'last_name' => 'Rodriguez',
                'gender' => 'Female',
                'date_of_birth' => '2009-05-12',
                'email' => 'student4@lphs.edu',
                'enrollment_status' => 'enrolled',
                'grade_level' => 8,
                'school_year' => '2024-2025',
                'student_id' => '2024-004',
                'section_id' => 4,
                'address' => '321 Elm St, City',
                'contact_number' => '09123456795',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('student5@lphs.edu'),
                'first_name' => 'Antonio',
                'last_name' => 'Hernandez',
                'gender' => 'Male',
                'date_of_birth' => '2008-09-30',
                'email' => 'student5@lphs.edu',
                'enrollment_status' => 'enrolled',
                'grade_level' => 9,
                'school_year' => '2024-2025',
                'student_id' => '2024-005',
                'section_id' => 7,
                'address' => '654 Maple Ave, City',
                'contact_number' => '09123456796',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($students as $student) {
            if ($student['user_id']) {
                $exists = $sm->where('user_id', $student['user_id'])->first();
                if (!$exists) {
                    $sm->insert($student);
                }
            }
        }
    }
    
    private function seedParents()
    {
        $pm = new ParentModel();
        
        $parents = [
            [
                'user_id' => $this->getUserIdByEmail('parent1@lphs.edu'),
                'first_name' => 'Roberto',
                'last_name' => 'Garcia',
                'email' => 'parent1@lphs.edu',
                'contact_number' => '09123456797',
                'address' => '123 Main St, City',
                'occupation' => 'Engineer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('parent2@lphs.edu'),
                'first_name' => 'Elena',
                'last_name' => 'Lopez',
                'email' => 'parent2@lphs.edu',
                'contact_number' => '09123456798',
                'address' => '456 Oak Ave, City',
                'occupation' => 'Teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('parent3@lphs.edu'),
                'first_name' => 'Carlos',
                'last_name' => 'Martinez',
                'email' => 'parent3@lphs.edu',
                'contact_number' => '09123456799',
                'address' => '789 Pine St, City',
                'occupation' => 'Doctor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($parents as $parent) {
            if ($parent['user_id']) {
                $exists = $pm->where('user_id', $parent['user_id'])->first();
                if (!$exists) {
                    $pm->insert($parent);
                }
            }
        }
    }
    
    private function seedStudentParents()
    {
        $db = \Config\Database::connect();
        
        $studentParents = [
            ['student_id' => 1, 'parent_id' => 1, 'relationship' => 'Father'],
            ['student_id' => 2, 'parent_id' => 2, 'relationship' => 'Mother'],
            ['student_id' => 3, 'parent_id' => 3, 'relationship' => 'Father'],
            ['student_id' => 4, 'parent_id' => 2, 'relationship' => 'Mother'],
            ['student_id' => 5, 'parent_id' => 1, 'relationship' => 'Father']
        ];
        
        foreach ($studentParents as $sp) {
            $exists = $db->table('student_parents')->where('student_id', $sp['student_id'])->where('parent_id', $sp['parent_id'])->first();
            if (!$exists) {
                $db->table('student_parents')->insert($sp);
            }
        }
    }
    
    private function seedGrades()
    {
        $gm = new GradeModel();
        
        $grades = [
            // Student 1 - Grade 7
            ['student_id' => 1, 'subject_id' => 1, 'quarter' => 1, 'grade' => 85, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 1, 'subject_id' => 2, 'quarter' => 1, 'grade' => 88, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 1, 'subject_id' => 3, 'quarter' => 1, 'grade' => 82, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            
            // Student 2 - Grade 7
            ['student_id' => 2, 'subject_id' => 1, 'quarter' => 1, 'grade' => 90, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 2, 'subject_id' => 2, 'quarter' => 1, 'grade' => 87, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 2, 'subject_id' => 3, 'quarter' => 1, 'grade' => 89, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            
            // Student 3 - Grade 8
            ['student_id' => 3, 'subject_id' => 9, 'quarter' => 1, 'grade' => 83, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 3, 'subject_id' => 10, 'quarter' => 1, 'grade' => 86, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
            ['student_id' => 3, 'subject_id' => 11, 'quarter' => 1, 'grade' => 81, 'remarks' => 'Passed', 'created_at' => date('Y-m-d H:i:s')],
        ];
        
        foreach ($grades as $grade) {
            $exists = $gm->where('student_id', $grade['student_id'])->where('subject_id', $grade['subject_id'])->where('quarter', $grade['quarter'])->first();
            if (!$exists) {
                $gm->insert($grade);
            }
        }
    }
    
    private function seedNotifications()
    {
        $nm = new NotificationModel();
        
        $notifications = [
            [
                'user_id' => $this->getUserIdByEmail('student1@lphs.edu'),
                'title' => 'Grade Update Available',
                'message' => 'Your Quarter 1 grades have been posted. Please check your dashboard.',
                'type' => 'grade_update',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('parent1@lphs.edu'),
                'title' => 'Child\'s Progress Report',
                'message' => 'Pedro\'s Quarter 1 progress report is now available.',
                'type' => 'progress_report',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $this->getUserIdByEmail('teacher1@lphs.edu'),
                'title' => 'New Student Assignment',
                'message' => 'You have been assigned to teach Mathematics 7 - St. Francis section.',
                'type' => 'assignment',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($notifications as $notification) {
            if ($notification['user_id']) {
                $exists = $nm->where('user_id', $notification['user_id'])->where('title', $notification['title'])->first();
                if (!$exists) {
                    $nm->insert($notification);
                }
            }
        }
    }
    
    private function seedEnrollmentDocuments()
    {
        $edm = new EnrollmentDocumentModel();
        
        $documents = [
            [
                'student_id' => 1,
                'document_type' => 'birth_certificate',
                'file_name' => 'birth_cert_pedro_garcia.pdf',
                'file_path' => 'uploads/documents/birth_cert_pedro_garcia.pdf',
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'student_id' => 1,
                'document_type' => 'report_card',
                'file_name' => 'report_card_pedro_garcia.pdf',
                'file_path' => 'uploads/documents/report_card_pedro_garcia.pdf',
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'student_id' => 2,
                'document_type' => 'birth_certificate',
                'file_name' => 'birth_cert_maria_lopez.pdf',
                'file_path' => 'uploads/documents/birth_cert_maria_lopez.pdf',
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($documents as $document) {
            $exists = $edm->where('student_id', $document['student_id'])->where('document_type', $document['document_type'])->first();
            if (!$exists) {
                $edm->insert($document);
            }
        }
    }
    
    private function seedAdditionalAnnouncements()
    {
        $am = new AnnouncementModel();
        
        $announcements = [
            [
                'title' => 'Parent-Teacher Conference Schedule',
                'slug' => 'parent-teacher-conference-schedule',
                'body' => 'Parent-Teacher conferences will be held on October 15-16, 2024. Please check the schedule posted on the bulletin board.',
                'target_roles' => 'parent,teacher',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Mathematics Competition Winners',
                'slug' => 'mathematics-competition-winners',
                'body' => 'Congratulations to our students who won in the Regional Mathematics Competition. Special recognition to Pedro Garcia (1st Place) and Maria Lopez (2nd Place).',
                'target_roles' => 'all',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Library Week Activities',
                'slug' => 'library-week-activities',
                'body' => 'Join us for Library Week from November 10-14, 2024. Activities include book fairs, reading challenges, and author visits.',
                'target_roles' => 'student,teacher',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($announcements as $announcement) {
            $exists = $am->where('slug', $announcement['slug'])->first();
            if (!$exists) {
                $am->insert($announcement);
            }
        }
    }
    
    private function createUserIfMissing(UserModel $users, string $email, string $password, string $group): ?int
    {
        $db = \Config\Database::connect();
        $existing = $users->where('email', $email)->first();
        if ($existing) {
            return (int) $existing->id;
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
    
    private function getUserIdByEmail(string $email): ?int
    {
        $users = model(UserModel::class);
        $user = $users->where('email', $email)->first();
        return $user ? (int) $user->id : null;
    }
    
    private function clearExistingData()
    {
        $db = \Config\Database::connect();
        
        // Clear tables in reverse dependency order
        $tables = [
            'grades',
            'student_parents', 
            'enrollment_documents',
            'notifications',
            'students',
            'teachers',
            'parents',
            'announcements'
        ];
        
        foreach ($tables as $table) {
            $db->table($table)->truncate();
        }
        
        echo "Existing data cleared.\n";
    }
}



