<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section style="background:linear-gradient(135deg,var(--ovp-vermelho) 0%,#3d0808 100%);color:#fff;padding:3rem 0 2.5rem;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p style="font-size:.78rem;font-weight:600;letter-spacing:.1em;opacity:.7;text-transform:uppercase;margin-bottom:.5rem;">
                    <i class="bi bi-journal-text me-2"></i>Estudos & Publicações
                </p>
                <h1 style="font-family:var(--font-serif);font-size:clamp(1.6rem,3vw,2.2rem);color:#fff;margin-bottom:.75rem;">
                    Pesquisa e análise sobre violência policial
                </h1>
                <p style="opacity:.85;max-width:560px;font-size:.95rem;">
                    Relatórios, artigos e notas de pesquisa produzidos pelo OVP-SP para contribuir com a análise crítica e a defesa dos direitos humanos.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge" style="background:rgba(255,255,255,.15);font-size:.85rem;padding:.5rem 1rem;">
                    <?= number_format($total) ?> publicação<?= $total !== 1 ? 'ões' : '' ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- CONTEÚDO -->
<section class="ovp-section">
    <div class="container">
        <!-- BUSCA -->
        <form method="get" action="<?= base_url('estudos') ?>" class="mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="input-group shadow-sm">
                        <input type="text" name="q" class="form-control form-control-lg"
                               placeholder="Buscar por título, autor ou tema..."
                               value="<?= esc($busca) ?>">
                        <button class="btn btn-dark px-4" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($busca): ?>
                        <a href="<?= base_url('estudos') ?>" class="btn btn-outline-danger">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if ($busca): ?>
                    <p class="text-muted mt-2 text-center" style="font-size:.85rem;">
                        <?= $total ?> resultado<?= $total !== 1 ? 's' : '' ?> para "<strong><?= esc($busca) ?></strong>"
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- GRID DE ESTUDOS -->
        <?php if (!empty($estudos)): ?>
        <div class="row g-4">
            <?php foreach ($estudos as $e): ?>
            <div class="col-md-6 col-lg-4">
                <article class="ovp-card h-100" style="cursor:pointer;" onclick="location.href='<?= base_url('estudos/' . $e['slug']) ?>'">
                    <!-- Cabeçalho colorido -->
                    <div style="height:6px;background:linear-gradient(90deg,var(--ovp-vermelho),#EA580C);"></div>
                    <div class="p-4 d-flex flex-column h-100" style="min-height:0;">
                        <div class="mb-2">
                            <?php if ($e['destaque']): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size:.68rem;">
                                <i class="bi bi-star-fill me-1"></i>Destaque
                            </span>
                            <?php endif; ?>
                            <?php if ($e['arquivo_pdf']): ?>
                            <span class="badge bg-primary-subtle text-primary-emphasis ms-1" style="font-size:.68rem;">
                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF disponível
                            </span>
                            <?php endif; ?>
                        </div>

                        <h2 class="card-title" style="font-size:1rem;line-height:1.4;margin-bottom:.6rem;">
                            <?= esc($e['titulo']) ?>
                        </h2>

                        <?php if ($e['resumo']): ?>
                        <p class="text-muted mb-3" style="font-size:.83rem;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex:1;">
                            <?= esc($e['resumo']) ?>
                        </p>
                        <?php else: ?>
                        <div style="flex:1;"></div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3" style="border-top:1px solid var(--ovp-borda);">
                            <span style="font-size:.75rem;color:var(--ovp-cinza-medio);">
                                <?php if ($e['autores']): ?>
                                <i class="bi bi-person me-1"></i><?= esc($e['autores']) ?>
                                <?php else: ?>
                                <i class="bi bi-calendar me-1"></i><?= date('M/Y', strtotime($e['created_at'])) ?>
                                <?php endif; ?>
                            </span>
                            <a href="<?= base_url('estudos/' . $e['slug']) ?>"
                               class="btn btn-sm btn-outline-danger" style="font-size:.75rem;"
                               onclick="event.stopPropagation()">
                                Ler <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- PAGINAÇÃO -->
        <?php $totalPaginas = (int)ceil($total / $porPagina); if ($totalPaginas > 1): ?>
        <div class="d-flex justify-content-center mt-5 gap-2">
            <?php if ($pagina > 1): ?>
            <a href="?p=<?= $pagina-1 ?><?= $busca ? '&q='.urlencode($busca) : '' ?>"
               class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left me-1"></i>Anterior
            </a>
            <?php endif; ?>
            <span class="btn btn-light border disabled" style="font-size:.85rem;">
                <?= $pagina ?> / <?= $totalPaginas ?>
            </span>
            <?php if ($pagina < $totalPaginas): ?>
            <a href="?p=<?= $pagina+1 ?><?= $busca ? '&q='.urlencode($busca) : '' ?>"
               class="btn btn-outline-secondary">
                Próxima <i class="bi bi-chevron-right ms-1"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-journal fs-1 text-muted d-block mb-3 opacity-25"></i>
            <p class="text-muted" style="font-size:.95rem;">
                <?= $busca
                    ? 'Nenhuma publicação encontrada para "<strong>' . esc($busca) . '</strong>".'
                    : 'Nenhuma publicação disponível no momento.' ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
