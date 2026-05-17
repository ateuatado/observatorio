<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * OvpTestCase
 *
 * Classe base para todos os testes do OVP que precisam de banco de dados.
 * Usa SQLite3 in-memory (configurado em Config\Database::$tests).
 *
 * Traits disponíveis:
 *   - DatabaseTestTrait  → $this->db, migrate, seed, refresh
 *   - ControllerTestTrait → testa controllers sem HTTP real
 *   - FeatureTestTrait   → testa rotas HTTP completas
 */
abstract class OvpTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * Executa as migrations SQLite antes do primeiro teste.
     * Usa a migration em tests/Support/Database/Migrations que
     * é compatível com SQLite3 (sem COMMENT, sem ENGINE=InnoDB, etc.)
     */
    protected $migrate     = true;
    protected $migrateOnce = false;  // recria o schema antes de cada teste para isolamento completo
    protected $refresh     = true;
    protected $seed        = '';

    /**
     * Aponta para as migrations compatíveis com SQLite3.
     * O CI4 vai procurar em tests/Support/Database/Migrations/
     */
    protected $namespace   = 'Tests\\Support';

    // =========================================================================
    // FACTORIES
    // =========================================================================

    /**
     * Insere uma localização mínima e retorna o ID.
     */
    protected function criarLocalizacao(array $override = []): int
    {
        $db = db_connect();
        $db->table('localizacoes')->insert(array_merge([
            'municipio'  => 'São Paulo',
            'estado'     => 'SP',
            'bairro'     => 'Centro',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $override));
        return (int) $db->insertID();
    }

    /**
     * Insere uma ocorrência mínima e retorna o ID.
     * Esta é a factory principal — usa a tabela 'ocorrencias'.
     */
    protected function criarOcorrencia(array $override = []): int
    {
        $locId = isset($override['localizacao_id'])
            ? $override['localizacao_id']
            : $this->criarLocalizacao();
        unset($override['localizacao_id']);

        $db = db_connect();
        $db->table('ocorrencias')->insert(array_merge([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5)),
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-15',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 1,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
            'cadastrado_por'     => null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ], $override));
        return (int) $db->insertID();
    }

    /**
     * Alias de criarOcorrencia() — mantido para compatibilidade com testes anteriores.
     */
    protected function criarCaso(array $override = []): int
    {
        return $this->criarOcorrencia($override);
    }

    /**
     * Insere uma vítima e retorna o ID.
     */
    protected function criarVitima(array $override = []): int
    {
        $db = db_connect();
        $db->table('vitimas')->insert(array_merge([
            'nome'          => 'João da Silva',
            'idade_aparente'=> 30,
            'sexo'          => 'masculino',
            'raca_cor'      => 'preta',
            'menor_de_idade'=> 0,
            'gestante'      => 0,
            'pcd'           => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ], $override));
        return (int) $db->insertID();
    }

    /**
     * Vincula uma vítima a uma ocorrência na tabela ocorrencia_vitima.
     */
    protected function vincularVitimaOcorrencia(int $ocorrenciaId, int $vitimaId, array $override = []): void
    {
        db_connect()->table('ocorrencia_vitima')->insert(array_merge([
            'ocorrencia_id' => $ocorrenciaId,
            'vitima_id'     => $vitimaId,
            'resultado'     => 'fatal',
            'identificada'  => 1,
        ], $override));
    }

    /**
     * Insere uma Ação de Segurança e retorna o ID.
     */
    protected function criarAcaoSeguranca(array $override = []): int
    {
        $db = db_connect();
        $db->table('acoes_seguranca')->insert(array_merge([
            'nome'              => 'Operação Teste',
            'tipo_agente'       => 'estatal',
            'data_inicio'       => '2024-01-01',
            'precisao_temporal' => 'aproximada',
            'status'            => 'confirmada',
            'visibilidade'      => 'restrita',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ], $override));
        return (int) $db->insertID();
    }

    /**
     * Cria o vínculo entre uma ocorrência e uma ação de segurança.
     */
    protected function criarVinculoOcorrenciaAcao(int $ocorrenciaId, int $acaoId, array $override = []): void
    {
        db_connect()->table('ocorrencia_acao')->insert(array_merge([
            'ocorrencia_id'  => $ocorrenciaId,
            'acao_id'        => $acaoId,
            'momento_vinculo'=> 'durante',
            'justificativa'  => 'Vínculo criado em teste.',
            'vinculado_em'   => date('Y-m-d H:i:s'),
        ], $override));
    }

    /**
     * Insere um estudo e retorna o ID.
     */
    protected function criarEstudo(array $override = []): int
    {
        $db = db_connect();
        $db->table('estudos')->insert(array_merge([
            'titulo'     => 'Violência Policial em SP: Um Estudo',
            'slug'       => 'violencia-policial-sp-estudo-' . uniqid(),
            'resumo'     => 'Resumo do estudo de teste.',
            'publicado'  => 1,
            'destaque'   => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $override));
        return (int) $db->insertID();
    }
}
