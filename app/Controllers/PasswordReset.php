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
     * Handle forgot password form submission
     */
    public function sendResetInstructions()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return view('auth/forgot_password', [
                'validation' => $this->validator
            ]);
        }

        $email = $this->request->getPost('email');
        
        // Check if user exists
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            return redirect()->back()
                ->with('error', 'No account found with that email address.');
        }

        // Check if user is a student (only students can request password reset)
        if (!$user->inGroup('student')) {
            return redirect()->back()
                ->with('error', 'Password reset is only available for student accounts. Please contact the administrator for assistance.');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Save reset request
        $this->resetRequestModel->insert([
            'user_id' => $user->id,
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // In a real application, you would send an email here
        // For now, we'll just show a success message
        return redirect()->back()
            ->with('success', 'Password reset request submitted successfully. An administrator will review your request and contact you with further instructions.');
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
