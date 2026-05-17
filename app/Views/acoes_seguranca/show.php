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
$precisaoLabel = ['exata' => 'Data exata', 'aproximada' => 'Data aproximada', 'estimada' => 'Data estimada'];
$momentoLabel  = ['antes' => 'Antes', 'durante' => 'Durante', 'depois' => 'Depois'];
$tipoViolenciaLabel = [
    'execucao'      => 'Execução',
    'chacina'       => 'Chacina',
    'tortura'       => 'Tortura',
    'abuso_poder'   => 'Abuso de poder',
    'morte_custodia'=> 'Morte em custódia',
    'desaparecimento'=> 'Desaparecimento',
    'ameaca'        => 'Ameaça',
];

$tipo   = $tiposLabel[$acao['tipo_agente']] ?? ['label' => $acao['tipo_agente'], 'color' => '#6b7280'];
$st     = $statusLabel[$acao['status']]    ?? ['label' => $acao['status'], 'class' => 'secondary'];
$inicio = $acao['data_inicio'] ? date('d/m/Y', strtotime($acao['data_inicio'])) : '—';
$fim    = $acao['data_fim']    ? date('d/m/Y', strtotime($acao['data_fim']))    : 'Em curso';
?>

<!-- BREADCRUMB -->
<nav style="font-size:.8rem;margin-bottom:1.5rem;">
    <a href="<?= base_url('acoes-seguranca') ?>" class="text-muted text-decoration-none">
        <i class="bi bi-shield-exclamation me-1"></i>Ações de Segurança
    </a>
    <span class="mx-2 text-muted">/</span>
    <span><?= $acao['nome'] ? esc($acao['nome']) : 'Ação não nomeada' ?></span>
</nav>

<!-- CABEÇALHO DA AÇÃO -->
<div class="ovp-card p-4 mb-4" style="border-left:5px solid <?= $tipo['color'] ?>;">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:<?= $tipo['color'] ?>;">
                <?= $tipo['label'] ?>
            </span>
            <h1 class="h4 fw-bold mt-1 mb-2" style="font-family:var(--font-heading);">
                <?= $acao['nome'] ? esc($acao['nome']) : '<em class="fw-normal text-muted" style="font-style:italic;">Ação não nomeada</em>' ?>
            </h1>

            <!-- Status e visibilidade -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge bg-<?= $st['class'] ?>-subtle text-<?= $st['class'] ?>-emphasis">
                    <?= $st['label'] ?>
                </span>
                <?php if ($acao['visibilidade'] !== 'publica'): ?>
                <span class="badge" style="background:<?= $acao['visibilidade'] === 'sigilosa' ? '#7f1d1d' : '#1e3a5f' ?>;color:#fff;font-size:.68rem;">
                    <i class="bi bi-lock me-1"></i><?= ucfirst($acao['visibilidade']) ?>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ações -->
        <div class="d-flex gap-2 flex-wrap">
            <?php if (auth()->user()->can('acoes.gerir')): ?>
            <a href="<?= base_url('acoes-seguranca/' . $acao['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
            <form method="post" action="<?= base_url('acoes-seguranca/' . $acao['id'] . '/arquivar') ?>"
                  onsubmit="return confirm('Arquivar esta ação?');" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-secondary" type="submit">
                    <i class="bi bi-archive me-1"></i>Arquivar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Período -->
    <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-4" style="font-size:.83rem;">
        <div>
            <span class="text-muted d-block" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Início</span>
            <strong><?= $inicio ?></strong>
        </div>
        <div>
            <span class="text-muted d-block" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Fim</span>
            <strong><?= $fim ?></strong>
        </div>
        <div>
            <span class="text-muted d-block" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Precisão temporal</span>
            <strong><?= $precisaoLabel[$acao['precisao_temporal']] ?? $acao['precisao_temporal'] ?></strong>
        </div>
    </div>
</div>

<!-- NARRATIVA -->
<div class="row g-4 mb-4">
    <?php if (!empty($acao['motivacao_declarada'])): ?>
    <div class="col-md-6">
        <div class="ovp-card p-3 h-100">
            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-megaphone me-2 text-primary"></i>Motivação declarada
            </h2>
            <p style="font-size:.85rem;line-height:1.65;margin:0;"><?= nl2br(esc($acao['motivacao_declarada'])) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($acao['motivacao_inferida'])): ?>
    <div class="col-md-6">
        <div class="ovp-card p-3 h-100">
            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-lightbulb me-2 text-warning"></i>Motivação inferida
            </h2>
            <p style="font-size:.85rem;line-height:1.65;margin:0;"><?= nl2br(esc($acao['motivacao_inferida'])) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($acao['descricao'])): ?>
    <div class="col-12">
        <div class="ovp-card p-3">
            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-journal-text me-2 text-danger"></i>Histórico / Narrativa
            </h2>
            <div style="font-size:.85rem;line-height:1.75;"><?= nl2br(esc($acao['descricao'])) ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- OCORRÊNCIAS VINCULADAS -->
<div class="ovp-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 fw-bold mb-0">
            <i class="bi bi-link-45deg me-2 text-danger"></i>
            Ocorrências vinculadas
            <?php if (!empty($ocorrencias)): ?>
            <span class="badge bg-danger ms-1"><?= count($ocorrencias) ?></span>
            <?php endif; ?>
        </h2>

        <?php if (auth()->user()->can('acoes.vincular')): ?>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#formVincular">
            <i class="bi bi-plus me-1"></i>Vincular ocorrência
        </button>
        <?php endif; ?>
    </div>

    <!-- Formulário de vínculo -->
    <?php if (auth()->user()->can('acoes.vincular')): ?>
    <div class="collapse mb-3" id="formVincular">
        <div class="card card-body border-danger-subtle bg-danger-subtle" style="background:rgba(239,68,68,.05)!important;">
            <form method="post" action="<?= base_url('acoes-seguranca/' . $acao['id'] . '/vincular') ?>">
                <?= csrf_field() ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;">
                            ID da ocorrência
                        </label>
                        <input type="number" name="ocorrencia_id" class="form-control form-control-sm"
                               placeholder="Ex: 42" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;">
                            Momento
                        </label>
                        <select name="momento_vinculo" class="form-select form-select-sm">
                            <option value="">— Indefinido —</option>
                            <option value="antes">Antes da ação</option>
                            <option value="durante">Durante a ação</option>
                            <option value="depois">Depois da ação</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;">
                            Justificativa do vínculo
                        </label>
                        <input type="text" name="justificativa" class="form-control form-control-sm"
                               placeholder="Fundamentação analítica (opcional)">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de ocorrências vinculadas -->
    <?php if (!empty($ocorrencias)): ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--ovp-cinza-medio);">
                <tr>
                    <th>Protocolo</th>
                    <th>Data do fato</th>
                    <th>Tipo</th>
                    <th>Localização</th>
                    <th>Momento</th>
                    <th>Vinculado em</th>
                    <?php if (auth()->user()->can('acoes.vincular')): ?><th></th><?php endif; ?>
                </tr>
            </thead>
            <tbody style="font-size:.83rem;">
                <?php foreach ($ocorrencias as $o): ?>
                <tr>
                    <td>
                        <a href="<?= base_url('acoes-seguranca/' . $o['id']) ?>"
                           class="text-decoration-none fw-semibold text-danger"
                           style="font-size:.78rem;font-family:monospace;">
                            <?= esc($o['protocolo_ovp'] ?? '#' . $o['id']) ?>
                        </a>
                    </td>
                    <td><?= $o['data_fato'] ? date('d/m/Y', strtotime($o['data_fato'])) : '—' ?></td>
                    <td>
                        <span class="badge-tipo badge-<?= esc($o['tipo_violencia'] ?? '') ?>" style="font-size:.7rem;">
                            <?= esc($tipoViolenciaLabel[$o['tipo_violencia']] ?? ucfirst($o['tipo_violencia'] ?? '—')) ?>
                        </span>
                    </td>
                    <td>
                        <?php $loc = array_filter([$o['bairro'] ?? null, $o['municipio'] ?? null]); ?>
                        <?= esc(implode(', ', $loc) ?: '—') ?>
                    </td>
                    <td>
                        <?php if (!empty($o['momento_vinculo'])): ?>
                        <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                            <?= $momentoLabel[$o['momento_vinculo']] ?? esc($o['momento_vinculo']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--ovp-cinza-medio);font-size:.75rem;">
                        <?= $o['vinculado_em'] ? date('d/m/Y H:i', strtotime($o['vinculado_em'])) : '—' ?>
                    </td>
                    <?php if (auth()->user()->can('acoes.vincular')): ?>
                    <td>
                        <form method="post"
                              action="<?= base_url('acoes-seguranca/' . $acao['id'] . '/desvincular/' . $o['id']) ?>"
                              onsubmit="return confirm('Remover este vínculo?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1" title="Remover vínculo">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php if (!empty($o['justificativa'])): ?>
                <tr style="background:rgba(0,0,0,.02);">
                    <td colspan="<?= auth()->user()->can('acoes.vincular') ? 7 : 6 ?>"
                        style="font-size:.75rem;color:var(--ovp-cinza-medio);padding-left:1.5rem;font-style:italic;">
                        <i class="bi bi-chat-left-text me-1"></i><?= esc($o['justificativa']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php else: ?>
    <div class="text-center py-4" style="color:var(--ovp-cinza-medio);">
        <i class="bi bi-link-45deg d-block fs-2 mb-2 opacity-25"></i>
        <small>Nenhuma ocorrência vinculada ainda.</small>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
