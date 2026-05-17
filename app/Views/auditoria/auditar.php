<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php
$ia = $camposIa; // array de campos sugeridos pela IA
?>

<div class="aud-header">
    <a href="<?= site_url("auditoria-historica/{$doc['id']}") ?>" class="btn-voltar">← Voltar para a Ficha</a>
    <h1 class="aud-h1">✏️ Auditoria de Documento</h1>
</div>

<?php if (session()->getFlashdata('erro')): ?>
    <div class="flash flash-erro"><?= esc(session()->getFlashdata('erro')) ?></div>
<?php endif; ?>

<!-- Aviso de documento já importado -->
<?php if ($doc['status'] === 'importado'): ?>
    <div class="flash flash-aviso">
        ⚠️ Atenção: este documento já foi importado como Caso #<?= $doc['caso_id'] ?>.
        Importar novamente criará um caso duplicado.
    </div>
<?php endif; ?>

<p class="aud-instrucao">
    Revise os dados abaixo — pré-preenchidos pela análise da IA — corrija qualquer imprecisão e confirme a importação.
    O texto completo do documento está disponível à esquerda para consulta.
</p>

<div class="aud-layout">

    <!-- Coluna esquerda: visualizador + texto extraído -->
    <div class="aud-esq">
        <div class="aud-pdf-wrap">
            <iframe src="<?= site_url("auditoria-historica/arquivo/{$doc['id']}") ?>#page=1"
                    class="aud-iframe" title="<?= esc($doc['nome_arquivo']) ?>"></iframe>
        </div>

        <?php if ($doc['texto_extraido']): ?>
        <div class="aud-texto-box">
            <h3>Texto extraído do PDF</h3>
            <div class="aud-texto-scroll"><?= nl2br(esc(mb_substr($doc['texto_extraido'], 0, 5000))) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Coluna direita: formulário de auditoria -->
    <div class="aud-dir">
        <div class="aud-nome-arquivo">
            📄 <strong><?= esc(mb_substr($doc['nome_arquivo'], 0, 90)) ?></strong>
            <?php if ($doc['veiculo_imprensa']): ?>
                <span class="aud-veiculo"><?= esc($doc['veiculo_imprensa']) ?></span>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?= site_url("auditoria-historica/{$doc['id']}/importar") ?>"
              id="form-auditoria">
            <?= csrf_field() ?>

            <!-- ─── DADOS DO FATO ─────────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>📅 Dados do Fato</legend>

                <div class="aud-row-2">
                    <div class="campo">
                        <label>Data do fato <span class="obrig">*</span></label>
                        <input type="date" name="data_fato"
                               value="<?= esc($ia['data_fato'] ?? $doc['data_documento'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label>Hora (aproximada)</label>
                        <input type="time" name="hora_fato" value="">
                    </div>
                </div>

                <div class="aud-row-2">
                    <div class="campo">
                        <label>Tipo de Violência <span class="obrig">*</span></label>
                        <select name="tipo_violencia" required>
                            <?php
                            $tipos = [
                                'execucao'       => 'Execução',
                                'chacina'        => 'Chacina',
                                'tortura'        => 'Tortura',
                                'abuso_poder'    => 'Abuso de Poder',
                                'morte_custodia' => 'Morte em Custódia',
                                'desaparecimento'=> 'Desaparecimento',
                                'ameaca'         => 'Ameaça',
                            ];
                            $tipoSugerido = $ia['tipo_violencia'] ?? '';
                            foreach ($tipos as $val => $label):
                            ?>
                            <option value="<?= $val ?>" <?= $tipoSugerido === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label>Status do Inquérito</label>
                        <select name="status_investigacao">
                            <option value="sem_inquerito">Sem inquérito</option>
                            <option value="inquerito_aberto">Inquérito aberto</option>
                            <option value="arquivado">Arquivado</option>
                            <option value="denunciado">Denunciado</option>
                            <option value="condenado">Condenado</option>
                        </select>
                    </div>
                </div>

                <div class="aud-row-3">
                    <div class="campo">
                        <label>Vítimas Fatais</label>
                        <input type="number" name="vitimas_fatais" min="0"
                               value="<?= (int)($ia['vitimas_fatais'] ?? 0) ?>">
                    </div>
                    <div class="campo">
                        <label>Vítimas Não-Fatais</label>
                        <input type="number" name="vitimas_nao_fatais" min="0"
                               value="<?= (int)($ia['vitimas_nao_fatais'] ?? 0) ?>">
                    </div>
                    <div class="campo">
                        <label>Subtipo</label>
                        <input type="text" name="subtipo" placeholder="Ex: invasão de domicílio"
                               value="<?= esc($ia['subtipo'] ?? '') ?>">
                    </div>
                </div>
            </fieldset>

            <!-- ─── LOCALIZAÇÃO ───────────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>📍 Localização</legend>

                <div class="aud-row-2">
                    <div class="campo">
                        <label>Município <span class="obrig">*</span></label>
                        <input type="text" name="municipio" required
                               value="<?= esc($ia['municipio'] ?? '') ?>" placeholder="Ex: São Paulo">
                    </div>
                    <div class="campo">
                        <label>Estado</label>
                        <input type="text" name="estado" maxlength="2"
                               value="<?= esc($ia['estado'] ?? 'SP') ?>" placeholder="SP">
                    </div>
                </div>

                <div class="aud-row-2">
                    <div class="campo">
                        <label>Bairro</label>
                        <input type="text" name="bairro" value="<?= esc($ia['bairro'] ?? '') ?>">
                    </div>
                    <div class="campo">
                        <label>Logradouro</label>
                        <input type="text" name="logradouro" value="">
                    </div>
                </div>

                <div class="campo">
                    <label>Tipo de local</label>
                    <select name="tipo_local">
                        <option value="">Não informado</option>
                        <option value="via_publica">Via pública</option>
                        <option value="residencia">Residência</option>
                        <option value="bar_comercio">Bar / Comércio</option>
                        <option value="unidade_policial">Unidade Policial</option>
                        <option value="unidade_prisional">Unidade Prisional</option>
                        <option value="rodovia">Rodovia</option>
                        <option value="hospital">Hospital</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
            </fieldset>

            <!-- ─── NARRATIVA ─────────────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>📝 Narrativa</legend>

                <div class="campo">
                    <label>Versão oficial</label>
                    <textarea name="versao_oficial" rows="3"
                              placeholder="Narrativa da Polícia Militar / autoridades..."><?= esc($ia['versao_oficial'] ?? '') ?></textarea>
                </div>
                <div class="campo">
                    <label>Versão de testemunhas / família</label>
                    <textarea name="versao_testemunhas" rows="3"
                              placeholder="Narrativa de testemunhas, familiares ou entidades..."></textarea>
                </div>
                <div class="campo">
                    <label>Descrição livre</label>
                    <textarea name="descricao_livre" rows="4"><?= esc($ia['descricao_livre'] ?? $doc['resumo_ia'] ?? '') ?></textarea>
                </div>
            </fieldset>

            <!-- ─── VÍTIMAS ────────────────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>👤 Vítimas <small>(opcional — adicione individualmente)</small></legend>
                <div id="vitimas-container"></div>
                <button type="button" class="btn-add" onclick="adicionarVitima()">+ Adicionar Vítima</button>
            </fieldset>

            <!-- ─── AGENTES ───────────────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>🚔 Agentes <small>(opcional)</small></legend>
                <div id="agentes-container"></div>
                <button type="button" class="btn-add" onclick="adicionarAgente()">+ Adicionar Agente</button>
            </fieldset>

            <!-- ─── NOTAS DO AUDITOR ──────────────────────────── -->
            <fieldset class="aud-fieldset">
                <legend>📋 Notas do Auditor</legend>
                <div class="campo">
                    <textarea name="notas_auditor" rows="3"
                              placeholder="Observações sobre a qualidade do documento, imprecisões encontradas, etc."><?= esc($doc['notas_auditor'] ?? '') ?></textarea>
                </div>
            </fieldset>

            <!-- ─── BOTÕES ────────────────────────────────────── -->
            <div class="aud-botoes">
                <a href="<?= site_url("auditoria-historica/{$doc['id']}") ?>" class="btn-cancelar">
                    Cancelar
                </a>
                <button type="submit" class="btn-confirmar">
                    ✅ Confirmar Importação como Caso
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template de vítima (oculto) -->
<template id="tpl-vitima">
    <div class="vitima-row" data-idx="__IDX__">
        <div class="vitima-header">
            <strong>Vítima #<span class="vitima-num">__NUM__</span></strong>
            <button type="button" class="btn-remover" onclick="removerItem(this, 'vitima')">✕</button>
        </div>
        <div class="aud-row-3">
            <div class="campo"><label>Nome</label>
                <input type="text" name="vitimas[__IDX__][nome]" placeholder="Nome (ou 'Não identificada')"></div>
            <div class="campo"><label>Sexo</label>
                <select name="vitimas[__IDX__][sexo]">
                    <option value="">—</option>
                    <option value="masculino">Masculino</option>
                    <option value="feminino">Feminino</option>
                    <option value="outro">Outro</option>
                </select></div>
            <div class="campo"><label>Idade aprox.</label>
                <input type="number" name="vitimas[__IDX__][idade_aparente]" min="0" max="120"></div>
        </div>
        <div class="aud-row-3">
            <div class="campo"><label>Raça/Cor</label>
                <select name="vitimas[__IDX__][raca_cor]">
                    <option value="">—</option>
                    <option value="branca">Branca</option>
                    <option value="preta">Preta</option>
                    <option value="parda">Parda</option>
                    <option value="amarela">Amarela</option>
                    <option value="indigena">Indígena</option>
                </select></div>
            <div class="campo"><label>Resultado</label>
                <select name="vitimas[__IDX__][resultado]">
                    <option value="fatal">Fatal</option>
                    <option value="ferido">Ferido</option>
                    <option value="preso">Preso</option>
                    <option value="outro">Outro</option>
                </select></div>
            <div class="campo"><label>Menor de idade?</label>
                <select name="vitimas[__IDX__][menor_de_idade]">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select></div>
        </div>
    </div>
</template>

<!-- Template de agente (oculto) -->
<template id="tpl-agente">
    <div class="agente-row" data-idx="__IDX__">
        <div class="vitima-header">
            <strong>Agente/Grupo #<span class="agente-num">__NUM__</span></strong>
            <button type="button" class="btn-remover" onclick="removerItem(this, 'agente')">✕</button>
        </div>
        <div class="aud-row-3">
            <div class="campo"><label>Corporação</label>
                <select name="agentes[__IDX__][corporacao]">
                    <option value="PM">PM</option>
                    <option value="PC">PC</option>
                    <option value="GCM">GCM</option>
                    <option value="PF">PF</option>
                    <option value="PRF">PRF</option>
                    <option value="ROTA">ROTA</option>
                    <option value="Outro">Outro</option>
                </select></div>
            <div class="campo"><label>Qtd. agentes</label>
                <input type="number" name="agentes[__IDX__][quantidade_agentes]" value="1" min="1"></div>
            <div class="campo"><label>Papel no caso</label>
                <select name="agentes[__IDX__][papel_no_caso]">
                    <option value="executor">Executor</option>
                    <option value="apoio">Apoio</option>
                    <option value="responsavel">Responsável</option>
                </select></div>
        </div>
        <div class="campo"><label>Descrição</label>
            <input type="text" name="agentes[__IDX__][descricao_agente]" placeholder="Ex: Dois PMs da Força Tática"></div>
    </div>
</template>

<style>
/* ═══════════════════════════════════════════════════════════
   AUDITORIA — Formulário de alto contraste (tema claro)
   Referência: WCAG 2.1 AA — contraste mínimo 4.5:1
   ═══════════════════════════════════════════════════════════ */

/* ── Cabeçalho ─────────────────────────────────────────────── */
.aud-header         { display:flex; align-items:center; gap:1.5rem;
                      margin-bottom:.5rem; flex-wrap:wrap; }
.aud-h1             { font-size:1.35rem; font-weight:700; color:#111827; margin:0; }
.btn-voltar         { font-size:.85rem; color:#2563eb; text-decoration:none;
                      display:inline-flex; align-items:center; gap:.3rem;
                      padding:.3rem .6rem; border-radius:5px; border:1px solid #bfdbfe;
                      background:#eff6ff; white-space:nowrap; }
.btn-voltar:hover   { background:#dbeafe; color:#1d4ed8; }
.aud-instrucao      { font-size:.88rem; color:#374151; margin-bottom:1.25rem;
                      padding:.6rem .9rem; background:#f0f9ff; border-left:3px solid #3b82f6;
                      border-radius:0 6px 6px 0; }

/* ── Layout dois painéis ───────────────────────────────────── */
.aud-layout         { display:grid; grid-template-columns:2fr 3fr; gap:1.5rem; }
@media(max-width:960px) { .aud-layout { grid-template-columns:1fr; } }

/* ── Painel esquerdo (visualizador) ───────────────────────── */
.aud-pdf-wrap       { margin-bottom:1rem; border:1px solid #d1d5db; border-radius:8px;
                      overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.aud-iframe         { width:100%; height:480px; border:none; background:#e5e7eb; display:block; }
.aud-texto-box      { background:#f8fafc; border:1px solid #e2e8f0;
                      border-radius:8px; padding:.85rem; }
.aud-texto-box h3   { font-size:.75rem; font-weight:700; color:#6b7280;
                      text-transform:uppercase; letter-spacing:.07em; margin:0 0 .6rem; }
.aud-texto-scroll   { max-height:260px; overflow-y:auto; font-size:.78rem;
                      line-height:1.65; color:#374151; white-space:pre-wrap;
                      font-family:ui-monospace, monospace; }

/* ── Banner do nome do arquivo ─────────────────────────────── */
.aud-nome-arquivo   { background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px;
                      padding:.65rem .85rem; margin-bottom:1.1rem;
                      font-size:.85rem; color:#1e293b; word-break:break-all; }
.aud-veiculo        { display:inline-block; font-size:.73rem; font-weight:600;
                      background:#dbeafe; color:#1d4ed8; padding:.15rem .5rem;
                      border-radius:4px; margin-left:.5rem; vertical-align:middle; }

/* ── Fieldsets por seção ───────────────────────────────────── */
.aud-fieldset       { border:1px solid #d1d5db; border-radius:10px;
                      padding:1rem 1.1rem 1.1rem; margin-bottom:1rem;
                      background:#fff; }
.aud-fieldset legend { font-size:.88rem; font-weight:700; color:#1e293b;
                       padding:0 .5rem; display:flex; align-items:center; gap:.35rem; }
.aud-fieldset legend small { font-weight:400; color:#6b7280; font-size:.78rem; }

/* Borda colorida por tipo de seção (via nth-of-type) */
.aud-fieldset:nth-of-type(1) { border-top:3px solid #ef4444; } /* Dados do fato */
.aud-fieldset:nth-of-type(2) { border-top:3px solid #f59e0b; } /* Localização */
.aud-fieldset:nth-of-type(3) { border-top:3px solid #8b5cf6; } /* Narrativa */
.aud-fieldset:nth-of-type(4) { border-top:3px solid #3b82f6; } /* Vítimas */
.aud-fieldset:nth-of-type(5) { border-top:3px solid #6b7280; } /* Agentes */
.aud-fieldset:nth-of-type(6) { border-top:3px solid #10b981; } /* Notas */

/* ── Campos do formulário ──────────────────────────────────── */
.campo              { display:flex; flex-direction:column; gap:.3rem; margin-bottom:.1rem; }

.campo label        { font-size:.8rem; font-weight:600; color:#374151;
                      display:flex; align-items:center; gap:.25rem; }

.campo input,
.campo select,
.campo textarea     {
    background:#fff;
    border:1.5px solid #9ca3af;
    color:#111827;
    border-radius:6px;
    padding:.45rem .65rem;
    font-size:.875rem;
    width:100%;
    box-sizing:border-box;
    transition:border-color .15s, box-shadow .15s;
    font-family:inherit;
}
.campo input:focus,
.campo select:focus,
.campo textarea:focus {
    border-color:#3b82f6;
    outline:none;
    box-shadow:0 0 0 3px rgba(59,130,246,.15);
}
.campo input::placeholder,
.campo textarea::placeholder { color:#9ca3af; }
.campo textarea     { resize:vertical; min-height:80px; }
.campo select       { appearance:auto; }

/* Campo obrigatório */
.obrig              { color:#dc2626; font-weight:700; }

/* Grids de campos */
.aud-row-2          { display:grid; grid-template-columns:1fr 1fr; gap:.85rem; margin-bottom:.85rem; }
.aud-row-3          { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.75rem; margin-bottom:.75rem; }
@media(max-width:640px) {
    .aud-row-2, .aud-row-3 { grid-template-columns:1fr; }
}

/* ── Cards de vítimas / agentes ────────────────────────────── */
.vitima-row,
.agente-row         { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;
                      padding:.85rem; margin-bottom:.75rem; }
.vitima-row         { border-left:3px solid #3b82f6; }
.agente-row         { border-left:3px solid #6b7280; }

.vitima-header      { display:flex; justify-content:space-between; align-items:center;
                      margin-bottom:.75rem; }
.vitima-header strong { font-size:.85rem; color:#1e293b; }

.btn-remover        { background:#fff; border:1px solid #fca5a5; color:#dc2626;
                      cursor:pointer; font-size:.8rem; font-weight:600; border-radius:5px;
                      padding:.2rem .5rem; transition:all .15s; }
.btn-remover:hover  { background:#fee2e2; border-color:#ef4444; }

.btn-add            { display:inline-flex; align-items:center; gap:.4rem;
                      background:#fff; color:#374151; border:1.5px dashed #9ca3af;
                      border-radius:7px; padding:.45rem 1rem; font-size:.83rem;
                      font-weight:600; cursor:pointer; transition:all .15s; margin-top:.25rem; }
.btn-add:hover      { border-color:#3b82f6; color:#2563eb; background:#eff6ff; }

/* ── Mensagens flash ───────────────────────────────────────── */
.flash              { padding:.8rem 1rem; border-radius:8px; margin-bottom:1rem;
                      font-size:.88rem; font-weight:500; display:flex;
                      align-items:flex-start; gap:.5rem; }
.flash-sucesso      { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; }
.flash-erro         { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
.flash-aviso        { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; }

/* ── Botões de ação finais ─────────────────────────────────── */
.aud-botoes         { display:flex; gap:.75rem; justify-content:flex-end;
                      margin-top:1.5rem; padding-top:1rem;
                      border-top:1px solid #e5e7eb; }

.btn-cancelar       { padding:.6rem 1.4rem; border-radius:7px; text-decoration:none;
                      border:1.5px solid #9ca3af; color:#374151; font-size:.9rem;
                      font-weight:600; background:#fff; transition:all .15s; }
.btn-cancelar:hover { border-color:#6b7280; background:#f9fafb; }

.btn-confirmar      { padding:.65rem 1.75rem; border-radius:7px; background:#16a34a;
                      color:#fff; border:none; font-size:.95rem; font-weight:700;
                      cursor:pointer; transition:background .2s;
                      box-shadow:0 2px 4px rgba(22,163,74,.25);
                      display:inline-flex; align-items:center; gap:.4rem; }
.btn-confirmar:hover { background:#15803d; box-shadow:0 3px 8px rgba(22,163,74,.35); }
.btn-confirmar:active { transform:translateY(1px); }
</style>

<script>
let vitimaIdx = 0;
let agenteIdx = 0;

function adicionarVitima() {
    const tpl = document.getElementById('tpl-vitima').innerHTML;
    const idx = vitimaIdx++;
    const html = tpl.replace(/__IDX__/g, idx).replace(/__NUM__/g, idx + 1);
    const div = document.createElement('div');
    div.innerHTML = html;
    document.getElementById('vitimas-container').appendChild(div.firstElementChild);
}

function adicionarAgente() {
    const tpl = document.getElementById('tpl-agente').innerHTML;
    const idx = agenteIdx++;
    const html = tpl.replace(/__IDX__/g, idx).replace(/__NUM__/g, idx + 1);
    const div = document.createElement('div');
    div.innerHTML = html;
    document.getElementById('agentes-container').appendChild(div.firstElementChild);
}

function removerItem(btn, tipo) {
    btn.closest(`.${tipo}-row`).remove();
}
</script>

<?= $this->endSection() ?>
