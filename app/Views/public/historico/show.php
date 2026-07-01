<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div style="background:var(--ovpdh-preto); padding:2.5rem 0; border-bottom:4px solid var(--ovpdh-vermelho);">
    <div class="container">
        <a href="<?= base_url('historico') ?>" style="color:rgba(255,255,255,.5); font-size:.875rem; text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Acervo
        </a>
        <h1 style="color:white; font-size:1.75rem; font-weight:800; margin-top:.75rem; margin-bottom:.5rem;"><?= esc($historico['titulo']) ?></h1>
        <div class="d-flex flex-wrap gap-2">
            <span class="historico-periodo"><?= esc($historico['periodo'] ?? '') ?></span>
            <?php if ($historico['categoria']): ?>
            <span class="historico-categoria-badge"><?= esc($historico['categoria']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<section style="padding:3rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-section">
                    <h2 style="font-size:1rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ovpdh-cinza); margin-bottom:1rem;">Sobre este Documento</h2>
                    <p style="font-size:.95rem; line-height:1.8; color:var(--ovpdh-cinza-escuro);"><?= nl2br(esc($historico['descricao'] ?? '')) ?></p>
                    
                    <?php if ($historico['arquivo_pdf']): ?>
                    <div class="mt-4">
                        <h3 style="font-size:.85rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--ovpdh-cinza); margin-bottom:1rem;">Visualizar Documento</h3>
                        <div style="border:2px solid var(--ovpdh-cinza-claro); border-radius:10px; overflow:hidden; background:#f5f5f5;">
                            <div style="background:var(--ovpdh-preto); padding:.75rem 1rem; display:flex; align-items:center; gap:.5rem;">
                                <i class="bi bi-file-pdf" style="color:var(--ovpdh-vermelho);"></i>
                                <span style="font-size:.8rem; color:white;"><?= esc($historico['arquivo_pdf']) ?></span>
                                <a href="<?= base_url('uploads/historico/' . $historico['arquivo_pdf']) ?>" download class="btn-ovpdh-primary ms-auto" style="padding:.3rem .8rem; font-size:.75rem;">
                                    <i class="bi bi-download"></i> Baixar
                                </a>
                            </div>
                            <div style="padding:2rem; text-align:center; color:var(--ovpdh-cinza);">
                                <i class="bi bi-file-pdf" style="font-size:3rem; color:var(--ovpdh-vermelho);"></i>
                                <p class="mt-2">Documento PDF disponível para download</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mt-4" style="background:#FEF3C7; border:1px solid #FCD34D; border-radius:10px; padding:1rem; font-size:.875rem;">
                        <i class="bi bi-clock me-2" style="color:#D97706;"></i>
                        <strong style="color:#92400E;">Documento em processo de digitalização.</strong>
                        <span style="color:#78350F;"> O PDF será disponibilizado em breve.</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-section">
                    <h2 style="font-size:.85rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--ovpdh-cinza); margin-bottom:1rem;">Informações</h2>
                    <div class="d-flex flex-column gap-3">
                        <?php if ($historico['periodo']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">Período</div>
                            <div style="font-size:.9rem; font-weight:600;"><?= esc($historico['periodo']) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($historico['categoria']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">Categoria</div>
                            <span class="historico-categoria-badge"><?= esc($historico['categoria']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($historico['autora']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">Autora</div>
                            <div style="font-size:.875rem; font-weight:600;"><?= esc($historico['autora']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('historico') ?>" class="btn-ovpdh-dark w-100" style="justify-content:center;">
                        <i class="bi bi-arrow-left"></i> Voltar ao Acervo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
