<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/produtos-admin') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Nova <span>Produção Acadêmica</span></h1>
    <p class="text-muted mb-0">Cadastre um artigo, livro ou relatório para visualização pública.</p>
</div>

<form method="POST" action="<?= base_url('painel/produtos-admin/novo') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Dados de Publicação</h2>
        <div class="row g-3">
            <div class="col-12">
                <label for="titulo">Título da Publicação <span class="required-star">*</span></label>
                <input type="text" name="titulo" id="titulo" class="form-control" required placeholder="Ex: Letalidade Policial em Minas Gerais: Uma análise das ocorrências de 2018-2022">
            </div>
            <div class="col-md-12">
                <label for="autores">Autores (Formato ABNT) <span class="required-star">*</span></label>
                <input type="text" name="autores" id="autores" class="form-control" required placeholder="Ex: BORGES, A. L.; FERREIRA, J. P.">
            </div>
            <div class="col-md-6">
                <label for="tipo">Tipo de Produção <span class="required-star">*</span></label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="Artigo Científico">Artigo Científico</option>
                    <option value="Livro/Capítulo">Livro/Capítulo</option>
                    <option value="Relatório de Pesquisa">Relatório de Pesquisa</option>
                    <option value="Dissertação de Mestrado">Dissertação de Mestrado</option>
                    <option value="Boletim Informativo">Boletim Informativo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="ano">Ano de Publicação</label>
                <input type="number" name="ano" id="ano" class="form-control" placeholder="Ex: 2024">
            </div>
            <div class="col-md-3">
                <label for="doi">DOI (Identificador Digital)</label>
                <input type="text" name="doi" id="doi" class="form-control" placeholder="Ex: 10.31060/rbsp...">
            </div>
            <div class="col-md-6">
                <label for="publicacao">Periódico / Revista / Editora</label>
                <input type="text" name="publicacao" id="publicacao" class="form-control" placeholder="Ex: Revista Brasileira de Segurança Pública">
            </div>
            <div class="col-md-6">
                <label key="link_externo" for="link_externo">Link Externo</label>
                <input type="text" name="link_externo" id="link_externo" class="form-control" placeholder="Ex: https://revista.org/artigo">
            </div>
            <div class="col-12">
                <label for="palavras_chave">Palavras-chave (Separadas por ponto e vírgula)</label>
                <input type="text" name="palavras_chave" id="palavras_chave" class="form-control" placeholder="Ex: letalidade policial; direitos humanos; raça">
            </div>
            <div class="col-12">
                <label for="resumo">Resumo / Abstract</label>
                <textarea name="resumo" id="resumo" class="form-control" rows="5" placeholder="Insira o resumo da obra..."></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Produção</button>
        <a href="<?= base_url('painel/produtos-admin') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
