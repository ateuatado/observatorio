<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- CABEÇALHO -->
<section style="background:var(--ovp-cinza-claro);padding:2.5rem 0 1.5rem;border-bottom:1px solid var(--ovp-borda);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="ovp-section-title mb-1">Casos documentados</h1>
                <p class="text-muted mb-0" style="font-size:.875rem;">
                    <?= number_format($total) ?> caso<?= $total != 1 ? 's' : '' ?> encontrado<?= $total != 1 ? 's' : '' ?>
                    <?php if (!empty($filtros['tipo'])): ?>
                        · filtrado por <strong><?= esc(ucfirst($filtros['tipo'])) ?></strong>
                    <?php endif; ?>
                </p>
            </div>
            <?php if (auth()->loggedIn()): ?>
            <a href="<?= base_url('ocorrencias/novo') ?>" class="btn-ovp">
                <i class="bi bi-plus-lg me-2"></i>Registrar caso
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="ovp-section" style="padding-top:2rem;">
    <div class="container">
        <div class="row g-4">

            <!-- ===== FILTROS (sidebar) ===== -->
            <div class="col-lg-3">
                <div class="ovp-card p-3 sticky-top" style="top:80px;">
                    <h2 style="font-size:.9rem;font-weight:700;margin-bottom:1rem;font-family:var(--font-body);">
                        <i class="bi bi-funnel me-2 text-danger"></i>Filtrar
                    </h2>

                    <form method="get" action="<?= base_url('ocorrencias') ?>">
                        <!-- Busca -->
                        <div class="mb-3">
                            <label class="form-label" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Busca livre</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="q" class="form-control" placeholder="Município, bairro..." value="<?= esc($filtros['busca'] ?? '') ?>">
                                <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </div>

                        <!-- Tipo -->
                        <div class="mb-3">
                            <label class="form-label" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Tipo de violência</label>
                            <?php
                            $tipos = ['execucao'=>'Execução','chacina'=>'Chacina','tortura'=>'Tortura','abuso_poder'=>'Abuso de poder','morte_custodia'=>'Morte em custódia','desaparecimento'=>'Desaparecimento'];
                            foreach ($tipos as $val => $label):
                            ?>
                            <div class="form-check" style="margin-bottom:.2rem;">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo_<?= $val ?>" value="<?= $val ?>"
                                    <?= ($filtros['tipo'] ?? '') === $val ? 'checked' : '' ?>
                                    onchange="this.form.submit()">
                                <label class="form-check-label" for="tipo_<?= $val ?>" style="font-size:.82rem;"><?= $label ?></label>
                            </div>
                            <?php endforeach; ?>
                            <?php if (!empty($filtros['tipo'])): ?>
                            <a href="<?= base_url('ocorrencias') ?>" class="btn btn-sm btn-link text-muted p-0" style="font-size:.75rem;">
                                <i class="bi bi-x me-1"></i>Limpar filtro
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Município -->
                        <?php if (!empty($municipios)): ?>
                        <div class="mb-3">
                            <label for="filtro_municipio" class="form-label" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Município</label>
                            <select name="municipio" id="filtro_municipio" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <?php foreach ($municipios as $m): ?>
                                <option value="<?= esc($m['municipio']) ?>" <?= ($filtros['municipio'] ?? '') === $m['municipio'] ? 'selected' : '' ?>>
                                    <?= esc($m['municipio']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Ano -->
                        <?php if (!empty($anos)): ?>
                        <div class="mb-3">
                            <label for="filtro_ano" class="form-label" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Ano</label>
                            <select name="ano" id="filtro_ano" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <?php foreach ($anos as $a): ?>
                                <option value="<?= $a['ano'] ?>" <?= ($filtros['ano'] ?? '') == $a['ano'] ? 'selected' : '' ?>>
                                    <?= $a['ano'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- ===== LISTA DE CASOS ===== -->
            <div class="col-lg-9">
                <?php if (!empty($casos)): ?>

                <div class="row g-3 mb-4">
                    <?php foreach ($casos as $caso): ?>
                    <div class="col-md-6 col-xl-4">
                        <a href="<?= base_url('ocorrencias/' . $caso['id']) ?>" class="text-decoration-none">
                            <div class="ovp-card ovp-card-caso p-3 h-100" style="cursor:pointer;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge-tipo badge-<?= esc($caso['tipo_violencia']) ?>">
                                        <?= esc(ucfirst(str_replace('_',' ',$caso['tipo_violencia']))) ?>
                                    </span>
                                    <span style="font-size:.72rem;color:var(--ovp-cinza-medio);">
                                        <?= date('d/m/Y', strtotime($caso['data_fato'])) ?>
                                    </span>
                                </div>

                                <div class="card-title mb-1">
                                    <?php
                                    $loc = [];
                                    if (!empty($caso['bairro']))    $loc[] = esc($caso['bairro']);
                                    if (!empty($caso['municipio'])) $loc[] = esc($caso['municipio']);
                                    echo implode(', ', $loc) ?: 'Localidade não informada';
                                    ?>
                                </div>

                                <?php if (!empty($caso['descricao_livre'])): ?>
                                <p style="font-size:.8rem;color:var(--ovp-cinza-medio);line-height:1.5;margin-bottom:.75rem;">
                                    <?= esc(mb_substr(strip_tags($caso['descricao_livre']), 0, 100)) ?>…
                                </p>
                                <?php endif; ?>

                                <div class="d-flex gap-3 mt-auto" style="font-size:.75rem;color:var(--ovp-cinza-medio);">
                                    <span><i class="bi bi-people-fill me-1 text-danger"></i><?= (int)$caso['vitimas_fatais'] ?> fatal<?= $caso['vitimas_fatais'] != 1 ? 'is' : '' ?></span>
                                    <?php if ($caso['vitimas_nao_fatais'] > 0): ?>
                                    <span><i class="bi bi-person-exclamation me-1"></i><?= (int)$caso['vitimas_nao_fatais'] ?> ferido<?= $caso['vitimas_nao_fatais'] != 1 ? 's' : '' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($total > $porPagina):
                    $totalPaginas = ceil($total / $porPagina);
                    $queryBase = http_build_query(array_filter(array_merge($filtros, ['p' => ''])));
                ?>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                        <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= $queryBase ?>p=<?= $p ?>"><?= $p ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-folder2 fs-1 d-block mb-3 opacity-25"></i>
                    <p class="text-muted">Nenhum caso encontrado com esses filtros.</p>
                    <a href="<?= base_url('ocorrencias') ?>" class="btn btn-sm btn-outline-secondary">Limpar filtros</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
