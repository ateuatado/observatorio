<?php

namespace Tests\Unit\Models;

use App\Models\LocalizacaoModel;
use Tests\Support\OvpTestCase;

/**
 * Testes unitários para LocalizacaoModel.
 *
 * Coberturas:
 *   - salvarOuEncontrar()  → cria nova localização; reutiliza existente
 *   - listaMunicipios()    → retorna distinct ordenado
 *   - allowedFields        → campos corretos são persistidos
 */
class LocalizacaoModelTest extends OvpTestCase
{
    private LocalizacaoModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new LocalizacaoModel();
    }

    // -------------------------------------------------------------------------
    // salvarOuEncontrar()
    // -------------------------------------------------------------------------

    public function testSalvarOuEncontrarCriaNovaCidade(): void
    {
        $id = $this->model->salvarOuEncontrar([
            'municipio' => 'Campinas',
            'bairro'    => 'Cambuí',
            'estado'    => 'SP',
        ]);

        $this->assertGreaterThan(0, $id);

        $loc = $this->model->find($id);
        $this->assertSame('Campinas', $loc['municipio']);
    }

    public function testSalvarOuEncontrarReutilizaExistente(): void
    {
        // Primeira chamada → cria
        $id1 = $this->model->salvarOuEncontrar([
            'municipio' => 'Santos',
            'bairro'    => 'Gonzaga',
            'estado'    => 'SP',
        ]);

        // Segunda chamada com mesmos dados → deve retornar o mesmo ID
        $id2 = $this->model->salvarOuEncontrar([
            'municipio' => 'Santos',
            'bairro'    => 'Gonzaga',
            'estado'    => 'SP',
        ]);

        $this->assertSame($id1, $id2, 'salvarOuEncontrar deve retornar o ID existente');

        // Verifica que não criou um segundo registro
        $total = $this->model->where('municipio', 'Santos')->countAllResults();
        $this->assertSame(1, $total);
    }

    public function testSalvarOuEncontrarBairroDiferenteCriaNovo(): void
    {
        $id1 = $this->model->salvarOuEncontrar([
            'municipio' => 'São Paulo',
            'bairro'    => 'Jardim Ângela',
        ]);

        $id2 = $this->model->salvarOuEncontrar([
            'municipio' => 'São Paulo',
            'bairro'    => 'Capão Redondo',  // bairro diferente → novo registro
        ]);

        $this->assertNotSame($id1, $id2);
    }

    public function testSalvarOuEncontrarSemBairroFunciona(): void
    {
        $id = $this->model->salvarOuEncontrar([
            'municipio' => 'Guarulhos',
            'estado'    => 'SP',
        ]);

        $this->assertGreaterThan(0, $id);
    }

    // -------------------------------------------------------------------------
    // listaMunicipios()
    // -------------------------------------------------------------------------

    public function testListaMunicipiosRetornaVazioSemRegistros(): void
    {
        $municipios = $this->model->listaMunicipios();
        $this->assertIsArray($municipios);
        $this->assertEmpty($municipios);
    }

    public function testListaMunicipiosRetornaDistinctOrdenado(): void
    {
        // Insere 3 localizações: 2 em SP, 1 em Campinas
        $this->model->salvarOuEncontrar(['municipio' => 'São Paulo',  'bairro' => 'Capão Redondo']);
        $this->model->salvarOuEncontrar(['municipio' => 'São Paulo',  'bairro' => 'Jardim Ângela']);
        $this->model->salvarOuEncontrar(['municipio' => 'Campinas',   'bairro' => 'Cambuí']);

        $municipios = $this->model->listaMunicipios();

        // Deve retornar apenas os municípios únicos
        $nomes = array_column($municipios, 'municipio');
        $this->assertContains('São Paulo', $nomes);
        $this->assertContains('Campinas', $nomes);

        // Campinas deve vir antes de São Paulo (ordenado ASC)
        $idxCampinas = array_search('Campinas', $nomes);
        $idxSaoPaulo = array_search('São Paulo', $nomes);
        $this->assertLessThan($idxSaoPaulo, $idxCampinas, 'Campinas < São Paulo (ASC)');
    }

    // -------------------------------------------------------------------------
    // allowedFields
    // -------------------------------------------------------------------------

    public function testTodosOsCamposPermitidosSaoSalvos(): void
    {
        $dados = [
            'logradouro'      => 'Rua das Flores',
            'numero'          => '123',
            'bairro'          => 'Centro',
            'zona_cidade'     => 'sul',
            'municipio'       => 'São Paulo',
            'estado'          => 'SP',
            'tipo_local'      => 'via_publica',
            'descricao_local' => 'Próximo à escola',
        ];

        $this->model->insert($dados);
        $id  = $this->model->insertID();
        $loc = $this->model->find($id);

        foreach ($dados as $campo => $valor) {
            $this->assertSame($valor, $loc[$campo], "Campo '{$campo}' deveria ser '{$valor}'");
        }
    }
}
