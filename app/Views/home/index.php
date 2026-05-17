<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- ========================================================
     HERO
     ======================================================== -->
<section class="ovp-hero">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-white text-danger mb-3" style="font-size:.72rem;font-weight:600;letter-spacing:.05em;">
                    <i class="bi bi-record-circle-fill me-1"></i>MONITORAMENTO ATIVO
                </span>
                <h1 class="mb-3">
                    Observatório de<br>Violências Policiais
                </h1>
                <p class="lead mb-4">
                    Documentação sistemática e pesquisa acadêmica sobre violência policial e direitos humanos no Estado de São Paulo, desde 1999.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= base_url('ocorrencias') ?>" class="btn btn-light text-danger fw-semibold px-4">
                        <i class="bi bi-folder2-open me-2"></i>Ver casos documentados
                    </a>
                    <a href="<?= base_url('estudos') ?>" class="btn btn-outline-light px-4">
                        <i class="bi bi-journal-text me-2"></i>Estudos publicados
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hero-stat">
                            <span class="stat-num"><?= number_format($stats['total_casos'] ?? 0) ?></span>
                            <span class="stat-label">Casos documentados</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat">
                            <span class="stat-num"><?= number_format($stats['total_vitimas'] ?? 0) ?></span>
                            <span class="stat-label">Vítimas registradas</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat">
                            <span class="stat-num"><?= $stats['anos_ativos'] ?? '25+' ?></span>
                            <span class="stat-label">Anos de pesquisa</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat">
                            <span class="stat-num"><?= number_format($stats['total_estudos'] ?? 0) ?></span>
                            <span class="stat-label">Publicações</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     ÚLTIMOS CASOS
     ======================================================== -->
<section class="ovp-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <h2 class="ovp-section-title">Casos recentes</h2>
                <span class="ovp-divider"></span>
                <p class="ovp-section-subtitle">Últimos casos documentados pelo OVP-SP</p>
            </div>
            <a href="<?= base_url('ocorrencias') ?>" class="btn-ovp-outline">
                Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <?php if (!empty($casos_recentes)): ?>
        <div class="row g-4">
            <?php foreach ($casos_recentes as $caso): ?>
            <div class="col-md-6 col-lg-4">
                <div class="ovp-card ovp-card-caso p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge-tipo badge-<?= esc($caso['tipo_violencia']) ?>">
                            <?= esc(ucfirst(str_replace('_', ' ', $caso['tipo_violencia']))) ?>
                        </span>
                        <small class="text-muted">
                            <?= date('d/m/Y', strtotime($caso['data_fato'])) ?>
                        </small>
                    </div>
                    <div class="card-title">
                        <?php
                        $label = $caso['municipio'] ?? 'Localidade não informada';
                        if (!empty($caso['bairro'])) $label = esc($caso['bairro']) . ', ' . $label;
                        echo esc($label);
                        ?>
                    </div>
                    <?php if (!empty($caso['descricao_livre'])): ?>
                    <p class="card-meta mb-2" style="font-size:.82rem;line-height:1.5;">
                        <?= esc(mb_substr(strip_tags($caso['descricao_livre']), 0, 120)) ?>…
                    </p>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="card-meta">
                            <i class="bi bi-people-fill me-1"></i>
                            <?= (int)$caso['vitimas_fatais'] ?> fatal<?= $caso['vitimas_fatais'] != 1 ? 'is' : '' ?>
                        </span>
                        <a href="<?= base_url('ocorrencias/' . $caso['id']) ?>" class="btn btn-sm btn-outline-danger" style="font-size:.78rem;padding:.25rem .65rem;">
                            Ver <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-folder2 fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-0">Nenhum caso publicado ainda.<br>
                <?php if (!auth()->loggedIn()): ?>
                    <a href="<?= base_url('login') ?>">Acesse como pesquisador</a> para cadastrar.
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================================
     SOBRE O OVP — faixa informativa
     ======================================================== -->
<section class="ovp-section ovp-section-alt">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <h2 class="ovp-section-title">O que é o OVP-SP?</h2>
                <span class="ovp-divider"></span>
                <p class="text-muted mb-3" style="line-height:1.8;">
                    O <strong>Observatório de Violências Policiais</strong> foi fundado para sistematizar e tornar públicos os dados sobre violência policial no Estado de São Paulo, contribuindo para a produção acadêmica e para a defesa dos direitos humanos.
                </p>
                <p class="text-muted mb-4" style="line-height:1.8;">
                    Em 2006, o OVP-SP foi integrado ao <strong>Centro de Estudos de História da América Latina (CEHAL)</strong> da PUC-SP — Núcleo Trabalho, Ideologia e Poder.
                </p>
                <a href="<?= base_url('sobre') ?>" class="btn-ovp">
                    <i class="bi bi-info-circle me-2"></i>Saiba mais sobre o OVP
                </a>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    <?php
                    $tipos = [
                        ['tipo' => 'execucao',    'icon' => 'bi-x-circle',        'label' => 'Execuções',         'desc' => 'Mortes por ação policial direta em confrontos e perseguições'],
                        ['tipo' => 'chacina',     'icon' => 'bi-exclamation-octagon','label' => 'Chacinas',       'desc' => 'Eventos com três ou mais vítimas fatais no mesmo episódio'],
                        ['tipo' => 'tortura',     'icon' => 'bi-shield-exclamation','label' => 'Torturas',        'desc' => 'Casos de tortura com ou sem resultado fatal'],
                        ['tipo' => 'custodia',    'icon' => 'bi-building-lock',    'label' => 'Mortes em custódia','desc' => 'Mortes em delegacias, presídios e unidades socioeducativas'],
                    ];
                    foreach ($tipos as $t):
                    ?>
                    <div class="col-6">
                        <div class="ovp-card p-3 d-flex gap-3 align-items-start">
                            <div class="flex-shrink-0" style="width:36px;height:36px;background:#FEE2E2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi <?= $t['icon'] ?> text-danger"></i>
                            </div>
                            <div>
                                <div style="font-size:.85rem;font-weight:600;color:var(--ovp-cinza-escuro);"><?= $t['label'] ?></div>
                                <div style="font-size:.78rem;color:var(--ovp-cinza-medio);line-height:1.4;"><?= $t['desc'] ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     ESTUDOS PUBLICADOS
     ======================================================== -->
<section class="ovp-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <h2 class="ovp-section-title">Estudos e publicações</h2>
                <span class="ovp-divider"></span>
                <p class="ovp-section-subtitle">Produção acadêmica do OVP-SP</p>
            </div>
            <a href="<?= base_url('estudos') ?>" class="btn-ovp-outline">
                Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <?php if (!empty($estudos_destaque)): ?>
        <div class="row g-4">
            <?php foreach ($estudos_destaque as $estudo): ?>
            <div class="col-md-6">
                <div class="ovp-card p-3 d-flex gap-3">
                    <div class="flex-shrink-0" style="width:44px;height:56px;background:#FEE2E2;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="card-type">Estudo</div>
                        <div class="card-title" style="font-size:.95rem;"><?= esc($estudo['titulo']) ?></div>
                        <div class="card-meta"><?= esc($estudo['autores'] ?? '') ?></div>
                        <a href="<?= base_url('estudos/' . $estudo['slug']) ?>" class="btn btn-sm btn-outline-danger mt-2" style="font-size:.78rem;">
                            Ler <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-0">Nenhuma publicação disponível ainda.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================================
     CTA — ÁREA DO PESQUISADOR
     ======================================================== -->
<?php if (!auth()->loggedIn()): ?>
<section style="background:linear-gradient(135deg,var(--ovp-vermelho) 0%,#4A0E0E 100%);padding:3.5rem 0;">
    <div class="container text-center text-white">
        <i class="bi bi-person-badge fs-1 d-block mb-3 opacity-75"></i>
        <h2 style="color:#fff;font-size:1.6rem;" class="mb-2">Área do Pesquisador</h2>
        <p class="mb-4" style="opacity:.85;max-width:480px;margin:0 auto 1.5rem;">
            Pesquisadores cadastrados podem registrar novos casos, adicionar documentos e contribuir com o banco de dados do OVP.
        </p>
        <a href="<?= base_url('login') ?>" class="btn btn-light text-danger fw-semibold px-4 py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Acessar sistema
        </a>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
