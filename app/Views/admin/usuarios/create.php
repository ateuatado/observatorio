<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/usuarios') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <h1 class="page-title-admin mt-2">Novo <span>Usuário</span></h1>
    <p class="text-muted mb-0">Cadastre um novo usuário com perfil de acesso específico no sistema.</p>
</div>

<form method="POST" action="<?= base_url('painel/usuarios/novo') ?>" class="form-admin">
    <?= csrf_field() ?>

    <div class="form-section">
        <h2 class="form-section-title"><span>1</span> Credenciais de Acesso</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="username">Nome de Usuário (Username) <span class="required-star">*</span></label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Ex: voluntario.maria">
            </div>
            <div class="col-md-6">
                <label for="email">E-mail <span class="required-star">*</span></label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Ex: maria@ovpdh.pucminas.br">
            </div>
            <div class="col-md-6">
                <label for="password">Senha <span class="required-star">*</span></label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Digite a senha temporária">
            </div>
            <div class="col-md-6">
                <label for="group">Grupo / Perfil de Acesso <span class="required-star">*</span></label>
                <select name="group" id="group" class="form-select" required>
                    <option value="">Selecione o perfil...</option>
                    <option value="superadmin">Super Administrador</option>
                    <option value="admin">Administrador</option>
                    <option value="voluntario" selected>Voluntário</option>
                    <option value="colaborador">Colaborador (Revisor)</option>
                    <option value="advogado">Advogado(a)</option>
                    <option value="academico">Acadêmico(a)</option>
                    <option value="ativista">Ativista de Direitos Humanos</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-ovpdh-primary">Salvar Usuário</button>
        <a href="<?= base_url('painel/usuarios') ?>" class="btn-ovpdh-dark">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
