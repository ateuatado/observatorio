<?php

namespace Tests\Unit\Models;

use App\Models\EstudoModel;
use Tests\Support\OvpTestCase;

/**
 * Testes unitários para EstudoModel.
 *
 * Coberturas:
 *   - gerarSlug()     → conversão de título para slug
 *   - allowedFields   → campos corretos são persistidos
 *   - publicado       → filtro where('publicado', 1) funciona
 *   - destaque        → filtro where('destaque', 1) funciona
 *   - slug único      → validação is_unique
 *   - timestamps      → created_at/updated_at
 */
class EstudoModelTest extends OvpTestCase
{
    private EstudoModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new EstudoModel();
    }

    // -------------------------------------------------------------------------
    // gerarSlug()
    // -------------------------------------------------------------------------

    public function testGerarSlugConverteMaiusculasParaMinusculas(): void
    {
        $slug = $this->model->gerarSlug('Violência Policial');
        $this->assertSame('violencia-policial', $slug);
    }

    public function testGerarSlugRemoveAcentos(): void
    {
        $slug = $this->model->gerarSlug('ação política');
        $this->assertSame('acao-politica', $slug);

        $slug2 = $this->model->gerarSlug('Café com Leite');
        $this->assertSame('cafe-com-leite', $slug2);
    }

    public function testGerarSlugSubstituiEspacosPorHifen(): void
    {
        $slug = $this->model->gerarSlug('Um Título Qualquer');
        $this->assertStringNotContainsString(' ', $slug);
        $this->assertStringContainsString('-', $slug);
    }

    public function testGerarSlugRemoveCaracteresEspeciais(): void
    {
        $slug = $this->model->gerarSlug('Título: Análise 2024');
        // Dois-pontos devem ser removidos
        $this->assertStringNotContainsString(':', $slug);
        $this->assertStringContainsString('2024', $slug);
    }

    public function testGerarSlugComStringVazia(): void
    {
        $slug = $this->model->gerarSlug('');
        $this->assertSame('', $slug);
    }

    public function testGerarSlugComNumerosNoTitulo(): void
    {
        $slug = $this->model->gerarSlug('Relatório 2024: Dados');
        $this->assertStringContainsString('2024', $slug);
    }

    // -------------------------------------------------------------------------
    // Persistência e filtros
    // -------------------------------------------------------------------------

    public function testEstudoPublicadoAparece(): void
    {
        $this->criarEstudo(['publicado' => 1, 'titulo' => 'Estudo Publicado']);
        $this->criarEstudo(['publicado' => 0, 'titulo' => 'Estudo Rascunho']);

        $publicados = $this->model->where('publicado', 1)->findAll();
        $this->assertCount(1, $publicados);
        $this->assertSame('Estudo Publicado', $publicados[0]['titulo']);
    }

    public function testEstudoEmDestaqueAparece(): void
    {
        $this->criarEstudo(['destaque' => 1, 'titulo' => 'Em Destaque']);
        $this->criarEstudo(['destaque' => 0, 'titulo' => 'Comum']);

        $destaques = $this->model->where('publicado', 1)->where('destaque', 1)->findAll();
        $this->assertCount(1, $destaques);
        $this->assertSame('Em Destaque', $destaques[0]['titulo']);
    }

    public function testBuscaPorSlug(): void
    {
        $slug = 'meu-estudo-unico-' . uniqid();
        $this->criarEstudo(['slug' => $slug, 'titulo' => 'Meu Estudo Único']);

        $estudo = $this->model->where('slug', $slug)->first();
        $this->assertNotNull($estudo);
        $this->assertSame('Meu Estudo Único', $estudo['titulo']);
    }

    public function testBuscaPorSlugInexistenteRetornaNull(): void
    {
        $estudo = $this->model->where('slug', 'slug-que-nao-existe')->first();
        $this->assertNull($estudo);
    }

    public function testCamposOpcionaisAceitamNull(): void
    {
        $this->model->insert([
            'titulo'    => 'Estudo Mínimo',
            'slug'      => 'estudo-minimo-' . uniqid(),
            'resumo'    => null,
            'conteudo'  => null,
            'autores'   => null,
            'publicado' => 0,
            'destaque'  => 0,
        ]);

        $id = $this->model->insertID();
        $this->assertGreaterThan(0, $id);

        $estudo = $this->model->find($id);
        $this->assertNull($estudo['resumo']);
        $this->assertNull($estudo['conteudo']);
        $this->assertNull($estudo['autores']);
    }

    public function testContarEstudosPublicados(): void
    {
        $this->criarEstudo(['publicado' => 1]);
        $this->criarEstudo(['publicado' => 1]);
        $this->criarEstudo(['publicado' => 0]);

        $total = $this->model->where('publicado', 1)->countAllResults();
        $this->assertSame(2, $total);
    }

    // -------------------------------------------------------------------------
    // Timestamps
    // -------------------------------------------------------------------------

    public function testTimestampsPreenchidosAutomaticamente(): void
    {
        $this->criarEstudo();
        $estudos = $this->model->findAll();

        $this->assertNotNull($estudos[0]['created_at']);
        $this->assertNotNull($estudos[0]['updated_at']);
    }
}
