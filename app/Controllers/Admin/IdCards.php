<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\SectionModel;
use App\Models\EnrollmentDocumentModel;

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

        $studentModel = new StudentModel();
        $sectionModel = new SectionModel();
        $documentModel = new EnrollmentDocumentModel();

        // Get filter parameters
        $gradeFilter = $this->request->getGet('grade');
        $sectionFilter = $this->request->getGet('section');
        $searchTerm = $this->request->getGet('search');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 9;

        // Build query with filters
        $builder = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('students.enrollment_status', 'enrolled');

        if ($gradeFilter) {
            $builder->where('students.grade_level', $gradeFilter);
        }

        if ($sectionFilter) {
            $builder->where('students.section_id', $sectionFilter);
        }

        if ($searchTerm) {
            $builder->groupStart()
                ->like('students.first_name', $searchTerm)
                ->orLike('students.last_name', $searchTerm)
                ->orLike('students.student_id', $searchTerm)
                ->groupEnd();
        }

        // Get total count for pagination
        $totalStudents = $builder->countAllResults(false);
        $totalPages = ceil($totalStudents / $perPage);
        $offset = ($page - 1) * $perPage;

        $students = $builder->orderBy('students.last_name', 'ASC')
            ->orderBy('students.first_name', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // Get student photos
        foreach ($students as &$student) {
            $photo = $documentModel->where('student_id', $student['id'])
                ->where('document_type', 'photo')
                ->first();
            $student['photo_path'] = $photo ? $photo['file_path'] : null;
        }

        // Get all sections for filter dropdown
        $allSections = $sectionModel->select('id, section_name, grade_level')
            ->where('is_active', true)
            ->orderBy('grade_level', 'ASC')
            ->orderBy('section_name', 'ASC')
            ->findAll();

        return view('admin/id_cards', [
            'title' => 'Student ID Cards - LPHS SMS',
            'students' => $students,
            'allSections' => $allSections,
            'gradeFilter' => $gradeFilter,
            'sectionFilter' => $sectionFilter,
            'searchTerm' => $searchTerm,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalStudents' => $totalStudents
        ]);
    }

    public function viewCard($studentId)
    {
        if (! $this->auth->user()->inGroup('admin')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $documentModel = new EnrollmentDocumentModel();

        $student = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Get student photo
        $photo = $documentModel->where('student_id', $studentId)
            ->where('document_type', 'photo')
            ->first();
        $student['photo_path'] = $photo ? $photo['file_path'] : null;

        return view('admin/id_card_view', [
            'title' => 'Student ID Card - ' . $student['first_name'] . ' ' . $student['last_name'],
            'student' => $student
        ]);
    }
}
