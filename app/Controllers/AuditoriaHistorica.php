<?php

namespace App\Controllers;

use App\Models\AcervoDocumentoModel;
use App\Services\PdfIndexerService;
use App\Services\PdfAnalysisService;
use App\Services\CasoImportService;

/**
 * Controller do módulo de Auditoria Histórica.
 *
 * Gerencia o ciclo de vida completo dos documentos do acervo:
 *   index      → listagem com filtros e dashboard de progresso
 *   show       → ficha do documento (miniatura + resumo IA + status)
 *   analisar   → dispara análise por IA/heurística
 *   auditar    → formulário pré-preenchido para revisão do pesquisador
 *   importar   → confirma importação como Caso
 *   descartar  → marca como descartado
 *   reindexar  → varre o filesystem por novos PDFs
 *   servirArquivo → serve o PDF via readfile() de forma segura
 */
class AuditoriaHistorica extends BaseController
{
    protected AcervoDocumentoModel $model;

    public function __construct()
    {
        $this->model = new AcervoDocumentoModel();
    }

    // =========================================================================
    // LISTAGEM
    // =========================================================================

    public function index(): string
    {
        $filtros = [
            'ano'    => $this->request->getGet('ano'),
            'mes'    => $this->request->getGet('mes'),
            'status' => $this->request->getGet('status'),
            'tipo'   => $this->request->getGet('tipo'),
            'q'      => $this->request->getGet('q'),
            'page'   => max(1, (int)($this->request->getGet('p') ?? 1)),
            'per_page' => 50,
        ];

        $documentos = $this->model->listarComFiltros($filtros);
        $total      = $this->model->contarComFiltros($filtros);
        $stats      = $this->model->contarPorStatus();
        $anos       = $this->model->anosDisponiveis();

        return view('auditoria/index', [
            'title'       => 'Auditoria Histórica',
            'documentos'  => $documentos,
            'total'       => $total,
            'stats'       => $stats,
            'anos'        => $anos,
            'filtros'     => $filtros,
            'totalPaginas'=> (int)ceil($total / $filtros['per_page']),
        ]);
    }

    // =========================================================================
    // FICHA DO DOCUMENTO
    // =========================================================================

    public function show(int $id): string
    {
        $doc = $this->model->find($id);
        if (!$doc) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Documento #{$id} não encontrado.");
        }

        // Link para o caso importado (se existir)
        $casoImportado = null;
        if ($doc['caso_id']) {
            $casoImportado = (new \App\Models\CasoModel())->find($doc['caso_id']);
        }

        return view('auditoria/show', [
            'title'         => 'Documento: ' . mb_substr($doc['nome_arquivo'], 0, 60),
            'doc'           => $doc,
            'casoImportado' => $casoImportado,
        ]);
    }

    // =========================================================================
    // ANÁLISE POR IA
    // =========================================================================

    public function analisar(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $doc = $this->model->find($id);
        if (!$doc) {
            return redirect()->to('auditoria-historica')->with('erro', 'Documento não encontrado.');
        }

        try {
            $service = new PdfAnalysisService();
            $analise = $service->analisar($id);

            return redirect()
                ->to("auditoria-historica/{$id}")
                ->with('sucesso', 'Análise concluída. Tipo identificado: ' . ucfirst($analise['tipo']));
        } catch (\Exception $e) {
            log_message('error', "AuditoriaHistorica::analisar #{$id}: " . $e->getMessage());
            return redirect()
                ->to("auditoria-historica/{$id}")
                ->with('erro', 'Falha na análise: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // AUDITORIA — formulário de revisão
    // =========================================================================

    public function auditar(int $id): string
    {
        $doc = $this->model->find($id);
        if (!$doc) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Documento #{$id} não encontrado.");
        }

        // Verifica se já foi importado
        if ($doc['status'] === 'importado') {
            return redirect()
                ->to("auditoria-historica/{$id}")
                ->with('aviso', 'Este documento já foi importado.');
        }

        // Soft-lock: marca como "auditando" se ainda estiver pendente
        $user = auth()->user();
        if ($doc['status'] === 'pendente') {
            $this->model->iniciarAuditoria($id, $user->id);
            $doc['status'] = 'auditando';
        }

        // Campos sugeridos pela IA
        $camposIa = [];
        if ($doc['dados_extraidos_ia']) {
            $camposIa = json_decode($doc['dados_extraidos_ia'], true) ?? [];
        }

        return view('auditoria/auditar', [
            'title'    => 'Auditar Documento',
            'doc'      => $doc,
            'camposIa' => $camposIa,
        ]);
    }

    // =========================================================================
    // IMPORTAR COMO CASO
    // =========================================================================

    public function importar(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $doc = $this->model->find($id);
        if (!$doc) {
            return redirect()->to('auditoria-historica')->with('erro', 'Documento não encontrado.');
        }

        if ($doc['status'] === 'importado') {
            return redirect()
                ->to("auditoria-historica/{$id}")
                ->with('aviso', "Já importado como Caso #{$doc['caso_id']}.");
        }

        $user    = auth()->user();
        $dados   = $this->request->getPost();

        // Estrutura vítimas do POST (campos vitimas[0][nome], vitimas[0][sexo], ...)
        $vitimas = [];
        if (!empty($dados['vitimas']) && is_array($dados['vitimas'])) {
            $vitimas = $dados['vitimas'];
        }

        $agentes = [];
        if (!empty($dados['agentes']) && is_array($dados['agentes'])) {
            $agentes = $dados['agentes'];
        }

        $dadosAuditados = array_merge($dados, [
            'vitimas' => $vitimas,
            'agentes' => $agentes,
        ]);

        $service    = new CasoImportService();
        $resultado  = $service->importarComoCaso($id, $dadosAuditados, $user->id);

        if ($resultado['sucesso']) {
            return redirect()
                ->to("ocorrencias/{$resultado['caso_id']}")
                ->with('sucesso', "Caso #{$resultado['caso_id']} criado com sucesso a partir do documento histórico.");
        }

        return redirect()
            ->to("auditoria-historica/{$id}/auditar")
            ->with('erro', $resultado['erro'] ?? 'Falha na importação.');
    }

    // =========================================================================
    // DESCARTAR
    // =========================================================================

    public function descartar(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $nota  = $this->request->getPost('nota') ?? '';
        $user  = auth()->user();

        $this->model->descartar($id, $nota, $user->id);

        return redirect()
            ->to('auditoria-historica')
            ->with('sucesso', 'Documento descartado.');
    }

    // =========================================================================
    // REINDEXAR
    // =========================================================================

    public function reindexar(): \CodeIgniter\HTTP\RedirectResponse
    {
        $service = new PdfIndexerService();

        set_time_limit(600); // 10 min para varrer o acervo completo
        $stats = $service->indexar();

        $msg = "Indexação concluída: {$stats['novos']} novos, {$stats['ja_existentes']} já existentes, {$stats['erros']} erros.";

        return redirect()
            ->to('auditoria-historica')
            ->with('sucesso', $msg);
    }

    // =========================================================================
    // SERVIR ARQUIVO PDF (seguro — via readfile)
    // =========================================================================

    public function servirArquivo(int $id): void
    {
        $doc = $this->model->find($id);
        if (!$doc) {
            show_404();
            return;
        }

        $caminhoAbsoluto = ROOTPATH . $doc['caminho_relativo'];

        if (!file_exists($caminhoAbsoluto)) {
            show_404();
            return;
        }

        // Headers de segurança: serve o arquivo sem expor o caminho real
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($caminhoAbsoluto));
        header('Content-Disposition: inline; filename="' . addslashes(basename($caminhoAbsoluto)) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');

        readfile($caminhoAbsoluto);
        exit;
    }
}
