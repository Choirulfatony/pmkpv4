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
    .table-responsive {
        position: relative;
    }
    .overlay-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--bs-body-bg, rgba(255, 255, 255, 0.8));
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    [data-bs-theme="dark"] .overlay {
        background: var(--bs-body-bg, rgba(33, 37, 41, 0.9));
    }
    .loader {
        width: 3em;
        height: 3em;
        transform: rotate(165deg);
    }
    .loader:before,
    .loader:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        display: block;
        width: 1em;
        height: 1em;
        border-radius: 0.5em;
        transform: translate(-50%, -50%);
    }
    .loader:before {
        animation: before8 2s infinite;
    }
    .loader:after {
        animation: after6 2s infinite;
    }
    @keyframes before8 {
        0% { width: 1em; box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
        35% { width: 4em; box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 0 1em rgba(111, 202, 220, 0.75); }
        70% { width: 1em; box-shadow: -2em -1em rgba(225, 20, 98, 0.75), 2em 1em rgba(111, 202, 220, 0.75); }
        100% { box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75); }
    }
    @keyframes after6 {
        0% { height: 1em; box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
        35% { height: 4em; box-shadow: 1em 0 rgba(61, 184, 143, 0.75), -1em 0 rgba(233, 169, 32, 0.75); }
        70% { height: 1em; box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75); }
        100% { box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75); }
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

        <!-- Gabungan Triwulan & Semester -->
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Tren Kinerja (Triwulan & Semester)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trenKinerjaChart"></canvas>
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
    <div id="loadingGrafik" class="card" style="display: none;">
        <div class="card-body text-center py-5">
            <div class="table-responsive" style="position: relative; min-height: 200px;">
                <div class="overlay-wrapper" id="loading_overlay_grafik">
                    <div class="overlay">
                        <i class="loader"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let lineChart, trenKinerjaChart, tahunanChart;

$(document).ready(function() {
    <?php if ($indicatorId): ?>
    loadGrafik();
    <?php else: ?>
    $('#loadingGrafik').show();
    <?php endif; ?>
});

function loadGrafik() {
    const tahun = $('#tahun').val();
    const indicatorId = $('#indicator_id').val();

    if (!indicatorId) {
        $('#indicatorInfo').hide();
        $('#grafikContainer').hide();
        $('#loadingGrafik').hide();
        return;
    }

    $('#loadingGrafik').show();
    $('#grafikContainer').hide();

    $.ajax({
        url: '<?= site_url('siimut/grafik-inm/data') ?>',
        type: 'POST',
        data: { tahun: tahun, indicator_id: indicatorId },
        success: function(response) {
            $('#loadingGrafik').hide();
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
            renderTrenKinerjaChart(response.triwulan, response.semester, response.indicator);
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
    
    // Convert object to array
    const data = [];
    for (let i = 1; i <= 12; i++) {
        data.push(bulanan[i] ? bulanan[i].nilai : null);
    }
    
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
                pointBackgroundColor: '#28a745',
                spanGaps: true
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
    const data = [triwulan[1]?.nilai, triwulan[2]?.nilai, triwulan[3]?.nilai, triwulan[4]?.nilai];
    const colors = [triwulan[1]?.tercap ? '#28a745' : '#dc3545', triwulan[2]?.tercap ? '#28a745' : '#dc3545', triwulan[3]?.tercap ? '#28a745' : '#dc3545', triwulan[4]?.tercap ? '#28a745' : '#dc3545'];
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
    const data = [semester[1]?.nilai, semester[2]?.nilai];
    const colors = [semester[1]?.tercap ? '#28a745' : '#dc3545', semester[2]?.tercap ? '#28a745' : '#dc3545'];
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
                borderWidth: 0,
                barThickness: 60
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, max: target * 1.3 }
            },
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: `Nilai: ${nilai}${indicator.indicator_units || ''} | Target: ${target}${indicator.indicator_units || ''} | ${tercapai ? 'TERCAPAI' : 'TIDAK TERCAPAI'}`,
                    font: { size: 14, weight: 'bold' },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.raw}${indicator.indicator_units || ''}`;
                        }
                    }
                }
            }
        }
    });
}

function renderTrenKinerjaChart(triwulan, semester, indicator) {
    const ctx = document.getElementById('trenKinerjaChart').getContext('2d');
    
    // Labels: TW1, TW2, TW3, TW4, S1, S2
    const labels = ['TW 1', 'TW 2', 'TW 3', 'TW 4', 'Sem 1', 'Sem 2'];
    
    // Data triwulan dan semester
    const dataTriwulan = [
        triwulan[1]?.nilai || 0,
        triwulan[2]?.nilai || 0,
        triwulan[3]?.nilai || 0,
        triwulan[4]?.nilai || 0
    ];
    
    const dataSemester = [
        semester[1]?.nilai || 0,
        semester[2]?.nilai || 0
    ];
    
    // Gabungkan data
    const data = [...dataTriwulan, ...dataSemester];
    
    // Warna: hijau jika tercapai, merah jika tidak
    const colorsTriwulan = triwulan.map(t => t?.tercap ? '#28a745' : '#dc3545');
    const colorsSemester = semester.map(s => s?.tercap ? '#20c997' : '#fd7e14');
    const colors = [...colorsTriwulan, ...colorsSemester];
    
    const target = indicator.indicator_target;

    if (trenKinerjaChart) trenKinerjaChart.destroy();

    trenKinerjaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                borderRadius: 8
            }, {
                label: 'Target',
                data: Array(6).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderWidth: 2,
                borderDash: [8, 4],
                pointRadius: 4,
                pointBackgroundColor: '#ffc107',
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                const idx = context.dataIndex;
                                let status = '';
                                if (idx < 4) {
                                    status = triwulan[idx + 1]?.tercap ? '✓ Tercapai' : '✗ Tidak';
                                } else {
                                    status = semester[idx - 3]?.tercap ? '✓ Tercapai' : '✗ Tidak';
                                }
                                return `Nilai: ${context.raw} ${indicator.indicator_units || ''} (${status})`;
                            }
                            return `Target: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nilai'
                    }
                }
            }
        }
    });
}
</script>