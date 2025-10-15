<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
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

        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->find(11); // Demo Teacher

        if (!$teacher) {
            return redirect()->to(base_url('teacher/dashboard'))->with('error', 'Teacher profile not found.');
        }

        return view('teacher/profile', [
            'title' => 'My Profile - LPHS SMS',
            'teacher' => $teacher
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

        $teacherModel = new TeacherModel();
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];

        if ($teacherModel->update(11, $data)) {
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

        $db = \Config\Database::connect();
        $userId = $this->auth->id();
        
        // Get current password hash from auth_identities
        $identity = $db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRow();
            
        if (!$identity) {
            return redirect()->back()->with('error', 'User authentication record not found.');
        }

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $identity->secret)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password hash
        $newPasswordHash = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        $updated = $db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->update([
                'secret' => $newPasswordHash,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
        if ($updated) {
            return redirect()->back()->with('success', 'Password changed successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to change password.');
        }
    }
}