<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ProdutoModel;

class Produtos extends BaseController
{
    protected ProdutoModel $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    public function index(): string
    {
        $db = \Config\Database::connect();
        
        $tipos = $db->table('produtos')
            ->select('tipo')
            ->distinct()
            ->where('ativo', 1)
            ->get()->getResultArray();

        $anos = $db->table('produtos')
            ->select('ano')
            ->distinct()
            ->where('ativo', 1)
            ->orderBy('ano', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'    => 'Produções Acadêmicas — OVPDH PUC Minas',
            'produtos' => $this->model->getAtivos(50),
            'tipos'    => array_column($tipos, 'tipo'),
            'anos'     => array_column($anos, 'ano'),
        ];

        return view('public/produtos/index', $data);
    }

    public function show(int $id): string
    {
        $produto = $this->model->find($id);
        if (! $produto || ! $produto['ativo']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Produto não encontrado.");
        }

        return view('public/produtos/show', [
            'title'   => $produto['titulo'] . ' — OVPDH',
            'produto' => $produto,
        ]);
    }
}
