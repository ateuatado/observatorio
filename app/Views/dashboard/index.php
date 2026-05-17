<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<!-- ===== CABEÇALHO DA PÁGINA ===== -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.4rem;margin:0;">Bom dia<?php
            $hour = (int)date('G');
            if ($hour < 12) echo ', bom dia';
            elseif ($hour < 18) echo ', boa tarde';
            else echo ', boa noite';
        ?>, <?= esc(explode(' ', auth()->user()->username ?? 'Pesquisador')[0]) ?>!</h1>
        <p class="text-muted mb-0" style="font-size:.875rem;">
            <?= date('l, d \d\e F \d\e Y', strtotime('now')) ?>
        </p>
    </div>
    <a href="<?= base_url('ocorrencias/novo') ?>" class="btn-ovp">
        <i class="bi bi-plus-lg me-2"></i>Registrar novo caso
    </a>
</div>

<!-- ===== MÉTRICAS RÁPIDAS ===== -->
<div class="row g-3 mb-4">
    <?php
    $metrics = [
        ['label'=>'Total de casos',   'value'=>$stats['total_casos']  ?? 0, 'icon'=>'bi-folder2-open',   'color'=>'#8B1A1A','bg'=>'#FEE2E2'],
        ['label'=>'Vítimas fatais',   'value'=>$stats['total_fatais'] ?? 0, 'icon'=>'bi-people-fill',    'color'=>'#9A3412','bg'=>'#FDE8D8'],
        ['label'=>'Aguard. publicação','value'=>$stats['nao_publicados']??0,'icon'=>'bi-hourglass-split','color'=>'#854D0E','bg'=>'#FEF9C3'],
        ['label'=>'Municípios',        'value'=>$stats['municipios']   ?? 0, 'icon'=>'bi-geo-alt-fill',  'color'=>'#1E40AF','bg'=>'#DBEAFE'],
    ];
    foreach ($metrics as $m):
    ?>
    <div class="col-6 col-xl-3">
        <div class="ovp-card p-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:<?= $m['bg'] ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi <?= $m['icon'] ?>" style="color:<?= $m['color'] ?>;font-size:1.15rem;"></i>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:700;line-height:1;font-family:var(--font-body);">
                    <?= number_format($m['value']) ?>
                </div>
                <div style="font-size:.78rem;color:var(--ovp-cinza-medio);"><?= $m['label'] ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ===== CONTEÚDO PRINCIPAL 2 colunas ===== -->
<div class="row g-4">

    <!-- Últimos casos cadastrados -->
    <div class="col-lg-7">
        <div class="ovp-card">
            <div class="p-3 d-flex justify-content-between align-items-center" style="border-bottom:1px solid var(--ovp-borda);">
                <h2 style="font-size:1rem;margin:0;font-family:var(--font-body);font-weight:600;">
                    <i class="bi bi-clock-history me-2 text-danger"></i>Casos recentes
                </h2>
                <a href="<?= base_url('ocorrencias') ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">Ver todos</a>
            </div>

            <?php if (!empty($casos_recentes)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead style="background:var(--ovp-cinza-claro);font-size:.75rem;">
                        <tr>
                            <th class="px-3 py-2">Data</th>
                            <th class="px-3 py-2">Tipo</th>
                            <th class="px-3 py-2">Município</th>
                            <th class="px-3 py-2 text-center">Vítimas</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($casos_recentes as $caso): ?>
                        <tr>
                            <td class="px-3 py-2 text-nowrap">
                                <?= date('d/m/Y', strtotime($caso['data_fato'])) ?>
                            </td>
                            <td class="px-3 py-2">
                                <span class="badge-tipo badge-<?= esc($caso['tipo_violencia']) ?>">
                                    <?= esc(ucfirst($caso['tipo_violencia'])) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2"><?= esc($caso['municipio'] ?? '—') ?></td>
                            <td class="px-3 py-2 text-center"><?= (int)$caso['vitimas_fatais'] ?></td>
                            <td class="px-3 py-2 text-center">
                                <?php if ($caso['publicado']): ?>
                                    <span class="badge text-bg-success" style="font-size:.68rem;">Publicado</span>
                                <?php else: ?>
                                    <span class="badge text-bg-warning" style="font-size:.68rem;">Rascunho</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2">
                                <a href="<?= base_url('ocorrencias/' . $caso['id']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder2 fs-2 d-block mb-2 opacity-25"></i>
                <p style="font-size:.85rem;" class="mb-2">Nenhum caso cadastrado ainda.</p>
                <a href="<?= base_url('ocorrencias/novo') ?>" class="btn-ovp btn-sm" style="font-size:.82rem;">
                    <i class="bi bi-plus-lg me-1"></i>Cadastrar primeiro caso
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna lateral: ações rápidas + resumo por tipo -->
    <div class="col-lg-5 d-flex flex-column gap-4">

        <!-- Ações rápidas -->
        <div class="ovp-card p-3">
            <h2 style="font-size:1rem;margin-bottom:1rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-lightning-charge me-2 text-danger"></i>Ações rápidas
            </h2>
            <div class="d-flex flex-column gap-2">
                <a href="<?= base_url('ocorrencias/novo') ?>" class="btn btn-outline-danger text-start d-flex align-items-center gap-2" style="font-size:.85rem;border-radius:8px;">
                    <i class="bi bi-plus-circle-fill"></i> Registrar novo caso
                </a>
                <a href="<?= base_url('documentos/upload') ?>" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2" style="font-size:.85rem;border-radius:8px;">
                    <i class="bi bi-upload"></i> Fazer upload de documento
                </a>
                <a href="<?= base_url('estudos/novo') ?>" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2" style="font-size:.85rem;border-radius:8px;">
                    <i class="bi bi-journal-plus"></i> Publicar estudo
                </a>
                <a href="<?= base_url('relatorios') ?>" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2" style="font-size:.85rem;border-radius:8px;">
                    <i class="bi bi-bar-chart-line"></i> Gerar relatório
                </a>
            </div>
        </div>

        <!-- Resumo por tipo de violência -->
        <div class="ovp-card p-3">
            <h2 style="font-size:1rem;margin-bottom:1rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-pie-chart me-2 text-danger"></i>Por tipo de violência
            </h2>
            <?php
            $tipos = $stats['por_tipo'] ?? [];
            $total = array_sum(array_column($tipos, 'total'));
            $cores = [
                'execucao'       => ['bg'=>'#FEE2E2','bar'=>'#DC2626'],
                'chacina'        => ['bg'=>'#FDE8D8','bar'=>'#EA580C'],
                'tortura'        => ['bg'=>'#FEF9C3','bar'=>'#CA8A04'],
                'abuso_poder'    => ['bg'=>'#DBEAFE','bar'=>'#2563EB'],
                'morte_custodia' => ['bg'=>'#F3E8FF','bar'=>'#9333EA'],
                'desaparecimento'=> ['bg'=>'#DCFCE7','bar'=>'#16A34A'],
            ];
            if (!empty($tipos)):
                foreach ($tipos as $t):
                    $pct = $total > 0 ? round(($t['total']/$total)*100) : 0;
                    $cor = $cores[$t['tipo_violencia']] ?? ['bg'=>'#F0F0F0','bar'=>'#999'];
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                    <span style="font-weight:500;"><?= esc(ucfirst(str_replace('_', ' ', $t['tipo_violencia']))) ?></span>
                    <span style="color:var(--ovp-cinza-medio);"><?= $t['total'] ?> (<?= $pct ?>%)</span>
                </div>
                <div style="height:8px;background:<?= $cor['bg'] ?>;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:<?= $pct ?>%;background:<?= $cor['bar'] ?>;border-radius:4px;transition:width .4s;"></div>
                </div>
            </div>
            <?php
                endforeach;
            else:
            ?>
            <p class="text-muted mb-0" style="font-size:.82rem;">Cadastre casos para ver estatísticas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
