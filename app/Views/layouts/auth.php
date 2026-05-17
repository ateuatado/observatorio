<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' | OVP-SP' : 'Painel | OVP-SP' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- OVP CSS -->
    <link href="<?= base_url('assets/css/ovp.css') ?>" rel="stylesheet">

    <?= $this->renderSection('head_extra') ?>
</head>
<body style="margin:0; padding:0;">

<div class="d-flex" style="min-height:100vh;">

    <!-- ============= SIDEBAR ============= -->
    <aside class="ovp-sidebar d-flex flex-column">
        <div class="sidebar-brand">
            <i class="bi bi-eye-fill text-white me-2"></i>
            <span>OVP-SP</span>
        </div>

        <nav class="flex-grow-1">
            <div class="ovp-section-label">Principal</div>
            <a href="<?= base_url('dashboard') ?>"
               class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="ovp-section-label" style="padding-top:1.5rem;">Cadastro</div>
            <?php if (auth()->user()->can('ocorrencias.criar')): ?>
            <a href="<?= base_url('ocorrencias/novo') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'ocorrencias/novo') ? 'active' : '' ?>">
                <i class="bi bi-plus-circle"></i> Nova Ocorrência
            </a>
            <?php endif; ?>
            <a href="<?= base_url('ocorrencias') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'ocorrencias') && !str_starts_with(uri_string(), 'ocorrencias/novo') ? 'active' : '' ?>">
                <i class="bi bi-folder2-open"></i> Ocorrências
            </a>
            <a href="<?= base_url('vitimas') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'vitimas') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Vítimas
            </a>
            <a href="<?= base_url('documentos') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'documentos') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Documentos
            </a>

            <?php if (auth()->user()->can('acoes.gerir') || auth()->user()->can('acoes.vincular')): ?>
            <div class="ovp-section-label" style="padding-top:1.5rem;">Curadoria</div>
            <a href="<?= base_url('acoes-seguranca') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'acoes-seguranca') ? 'active' : '' ?>">
                <i class="bi bi-shield-exclamation"></i> Ações de Segurança
            </a>
            <?php endif; ?>

            <div class="ovp-section-label" style="padding-top:1.5rem;">Arquivo Histórico</div>
            <a href="<?= base_url('auditoria-historica') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'auditoria-historica') ? 'active' : '' ?>"
               style="display:flex;justify-content:space-between;align-items:center;">
                <span><i class="bi bi-folder2-open"></i> Auditoria Histórica</span>
                <?php
                // Badge de pendentes (leve — só consulta se não estiver em cache)
                try {
                    $pendentes = (new \App\Models\AcervoDocumentoModel())
                        ->where('status', 'pendente')->countAllResults();
                    if ($pendentes > 0):
                ?>
                    <span style="background:#f59e0b;color:#000;font-size:.65rem;font-weight:700;
                                 padding:.1rem .4rem;border-radius:10px;min-width:18px;text-align:center;">
                        <?= $pendentes > 999 ? '999+' : $pendentes ?>
                    </span>
                <?php endif; } catch (\Exception $e) { /* tabela ainda não existe */ } ?>
            </a>

            <div class="ovp-section-label" style="padding-top:1.5rem;">Publicações</div>
            <a href="<?= base_url('estudos/admin') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'estudos') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Estudos
            </a>

            <div class="ovp-section-label" style="padding-top:1.5rem;">Análise</div>
            <a href="<?= base_url('relatorios') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'relatorios') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Relatórios
            </a>
            <a href="<?= base_url('mapa') ?>"
               class="nav-link <?= str_starts_with(uri_string(), 'mapa') ? 'active' : '' ?>">
                <i class="bi bi-map"></i> Mapa
            </a>
        </nav>

        <!-- perfil do usuário no rodapé da sidebar -->
        <div style="padding:1rem 1.25rem; border-top:1px solid rgba(255,255,255,.1);">
            <div class="d-flex align-items-center gap-2">
                <div style="width:34px;height:34px;background:var(--ovp-vermelho);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-person-fill text-white" style="font-size:.85rem;"></i>
                </div>
                <div style="overflow:hidden;">
                    <div style="font-size:.8rem;color:#fff;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?= esc(auth()->user()->username ?? auth()->user()->email) ?>
                    </div>
                    <a href="<?= base_url('logout') ?>"
                       style="font-size:.72rem;color:rgba(255,255,255,.5);text-decoration:none;"
                       onclick="return confirm('Deseja sair do sistema?');">
                        <i class="bi bi-box-arrow-left me-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============= CONTEÚDO PRINCIPAL ============= -->
    <div class="ovp-main-content d-flex flex-column">

        <!-- TOPBAR -->
        <div class="ovp-topbar">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:.78rem;">
                    <i class="bi bi-house me-1"></i>
                    <a href="<?= base_url() ?>" target="_blank" class="text-muted text-decoration-none">Ver site público</a>
                </span>
                <span class="text-muted" style="font-size:.78rem;">
                    &bull;
                    <span class="ms-1"><?= isset($breadcrumb) ? esc($breadcrumb) : 'Dashboard' ?></span>
                </span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-danger-subtle text-danger-emphasis" style="font-size:.72rem;">
                    <?php
                    $grupos = auth()->user()->getGroups();
                    $grupoTitulo = [
                        'admin'            => 'Administrador',
                        'curador'          => 'Curador',
                        'curador_juridico' => 'Curador Jurídico',
                        'pesquisador'      => 'Pesquisador',
                        'colaborador'      => 'Colaborador',
                    ];
                    $g = $grupos[0] ?? 'colaborador';
                    echo esc($grupoTitulo[$g] ?? ucfirst($g));
                    ?>
                </span>
            </div>
        </div>

        <!-- ÁREA DE CONTEÚDO -->
        <main class="flex-grow-1 p-4">
            <?php if (session()->has('message')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= session('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>

        <footer style="padding:.75rem 1.5rem;border-top:1px solid var(--ovp-borda);font-size:.75rem;color:var(--ovp-cinza-medio);">
            OVP-SP &copy; <?= date('Y') ?> &bull; Sistema v1.0 &bull; CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
        </footer>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
