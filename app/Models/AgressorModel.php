<?php

namespace App\Models;

use CodeIgniter\Model;

class AgressorModel extends Model
{
    protected $table            = 'agressores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'ocorrencia_id', 'tipo_agente', 'orgao', 'batalhao',
        'posto', 'identificacao', 'identificado', 'observacoes',
    ];

    public function getByOcorrencia(int $ocorrenciaId): array
    {
        return $this->where('ocorrencia_id', $ocorrenciaId)->findAll();
    }
}
