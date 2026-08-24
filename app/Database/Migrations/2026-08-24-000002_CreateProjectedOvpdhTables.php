<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class CreateProjectedOvpdhTables extends Migration
{
    public function up(): void
    {
        if ($this->db->getPlatform() !== 'Postgre') {
            throw new RuntimeException('A estrutura projetada do OVPDH requer PostgreSQL com PostGIS habilitado.');
        }

        $postgisHabilitado = $this->db->query("SELECT EXISTS (SELECT 1 FROM pg_extension WHERE extname = 'postgis') AS habilitado")
            ->getRow('array');

        if (! ($postgisHabilitado['habilitado'] ?? false)) {
            throw new RuntimeException('A extensão PostGIS deve estar habilitada antes de executar esta migration.');
        }

        $this->createTiposViolencia();
        $this->createOcorrenciaTiposViolencia();
        $this->createOcorrenciaFontes();
        $this->createCondicoesVitima();
        $this->createVitimaCondicoes();
        $this->createViolacoes();
        $this->createMeiosELesoes();
        $this->createOrigemLegado();
        $this->createGeometrias();
    }

    public function down(): void
    {
        $this->forge->dropTable('ocorrencia_geometrias', true);
        $this->forge->dropTable('catalogo_legado_mapeamentos', true);
        $this->forge->dropTable('ocorrencia_origens_legado', true);
        $this->forge->dropTable('violacao_lesoes_corpo', true);
        $this->forge->dropTable('violacao_meios_instrumentos', true);
        $this->forge->dropTable('lesoes_corpo', true);
        $this->forge->dropTable('meios_instrumentos', true);
        $this->forge->dropTable('violacoes', true);
        $this->forge->dropTable('vitima_condicoes', true);
        $this->forge->dropTable('condicoes_vitima', true);
        $this->forge->dropTable('ocorrencia_fontes', true);
        $this->forge->dropTable('ocorrencia_tipos_violencia', true);
        $this->forge->dropTable('tipos_violencia', true);
    }

    private function createTiposViolencia(): void
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'codigo'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nome'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'ativo'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('codigo');
        $this->forge->addUniqueKey('nome');
        $this->forge->createTable('tipos_violencia');
    }

    private function createOcorrenciaTiposViolencia(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id'     => ['type' => 'INT', 'unsigned' => true],
            'tipo_violencia_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['ocorrencia_id', 'tipo_violencia_id']);
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('tipo_violencia_id', 'tipos_violencia', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('ocorrencia_tipos_violencia');
    }

    private function createOcorrenciaFontes(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id' => ['type' => 'INT', 'unsigned' => true],
            'tipo'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'titulo'        => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'referencia'    => ['type' => 'TEXT', 'null' => true],
            'url'           => ['type' => 'VARCHAR', 'constraint' => 2048, 'null' => true],
            'descricao'     => ['type' => 'TEXT', 'null' => true],
            'data_fonte'    => ['type' => 'DATE', 'null' => true],
            'data_acesso'   => ['type' => 'DATE', 'null' => true],
            'restrita'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('ocorrencia_id');
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('ocorrencia_fontes');
    }

    private function createCondicoesVitima(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao'  => ['type' => 'TEXT', 'null' => true],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nome');
        $this->forge->createTable('condicoes_vitima');
    }

    private function createVitimaCondicoes(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'vitima_id'           => ['type' => 'INT', 'unsigned' => true],
            'condicao_vitima_id'  => ['type' => 'INT', 'unsigned' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['vitima_id', 'condicao_vitima_id']);
        $this->forge->addForeignKey('vitima_id', 'vitimas', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('condicao_vitima_id', 'condicoes_vitima', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('vitima_condicoes');
    }

    private function createViolacoes(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id'     => ['type' => 'INT', 'unsigned' => true],
            'tipo_violencia_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'conduta'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'enquadramento_juridico' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'justificativa'     => ['type' => 'TEXT', 'null' => true],
            'observacoes'       => ['type' => 'TEXT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('ocorrencia_id');
        $this->forge->addKey('tipo_violencia_id');
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('tipo_violencia_id', 'tipos_violencia', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('violacoes');
    }

    private function createMeiosELesoes(): void
    {
        $this->createCatalogo('meios_instrumentos');
        $this->createCatalogo('lesoes_corpo');

        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'violacao_id'          => ['type' => 'INT', 'unsigned' => true],
            'meio_instrumento_id'  => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['violacao_id', 'meio_instrumento_id']);
        $this->forge->addForeignKey('violacao_id', 'violacoes', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('meio_instrumento_id', 'meios_instrumentos', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('violacao_meios_instrumentos');

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'violacao_id'     => ['type' => 'INT', 'unsigned' => true],
            'lesao_corpo_id'  => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['violacao_id', 'lesao_corpo_id']);
        $this->forge->addForeignKey('violacao_id', 'violacoes', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('lesao_corpo_id', 'lesoes_corpo', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('violacao_lesoes_corpo');
    }

    private function createCatalogo(string $table): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao'  => ['type' => 'TEXT', 'null' => true],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nome');
        $this->forge->createTable($table);
    }

    private function createOrigemLegado(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id'         => ['type' => 'INT', 'unsigned' => true],
            'sistema_origem'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'esquema_origem'        => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'public'],
            'tabela_origem'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'identificador_origem'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'importado_em'          => ['type' => 'DATETIME', 'null' => true],
            'resultado'             => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'importado'],
            'observacoes'           => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['sistema_origem', 'esquema_origem', 'tabela_origem', 'identificador_origem']);
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('ocorrencia_origens_legado');

        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'catalogo_origem'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'identificador_origem' => ['type' => 'VARCHAR', 'constraint' => 100],
            'valor_origem'         => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'catalogo_destino'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'identificador_destino'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'aprovado_por'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'aprovado_em'          => ['type' => 'DATETIME', 'null' => true],
            'observacoes'          => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['catalogo_origem', 'identificador_origem', 'catalogo_destino']);
        $this->forge->createTable('catalogo_legado_mapeamentos');
    }

    private function createGeometrias(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE ocorrencia_geometrias (
    id SERIAL PRIMARY KEY,
    ocorrencia_id INTEGER NOT NULL UNIQUE REFERENCES ocorrencias(id) ON DELETE RESTRICT,
    geometria geometry(Point, 4326) NOT NULL,
    precisao VARCHAR(30) NOT NULL DEFAULT 'aproximada',
    fonte VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)
SQL);
        $this->db->query('CREATE INDEX ocorrencia_geometrias_geometria_gix ON ocorrencia_geometrias USING GIST (geometria)');
    }
}
