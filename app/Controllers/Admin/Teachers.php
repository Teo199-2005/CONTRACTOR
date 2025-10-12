<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\SectionModel;
use App\Models\TeacherScheduleModel;
use App\Models\SubjectModel;

class Teachers extends BaseController
{
    /**
     * Display teachers list
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        
        // Get search parameters
        $search = $this->request->getGet('search');
        
        // Build query
        $builder = $teacherModel->select('teachers.*');
        
        if ($search) {
            $builder->groupStart()
                   ->like('teachers.first_name', $search)
                   ->orLike('teachers.last_name', $search)
                   ->orLike('teachers.license_number', $search)
                   ->orLike('teachers.email', $search)
                   ->groupEnd();
        }
        
        $teachers = $builder->findAll();

        return view('admin/teachers', [
            'title' => 'Manage Teachers - LPHS SMS',
            'teachers' => $teachers,
            'search' => $search
        ]);
    }

    /**
     * Show create teacher form
     */
    public function create()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        return view('admin/teachers_create', [
            'title' => 'Add New Teacher - LPHS SMS'
        ]);
    }

    /**
     * Store new teacher
     */
    public function store()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        // Auto-generate teacher ID
        $currentYear = date('Y');
        $teacherModel = model(TeacherModel::class);
        $lastTeacher = $teacherModel->select('teacher_id')
            ->like('teacher_id', $currentYear . '-', 'after')
            ->orderBy('teacher_id', 'DESC')
            ->first();
        
        if ($lastTeacher && preg_match('/' . $currentYear . '-(\d+)/', $lastTeacher['teacher_id'], $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $teacherId = $currentYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $rules = [
            'license_number' => 'permit_empty|max_length[20]',
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'department' => 'permit_empty|max_length[100]',
            'position' => 'permit_empty|max_length[100]',
            'specialization' => 'permit_empty|max_length[100]',
            'date_hired' => 'required|valid_date',
            'employment_status' => 'required|in_list[active,inactive,resigned,terminated]'
        ];

        if (!$this->validate($rules)) {
            return view('admin/teachers_create', [
                'title' => 'Add New Teacher - LPHS SMS',
                'validation' => $this->validator
            ]);
        }

        $userModel = model(UserModel::class);
        $teacherModel = model(TeacherModel::class);

        // Create user account first
        $userData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'active' => 1
        ];

        $user = $userModel->save($userData);
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user account.');
        }

        $userId = $userModel->getInsertID();

        // Add user to teacher group
        $userEntity = $userModel->find($userId);
        $userEntity->addGroup('teacher');

        // Create teacher record
        $teacherData = [
            'user_id' => $userId,
            'teacher_id' => $teacherId,
            'license_number' => $this->request->getPost('license_number'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'department' => $this->request->getPost('subjects'),
            'position' => $this->request->getPost('position'),
            'specialization' => $this->request->getPost('subjects'),
            'date_hired' => $this->request->getPost('date_hired'),
            'employment_status' => $this->request->getPost('employment_status')
        ];

        if ($teacherModel->save($teacherData)) {
            return redirect()->to('admin/teachers')
                ->with('success', 'Teacher created successfully.');
        } else {
            // If teacher creation fails, delete the user account
            $userModel->delete($userId);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create teacher record.');
        }
    }

    /**
     * Show edit teacher form
     */
    public function edit($teacherId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);

        // Get teacher with user details - using WHERE clause instead of find()
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner')
            ->where('teachers.id', $teacherId)
            ->first();

        if (!$teacher) {
            return redirect()->to('admin/teachers')
                ->with('error', 'Teacher not found.');
        }

        return view('admin/teachers_edit', [
            'title' => 'Edit Teacher - LPHS SMS',
            'teacher' => $teacher
        ]);
    }

    /**
     * Get teacher edit form for modal
     */
    public function editForm($teacherId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized access']);
        }

        // Validate teacher ID
        if (!is_numeric($teacherId) || $teacherId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid teacher ID']);
        }

        $teacherModel = model(TeacherModel::class);
        
        try {
            // Check if teacher exists without join first
            $teacherExists = $teacherModel->find($teacherId);
            if (!$teacherExists) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
            }

            // Get teacher with user details - using WHERE clause instead of find()
            $teacher = $teacherModel->select('teachers.*, users.email')
                ->join('users', 'users.id = teachers.user_id', 'left')
                ->where('teachers.id', $teacherId)
                ->first();

            if (!$teacher) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher data could not be loaded']);
            }

            return view('admin/partials/teacher_edit_form', [
                'teacher' => $teacher
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading teacher edit form: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error occurred while loading teacher data']);
        }
    }

    /**
     * Update teacher
     */
    public function update($teacherId)
    {
        try {
            if (!auth()->user()->inGroup('admin')) {
                return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
            }

            $teacherModel = model(TeacherModel::class);
            $teacher = $teacherModel->find($teacherId);
            
            if (!$teacher) {
                return $this->response->setJSON(['success' => false, 'error' => 'Teacher not found']);
            }

            $data = [
                'license_number' => $this->request->getPost('license_number') ?: null,
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'gender' => $this->request->getPost('gender'),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'contact_number' => $this->request->getPost('contact_number') ?: null,
                'address' => $this->request->getPost('address') ?: null,
                'department' => $this->request->getPost('department') ?: null,
                'position' => $this->request->getPost('position') ?: null,
                'specialization' => $this->request->getPost('specialization') ?: null,
                'date_hired' => $this->request->getPost('date_hired'),
                'employment_status' => $this->request->getPost('employment_status')
            ];

            if ($teacherModel->update($teacherId, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Teacher updated successfully.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Failed to update teacher.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Teacher update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Server error occurred.'
            ]);
        }
    }



    /**
     * Delete teacher
     */
    public function delete($teacherId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $teacherModel = model(TeacherModel::class);
        $userModel = model(UserModel::class);

        $teacher = $teacherModel->find($teacherId);
        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
        }

        // Delete teacher record first
        if ($teacherModel->delete($teacherId)) {
            // Then delete user account
            $userModel->delete($teacher['user_id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher deleted successfully.'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to delete teacher.'
            ]);
        }
    }

    /**
     * View teacher details as full page
     */
    public function viewTeacher($teacherId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        $sectionModel = model(SectionModel::class);

        // Get teacher details
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'left')
            ->where('teachers.id', $teacherId)
            ->first();

        if (!$teacher) {
            return redirect()->to('admin/teachers')->with('error', 'Teacher not found');
        }

        // Get sections assigned to this teacher
        $sections = $sectionModel->where('adviser_id', $teacherId)->findAll();

        return view('admin/teacher_view', [
            'title' => 'Teacher Details - LPHS SMS',
            'teacher' => $teacher,
            'sections' => $sections
        ]);
    }

    /**
     * Get teacher details for modal display
     */
    public function details($teacherId)
    {
        return $this->getTeacherDetails($teacherId);
    }

    /**
     * Get teacher details for modal display
     */
    public function getTeacherDetails($teacherId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $teacherModel = model(TeacherModel::class);
        $sectionModel = model(SectionModel::class);

        // Get teacher details - using WHERE clause instead of find()
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'left')
            ->where('teachers.id', $teacherId)
            ->first();

        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
        }

        // Get sections assigned to this teacher
        $sections = $sectionModel->where('adviser_id', $teacherId)->findAll();

        return view('admin/partials/teacher_details_modal', [
            'teacher' => $teacher,
            'sections' => $sections
        ]);
    }

    /**
     * Manage teacher schedule
     */
    public function schedule($teacherId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        $scheduleModel = model(TeacherScheduleModel::class);
        $subjectModel = model(SubjectModel::class);
        $sectionModel = model(SectionModel::class);

        $teacher = $teacherModel->find($teacherId);
        if (!$teacher) {
            return redirect()->to('admin/teachers')->with('error', 'Teacher not found');
        }

        $schedules = $scheduleModel->getTeacherSchedule($teacherId);
        $subjects = $subjectModel->findAll();
        $allSections = $sectionModel->select('id, section_name, grade_level')
                                    ->where('is_active', true)
                                    ->findAll();
        
        // Remove duplicates by creating unique key
        $uniqueSections = [];
        $sections = [];
        foreach ($allSections as $section) {
            $key = $section['section_name'] . '_' . $section['grade_level'];
            if (!isset($uniqueSections[$key])) {
                $uniqueSections[$key] = true;
                $sections[] = $section;
            }
        }

        return view('admin/teacher_schedule', [
            'title' => 'Manage Schedule - ' . $teacher['first_name'] . ' ' . $teacher['last_name'],
            'teacher' => $teacher,
            'schedules' => $schedules,
            'subjects' => $subjects,
            'sections' => $sections
        ]);
    }

    /**
     * Save teacher schedule
     */
    public function saveSchedule($teacherId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $scheduleModel = model(TeacherScheduleModel::class);
        
        // Get JSON data from request body
        $input = $this->request->getJSON(true);
        $schedules = $input['schedules'] ?? [];

        if (empty($schedules)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No schedule data provided']);
        }

        // Delete existing schedules
        $scheduleModel->where('teacher_id', $teacherId)->delete();

        // Insert new schedules
        foreach ($schedules as $schedule) {
            $scheduleModel->insert([
                'teacher_id' => $teacherId,
                'subject_id' => $schedule['subject_id'],
                'section_id' => $schedule['section_id'],
                'day_of_week' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
                'room' => $schedule['room'] ?? null,
                'school_year' => '2024-2025'
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Schedule saved successfully']);
    }
}
