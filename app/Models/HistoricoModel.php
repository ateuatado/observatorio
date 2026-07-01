<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoModel extends Model
{
    protected $table            = 'historico';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'titulo', 'descricao', 'periodo', 'ano_inicio', 'ano_fim',
        'categoria', 'arquivo_pdf', 'miniatura', 'autora', 'ativo',
    ];

    public function getAtivos(int $limit = 20, int $offset = 0): array
    {
        return $this->where('ativo', 1)
            ->orderBy('ano_inicio', 'DESC')
            ->findAll($limit, $offset);
    }
}
