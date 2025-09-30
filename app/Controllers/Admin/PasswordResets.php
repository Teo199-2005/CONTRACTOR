<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PasswordResetRequestModel;
use CodeIgniter\Shield\Models\UserModel;

class PasswordResets extends BaseController
{
    protected $resetRequestModel;
    protected $userModel;

    public function __construct()
    {
        $this->resetRequestModel = model(PasswordResetRequestModel::class);
        $this->userModel = model(UserModel::class);
    }

    /**
     * Display password reset requests
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        // Check if table exists and handle gracefully
        try {
            // Mark expired requests
            $this->resetRequestModel->markExpiredRequests();

            // Get all requests with details
            $requests = $this->resetRequestModel->getAllRequestsWithDetails();
        } catch (\Exception $e) {
            // If table doesn't exist, show empty state with instructions
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return view('admin/password_resets', [
                    'title' => 'Password Reset Requests - LPHS SMS',
                    'requests' => [],
                    'table_missing' => true
                ]);
            }
            throw $e;
        }

        return view('admin/password_resets', [
            'title' => 'Password Reset Requests - LPHS SMS',
            'requests' => $requests
        ]);
    }

    /**
     * Approve a password reset request
     */
    public function approve($requestId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $notes = $this->request->getPost('notes');
        $adminId = auth()->user()->id;

        $success = $this->resetRequestModel->approveRequest($requestId, $adminId, $notes);

        if ($success) {
            // Get the request details to send email/notification
            $request = $this->resetRequestModel->find($requestId);
            
            // In a real application, you would send an email with the reset link here
            // For now, we'll just return success
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset request approved successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to approve password reset request.'
        ]);
    }

    /**
     * Reject a password reset request
     */
    public function reject($requestId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $notes = $this->request->getPost('notes');
        $adminId = auth()->user()->id;

        $success = $this->resetRequestModel->rejectRequest($requestId, $adminId, $notes);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset request rejected.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to reject password reset request.'
        ]);
    }

    /**
     * Get request details for modal
     */
    public function getRequestDetails($requestId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $request = $this->resetRequestModel
            ->select('password_reset_requests.*, users.email as user_email, students.first_name, students.last_name, students.student_id, students.contact_number, admin_users.email as approved_by_email')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->join('users as admin_users', 'admin_users.id = password_reset_requests.approved_by', 'left')
            ->find($requestId);

        if (!$request) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Request not found']);
        }

        return $this->response->setJSON($request);
    }

    /**
     * Generate reset link for approved request
     */
    public function generateResetLink($requestId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $request = $this->resetRequestModel->find($requestId);

        if (!$request || $request['status'] !== 'approved') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request not found or not approved']);
        }

        $resetLink = base_url('reset-password/' . $request['token']);

        return $this->response->setJSON([
            'success' => true,
            'reset_link' => $resetLink,
            'expires_at' => $request['expires_at']
        ]);
    }

    /**
     * Get count of pending password reset requests
     */
    public function getCount()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            // Mark expired requests first
            $this->resetRequestModel->markExpiredRequests();

            // Get count of pending requests
            $count = $this->resetRequestModel
                ->where('status', 'pending')
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->countAllResults();

            return $this->response->setJSON(['count' => $count]);
        } catch (\Exception $e) {
            // If table doesn't exist, return 0 count
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return $this->response->setJSON(['count' => 0]);
            }
            throw $e;
        }
    }
}
