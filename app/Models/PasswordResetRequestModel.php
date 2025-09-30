<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetRequestModel extends Model
{
    protected $table = 'password_reset_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id', 'email', 'token', 'expires_at', 'status', 
        'approved_by', 'approved_at', 'used_at', 'admin_notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'email' => 'required|valid_email',
        'token' => 'required|min_length[32]',
        'expires_at' => 'required|valid_date',
        'status' => 'required|in_list[pending,approved,rejected,used,expired]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid integer'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Email must be a valid email address'
        ],
        'token' => [
            'required' => 'Token is required',
            'min_length' => 'Token must be at least 32 characters long'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: pending, approved, rejected, used, expired'
        ]
    ];

    /**
     * Get pending password reset requests for admin review
     */
    public function getPendingRequests()
    {
        return $this->select('password_reset_requests.*, users.email as user_email, students.first_name, students.last_name, students.student_id')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->where('password_reset_requests.status', 'pending')
            ->where('password_reset_requests.expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('password_reset_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all password reset requests with user details
     */
    public function getAllRequestsWithDetails()
    {
        return $this->select('password_reset_requests.*, users.email as user_email, students.first_name, students.last_name, students.student_id, admin_users.email as approved_by_email')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->join('users as admin_users', 'admin_users.id = password_reset_requests.approved_by', 'left')
            ->orderBy('password_reset_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Approve a password reset request
     */
    public function approveRequest($requestId, $adminId, $notes = null)
    {
        return $this->update($requestId, [
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_notes' => $notes
        ]);
    }

    /**
     * Reject a password reset request
     */
    public function rejectRequest($requestId, $adminId, $notes = null)
    {
        return $this->update($requestId, [
            'status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_notes' => $notes
        ]);
    }

    /**
     * Mark expired requests
     */
    public function markExpiredRequests()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->where('status', 'pending')
            ->set(['status' => 'expired'])
            ->update();
    }
}
