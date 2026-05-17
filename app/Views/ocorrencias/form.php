<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php $editando = !empty($caso); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.3rem;margin:0;"><?= $editando ? 'Editar caso ' . esc($caso['protocolo_ovp'] ?? '') : 'Registrar novo caso' ?></h1>
        <p class="text-muted mb-0" style="font-size:.8rem;">Preencha os dados do evento de violência policial</p>
    </div>
    <a href="<?= base_url('ocorrencias') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php if (session()->has('errors')): ?>
<div class="alert alert-danger mb-4">
    <i class="bi bi-exclamation-triangle me-2"></i><strong>Corrija os erros:</strong>
    <ul class="mb-0 mt-1 ps-3">
        <?php foreach (session('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?= $editando ? base_url('ocorrencias/'.$caso['id'].'/update') : base_url('ocorrencias/salvar') ?>" method="post" id="formCaso">
<?= csrf_field() ?>

<!-- NAV ABAS -->
<ul class="nav nav-tabs mb-4" id="abas" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aba-basico" type="button"><i class="bi bi-info-circle me-1"></i>Dados básicos</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-local" type="button"><i class="bi bi-geo-alt me-1"></i>Localização</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-vitimas" type="button"><i class="bi bi-people me-1"></i>Vítimas</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-agentes" type="button"><i class="bi bi-person-badge me-1"></i>Agentes</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-status" type="button"><i class="bi bi-flag me-1"></i>Status</button></li>
</ul>

<div class="tab-content">

<!-- ABA 1: DADOS BÁSICOS -->
<div class="tab-pane fade show active" id="aba-basico">
    <div class="ovp-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Data do fato <span class="text-danger">*</span></label>
                <input type="date" name="data_fato" class="form-control" required value="<?= old('data_fato', $caso['data_fato'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Horário (aprox.)</label>
                <input type="time" name="hora_fato" class="form-control" value="<?= old('hora_fato', $caso['hora_fato'] ?? '') ?>">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Tipo de violência <span class="text-danger">*</span></label>
                <select name="tipo_violencia" class="form-select" required>
                    <option value="">-- Selecione --</option>
                    <?php foreach(['execucao'=>'Execução','chacina'=>'Chacina','tortura'=>'Tortura','abuso_poder'=>'Abuso de poder','morte_custodia'=>'Morte em custódia','desaparecimento'=>'Desaparecimento','ameaca'=>'Ameaça'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= old('tipo_violencia', $caso['tipo_violencia'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Vítimas fatais</label>
                <input type="number" name="vitimas_fatais" class="form-control" min="0" value="<?= old('vitimas_fatais', $caso['vitimas_fatais'] ?? 0) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Feridos / sobreviventes</label>
                <input type="number" name="vitimas_nao_fatais" class="form-control" min="0" value="<?= old('vitimas_nao_fatais', $caso['vitimas_nao_fatais'] ?? 0) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Descrição narrativa do caso</label>
                <textarea name="descricao_livre" class="form-control" rows="6" placeholder="Relate o que aconteceu com o máximo de detalhes disponíveis..."><?= old('descricao_livre', $caso['descricao_livre'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Versão oficial / policial</label>
                <textarea name="versao_oficial" class="form-control" rows="4" placeholder="O que a polícia declarou..."><?= old('versao_oficial', $caso['versao_oficial'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Versão de testemunhas</label>
                <textarea name="versao_testemunhas" class="form-control" rows="4" placeholder="O que testemunhas e/ou sobreviventes relataram..."><?= old('versao_testemunhas', $caso['versao_testemunhas'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- ABA 2: LOCALIZAÇÃO -->
<div class="tab-pane fade" id="aba-local">
    <div class="ovp-card p-4 mb-4">
        <p class="text-muted mb-3" style="font-size:.85rem;"><i class="bi bi-info-circle me-1"></i>Preencha o máximo de informações disponíveis sobre o local do fato.</p>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Logradouro (rua, avenida, rodovia)</label>
                <input type="text" name="logradouro" class="form-control" value="<?= old('logradouro', $caso['logradouro'] ?? '') ?>" placeholder="Ex: Av. Aricanduva">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Número / Km</label>
                <input type="text" name="numero" class="form-control" value="<?= old('numero', $caso['numero'] ?? '') ?>" placeholder="Ex: 1500 ou Km 32">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="<?= old('bairro', $caso['bairro'] ?? '') ?>" placeholder="Ex: Jardim São Luís">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Zona</label>
                <select name="zona_cidade" class="form-select">
                    <option value="">--</option>
                    <?php foreach(['norte'=>'Norte','sul'=>'Sul','leste'=>'Leste','oeste'=>'Oeste','centro'=>'Centro'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= old('zona_cidade', $caso['zona_cidade'] ?? '') === $v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Município <span class="text-danger">*</span></label>
                <input type="text" name="municipio" class="form-control" required value="<?= old('municipio', $caso['municipio'] ?? '') ?>" placeholder="Ex: São Paulo" list="lista-municipios">
                <datalist id="lista-municipios">
                    <?php foreach($municipios ?? [] as $m): ?>
                    <option value="<?= esc($m['municipio']) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Estado</label>
                <input type="text" name="estado" class="form-control" maxlength="2" value="<?= old('estado', $caso['estado'] ?? 'SP') ?>" placeholder="SP">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Tipo de local</label>
                <select name="tipo_local" class="form-select">
                    <option value="">-- Selecione --</option>
                    <?php foreach(['via_publica'=>'Via pública','residencia'=>'Residência','bar_comercio'=>'Bar / comércio','unidade_policial'=>'Unidade policial','unidade_prisional'=>'Unidade prisional / CDP','unidade_socioeduc'=>'Unidade socioeducativa (CASA/FEBEM)','rodovia'=>'Rodovia','hospital'=>'Hospital','outro'=>'Outro'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= old('tipo_local', $caso['tipo_local'] ?? '') === $v ? 'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Descrição complementar do local</label>
                <input type="text" name="descricao_local" class="form-control" value="<?= old('descricao_local', $caso['descricao_local'] ?? '') ?>" placeholder="Ex: Em frente ao mercado, próximo à escola...">
            </div>
        </div>
    </div>
</div>

<!-- ABA 3: VÍTIMAS -->
<div class="tab-pane fade" id="aba-vitimas">
    <div class="ovp-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 style="font-size:1rem;font-family:var(--font-body);margin:0;">Vítimas do caso</h3>
                <p class="text-muted mb-0" style="font-size:.8rem;">Adicione uma linha por vítima. Deixe o nome em branco se não identificada.</p>
            </div>
            <button type="button" class="btn-ovp btn-sm" id="btnAddVitima" style="font-size:.82rem;padding:.35rem .8rem;">
                <i class="bi bi-plus-lg me-1"></i>Adicionar vítima
            </button>
        </div>

        <div id="listaVitimas">
            <!-- Template de vítima (escondido) -->
            <div class="vitima-row border rounded p-3 mb-3 bg-light d-none" id="templateVitima">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-semibold" style="font-size:.85rem;">Vítima <span class="num-vitima"></span></span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remover-vitima"><i class="bi bi-trash"></i></button>
                </div>
                <div class="row g-2">
                    <div class="col-md-5"><label class="form-label" style="font-size:.78rem;">Nome completo <span class="text-muted">(deixe vazio se não identificado/a)</span></label>
                        <input type="text" name="vitimas[__IDX__][nome]" class="form-control form-control-sm" placeholder="Nome ou 'Não identificado'"></div>
                    <div class="col-md-2"><label class="form-label" style="font-size:.78rem;">Idade</label>
                        <input type="number" name="vitimas[__IDX__][idade_aparente]" class="form-control form-control-sm" min="0" max="120" placeholder="Idade"></div>
                    <div class="col-md-2"><label class="form-label" style="font-size:.78rem;">Sexo</label>
                        <select name="vitimas[__IDX__][sexo]" class="form-select form-select-sm">
                            <option value="">--</option>
                            <option value="masculino">Masc.</option>
                            <option value="feminino">Fem.</option>
                            <option value="nao_informado">N/I</option>
                        </select></div>
                    <div class="col-md-3"><label class="form-label" style="font-size:.78rem;">Raça/Cor</label>
                        <select name="vitimas[__IDX__][raca_cor]" class="form-select form-select-sm">
                            <option value="">--</option>
                            <option value="preta">Preta</option>
                            <option value="parda">Parda</option>
                            <option value="branca">Branca</option>
                            <option value="amarela">Amarela</option>
                            <option value="indigena">Indígena</option>
                            <option value="nao_informada">N/I</option>
                        </select></div>
                    <div class="col-md-4"><label class="form-label" style="font-size:.78rem;">Profissão / ocupação</label>
                        <input type="text" name="vitimas[__IDX__][profissao]" class="form-control form-control-sm" placeholder="Ex: Estudante, Pedreiro..."></div>
                    <div class="col-md-4"><label class="form-label" style="font-size:.78rem;">Resultado</label>
                        <select name="vitimas[__IDX__][resultado]" class="form-select form-select-sm">
                            <option value="fatal">Fatal</option>
                            <option value="ferido">Ferido/a</option>
                            <option value="sobreviveu">Sobreviveu</option>
                            <option value="desaparecido">Desaparecido/a</option>
                        </select></div>
                    <div class="col-md-4"><label class="form-label" style="font-size:.78rem;">Condição jurídica</label>
                        <select name="vitimas[__IDX__][condicao_juridica]" class="form-select form-select-sm">
                            <option value="">--</option>
                            <option value="civil_inocente">Civil inocente</option>
                            <option value="suspeito">Suspeito/a</option>
                            <option value="em_fuga">Em fuga</option>
                            <option value="preso">Preso/a</option>
                            <option value="menor_infrator">Menor infrator</option>
                            <option value="manifestante">Manifestante</option>
                        </select></div>
                    <div class="col-12 d-flex gap-3">
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="vitimas[__IDX__][menor_de_idade]" value="1" id="menor__IDX__">
                            <label class="form-check-label" for="menor__IDX__" style="font-size:.8rem;">Menor de idade</label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="vitimas[__IDX__][gestante]" value="1" id="gestante__IDX__">
                            <label class="form-check-label" for="gestante__IDX__" style="font-size:.8rem;">Gestante</label>
                        </div>
                    </div>
                    <div class="col-12"><label class="form-label" style="font-size:.78rem;">Observações</label>
                        <input type="text" name="vitimas[__IDX__][observacoes]" class="form-control form-control-sm" placeholder="Contexto, antecedentes, ferimentos específicos..."></div>
                </div>
            </div>
        </div>
        <p class="text-muted text-center py-3 mb-0" id="semVitimas" style="font-size:.85rem;">
            <i class="bi bi-person-plus d-block fs-4 mb-1 opacity-25"></i>
            Clique em "Adicionar vítima" para registrar cada vítima individualmente.
        </p>
    </div>
</div>

<!-- ABA 4: AGENTES -->
<div class="tab-pane fade" id="aba-agentes">
    <div class="ovp-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 style="font-size:1rem;font-family:var(--font-body);margin:0;">Agentes envolvidos</h3>
                <p class="text-muted mb-0" style="font-size:.8rem;">Registre grupos de agentes. Identifique individualmente apenas quando possível.</p>
            </div>
            <button type="button" class="btn-ovp btn-sm" id="btnAddAgente" style="font-size:.82rem;padding:.35rem .8rem;">
                <i class="bi bi-plus-lg me-1"></i>Adicionar agente/grupo
            </button>
        </div>

        <div id="listaAgentes">
            <div class="agente-row border rounded p-3 mb-3 bg-light d-none" id="templateAgente">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-semibold" style="font-size:.85rem;">Agente / grupo <span class="num-agente"></span></span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remover-agente"><i class="bi bi-trash"></i></button>
                </div>
                <div class="row g-2">
                    <div class="col-md-8"><label class="form-label" style="font-size:.78rem;">Descrição <span class="text-muted">(quando não há nome identificado)</span></label>
                        <input type="text" name="agentes[__IDX__][descricao]" class="form-control form-control-sm" placeholder="Ex: Dois PMs da Força Tática, não identificados"></div>
                    <div class="col-md-2"><label class="form-label" style="font-size:.78rem;">Quantidade</label>
                        <input type="number" name="agentes[__IDX__][quantidade]" class="form-control form-control-sm" min="1" value="1"></div>
                    <div class="col-md-2"><label class="form-label" style="font-size:.78rem;">Corporação</label>
                        <select name="agentes[__IDX__][corporacao]" class="form-select form-select-sm">
                            <option value="">--</option>
                            <option>PM</option><option>PC</option><option>ROTA</option>
                            <option>CHOQUE</option><option>ROCAM</option><option>GCM</option>
                            <option>PF</option><option>Força Nacional</option>
                            <option>Agente Penitenciário</option><option>Outro</option>
                        </select></div>
                    <div class="col-md-4"><label class="form-label" style="font-size:.78rem;">Unidade / Batalhão</label>
                        <input type="text" name="agentes[__IDX__][unidade]" class="form-control form-control-sm" placeholder="Ex: 1º Batalhão de Choque"></div>
                    <div class="col-md-3"><label class="form-label" style="font-size:.78rem;">Prefixo da viatura</label>
                        <input type="text" name="agentes[__IDX__][prefixo_viatura]" class="form-control form-control-sm" placeholder="Ex: 3195"></div>
                    <div class="col-md-5"><label class="form-label" style="font-size:.78rem;">Papel no caso</label>
                        <select name="agentes[__IDX__][papel]" class="form-select form-select-sm">
                            <option value="executor">Executor</option>
                            <option value="participante">Participante</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="informado">Informado</option>
                        </select></div>
                    <div class="col-12 d-flex gap-3">
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="agentes[__IDX__][fardado]" value="1" id="fardado__IDX__" checked>
                            <label class="form-check-label" for="fardado__IDX__" style="font-size:.8rem;">Fardado</label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="agentes[__IDX__][encapuzado]" value="1" id="encap__IDX__">
                            <label class="form-check-label" for="encap__IDX__" style="font-size:.8rem;">Encapuzado</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted text-center py-3 mb-0" id="semAgentes" style="font-size:.85rem;">
            <i class="bi bi-person-badge d-block fs-4 mb-1 opacity-25"></i>
            Clique em "Adicionar agente/grupo" para registrar os envolvidos.
        </p>
    </div>
</div>

<!-- ABA 5: STATUS -->
<div class="tab-pane fade" id="aba-status">
    <div class="ovp-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status da investigação</label>
                <select name="status_investigacao" class="form-select">
                    <?php foreach(['sem_inquerito'=>'Sem inquérito instaurado','inquerito_aberto'=>'Inquérito em andamento','arquivado'=>'Arquivado sem indiciamento','indiciado'=>'Agente(s) indiciado(s)','acao_penal'=>'Ação penal em curso','condenado'=>'Condenado','absolvido'=>'Absolvido'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= old('status_investigacao',$caso['status_investigacao']??'sem_inquerito')===$v?'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Delegacia responsável</label>
                <input type="text" name="delegacia_responsavel" class="form-control" placeholder="Ex: DHPP — Depto. de Homicídios">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Número do inquérito</label>
                <input type="text" name="numero_inquerito" class="form-control" placeholder="Ex: 123/2006">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="publicado" value="1" id="publicado" <?= old('publicado',$caso['publicado']??0)?'checked':'' ?>>
                    <label class="form-check-label fw-semibold" for="publicado">
                        Publicar este caso (visível no site público)
                    </label>
                </div>
                <small class="text-muted">Casos não publicados ficam como rascunho visível apenas para pesquisadores.</small>
            </div>
        </div>
    </div>
</div>

</div><!-- /tab-content -->

<!-- BOTÕES -->
<div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid var(--ovp-borda);">
    <a href="<?= base_url('ocorrencias') ?>" class="btn btn-outline-secondary">Cancelar</a>
    <div class="d-flex gap-2">
        <button type="submit" name="publicado" value="0" class="btn btn-outline-danger">
            <i class="bi bi-floppy me-1"></i>Salvar como rascunho
        </button>
        <button type="submit" name="publicado_final" value="1" class="btn-ovp">
            <i class="bi bi-check-lg me-1"></i><?= $editando ? 'Atualizar caso' : 'Salvar e publicar' ?>
        </button>
    </div>
</div>

</form>

<?= $this->endSection() ?>

<?= $this->section('head_extra') ?>
<style>
/* ═══════════════════════════════════════════════════════════════
   FORMULÁRIO DE CASO — Alto Contraste (WCAG 2.1 AA)
   Sobrescreve Bootstrap para garantir legibilidade máxima
   ═══════════════════════════════════════════════════════════════ */

/* ── Cabeçalho da página ─────────────────────────────────────── */
h1 { color:#111827 !important; }
.text-muted { color:#4b5563 !important; }

/* ── Abas de navegação ───────────────────────────────────────── */
.nav-tabs { border-bottom:2px solid #d1d5db; gap:.25rem; }
.nav-tabs .nav-link {
    color:#374151; font-weight:600; font-size:.875rem;
    border:1px solid transparent; border-radius:7px 7px 0 0;
    padding:.5rem 1rem; transition:all .15s;
}
.nav-tabs .nav-link:hover { background:#f1f5f9; color:#1e293b; border-color:#e2e8f0; }
.nav-tabs .nav-link.active {
    background:#fff; color:#1d4ed8; font-weight:700;
    border-color:#d1d5db #d1d5db #fff; border-bottom:2px solid #1d4ed8;
}

/* ── Cards por aba ───────────────────────────────────────────── */
.ovp-card {
    background:#fff !important;
    border:1px solid #d1d5db !important;
    border-radius:10px !important;
    box-shadow:0 1px 4px rgba(0,0,0,.06) !important;
}

/* Acento colorido no topo por aba */
#aba-basico  .ovp-card { border-top:3px solid #ef4444 !important; }
#aba-local   .ovp-card { border-top:3px solid #f59e0b !important; }
#aba-vitimas .ovp-card { border-top:3px solid #3b82f6 !important; }
#aba-agentes .ovp-card { border-top:3px solid #6b7280 !important; }
#aba-status  .ovp-card { border-top:3px solid #10b981 !important; }

/* ── Labels ──────────────────────────────────────────────────── */
.form-label, label {
    font-size:.82rem !important;
    font-weight:600 !important;
    color:#374151 !important;
    margin-bottom:.3rem !important;
}
.fw-semibold { color:#1e293b !important; }

/* ── Inputs, selects e textareas ─────────────────────────────── */
.form-control,
.form-select,
.form-control-sm,
.form-select-sm {
    background:#fff !important;
    border:1.5px solid #9ca3af !important;
    color:#111827 !important;
    border-radius:6px !important;
    font-size:.875rem !important;
    padding:.45rem .7rem !important;
    transition:border-color .15s, box-shadow .15s !important;
    font-family:inherit !important;
}
.form-control-sm,
.form-select-sm {
    font-size:.8rem !important;
    padding:.35rem .6rem !important;
}
.form-control:focus,
.form-select:focus {
    border-color:#3b82f6 !important;
    box-shadow:0 0 0 3px rgba(59,130,246,.15) !important;
    outline:none !important;
}
.form-control::placeholder { color:#9ca3af !important; }
textarea.form-control { resize:vertical; min-height:90px; }

/* ── Texto de ajuda e muted ──────────────────────────────────── */
.form-text, small.text-muted { color:#6b7280 !important; font-size:.78rem !important; }

/* ── Cards de vítima e agente ────────────────────────────────── */
.vitima-row.border,
.agente-row.border {
    background:#f8fafc !important;
    border:1px solid #e2e8f0 !important;
    border-radius:8px !important;
}
.vitima-row.border { border-left:3px solid #3b82f6 !important; }
.agente-row.border { border-left:3px solid #6b7280 !important; }
.vitima-row .fw-semibold,
.agente-row .fw-semibold { color:#1e293b !important; font-size:.87rem !important; }

/* ── Botão remover (Bootstrap outline-danger) ─────────────────── */
.btn-outline-danger {
    border:1px solid #fca5a5 !important;
    color:#dc2626 !important;
    background:#fff !important;
    font-size:.78rem !important; font-weight:600 !important;
}
.btn-outline-danger:hover {
    background:#fee2e2 !important;
    border-color:#ef4444 !important;
    color:#b91c1c !important;
}

/* ── Checkboxes ──────────────────────────────────────────────── */
.form-check-label { color:#374151 !important; font-size:.82rem !important; font-weight:500 !important; }
.form-check-input { border:1.5px solid #9ca3af !important; }
.form-check-input:checked { background-color:#2563eb !important; border-color:#2563eb !important; }

/* ── Switch de publicação ────────────────────────────────────── */
.form-switch .form-check-label { font-size:.9rem !important; color:#1e293b !important; font-weight:600 !important; }

/* ── Placeholder vazio (sem vítimas / agentes) ───────────────── */
#semVitimas, #semAgentes { color:#9ca3af !important; }

/* ── Alertas de erro ─────────────────────────────────────────── */
.alert-danger {
    background:#fee2e2 !important; border:1px solid #fca5a5 !important;
    color:#991b1b !important; border-radius:8px !important;
}
.alert-danger strong { color:#7f1d1d !important; }

/* ── Barra de botões finais ──────────────────────────────────── */
.btn-outline-secondary {
    border:1.5px solid #9ca3af !important; color:#374151 !important;
    background:#fff !important; font-weight:600 !important;
    border-radius:7px !important; padding:.5rem 1.2rem !important;
}
.btn-outline-secondary:hover { background:#f9fafb !important; border-color:#6b7280 !important; }

/* Rascunho */
button[name="publicado"][value="0"] {
    border:1.5px solid #9ca3af !important; color:#374151 !important;
    background:#fff !important; font-weight:600 !important;
    border-radius:7px !important; padding:.5rem 1.2rem !important;
}
button[name="publicado"][value="0"]:hover { background:#f9fafb !important; }

/* Salvar e publicar */
.btn-ovp {
    background:#16a34a !important; color:#fff !important;
    border:none !important; font-weight:700 !important;
    border-radius:7px !important; padding:.55rem 1.5rem !important;
    font-size:.95rem !important;
    box-shadow:0 2px 4px rgba(22,163,74,.25) !important;
    transition:background .2s, box-shadow .2s !important;
}
.btn-ovp:hover {
    background:#15803d !important;
    box-shadow:0 3px 8px rgba(22,163,74,.35) !important;
}
/* botão adicionar vítima/agente */
.btn-ovp.btn-sm {
    background:#2563eb !important;
    box-shadow:0 2px 4px rgba(37,99,235,.2) !important;
    font-size:.82rem !important; padding:.35rem .9rem !important;
}
.btn-ovp.btn-sm:hover { background:#1d4ed8 !important; }

/* ── Linha divisória dos botões finais ───────────────────────── */
[style*="border-top:1px solid var(--ovp-borda)"] {
    border-top:1px solid #e5e7eb !important;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(function() {
    let idxVitima = 0, idxAgente = 0;
    const tplV = document.getElementById('templateVitima');
    const tplA = document.getElementById('templateAgente');

    function adicionarVitima() {
        const clone = tplV.cloneNode(true);
        clone.id = 'vitima_' + idxVitima;
        clone.classList.remove('d-none');
        clone.innerHTML = clone.innerHTML.replaceAll('__IDX__', idxVitima);
        clone.querySelector('.num-vitima').textContent = '#' + (idxVitima + 1);
        clone.querySelector('.btn-remover-vitima').addEventListener('click', () => {
            clone.remove();
            document.getElementById('semVitimas').style.display =
                document.querySelectorAll('#listaVitimas .vitima-row:not(.d-none)').length === 0 ? '' : 'none';
        });
        document.getElementById('listaVitimas').appendChild(clone);
        document.getElementById('semVitimas').style.display = 'none';
        idxVitima++;
    }

    function adicionarAgente() {
        const clone = tplA.cloneNode(true);
        clone.id = 'agente_' + idxAgente;
        clone.classList.remove('d-none');
        clone.innerHTML = clone.innerHTML.replaceAll('__IDX__', idxAgente);
        clone.querySelector('.num-agente').textContent = '#' + (idxAgente + 1);
        clone.querySelector('.btn-remover-agente').addEventListener('click', () => {
            clone.remove();
            document.getElementById('semAgentes').style.display =
                document.querySelectorAll('#listaAgentes .agente-row:not(.d-none)').length === 0 ? '' : 'none';
        });
        document.getElementById('listaAgentes').appendChild(clone);
        document.getElementById('semAgentes').style.display = 'none';
        idxAgente++;
    }

    document.getElementById('btnAddVitima').addEventListener('click', adicionarVitima);
    document.getElementById('btnAddAgente').addEventListener('click', adicionarAgente);

    // Pré-popular se editando
    <?php if (!empty($vitimas_existentes)): ?>
    <?php foreach($vitimas_existentes as $v): ?>adicionarVitima();<?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($agentes_existentes)): ?>
    <?php foreach($agentes_existentes as $a): ?>adicionarAgente();<?php endforeach; ?>
    <?php endif; ?>
})();
</script>
<?= $this->endSection() ?>
