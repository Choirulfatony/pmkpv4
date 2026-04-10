<style>
    .chart-container {
        position: relative;
        height: 350px;
        background: var(--bs-body-bg);
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
    .card-grafik .card-header { background: var(--bs-tertiary-bg); border-bottom: 2px solid #28a745; font-weight: bold; color: var(--bs-body-color); }
    .table-responsive { position: relative; }
    .overlay-wrapper { position: absolute; top: 0; left: 0; right: 0; bottom: 0; }
    .overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: var(--bs-body-bg); display: flex; justify-content: center; align-items: center; z-index: 9999; }
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
                <h4 id="indicatorName" class="mb-2 fw-bold"></h4>
                <div>
                    <span class="badge bg-secondary me-2">Target: <span id="indicatorTarget"></span> <span id="indicatorUnits"></span></span>
                    <span class="badge bg-secondary me-2">Satuan: <span id="indicatorUnitsLabel"></span></span>
                    <span id="statusBadge" class="badge"></span>
                </div>
            </div>
            <div class="col-md-4 text-end">
            </div>
        </div>
    </div>

    <div id="grafikContainer" style="display: none;">
        <!-- 🔥 1. Grafik Bulanan (UTAMA) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-graph-up me-2"></i>Tren Bulanan</div>
                    <div class="card-body">
                        <div class="chart-container"><canvas id="lineChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔥 1b. Tabel Detail Bulanan -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-table me-2"></i>Detail Bulanan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover" id="tabelNumDenum">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Bulan</th>
                                        <th class="text-center">Num</th>
                                        <th class="text-center">Denum</th>
                                        <th class="text-center">%</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelNumDenumBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔥 2. Ringkasan Tahunan (Card Kecil) -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-1" id="summaryNilai">-</h3>
                        <small class="text-muted">Capaian</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-1" id="summaryTarget">-</h3>
                        <small class="text-muted">Target</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-1" id="summaryTrend">-</h3>
                        <small class="text-muted">Trend</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keterangan / Penjelasan -->
        <div class="row mb-3">
            <div class="col-12">
                <div id="keterangan" class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i> Pilih indikator untuk melihat keterangan.
                </div>
            </div>
        </div>

        <!-- 🔥 3. Triwulan & Semester (Side by Side) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Triwulan</div>
                    <div class="card-body"><div class="chart-container"><canvas id="triwulanChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Semester</div>
                    <div class="card-body"><div class="chart-container"><canvas id="semesterChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <!-- 🔥 4. Per Tahun (History - di bawah) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-calendar-range me-2"></i>Tren Per Tahun</div>
                    <div class="card-body"><div class="chart-container" style="height: 250px;"><canvas id="perTahunChart"></canvas></div></div>
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    Chart.register(ChartDataLabels);
    var lineChart, triwulanChart, semesterChart, perTahunChart;
var currentIndicatorData = null;

window.addEventListener('themechange', function(e) {
    if (currentIndicatorData && currentIndicatorData.bulanan && currentIndicatorData.indicator) {
        renderLineChart(currentIndicatorData.bulanan, currentIndicatorData.indicator);
        renderTabelNumDenum(currentIndicatorData.bulanan, currentIndicatorData.indicator);
        renderTriwulanChart(currentIndicatorData.triwulan, currentIndicatorData.indicator);
        renderSemesterChart(currentIndicatorData.semester, currentIndicatorData.indicator);
        renderPerTahunChart(currentIndicatorData.per_tahun, currentIndicatorData.indicator);
    }
});

function getMaxScale(target, units, dataArray) {
    var maxData = Math.max.apply(null, dataArray.filter(function(x) { return x > 0; }));
    
    var isPercent = units.indexOf('%') !== -1;
    var isTime = units.indexOf('menit') !== -1 || units.indexOf('mnt') !== -1 || units.indexOf('menit') !== -1 || units.indexOf('detik') !== -1 || units.indexOf('dtk') !== -1;
    var isIndex = units.indexOf('indek') !== -1 || units.indexOf('indeks') !== -1 || units.indexOf('index') !== -1;
    
    if (isPercent) {
        // Persen: target * 2 atau max data * 1.2
        var calcMax = target * 2;
        return Math.max(maxData * 1.2, calcMax);
    } else if (isTime) {
        // Waktu: max data * 1.3 atau target * 1.3
        return Math.max(maxData * 1.3, target * 1.3);
    } else if (isIndex) {
        // Indeks: gunakan max(data, target) * 1.3
        return Math.max(maxData, target) * 1.3;
    }
    // Default: max(data * 1.2, target * 1.3)
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
                currentIndicatorData = response;
                document.getElementById('grafikContainer').style.display = 'block';
                document.getElementById('indicatorInfo').style.display = 'block';
                document.getElementById('indicatorName').textContent = response.indicator.indicator_element;
                document.getElementById('indicatorTarget').textContent = response.indicator.indicator_target;
                var units = response.indicator.indicator_units || '';
                document.getElementById('indicatorUnits').textContent = units;
                document.getElementById('indicatorUnitsLabel').textContent = units;
                var statusBadge = document.getElementById('statusBadge');
                var target = parseFloat(response.indicator.indicator_target || 0);
                var units = response.indicator.indicator_units || '';
                var nilai = response.tahunan.nilai || 0;
                
                if (response.tahunan.tercap) {
                    statusBadge.textContent = 'TERCAPAI';
                    statusBadge.className = 'status-badge status-tercap';
                } else {
                    statusBadge.textContent = 'TIDAK TERCAPAI';
                    statusBadge.className = 'status-badge status-tidak';
                }
                
                // Update summary cards
                document.getElementById('summaryNilai').textContent = nilai + ' ' + units;
                document.getElementById('summaryTarget').textContent = target + ' ' + units;
                
                // Calculate trend (bandingkan dengan nilai tahun lalu)
                var perTahun = response.per_tahun;
                var tahunKeys = Object.keys(perTahun).sort();
                var lastYear = tahunKeys[tahunKeys.length - 2]; // tahun lalu
                var trendEl = document.getElementById('summaryTrend');
                if (lastYear && perTahun[lastYear] && perTahun[lastYear].nilai) {
                    var currentYear = tahunKeys[tahunKeys.length - 1];
                    var diff = nilai - perTahun[lastYear].nilai;
                    if (diff > 0) {
                        trendEl.textContent = '⬆ +' + diff.toFixed(1) + '%';
                    } else if (diff < 0) {
                        trendEl.textContent = '⬇ ' + diff.toFixed(1) + '%';
                    } else {
                        trendEl.textContent = '➡ Stabil';
                    }
                    
                    // Generate Keterangan
                    var tercapStatus = response.tahunan.tercap ? 'telah melampaui target' : 'belum mencapai target';
                    var trendText = diff < 0 
                        ? 'mengalami penurunan ' + Math.abs(diff).toFixed(1) + '%' 
                        : (diff > 0 ? 'mengalami peningkatan ' + diff.toFixed(1) + '%' : 'stabil');
                    var keteranganHtml = response.tahunan.tercap 
                        ? '<span class="text-success fw-bold">Capaian indikator ' + nilai + ' ' + units + ' ' + tercapStatus + ' ' + target + ' ' + units + '. Indikator ' + trendText + ' dibanding tahun sebelumnya.</span>'
                        : '<span class="text-danger fw-bold">Capaian indikator ' + nilai + ' ' + units + ' ' + tercapStatus + ' ' + target + ' ' + units + '. Indikator ' + trendText + ' dibanding tahun sebelumnya. Perlu tindakan perbaikan.</span>';
                    document.getElementById('keterangan').innerHTML = '<i class="bi bi-info-circle me-1"></i> ' + keteranganHtml;
                } else {
                    trendEl.textContent = '-';
                    document.getElementById('keterangan').innerHTML = '<i class="bi bi-info-circle me-1"></i> Capaian indikator: ' + nilai + ' ' + units + ' | Target: ' + target + ' ' + units;
                }
                
                renderLineChart(response.bulanan, response.indicator);
                renderTabelNumDenum(response.bulanan, response.indicator);
                renderTriwulanChart(response.triwulan, response.indicator);
                renderSemesterChart(response.semester, response.indicator);
                renderPerTahunChart(response.per_tahun, response.indicator);
                // renderTahunanChart removed - now shown as summary cards
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
                pointRadius: 0,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#28a745',
                datalabels: {
                    display: true,
                    anchor: 'end',
                    align: 'top',
                    offset: -20,
                    font: { size: 10, weight: 'bold' },
                    color: '#28a745',
                    formatter: function(value) {
                        return value > 0 ? value : '';
                    }
                }
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

function renderTriwulanChart(triwulan, indicator) {
    var ctx = document.getElementById('triwulanChart').getContext('2d');
    var labels = ['TW 1', 'TW 2', 'TW 3', 'TW 4'];
    var data = [
        triwulan[1] ? triwulan[1].nilai : 0,
        triwulan[2] ? triwulan[2].nilai : 0,
        triwulan[3] ? triwulan[3].nilai : 0,
        triwulan[4] ? triwulan[4].nilai : 0
    ];
    var colors = [
        triwulan[1] && triwulan[1].nilai ? (triwulan[1].tercap ? '#28a745' : '#dc3545') : '#6c757d',
        triwulan[2] && triwulan[2].nilai ? (triwulan[2].tercap ? '#28a745' : '#dc3545') : '#6c757d',
        triwulan[3] && triwulan[3].nilai ? (triwulan[3].tercap ? '#28a745' : '#dc3545') : '#6c757d',
        triwulan[4] && triwulan[4].nilai ? (triwulan[4].tercap ? '#28a745' : '#dc3545') : '#6c757d'
    ];
    var target = parseFloat(indicator.indicator_target);
    var units = indicator.indicator_units || '';
    var maxScale = getMaxScale(target, units, data);

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
                label: 'Target (' + target + units + ')',
                data: Array(4).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderDash: [8, 4],
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, max: maxScale, title: { display: true, text: 'Nilai ' + units } } }
        }
    });
}

function renderSemesterChart(semester, indicator) {
    var ctx = document.getElementById('semesterChart').getContext('2d');
    var labels = ['Semester 1', 'Semester 2'];
    var data = [
        semester[1] ? semester[1].nilai : 0,
        semester[2] ? semester[2].nilai : 0
    ];
    var colors = [
        semester[1] && semester[1].nilai ? (semester[1].tercap ? '#28a745' : '#dc3545') : '#6c757d',
        semester[2] && semester[2].nilai ? (semester[2].tercap ? '#28a745' : '#dc3545') : '#6c757d'
    ];
    var target = parseFloat(indicator.indicator_target);
    var units = indicator.indicator_units || '';
    var maxScale = getMaxScale(target, units, data);

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
                label: 'Target (' + target + units + ')',
                data: Array(2).fill(target),
                type: 'line',
                borderColor: '#ffc107',
                borderDash: [8, 4],
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, max: maxScale, title: { display: true, text: 'Nilai ' + units } } }
        }
    });
}

function renderTabelNumDenum(bulanan, indicator) {
    var bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var target = parseFloat(indicator.indicator_target || 0);
    var units = indicator.indicator_units || '';
    var operator = indicator.indicator_target_calculation || '>=';
    var tbody = document.getElementById('tabelNumDenumBody');
    var html = '';
    
    var totalNum = 0;
    var totalDenum = 0;
    
    for (var i = 1; i <= 12; i++) {
        var item = bulanan[i];
        var num = item ? (item.num || 0) : 0;
        var denum = item ? (item.denum || 0) : 0;
        var nilai = item ? (item.nilai || 0) : 0;
        
        totalNum += num;
        totalDenum += denum;
        
        // Cek tercap berdasarkan operator dari indikator
        var tercap = false;
        if (item && item.nilai !== null && item.nilai !== undefined) {
            if (operator === '<=') {
                tercap = nilai <= target;
            } else if (operator === '<') {
                tercap = nilai < target;
            } else if (operator === '>') {
                tercap = nilai > target;
            } else {
                tercap = nilai >= target;
            }
        }
        
        var rowClass = tercap ? '' : 'table-danger';
        var statusBadge = tercap 
            ? '<span class="badge bg-success px-3">✔ Tercapai</span>' 
            : '<span class="badge bg-danger px-3">✖ Tidak</span>';
        
        html += '<tr class="' + rowClass + '">' +
            '<td class="text-center">' + bulanNames[i-1] + '</td>' +
            '<td class="text-center">' + num + '</td>' +
            '<td class="text-center">' + denum + '</td>' +
            '<td class="text-center fw-bold">' + nilai.toFixed(2) + ' ' + units + '</td>' +
            '<td class="text-center">' + statusBadge + '</td></tr>';
    }
    
    // Total row
    var totalPersen = totalDenum > 0 ? (totalNum / totalDenum) * 100 : 0;
    html += '<tr class="table-primary fw-bold">' +
        '<td class="text-center">Total</td>' +
        '<td class="text-center">' + totalNum + '</td>' +
        '<td class="text-center">' + totalDenum + '</td>' +
        '<td class="text-center">' + totalPersen.toFixed(2) + ' ' + units + '</td>' +
        '<td class="text-center">-</td></tr>';
    
    tbody.innerHTML = html;
}

function renderPerTahunChart(perTahun, indicator) {
    var ctx = document.getElementById('perTahunChart').getContext('2d');
    var labels = [];
    var data = [];
    var colors = [];
    
    for (var year in perTahun) {
        if (perTahun.hasOwnProperty(year)) {
            var nilai = perTahun[year].nilai;
            var hasData = nilai !== null && nilai !== undefined;
            
            labels.push(year);
            data.push(hasData ? nilai : null);
            // Use gray for no data, green if achieved, red if not achieved
            colors.push(hasData ? '#28a745' : '#6c757d');
        }
    }
    
    var target = parseFloat(indicator.indicator_target || 0);
    var units = indicator.indicator_units || '';
    var maxData = data.length > 0 ? Math.max.apply(null, data.filter(function(x) { return x > 0; })) : 0;
    var maxScale = Math.max(maxData * 1.3, target * 1.3);

    if (perTahunChart) perTahunChart.destroy();
    perTahunChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#28a745'
            }, {
                label: 'Target (' + target + ' ' + units + ')',
                data: Array(labels.length).fill(target),
                borderColor: '#ffc107',
                borderDash: [8, 4],
                fill: false,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, max: maxScale, title: { display: true, text: 'Nilai ' + units } } }
        }
    });
}

<?php if ($indicatorId): ?>
loadGrafik();
<?php else: ?>
document.getElementById('loadingGrafik').style.display = 'block';
<?php endif; ?>
</script>