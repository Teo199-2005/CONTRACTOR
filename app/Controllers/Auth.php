<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\EnrollmentDocumentModel;

class Auth extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function login()
    {
        // If user is already logged in, redirect to dashboard
        try {
            if ($this->auth->loggedIn()) {
                return redirect()->to($this->getDashboardUrl());
            }
        } catch (\Throwable $e) {
            // Database may not be configured yet; continue to show login form
        }

        // Use modern login page
        return view('auth/login', [
            'title' => 'Login - LPHS SMS'
        ]);
    }

    public function attempt()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $credentials = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        $remember = (bool) $this->request->getPost('remember');

        $result = $this->auth->attempt($credentials, $remember);

        if ($result->isOK()) {
            return redirect()->to($this->getDashboardUrl());
        }

        // Manual fallback: verify against Shield identities and perform session login
        try {
            /** @var \CodeIgniter\Shield\Models\UserIdentityModel $identityModel */
            $identityModel = model(\CodeIgniter\Shield\Models\UserIdentityModel::class);
            $identity = $identityModel
                ->where('type', 'email_password')
                ->where('secret', $credentials['email'])
                ->first();

            if (! $identity) {
                return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
            }

            $passwords = service('passwords');
            if (! $passwords->verify($credentials['password'], $identity->secret2)) {
                return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
            }

            /** @var \CodeIgniter\Shield\Models\UserModel $userModel */
            $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);
            $user = $userModel->find($identity->user_id);
            if (! $user) {
                return redirect()->back()->withInput()->with('error', 'Account not found.');
            }

            // Ensure active
            if ((int) ($user->active ?? 0) !== 1) {
                $user->active = 1;
                $userModel->save($user);
            }

            // Perform session login
            $sessionAuth = service('auth')->getAuthenticator('session');
            $sessionAuth->login($user);

            return redirect()->to($this->getDashboardUrl());
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Authentication system error.');
        }
    }

    public function register()
    {
        return view('auth/register', [
            'title' => 'Student Registration - LPHS SMS'
        ]);
    }

    public function forgot()
    {
        // Delegate to Shield magic link if enabled, otherwise show informational page
        if (setting('Auth.allowMagicLinkLogins')) {
            return redirect()->to(url_to('magic-link'));
        }
        return redirect()->to(base_url('login'))
            ->with('error', 'Password recovery is not enabled. Please contact the administrator.');
    }

    /**
     * Redirect logged-in user to the correct dashboard
     */
    public function dashboard()
    {
        try {
            if ($this->auth->loggedIn()) {
                return redirect()->to($this->getDashboardUrl());
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return redirect()->to(base_url('login'));
    }

    /**
     * Demo quick-login by role
     * WARNING: For demo/development use only. Remove in production.
     */
    public function demo(string $role)
    {
        $role = strtolower($role);
        $map = [
            'admin' => 'demo.admin@lphs.edu',
            'teacher' => 'demo.teacher@lphs.edu',
            'student' => 'demo.student@lphs.edu',
            'parent' => 'demo.parent@lphs.edu',
            'newstudent' => 'new.student@lphs.edu', // New approved student account
        ];
        if (! isset($map[$role])) {
            return redirect()->to(base_url('login'))->with('error', 'Unknown demo role.');
        }

        $email = $map[$role];

        try {
            /** @var \CodeIgniter\Shield\Models\UserIdentityModel $identityModel */
            $identityModel = model(\CodeIgniter\Shield\Models\UserIdentityModel::class);
            /** @var \CodeIgniter\Shield\Models\UserModel $userModel */
            $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);

            // Find existing identity
            $identity = $identityModel
                ->where('type', 'email_password')
                ->where('secret', $email)
                ->first();

            if (! $identity) {
                // Create user + identity via Shield User entity to ensure identity creation
                $userEntity = new \CodeIgniter\Shield\Entities\User([
                    'email' => $email,
                    'password' => 'DemoPass123!',
                    'active' => 1,
                ]);
                $userModel->save($userEntity);
                $identity = $identityModel
                    ->where('type', 'email_password')
                    ->where('secret', $email)
                    ->first();
                if (! $identity) {
                    throw new \RuntimeException('Failed to create demo identity');
                }
            }

            // Ensure password hash, active flag, and group link
            if ($identity) {
                $identity->secret2 = service('passwords')->hash('DemoPass123!');
                $identityModel->save($identity);

                $user = $userModel->find($identity->user_id);
                if ($user) {
                    if ((int) ($user->active ?? 0) !== 1) {
                        $user->active = 1;
                        $userModel->save($user);
                    }
                    $db = \Config\Database::connect();

                    // Determine the actual group for newstudent role
                    $actualGroup = ($role === 'newstudent') ? 'student' : $role;

                    $db->table('auth_groups_users')->ignore(true)->insert([
                        'user_id' => $user->id,
                        'group'   => $actualGroup,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    // Create student record for newstudent demo account
                    if ($role === 'newstudent') {
                        $studentModel = new \App\Models\StudentModel();
                        $existingStudent = $studentModel->where('user_id', $user->id)->first();

                        if (!$existingStudent) {
                            $studentModel->insert([
                                'user_id' => $user->id,
                                'first_name' => 'John',
                                'last_name' => 'Doe',
                                'gender' => 'Male',
                                'date_of_birth' => '2009-05-15',
                                'email' => 'new.student@lphs.edu',
                                'enrollment_status' => 'approved', // Already approved by admin
                                'grade_level' => 8,
                                'school_year' => '2024-2025',
                                'student_id' => $studentModel->createUniqueStudentId(),
                                'address' => '123 Main Street, City',
                                'contact_number' => '09123456789',
                                'emergency_contact_name' => 'Jane Doe',
                                'emergency_contact_number' => '09987654321',
                                'emergency_contact_relationship' => 'Mother',
                            ]);
                        }
                    }

                    // Log in via session authenticator and go to dashboard
                    $sessionAuth = service('auth')->getAuthenticator('session');
                    $sessionAuth->login($user);
                    return redirect()->to(base_url('dashboard'));
                }
            }
        } catch (\Throwable $e) {
            // As a last resort, run seeder then redirect back to demo to try again
            try {
                $seeder = \Config\Database::seeder();
                $seeder->call('DemoAccountsSeeder');
            } catch (\Throwable $e2) {}
            return redirect()->to(base_url('login'))->with('error', 'Demo login failed. Please click Demo Login again.');
        }

        return redirect()->to(base_url('login'))->with('error', 'Demo login failed. Please click Demo Login again.');
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'grade_level' => 'required|integer|greater_than[6]|less_than[11]',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
            // Files are optional; basic validation is applied during handling
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $studentModel = new StudentModel();

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user account using Shield User entity
            $userEntity = new \CodeIgniter\Shield\Entities\User([
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'active' => 0 // Inactive until admin approval
            ]);

            $userModel->save($userEntity);
            $userId = $userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Assign student role
            $db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Create student record
            $studentData = [
                'user_id' => $userId,
                'first_name' => $this->request->getPost('first_name'),
                'middle_name' => $this->request->getPost('middle_name'),
                'last_name' => $this->request->getPost('last_name'),
                'suffix' => $this->request->getPost('suffix'),
                'gender' => $this->request->getPost('gender'),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'place_of_birth' => $this->request->getPost('place_of_birth'),
                'nationality' => $this->request->getPost('nationality') ?: 'Filipino',
                'religion' => $this->request->getPost('religion'),
                'contact_number' => $this->request->getPost('contact_number'),
                'email' => $this->request->getPost('email'),
                'address' => $this->request->getPost('address'),
                'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
                'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
                'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship'),
                'enrollment_status' => 'pending',
                'grade_level' => $this->request->getPost('grade_level'),
                'school_year' => '2024-2025'
            ];

            $studentId = $studentModel->insert($studentData);

            if (!$studentId) {
                throw new \Exception('Failed to create student record');
            }

            // Handle optional enrollment document uploads
            $this->handleEnrollmentUploads((int) $studentId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to(base_url('login'))
                ->with('success', 'Registration submitted successfully! Please wait for admin approval.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function logout(): ResponseInterface
    {
        $this->auth->logout();
        return redirect()->to(base_url('/'));
    }

    /**
     * Debug authentication state
     */
    public function debugAuth()
    {
        $data = [
            'logged_in' => $this->auth->loggedIn(),
            'user_id' => $this->auth->id(),
            'user' => null,
            'groups' => [],
            'session_data' => session()->get()
        ];
        
        if ($this->auth->loggedIn()) {
            try {
                $user = $this->auth->user();
                $data['user'] = [
                    'id' => $user->id,
                    'email' => $user->email,
                    'active' => $user->active ?? 'unknown'
                ];
                $data['groups'] = $user->getGroups();
            } catch (\Throwable $e) {
                $data['error'] = $e->getMessage();
            }
        }
        
        return $this->response->setJSON($data);
    }

    /**
     * Simple test method without authentication
     */
    public function testSimple()
    {
        return $this->response->setJSON([
            'message' => 'Simple test route working',
            'timestamp' => date('Y-m-d H:i:s'),
            'base_url' => base_url()
        ]);
    }

    /**
     * Test the auth service directly
     */
    public function testAuthService()
    {
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
            'auth_service_exists' => service('auth') ? 'Yes' : 'No',
        ];

        try {
            $auth = service('auth');
            $data['auth_class'] = get_class($auth);
            $data['logged_in'] = $auth->loggedIn();
            
            if ($data['logged_in']) {
                $user = $auth->user();
                $data['user_id'] = $user->id ?? 'Unknown';
                $data['user_email'] = $user->email ?? 'Unknown';
            }
        } catch (\Throwable $e) {
            $data['error'] = $e->getMessage();
            $data['error_trace'] = $e->getTraceAsString();
        }

        return $this->response->setJSON($data);
    }

    /**
     * Get dashboard URL based on user role
     */
    private function getDashboardUrl(): string
    {
        // Check if user is logged in before accessing user data
        if (!$this->auth->loggedIn()) {
            return base_url('login');
        }

        try {
            $user = $this->auth->user();

            if ($user->inGroup('admin')) {
                return base_url('admin/dashboard');
            } elseif ($user->inGroup('teacher')) {
                return base_url('teacher/dashboard');
            } elseif ($user->inGroup('student')) {
                // Check if student is approved before allowing access
                $studentModel = new \App\Models\StudentModel();
                $student = $studentModel->where('user_id', $user->id)->first();

                if (!$student) {
                    // Student record not found - logout and redirect to login with error
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Student record not found. Please contact the administration.');
                    return base_url('login');
                }

                // Check enrollment status
                if ($student['enrollment_status'] === 'pending') {
                    // Student is pending approval - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your enrollment is still pending approval. Please wait for admin approval before accessing the system.');
                    return base_url('login');
                } elseif ($student['enrollment_status'] === 'rejected') {
                    // Student was rejected - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your enrollment application has been rejected. Please contact the administration for more information.');
                    return base_url('login');
                } elseif ($student['enrollment_status'] !== 'approved' && $student['enrollment_status'] !== 'enrolled') {
                    // Student has invalid status - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your account status is invalid. Please contact the administration.');
                    return base_url('login');
                }

                // Student is approved or enrolled - allow access
                return base_url('student/dashboard');
            } elseif ($user->inGroup('parent')) {
                return base_url('parent/dashboard');
            }
        } catch (\Throwable $e) {
            // If there's any error getting user data, redirect to login
            return base_url('login');
        }

        return base_url('/');
    }

    /**
     * Save enrollment documents if provided during registration.
     */
    private function handleEnrollmentUploads(int $studentId): void
    {
        $documentFields = [
            'birth_certificate' => 'birth_certificate',
            'report_card' => 'report_card',
            'good_moral' => 'good_moral',
            'medical_certificate' => 'medical_certificate',
            'photo' => 'photo',
        ];

        $uploadBase = WRITEPATH . 'uploads/enrollment/' . $studentId;
        if (!is_dir($uploadBase)) {
            @mkdir($uploadBase, 0775, true);
        }

        $docModel = new EnrollmentDocumentModel();

        foreach ($documentFields as $fieldName => $type) {
            $file = $this->request->getFile($fieldName);
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                continue;
            }

            // Basic whitelist check
            $mime = $file->getMimeType();
            $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($mime, $allowed, true)) {
                // Skip unsupported files silently; could also collect errors
                continue;
            }

            $newName = $type . '_' . time() . '_' . $file->getRandomName();
            $file->move($uploadBase, $newName);

            $relativePath = 'uploads/enrollment/' . $studentId . '/' . $newName;

            $docModel->insert([
                'student_id' => $studentId,
                'document_type' => $type,
                'document_name' => $file->getClientName(),
                'file_path' => $relativePath,
                'file_size' => $file->getSize(),
                'mime_type' => $mime,
                'is_verified' => 0,
            ]);
        }
    }
}

