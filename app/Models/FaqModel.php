<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faq';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'question','answer','keywords','category','is_active','view_count','created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function searchByKeywords(string $query, int $limit = 5): array
    {
        $q = trim($query);
        if ($q === '') return [];
        return $this->where('is_active', 1)
            ->groupStart()
                ->like('keywords', $q)
                ->orLike('question', $q)
            ->groupEnd()
            ->orderBy('view_count', 'DESC')
            ->findAll($limit);
    }
} 