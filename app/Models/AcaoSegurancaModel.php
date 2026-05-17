<?php

namespace App\Models;

use CodeIgniter\Model;

class AcaoSegurancaModel extends Model
{
    protected $table            = 'acoes_seguranca';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'nome',
        'tipo_agente',
        'data_inicio',
        'data_fim',
        'precisao_temporal',
        'motivacao_declarada',
        'motivacao_inferida',
        'descricao',
        'status',
        'visibilidade',
        'cadastrado_por',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'tipo_agente'       => 'required|in_list[estatal,paraestatal,milicia,comunitario,indefinido]',
        'precisao_temporal' => 'required|in_list[exata,aproximada,estimada]',
        'status'            => 'required|in_list[em_analise,confirmada,arquivada]',
        'visibilidade'      => 'required|in_list[publica,restrita,sigilosa]',
    ];

    // ----------------------------------------------------------------
    // Métodos analíticos
    // ----------------------------------------------------------------

    /**
     * Lista as ocorrências vinculadas a uma Ação de Segurança,
     * com metadados do vínculo (momento, curador, justificativa).
     */
    public function ocorrenciasVinculadas(int $acaoId): array
    {
        return $this->db->table('ocorrencia_acao oa')
            ->select('oa.momento_vinculo, oa.justificativa, oa.vinculado_em, o.*,
                      l.municipio, l.bairro')
            ->join('ocorrencias o', 'o.id = oa.ocorrencia_id', 'left')
            ->join('localizacoes l', 'l.id = o.localizacao_id', 'left')
            ->where('oa.acao_id', $acaoId)
            ->orderBy('o.data_fato', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Verifica se o vínculo ocorrencia↔ação já existe.
     */
    public function vinculoExiste(int $ocorrenciaId, int $acaoId): bool
    {
        return $this->db->table('ocorrencia_acao')
            ->where('ocorrencia_id', $ocorrenciaId)
            ->where('acao_id', $acaoId)
            ->countAllResults() > 0;
    }

    /**
     * Cria o vínculo entre uma ocorrência e uma ação.
     */
    public function vincularOcorrencia(int $ocorrenciaId, int $acaoId, array $dados = []): bool
    {
        if ($this->vinculoExiste($ocorrenciaId, $acaoId)) {
            return false;
        }

        return $this->db->table('ocorrencia_acao')->insert([
            'ocorrencia_id'  => $ocorrenciaId,
            'acao_id'        => $acaoId,
            'momento_vinculo'=> $dados['momento_vinculo'] ?? null,
            'justificativa'  => $dados['justificativa'] ?? null,
            'vinculado_por'  => $dados['vinculado_por'] ?? null,
            'vinculado_em'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove o vínculo entre uma ocorrência e uma ação.
     */
    public function desvincularOcorrencia(int $ocorrenciaId, int $acaoId): bool
    {
        return $this->db->table('ocorrencia_acao')
            ->where('ocorrencia_id', $ocorrenciaId)
            ->where('acao_id', $acaoId)
            ->delete();
    }
}
