<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Cadastro de <span>Vítimas</span></h1>
        <p class="text-muted mb-0">Gerencie a lista de vítimas associadas às ocorrências registradas.</p>
    </div>
    <?php if (auth()->user()->can('ocorrencias.create')): ?>
    <a href="<?= base_url('painel/vitimas/nova') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-person-plus"></i> Nova Vítima
    </a>
    <?php endif; ?>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Idade</th>
                    <th>Gênero</th>
                    <th>Raça/Etnia</th>
                    <th>Caso Vinculado</th>
                    <th>Desfecho</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vitimas as $v): ?>
                <tr>
                    <td class="fw-bold text-dark">
                        <?php if ($v['anonimo']): ?>
                            <span class="text-muted"><i class="bi bi-eye-slash me-1"></i>Anônimo</span>
                        <?php else: ?>
                            <?= esc($v['nome']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($v['idade'] ?? 'N/A') ?> anos</td>
                    <td><?= esc($v['genero'] ?? 'N/A') ?></td>
                    <td><?= esc($v['raca_etnia'] ?? 'N/A') ?></td>
                    <td>
                        <a href="<?= base_url('painel/ocorrencias/' . $v['ocorrencia_id']) ?>" style="font-size: .8rem;" class="fw-semibold">
                            <?= esc(character_limiter($v['ocorrencia_titulo'], 30)) ?>
                        </a>
                    </td>
                    <td><span class="badge bg-danger"><?= esc($v['desfecho'] ?? 'N/A') ?></span></td>
                    <td class="text-end">
                        <?php if (auth()->user()->can('ocorrencias.edit')): ?>
                        <a href="<?= base_url('painel/vitimas/' . $v['id'] . '/editar') ?>" class="btn btn-sm btn-light">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($vitimas)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Nenhuma vítima cadastrada.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
