<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OcorrenciaModel;
use App\Models\VitimaModel;
use App\Models\AgressorModel;
use App\Models\RevisaoModel;

class Ocorrencias extends BaseController
{
    protected OcorrenciaModel $model;
    protected VitimaModel     $vitimaModel;
    protected AgressorModel   $agressorModel;
    protected RevisaoModel    $revisaoModel;

    public function __construct()
    {
        $this->model         = new OcorrenciaModel();
        $this->vitimaModel   = new VitimaModel();
        $this->agressorModel = new AgressorModel();
        $this->revisaoModel  = new RevisaoModel();
    }

    public function index(): string
    {
        $user = auth()->user();
        $query = $this->model->orderBy('created_at', 'DESC');

        // Voluntários veem apenas as próprias
        if ($user->inGroup('voluntario')) {
            $query->where('user_id', $user->id);
        }

        $status = $this->request->getGet('status');
        if ($status && in_array($status, ['rascunho','em_revisao','aprovado','publicado'])) {
            $query->where('status', $status);
        }

        $ocorrencias = $query->paginate(15);

        return view('admin/ocorrencias/index', [
            'title'       => 'Ocorrências — OVPDH',
            'ocorrencias' => $ocorrencias,
            'pager'       => $this->model->pager,
            'statusAtual' => $status,
            'user'        => $user,
        ]);
    }

    public function show(int $id): string
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        return view('admin/ocorrencias/show', [
            'title'      => 'Ocorrência #' . $id . ' — OVPDH',
            'ocorrencia' => $ocorrencia,
            'vitimas'    => $this->vitimaModel->getByOcorrencia($id),
            'agressores' => $this->agressorModel->getByOcorrencia($id),
            'revisoes'   => $this->revisaoModel->getByOcorrencia($id),
            'user'       => auth()->user(),
        ]);
    }

    public function create(): string
    {
        return view('admin/ocorrencias/create', [
            'title' => 'Nova Ocorrência — OVPDH',
            'user'  => auth()->user(),
        ]);
    }

    public function store()
    {
        $rules = [
            'titulo'         => 'required|min_length[5]|max_length[255]',
            'tipo_violencia' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->model->insert([
            'titulo'          => $this->request->getPost('titulo'),
            'descricao'       => $this->request->getPost('descricao'),
            'data_ocorrencia' => $this->request->getPost('data_ocorrencia') ?: null,
            'hora_ocorrencia' => $this->request->getPost('hora_ocorrencia') ?: null,
            'tipo_violencia'  => $this->request->getPost('tipo_violencia'),
            'subtipo'         => $this->request->getPost('subtipo') ?: null,
            'local_descricao' => $this->request->getPost('local_descricao'),
            'bairro'          => $this->request->getPost('bairro'),
            'cidade'          => $this->request->getPost('cidade') ?: 'Belo Horizonte',
            'estado'          => $this->request->getPost('estado') ?: 'MG',
            'fontes'          => $this->request->getPost('fontes'),
            'status'          => 'rascunho',
            'prioridade'      => $this->request->getPost('prioridade') ?: 'normal',
            'user_id'         => auth()->id(),
        ]);

        // Log revisão
        $this->revisaoModel->insert([
            'ocorrencia_id' => $id,
            'user_id'       => auth()->id(),
            'acao'          => 'criado',
            'status_novo'   => 'rascunho',
            'comentario'    => 'Ocorrência cadastrada.',
        ]);

        return redirect()->to('/painel/ocorrencias/' . $id)
            ->with('success', 'Ocorrência cadastrada com sucesso! Status: Rascunho.');
    }

    public function edit(int $id): string
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        return view('admin/ocorrencias/edit', [
            'title'      => 'Editar Ocorrência #' . $id,
            'ocorrencia' => $ocorrencia,
            'user'       => auth()->user(),
        ]);
    }

    public function update(int $id)
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $this->model->update($id, [
            'titulo'          => $this->request->getPost('titulo'),
            'descricao'       => $this->request->getPost('descricao'),
            'data_ocorrencia' => $this->request->getPost('data_ocorrencia') ?: null,
            'hora_ocorrencia' => $this->request->getPost('hora_ocorrencia') ?: null,
            'tipo_violencia'  => $this->request->getPost('tipo_violencia'),
            'subtipo'         => $this->request->getPost('subtipo') ?: null,
            'local_descricao' => $this->request->getPost('local_descricao'),
            'bairro'          => $this->request->getPost('bairro'),
            'cidade'          => $this->request->getPost('cidade') ?: 'Belo Horizonte',
            'estado'          => $this->request->getPost('estado') ?: 'MG',
            'fontes'          => $this->request->getPost('fontes'),
            'prioridade'      => $this->request->getPost('prioridade') ?: 'normal',
        ]);

        return redirect()->to('/painel/ocorrencias/' . $id)->with('success', 'Ocorrência atualizada.');
    }

    public function updateStatus(int $id)
    {
        $ocorrencia = $this->model->find($id);
        if (! $ocorrencia) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $novoStatus = $this->request->getPost('status');
        $statusValidos = ['rascunho', 'em_revisao', 'aprovado', 'publicado', 'arquivado'];

        if (! in_array($novoStatus, $statusValidos)) {
            return redirect()->back()->with('error', 'Status inválido.');
        }

        $update = ['status' => $novoStatus];
        if ($novoStatus === 'publicado') {
            $update['publicado_em'] = date('Y-m-d H:i:s');
        }
        if (in_array($novoStatus, ['aprovado','publicado'])) {
            $update['revisor_id']  = auth()->id();
            $update['revisado_em'] = date('Y-m-d H:i:s');
        }

        $this->model->update($id, $update);

        $this->revisaoModel->insert([
            'ocorrencia_id'  => $id,
            'user_id'        => auth()->id(),
            'acao'           => $novoStatus,
            'status_anterior'=> $ocorrencia['status'],
            'status_novo'    => $novoStatus,
            'comentario'     => $this->request->getPost('comentario') ?: null,
        ]);

        return redirect()->to('/painel/ocorrencias/' . $id)
            ->with('success', 'Status atualizado para: ' . $novoStatus);
    }
}
