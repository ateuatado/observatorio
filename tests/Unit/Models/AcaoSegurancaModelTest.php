<?php

namespace Tests\Unit\Models;

use App\Models\AcaoSegurancaModel;
use Tests\Support\OvpTestCase;

/**
 * Testes unitários para AcaoSegurancaModel.
 *
 * Coberturas:
 *   - vinculoExiste()           → detecta vínculo existente e não existente
 *   - vincularOcorrencia()      → cria vínculo, impede duplicata
 *   - desvincularOcorrencia()   → remove vínculo
 *   - ocorrenciasVinculadas()   → lista ocorrências com metadados do vínculo
 *   - soft-delete               → deleted_at preenchido, find() retorna null
 *   - allowedFields             → campo proibido é ignorado
 *   - validações                → tipo_agente inválido é rejeitado
 */
class AcaoSegurancaModelTest extends OvpTestCase
{
    private AcaoSegurancaModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new AcaoSegurancaModel();
    }

    // -------------------------------------------------------------------------
    // vinculoExiste()
    // -------------------------------------------------------------------------

    public function testVinculoNaoExisteInicialmente(): void
    {
        $ocId  = $this->criarOcorrencia();
        $acId  = $this->criarAcaoSeguranca();

        $this->assertFalse($this->model->vinculoExiste($ocId, $acId));
    }

    public function testVinculoExisteApósCriação(): void
    {
        $ocId = $this->criarOcorrencia();
        $acId = $this->criarAcaoSeguranca();

        $this->criarVinculoOcorrenciaAcao($ocId, $acId);

        $this->assertTrue($this->model->vinculoExiste($ocId, $acId));
    }

    // -------------------------------------------------------------------------
    // vincularOcorrencia()
    // -------------------------------------------------------------------------

    public function testVincularOcorrenciaCriaRegistro(): void
    {
        $ocId = $this->criarOcorrencia();
        $acId = $this->criarAcaoSeguranca();

        $resultado = $this->model->vincularOcorrencia($ocId, $acId, [
            'momento_vinculo' => 'durante',
            'justificativa'   => 'Vinculação analítica.',
            'vinculado_por'   => null,
        ]);

        $this->assertTrue((bool) $resultado);
        $this->assertTrue($this->model->vinculoExiste($ocId, $acId));
    }

    public function testVincularOcorrenciaRetornaFalseParaDuplicata(): void
    {
        $ocId = $this->criarOcorrencia();
        $acId = $this->criarAcaoSeguranca();

        $this->model->vincularOcorrencia($ocId, $acId);
        $segundo = $this->model->vincularOcorrencia($ocId, $acId);

        $this->assertFalse((bool) $segundo, 'Vínculo duplicado deve retornar false');
    }

    // -------------------------------------------------------------------------
    // desvincularOcorrencia()
    // -------------------------------------------------------------------------

    public function testDesvincularOcorrenciaRemoveRegistro(): void
    {
        $ocId = $this->criarOcorrencia();
        $acId = $this->criarAcaoSeguranca();

        $this->criarVinculoOcorrenciaAcao($ocId, $acId);
        $this->assertTrue($this->model->vinculoExiste($ocId, $acId));

        $this->model->desvincularOcorrencia($ocId, $acId);

        $this->assertFalse($this->model->vinculoExiste($ocId, $acId));
    }

    // -------------------------------------------------------------------------
    // ocorrenciasVinculadas()
    // -------------------------------------------------------------------------

    public function testOcorrenciasVinculadasRetornaVazioSemVinculos(): void
    {
        $acId = $this->criarAcaoSeguranca();
        $lista = $this->model->ocorrenciasVinculadas($acId);

        $this->assertIsArray($lista);
        $this->assertCount(0, $lista);
    }

    public function testOcorrenciasVinculadasRetornaUmaOcorrencia(): void
    {
        $acId = $this->criarAcaoSeguranca();
        $ocId = $this->criarOcorrencia(['tipo_violencia' => 'chacina']);

        $this->criarVinculoOcorrenciaAcao($ocId, $acId, ['momento_vinculo' => 'depois']);

        $lista = $this->model->ocorrenciasVinculadas($acId);

        $this->assertCount(1, $lista);
        $this->assertSame('depois', $lista[0]['momento_vinculo']);
        $this->assertSame('chacina', $lista[0]['tipo_violencia']);
    }

    public function testUmaAcaoPodeVincularMultiplasOcorrencias(): void
    {
        $acId = $this->criarAcaoSeguranca();
        $oc1  = $this->criarOcorrencia();
        $oc2  = $this->criarOcorrencia();
        $oc3  = $this->criarOcorrencia();

        $this->criarVinculoOcorrenciaAcao($oc1, $acId);
        $this->criarVinculoOcorrenciaAcao($oc2, $acId);
        $this->criarVinculoOcorrenciaAcao($oc3, $acId);

        $lista = $this->model->ocorrenciasVinculadas($acId);
        $this->assertCount(3, $lista);
    }

    // -------------------------------------------------------------------------
    // Soft-delete
    // -------------------------------------------------------------------------

    public function testSoftDeleteNaoRetornaAcaoDeletada(): void
    {
        $acId = $this->criarAcaoSeguranca();

        $this->model->delete($acId);

        $encontrado = $this->model->find($acId);
        $this->assertNull($encontrado, 'Ação deletada não deve ser retornada por find()');
    }

    public function testSoftDeletePreservaRegistroComWithDeleted(): void
    {
        $acId = $this->criarAcaoSeguranca();

        $this->model->delete($acId);

        $encontrado = $this->model->withDeleted()->find($acId);
        $this->assertNotNull($encontrado);
        $this->assertNotNull($encontrado['deleted_at']);
    }

    // -------------------------------------------------------------------------
    // allowedFields
    // -------------------------------------------------------------------------

    public function testInsertComCampoProibidoNaoFalha(): void
    {
        $acId = $this->model->insert([
            'nome'              => 'Op. Permitida',
            'tipo_agente'       => 'estatal',
            'precisao_temporal' => 'exata',
            'status'            => 'em_analise',
            'visibilidade'      => 'restrita',
            'campo_inexistente' => 'deve ser ignorado',
        ]);

        $this->assertNotFalse($acId, 'Insert deve ter sucesso mesmo com campo proibido');
    }

    // -------------------------------------------------------------------------
    // Validações
    // -------------------------------------------------------------------------

    public function testTipoAgenteInvalidoFalhaNaValidacao(): void
    {
        $ok = $this->model->validate([
            'tipo_agente'       => 'invalido_xpto',
            'precisao_temporal' => 'exata',
            'status'            => 'em_analise',
            'visibilidade'      => 'restrita',
        ]);

        $this->assertFalse($ok, 'tipo_agente inválido deve falhar na validação');
        $this->assertArrayHasKey('tipo_agente', $this->model->errors());
    }

    public function testStatusInvalidoFalhaNaValidacao(): void
    {
        $ok = $this->model->validate([
            'tipo_agente'       => 'estatal',
            'precisao_temporal' => 'exata',
            'status'            => 'publicado_xpto',
            'visibilidade'      => 'restrita',
        ]);

        $this->assertFalse($ok);
        $this->assertArrayHasKey('status', $this->model->errors());
    }

    public function testVisibilidadeInvalidaFalhaNaValidacao(): void
    {
        $ok = $this->model->validate([
            'tipo_agente'       => 'estatal',
            'precisao_temporal' => 'exata',
            'status'            => 'em_analise',
            'visibilidade'      => 'aberto_para_todos',
        ]);

        $this->assertFalse($ok);
        $this->assertArrayHasKey('visibilidade', $this->model->errors());
    }

    public function testRegistroValidoPassaNaValidacao(): void
    {
        $ok = $this->model->validate([
            'tipo_agente'       => 'milicia',
            'precisao_temporal' => 'estimada',
            'status'            => 'confirmada',
            'visibilidade'      => 'sigilosa',
        ]);

        $this->assertTrue($ok, 'Registro válido deve passar na validação');
    }
}
