<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Registro de <span>Ocorrências</span></h1>
        <p class="text-muted mb-0">Gerencie todos os casos de violência policial registrados.</p>
    </div>
    <?php if (auth()->user()->can('ocorrencias.create')): ?>
    <a href="<?= base_url('painel/ocorrencias/nova') ?>" class="btn-ovpdh-primary">
        <i class="bi bi-plus-circle"></i> Cadastrar Caso
    </a>
    <?php endif; ?>
</div>

<div class="form-section p-3 mb-4">
    <form method="GET" action="" class="row g-2">
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos os status</option>
                <option value="rascunho" <?= $statusAtual === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                <option value="em_revisao" <?= $statusAtual === 'em_revisao' ? 'selected' : '' ?>>Em Revisão</option>
                <option value="aprovado" <?= $statusAtual === 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                <option value="publicado" <?= $statusAtual === 'publicado' ? 'selected' : '' ?>>Publicado</option>
            </select>
        </div>
    </form>
</div>

<div class="table-ovpdh">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título do Caso</th>
                    <th>Tipo Violência</th>
                    <th>Data</th>
                    <th>Localidade</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ocorrencias as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td>
                        <div class="fw-bold text-dark"><?= esc($o['titulo']) ?></div>
                        <div class="text-muted" style="font-size: .75rem;">Adicionado em: <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></div>
                    </td>
                    <td><span class="card-tipo-badge"><?= esc($o['tipo_violencia']) ?></span></td>
                    <td><?= $o['data_ocorrencia'] ? date('d/m/Y', strtotime($o['data_ocorrencia'])) : 'N/A' ?></td>
                    <td><?= esc($o['bairro']) ?> / <?= esc($o['cidade']) ?></td>
                    <td><span class="badge-status badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="<?= base_url('painel/ocorrencias/' . $o['id']) ?>" class="btn btn-sm btn-light" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (auth()->user()->can('ocorrencias.edit')): ?>
                            <a href="<?= base_url('painel/ocorrencias/' . $o['id'] . '/editar') ?>" class="btn btn-sm btn-light" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($ocorrencias)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Nenhuma ocorrência encontrada.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>
