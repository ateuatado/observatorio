<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section class="historico-header">
    <div class="container">
        <div class="section-label" style="color:var(--ovpdh-vermelho-claro);"><i class="bi bi-archive"></i> Memória e História</div>
        <h1 class="section-title" style="color:var(--ovpdh-branco);">Acervo Histórico</h1>
        <p style="color:rgba(255,255,255,.6); max-width:600px; font-size:.95rem;">
            Documentação histórica sobre violência de Estado no Brasil, com foco em Minas Gerais. Arquivos da Profa. Dra. Helena Ferreira Campos, fundadora do OVPDH.
        </p>
    </div>
</section>

<section style="padding:3rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <!-- Filtros -->
        <div class="d-flex flex-wrap gap-2 mb-4" id="filtros-historico">
            <button class="btn btn-sm filtro-btn active" data-categoria="todos"
                style="border-radius:20px; border:1.5px solid var(--ovpdh-cinza-claro); font-size:.78rem; font-weight:600;">Todos</button>
            <?php foreach ($categorias as $cat): ?>
            <button class="btn btn-sm filtro-btn" data-categoria="<?= esc($cat) ?>"
                style="border-radius:20px; border:1.5px solid var(--ovpdh-cinza-claro); font-size:.78rem; font-weight:600;"><?= esc($cat) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="d-flex flex-column gap-3" id="lista-historico">
            <?php foreach ($historicos as $h): ?>
            <div class="historico-card p-4" data-categoria="<?= esc($h['categoria'] ?? '') ?>">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="historico-periodo"><i class="bi bi-calendar3 me-1"></i><?= esc($h['periodo'] ?? '') ?></span>
                            <?php if ($h['categoria']): ?>
                            <span class="historico-categoria-badge"><?= esc($h['categoria']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h2 style="font-size:1.05rem; font-weight:700; color:var(--ovpdh-preto); margin-bottom:.5rem;"><?= esc($h['titulo']) ?></h2>
                        <p style="font-size:.875rem; color:var(--ovpdh-cinza); line-height:1.6; margin-bottom:.75rem;"><?= esc($h['descricao'] ?? '') ?></p>
                        <?php if ($h['autora']): ?>
                        <div style="font-size:.78rem; color:var(--ovpdh-cinza-medio);">
                            <i class="bi bi-person-fill me-1 text-vermelho"></i><strong>Autora:</strong> <?= esc($h['autora']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end" style="min-width:140px;">
                        <a href="<?= base_url('historico/' . $h['id']) ?>" class="btn-ovpdh-primary" style="font-size:.8rem; padding:.45rem 1rem;">
                            <i class="bi bi-eye"></i> Ver detalhes
                        </a>
                        <?php if ($h['arquivo_pdf']): ?>
                        <a href="<?= base_url('uploads/historico/' . $h['arquivo_pdf']) ?>" class="btn-ovpdh-outline" style="font-size:.8rem; padding:.4rem 1rem;" download>
                            <i class="bi bi-file-pdf"></i> Download PDF
                        </a>
                        <?php else: ?>
                        <span style="font-size:.75rem; color:var(--ovpdh-cinza-medio);"><i class="bi bi-lock me-1"></i>PDF em digitalização</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($historicos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-archive" style="font-size:3rem; color:var(--ovpdh-cinza-medio);"></i>
                <p class="mt-3 text-muted">Nenhum documento histórico cadastrado ainda.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active', 'btn-danger'));
        this.classList.add('active');
        this.style.background = 'var(--ovpdh-vermelho)';
        this.style.color = 'white';
        this.style.borderColor = 'var(--ovpdh-vermelho)';

        document.querySelectorAll('.filtro-btn:not(.active)').forEach(b => {
            b.style.background = '';
            b.style.color = '';
            b.style.borderColor = '';
        });

        const cat = this.dataset.categoria;
        document.querySelectorAll('#lista-historico .historico-card').forEach(card => {
            if (cat === 'todos' || card.dataset.categoria === cat) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
// Ativar estilo inicial
document.querySelector('.filtro-btn.active').style.background = 'var(--ovpdh-vermelho)';
document.querySelector('.filtro-btn.active').style.color = 'white';
document.querySelector('.filtro-btn.active').style.borderColor = 'var(--ovpdh-vermelho)';
</script>
<?= $this->endSection() ?>
