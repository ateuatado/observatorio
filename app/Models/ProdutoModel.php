<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'titulo', 'autores', 'tipo', 'resumo', 'ano', 'publicacao',
        'doi', 'link_externo', 'arquivo', 'palavras_chave', 'ativo',
    ];

    public function getAtivos(int $limit = 20, int $offset = 0): array
    {
        return $this->where('ativo', 1)
            ->orderBy('ano', 'DESC')
            ->findAll($limit, $offset);
    }

    public function getRecentes(int $limit = 4): array
    {
        return $this->where('ativo', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }
}
