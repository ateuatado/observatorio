<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOvpdhTables extends Migration
{
    public function up(): void
    {
        // Ocorrências
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao'       => ['type' => 'TEXT', 'null' => true],
            'data_ocorrencia' => ['type' => 'DATE', 'null' => true],
            'hora_ocorrencia' => ['type' => 'TIME', 'null' => true],
            'tipo_violencia'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'subtipo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'local_descricao' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bairro'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => 'Belo Horizonte'],
            'estado'          => ['type' => 'CHAR', 'constraint' => 2, 'null' => true, 'default' => 'MG'],
            'latitude'        => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'longitude'       => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'fontes'          => ['type' => 'TEXT', 'null' => true],
            'evidencias'      => ['type' => 'TEXT', 'null' => true],
            // VARCHAR mantém a migration compatível com PostgreSQL. As transições
            // permitidas são validadas pela camada de domínio da aplicação.
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'rascunho'],
            'prioridade'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'normal'],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'revisor_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'revisado_em'     => ['type' => 'DATETIME', 'null' => true],
            'publicado_em'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('ocorrencias');

        // Vítimas
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'anonimo'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'idade'          => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'genero'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'raca_etnia'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'condicao_social'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'escolaridade'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'profissao'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'relato'         => ['type' => 'TEXT', 'null' => true],
            'desfecho'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vitimas');

        // Agressores
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo_agente'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'orgao'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'batalhao'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'posto'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'identificacao' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'identificado'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'observacoes'   => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('ocorrencia_id', 'ocorrencias', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('agressores');

        // Revisões/Auditoria
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ocorrencia_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'acao'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'status_anterior'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_novo'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'comentario'    => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('ocorrencia_revisoes');

        // Histórico (acervo histórico)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'descricao'   => ['type' => 'TEXT', 'null' => true],
            'periodo'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ano_inicio'  => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'ano_fim'     => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'categoria'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'arquivo_pdf' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'miniatura'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'autora'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ativo'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('historico');

        // Produtos (publicações acadêmicas)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'autores'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'tipo'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'resumo'      => ['type' => 'TEXT', 'null' => true],
            'ano'         => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'publicacao'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'doi'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'link_externo'=> ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'arquivo'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'palavras_chave'=> ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'ativo'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('produtos');
    }

    public function down(): void
    {
        $this->forge->dropTable('ocorrencia_revisoes', true);
        $this->forge->dropTable('vitimas', true);
        $this->forge->dropTable('agressores', true);
        $this->forge->dropTable('ocorrencias', true);
        $this->forge->dropTable('historico', true);
        $this->forge->dropTable('produtos', true);
    }
}
