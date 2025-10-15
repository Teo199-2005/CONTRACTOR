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
            
        // Ensure email is properly set - prioritize students table email
        if ($student) {
            $student['email'] = !empty($student['email']) ? $student['email'] : ($student['user_email'] ?? '');
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
            
        // Ensure email is properly set - prioritize students table email
        if ($student) {
            $student['email'] = !empty($student['email']) ? $student['email'] : ($student['user_email'] ?? '');
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
        
        // Get next year applications
        $db = \Config\Database::connect();
        $nextYearApplications = $db->table('next_year_applications nya')
            ->select('nya.*, s.first_name, s.last_name, s.lrn')
            ->join('students s', 's.id = nya.student_id')
            ->where('nya.status', 'pending')
            ->orderBy('nya.applied_at', 'DESC')
            ->get()
            ->getResultArray();
        
        return view('admin/students_pending', [
            'title' => 'Pending Applications - LPHS SMS',
            'pendingStudents' => $pendingStudents,
            'nextYearApplications' => $nextYearApplications
        ]);
    }

    /**
     * Show history of processed applications
     */
    public function pendingHistory()
    {
        $studentModel = model(StudentModel::class);
        
        // Pagination setup
        $perPage = 15;
        $currentPage = $this->request->getGet('page') ?? 1;
        $offset = ($currentPage - 1) * $perPage;
        
        // Get total count
        $totalRecords = $studentModel->whereIn('students.enrollment_status', ['enrolled', 'rejected'])
                                   ->countAllResults();
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get paginated results
        $processedStudents = $studentModel->select('students.*, users.email')
                                         ->join('users', 'users.id = students.user_id', 'left')
                                         ->whereIn('students.enrollment_status', ['enrolled', 'rejected'])
                                         ->orderBy('students.updated_at', 'DESC')
                                         ->limit($perPage, $offset)
                                         ->findAll();
        
        return view('admin/students_pending_history', [
            'title' => 'Application History - LPHS SMS',
            'processedStudents' => $processedStudents,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
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
     * Show enroll student page
     */
    public function enroll()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $sectionModel = model(SectionModel::class);
        $sections = $sectionModel->findAll();

        return view('admin/students_enroll', [
            'title' => 'Enroll New Student - LPHS SMS',
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
            'middle_name' => 'permit_empty|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'suffix' => 'permit_empty|max_length[10]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'lrn' => 'permit_empty|max_length[20]',
            'grade_level' => 'required|in_list[7,8,9,10,11,12]',
            'student_type' => 'required|in_list[New Student,Transferee]',
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
            'emergency_contact_relationship' => 'permit_empty|max_length[50]',
            'birth_certificate' => 'permit_empty|uploaded[birth_certificate]|max_size[birth_certificate,5120]',
            'report_card' => 'permit_empty|uploaded[report_card]|max_size[report_card,5120]',
            'good_moral' => 'permit_empty|uploaded[good_moral]|max_size[good_moral,5120]',
            'photo' => 'permit_empty|uploaded[photo]|max_size[photo,2048]|is_image[photo]'
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
        
        // Check if user with this email already exists
        $existingUser = $userModel->where('email', $this->request->getPost('email'))->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'A user with this email already exists.');
        }

        // Create user account manually to ensure proper password handling
        $db = \Config\Database::connect();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        // Insert user record
        $userData = [
            'email' => $email,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $db->table('users')->insert($userData);
        $userId = $db->insertID();
        
        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user account.');
        }
        
        // Insert auth_identities record with proper password hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $db->table('auth_identities')->insert([
            'user_id' => $userId,
            'type' => 'email_password',
            'name' => '',
            'secret' => $email,
            'secret2' => $hashedPassword,
            'expires' => null,
            'extra' => null,
            'force_reset' => 0,
            'last_used_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Add user to student group
        $db->table('auth_groups_users')->insert([
            'user_id' => $userId,
            'group' => 'student',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Handle LRN - use provided or auto-generate
        $lrn = $this->request->getPost('lrn');
        if (empty($lrn)) {
            $lastStudent = $studentModel->select('lrn')
                ->orderBy('lrn', 'DESC')
                ->first();
            
            if ($lastStudent && is_numeric($lastStudent['lrn'])) {
                $nextNumber = intval($lastStudent['lrn']) + 1;
            } else {
                $nextNumber = 100000000001; // Start with 12-digit LRN
            }
            
            $lrn = (string)$nextNumber;
        }

        // Create student record (like registration process)
        $studentData = [
            'user_id' => $userId,
            'lrn' => $lrn,
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'suffix' => $this->request->getPost('suffix'),
            'student_type' => $this->request->getPost('student_type'),
            'grade_level' => $this->request->getPost('grade_level'),
            'section_id' => $this->request->getPost('section_id') ?: null,
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'place_of_birth' => $this->request->getPost('place_of_birth'),
            'nationality' => $this->request->getPost('nationality') ?: 'Filipino',
            'religion' => $this->request->getPost('religion'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email' => $this->request->getPost('email'), // Store email in student record too
            'address' => $this->request->getPost('address'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
            'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship'),
            'enrollment_status' => 'enrolled', // Direct enrollment by admin
            'school_year' => '2024-2025',
            'temp_password' => $this->request->getPost('password') // Store password for email
        ];

        if ($studentModel->save($studentData)) {
            $studentId = $studentModel->getInsertID();
            
            // Handle document uploads
            $documentModel = model(EnrollmentDocumentModel::class);
            $documentTypes = ['birth_certificate', 'report_card', 'good_moral', 'photo'];
            
            foreach ($documentTypes as $docType) {
                $file = $this->request->getFile($docType);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $uploadPath = FCPATH . 'uploads/enrollment_documents/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    if ($file->move($uploadPath, $newName)) {
                        $documentModel->save([
                            'student_id' => $studentId,
                            'document_type' => $docType,
                            'document_name' => $file->getClientName(),
                            'file_path' => $newName,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getClientMimeType(),
                            'is_verified' => false
                        ]);
                    }
                }
            }
            
            // Send enrollment email with login credentials
            try {
                $emailService = new SupabaseEmailService();
                $studentName = $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name');
                $studentEmail = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                $emailService->sendVerificationEmail(
                    $studentEmail,
                    $studentName,
                    $lrn,
                    $password
                );
            } catch (\Exception $e) {
                log_message('error', 'Failed to send enrollment email: ' . $e->getMessage());
            }
            
            return redirect()->to('admin/students')->with('success', 'Student enrolled successfully.');
        } else {
            // If student creation fails, delete the user account
            $userModel->delete($userId);
            return redirect()->back()->withInput()->with('error', 'Failed to create student record.');
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

        // Update student status to enrolled
        $studentModel->update($studentId, ['enrollment_status' => 'enrolled']);
        
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
                } else {
                    // Update existing identity to set name field
                    $db->table('auth_identities')
                        ->where('user_id', $student['user_id'])
                        ->where('type', 'email_password')
                        ->update([
                            'name' => $student['email'] ?? $user->email ?? 'unknown',
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
        $userModel = model(UserModel::class);
        
        $student = $studentModel->find($studentId);
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        // Update student status to rejected
        $studentModel->update($studentId, ['enrollment_status' => 'rejected']);
        
        // Get student email for notification
        $user = $userModel->find($student['user_id']);
        $studentEmail = null;
        
        if ($user && isset($user->email) && !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $studentEmail = $user->email;
        } elseif (isset($student['email']) && !empty($student['email']) && filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
            $studentEmail = $student['email'];
        }
        
        // Send rejection email if email exists
        $emailSent = false;
        if ($studentEmail) {
            $emailService = new SupabaseEmailService();
            $studentName = $student['first_name'] . ' ' . $student['last_name'];
            $emailSent = $emailService->sendRejectionEmail($studentEmail, $studentName);
        }
        
        $message = 'Student application rejected.';
        if ($emailSent) {
            $message .= ' Rejection notification sent via email.';
        } elseif ($studentEmail) {
            $message .= ' Email sending failed.';
        } else {
            $message .= ' No email address found.';
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Archive student (soft delete)
     */
    public function archive($studentId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $student = $studentModel->find($studentId);
        
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Student not found']);
        }

        if ($studentModel->delete($studentId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student archived successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to archive student.'
        ]);
    }

    /**
     * Show archived students
     */
    public function archived()
    {
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = model(StudentModel::class);
        $archivedStudents = $studentModel->onlyDeleted()->findAll();

        return view('admin/students_archived', [
            'title' => 'Archived Students - LPHS SMS',
            'archivedStudents' => $archivedStudents
        ]);
    }

    /**
     * Restore archived student
     */
    public function restore($studentId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $student = $studentModel->onlyDeleted()->find($studentId);
        
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Archived student not found']);
        }

        // Restore the student by updating deleted_at to NULL
        $db = \Config\Database::connect();
        $result = $db->table('students')
            ->where('id', $studentId)
            ->update(['deleted_at' => null]);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student restored successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to restore student.'
        ]);
    }

    /**
     * View document as a separate page
     */
    public function viewDocument($filename)
    {
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('login'));
        }

        // Check if file exists
        $filePath = FCPATH . 'uploads/enrollment_documents/' . $filename;
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        return view('admin/document_view', [
            'title' => 'Document Viewer - LPHS SMS',
            'filename' => $filename,
            'fileUrl' => base_url('uploads/enrollment_documents/' . $filename)
        ]);
    }

    /**
     * Permanently delete student
     */
    public function deletePermanently($studentId)
    {
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $studentModel = model(StudentModel::class);
        $userModel = model(UserModel::class);

        $student = $studentModel->onlyDeleted()->find($studentId);
        if (!$student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Archived student not found']);
        }

        // Delete all related records permanently
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Delete auth_identities first
            if ($student['user_id']) {
                $db->table('auth_identities')->where('user_id', $student['user_id'])->delete();
                $db->table('auth_groups_users')->where('user_id', $student['user_id'])->delete();
                $userModel->delete($student['user_id'], true); // Force delete
            }
            
            // Permanently delete student record
            $studentModel->delete($studentId, true); // Force delete
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student permanently deleted.'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to permanently delete student: ' . $e->getMessage()
            ]);
        }
    }

    public function viewGrades($studentId)
    {
        $studentModel = new \App\Models\StudentModel();
        $gradeModel = new \App\Models\GradeModel();
        $subjectModel = new \App\Models\SubjectModel();
        
        $student = $studentModel->find($studentId);
        if (!$student) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Student not found');
        }
        
        $schoolYear = '2025-2026';
        $subjects = $subjectModel->getByGradeLevel($student['grade_level']);
        
        $grades = [];
        $allQuarterGrades = [];
        
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterGrades = [];
            $quarterTotal = 0;
            $quarterCount = 0;
            
            foreach ($subjects as $subject) {
                $grade = $gradeModel->where('student_id', $studentId)
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('quarter', $quarter)
                    ->first();
                
                $quarterGrades[] = [
                    'subject' => $subject,
                    'grade' => $grade
                ];
                
                if ($grade && $grade['grade']) {
                    $quarterTotal += $grade['grade'];
                    $quarterCount++;
                }
            }
            
            $grades[$quarter] = $quarterGrades;
            $allQuarterGrades[$quarter] = $quarterCount > 0 ? $quarterTotal / $quarterCount : null;
        }
        
        $gwa = $gradeModel->getFinalAverage($studentId, $schoolYear);
        
        return view('admin/student_grades', [
            'title' => 'Student Grades - ' . $student['first_name'] . ' ' . $student['last_name'],
            'student' => $student,
            'grades' => $grades,
            'allQuarterGrades' => $allQuarterGrades,
            'gwa' => $gwa,
            'schoolYear' => $schoolYear
        ]);
    }

    public function rejectApplication($applicationId)
    {
        $db = \Config\Database::connect();
        $result = $db->table('next_year_applications')
            ->where('id', $applicationId)
            ->update(['status' => 'rejected', 'updated_at' => date('Y-m-d H:i:s')]);
        
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Application rejected successfully.']);
        }
        
        return $this->response->setJSON(['success' => false, 'error' => 'Failed to reject application.']);
    }

    public function promote($studentId)
    {
        $input = $this->request->getJSON(true);
        $nextGradeLevel = $input['next_grade_level'] ?? null;
        
        if (!$nextGradeLevel) {
            return $this->response->setJSON(['success' => false, 'error' => 'Next grade level is required.']);
        }
        
        $studentModel = new \App\Models\StudentModel();
        $student = $studentModel->find($studentId);
        
        if (!$student) {
            return $this->response->setJSON(['success' => false, 'error' => 'Student not found.']);
        }
        
        try {
            $db = \Config\Database::connect();
            
            // Update student grade level and clear section assignment
            $result1 = $studentModel->update($studentId, [
                'grade_level' => $nextGradeLevel,
                'section_id' => null
            ]);
            
            // Update application status
            $result2 = $db->table('next_year_applications')
                ->where('student_id', $studentId)
                ->where('school_year', '2026-2027')
                ->update([
                    'status' => 'approved',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            if (!$result1 || !$result2) {
                return $this->response->setJSON(['success' => false, 'error' => 'Failed to update student records.']);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Student promoted to Grade {$nextGradeLevel} successfully!"
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to promote student: ' . $e->getMessage()]);
        }
    }
}