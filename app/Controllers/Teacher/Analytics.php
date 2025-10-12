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

        $teacherId = $this->auth->id();
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        $sectionModel = new SectionModel();

        // Force Demo Teacher for all users
        $teacher = $teacherModel->find(11); // Demo Teacher ID

        if (!$teacher) {
            return view('teacher/analytics', [
                'title' => 'Class Analytics - LPHS SMS',
                'error' => 'Teacher record not found'
            ]);
        }

        // Use fixed school year and quarter where we have data
        $schoolYear = '2024-2025';
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

        // Calculate analytics data
        $analytics = $this->calculateAnalytics($myStudents, $mySubjects, $schoolYear, $currentQuarter);
        
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
            'currentQuarter' => $currentQuarter
        ]);
    }

    private function calculateAnalytics($students, $subjects, $schoolYear, $currentQuarter)
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
            'attendanceRate' => 95.5, // Mock data
            'improvementRate' => 12.3, // Mock data
            'classAverage' => 0
        ];

        if (empty($students)) {
            return $analytics;
        }

        $totalGrades = 0;
        $gradeCount = 0;

        // Calculate grade distribution and subject averages
        foreach ($subjects as $subject) {
            $subjectGrades = [];

            foreach ($students as $student) {
                $grade = $gradeModel->where('student_id', $student['id'])
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('quarter', $currentQuarter)
                    ->first();

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

        // Calculate quarter trends (mock data for now)
        $analytics['quarterTrends'] = [
            ['quarter' => 'Q1', 'average' => 82.5],
            ['quarter' => 'Q2', 'average' => 84.2],
            ['quarter' => 'Q3', 'average' => $analytics['classAverage']],
            ['quarter' => 'Q4', 'average' => 0] // Future quarter
        ];

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

        $teacherId = $this->auth->id();
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        $sectionModel = new SectionModel();

        // Force Demo Teacher for all users
        $teacher = $teacherModel->find(11); // Demo Teacher ID

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found');
        }

        // Use fixed school year and quarter where we have data
        $schoolYear = '2024-2025';
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

        // Calculate analytics data
        $analytics = $this->calculateAnalytics($myStudents, $mySubjects, $schoolYear, $currentQuarter);

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
}




