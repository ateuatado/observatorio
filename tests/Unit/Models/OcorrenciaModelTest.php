<?php

namespace Tests\Unit\Models;

use App\Models\OcorrenciaModel;
use Tests\Support\OvpTestCase;

/**
 * Testes unitários para OcorrenciaModel.
 *
 * Coberturas:
 *   - gerarProtocolo()      → sequência e formato OVP-AAAA-NNNNN
 *   - somaVitimas()         → soma de vitimas_fatais
 *   - contarMunicipios()    → distinct de municípios
 *   - contagemPorTipo()     → agrupamento por tipo_violencia
 *   - acoesVinculadas()     → retorna ações vinculadas via ocorrencia_acao
 *   - soft-delete           → deleted_at preenchido, registro não aparece em findAll()
 *   - allowedFields         → campos proibidos são ignorados no insert
 */
class OcorrenciaModelTest extends OvpTestCase
{
    private OcorrenciaModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new OcorrenciaModel();
    }

    // -------------------------------------------------------------------------
    // gerarProtocolo()
    // -------------------------------------------------------------------------

    public function testGerarProtocoloPrimeiroDoAno(): void
    {
        $protocolo = $this->model->gerarProtocolo();
        $ano       = date('Y');
        $this->assertMatchesRegularExpression(
            "/^OVP-{$ano}-\d{5}$/",
            $protocolo,
            'Protocolo deve ter o formato OVP-AAAA-NNNNN'
        );
    }

    public function testGerarProtocoloIncrementaSequencia(): void
    {
        $locId = $this->criarLocalizacao();

        // Insere uma ocorrência com protocolo conhecido
        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 0,
        ]);

        $proximo = $this->model->gerarProtocolo();
        $this->assertSame('OVP-' . date('Y') . '-00002', $proximo);
    }

    // -------------------------------------------------------------------------
    // somaVitimas()
    // -------------------------------------------------------------------------

    public function testSomaVitimasRetornaZeroSemOcorrencias(): void
    {
        $this->assertSame(0, $this->model->somaVitimas());
    }

    public function testSomaVitimasSomaCorretamente(): void
    {
        $locId = $this->criarLocalizacao();

        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 3,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);

        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00002',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-02-01',
            'tipo_violencia'     => 'chacina',
            'vitimas_fatais'     => 7,
            'vitimas_nao_fatais' => 2,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);

        $this->assertSame(10, $this->model->somaVitimas());
    }

    // -------------------------------------------------------------------------
    // contarMunicipios()
    // -------------------------------------------------------------------------

    public function testContarMunicipiosRetornaDistinct(): void
    {
        $locSP  = $this->criarLocalizacao(['municipio' => 'São Paulo']);
        $locGRU = $this->criarLocalizacao(['municipio' => 'Guarulhos', 'bairro' => 'Bairro A']);

        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locSP,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 1,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);

        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00002',
            'localizacao_id'     => $locGRU,
            'data_fato'          => '2024-02-01',
            'tipo_violencia'     => 'tortura',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 1,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);

        $this->assertSame(2, $this->model->contarMunicipios());
    }

    // -------------------------------------------------------------------------
    // contagemPorTipo()
    // -------------------------------------------------------------------------

    public function testContagemPorTipoRetornaAgrupamento(): void
    {
        $locId = $this->criarLocalizacao();

        foreach (['execucao', 'execucao', 'tortura'] as $idx => $tipo) {
            $this->model->insert([
                'protocolo_ovp'      => 'OVP-' . date('Y') . '-' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT),
                'localizacao_id'     => $locId,
                'data_fato'          => '2024-01-01',
                'tipo_violencia'     => $tipo,
                'vitimas_fatais'     => 0,
                'vitimas_nao_fatais' => 0,
                'status_investigacao'=> 'sem_inquerito',
                'publicado'          => 1,
            ]);
        }

        $contagem = $this->model->contagemPorTipo();
        $this->assertNotEmpty($contagem);

        $mapa = array_column($contagem, 'total', 'tipo_violencia');
        $this->assertSame('2', (string)$mapa['execucao']);
        $this->assertSame('1', (string)$mapa['tortura']);
    }

    // -------------------------------------------------------------------------
    // acoesVinculadas()
    // -------------------------------------------------------------------------

    public function testAcoesVinculadasRetornaVazioSemVinculos(): void
    {
        $id = $this->criarOcorrencia();
        $acoes = $this->model->acoesVinculadas($id);
        $this->assertIsArray($acoes);
        $this->assertCount(0, $acoes);
    }

    public function testAcoesVinculadasRetornaAcaoCriada(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $acaoId       = $this->criarAcaoSeguranca(['nome' => 'Operação Escudo']);

        $this->criarVinculoOcorrenciaAcao($ocorrenciaId, $acaoId, [
            'momento_vinculo' => 'durante',
            'justificativa'   => 'Ocorreu no contexto da operação.',
        ]);

        $acoes = $this->model->acoesVinculadas($ocorrenciaId);

        $this->assertCount(1, $acoes);
        $this->assertSame('Operação Escudo', $acoes[0]['nome']);
        $this->assertSame('durante', $acoes[0]['momento_vinculo']);
    }

    public function testUmaOcorrenciaPodeSerVinculadaAMultiplasAcoes(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $acao1 = $this->criarAcaoSeguranca(['nome' => 'Ação A']);
        $acao2 = $this->criarAcaoSeguranca(['nome' => 'Ação B', 'tipo_agente' => 'milicia']);

        $this->criarVinculoOcorrenciaAcao($ocorrenciaId, $acao1);
        $this->criarVinculoOcorrenciaAcao($ocorrenciaId, $acao2);

        $acoes = $this->model->acoesVinculadas($ocorrenciaId);
        $this->assertCount(2, $acoes);
    }

    // -------------------------------------------------------------------------
    // Soft-delete
    // -------------------------------------------------------------------------

    public function testSoftDeleteNaoRetornaRegistroDeletado(): void
    {
        $locId = $this->criarLocalizacao();
        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);
        $id = $this->model->insertID();

        $this->model->delete($id);

        $encontrado = $this->model->find($id);
        $this->assertNull($encontrado, 'Ocorrência deletada não deve ser retornada por find()');
    }

    public function testSoftDeletePreservaRegistroNoBanco(): void
    {
        $locId = $this->criarLocalizacao();
        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 1,
        ]);
        $id = $this->model->insertID();

        $this->model->delete($id);

        $encontrado = $this->model->withDeleted()->find($id);
        $this->assertNotNull($encontrado);
        $this->assertNotNull($encontrado['deleted_at']);
    }

    // -------------------------------------------------------------------------
    // allowedFields
    // -------------------------------------------------------------------------

    public function testCampoNaoPermitidoEhIgnorado(): void
    {
        $locId = $this->criarLocalizacao();
        $this->model->insert([
            'protocolo_ovp'      => 'OVP-' . date('Y') . '-00001',
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 0,
            'campo_inexistente'  => 'deve ser ignorado',
        ]);

        $id = $this->model->insertID();
        $this->assertGreaterThan(0, $id, 'Insert deve ter sucesso mesmo com campo proibido');
    }
}
