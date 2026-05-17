<?php

namespace App\Controllers;

use App\Models\OcorrenciaModel;
use App\Models\LocalizacaoModel;

class Ocorrencias extends BaseController
{
    protected OcorrenciaModel       $OcorrenciaModel;
    protected LocalizacaoModel $locModel;

    public function __construct()
    {
        $this->db        = \Config\Database::connect();
        $this->OcorrenciaModel = new OcorrenciaModel();
        $this->locModel  = new LocalizacaoModel();
    }

    // ================================================================
    // ÁREA PÚBLICA
    // ================================================================

    /**
     * Listagem pública de casos publicados, com filtros por URL.
     */
    public function index(): string
    {
        $tipo       = $this->request->getGet('tipo');
        $municipio  = $this->request->getGet('municipio');
        $ano        = $this->request->getGet('ano');
        $busca      = $this->request->getGet('q');
        $pagina     = (int)($this->request->getGet('p') ?? 1);
        $porPagina  = 12;

        // Build base WHERE conditions
        $where = ['ocorrencias.publicado' => 1];
        if ($tipo)      $where['ocorrencias.tipo_violencia'] = $tipo;
        if ($municipio) $where['localizacoes.municipio'] = $municipio;

        // Count query (raw)
        $countSql = 'SELECT COUNT(*) AS total FROM ocorrencias
            LEFT JOIN localizacoes ON localizacoes.id = ocorrencias.localizacao_id
            WHERE ocorrencias.publicado = 1';
        $params = [];
        $isSqlite = str_contains(strtolower($this->db->DBDriver), 'sqlite');
        $yearFn   = $isSqlite ? "strftime('%Y', ocorrencias.data_fato)" : 'YEAR(ocorrencias.data_fato)';

        if ($tipo)      { $countSql .= ' AND ocorrencias.tipo_violencia = ?';     $params[] = $tipo; }
        if ($municipio) { $countSql .= ' AND localizacoes.municipio = ?';   $params[] = $municipio; }
        if ($ano)       { $countSql .= " AND {$yearFn} = ?";               $params[] = (string)$ano; }
        if ($busca)     { $countSql .= ' AND (ocorrencias.descricao_livre LIKE ? OR localizacoes.municipio LIKE ? OR localizacoes.bairro LIKE ?)'; $params[] = "%$busca%"; $params[] = "%$busca%"; $params[] = "%$busca%"; }
        $totalCasos = (int)$this->db->query($countSql, $params)->getRow()->total;

        // Data query
        $dataSql = 'SELECT ocorrencias.*, localizacoes.municipio, localizacoes.bairro, localizacoes.zona_cidade
            FROM ocorrencias
            LEFT JOIN localizacoes ON localizacoes.id = ocorrencias.localizacao_id
            WHERE ocorrencias.publicado = 1';
        if ($tipo)      { $dataSql .= ' AND ocorrencias.tipo_violencia = ?'; }
        if ($municipio) { $dataSql .= ' AND localizacoes.municipio = ?'; }
        if ($ano)       { $dataSql .= " AND {$yearFn} = ?"; }
        if ($busca)     { $dataSql .= ' AND (ocorrencias.descricao_livre LIKE ? OR localizacoes.municipio LIKE ? OR localizacoes.bairro LIKE ?)'; }
        $dataSql .= ' ORDER BY ocorrencias.data_fato DESC LIMIT ' . $porPagina . ' OFFSET ' . (($pagina - 1) * $porPagina);
        $casos = $this->db->query($dataSql, $params)->getResultArray();

        // Dados para os filtros
        $municipios = $this->locModel->listaMunicipios();
        $yearFnSimple = $isSqlite ? "strftime('%Y', data_fato)" : 'YEAR(data_fato)';
        $anos = $this->db->query(
            "SELECT DISTINCT {$yearFnSimple} AS ano FROM ocorrencias WHERE publicado = 1 ORDER BY ano DESC"
        )->getResultArray();

        return view('ocorrencias/index', [
            'title'       => 'Casos documentados',
            'casos'       => $casos,
            'total'       => $totalCasos,
            'pagina'      => $pagina,
            'porPagina'   => $porPagina,
            'municipios'  => $municipios,
            'anos'        => $anos,
            'filtros'     => compact('tipo', 'municipio', 'ano', 'busca'),
        ]);
    }

    /**
     * Detalhe público de um caso.
     */
    public function show(int $id): string
    {
        $caso = $this->OcorrenciaModel
            ->select('ocorrencias.*, localizacoes.*')
            ->join('localizacoes', 'localizacoes.id = ocorrencias.localizacao_id', 'left')
            ->where('ocorrencias.id', $id)
            ->where('ocorrencias.publicado', 1)
            ->first();

        if (!$caso) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Caso #{$id} não encontrado.");
        }

        // Vítimas do caso
        $vitimas = $this->db->table('ocorrencia_vitima cv')
            ->select('cv.*, v.nome, v.idade_aparente, v.sexo, v.raca_cor, v.profissao')
            ->join('vitimas v', 'v.id = cv.vitima_id', 'left')
            ->where('cv.ocorrencia_id', $id)
            ->get()->getResultArray();

        // Agentes do caso
        $agentes = $this->db->table('ocorrencia_agente ca')
            ->select('ca.*, a.nome, a.corporacao')
            ->join('agentes a', 'a.id = ca.agente_id', 'left')
            ->where('ca.ocorrencia_id', $id)
            ->get()->getResultArray();

        // Processo e documentos (tabelas opcionais — retornam vazio se não existirem)
        $processo   = [];
        $documentos = [];
        try {
            $processo = $this->db->table('processos')->where('ocorrencia_id', $id)->get()->getRowArray() ?? [];
        } catch (\Exception $e) { /* tabela não existe ainda */ }
        try {
            $documentos = $this->db->table('documentos')->where('ocorrencia_id', $id)->get()->getResultArray();
        } catch (\Exception $e) { /* tabela não existe ainda */ }

        return view('ocorrencias/show', [
            'title'      => 'Caso OVP-' . str_pad($id, 5, '0', STR_PAD_LEFT),
            'caso'       => $caso,
            'vitimas'    => $vitimas,
            'agentes'    => $agentes,
            'processo'   => $processo,
            'documentos' => $documentos,
        ]);
    }

    // ================================================================
    // ÁREA AUTENTICADA
    // ================================================================

    /**
     * Formulário para novo caso.
     */
    public function novo(): string
    {
        return view('ocorrencias/form', [
            'title'     => 'Registrar novo caso',
            'breadcrumb'=> 'Novo caso',
            'caso'      => null,
            'localizacao'=> null,
        ]);
    }

    /**
     * Salva um novo caso com localização e vítimas.
     */
    public function salvar()
    {
        $rules = [
            'data_fato'      => 'required|valid_date[Y-m-d]',
            'tipo_violencia' => 'required',
            'municipio'      => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Salvar localização
            $locId = $this->locModel->salvarOuEncontrar([
                'logradouro'   => $this->request->getPost('logradouro'),
                'numero'       => $this->request->getPost('numero'),
                'bairro'       => $this->request->getPost('bairro'),
                'zona_cidade'  => $this->request->getPost('zona_cidade'),
                'municipio'    => $this->request->getPost('municipio'),
                'estado'       => $this->request->getPost('estado') ?: 'SP',
                'tipo_local'   => $this->request->getPost('tipo_local'),
                'descricao_local' => $this->request->getPost('descricao_local'),
            ]);

            // 2. Salvar caso
            $casoData = [
                'protocolo_ovp'      => $this->OcorrenciaModel->gerarProtocolo(),
                'localizacao_id'     => $locId,
                'data_fato'          => $this->request->getPost('data_fato'),
                'hora_fato'          => $this->request->getPost('hora_fato') ?: null,
                'tipo_violencia'     => $this->request->getPost('tipo_violencia'),
                'subtipo'            => $this->request->getPost('subtipo'),
                'vitimas_fatais'     => (int)$this->request->getPost('vitimas_fatais') ?: 0,
                'vitimas_nao_fatais' => (int)$this->request->getPost('vitimas_nao_fatais') ?: 0,
                'versao_oficial'     => $this->request->getPost('versao_oficial'),
                'versao_testemunhas' => $this->request->getPost('versao_testemunhas'),
                'descricao_livre'    => $this->request->getPost('descricao_livre'),
                'status_investigacao'=> $this->request->getPost('status_investigacao') ?: 'sem_inquerito',
                'publicado'          => (int)$this->request->getPost('publicado') ?: 0,
                'cadastrado_por'     => auth()->id(),
            ];

            $this->OcorrenciaModel->insert($casoData);
            $casoId = $this->OcorrenciaModel->insertID();

            // 3. Salvar vítimas (array dinâmico do form)
            $vitimas = $this->request->getPost('vitimas') ?? [];
            foreach ($vitimas as $v) {
                if (empty($v['nome']) && empty($v['idade_aparente'])) continue;

                $vitimaId = $db->table('vitimas')->insert([
                    'nome'            => $v['nome'] ?: null,
                    'idade_aparente'  => $v['idade_aparente'] ?: null,
                    'sexo'            => $v['sexo'] ?: null,
                    'raca_cor'        => $v['raca_cor'] ?: null,
                    'profissao'       => $v['profissao'] ?: null,
                    'condicao_juridica'=> $v['condicao_juridica'] ?: null,
                    'menor_de_idade'  => isset($v['menor_de_idade']) ? 1 : 0,
                    'observacoes'     => $v['observacoes'] ?: null,
                ]);
                $vitimaId = $db->insertID();

                $db->table('ocorrencia_vitima')->insert([
                    'ocorrencia_id'     => $casoId,
                    'vitima_id'   => $vitimaId,
                    'resultado'   => $v['resultado'] ?: null,
                    'ferimentos'  => $v['ferimentos'] ?: null,
                    'identificada'=> empty($v['nome']) ? 0 : 1,
                ]);
            }

            // 4. Salvar agentes (array dinâmico)
            $agentes = $this->request->getPost('agentes') ?? [];
            foreach ($agentes as $a) {
                if (empty($a['corporacao']) && empty($a['descricao'])) continue;

                $db->table('ocorrencia_agente')->insert([
                    'ocorrencia_id'            => $casoId,
                    'agente_id'          => null, // sem cadastro individual ainda
                    'descricao_agente'   => $a['descricao'] ?: null,
                    'quantidade_agentes' => $a['quantidade'] ?: 1,
                    'corporacao'         => $a['corporacao'] ?: null,
                    'fardado'            => isset($a['fardado']) ? 1 : 0,
                    'encapuzado'         => isset($a['encapuzado']) ? 1 : 0,
                    'prefixo_viatura'    => $a['prefixo_viatura'] ?: null,
                    'papel_no_caso'      => $a['papel'] ?: 'executor',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro na transação');
            }

            return redirect()->to("ocorrencias/{$casoId}")->with('message', "Ocorrência {$casoData['protocolo_ovp']} registrada com sucesso!");

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao salvar caso: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao salvar o caso. Tente novamente.');
        }
    }

    /**
     * Formulário de edição de caso existente.
     */
    public function editar(int $id): string
    {
        $caso = $this->OcorrenciaModel
            ->select('ocorrencias.*, localizacoes.*')
            ->join('localizacoes', 'localizacoes.id = ocorrencias.localizacao_id', 'left')
            ->where('ocorrencias.id', $id)
            ->first();

        if (!$caso) {
            return redirect()->to('ocorrencias')->with('error', 'Caso não encontrado.');
        }

        $vitimas = $this->db->table('ocorrencia_vitima cv')
            ->select('cv.*, v.*')
            ->join('vitimas v', 'v.id = cv.vitima_id')
            ->where('cv.caso_id', $id)
            ->get()->getResultArray();

        $agentes = $this->db->table('ocorrencia_agente')
            ->where('ocorrencia_id', $id)
            ->get()->getResultArray();

        $municipios = $this->locModel->listaMunicipios();

        return view('ocorrencias/form', [
            'title'              => 'Editar caso ' . ($caso['protocolo_ovp'] ?? "#$id"),
            'breadcrumb'         => 'Editar caso',
            'caso'               => $caso,
            'vitimas_existentes' => $vitimas,
            'agentes_existentes' => $agentes,
            'municipios'         => $municipios,
        ]);
    }

    /**
     * Atualiza um caso existente (persistência real).
     */
    public function update(int $id)
    {
        $caso = $this->OcorrenciaModel->find($id);
        if (!$caso) {
            return redirect()->to('ocorrencias')->with('error', 'Caso não encontrado.');
        }

        $rules = [
            'data_fato'      => 'required|valid_date[Y-m-d]',
            'tipo_violencia' => 'required',
            'municipio'      => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Atualizar / criar localização
            $locId = $this->locModel->salvarOuEncontrar([
                'logradouro'      => $this->request->getPost('logradouro'),
                'numero'          => $this->request->getPost('numero'),
                'bairro'          => $this->request->getPost('bairro'),
                'zona_cidade'     => $this->request->getPost('zona_cidade'),
                'municipio'       => $this->request->getPost('municipio'),
                'estado'          => $this->request->getPost('estado') ?: 'SP',
                'tipo_local'      => $this->request->getPost('tipo_local'),
                'descricao_local' => $this->request->getPost('descricao_local'),
            ]);

            // 2. Atualizar campos do caso
            $this->OcorrenciaModel->update($id, [
                'localizacao_id'      => $locId,
                'data_fato'           => $this->request->getPost('data_fato'),
                'hora_fato'           => $this->request->getPost('hora_fato') ?: null,
                'tipo_violencia'      => $this->request->getPost('tipo_violencia'),
                'subtipo'             => $this->request->getPost('subtipo'),
                'vitimas_fatais'      => (int)$this->request->getPost('vitimas_fatais') ?: 0,
                'vitimas_nao_fatais'  => (int)$this->request->getPost('vitimas_nao_fatais') ?: 0,
                'versao_oficial'      => $this->request->getPost('versao_oficial'),
                'versao_testemunhas'  => $this->request->getPost('versao_testemunhas'),
                'descricao_livre'     => $this->request->getPost('descricao_livre'),
                'status_investigacao' => $this->request->getPost('status_investigacao') ?: 'sem_inquerito',
                'publicado'           => (int)$this->request->getPost('publicado') ?: 0,
            ]);

            // 3. Re-inserir vítimas (apaga e recria)
            // Apaga os pivôs e as vítimas exclusivas deste caso
            $vitimaIds = $db->table('ocorrencia_vitima')
                ->select('vitima_id')
                ->where('ocorrencia_id', $id)
                ->get()->getResultArray();
            $db->table('ocorrencia_vitima')->where('ocorrencia_id', $id)->delete();
            foreach ($vitimaIds as $row) {
                // Remove a vítima somente se não vinculada a outros casos
                $outrosCasos = $db->table('ocorrencia_vitima')
                    ->where('vitima_id', $row['vitima_id'])
                    ->countAllResults();
                if ($outrosCasos === 0) {
                    $db->table('vitimas')->where('id', $row['vitima_id'])->delete();
                }
            }

            $vitimas = $this->request->getPost('vitimas') ?? [];
            foreach ($vitimas as $v) {
                if (empty($v['nome']) && empty($v['idade_aparente'])) continue;

                $db->table('vitimas')->insert([
                    'nome'             => $v['nome'] ?: null,
                    'idade_aparente'   => $v['idade_aparente'] ?: null,
                    'sexo'             => $v['sexo'] ?: null,
                    'raca_cor'         => $v['raca_cor'] ?: null,
                    'profissao'        => $v['profissao'] ?: null,
                    'condicao_juridica'=> $v['condicao_juridica'] ?: null,
                    'menor_de_idade'   => isset($v['menor_de_idade']) ? 1 : 0,
                    'observacoes'      => $v['observacoes'] ?: null,
                ]);
                $vitimaId = $db->insertID();

                $db->table('ocorrencia_vitima')->insert([
                    'ocorrencia_id'     => $id,
                    'vitima_id'   => $vitimaId,
                    'resultado'   => $v['resultado'] ?: null,
                    'ferimentos'  => $v['ferimentos'] ?: null,
                    'identificada'=> empty($v['nome']) ? 0 : 1,
                ]);
            }

            // 4. Re-inserir agentes
            $db->table('ocorrencia_agente')->where('ocorrencia_id', $id)->delete();
            $agentes = $this->request->getPost('agentes') ?? [];
            foreach ($agentes as $a) {
                if (empty($a['corporacao']) && empty($a['descricao'])) continue;

                $db->table('ocorrencia_agente')->insert([
                    'ocorrencia_id'           => $id,
                    'agente_id'         => null,
                    'descricao_agente'  => $a['descricao'] ?: null,
                    'quantidade_agentes'=> $a['quantidade'] ?: 1,
                    'corporacao'        => $a['corporacao'] ?: null,
                    'fardado'           => isset($a['fardado']) ? 1 : 0,
                    'encapuzado'        => isset($a['encapuzado']) ? 1 : 0,
                    'prefixo_viatura'   => $a['prefixo_viatura'] ?: null,
                    'papel_no_caso'     => $a['papel'] ?: 'executor',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro na transação');
            }

            return redirect()->to("ocorrencias/{$id}")->with('message', 'Ocorrência atualizada com sucesso!');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao atualizar caso: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar o caso. Tente novamente.');
        }
    }

    /**
     * Alterna status de publicação (toggle).
     */
    public function publicar(int $id)
    {
        $caso = $this->OcorrenciaModel->find($id);
        if (!$caso) return redirect()->to('dashboard')->with('error', 'Caso não encontrado.');

        $novo = $caso['publicado'] ? 0 : 1;
        $this->OcorrenciaModel->update($id, ['publicado' => $novo]);

        $msg = $novo ? 'Caso publicado com sucesso!' : 'Caso despublicado.';
        return redirect()->back()->with('message', $msg);
    }

    /**
     * Soft-delete de um caso.
     */
    public function deletar(int $id)
    {
        $this->OcorrenciaModel->delete($id);
        return redirect()->to('ocorrencias')->with('message', 'Caso removido.');
    }
}

