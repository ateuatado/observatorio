<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('painel/ocorrencias') ?>" class="text-muted text-decoration-none" style="font-size: .85rem;">
        <i class="bi bi-arrow-left"></i> Voltar à Listagem
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <h1 class="page-title-admin">Detalhes do Caso <span>#<?= $ocorrencia['id'] ?></span></h1>
        <div class="d-flex gap-2">
            <?php if (auth()->user()->can('ocorrencias.edit')): ?>
            <a href="<?= base_url('painel/ocorrencias/' . $ocorrencia['id'] . '/editar') ?>" class="btn-ovpdh-outline">
                <i class="bi bi-pencil"></i> Editar Caso
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Informações Centrais -->
    <div class="col-lg-8">
        <div class="form-section">
            <h2 class="form-section-title"><span>1</span> Dados do Caso</h2>
            <div class="row g-3">
                <div class="col-12">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza);">Título</div>
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
                        <?= $ocorrencia['hora_ocorrencia'] ? ' às ' . date('H:i', strtotime($ocorrencia['hora_ocorrencia'])) : '' ?>
                    </div>
                </div>
                <div class="col-12">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza);">Localização</div>
                    <div class="fw-semibold mt-1">
                        <?= esc($ocorrencia['local_descricao']) ?> — <?= esc($ocorrencia['bairro']) ?>, <?= esc($ocorrencia['cidade']) ?>/<?= esc($ocorrencia['estado']) ?>
                    </div>
                </div>
                <div class="col-12">
                    <div style="font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza); margin-bottom: .25rem;">Descrição dos Fatos</div>
                    <p style="font-size:.9rem; line-height:1.75; color:var(--ovpdh-cinza-escuro);"><?= nl2br(esc($ocorrencia['descricao'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Vítimas vinculadas -->
        <div class="form-section">
            <h2 class="form-section-title"><span>2</span> Vítimas do Caso</h2>
            <?php if (empty($vitimas)): ?>
                <div class="text-muted py-2" style="font-size: .9rem;">Nenhuma vítima vinculada a este caso.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size: .85rem;">
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

        <!-- Agressores vinculados -->
        <div class="form-section">
            <h2 class="form-section-title"><span>3</span> Agressores (Agentes do Estado)</h2>
            <?php if (empty($agressores)): ?>
                <div class="text-muted py-2" style="font-size: .9rem;">Nenhum agressor cadastrado.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size: .85rem;">
                        <thead>
                            <tr>
                                <th>Agente</th>
                                <th>Órgão/Corporação</th>
                                <th>Batalhão</th>
                                <th>Identificado?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agressores as $a): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($a['tipo_agente'] ?? 'N/A') ?></td>
                                <td><?= esc($a['orgao'] ?? 'N/A') ?></td>
                                <td><?= esc($a['batalhao'] ?? 'N/A') ?></td>
                                <td><?= $a['identificado'] ? 'Sim (' . esc($a['identificacao']) . ')' : 'Não' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Barra Lateral: Status & Auditoria -->
    <div class="col-lg-4">
        <div class="form-section">
            <h2 class="form-section-title">Status Atual</h2>
            <div class="text-center py-3">
                <span class="badge-status badge-<?= $ocorrencia['status'] ?> fs-6 py-2 px-4"><?= $ocorrencia['status'] ?></span>
            </div>

            <!-- Formulário de revisão rápida para quem tem poder de aprovação -->
            <?php if (auth()->user()->can('ocorrencias.review')): ?>
            <div class="border-top pt-3 mt-3">
                <h3 style="font-size: .8rem; font-weight: 700; text-transform: uppercase; color: var(--ovpdh-cinza); margin-bottom: .75rem;">Mudar Status</h3>
                <form method="POST" action="<?= base_url('painel/ocorrencias/' . $ocorrencia['id'] . '/status') ?>" class="form-admin">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="rascunho" <?= $ocorrencia['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho (Rejeitar/Retornar)</option>
                            <option value="em_revisao" <?= $ocorrencia['status'] === 'em_revisao' ? 'selected' : '' ?>>Em Revisão</option>
                            <option value="aprovado" <?= $ocorrencia['status'] === 'aprovado' ? 'selected' : '' ?>>Aprovar (Interno)</option>
                            <option value="publicado" <?= $ocorrencia['status'] === 'publicado' ? 'selected' : '' ?>>Publicar (Área Pública)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="comentario" class="form-control form-control-sm" rows="3" placeholder="Insira observações ou justificativas de revisão..."></textarea>
                    </div>
                    <button type="submit" class="btn-ovpdh-primary w-100 justify-content-center btn-sm">Mudar Status</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Histórico de Auditoria</h2>
            <div class="timeline mt-2">
                <?php foreach ($revisoes as $rev): ?>
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
