<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <h1 class="page-title-admin">Fila de <span>Revisão</span></h1>
    <p class="text-muted mb-0">Casos que aguardam revisão metodológica e curadoria antes de serem disponibilizados publicamente.</p>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo de Violência</th>
                    <th>Local</th>
                    <th>Cadastrado em</th>
                    <th class="text-end">Revisar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendentes as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><div class="fw-bold text-dark"><?= esc($p['titulo']) ?></div></td>
                    <td><span class="card-tipo-badge"><?= esc($p['tipo_violencia']) ?></span></td>
                    <td><?= esc($p['bairro']) ?> / <?= esc($p['cidade']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="<?= base_url('painel/revisao/' . $p['id']) ?>" class="btn-ovpdh-primary btn-sm px-3">
                            <i class="bi bi-shield-check"></i> Abrir Fila
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pendentes)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-check fs-2 text-success"></i>
                        <p class="mt-2 mb-0">Nenhum caso na fila de revisão neste momento!</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
