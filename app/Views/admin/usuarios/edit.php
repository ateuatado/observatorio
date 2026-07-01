<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/usuarios') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Editar Usuário: <span><?= esc($usuario->username) ?></span></h1>
    <p class="text-muted mb-0">Atualize o grupo ou status de acesso do usuário.</p>
</div>

<form method="POST" action="<?= base_url('painel/usuarios/' . $usuario->id . '/editar') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Perfil de Acesso</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="username">Nome de Usuário</label>
                <input type="text" class="form-control" disabled value="<?= esc($usuario->username) ?>">
            </div>
            <div class="col-md-6">
                <label for="email">E-mail</label>
                <input type="text" class="form-control" disabled value="<?= esc($usuario->email) ?>">
            </div>
            <div class="col-md-6">
                <label for="group">Grupo / Perfil <span class="required-star">*</span></label>
                <select name="group" id="group" class="form-select" required>
                    <option value="superadmin" <?= in_array('superadmin', $usuario->grupos) ? 'selected' : '' ?>>Super Administrador</option>
                    <option value="admin" <?= in_array('admin', $usuario->grupos) ? 'selected' : '' ?>>Administrador</option>
                    <option value="voluntario" <?= in_array('voluntario', $usuario->grupos) ? 'selected' : '' ?>>Voluntário</option>
                    <option value="colaborador" <?= in_array('colaborador', $usuario->grupos) ? 'selected' : '' ?>>Colaborador (Revisor)</option>
                    <option value="advogado" <?= in_array('advogado', $usuario->grupos) ? 'selected' : '' ?>>Advogado(a)</option>
                    <option value="academico" <?= in_array('academico', $usuario->grupos) ? 'selected' : '' ?>>Acadêmico(a)</option>
                    <option value="ativista" <?= in_array('ativista', $usuario->grupos) ? 'selected' : '' ?>>Ativista de Direitos Humanos</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Alterações</button>
        <a href="<?= base_url('painel/usuarios') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
