<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para o catálogo do acervo histórico de documentos PDF.
 *
 * Gerencia o ciclo de vida completo: indexação → análise → auditoria → importação.
 */
class AcervoDocumentoModel extends Model
{
    protected $table         = 'acervo_documentos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // gerenciamos indexado_em / updated_at manualmente

    protected $allowedFields = [
        'caminho_relativo', 'hash_sha256', 'nome_arquivo', 'tamanho_bytes',
        'pasta_ano', 'pasta_mes', 'id_interno', 'veiculo_imprensa', 'data_documento',
        'texto_extraido', 'resumo_ia', 'tipo_identificado', 'dados_extraidos_ia',
        'ia_processado', 'ia_processado_em', 'miniatura_path',
        'status', 'importado_em', 'importado_por', 'caso_id', 'estudo_id',
        'notas_auditor', 'indexado_em', 'updated_at',
    ];

    // -------------------------------------------------------------------------
    // Consultas de listagem e filtros
    // -------------------------------------------------------------------------

    /**
     * Retorna documentos com filtros opcionais para a interface de auditoria.
     *
     * @param array{
     *   ano?: int,
     *   mes?: int,
     *   status?: string,
     *   tipo?: string,
     *   q?: string,
     *   page?: int,
     *   per_page?: int
     * } $filtros
     */
    public function listarComFiltros(array $filtros = []): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, nome_arquivo, caminho_relativo, pasta_ano, pasta_mes,
                          id_interno, veiculo_imprensa, data_documento,
                          tipo_identificado, status, ia_processado,
                          tamanho_bytes, caso_id, estudo_id, resumo_ia, indexado_em');

        if (!empty($filtros['ano'])) {
            $builder->where('pasta_ano', (int)$filtros['ano']);
        }
        if (!empty($filtros['mes'])) {
            $builder->where('pasta_mes', (int)$filtros['mes']);
        }
        if (!empty($filtros['status'])) {
            $builder->where('status', $filtros['status']);
        }
        if (!empty($filtros['tipo'])) {
            $builder->where('tipo_identificado', $filtros['tipo']);
        }
        if (!empty($filtros['q'])) {
            $q = $filtros['q'];
            $builder->groupStart()
                ->like('nome_arquivo', $q)
                ->orLike('resumo_ia', $q)
                ->orLike('veiculo_imprensa', $q)
                ->groupEnd();
        }
        if (!empty($filtros['ia_processado'])) {
            $builder->where('ia_processado', (int)$filtros['ia_processado']);
        }

        $builder->orderBy('pasta_ano', 'DESC')
                ->orderBy('pasta_mes', 'DESC')
                ->orderBy('nome_arquivo', 'ASC');

        $perPage = (int)($filtros['per_page'] ?? 50);
        $page    = max(1, (int)($filtros['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        return $builder->limit($perPage, $offset)->get()->getResultArray();
    }

    /**
     * Conta total de documentos com os mesmos filtros (para paginação).
     */
    public function contarComFiltros(array $filtros = []): int
    {
        $builder = $this->db->table($this->table);

        if (!empty($filtros['ano']))    $builder->where('pasta_ano', (int)$filtros['ano']);
        if (!empty($filtros['mes']))    $builder->where('pasta_mes', (int)$filtros['mes']);
        if (!empty($filtros['status'])) $builder->where('status', $filtros['status']);
        if (!empty($filtros['tipo']))   $builder->where('tipo_identificado', $filtros['tipo']);
        if (!empty($filtros['q'])) {
            $q = $filtros['q'];
            $builder->groupStart()
                ->like('nome_arquivo', $q)
                ->orLike('resumo_ia', $q)
                ->groupEnd();
        }

        return (int)$builder->countAllResults();
    }

    /**
     * Retorna contagens agrupadas por status para o dashboard de progresso.
     *
     * @return array{pendente: int, auditando: int, importado: int, descartado: int, total: int}
     */
    public function contarPorStatus(): array
    {
        $rows = $this->db->table($this->table)
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()->getResultArray();

        $counts = ['pendente' => 0, 'auditando' => 0, 'importado' => 0, 'descartado' => 0];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int)$row['total'];
        }
        $counts['total'] = array_sum($counts);
        return $counts;
    }

    /**
     * Retorna anos distintos presentes no acervo (para o filtro de ano).
     */
    public function anosDisponiveis(): array
    {
        return $this->db->table($this->table)
            ->select('pasta_ano')
            ->where('pasta_ano IS NOT NULL', null, false)
            ->groupBy('pasta_ano')
            ->orderBy('pasta_ano', 'ASC')
            ->get()->getResultArray();
    }

    // -------------------------------------------------------------------------
    // Controle de importação e status
    // -------------------------------------------------------------------------

    /**
     * Verifica se um arquivo já foi indexado pelo hash.
     */
    public function existePorHash(string $hash): bool
    {
        return $this->where('hash_sha256', $hash)->countAllResults() > 0;
    }

    /**
     * Verifica se um arquivo já foi indexado pelo caminho relativo.
     */
    public function existePorCaminho(string $caminho): bool
    {
        return $this->where('caminho_relativo', $caminho)->countAllResults() > 0;
    }

    /**
     * Marca um documento como em auditoria (soft-lock para evitar importação dupla simultânea).
     */
    public function iniciarAuditoria(int $id, int $userId): bool
    {
        return $this->update($id, [
            'status'        => 'auditando',
            'importado_por' => $userId,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Marca documento como importado e registra o vínculo com caso ou estudo.
     */
    public function marcarComoImportado(int $id, string $tipo, int $referenciaId, int $userId): bool
    {
        $dados = [
            'status'       => 'importado',
            'importado_em' => date('Y-m-d H:i:s'),
            'importado_por'=> $userId,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($tipo === 'caso') {
            $dados['caso_id'] = $referenciaId;
        } elseif ($tipo === 'estudo') {
            $dados['estudo_id'] = $referenciaId;
        }

        return $this->update($id, $dados);
    }

    /**
     * Marca documento como descartado com nota do auditor.
     */
    public function descartar(int $id, string $nota = '', int $userId = 0): bool
    {
        return $this->update($id, [
            'status'        => 'descartado',
            'notas_auditor' => $nota,
            'importado_por' => $userId ?: null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Salva resultado da análise por IA/heurística.
     */
    public function salvarAnalise(int $id, array $analise): bool
    {
        return $this->update($id, [
            'resumo_ia'          => $analise['resumo']          ?? null,
            'tipo_identificado'  => $analise['tipo']            ?? 'indefinido',
            'dados_extraidos_ia' => json_encode($analise['campos'] ?? []),
            'ia_processado'      => 1,
            'ia_processado_em'   => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Salva texto extraído do PDF.
     */
    public function salvarTexto(int $id, string $texto): bool
    {
        return $this->update($id, [
            'texto_extraido' => $texto,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Salva o caminho da miniatura gerada.
     */
    public function salvarMiniatura(int $id, string $path): bool
    {
        return $this->update($id, [
            'miniatura_path' => $path,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
