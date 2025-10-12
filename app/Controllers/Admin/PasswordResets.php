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

        // Get all requests with details
        try {
            $this->resetRequestModel->markExpiredRequests();
            $requests = $this->resetRequestModel->getAllRequestsWithDetails();
        } catch (\Exception $e) {
            $requests = [];
        }

        return view('admin/password_resets', [
            'title' => 'Password Reset Requests - LPHS SMS',
            'requests' => $requests,
            'table_missing' => false
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

        // Get the request details first
        $request = $this->resetRequestModel
            ->select('password_reset_requests.*, students.id as student_id')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->find($requestId);

        if (!$request) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Request not found']);
        }

        $success = $this->resetRequestModel->approveRequest($requestId, $adminId, $notes);

        if ($success) {
            // Redirect to student view page with password reset functionality
            $studentId = $request['student_id'];
            
            return $this->response->setJSON([
                'success' => true,
                'redirect' => base_url("admin/students/view/{$studentId}?password_reset=true"),
                'message' => 'Password reset request approved. Redirecting to student page...'
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

    /**
     * Approve all pending password reset requests
     */
    public function approveAll()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $adminId = auth()->user()->id;
        
        $success = $this->resetRequestModel
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->set([
                'status' => 'approved',
                'approved_by' => $adminId,
                'admin_notes' => 'Bulk approved by admin',
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All pending requests approved successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to approve all requests.'
        ]);
    }

    /**
     * Reject all pending password reset requests
     */
    public function rejectAll()
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $adminId = auth()->user()->id;
        
        $success = $this->resetRequestModel
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->set([
                'status' => 'rejected',
                'approved_by' => $adminId,
                'admin_notes' => 'Bulk rejected by admin',
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All pending requests rejected successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to reject all requests.'
        ]);
    }

    /**
     * Delete a password reset request
     */
    public function delete($requestId)
    {
        // Check if user is admin
        if (!auth()->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $success = $this->resetRequestModel->delete($requestId);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset request deleted successfully.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to delete password reset request.'
        ]);
    }
}
