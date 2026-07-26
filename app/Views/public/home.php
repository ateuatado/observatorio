<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section class="hero-ovpdh">
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="bi bi-shield-check"></i>
                    Observatório Acadêmico — PUC São Paulo
                </div>
                <h1 class="hero-title">
                    Documentando <span>Violência Policial</span> e Defendendo Direitos Humanos
                </h1>
                <p class="hero-subtitle">
                    O OVPDH é um observatório acadêmico da PUC São Paulo dedicado ao registro sistemático, pesquisa e denúncia de casos de violência policial e violações de direitos humanos em Minas Gerais.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= base_url('historico') ?>" class="btn-ovpdh-primary">
                        <i class="bi bi-archive"></i> Acervo Histórico
                    </a>
                    <a href="<?= base_url('produtos') ?>" class="btn-ovpdh-outline" style="border-color:rgba(255,255,255,.3); color:rgba(255,255,255,.8);">
                        <i class="bi bi-journal-text"></i> Produções Acadêmicas
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number"><?= number_format($totalOcorrencias) ?>+</div>
                        <div class="hero-stat-label">Casos Registrados</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number"><?= $totalProdutos ?>+</div>
                        <div class="hero-stat-label">Produções Acadêmicas</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number"><?= $totalHistorico ?>+</div>
                        <div class="hero-stat-label">Documentos Históricos</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">10+</div>
                        <div class="hero-stat-label">Anos de Atuação</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex hero-visual">
                <div class="hero-emblem">
                    <div class="hero-emblem-inner">
                        <div class="hero-emblem-sigla">OVPDH</div>
                        <div class="hero-emblem-ano">PUC São Paulo · Desde 2013</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISSÃO / FEATURES -->
<section class="py-6" style="padding:5rem 0; background:var(--ovpdh-branco);">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="section-label"><i class="bi bi-bullseye"></i> Nossa Missão</div>
                <h2 class="section-title">Pesquisa, Memória e Defesa dos Direitos Humanos</h2>
                <p class="section-subtitle">
                    Atuamos na interseção entre academia e ativismo, produzindo conhecimento rigoroso sobre violência policial para subsidiar políticas públicas e a defesa dos direitos humanos.
                </p>
                <div class="mt-4">
                    <a href="<?= base_url('sobre') ?>" class="btn-ovpdh-outline">
                        <i class="bi bi-arrow-right"></i> Conheça o OVPDH
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="d-flex flex-column gap-3">
                    <div class="feature-item">
                        <div class="feature-icon-box"><i class="bi bi-search"></i></div>
                        <div>
                            <div class="fw-bold mb-1">Registro e Documentação</div>
                            <div class="text-muted" style="font-size:.875rem;">Cadastro sistemático de ocorrências de violência policial com protocolo metodológico rigoroso.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-box"><i class="bi bi-book"></i></div>
                        <div>
                            <div class="fw-bold mb-1">Pesquisa Acadêmica</div>
                            <div class="text-muted" style="font-size:.875rem;">Produção de artigos, relatórios e livros a partir dos dados coletados, com rigor científico.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-box"><i class="bi bi-archive"></i></div>
                        <div>
                            <div class="fw-bold mb-1">Memória Histórica</div>
                            <div class="text-muted" style="font-size:.875rem;">Preservação e digitalização de documentos históricos sobre violência de Estado, incluindo o período da ditadura.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-box"><i class="bi bi-megaphone"></i></div>
                        <div>
                            <div class="fw-bold mb-1">Advocacia e Incidência</div>
                            <div class="text-muted" style="font-size:.875rem;">Suporte a vítimas, familiares e organizações de direitos humanos com dados e análises técnicas.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUÇÕES RECENTES -->
<?php if (!empty($recentProdutos)): ?>
<section style="padding:5rem 0; background:var(--ovpdh-cinza-ultra);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="section-label"><i class="bi bi-journal-text"></i> Produções</div>
                <h2 class="section-title mb-0">Últimas Publicações</h2>
            </div>
            <a href="<?= base_url('produtos') ?>" class="btn-ovpdh-outline d-none d-md-flex">
                Ver todas <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($recentProdutos as $p): ?>
            <div class="col-md-4">
                <div class="card-ovpdh h-100">
                    <div class="card-header-ovpdh">
                        <?php
                        $tipoClass = match(true) {
                            str_contains($p['tipo'] ?? '', 'Artigo') => 'tipo-artigo',
                            str_contains($p['tipo'] ?? '', 'Livro') => 'tipo-livro',
                            str_contains($p['tipo'] ?? '', 'Relatório') => 'tipo-relatorio',
                            str_contains($p['tipo'] ?? '', 'Dissertação') => 'tipo-dissertacao',
                            str_contains($p['tipo'] ?? '', 'Boletim') => 'tipo-boletim',
                            default => 'tipo-default'
                        };
                        ?>
                        <span class="produto-tipo-pill <?= $tipoClass ?>"><?= esc($p['tipo'] ?? 'Publicação') ?></span>
                        <div class="text-white fw-bold mt-2 line-clamp-2" style="font-size:.9rem;"><?= esc($p['titulo']) ?></div>
                    </div>
                    <div class="card-body">
                        <div class="text-muted line-clamp-3" style="font-size:.825rem; margin-bottom:1rem;"><?= esc($p['resumo'] ?? '') ?></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:.75rem; color:var(--ovpdh-cinza);"><?= esc($p['ano'] ?? '') ?></span>
                            <a href="<?= base_url('produtos/' . $p['id']) ?>" class="btn-ovpdh-primary" style="padding:.35rem .85rem; font-size:.78rem;">
                                Ver mais <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 d-md-none">
            <a href="<?= base_url('produtos') ?>" class="btn-ovpdh-outline">Ver todas as produções</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ACERVO HISTÓRICO DESTAQUE -->
<?php if (!empty($recentHistorico)): ?>
<section style="padding:5rem 0; background:var(--ovpdh-preto);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="section-label" style="color:var(--ovpdh-vermelho-claro);">
                    <i class="bi bi-archive"></i> Memória
                </div>
                <h2 class="section-title" style="color:var(--ovpdh-branco);">Acervo Histórico</h2>
                <p style="color:rgba(255,255,255,.5); font-size:.9rem; max-width:500px;">
                    Documentos históricos da Profa. Dra. Helena Ferreira Campos sobre violência de Estado no Brasil.
                </p>
            </div>
            <a href="<?= base_url('historico') ?>" class="btn-ovpdh-outline d-none d-md-flex">
                Ver acervo <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($recentHistorico as $h): ?>
            <div class="col-md-4">
                <a href="<?= base_url('historico/' . $h['id']) ?>" style="text-decoration:none;">
                    <div class="historico-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="historico-periodo"><?= esc($h['periodo'] ?? '') ?></span>
                            <?php if ($h['categoria']): ?>
                            <span class="historico-categoria-badge"><?= esc($h['categoria']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="fw-bold mb-2" style="font-size:.9rem; color:var(--ovpdh-preto);"><?= esc($h['titulo']) ?></div>
                        <div class="line-clamp-3" style="font-size:.8rem; color:var(--ovpdh-cinza);"><?= esc($h['descricao'] ?? '') ?></div>
                        <?php if ($h['autora']): ?>
                        <div class="mt-2" style="font-size:.75rem; color:var(--ovpdh-cinza-medio);">
                            <i class="bi bi-person me-1"></i><?= esc($h['autora']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA BANNER -->
<section class="cta-banner">
    <div class="container text-center">
        <div class="section-label justify-content-center" style="color:var(--ovpdh-vermelho-claro);">
            <i class="bi bi-people-fill"></i> Faça Parte
        </div>
        <h2 class="section-title" style="color:white; text-align:center;">Junte-se ao OVPDH</h2>
        <p style="color:rgba(255,255,255,.6); max-width:500px; margin:0 auto 2rem; font-size:.95rem;">
            Voluntários, acadêmicos, advogados e ativistas. O OVPDH é feito de pessoas comprometidas com os direitos humanos.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= base_url('sobre') ?>" class="btn-ovpdh-primary">
                <i class="bi bi-info-circle"></i> Saiba mais
            </a>
            <a href="mailto:ovpdh@pucsp.br" class="btn-ovpdh-outline" style="border-color:rgba(255,255,255,.3); color:rgba(255,255,255,.8);">
                <i class="bi bi-envelope"></i> Entre em contato
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
