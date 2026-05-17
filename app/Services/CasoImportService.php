<?php

namespace App\Services;

use App\Models\AcervoDocumentoModel;
use App\Models\CasoModel;
use App\Models\LocalizacaoModel;
use App\Models\VitimaModel;

/**
 * Serviço de importação transacional de um documento auditado para o banco principal.
 *
 * Garante que:
 *  1. A transação é atômica (rollback em falha)
 *  2. O documento é marcado como importado com referência ao caso criado
 *  3. Não é possível importar o mesmo documento duas vezes
 */
class CasoImportService
{
    private AcervoDocumentoModel $acervoModel;
    private CasoModel            $casoModel;
    private LocalizacaoModel     $locModel;
    private VitimaModel          $vitimaModel;

    public function __construct()
    {
        $this->acervoModel  = new AcervoDocumentoModel();
        $this->casoModel    = new CasoModel();
        $this->locModel     = new LocalizacaoModel();
        $this->vitimaModel  = new VitimaModel();
    }

    /**
     * Importa um documento como Caso.
     *
     * @param int   $documentoId  ID em acervo_documentos
     * @param array $dadosAuditados Dados corrigidos pelo pesquisador
     * @param int   $userId        ID do usuário logado
     *
     * @return array{sucesso: bool, caso_id: int|null, erro: string|null}
     */
    public function importarComoCaso(int $documentoId, array $dadosAuditados, int $userId): array
    {
        $doc = $this->acervoModel->find($documentoId);

        // Salvaguarda anti-duplicata
        if (!$doc) {
            return ['sucesso' => false, 'caso_id' => null, 'erro' => 'Documento não encontrado.'];
        }
        if ($doc['status'] === 'importado') {
            return [
                'sucesso' => false,
                'caso_id' => $doc['caso_id'],
                'erro'    => 'Este documento já foi importado como Caso #' . $doc['caso_id'] . '.',
            ];
        }

        $db = db_connect();
        $db->transStart();

        try {
            // 1. Localização
            $locId = $this->locModel->salvarOuEncontrar([
                'municipio'   => $dadosAuditados['municipio']   ?? 'Não informado',
                'bairro'      => $dadosAuditados['bairro']      ?? null,
                'zona_cidade' => $dadosAuditados['zona_cidade'] ?? null,
                'estado'      => $dadosAuditados['estado']      ?? 'SP',
                'tipo_local'  => $dadosAuditados['tipo_local']  ?? null,
                'logradouro'  => $dadosAuditados['logradouro']  ?? null,
            ]);

            // 2. Gerar protocolo OVP
            $protocolo = $this->casoModel->gerarProtocolo();

            // 3. Criar o caso
            $casoId = $this->casoModel->insert([
                'protocolo_ovp'      => $protocolo,
                'localizacao_id'     => $locId,
                'data_fato'          => $dadosAuditados['data_fato']          ?? null,
                'hora_fato'          => $dadosAuditados['hora_fato']          ?? null,
                'tipo_violencia'     => $dadosAuditados['tipo_violencia']     ?? 'execucao',
                'subtipo'            => $dadosAuditados['subtipo']            ?? null,
                'vitimas_fatais'     => (int)($dadosAuditados['vitimas_fatais']     ?? 0),
                'vitimas_nao_fatais' => (int)($dadosAuditados['vitimas_nao_fatais'] ?? 0),
                'versao_oficial'     => $dadosAuditados['versao_oficial']     ?? null,
                'versao_testemunhas' => $dadosAuditados['versao_testemunhas'] ?? null,
                'descricao_livre'    => $dadosAuditados['descricao_livre']    ?? null,
                'status_investigacao'=> $dadosAuditados['status_investigacao']?? 'sem_inquerito',
                'publicado'          => 0, // sempre entra como rascunho
                'destaque'           => 0,
                'cadastrado_por'     => $userId,
            ]);

            if (!$casoId) {
                throw new \RuntimeException('Falha ao criar registro de caso.');
            }

            // 4. Vítimas (opcional — array de vítimas do formulário)
            $vitimas = $dadosAuditados['vitimas'] ?? [];
            foreach ($vitimas as $v) {
                if (empty($v['nome']) && empty($v['sexo'])) continue;

                $vitimaId = $this->vitimaModel->insert([
                    'nome'              => $v['nome']           ?? null,
                    'idade_aparente'    => $v['idade_aparente']  ?? null,
                    'sexo'              => $v['sexo']            ?? null,
                    'raca_cor'          => $v['raca_cor']        ?? null,
                    'condicao_juridica' => $v['condicao_juridica'] ?? null,
                    'menor_de_idade'    => (int)($v['menor_de_idade'] ?? 0),
                    'gestante'          => (int)($v['gestante']       ?? 0),
                    'pcd'               => (int)($v['pcd']            ?? 0),
                ]);

                if ($vitimaId) {
                    $db->table('caso_vitima')->insert([
                        'caso_id'      => $casoId,
                        'vitima_id'    => $vitimaId,
                        'resultado'    => $v['resultado']   ?? null,
                        'ferimentos'   => $v['ferimentos']  ?? null,
                        'identificada' => (int)($v['identificada'] ?? 1),
                    ]);
                }
            }

            // 5. Agentes (opcional)
            $agentes = $dadosAuditados['agentes'] ?? [];
            foreach ($agentes as $a) {
                $db->table('caso_agente')->insert([
                    'caso_id'            => $casoId,
                    'agente_id'          => null,
                    'descricao_agente'   => $a['descricao_agente']   ?? null,
                    'quantidade_agentes' => (int)($a['quantidade_agentes'] ?? 1),
                    'corporacao'         => $a['corporacao']          ?? null,
                    'fardado'            => (int)($a['fardado']       ?? 0),
                    'encapuzado'         => (int)($a['encapuzado']    ?? 0),
                    'papel_no_caso'      => $a['papel_no_caso']       ?? 'executor',
                ]);
            }

            // 6. Vincular o documento ao caso criado
            $db->table('documentos')->insert([
                'caso_id'      => $casoId,
                'nome_arquivo' => $doc['nome_arquivo'],
                'tipo'         => 'acervo_historico',
                'caminho'      => $doc['caminho_relativo'],
                'enviado_por'  => $userId,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transação falhou durante o commit.');
            }

            // 7. Marcar documento como importado (fora da transação para não reverter em erro de auditoria)
            $this->acervoModel->marcarComoImportado($documentoId, 'caso', $casoId, $userId);
            $this->acervoModel->update($documentoId, [
                'notas_auditor' => $dadosAuditados['notas_auditor'] ?? null,
            ]);

            return ['sucesso' => true, 'caso_id' => $casoId, 'erro' => null];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', "CasoImportService: {$e->getMessage()}");
            return ['sucesso' => false, 'caso_id' => null, 'erro' => $e->getMessage()];
        }
    }
}
