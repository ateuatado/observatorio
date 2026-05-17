<?php

namespace App\Controllers;

use App\Models\OcorrenciaModel;
use App\Models\EstudoModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $ocorrenciaModel = new OcorrenciaModel();
        $estudoModel     = new EstudoModel();

        // Estatísticas gerais
        $stats = [
            'total_casos'    => $ocorrenciaModel->countAll(),
            'total_fatais'   => $ocorrenciaModel->somaVitimas(),
            'nao_publicados' => $ocorrenciaModel->where('publicado', 0)->countAllResults(),
            'municipios'     => $ocorrenciaModel->contarMunicipios(),
            'por_tipo'       => $ocorrenciaModel->contagemPorTipo(),
        ];

        // Últimas 10 ocorrências (todas, não só publicadas)
        $casos_recentes = $ocorrenciaModel
            ->select('ocorrencias.*, localizacoes.municipio, localizacoes.bairro')
            ->join('localizacoes', 'localizacoes.id = ocorrencias.localizacao_id', 'left')
            ->orderBy('ocorrencias.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return view('dashboard/index', [
            'title'          => 'Dashboard',
            'breadcrumb'     => 'Dashboard',
            'stats'          => $stats,
            'casos_recentes' => $casos_recentes,
        ]);
    }
}
