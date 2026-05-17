<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php
$sexoLabels = ['masculino'=>'Masculino','feminino'=>'Feminino','nao_binario'=>'Não-binário','nao_informado'=>'N/I'];
$racaLabels  = ['branca'=>'Branca','preta'=>'Preta','parda'=>'Parda','amarela'=>'Amarela','indigena'=>'Indígena','nao_informada'=>'N/I'];
$condLabels  = ['civil_inocente'=>'Civil inocente','suspeito'=>'Suspeito/a','em_fuga'=>'Em fuga','preso'=>'Preso/a','menor_infrator'=>'Menor infrator','manifestante'=>'Manifestante'];
?>

<!-- CABEÇALHO -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <p class="text-muted mb-1" style="font-size:.8rem;">
            <a href="<?= base_url('vitimas') ?>" class="text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Vítimas
            </a>
        </p>
        <h1 style="font-size:1.4rem;margin:0;">
            <?= $vitima['nome'] ? esc($vitima['nome']) : '<em class="text-muted">Vítima não identificada</em>' ?>
        </h1>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php if ($vitima['sexo']): ?>
                <span class="badge bg-light text-dark border"><?= esc($sexoLabels[$vitima['sexo']] ?? $vitima['sexo']) ?></span>
            <?php endif; ?>
            <?php if ($vitima['raca_cor']): ?>
                <span class="badge bg-light text-dark border"><?= esc($racaLabels[$vitima['raca_cor']] ?? $vitima['raca_cor']) ?></span>
            <?php endif; ?>
            <?php if ($vitima['menor_de_idade']): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis">Menor de idade</span>
            <?php endif; ?>
            <?php if ($vitima['gestante']): ?>
                <span class="badge bg-info-subtle text-info-emphasis">Gestante</span>
            <?php endif; ?>
            <?php if ($vitima['pcd']): ?>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">PcD</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('vitimas/' . $vitima['id'] . '/editar') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Dados da vítima -->
    <div class="col-lg-4">
        <div class="ovp-card p-4">
            <h2 style="font-size:1rem;margin-bottom:1.25rem;font-family:var(--font-body);font-weight:600;">
                <i class="bi bi-person me-2 text-danger"></i>Dados pessoais
            </h2>
            <dl class="row mb-0" style="font-size:.85rem;row-gap:.5rem;">
                <dt class="col-5 text-muted fw-normal">Nome</dt>
                <dd class="col-7 mb-0"><?= $vitima['nome'] ? esc($vitima['nome']) : '<em class="text-muted">Não identificada</em>' ?></dd>

                <dt class="col-5 text-muted fw-normal">Idade</dt>
                <dd class="col-7 mb-0"><?= $vitima['idade_aparente'] ? $vitima['idade_aparente'] . ' anos (aprox.)' : '—' ?></dd>

                <dt class="col-5 text-muted fw-normal">Nascimento</dt>
                <dd class="col-7 mb-0"><?= $vitima['data_nascimento'] ? date('d/m/Y', strtotime($vitima['data_nascimento'])) : '—' ?></dd>

                <dt class="col-5 text-muted fw-normal">Sexo</dt>
                <dd class="col-7 mb-0"><?= esc($sexoLabels[$vitima['sexo'] ?? ''] ?? ($vitima['sexo'] ?? '—')) ?></dd>

                <dt class="col-5 text-muted fw-normal">Raça/Cor</dt>
                <dd class="col-7 mb-0"><?= esc($racaLabels[$vitima['raca_cor'] ?? ''] ?? ($vitima['raca_cor'] ?? '—')) ?></dd>

                <dt class="col-5 text-muted fw-normal">Profissão</dt>
                <dd class="col-7 mb-0"><?= esc($vitima['profissao'] ?? '—') ?></dd>

                <dt class="col-5 text-muted fw-normal">Condição jur.</dt>
                <dd class="col-7 mb-0"><?= esc($condLabels[$vitima['condicao_juridica'] ?? ''] ?? ($vitima['condicao_juridica'] ?? '—')) ?></dd>
            </dl>

            <?php if ($vitima['antecedentes_versao_policial']): ?>
            <hr>
            <p class="mb-1" style="font-size:.78rem;font-weight:600;color:var(--ovp-cinza-medio);">ANTECEDENTES (versão policial)</p>
            <p style="font-size:.83rem;"><?= nl2br(esc($vitima['antecedentes_versao_policial'])) ?></p>
            <?php endif; ?>

            <?php if ($vitima['observacoes']): ?>
            <hr>
            <p class="mb-1" style="font-size:.78rem;font-weight:600;color:var(--ovp-cinza-medio);">OBSERVAÇÕES</p>
            <p style="font-size:.83rem;"><?= nl2br(esc($vitima['observacoes'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Casos vinculados -->
    <div class="col-lg-8">
        <div class="ovp-card">
            <div class="p-3 d-flex justify-content-between align-items-center" style="border-bottom:1px solid var(--ovp-borda);">
                <h2 style="font-size:1rem;margin:0;font-family:var(--font-body);font-weight:600;">
                    <i class="bi bi-folder2-open me-2 text-danger"></i>Casos vinculados
                </h2>
                <span class="badge bg-danger-subtle text-danger-emphasis"><?= count($casos) ?></span>
            </div>

            <?php if (!empty($casos)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead style="background:var(--ovp-cinza-claro);font-size:.75rem;">
                        <tr>
                            <th class="px-3 py-2">Protocolo</th>
                            <th class="px-3 py-2">Data</th>
                            <th class="px-3 py-2">Tipo</th>
                            <th class="px-3 py-2">Local</th>
                            <th class="px-3 py-2">Resultado</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($casos as $c): ?>
                        <tr>
                            <td class="px-3 py-2">
                                <code style="font-size:.78rem;"><?= esc($c['protocolo_ovp'] ?? "#{$c['id']}") ?></code>
                            </td>
                            <td class="px-3 py-2 text-nowrap"><?= date('d/m/Y', strtotime($c['data_fato'])) ?></td>
                            <td class="px-3 py-2">
                                <span class="badge-tipo badge-<?= esc($c['tipo_violencia']) ?>">
                                    <?= esc(ucfirst(str_replace('_', ' ', $c['tipo_violencia']))) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2"><?= esc(($c['bairro'] ? $c['bairro'] . ' / ' : '') . ($c['municipio'] ?? '—')) ?></td>
                            <td class="px-3 py-2">
                                <?php
                                $resLabels = ['fatal'=>'Fatal','ferido'=>'Ferido/a','sobreviveu'=>'Sobreviveu','desaparecido'=>'Desaparecido/a'];
                                $res = $c['resultado'] ?? null;
                                $resCls = ['fatal'=>'danger','ferido'=>'warning','sobreviveu'=>'success','desaparecido'=>'secondary'];
                                ?>
                                <?php if ($res): ?>
                                <span class="badge bg-<?= $resCls[$res] ?? 'secondary' ?>-subtle text-<?= $resCls[$res] ?? 'secondary' ?>-emphasis" style="font-size:.7rem;">
                                    <?= esc($resLabels[$res] ?? $res) ?>
                                </span>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td class="px-3 py-2">
                                <a href="<?= base_url('ocorrencias/' . $c['id']) ?>"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-folder2 fs-3 d-block mb-2 opacity-25"></i>
                <p style="font-size:.85rem;" class="mb-0">Esta vítima ainda não está vinculada a nenhum caso.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
