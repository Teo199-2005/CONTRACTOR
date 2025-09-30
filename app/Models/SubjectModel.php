<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'subject_code', 'subject_name', 'description', 'grade_level',
        'units', 'is_core', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_core' => 'boolean',
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
        'subject_code' => 'required|max_length[20]|is_unique[subjects.subject_code,id,{id}]',
        'subject_name' => 'required|max_length[255]',
        'grade_level' => 'required|integer|greater_than[6]|less_than[11]',
        'units' => 'decimal|greater_than[0]'
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
     * Get subjects by grade level
     */
    public function getByGradeLevel($gradeLevel)
    {
        return $this->where('grade_level', $gradeLevel)
                    ->where('is_active', true)
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get core subjects by grade level
     */
    public function getCoreSubjects($gradeLevel)
    {
        return $this->where('grade_level', $gradeLevel)
                    ->where('is_core', true)
                    ->where('is_active', true)
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get elective subjects by grade level
     */
    public function getElectiveSubjects($gradeLevel)
    {
        return $this->where('grade_level', $gradeLevel)
                    ->where('is_core', false)
                    ->where('is_active', true)
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get all active subjects
     */
    public function getActiveSubjects()
    {
        return $this->where('is_active', true)
                    ->orderBy('grade_level', 'ASC')
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
    }
}
