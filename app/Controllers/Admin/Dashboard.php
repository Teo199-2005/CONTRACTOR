<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\SectionModel;
use App\Models\AnnouncementModel;
use App\Models\GradeModel;
use App\Models\EnrollmentDocumentModel;
use CodeIgniter\Shield\Models\UserModel;

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
        $systemSettingModel = new \App\Models\SystemSettingModel();

        // Get dashboard statistics
        $stats = [
            'total_students' => $studentModel->where('enrollment_status', 'enrolled')->countAllResults(),
            'pending_enrollments' => $studentModel->where('enrollment_status', 'pending')->countAllResults(),
            'total_teachers' => $teacherModel->where('employment_status', 'active')->countAllResults(),
            'total_sections' => $sectionModel->where('is_active', true)->countAllResults(),
            'total_announcements' => $announcementModel->countAllResults()
        ];

        // Get recent enrollments (all statuses)
        $recentEnrollments = $studentModel->select('students.*, users.email')
            ->join('users', 'users.id = students.user_id', 'left')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get enrollment statistics by grade level
        $enrollmentByGrade = [];
        for ($grade = 7; $grade <= 12; $grade++) {
            $enrollmentByGrade[$grade] = $studentModel->where('grade_level', $grade)
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
        }

        // Get recent announcements
        $recentAnnouncements = $announcementModel->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get enrollment data for charts - use real database data
        $enrollmentData = $this->getEnrollmentDataFromDB();
        $predictionData = $this->generatePredictions($enrollmentData);
        
        // Get current month enrollments for real-time updates
        $db = \Config\Database::connect();
        
        // Get monthly enrollment data for enrolled students chart
        $monthlyEnrollmentData = $this->getMonthlyEnrollmentData();
        
        // Count all students enrolled in current school year (2025-2026)
        $currentMonthEnrollments = $db->query("
            SELECT COUNT(*) as count FROM students 
            WHERE enrollment_status = 'enrolled' 
            AND school_year = '2025-2026'
            AND deleted_at IS NULL
        ")->getRow()->count ?? 0;

        return view('admin/dashboard', [
            'title' => 'Admin Dashboard - LPHS SMS',
            'stats' => $stats,
            'recentEnrollments' => $recentEnrollments,
            'enrollmentByGrade' => $enrollmentByGrade,
            'recentAnnouncements' => $recentAnnouncements,
            'enrollmentData' => $enrollmentData,
            'enrollmentDataJson' => json_encode($enrollmentData),
            'predictionData' => json_encode($predictionData),
            'monthlyEnrollmentData' => json_encode($monthlyEnrollmentData),
            'currentMonthEnrollments' => $currentMonthEnrollments,
            'currentQuarter' => $systemSettingModel->getCurrentQuarter()
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

        // Get student info before rejection for logging
        $student = $studentModel->find($studentId);
        
        if ($studentModel->rejectEnrollment($studentId, $reason)) {
            // Log the rejection for debugging
            log_message('info', "Student enrollment rejected: ID {$studentId}, Name: {$student['first_name']} {$student['last_name']}");
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
                ->orLike('students.lrn', $search)
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
        if (!$this->auth->user() || !$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        // Auto-fix sections if cleanup parameter is present
        if ($this->request->getGet('cleanup') === 'true') {
            $this->cleanupSections();
            return redirect()->to(base_url('admin/sections'))->with('success', 'Sections cleaned up successfully!');
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
        
        // Debug logging
        log_message('info', 'Available teachers count: ' . count($availableTeachers));
        foreach ($availableTeachers as $teacher) {
            log_message('info', 'Teacher: ' . $teacher['first_name'] . ' ' . $teacher['last_name'] . ' (ID: ' . $teacher['id'] . ')');
        }

        return view('admin/sections', [
            'title' => 'Manage Sections & Faculty Assignment - LPHS SMS',
            'sections' => $sections,
            'availableTeachers' => $availableTeachers,
            'gradeFilter' => $gradeFilter,
            'adviserFilter' => $adviserFilter,
            'searchTerm' => $searchTerm
        ]);
    }

    private function cleanupSections()
    {
        $db = \Config\Database::connect();
        
        // Find wrong section
        $wrongSection = $db->query("SELECT id FROM sections WHERE section_name = 'Grade 10 - Aristotle'")->getRowArray();
        $correctSection = $db->query("SELECT id FROM sections WHERE section_name = 'Aristotle'")->getRowArray();
        $aphroditeSection = $db->query("SELECT id FROM sections WHERE section_name = 'Aphrodite'")->getRowArray();
        
        if ($wrongSection && $correctSection) {
            // Move students from wrong section to correct section
            $db->query("UPDATE students SET section_id = ? WHERE section_id = ?", [$correctSection['id'], $wrongSection['id']]);
            // Delete wrong section
            $db->query("DELETE FROM sections WHERE id = ?", [$wrongSection['id']]);
        }
        
        if ($correctSection && $aphroditeSection) {
            // Move Grade 10 students from Aphrodite to Aristotle
            $db->query("UPDATE students SET section_id = ? WHERE (grade_level = 10 OR lrn LIKE 'DEMO-STUDENT-%') AND section_id = ?", [$correctSection['id'], $aphroditeSection['id']]);
        }
    }

    public function assignAdviser($sectionId)
    {
        try {
            if (!$this->auth->user()->inGroup('admin')) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized access']);
                }
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

            // Check if section already has an adviser
            if (!empty($section['adviser_id'])) {
                return redirect()->back()->with('error', 'This section already has an adviser assigned.');
            }

            // Check if teacher exists and is available
            $teacher = $teacherModel->find($adviserId);
            if (!$teacher) {
                return redirect()->back()->with('error', 'Teacher not found.');
            }

            if ($teacher['employment_status'] !== 'active') {
                return redirect()->back()->with('error', 'Selected teacher is not active.');
            }

            // Check if teacher is already assigned to another section
            $existingAssignment = $sectionModel->where('adviser_id', $adviserId)
                                              ->where('is_active', true)
                                              ->first();
            if ($existingAssignment) {
                return redirect()->back()->with('error', 'This teacher is already assigned to another section.');
            }

            // Debug logging
            log_message('info', "Attempting to assign teacher ID {$adviserId} to section ID {$sectionId}");
            
            // Assign the adviser using raw query to ensure it works
            $db = \Config\Database::connect();
            $result = $db->query("UPDATE sections SET adviser_id = ?, updated_at = NOW() WHERE id = ?", [$adviserId, $sectionId]);
            
            if ($result) {
                // Verify the update worked
                $updatedSection = $db->query("SELECT id, section_name, adviser_id FROM sections WHERE id = ?", [$sectionId])->getRowArray();
                log_message('info', "Direct DB update result - Section {$sectionId} adviser_id: " . ($updatedSection['adviser_id'] ?? 'NULL'));
                
                if ($updatedSection && $updatedSection['adviser_id'] == $adviserId) {
                    return redirect()->back()->with('success', 'Teacher successfully assigned as section adviser.');
                } else {
                    log_message('error', "Database update failed - adviser_id not set correctly for section {$sectionId}");
                    return redirect()->back()->with('error', 'Assignment appeared successful but database was not updated.');
                }
            } else {
                log_message('error', "Failed to assign teacher {$adviserId} to section {$sectionId}");
                return redirect()->back()->with('error', 'Failed to assign teacher as adviser.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in assignAdviser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while assigning the adviser.');
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
        $students = $studentModel->select('id, lrn, first_name, last_name, created_at')
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

    public function getUnassignedStudents($gradeLevel)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $studentModel = new StudentModel();

        // Get students without section assignment for the specified grade level
        $students = $studentModel->select('id, lrn, first_name, last_name')
                                 ->where('grade_level', $gradeLevel)
                                 ->where('enrollment_status', 'enrolled')
                                 ->where('section_id IS NULL')
                                 ->orderBy('last_name', 'ASC')
                                 ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'students' => $students,
            'message' => count($students) > 0 ? null : 'No unassigned students found for Grade ' . $gradeLevel
        ]);
    }

    public function assignStudentsToSection($sectionId)
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

        // Get student IDs from request
        $input = json_decode($this->request->getBody(), true);
        $studentIds = $input['student_ids'] ?? [];

        if (empty($studentIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No students selected']);
        }

        // Check section capacity
        $currentEnrollment = $studentModel->where('section_id', $sectionId)
                                          ->where('enrollment_status', 'enrolled')
                                          ->countAllResults();
        
        $availableSlots = $section['max_capacity'] - $currentEnrollment;
        
        if (count($studentIds) > $availableSlots) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Section only has {$availableSlots} available slots, but you selected " . count($studentIds) . " students"
            ]);
        }

        // Assign students to section
        $assignedCount = 0;
        foreach ($studentIds as $studentId) {
            $student = $studentModel->find($studentId);
            if ($student && empty($student['section_id'])) {
                if ($studentModel->update($studentId, ['section_id' => $sectionId])) {
                    $assignedCount++;
                }
            }
        }

        // Update section enrollment count
        $sectionModel->updateEnrollmentCount($sectionId);

        if ($assignedCount > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully assigned {$assignedCount} student(s) to {$section['section_name']}"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No students were assigned. They may already be assigned to sections.'
            ]);
        }
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
            // Use enrollment_date if available, otherwise fall back to created_at
            $dateField = $db->fieldExists('enrollment_date', 'students') ? 'enrollment_date' : 'created_at';
            
            $countThis = $studentModel->where("MONTH({$dateField})", $monthNum)
                ->where("YEAR({$dateField})", $currentYear)
                ->where('enrollment_status', 'enrolled')
                ->countAllResults();
            $enrollmentTrends[] = ['month' => $month, 'count' => $countThis];

            $countPrev = $studentModel->where("MONTH({$dateField})", $monthNum)
                ->where("YEAR({$dateField})", $previousYear)
                ->where('enrollment_status', 'enrolled')
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
        for ($grade = 7; $grade <= 12; $grade++) {
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

        // Teacher statistics
        $teacherModel = new TeacherModel();
        $teacherStats = [
            'active' => $teacherModel->where('employment_status', 'active')->countAllResults(),
            'inactive' => $teacherModel->where('employment_status', 'inactive')->countAllResults(),
            'with_adviser' => $db->query("SELECT COUNT(DISTINCT adviser_id) as count FROM sections WHERE adviser_id IS NOT NULL AND is_active = 1")->getRow()->count ?? 0
        ];
        $teacherStats['without_adviser'] = $teacherStats['active'] - $teacherStats['with_adviser'];

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
        $gradeAverages = [7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
        try {
            $avgRows = $db->table('grades')
                ->select('students.grade_level as grade_level, AVG(grades.grade) as avg_grade')
                ->join('students', 'students.id = grades.student_id', 'left')
                ->where('grades.school_year', '2025-2026')
                ->where('grades.quarter', 1)
                ->where('grades.grade IS NOT NULL')
                ->groupBy('students.grade_level')
                ->get()
                ->getResultArray();
            foreach ($avgRows as $row) {
                $gl = (int) ($row['grade_level'] ?? 0);
                if ($gl >= 7 && $gl <= 12) {
                    $gradeAverages[$gl] = round((float) $row['avg_grade'], 1);
                }
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }

        // Add cache busting timestamp
        $cacheTimestamp = time();
        
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
            'teacherStats' => $teacherStats,
            'cacheTimestamp' => $cacheTimestamp,
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
        for ($grade = 7; $grade <= 12; $grade++) {
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

        $gradeAverages = [7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
        try {
            $avgRows = $db->table('grades')
                ->select('students.grade_level as grade_level, AVG(grades.grade) as avg_grade')
                ->join('students', 'students.id = grades.student_id', 'left')
                ->where('grades.school_year', '2025-2026')
                ->where('grades.quarter', 1)
                ->where('grades.grade IS NOT NULL')
                ->groupBy('students.grade_level')
                ->get()
                ->getResultArray();
            foreach ($avgRows as $row) {
                $gl = (int) ($row['grade_level'] ?? 0);
                if ($gl >= 7 && $gl <= 12) {
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
            'schoolYear' => '2025-2026'
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

    private function getEnrollmentDataFromDB(): array
    {
        $db = \Config\Database::connect();
        
        // Get enrollment data for multiple years
        $enrollmentData = [];
        
        for ($year = 2023; $year <= 2025; $year++) {
            $monthlyResults = $db->query("
                SELECT 
                    MONTH(created_at) as month,
                    COUNT(*) as count
                FROM students 
                WHERE YEAR(created_at) = ?
                AND deleted_at IS NULL
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ", [$year])->getResultArray();
            
            // Initialize monthly array with zeros
            $monthly = array_fill(0, 12, 0);
            $yearlyTotal = 0;
            
            // Fill in actual counts from database
            foreach ($monthlyResults as $result) {
                if ($result['month'] >= 1 && $result['month'] <= 12) {
                    $monthly[$result['month'] - 1] = (int)$result['count'];
                    $yearlyTotal += (int)$result['count'];
                }
            }
            
            $enrollmentData[$year] = [
                'monthly' => $monthly, 
                'yearly' => [$yearlyTotal]
            ];
        }
        
        return $enrollmentData;
    }
    
    private function getEnrollmentData(): array
    {
        return $this->getEnrollmentDataFromDB();
    }
    
    private function generatePredictions(array $historicalData): array
    {
        // Get current enrolled student data as baseline
        $currentEnrolledData = $this->getMonthlyEnrollmentData();
        $currentTotal = array_sum($currentEnrolledData);
        
        // Calculate growth rate based on current enrollment trends
        $baseGrowthRate = 0.08; // 8% annual growth (typical for growing schools)
        
        $predictions = [];
        
        // Generate predictions for 2026-2028 based on current enrollment data
        for ($year = 2026; $year <= 2028; $year++) {
            $yearsFromNow = $year - 2025;
            $growthFactor = pow(1 + $baseGrowthRate, $yearsFromNow);
            
            // Apply growth to each month's current enrollment
            $monthlyPredictions = [];
            foreach ($currentEnrolledData as $monthValue) {
                $monthlyPredictions[] = round($monthValue * $growthFactor);
            }
            
            $predictions[$year] = [
                'monthly' => $monthlyPredictions,
                'yearly' => [array_sum($monthlyPredictions)]
            ];
        }
        
        return $predictions;
    }

    public function testAssignment($sectionId = null)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $sectionModel = new SectionModel();
        
        if ($sectionId) {
            $section = $sectionModel->find($sectionId);
            return $this->response->setJSON([
                'section' => $section,
                'adviser_id' => $section['adviser_id'] ?? 'NULL'
            ]);
        }
        
        $sections = $sectionModel->select('id, section_name, adviser_id')
                                ->where('adviser_id IS NOT NULL')
                                ->findAll();
        
        return $this->response->setJSON([
            'sections_with_advisers' => $sections,
            'count' => count($sections)
        ]);
    }

    public function updateQuarter()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $quarter = $this->request->getPost('quarter');
        if (!in_array($quarter, [1, 2, 3, 4])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid quarter']);
        }

        $systemSettingModel = new \App\Models\SystemSettingModel();
        if ($systemSettingModel->setCurrentQuarter($quarter)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Quarter updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update quarter']);
    }

    public function createAdmin()
    {
        // Enhanced authorization check
        if (!$this->auth->user()->inGroup('admin')) {
            log_message('warning', 'Unauthorized admin creation attempt from user ID: ' . ($this->auth->id() ?? 'unknown'));
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // CSRF protection
        if (!$this->request->is('post') || !$this->validate(['csrf_token' => 'required'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Rate limiting check (max 3 admin creations per hour per user)
        $cache = \Config\Services::cache();
        $rateLimitKey = 'admin_creation_' . $this->auth->id();
        $attempts = $cache->get($rateLimitKey) ?? 0;
        
        if ($attempts >= 3) {
            log_message('warning', 'Rate limit exceeded for admin creation by user ID: ' . $this->auth->id());
            return $this->response->setJSON(['success' => false, 'message' => 'Too many admin creation attempts. Try again later.']);
        }

        // Enhanced validation rules
        $rules = [
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            'first_name' => 'required|min_length[2]|max_length[50]|alpha_space',
            'last_name' => 'required|min_length[2]|max_length[50]|alpha_space',
            'password' => 'required|min_length[12]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            $cache->save($rateLimitKey, $attempts + 1, 3600); // 1 hour
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed. Password must be 12+ chars with uppercase, lowercase, number, and special character.']);
        }

        try {
            $db = \Config\Database::connect();
            $users = model(\CodeIgniter\Shield\Models\UserModel::class);
            
            $email = $this->request->getPost('email');
            $firstName = $this->request->getPost('first_name');
            $lastName = $this->request->getPost('last_name');
            
            // Create user entity with inactive status for verification
            $user = new \CodeIgniter\Shield\Entities\User([
                'email' => $email,
                'password' => $this->request->getPost('password'),
                'active' => 0 // Require activation
            ]);

            $users->save($user);
            $userId = $users->getInsertID();
            
            if ($userId) {
                // Add to admin group
                $db->table('auth_groups_users')->ignore(true)->insert([
                    'user_id' => $userId,
                    'group' => 'admin',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                
                // Log successful admin creation
                log_message('info', 'Admin account created successfully. Email: ' . $email . ' by user ID: ' . $this->auth->id());
                
                // Clear rate limit on success
                $cache->delete($rateLimitKey);
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Admin account created. Account requires activation before use. Email: ' . $email
                ]);
            }
        } catch (\Exception $e) {
            // Log error without exposing sensitive details
            log_message('error', 'Admin creation failed for email: ' . ($this->request->getPost('email') ?? 'unknown') . ' Error: ' . $e->getMessage());
            $cache->save($rateLimitKey, $attempts + 1, 3600);
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create admin account. Please try again.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create admin account']);
    }

    /**
     * Debug method to check enrollment data by month
     */
    public function debugEnrollmentData()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = new StudentModel();
        $db = \Config\Database::connect();
        
        // Get enrollment data by month for 2024
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = $studentModel->where('YEAR(created_at)', 2024)
                ->where('MONTH(created_at)', $month)
                ->countAllResults();
            $monthlyData[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'count' => $count
            ];
        }
        
        // Get sample student records
        $sampleStudents = $db->query("
            SELECT id, first_name, last_name, created_at, enrollment_status 
            FROM students 
            WHERE deleted_at IS NULL 
            ORDER BY created_at DESC 
            LIMIT 20
        ")->getResultArray();
        
        return $this->response->setJSON([
            'monthly_data_2024' => $monthlyData,
            'sample_students' => $sampleStudents,
            'total_students' => $studentModel->countAllResults()
        ]);
    }

    /**
     * Get monthly enrollment data for enrolled students chart
     */
    private function getMonthlyEnrollmentData(): array
    {
        $db = \Config\Database::connect();
        
        // Get students enrolled before today (for scattering)
        $oldStudents = $db->query("
            SELECT COUNT(*) as count 
            FROM students 
            WHERE DATE(created_at) < CURDATE()
            AND enrollment_status = 'enrolled'
            AND deleted_at IS NULL
        ")->getRow()->count ?? 0;
        
        // Get students enrolled today and onwards (real data)
        $newStudents = $db->query("
            SELECT MONTH(created_at) as month, COUNT(*) as count 
            FROM students 
            WHERE DATE(created_at) >= CURDATE()
            AND enrollment_status = 'enrolled'
            AND deleted_at IS NULL
            GROUP BY MONTH(created_at)
        ")->getResultArray();
        
        // Philippine enrollment distribution pattern (Jan-Oct only, Nov-Dec = 0)
        $distribution = [0.03, 0.02, 0.02, 0.07, 0.20, 0.45, 0.15, 0.04, 0.02, 0.00, 0.00, 0.00];
        
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            if ($month <= 10) {
                // Scatter old data across Jan-Oct only
                $count = (int)round($oldStudents * $distribution[$month - 1]);
            } else {
                // Nov-Dec start with 0 (no scattered data)
                $count = 0;
            }
            
            // Add real new enrollments for this month
            foreach ($newStudents as $newStudent) {
                if ($newStudent['month'] == $month) {
                    $count += (int)$newStudent['count'];
                }
            }
            
            $monthlyData[] = $count;
        }
        
        return $monthlyData;
    }

    /**
     * Debug method to check enrollment status distribution
     */
    public function debugEnrollmentStatus()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = new StudentModel();
        $db = \Config\Database::connect();
        
        // Get raw counts from database
        $statusCounts = $db->query("
            SELECT enrollment_status, COUNT(*) as count 
            FROM students 
            WHERE deleted_at IS NULL 
            GROUP BY enrollment_status
        ")->getResultArray();
        
        // Get recent rejected students
        $recentRejected = $studentModel->where('enrollment_status', 'rejected')
            ->orderBy('updated_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        return $this->response->setJSON([
            'status_counts' => $statusCounts,
            'recent_rejected' => $recentRejected,
            'total_students' => $studentModel->countAllResults()
        ]);
    }

    /**
     * Debug and fix student section assignments
     */
    public function fixStudentSections()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        
        // Find and remove the incorrectly named "Grade 10 - Aristotle" section
        $wrongSection = $db->query("
            SELECT id, section_name FROM sections 
            WHERE section_name = 'Grade 10 - Aristotle'
        ")->getRowArray();
        
        // Find the correct Aristotle section
        $correctSection = $db->query("
            SELECT id, section_name FROM sections 
            WHERE section_name = 'Aristotle'
        ")->getRowArray();
        
        $moved = 0;
        $deleted = 0;
        
        if ($wrongSection && $correctSection) {
            // Move students from wrong section to correct section
            $result = $db->query("
                UPDATE students 
                SET section_id = ? 
                WHERE section_id = ?
            ", [$correctSection['id'], $wrongSection['id']]);
            
            if ($result) {
                $moved = $db->affectedRows();
            }
            
            // Delete the incorrectly named section
            $deleteResult = $db->query("
                DELETE FROM sections WHERE id = ?
            ", [$wrongSection['id']]);
            
            if ($deleteResult) {
                $deleted = 1;
            }
        }
        
        // Also fix Grade 10 students in Aphrodite section
        $aphroditeSection = $db->query("
            SELECT id FROM sections WHERE section_name = 'Aphrodite'
        ")->getRowArray();
        
        $fixedAphrodite = 0;
        if ($correctSection && $aphroditeSection) {
            $result = $db->query("
                UPDATE students 
                SET section_id = ? 
                WHERE (grade_level = 10 OR lrn LIKE 'DEMO-STUDENT-%') 
                AND section_id = ?
            ", [$correctSection['id'], $aphroditeSection['id']]);
            
            if ($result) {
                $fixedAphrodite = $db->affectedRows();
            }
        }
        
        return $this->response->setJSON([
            'moved_from_wrong_section' => $moved,
            'deleted_sections' => $deleted,
            'fixed_from_aphrodite' => $fixedAphrodite,
            'message' => "Moved {$moved} students from wrong section, deleted {$deleted} incorrect section, fixed {$fixedAphrodite} from Aphrodite"
        ]);
    }
}
