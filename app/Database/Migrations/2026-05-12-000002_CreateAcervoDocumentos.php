<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria a tabela de indexação do acervo histórico de documentos PDF.
 *
 * A tabela funciona como um catálogo de todos os PDFs em arquivos/usb/,
 * com rastreamento de análise por IA e controle de importação para o banco principal.
 */
class CreateAcervoDocumentos extends Migration
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS acervo_documentos (
                id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                -- Localização física
                caminho_relativo    VARCHAR(1000) NOT NULL COMMENT 'Caminho relativo a ROOTPATH, ex: arquivos/usb/2018/ID.1.pdf',
                hash_sha256         CHAR(64)      NOT NULL COMMENT 'SHA-256 do arquivo — garante unicidade mesmo se renomeado',
                nome_arquivo        VARCHAR(500)  NOT NULL,
                tamanho_bytes       INT UNSIGNED  NULL,
                pasta_ano           SMALLINT      NULL COMMENT 'Ano extraído do caminho de pasta, ex: 2018',
                pasta_mes           TINYINT       NULL COMMENT 'Mês extraído do caminho, 1-12',

                -- Metadados extraídos automaticamente do nome do arquivo
                id_interno          VARCHAR(50)   NULL COMMENT 'Ex: ID.42.0 — identificador do arquivo original',
                veiculo_imprensa    VARCHAR(150)  NULL COMMENT 'Ex: G1, Brasil de Fato, Ponte Jornalismo',
                data_documento      DATE          NULL COMMENT 'Data extraída do nome ou conteúdo do PDF',

                -- Análise de conteúdo
                texto_extraido      LONGTEXT      NULL COMMENT 'Texto bruto extraído do PDF',
                resumo_ia           TEXT          NULL COMMENT 'Resumo gerado pela IA (ou heurística)',
                tipo_identificado   ENUM('caso','estudo','outro','indefinido') NOT NULL DEFAULT 'indefinido',
                dados_extraidos_ia  JSON          NULL COMMENT 'Campos estruturados extraídos: data, local, vítimas, etc.',
                ia_processado       TINYINT(1)    NOT NULL DEFAULT 0,
                ia_processado_em    DATETIME      NULL,

                -- Miniatura
                miniatura_path      VARCHAR(500)  NULL COMMENT 'Caminho relativo à miniatura gerada (dentro de writable/thumbs/)',

                -- Controle de importação (anti-duplicata)
                status              ENUM('pendente','auditando','importado','descartado') NOT NULL DEFAULT 'pendente',
                importado_em        DATETIME      NULL,
                importado_por       INT UNSIGNED  NULL,
                caso_id             INT UNSIGNED  NULL REFERENCES casos(id) ON DELETE SET NULL,
                estudo_id           INT UNSIGNED  NULL REFERENCES estudos(id) ON DELETE SET NULL,
                notas_auditor       TEXT          NULL,

                -- Timestamps
                indexado_em         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at          DATETIME      NULL ON UPDATE CURRENT_TIMESTAMP,

                UNIQUE KEY uq_hash        (hash_sha256),
                UNIQUE KEY uq_caminho     (caminho_relativo(768))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Índices para filtros da interface de auditoria
        $this->db->query('CREATE INDEX idx_acervo_status    ON acervo_documentos (status)');
        $this->db->query('CREATE INDEX idx_acervo_tipo      ON acervo_documentos (tipo_identificado)');
        $this->db->query('CREATE INDEX idx_acervo_ano       ON acervo_documentos (pasta_ano)');
        $this->db->query('CREATE INDEX idx_acervo_caso      ON acervo_documentos (caso_id)');
        $this->db->query('CREATE INDEX idx_acervo_ia        ON acervo_documentos (ia_processado)');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS acervo_documentos');
    }
}
