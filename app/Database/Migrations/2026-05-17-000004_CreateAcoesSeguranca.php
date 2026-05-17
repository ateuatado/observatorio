<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria as tabelas da entidade Ação de Segurança.
 *
 * acoes_seguranca  — entidade principal (operações, ações armadas, eventos comunitários)
 * ocorrencia_acao  — pivot many-to-many: ocorrencias <-> acoes_seguranca
 *                    com metadados de curadoria (quem, quando, momento do vínculo)
 */
class CreateAcoesSeguranca extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // AÇÕES DE SEGURANÇA
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS acoes_seguranca (
                id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome                 VARCHAR(200) NULL            COMMENT "NULL quando não nomeada",
                tipo_agente          VARCHAR(30)  NOT NULL        COMMENT "estatal,paraestatal,milicia,comunitario,indefinido",
                data_inicio          DATE         NULL,
                data_fim             DATE         NULL            COMMENT "NULL = em curso ou data desconhecida",
                precisao_temporal    VARCHAR(20)  NOT NULL DEFAULT "aproximada" COMMENT "exata,aproximada,estimada",
                motivacao_declarada  TEXT         NULL            COMMENT "Justificativa pública ou oficial da ação",
                motivacao_inferida   TEXT         NULL            COMMENT "Análise dos pesquisadores/curadores",
                descricao            TEXT         NULL            COMMENT "Narrativa histórica livre",
                status               VARCHAR(20)  NOT NULL DEFAULT "em_analise" COMMENT "em_analise,confirmada,arquivada",
                visibilidade         VARCHAR(20)  NOT NULL DEFAULT "restrita"   COMMENT "publica,restrita,sigilosa",
                cadastrado_por       INT UNSIGNED NULL,
                created_at           DATETIME     NULL,
                updated_at           DATETIME     NULL,
                deleted_at           DATETIME     NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // PIVOT: ocorrencias <-> acoes_seguranca
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS ocorrencia_acao (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ocorrencia_id   INT UNSIGNED NOT NULL,
                acao_id         INT UNSIGNED NOT NULL,
                momento_vinculo VARCHAR(20)  NULL    COMMENT "antes,durante,depois",
                vinculado_por   INT UNSIGNED NULL    COMMENT "ID do usuário curador que registrou o vínculo",
                vinculado_em    DATETIME     NULL,
                justificativa   TEXT         NULL    COMMENT "Fundamentação analítica do vínculo",
                UNIQUE KEY uq_ocorrencia_acao (ocorrencia_id, acao_id),
                FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
                FOREIGN KEY (acao_id)       REFERENCES acoes_seguranca(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS ocorrencia_acao');
        $this->db->query('DROP TABLE IF EXISTS acoes_seguranca');
    }
}
