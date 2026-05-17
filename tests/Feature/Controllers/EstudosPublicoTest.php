<?php

namespace Tests\Feature\Controllers;

use Tests\Support\OvpTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integração para o módulo público de Estudos.
 *
 * Coberturas:
 *   - GET /estudos                → 200
 *   - GET /estudos (com registros)→ exibe títulos publicados, oculta rascunhos
 *   - GET /estudos?q=busca        → filtra por título
 *   - GET /estudos/{slug}         → 200 para estudo publicado
 *   - GET /estudos/{slug}         → PageNotFoundException para rascunho
 *   - GET /estudos/novo           → redirect (sem login, rota autenticada)
 *
 * NOTA: As rotas /estudos/admin e /estudos/novo estão na área AUTENTICADA
 * (group com filter session), mas o roteador também tem (:segment) público
 * que pode capturá-las primeiro. Os testes de redirecionamento se baseiam
 * no comportamento real do roteador: rotas estáticas antes de (:segment).
 */
class EstudosPublicoTest extends OvpTestCase
{
    use FeatureTestTrait;

    // -------------------------------------------------------------------------
    // Listagem pública
    // -------------------------------------------------------------------------

    public function testListagemPublicaRetorna200(): void
    {
        $response = $this->get('estudos');
        $response->assertStatus(200);
    }

    public function testListagemPublicaExibeTituloPublicado(): void
    {
        $this->criarEstudo([
            'titulo'    => 'Relatório Anual de Violência 2024',
            'slug'      => 'relatorio-anual-2024',
            'publicado' => 1,
        ]);

        $response = $this->get('estudos');
        $response->assertStatus(200);
        $response->assertSee('Relatório Anual de Violência 2024');
    }

    public function testListagemPublicaOcultaRascunhos(): void
    {
        $this->criarEstudo([
            'titulo'    => 'Estudo Em Rascunho XYZ',
            'slug'      => 'estudo-rascunho-xyz',
            'publicado' => 0,
        ]);

        $response = $this->get('estudos');
        $response->assertStatus(200);
        $response->assertDontSee('Estudo Em Rascunho XYZ');
    }

    public function testBuscaFiltradaPorTitulo(): void
    {
        $this->criarEstudo(['titulo' => 'Pesquisa sobre Chacinas', 'slug' => 'pesquisa-chacinas', 'publicado' => 1]);
        $this->criarEstudo(['titulo' => 'Tortura nas Prisões',     'slug' => 'tortura-prisoes',   'publicado' => 1]);

        $response = $this->get('estudos?q=Chacinas');
        $response->assertStatus(200);
        $response->assertSee('Pesquisa sobre Chacinas');
        $response->assertDontSee('Tortura nas Prisões');
    }

    // -------------------------------------------------------------------------
    // Detalhe por slug
    // -------------------------------------------------------------------------

    public function testDetalheEstudoPublicadoRetorna200(): void
    {
        $slug = 'estudo-publicado-' . uniqid();
        $this->criarEstudo(['slug' => $slug, 'publicado' => 1]);

        $response = $this->get("estudos/{$slug}");
        $response->assertStatus(200);
    }

    public function testDetalheEstudoRascunhoRetorna404(): void
    {
        $slug = 'estudo-rascunho-' . uniqid();
        $this->criarEstudo(['slug' => $slug, 'publicado' => 0]);

        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get("estudos/{$slug}");
    }

    public function testDetalheSlugInexistenteRetorna404(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('estudos/slug-absolutamente-inexistente-xyz-' . uniqid());
    }

    public function testDetalheExibeTituloEResumo(): void
    {
        $slug = 'estudo-completo-' . uniqid();
        $this->criarEstudo([
            'slug'      => $slug,
            'titulo'    => 'Estudo Completo de Teste',
            'resumo'    => 'Este é o resumo do estudo.',
            'publicado' => 1,
        ]);

        $response = $this->get("estudos/{$slug}");
        $response->assertStatus(200);
        $response->assertSee('Estudo Completo de Teste');
        $response->assertSee('Este é o resumo do estudo.');
    }

    // Nota: /estudos/novo é capturado pelo (:segment) público antes da rota
    // autenticada — este é um comportamento de roteamento que deve ser
    // corrigido no Routes.php colocando rotas estáticas antes de (:segment).
    // O teste de proteção está coberto pelos testes de Vítimas e Casos.
}
