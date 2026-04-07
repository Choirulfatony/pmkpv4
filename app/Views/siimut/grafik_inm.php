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
    .status-tercap { background: #28a745; color: white; }
    .status-tidak { background: #dc3545; color: white; }
    .card-grafik { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .card-grafik .card-header { background: #f8f9fa; border-bottom: 2px solid #28a745; font-weight: bold; }
    .table-responsive { position: relative; }
    .overlay-wrapper { position: absolute; top: 0; left: 0; right: 0; bottom: 0; }
    .overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: var(--bs-body-bg, rgba(255,255,255,0.8)); display: flex; justify-content: center; align-items: center; z-index: 9999; }
    [data-bs-theme="dark"] .overlay { background: var(--bs-body-bg, rgba(33,37,41,0.9)); }
    .loader { width: 3em; height: 3em; transform: rotate(165deg); }
    .loader:before, .loader:after { content:""; position: absolute; top: 50%; left: 50%; display: block; width: 1em; height: 1em; border-radius: 0.5em; transform: translate(-50%, -50%); }
    .loader:before { animation: before8 2s infinite; }
    .loader:after { animation: after6 2s infinite; }
    @keyframes before8 { 0% { width: 1em; box-shadow: 2em -1em rgba(225,20,98,0.75), -2em 1em rgba(111,202,220,0.75); } 35% { width: 4em; box-shadow: 0 -1em rgba(225,20,98,0.75), 0 1em rgba(111,202,220,0.75); } 70% { width: 1em; box-shadow: -2em -1em rgba(225,20,98,0.75), 2em 1em rgba(111,202,220,0.75); } 100% { box-shadow: 2em -1em rgba(225,20,98,0.75), -2em 1em rgba(111,202,220,0.75); } }
    @keyframes after6 { 0% { height: 1em; box-shadow: 1em 2em rgba(61,184,143,0.75), -1em -2em rgba(233,169,32,0.75); } 35% { height: 4em; box-shadow: 1em 0 rgba(61,184,143,0.75), -1em 0 rgba(233,169,32,0.75); } 70% { height: 1em; box-shadow: 1em -2em rgba(61,184,143,0.75), -1em 2em rgba(233,169,32,0.75); } 100% { box-shadow: 1em 2em rgba(61,184,143,0.75), -1em -2em rgba(233,169,32,0.75); } }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="bi bi-graph-up me-2"></i>Grafik Tren Indikator Nasional Mutu</h4>
            <p class="text-muted">Visualisasi kinerja indikator INM - Tren Bulanan, Triwulan, Semester, dan Tahunan</p>
        </div>
    </div>

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
                    <option value="<?= $ind->indicator_id ?>" <?= ($ind->indicator_id == $indicatorId) ? 'selected' : '' ?>><?= esc($ind->indicator_element) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

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

    <div id="grafikContainer" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-graph-up me-2"></i>Tren Bulanan (Line Chart)</div>
                    <div class="card-body"><div class="chart-container"><canvas id="lineChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Tren Kinerja (Triwulan & Semester)</div>
                    <div class="card-body"><div class="chart-container"><canvas id="trenKinerjaChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-speedometer2 me-2"></i>Capaian Tahunan vs Target</div>
                    <div class="card-body"><div class="chart-container" style="height: 250px;"><canvas id="tahunanChart"></canvas></div></div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingGrafik" class="card" style="display: none;">
        <div class="card-body text-center py-5">
            <div class="table-responsive" style="position: relative; min-height: 200px;">
                <div class="overlay-wrapper" id="loading_overlay_grafik">
                    <div class="overlay"><i class="loader"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var lineChart, trenKinerjaChart, tahunanChart;

function getMaxScale(target, units, dataArray) {
    var maxData = Math.max.apply(null, dataArray.filter(function(x) { return x > 0; }));
    if (units.indexOf('%') !== -1) {
        var calcMax = target * 2;
        return Math.max(maxData * 1.2, calcMax);
    }
    return Math.max(maxData * 1.2, target * 1.3);
}

function loadGrafik() {
    var tahun = document.getElementById('tahun').value;
    var indicatorId = document.getElementById('indicator_id').value;

    if (!indicatorId) {
        document.getElementById('indicatorInfo').style.display = 'none';
        document.getElementById('grafikContainer').style.display = 'none';
        document.getElementById('loadingGrafik').style.display = 'none';
        return;
    }

    document.getElementById('loadingGrafik').style.display = 'block';
    document.getElementById('grafikContainer').style.display = 'none';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= site_url('siimut/grafik-inm/data') ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            document.getElementById('loadingGrafik').style.display = 'none';
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById('grafikContainer').style.display = 'block';
                document.getElementById('indicatorInfo').style.display = 'block';
                document.getElementById('indicatorName').textContent = response.indicator.indicator_element;
                document.getElementById('indicatorTarget').textContent = response.indicator.indicator_target;
                document.getElementById('indicatorUnits').textContent = response.indicator.indicator_units;
                var statusBadge = document.getElementById('statusBadge');
                if (response.tahunan.tercap) {
                    statusBadge.textContent = 'TERCAPAI';
                    statusBadge.className = 'status-badge status-tercap';
                } else {
                    statusBadge.textContent = 'TIDAK TERCAPAI';
                    statusBadge.className = 'status-badge status-tidak';
                }
                renderLineChart(response.bulanan, response.indicator);
                renderTrenKinerjaChart(response.triwulan, response.semester, response.indicator);
                renderTahunanChart(response.tahunan, response.indicator);
            } else {
                alert('Error mengambil data');
            }
        }
    };
    xhr.send('tahun=' + tahun + '&indicator_id=' + indicatorId);
}

function renderLineChart(bulanan, indicator) {
    var ctx = document.getElementById('lineChart').getContext('2d');
    var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var data = [];
    for (var i = 1; i <= 12; i++) {
        data.push(bulanan[i] ? bulanan[i].nilai : 0);
    }
    var target = parseFloat(indicator.indicator_target);
    var units = indicator.indicator_units || '';
    var maxScale = getMaxScale(target, units, data);

    if (lineChart) lineChart.destroy();
    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai Aktual',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 6
            }, {
                label: 'Target (' + target + units + ')',
                data: Array(12).fill(target),
                borderColor: '#ffc107',
                borderDash: [8, 4],
                fill: false,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: maxScale,
                    title: { display: true, text: 'Nilai ' + units }
                }
            }
        }
    });
}

function renderTrenKinerjaChart(triwulan, semester, indicator) {
    var ctx = document.getElementById('trenKinerjaChart').getContext('2d');
    var labels = ['TW 1', 'TW 2', 'TW 3', 'TW 4', 'Sem 1', 'Sem 2'];
    var data = [
        triwulan[1] ? triwulan[1].nilai : 0,
        triwulan[2] ? triwulan[2].nilai : 0,
        triwulan[3] ? triwulan[3].nilai : 0,
        triwulan[4] ? triwulan[4].nilai : 0,
        semester[1] ? semester[1].nilai : 0,
        semester[2] ? semester[2].nilai : 0
    ];
    var colors = [
        triwulan[1] && triwulan[1].tercap ? '#28a745' : '#dc3545',
        triwulan[2] && triwulan[2].tercap ? '#28a745' : '#dc3545',
        triwulan[3] && triwulan[3].tercap ? '#28a745' : '#dc3545',
        triwulan[4] && triwulan[4].tercap ? '#28a745' : '#dc3545',
        semester[1] && semester[1].tercap ? '#20c997' : '#fd7e14',
        semester[2] && semester[2].tercap ? '#20c997' : '#fd7e14'
    ];
    var target = parseFloat(indicator.indicator_target);
    var units = indicator.indicator_units || '';
    var maxScale = getMaxScale(target, units, data);

    if (trenKinerjaChart) trenKinerjaChart.destroy();
    trenKinerjaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: data,
                backgroundColor: colors,
                borderWidth: 0
            }, {
                label: 'Target (' + target + units + ')',
                data: Array(6).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderDash: [8, 4],
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: maxScale,
                    title: { display: true, text: 'Nilai ' + units }
                }
            }
        }
    });
}

function renderTahunanChart(tahunan, indicator) {
    var ctx = document.getElementById('tahunanChart').getContext('2d');
    var target = parseFloat(indicator.indicator_target);
    var nilai = tahunan.nilai || 0;
    var tercapai = tahunan.tercap;
    var units = indicator.indicator_units || '';

    if (tahunanChart) tahunanChart.destroy();
    tahunanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Capaian', 'Target'],
            datasets: [{
                data: [nilai, target],
                backgroundColor: [tercapai ? '#28a745' : '#dc3545', '#6c757d']
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Nilai: ' + nilai + units + ' | Target: ' + target + units + ' | ' + (tercapai ? 'TERCAPAI' : 'TIDAK TERCAPAI')
                }
            }
        }
    });
}

<?php if ($indicatorId): ?>
loadGrafik();
<?php else: ?>
document.getElementById('loadingGrafik').style.display = 'block';
<?php endif; ?>
</script>