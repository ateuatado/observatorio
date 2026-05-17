<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $meta_description ?? 'OVP-SP — Observatório de Violências Policiais de São Paulo. Documentação e pesquisa sobre violência policial e direitos humanos.' ?>">
    <title><?= isset($title) ? esc($title) . ' | OVP-SP' : 'OVP-SP — Observatório de Violências Policiais' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- OVP Custom CSS -->
    <link href="<?= base_url('assets/css/ovp.css') ?>" rel="stylesheet">

    <?= $this->renderSection('head_extra') ?>
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-expand-lg ovp-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <i class="bi bi-eye-fill"></i>
            OVP-SP
            <span class="badge-ano">desde 1999</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPublic">
            <i class="bi bi-list text-white fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="navPublic">
            <ul class="navbar-nav me-auto ms-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === '' ? 'active' : '' ?>" href="<?= base_url() ?>">
                        <i class="bi bi-house"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'ocorrencias') ? 'active' : '' ?>" href="<?= base_url('ocorrencias') ?>">
                        <i class="bi bi-folder2-open"></i> Ocorrências
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('estudos') ?>">
                        <i class="bi bi-journal-text"></i> Estudos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('sobre') ?>">
                        <i class="bi bi-info-circle"></i> Sobre
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if (auth()->loggedIn()): ?>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-ovp btn-sm">
                        <i class="bi bi-grid-fill me-1"></i>Painel
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Área do Pesquisador
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- ===================== CONTEÚDO ===================== -->
<?= $this->renderSection('content') ?>

<!-- ===================== FOOTER ===================== -->
<footer class="ovp-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-2">
                    <i class="bi bi-eye-fill me-2"></i>OVP-SP
                </div>
                <p class="small" style="opacity:.6; max-width:280px; line-height:1.6;">
                    Observatório de Violências Policiais — São Paulo.<br>
                    Documentação, pesquisa e memória sobre violência policial e direitos humanos.
                </p>
                <p class="small" style="opacity:.5;">
                    Vinculado ao CEHAL–PUC-SP<br>
                    Núcleo Trabalho, Ideologia e Poder
                </p>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Acervo</h6>
                <ul class="list-unstyled small">
                    <li><a href="<?= base_url('ocorrencias') ?>">Ocorrências documentadas</a></li>
                    <li><a href="<?= base_url('ocorrencias?tipo=chacina') ?>">Chacinas</a></li>
                    <li><a href="<?= base_url('ocorrencias?tipo=tortura') ?>">Torturas</a></li>
                    <li><a href="<?= base_url('ocorrencias?tipo=execucao') ?>">Execuções</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Pesquisa</h6>
                <ul class="list-unstyled small">
                    <li><a href="<?= base_url('estudos') ?>">Estudos publicados</a></li>
                    <li><a href="<?= base_url('estudos?tipo=relatorio') ?>">Relatórios</a></li>
                    <li><a href="<?= base_url('estudos?tipo=artigo') ?>">Artigos</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Sobre o OVP</h6>
                <p class="small" style="opacity:.6; line-height:1.6;">
                    O OVP-SP monitora e documenta casos de violência policial no Estado de São Paulo desde 1999, contribuindo para a produção acadêmica e para a defesa dos direitos humanos.
                </p>
                <a href="<?= base_url('sobre') ?>" class="btn-ovp-outline btn-sm" style="color:rgba(255,255,255,.7); border-color:rgba(255,255,255,.3);">
                    Saiba mais <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="ovp-footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>&copy; <?= date('Y') ?> OVP-SP — Observatório de Violências Policiais de São Paulo</span>
            <span>Sistema v1.0 &bull; CodeIgniter 4 + Shield</span>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
