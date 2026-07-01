<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<section style="background:linear-gradient(160deg, var(--ovpdh-preto) 0%, #2d0608 100%); padding:4rem 0 3rem; border-bottom:4px solid var(--ovpdh-vermelho);">
    <div class="container">
        <div class="section-label" style="color:var(--ovpdh-vermelho-claro);"><i class="bi bi-journal-text"></i> Conhecimento</div>
        <h1 class="section-title" style="color:var(--ovpdh-branco);">Produções Acadêmicas</h1>
        <p style="color:rgba(255,255,255,.6); max-width:600px; font-size:.95rem;">
            Artigos, livros, relatórios, dissertações e boletins produzidos pela equipe do OVPDH e pesquisadores parceiros.
        </p>
    </div>
</section>

<section style="padding:3rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <!-- Filtros -->
        <div class="row g-2 mb-4">
            <div class="col-md-auto">
                <div class="d-flex flex-wrap gap-2" id="filtros-tipo">
                    <button class="btn btn-sm filtro-tipo-btn active" data-tipo="todos"
                        style="border-radius:20px; border:1.5px solid var(--ovpdh-cinza-claro); font-size:.78rem; font-weight:600;">Todos os tipos</button>
                    <?php foreach ($tipos as $tipo): ?>
                    <button class="btn btn-sm filtro-tipo-btn" data-tipo="<?= esc($tipo) ?>"
                        style="border-radius:20px; border:1.5px solid var(--ovpdh-cinza-claro); font-size:.78rem; font-weight:600;"><?= esc($tipo) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-4" id="lista-produtos">
            <?php foreach ($produtos as $p):
                $tipoClass = match(true) {
                    str_contains($p['tipo'] ?? '', 'Artigo') => 'tipo-artigo',
                    str_contains($p['tipo'] ?? '', 'Livro') => 'tipo-livro',
                    str_contains($p['tipo'] ?? '', 'Relatório') => 'tipo-relatorio',
                    str_contains($p['tipo'] ?? '', 'Dissertação') => 'tipo-dissertacao',
                    str_contains($p['tipo'] ?? '', 'Boletim') => 'tipo-boletim',
                    default => 'tipo-default'
                };
            ?>
            <div class="col-md-6 col-lg-4 produto-item" data-tipo="<?= esc($p['tipo'] ?? '') ?>">
                <div class="card-ovpdh h-100">
                    <div class="card-header-ovpdh">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="produto-tipo-pill <?= $tipoClass ?>"><?= esc($p['tipo'] ?? '') ?></span>
                            <span style="font-size:.75rem; color:rgba(255,255,255,.4);"><?= esc($p['ano'] ?? '') ?></span>
                        </div>
                        <div class="text-white fw-bold mt-2 line-clamp-2" style="font-size:.9rem;"><?= esc($p['titulo']) ?></div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <?php if ($p['autores']): ?>
                        <div style="font-size:.75rem; color:var(--ovpdh-cinza); margin-bottom:.75rem;">
                            <i class="bi bi-person me-1"></i><?= esc($p['autores']) ?>
                        </div>
                        <?php endif; ?>
                        <div class="line-clamp-3 flex-grow-1" style="font-size:.825rem; color:var(--ovpdh-cinza-escuro); margin-bottom:1rem;"><?= esc($p['resumo'] ?? '') ?></div>
                        <?php if ($p['palavras_chave']): ?>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <?php foreach (explode(';', $p['palavras_chave']) as $kw): ?>
                            <span style="background:var(--ovpdh-cinza-ultra); color:var(--ovpdh-cinza); padding:.15rem .5rem; border-radius:4px; font-size:.68rem;"><?= esc(trim($kw)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <?php if ($p['doi']): ?>
                            <span style="font-size:.7rem; color:var(--ovpdh-cinza-medio);">DOI disponível</span>
                            <?php else: ?>
                            <span></span>
                            <?php endif; ?>
                            <a href="<?= base_url('produtos/' . $p['id']) ?>" class="btn-ovpdh-primary" style="padding:.35rem .85rem; font-size:.78rem;">
                                Ver <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function initFiltros(btnSelector, itemSelector, dataAttr) {
    document.querySelectorAll(btnSelector).forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll(btnSelector).forEach(b => { b.classList.remove('active'); b.style.background=''; b.style.color=''; b.style.borderColor=''; });
            this.classList.add('active');
            this.style.background = 'var(--ovpdh-vermelho)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--ovpdh-vermelho)';
            const val = this.dataset[dataAttr] || this.dataset.tipo;
            document.querySelectorAll(itemSelector).forEach(item => {
                item.style.display = (val === 'todos' || item.dataset[dataAttr] === val || item.dataset.tipo === val) ? '' : 'none';
            });
        });
    });
    const activeBtn = document.querySelector(btnSelector + '.active');
    if (activeBtn) { activeBtn.style.background='var(--ovpdh-vermelho)'; activeBtn.style.color='white'; activeBtn.style.borderColor='var(--ovpdh-vermelho)'; }
}
initFiltros('.filtro-tipo-btn', '.produto-item', 'tipo');
</script>
<?= $this->endSection() ?>
