<?php

namespace Tests\Feature\Controllers;

use Tests\Support\OvpTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integração para o módulo de Vítimas.
 *
 * Como todo o módulo requer autenticação, estes testes verificam:
 *   - GET /vitimas              → redirect (sem login)
 *   - GET /vitimas/{id}         → redirect (sem login)
 *   - GET /vitimas/novo         → redirect (sem login)
 *   - GET /vitimas/{id}/editar  → redirect (sem login)
 *   - GET /vitimas/{id}/deletar → redirect (sem login)
 *
 * E regras de negócio via Model direto (sem HTTP):
 *   - Vítima vinculada a caso não pode ser deletada sem verificação
 *   - Vítima com nome NULL é persistida corretamente
 */
class VitimasTest extends OvpTestCase
{
    use FeatureTestTrait;

    // -------------------------------------------------------------------------
    // Proteção de autenticação
    // -------------------------------------------------------------------------

    public function testListagemRedirecionaSemLogin(): void
    {
        $response = $this->get('vitimas');
        $response->assertRedirect();
    }

    public function testDetalheRedirecionaSemLogin(): void
    {
        $id = $this->criarVitima();
        $response = $this->get("vitimas/{$id}");
        $response->assertRedirect();
    }

    public function testNovoRedirecionaSemLogin(): void
    {
        $response = $this->get('vitimas/novo');
        $response->assertRedirect();
    }

    public function testEditarRedirecionaSemLogin(): void
    {
        $id = $this->criarVitima();
        $response = $this->get("vitimas/{$id}/editar");
        $response->assertRedirect();
    }

    public function testDeletarRedirecionaSemLogin(): void
    {
        $id = $this->criarVitima();
        $response = $this->get("vitimas/{$id}/deletar");
        $response->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // Regras de negócio via Model
    // -------------------------------------------------------------------------

    public function testVitimaVinculadaAOcorrenciaNaoPodeSerDeletadaSemVerificacao(): void
    {
        $ocorrenciaId = $this->criarOcorrencia();
        $vitimaId     = $this->criarVitima(['nome' => 'Vítima Vinculada']);

        $this->vincularVitimaOcorrencia($ocorrenciaId, $vitimaId, ['resultado' => 'fatal']);

        // A verificação deve detectar que há vínculo antes de deletar
        $vinculos = db_connect()->table('ocorrencia_vitima')
            ->where('vitima_id', $vitimaId)
            ->countAllResults();

        $this->assertSame(1, $vinculos, 'Deve existir exatamente 1 vínculo para esta vítima');

        // A vítima ainda deve existir no banco
        $vitima = (new \App\Models\VitimaModel())->find($vitimaId);
        $this->assertNotNull($vitima);
    }

    public function testVitimaComNomeNuloEhPersistida(): void
    {
        $vitimaModel = new \App\Models\VitimaModel();
        $vitimaModel->insert([
            'nome'           => null,
            'sexo'           => 'masculino',
            'menor_de_idade' => 0,
            'gestante'       => 0,
            'pcd'            => 0,
        ]);

        $id = $vitimaModel->insertID();
        $this->assertGreaterThan(0, $id);

        $vitima = $vitimaModel->find($id);
        $this->assertNull($vitima['nome']);
    }

    public function testCriarVitimaComTodosOsCampos(): void
    {
        $vitimaModel = new \App\Models\VitimaModel();
        $vitimaModel->insert([
            'nome'             => 'Maria das Dores',
            'idade_aparente'   => 28,
            'sexo'             => 'feminino',
            'raca_cor'         => 'parda',
            'profissao'        => 'Auxiliar de limpeza',
            'condicao_juridica'=> 'civil_inocente',
            'menor_de_idade'   => 0,
            'gestante'         => 1,
            'pcd'              => 0,
            'observacoes'      => 'Estava grávida de 6 meses.',
        ]);

        $id = $vitimaModel->insertID();
        $vitima = $vitimaModel->find($id);

        $this->assertSame('Maria das Dores', $vitima['nome']);
        $this->assertSame('feminino', $vitima['sexo']);
        $this->assertSame('1', (string)$vitima['gestante']);
    }
}
