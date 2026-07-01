<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/revisao') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Fila
    </a>
    <h1 class="page-title-admin mt-2">Revisão Metodológica: Caso <span>#<?= $ocorrencia['id'] ?></span></h1>
    <p class="text-muted mb-0">Avalie os dados inseridos, faça anotações e tome uma ação de curadoria.</p>
</div>

<div class="row g-4">
    <!-- Informações Centrais -->
    <div class="col-lg-8">
        <div class="form-section">
            <h2 class="form-section-title"><span>1</span> Conteúdo do Registro</h2>
            <div class="row g-3">
                <div class="col-12">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza);">Título do Caso</div>
                    <h5 class="fw-bold mt-1 text-dark"><?= esc($ocorrencia['titulo']) ?></h5>
                </div>
                <div class="col-md-6">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza);">Tipo de Violência</div>
                    <span class="card-tipo-badge mt-1"><?= esc($ocorrencia['tipo_violencia']) ?></span>
                </div>
                <div class="col-md-6">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza);">Data / Hora</div>
                    <div class="fw-semibold mt-1">
                        <?= $ocorrencia['data_ocorrencia'] ? date('d/m/Y', strtotime($ocorrencia['data_ocorrencia'])) : 'N/A' ?>
                    </div>
                </div>
                <div class="col-12">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza); margin-bottom: .25rem;">Descrição dos Fatos</div>
                    <p style="font-size:.9rem; line-height:1.75; color:var(--ovpdh-cinza-escuro);"><?= nl2br(esc($ocorrencia['descricao'])) ?></p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title"><span>2</span> Vítimas Associadas (<?= count($vitimas) ?>)</h2>
            <?php if (empty($vitimas)): ?>
                <div class="text-muted py-2">Nenhuma vítima vinculada.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.85rem;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Idade</th>
                                <th>Gênero</th>
                                <th>Raça/Etnia</th>
                                <th>Desfecho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vitimas as $v): ?>
                            <tr>
                                <td class="fw-bold"><?= $v['anonimo'] ? 'Anônimo' : esc($v['nome']) ?></td>
                                <td><?= esc($v['idade'] ?? 'N/A') ?></td>
                                <td><?= esc($v['genero'] ?? 'N/A') ?></td>
                                <td><?= esc($v['raca_etnia'] ?? 'N/A') ?></td>
                                <td><span class="badge bg-danger"><?= esc($v['desfecho'] ?? 'N/A') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-section">
            <h2 class="form-section-title"><span>3</span> Agressores Associados (<?= count($agressores) ?>)</h2>
            <?php if (empty($agressores)): ?>
                <div class="text-muted py-2">Nenhum agressor cadastrado.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.85rem;">
                        <thead>
                            <tr>
                                <th>Corporação</th>
                                <th>Batalhão</th>
                                <th>Identificado?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agressores as $a): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($a['tipo_agente'] ?? 'N/A') ?> — <?= esc($a['orgao'] ?? 'N/A') ?></td>
                                <td><?= esc($a['batalhao'] ?? 'N/A') ?></td>
                                <td><?= $a['identificado'] ? 'Sim' : 'Não' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Interface de Decisão -->
    <div class="col-lg-4">
        <div class="form-section">
            <h2 class="form-section-title">Decisão de Curadoria</h2>
            <form method="POST" action="<?= base_url('painel/revisao/' . $ocorrencia['id'] . '/acao') ?>" class="form-admin">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="acao">Ação Metodológica</label>
                    <select name="acao" id="acao" class="form-select">
                        <option value="aprovar">Aprovar Registro (Interno)</option>
                        <option value="publicar">Publicar no Site Público</option>
                        <option value="rejeitar">Rejeitar (Retornar para Rascunho)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comentario">Comentários de Revisão</label>
                    <textarea name="comentario" id="comentario" class="form-control" rows="4" placeholder="Insira os apontamentos, correções necessárias ou justificativa..."></textarea>
                </div>
                <button type="submit" class="btn-ovpdh-primary w-100 justify-content-center">Aplicar Ação</button>
            </form>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Histórico de Alterações</h2>
            <div class="timeline mt-2">
                <?php foreach ($historico as $rev): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="fw-bold text-dark text-capitalize" style="font-size:.78rem;"><?= esc($rev['acao']) ?></div>
                        <?php if ($rev['comentario']): ?>
                        <div class="text-muted mt-1" style="font-size:.75rem; font-style:italic;">"<?= esc($rev['comentario']) ?>"</div>
                        <?php endif; ?>
                        <div class="timeline-date"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
