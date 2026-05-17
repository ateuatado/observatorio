<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="auditoria-header">
    <div class="auditoria-title-row">
        <div>
            <h1 class="auditoria-h1">
                <span class="auditoria-icon">📂</span> Auditoria Histórica
            </h1>
            <p class="auditoria-subtitle">Acervo de documentos históricos do OVP — indexação, análise e importação</p>
        </div>
        <form method="POST" action="<?= site_url('auditoria-historica/reindexar') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn-reindexar" title="Varre o diretório arquivos/usb/ e indexa novos PDFs">
                🔄 Reindexar Acervo
            </button>
        </form>
    </div>

    <!-- Dashboard de progresso -->
    <div class="auditoria-stats">
        <?php
        $pct = $stats['total'] > 0 ? round(($stats['importado'] / $stats['total']) * 100, 1) : 0;
        ?>
        <div class="stat-card stat-total">
            <span class="stat-num"><?= number_format($stats['total']) ?></span>
            <span class="stat-label">Total</span>
        </div>
        <div class="stat-card stat-pendente">
            <span class="stat-num"><?= number_format($stats['pendente']) ?></span>
            <span class="stat-label">Pendentes</span>
        </div>
        <div class="stat-card stat-auditando">
            <span class="stat-num"><?= number_format($stats['auditando']) ?></span>
            <span class="stat-label">Em Auditoria</span>
        </div>
        <div class="stat-card stat-importado">
            <span class="stat-num"><?= number_format($stats['importado']) ?></span>
            <span class="stat-label">Importados</span>
        </div>
        <div class="stat-card stat-descartado">
            <span class="stat-num"><?= number_format($stats['descartado']) ?></span>
            <span class="stat-label">Descartados</span>
        </div>
        <div class="stat-card stat-progress">
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: <?= $pct ?>%"></div>
            </div>
            <span class="stat-label"><?= $pct ?>% importado</span>
        </div>
    </div>
</div>

<!-- Mensagens flash -->
<?php if (session()->getFlashdata('sucesso')): ?>
    <div class="flash flash-sucesso"><?= esc(session()->getFlashdata('sucesso')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('erro')): ?>
    <div class="flash flash-erro"><?= esc(session()->getFlashdata('erro')) ?></div>
<?php endif; ?>

<!-- Filtros -->
<form method="GET" action="<?= site_url('auditoria-historica') ?>" class="auditoria-filtros">
    <select name="ano" onchange="this.form.submit()">
        <option value="">Todos os anos</option>
        <?php foreach ($anos as $a): ?>
            <option value="<?= $a['pasta_ano'] ?>" <?= ($filtros['ano'] == $a['pasta_ano']) ? 'selected' : '' ?>>
                <?= $a['pasta_ano'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="status" onchange="this.form.submit()">
        <option value="">Todos os status</option>
        <option value="pendente"   <?= $filtros['status'] === 'pendente'   ? 'selected' : '' ?>>⏳ Pendente</option>
        <option value="auditando"  <?= $filtros['status'] === 'auditando'  ? 'selected' : '' ?>>🔍 Em Auditoria</option>
        <option value="importado"  <?= $filtros['status'] === 'importado'  ? 'selected' : '' ?>>✅ Importado</option>
        <option value="descartado" <?= $filtros['status'] === 'descartado' ? 'selected' : '' ?>>🗑️ Descartado</option>
    </select>

    <select name="tipo" onchange="this.form.submit()">
        <option value="">Todos os tipos</option>
        <option value="caso"       <?= $filtros['tipo'] === 'caso'       ? 'selected' : '' ?>>⚖️ Caso</option>
        <option value="estudo"     <?= $filtros['tipo'] === 'estudo'     ? 'selected' : '' ?>>📊 Estudo</option>
        <option value="outro"      <?= $filtros['tipo'] === 'outro'      ? 'selected' : '' ?>>📄 Outro</option>
        <option value="indefinido" <?= $filtros['tipo'] === 'indefinido' ? 'selected' : '' ?>>❓ Indefinido</option>
    </select>

    <div class="filtro-busca">
        <input type="text" name="q" placeholder="Buscar por nome, veículo, resumo..."
               value="<?= esc($filtros['q'] ?? '') ?>">
        <button type="submit">🔎</button>
    </div>

    <?php if ($filtros['ano'] || $filtros['status'] || $filtros['tipo'] || $filtros['q']): ?>
        <a href="<?= site_url('auditoria-historica') ?>" class="btn-limpar">✕ Limpar</a>
    <?php endif; ?>
</form>

<!-- Tabela de documentos -->
<div class="auditoria-tabela-wrap">
    <?php if (empty($documentos)): ?>
        <div class="auditoria-vazio">
            <p>Nenhum documento encontrado<?= ($filtros['ano'] || $filtros['status'] || $filtros['q']) ? ' com estes filtros' : '. Clique em <strong>Reindexar Acervo</strong> para começar.' ?></p>
        </div>
    <?php else: ?>
        <table class="auditoria-tabela">
            <thead>
                <tr>
                    <th width="32">Status</th>
                    <th>Documento</th>
                    <th width="60">Ano</th>
                    <th width="130">Veículo</th>
                    <th width="100">Tipo IA</th>
                    <th width="110">Data</th>
                    <th width="120">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documentos as $doc): ?>
                    <?php
                    $statusIcon = match($doc['status']) {
                        'importado'  => '<span class="selo importado" title="Importado">✅</span>',
                        'auditando'  => '<span class="selo auditando" title="Em auditoria">🔍</span>',
                        'descartado' => '<span class="selo descartado" title="Descartado">🗑️</span>',
                        default      => '<span class="selo pendente" title="Pendente">⏳</span>',
                    };
                    $tipoBadge = match($doc['tipo_identificado']) {
                        'caso'      => '<span class="badge badge-caso">Caso</span>',
                        'estudo'    => '<span class="badge badge-estudo">Estudo</span>',
                        'outro'     => '<span class="badge badge-outro">Outro</span>',
                        default     => '<span class="badge badge-indef">—</span>',
                    };
                    $nomeExibido = mb_substr($doc['nome_arquivo'], 0, 70) . (mb_strlen($doc['nome_arquivo']) > 70 ? '…' : '');
                    ?>
                    <tr class="row-<?= $doc['status'] ?>">
                        <td class="td-status"><?= $statusIcon ?></td>
                        <td class="td-nome">
                            <a href="<?= site_url("auditoria-historica/{$doc['id']}") ?>" title="<?= esc($doc['nome_arquivo']) ?>">
                                <?= esc($nomeExibido) ?>
                            </a>
                            <?php if ($doc['resumo_ia']): ?>
                                <p class="resumo-preview"><?= esc(mb_substr($doc['resumo_ia'], 0, 100)) ?>…</p>
                            <?php endif; ?>
                        </td>
                        <td class="td-ano"><?= $doc['pasta_ano'] ?? '—' ?></td>
                        <td class="td-veiculo"><?= esc($doc['veiculo_imprensa'] ?? '—') ?></td>
                        <td class="td-tipo"><?= $tipoBadge ?></td>
                        <td class="td-data"><?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '—' ?></td>
                        <td class="td-acoes">
                            <a href="<?= site_url("auditoria-historica/{$doc['id']}") ?>" class="btn-acao btn-ver">Ver</a>
                            <?php if ($doc['status'] !== 'importado' && $doc['status'] !== 'descartado'): ?>
                                <a href="<?= site_url("auditoria-historica/{$doc['id']}/auditar") ?>" class="btn-acao btn-auditar">Auditar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <div class="auditoria-paginacao">
                <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                    <?php
                    $params = array_filter([
                        'ano' => $filtros['ano'], 'status' => $filtros['status'],
                        'tipo' => $filtros['tipo'], 'q' => $filtros['q'], 'p' => $p,
                    ]);
                    ?>
                    <a href="<?= site_url('auditoria-historica?' . http_build_query($params)) ?>"
                       class="pag-btn <?= $filtros['page'] == $p ? 'ativo' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <p class="total-resultado"><?= number_format($total) ?> documento<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?></p>
</div>

<style>
/* ─── Auditoria Histórica — tema claro ─────────────────────── */
.auditoria-header     { margin-bottom:1.5rem; }
.auditoria-title-row  { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; }
.auditoria-h1         { font-size:1.5rem; font-weight:700; color:#1e293b; margin:0 0 .25rem; }
.auditoria-subtitle   { font-size:.85rem; color:#64748b; margin:0; }

.btn-reindexar        { background:#3b82f6; color:#fff; border:none; border-radius:6px;
                        padding:.5rem 1.1rem; cursor:pointer; font-size:.85rem; font-weight:600;
                        transition:background .2s; white-space:nowrap; }
.btn-reindexar:hover  { background:#2563eb; }

/* Cards de estatísticas */
.auditoria-stats           { display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1rem; }
.stat-card                 { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;
                             padding:.75rem 1.25rem; display:flex; flex-direction:column;
                             align-items:center; min-width:90px; }
.stat-num                  { font-size:1.6rem; font-weight:700; color:#1e293b; }
.stat-label                { font-size:.72rem; color:#64748b; margin-top:.2rem; }
.stat-pendente .stat-num   { color:#d97706; }
.stat-auditando .stat-num  { color:#2563eb; }
.stat-importado .stat-num  { color:#059669; }
.stat-descartado .stat-num { color:#94a3b8; }
.stat-total .stat-num      { color:#1e293b; }
.stat-progress             { flex:1; min-width:200px; }
.progress-bar-wrap         { width:100%; height:10px; background:#e2e8f0; border-radius:5px; margin-bottom:.4rem; }
.progress-bar-fill         { height:100%; background:#059669; border-radius:5px; transition:width .4s; }

/* Mensagens flash */
.flash              { padding:.75rem 1rem; border-radius:6px; margin-bottom:1rem; font-size:.9rem; font-weight:500; }
.flash-sucesso      { background:#d1fae5; border:1px solid #059669; color:#065f46; }
.flash-erro         { background:#fee2e2; border:1px solid #dc2626; color:#991b1b; }

/* Filtros */
.auditoria-filtros  { display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.25rem; align-items:center; }
.auditoria-filtros select,
.auditoria-filtros input { background:#fff; border:1px solid #cbd5e1; color:#1e293b;
                           border-radius:6px; padding:.42rem .75rem; font-size:.85rem; }
.auditoria-filtros select:focus,
.auditoria-filtros input:focus { border-color:#3b82f6; outline:none; }
.filtro-busca       { display:flex; }
.filtro-busca input { border-radius:6px 0 0 6px; width:260px; border-right:none; }
.filtro-busca button { background:#3b82f6; color:#fff; border:none; border-radius:0 6px 6px 0;
                       padding:0 .85rem; cursor:pointer; font-size:1rem; }
.btn-limpar         { font-size:.8rem; color:#64748b; text-decoration:none; padding:.42rem .7rem;
                      border:1px solid #cbd5e1; border-radius:5px; background:#fff; }
.btn-limpar:hover   { color:#1e293b; border-color:#94a3b8; }

/* Tabela */
.auditoria-tabela-wrap  { overflow-x:auto; background:#fff; border:1px solid #e2e8f0; border-radius:10px; }
.auditoria-tabela       { width:100%; border-collapse:collapse; font-size:.85rem; }
.auditoria-tabela th    { background:#f1f5f9; color:#475569; font-weight:600;
                          text-align:left; padding:.65rem .85rem; white-space:nowrap;
                          border-bottom:1px solid #e2e8f0; }
.auditoria-tabela td    { padding:.6rem .85rem; border-bottom:1px solid #f1f5f9;
                          vertical-align:middle; color:#334155; }
.auditoria-tabela tbody tr:last-child td { border-bottom:none; }
.auditoria-tabela tbody tr:hover td { background:#f8fafc; }

.td-nome a          { color:#1e40af; text-decoration:none; font-weight:500; }
.td-nome a:hover    { color:#3b82f6; text-decoration:underline; }
.resumo-preview     { font-size:.78rem; color:#64748b; margin:.2rem 0 0; line-height:1.4; }
.td-status          { text-align:center; font-size:1.1rem; }
.td-ano, .td-data   { color:#475569; }
.td-veiculo         { color:#475569; font-size:.8rem; }

/* Badges de tipo IA */
.badge              { font-size:.72rem; padding:.2rem .55rem; border-radius:5px; font-weight:600; }
.badge-caso         { background:#fee2e2; color:#b91c1c; }
.badge-estudo       { background:#dbeafe; color:#1d4ed8; }
.badge-outro        { background:#f1f5f9; color:#475569; }
.badge-indef        { background:#f1f5f9; color:#94a3b8; }

/* Botões de ação na tabela */
.td-acoes           { white-space:nowrap; }
.btn-acao           { font-size:.75rem; padding:.25rem .6rem; border-radius:4px;
                      text-decoration:none; border:1px solid; display:inline-block;
                      margin-right:.25rem; transition:all .15s; font-weight:500; }
.btn-ver            { color:#2563eb; border-color:#bfdbfe; background:#eff6ff; }
.btn-ver:hover      { background:#dbeafe; border-color:#93c5fd; }
.btn-auditar        { color:#b45309; border-color:#fde68a; background:#fffbeb; }
.btn-auditar:hover  { background:#fef3c7; border-color:#fbbf24; }

/* Linhas por status */
.row-importado td   { opacity:.65; }
.row-descartado td  { opacity:.45; }
.row-descartado .td-nome a { text-decoration:line-through; }

/* Paginação */
.auditoria-paginacao { display:flex; gap:.3rem; flex-wrap:wrap; margin-top:1rem; padding:.5rem 0; }
.pag-btn            { padding:.3rem .65rem; border-radius:5px; font-size:.8rem; text-decoration:none;
                      background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.pag-btn:hover      { background:#e2e8f0; }
.pag-btn.ativo      { background:#3b82f6; color:#fff; border-color:#3b82f6; }

.total-resultado    { font-size:.8rem; color:#64748b; margin-top:.75rem; padding:0 .25rem; }
.auditoria-vazio    { text-align:center; padding:3rem; color:#94a3b8; font-size:.9rem; }
</style>

<?= $this->endSection() ?>
