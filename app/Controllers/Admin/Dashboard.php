<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\SectionModel;
use App\Models\AnnouncementModel;
use App\Models\GradeModel;
use App\Models\EnrollmentDocumentModel;

class Dashboard extends BaseController
{
    protected $auth;
    
    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        // Check if user is admin
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $teacherModel = new TeacherModel();
        $sectionModel = new SectionModel();
        $announcementModel = new AnnouncementModel();

        // Get dashboard statistics
        $stats = [
            'total_students' => $studentModel->where('enrollment_status', 'enrolled')->countAllResults(),
            'pending_enrollments' => $studentModel->where('enrollment_status', 'pending')->countAllResults(),
            'total_teachers' => $teacherModel->where('employment_status', 'active')->countAllResults(),
            'total_sections' => $sectionModel->where('is_active', true)->countAllResults(),
            'total_announcements' => $announcementModel->countAllResults()
        ];

        // Get recent enrollments
        $recentEnrollments = $studentModel->where('enrollment_status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get enrollment statistics by grade level
        $enrollmentByGrade = [];
        for ($grade = 7; $grade <= 10; $grade++) {
            $enrollmentByGrade[$grade] = $studentModel->where('grade_level', $grade)
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
        }

        // Get recent announcements
        $recentAnnouncements = $announcementModel->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('admin/dashboard', [
            'title' => 'Admin Dashboard - LPHS SMS',
            'stats' => $stats,
            'recentEnrollments' => $recentEnrollments,
            'enrollmentByGrade' => $enrollmentByGrade,
            'recentAnnouncements' => $recentAnnouncements
        ]);
    }

    public function enrollments()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $sectionModel = new SectionModel();

        $status = $this->request->getGet('status') ?? 'pending';
        $gradeLevel = $this->request->getGet('grade');

        $builder = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('enrollment_status', $status);

        if ($gradeLevel) {
            $builder->where('students.grade_level', $gradeLevel);
        }

        $students = $builder->orderBy('students.created_at', 'DESC')->findAll();

        // Get available sections for assignment (needed for both pending and approved students)
        $currentYear = date('Y');
        $schoolYear = $currentYear . '-' . ($currentYear + 1);
        $availableSections = [];
        for ($grade = 7; $grade <= 10; $grade++) {
            $availableSections[$grade] = $sectionModel->getAvailableSections($grade, $schoolYear);
        }

        return view('admin/enrollments', [
            'title' => 'Manage Enrollments - LPHS SMS',
            'students' => $students,
            'status' => $status,
            'gradeLevel' => $gradeLevel,
            'availableSections' => $availableSections
        ]);
    }

    public function approveEnrollment($studentId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $sectionModel = new SectionModel();
        $sectionId = $this->request->getPost('section_id');

        $student = $studentModel->find($studentId);
        if (! $student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        // If section is provided, verify it has capacity
        if (!empty($sectionId)) {
            if (!$sectionModel->hasAvailableSlots($sectionId)) {
                return redirect()->back()->with('error', 'Selected section is at full capacity.');
            }
        } else {
            // Auto-assign to best available section if none selected
            $currentYear = date('Y');
            $schoolYear = $currentYear . '-' . ($currentYear + 1);
            $best = $sectionModel->selectBestAvailableSection((int) ($student['grade_level'] ?? 0), $schoolYear);
            if ($best) {
                $sectionId = (int) $best['id'];
            } else {
                return redirect()->back()->with('error', 'No available sections found for Grade ' . $student['grade_level'] . '. Please create a new section or increase capacity.');
            }
        }

        // Transition logic: pending -> approved, approved -> enrolled
        if ($student['enrollment_status'] === 'pending') {
            // Approve first; StudentModel will generate student_id and set section if provided
            $ok = $studentModel->approveEnrollment($studentId, $sectionId ?: null);
            if (! $ok) {
                return redirect()->back()->with('error', 'Failed to approve enrollment.');
            }

            // If we have a section, enroll directly
            if (! empty($sectionId)) {
                $currentYear = date('Y');
                $schoolYear = $currentYear . '-' . ($currentYear + 1);
                $studentModel->enrollStudent($studentId, (int) $sectionId, $schoolYear);
                $sectionModel->updateEnrollmentCount((int) $sectionId);

                // Get section name for success message
                $section = $sectionModel->find($sectionId);
                $sectionName = $section ? $section['section_name'] : 'Unknown Section';

                return redirect()->back()->with('success', "Student approved and assigned to {$sectionName}.");
            }
            return redirect()->back()->with('success', 'Student approved. Assign a section to complete enrollment.');
        }

        if ($student['enrollment_status'] === 'approved') {
            if (empty($sectionId)) {
                return redirect()->back()->with('error', 'Please select a section to enroll the student.');
            }
            $currentYear = date('Y');
            $schoolYear = $currentYear . '-' . ($currentYear + 1);
            $ok = $studentModel->enrollStudent($studentId, (int) $sectionId, $schoolYear);
            if ($ok) {
                $sectionModel->updateEnrollmentCount((int) $sectionId);
                return redirect()->back()->with('success', 'Student enrolled successfully.');
            }
            return redirect()->back()->with('error', 'Failed to enroll student.');
        }

        return redirect()->back()->with('info', 'No action taken. Current status: ' . ucfirst($student['enrollment_status']));
    }

    public function rejectEnrollment($studentId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $reason = $this->request->getPost('reason');

        if ($studentModel->rejectEnrollment($studentId, $reason)) {
            return redirect()->back()->with('success', 'Student enrollment rejected.');
        }

        return redirect()->back()->with('error', 'Failed to reject enrollment.');
    }

    public function students()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $sectionModel = new SectionModel();
        $gradeLevel = $this->request->getGet('grade');
        $section = $this->request->getGet('section');
        $search = $this->request->getGet('search');

        $builder = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('enrollment_status', 'enrolled');

        if ($gradeLevel) {
            $builder->where('students.grade_level', $gradeLevel);
        }

        if ($section) {
            $builder->where('students.section_id', $section);
        }

        if ($search) {
            $builder->groupStart()
                ->like('students.first_name', $search)
                ->orLike('students.last_name', $search)
                ->orLike('students.student_id', $search)
                ->groupEnd();
        }

        $students = $builder->orderBy('students.last_name', 'ASC')->findAll();

        // Get all sections for the dropdown
        $allSections = $sectionModel->select('id, section_name, grade_level')
            ->where('is_active', true)
            ->orderBy('grade_level', 'ASC')
            ->orderBy('section_name', 'ASC')
            ->findAll();

        return view('admin/students', [
            'title' => 'Manage Students - LPHS SMS',
            'students' => $students,
            'gradeLevel' => $gradeLevel,
            'section' => $section,
            'search' => $search,
            'allSections' => $allSections
        ]);
    }

    public function teachers()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = new TeacherModel();
        $teachers = $teacherModel->getActiveTeachers();

        return view('admin/teachers', [
            'title' => 'Manage Teachers - LPHS SMS',
            'teachers' => $teachers
        ]);
    }

    public function sections()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = new SectionModel();
        $teacherModel = new TeacherModel();
        $currentYear = date('Y');
        $schoolYear = $currentYear . '-' . ($currentYear + 1);

        // Get filter parameters
        $gradeFilter = $this->request->getGet('grade');
        $adviserFilter = $this->request->getGet('adviser_status');
        $searchTerm = $this->request->getGet('search');

        // Get sections with adviser information
        $sections = $sectionModel->getSectionsWithAdviser($schoolYear);

        // Apply filters
        if (!empty($gradeFilter)) {
            $sections = array_filter($sections, fn($s) => $s['grade_level'] == $gradeFilter);
        }

        if (!empty($adviserFilter)) {
            if ($adviserFilter === 'with_adviser') {
                $sections = array_filter($sections, fn($s) => !empty($s['adviser_name']));
            } elseif ($adviserFilter === 'no_adviser') {
                $sections = array_filter($sections, fn($s) => empty($s['adviser_name']));
            }
        }

        if (!empty($searchTerm)) {
            $sections = array_filter($sections, function($s) use ($searchTerm) {
                return stripos($s['section_name'], $searchTerm) !== false ||
                       stripos($s['adviser_name'] ?? '', $searchTerm) !== false;
            });
        }

        // Get available teachers (those not currently assigned as advisers)
        $availableTeachers = $teacherModel->getAvailableAdvisers();

        return view('admin/sections', [
            'title' => 'Manage Sections & Faculty Assignment - LPHS SMS',
            'sections' => $sections,
            'availableTeachers' => $availableTeachers,
            'gradeFilter' => $gradeFilter,
            'adviserFilter' => $adviserFilter,
            'searchTerm' => $searchTerm
        ]);
    }

    public function assignAdviser($sectionId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = new SectionModel();
        $teacherModel = new TeacherModel();
        $adviserId = $this->request->getPost('adviser_id');

        if (empty($adviserId)) {
            return redirect()->back()->with('error', 'Please select a teacher to assign as adviser.');
        }

        // Check if section exists
        $section = $sectionModel->find($sectionId);
        if (!$section) {
            return redirect()->back()->with('error', 'Section not found.');
        }

        // Check if teacher exists and is available
        $teacher = $teacherModel->find($adviserId);
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found.');
        }

        // Check if teacher is already assigned to another section
        $existingAssignment = $sectionModel->where('adviser_id', $adviserId)
                                          ->where('is_active', true)
                                          ->first();
        if ($existingAssignment) {
            return redirect()->back()->with('error', 'This teacher is already assigned to another section.');
        }

        // Assign the adviser
        $success = $sectionModel->update($sectionId, ['adviser_id' => $adviserId]);

        if ($success) {
            return redirect()->back()->with('success', 'Teacher successfully assigned as section adviser.');
        } else {
            return redirect()->back()->with('error', 'Failed to assign teacher as adviser.');
        }
    }

    public function removeAdviser($sectionId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = new SectionModel();

        // Check if section exists
        $section = $sectionModel->find($sectionId);
        if (!$section) {
            return redirect()->back()->with('error', 'Section not found.');
        }

        // Remove the adviser
        $success = $sectionModel->update($sectionId, ['adviser_id' => null]);

        if ($success) {
            return redirect()->back()->with('success', 'Adviser removed from section successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to remove adviser from section.');
        }
    }

    public function getSectionStudents($sectionId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $studentModel = new StudentModel();
        $sectionModel = new SectionModel();

        // Check if section exists
        $section = $sectionModel->find($sectionId);
        if (!$section) {
            return $this->response->setJSON(['success' => false, 'message' => 'Section not found']);
        }

        // Get students in this section
        $students = $studentModel->select('id, student_id, first_name, last_name, created_at')
                                 ->where('section_id', $sectionId)
                                 ->where('enrollment_status', 'enrolled')
                                 ->orderBy('last_name', 'ASC')
                                 ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'students' => $students,
            'section' => $section
        ]);
    }

    public function updateSection($sectionId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = new SectionModel();
        $section = $sectionModel->find($sectionId);
        if (!$section) {
            return redirect()->back()->with('error', 'Section not found.');
        }

        $rules = [
            'section_name' => 'required|max_length[100]',
            'max_capacity' => 'required|integer|greater_than[0]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'section_name' => $this->request->getPost('section_name'),
            'max_capacity' => (int) $this->request->getPost('max_capacity'),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Ensure current_enrollment does not exceed new max_capacity
        if (($section['current_enrollment'] ?? 0) > $data['max_capacity']) {
            $data['current_enrollment'] = $data['max_capacity'];
        }

        if ($sectionModel->update($sectionId, $data)) {
            return redirect()->back()->with('success', 'Section updated successfully.');
        }
        return redirect()->back()->with('error', 'Failed to update section.');
    }

    public function analytics()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $db = \Config\Database::connect();

        // Enrollment trends data (current year and previous year)
        $enrollmentTrends = [];
        $enrollmentTrendsPrev = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $currentYear = (int) date('Y');
        $previousYear = $currentYear - 1;
        
        foreach ($months as $index => $month) {
            $monthNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $countThis = $studentModel->where('MONTH(created_at)', $monthNum)
                ->where('YEAR(created_at)', $currentYear)
                ->countAllResults();
            $enrollmentTrends[] = ['month' => $month, 'count' => $countThis];

            $countPrev = $studentModel->where('MONTH(created_at)', $monthNum)
                ->where('YEAR(created_at)', $previousYear)
                ->countAllResults();
            $enrollmentTrendsPrev[] = ['month' => $month, 'count' => $countPrev];
        }

        // Gender distribution (enrolled only)
        $genderDistribution = [
            'male' => $studentModel->where('gender', 'Male')->where('enrollment_status', 'enrolled')->countAllResults(),
            'female' => $studentModel->where('gender', 'Female')->where('enrollment_status', 'enrolled')->countAllResults()
        ];

        // Grade level distribution (enrolled only)
        $gradeDistribution = [];
        for ($grade = 7; $grade <= 10; $grade++) {
            $gradeDistribution[$grade] = $studentModel->where('grade_level', $grade)
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
        }

        // Enrollment status distribution
        $statusDistribution = [
            'enrolled' => $studentModel->where('enrollment_status', 'enrolled')->countAllResults(),
            'pending'  => $studentModel->where('enrollment_status', 'pending')->countAllResults(),
            'approved' => $studentModel->where('enrollment_status', 'approved')->countAllResults(),
            'rejected' => $studentModel->where('enrollment_status', 'rejected')->countAllResults(),
        ];

        // Recent enrolled students
        $recentEnrolled = $studentModel->where('enrollment_status', 'enrolled')
            ->orderBy('updated_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Key Metrics
        $total = array_sum($statusDistribution);
        $metrics = [
            'completionRate' => $total > 0 ? round(($statusDistribution['enrolled'] / $total) * 100) : 0,
            'pendingRate'    => $total > 0 ? round(($statusDistribution['pending'] / $total) * 100) : 0,
            'approvalRate'   => ($statusDistribution['approved'] + $statusDistribution['pending']) > 0
                                ? round(($statusDistribution['approved'] / ($statusDistribution['approved'] + $statusDistribution['pending'])) * 100)
                                : 0,
            'genderBalance'  => ($genderDistribution['male'] + $genderDistribution['female']) > 0
                                ? abs($genderDistribution['male'] - $genderDistribution['female'])
                                : 0,
        ];

        // Average grade per grade level (Quarter 1 current year)
        $gradeAverages = [7 => 0, 8 => 0, 9 => 0, 10 => 0];
        try {
            $avgRows = $db->table('grades')
                ->select('students.grade_level as grade_level, AVG(grades.grade) as avg_grade')
                ->join('students', 'students.id = grades.student_id', 'left')
                ->where('grades.school_year', '2024-2025')
                ->where('grades.quarter', 1)
                ->where('grades.grade IS NOT NULL')
                ->groupBy('students.grade_level')
                ->get()
                ->getResultArray();
            foreach ($avgRows as $row) {
                $gl = (int) ($row['grade_level'] ?? 0);
                if ($gl >= 7 && $gl <= 10) {
                    $gradeAverages[$gl] = round((float) $row['avg_grade'], 1);
                }
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }

        return view('admin/analytics', [
            'title' => 'Analytics Dashboard - LPHS SMS',
            'enrollmentTrends' => $enrollmentTrends,
            'enrollmentTrendsPrev' => $enrollmentTrendsPrev,
            'genderDistribution' => $genderDistribution,
            'gradeDistribution' => $gradeDistribution,
            'statusDistribution' => $statusDistribution,
            'recentEnrolled' => $recentEnrolled,
            'metrics' => $metrics,
            'gradeAverages' => $gradeAverages,
        ]);
    }

    /**
     * Get student details with documents for modal display
     */
    public function getStudentDetails($studentId)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = new StudentModel();
        $documentModel = new EnrollmentDocumentModel();

        // Get student details
        $student = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->find($studentId);

        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Get student documents
        $documents = $documentModel->where('student_id', $studentId)->findAll();

        // Organize documents by type
        $documentsByType = [];
        foreach ($documents as $doc) {
            $documentsByType[$doc['document_type']] = $doc;
        }

        return view('admin/partials/student_details_modal', [
            'student' => $student,
            'documents' => $documentsByType
        ]);
    }

    public function exportPdf()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        // Increase execution time for PDF generation
        set_time_limit(120);
        ini_set('memory_limit', '256M');

        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $db = \Config\Database::connect();

        // Get all analytics data
        $genderDistribution = [
            'male' => $studentModel->where('gender', 'Male')->where('enrollment_status', 'enrolled')->countAllResults(),
            'female' => $studentModel->where('gender', 'Female')->where('enrollment_status', 'enrolled')->countAllResults()
        ];

        $gradeDistribution = [];
        for ($grade = 7; $grade <= 10; $grade++) {
            $gradeDistribution[$grade] = $studentModel->where('grade_level', $grade)
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
        }

        $statusDistribution = [
            'enrolled' => $studentModel->where('enrollment_status', 'enrolled')->countAllResults(),
            'pending'  => $studentModel->where('enrollment_status', 'pending')->countAllResults(),
            'approved' => $studentModel->where('enrollment_status', 'approved')->countAllResults(),
            'rejected' => $studentModel->where('enrollment_status', 'rejected')->countAllResults(),
        ];

        $total = array_sum($statusDistribution);
        $metrics = [
            'completionRate' => $total > 0 ? round(($statusDistribution['enrolled'] / $total) * 100) : 0,
            'pendingRate'    => $total > 0 ? round(($statusDistribution['pending'] / $total) * 100) : 0,
            'approvalRate'   => ($statusDistribution['approved'] + $statusDistribution['pending']) > 0
                                ? round(($statusDistribution['approved'] / ($statusDistribution['approved'] + $statusDistribution['pending'])) * 100)
                                : 0,
            'genderBalance'  => ($genderDistribution['male'] + $genderDistribution['female']) > 0
                                ? abs($genderDistribution['male'] - $genderDistribution['female'])
                                : 0,
        ];

        $gradeAverages = [7 => 0, 8 => 0, 9 => 0, 10 => 0];
        try {
            $avgRows = $db->table('grades')
                ->select('students.grade_level as grade_level, AVG(grades.grade) as avg_grade')
                ->join('students', 'students.id = grades.student_id', 'left')
                ->where('grades.school_year', '2024-2025')
                ->where('grades.quarter', 1)
                ->where('grades.grade IS NOT NULL')
                ->groupBy('students.grade_level')
                ->get()
                ->getResultArray();
            foreach ($avgRows as $row) {
                $gl = (int) ($row['grade_level'] ?? 0);
                if ($gl >= 7 && $gl <= 10) {
                    $gradeAverages[$gl] = round((float) $row['avg_grade'], 1);
                }
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }

        $data = [
            'genderDistribution' => $genderDistribution,
            'gradeDistribution' => $gradeDistribution,
            'statusDistribution' => $statusDistribution,
            'metrics' => $metrics,
            'gradeAverages' => $gradeAverages,
            'reportDate' => date('F j, Y'),
            'schoolYear' => '2024-2025'
        ];

        $html = view('admin/analytics_pdf', $data);
        
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Times');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'LPHS_Analytics_Report_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * API endpoint for enrollment data
     */
    public function enrollmentData()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = new StudentModel();
        $db = \Config\Database::connect();

        // Get enrollment data by month for the last 3 years
        $enrollmentData = [];
        $predictionData = [];
        
        for ($year = 2023; $year <= 2025; $year++) {
            $monthly = [];
            $yearlyTotal = 0;
            
            for ($month = 1; $month <= 12; $month++) {
                $count = $studentModel->where('YEAR(created_at)', $year)
                    ->where('MONTH(created_at)', $month)
                    ->where('enrollment_status !=', 'rejected')
                    ->countAllResults();
                $monthly[] = $count;
                $yearlyTotal += $count;
            }
            
            $enrollmentData[$year] = [
                'monthly' => $monthly,
                'yearly' => [$yearlyTotal]
            ];
        }
        
        // Generate predictions based on historical data
        for ($year = 2025; $year <= 2027; $year++) {
            $baseYear = $year - 1;
            $growthRate = 1.08; // 8% growth prediction
            
            $monthly = [];
            $yearlyTotal = 0;
            
            if (isset($enrollmentData[$baseYear])) {
                foreach ($enrollmentData[$baseYear]['monthly'] as $monthValue) {
                    $predicted = round($monthValue * $growthRate);
                    $monthly[] = $predicted;
                    $yearlyTotal += $predicted;
                }
            } else {
                // Fallback prediction
                $monthly = [55, 63, 61, 78, 91, 98, 105, 101, 108, 115, 111, 118];
                $yearlyTotal = array_sum($monthly);
            }
            
            $predictionData[$year] = [
                'monthly' => $monthly,
                'yearly' => [$yearlyTotal]
            ];
        }

        return $this->response->setJSON([
            'enrollment' => $enrollmentData,
            'predictions' => $predictionData
        ]);
    }
}
