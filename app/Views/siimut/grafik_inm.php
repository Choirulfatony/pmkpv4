<style>
    .chart-container {
        position: relative;
        height: 350px;
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .indicator-info {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .target-badge {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
    }
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
    }
    .status-tercap {
        background: #28a745;
        color: white;
    }
    .status-tidak {
        background: #dc3545;
        color: white;
    }
    .card-grafik {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .card-grafik .card-header {
        background: #f8f9fa;
        border-bottom: 2px solid #28a745;
        font-weight: bold;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="bi bi-graph-up me-2"></i>Grafik Tren Indikator Nasional Mutu</h4>
            <p class="text-muted">Visualisasi kinerja indikator INM - Tren Bulanan, Triwulan, Semester, dan Tahunan</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">Pilih Tahun</label>
            <select class="form-select" id="tahun" onchange="loadGrafik()">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Pilih Indikator</label>
            <select class="form-select" id="indicator_id" onchange="loadGrafik()">
                <option value="">-- Pilih Indikator --</option>
                <?php foreach ($indicators as $ind): ?>
                    <option value="<?= $ind->indicator_id ?>" <?= ($ind->indicator_id == $indicatorId) ? 'selected' : '' ?>>
                        <?= esc($ind->indicator_element) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Indicator Info -->
    <div id="indicatorInfo" class="indicator-info" style="display: none;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 id="indicatorName" class="mb-1"></h5>
                <span class="target-badge">Target: <span id="indicatorTarget"></span> <span id="indicatorUnits"></span></span>
            </div>
            <div class="col-md-4 text-end">
                <span id="statusBadge" class="status-badge"></span>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div id="grafikContainer" style="display: none;">
        <!-- Line Chart - Bulanan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header">
                        <i class="bi bi-graph-up me-2"></i>Tren Bulanan (Line Chart)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart - Triwulan & Semester -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Perbandingan Triwulan (Bar Chart)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="triwulanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Perbandingan Semester (Bar Chart)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="semesterChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gauge/Bar - Tahunan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header">
                        <i class="bi bi-speedometer2 me-2"></i>Capaian Tahunan vs Target (Gauge Chart)
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="tahunanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="text-center py-5">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Pilih indikator untuk melihat grafik</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let lineChart, triwulanChart, semesterChart, tahunanChart;

$(document).ready(function() {
    <?php if ($indicatorId): ?>
    loadGrafik();
    <?php endif; ?>
});

function loadGrafik() {
    const tahun = $('#tahun').val();
    const indicatorId = $('#indicator_id').val();

    if (!indicatorId) {
        $('#indicatorInfo').hide();
        $('#grafikContainer').hide();
        $('#loading').show();
        return;
    }

    $('#loading').show();
    $('#grafikContainer').hide();

    $.ajax({
        url: '<?= site_url('siimut/grafik-inm/data') ?>',
        type: 'POST',
        data: { tahun: tahun, indicator_id: indicatorId },
        success: function(response) {
            $('#loading').hide();
            $('#grafikContainer').show();
            $('#indicatorInfo').show();

            // Info indikator
            $('#indicatorName').text(response.indicator.indicator_element);
            $('#indicatorTarget').text(response.indicator.indicator_target);
            $('#indicatorUnits').text(response.indicator.indicator_units);

            // Status
            const statusBadge = $('#statusBadge');
            if (response.tahunan.tercap) {
                statusBadge.text('TERCAPAI').removeClass('status-tidak').addClass('status-tercap');
            } else {
                statusBadge.text('TIDAK TERCAPAI').removeClass('status-tercap').addClass('status-tidak');
            }

            // Render charts
            renderLineChart(response.bulanan, response.indicator);
            renderTriwulanChart(response.triwulan, response.indicator);
            renderSemesterChart(response.semester, response.indicator);
            renderTahunanChart(response.tahunan, response.indicator);
        },
        error: function() {
            alert('Error mengambil data');
        }
    });
}

function renderLineChart(bulanan, indicator) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const data = bulanan.map(b => b.nilai);
    const target = indicator.indicator_target;

    if (lineChart) lineChart.destroy();

    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai Indikator',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#28a745'
            }, {
                label: 'Target',
                data: Array(12).fill(target),
                borderColor: '#dc3545',
                borderDash: [5, 5],
                fill: false,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function renderTriwulanChart(triwulan, indicator) {
    const ctx = document.getElementById('triwulanChart').getContext('2d');
    
    const labels = ['TW 1', 'TW 2', 'TW 3', 'TW 4'];
    const data = triwulan.map(t => t.nilai);
    const colors = triwulan.map(t => t.tercap ? '#28a745' : '#dc3545');
    const target = indicator.indicator_target;

    if (triwulanChart) triwulanChart.destroy();

    triwulanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: data,
                backgroundColor: colors,
                borderWidth: 0
            }, {
                label: 'Target',
                data: Array(4).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderDash: [5, 5],
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function renderSemesterChart(semester, indicator) {
    const ctx = document.getElementById('semesterChart').getContext('2d');
    
    const labels = ['Semester 1', 'Semester 2'];
    const data = semester.map(s => s.nilai);
    const colors = semester.map(s => s.tercap ? '#28a745' : '#dc3545');
    const target = indicator.indicator_target;

    if (semesterChart) semesterChart.destroy();

    semesterChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: data,
                backgroundColor: colors,
                borderWidth: 0
            }, {
                label: 'Target',
                data: Array(2).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderDash: [5, 5],
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function renderTahunanChart(tahunan, indicator) {
    const ctx = document.getElementById('tahunanChart').getContext('2d');
    
    const target = parseFloat(indicator.indicator_target);
    const nilai = tahunan.nilai || 0;
    const tercapai = tahunan.tercap;

    if (tahunanChart) tahunanChart.destroy();

    // Gauge simulation with horizontal bar
    tahunanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Capaian', 'Target'],
            datasets: [{
                data: [nilai, target],
                backgroundColor: [
                    tercapai ? '#28a745' : '#dc3545',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, max: target * 1.2 }
            },
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: `Nilai: ${nilai} | Target: ${target} | ${tercapai ? 'TERCAPAI' : 'TIDAK TERCAPAI'}`,
                    font: { size: 16 }
                }
            }
        }
    });
}
</script>