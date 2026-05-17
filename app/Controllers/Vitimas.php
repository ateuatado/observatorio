<?php

namespace App\Controllers;

use App\Models\VitimaModel;

class Vitimas extends BaseController
{
    protected VitimaModel $vitimaModel;

    public function __construct()
    {
        $this->vitimaModel = new VitimaModel();
    }

    // ================================================================
    // ÁREA AUTENTICADA
    // ================================================================

    /**
     * Listagem de vítimas com busca e paginação.
     */
    public function index(): string
    {
        $busca    = $this->request->getGet('q') ?? '';
        $pagina   = max(1, (int)($this->request->getGet('p') ?? 1));
        $porPagina = 20;
        $offset   = ($pagina - 1) * $porPagina;

        $vitimas = $this->vitimaModel->listarComCasos($porPagina, $offset, $busca);
        $total   = $this->vitimaModel->contarTotal($busca);

        return view('vitimas/index', [
            'title'     => 'Vítimas cadastradas',
            'breadcrumb'=> 'Vítimas',
            'vitimas'   => $vitimas,
            'total'     => $total,
            'pagina'    => $pagina,
            'porPagina' => $porPagina,
            'busca'     => $busca,
        ]);
    }

    /**
     * Detalhe de uma vítima com seus casos vinculados.
     */
    public function show(int $id): string
    {
        $vitima = $this->vitimaModel->find($id);

        if (!$vitima) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Vítima #{$id} não encontrada.");
        }

        // Ocorrências vinculadas a esta vítima
        $casos = $this->db->table('ocorrencia_vitima cv')
            ->select('cv.resultado, cv.ferimentos, cv.identificada, c.id, c.protocolo_ovp, c.data_fato, c.tipo_violencia, l.municipio, l.bairro')
            ->join('ocorrencias c', 'c.id = cv.ocorrencia_id', 'left')
            ->join('localizacoes l', 'l.id = c.localizacao_id', 'left')
            ->where('cv.vitima_id', $id)
            ->orderBy('c.data_fato', 'DESC')
            ->get()->getResultArray();

        return view('vitimas/show', [
            'title'     => 'Vítima — ' . ($vitima['nome'] ?? 'Não identificada'),
            'breadcrumb'=> 'Vítimas',
            'vitima'    => $vitima,
            'casos'     => $casos,
        ]);
    }

    /**
     * Formulário para nova vítima avulsa.
     */
    public function novo(): string
    {
        return view('vitimas/form', [
            'title'     => 'Cadastrar vítima',
            'breadcrumb'=> 'Vítimas',
            'vitima'    => null,
        ]);
    }

    /**
     * Salva uma nova vítima avulsa (sem vínculo de caso).
     */
    public function salvar()
    {
        $rules = [
            'nome' => 'permit_empty|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->vitimaModel->insert([
            'nome'             => $this->request->getPost('nome') ?: null,
            'data_nascimento'  => $this->request->getPost('data_nascimento') ?: null,
            'idade_aparente'   => $this->request->getPost('idade_aparente') ?: null,
            'sexo'             => $this->request->getPost('sexo') ?: null,
            'raca_cor'         => $this->request->getPost('raca_cor') ?: null,
            'profissao'        => $this->request->getPost('profissao') ?: null,
            'condicao_juridica'=> $this->request->getPost('condicao_juridica') ?: null,
            'menor_de_idade'   => (int)$this->request->getPost('menor_de_idade') ?: 0,
            'gestante'         => (int)$this->request->getPost('gestante') ?: 0,
            'pcd'              => (int)$this->request->getPost('pcd') ?: 0,
            'antecedentes_versao_policial' => $this->request->getPost('antecedentes_versao_policial') ?: null,
            'observacoes'      => $this->request->getPost('observacoes') ?: null,
        ]);

        $id = $this->vitimaModel->insertID();
        return redirect()->to("vitimas/{$id}")->with('message', 'Vítima cadastrada com sucesso!');
    }

    /**
     * Formulário de edição.
     */
    public function editar(int $id): string
    {
        $vitima = $this->vitimaModel->find($id);

        if (!$vitima) {
            return redirect()->to('vitimas')->with('error', 'Vítima não encontrada.');
        }

        return view('vitimas/form', [
            'title'     => 'Editar vítima',
            'breadcrumb'=> 'Vítimas',
            'vitima'    => $vitima,
        ]);
    }

    /**
     * Atualiza uma vítima existente.
     */
    public function update(int $id)
    {
        $vitima = $this->vitimaModel->find($id);
        if (!$vitima) {
            return redirect()->to('vitimas')->with('error', 'Vítima não encontrada.');
        }

        $this->vitimaModel->update($id, [
            'nome'             => $this->request->getPost('nome') ?: null,
            'data_nascimento'  => $this->request->getPost('data_nascimento') ?: null,
            'idade_aparente'   => $this->request->getPost('idade_aparente') ?: null,
            'sexo'             => $this->request->getPost('sexo') ?: null,
            'raca_cor'         => $this->request->getPost('raca_cor') ?: null,
            'profissao'        => $this->request->getPost('profissao') ?: null,
            'condicao_juridica'=> $this->request->getPost('condicao_juridica') ?: null,
            'menor_de_idade'   => (int)$this->request->getPost('menor_de_idade') ?: 0,
            'gestante'         => (int)$this->request->getPost('gestante') ?: 0,
            'pcd'              => (int)$this->request->getPost('pcd') ?: 0,
            'antecedentes_versao_policial' => $this->request->getPost('antecedentes_versao_policial') ?: null,
            'observacoes'      => $this->request->getPost('observacoes') ?: null,
        ]);

        return redirect()->to("vitimas/{$id}")->with('message', 'Dados da vítima atualizados!');
    }

    /**
     * Exclui uma vítima (somente se não vinculada a casos).
     */
    public function deletar(int $id)
    {
        $vinculada = $this->db->table('ocorrencia_vitima')
            ->where('vitima_id', $id)
            ->countAllResults();

        if ($vinculada > 0) {
            return redirect()->back()->with('error', 'Esta vítima está vinculada a um ou mais casos e não pode ser excluída diretamente. Remova o vínculo pelo formulário do caso.');
        }

        $this->vitimaModel->delete($id);
        return redirect()->to('vitimas')->with('message', 'Vítima removida.');
    }
}
