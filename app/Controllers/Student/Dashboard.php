<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\GradeModel;
use App\Models\AnnouncementModel;
use App\Models\SubjectModel;
use App\Models\NotificationModel;

class Dashboard extends BaseController
{
    protected $auth;
    protected $studentRecord;
    
    public function __construct()
    {
        $this->auth = auth();
        helper('time');
    }

    protected function getStudentRecord()
    {
        if (!$this->studentRecord) {
            $studentModel = new StudentModel();
            $this->studentRecord = $studentModel->where('user_id', $this->auth->id())->first();
        }
        return $this->studentRecord;
    }

    /**
     * Generate performance message based on grade average
     */
    protected function getPerformanceMessage($average)
    {
        if ($average === null) {
            return ['message' => 'No grades yet', 'class' => 'bg-secondary text-white', 'icon' => '📊'];
        }

        if ($average >= 95) {
            return ['message' => '🏆 Outstanding!', 'class' => 'bg-success text-white', 'icon' => '🏆'];
        } elseif ($average >= 90) {
            return ['message' => '⭐ Excellent work!', 'class' => 'bg-success text-white', 'icon' => '⭐'];
        } elseif ($average >= 85) {
            return ['message' => '👍 Great work!', 'class' => 'bg-success text-white', 'icon' => '👍'];
        } elseif ($average >= 80) {
            return ['message' => '✅ Good job!', 'class' => 'bg-info text-white', 'icon' => '✅'];
        } elseif ($average >= 75) {
            return ['message' => '📈 Keep improving!', 'class' => 'bg-warning text-white', 'icon' => '📈'];
        } else {
            return ['message' => '💪 Need more effort!', 'class' => 'bg-danger text-white', 'icon' => '💪'];
        }
    }

    public function index()
    {
        // TEMPORARY: Bypass authentication to test if the issue is with auth or the view
        try {
            // Try to get auth status without redirecting
            $isLoggedIn = $this->auth->loggedIn();
            $authStatus = $isLoggedIn ? 'Logged in' : 'Not logged in';
        } catch (\Throwable $e) {
            $authStatus = 'Auth error: ' . $e->getMessage();
        }

        // Get recent announcements
        $announcementModel = new AnnouncementModel();
        $announcements = $announcementModel->where('target_roles', 'all')
            ->orWhere('target_roles', 'student')
            ->orderBy('published_at', 'DESC')
            ->limit(3)
            ->findAll();

        // Get recent notifications for current user
        $notificationModel = new NotificationModel();
        $notifications = $notificationModel->where('user_id', $this->auth->id() ?? 1)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        $unreadCount = $notificationModel->where('user_id', $this->auth->id() ?? 1)
            ->where('is_read', false)
            ->countAllResults();

        // Try to get real student data if possible
        $student = null;
        $quarterAverage = null;
        $currentQuarter = $this->getCurrentQuarter();
        $schoolYear = '2025-2026';

        try {
            if ($this->auth->loggedIn() && $this->auth->user()->inGroup('student')) {
                // Get real student data
                $studentModel = new \App\Models\StudentModel();
                $gradeModel = new \App\Models\GradeModel();

                $student = $studentModel->where('user_id', $this->auth->id())->first();

                if ($student) {
                    // Calculate real quarter average
                    $quarterAverage = $gradeModel->getQuarterAverage($student['id'], $schoolYear, $currentQuarter);
                }
            }
        } catch (\Throwable $e) {
            // Fall back to test data if there's an error
        }

        // Use test data if no real student found - match teacher's Grade 7 class
        if (!$student) {
            $student = [
                'id' => 1,
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'lrn' => '123456789001',
                'grade_level' => 7,
                'section_name' => 'Aphrodite',
                'enrollment_status' => 'enrolled',
                'email' => 'demo.student@example.com',
                'contact_number' => '+63 912 345 6789',
                'address' => '123 Test Street, Test City',
                'emergency_contact_name' => 'Test Parent',
                'emergency_contact_number' => '+63 998 765 4321',
                'emergency_contact_relationship' => 'Parent'
            ];
            // Use sample data if no real grades
            if ($quarterAverage === null) {
                $quarterAverage = null; // No grades yet
            }
        }

        // Return a simple test view with auth status
        return view('student/dashboard', [
            'title' => 'Student Dashboard - LPHS SMS (Test Mode)',
            'student' => $student,
            'announcements' => $announcements,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'recentGrades' => [],
            'quarterAverage' => $quarterAverage,
            'currentQuarter' => $currentQuarter,
            'performanceMessage' => $this->getPerformanceMessage($quarterAverage),
            'debug_info' => [
                'auth_status' => $authStatus,
                'timestamp' => date('Y-m-d H:i:s'),
                'session_id' => session_id(),
                'real_data' => $student['id'] !== 1 ? 'Yes' : 'No',
                'quarter_average' => $quarterAverage,
                'current_quarter' => $currentQuarter,
                'school_year' => $schoolYear,
                'student_id' => $student['id'] ?? 'N/A'
            ]
        ]);
    }

    public function profile()
    {
        // Check if user is authenticated
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $studentModel = new StudentModel();
        $studentWithSection = $studentModel->getStudentWithSection($student['id']);

        return view('student/profile', [
            'title' => 'My Profile - LPHS SMS',
            'student' => $studentWithSection
        ]);
    }

    public function grades()
    {
        // Check if user is authenticated; allow dev/test mode fallback
        if (!$this->auth->loggedIn()) {
            if ($this->isDevTestMode()) {
                // Render grades page with sample data in development/test
                $schoolYear = $this->request->getGet('school_year') ?? '2025-2026';
                $quarter = (int)($this->request->getGet('quarter') ?? 1);

                // Sample grades data for testing - Grade 7 student
                $sampleGrades = $this->getSampleGrade7Data();

                return view('student/grades', [
                    'title' => 'My Grades - LPHS SMS',
                    'student' => [
                        'id' => 1,
                        'first_name' => 'Demo',
                        'last_name' => 'Student',
                        'lrn' => '123456789001',
                        'grade_level' => 7,
                        'enrollment_status' => 'enrolled'
                    ],
                    'grades' => $sampleGrades,
                    'schoolYear' => '2025-2026',
                    'quarter' => 2,
                    'quarterAverage' => 85.5,
                    'gwa' => 84.75,
                    'canEnrollNextSemester' => true,
                    'allQuarterGrades' => [1 => 86.2, 2 => 85.1, 3 => 83.8, 4 => 83.9],
                ]);
            }
            return redirect()->to(base_url('login'));
        }

        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();

        $schoolYear = $this->request->getGet('school_year') ?? '2025-2026';
        $quarter = $this->request->getGet('quarter') ?? 2; // Current quarter

        // Get subjects for student's grade level
        $subjects = $subjectModel->getByGradeLevel($student['grade_level']);

        // Get grades for the selected period
        $grades = [];
        foreach ($subjects as $subject) {
            $grade = $gradeModel->where('student_id', $student['id'])
                ->where('subject_id', $subject['id'])
                ->where('school_year', $schoolYear)
                ->where('quarter', $quarter)
                ->first();

            $grades[] = [
                'subject' => $subject,
                'grade' => $grade
            ];
        }

        // Calculate quarter average
        $quarterAverage = $gradeModel->getQuarterAverage($student['id'], $schoolYear, $quarter);

        // Calculate GWA (General Weighted Average) for the entire school year
        $gwa = $gradeModel->getFinalAverage($student['id'], $schoolYear);

        // Get all quarter grades for overview
        $allQuarterGrades = [];
        for ($q = 1; $q <= 4; $q++) {
            $qAvg = $gradeModel->getQuarterAverage($student['id'], $schoolYear, $q);
            $allQuarterGrades[$q] = $qAvg;
        }

        // Check if student can enroll for next semester (GWA must be 75 or above)
        $canEnrollNextSemester = ($gwa !== null && $gwa >= 75.0);
        
        // For demo mode, provide sample data if no real grades
        if ($gwa === null && $this->isDevTestMode()) {
            $gwa = 84.75;
            $allQuarterGrades = [1 => 86.2, 2 => 85.1, 3 => 83.8, 4 => 83.9];
            $canEnrollNextSemester = true;
        }

        return view('student/grades', [
            'title' => 'My Grades - LPHS SMS',
            'student' => $student,
            'grades' => $grades,
            'schoolYear' => $schoolYear,
            'quarter' => $quarter,
            'quarterAverage' => $quarterAverage,
            'gwa' => $gwa,
            'canEnrollNextSemester' => $canEnrollNextSemester,
            'allQuarterGrades' => $allQuarterGrades,
        ]);
    }

    public function schedule()
    {
        // Check if user is authenticated; allow dev/test mode fallback
        if (!$this->auth->loggedIn()) {
            if ($this->isDevTestMode()) {
                $subjectModel = new SubjectModel();
                $subjects = $subjectModel->getByGradeLevel(10);
                return view('student/schedule', [
                    'title' => 'Class Schedule - LPHS SMS',
                    'student' => [ 'id' => 0, 'grade_level' => 10 ],
                    'subjects' => $subjects,
                ]);
            }
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $subjectModel = new SubjectModel();
        $subjects = $subjectModel->getByGradeLevel($student['grade_level']);

        return view('student/schedule', [
            'title' => 'Class Schedule - LPHS SMS',
            'student' => $student,
            'subjects' => $subjects
        ]);
    }

    public function announcements()
    {
        // Check if user is authenticated
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $announcementModel = new AnnouncementModel();
        
        $announcements = $announcementModel->where('target_roles', 'all')
            ->orWhere('target_roles', 'student')
            ->orderBy('published_at', 'DESC')
            ->findAll();

        return view('student/announcements', [
            'title' => 'Announcements - LPHS SMS',
            'announcements' => $announcements
        ]);
    }

    public function updateProfile()
    {
        // Check if user is authenticated
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $rules = [
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
            'emergency_contact_name' => 'permit_empty|max_length[255]',
            'emergency_contact_number' => 'permit_empty|max_length[20]',
            'emergency_contact_relationship' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel = new StudentModel();
        $updateData = [
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
            'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship')
        ];

        if ($studentModel->update($student['id'], $updateData)) {
            return redirect()->to(base_url('student/profile'))->with('success', 'Profile updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    /**
     * Simple test method without authentication
     */
    public function testSimple()
    {
        return $this->response->setJSON([
            'message' => 'Student Dashboard test route working',
            'timestamp' => date('Y-m-d H:i:s'),
            'controller' => 'Student\Dashboard'
        ]);
    }

    /**
     * Get current quarter based on date
     */
    private function getCurrentQuarter(): int
    {
        $month = (int) date('n');
        
        if ($month >= 6 && $month <= 8) {
            return 1; // June-August
        } elseif ($month >= 9 && $month <= 11) {
            return 2; // September-November
        } elseif ($month >= 12 || $month <= 2) {
            return 3; // December-February
        } else {
            return 4; // March-May
        }
    }

    /**
     * Determine if app is in development/test mode to allow UI preview without auth
     */
    private function isDevTestMode(): bool
    {
        return defined('ENVIRONMENT') && ENVIRONMENT !== 'production';
    }

    /**
     * Get sample Grade 7 data for testing (no grades yet)
     */
    private function getSampleGrade7Data()
    {
        return [
            [
                'subject' => ['id' => 1, 'subject_name' => 'Araling Panlipunan 7', 'subject_code' => 'AP7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 2, 'subject_name' => 'Edukasyon sa Pagpapakatao 7', 'subject_code' => 'ESP7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 3, 'subject_name' => 'English 7', 'subject_code' => 'ENG7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 4, 'subject_name' => 'Filipino 7', 'subject_code' => 'FIL7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 5, 'subject_name' => 'MAPEH 7', 'subject_code' => 'MAPEH7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 6, 'subject_name' => 'Mathematics 7', 'subject_code' => 'MATH7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 7, 'subject_name' => 'Science 7', 'subject_code' => 'SCI7', 'units' => 1.0],
                'grade' => null
            ],
            [
                'subject' => ['id' => 8, 'subject_name' => 'Technology and Livelihood Education 7', 'subject_code' => 'TLE7', 'units' => 1.0],
                'grade' => null
            ]
        ];
    }

    /**
     * Get sample grades data for testing
     */
    private function getSampleGradesData()
    {
        return [
            [
                'subject' => ['id' => 1, 'subject_name' => 'Mathematics', 'subject_code' => 'MATH10', 'units' => 3],
                'grade' => ['grade' => 88.5, 'remarks' => 'Good']
            ],
            [
                'subject' => ['id' => 2, 'subject_name' => 'English', 'subject_code' => 'ENG10', 'units' => 3],
                'grade' => ['grade' => 92.0, 'remarks' => 'Excellent']
            ],
            [
                'subject' => ['id' => 3, 'subject_name' => 'Science', 'subject_code' => 'SCI10', 'units' => 3],
                'grade' => ['grade' => 85.0, 'remarks' => 'Very Good']
            ],
            [
                'subject' => ['id' => 4, 'subject_name' => 'Filipino', 'subject_code' => 'FIL10', 'units' => 3],
                'grade' => ['grade' => 90.5, 'remarks' => 'Excellent']
            ],
            [
                'subject' => ['id' => 5, 'subject_name' => 'Araling Panlipunan', 'subject_code' => 'AP10', 'units' => 3],
                'grade' => ['grade' => 87.0, 'remarks' => 'Very Good']
            ],
            [
                'subject' => ['id' => 6, 'subject_name' => 'Physical Education', 'subject_code' => 'PE10', 'units' => 2],
                'grade' => ['grade' => 95.0, 'remarks' => 'Outstanding']
            ]
        ];
    }

    /**
     * Get sample all quarter grades for testing
     */
    private function getSampleAllQuarterGrades()
    {
        return [
            1 => 85.5,
            2 => 87.2,
            3 => 84.8,
            4 => null // Not yet available
        ];
    }

    public function enrollment()
    {
        return view('student/enrollment', [
            'title' => 'Next Semester Enrollment - LPHS SMS'
        ]);
    }

    public function submitEnrollment()
    {
        // Handle enrollment form submission
        return redirect()->to(base_url('student/enrollment'))->with('success', 'Enrollment application submitted successfully!');
    }

    public function applyNextYear()
    {
        // Handle AJAX request for next year enrollment
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request']);
        }

        try {
            $input = json_decode($this->request->getBody(), true);
            $nextGradeLevel = $input['next_grade_level'] ?? null;
            $currentGwa = $input['current_gwa'] ?? null;

            if (!$nextGradeLevel || !$currentGwa) {
                return $this->response->setJSON(['success' => false, 'error' => 'Missing required data']);
            }

            // Get student record - try multiple approaches
            $student = null;
            $db = \Config\Database::connect();
            
            // Try to get authenticated student first
            if ($this->auth->loggedIn()) {
                $student = $this->getStudentRecord();
            }
            
            // Fallback: try to find student by LRN 100000000001 for demo
            if (!$student) {
                $student = $db->table('students')
                    ->where('lrn', '100000000001')
                    ->get()->getRowArray();
            }

            if (!$student) {
                return $this->response->setJSON(['success' => false, 'error' => 'Student record not found']);
            }

            // Check if table exists first
            if (!$db->tableExists('next_year_applications')) {
                return $this->response->setJSON(['success' => false, 'error' => 'Database table not found. Please contact administrator.']);
            }

            // Check if already applied
            $existing = $db->table('next_year_applications')
                ->where('student_id', $student['id'])
                ->where('school_year', '2026-2027')
                ->get()->getRow();

            if ($existing) {
                return $this->response->setJSON(['success' => false, 'error' => 'You have already applied for next school year']);
            }

            // Insert application
            $applicationData = [
                'student_id' => $student['id'],
                'current_grade_level' => $student['grade_level'],
                'next_grade_level' => $nextGradeLevel,
                'gwa' => $currentGwa,
                'school_year' => '2026-2027',
                'status' => 'pending',
                'applied_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $db->table('next_year_applications')->insert($applicationData);
            
            if (!$result) {
                $error = $db->error();
                return $this->response->setJSON(['success' => false, 'error' => 'Failed to save application: ' . $error['message']]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Application submitted successfully! The admin will review your application for Grade ' . $nextGradeLevel . '.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Next year application error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
