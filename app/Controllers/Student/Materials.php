<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;

class Materials extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'));
        }

        $studentModel = new StudentModel();
        $student = $studentModel->where('user_id', $this->auth->id())->first();
        
        if (!$student) {
            return redirect()->to(base_url('student/dashboard'))->with('error', 'Student record not found.');
        }

        $schedule = $this->getClassSchedule($student['grade_level'], $student['section_id']);
        $materials = $this->getClassMaterials($student['grade_level'], $student['section_id']);

        return view('student/materials', [
            'title' => 'Class Materials - LPHS SMS',
            'student' => $student,
            'schedule' => $schedule,
            'materials' => $materials
        ]);
    }

    private function getClassSchedule($gradeLevel, $sectionId)
    {
        $schedules = [
            7 => [
                'Aphrodite' => [
                    '7:30-8:30' => 'Filipino (Annabel Portades)',
                    '8:30-9:30' => 'MAPEH (Laila Salvadora)',
                    '9:45-10:45' => 'English (Joven Labilles)',
                    '10:45-11:45' => 'Values Ed. (Charito Malapo)',
                    '1:00-2:00' => 'Science (Aiza Sabordo)',
                    '2:00-3:00' => 'Math (Maricar Sapugay)',
                    '3:00-4:00' => 'TLE FSC (Christine Lumabe)',
                    '4:00-5:00' => 'Aral Pan (Nathan Dolorical)'
                ],
                'Belus' => [
                    '7:30-8:30' => 'Science (Aiza Sabordo)',
                    '8:30-9:30' => 'Math (Maricar Sapugay)',
                    '9:45-10:45' => 'Values Ed. (Charito Malapo)',
                    '10:45-11:45' => 'English (Joven Labilles)',
                    '1:00-2:00' => 'MAPEH (Laila Salvadora)',
                    '2:00-3:00' => 'Aral Pan (Nathan Dolorical)',
                    '3:00-4:00' => 'Filipino (Annabel Portades)',
                    '4:00-5:00' => 'TLE FSC (Christine Lumabe)'
                ]
            ],
            8 => [
                'Aesop' => [
                    '7:30-8:30' => 'Filipino (Midlyn Castillo)',
                    '8:30-9:30' => 'Values Ed. (Annabel Portades)',
                    '9:45-10:45' => 'Math (Elisa Ereno)',
                    '10:45-11:45' => 'Science (Roselle Plotado)',
                    '1:00-2:00' => 'English (Joven Labilles)',
                    '2:00-3:00' => 'TLE FSC (Christine Lumabe)',
                    '3:00-4:00' => 'Aral Pan (Nathan Dolorical)',
                    '4:00-5:00' => 'MAPEH (Laila Salvadora)'
                ],
                'Bower' => [
                    '7:30-8:30' => 'Mathematics (Elisa Ereno)',
                    '8:30-9:30' => 'Aral Pan (Nathan Dolorical)',
                    '9:45-10:45' => 'Filipino (Midlyn Castillo)',
                    '10:45-11:45' => 'TLE FSC (Christine Lumabe)',
                    '1:00-2:00' => 'Values Ed. (Charito Malapo)',
                    '2:00-3:00' => 'English (Elaine Oida)',
                    '3:00-4:00' => 'MAPEH (Laila Salvadora)',
                    '4:00-5:00' => 'Science (Roselle Plotado)'
                ]
            ],
            9 => [
                'Argon' => [
                    '7:30-8:30' => 'Values Ed. (Maricar Sapugay)',
                    '8:30-9:30' => 'MAPEH (Roselle Plotado)',
                    '9:45-10:45' => 'English (Annabel Portades)',
                    '10:45-11:45' => 'Aral Pan (Nathan Dolorical)',
                    '1:00-2:00' => 'Filipino (Midlyn Castillo)',
                    '2:00-3:00' => 'Math (Elisa Ereno)',
                    '3:00-4:00' => 'Science (Elaine Oida)',
                    '4:00-5:00' => 'TLE (Charito Malapo)'
                ],
                'Beryllium' => [
                    '7:30-8:30' => 'Science (Elaine Oida)',
                    '8:30-9:30' => 'TLE/BPP (Charito Malapo)',
                    '9:45-10:45' => 'Aral Pan (Nathan Dolorical)',
                    '10:45-11:45' => 'Filipino (Annabel Portades)',
                    '1:00-2:00' => 'English (Christine Lumabe)',
                    '2:00-3:00' => 'MAPEH (Roselle Plotado)',
                    '3:00-4:00' => 'Values Ed. (Maricar Sapugay)',
                    '4:00-5:00' => 'Math (Elisa Ereno)'
                ]
            ],
            10 => [
                'Aristotle' => [
                    '7:30-8:30' => 'Filipino (Midlyn Castillo)',
                    '8:30-9:30' => 'Filipino (Midlyn Castillo)',
                    '9:45-10:45' => 'Science (Jeanette Rodriguez)',
                    '10:45-11:45' => 'Values Ed. (Maricar Sapugay)',
                    '1:00-2:00' => 'Math (Elisa Ereno)',
                    '2:00-3:00' => 'Values Ed. (Aiza Sabordo)',
                    '3:00-4:00' => 'MAPEH (Roselle Plotado)',
                    '4:00-5:00' => 'Aral Pan (Aiza Sabordo)'
                ],
                'Bartley' => [
                    '7:30-8:30' => 'Mathematics (Jeanette Rodriguez)',
                    '8:30-9:30' => 'Math (Elisa Ereno)',
                    '9:45-10:45' => 'Aral Pan (Aiza Sabordo)',
                    '10:45-11:45' => 'MAPEH (Laila Salvadora)',
                    '1:00-2:00' => 'TLE (Elaine Oida)',
                    '2:00-3:00' => 'English (Joven Labilles)',
                    '3:00-4:00' => 'Filipino (Midlyn Castillo)',
                    '4:00-5:00' => 'Values Ed. (Jeanette Rodriguez)'
                ]
            ]
        ];

        $sectionName = $this->getSectionName($gradeLevel, $sectionId);
        return $schedules[$gradeLevel][$sectionName] ?? [];
    }

    private function getClassMaterials($gradeLevel, $sectionId)
    {
        return [
            'Filipino' => ['Week 1: Tula at Maikling Kwento', 'Week 2: Gramatika at Pananalita'],
            'Mathematics' => ['Week 1: Algebra Basics', 'Week 2: Geometry Fundamentals'],
            'Science' => ['Week 1: Scientific Method', 'Week 2: Matter and Energy'],
            'English' => ['Week 1: Grammar Review', 'Week 2: Reading Comprehension'],
            'MAPEH' => ['Week 1: Music Theory', 'Week 2: Physical Education Basics'],
            'Values Education' => ['Week 1: Moral Values', 'Week 2: Ethics and Character'],
            'Araling Panlipunan' => ['Week 1: Philippine History', 'Week 2: Geography'],
            'TLE' => ['Week 1: Basic Skills', 'Week 2: Practical Applications']
        ];
    }

    private function getSectionName($gradeLevel, $sectionId)
    {
        // Get section name from database or use fallback mapping
        $db = \Config\Database::connect();
        $section = $db->table('sections')->where('id', $sectionId)->get()->getRowArray();
        
        if ($section && !empty($section['section_name'])) {
            return $section['section_name'];
        }
        
        // Fallback mapping
        $sections = [
            7 => [1 => 'Aphrodite', 2 => 'Belus'],
            8 => [1 => 'Aesop', 2 => 'Bower'],
            9 => [1 => 'Argon', 2 => 'Beryllium'],
            10 => [1 => 'Aristotle', 2 => 'Bartley']
        ];
        return $sections[$gradeLevel][$sectionId] ?? 'Aesop';
    }
}

