<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcements extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $model = new AnnouncementModel();
        
        // Get announcements for teachers (from admin) and all announcements
        $announcements = $model->where('target_roles', 'teacher')
                              ->orWhere('target_roles', 'all')
                              ->orWhere('target_roles', 'admin')
                              ->orderBy('created_at', 'DESC')
                              ->findAll();
        
        return view('teacher/announcements', [
            'title' => 'Announcements - LPHS SMS',
            'announcements' => $announcements
        ]);
    }

    public function post()
    {
        if (! $this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new AnnouncementModel();
        $model->insert([
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'body' => $this->request->getPost('body'),
            'target_roles' => 'student',
            'published_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Announcement posted to students.');
    }
}




