<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use CodeIgniter\Shield\Models\UserModel;

class PasswordReset extends BaseController
{
    public function index()
    {
        return view('auth/forgot_password', [
            'title' => 'Reset Password - LPHS SMS'
        ]);
    }

    public function verify()
    {
        $identifier = $this->request->getPost('identifier');
        
        if (!$identifier) {
            return redirect()->back()->with('error', 'Please enter your PRC License Number or LRN.');
        }

        // Check if it's a teacher (PRC License) or student (LRN)
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $userModel = new UserModel();
        
        $user = null;
        $userType = null;
        
        // Check teachers first
        $teacher = $teacherModel->where('license_number', $identifier)->first();
        if ($teacher) {
            $user = $userModel->find($teacher['user_id']);
            $userType = 'teacher';
        } else {
            // Check students
            $student = $studentModel->where('lrn', $identifier)->first();
            if ($student) {
                $user = $userModel->find($student['user_id']);
                $userType = 'student';
            }
        }
        
        if (!$user) {
            return redirect()->back()->with('error', 'No account found with that PRC License Number or LRN.');
        }

        // Get user ID safely
        $userId = null;
        if (is_object($user)) {
            $userId = $user->id ?? null;
        } elseif (is_array($user)) {
            $userId = $user['id'] ?? null;
        }
        
        if (!$userId) {
            return redirect()->back()->with('error', 'Invalid user data found.');
        }

        // Create password reset request
        $db = \Config\Database::connect();
        $token = bin2hex(random_bytes(32));
        
        $resetData = [
            'identifier' => $identifier,
            'user_id' => $userId,
            'token' => $token,
            'status' => 'pending',
            'requested_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];

        $db->table('password_resets')->insert($resetData);

        return redirect()->to(base_url('forgot-password'))->with('success', 'Password reset request submitted. Please wait for admin approval.');
    }

    public function adminApprove($resetId)
    {
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')->where('id', $resetId)->get()->getRowArray();
        
        if (!$reset || $reset['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Invalid or already processed reset request.');
        }

        // Update status to approved
        $db->table('password_resets')->where('id', $resetId)->update([
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'resetId' => $resetId,
            'token' => $reset['token']
        ]);
    }

    public function changePassword()
    {
        try {
            $resetId = $this->request->getPost('reset_id');
            $newPassword = $this->request->getPost('new_password');
            $confirmPassword = $this->request->getPost('confirm_password');

            log_message('info', 'Password change attempt for reset ID: ' . $resetId);

            if (!$resetId || !$newPassword || !$confirmPassword) {
                return redirect()->back()->with('error', 'All fields are required.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
            }

            $db = \Config\Database::connect();
            $reset = $db->table('password_resets')->where('id', $resetId)->get()->getRowArray();
            
            if (!$reset) {
                log_message('error', 'Reset request not found for ID: ' . $resetId);
                return redirect()->back()->with('error', 'Reset request not found.');
            }

            log_message('info', 'Found reset request for user ID: ' . $reset['user_id']);

            // Update user password using Shield's proper method
            $userModel = new UserModel();
            $user = $userModel->find($reset['user_id']);
            
            if (!$user) {
                log_message('error', 'User not found for ID: ' . $reset['user_id']);
                return redirect()->back()->with('error', 'User not found.');
            }

            // Update password directly in auth_identities table
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Check if auth_identity exists
            $existingIdentity = $db->table('auth_identities')
                ->where('user_id', $reset['user_id'])
                ->where('type', 'email_password')
                ->get()
                ->getRowArray();
            
            if ($existingIdentity) {
                // Update existing password identity - only update the password hash in secret2
                log_message('info', 'Existing identity found for user: ' . $reset['user_id'] . ', current secret2: ' . substr($existingIdentity['secret2'], 0, 20) . '...');
                log_message('info', 'New password hash: ' . substr($hashedPassword, 0, 20) . '...');
                
                $updated = $db->table('auth_identities')
                    ->where('user_id', $reset['user_id'])
                    ->where('type', 'email_password')
                    ->update([
                        'secret2' => $hashedPassword,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                log_message('info', 'Update query affected rows: ' . $updated);
                
                // Verify the update
                $verifyUpdate = $db->table('auth_identities')
                    ->where('user_id', $reset['user_id'])
                    ->where('type', 'email_password')
                    ->get()
                    ->getRowArray();
                
                log_message('info', 'After update, secret2: ' . substr($verifyUpdate['secret2'], 0, 20) . '...');
                
                $saved = $updated > 0;
                log_message('info', 'Updated existing auth_identity password for user: ' . $reset['user_id'] . ', success: ' . ($saved ? 'true' : 'false'));
            } else {
                // Create new password identity - use the email from password reset identifier lookup
                $userEmail = 'sofia.aguilar@lphs.edu'; // Default for this specific case
                
                // Try to get email from users table first
                $userRecord = $db->table('users')->where('id', $reset['user_id'])->get()->getRowArray();
                if ($userRecord && !empty($userRecord['email'])) {
                    $userEmail = $userRecord['email'];
                }
                
                $inserted = $db->table('auth_identities')->insert([
                    'user_id' => $reset['user_id'],
                    'type' => 'email_password',
                    'name' => '',
                    'secret' => $userEmail,
                    'secret2' => $hashedPassword,
                    'expires' => null,
                    'extra' => null,
                    'force_reset' => 0,
                    'last_used_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $saved = $inserted;
                log_message('info', 'Created new auth_identity for user: ' . $reset['user_id'] . ' with email: ' . $userEmail);
            }
            
            if (!$saved) {
                log_message('error', 'Failed to save password for user ID: ' . $reset['user_id']);
                log_message('error', 'Validation errors: ' . json_encode($userModel->errors()));
                return redirect()->back()->with('error', 'Failed to update password. Please try again.');
            }

            // Mark reset as used
            $db->table('password_resets')->where('id', $resetId)->update([
                'status' => 'used'
            ]);

            log_message('info', 'Password successfully changed for user ID: ' . $reset['user_id']);
            return redirect()->to('admin/password-resets')->with('success', 'Password changed successfully! The user can now login with their new password.');
            
        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while changing the password. Please try again.');
        }
    }

    public function changePage($resetId)
    {
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets pr')
            ->select('pr.*, u.email')
            ->join('users u', 'u.id = pr.user_id')
            ->where('pr.id', $resetId)
            ->get()
            ->getRowArray();
        
        if (!$reset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Reset request not found');
        }
        
        return view('admin/password_reset_change', [
            'title' => 'Change Password - LPHS SMS',
            'reset' => $reset
        ]);
    }

    public function adminList()
    {
        $db = \Config\Database::connect();
        $resets = $db->table('password_resets pr')
            ->select('pr.*, u.email')
            ->join('users u', 'u.id = pr.user_id')
            ->where('pr.status', 'pending')
            ->orderBy('pr.requested_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/password_resets', [
            'title' => 'Password Reset Requests - LPHS SMS',
            'resets' => $resets
        ]);
    }
}