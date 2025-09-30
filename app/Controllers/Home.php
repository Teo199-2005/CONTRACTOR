<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): ResponseInterface|string
    {
        // If logged in, send user to their role dashboard instead of landing page
        try {
            $auth = auth();
            if ($auth->loggedIn()) {
                $user = $auth->user();
                if ($user->inGroup('admin')) {
                    return redirect()->to(base_url('admin/dashboard'));
                }
                if ($user->inGroup('teacher')) {
                    return redirect()->to(base_url('teacher/dashboard'));
                }
                if ($user->inGroup('student')) {
                    return redirect()->to(base_url('student/dashboard'));
                }
                if ($user->inGroup('parent')) {
                    return redirect()->to(base_url('parent/dashboard'));
                }
            }
        } catch (\Throwable $e) {
            // ignore and show public landing page
        }

        $announcements = [];
        try {
            $model = new AnnouncementModel();
            $announcements = $model->orderBy('published_at', 'DESC')->findAll(5);
        } catch (\Throwable $e) {
            // Table may not exist yet during first run.
        }
        return view('landing', [
            'title' => 'LPHS School Management System',
            'announcements' => $announcements,
        ]);
    }

    // About pages disabled per navigation cleanup
}
