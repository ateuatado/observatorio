<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OcorrenciaModel;
use App\Models\VitimaModel;

class Relatorios extends BaseController
{
    public function index(): string
    {
        $ocorrenciaModel = new OcorrenciaModel();
        $vitimaModel     = new VitimaModel();

        $byTipo  = $ocorrenciaModel->countByTipo();
        $byMes   = $ocorrenciaModel->countByMes(24);
        $byRaca  = $vitimaModel->countByRaca();

        $db = \Config\Database::connect();
        // Por cidade
        $byCidade = $db->table('ocorrencias')
            ->select('cidade, COUNT(*) as total')
            ->where('deleted_at IS NULL')
            ->groupBy('cidade')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // Por status
        $counts = $ocorrenciaModel->countByStatus();

        $meses = [];
        $totaisMes = [];
        foreach ($byMes as $m) {
            $dt = \DateTime::createFromFormat('Y-m', $m['mes']);
            $meses[] = $dt ? $dt->format('M/Y') : $m['mes'];
            $totaisMes[] = (int)$m['total'];
        }

        return view('admin/relatorios/index', [
            'title'      => 'Relatórios — OVPDH',
            'byTipo'     => $byTipo,
            'byCidade'   => $byCidade,
            'byRaca'     => $byRaca,
            'counts'     => $counts,
            'meses'      => json_encode($meses),
            'totaisMes'  => json_encode($totaisMes),
            'tipoLabels' => json_encode(array_column($byTipo, 'tipo_violencia')),
            'tipoTotais' => json_encode(array_map('intval', array_column($byTipo, 'total'))),
            'racaLabels' => json_encode(array_column($byRaca, 'raca_etnia')),
            'racaTotais' => json_encode(array_map('intval', array_column($byRaca, 'total'))),
            'user'       => auth()->user(),
        ]);
    }
}
