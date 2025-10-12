<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id', 'user_id', 'lrn', 'student_type', 'first_name', 'middle_name', 'last_name', 'suffix',
        'gender', 'date_of_birth', 'place_of_birth', 'nationality', 'religion',
        'contact_number', 'email', 'address', 'emergency_contact_name',
        'emergency_contact_number', 'emergency_contact_relationship', 'photo_path',
        'enrollment_status', 'grade_level', 'section_id', 'school_year'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'gender' => 'required|in_list[Male,Female]',
        'date_of_birth' => 'required|valid_date',
        'enrollment_status' => 'in_list[pending,approved,rejected,enrolled,graduated,dropped]',
        'grade_level' => 'permit_empty|integer|greater_than[6]|less_than[13]',
        'email' => 'permit_empty|valid_email'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateStudentId'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate unique student ID before insert
     */
    protected function generateStudentId(array $data)
    {
        if (isset($data['data']['enrollment_status']) && $data['data']['enrollment_status'] === 'approved') {
            if (empty($data['data']['student_id'])) {
                $data['data']['student_id'] = $this->createUniqueStudentId();
            }
        }
        return $data;
    }

    /**
     * Create a unique student ID
     */
    public function createUniqueStudentId(): string
    {
        $year = date('Y');
        $lastStudent = $this->select('student_id')
            ->where('student_id LIKE', $year . '%')
            ->orderBy('student_id', 'DESC')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent['student_id'], 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get student with section information
     */
    public function getStudentWithSection($id)
    {
        return $this->select('students.*, sections.section_name, sections.grade_level as section_grade')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('students.id', $id)
            ->first();
    }

    /**
     * Get students by enrollment status
     */
    public function getByEnrollmentStatus($status)
    {
        return $this->where('enrollment_status', $status)->findAll();
    }

    /**
     * Get students by grade level
     */
    public function getByGradeLevel($gradeLevel)
    {
        return $this->where('grade_level', $gradeLevel)->findAll();
    }

    /**
     * Get students by section
     */
    public function getBySection($sectionId)
    {
        return $this->where('section_id', $sectionId)->findAll();
    }

    /**
     * Get full name of student
     */
    public function getFullName($student): string
    {
        $name = $student['first_name'];
        if (!empty($student['middle_name'])) {
            $name .= ' ' . $student['middle_name'];
        }
        $name .= ' ' . $student['last_name'];
        if (!empty($student['suffix'])) {
            $name .= ' ' . $student['suffix'];
        }
        return $name;
    }

    /**
     * Approve student enrollment
     */
    public function approveEnrollment($id, $sectionId = null)
    {
        $data = [
            'enrollment_status' => 'approved',
            'student_id' => $this->createUniqueStudentId()
        ];

        // Auto-assign section if not provided
        if (!$sectionId) {
            $student = $this->find($id);
            if ($student && !empty($student['grade_level'])) {
                $sectionModel = new \App\Models\SectionModel();
                $best = $sectionModel->selectBestAvailableSection((int) $student['grade_level'], $student['school_year'] ?? null);
                if ($best) {
                    $sectionId = (int) $best['id'];
                }
            }
        }

        if ($sectionId) {
            $data['section_id'] = $sectionId;
        }

        return $this->update($id, $data);
    }

    /**
     * Reject student enrollment
     */
    public function rejectEnrollment($id, $reason = null)
    {
        return $this->update($id, [
            'enrollment_status' => 'rejected'
        ]);
    }

    /**
     * Enroll approved student
     */
    public function enrollStudent($id, $sectionId, $schoolYear)
    {
        return $this->update($id, [
            'enrollment_status' => 'enrolled',
            'section_id' => $sectionId,
            'school_year' => $schoolYear
        ]);
    }
}
