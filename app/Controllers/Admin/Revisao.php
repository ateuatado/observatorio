<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OcorrenciaModel;
use App\Models\VitimaModel;
use App\Models\AgressorModel;
use App\Models\RevisaoModel;

class Revisao extends BaseController
{
    protected OcorrenciaModel $model;
    protected RevisaoModel    $revisaoModel;

    public function __construct()
    {
        $this->model       = new OcorrenciaModel();
        $this->revisaoModel = new RevisaoModel();
    }

    public function index(): string
    {
        $pendentes = $this->model
            ->where('status', 'em_revisao')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('admin/revisao/index', [
            'title'     => 'Fila de Revisão — OVPDH',
            'pendentes' => $pendentes,
            'user'      => auth()->user(),
        ]);
    }

    public function show(int $id): string
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        return view('admin/revisao/show', [
            'title'      => 'Revisão #' . $id . ' — OVPDH',
            'ocorrencia' => $ocorrencia,
            'vitimas'    => (new VitimaModel())->getByOcorrencia($id),
            'agressores' => (new AgressorModel())->getByOcorrencia($id),
            'historico'  => $this->revisaoModel->getByOcorrencia($id),
            'user'       => auth()->user(),
        ]);
    }

    public function acao(int $id)
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $acao = $this->request->getPost('acao');
        $mapa = ['aprovar' => 'aprovado', 'rejeitar' => 'rascunho', 'publicar' => 'publicado'];

        if (! isset($mapa[$acao])) {
            return redirect()->back()->with('error', 'Ação inválida.');
        }

        $novoStatus = $mapa[$acao];
        $update = [
            'status'      => $novoStatus,
            'revisor_id'  => auth()->id(),
            'revisado_em' => date('Y-m-d H:i:s'),
        ];
        if ($novoStatus === 'publicado') {
            $update['publicado_em'] = date('Y-m-d H:i:s');
        }

        $this->model->update($id, $update);
        $this->revisaoModel->insert([
            'ocorrencia_id'  => $id,
            'user_id'        => auth()->id(),
            'acao'           => $acao,
            'status_anterior'=> $ocorrencia['status'],
            'status_novo'    => $novoStatus,
            'comentario'     => $this->request->getPost('comentario'),
        ]);

        return redirect()->to('/painel/revisao')->with('success', 'Ocorrência ' . $acao . 'da com sucesso.');
    }
}
