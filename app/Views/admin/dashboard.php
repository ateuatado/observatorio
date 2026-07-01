<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card vermelho">
            <div class="stat-icon vermelho"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-number"><?= $counts['em_revisao'] ?></div>
            <div class="stat-label">Em Revisão</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card amarelo">
            <div class="stat-icon amarelo"><i class="bi bi-clock-history"></i></div>
            <div class="stat-number"><?= $counts['rascunho'] ?></div>
            <div class="stat-label">Rascunhos</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card verde">
            <div class="stat-icon verde"><i class="bi bi-check-circle"></i></div>
            <div class="stat-number"><?= $counts['aprovado'] ?></div>
            <div class="stat-label">Aprovados</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card azul">
            <div class="stat-icon azul"><i class="bi bi-globe"></i></div>
            <div class="stat-number"><?= $counts['publicado'] ?></div>
            <div class="stat-label">Publicados</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-card-title"><i class="bi bi-graph-up"></i> Histórico Semestral (Ocorrências)</div>
            <div style="height: 300px; position: relative;">
                <canvas id="chartEvolucao"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-card-title"><i class="bi bi-pie-chart"></i> Perfil Étnico/Racial Vítimas</div>
            <div style="height: 300px; position: relative;">
                <canvas id="chartRaca"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Últimas Ocorrências -->
    <div class="col-lg-7">
        <div class="form-section h-100">
            <div class="chart-card-title"><i class="bi bi-list-stars"></i> Atividades Recentes</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle" style="font-size: .85rem;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentes as $r): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('painel/ocorrencias/' . $r['id']) ?>" class="fw-semibold text-dark">
                                    <?= esc(character_limiter($r['titulo'], 40)) ?>
                                </a>
                            </td>
                            <td><?= $r['data_ocorrencia'] ? date('d/m/Y', strtotime($r['data_ocorrencia'])) : 'N/A' ?></td>
                            <td>
                                <span class="badge-status badge-<?= $r['status'] ?>"><?= $r['status'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fila de Revisão Rápida -->
    <div class="col-lg-5">
        <div class="form-section h-100">
            <div class="chart-card-title"><i class="bi bi-clipboard-check"></i> Aguardando Revisão</div>
            <?php if (empty($pendentes)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                    <p class="mt-2 mb-0">Nenhum caso pendente de revisão!</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendentes as $p): ?>
                    <a href="<?= base_url('painel/revisao/' . $p['id']) ?>" class="list-group-item list-group-item-action px-0 py-2">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 fw-bold text-dark" style="font-size:.85rem;"><?= esc(character_limiter($p['titulo'], 30)) ?></h6>
                            <small class="text-muted" style="font-size:.7rem;"><?= date('d/m', strtotime($p['created_at'])) ?></small>
                        </div>
                        <p class="mb-1 text-muted" style="font-size:.75rem;"><?= esc($p['tipo_violencia']) ?> · <?= esc($p['bairro']) ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gráfico de Linha / Evolução
    const ctxEvolucao = document.getElementById('chartEvolucao').getContext('2d');
    new Chart(ctxEvolucao, {
        type: 'line',
        data: {
            labels: <?= $meses ?>,
            datasets: [{
                label: 'Ocorrências Registradas',
                data: <?= $totaisMes ?>,
                borderColor: '#C0272D',
                backgroundColor: 'rgba(192, 39, 45, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Gráfico de Rosca / Raça
    const ctxRaca = document.getElementById('chartRaca').getContext('2d');
    new Chart(ctxRaca, {
        type: 'doughnut',
        data: {
            labels: <?= $racaLabels ?>,
            datasets: [{
                data: <?= $racaTotais ?>,
                backgroundColor: ['#111111', '#C0272D', '#6B7280', '#9CA3AF', '#E5E7EB'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
