<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\SectionModel;

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
        $builder = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner');
        
        if ($search) {
            $builder->groupStart()
                   ->like('teachers.first_name', $search)
                   ->orLike('teachers.last_name', $search)
                   ->orLike('teachers.teacher_id', $search)
                   ->orLike('users.email', $search)
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
            'hire_date' => 'required|valid_date',
            'employment_status' => 'required|in_list[active,inactive,on_leave]'
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
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'department' => $this->request->getPost('subjects'),
            'position' => $this->request->getPost('position'),
            'specialization' => $this->request->getPost('subjects'),
            'hire_date' => $this->request->getPost('hire_date'),
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

        // Get teacher with user details
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner')
            ->find($teacherId);

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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $teacherModel = model(TeacherModel::class);

        // Get teacher with user details
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner')
            ->find($teacherId);

        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
        }

        return view('admin/partials/teacher_edit_form', [
            'teacher' => $teacher
        ]);
    }

    /**
     * Update teacher
     */
    public function update($teacherId)
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

        $rules = [
            'teacher_id' => "required|is_unique[teachers.teacher_id,id,{$teacherId}]",
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[users.email,id,{$teacher['user_id']}]",
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'department' => 'permit_empty|max_length[100]',
            'position' => 'permit_empty|max_length[100]',
            'specialization' => 'permit_empty|max_length[100]',
            'hire_date' => 'required|valid_date',
            'employment_status' => 'required|in_list[active,inactive,on_leave]'
        ];

        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Update user account
        $userData = [
            'email' => $this->request->getPost('email')
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = $this->request->getPost('password');
        }

        $userModel->update($teacher['user_id'], $userData);

        // Update teacher record
        $teacherData = [
            'teacher_id' => $this->request->getPost('teacher_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'department' => $this->request->getPost('department'),
            'position' => $this->request->getPost('position'),
            'specialization' => $this->request->getPost('specialization'),
            'hire_date' => $this->request->getPost('hire_date'),
            'employment_status' => $this->request->getPost('employment_status')
        ];

        if ($teacherModel->update($teacherId, $teacherData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to update teacher record.'
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

        // Get teacher details
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner')
            ->find($teacherId);

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
}
