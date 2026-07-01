<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdutoModel;

class ProdutosAdmin extends BaseController
{
    protected ProdutoModel $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    public function index(): string
    {
        return view('admin/produtos/index', [
            'title'    => 'Gerenciar Produções — OVPDH',
            'produtos' => $this->model->orderBy('ano', 'DESC')->findAll(),
            'user'     => auth()->user(),
        ]);
    }

    public function create(): string
    {
        return view('admin/produtos/create', [
            'title' => 'Nova Produção Acadêmica — OVPDH',
            'user'  => auth()->user(),
        ]);
    }

    public function store()
    {
        $this->model->insert([
            'titulo'        => $this->request->getPost('titulo'),
            'autores'       => $this->request->getPost('autores'),
            'tipo'          => $this->request->getPost('tipo'),
            'resumo'        => $this->request->getPost('resumo'),
            'ano'           => $this->request->getPost('ano') ?: null,
            'publicacao'    => $this->request->getPost('publicacao'),
            'doi'           => $this->request->getPost('doi'),
            'link_externo'  => $this->request->getPost('link_externo'),
            'palavras_chave'=> $this->request->getPost('palavras_chave'),
            'ativo'         => 1,
        ]);

        return redirect()->to('/painel/produtos-admin')->with('success', 'Produção acadêmica cadastrada.');
    }
}
