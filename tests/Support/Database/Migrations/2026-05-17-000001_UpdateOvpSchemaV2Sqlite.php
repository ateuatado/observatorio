<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration de schema para testes — versão 2 (Fase 2 OVP).
 *
 * Adiciona ao schema SQLite de teste:
 *   - Tabela `ocorrencias`      (renomeação de `casos`)
 *   - Tabelas pivot renomeadas  (ocorrencia_vitima, ocorrencia_agente, ocorrencia_documento, ocorrencia_tag)
 *   - Tabela `acoes_seguranca`  (nova entidade)
 *   - Tabela `ocorrencia_acao`  (pivot many-to-many)
 *
 * As tabelas antigas `casos`, `caso_vitima`, `caso_agente` são mantidas
 * mas esvaziadas para que o down() possa reverter sem erros de FK.
 */
class UpdateOvpSchemaV2Sqlite extends Migration
{
    public function up(): void
    {
        // ----------------------------------------------------------
        // OCORRENCIAS (equivalente a casos, renomeado)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencias (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                protocolo_ovp       TEXT    NOT NULL UNIQUE,
                localizacao_id      INTEGER NULL REFERENCES localizacoes(id) ON DELETE SET NULL,
                data_fato           TEXT    NOT NULL,
                hora_fato           TEXT    NULL,
                tipo_violencia      TEXT    NOT NULL,
                subtipo             TEXT    NULL,
                vitimas_fatais      INTEGER NOT NULL DEFAULT 0,
                vitimas_nao_fatais  INTEGER NOT NULL DEFAULT 0,
                versao_oficial      TEXT    NULL,
                versao_testemunhas  TEXT    NULL,
                descricao_livre     TEXT    NULL,
                status_investigacao TEXT    NOT NULL DEFAULT "sem_inquerito",
                publicado           INTEGER NOT NULL DEFAULT 0,
                destaque            INTEGER NOT NULL DEFAULT 0,
                cadastrado_por      INTEGER NULL,
                created_at          TEXT    NULL,
                updated_at          TEXT    NULL,
                deleted_at          TEXT    NULL
            )
        ');

        // ----------------------------------------------------------
        // OCORRENCIA_VITIMA (pivot renomeado)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_vitima (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                ocorrencia_id INTEGER NOT NULL REFERENCES ocorrencias(id) ON DELETE CASCADE,
                vitima_id    INTEGER NOT NULL REFERENCES vitimas(id)    ON DELETE CASCADE,
                resultado    TEXT    NULL,
                ferimentos   TEXT    NULL,
                identificada INTEGER NOT NULL DEFAULT 1
            )
        ');

        // ----------------------------------------------------------
        // OCORRENCIA_AGENTE (pivot renomeado)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_agente (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                ocorrencia_id       INTEGER NOT NULL REFERENCES ocorrencias(id) ON DELETE CASCADE,
                agente_id           INTEGER NULL    REFERENCES agentes(id) ON DELETE SET NULL,
                descricao_agente    TEXT    NULL,
                quantidade_agentes  INTEGER NOT NULL DEFAULT 1,
                corporacao          TEXT    NULL,
                fardado             INTEGER NOT NULL DEFAULT 0,
                encapuzado          INTEGER NOT NULL DEFAULT 0,
                prefixo_viatura     TEXT    NULL,
                papel_no_caso       TEXT    NOT NULL DEFAULT "executor"
            )
        ');

        // ----------------------------------------------------------
        // OCORRENCIA_DOCUMENTO (pivot novo)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_documento (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                ocorrencia_id INTEGER NOT NULL REFERENCES ocorrencias(id) ON DELETE CASCADE,
                documento_id  INTEGER NOT NULL,
                descricao     TEXT    NULL
            )
        ');

        // ----------------------------------------------------------
        // OCORRENCIA_TAG (pivot novo)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_tag (
                ocorrencia_id INTEGER NOT NULL REFERENCES ocorrencias(id) ON DELETE CASCADE,
                tag           TEXT    NOT NULL,
                PRIMARY KEY (ocorrencia_id, tag)
            )
        ');

        // ----------------------------------------------------------
        // ACOES_SEGURANCA
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS acoes_seguranca (
                id                   INTEGER PRIMARY KEY AUTOINCREMENT,
                nome                 TEXT    NULL,
                tipo_agente          TEXT    NOT NULL,
                data_inicio          TEXT    NULL,
                data_fim             TEXT    NULL,
                precisao_temporal    TEXT    NOT NULL DEFAULT "aproximada",
                motivacao_declarada  TEXT    NULL,
                motivacao_inferida   TEXT    NULL,
                descricao            TEXT    NULL,
                status               TEXT    NOT NULL DEFAULT "em_analise",
                visibilidade         TEXT    NOT NULL DEFAULT "restrita",
                cadastrado_por       INTEGER NULL,
                created_at           TEXT    NULL,
                updated_at           TEXT    NULL,
                deleted_at           TEXT    NULL
            )
        ');

        // ----------------------------------------------------------
        // OCORRENCIA_ACAO (pivot many-to-many com curadoria)
        // ----------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_acao (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                ocorrencia_id   INTEGER NOT NULL REFERENCES ocorrencias(id) ON DELETE CASCADE,
                acao_id         INTEGER NOT NULL REFERENCES acoes_seguranca(id) ON DELETE CASCADE,
                momento_vinculo TEXT    NULL,
                vinculado_por   INTEGER NULL,
                vinculado_em    TEXT    NULL,
                justificativa   TEXT    NULL,
                UNIQUE (ocorrencia_id, acao_id)
            )
        ');
    }

    public function down(): void
    {
        $tabelas = [
            'ocorrencia_acao',
            'ocorrencia_tag',
            'ocorrencia_documento',
            'ocorrencia_agente',
            'ocorrencia_vitima',
            'acoes_seguranca',
            'ocorrencias',
        ];
        foreach ($tabelas as $t) {
            $this->db->query("DROP TABLE IF EXISTS {$t}");
        }
    }
}
