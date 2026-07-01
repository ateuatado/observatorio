<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title-admin">Relatórios e <span>Análises</span></h1>
        <p class="text-muted mb-0">Estatísticas consolidadas sobre os casos de violência policial registrados.</p>
    </div>
    <button onclick="window.print()" class="btn-ovpdh-outline">
        <i class="bi bi-printer"></i> Imprimir Dossiê
    </button>
</div>

<div class="row g-4 mb-4">
    <!-- Totais -->
    <div class="col-lg-3">
        <div class="d-flex flex-column gap-3">
            <div class="stat-card vermelho">
                <div class="stat-number"><?= array_sum($counts) ?></div>
                <div class="stat-label">Total Ocorrências</div>
            </div>
            <div class="stat-card preto">
                <div class="stat-number"><?= $counts['publicado'] ?></div>
                <div class="stat-label">Casos Públicos</div>
            </div>
            <div class="stat-card amarelo">
                <div class="stat-number"><?= $counts['em_revisao'] ?></div>
                <div class="stat-label">Fila de Revisão</div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Linha / Série Temporal -->
    <div class="col-lg-9">
        <div class="chart-card">
            <div class="chart-card-title"><i class="bi bi-graph-up"></i> Histórico de Registros (Últimos 24 meses)</div>
            <div style="height: 250px; position: relative;">
                <canvas id="chartEvolucaoLonga"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tabela Tipologia -->
    <div class="col-md-6">
        <div class="form-section">
            <div class="chart-card-title"><i class="bi bi-tag"></i> Distribuição por Tipologia de Violência</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle" style="font-size: .85rem;">
                    <thead>
                        <tr>
                            <th>Tipologia</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($byTipo as $t): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($t['tipo_violencia']) ?></td>
                            <td class="text-end"><?= $t['total'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabela Cidades -->
    <div class="col-md-6">
        <div class="form-section">
            <div class="chart-card-title"><i class="bi bi-geo-alt"></i> Concentração por Município</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle" style="font-size: .85rem;">
                    <thead>
                        <tr>
                            <th>Município</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($byCidade as $c): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($c['cidade']) ?></td>
                            <td class="text-end"><?= $c['total'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chartEvolucaoLonga').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= $meses ?>,
            datasets: [{
                label: 'Ocorrências por Mês',
                data: <?= $totaisMes ?>,
                backgroundColor: '#C0272D',
                borderRadius: 4
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
});
</script>
<?= $this->endSection() ?>
