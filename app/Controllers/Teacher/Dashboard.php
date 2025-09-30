<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\GradeModel;
use App\Models\StudentModel;
use App\Models\SubjectModel;

class Dashboard extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $teacherId = $this->auth->id();
        $gradeModel = new GradeModel();
        $studentModel = new StudentModel();
        $subjectModel = new SubjectModel();

        // Get teacher's basic info
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $teacherId)->first();

        // Initialize default values
        $myStudents = [];
        $mySubjects = [];
        $recentGrades = [];
        $classAverages = [];
        $gradeDistribution = ['excellent' => 0, 'very_good' => 0, 'good' => 0, 'fair' => 0, 'passing' => 0, 'failing' => 0];
        $quarterPerformance = [0, 0, 0, 0];

        if ($teacher) {
            // Get students from sections where this teacher is adviser
            $myStudents = $studentModel->select('students.id, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->limit(10)
                ->findAll();
            
            // If no students from advised sections, get students from grades table
            if (empty($myStudents)) {
                $myStudents = $gradeModel->db->query("
                    SELECT DISTINCT s.id, s.first_name, s.last_name, s.grade_level, sec.section_name
                    FROM grades g
                    JOIN students s ON s.id = g.student_id
                    LEFT JOIN sections sec ON sec.id = s.section_id
                    WHERE g.teacher_id = ? AND g.school_year = ?
                    LIMIT 10
                ", [$teacher['id'], '2024-2025'])->getResultArray();
            }

            // Get subjects taught by this teacher
            $mySubjects = $gradeModel->db->query("
                SELECT DISTINCT sub.id, sub.subject_name, sub.subject_code, sub.grade_level
                FROM grades g
                JOIN subjects sub ON sub.id = g.subject_id
                WHERE g.teacher_id = ? AND g.school_year = ?
            ", [$teacher['id'], '2024-2025'])->getResultArray();

            // Get recent grades entered by this teacher
            $recentGrades = $gradeModel->db->query("
                SELECT g.*, s.first_name, s.last_name, sub.subject_name, g.created_at
                FROM grades g
                JOIN students s ON s.id = g.student_id
                JOIN subjects sub ON sub.id = g.subject_id
                WHERE g.teacher_id = ?
                ORDER BY g.created_at DESC
                LIMIT 5
            ", [$teacher['id']])->getResultArray();

            // Calculate class averages by subject
            foreach ($mySubjects as $subject) {
                $average = $gradeModel->db->query("
                    SELECT AVG(grade) as avg_grade
                    FROM grades
                    WHERE teacher_id = ? AND subject_id = ? AND school_year = ? AND grade IS NOT NULL
                ", [$teacher['id'], $subject['id'], '2024-2025'])->getRowArray();

                $classAverages[] = [
                    'subject' => $subject['subject_name'],
                    'average' => $average['avg_grade'] ? round($average['avg_grade'], 2) : 0
                ];
            }

            // Get grade distribution
            $grades = $gradeModel->db->query("
                SELECT grade
                FROM grades
                WHERE teacher_id = ? AND school_year = ? AND grade IS NOT NULL
            ", [$teacher['id'], '2024-2025'])->getResultArray();

            foreach ($grades as $grade) {
                $gradeValue = $grade['grade'];
                if ($gradeValue >= 90) {
                    $gradeDistribution['excellent']++;
                } elseif ($gradeValue >= 85) {
                    $gradeDistribution['very_good']++;
                } elseif ($gradeValue >= 80) {
                    $gradeDistribution['good']++;
                } elseif ($gradeValue >= 75) {
                    $gradeDistribution['fair']++;
                } elseif ($gradeValue >= 70) {
                    $gradeDistribution['passing']++;
                } else {
                    $gradeDistribution['failing']++;
                }
            }

            // Get quarter performance data
            for ($quarter = 1; $quarter <= 4; $quarter++) {
                $avg = $gradeModel->db->query("
                    SELECT AVG(grade) as avg_grade
                    FROM grades
                    WHERE teacher_id = ? AND school_year = ? AND quarter = ? AND grade IS NOT NULL
                ", [$teacher['id'], '2024-2025', $quarter])->getRowArray();

                $quarterPerformance[$quarter - 1] = $avg['avg_grade'] ? round($avg['avg_grade'], 2) : 0;
            }
        }

        return view('teacher/dashboard', [
            'title' => 'Teacher Dashboard - LPHS SMS',
            'teacher' => $teacher,
            'myStudents' => $myStudents,
            'mySubjects' => $mySubjects,
            'recentGrades' => $recentGrades,
            'classAverages' => $classAverages,
            'gradeDistribution' => $gradeDistribution,
            'quarterPerformance' => $quarterPerformance,
            'totalStudents' => count($myStudents),
            'totalSubjects' => count($mySubjects),
            'currentQuarter' => $this->getCurrentQuarter()
        ]);
    }

    /**
     * Get current quarter based on date
     */
    private function getCurrentQuarter()
    {
        $month = (int) date('n');
        if ($month >= 6 && $month <= 8) return 1;      // June-August
        if ($month >= 9 && $month <= 11) return 2;     // September-November
        if ($month >= 12 || $month <= 2) return 3;     // December-February
        return 4;                                       // March-May
    }

    public function grades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $subjectModel = new SubjectModel();
        $gradeModel = new GradeModel();
        
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        $subjects = [];
        $studentGrades = [];
        
        if ($teacher) {
            // Get students from advised sections
            $students = $studentModel->select('students.id, students.student_id, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
            
            // Get subjects for the grade levels of advised students
            if (!empty($students)) {
                $gradeLevels = array_unique(array_column($students, 'grade_level'));
                $subjects = $subjectModel->whereIn('grade_level', $gradeLevels)
                    ->orderBy('grade_level', 'ASC')
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
                
                // Get existing grades for current quarter
                $currentQuarter = $this->getCurrentQuarter();
                foreach ($students as $student) {
                    foreach ($subjects as $subject) {
                        $grade = $gradeModel->where('student_id', $student['id'])
                            ->where('subject_id', $subject['id'])
                            ->where('teacher_id', $teacher['id'])
                            ->where('quarter', $currentQuarter)
                            ->where('school_year', '2024-2025')
                            ->first();
                        
                        $studentGrades[$student['id']][$subject['id']] = $grade;
                    }
                }
            }
        }
        
        return view('teacher/grades', [
            'title' => 'Enter Grades - LPHS SMS',
            'students' => $students,
            'subjects' => $subjects,
            'teacher' => $teacher,
            'studentGrades' => $studentGrades,
            'currentQuarter' => $this->getCurrentQuarter()
        ]);
    }

    public function saveGrades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $rules = [
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'quarter' => 'required|integer|greater_than[0]|less_than[5]',
            'grade' => 'required|decimal|greater_than_equal_to[60]|less_than_equal_to[100]',
            'remarks' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input and try again.');
        }

        // Get teacher record
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        $data = [
            'student_id' => (int) $this->request->getPost('student_id'),
            'subject_id' => (int) $this->request->getPost('subject_id'),
            'teacher_id' => (int) $teacher['id'],
            'school_year' => '2024-2025',
            'quarter' => (int) $this->request->getPost('quarter'),
            'grade' => (float) $this->request->getPost('grade'),
            'remarks' => $this->request->getPost('remarks')
        ];

        $gradeModel = new GradeModel();
        $saved = $gradeModel->upsertGrade($data);

        if (!$saved) {
            return redirect()->back()->withInput()->with('error', 'Failed to save grade.');
        }

        return redirect()->back()->with('success', 'Grade saved successfully!');
    }

    public function students()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        
        if ($teacher) {
            // Get students from sections where this teacher is adviser
            $students = $studentModel->select('students.*, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
        }
        
        return view('teacher/students', [
            'title' => 'My Students - LPHS SMS',
            'students' => $students,
            'teacher' => $teacher
        ]);
    }

    public function schedule()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        return view('teacher/schedule', ['title' => 'My Schedule - LPHS SMS']);
    }
} 