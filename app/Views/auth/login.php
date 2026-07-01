<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> — OVPDH<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="brand-logo-text justify-content-center d-flex mb-3">
                            <span class="brand-sigla fs-2 text-vermelho" style="font-weight: 800; font-family: 'Outfit', sans-serif;">OVPDH</span>
                        </div>
                        <h4 class="fw-bold" style="color: var(--ovpdh-preto);">Acesso ao Sistema</h4>
                        <p class="text-muted small">Área restrita para voluntários, colaboradores e administradores.</p>
                    </div>

        <?php if (session('error') !== null) : ?>
            <div class="alert-ovpdh-error mb-3" role="alert"><?= session('error') ?></div>
        <?php elseif (session('errors') !== null) : ?>
            <div class="alert-ovpdh-error mb-3" role="alert">
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <?= $error ?>
                        <br>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= session('errors') ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if (session('message') !== null) : ?>
            <div class="alert-ovpdh-success mb-3" role="alert"><?= session('message') ?></div>
        <?php endif ?>

        <form action="<?= url_to('login') ?>" method="post" class="form-ovpdh">
            <?= csrf_field() ?>

            <!-- Email -->
            <div class="mb-3">
                <label for="floatingEmailInput"><?= lang('Auth.email') ?></label>
                <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="floatingPasswordInput"><?= lang('Auth.password') ?></label>
                <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" required>
            </div>

            <!-- Remember me -->
            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php if (old('remember')): ?>checked<?php endif ?>>
                    <label class="form-check-label text-muted" for="remember" style="font-size: .8rem; font-weight: normal; text-transform: none;">
                        <?= lang('Auth.rememberMe') ?>
                    </label>
                </div>
            <?php endif; ?>

            <div class="d-grid mt-4">
                <button type="submit" class="btn-login-submit"><?= lang('Auth.login') ?></button>
            </div>

        </form>

        <div class="login-divider"></div>

        <div class="text-center">
            <a href="<?= base_url('/') ?>" class="text-muted" style="font-size: .8rem;">
                <i class="bi bi-arrow-left"></i> Voltar à Página Inicial
            </a>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
