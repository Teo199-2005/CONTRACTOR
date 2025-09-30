<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcements extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    /**
     * Display list of announcements with CRUD interface
     */
    public function index()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        
        // Get all announcements with pagination
        $announcements = $announcementModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get statistics
        $stats = [
            'total' => $announcementModel->countAllResults(false),
            'published' => $announcementModel->countAllResults(false), // All announcements are published
        ];

        return view('admin/announcements', [
            'title' => 'Announcements - LPHS SMS',
            'announcements' => $announcements,
            'stats' => $stats,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        return view('admin/announcements_create', [
            'title' => 'Create Announcement - LPHS SMS',
        ]);
    }

    /**
     * Store new announcement
     */
    public function store()
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,admin,teacher,student,parent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'body' => $this->request->getPost('body'),
            'target_roles' => $this->request->getPost('target_roles'),
            'created_by' => $this->auth->id(),
            'published_at' => date('Y-m-d H:i:s'), // Always publish immediately
        ];

        $announcementModel = new AnnouncementModel();
        
        if ($announcementModel->save($data)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement published successfully!');
        } else {
            return redirect()->back()->withInput()->with('errors', $announcementModel->errors());
        }
    }

    /**
     * Show specific announcement
     */
    public function show($id)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        return view('admin/announcements_show', [
            'title' => 'View Announcement - LPHS SMS',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        return view('admin/announcements_edit', [
            'title' => 'Edit Announcement - LPHS SMS',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update announcement
     */
    public function update($id)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,admin,teacher,student,parent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'body' => $this->request->getPost('body'),
            'target_roles' => $this->request->getPost('target_roles'),
            'published_at' => $announcement['published_at'] ?: date('Y-m-d H:i:s'), // Ensure it's always published
        ];

        if ($announcementModel->update($id, $data)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('errors', $announcementModel->errors());
        }
    }

    /**
     * Delete announcement
     */
    public function delete($id)
    {
        if (!$this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        if ($announcementModel->delete($id)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement deleted successfully!');
        } else {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Failed to delete announcement.');
        }
    }



    /**
     * Get announcement statistics (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX() || !$this->auth->user()->inGroup('admin')) {
            return $this->response->setStatusCode(403);
        }

        $announcementModel = new AnnouncementModel();
        
        $stats = [
            'total' => $announcementModel->countAllResults(false),
            'published' => $announcementModel->countAllResults(false), // All announcements are published
        ];

        return $this->response->setJSON($stats);
    }
}
