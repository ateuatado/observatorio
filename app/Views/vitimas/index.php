<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.3rem;margin:0;">Vítimas cadastradas</h1>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            <?= number_format($total) ?> registro<?= $total !== 1 ? 's' : '' ?> no banco de dados
        </p>
    </div>
    <a href="<?= base_url('vitimas/novo') ?>" class="btn-ovp">
        <i class="bi bi-person-plus me-2"></i>Nova vítima
    </a>
</div>

<!-- BUSCA -->
<form method="get" action="<?= base_url('vitimas') ?>" class="mb-4">
    <div class="input-group" style="max-width:420px;">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nome ou profissão..."
               value="<?= esc($busca) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        <?php if ($busca): ?>
        <a href="<?= base_url('vitimas') ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
    </div>
</form>

<!-- TABELA -->
<?php if (!empty($vitimas)): ?>
<div class="ovp-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.84rem;">
            <thead style="background:var(--ovp-cinza-claro);font-size:.75rem;">
                <tr>
                    <th class="px-3 py-2">Nome</th>
                    <th class="px-3 py-2">Sexo</th>
                    <th class="px-3 py-2">Raça/Cor</th>
                    <th class="px-3 py-2">Idade</th>
                    <th class="px-3 py-2">Profissão</th>
                    <th class="px-3 py-2 text-center">Casos</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($vitimas as $v): ?>
                <tr>
                    <td class="px-3 py-2">
                        <?php if ($v['nome']): ?>
                            <strong><?= esc($v['nome']) ?></strong>
                        <?php else: ?>
                            <span class="text-muted fst-italic">Não identificada</span>
                        <?php endif; ?>
                        <?php if ($v['menor_de_idade']): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis ms-1" style="font-size:.65rem;">Menor</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2"><?= esc(ucfirst($v['sexo'] ?? '—')) ?></td>
                    <td class="px-3 py-2"><?= esc(ucfirst(str_replace('_', ' ', $v['raca_cor'] ?? '—'))) ?></td>
                    <td class="px-3 py-2"><?= $v['idade_aparente'] ? $v['idade_aparente'] . ' anos' : '—' ?></td>
                    <td class="px-3 py-2"><?= esc($v['profissao'] ?? '—') ?></td>
                    <td class="px-3 py-2 text-center">
                        <span class="badge <?= $v['total_casos'] > 0 ? 'bg-danger-subtle text-danger-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>">
                            <?= $v['total_casos'] ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-end">
                        <a href="<?= base_url('vitimas/' . $v['id']) ?>"
                           class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;"
                           title="Ver detalhes">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('vitimas/' . $v['id'] . '/editar') ?>"
                           class="btn btn-sm btn-outline-primary py-0 px-2 ms-1" style="font-size:.75rem;"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <?php
    $totalPaginas = (int)ceil($total / $porPagina);
    if ($totalPaginas > 1):
    ?>
    <div class="px-3 py-2 d-flex justify-content-between align-items-center" style="border-top:1px solid var(--ovp-borda);">
        <span style="font-size:.78rem;color:var(--ovp-cinza-medio);">
            Página <?= $pagina ?> de <?= $totalPaginas ?>
        </span>
        <div class="d-flex gap-1">
            <?php if ($pagina > 1): ?>
            <a href="?p=<?= $pagina - 1 ?><?= $busca ? '&q=' . urlencode($busca) : '' ?>"
               class="btn btn-sm btn-outline-secondary py-0 px-2">
                <i class="bi bi-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php if ($pagina < $totalPaginas): ?>
            <a href="?p=<?= $pagina + 1 ?><?= $busca ? '&q=' . urlencode($busca) : '' ?>"
               class="btn btn-sm btn-outline-secondary py-0 px-2">
                <i class="bi bi-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="ovp-card">
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-2" style="font-size:.9rem;">
            <?= $busca ? 'Nenhuma vítima encontrada para "<strong>' . esc($busca) . '</strong>".' : 'Nenhuma vítima cadastrada ainda.' ?>
        </p>
        <?php if (!$busca): ?>
        <p style="font-size:.82rem;" class="text-muted">
            Vítimas são cadastradas automaticamente ao registrar um caso,<br>ou podem ser cadastradas individualmente aqui.
        </p>
        <a href="<?= base_url('vitimas/novo') ?>" class="btn-ovp btn-sm mt-2" style="font-size:.82rem;">
            <i class="bi bi-person-plus me-1"></i>Cadastrar vítima
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
