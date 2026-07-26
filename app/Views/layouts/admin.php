<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Painel — OVPDH') ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap CSS (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (local) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- OVPDH Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/ovpdh.css') ?>">

    <?= $this->renderSection('head') ?>
</head>
<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">O</div>
            <div class="sidebar-logo-text">
                <div class="sigla">OVPDH</div>
                <div class="nome">Observatório de Violência<br>Policial e DH — PUC São Paulo</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">

            <div class="sidebar-section-label">Principal</div>
            <a href="<?= base_url('painel/dashboard') ?>" class="sidebar-link <?= str_contains(current_url(), 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-section-label mt-2">Ocorrências</div>
            <a href="<?= base_url('painel/ocorrencias') ?>" class="sidebar-link <?= str_contains(current_url(), '/ocorrencias') ? 'active' : '' ?>">
                <i class="bi bi-exclamation-triangle"></i> Todas as Ocorrências
            </a>
            <?php if (auth()->user() && auth()->user()->can('ocorrencias.create')): ?>
            <a href="<?= base_url('painel/ocorrencias/nova') ?>" class="sidebar-link <?= str_contains(current_url(), 'ocorrencias/nova') ? 'active' : '' ?>">
                <i class="bi bi-plus-circle"></i> Nova Ocorrência
            </a>
            <?php endif; ?>
            <?php if (auth()->user() && auth()->user()->can('ocorrencias.review')): ?>
            <a href="<?= base_url('painel/revisao') ?>" class="sidebar-link <?= str_contains(current_url(), 'revisao') ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check"></i> Fila de Revisão
                <?php
                $pendentesCount = db_connect()->table('ocorrencias')->where('status','em_revisao')->where('deleted_at IS NULL')->countAllResults();
                if ($pendentesCount > 0): ?>
                    <span class="badge-nav"><?= $pendentesCount ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <div class="sidebar-section-label mt-2">Cadastros</div>
            <a href="<?= base_url('painel/vitimas') ?>" class="sidebar-link <?= str_contains(current_url(), '/vitimas') ? 'active' : '' ?>">
                <i class="bi bi-person-heart"></i> Vítimas
            </a>
            <a href="<?= base_url('painel/agressores') ?>" class="sidebar-link <?= str_contains(current_url(), '/agressores') ? 'active' : '' ?>">
                <i class="bi bi-shield-exclamation"></i> Agressores
            </a>

            <?php if (auth()->user() && auth()->user()->can('relatorios.view')): ?>
            <div class="sidebar-section-label mt-2">Análise</div>
            <a href="<?= base_url('painel/relatorios') ?>" class="sidebar-link <?= str_contains(current_url(), 'relatorios') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Relatórios
            </a>
            <?php endif; ?>

            <?php if (auth()->user() && (auth()->user()->can('historico.manage') || auth()->user()->can('produtos.manage'))): ?>
            <div class="sidebar-section-label mt-2">Conteúdo</div>
            <?php if (auth()->user()->can('historico.manage')): ?>
            <a href="<?= base_url('painel/historico') ?>" class="sidebar-link <?= str_contains(current_url(), 'painel/historico') ? 'active' : '' ?>">
                <i class="bi bi-archive"></i> Acervo Histórico
            </a>
            <?php endif; ?>
            <?php if (auth()->user()->can('produtos.manage')): ?>
            <a href="<?= base_url('painel/produtos-admin') ?>" class="sidebar-link <?= str_contains(current_url(), 'produtos-admin') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Produções
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (auth()->user() && auth()->user()->can('users.manage')): ?>
            <div class="sidebar-section-label mt-2">Administração</div>
            <a href="<?= base_url('painel/usuarios') ?>" class="sidebar-link <?= str_contains(current_url(), 'usuarios') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Usuários
            </a>
            <?php endif; ?>

            <div class="sidebar-section-label mt-2">Site Público</div>
            <a href="<?= base_url('/') ?>" target="_blank" class="sidebar-link">
                <i class="bi bi-box-arrow-up-right"></i> Ver Site
            </a>

        </nav>

        <!-- User Info -->
        <div class="sidebar-user">
            <?php $u = auth()->user(); ?>
            <div class="sidebar-user-info">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr($u?->username ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <div class="sidebar-user-name"><?= esc($u?->username ?? '') ?></div>
                    <div class="sidebar-user-role"><?= esc(implode(', ', $u?->getGroups() ?? [])) ?></div>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="sidebar-link mt-1" style="color:rgba(220,38,38,.7);">
                <i class="bi bi-box-arrow-left"></i> Sair
            </a>
        </div>
    </aside>

    <!-- CONTENT -->
    <div class="admin-content">

        <!-- TOPBAR -->
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm border-0 d-lg-none" id="sidebarToggle" style="background:rgba(0,0,0,.05);">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="topbar-breadcrumb">
                    <a href="<?= base_url('painel/dashboard') ?>" style="color:inherit;">Painel</a>
                    <span class="mx-2 text-muted">/</span>
                    <span class="current"><?= esc($pageTitle ?? ($title ?? 'Dashboard')) ?></span>
                </div>
            </div>
            <div class="topbar-actions">
                <span class="badge rounded-pill" style="background:rgba(192,39,45,.1); color:var(--ovpdh-vermelho); font-size:.7rem; padding:.3rem .7rem;">
                    <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>
                    Sistema Online
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown"
                            style="background:rgba(0,0,0,.04); border-radius:8px; padding:.4rem .8rem;">
                        <div class="sidebar-avatar" style="width:28px;height:28px;font-size:.75rem;">
                            <?= strtoupper(substr($u?->username ?? 'U', 0, 1)) ?>
                        </div>
                        <span style="font-size:.8rem; font-weight:600; color:var(--ovpdh-preto);"><?= esc($u?->username ?? '') ?></span>
                        <i class="bi bi-chevron-down" style="font-size:.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius:10px; font-size:.85rem; min-width:180px;">
                        <li><span class="dropdown-item-text text-muted" style="font-size:.75rem;"><?= esc(implode(', ', $u?->getGroups() ?? [])) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('/') ?>" target="_blank"><i class="bi bi-house me-2"></i>Ver site</a></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- MAIN -->
        <main class="admin-main">

            <!-- Flash Messages -->
            <?php if (session()->has('success')): ?>
            <div class="alert-ovpdh-success mb-3">
                <i class="bi bi-check-circle-fill"></i>
                <?= esc(session('success')) ?>
            </div>
            <?php endif; ?>
            <?php if (session()->has('error')): ?>
            <div class="alert-ovpdh-error mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= esc(session('error')) ?>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<!-- Bootstrap JS (local) -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- Chart.js (local) -->
<script src="<?= base_url('assets/js/chart.umd.min.js') ?>"></script>
<!-- OVPDH JS -->
<script src="<?= base_url('assets/js/ovpdh.js') ?>"></script>
<script>
// Sidebar toggle mobile
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('adminSidebar').classList.toggle('show');
});
</script>
<?= $this->renderSection('scripts') ?>

</body>
</html>
