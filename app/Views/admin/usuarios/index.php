<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Gestão de <span>Usuários</span></h1>
        <p class="text-muted mb-0">Controle os acessos e perfis do sistema do Observatório.</p>
    </div>
    <?php if (auth()->user()->can('users.create')): ?>
    <a href="<?= base_url('painel/usuarios/novo') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-person-plus"></i> Novo Usuário
    </a>
    <?php endif; ?>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Grupo / Perfil</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="sidebar-avatar" style="width:32px; height:32px; font-size:.8rem;">
                                <?= strtoupper(substr($u->username ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-bold text-dark"><?= esc($u->username) ?></div>
                                <div class="text-muted" style="font-size:.7rem;">Último Acesso: <?= $u->last_active ? date('d/m/Y H:i', strtotime($u->last_active)) : 'Nunca' ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= esc($u->email) ?></td>
                    <td>
                        <?php foreach ($u->grupos as $g): ?>
                            <span class="grupo-badge grupo-<?= $g ?>"><?= esc($g) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if ($u->active): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="<?= base_url('painel/usuarios/' . $u->id . '/editar') ?>" class="btn btn-sm btn-light" title="Editar Grupos">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= base_url('painel/usuarios/' . $u->id . '/toggle') ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-light text-danger" title="<?= $u->active ? 'Desativar' : 'Ativar' ?>">
                                    <i class="bi bi-power"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
