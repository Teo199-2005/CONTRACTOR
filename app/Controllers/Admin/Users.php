<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;
use App\Models\StudentModel;
use App\Models\TeacherModel;

class Users extends BaseController
{
    protected $auth;
    protected $userModel;

    public function __construct()
    {
        $this->auth = auth();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $users = $this->userModel->orderBy('id', 'DESC')->findAll(50);
        
        // Get student section information for each user
        $studentModel = new StudentModel();
        $usersWithSections = [];
        
        foreach ($users as $user) {
            $userData = (array) $user;
            $userData['section_assigned'] = false;
            $userData['section_name'] = null;
            $userData['user_role'] = 'No Role';
            
            // Get user role
            $groups = $user->getGroups();
            if (!empty($groups)) {
                $userData['user_role'] = $groups[0];
            }
            
            // Check if user is a student and has section assigned
            if ($user->inGroup('student')) {
                $student = $studentModel->where('user_id', $user->id)->first();
                if ($student && !empty($student['section_id'])) {
                    $userData['section_assigned'] = true;
                    // Get section name
                    $sectionModel = new \App\Models\SectionModel();
                    $section = $sectionModel->find($student['section_id']);
                    $userData['section_name'] = $section ? $section['section_name'] : null;
                }
            }
            
            $usersWithSections[] = $userData;
        }
        
        return view('admin/users', [
            'title' => 'Manage Users - LPHS SMS',
            'users' => $usersWithSections,
        ]);
    }

    public function create()
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $role = $this->request->getPost('role');
            
            $rules = [
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]',
                'role' => 'required|in_list[admin,teacher,student,parent]'
            ];

            if (! $this->validate($rules)) {
                return view('admin/users_create', [
                    'title' => 'Create User - LPHS SMS',
                    'validation' => $this->validator
                ]);
            }

            if (empty($email) || empty($password)) {
                return view('admin/users_create', [
                    'title' => 'Create User - LPHS SMS',
                    'error' => 'Email and password are required.'
                ]);
            }

            $user = new User([
                'email' => $email,
                'password' => $password
            ]);

            if ($this->userModel->save($user)) {
                $userId = $this->userModel->getInsertID();
                $user = $this->userModel->findById($userId);
                $user->addToGroup($role);
                
                // Create role-specific records
                if ($role === 'student') {
                    $this->createStudentRecord($userId);
                } elseif ($role === 'teacher') {
                    $this->createTeacherRecord($userId);
                }
                
                return redirect()->to(base_url('admin/users'))->with('success', 'User created successfully.');
            } else {
                return view('admin/users_create', [
                    'title' => 'Create User - LPHS SMS',
                    'error' => 'Failed to create user.'
                ]);
            }
        }

        return view('admin/users_create', [
            'title' => 'Create User - LPHS SMS'
        ]);
    }

    public function edit($id)
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'role' => 'required|in_list[admin,teacher,student,parent]'
            ];

            if ($this->request->getPost('password')) {
                $rules['password'] = 'min_length[8]';
            }

            if (! $this->validate($rules)) {
                return view('admin/users_edit', [
                    'title' => 'Edit User - LPHS SMS',
                    'user' => $user,
                    'validation' => $this->validator
                ]);
            }

            $updateData = [];
            
            // Only update email if it's different
            if ($this->request->getPost('email') !== $user->email) {
                $updateData['email'] = $this->request->getPost('email');
            }
            
            // Only update password if provided
            if ($this->request->getPost('password')) {
                $updateData['password'] = $this->request->getPost('password');
            }

            // Only update if there's data to update
            if (!empty($updateData)) {
                $this->userModel->update($id, $updateData);
            }
            
            // Update role
            $user->syncGroups($this->request->getPost('role'));

            return redirect()->to(base_url('admin/users'))->with('success', 'User updated successfully.');
        }

        return view('admin/users_edit', [
            'title' => 'Edit User - LPHS SMS',
            'user' => $user
        ]);
    }

    public function delete($id)
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        // Prevent admin from deleting themselves
        if ($user->id === $this->auth->user()->id) {
            return redirect()->to(base_url('admin/users'))->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);
        return redirect()->to(base_url('admin/users'))->with('success', 'User deleted successfully.');
    }

    public function bulkDelete()
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = json_decode($this->request->getBody(), true);
        $selectedUsers = $input['selected_users'] ?? [];
        
        if (empty($selectedUsers)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No users selected.']);
        }

        $currentUserId = $this->auth->user()->id;
        $deletedCount = 0;

        foreach ($selectedUsers as $userId) {
            if ($userId != $currentUserId) {
                $this->userModel->delete($userId);
                $deletedCount++;
            }
        }

        $message = $deletedCount > 0 ? "Successfully deleted {$deletedCount} user(s)." : 'No users were deleted.';
        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }
    
    private function createStudentRecord($userId)
    {
        $studentModel = new StudentModel();
        $data = [
            'user_id' => $userId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'grade_level' => $this->request->getPost('grade_level'),
            'lrn' => $this->request->getPost('lrn'),
            'email' => $this->request->getPost('email'),
            'enrollment_status' => 'pending',
            'school_year' => date('Y') . '-' . (date('Y') + 1)
        ];
        
        return $studentModel->insert($data);
    }
    
    private function createTeacherRecord($userId)
    {
        $teacherModel = new TeacherModel();
        $data = [
            'user_id' => $userId,
            'first_name' => $this->request->getPost('teacher_first_name'),
            'last_name' => $this->request->getPost('teacher_last_name'),
            'employee_id' => $this->request->getPost('employee_id'),
            'department' => $this->request->getPost('department'),
            'email' => $this->request->getPost('email'),
            'employment_status' => 'active'
        ];
        
        return $teacherModel->insert($data);
    }
}




