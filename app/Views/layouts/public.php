<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Observatório de Violência Policial e Direitos Humanos — PUC São Paulo. Documentação e pesquisa sobre violência policial e direitos humanos em Minas Gerais.">
    <title><?= esc($title ?? 'OVPDH — PUC São Paulo') ?></title>

    <!-- Bootstrap CSS (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- OVPDH Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/ovpdh.css') ?>">

    <?= $this->renderSection('head') ?>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-ovpdh">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <div class="brand-logo-text">
                <span class="brand-sigla">OVPDH</span>
                <span class="brand-nome">Observatório de Violência Policial<br>e Direitos Humanos — PUC São Paulo</span>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarPublic">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= (current_url() == base_url('/')) ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                        <i class="bi bi-house-door me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/historico') ? 'active' : '' ?>" href="<?= base_url('historico') ?>">
                        <i class="bi bi-archive me-1"></i>Acervo Histórico
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/produtos') ? 'active' : '' ?>" href="<?= base_url('produtos') ?>">
                        <i class="bi bi-journal-text me-1"></i>Produções
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/sobre') ? 'active' : '' ?>" href="<?= base_url('sobre') ?>">
                        <i class="bi bi-info-circle me-1"></i>Sobre
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pucsp') ?>" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>PUC São Paulo
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <?php if (auth()->loggedIn()): ?>
                        <a class="btn-login-nav" href="<?= base_url('painel/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Painel
                        </a>
                    <?php else: ?>
                        <a class="btn-login-nav" href="<?= base_url('login') ?>">
                            <i class="bi bi-lock-fill me-1"></i>Acesso Restrito
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<?= $this->renderSection('content') ?>

<!-- FOOTER -->
<footer class="footer-ovpdh">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand-sigla">OVPDH</div>
                <div class="footer-heading mt-1">Observatório de Violência Policial<br>e Direitos Humanos — PUC São Paulo</div>
                <p class="mt-3" style="font-size:.85rem; line-height:1.6; color: rgba(255,255,255,.4);">
                    Documentamos, pesquisamos e denunciamos casos de violência policial e violações de direitos humanos em Minas Gerais, produzindo conhecimento para a transformação social.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-sm" style="background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); border-radius:6px;">
                        <i class="bi bi-envelope"></i>
                    </a>
                    <a href="#" class="btn btn-sm" style="background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); border-radius:6px;">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-sm" style="background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); border-radius:6px;">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>
            <div class="col-6 col-lg-2 offset-lg-1">
                <div class="footer-heading">Navegação</div>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="<?= base_url('/') ?>">Início</a></li>
                    <li><a href="<?= base_url('historico') ?>">Acervo Histórico</a></li>
                    <li><a href="<?= base_url('produtos') ?>">Produções Acadêmicas</a></li>
                    <li><a href="<?= base_url('sobre') ?>">Sobre o OVPDH</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Institucional</div>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="<?= base_url('pucsp') ?>" target="_blank">PUC São Paulo</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="<?= base_url('login') ?>">Área Restrita</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <div class="footer-heading">Contato</div>
                <div class="d-flex flex-column gap-2" style="font-size:.85rem; color:rgba(255,255,255,.4);">
                    <div><i class="bi bi-geo-alt me-2 text-vermelho"></i>Belo Horizonte, MG</div>
                    <div><i class="bi bi-building me-2 text-vermelho"></i>PUC São Paulo — Programa de Pós-Graduação</div>
                    <div><i class="bi bi-envelope me-2 text-vermelho"></i>ovpdh@pucsp.br</div>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" style="font-size:.78rem; color:rgba(255,255,255,.3);">
            <div>© <?= date('Y') ?> OVPDH — Observatório de Violência Policial e Direitos Humanos — PUC São Paulo. Todos os direitos reservados.</div>
            <div>Desenvolvido com <i class="bi bi-heart-fill text-vermelho"></i> para os Direitos Humanos</div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS (local) -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- OVPDH JS -->
<script src="<?= base_url('assets/js/ovpdh.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>
