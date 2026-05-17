<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.3rem;margin:0;">Gerenciar Estudos</h1>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            <?= number_format($total) ?> publicação<?= $total !== 1 ? 'ões' : '' ?> no total
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('estudos') ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
            <i class="bi bi-eye me-1"></i>Ver público
        </a>
        <a href="<?= base_url('estudos/novo') ?>" class="btn-ovp">
            <i class="bi bi-journal-plus me-2"></i>Novo estudo
        </a>
    </div>
</div>

<!-- BUSCA -->
<form method="get" action="<?= base_url('estudos/admin') ?>" class="mb-4">
    <div class="input-group" style="max-width:420px;">
        <input type="text" name="q" class="form-control" placeholder="Buscar por título ou autor..."
               value="<?= esc($busca) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        <?php if ($busca): ?>
        <a href="<?= base_url('estudos/admin') ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
    </div>
</form>

<?php if (!empty($estudos)): ?>
<div class="ovp-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.84rem;">
            <thead style="background:var(--ovp-cinza-claro);font-size:.75rem;">
                <tr>
                    <th class="px-3 py-2">Título</th>
                    <th class="px-3 py-2">Autores</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Destaque</th>
                    <th class="px-3 py-2 text-nowrap">Criado em</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($estudos as $e): ?>
                <tr>
                    <td class="px-3 py-2">
                        <strong><?= esc($e['titulo']) ?></strong>
                        <?php if ($e['arquivo_pdf']): ?>
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1" style="font-size:.65rem;">PDF</span>
                        <?php endif; ?>
                        <?php if ($e['resumo']): ?>
                        <p class="text-muted mb-0 mt-1" style="font-size:.78rem;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                            <?= esc($e['resumo']) ?>
                        </p>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2 text-muted"><?= esc($e['autores'] ?? '—') ?></td>
                    <td class="px-3 py-2 text-center">
                        <?php if ($e['publicado']): ?>
                            <span class="badge text-bg-success" style="font-size:.68rem;">Publicado</span>
                        <?php else: ?>
                            <span class="badge text-bg-warning" style="font-size:.68rem;">Rascunho</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?php if ($e['destaque']): ?>
                            <i class="bi bi-star-fill text-warning"></i>
                        <?php else: ?>
                            <i class="bi bi-star text-muted opacity-25"></i>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2 text-nowrap text-muted">
                        <?= date('d/m/Y', strtotime($e['created_at'])) ?>
                    </td>
                    <td class="px-3 py-2 text-end text-nowrap">
                        <?php if ($e['publicado']): ?>
                        <a href="<?= base_url('estudos/' . $e['slug']) ?>" target="_blank"
                           class="btn btn-sm btn-outline-secondary py-0 px-2 me-1" title="Ver publicado">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php endif; ?>
                        <a href="<?= base_url('estudos/' . $e['slug'] . '/editar') ?>"
                           class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?= base_url('estudos/' . $e['id'] . '/publicar') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm py-0 px-2 ms-1 <?= $e['publicado'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                    title="<?= $e['publicado'] ? 'Despublicar' : 'Publicar' ?>">
                                <i class="bi bi-<?= $e['publicado'] ? 'eye-slash' : 'check-circle' ?>"></i>
                            </button>
                        </form>
                        <a href="<?= base_url('estudos/' . $e['id'] . '/deletar') ?>"
                           class="btn btn-sm btn-outline-danger py-0 px-2 ms-1" title="Excluir"
                           onclick="return confirm('Excluir este estudo? Esta ação não pode ser desfeita.');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <?php $totalPaginas = (int)ceil($total / $porPagina); if ($totalPaginas > 1): ?>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" style="border-top:1px solid var(--ovp-borda);">
        <span style="font-size:.78rem;color:var(--ovp-cinza-medio);">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
        <div class="d-flex gap-1">
            <?php if ($pagina > 1): ?>
            <a href="?p=<?= $pagina-1 ?><?= $busca ? '&q='.urlencode($busca) : '' ?>" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-chevron-left"></i></a>
            <?php endif; ?>
            <?php if ($pagina < $totalPaginas): ?>
            <a href="?p=<?= $pagina+1 ?><?= $busca ? '&q='.urlencode($busca) : '' ?>" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="ovp-card">
    <div class="text-center py-5 text-muted">
        <i class="bi bi-journal fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-3" style="font-size:.9rem;">
            <?= $busca ? 'Nenhum estudo encontrado para "<strong>' . esc($busca) . '</strong>".' : 'Nenhum estudo cadastrado ainda.' ?>
        </p>
        <?php if (!$busca): ?>
        <a href="<?= base_url('estudos/novo') ?>" class="btn-ovp btn-sm" style="font-size:.82rem;">
            <i class="bi bi-journal-plus me-1"></i>Criar primeiro estudo
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
