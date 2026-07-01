<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Cadastro de <span>Agressores</span></h1>
        <p class="text-muted mb-0">Gerencie os agentes públicos vinculados às ocorrências de abuso.</p>
    </div>
    <?php if (auth()->user()->can('ocorrencias.create')): ?>
    <a href="<?= base_url('painel/agressores/novo') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-shield-plus"></i> Novo Agressor
    </a>
    <?php endif; ?>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Agente</th>
                    <th>Órgão / Corporação</th>
                    <th>Batalhão</th>
                    <th>Posto / Patente</th>
                    <th>Identificado?</th>
                    <th>Ocorrência Relacionada</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agressores as $a): ?>
                <tr>
                    <td class="fw-bold text-dark"><?= esc($a['tipo_agente'] ?? 'N/A') ?></td>
                    <td><?= esc($a['orgao'] ?? 'N/A') ?></td>
                    <td><?= esc($a['batalhao'] ?? 'N/A') ?></td>
                    <td><?= esc($a['posto'] ?? 'N/A') ?></td>
                    <td>
                        <?php if ($a['identificado']): ?>
                            <span class="badge bg-success">Sim (<?= esc($a['identificacao']) ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Não</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('painel/ocorrencias/' . $a['ocorrencia_id']) ?>" style="font-size: .8rem;" class="fw-semibold">
                            <?= esc(character_limiter($a['ocorrencia_titulo'], 30)) ?>
                        </a>
                    </td>
                    <td class="text-end">
                        <?php if (auth()->user()->can('ocorrencias.edit')): ?>
                        <a href="<?= base_url('painel/agressores/' . $a['id'] . '/editar') ?>" class="btn btn-sm btn-light">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($agressores)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Nenhum agressor cadastrado.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
