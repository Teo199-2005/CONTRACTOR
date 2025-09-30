<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;

class IdCards extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }
        $students = (new StudentModel())
            ->where('enrollment_status', 'enrolled')
            ->orderBy('last_name', 'ASC')
            ->findAll(50);

        return view('admin/id_cards', [
            'title' => 'Student ID Cards - LPHS SMS',
            'students' => $students,
        ]);
    }
}




