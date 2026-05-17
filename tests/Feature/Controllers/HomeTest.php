<?php

namespace Tests\Feature\Controllers;

use Tests\Support\OvpTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integração para as rotas públicas (Home).
 *
 * Coberturas:
 *   - GET /     → 200
 *   - GET /sobre → 200
 *   - Página inicial contém links para casos e estudos
 *
 * NOTA: Rota inexistente lança PageNotFoundException (não retorna 404 response)
 * no ambiente de testes CI4. Por isso usamos expectException em vez de assertStatus.
 */
class HomeTest extends OvpTestCase
{
    use FeatureTestTrait;

    public function testHomeRetorna200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testSobreRetorna200(): void
    {
        $response = $this->get('sobre');
        $response->assertStatus(200);
    }

    public function testRotaInexistenteRetorna404(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('rota-que-nao-existe-xyz-abc-' . uniqid());
    }

    public function testHomeContemLinkParaCasos(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('casos');
    }

    public function testHomeContemLinkParaEstudos(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('estudos');
    }
}
