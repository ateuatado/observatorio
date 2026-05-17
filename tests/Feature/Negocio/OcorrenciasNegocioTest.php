<?php

namespace Tests\Feature\Negocio;

use App\Models\OcorrenciaModel;
use App\Models\LocalizacaoModel;
use Tests\Support\OvpTestCase;

/**
 * Testes de regras de negócio do módulo Ocorrências.
 *
 * Exercita a camada Model+DB diretamente, sem HTTP:
 *   - Protocolo OVP é único (UNIQUE constraint)
 *   - Publicação / despublicação altera o campo publicado
 *   - Pivot ocorrencia_vitima registra resultado por vítima
 *   - Cascade DELETE em ocorrencia_vitima ao deletar a ocorrência
 *   - Pivot ocorrencia_agente registra corporação
 *   - Duas ocorrências podem compartilhar a mesma localização
 *   - Filtragem por publicado via model
 *   - gerarProtocolo contém o ano correto
 */
class OcorrenciasNegocioTest extends OvpTestCase
{
    private OcorrenciaModel $model;
    private LocalizacaoModel $locModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model    = new OcorrenciaModel();
        $this->locModel = new LocalizacaoModel();
    }

    // -------------------------------------------------------------------------
    // Protocolo OVP único
    // -------------------------------------------------------------------------

    public function testProtocoloOvpEhUnico(): void
    {
        $locId = $this->criarLocalizacao();
        $proto = 'OVP-' . date('Y') . '-' . uniqid();

        $this->model->insert([
            'protocolo_ovp'      => $proto,
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-01-01',
            'tipo_violencia'     => 'execucao',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 0,
        ]);

        // Segundo insert com mesmo protocolo deve lançar exception (UNIQUE constraint)
        $this->expectException(\Exception::class);

        db_connect()->table('ocorrencias')->insert([
            'protocolo_ovp'      => $proto,  // duplicado
            'localizacao_id'     => $locId,
            'data_fato'          => '2024-02-01',
            'tipo_violencia'     => 'tortura',
            'vitimas_fatais'     => 0,
            'vitimas_nao_fatais' => 0,
            'status_investigacao'=> 'sem_inquerito',
            'publicado'          => 0,
        ]);
    }

    // -------------------------------------------------------------------------
    // Toggle de publicação
    // -------------------------------------------------------------------------

    public function testPublicarOcorrenciaAlteraFlag(): void
    {
        $id = $this->criarOcorrencia(['publicado' => 0]);

        $this->model->update($id, ['publicado' => 1]);

        $atualizado = $this->model->find($id);
        $this->assertSame(1, (int)$atualizado['publicado']);
    }

    public function testDespublicarOcorrenciaAlteraFlag(): void
    {
        $id = $this->criarOcorrencia(['publicado' => 1]);

        $this->model->update($id, ['publicado' => 0]);

        $atualizado = $this->model->find($id);
        $this->assertSame(0, (int)$atualizado['publicado']);
    }

    // -------------------------------------------------------------------------
    // Pivot ocorrencia_vitima
    // -------------------------------------------------------------------------

    public function testOcorrenciaVitimaRegistraResultado(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $vitimaId     = $this->criarVitima();

        $this->vincularVitimaOcorrencia($ocorrenciaId, $vitimaId, [
            'resultado'   => 'fatal',
            'identificada'=> 1,
        ]);

        $pivot = db_connect()->table('ocorrencia_vitima')
            ->where('ocorrencia_id', $ocorrenciaId)
            ->where('vitima_id', $vitimaId)
            ->get()->getRowArray();

        $this->assertNotNull($pivot);
        $this->assertSame('fatal', $pivot['resultado']);
        $this->assertSame('1', (string)$pivot['identificada']);
    }

    public function testCascadeDeleteRemovePivotAoDeletarOcorrencia(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $vitimaId     = $this->criarVitima();
        $db           = db_connect();

        $this->vincularVitimaOcorrencia($ocorrenciaId, $vitimaId);

        // Habilita FKs no SQLite e deleta a ocorrência
        $db->query('PRAGMA foreign_keys = ON');
        $db->table('ocorrencias')->where('id', $ocorrenciaId)->delete();

        $pivot = $db->table('ocorrencia_vitima')
            ->where('ocorrencia_id', $ocorrenciaId)
            ->countAllResults();

        $this->assertSame(0, $pivot, 'CASCADE DELETE deve remover registros de ocorrencia_vitima');
    }

    // -------------------------------------------------------------------------
    // Pivot ocorrencia_agente
    // -------------------------------------------------------------------------

    public function testOcorrenciaAgenteRegistraCorporacao(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $db           = db_connect();

        $db->table('ocorrencia_agente')->insert([
            'ocorrencia_id'     => $ocorrenciaId,
            'agente_id'         => null,
            'descricao_agente'  => 'Dois PMs da Força Tática',
            'quantidade_agentes'=> 2,
            'corporacao'        => 'PM',
            'fardado'           => 1,
            'encapuzado'        => 0,
            'papel_no_caso'     => 'executor',
        ]);

        $agente = $db->table('ocorrencia_agente')
            ->where('ocorrencia_id', $ocorrenciaId)
            ->get()->getRowArray();

        $this->assertSame('PM', $agente['corporacao']);
        $this->assertSame('2', (string)$agente['quantidade_agentes']);
        $this->assertSame('executor', $agente['papel_no_caso']);
    }

    // -------------------------------------------------------------------------
    // Localização compartilhada
    // -------------------------------------------------------------------------

    public function testDuasOcorrenciasPodemCompartilharLocalizacao(): void
    {
        $locId = $this->criarLocalizacao(['municipio' => 'São Paulo', 'bairro' => 'Paraisópolis']);

        $id1 = $this->criarOcorrencia(['localizacao_id' => $locId]);
        $id2 = $this->criarOcorrencia(['localizacao_id' => $locId]);

        $oc1 = $this->model->find($id1);
        $oc2 = $this->model->find($id2);

        $this->assertSame((int)$oc1['localizacao_id'], (int)$oc2['localizacao_id']);
        $this->assertSame($locId, (int)$oc1['localizacao_id']);
    }

    // -------------------------------------------------------------------------
    // Filtragem
    // -------------------------------------------------------------------------

    public function testListagemRetornaApenasPublicados(): void
    {
        $this->criarOcorrencia(['publicado' => 1]);
        $this->criarOcorrencia(['publicado' => 1]);
        $this->criarOcorrencia(['publicado' => 0]);

        $publicados = $this->model->where('publicado', 1)->findAll();
        $this->assertCount(2, $publicados);
    }

    // -------------------------------------------------------------------------
    // gerarProtocolo
    // -------------------------------------------------------------------------

    public function testProtocoloContemAnoCorreto(): void
    {
        $protocolo = $this->model->gerarProtocolo();
        $this->assertStringContainsString(date('Y'), $protocolo);
    }
}
