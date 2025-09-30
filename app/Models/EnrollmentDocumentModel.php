<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentDocumentModel extends Model
{
    protected $table = 'enrollment_documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'student_id', 'document_type', 'document_name', 'file_path', 'file_size', 'mime_type',
        'is_verified', 'verified_by', 'verified_at', 'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'student_id' => 'required|integer',
        'document_type' => 'required|in_list[birth_certificate,report_card,good_moral,medical_certificate,photo,other]',
        'file_path' => 'required',
        'file_size' => 'required|integer',
        'mime_type' => 'required'
    ];
} 