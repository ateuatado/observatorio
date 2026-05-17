<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | OVP-SP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/ovp.css') ?>" rel="stylesheet">
</head>
<body>

<div class="ovp-login-wrap">
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'80\' height=\'80\' viewBox=\'0 0 80 80\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');pointer-events:none;"></div>

    <div class="ovp-login-card position-relative">

        <!-- Logo -->
        <div class="ovp-login-logo">
            <div class="logo-sigla">OVP</div>
            <h1>Observatório de Violências Policiais</h1>
            <p>Área restrita a pesquisadores cadastrados</p>
        </div>

        <!-- Mensagens de erro -->
        <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-ovp d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <span><?= session('error') ?></span>
        </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-ovp mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Verifique os campos:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (session()->has('message')): ?>
        <div class="alert alert-success alert-ovp d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span><?= session('message') ?></span>
        </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold" style="font-size:.85rem;">
                    <i class="bi bi-envelope me-1"></i>E-mail
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="seu@email.com"
                    value="<?= old('email') ?>"
                    required
                    autocomplete="email"
                    style="border-radius:8px;padding:.65rem .85rem;"
                >
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold mb-0" style="font-size:.85rem;">
                        <i class="bi bi-lock me-1"></i>Senha
                    </label>
                    <a href="<?= base_url('forgot-password') ?>" style="font-size:.78rem;">Esqueci minha senha</a>
                </div>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                        style="border-radius:8px 0 0 8px;padding:.65rem .85rem;"
                    >
                    <button class="btn btn-outline-secondary" type="button" id="togglePass"
                        onclick="var i=document.getElementById('password');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('bi-eye');this.querySelector('i').classList.toggle('bi-eye-slash');"
                        style="border-radius:0 8px 8px 0;">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember" style="font-size:.85rem;">
                        Manter conectado
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-ovp w-100 py-2" style="border-radius:8px;font-size:.95rem;">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar no sistema
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url() ?>" style="font-size:.82rem;color:var(--ovp-cinza-medio);text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao site público
            </a>
        </div>

        <div class="text-center mt-4 pt-3" style="border-top:1px solid var(--ovp-borda);">
            <p style="font-size:.75rem;color:var(--ovp-cinza-medio);margin:0;">
                Acesso restrito a pesquisadores credenciados.<br>
                Para solicitar cadastro, entre em contato com a coordenação do OVP.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
