<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php $editando = !empty($acao); ?>

<!-- BREADCRUMB -->
<nav style="font-size:.8rem;margin-bottom:1.5rem;">
    <a href="<?= base_url('acoes-seguranca') ?>" class="text-muted text-decoration-none">
        <i class="bi bi-shield-exclamation me-1"></i>Ações de Segurança
    </a>
    <span class="mx-2 text-muted">/</span>
    <span><?= $editando ? esc($acao['nome'] ?? 'Editar ação') : 'Nova Ação' ?></span>
</nav>

<div class="row justify-content-center">
<div class="col-xl-9">

<div class="ovp-card p-4">
    <h1 class="h5 fw-bold mb-4" style="font-family:var(--font-heading);">
        <i class="bi bi-<?= $editando ? 'pencil-square' : 'shield-plus' ?> me-2 text-danger"></i>
        <?= $editando ? 'Editar Ação de Segurança' : 'Nova Ação de Segurança' ?>
    </h1>

    <form method="post"
          action="<?= $editando ? base_url('acoes-seguranca/' . $acao['id'] . '/update') : base_url('acoes-seguranca/salvar') ?>">
        <?= csrf_field() ?>

        <!-- =========================================================
             IDENTIFICAÇÃO
        ========================================================== -->
        <fieldset class="mb-4">
            <legend class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.07em;color:var(--ovp-cinza-medio);border-bottom:1px solid var(--ovp-borda);padding-bottom:.5rem;">
                <i class="bi bi-tag me-1"></i>Identificação
            </legend>

            <div class="row g-3">
                <!-- Nome -->
                <div class="col-md-8">
                    <label for="nome" class="form-label fw-semibold" style="font-size:.83rem;">
                        Nome da operação / ação
                        <span class="text-muted fw-normal">(deixe em branco se não nomeada)</span>
                    </label>
                    <input type="text" id="nome" name="nome" class="form-control"
                           placeholder="Ex: Operação Escudo, Operação Verão..."
                           value="<?= esc($acao['nome'] ?? old('nome') ?? '') ?>">
                </div>

                <!-- Tipo de agente -->
                <div class="col-md-4">
                    <label for="tipo_agente" class="form-label fw-semibold" style="font-size:.83rem;">
                        Tipo de agente <span class="text-danger">*</span>
                    </label>
                    <select id="tipo_agente" name="tipo_agente" class="form-select" required>
                        <option value="">— Selecione —</option>
                        <?php
                        $tipos = [
                            'estatal'     => 'Estatal (PM, PC, Guarda Civil etc.)',
                            'paraestatal' => 'Paraestatal (milícia com apoio estatal)',
                            'milicia'     => 'Milícia / Grupo armado paralelo',
                            'comunitario' => 'Ação comunitária',
                            'indefinido'  => 'Indefinido / Em investigação',
                        ];
                        $cur = $acao['tipo_agente'] ?? old('tipo_agente') ?? '';
                        foreach ($tipos as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= $cur === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <!-- =========================================================
             PERÍODO
        ========================================================== -->
        <fieldset class="mb-4">
            <legend class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.07em;color:var(--ovp-cinza-medio);border-bottom:1px solid var(--ovp-borda);padding-bottom:.5rem;">
                <i class="bi bi-calendar-range me-1"></i>Período
            </legend>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="data_inicio" class="form-label fw-semibold" style="font-size:.83rem;">Data de início</label>
                    <input type="date" id="data_inicio" name="data_inicio" class="form-control"
                           value="<?= esc($acao['data_inicio'] ?? old('data_inicio') ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="data_fim" class="form-label fw-semibold" style="font-size:.83rem;">
                        Data de fim
                        <span class="text-muted fw-normal">(vazio = em curso)</span>
                    </label>
                    <input type="date" id="data_fim" name="data_fim" class="form-control"
                           value="<?= esc($acao['data_fim'] ?? old('data_fim') ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="precisao_temporal" class="form-label fw-semibold" style="font-size:.83rem;">
                        Precisão das datas <span class="text-danger">*</span>
                    </label>
                    <select id="precisao_temporal" name="precisao_temporal" class="form-select" required>
                        <?php
                        $precisoes = ['exata' => 'Data exata', 'aproximada' => 'Aproximada', 'estimada' => 'Estimada'];
                        $curP = $acao['precisao_temporal'] ?? old('precisao_temporal') ?? 'aproximada';
                        foreach ($precisoes as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= $curP === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <!-- =========================================================
             NARRATIVA
        ========================================================== -->
        <fieldset class="mb-4">
            <legend class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.07em;color:var(--ovp-cinza-medio);border-bottom:1px solid var(--ovp-borda);padding-bottom:.5rem;">
                <i class="bi bi-journal-text me-1"></i>Narrativa e contexto
            </legend>

            <div class="mb-3">
                <label for="motivacao_declarada" class="form-label fw-semibold" style="font-size:.83rem;">
                    Motivação declarada
                    <span class="text-muted fw-normal">— justificativa pública ou oficial</span>
                </label>
                <textarea id="motivacao_declarada" name="motivacao_declarada"
                          class="form-control" rows="3"
                          placeholder="O que foi declarado oficialmente sobre os objetivos desta ação..."
                ><?= esc($acao['motivacao_declarada'] ?? old('motivacao_declarada') ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="motivacao_inferida" class="form-label fw-semibold" style="font-size:.83rem;">
                    Motivação inferida
                    <span class="text-muted fw-normal">— análise dos pesquisadores/curadores</span>
                </label>
                <textarea id="motivacao_inferida" name="motivacao_inferida"
                          class="form-control" rows="3"
                          placeholder="Com base nas ocorrências e fontes, qual a motivação real ou contextual desta ação..."
                ><?= esc($acao['motivacao_inferida'] ?? old('motivacao_inferida') ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label fw-semibold" style="font-size:.83rem;">
                    Histórico / Narrativa completa
                </label>
                <textarea id="descricao" name="descricao"
                          class="form-control" rows="6"
                          placeholder="Descrição histórica detalhada da ação, seus desdobramentos e consequências..."
                ><?= esc($acao['descricao'] ?? old('descricao') ?? '') ?></textarea>
            </div>
        </fieldset>

        <!-- =========================================================
             STATUS E VISIBILIDADE
        ========================================================== -->
        <fieldset class="mb-4">
            <legend class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.07em;color:var(--ovp-cinza-medio);border-bottom:1px solid var(--ovp-borda);padding-bottom:.5rem;">
                <i class="bi bi-shield-lock me-1"></i>Status e visibilidade
            </legend>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold" style="font-size:.83rem;">
                        Status do registro <span class="text-danger">*</span>
                    </label>
                    <select id="status" name="status" class="form-select" required>
                        <?php
                        $statuses = ['em_analise' => 'Em análise', 'confirmada' => 'Confirmada', 'arquivada' => 'Arquivada'];
                        $curS = $acao['status'] ?? old('status') ?? 'em_analise';
                        foreach ($statuses as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= $curS === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Somente ações "Confirmadas" são exibidas por padrão na listagem.
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="visibilidade" class="form-label fw-semibold" style="font-size:.83rem;">
                        Visibilidade <span class="text-danger">*</span>
                    </label>
                    <select id="visibilidade" name="visibilidade" class="form-select" required>
                        <?php
                        $visibilidades = [
                            'publica'  => 'Pública — visível a todos',
                            'restrita' => 'Restrita — somente pesquisadores+',
                            'sigilosa' => 'Sigilosa — somente curadores com acesso especial',
                        ];
                        $curV = $acao['visibilidade'] ?? old('visibilidade') ?? 'restrita';
                        foreach ($visibilidades as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= $curV === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                        Recomenda-se manter como "Restrita" até revisão jurídica.
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- ERROS -->
        <?php if (!empty(session('errors'))): ?>
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3" style="font-size:.85rem;">
                <?php foreach (session('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- BOTÕES -->
        <div class="d-flex gap-2 justify-content-end">
            <a href="<?= base_url('acoes-seguranca') ?>" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn-ovp">
                <i class="bi bi-<?= $editando ? 'floppy2' : 'shield-plus' ?> me-2"></i>
                <?= $editando ? 'Salvar alterações' : 'Cadastrar Ação' ?>
            </button>
        </div>
    </form>
</div>

</div>
</div>

<?= $this->endSection() ?>
