<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentDocumentModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\SectionModel;

class Students extends BaseController
{
    public function index()
    {
        $studentModel = model(StudentModel::class);

        // Get filter parameters
        $gradeLevel = $this->request->getGet('grade');
        $section = $this->request->getGet('section');
        $search = $this->request->getGet('search');

        // Build query for students using the students table as primary
        $builder = $studentModel->select('students.*, sections.section_name, users.email, users.id as user_id')
                                ->join('sections', 'sections.id = students.section_id', 'left')
                                ->join('users', 'users.id = students.user_id', 'left')
                                ->where('students.enrollment_status', 'enrolled');

        // Apply filters
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

        $students = $builder->findAll();
        
        // Get all sections for filter dropdown
        $sectionModel = model('SectionModel');
        $allSections = $sectionModel->findAll();
        
        return view('admin/students', [
            'title' => 'Manage Students - LPHS SMS',
            'students' => $students,
            'allSections' => $allSections,
            'gradeLevel' => $gradeLevel,
            'section' => $section,
            'search' => $search
        ]);
    }

    /**
     * Get student details with documents for modal display
     */
    public function getStudentDetails($studentId)
    {
        $studentModel = model(StudentModel::class);
        $documentModel = model(EnrollmentDocumentModel::class);

        // Get student details with section and user info
        $student = $studentModel->select('students.*, sections.section_name, users.email')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->find($studentId);

        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Get student documents using the students table ID
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

    /**
     * Show create student form
     */
    public function create()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = model(SectionModel::class);
        $sections = $sectionModel->findAll();

        return view('admin/students_create', [
            'title' => 'Add New Student - LPHS SMS',
            'sections' => $sections
        ]);
    }

    /**
     * Store new student
     */
    public function store()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'student_id' => 'required|is_unique[students.student_id]',
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'grade_level' => 'required|in_list[Grade 7,Grade 8,Grade 9,Grade 10]',
            'section_id' => 'permit_empty|integer',
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'place_of_birth' => 'permit_empty|max_length[100]',
            'nationality' => 'permit_empty|max_length[50]',
            'religion' => 'permit_empty|max_length[50]',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'emergency_contact_name' => 'permit_empty|max_length[100]',
            'emergency_contact_number' => 'permit_empty|max_length[20]',
            'emergency_contact_relationship' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            $sectionModel = model(SectionModel::class);
            $sections = $sectionModel->findAll();

            return view('admin/students_create', [
                'title' => 'Add New Student - LPHS SMS',
                'sections' => $sections,
                'validation' => $this->validator
            ]);
        }

        $userModel = model(UserModel::class);
        $studentModel = model(StudentModel::class);

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

        // Add user to student group
        $userEntity = $userModel->find($userId);
        $userEntity->addGroup('student');

        // Create student record
        $studentData = [
            'user_id' => $userId,
            'student_id' => $this->request->getPost('student_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'grade_level' => $this->request->getPost('grade_level'),
            'section_id' => $this->request->getPost('section_id') ?: null,
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'place_of_birth' => $this->request->getPost('place_of_birth'),
            'nationality' => $this->request->getPost('nationality'),
            'religion' => $this->request->getPost('religion'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
            'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship'),
            'enrollment_status' => 'enrolled',
            'school_year' => '2024-2025'
        ];

        if ($studentModel->save($studentData)) {
            return redirect()->to('admin/students')
                ->with('success', 'Student created successfully.');
        } else {
            // If student creation fails, delete the user account
            $userModel->delete($userId);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create student record.');
        }
    }

    /**
     * Show edit student form
     */
    public function edit($studentId)
    {
        // Check if user is admin
        if (!auth()->user() || !auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('login'))->with('error', 'Admin access required.');
        }

        $studentModel = model(StudentModel::class);
        $sectionModel = model(SectionModel::class);
        $userModel = model(UserModel::class);

        // Get student with user details
        $student = $studentModel->select('students.*, users.email')
            ->join('users', 'users.id = students.user_id')
            ->find($studentId);

        if (!$student) {
            return redirect()->to('admin/students')
                ->with('error', 'Student not found.');
        }

        $sections = $sectionModel->findAll();

        return view('admin/students_edit', [
            'title' => 'Edit Student - LPHS SMS',
            'student' => $student,
            'sections' => $sections
        ]);
    }

    /**
     * Update student
     */
    public function update($studentId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = model(StudentModel::class);
        $userModel = model(UserModel::class);

        $student = $studentModel->find($studentId);
        if (!$student) {
            return redirect()->to('admin/students')
                ->with('error', 'Student not found.');
        }

        $rules = [
            'student_id' => "required|is_unique[students.student_id,id,{$studentId}]",
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[users.email,id,{$student['user_id']}]",
            'grade_level' => 'required|integer',
            'section_id' => 'permit_empty|integer',
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'emergency_contact_name' => 'permit_empty|max_length[100]',
            'emergency_contact_number' => 'permit_empty|max_length[20]',
            'emergency_contact_relationship' => 'permit_empty|max_length[50]',
            'enrollment_status' => 'required|in_list[enrolled,suspended,graduated,transferred]'
        ];

        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            $sectionModel = model(SectionModel::class);
            $sections = $sectionModel->findAll();

            return view('admin/students_edit', [
                'title' => 'Edit Student - LPHS SMS',
                'student' => $student,
                'sections' => $sections,
                'validation' => $this->validator
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

        $userModel->update($student['user_id'], $userData);

        // Update student record
        $studentData = [
            'student_id' => $this->request->getPost('student_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'grade_level' => $this->request->getPost('grade_level'),
            'section_id' => $this->request->getPost('section_id') ?: null,
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'place_of_birth' => $this->request->getPost('place_of_birth'),
            'nationality' => $this->request->getPost('nationality'),
            'religion' => $this->request->getPost('religion'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
            'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship'),
            'enrollment_status' => $this->request->getPost('enrollment_status')
        ];

        if ($studentModel->update($studentId, $studentData)) {
            return redirect()->to('admin/students')
                ->with('success', 'Student updated successfully.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update student record.');
        }
    }

    /**
     * Delete student
     */
    public function delete($studentId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $userModel = model(UserModel::class);

        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Delete student record first
        if ($studentModel->delete($studentId)) {
            // Then delete user account
            $userModel->delete($student['user_id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student deleted successfully.'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to delete student.'
            ]);
        }
    }
}