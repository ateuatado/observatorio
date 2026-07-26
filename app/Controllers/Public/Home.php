<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ProdutoModel;
use App\Models\HistoricoModel;
use App\Models\OcorrenciaModel;

class Home extends BaseController
{
    public function index(): string
    {
        $produtoModel   = new ProdutoModel();
        $historicoModel = new HistoricoModel();
        $ocorrenciaModel = new OcorrenciaModel();

        $data = [
            'title'         => 'Observatório de Violência Policial e Direitos Humanos — PUC São Paulo',
            'recentProdutos'=> $produtoModel->getRecentes(3),
            'recentHistorico'=> $historicoModel->getAtivos(3),
            'totalOcorrencias' => $ocorrenciaModel->where('status', 'publicado')->countAllResults(),
            'totalProdutos'  => $produtoModel->where('ativo', 1)->countAllResults(),
            'totalHistorico' => $historicoModel->where('ativo', 1)->countAllResults(),
        ];

        return view('public/home', $data);
    }
}
