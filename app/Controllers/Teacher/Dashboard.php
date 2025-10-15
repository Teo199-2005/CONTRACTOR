<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\GradeModel;
use App\Models\StudentModel;
use App\Models\SubjectModel;
use App\Models\AttendanceModel;

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
        $gradeDistribution = ['excellent' => 0, 'very_good' => 0, 'good' => 0, 'fair' => 0, 'failing' => 0];
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
                ", [$teacher['id'], '2025-2026'])->getResultArray();
            }

            // Get subjects taught by this teacher
            $mySubjects = $gradeModel->db->query("
                SELECT DISTINCT sub.id, sub.subject_name, sub.subject_code, sub.grade_level
                FROM grades g
                JOIN subjects sub ON sub.id = g.subject_id
                WHERE g.teacher_id = ? AND g.school_year = ?
            ", [$teacher['id'], '2025-2026'])->getResultArray();

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
                ", [$teacher['id'], $subject['id'], '2025-2026'])->getRowArray();

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
            ", [$teacher['id'], '2025-2026'])->getResultArray();

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
                ", [$teacher['id'], '2025-2026', $quarter])->getRowArray();

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
     * Get current quarter from system settings
     */
    private function getCurrentQuarter()
    {
        $systemSettingModel = new \App\Models\SystemSettingModel();
        return $systemSettingModel->getCurrentQuarter();
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
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        $students = [];
        $subjects = [];
        $studentGrades = [];
        $totalPages = 1;
        $currentPage = 1;
        
        if ($teacher) {
            // Get pagination parameters
            $currentPage = (int) ($this->request->getGet('page') ?? 1);
            $perPage = 15;
            $offset = ($currentPage - 1) * $perPage;
            
            // Get total count for pagination
            $totalStudents = $studentModel->select('students.id')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->countAllResults();
            
            $totalPages = ceil($totalStudents / $perPage);
            
            // Get students from advised sections with pagination
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->limit($perPage, $offset)
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
                            ->where('school_year', '2025-2026')
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
            'currentQuarter' => $this->getCurrentQuarter(),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
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
            'school_year' => '2025-2026',
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

    public function saveBulkGrades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        $grades = $this->request->getPost('grades');
        $quarter = $this->request->getPost('quarter');
        
        if (!$grades || !$quarter) {
            return redirect()->back()->with('error', 'Invalid grade data.');
        }

        $gradeModel = new GradeModel();
        $savedCount = 0;
        
        foreach ($grades as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $gradeValue) {
                if (!empty($gradeValue) && is_numeric($gradeValue)) {
                    $data = [
                        'student_id' => (int) $studentId,
                        'subject_id' => (int) $subjectId,
                        'teacher_id' => (int) $teacher['id'],
                        'school_year' => '2025-2026',
                        'quarter' => (int) $quarter,
                        'grade' => (float) $gradeValue,
                        'remarks' => null
                    ];
                    
                    if ($gradeModel->upsertGrade($data)) {
                        $savedCount++;
                    }
                }
            }
        }
        
        return redirect()->back()->with('success', "Successfully saved {$savedCount} grades!");
    }

    public function students()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        // Get teacher record for logged-in user
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
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $scheduleModel = new \App\Models\TeacherScheduleModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $schedules = [];
        
        if ($teacher) {
            $schedules = $scheduleModel->getTeacherSchedule($teacher['id']);
        }
        
        return view('teacher/schedule', [
            'title' => 'My Schedule - LPHS SMS',
            'teacher' => $teacher,
            'schedules' => $schedules
        ]);
    }

    public function attendance()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        $attendanceData = [];
        $selectedDate = $this->request->getGet('date') ?? date('Y-m-d');
        
        if ($teacher) {
            // Get students from advised sections
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
            
            // Get attendance for selected date
            $attendanceRecords = $attendanceModel->getAttendanceByDate($teacher['id'], $selectedDate);
            foreach ($attendanceRecords as $record) {
                $attendanceData[$record['student_id']] = $record;
            }
        }
        
        return view('teacher/attendance', [
            'title' => 'Student Attendance - LPHS SMS',
            'students' => $students,
            'teacher' => $teacher,
            'attendanceData' => $attendanceData,
            'selectedDate' => $selectedDate
        ]);
    }

    public function saveAttendance()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }
        
        $date = $this->request->getPost('date');
        $attendanceData = $this->request->getPost('attendance');
        
        if (!$date || !$attendanceData) {
            return redirect()->back()->with('error', 'Invalid attendance data.');
        }
        
        $saved = 0;
        foreach ($attendanceData as $studentId => $status) {
            $data = [
                'student_id' => (int) $studentId,
                'teacher_id' => (int) $teacher['id'],
                'date' => $date,
                'status' => $status,
                'remarks' => $this->request->getPost('remarks')[$studentId] ?? null
            ];
            
            if ($attendanceModel->markAttendance($data)) {
                $saved++;
            }
        }
        
        return redirect()->back()->with('success', "Attendance saved for {$saved} students.");
    }

    public function attendanceHistory()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        $fromDate = $this->request->getGet('from');
        $toDate = $this->request->getGet('to');
        
        if (!$fromDate || !$toDate) {
            return $this->response->setJSON(['success' => false, 'error' => 'From and to dates are required']);
        }
        
        // Get attendance history
        $db = \Config\Database::connect();
        $history = $db->query("
            SELECT a.date, a.status, a.remarks, 
                   CONCAT(s.first_name, ' ', s.last_name) as student_name,
                   s.lrn
            FROM attendance a
            JOIN students s ON s.id = a.student_id
            WHERE a.teacher_id = ? AND a.date BETWEEN ? AND ?
            ORDER BY a.date DESC, s.last_name ASC
        ", [$teacher['id'], $fromDate, $toDate])->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'history' => $history
        ]);
    }

    public function attendanceHistoryPage()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        
        if ($teacher) {
            // Get students from advised sections
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
        }
        
        return view('teacher/attendance_history', [
            'title' => 'Attendance History - LPHS SMS',
            'students' => $students,
            'teacher' => $teacher
        ]);
    }

    public function sections()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $sections = [];
        
        if ($teacher) {
            // Get sections where this teacher is adviser
            $sections = $sectionModel->select('sections.*, COUNT(students.id) as current_enrollment')
                ->join('students', 'students.section_id = sections.id AND students.enrollment_status = "enrolled"', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->groupBy('sections.id')
                ->orderBy('sections.grade_level', 'ASC')
                ->orderBy('sections.section_name', 'ASC')
                ->findAll();
        }
        
        return view('teacher/sections', [
            'title' => 'My Sections - LPHS SMS',
            'sections' => $sections,
            'teacher' => $teacher
        ]);
    }

    public function getSectionStudents($sectionId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Verify teacher is adviser of this section
        $section = $sectionModel->where('id', $sectionId)
            ->where('adviser_id', $teacher['id'])
            ->first();
        
        if (!$section) {
            return $this->response->setJSON(['success' => false, 'error' => 'Section not found or not assigned to you']);
        }
        
        // Get students in this section
        $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.enrollment_status, students.created_at')
            ->where('students.section_id', $sectionId)
            ->where('students.enrollment_status', 'enrolled')
            ->orderBy('students.last_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    public function getUnassignedStudents($gradeLevel)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Get unassigned students for this grade level
        $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name')
            ->where('students.grade_level', $gradeLevel)
            ->where('students.enrollment_status', 'enrolled')
            ->where('(students.section_id IS NULL OR students.section_id = 0)')
            ->orderBy('students.last_name', 'ASC')
            ->findAll();
        
        if (empty($students)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No unassigned students found for Grade ' . $gradeLevel
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    public function assignStudentsToSection($sectionId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Verify teacher is adviser of this section
        $section = $sectionModel->where('id', $sectionId)
            ->where('adviser_id', $teacher['id'])
            ->first();
        
        if (!$section) {
            return $this->response->setJSON(['success' => false, 'error' => 'Section not found or not assigned to you']);
        }
        
        $input = $this->request->getJSON(true);
        $studentIds = $input['student_ids'] ?? [];
        
        if (empty($studentIds)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No students selected']);
        }
        
        $assigned = 0;
        foreach ($studentIds as $studentId) {
            if ($studentModel->update($studentId, ['section_id' => $sectionId])) {
                $assigned++;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully assigned {$assigned} student(s) to the section"
        ]);
    }

    public function generateReportCard($studentId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found');
        }
        
        // Get student and verify teacher has access
        $student = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('students.id', $studentId)
            ->where('sections.adviser_id', $teacher['id'])
            ->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found or not in your section');
        }
        
        // Get subjects for student's grade level
        $subjects = $subjectModel->where('grade_level', $student['grade_level'])
            ->where('is_active', true)
            ->findAll();
        
        // Get grades for current school year
        $schoolYear = '2025-2026';
        $grades = [];
        $quarterAverages = [];
        
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterGrades = [];
            foreach ($subjects as $subject) {
                $grade = $gradeModel->where('student_id', $studentId)
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('quarter', $quarter)
                    ->first();
                
                $quarterGrades[$subject['id']] = $grade ? $grade['grade'] : null;
            }
            $grades[$quarter] = $quarterGrades;
            
            // Calculate quarter average
            $validGrades = array_filter($quarterGrades, function($g) { return $g !== null; });
            $quarterAverages[$quarter] = !empty($validGrades) ? array_sum($validGrades) / count($validGrades) : 0;
        }
        
        // Calculate final average
        $validQuarters = array_filter($quarterAverages, function($avg) { return $avg > 0; });
        $finalAverage = !empty($validQuarters) ? array_sum($validQuarters) / count($validQuarters) : 0;
        
        $data = [
            'student' => $student,
            'teacher' => $teacher,
            'subjects' => $subjects,
            'grades' => $grades,
            'quarterAverages' => $quarterAverages,
            'finalAverage' => $finalAverage,
            'schoolYear' => $schoolYear,
            'reportDate' => date('F j, Y')
        ];
        
        $html = view('teacher/report_card_pdf', $data);
        
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Times');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'LPHS_Report_Card_' . $student['first_name'] . '_' . $student['last_name'] . '_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
    }
}