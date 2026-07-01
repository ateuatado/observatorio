<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgressorModel;
use App\Models\OcorrenciaModel;

class Agressores extends BaseController
{
    protected AgressorModel $model;

    public function __construct()
    {
        $this->model = new AgressorModel();
    }

    public function index(): string
    {
        helper('text');
        $db = \Config\Database::connect();
        $agressores = $db->table('agressores a')
            ->select('a.*, o.titulo as ocorrencia_titulo')
            ->join('ocorrencias o', 'o.id = a.ocorrencia_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/agressores/index', [
            'title'      => 'Agressores — OVPDH',
            'agressores' => $agressores,
            'user'       => auth()->user(),
        ]);
    }

    public function create(): string
    {
        $ocorrencias = (new OcorrenciaModel())->findAll();
        return view('admin/agressores/create', [
            'title'       => 'Cadastrar Agressor — OVPDH',
            'ocorrencias' => $ocorrencias,
            'user'        => auth()->user(),
        ]);
    }

    public function store()
    {
        $this->model->insert([
            'ocorrencia_id' => $this->request->getPost('ocorrencia_id'),
            'tipo_agente'   => $this->request->getPost('tipo_agente'),
            'orgao'         => $this->request->getPost('orgao'),
            'batalhao'      => $this->request->getPost('batalhao'),
            'posto'         => $this->request->getPost('posto'),
            'identificacao' => $this->request->getPost('identificacao'),
            'identificado'  => $this->request->getPost('identificado') ? 1 : 0,
            'observacoes'   => $this->request->getPost('observacoes'),
        ]);

        return redirect()->to('/painel/agressores')->with('success', 'Agressor cadastrado com sucesso.');
    }

    public function edit(int $id): string
    {
        $agressor = $this->model->find($id);
        if (! $agressor) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $ocorrencias = (new OcorrenciaModel())->findAll();
        return view('admin/agressores/edit', [
            'title'       => 'Editar Agressor — OVPDH',
            'agressor'    => $agressor,
            'ocorrencias' => $ocorrencias,
            'user'        => auth()->user(),
        ]);
    }

    public function update(int $id)
    {
        $agressor = $this->model->find($id);
        if (! $agressor) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $this->model->update($id, [
            'ocorrencia_id' => $this->request->getPost('ocorrencia_id'),
            'tipo_agente'   => $this->request->getPost('tipo_agente'),
            'orgao'         => $this->request->getPost('orgao'),
            'batalhao'      => $this->request->getPost('batalhao'),
            'posto'         => $this->request->getPost('posto'),
            'identificacao' => $this->request->getPost('identificacao'),
            'identificado'  => $this->request->getPost('identificado') ? 1 : 0,
            'observacoes'   => $this->request->getPost('observacoes'),
        ]);

        return redirect()->to('/painel/agressores')->with('success', 'Agressor atualizado.');
    }
}
