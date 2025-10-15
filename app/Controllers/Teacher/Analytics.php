<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\GradeModel;
use App\Models\TeacherModel;
use App\Models\SubjectModel;
use App\Models\SectionModel;
use App\Models\AttendanceModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Analytics extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        // Check authentication
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $userId = $this->auth->id();
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        $sectionModel = new SectionModel();

        // Get the actual logged-in teacher
        $teacher = $teacherModel->where('user_id', $userId)->first();

        if (!$teacher) {
            return view('teacher/analytics', [
                'title' => 'Class Analytics - LPHS SMS',
                'error' => 'Teacher record not found'
            ]);
        }

        // Use current school year and quarter
        $schoolYear = '2025-2026';
        $currentQuarter = 1;

        // Get the section info for display
        $teacherSection = $sectionModel->where('adviser_id', $teacher['id'])->first();

        // Get students from sections where this teacher is adviser
        $myStudents = $studentModel->select('students.*, sections.section_name, sections.grade_level as section_grade')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('sections.adviser_id', $teacher['id'])
            ->where('students.enrollment_status', 'enrolled')
            ->findAll();

        // Get subjects for the grade levels of advised students
        $mySubjects = [];
        if (!empty($myStudents)) {
            $gradeLevels = array_unique(array_column($myStudents, 'grade_level'));
            $mySubjects = $subjectModel->whereIn('grade_level', $gradeLevels)
                ->where('is_active', true)
                ->findAll();
        }

        // Calculate actual analytics from grades
        $analytics = $this->calculateAnalytics($myStudents, $mySubjects, $schoolYear, $currentQuarter, $teacher['id']);
        
        // If no real grades found, use empty analytics
        if ($analytics['classAverage'] == 0 && empty($analytics['subjectAverages'])) {
            $analytics = $this->getEmptyAnalytics(count($myStudents));
        }
        
        // Add attendance analytics
        $attendanceModel = new AttendanceModel();
        if ($teacher) {
            $attendanceStats = $attendanceModel->getAttendanceStats($teacher['id'], date('Y-m-01'), date('Y-m-d'));
            $analytics['attendanceStats'] = $this->processAttendanceStats($attendanceStats);
        }

        return view('teacher/analytics', [
            'title' => 'Class Analytics - LPHS SMS',
            'teacher' => $teacher,
            'myStudents' => $myStudents,
            'mySubjects' => $mySubjects,
            'analytics' => $analytics,
            'schoolYear' => $schoolYear,
            'currentQuarter' => $currentQuarter,
            'teacherSection' => $teacherSection
        ]);
    }

    private function calculateAnalytics($students, $subjects, $schoolYear, $currentQuarter, $teacherId = null)
    {
        $gradeModel = new GradeModel();

        $analytics = [
            'totalStudents' => count($students),
            'totalSubjects' => count($subjects),
            'gradeDistribution' => [
                'excellent' => 0, // 90-100
                'very_good' => 0, // 85-89
                'good' => 0,      // 80-84
                'fair' => 0,      // 75-79
                'passing' => 0,   // 70-74
                'failing' => 0    // <70
            ],
            'subjectAverages' => [],
            'quarterTrends' => [],
            'studentPerformance' => [],
            'attendanceRate' => 0, // Will be calculated from actual data
            'improvementRate' => 0, // Will be calculated from actual data
            'classAverage' => 0
        ];

        if (empty($students)) {
            log_message('info', 'No students found for teacher ID: ' . ($teacherId ?? 'unknown'));
            return $analytics;
        }
        
        log_message('info', 'Calculating analytics for ' . count($students) . ' students, school year: ' . $schoolYear . ', quarter: ' . $currentQuarter . ', teacher ID: ' . ($teacherId ?? 'unknown'));

        $totalGrades = 0;
        $gradeCount = 0;

        // Calculate grade distribution and subject averages
        foreach ($subjects as $subject) {
            $subjectGrades = [];

            foreach ($students as $student) {
                // Get grades for this specific student and subject
                $grade = $gradeModel->where('student_id', $student['id'])
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('quarter', $currentQuarter)
                    ->first();
                    
                // Debug: Log grade query
                if (!$grade) {
                    log_message('debug', 'No grade found for student ' . $student['id'] . ', subject ' . $subject['id'] . ', SY: ' . $schoolYear . ', Q: ' . $currentQuarter);
                } else {
                    log_message('debug', 'Found grade: ' . $grade['grade'] . ' for student ' . $student['id'] . ', subject ' . $subject['id']);
                }

                if ($grade && $grade['grade'] !== null) {
                    $gradeValue = (float)$grade['grade'];
                    $subjectGrades[] = $gradeValue;
                    $totalGrades += $gradeValue;
                    $gradeCount++;

                    // Grade distribution
                    if ($gradeValue >= 90) {
                        $analytics['gradeDistribution']['excellent']++;
                    } elseif ($gradeValue >= 85) {
                        $analytics['gradeDistribution']['very_good']++;
                    } elseif ($gradeValue >= 80) {
                        $analytics['gradeDistribution']['good']++;
                    } elseif ($gradeValue >= 75) {
                        $analytics['gradeDistribution']['fair']++;
                    } elseif ($gradeValue >= 70) {
                        $analytics['gradeDistribution']['passing']++;
                    } else {
                        $analytics['gradeDistribution']['failing']++;
                    }
                }
            }

            if (!empty($subjectGrades)) {
                $analytics['subjectAverages'][] = [
                    'subject' => $subject['subject_name'],
                    'average' => round(array_sum($subjectGrades) / count($subjectGrades), 2),
                    'count' => count($subjectGrades)
                ];
            }
        }

        // Calculate overall class average
        if ($gradeCount > 0) {
            $analytics['classAverage'] = round($totalGrades / $gradeCount, 2);
        }
        
        // Calculate actual attendance rate if we have attendance data
        if (isset($analytics['attendanceStats']) && $analytics['attendanceStats']['total'] > 0) {
            $analytics['attendanceRate'] = $analytics['attendanceStats']['attendanceRate'];
        }
        
        // Calculate improvement rate based on quarter comparison
        if (count($analytics['quarterTrends']) >= 2) {
            $currentAvg = $analytics['classAverage'];
            $previousAvg = $analytics['quarterTrends'][0]['average'] ?? 0;
            if ($previousAvg > 0) {
                $analytics['improvementRate'] = round((($currentAvg - $previousAvg) / $previousAvg) * 100, 1);
            }
        }

        // Calculate quarter trends based on actual data only
        $analytics['quarterTrends'] = [];
        for ($q = 1; $q <= 4; $q++) {
            if ($q == $currentQuarter) {
                $analytics['quarterTrends'][] = ['quarter' => 'Q' . $q, 'average' => $analytics['classAverage']];
            } else {
                // Only show 0 for other quarters since we don't have historical data
                $analytics['quarterTrends'][] = ['quarter' => 'Q' . $q, 'average' => 0];
            }
        }

        // Calculate individual student performance
        foreach ($students as $student) {
            $studentGrades = $gradeModel->where('student_id', $student['id'])
                ->where('school_year', $schoolYear)
                ->where('quarter', $currentQuarter)
                ->findAll();

            if (!empty($studentGrades)) {
                $studentAverage = array_sum(array_column($studentGrades, 'grade')) / count($studentGrades);
                $analytics['studentPerformance'][] = [
                    'name' => $student['first_name'] . ' ' . $student['last_name'],
                    'average' => round($studentAverage, 2),
                    'grade_count' => count($studentGrades)
                ];
            }
        }

        return $analytics;
    }
    
    private function processAttendanceStats($attendanceStats)
    {
        $stats = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'total' => 0,
            'attendanceRate' => 0
        ];
        
        foreach ($attendanceStats as $stat) {
            $stats[$stat['status']] = (int)$stat['count'];
            $stats['total'] += (int)$stat['count'];
        }
        
        if ($stats['total'] > 0) {
            $stats['attendanceRate'] = round(($stats['present'] / $stats['total']) * 100, 2);
        }
        
        return $stats;
    }

    private function getCurrentQuarter()
    {
        $month = (int)date('n');

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

    public function exportPdf()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        // Increase execution time for PDF generation
        set_time_limit(120);
        ini_set('memory_limit', '256M');
        
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        $sectionModel = new SectionModel();

        // Get the teacher - either logged in teacher or specific teacher for admin
        $userId = $this->auth->id();
        $teacher = null;
        
        // Check if user is admin accessing from announcement
        if ($this->auth->user()->inGroup('admin')) {
            // For admin, get teacher from announcement context or URL parameter
            $teacherName = $this->request->getGet('teacher');
            if ($teacherName) {
                // Parse teacher name from parameter
                $nameParts = explode(' ', $teacherName, 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';
                
                $teacher = $teacherModel->where('first_name', $firstName)
                                       ->where('last_name', $lastName)
                                       ->first();
            }
            
            // Fallback to first active teacher if not found
            if (!$teacher) {
                $teacher = $teacherModel->where('employment_status', 'active')->first();
            }
        } else {
            // For regular teacher, get their own record
            $teacher = $teacherModel->where('user_id', $userId)->first();
        }

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found');
        }

        // Use fixed school year and quarter where we have data
        $schoolYear = '2025-2026';
        $currentQuarter = 1;

        // Get students from sections where this teacher is adviser
        $myStudents = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('sections.adviser_id', $teacher['id'])
            ->where('students.enrollment_status', 'enrolled')
            ->findAll();

        // Get subjects for the grade levels of advised students
        $mySubjects = [];
        if (!empty($myStudents)) {
            $gradeLevels = array_unique(array_column($myStudents, 'grade_level'));
            $mySubjects = $subjectModel->whereIn('grade_level', $gradeLevels)
                ->where('is_active', true)
                ->findAll();
        }

        // Calculate analytics data - only real data, no demo
        $analytics = $this->calculateAnalytics($myStudents, $mySubjects, $schoolYear, $currentQuarter, $teacher['id']);
        
        // If no grades found, use empty analytics
        if ($analytics['classAverage'] == 0 && empty($analytics['subjectAverages'])) {
            $analytics = $this->getEmptyAnalytics(count($myStudents));
        }

        $data = [
            'teacher' => $teacher,
            'myStudents' => $myStudents,
            'mySubjects' => $mySubjects,
            'analytics' => $analytics,
            'schoolYear' => $schoolYear,
            'currentQuarter' => $currentQuarter,
            'reportDate' => date('F j, Y')
        ];

        $html = view('teacher/analytics_pdf', $data);
        
        $options = new Options();
        $options->set('defaultFont', 'Times');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'LPHS_Teacher_Analytics_Report_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function sendToAdmin()
    {
        if (!$this->auth->loggedIn()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $teacherModel = new TeacherModel();
        $userId = $this->auth->id();
        $teacher = $teacherModel->where('user_id', $userId)->first();

        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher not found']);
        }

        $input = $this->request->getJSON(true);
        $analytics = $input['analytics'] ?? [];
        $schoolYear = $input['schoolYear'] ?? '2024-2025';
        $currentQuarter = $input['currentQuarter'] ?? 1;

        // Create announcement with analytics data
        $announcementModel = model('AnnouncementModel');
        
        $title = 'Class Analytics Report - ' . $teacher['first_name'] . ' ' . $teacher['last_name'];
        $body = $this->formatAnalyticsForAnnouncement($analytics, $teacher, $schoolYear, $currentQuarter);
        
        $announcementData = [
            'title' => $title,
            'slug' => 'analytics-report-' . date('Y-m-d-H-i-s'),
            'body' => $body,
            'target_roles' => 'admin',
            'published_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->auth->id()
        ];

        if ($announcementModel->save($announcementData)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Analytics report sent to admin']);
        } else {
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to create announcement']);
        }
    }

    private function formatAnalyticsForAnnouncement($analytics, $teacher, $schoolYear, $currentQuarter)
    {
        $body = "<h4>Class Analytics Report</h4>";
        $body .= "<p><strong>Teacher:</strong> {$teacher['first_name']} {$teacher['last_name']}</p>";
        $body .= "<p><strong>School Year:</strong> {$schoolYear}</p>";
        $body .= "<p><strong>Quarter:</strong> {$currentQuarter}</p>";
        $body .= "<p><strong>Report Date:</strong> " . date('F j, Y g:i A') . "</p>";
        
        $body .= "<h5>Summary Statistics</h5>";
        $body .= "<ul>";
        $body .= "<li>Total Students: " . ($analytics['totalStudents'] ?? 0) . "</li>";
        $body .= "<li>Class Average: " . number_format($analytics['classAverage'] ?? 0, 1) . "%</li>";
        $body .= "<li>Attendance Rate: " . number_format($analytics['attendanceRate'] ?? 0, 1) . "%</li>";
        $body .= "<li>Improvement Rate: +" . number_format($analytics['improvementRate'] ?? 0, 1) . "%</li>";
        $body .= "</ul>";
        
        if (!empty($analytics['attendanceStats'])) {
            $body .= "<h5>Attendance Details</h5>";
            $body .= "<ul>";
            $body .= "<li>Present: " . ($analytics['attendanceStats']['present'] ?? 0) . "</li>";
            $body .= "<li>Absent: " . ($analytics['attendanceStats']['absent'] ?? 0) . "</li>";
            $body .= "<li>Late: " . ($analytics['attendanceStats']['late'] ?? 0) . "</li>";
            $body .= "<li>Excused: " . ($analytics['attendanceStats']['excused'] ?? 0) . "</li>";
            $body .= "</ul>";
        }
        
        if (!empty($analytics['subjectAverages'])) {
            $body .= "<h5>Subject Averages</h5>";
            $body .= "<ul>";
            foreach ($analytics['subjectAverages'] as $subject) {
                $body .= "<li>{$subject['subject']}: " . number_format($subject['average'], 1) . "%</li>";
            }
            $body .= "</ul>";
        }
        
        return $body;
    }
    
    private function getEmptyAnalytics($studentCount = 0)
    {
        return [
            'totalStudents' => $studentCount,
            'totalSubjects' => 0,
            'gradeDistribution' => [
                'excellent' => 0,
                'very_good' => 0,
                'good' => 0,
                'fair' => 0,
                'passing' => 0,
                'failing' => 0
            ],
            'subjectAverages' => [],
            'quarterTrends' => [
                ['quarter' => 'Q1', 'average' => 0],
                ['quarter' => 'Q2', 'average' => 0],
                ['quarter' => 'Q3', 'average' => 0],
                ['quarter' => 'Q4', 'average' => 0]
            ],
            'studentPerformance' => [],
            'attendanceRate' => 0,
            'improvementRate' => 0,
            'classAverage' => 0,
            'attendanceStats' => [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'total' => 0,
                'attendanceRate' => 0
            ]
        ];
    }
}




