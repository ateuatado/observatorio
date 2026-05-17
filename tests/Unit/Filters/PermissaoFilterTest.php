<?php

namespace Tests\Unit\Filters;

use App\Filters\PermissaoFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;

/**
 * Testes unitários para PermissaoFilter.
 *
 * Coberturas:
 *   - O filtro implementa FilterInterface
 *   - O filtro tem os métodos before() e after()
 *   - after() sempre retorna null (sem efeito colateral)
 *   - Estrutura básica do filtro é instanciável
 *
 * Nota: Testes de comportamento real (redirecionamento 403, verificação
 * de permissão via Shield) requerem sessão autenticada e são cobertos
 * nos testes Feature de integração.
 */
class PermissaoFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected PermissaoFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new PermissaoFilter();
    }

    // -------------------------------------------------------------------------
    // Estrutura
    // -------------------------------------------------------------------------

    public function testFilterImplementaFilterInterface(): void
    {
        $this->assertInstanceOf(
            \CodeIgniter\Filters\FilterInterface::class,
            $this->filter
        );
    }

    public function testFilterTemMetodoBefore(): void
    {
        $this->assertTrue(
            method_exists($this->filter, 'before'),
            'PermissaoFilter deve ter método before()'
        );
    }

    public function testFilterTemMetodoAfter(): void
    {
        $this->assertTrue(
            method_exists($this->filter, 'after'),
            'PermissaoFilter deve ter método after()'
        );
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
    // Instância e diferença do AuthFilter
    // -------------------------------------------------------------------------

    public function testPermissaoFilterEDiferenteDauthFilter(): void
    {
        $this->assertNotInstanceOf(
            \App\Filters\AuthFilter::class,
            $this->filter,
            'PermissaoFilter não deve ser instância de AuthFilter'
        );
    }

    public function testPermissaoFilterEInstanciaCorreta(): void
    {
        $this->assertInstanceOf(PermissaoFilter::class, $this->filter);
    }
}
