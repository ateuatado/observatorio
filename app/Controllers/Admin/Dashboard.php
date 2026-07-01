<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OcorrenciaModel;
use App\Models\VitimaModel;
use App\Models\AgressorModel;
use App\Models\ProdutoModel;
use App\Models\HistoricoModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        helper('text');
        $ocorrenciaModel = new OcorrenciaModel();
        $vitimaModel     = new VitimaModel();
        $produtoModel    = new ProdutoModel();

        $counts = $ocorrenciaModel->countByStatus();
        $byTipo = $ocorrenciaModel->countByTipo();
        $byMes  = $ocorrenciaModel->countByMes(12);
        $byRaca = $vitimaModel->countByRaca();

        // Últimas ocorrências
        $recentes = $ocorrenciaModel
            ->orderBy('created_at', 'DESC')
            ->findAll(8);

        // Ocorrências pendentes de revisão
        $pendentes = $ocorrenciaModel
            ->where('status', 'em_revisao')
            ->orderBy('created_at', 'ASC')
            ->findAll(5);

        // Montar labels/dados para Chart.js
        $meses = [];
        $totaisMes = [];
        foreach ($byMes as $m) {
            $dt = \DateTime::createFromFormat('Y-m', $m['mes']);
            $meses[] = $dt ? $dt->format('M/Y') : $m['mes'];
            $totaisMes[] = (int)$m['total'];
        }

        $tipoLabels = array_column($byTipo, 'tipo_violencia');
        $tipoTotais = array_map('intval', array_column($byTipo, 'total'));

        $racaLabels = array_column($byRaca, 'raca_etnia');
        $racaTotais = array_map('intval', array_column($byRaca, 'total'));

        $user = auth()->user();

        return view('admin/dashboard', [
            'title'        => 'Dashboard — OVPDH',
            'counts'       => $counts,
            'recentes'     => $recentes,
            'pendentes'    => $pendentes,
            'meses'        => json_encode($meses),
            'totaisMes'    => json_encode($totaisMes),
            'tipoLabels'   => json_encode($tipoLabels),
            'tipoTotais'   => json_encode($tipoTotais),
            'racaLabels'   => json_encode($racaLabels),
            'racaTotais'   => json_encode($racaTotais),
            'user'         => $user,
        ]);
    }
}
