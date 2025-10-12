<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherScheduleModel extends Model
{
    protected $table = 'teacher_schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'teacher_id', 'subject_id', 'section_id', 'day_of_week', 
        'start_time', 'end_time', 'room', 'school_year'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getTeacherSchedule($teacherId, $schoolYear = '2024-2025')
    {
        return $this->select('teacher_schedules.*, subjects.subject_name, subjects.subject_code, sections.section_name, sections.grade_level')
            ->join('subjects', 'subjects.id = teacher_schedules.subject_id')
            ->join('sections', 'sections.id = teacher_schedules.section_id')
            ->where('teacher_schedules.teacher_id', $teacherId)
            ->where('teacher_schedules.school_year', $schoolYear)
            ->orderBy('teacher_schedules.day_of_week')
            ->orderBy('teacher_schedules.start_time')
            ->findAll();
    }
}