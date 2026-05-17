<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renomeia as tabelas de "casos" para "ocorrencias" em toda a schema OVP.
 *
 * Estratégia RENAME TABLE (preserva dados e estrutura).
 * Não afeta tabelas de outras entidades (acervo_documentos, estudos, etc.).
 *
 * Tabelas renomeadas:
 *   casos           → ocorrencias
 *   caso_vitima     → ocorrencia_vitima
 *   caso_agente     → ocorrencia_agente
 *   caso_documento  → ocorrencia_documento
 *   caso_tag        → ocorrencia_tag
 */
class RenomearCasosParaOcorrencias extends Migration
{
    /**
     * Mapeamento: nome_atual => nome_novo
     */
    private array $renomear = [
        'casos'          => 'ocorrencias',
        'caso_vitima'    => 'ocorrencia_vitima',
        'caso_agente'    => 'ocorrencia_agente',
        'caso_documento' => 'ocorrencia_documento',
        'caso_tag'       => 'ocorrencia_tag',
    ];

    public function up(): void
    {
        // Desabilita verificações de FK durante o RENAME para evitar
        // conflitos de dependência entre as tabelas
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->renomear as $atual => $novo) {
            if ($this->db->tableExists($atual)) {
                $this->db->query("RENAME TABLE `{$atual}` TO `{$novo}`");
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Reverso do mapeamento para o rollback
        foreach (array_reverse($this->renomear) as $novo => $atual) {
            if ($this->db->tableExists($atual)) {
                $this->db->query("RENAME TABLE `{$atual}` TO `{$novo}`");
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
