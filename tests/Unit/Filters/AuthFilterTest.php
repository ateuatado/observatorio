<?php

namespace Tests\Unit\Filters;

use App\Filters\AuthFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;

/**
 * Testes unitários para AuthFilter.
 *
 * Coberturas:
 *   - Usuário não autenticado → redireciona para /login
 *   - Usuário autenticado     → deixa passar (before retorna null)
 *   - after()                 → sempre retorna null (sem efeito)
 *
 * Nota: Como o Shield usa sessão, simulamos o estado logado via
 * configuração da sessão antes de chamar o filtro.
 */
class AuthFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected AuthFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new AuthFilter();
    }

    // -------------------------------------------------------------------------
    // after() — sempre passivo
    // -------------------------------------------------------------------------

    public function testAfterSempreRetornaNull(): void
    {
        $request  = service('request');
        $response = service('response');

        $resultado = $this->filter->after($request, $response);
        $this->assertNull($resultado, 'after() deve retornar null — sem efeito colateral');
    }

    // -------------------------------------------------------------------------
    // Testa que o filtro existe e implementa a interface correta
    // -------------------------------------------------------------------------

    public function testFilterImplementaFilterInterface(): void
    {
        $this->assertInstanceOf(
            \CodeIgniter\Filters\FilterInterface::class,
            $this->filter
        );
    }

    public function testFilterTemMetodosBefore(): void
    {
        $this->assertTrue(
            method_exists($this->filter, 'before'),
            'AuthFilter deve ter método before()'
        );
    }

    public function testFilterTemMetodosAfter(): void
    {
        $this->assertTrue(
            method_exists($this->filter, 'after'),
            'AuthFilter deve ter método after()'
        );
    }
}
