<?php

namespace App\Controllers\Parent;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Dashboard extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (!$this->auth->user()->inGroup('parent')) {
            return redirect()->to(base_url('/'));
        }
        return view('parent/dashboard', ['title' => 'Parent Dashboard - LPHS SMS']);
    }

    public function children()
    {
        if (!$this->auth->user()->inGroup('parent')) {
            return redirect()->to(base_url('/'));
        }
        return view('parent/children', ['title' => 'My Children - LPHS SMS']);
    }

    public function childGrades($studentId)
    {
        if (!$this->auth->user()->inGroup('parent')) {
            return redirect()->to(base_url('/'));
        }
        return view('parent/grades', ['title' => 'Child Grades - LPHS SMS', 'studentId' => $studentId]);
    }

    public function announcements()
    {
        if (!$this->auth->user()->inGroup('parent')) {
            return redirect()->to(base_url('/'));
        }
        $ann = new AnnouncementModel();
        $list = $ann->where('target_roles', 'all')->orWhere('target_roles', 'parent')->orderBy('published_at','DESC')->findAll();
        return view('parent/announcements', ['title' => 'Announcements - LPHS SMS', 'announcements' => $list]);
    }
} 