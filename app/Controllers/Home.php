<?php

namespace App\Controllers;

use App\Models\OcorrenciaModel;
use App\Models\EstudoModel;

class Home extends BaseController
{
    public function index(): string
    {
        $ocorrenciaModel = new OcorrenciaModel();
        $estudoModel     = new EstudoModel();

        // Estatísticas públicas
        $stats = [
            'total_casos'   => $ocorrenciaModel->where('publicado', 1)->countAllResults(),
            'total_vitimas' => $ocorrenciaModel->somaVitimas(),
            'anos_ativos'   => date('Y') - 1999,
            'total_estudos' => $estudoModel->where('publicado', 1)->countAllResults(),
        ];

        // Últimas 6 ocorrências publicadas
        $casos_recentes = $ocorrenciaModel
            ->select('ocorrencias.*, localizacoes.municipio, localizacoes.bairro')
            ->join('localizacoes', 'localizacoes.id = ocorrencias.localizacao_id', 'left')
            ->where('ocorrencias.publicado', 1)
            ->orderBy('ocorrencias.data_fato', 'DESC')
            ->limit(6)
            ->findAll();

        // Estudos em destaque
        $estudos_destaque = $estudoModel
            ->where('publicado', 1)
            ->where('destaque', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->findAll();

        return view('home/index', [
            'title'           => 'Início',
            'stats'           => $stats,
            'casos_recentes'  => $casos_recentes,
            'estudos_destaque'=> $estudos_destaque,
        ]);
    }

    public function sobre(): string
    {
        return view('home/sobre', [
            'title'            => 'Sobre o OVP',
            'meta_description' => 'Conheça a história e a missão do Observatório de Violências Policiais de São Paulo.',
        ]);
    }
}
