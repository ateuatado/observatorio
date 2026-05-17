<?php

namespace App\Models;

use CodeIgniter\Model;

class CasoModel extends Model
{
    protected $table         = 'casos';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'protocolo_ovp',
        'localizacao_id',
        'data_fato',
        'hora_fato',
        'tipo_violencia',
        'subtipo',
        'vitimas_fatais',
        'vitimas_nao_fatais',
        'versao_oficial',
        'versao_testemunhas',
        'descricao_livre',
        'status_investigacao',
        'publicado',
        'destaque',
        'cadastrado_por',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'data_fato'      => 'required|valid_date',
        'tipo_violencia' => 'required|in_list[execucao,chacina,tortura,abuso_poder,morte_custodia,desaparecimento,ameaca]',
        'vitimas_fatais' => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'data_fato'      => ['required' => 'A data do fato é obrigatória.'],
        'tipo_violencia' => ['required' => 'O tipo de violência é obrigatório.'],
    ];

    // ----------------------------------------------------------------
    // Métodos analíticos
    // ----------------------------------------------------------------

    /**
     * Soma total de vítimas fatais nos casos publicados.
     */
    public function somaVitimas(): int
    {
        $result = $this->db->table($this->table)
            ->selectSum('vitimas_fatais')
            ->get()->getRow();
        return (int)($result->vitimas_fatais ?? 0);
    }

    /**
     * Conta o número de municípios distintos.
     */
    public function contarMunicipios(): int
    {
        $result = $this->db->table('casos c')
            ->join('localizacoes l', 'l.id = c.localizacao_id', 'left')
            ->selectMax('l.municipio', 'total')
            ->countAllResults(false);

        $result = $this->db->table('localizacoes')
            ->select('COUNT(DISTINCT municipio) AS total')
            ->join('casos', 'casos.localizacao_id = localizacoes.id', 'inner')
            ->get()->getRow();

        return (int)($result->total ?? 0);
    }

    /**
     * Contagem agrupada por tipo_violencia.
     */
    public function contagemPorTipo(): array
    {
        return $this->db->table($this->table)
            ->select('tipo_violencia, COUNT(*) AS total')
            ->groupBy('tipo_violencia')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Gera um protocolo OVP único: OVP-AAAA-NNNNN
     */
    public function gerarProtocolo(): string
    {
        $ano = date('Y');
        $ultimo = $this->db->table($this->table)
            ->select('protocolo_ovp')
            ->like('protocolo_ovp', "OVP-{$ano}-", 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()->getRow();

        $seq = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->protocolo_ovp);
            $seq = (int)end($partes) + 1;
        }

        return sprintf('OVP-%s-%05d', $ano, $seq);
    }
}
