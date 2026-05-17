<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- CABEÇALHO DO ESTUDO -->
<header style="background:linear-gradient(135deg,var(--ovp-vermelho) 0%,#3d0808 100%);color:#fff;padding:3.5rem 0 2.5rem;">
    <div class="container">
        <p style="font-size:.78rem;font-weight:600;letter-spacing:.1em;opacity:.7;text-transform:uppercase;margin-bottom:.75rem;">
            <a href="<?= base_url('estudos') ?>" style="color:rgba(255,255,255,.7);text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>Estudos & Publicações
            </a>
        </p>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php if ($estudo['destaque']): ?>
            <span class="badge" style="background:rgba(255,215,0,.2);color:#FFD700;font-size:.72rem;">
                <i class="bi bi-star-fill me-1"></i>Destaque
            </span>
            <?php endif; ?>
            <?php if ($estudo['arquivo_pdf']): ?>
            <span class="badge" style="background:rgba(255,255,255,.15);font-size:.72rem;">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF disponível
            </span>
            <?php endif; ?>
        </div>
        <h1 style="font-family:var(--font-serif);font-size:clamp(1.5rem,3vw,2rem);color:#fff;line-height:1.3;max-width:800px;margin-bottom:1rem;">
            <?= esc($estudo['titulo']) ?>
        </h1>
        <?php if ($estudo['autores']): ?>
        <p style="opacity:.8;font-size:.9rem;">
            <i class="bi bi-person me-1"></i><?= esc($estudo['autores']) ?>
        </p>
        <?php endif; ?>
        <p style="opacity:.6;font-size:.8rem;margin:0;">
            <i class="bi bi-calendar me-1"></i>
            Publicado em <?= date('d \d\e F \d\e Y', strtotime($estudo['created_at'])) ?>
            <?php if ($estudo['updated_at'] && $estudo['updated_at'] !== $estudo['created_at']): ?>
            &bull; Atualizado em <?= date('d/m/Y', strtotime($estudo['updated_at'])) ?>
            <?php endif; ?>
        </p>
    </div>
</header>

<!-- CONTEÚDO -->
<section class="ovp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Botão de PDF se disponível -->
                <?php if ($estudo['arquivo_pdf']): ?>
                <div class="mb-4 p-3 rounded-3 d-flex align-items-center gap-3" style="background:#FEF2F2;border:1px solid #FECACA;">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.75rem;flex-shrink:0;"></i>
                    <div>
                        <p class="fw-semibold mb-0" style="font-size:.9rem;">Esta publicação está disponível em PDF</p>
                        <p class="text-muted mb-0" style="font-size:.8rem;">Faça o download do documento completo</p>
                    </div>
                    <a href="<?= base_url($estudo['arquivo_pdf']) ?>" target="_blank"
                       class="btn-ovp ms-auto flex-shrink-0" style="font-size:.85rem;">
                        <i class="bi bi-download me-1"></i>Baixar PDF
                    </a>
                </div>
                <?php endif; ?>

                <!-- Resumo destacado -->
                <?php if ($estudo['resumo']): ?>
                <div class="mb-5 p-4 rounded-3" style="background:var(--ovp-cinza-claro);border-left:4px solid var(--ovp-vermelho);">
                    <p class="fw-semibold mb-1" style="font-size:.78rem;color:var(--ovp-vermelho);text-transform:uppercase;letter-spacing:.08em;">Resumo</p>
                    <p class="mb-0" style="font-size:.95rem;line-height:1.75;"><?= esc($estudo['resumo']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Conteúdo principal -->
                <?php if ($estudo['conteudo']): ?>
                <div style="font-size:1rem;line-height:1.85;color:var(--ovp-cinza);">
                    <?= nl2br(esc($estudo['conteudo'])) ?>
                </div>
                <?php elseif (!$estudo['arquivo_pdf']): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal fs-1 d-block mb-3 opacity-25"></i>
                    <p>O conteúdo completo desta publicação ainda não foi adicionado.</p>
                </div>
                <?php endif; ?>

                <!-- Rodapé do artigo -->
                <hr class="my-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="<?= base_url('estudos') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Ver todas as publicações
                    </a>
                    <?php if ($estudo['arquivo_pdf']): ?>
                    <a href="<?= base_url($estudo['arquivo_pdf']) ?>" target="_blank" class="btn-ovp btn-sm">
                        <i class="bi bi-download me-1"></i>Download PDF
                    </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
