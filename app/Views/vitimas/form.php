<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php $editando = !empty($vitima); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.3rem;margin:0;">
            <?= $editando ? 'Editar vítima' : 'Cadastrar vítima' ?>
        </h1>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            <?= $editando ? 'Atualize os dados cadastrais desta vítima.' : 'Cadastro avulso de vítima (sem vínculo a caso específico).' ?>
        </p>
    </div>
    <a href="<?= $editando ? base_url('vitimas/' . $vitima['id']) : base_url('vitimas') ?>"
       class="btn btn-sm btn-outline-secondary">
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

<form action="<?= $editando ? base_url('vitimas/' . $vitima['id'] . '/update') : base_url('vitimas/salvar') ?>"
      method="post" id="formVitima">
<?= csrf_field() ?>

<div class="row g-4">
    <!-- Coluna principal -->
    <div class="col-lg-8">
        <div class="ovp-card p-4 mb-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-person me-2 text-danger"></i>Dados de identificação
            </h2>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nome completo
                        <span class="text-muted fw-normal" style="font-size:.82rem;">(deixe em branco se não identificada)</span>
                    </label>
                    <input type="text" name="nome" class="form-control"
                           value="<?= old('nome', $vitima['nome'] ?? '') ?>"
                           placeholder="Nome ou deixe vazio para 'Não identificada'">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data de nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control"
                           value="<?= old('data_nascimento', $vitima['data_nascimento'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Idade aparente</label>
                    <input type="number" name="idade_aparente" class="form-control" min="0" max="120"
                           value="<?= old('idade_aparente', $vitima['idade_aparente'] ?? '') ?>"
                           placeholder="Idade estimada">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Profissão / Ocupação</label>
                    <input type="text" name="profissao" class="form-control"
                           value="<?= old('profissao', $vitima['profissao'] ?? '') ?>"
                           placeholder="Ex: Estudante, Pedreiro...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">--</option>
                        <?php foreach(['masculino'=>'Masculino','feminino'=>'Feminino','nao_binario'=>'Não-binário','nao_informado'=>'Não informado'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= old('sexo', $vitima['sexo'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Raça / Cor</label>
                    <select name="raca_cor" class="form-select">
                        <option value="">--</option>
                        <?php foreach(['branca'=>'Branca','preta'=>'Preta','parda'=>'Parda','amarela'=>'Amarela','indigena'=>'Indígena','nao_informada'=>'Não informada'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= old('raca_cor', $vitima['raca_cor'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Condição jurídica</label>
                    <select name="condicao_juridica" class="form-select">
                        <option value="">--</option>
                        <?php foreach(['civil_inocente'=>'Civil inocente','suspeito'=>'Suspeito/a','em_fuga'=>'Em fuga','preso'=>'Preso/a','menor_infrator'=>'Menor infrator','manifestante'=>'Manifestante'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= old('condicao_juridica', $vitima['condicao_juridica'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 d-flex gap-4 flex-wrap">
                    <?php foreach([['menor_de_idade','menor_de_idade','Menor de idade'],['gestante','gestante','Gestante'],['pcd','pcd','Pessoa com deficiência (PcD)']] as [$name,$id,$label]): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="<?= $name ?>" value="1"
                               id="chk_<?= $id ?>"
                               <?= old($name, $vitima[$name] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chk_<?= $id ?>" style="font-size:.87rem;"><?= $label ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna lateral -->
    <div class="col-lg-4">
        <div class="ovp-card p-4 mb-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-file-text me-2 text-danger"></i>Informações complementares
            </h2>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Antecedentes (versão policial)</label>
                    <textarea name="antecedentes_versao_policial" class="form-control" rows="4"
                              placeholder="Registre o que a polícia alega sobre antecedentes..."><?= old('antecedentes_versao_policial', $vitima['antecedentes_versao_policial'] ?? '') ?></textarea>
                    <small class="text-muted">Esta informação é para registro interno e análise crítica.</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Observações gerais</label>
                    <textarea name="observacoes" class="form-control" rows="4"
                              placeholder="Outras informações relevantes..."><?= old('observacoes', $vitima['observacoes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BOTÕES -->
<div class="d-flex justify-content-between align-items-center mt-2 pt-3" style="border-top:1px solid var(--ovp-borda);">
    <a href="<?= $editando ? base_url('vitimas/' . $vitima['id']) : base_url('vitimas') ?>"
       class="btn btn-outline-secondary">Cancelar</a>
    <div class="d-flex gap-2">
        <?php if ($editando): ?>
        <a href="<?= base_url('vitimas/' . $vitima['id'] . '/deletar') ?>"
           class="btn btn-outline-danger"
           onclick="return confirm('Remover esta vítima? A operação falhará se ela estiver vinculada a algum caso.');">
            <i class="bi bi-trash me-1"></i>Excluir
        </a>
        <?php endif; ?>
        <button type="submit" class="btn-ovp">
            <i class="bi bi-check-lg me-1"></i><?= $editando ? 'Salvar alterações' : 'Cadastrar vítima' ?>
        </button>
    </div>
</div>

</form>

<?= $this->endSection() ?>
