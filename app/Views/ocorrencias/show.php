<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<?php
$tiposLabel = [
    'execucao'       => 'Execução',
    'chacina'        => 'Chacina',
    'tortura'        => 'Tortura',
    'abuso_poder'    => 'Abuso de poder',
    'morte_custodia' => 'Morte em custódia',
    'desaparecimento'=> 'Desaparecimento',
    'ameaca'         => 'Ameaça',
];
$statusLabel = [
    'sem_inquerito'  => ['Sem inquérito',    'secondary'],
    'inquerito_aberto'=> ['Inquérito aberto', 'primary'],
    'arquivado'      => ['Arquivado',         'danger'],
    'indiciado'      => ['Agente indiciado',  'warning'],
    'acao_penal'     => ['Ação penal',        'info'],
    'condenado'      => ['Condenado',         'success'],
    'absolvido'      => ['Absolvido',         'dark'],
];
$status = $statusLabel[$caso['status_investigacao'] ?? 'sem_inquerito'] ?? ['—', 'secondary'];
?>

<!-- BREADCRUMB -->
<div style="background:var(--ovp-cinza-claro);border-bottom:1px solid var(--ovp-borda);padding:.65rem 0;">
    <div class="container">
        <nav aria-label="breadcrumb" style="font-size:.8rem;">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('ocorrencias') ?>">Casos</a></li>
                <li class="breadcrumb-item active"><?= esc($caso['protocolo_ovp'] ?? "#{$caso['id']}") ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="ovp-section" style="padding-top:2.5rem;">
    <div class="container">
        <div class="row g-4">

            <!-- ===== COLUNA PRINCIPAL ===== -->
            <div class="col-lg-8">

                <!-- Cabeçalho do caso -->
                <div class="mb-4">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <span class="badge-tipo badge-<?= esc($caso['tipo_violencia']) ?>" style="font-size:.8rem;">
                            <?= esc($tiposLabel[$caso['tipo_violencia']] ?? ucfirst($caso['tipo_violencia'])) ?>
                        </span>
                        <span class="badge text-bg-<?= $status[1] ?>" style="font-size:.72rem;">
                            <?= $status[0] ?>
                        </span>
                        <?php if (!empty($caso['protocolo_ovp'])): ?>
                        <span style="font-size:.75rem;color:var(--ovp-cinza-medio);font-family:monospace;">
                            <?= esc($caso['protocolo_ovp']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <h1 style="font-size:1.6rem;line-height:1.3;margin-bottom:.5rem;">
                        <?php
                        $loc = [];
                        if (!empty($caso['bairro']))    $loc[] = esc($caso['bairro']);
                        if (!empty($caso['municipio'])) $loc[] = esc($caso['municipio']);
                        echo implode(', ', $loc) ?: 'Localidade não informada';
                        ?>
                    </h1>

                    <div class="d-flex flex-wrap gap-3" style="font-size:.83rem;color:var(--ovp-cinza-medio);">
                        <span><i class="bi bi-calendar3 me-1"></i><?= date('d \d\e F \d\e Y', strtotime($caso['data_fato'])) ?></span>
                        <?php if (!empty($caso['hora_fato'])): ?>
                        <span><i class="bi bi-clock me-1"></i><?= esc(substr($caso['hora_fato'],0,5)) ?>h</span>
                        <?php endif; ?>
                        <?php if (!empty($caso['municipio'])): ?>
                        <span><i class="bi bi-geo-alt me-1"></i><?= esc($caso['municipio']) ?><?= !empty($caso['estado']) ? ', ' . esc($caso['estado']) : '' ?></span>
                        <?php endif; ?>
                        <span><i class="bi bi-people-fill text-danger me-1"></i><?= (int)$caso['vitimas_fatais'] ?> vítima<?= $caso['vitimas_fatais'] != 1 ? 's fatais' : ' fatal' ?></span>
                    </div>
                </div>

                <!-- Descrição narrativa -->
                <?php if (!empty($caso['descricao_livre'])): ?>
                <div class="ovp-card p-4 mb-4">
                    <h2 style="font-size:1rem;font-family:var(--font-body);font-weight:600;margin-bottom:1rem;">
                        <i class="bi bi-file-text me-2 text-danger"></i>Relato do caso
                    </h2>
                    <div style="font-size:.92rem;line-height:1.8;color:var(--ovp-cinza);">
                        <?= nl2br(esc($caso['descricao_livre'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Versões -->
                <?php if (!empty($caso['versao_oficial']) || !empty($caso['versao_testemunhas'])): ?>
                <div class="row g-3 mb-4">
                    <?php if (!empty($caso['versao_oficial'])): ?>
                    <div class="col-md-6">
                        <div class="ovp-card p-3 h-100" style="border-left:3px solid #2563EB;">
                            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#2563EB;margin-bottom:.5rem;">
                                <i class="bi bi-shield me-1"></i>Versão policial / oficial
                            </div>
                            <p style="font-size:.85rem;line-height:1.6;margin:0;color:var(--ovp-cinza);">
                                <?= nl2br(esc($caso['versao_oficial'])) ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($caso['versao_testemunhas'])): ?>
                    <div class="col-md-6">
                        <div class="ovp-card p-3 h-100" style="border-left:3px solid #CA8A04;">
                            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#CA8A04;margin-bottom:.5rem;">
                                <i class="bi bi-person-raised-hand me-1"></i>Versão de testemunhas / vítimas
                            </div>
                            <p style="font-size:.85rem;line-height:1.6;margin:0;color:var(--ovp-cinza);">
                                <?= nl2br(esc($caso['versao_testemunhas'])) ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Vítimas -->
                <?php if (!empty($vitimas)): ?>
                <div class="ovp-card mb-4">
                    <div class="p-3" style="border-bottom:1px solid var(--ovp-borda);">
                        <h2 style="font-size:1rem;font-family:var(--font-body);font-weight:600;margin:0;">
                            <i class="bi bi-people me-2 text-danger"></i>Vítimas
                            <span class="badge text-bg-danger ms-1" style="font-size:.7rem;"><?= count($vitimas) ?></span>
                        </h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0" style="font-size:.83rem;">
                            <thead style="background:var(--ovp-cinza-claro);font-size:.75rem;">
                                <tr>
                                    <th class="px-3 py-2">Nome</th>
                                    <th class="px-3 py-2">Idade</th>
                                    <th class="px-3 py-2">Sexo</th>
                                    <th class="px-3 py-2">Raça/Cor</th>
                                    <th class="px-3 py-2">Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vitimas as $v): ?>
                                <tr>
                                    <td class="px-3 py-2">
                                        <?php if (empty($v['nome'])): ?>
                                            <span class="text-muted fst-italic">Não identificad<?= ($v['sexo'] ?? '') === 'feminino' ? 'a' : 'o' ?></span>
                                        <?php else: ?>
                                            <?= esc($v['nome']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-2"><?= $v['idade_aparente'] ? esc($v['idade_aparente']) . ' anos' : '—' ?></td>
                                    <td class="px-3 py-2"><?= esc(ucfirst($v['sexo'] ?? '—')) ?></td>
                                    <td class="px-3 py-2"><?= esc(ucfirst($v['raca_cor'] ?? '—')) ?></td>
                                    <td class="px-3 py-2">
                                        <?php
                                        $res = $v['resultado'] ?? '';
                                        $cores = ['fatal'=>'danger','ferido'=>'warning','sobreviveu'=>'success','desaparecido'=>'dark'];
                                        $cor = $cores[$res] ?? 'secondary';
                                        ?>
                                        <span class="badge text-bg-<?= $cor ?>" style="font-size:.68rem;">
                                            <?= esc(ucfirst($res ?: '—')) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Agentes -->
                <?php if (!empty($agentes)): ?>
                <div class="ovp-card mb-4">
                    <div class="p-3" style="border-bottom:1px solid var(--ovp-borda);">
                        <h2 style="font-size:1rem;font-family:var(--font-body);font-weight:600;margin:0;">
                            <i class="bi bi-person-badge me-2 text-danger"></i>Agentes envolvidos
                        </h2>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <?php foreach ($agentes as $a): ?>
                        <div class="d-flex align-items-start gap-2" style="font-size:.85rem;padding:.5rem;background:var(--ovp-cinza-claro);border-radius:8px;">
                            <i class="bi bi-person-fill text-danger mt-1"></i>
                            <div>
                                <?php if (!empty($a['descricao_agente'])): ?>
                                    <span><?= esc($a['descricao_agente']) ?></span>
                                <?php elseif (!empty($a['nome'])): ?>
                                    <strong><?= esc($a['nome']) ?></strong>
                                <?php else: ?>
                                    <span class="fst-italic text-muted">
                                        <?= $a['quantidade_agentes'] > 1 ? $a['quantidade_agentes'] . ' agentes ' : 'Agente ' ?>
                                        não identificado<?= $a['quantidade_agentes'] > 1 ? 's' : '' ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($a['corporacao'])): ?>
                                    <span class="badge text-bg-secondary ms-1" style="font-size:.68rem;"><?= esc($a['corporacao']) ?></span>
                                <?php endif; ?>
                                <?php if ($a['encapuzado'] ?? 0): ?>
                                    <span class="badge text-bg-dark ms-1" style="font-size:.68rem;">Encapuzado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Documentos e fontes -->
                <?php if (!empty($documentos)): ?>
                <div class="ovp-card mb-4">
                    <div class="p-3" style="border-bottom:1px solid var(--ovp-borda);">
                        <h2 style="font-size:1rem;font-family:var(--font-body);font-weight:600;margin:0;">
                            <i class="bi bi-files me-2 text-danger"></i>Documentos e fontes
                        </h2>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <?php foreach ($documentos as $doc): ?>
                        <div class="d-flex align-items-start gap-3" style="padding:.5rem;border-radius:8px;background:var(--ovp-cinza-claro);">
                            <i class="bi bi-file-earmark-text text-danger fs-5 mt-1 flex-shrink-0"></i>
                            <div style="font-size:.83rem;">
                                <div style="font-weight:600;"><?= esc($doc['titulo']) ?></div>
                                <div style="color:var(--ovp-cinza-medio);">
                                    <?= !empty($doc['nome_veiculo']) ? esc($doc['nome_veiculo']) . ' · ' : '' ?>
                                    <?= !empty($doc['data_publicacao']) ? date('d/m/Y', strtotime($doc['data_publicacao'])) : '' ?>
                                </div>
                                <?php if (!empty($doc['url_original'])): ?>
                                <a href="<?= esc($doc['url_original']) ?>" target="_blank" rel="noopener" style="font-size:.78rem;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver fonte
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- ===== COLUNA LATERAL ===== -->
            <div class="col-lg-4">
                <div class="ovp-card p-3 mb-3 sticky-top" style="top:80px;">
                    <h3 style="font-size:.85rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:1rem;color:var(--ovp-cinza-medio);">
                        Dados do caso
                    </h3>

                    <dl style="font-size:.85rem;">
                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Data do fato</dt>
                        <dd style="margin-bottom:.85rem;"><?= date('d/m/Y', strtotime($caso['data_fato'])) ?></dd>

                        <?php if (!empty($caso['hora_fato'])): ?>
                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Horário</dt>
                        <dd style="margin-bottom:.85rem;"><?= esc(substr($caso['hora_fato'],0,5)) ?>h (aprox.)</dd>
                        <?php endif; ?>

                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Localização</dt>
                        <dd style="margin-bottom:.85rem;">
                            <?php
                            $partes = array_filter([
                                $caso['logradouro'] ?? '',
                                $caso['bairro'] ?? '',
                                $caso['municipio'] ?? '',
                                $caso['estado'] ?? '',
                            ]);
                            echo esc(implode(', ', $partes)) ?: '—';
                            ?>
                        </dd>

                        <?php if (!empty($caso['tipo_local'])): ?>
                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Tipo de local</dt>
                        <dd style="margin-bottom:.85rem;"><?= esc(ucfirst(str_replace('_', ' ', $caso['tipo_local']))) ?></dd>
                        <?php endif; ?>

                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Vítimas fatais</dt>
                        <dd style="margin-bottom:.85rem;font-size:1.1rem;font-weight:700;color:var(--ovp-vermelho);"><?= (int)$caso['vitimas_fatais'] ?></dd>

                        <?php if ($caso['vitimas_nao_fatais'] > 0): ?>
                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Feridos</dt>
                        <dd style="margin-bottom:.85rem;"><?= (int)$caso['vitimas_nao_fatais'] ?></dd>
                        <?php endif; ?>

                        <dt style="color:var(--ovp-cinza-medio);font-weight:500;margin-bottom:.15rem;">Status judicial</dt>
                        <dd style="margin-bottom:.85rem;">
                            <span class="badge text-bg-<?= $status[1] ?>"><?= $status[0] ?></span>
                        </dd>
                    </dl>

                    <div class="d-flex flex-column gap-2 mt-3 pt-3" style="border-top:1px solid var(--ovp-borda);">
                        <a href="<?= base_url('ocorrencias') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Voltar à lista
                        </a>
                        <?php if (auth()->loggedIn()): ?>
                        <a href="<?= base_url('ocorrencias/' . $caso['id'] . '/editar') ?>" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-pencil me-1"></i>Editar caso
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
