<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentDocumentModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\SectionModel;
use App\Libraries\SupabaseEmailService;
use CodeIgniter\Shield\Entities\User;

class Students extends BaseController
{
    public function index()
    {
        $studentModel = model(StudentModel::class);

        // Get filter parameters
        $gradeLevel = $this->request->getGet('grade');
        $section = $this->request->getGet('section');
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status') ?: 'enrolled'; // Default to enrolled

        // Build query for students using the students table as primary
        $builder = $studentModel->select('students.*, sections.section_name, users.email, users.id as user_id')
                                ->join('sections', 'sections.id = students.section_id', 'left')
                                ->join('users', 'users.id = students.user_id', 'left')
                                ->where('students.enrollment_status', $status)
                                ->orderBy('students.created_at', 'DESC');

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
                   ->orLike('students.lrn', $search)
                   ->groupEnd();
        }

        $students = $builder->findAll();
        
        // Get all sections for filter dropdown
        $sectionModel = model('SectionModel');
        $allSections = $sectionModel->findAll();
        
        // Get pending applications count for badge
        $pendingCount = $studentModel->where('enrollment_status', 'pending')->countAllResults();
        
        return view('admin/students', [
            'title' => 'Manage Students - LPHS SMS',
            'students' => $students,
            'allSections' => $allSections,
            'gradeLevel' => $gradeLevel,
            'section' => $section,
            'search' => $search,
            'status' => $status,
            'pendingCount' => $pendingCount
        ]);
    }

    /**
     * View student details as full page
     */
    public function viewStudent($studentId)
    {
        $studentModel = model(StudentModel::class);
        $documentModel = model(EnrollmentDocumentModel::class);

        // Get student details with section and user info
        $student = $studentModel->select('students.*, sections.section_name, users.email as user_email')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->find($studentId);
            
        // Use student email if available, otherwise use user email
        if ($student) {
            $student['email'] = $student['email'] ?? $student['user_email'] ?? '';
        }

        if (!$student) {
            return redirect()->to('admin/students/pending')->with('error', 'Student not found');
        }

        // Get student documents
        $documents = $documentModel->where('student_id', $studentId)->findAll();

        // Organize documents by type
        $documentsByType = [];
        foreach ($documents as $doc) {
            $documentsByType[$doc['document_type']] = $doc;
        }

        // Check if this is a password reset request
        $passwordReset = $this->request->getGet('password_reset') === 'true';

        return view('admin/student_view', [
            'title' => 'Student Details - LPHS SMS',
            'student' => $student,
            'documents' => $documentsByType,
            'passwordReset' => $passwordReset
        ]);
    }

    /**
     * Update student password
     */
    public function updatePassword($id)
    {
        $studentModel = model(StudentModel::class);
        $student = $studentModel->find($id);
        
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        $input = $this->request->getJSON(true);
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        if (empty($password) || empty($confirmPassword)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Password and confirmation are required']);
        }

        if ($password !== $confirmPassword) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Passwords do not match']);
        }

        if (strlen($password) < 6) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Password must be at least 6 characters long']);
        }

        // Update password in auth_identities table
        $db = \Config\Database::connect();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $result = $db->table('auth_identities')
            ->where('user_id', $student['user_id'])
            ->where('type', 'email_password')
            ->update(['secret2' => $hashedPassword]);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to update password'
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
        $student = $studentModel->select('students.*, sections.section_name, users.email as user_email')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->find($studentId);
            
        // Use student email if available, otherwise use user email
        if ($student) {
            $student['email'] = $student['email'] ?? $student['user_email'] ?? '';
        }
            
        // Debug: Log the student data to check email
        log_message('debug', 'Student details for ID ' . $studentId . ': ' . json_encode($student));

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
     * Get pending applications count (API)
     */
    public function getPendingCount()
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $count = $studentModel->where('enrollment_status', 'pending')->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }

    /**
     * Show pending applications for approval
     */
    public function pending()
    {
        $studentModel = model(StudentModel::class);
        
        $pendingStudents = $studentModel->select('students.*, users.email')
                                       ->join('users', 'users.id = students.user_id', 'left')
                                       ->where('students.enrollment_status', 'pending')
                                       ->orderBy('students.created_at', 'DESC')
                                       ->findAll();
        
        return view('admin/students_pending', [
            'title' => 'Pending Applications - LPHS SMS',
            'pendingStudents' => $pendingStudents
        ]);
    }
    
    /**
     * Show email setup instructions
     */
    public function emailInstructions()
    {
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }
        
        return view('admin/email_instructions');
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

            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'permit_empty|valid_email|is_unique[users.email]',
            'grade_level' => 'required|in_list[7,8,9,10]',
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

        $userId = null;
        
        // Create user account only if email is provided
        if ($this->request->getPost('email')) {
            $userData = [
                'email' => $this->request->getPost('email'),
                'password' => 'student123', // Default password
                'active' => 1
            ];

            $user = $userModel->save($userData);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'error' => 'Failed to create user account.']);
            }

            $userId = $userModel->getInsertID();

            // Add user to student group
            $userEntity = $userModel->find($userId);
            $userEntity->addGroup('student');
        }

        // Auto-generate LRN
        $lastStudent = $studentModel->select('lrn')
            ->orderBy('lrn', 'DESC')
            ->first();
        
        if ($lastStudent && is_numeric($lastStudent['lrn'])) {
            $nextNumber = intval($lastStudent['lrn']) + 1;
        } else {
            $nextNumber = 100000000001; // Start with 12-digit LRN
        }
        
        $lrn = (string)$nextNumber;

        // Create student record
        $studentData = [
            'user_id' => $userId,
            'lrn' => $lrn,
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
            return $this->response->setJSON(['success' => true, 'message' => 'Student enrolled successfully.']);
        } else {
            // If student creation fails, delete the user account
            if ($userId) {
                $userModel->delete($userId);
            }
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to create student record.']);
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
            'lrn' => "required|is_unique[students.lrn,id,{$studentId}]",
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
            'lrn' => $this->request->getPost('lrn'),
            'student_type' => $this->request->getPost('student_type'),
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
     * Approve student enrollment and send email verification
     */
    public function approve($studentId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $userModel = model(UserModel::class);
        
        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Update student status to approved
        $studentModel->update($studentId, ['enrollment_status' => 'approved']);
        
        // Get the student's registration password from the students table
        $tempPassword = $student['temp_password'] ?? 'Demo123!'; // Default fallback
        
        // Update user account to active
        if ($student['user_id']) {
            $userModel->update($student['user_id'], ['active' => 1]);
            
            // Set the password properly in auth_identities table
            $db = \Config\Database::connect();
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
            
            // Update or insert auth identity with correct password
            $existingIdentity = $db->table('auth_identities')
                ->where('user_id', $student['user_id'])
                ->where('type', 'email_password')
                ->get()
                ->getRow();
                
            if ($existingIdentity) {
                $db->table('auth_identities')
                    ->where('user_id', $student['user_id'])
                    ->where('type', 'email_password')
                    ->update(['secret' => $hashedPassword]);
            } else {
                // Only insert if we have a valid email
                if ($studentEmail) {
                    $db->table('auth_identities')->insert([
                        'user_id' => $student['user_id'],
                        'type' => 'email_password',
                        'name' => $studentEmail,
                        'secret' => $hashedPassword,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        // Get student email from users table or student record
        $userModel = model(UserModel::class);
        $user = $userModel->find($student['user_id']);
        $studentEmail = null;
        
        // Debug logging
        log_message('debug', 'Student approval - User ID: ' . $student['user_id']);
        log_message('debug', 'Student approval - User object: ' . json_encode($user));
        log_message('debug', 'Student approval - Student email field: ' . ($student['email'] ?? 'null'));
        
        // Try to get email from user record first
        if ($user && isset($user->email) && !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $studentEmail = $user->email;
            log_message('debug', 'Using user email: ' . $studentEmail);
        }
        // Fallback to student record email if it exists and is valid
        elseif (isset($student['email']) && !empty($student['email']) && filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
            $studentEmail = $student['email'];
            log_message('debug', 'Using student email: ' . $studentEmail);
        } else {
            log_message('debug', 'No valid email found for student');
        }
        
        // If still no email, skip email sending
        if (!$studentEmail) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student approved successfully. No email address found - please provide login credentials manually.',
                'credentials' => [
                    'lrn' => $student['lrn'],
                    'password' => $tempPassword
                ]
            ]);
        }
        
        // This check is now handled above
        
        // Send email verification
        $emailService = new SupabaseEmailService();
        $studentName = $student['first_name'] . ' ' . $student['last_name'];
        
        $emailSent = $emailService->sendVerificationEmail(
            $studentEmail, 
            $studentName, 
            $student['lrn'],
            $tempPassword
        );
        
        // Always return success since approval worked, just note email status
        $message = 'Student approved successfully.';
        if ($emailSent) {
            $message .= ' Verification email sent with login credentials.';
        } else {
            $message .= ' Email sending failed. Please manually provide login credentials: LRN: ' . $student['lrn'] . ', Password: ' . $tempPassword;
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'credentials' => [
                'lrn' => $student['lrn'],
                'email' => $studentEmail,
                'password' => $tempPassword
            ]
        ]);
    }

    /**
     * Reject student enrollment
     */
    public function reject($studentId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        
        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Update student status to rejected
        $studentModel->update($studentId, ['enrollment_status' => 'rejected']);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Student application rejected.'
        ]);
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