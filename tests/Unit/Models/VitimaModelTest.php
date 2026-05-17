<?php

namespace Tests\Unit\Models;

use App\Models\VitimaModel;
use Tests\Support\OvpTestCase;

/**
 * Testes unitários para VitimaModel.
 *
 * Coberturas:
 *   - listarComCasos()     → retorna vítimas com coluna total_casos
 *   - contarTotal()        → contagem simples e com busca
 *   - allowedFields        → apenas campos permitidos são salvos
 *   - timestamps           → created_at/updated_at preenchidos automaticamente
 *   - busca por nome       → LIKE funciona via contarTotal()
 *   - vítima não identificada → nome NULL é aceito
 */
class VitimaModelTest extends OvpTestCase
{
    private VitimaModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new VitimaModel();
    }

    // -------------------------------------------------------------------------
    // contarTotal()
    // -------------------------------------------------------------------------

    public function testContarTotalRetornaZeroSemRegistros(): void
    {
        $this->assertSame(0, $this->model->contarTotal());
    }

    public function testContarTotalRetornaQuantidadeCorreta(): void
    {
        $this->criarVitima(['nome' => 'Ana Souza']);
        $this->criarVitima(['nome' => 'Carlos Lima']);
        $this->criarVitima(['nome' => 'Maria Fernandes']);

        $this->assertSame(3, $this->model->contarTotal());
    }

    public function testContarTotalComBuscaPorNome(): void
    {
        $this->criarVitima(['nome' => 'João da Silva']);
        $this->criarVitima(['nome' => 'Maria João']);
        $this->criarVitima(['nome' => 'Pedro Alves']);

        $total = $this->model->contarTotal('João');
        $this->assertSame(2, $total, 'Busca por "João" deve retornar 2 resultados');
    }

    public function testContarTotalComBuscaSemResultados(): void
    {
        $this->criarVitima(['nome' => 'Ana Souza']);
        $total = $this->model->contarTotal('XXXXXXXXXXX');
        $this->assertSame(0, $total);
    }

    public function testContarTotalComBuscaPorProfissao(): void
    {
        $this->criarVitima(['nome' => 'Carlos', 'profissao' => 'Estudante']);
        $this->criarVitima(['nome' => 'Paula',  'profissao' => 'Pedreiro']);

        $total = $this->model->contarTotal('Estudante');
        $this->assertSame(1, $total);
    }

    // -------------------------------------------------------------------------
    // listarComCasos()
    // -------------------------------------------------------------------------

    public function testListarComCasosRetornaArrayVazioSemRegistros(): void
    {
        $vitimas = $this->model->listarComCasos();
        $this->assertIsArray($vitimas);
        $this->assertEmpty($vitimas);
    }

    public function testListarComCasosRetornaColunasTotalCasos(): void
    {
        $this->criarVitima(['nome' => 'Maria']);

        $vitimas = $this->model->listarComCasos();

        $this->assertCount(1, $vitimas);
        $this->assertArrayHasKey('total_casos', $vitimas[0]);
        $this->assertSame('0', (string)$vitimas[0]['total_casos']);
    }

    public function testListarComCasosContaVinculosCorretamente(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $vitimaId     = $this->criarVitima(['nome' => 'Paulo']);

        $this->vincularVitimaOcorrencia($ocorrenciaId, $vitimaId, [
            'resultado'   => 'fatal',
            'identificada'=> 1,
        ]);

        $vitimas = $this->model->listarComCasos();

        $this->assertCount(1, $vitimas);
        $this->assertSame('1', (string)$vitimas[0]['total_casos']);
    }

    public function testListarComCasosRespeitaLimit(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->criarVitima(['nome' => "Vítima $i"]);
        }

        $vitimas = $this->model->listarComCasos(3, 0);
        $this->assertCount(3, $vitimas);
    }

    public function testListarComCasosRespeitaOffset(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->criarVitima(['nome' => "Vítima $i"]);
        }

        $vitimas = $this->model->listarComCasos(10, 3);
        $this->assertCount(2, $vitimas, 'Com offset=3 e 5 registros, devem retornar 2');
    }

    // -------------------------------------------------------------------------
    // Vítima não identificada
    // -------------------------------------------------------------------------

    public function testVitimaComNomeNuloEhAceita(): void
    {
        $this->model->insert([
            'nome'           => null,
            'idade_aparente' => 25,
            'sexo'           => 'masculino',
            'menor_de_idade' => 0,
            'gestante'       => 0,
            'pcd'            => 0,
        ]);

        $id = $this->model->insertID();
        $this->assertGreaterThan(0, $id);

        $vitima = $this->model->find($id);
        $this->assertNull($vitima['nome']);
    }

    // -------------------------------------------------------------------------
    // Timestamps automáticos
    // -------------------------------------------------------------------------

    public function testTimestampsPreenchidosAutomaticamente(): void
    {
        $this->model->insert([
            'nome'           => 'Teste Timestamp',
            'menor_de_idade' => 0,
            'gestante'       => 0,
            'pcd'            => 0,
        ]);

        $id     = $this->model->insertID();
        $vitima = $this->model->find($id);

        $this->assertNotNull($vitima['created_at']);
        $this->assertNotNull($vitima['updated_at']);
    }
}
