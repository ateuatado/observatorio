<?php

namespace App\Controllers\Admin;

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
        return view('admin/historico/index', [
            'title'      => 'Gerenciar Histórico — OVPDH',
            'historicos' => $this->model->orderBy('ano_inicio', 'DESC')->findAll(),
            'user'       => auth()->user(),
        ]);
    }

    public function create(): string
    {
        return view('admin/historico/create', [
            'title' => 'Novo Documento Histórico — OVPDH',
            'user'  => auth()->user(),
        ]);
    }

    public function store()
    {
        $this->model->insert([
            'titulo'    => $this->request->getPost('titulo'),
            'descricao' => $this->request->getPost('descricao'),
            'periodo'   => $this->request->getPost('periodo'),
            'ano_inicio'=> $this->request->getPost('ano_inicio') ?: null,
            'ano_fim'   => $this->request->getPost('ano_fim') ?: null,
            'categoria' => $this->request->getPost('categoria'),
            'autora'    => $this->request->getPost('autora'),
            'ativo'     => 1,
        ]);

        return redirect()->to('/painel/historico')->with('success', 'Documento histórico cadastrado.');
    }
}
