<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOvpSchema extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // LOCALIZACAO
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS localizacoes (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                logradouro VARCHAR(255) NULL,
                numero     VARCHAR(20)  NULL,
                bairro     VARCHAR(100) NULL,
                zona_cidade VARCHAR(50) NULL COMMENT "norte,sul,leste,oeste,centro",
                municipio  VARCHAR(100) NOT NULL,
                estado     CHAR(2)      NOT NULL DEFAULT "SP",
                regiao_metropolitana VARCHAR(100) NULL,
                tipo_local VARCHAR(50)  NULL COMMENT "via_publica,residencia,bar_comercio,unidade_policial,unidade_prisional,unidade_socioeduc,rodovia,hospital,outro",
                descricao_local TEXT    NULL,
                latitude   DECIMAL(10,7) NULL,
                longitude  DECIMAL(10,7) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // CASOS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS casos (
                id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                protocolo_ovp       VARCHAR(20) NULL UNIQUE COMMENT "OVP-AAAA-NNNNN",
                localizacao_id      INT UNSIGNED NULL,
                data_fato           DATE        NOT NULL,
                hora_fato           TIME        NULL,
                tipo_violencia      VARCHAR(30) NOT NULL COMMENT "execucao,chacina,tortura,abuso_poder,morte_custodia,desaparecimento,ameaca",
                subtipo             VARCHAR(50) NULL,
                vitimas_fatais      TINYINT UNSIGNED NOT NULL DEFAULT 0,
                vitimas_nao_fatais  TINYINT UNSIGNED NOT NULL DEFAULT 0,
                versao_oficial      TEXT NULL,
                versao_testemunhas  TEXT NULL,
                descricao_livre     TEXT NULL,
                status_investigacao VARCHAR(30) NOT NULL DEFAULT "sem_inquerito" COMMENT "sem_inquerito,inquerito_aberto,arquivado,indiciado,acao_penal,condenado,absolvido",
                publicado           TINYINT(1)  NOT NULL DEFAULT 0,
                destaque            TINYINT(1)  NOT NULL DEFAULT 0,
                cadastrado_por      INT UNSIGNED NULL,
                created_at          DATETIME NULL,
                updated_at          DATETIME NULL,
                deleted_at          DATETIME NULL,
                FOREIGN KEY (localizacao_id) REFERENCES localizacoes(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // VITIMAS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS vitimas (
                id                          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome                        VARCHAR(200) NULL COMMENT "NULL quando nao identificada",
                data_nascimento             DATE NULL,
                idade_aparente              TINYINT UNSIGNED NULL,
                sexo                        VARCHAR(20)  NULL COMMENT "masculino,feminino,nao_binario,nao_informado",
                raca_cor                    VARCHAR(20)  NULL COMMENT "branca,preta,parda,amarela,indigena,nao_informada",
                profissao                   VARCHAR(100) NULL,
                condicao_juridica           VARCHAR(30)  NULL COMMENT "civil_inocente,suspeito,em_fuga,preso,menor_infrator,manifestante",
                menor_de_idade              TINYINT(1)   NOT NULL DEFAULT 0,
                gestante                    TINYINT(1)   NOT NULL DEFAULT 0,
                pcd                         TINYINT(1)   NOT NULL DEFAULT 0,
                antecedentes_versao_policial TEXT NULL,
                observacoes                 TEXT NULL,
                created_at                  DATETIME NULL,
                updated_at                  DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // CASOS x VITIMAS (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_vitima (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                caso_id         INT UNSIGNED NOT NULL,
                vitima_id       INT UNSIGNED NOT NULL,
                resultado       VARCHAR(30) NULL COMMENT "fatal,ferido,sobreviveu,desaparecido",
                ferimentos      TEXT NULL,
                parte_corpo     VARCHAR(100) NULL,
                identificada    TINYINT(1) NOT NULL DEFAULT 1,
                FOREIGN KEY (caso_id)   REFERENCES casos(id)   ON DELETE CASCADE,
                FOREIGN KEY (vitima_id) REFERENCES vitimas(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // AGENTES POLICIAIS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS agentes (
                id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome                VARCHAR(200) NULL,
                matricula           VARCHAR(50)  NULL,
                corporacao          VARCHAR(50)  NULL COMMENT "PM,PC,ROTA,CHOQUE,ROCAM,GCM,PF,Forca_Nacional,Agente_Pen,Outro",
                unidade_batalhao    VARCHAR(100) NULL,
                graduacao_posto     VARCHAR(50)  NULL,
                identificado        TINYINT(1)   NOT NULL DEFAULT 0,
                historico           TEXT NULL,
                created_at          DATETIME NULL,
                updated_at          DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // CASOS x AGENTES (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_agente (
                id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                caso_id                 INT UNSIGNED NOT NULL,
                agente_id               INT UNSIGNED NULL,
                descricao_agente        VARCHAR(255) NULL COMMENT "descricao livre quando nao identificado",
                quantidade_agentes      TINYINT UNSIGNED NULL DEFAULT 1,
                corporacao              VARCHAR(50) NULL,
                fardado                 TINYINT(1) NULL,
                encapuzado              TINYINT(1) NOT NULL DEFAULT 0,
                prefixo_viatura         VARCHAR(30) NULL,
                papel_no_caso           VARCHAR(50) NULL COMMENT "executor,participante,supervisor,informado",
                FOREIGN KEY (caso_id)   REFERENCES casos(id) ON DELETE CASCADE,
                FOREIGN KEY (agente_id) REFERENCES agentes(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // PROCESSOS JUDICIAIS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS processos (
                id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                caso_id                 INT UNSIGNED NOT NULL UNIQUE,
                numero_inquerito        VARCHAR(50) NULL,
                delegacia_responsavel   VARCHAR(100) NULL,
                inquerito_instaurado    TINYINT(1) NOT NULL DEFAULT 0,
                mp_denunciou            TINYINT(1) NOT NULL DEFAULT 0,
                fase_processual         VARCHAR(50) NULL,
                resultado_final         VARCHAR(50) NULL COMMENT "arquivado,condenado,absolvido,em_andamento",
                data_ultima_atualizacao DATE NULL,
                observacoes             TEXT NULL,
                FOREIGN KEY (caso_id) REFERENCES casos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // FONTES (veículos de comunicação)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS fontes (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome_veiculo    VARCHAR(150) NOT NULL,
                tipo_veiculo    VARCHAR(30) NULL COMMENT "jornal,portal,revista,tv,radio,academico,relatorio",
                cidade_sede     VARCHAR(100) NULL,
                estado_sede     CHAR(2) NULL,
                url_base        VARCHAR(255) NULL,
                created_at      DATETIME NULL,
                updated_at      DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // DOCUMENTOS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS documentos (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                fonte_id        INT UNSIGNED NULL,
                titulo          VARCHAR(500) NOT NULL,
                tipo_documento  VARCHAR(30) NULL COMMENT "noticia,artigo_academico,relatorio,cartilha,laudo,manifesto,entrevista,lei,sentenca,inquerito",
                data_publicacao DATE NULL,
                url_original    VARCHAR(1000) NULL,
                arquivo_local   VARCHAR(500) NULL,
                formato_arquivo VARCHAR(10) NULL COMMENT "pdf,html,docx,jpg",
                resumo          TEXT NULL,
                conteudo_texto  LONGTEXT NULL,
                publico         TINYINT(1) NOT NULL DEFAULT 1,
                created_at      DATETIME NULL,
                updated_at      DATETIME NULL,
                FOREIGN KEY (fonte_id) REFERENCES fontes(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // CASOS x DOCUMENTOS (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_documento (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                caso_id         INT UNSIGNED NOT NULL,
                documento_id    INT UNSIGNED NOT NULL,
                relacao         VARCHAR(50) NULL COMMENT "fonte_primaria,fonte_secundaria,laudo,inquerito,sentenca",
                FOREIGN KEY (caso_id)      REFERENCES casos(id)     ON DELETE CASCADE,
                FOREIGN KEY (documento_id) REFERENCES documentos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // TAGS
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS tags (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome        VARCHAR(80) NOT NULL UNIQUE,
                categoria   VARCHAR(50) NULL COMMENT "contexto_politico,perfil_vitima,modus_operandi,instituicao,resultado_juridico,regiao,relevancia",
                descricao   TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // CASOS x TAGS (pivot)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS caso_tag (
                caso_id     INT UNSIGNED NOT NULL,
                tag_id      INT UNSIGNED NOT NULL,
                PRIMARY KEY (caso_id, tag_id),
                FOREIGN KEY (caso_id) REFERENCES casos(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id)  REFERENCES tags(id)  ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // -------------------------------------------------------
        // ESTUDOS / PUBLICAÇÕES (conteúdo editorial público)
        // -------------------------------------------------------
        $this->db->query('
            CREATE TABLE IF NOT EXISTS estudos (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                titulo      VARCHAR(500) NOT NULL,
                slug        VARCHAR(255) NOT NULL UNIQUE,
                resumo      TEXT NULL,
                conteudo    LONGTEXT NULL,
                autores     VARCHAR(500) NULL,
                publicado   TINYINT(1) NOT NULL DEFAULT 0,
                destaque    TINYINT(1) NOT NULL DEFAULT 0,
                arquivo_pdf VARCHAR(500) NULL,
                created_at  DATETIME NULL,
                updated_at  DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $tables = [
            'caso_tag', 'tags',
            'caso_documento', 'documentos', 'fontes',
            'processos',
            'caso_agente', 'agentes',
            'caso_vitima', 'vitimas',
            'casos', 'localizacoes',
            'estudos',
        ];
        foreach ($tables as $table) {
            $this->db->query("DROP TABLE IF EXISTS {$table}");
        }
    }
}
