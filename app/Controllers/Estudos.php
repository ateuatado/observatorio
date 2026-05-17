<?php

namespace App\Controllers;

use App\Models\EstudoModel;

class Estudos extends BaseController
{
    protected EstudoModel $estudoModel;

    public function __construct()
    {
        $this->estudoModel = new EstudoModel();
    }

    // ================================================================
    // ÁREA PÚBLICA
    // ================================================================

    /**
     * Listagem pública de estudos publicados.
     */
    public function index(): string
    {
        $busca    = $this->request->getGet('q') ?? '';
        $pagina   = max(1, (int)($this->request->getGet('p') ?? 1));
        $porPagina = 9;
        $offset   = ($pagina - 1) * $porPagina;

        $builder = $this->estudoModel->where('publicado', 1);

        if ($busca) {
            $builder->groupStart()
                ->like('titulo', $busca)
                ->orLike('resumo', $busca)
                ->orLike('autores', $busca)
                ->groupEnd();
        }

        $total   = $builder->countAllResults(false);
        $estudos = $builder->orderBy('created_at', 'DESC')->limit($porPagina, $offset)->findAll();

        return view('estudos/index', [
            'title'     => 'Estudos e Publicações',
            'estudos'   => $estudos,
            'total'     => $total,
            'pagina'    => $pagina,
            'porPagina' => $porPagina,
            'busca'     => $busca,
        ]);
    }

    /**
     * Detalhe público de um estudo (por slug).
     */
    public function show(string $slug): string
    {
        $estudo = $this->estudoModel->where('slug', $slug)->where('publicado', 1)->first();

        if (!$estudo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Estudo '{$slug}' não encontrado.");
        }

        return view('estudos/show', [
            'title'       => esc($estudo['titulo']),
            'estudo'      => $estudo,
        ]);
    }

    // ================================================================
    // ÁREA AUTENTICADA
    // ================================================================

    /**
     * Listagem administrativa (todos, incluindo rascunhos).
     */
    public function listar(): string
    {
        $busca    = $this->request->getGet('q') ?? '';
        $pagina   = max(1, (int)($this->request->getGet('p') ?? 1));
        $porPagina = 15;
        $offset   = ($pagina - 1) * $porPagina;

        $builder = $this->estudoModel;
        if ($busca) {
            $builder->groupStart()
                ->like('titulo', $busca)
                ->orLike('autores', $busca)
                ->groupEnd();
        }

        $total   = $builder->countAllResults(false);
        $estudos = $builder->orderBy('created_at', 'DESC')->limit($porPagina, $offset)->findAll();

        return view('estudos/admin_index', [
            'title'     => 'Gerenciar Estudos',
            'breadcrumb'=> 'Estudos',
            'estudos'   => $estudos,
            'total'     => $total,
            'pagina'    => $pagina,
            'porPagina' => $porPagina,
            'busca'     => $busca,
        ]);
    }

    /**
     * Formulário para novo estudo.
     */
    public function novo(): string
    {
        return view('estudos/form', [
            'title'     => 'Publicar novo estudo',
            'breadcrumb'=> 'Estudos',
            'estudo'    => null,
        ]);
    }

    /**
     * Salva um novo estudo.
     */
    public function salvar()
    {
        $titulo = $this->request->getPost('titulo') ?? '';
        $slug   = $this->estudoModel->gerarSlug($titulo);

        // Garantir unicidade do slug
        $existente = $this->estudoModel->where('slug', $slug)->first();
        if ($existente) {
            $slug .= '-' . time();
        }

        $rules = [
            'titulo' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->estudoModel->insert([
            'titulo'     => $titulo,
            'slug'       => $slug,
            'resumo'     => $this->request->getPost('resumo') ?: null,
            'conteudo'   => $this->request->getPost('conteudo') ?: null,
            'autores'    => $this->request->getPost('autores') ?: null,
            'publicado'  => (int)($this->request->getPost('publicado') ?? 0),
            'destaque'   => (int)($this->request->getPost('destaque') ?? 0),
        ]);

        $id = $this->estudoModel->insertID();

        // Handle PDF upload
        $pdf = $this->request->getFile('arquivo_pdf');
        if ($pdf && $pdf->isValid() && !$pdf->hasMoved()) {
            $novoNome = $slug . '.' . $pdf->getExtension();
            $pdf->move(FCPATH . 'arquivos/estudos', $novoNome);
            $this->estudoModel->update($id, ['arquivo_pdf' => 'arquivos/estudos/' . $novoNome]);
        }

        $estudo = $this->estudoModel->find($id);
        $msg = $estudo['publicado'] ? 'Estudo publicado com sucesso!' : 'Estudo salvo como rascunho.';

        return redirect()->to("estudos/{$slug}")->with('message', $msg);
    }

    /**
     * Formulário de edição.
     */
    public function editar(string $slug): string
    {
        $estudo = $this->estudoModel->where('slug', $slug)->first();

        if (!$estudo) {
            return redirect()->to('estudos/admin')->with('error', 'Estudo não encontrado.');
        }

        return view('estudos/form', [
            'title'     => 'Editar: ' . $estudo['titulo'],
            'breadcrumb'=> 'Estudos',
            'estudo'    => $estudo,
        ]);
    }

    /**
     * Atualiza um estudo existente.
     */
    public function update(int $id)
    {
        $estudo = $this->estudoModel->find($id);
        if (!$estudo) {
            return redirect()->to('estudos/admin')->with('error', 'Estudo não encontrado.');
        }

        $rules = ['titulo' => 'required|min_length[5]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'titulo'    => $this->request->getPost('titulo'),
            'resumo'    => $this->request->getPost('resumo') ?: null,
            'conteudo'  => $this->request->getPost('conteudo') ?: null,
            'autores'   => $this->request->getPost('autores') ?: null,
            'publicado' => (int)($this->request->getPost('publicado') ?? 0),
            'destaque'  => (int)($this->request->getPost('destaque') ?? 0),
        ];

        // Atualizar slug se título mudou
        if ($dados['titulo'] !== $estudo['titulo']) {
            $novoSlug = $this->estudoModel->gerarSlug($dados['titulo']);
            $existente = $this->estudoModel->where('slug', $novoSlug)->where('id !=', $id)->first();
            $dados['slug'] = $existente ? $novoSlug . '-' . $id : $novoSlug;
        }

        $pdf = $this->request->getFile('arquivo_pdf');
        if ($pdf && $pdf->isValid() && !$pdf->hasMoved()) {
            $slug = $dados['slug'] ?? $estudo['slug'];
            $novoNome = $slug . '.' . $pdf->getExtension();
            $pdf->move(FCPATH . 'arquivos/estudos', $novoNome);
            $dados['arquivo_pdf'] = 'arquivos/estudos/' . $novoNome;
        }

        $this->estudoModel->update($id, $dados);

        $slugFinal = $dados['slug'] ?? $estudo['slug'];
        return redirect()->to("estudos/{$slugFinal}")->with('message', 'Estudo atualizado com sucesso!');
    }

    /**
     * Alterna publicação de um estudo.
     */
    public function publicar(int $id)
    {
        $estudo = $this->estudoModel->find($id);
        if (!$estudo) return redirect()->to('estudos/admin')->with('error', 'Estudo não encontrado.');

        $novo = $estudo['publicado'] ? 0 : 1;
        $this->estudoModel->update($id, ['publicado' => $novo]);

        $msg = $novo ? 'Estudo publicado!' : 'Estudo despublicado.';
        return redirect()->back()->with('message', $msg);
    }

    /**
     * Exclui um estudo.
     */
    public function deletar(int $id)
    {
        $this->estudoModel->delete($id);
        return redirect()->to('estudos/admin')->with('message', 'Estudo removido.');
    }
}
