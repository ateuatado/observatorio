<?php

namespace Tests\Feature\Controllers;

use Tests\Support\OvpTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integração para o módulo público de Ocorrências.
 *
 * Coberturas:
 *   - GET /ocorrencias               → 200
 *   - GET /ocorrencias/{id} publicado → 200
 *   - GET /ocorrencias/{id} rascunho  → PageNotFoundException (404)
 *   - GET /ocorrencias/novo           → redirect (sem login) via filtro session+permissao
 *   - GET /dashboard                  → redirect (sem login)
 *   - Filtro por tipo via query string → 200 + conteúdo
 */
class OcorrenciasPublicoTest extends OvpTestCase
{
    use FeatureTestTrait;

    // -------------------------------------------------------------------------
    // Listagem pública
    // -------------------------------------------------------------------------

    public function testListagemPublicaRetorna200(): void
    {
        $response = $this->get('ocorrencias');
        $response->assertStatus(200);
    }

    public function testListagemPublicaContemOcorrenciaPublicada(): void
    {
        $this->criarOcorrencia(['publicado' => 1, 'tipo_violencia' => 'chacina']);

        $response = $this->get('ocorrencias');
        $response->assertStatus(200);
        $response->assertSee('Chacina');
    }

    public function testListagemNaoContemOcorrenciaNaoPublicada(): void
    {
        // Cria ocorrência NÃO publicada com descrição única para checar ausência
        $this->criarOcorrencia([
            'publicado'       => 0,
            'tipo_violencia'  => 'tortura',
            'descricao_livre' => 'TEXTO_UNICO_NAO_PUBLICADO_XYZ',
        ]);

        $response = $this->get('ocorrencias');
        $response->assertStatus(200);
        // A descrição exclusiva não deve aparecer no HTML público
        $response->assertDontSee('TEXTO_UNICO_NAO_PUBLICADO_XYZ');
    }

    public function testFiltroTipoViolencia(): void
    {
        $this->criarOcorrencia(['publicado' => 1, 'tipo_violencia' => 'tortura']);
        $this->criarOcorrencia(['publicado' => 1, 'tipo_violencia' => 'execucao']);

        $response = $this->get('ocorrencias?tipo=tortura');
        $response->assertStatus(200);
        $response->assertSee('Tortura');
    }

    // -------------------------------------------------------------------------
    // Detalhe público
    // -------------------------------------------------------------------------

    public function testDetalhePublicoOcorrenciaPublicadaRetorna200(): void
    {
        $id = $this->criarOcorrencia(['publicado' => 1]);
        $response = $this->get("ocorrencias/{$id}");
        $response->assertStatus(200);
    }

    public function testDetalhePublicoOcorrenciaNaoPublicadaRetorna404(): void
    {
        $id = $this->criarOcorrencia(['publicado' => 0]);

        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get("ocorrencias/{$id}");
    }

    public function testDetalhePublicoIdInexistenteRetorna404(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('ocorrencias/99999');
    }

    // -------------------------------------------------------------------------
    // Rotas autenticadas — devem redirecionar sem login
    // -------------------------------------------------------------------------

    public function testRotaNovoRedirecionaSemLogin(): void
    {
        $response = $this->get('ocorrencias/novo');
        $response->assertRedirect();
    }

    public function testDashboardRedirecionaSemLogin(): void
    {
        $response = $this->get('dashboard');
        $response->assertRedirect();
    }

    public function testRotaAcoesSegurancaRedirecionaSemLogin(): void
    {
        $response = $this->get('acoes-seguranca');
        $response->assertRedirect();
    }
}
