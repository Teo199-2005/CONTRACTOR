<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\PasswordResetRequestModel;

class PasswordReset extends BaseController
{
    protected $userModel;
    protected $resetRequestModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->resetRequestModel = model(PasswordResetRequestModel::class);
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    /**
     * Verify identity with PRC license or LRN
     */
    public function verifyIdentity()
    {
        $rules = [
            'identifier' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/forgot_password', [
                'validation' => $this->validator
            ]);
        }

        $identifier = $this->request->getPost('identifier');
        
        // Check if it's a teacher (PRC license) or student (LRN)
        $teacherModel = model('TeacherModel');
        $studentModel = model('StudentModel');
        
        $teacher = $teacherModel->where('license_number', $identifier)->first();
        $student = $studentModel->where('lrn', $identifier)->first();
        
        if ($teacher) {
            $user = $this->userModel->find($teacher['user_id']);
            if ($user) {
                session()->set('reset_user_id', $user->id);
                session()->set('reset_user_type', 'teacher');
                return view('auth/reset_password_form', ['user_type' => 'teacher']);
            }
        } elseif ($student) {
            $user = $this->userModel->find($student['user_id']);
            if ($user) {
                session()->set('reset_user_id', $user->id);
                session()->set('reset_user_type', 'student');
                return view('auth/reset_password_form', ['user_type' => 'student']);
            }
        }
        
        return redirect()->back()
            ->with('error', 'No account found with that PRC license number or LRN.');
    }

    /**
     * Debug endpoint to check users (remove in production)
     */
    public function debugUsers()
    {
        if (ENVIRONMENT !== 'development') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        
        $users = $this->userModel->select('id, email, active, created_at')->findAll();
        
        echo '<h3>Users in system:</h3>';
        echo '<table border="1" style="border-collapse: collapse; padding: 5px;">';
        echo '<tr><th>ID</th><th>Email</th><th>Active</th><th>Created</th></tr>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . $user['id'] . '</td>';
            echo '<td>' . $user['email'] . '</td>';
            echo '<td>' . ($user['active'] ? 'Yes' : 'No') . '</td>';
            echo '<td>' . $user['created_at'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        // Also check students table
        $studentModel = model('StudentModel');
        $students = $studentModel->select('id, email, first_name, last_name, enrollment_status')->findAll();
        
        echo '<h3>Students in system:</h3>';
        echo '<table border="1" style="border-collapse: collapse; padding: 5px;">';
        echo '<tr><th>ID</th><th>Email</th><th>Name</th><th>Status</th></tr>';
        foreach ($students as $student) {
            echo '<tr>';
            echo '<td>' . $student['id'] . '</td>';
            echo '<td>' . ($student['email'] ?? 'N/A') . '</td>';
            echo '<td>' . $student['first_name'] . ' ' . $student['last_name'] . '</td>';
            echo '<td>' . $student['enrollment_status'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Handle password change form submission
     */
    public function changePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            $userType = session()->get('reset_user_type');
            return view('auth/reset_password_form', [
                'user_type' => $userType,
                'validation' => $this->validator
            ]);
        }

        $userId = session()->get('reset_user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        if (!$userId) {
            return redirect()->to('forgot-password')
                ->with('error', 'Session expired. Please start over.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('forgot-password')
                ->with('error', 'User not found.');
        }

        // Verify current password using Shield's authentication
        $db = \Config\Database::connect();
        $identity = $db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRow();
            
        if (!$identity || !password_verify($currentPassword, $identity->secret)) {
            $userType = session()->get('reset_user_type');
            return view('auth/reset_password_form', [
                'user_type' => $userType,
                'error' => 'Current password is incorrect.'
            ]);
        }

        // Update password in auth_identities table
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->update(['secret' => $hashedPassword]);
            
        if ($updated) {
            session()->remove(['reset_user_id', 'reset_user_type']);
            return redirect()->to('login')
                ->with('success', 'Password updated successfully. You can now login with your new password.');
        }

        return redirect()->back()
            ->with('error', 'Failed to update password. Please try again.');
    }

    /**
     * Show reset password form (when user clicks email link)
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('login')
                ->with('error', 'Invalid reset token.');
        }

        // Find reset request
        $resetRequest = $this->resetRequestModel
            ->where('token', $token)
            ->where('status', 'approved')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (!$resetRequest) {
            return redirect()->to('login')
                ->with('error', 'Invalid or expired reset token.');
        }

        return view('auth/reset_password', [
            'token' => $token,
            'email' => $resetRequest['email']
        ]);
    }

    /**
     * Handle password reset form submission
     */
    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            $token = $this->request->getPost('token');
            return view('auth/reset_password', [
                'token' => $token,
                'validation' => $this->validator
            ]);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Find reset request
        $resetRequest = $this->resetRequestModel
            ->where('token', $token)
            ->where('status', 'approved')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (!$resetRequest) {
            return redirect()->to('login')
                ->with('error', 'Invalid or expired reset token.');
        }

        // Update user password
        $user = $this->userModel->find($resetRequest['user_id']);
        if ($user) {
            $user->password = $password;
            $this->userModel->save($user);

            // Mark reset request as used
            $this->resetRequestModel->update($resetRequest['id'], [
                'status' => 'used',
                'used_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('login')
                ->with('success', 'Password updated successfully. You can now login with your new password.');
        }

        return redirect()->to('login')
            ->with('error', 'Failed to update password. Please try again.');
    }
}
