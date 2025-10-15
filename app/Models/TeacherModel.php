<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table = 'teachers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'employee_id', 'user_id', 'first_name', 'middle_name', 'last_name', 'suffix',
        'gender', 'date_of_birth', 'contact_number', 'email', 'address',
        'department', 'position', 'specialization', 'date_hired',
        'employment_status', 'photo_path', 'license_number'
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
        'employee_id' => 'permit_empty|is_unique[teachers.employee_id,id,{id}]',
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'gender' => 'required|in_list[Male,Female]',
        'email' => 'required|valid_email|is_unique[teachers.email,id,{id}]',
        'employment_status' => 'in_list[active,inactive,resigned,terminated]',
        'position' => 'max_length[100]',
        'license_number' => 'permit_empty|max_length[20]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateEmployeeId'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate unique employee ID before insert
     */
    protected function generateEmployeeId(array $data)
    {
        if (empty($data['data']['employee_id'])) {
            $data['data']['employee_id'] = $this->createUniqueEmployeeId();
        }
        return $data;
    }

    /**
     * Create a unique employee ID
     */
    public function createUniqueEmployeeId(): string
    {
        $year = date('Y');
        $lastTeacher = $this->select('employee_id')
            ->where('employee_id LIKE', 'T' . $year . '%')
            ->orderBy('employee_id', 'DESC')
            ->first();

        if ($lastTeacher) {
            $lastNumber = (int) substr($lastTeacher['employee_id'], 5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'T' . $year . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get active teachers
     */
    public function getActiveTeachers()
    {
        return $this->where('employment_status', 'active')->findAll();
    }

    /**
     * Get teachers by department
     */
    public function getByDepartment($department)
    {
        return $this->where('department', $department)
            ->where('employment_status', 'active')
            ->findAll();
    }

    /**
     * Get full name of teacher
     */
    public function getFullName($teacher): string
    {
        $name = $teacher['first_name'];
        if (!empty($teacher['middle_name'])) {
            $name .= ' ' . $teacher['middle_name'];
        }
        $name .= ' ' . $teacher['last_name'];
        if (!empty($teacher['suffix'])) {
            $name .= ' ' . $teacher['suffix'];
        }
        return $name;
    }

    /**
     * Get teacher with sections they advise
     */
    public function getTeacherWithSections($id)
    {
        return $this->select('teachers.*, sections.section_name, sections.grade_level')
            ->join('sections', 'sections.adviser_id = teachers.id', 'left')
            ->where('teachers.id', $id)
            ->first();
    }

    /**
     * Get teachers available for section advising
     */
    public function getAvailableAdvisers()
    {
        // Get all active teachers (since none are currently assigned)
        return $this->select('id, first_name, last_name, email, license_number')
                   ->where('employment_status', 'active')
                   ->where('deleted_at IS NULL')
                   ->orderBy('first_name', 'ASC')
                   ->orderBy('last_name', 'ASC')
                   ->findAll();
    }
}
