<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/ocorrencias') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Cadastrar <span>Ocorrência</span></h1>
    <p class="text-muted mb-0">Insira as informações do caso de violência policial.</p>
</div>

<?php if (session()->has('errors')): ?>
<div class="alert-ovpdh-error mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <ul class="mb-0 ps-3">
        <?php foreach (session('errors') as $error): ?>
        <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?= base_url('painel/ocorrencias/nova') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Dados Principais do Caso</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="titulo">Título do Caso <span class="required-star">*</span></label>
                <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ex: Jovem baleado durante abordagem no Aglomerado da Serra" required value="<?= old('titulo') ?>">
            </div>
            <div class="col-md-6">
                <label for="tipo_violencia">Tipo de Violência <span class="required-star">*</span></label>
                <select name="tipo_violencia" id="tipo_violencia" class="form-select" required>
                    <option value="">Selecione...</option>
                    <option value="Homicídio" <?= old('tipo_violencia') === 'Homicídio' ? 'selected' : '' ?>>Homicídio</option>
                    <option value="Lesão Corporal" <?= old('tipo_violencia') === 'Lesão Corporal' ? 'selected' : '' ?>>Lesão Corporal</option>
                    <option value="Abuso de Autoridade" <?= old('tipo_violencia') === 'Abuso de Autoridade' ? 'selected' : '' ?>>Abuso de Autoridade</option>
                    <option value="Tortura" <?= old('tipo_violencia') === 'Tortura' ? 'selected' : '' ?>>Tortura</option>
                    <option value="Prisão Arbitrária" <?= old('tipo_violencia') === 'Prisão Arbitrária' ? 'selected' : '' ?>>Prisão Arbitrária</option>
                    <option value="Violência Sexual" <?= old('tipo_violencia') === 'Violência Sexual' ? 'selected' : '' ?>>Violência Sexual</option>
                    <option value="Execução Extrajudicial" <?= old('tipo_violencia') === 'Execução Extrajudicial' ? 'selected' : '' ?>>Execução Extrajudicial</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="subtipo">Subtipo / Especificação</label>
                <input type="text" name="subtipo" id="subtipo" class="form-control" placeholder="Ex: Disparo de arma de fogo" value="<?= old('subtipo') ?>">
            </div>
            <div class="col-md-6">
                <label for="data_ocorrencia">Data do Fato</label>
                <input type="date" name="data_ocorrencia" id="data_ocorrencia" class="form-control" value="<?= old('data_ocorrencia') ?>">
            </div>
            <div class="col-md-6">
                <label for="hora_ocorrencia">Hora Aproximada</label>
                <input type="time" name="hora_ocorrencia" id="hora_ocorrencia" class="form-control" value="<?= old('hora_ocorrencia') ?>">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>2</span> Localização geográfica</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="local_descricao">Endereço / Descrição do Local</label>
                <input type="text" name="local_descricao" id="local_descricao" class="form-control" placeholder="Ex: Rua Principal, altura do nº 100" value="<?= old('local_descricao') ?>">
            </div>
            <div class="col-md-3">
                <label for="bairro">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Ex: Aglomerado da Serra" value="<?= old('bairro') ?>">
            </div>
            <div class="col-md-3">
                <label for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-control" value="<?= old('cidade', 'Belo Horizonte') ?>">
            </div>
            <div class="col-md-2">
                <label for="estado">Estado</label>
                <input type="text" name="estado" id="estado" class="form-control" value="<?= old('estado', 'MG') ?>" maxlength="2">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 class="form-section-title"><span>3</span> Detalhes e Evidências</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="descricao">Relato Detalhado dos Fatos</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="6" placeholder="Insira o relato dos acontecimentos o mais fielmente possível..."><?= old('descricao') ?></textarea>
            </div>
            <div class="col-12">
                <label for="fontes">Links de Notícias / Fontes de Informação</label>
                <textarea name="fontes" id="fontes" class="form-control" rows="2" placeholder="Insira um link por linha"><?= old('fontes') ?></textarea>
            </div>
            <div class="col-md-4">
                <label for="prioridade">Prioridade de Análise</label>
                <select name="prioridade" id="prioridade" class="form-select">
                    <option value="normal" <?= old('prioridade') === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="alta" <?= old('prioridade') === 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="urgente" <?= old('prioridade') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar como Rascunho</button>
        <a href="<?= base_url('painel/ocorrencias') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
