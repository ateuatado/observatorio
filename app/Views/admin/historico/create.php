<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/historico') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar ao Acervo
    </a>
    <h1 class="page-title-admin mt-2">Novo <span>Documento Histórico</span></h1>
    <p class="text-muted mb-0">Cadastre um novo item para exibição pública na seção histórica do Observatório.</p>
</div>

<form method="POST" action="<?= base_url('painel/historico/novo') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Informações Básicas</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="titulo">Título do Documento <span class="required-star">*</span></label>
                <input type="text" name="titulo" id="titulo" class="form-control" required placeholder="Ex: Relatório de Violência Policial em Minas Gerais — 1964-1968">
            </div>
            <div class="col-md-6">
                <label for="periodo">Período Histórico (Ex: 1964 - 1968)</label>
                <input type="text" name="periodo" id="periodo" class="form-control" placeholder="Ex: 1964 - 1968">
            </div>
            <div class="col-md-6">
                <label for="categoria">Categoria / Tema</label>
                <input type="text" name="categoria" id="categoria" class="form-control" placeholder="Ex: Ditadura Militar, Desaparecidos Políticos">
            </div>
            <div class="col-md-6">
                <label for="ano_inicio">Ano Início (Para ordenação)</label>
                <input type="number" name="ano_inicio" id="ano_inicio" class="form-control" placeholder="Ex: 1964">
            </div>
            <div class="col-md-6">
                <label for="ano_fim">Ano Fim</label>
                <input type="number" name="ano_fim" id="ano_fim" class="form-control" placeholder="Ex: 1968">
            </div>
            <div class="col-md-12">
                <label for="autora">Autora / Compiladora original</label>
                <input type="text" name="autora" id="autora" class="form-control" placeholder="Ex: Profa. Dra. Helena Ferreira Campos">
            </div>
            <div class="col-12">
                <label for="descricao">Descrição / Resumo do Documento</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="5" placeholder="Insira o resumo ou detalhes do documento..."></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Documento</button>
        <a href="<?= base_url('painel/historico') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
