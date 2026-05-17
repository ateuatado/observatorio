<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalizacaoModel extends Model
{
    protected $table         = 'localizacoes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'logradouro', 'numero', 'bairro', 'zona_cidade',
        'municipio', 'estado', 'regiao_metropolitana',
        'tipo_local', 'descricao_local', 'latitude', 'longitude',
    ];
    protected $useTimestamps = true;

    /**
     * Lista de municípios distintos já cadastrados.
     */
    public function listaMunicipios(): array
    {
        return $this->db->table($this->table)
            ->select('municipio')
            ->groupBy('municipio')
            ->orderBy('municipio', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Salva ou encontra uma localização com base em municipio + bairro.
     * Retorna o ID da localização.
     */
    public function salvarOuEncontrar(array $dados): int
    {
        $existe = $this->where('municipio', $dados['municipio'])
            ->where('bairro', $dados['bairro'] ?? null)
            ->first();

        if ($existe) {
            return $existe['id'];
        }

        $this->insert($dados);
        return $this->insertID();
    }
}
