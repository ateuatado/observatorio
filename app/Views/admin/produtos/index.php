<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Gerenciar <span>Produções Acadêmicas</span></h1>
        <p class="text-muted mb-0">Controle os artigos, livros e produções intelectuais do Observatório.</p>
    </div>
    <a href="<?= base_url('painel/produtos-admin/novo') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-plus-circle"></i> Nova Produção
    </a>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autores</th>
                    <th>Tipo</th>
                    <th>Ano</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr>
                    <td class="fw-bold text-dark"><?= esc($p['titulo']) ?></td>
                    <td><?= esc($p['autores']) ?></td>
                    <td><?= esc($p['tipo']) ?></td>
                    <td><?= esc($p['ano']) ?></td>
                    <td>
                        <?php if ($p['ativo']): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
