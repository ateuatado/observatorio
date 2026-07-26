<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\HistoricoModel;

class Historico extends BaseController
{
    protected HistoricoModel $model;

    public function __construct()
    {
        $this->model = new HistoricoModel();
    }

    public function index(): string
    {
        $db = \Config\Database::connect();
        $categorias = $db->table('historico')
            ->select('categoria')
            ->distinct()
            ->where('ativo', 1)
            ->get()->getResultArray();

        $data = [
            'title'      => 'Acervo Histórico — OVPDH PUC São Paulo',
            'historicos' => $this->model->getAtivos(50),
            'categorias' => array_column($categorias, 'categoria'),
        ];

        return view('public/historico/index', $data);
    }

    public function show(int $id): string
    {
        $historico = $this->model->find($id);
        if (! $historico || ! $historico['ativo']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Documento histórico não encontrado.");
        }

        return view('public/historico/show', [
            'title'     => $historico['titulo'] . ' — OVPDH',
            'historico' => $historico,
        ]);
    }
}
