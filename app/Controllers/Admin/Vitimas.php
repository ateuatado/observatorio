<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VitimaModel;
use App\Models\OcorrenciaModel;

class Vitimas extends BaseController
{
    protected VitimaModel $model;

    public function __construct()
    {
        $this->model = new VitimaModel();
    }

    public function index(): string
    {
        helper('text');
        $db = \Config\Database::connect();
        $vitimas = $db->table('vitimas v')
            ->select('v.*, o.titulo as ocorrencia_titulo, o.status as ocorrencia_status')
            ->join('ocorrencias o', 'o.id = v.ocorrencia_id', 'left')
            ->orderBy('v.created_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/vitimas/index', [
            'title'   => 'Vítimas — OVPDH',
            'vitimas' => $vitimas,
            'user'    => auth()->user(),
        ]);
    }

    public function create(): string
    {
        $ocorrencias = (new OcorrenciaModel())->findAll();
        return view('admin/vitimas/create', [
            'title'       => 'Cadastrar Vítima — OVPDH',
            'ocorrencias' => $ocorrencias,
            'user'        => auth()->user(),
        ]);
    }

    public function store()
    {
        $this->model->insert([
            'ocorrencia_id'   => $this->request->getPost('ocorrencia_id'),
            'nome'            => $this->request->getPost('anonimo') ? null : $this->request->getPost('nome'),
            'anonimo'         => $this->request->getPost('anonimo') ? 1 : 0,
            'idade'           => $this->request->getPost('idade') ?: null,
            'genero'          => $this->request->getPost('genero'),
            'raca_etnia'      => $this->request->getPost('raca_etnia'),
            'condicao_social' => $this->request->getPost('condicao_social'),
            'escolaridade'    => $this->request->getPost('escolaridade'),
            'profissao'       => $this->request->getPost('profissao'),
            'relato'          => $this->request->getPost('relato'),
            'desfecho'        => $this->request->getPost('desfecho'),
        ]);

        return redirect()->to('/painel/vitimas')->with('success', 'Vítima cadastrada com sucesso.');
    }

    public function edit(int $id): string
    {
        $vitima = $this->model->find($id);
        if (! $vitima) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $ocorrencias = (new OcorrenciaModel())->findAll();
        return view('admin/vitimas/edit', [
            'title'       => 'Editar Vítima — OVPDH',
            'vitima'      => $vitima,
            'ocorrencias' => $ocorrencias,
            'user'        => auth()->user(),
        ]);
    }

    public function update(int $id)
    {
        $vitima = $this->model->find($id);
        if (! $vitima) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $this->model->update($id, [
            'ocorrencia_id'   => $this->request->getPost('ocorrencia_id'),
            'nome'            => $this->request->getPost('anonimo') ? null : $this->request->getPost('nome'),
            'anonimo'         => $this->request->getPost('anonimo') ? 1 : 0,
            'idade'           => $this->request->getPost('idade') ?: null,
            'genero'          => $this->request->getPost('genero'),
            'raca_etnia'      => $this->request->getPost('raca_etnia'),
            'condicao_social' => $this->request->getPost('condicao_social'),
            'escolaridade'    => $this->request->getPost('escolaridade'),
            'profissao'       => $this->request->getPost('profissao'),
            'relato'          => $this->request->getPost('relato'),
            'desfecho'        => $this->request->getPost('desfecho'),
        ]);

        return redirect()->to('/painel/vitimas')->with('success', 'Vítima atualizada.');
    }
}
