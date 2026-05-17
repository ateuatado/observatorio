<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php
$statusLabel = match($doc['status']) {
    'importado'  => ['✅ Importado', 'importado'],
    'auditando'  => ['🔍 Em Auditoria', 'auditando'],
    'descartado' => ['🗑️ Descartado', 'descartado'],
    default      => ['⏳ Pendente', 'pendente'],
};
$tipoLabel = match($doc['tipo_identificado']) {
    'caso'    => '⚖️ Caso de Violência Policial',
    'estudo'  => '📊 Estudo/Análise',
    'outro'   => '📄 Outro',
    default   => '❓ Indefinido',
};
$camposIa = $doc['dados_extraidos_ia'] ? (json_decode($doc['dados_extraidos_ia'], true) ?? []) : [];
?>

<div class="doc-ficha-header">
    <a href="<?= site_url('auditoria-historica') ?>" class="btn-voltar">← Auditoria Histórica</a>
    <span class="doc-status-badge status-<?= $statusLabel[1] ?>"><?= $statusLabel[0] ?></span>
</div>

<!-- Mensagens flash -->
<?php if (session()->getFlashdata('sucesso')): ?>
    <div class="flash flash-sucesso"><?= esc(session()->getFlashdata('sucesso')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('erro')): ?>
    <div class="flash flash-erro"><?= esc(session()->getFlashdata('erro')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('aviso')): ?>
    <div class="flash flash-aviso"><?= esc(session()->getFlashdata('aviso')) ?></div>
<?php endif; ?>

<!-- Alerta se já importado -->
<?php if ($doc['status'] === 'importado' && $casoImportado): ?>
    <div class="importado-alerta">
        ✅ Este documento foi importado como
        <a href="<?= site_url("casos/{$casoImportado['id']}") ?>">
            Caso <?= esc($casoImportado['protocolo_ovp']) ?>
        </a>
        <?php if ($doc['importado_em']): ?>
            em <?= date('d/m/Y H:i', strtotime($doc['importado_em'])) ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="doc-ficha-layout">

    <!-- Coluna esquerda: visualizador -->
    <div class="doc-ficha-esq">
        <div class="doc-viewer">
            <?php
            $pdfUrl = site_url("auditoria-historica/arquivo/{$doc['id']}");
            ?>
            <?php if ($doc['miniatura_path'] && file_exists(ROOTPATH . $doc['miniatura_path'])): ?>
                <img src="<?= site_url($doc['miniatura_path']) ?>" alt="Capa do documento" class="doc-miniatura">
            <?php else: ?>
                <!-- Visualizador PDF nativo do browser -->
                <iframe src="<?= $pdfUrl ?>#page=1" class="doc-iframe" title="<?= esc($doc['nome_arquivo']) ?>"></iframe>
            <?php endif; ?>
        </div>

        <div class="doc-acoes-arquivo">
            <a href="<?= $pdfUrl ?>" target="_blank" class="btn-pdf-abrir">📄 Abrir PDF</a>
            <a href="<?= $pdfUrl ?>" download="<?= esc(urlencode($doc['nome_arquivo'])) ?>" class="btn-pdf-baixar">⬇️ Baixar</a>
        </div>

        <!-- Metadados do arquivo -->
        <div class="doc-meta-box">
            <h3>Metadados do arquivo</h3>
            <table class="meta-tabela">
                <tr><th>Nome</th><td><?= esc($doc['nome_arquivo']) ?></td></tr>
                <?php if ($doc['id_interno']): ?>
                <tr><th>ID Interno</th><td><?= esc($doc['id_interno']) ?></td></tr>
                <?php endif; ?>
                <?php if ($doc['veiculo_imprensa']): ?>
                <tr><th>Veículo</th><td><?= esc($doc['veiculo_imprensa']) ?></td></tr>
                <?php endif; ?>
                <?php if ($doc['pasta_ano']): ?>
                <tr><th>Ano</th><td><?= $doc['pasta_ano'] ?><?= $doc['pasta_mes'] ? ' / mês ' . $doc['pasta_mes'] : '' ?></td></tr>
                <?php endif; ?>
                <?php if ($doc['data_documento']): ?>
                <tr><th>Data extraída</th><td><?= date('d/m/Y', strtotime($doc['data_documento'])) ?></td></tr>
                <?php endif; ?>
                <?php if ($doc['tamanho_bytes']): ?>
                <tr><th>Tamanho</th><td><?= number_format($doc['tamanho_bytes'] / 1024, 0) ?> KB</td></tr>
                <?php endif; ?>
                <tr><th>Indexado em</th><td><?= date('d/m/Y H:i', strtotime($doc['indexado_em'])) ?></td></tr>
                <tr><th>Caminho</th><td class="meta-caminho"><?= esc($doc['caminho_relativo']) ?></td></tr>
            </table>
        </div>
    </div>

    <!-- Coluna direita: análise e ações -->
    <div class="doc-ficha-dir">
        <h2 class="doc-titulo"><?= esc(mb_substr($doc['nome_arquivo'], 0, 100)) ?></h2>

        <!-- Tipo identificado -->
        <div class="doc-tipo-badge tipo-<?= $doc['tipo_identificado'] ?>">
            <?= $tipoLabel ?>
        </div>

        <!-- Resumo IA -->
        <div class="doc-resumo-box">
            <h3>Resumo <?= $doc['ia_processado'] ? '(gerado por IA)' : '(não analisado)' ?></h3>
            <?php if ($doc['resumo_ia']): ?>
                <p><?= nl2br(esc($doc['resumo_ia'])) ?></p>
            <?php else: ?>
                <p class="sem-analise">Este documento ainda não foi analisado.</p>
            <?php endif; ?>
        </div>

        <!-- Campos extraídos pela IA -->
        <?php if (!empty($camposIa)): ?>
        <div class="doc-campos-ia">
            <h3>Dados extraídos pela IA</h3>
            <table class="meta-tabela">
                <?php foreach ($camposIa as $chave => $valor): if ($valor === null) continue; ?>
                <tr>
                    <th><?= esc(ucwords(str_replace('_', ' ', $chave))) ?></th>
                    <td><?= is_array($valor) ? esc(implode(', ', $valor)) : esc($valor) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <!-- Ações -->
        <div class="doc-acoes-principais">
            <?php if (!$doc['ia_processado'] && $doc['status'] !== 'importado'): ?>
                <form method="POST" action="<?= site_url("auditoria-historica/{$doc['id']}/analisar") ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-analisar">
                        🤖 Analisar com IA
                    </button>
                </form>
            <?php elseif ($doc['ia_processado'] && $doc['status'] !== 'importado'): ?>
                <form method="POST" action="<?= site_url("auditoria-historica/{$doc['id']}/analisar") ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-reanalisar">
                        🔄 Re-analisar
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($doc['status'] !== 'importado' && $doc['status'] !== 'descartado'): ?>
                <a href="<?= site_url("auditoria-historica/{$doc['id']}/auditar") ?>"
                   class="btn-action btn-auditar-grande">
                    ✏️ Auditar e Importar
                </a>
            <?php elseif ($doc['status'] === 'importado'): ?>
                <a href="<?= site_url("casos/{$doc['caso_id']}") ?>"
                   class="btn-action btn-ver-caso">
                    ⚖️ Ver Caso Importado
                </a>
            <?php endif; ?>

            <?php if ($doc['status'] !== 'importado' && $doc['status'] !== 'descartado'): ?>
                <button class="btn-action btn-descartar-toggle" onclick="toggleDescartar()">
                    🗑️ Descartar
                </button>
                <div id="form-descartar" class="form-descartar" style="display:none">
                    <form method="POST" action="<?= site_url("auditoria-historica/{$doc['id']}/descartar") ?>">
                        <?= csrf_field() ?>
                        <textarea name="nota" placeholder="Motivo do descarte (opcional)..." rows="3"></textarea>
                        <button type="submit" class="btn-descartar-confirmar">Confirmar descarte</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notas do auditor -->
        <?php if ($doc['notas_auditor']): ?>
            <div class="doc-notas-auditor">
                <h3>Notas do Auditor</h3>
                <p><?= nl2br(esc($doc['notas_auditor'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.doc-ficha-header       { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.btn-voltar             { font-size:.85rem; color:var(--ovp-primary,#3b82f6); text-decoration:none; }
.btn-voltar:hover       { text-decoration:underline; }
.doc-status-badge       { font-size:.8rem; padding:.3rem .75rem; border-radius:20px; font-weight:600; }
.status-pendente        { background:rgba(245,158,11,.15); color:#f59e0b; }
.status-auditando       { background:rgba(96,165,250,.15); color:#60a5fa; }
.status-importado       { background:rgba(52,211,153,.15); color:#34d399; }
.status-descartado      { background:rgba(156,163,175,.15); color:#9ca3af; }

.importado-alerta       { background:rgba(52,211,153,.1); border:1px solid #34d399; border-radius:8px;
                          padding:.75rem 1rem; margin-bottom:1rem; font-size:.9rem; color:#34d399; }
.importado-alerta a     { color:#34d399; font-weight:700; }

.doc-ficha-layout       { display:grid; grid-template-columns:minmax(320px,2fr) 3fr; gap:1.5rem; }
@media (max-width:900px) { .doc-ficha-layout { grid-template-columns:1fr; } }

/* Visualizador */
.doc-viewer             { background:rgba(0,0,0,.3); border-radius:8px; overflow:hidden; }
.doc-iframe             { width:100%; height:520px; border:none; }
.doc-miniatura          { width:100%; border-radius:8px; }
.doc-acoes-arquivo      { display:flex; gap:.5rem; margin:.75rem 0; }
.btn-pdf-abrir,
.btn-pdf-baixar         { flex:1; text-align:center; padding:.5rem; border-radius:6px; font-size:.85rem;
                          text-decoration:none; border:1px solid rgba(255,255,255,.15); color:var(--ovp-white,#f0f4f8); }
.btn-pdf-abrir:hover    { background:rgba(255,255,255,.08); }
.btn-pdf-baixar:hover   { background:rgba(255,255,255,.08); }

/* Meta */
.doc-meta-box,
.doc-resumo-box,
.doc-campos-ia,
.doc-notas-auditor      { background:rgba(255,255,255,.04); border-radius:8px; padding:1rem; margin-bottom:1rem; }
.doc-meta-box h3,
.doc-resumo-box h3,
.doc-campos-ia h3,
.doc-notas-auditor h3   { font-size:.85rem; font-weight:600; color:var(--ovp-muted,#8899aa); margin:0 0 .6rem; text-transform:uppercase; letter-spacing:.06em; }
.meta-tabela            { width:100%; border-collapse:collapse; font-size:.82rem; }
.meta-tabela th         { color:var(--ovp-muted,#8899aa); font-weight:500; padding:.3rem .5rem .3rem 0; white-space:nowrap; width:120px; vertical-align:top; }
.meta-tabela td         { color:var(--ovp-white,#f0f4f8); padding:.3rem .5rem; word-break:break-all; }
.meta-caminho           { font-family:monospace; font-size:.75rem; color:#60a5fa; }

/* Direita */
.doc-titulo             { font-size:1.05rem; font-weight:700; line-height:1.4; margin-bottom:.75rem; }
.doc-tipo-badge         { display:inline-block; padding:.35rem .85rem; border-radius:20px; font-size:.85rem;
                          font-weight:600; margin-bottom:1rem; }
.tipo-caso              { background:rgba(239,68,68,.15); color:#f87171; }
.tipo-estudo            { background:rgba(96,165,250,.15); color:#60a5fa; }
.tipo-outro             { background:rgba(156,163,175,.15); color:#9ca3af; }
.tipo-indefinido        { background:rgba(255,255,255,.06); color:#6b7280; }
.sem-analise            { color:var(--ovp-muted,#8899aa); font-style:italic; }

/* Ações */
.doc-acoes-principais   { display:flex; flex-direction:column; gap:.5rem; margin-bottom:1rem; }
.btn-action             { padding:.65rem 1rem; border-radius:8px; font-size:.9rem; font-weight:600; cursor:pointer;
                          border:none; text-align:center; text-decoration:none; display:block; transition:all .15s; }
.btn-analisar           { background:rgba(96,165,250,.15); color:#60a5fa; border:1px solid rgba(96,165,250,.3); }
.btn-analisar:hover     { background:rgba(96,165,250,.25); }
.btn-reanalisar         { background:rgba(156,163,175,.1); color:#9ca3af; border:1px solid rgba(156,163,175,.2); }
.btn-auditar-grande     { background:rgba(245,158,11,.15); color:#f59e0b; border:1px solid rgba(245,158,11,.3); }
.btn-auditar-grande:hover { background:rgba(245,158,11,.25); }
.btn-ver-caso           { background:rgba(52,211,153,.15); color:#34d399; border:1px solid rgba(52,211,153,.3); }
.btn-descartar-toggle   { background:transparent; color:#9ca3af; border:1px solid rgba(156,163,175,.2); }
.btn-descartar-toggle:hover { color:#f87171; border-color:rgba(239,68,68,.3); }

.form-descartar         { background:rgba(239,68,68,.07); border:1px solid rgba(239,68,68,.2); border-radius:6px; padding:.75rem; }
.form-descartar textarea { width:100%; background:rgba(0,0,0,.2); color:#f0f4f8; border:1px solid rgba(255,255,255,.1);
                           border-radius:4px; padding:.5rem; font-size:.85rem; resize:vertical; box-sizing:border-box; }
.btn-descartar-confirmar { background:#ef4444; color:#fff; border:none; border-radius:5px; padding:.45rem .9rem;
                           margin-top:.5rem; cursor:pointer; font-size:.85rem; }

/* Flash */
.flash              { padding:.75rem 1rem; border-radius:6px; margin-bottom:1rem; font-size:.9rem; }
.flash-sucesso      { background:rgba(52,211,153,.15); border:1px solid #34d399; color:#34d399; }
.flash-erro         { background:rgba(239,68,68,.15); border:1px solid #ef4444; color:#ef4444; }
.flash-aviso        { background:rgba(245,158,11,.15); border:1px solid #f59e0b; color:#f59e0b; }
</style>

<script>
function toggleDescartar() {
    const f = document.getElementById('form-descartar');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
</script>

<?= $this->endSection() ?>
