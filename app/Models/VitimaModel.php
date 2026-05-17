<?php

namespace App\Models;

use CodeIgniter\Model;

class VitimaModel extends Model
{
    protected $table         = 'vitimas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nome',
        'data_nascimento',
        'idade_aparente',
        'sexo',
        'raca_cor',
        'profissao',
        'condicao_juridica',
        'menor_de_idade',
        'gestante',
        'pcd',
        'antecedentes_versao_policial',
        'observacoes',
    ];

    protected $validationRules = [
        'sexo'     => 'permit_empty|in_list[masculino,feminino,nao_binario,nao_informado]',
        'raca_cor' => 'permit_empty|in_list[branca,preta,parda,amarela,indigena,nao_informada]',
    ];

    /**
     * Busca vítimas com contagem de casos vinculados.
     */
    public function listarComCasos(int $limit = 20, int $offset = 0, string $busca = ''): array
    {
        $builder = $this->db->table('vitimas v')
            ->select('v.*, COUNT(cv.ocorrencia_id) AS total_casos')
            ->join('ocorrencia_vitima cv', 'cv.vitima_id = v.id', 'left')
            ->groupBy('v.id')
            ->orderBy('v.created_at', 'DESC')
            ->limit($limit, $offset);

        if ($busca) {
            $builder->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.profissao', $busca)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Contagem total de vítimas (com busca opcional).
     */
    public function contarTotal(string $busca = ''): int
    {
        $builder = $this->db->table('vitimas v');
        if ($busca) {
            $builder->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.profissao', $busca)
                ->groupEnd();
        }
        return (int) $builder->countAllResults();
    }
}
