<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use CodeIgniter\Shield\Models\UserModel;

class Profile extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $studentModel = new StudentModel();
        $student = $studentModel->where('lrn', 'DEMO-STUDENT-001')->first();

        if (!$student) {
            return redirect()->to(base_url('student/dashboard'))->with('error', 'Student profile not found.');
        }

        return view('student/profile', [
            'title' => 'My Profile - LPHS SMS',
            'student' => $student
        ]);
    }

    public function update()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'first_name' => 'required|max_length[100]',
            'middle_name' => 'max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'max_length[20]',
            'address' => 'max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel = new StudentModel();
        $student = $studentModel->where('lrn', 'DEMO-STUDENT-001')->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];

        if ($studentModel->update($student['id'], $data)) {
            return redirect()->back()->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update profile.');
        }
    }

    public function changePassword()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        
        // Get current user
        $user = $this->auth->user();
        
        // Verify current password using Shield's method
        $db = \Config\Database::connect();
        $identity = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->get()
            ->getRow();
            
        if (!$identity || !password_verify($currentPassword, $identity->secret)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password in auth_identities table
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->update(['secret' => $hashedPassword]);

        if ($result) {
            return redirect()->back()->with('success', 'Password changed successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to change password.');
        }
    }
}