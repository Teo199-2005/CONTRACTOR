<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;

class Materials extends BaseController
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
        return view('teacher/materials', [
            'title' => 'Learning Materials - LPHS SMS',
        ]);
    }

    public function upload()
    {
        if (! $this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        $file = $this->request->getFile('material');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file.');
        }
        $file->move(WRITEPATH . 'uploads/materials');
        return redirect()->back()->with('success', 'Material uploaded.');
    }
}




