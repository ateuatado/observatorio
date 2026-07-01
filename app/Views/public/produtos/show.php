<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div style="background:var(--ovpdh-preto); padding:2.5rem 0; border-bottom:4px solid var(--ovpdh-vermelho);">
    <div class="container">
        <a href="<?= base_url('produtos') ?>" style="color:rgba(255,255,255,.5); font-size:.875rem; text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i> Voltar às Produções
        </a>
        <?php
        $tipoClass = match(true) {
            str_contains($produto['tipo'] ?? '', 'Artigo') => 'tipo-artigo',
            str_contains($produto['tipo'] ?? '', 'Livro') => 'tipo-livro',
            str_contains($produto['tipo'] ?? '', 'Relatório') => 'tipo-relatorio',
            str_contains($produto['tipo'] ?? '', 'Dissertação') => 'tipo-dissertacao',
            str_contains($produto['tipo'] ?? '', 'Boletim') => 'tipo-boletim',
            default => 'tipo-default'
        };
        ?>
        <div class="d-flex gap-2 mt-2 mb-2">
            <span class="produto-tipo-pill <?= $tipoClass ?>"><?= esc($produto['tipo'] ?? '') ?></span>
            <?php if ($produto['ano']): ?>
            <span style="color:rgba(255,255,255,.4); font-size:.85rem; padding:.25rem 0;"><?= esc($produto['ano']) ?></span>
            <?php endif; ?>
        </div>
        <h1 style="color:white; font-size:1.6rem; font-weight:800; max-width:800px;"><?= esc($produto['titulo']) ?></h1>
        <?php if ($produto['autores']): ?>
        <div style="color:rgba(255,255,255,.5); font-size:.875rem; margin-top:.5rem;">
            <i class="bi bi-person me-1"></i><?= esc($produto['autores']) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<section style="padding:3rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-section">
                    <h2 style="font-size:.85rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--ovpdh-cinza); margin-bottom:1rem;">Resumo</h2>
                    <p style="font-size:.95rem; line-height:1.85; color:var(--ovpdh-cinza-escuro);"><?= nl2br(esc($produto['resumo'] ?? 'Resumo não disponível.')) ?></p>
                    <?php if ($produto['palavras_chave']): ?>
                    <div class="mt-3">
                        <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.5rem;">Palavras-chave</div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (explode(';', $produto['palavras_chave']) as $kw): ?>
                            <span style="background:var(--ovpdh-cinza-claro); color:var(--ovpdh-cinza-escuro); padding:.25rem .7rem; border-radius:20px; font-size:.78rem; font-weight:500;"><?= esc(trim($kw)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-section">
                    <h2 style="font-size:.85rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--ovpdh-cinza); margin-bottom:1rem;">Detalhes</h2>
                    <div class="d-flex flex-column gap-3">
                        <?php if ($produto['publicacao']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">Publicado em</div>
                            <div style="font-size:.875rem; font-weight:600;"><?= esc($produto['publicacao']) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($produto['ano']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">Ano</div>
                            <div style="font-size:.875rem; font-weight:600;"><?= esc($produto['ano']) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($produto['doi']): ?>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--ovpdh-cinza); margin-bottom:.25rem;">DOI</div>
                            <a href="https://doi.org/<?= esc($produto['doi']) ?>" target="_blank" style="font-size:.8rem; word-break:break-all;"><?= esc($produto['doi']) ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-4">
                        <?php if ($produto['link_externo'] && $produto['link_externo'] !== '#'): ?>
                        <a href="<?= esc($produto['link_externo']) ?>" target="_blank" class="btn-ovpdh-primary" style="justify-content:center;">
                            <i class="bi bi-box-arrow-up-right"></i> Acessar Publicação
                        </a>
                        <?php endif; ?>
                        <a href="<?= base_url('produtos') ?>" class="btn-ovpdh-dark" style="justify-content:center;">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
