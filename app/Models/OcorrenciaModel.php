<?php

namespace App\Models;

use CodeIgniter\Model;

class OcorrenciaModel extends Model
{
    protected $table            = 'ocorrencias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'titulo', 'descricao', 'data_ocorrencia', 'hora_ocorrencia',
        'tipo_violencia', 'subtipo', 'local_descricao', 'bairro',
        'cidade', 'estado', 'latitude', 'longitude', 'fontes',
        'evidencias', 'status', 'prioridade', 'user_id', 'revisor_id',
        'revisado_em', 'publicado_em',
    ];

    protected $validationRules = [
        'titulo'         => 'required|min_length[5]|max_length[255]',
        'tipo_violencia' => 'required',
        'data_ocorrencia'=> 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'titulo' => [
            'required'   => 'O título é obrigatório.',
            'min_length' => 'O título deve ter pelo menos 5 caracteres.',
        ],
        'tipo_violencia' => [
            'required' => 'O tipo de violência é obrigatório.',
        ],
    ];

    public function getComVitimas(int $id): ?array
    {
        return $this->db->table('ocorrencias o')
            ->select('o.*, GROUP_CONCAT(v.nome SEPARATOR ", ") as nomes_vitimas')
            ->join('vitimas v', 'v.ocorrencia_id = o.id', 'left')
            ->where('o.id', $id)
            ->where('o.deleted_at IS NULL')
            ->groupBy('o.id')
            ->get()->getRowArray();
    }

    public function getPublicadas(int $limit = 10, int $offset = 0): array
    {
        return $this->where('status', 'publicado')
            ->orderBy('data_ocorrencia', 'DESC')
            ->findAll($limit, $offset);
    }

    public function countByStatus(): array
    {
        $results = $this->db->table('ocorrencias')
            ->select('status, COUNT(*) as total')
            ->whereNotIn('status', ['arquivado'])
            ->where('deleted_at IS NULL')
            ->groupBy('status')
            ->get()->getResultArray();

        $counts = ['rascunho' => 0, 'em_revisao' => 0, 'aprovado' => 0, 'publicado' => 0];
        foreach ($results as $r) {
            $counts[$r['status']] = (int)$r['total'];
        }
        return $counts;
    }

    public function countByTipo(): array
    {
        return $this->db->table('ocorrencias')
            ->select('tipo_violencia, COUNT(*) as total')
            ->where('status', 'publicado')
            ->where('deleted_at IS NULL')
            ->groupBy('tipo_violencia')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }

    public function countByMes(int $meses = 12): array
    {
        return $this->db->table('ocorrencias')
            ->select('DATE_FORMAT(data_ocorrencia, "%Y-%m") as mes, COUNT(*) as total')
            ->where('data_ocorrencia >=', date('Y-m-d', strtotime("-{$meses} months")))
            ->where('deleted_at IS NULL')
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get()->getResultArray();
    }
}
