<?php

namespace App\Controllers;

use App\Models\AcaoSegurancaModel;
use App\Models\OcorrenciaModel;

/**
 * Controller para Ações de Segurança.
 * Acesso restrito a usuários com permissão 'acoes.gerir'.
 */
class AcoesSeguranca extends BaseController
{
    protected AcaoSegurancaModel $acaoModel;
    protected OcorrenciaModel    $ocorrenciaModel;

    public function __construct()
    {
        $this->db              = \Config\Database::connect();
        $this->acaoModel       = new AcaoSegurancaModel();
        $this->ocorrenciaModel = new OcorrenciaModel();
    }

    // ================================================================
    // LISTAGEM
    // ================================================================

    public function index(): string
    {
        $tipo   = $this->request->getGet('tipo');
        $status = $this->request->getGet('status') ?? 'confirmada';

        $builder = $this->acaoModel;
        if ($tipo)   $builder = $builder->where('tipo_agente', $tipo);
        if ($status) $builder = $builder->where('status', $status);

        // Visibilidade: Curador Jurídico e Curador veem tudo; pesquisador só vê restrito+público
        $user = auth()->user();
        if (! $user->can('dados.sigiloso')) {
            $builder = $builder->whereIn('visibilidade', ['publica', 'restrita']);
        }

        $acoes = $builder->orderBy('data_inicio', 'DESC')->findAll();

        return view('acoes_seguranca/index', [
            'title'   => 'Ações de Segurança',
            'acoes'   => $acoes,
            'filtros' => compact('tipo', 'status'),
        ]);
    }

    // ================================================================
    // DETALHE
    // ================================================================

    public function show(int $id): string
    {
        $acao = $this->acaoModel->find($id);
        if (! $acao) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Ação #{$id} não encontrada.");
        }

        // Verificar visibilidade
        $this->verificarVisibilidade($acao);

        $ocorrencias = $this->acaoModel->ocorrenciasVinculadas($id);

        return view('acoes_seguranca/show', [
            'title'       => 'Ação: ' . ($acao['nome'] ?? 'Não nomeada'),
            'acao'        => $acao,
            'ocorrencias' => $ocorrencias,
        ]);
    }

    // ================================================================
    // CADASTRO
    // ================================================================

    public function novo(): string
    {
        return view('acoes_seguranca/form', [
            'title' => 'Nova Ação de Segurança',
            'acao'  => null,
        ]);
    }

    public function salvar()
    {
        if (! $this->validate($this->acaoModel->getValidationRules())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'nome'                => $this->request->getPost('nome') ?: null,
            'tipo_agente'         => $this->request->getPost('tipo_agente'),
            'data_inicio'         => $this->request->getPost('data_inicio') ?: null,
            'data_fim'            => $this->request->getPost('data_fim') ?: null,
            'precisao_temporal'   => $this->request->getPost('precisao_temporal') ?? 'aproximada',
            'motivacao_declarada' => $this->request->getPost('motivacao_declarada'),
            'motivacao_inferida'  => $this->request->getPost('motivacao_inferida'),
            'descricao'           => $this->request->getPost('descricao'),
            'status'              => $this->request->getPost('status') ?? 'em_analise',
            'visibilidade'        => $this->request->getPost('visibilidade') ?? 'restrita',
            'cadastrado_por'      => auth()->id(),
        ];

        $id = $this->acaoModel->insert($dados);

        return redirect()->to("acoes-seguranca/{$id}")
            ->with('message', 'Ação de Segurança cadastrada com sucesso!');
    }

    // ================================================================
    // EDIÇÃO
    // ================================================================

    public function editar(int $id): string
    {
        $acao = $this->acaoModel->find($id);
        if (! $acao) {
            return redirect()->to('acoes-seguranca')->with('error', 'Ação não encontrada.');
        }

        return view('acoes_seguranca/form', [
            'title' => 'Editar Ação: ' . ($acao['nome'] ?? '#' . $id),
            'acao'  => $acao,
        ]);
    }

    public function update(int $id)
    {
        $acao = $this->acaoModel->find($id);
        if (! $acao) {
            return redirect()->to('acoes-seguranca')->with('error', 'Ação não encontrada.');
        }

        if (! $this->validate($this->acaoModel->getValidationRules())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->acaoModel->update($id, [
            'nome'                => $this->request->getPost('nome') ?: null,
            'tipo_agente'         => $this->request->getPost('tipo_agente'),
            'data_inicio'         => $this->request->getPost('data_inicio') ?: null,
            'data_fim'            => $this->request->getPost('data_fim') ?: null,
            'precisao_temporal'   => $this->request->getPost('precisao_temporal') ?? 'aproximada',
            'motivacao_declarada' => $this->request->getPost('motivacao_declarada'),
            'motivacao_inferida'  => $this->request->getPost('motivacao_inferida'),
            'descricao'           => $this->request->getPost('descricao'),
            'status'              => $this->request->getPost('status'),
            'visibilidade'        => $this->request->getPost('visibilidade'),
        ]);

        return redirect()->to("acoes-seguranca/{$id}")
            ->with('message', 'Ação atualizada com sucesso!');
    }

    // ================================================================
    // VÍNCULOS COM OCORRÊNCIAS
    // ================================================================

    /**
     * POST — vincula uma ocorrência a esta ação.
     * Registra o curador responsável pelo vínculo.
     */
    public function vincular(int $acaoId)
    {
        $ocorrenciaId = (int) $this->request->getPost('ocorrencia_id');

        if (! $ocorrenciaId || ! $this->ocorrenciaModel->find($ocorrenciaId)) {
            return redirect()->back()->with('error', 'Ocorrência não encontrada.');
        }

        $ok = $this->acaoModel->vincularOcorrencia($ocorrenciaId, $acaoId, [
            'momento_vinculo' => $this->request->getPost('momento_vinculo'),
            'justificativa'   => $this->request->getPost('justificativa'),
            'vinculado_por'   => auth()->id(),
        ]);

        $msg = $ok ? 'Ocorrência vinculada com sucesso.' : 'Vínculo já existia.';
        return redirect()->back()->with('message', $msg);
    }

    /**
     * POST — remove o vínculo entre uma ocorrência e esta ação.
     */
    public function desvincular(int $acaoId, int $ocorrenciaId)
    {
        $this->acaoModel->desvincularOcorrencia($ocorrenciaId, $acaoId);
        return redirect()->back()->with('message', 'Vínculo removido.');
    }

    /**
     * Soft-delete da ação.
     */
    public function arquivar(int $id)
    {
        $this->acaoModel->delete($id);
        return redirect()->to('acoes-seguranca')->with('message', 'Ação arquivada.');
    }

    // ================================================================
    // Helpers privados
    // ================================================================

    private function verificarVisibilidade(array $acao): void
    {
        $user = auth()->user();
        if ($acao['visibilidade'] === 'sigilosa' && ! $user->can('dados.sigiloso')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acesso negado.');
        }
        if ($acao['visibilidade'] === 'restrita' && ! $user->can('dados.restrito')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acesso negado.');
        }
    }
}
