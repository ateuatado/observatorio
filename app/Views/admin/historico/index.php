<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Gerenciar <span>Acervo Histórico</span></h1>
        <p class="text-muted mb-0">Controle os arquivos do acervo do Observatório expostos ao público.</p>
    </div>
    <a href="<?= base_url('painel/historico/novo') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-plus-circle"></i> Novo Documento
    </a>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Período</th>
                    <th>Categoria</th>
                    <th>Autora</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historicos as $h): ?>
                <tr>
                    <td class="fw-bold text-dark"><?= esc($h['titulo']) ?></td>
                    <td><?= esc($h['periodo']) ?></td>
                    <td><span class="badge bg-secondary"><?= esc($h['categoria']) ?></span></td>
                    <td><?= esc($h['autora']) ?></td>
                    <td>
                        <?php if ($h['ativo']): ?>
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
