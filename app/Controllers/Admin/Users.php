<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

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
        return view('admin/users', [
            'title' => 'Manage Users - LPHS SMS',
            'users' => $users,
        ]);
    }

    public function create()
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getMethod() === 'POST') {
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

            $user = new User([
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password')
            ]);

            $this->userModel->save($user);
            $user = $this->userModel->findById($this->userModel->getInsertID());
            $user->addToGroup($this->request->getPost('role'));

            return redirect()->to(base_url('admin/users'))->with('success', 'User created successfully.');
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
}




