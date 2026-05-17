<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration de schema para testes com SQLite3 in-memory.
 *
 * Esta migration replica o schema de produção (CreateOvpSchema)
 * removendo sintaxe específica do MySQL:
 *   - ENGINE=InnoDB → removido
 *   - COMMENT "..." inline → removido
 *   - AUTO_INCREMENT → substituído por AUTOINCREMENT (SQLite)
 *   - INT UNSIGNED → INTEGER (SQLite aceita qualquer INT para rowid)
 *   - DECIMAL(10,7) → REAL
 *   - TINYINT(1) → INTEGER
 *   - TEXT → TEXT (compatível)
 *   - VARCHAR → TEXT (SQLite trata todos como TEXT)
 */
class CreateOvpSchemaSqlite extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // LOCALIZACOES
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS localizacoes (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                logradouro          TEXT    NULL,
                numero              TEXT    NULL,
                bairro              TEXT    NULL,
                zona_cidade         TEXT    NULL,
                municipio           TEXT    NOT NULL,
                estado              TEXT    NOT NULL DEFAULT "SP",
                regiao_metropolitana TEXT   NULL,
                tipo_local          TEXT    NULL,
                descricao_local     TEXT    NULL,
                latitude            REAL    NULL,
                longitude           REAL    NULL,
                created_at          TEXT    NULL,
                updated_at          TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // CASOS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS casos (
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

        // -------------------------------------------------------
        // VITIMAS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS vitimas (
                id                          INTEGER PRIMARY KEY AUTOINCREMENT,
                nome                        TEXT    NULL,
                data_nascimento             TEXT    NULL,
                idade_aparente              INTEGER NULL,
                sexo                        TEXT    NULL,
                raca_cor                    TEXT    NULL,
                profissao                   TEXT    NULL,
                condicao_juridica           TEXT    NULL,
                menor_de_idade              INTEGER NOT NULL DEFAULT 0,
                gestante                    INTEGER NOT NULL DEFAULT 0,
                pcd                         INTEGER NOT NULL DEFAULT 0,
                antecedentes_versao_policial TEXT   NULL,
                observacoes                 TEXT    NULL,
                created_at                  TEXT    NULL,
                updated_at                  TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // CASO_VITIMA (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_vitima (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                caso_id     INTEGER NOT NULL REFERENCES casos(id) ON DELETE CASCADE,
                vitima_id   INTEGER NOT NULL REFERENCES vitimas(id) ON DELETE CASCADE,
                resultado   TEXT    NULL,
                ferimentos  TEXT    NULL,
                identificada INTEGER NOT NULL DEFAULT 1
            )
        ');

        // -------------------------------------------------------
        // AGENTES
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS agentes (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                nome       TEXT    NULL,
                matricula  TEXT    NULL,
                corporacao TEXT    NULL,
                created_at TEXT    NULL,
                updated_at TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // CASO_AGENTE (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_agente (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                caso_id             INTEGER NOT NULL REFERENCES casos(id) ON DELETE CASCADE,
                agente_id           INTEGER NULL REFERENCES agentes(id) ON DELETE SET NULL,
                descricao_agente    TEXT    NULL,
                quantidade_agentes  INTEGER NOT NULL DEFAULT 1,
                corporacao          TEXT    NULL,
                fardado             INTEGER NOT NULL DEFAULT 0,
                encapuzado          INTEGER NOT NULL DEFAULT 0,
                prefixo_viatura     TEXT    NULL,
                papel_no_caso       TEXT    NOT NULL DEFAULT "executor"
            )
        ');

        // -------------------------------------------------------
        // ESTUDOS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS estudos (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                titulo      TEXT    NOT NULL,
                slug        TEXT    NOT NULL UNIQUE,
                resumo      TEXT    NULL,
                conteudo    TEXT    NULL,
                autores     TEXT    NULL,
                publicado   INTEGER NOT NULL DEFAULT 0,
                destaque    INTEGER NOT NULL DEFAULT 0,
                arquivo_pdf TEXT    NULL,
                created_at  TEXT    NULL,
                updated_at  TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // DOCUMENTOS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS documentos (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                caso_id      INTEGER NULL REFERENCES casos(id) ON DELETE SET NULL,
                nome_arquivo TEXT    NOT NULL,
                tipo         TEXT    NULL,
                mime_type    TEXT    NULL,
                tamanho_bytes INTEGER NULL,
                caminho      TEXT    NOT NULL,
                enviado_por  INTEGER NULL,
                created_at   TEXT    NULL,
                updated_at   TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // USERS (Shield mínimo para FKs)
        // -------------------------------------------------------
        if (! $this->db->tableExists('users')) {
            $this->db->query('
                CREATE TABLE IF NOT EXISTS users (
                    id         INTEGER PRIMARY KEY AUTOINCREMENT,
                    username   TEXT    NULL UNIQUE,
                    status     TEXT    NULL,
                    status_message TEXT NULL,
                    active     INTEGER NOT NULL DEFAULT 0,
                    last_active TEXT   NULL,
                    created_at TEXT    NULL,
                    updated_at TEXT    NULL,
                    deleted_at TEXT    NULL
                )
            ');
        }

        // -------------------------------------------------------
        // SHIELD — auth_identities (email/senha)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_identities (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id      INTEGER NOT NULL,
                type         TEXT    NOT NULL,
                name         TEXT    NULL,
                secret       TEXT    NOT NULL,
                secret2      TEXT    NULL,
                expires      TEXT    NULL,
                extra        TEXT    NULL,
                force_reset  INTEGER NOT NULL DEFAULT 0,
                last_used_at TEXT    NULL,
                created_at   TEXT    NULL,
                updated_at   TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // SHIELD — auth_logins
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_logins (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT    NULL,
                user_agent TEXT    NULL,
                id_type    TEXT    NOT NULL,
                identifier TEXT    NOT NULL,
                user_id    INTEGER NULL,
                date       TEXT    NOT NULL,
                success    INTEGER NOT NULL
            )
        ');

        // -------------------------------------------------------
        // SHIELD — auth_token_logins
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_token_logins (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT    NULL,
                user_agent TEXT    NULL,
                id_type    TEXT    NOT NULL,
                identifier TEXT    NOT NULL,
                user_id    INTEGER NULL,
                date       TEXT    NOT NULL,
                success    INTEGER NOT NULL
            )
        ');

        // -------------------------------------------------------
        // SHIELD — auth_remember_tokens
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_remember_tokens (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                selector   TEXT    NOT NULL UNIQUE,
                hashedValidator TEXT NOT NULL,
                user_id    INTEGER NOT NULL,
                expires    TEXT    NOT NULL,
                created_at TEXT    NULL,
                updated_at TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // SHIELD — auth_groups_users
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_groups_users (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id    INTEGER NOT NULL,
                "group"    TEXT    NOT NULL,
                created_at TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // SHIELD — auth_permissions_users
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS auth_permissions_users (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id    INTEGER NOT NULL,
                permission TEXT    NOT NULL,
                created_at TEXT    NULL
            )
        ');

        // -------------------------------------------------------
        // SETTINGS (codeigniter4/settings)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS settings (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                class      TEXT    NOT NULL,
                key        TEXT    NOT NULL,
                type       TEXT    NOT NULL DEFAULT "string",
                value      TEXT    NULL,
                context    TEXT    NULL,
                created_at TEXT    NULL,
                updated_at TEXT    NULL,
                UNIQUE (class, key, context)
            )
        ');

        // -------------------------------------------------------
        // ACERVO_DOCUMENTOS (módulo Auditoria Histórica)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS acervo_documentos (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                caminho_relativo    TEXT    NOT NULL UNIQUE,
                hash_sha256         TEXT    NOT NULL UNIQUE,
                nome_arquivo        TEXT    NOT NULL,
                tamanho_bytes       INTEGER NULL,
                pasta_ano           INTEGER NULL,
                pasta_mes           INTEGER NULL,
                id_interno          TEXT    NULL,
                veiculo_imprensa    TEXT    NULL,
                data_documento      TEXT    NULL,
                texto_extraido      TEXT    NULL,
                resumo_ia           TEXT    NULL,
                tipo_identificado   TEXT    NOT NULL DEFAULT "indefinido",
                dados_extraidos_ia  TEXT    NULL,
                ia_processado       INTEGER NOT NULL DEFAULT 0,
                ia_processado_em    TEXT    NULL,
                miniatura_path      TEXT    NULL,
                status              TEXT    NOT NULL DEFAULT "pendente",
                importado_em        TEXT    NULL,
                importado_por       INTEGER NULL,
                caso_id             INTEGER NULL REFERENCES casos(id) ON DELETE SET NULL,
                estudo_id           INTEGER NULL REFERENCES estudos(id) ON DELETE SET NULL,
                notas_auditor       TEXT    NULL,
                indexado_em         TEXT    NULL,
                updated_at          TEXT    NULL
            )
        ');

    }

    public function down(): void
    {
        $tabelas = [
            'auth_permissions_users', 'auth_groups_users', 'auth_remember_tokens',
            'auth_token_logins', 'auth_logins', 'auth_identities',
            'acervo_documentos', 'settings', 'documentos', 'caso_agente', 'caso_vitima',
            'estudos', 'vitimas', 'agentes', 'casos', 'localizacoes', 'users',
        ];
        foreach ($tabelas as $tabela) {
            $this->db->query("DROP TABLE IF EXISTS {$tabela}");
        }
    }
}
