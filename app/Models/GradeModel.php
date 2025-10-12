<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeModel extends Model
{
    protected $table = 'grades';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id', 'subject_id', 'teacher_id', 'school_year',
        'quarter', 'grade', 'remarks', 'date_recorded'
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
        'student_id' => 'required|integer',
        'subject_id' => 'required|integer',
        'teacher_id' => 'required|integer',
        'school_year' => 'required|max_length[9]',
        'quarter' => 'required|integer|greater_than[0]|less_than[5]',
        'grade' => 'permit_empty|decimal|greater_than_equal_to[60]|less_than_equal_to[100]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDateRecorded'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['setDateRecorded'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Set date recorded before insert/update
     */
    protected function setDateRecorded(array $data)
    {
        if (isset($data['data']['grade']) && !empty($data['data']['grade'])) {
            $data['data']['date_recorded'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get grades for a student in a specific quarter
     */
    public function getStudentQuarterGrades($studentId, $schoolYear, $quarter)
    {
        return $this->select('grades.*, subjects.subject_name, subjects.subject_code, subjects.units')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->where('grades.student_id', $studentId)
            ->where('grades.school_year', $schoolYear)
            ->where('grades.quarter', $quarter)
            ->orderBy('subjects.subject_name', 'ASC')
            ->findAll();
    }

    /**
     * Get all grades for a student in a school year
     */
    public function getStudentYearGrades($studentId, $schoolYear)
    {
        return $this->select('grades.*, subjects.subject_name, subjects.subject_code, subjects.units')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->where('grades.student_id', $studentId)
            ->where('grades.school_year', $schoolYear)
            ->orderBy('subjects.subject_name', 'ASC')
            ->orderBy('grades.quarter', 'ASC')
            ->findAll();
    }

    /**
     * Calculate quarter average for a student
     */
    public function getQuarterAverage($studentId, $schoolYear, $quarter)
    {
        $grades = $this->select('grades.grade, subjects.units')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->where('grades.student_id', $studentId)
            ->where('grades.school_year', $schoolYear)
            ->where('grades.quarter', $quarter)
            ->where('grades.grade IS NOT NULL')
            ->findAll();

        if (empty($grades)) {
            return null;
        }

        $totalWeightedGrades = 0;
        $totalUnits = 0;

        foreach ($grades as $grade) {
            $totalWeightedGrades += $grade['grade'] * $grade['units'];
            $totalUnits += $grade['units'];
        }

        return $totalUnits > 0 ? round($totalWeightedGrades / $totalUnits, 2) : null;
    }

    /**
     * Calculate final average for a student in a school year
     */
    public function getFinalAverage($studentId, $schoolYear)
    {
        $quarterAverages = [];
        
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $average = $this->getQuarterAverage($studentId, $schoolYear, $quarter);
            if ($average !== null) {
                $quarterAverages[] = $average;
            }
        }

        if (empty($quarterAverages)) {
            return null;
        }

        return round(array_sum($quarterAverages) / count($quarterAverages), 2);
    }

    /**
     * Get grades for a teacher's subject
     */
    public function getTeacherSubjectGrades($teacherId, $subjectId, $schoolYear, $quarter = null)
    {
        $builder = $this->select('grades.*, students.first_name, students.last_name, students.student_id as student_number')
            ->join('students', 'students.id = grades.student_id')
            ->where('grades.teacher_id', $teacherId)
            ->where('grades.subject_id', $subjectId)
            ->where('grades.school_year', $schoolYear);

        if ($quarter) {
            $builder->where('grades.quarter', $quarter);
        }

        return $builder->orderBy('students.last_name', 'ASC')
            ->orderBy('students.first_name', 'ASC')
            ->findAll();
    }

    /**
     * Get class average for a subject and quarter
     */
    public function getClassAverage($subjectId, $schoolYear, $quarter)
    {
        $result = $this->select('AVG(grade) as average')
            ->where('subject_id', $subjectId)
            ->where('school_year', $schoolYear)
            ->where('quarter', $quarter)
            ->where('grade IS NOT NULL')
            ->first();

        return $result ? round($result['average'], 2) : null;
    }

    /**
     * Get grade distribution for a subject
     */
    public function getGradeDistribution($subjectId, $schoolYear, $quarter)
    {
        $grades = $this->select('grade')
            ->where('subject_id', $subjectId)
            ->where('school_year', $schoolYear)
            ->where('quarter', $quarter)
            ->where('grade IS NOT NULL')
            ->findAll();

        $distribution = [
            'excellent' => 0, // 90-100
            'very_good' => 0, // 85-89
            'good' => 0,      // 80-84
            'fair' => 0,      // 75-79
            'failing' => 0    // Below 75
        ];

        foreach ($grades as $grade) {
            $gradeValue = $grade['grade'];
            
            if ($gradeValue >= 90) {
                $distribution['excellent']++;
            } elseif ($gradeValue >= 85) {
                $distribution['very_good']++;
            } elseif ($gradeValue >= 80) {
                $distribution['good']++;
            } elseif ($gradeValue >= 75) {
                $distribution['fair']++;
            } else {
                $distribution['failing']++;
            }
        }

        return $distribution;
    }

    /**
     * Check if grade exists for student, subject, and quarter
     */
    public function gradeExists($studentId, $subjectId, $schoolYear, $quarter)
    {
        return $this->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('school_year', $schoolYear)
            ->where('quarter', $quarter)
            ->first() !== null;
    }

    /**
     * Update or insert grade
     */
    public function upsertGrade($data)
    {
        $existing = $this->where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('school_year', $data['school_year'])
            ->where('quarter', $data['quarter'])
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Get student ranking in class for a quarter
     */
    public function getStudentRanking($studentId, $schoolYear, $quarter)
    {
        // Get all students in the same grade level and section
        $studentModel = new StudentModel();
        $student = $studentModel->find($studentId);
        
        if (!$student) {
            return null;
        }

        // Get all students in the same section
        $classmates = $studentModel->where('section_id', $student['section_id'])
            ->where('enrollment_status', 'enrolled')
            ->findAll();

        $rankings = [];
        
        foreach ($classmates as $classmate) {
            $average = $this->getQuarterAverage($classmate['id'], $schoolYear, $quarter);
            if ($average !== null) {
                $rankings[] = [
                    'student_id' => $classmate['id'],
                    'average' => $average
                ];
            }
        }

        // Sort by average descending
        usort($rankings, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Find student's rank
        foreach ($rankings as $index => $ranking) {
            if ($ranking['student_id'] == $studentId) {
                return [
                    'rank' => $index + 1,
                    'total_students' => count($rankings),
                    'average' => $ranking['average']
                ];
            }
        }

        return null;
    }
}
