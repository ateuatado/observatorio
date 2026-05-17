<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php $editando = !empty($estudo); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.3rem;margin:0;">
            <?= $editando ? 'Editar estudo' : 'Publicar novo estudo' ?>
        </h1>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            <?= $editando ? 'Atualize o conteúdo desta publicação.' : 'Crie uma nova publicação, relatório ou nota de pesquisa.' ?>
        </p>
    </div>
    <a href="<?= base_url('estudos/admin') ?>" class="btn btn-sm btn-outline-secondary">
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

<form action="<?= $editando ? base_url('estudos/' . $estudo['id'] . '/update') : base_url('estudos/salvar') ?>"
      method="post" enctype="multipart/form-data" id="formEstudo">
<?= csrf_field() ?>

<div class="row g-4">
    <!-- Coluna principal -->
    <div class="col-lg-8">
        <div class="ovp-card p-4 mb-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-journal-text me-2 text-danger"></i>Conteúdo
            </h2>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control form-control-lg" required
                           style="font-size:1rem;"
                           value="<?= old('titulo', $estudo['titulo'] ?? '') ?>"
                           placeholder="Título da publicação ou relatório...">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Autores</label>
                    <input type="text" name="autores" class="form-control"
                           value="<?= old('autores', $estudo['autores'] ?? '') ?>"
                           placeholder="Ex: Silva, J.; Oliveira, M.">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Resumo</label>
                    <textarea name="resumo" class="form-control" rows="3"
                              placeholder="Breve resumo para exibição nas listagens públicas..."><?= old('resumo', $estudo['resumo'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Conteúdo completo</label>
                    <textarea name="conteudo" class="form-control" rows="14"
                              placeholder="Escreva o conteúdo completo da publicação aqui. Suporta texto corrido."
                              style="font-size:.9rem;line-height:1.7;"><?= old('conteudo', $estudo['conteudo'] ?? '') ?></textarea>
                    <small class="text-muted">Dica: para publicações em PDF, pode deixar o conteúdo vazio e anexar o arquivo abaixo.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna lateral -->
    <div class="col-lg-4">
        <!-- Publicação -->
        <div class="ovp-card p-4 mb-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-send me-2 text-danger"></i>Publicação
            </h2>
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="publicado" value="1"
                               id="publicado"
                               <?= old('publicado', $estudo['publicado'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="publicado">
                            Publicar (visível ao público)
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">Desmarque para salvar como rascunho.</small>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="destaque" value="1"
                               id="destaque"
                               <?= old('destaque', $estudo['destaque'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="destaque">
                            Exibir em destaque na página inicial
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arquivo PDF -->
        <div class="ovp-card p-4 mb-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Arquivo PDF
            </h2>
            <?php if ($editando && $estudo['arquivo_pdf']): ?>
            <div class="mb-3 p-2 rounded" style="background:var(--ovp-cinza-claro);">
                <p class="mb-1" style="font-size:.78rem;font-weight:600;color:var(--ovp-cinza-medio);">ARQUIVO ATUAL</p>
                <a href="<?= base_url($estudo['arquivo_pdf']) ?>" target="_blank"
                   class="d-flex align-items-center gap-2 text-decoration-none" style="font-size:.82rem;">
                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                    <?= basename($estudo['arquivo_pdf']) ?>
                </a>
            </div>
            <?php endif; ?>
            <div>
                <label class="form-label fw-semibold">
                    <?= ($editando && $estudo['arquivo_pdf']) ? 'Substituir PDF' : 'Anexar PDF' ?>
                </label>
                <input type="file" name="arquivo_pdf" class="form-control" accept=".pdf">
                <small class="text-muted">Formatos aceitos: PDF. Tamanho máx.: 20MB.</small>
            </div>
        </div>

        <?php if ($editando): ?>
        <div class="ovp-card p-3 mb-4" style="border-left:3px solid var(--ovp-vermelho);">
            <p class="mb-1" style="font-size:.75rem;font-weight:600;color:var(--ovp-cinza-medio);">SLUG (URL)</p>
            <code style="font-size:.78rem;">/estudos/<?= esc($estudo['slug']) ?></code>
            <p class="text-muted mb-0 mt-1" style="font-size:.73rem;">O slug será regenerado automaticamente se o título mudar.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- BOTÕES -->
<div class="d-flex justify-content-between align-items-center mt-2 pt-3" style="border-top:1px solid var(--ovp-borda);">
    <a href="<?= base_url('estudos/admin') ?>" class="btn btn-outline-secondary">Cancelar</a>
    <div class="d-flex gap-2">
        <?php if ($editando): ?>
        <a href="<?= base_url('estudos/' . $estudo['id'] . '/deletar') ?>"
           class="btn btn-outline-danger"
           onclick="return confirm('Excluir este estudo definitivamente?');">
            <i class="bi bi-trash me-1"></i>Excluir
        </a>
        <?php endif; ?>
        <button type="submit" class="btn-ovp">
            <i class="bi bi-check-lg me-1"></i><?= $editando ? 'Salvar alterações' : 'Publicar estudo' ?>
        </button>
    </div>
</div>

</form>

<?= $this->endSection() ?>
