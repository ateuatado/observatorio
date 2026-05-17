<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php
$tiposLabel = [
    'estatal'      => ['label' => 'Estatal',      'color' => '#3b82f6'],
    'paraestatal'  => ['label' => 'Paraestatal',  'color' => '#8b5cf6'],
    'milicia'      => ['label' => 'Milícia',      'color' => '#ef4444'],
    'comunitario'  => ['label' => 'Comunitário',  'color' => '#10b981'],
    'indefinido'   => ['label' => 'Indefinido',   'color' => '#6b7280'],
];
$statusLabel = [
    'em_analise' => ['label' => 'Em análise', 'class' => 'warning'],
    'confirmada' => ['label' => 'Confirmada', 'class' => 'success'],
    'arquivada'  => ['label' => 'Arquivada',  'class' => 'secondary'],
];
?>

<!-- CABEÇALHO -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1" style="font-family:var(--font-heading);">
            <i class="bi bi-shield-exclamation me-2 text-danger"></i>Ações de Segurança
        </h1>
        <p class="text-muted mb-0" style="font-size:.85rem;">
            Contextos operacionais que podem estar vinculados a ocorrências documentadas.
        </p>
    </div>
    <?php if (auth()->user()->can('acoes.gerir')): ?>
    <a href="<?= base_url('acoes-seguranca/novo') ?>" class="btn-ovp">
        <i class="bi bi-plus-lg me-2"></i>Nova Ação
    </a>
    <?php endif; ?>
</div>

<!-- FILTROS -->
<form method="get" class="d-flex flex-wrap gap-2 mb-4 align-items-end">
    <div>
        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Tipo de agente</label>
        <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:160px;">
            <option value="">Todos os tipos</option>
            <?php foreach ($tiposLabel as $val => $info): ?>
            <option value="<?= $val ?>" <?= ($filtros['tipo'] ?? '') === $val ? 'selected' : '' ?>><?= $info['label'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Status</label>
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:160px;">
            <option value="">Todos</option>
            <?php foreach ($statusLabel as $val => $info): ?>
            <option value="<?= $val ?>" <?= ($filtros['status'] ?? '') === $val ? 'selected' : '' ?>><?= $info['label'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if (!empty($filtros['tipo']) || !empty($filtros['status'])): ?>
    <a href="<?= base_url('acoes-seguranca') ?>" class="btn btn-sm btn-outline-secondary align-self-end">
        <i class="bi bi-x me-1"></i>Limpar
    </a>
    <?php endif; ?>
</form>

<!-- LISTA -->
<?php if (!empty($acoes)): ?>
<div class="row g-3">
    <?php foreach ($acoes as $acao): ?>
    <?php
        $tipo  = $tiposLabel[$acao['tipo_agente']] ?? ['label' => $acao['tipo_agente'], 'color' => '#6b7280'];
        $st    = $statusLabel[$acao['status']] ?? ['label' => $acao['status'], 'class' => 'secondary'];
        $nome  = $acao['nome'] ? esc($acao['nome']) : '<em class="text-muted">Ação não nomeada</em>';
        $inicio = $acao['data_inicio'] ? date('d/m/Y', strtotime($acao['data_inicio'])) : '—';
        $fim    = $acao['data_fim']    ? date('d/m/Y', strtotime($acao['data_fim']))    : 'Em curso';
    ?>
    <div class="col-lg-6">
        <div class="ovp-card p-3 h-100" style="border-left:4px solid <?= $tipo['color'] ?>;">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:<?= $tipo['color'] ?>;">
                        <?= $tipo['label'] ?>
                    </span>
                    <h2 class="h6 fw-bold mb-0 mt-1"><?= $nome ?></h2>
                </div>
                <span class="badge bg-<?= $st['class'] ?>-subtle text-<?= $st['class'] ?>-emphasis" style="white-space:nowrap;font-size:.7rem;">
                    <?= $st['label'] ?>
                </span>
            </div>

            <div class="d-flex gap-3 mb-2" style="font-size:.78rem;color:var(--ovp-cinza-medio);">
                <span><i class="bi bi-calendar-event me-1"></i><?= $inicio ?></span>
                <span><i class="bi bi-calendar-check me-1"></i><?= $fim ?></span>
                <span class="ms-auto" style="font-size:.7rem;font-style:italic;"><?= esc($acao['precisao_temporal']) ?></span>
            </div>

            <?php if (!empty($acao['descricao'])): ?>
            <p style="font-size:.8rem;color:var(--ovp-cinza-medio);line-height:1.5;margin-bottom:.75rem;">
                <?= esc(mb_substr(strip_tags($acao['descricao']), 0, 120)) ?>…
            </p>
            <?php endif; ?>

            <?php if ($acao['visibilidade'] !== 'publica'): ?>
            <span class="badge" style="font-size:.65rem;background:<?= $acao['visibilidade'] === 'sigilosa' ? '#7f1d1d' : '#1e3a5f' ?>;color:#fff;">
                <i class="bi bi-lock me-1"></i><?= ucfirst($acao['visibilidade']) ?>
            </span>
            <?php endif; ?>

            <div class="mt-2 pt-2 border-top d-flex gap-2">
                <a href="<?= base_url('acoes-seguranca/' . $acao['id']) ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye me-1"></i>Ver
                </a>
                <?php if (auth()->user()->can('acoes.gerir')): ?>
                <a href="<?= base_url('acoes-seguranca/' . $acao['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-shield-slash fs-1 d-block mb-3 opacity-25"></i>
    <p class="text-muted">Nenhuma Ação de Segurança encontrada com esses filtros.</p>
    <?php if (auth()->user()->can('acoes.gerir')): ?>
    <a href="<?= base_url('acoes-seguranca/novo') ?>" class="btn btn-sm btn-danger">
        <i class="bi bi-plus-lg me-2"></i>Cadastrar primeira ação
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
