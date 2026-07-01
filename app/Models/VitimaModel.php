<?php

namespace App\Models;

use CodeIgniter\Model;

class VitimaModel extends Model
{
    protected $table            = 'vitimas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'ocorrencia_id', 'nome', 'anonimo', 'idade', 'genero',
        'raca_etnia', 'condicao_social', 'escolaridade', 'profissao',
        'relato', 'desfecho',
    ];

    public function getByOcorrencia(int $ocorrenciaId): array
    {
        return $this->where('ocorrencia_id', $ocorrenciaId)->findAll();
    }

    public function countByRaca(): array
    {
        return $this->db->table('vitimas')
            ->select('raca_etnia, COUNT(*) as total')
            ->where('raca_etnia IS NOT NULL')
            ->groupBy('raca_etnia')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }
}
