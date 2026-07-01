<?php

namespace App\Models;

use CodeIgniter\Model;

class RevisaoModel extends Model
{
    protected $table            = 'ocorrencia_revisoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'ocorrencia_id', 'user_id', 'acao', 'status_anterior',
        'status_novo', 'comentario',
    ];

    public function getByOcorrencia(int $ocorrenciaId): array
    {
        return $this->where('ocorrencia_id', $ocorrenciaId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
