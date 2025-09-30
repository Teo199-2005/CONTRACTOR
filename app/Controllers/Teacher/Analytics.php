<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\GradeModel;
use App\Models\TeacherModel;
use App\Models\SubjectModel;
use App\Models\SectionModel;

class Analytics extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $teacherId = $this->auth->id();
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        $sectionModel = new SectionModel();

        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();

        if (!$teacher) {
            return view('teacher/analytics', [
                'title' => 'Class Analytics - LPHS SMS',
                'error' => 'Teacher record not found'
            ]);
        }

        // Get current school year and quarter
        $currentYear = date('Y');
        $schoolYear = $currentYear . '-' . ($currentYear + 1);
        $currentQuarter = $this->getCurrentQuarter();

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
}




