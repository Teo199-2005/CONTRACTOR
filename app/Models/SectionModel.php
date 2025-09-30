<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table = 'sections';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'section_name', 'grade_level', 'school_year', 'adviser_id',
        'max_capacity', 'current_enrollment', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'section_name' => 'required|max_length[100]',
        'grade_level' => 'required|integer|greater_than[6]|less_than[11]',
        'school_year' => 'required|max_length[9]',
        'max_capacity' => 'integer|greater_than[0]',
        'current_enrollment' => 'integer|greater_than_equal_to[0]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get sections by grade level
     */
    public function getByGradeLevel($gradeLevel, $schoolYear = null)
    {
        $builder = $this->where('grade_level', $gradeLevel)
                        ->where('is_active', true);
        
        if ($schoolYear) {
            $builder->where('school_year', $schoolYear);
        }
        
        return $builder->findAll();
    }

    /**
     * Get sections with available slots
     */
    public function getAvailableSections($gradeLevel, $schoolYear = null)
    {
        $builder = $this->where('grade_level', $gradeLevel)
                        ->where('is_active', true)
                        ->where('current_enrollment < max_capacity');
        
        if ($schoolYear) {
            $builder->where('school_year', $schoolYear);
        }
        
        return $builder->findAll();
    }

    /**
     * Pick the best available section for a given grade level and school year.
     * Prefers the section with the lowest current_enrollment (to balance load),
     * then higher max_capacity as a tiebreaker.
     */
    public function selectBestAvailableSection(int $gradeLevel, ?string $schoolYear = null): ?array
    {
        $builder = $this->where('grade_level', $gradeLevel)
                        ->where('is_active', true)
                        ->where('current_enrollment < max_capacity')
                        ->orderBy('current_enrollment', 'ASC')
                        ->orderBy('max_capacity', 'DESC');

        if ($schoolYear) {
            $builder->where('school_year', $schoolYear);
        }

        $section = $builder->first();
        return $section ?: null;
    }

    /**
     * Get section with adviser information
     */
    public function getSectionWithAdviser($id)
    {
        return $this->select('sections.*, teachers.first_name as adviser_first_name, 
                             teachers.last_name as adviser_last_name, teachers.teacher_id')
            ->join('teachers', 'teachers.id = sections.adviser_id', 'left')
            ->where('sections.id', $id)
            ->first();
    }

    /**
     * Get sections with student count
     */
    public function getSectionsWithStudentCount($schoolYear = null)
    {
        $builder = $this->select('sections.*, COUNT(students.id) as actual_enrollment')
            ->join('students', 'students.section_id = sections.id AND students.enrollment_status = "enrolled"', 'left')
            ->groupBy('sections.id');
        
        if ($schoolYear) {
            $builder->where('sections.school_year', $schoolYear);
        }
        
        return $builder->findAll();
    }

    /**
     * Update enrollment count
     */
    public function updateEnrollmentCount($sectionId)
    {
        $studentModel = new \App\Models\StudentModel();
        $count = $studentModel->where('section_id', $sectionId)
                             ->where('enrollment_status', 'enrolled')
                             ->countAllResults();
        
        return $this->update($sectionId, ['current_enrollment' => $count]);
    }

    /**
     * Check if section has available slots
     */
    public function hasAvailableSlots($sectionId): bool
    {
        $section = $this->find($sectionId);
        if (!$section) {
            return false;
        }

        return $section['current_enrollment'] < $section['max_capacity'];
    }

    /**
     * Get sections with adviser information and student count
     */
    public function getSectionsWithAdviser(string $schoolYear): array
    {
        return $this->select('sections.*, COUNT(students.id) as current_enrollment,
                             teachers.first_name as adviser_first_name,
                             teachers.last_name as adviser_last_name,
                             teachers.email as adviser_email,
                             CONCAT(teachers.first_name, " ", teachers.last_name) as adviser_name')
            ->join('students', 'students.section_id = sections.id AND students.enrollment_status = "enrolled"', 'left')
            ->join('teachers', 'teachers.id = sections.adviser_id', 'left')
            ->where('sections.school_year', $schoolYear)
            ->groupBy('sections.id')
            ->orderBy('sections.grade_level', 'ASC')
            ->orderBy('sections.section_name', 'ASC')
            ->findAll();
    }
}
