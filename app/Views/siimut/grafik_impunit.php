<style>
    /* Select2 dark mode support - with high specificity */
    [data-bs-theme="dark"] .select2-selection,
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection,
    [data-bs-theme="dark"] .select2-container--open .select2-selection {
        background-color: #2b3035 !important;
        border-color: #495057 !important;
    }

    [data-bs-theme="dark"] .select2-selection__rendered,
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection__rendered,
    [data-bs-theme="dark"] #select2-indicator_id-container,
    [data-bs-theme="dark"] #select2-tahun-container {
        color: #dee2e6 !important;
        background-color: transparent !important;
    }

    [data-bs-theme="dark"] .select2-dropdown,
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown {
        background-color: #2b3035 !important;
        border-color: #495057 !important;
    }

    [data-bs-theme="dark"] .select2-results__option,
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option {
        color: #dee2e6 !important;
    }

    [data-bs-theme="dark"] .select2-results__option--highlighted,
    [data-bs-theme="dark"] .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .select2-selection__arrow b,
    [data-bs-theme="dark"] .select2-selection--single .select2-selection__arrow::after {
        border-color: #dee2e6 transparent transparent transparent !important;
    }

    .chart-container {
        position: relative;
        height: 350px;
        background: var(--bs-body-bg);
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .indicator-info {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .target-badge {
        background: rgba(255, 255, 255, 0.2);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-grafik .card-header {
        background: var(--bs-tertiary-bg);
        border-bottom: 2px solid #28a745;
        font-weight: bold;
        color: var(--bs-body-color);
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
        background: var(--bs-body-bg);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
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
        0% {
            width: 1em;
            box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75);
        }

        35% {
            width: 4em;
            box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 0 1em rgba(111, 202, 220, 0.75);
        }

        70% {
            width: 1em;
            box-shadow: 0 -1em rgba(225, 20, 98, 0.75), 2em 1em rgba(111, 202, 220, 0.75);
        }

        100% {
            box-shadow: 2em -1em rgba(225, 20, 98, 0.75), -2em 1em rgba(111, 202, 220, 0.75);
        }
    }

    @keyframes after6 {
        0% {
            height: 1em;
            box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75);
        }

        35% {
            height: 4em;
            box-shadow: 1em 0 rgba(61, 184, 143, 0.75), -1em 0 rgba(233, 169, 32, 0.75);
        }

        70% {
            height: 1em;
            box-shadow: 1em -2em rgba(61, 184, 143, 0.75), -1em 2em rgba(233, 169, 32, 0.75);
        }

        100% {
            box-shadow: 1em 2em rgba(61, 184, 143, 0.75), -1em -2em rgba(233, 169, 32, 0.75);
        }
    }
</style>

<div class="container-fluid py-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">

            <div class="row align-items-center">

                <!-- Judul -->
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-graph-up me-2"></i>
                        Grafik Tren IMPUnit
                    </h5>
                    <small class="text-muted">
                        Monitoring indikator Investasi Manajemen Unit (IMPUnit)
                    </small>
                </div>

                <!-- Filter -->
                <div class="col-md-6">
                    <div class="row g-2 justify-content-end">

                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="tahun">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <select class="form-select form-select-sm select2" id="indicator_id" style="width:100%;">
                                <option value="">Pilih indikator...</option>
                                <?php foreach ($indicators as $ind): ?>
                                    <option value="<?= $ind->indicator_id ?>"
                                        <?= ($ind->indicator_id == $indicatorId) ? 'selected' : '' ?>>
                                        <?= esc($ind->indicator_element) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- <div class="row mb-4">
        <div class="col-12">
            <h4><i class="bi bi-graph-up me-2"></i>Grafik Tren Indikator Mutu Prioritas RS (IMPRS)</h4>
            <p class="text-muted">Visualisasi kinerja Indikator Mutu Prioritas RS - Tren Bulanan, Triwulan, Semester, dan Tahunan</p>
        </div>
    </div> -->

    <div id="indicatorInfo" class="card border-0 shadow-sm mb-3" style="display:none;">
        <div class="card-body py-3">

            <div class="row align-items-center">

                <div class="col-12">
                    <h3 id="indicatorName" class="fw-bold mb-3 text-primary"></h3>

                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-primary fs-6 py-2 px-3">
                            <i class="bi bi-bullseye me-1"></i>Target: <span id="indicatorTarget" class="fw-bold"></span>
                        </span>

                        <span class="badge bg-secondary fs-6 py-2 px-3">
                            <i class="bi bi-rulers me-1"></i>Satuan: <span id="indicatorUnitsLabel" class="fw-bold"></span>
                        </span>

                        <span id="statusBadge" class="badge fs-6 py-2 px-3"></span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div id="grafikContainer" style="display: none;">
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

        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-table me-2"></i>Detail Bulanan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tabelNumDenum" style="border-collapse: collapse;">
                                <thead>
                                    <tr class="text-white" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
                                        <th class="text-center py-2">Bulan</th>
                                        <th class="text-center py-2">Num</th>
                                        <th class="text-center py-2">Denum</th>
                                        <th class="text-center py-2">Capaian (%)</th>
                                        <th class="text-center py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelNumDenumBody">
                                </tbody>
                            </table>
                            <div class="mt-3 p-3 bg-light rounded border">
                                <strong class="text-muted"><i class="bi bi-info-circle me-1"></i> Keterangan:</strong>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span><span class="badge px-2 py-1" style="background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;">✅ Tercapai</span></span>
                                    <span><span class="badge px-2 py-1" style="background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2;">❌ Tidak</span></span>
                                    <span><span class="badge px-2 py-1" style="background-color: #f5f5f5; color: #999999; border: 1px solid #e0e0e0;">N/A</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-success border-2 shadow-sm h-100" style="border-width: 2px;">
                    <div class="card-body text-center py-3">
                        <h1 class="mb-1 text-success fw-bold display-4" id="summaryNilai">-</h1>
                        <small class="text-muted fw-semibold">Capaian</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary border-2 shadow-sm h-100" style="border-width: 2px;">
                    <div class="card-body text-center py-3">
                        <h1 class="mb-1 text-primary fw-bold display-4" id="summaryTarget">-</h1>
                        <small class="text-muted fw-semibold">Target</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning border-2 shadow-sm h-100" style="border-width: 2px;">
                    <div class="card-body text-center py-3">
                        <h1 class="mb-1 display-6" id="summaryTrend">-</h1>
                        <small class="text-muted fw-semibold">Trend</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-2 shadow-sm h-100" style="border-width: 2px;">
                    <div class="card-body text-center py-3">
                        <h1 class="mb-1 fw-bold display-6" id="summaryStatus">-</h1>
                        <small class="text-muted fw-semibold">Status</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div id="keterangan" class="alert alert-light border mb-0">
                    <i class="bi bi-info-circle me-1"></i> Pilih indikator untuk melihat keterangan.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Triwulan</div>
                    <div class="card-body">
                        <div class="chart-container"><canvas id="triwulanChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Semester</div>
                    <div class="card-body">
                        <div class="chart-container"><canvas id="semesterChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-grafik">
                    <div class="card-header"><i class="bi bi-calendar-range me-2"></i>Tren Per Tahun</div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;"><canvas id="perTahunChart"></canvas></div>
                    </div>
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

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
<script>
    var lineChart, triwulanChart, semesterChart, perTahunChart;
    var currentIndicatorData = null;

    $(document).ready(function() {
        // Initialize Select2 for both dropdowns
        var $tahun = $('#tahun').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Tahun',
            allowClear: false,
            width: '100%'
        });

        var $indicator = $('#indicator_id').select2({
            theme: 'bootstrap-5',
            placeholder: '--Pilih Indikator--',
            allowClear: true,
            width: '100%'
        });

        // Handle tahun change - reload graph without resetting indicator
        $tahun.on('change', function() {
            loadGrafik(true);
        });

        // Handle indicator change
        $indicator.on('change', function() {
            loadGrafik(false);
        });

        // Clear indicator selection on page load if no URL indicator_id param
        var urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('indicator_id')) {
            $indicator.val('').trigger('change');
        }
    });

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
        var maxData = Math.max.apply(null, dataArray.filter(function(x) {
            return x > 0;
        }));

        var isPercent = units.indexOf('%') !== -1;
        var isTime = units.indexOf('menit') !== -1 || units.indexOf('mnt') !== -1 || units.indexOf('menit') !== -1 || units.indexOf('detik') !== -1 || units.indexOf('dtk') !== -1;
        var isIndex = units.indexOf('indek') !== -1 || units.indexOf('indeks') !== -1 || units.indexOf('index') !== -1;

        if (isPercent) {
            var calcMax = target * 2;
            return Math.max(maxData * 1.2, calcMax);
        } else if (isTime) {
            return Math.max(maxData * 1.3, target * 1.3);
        } else if (isIndex) {
            return Math.max(maxData, target) * 1.3;
        }
        return Math.max(maxData * 1.2, target * 1.3);
    }

    function resetSummaryCards() {
        var summaryNilai = document.getElementById('summaryNilai');
        var summaryTarget = document.getElementById('summaryTarget');
        var summaryTrend = document.getElementById('summaryTrend');
        var summaryStatus = document.getElementById('summaryStatus');
        var keterangan = document.getElementById('keterangan');
        var tabelNumDenumBody = document.getElementById('tabelNumDenumBody');

        if (summaryNilai) summaryNilai.textContent = '-';
        if (summaryTarget) summaryTarget.textContent = '-';
        if (summaryTrend) {
            summaryTrend.textContent = '-';
            summaryTrend.className = 'mb-1';
        }
        if (summaryStatus) {
            summaryStatus.textContent = '-';
            summaryStatus.className = 'mb-1';
            if (summaryStatus.parentElement && summaryStatus.parentElement.parentElement) {
                summaryStatus.parentElement.parentElement.className = 'card border-2 shadow-sm h-100';
            }
        }
        if (keterangan) keterangan.innerHTML = '<i class="bi bi-info-circle me-1"></i> Pilih indikator untuk melihat keterangan.';
        if (tabelNumDenumBody) tabelNumDenumBody.innerHTML = '';
    }

    function loadGrafik(isYearChange) {
        var tahun = document.getElementById('tahun').value;
        var indicatorId = document.getElementById('indicator_id').value;

        // When year changes, keep indicator selection and reload graph
        // Only hide graph if no indicator is selected
        if (!indicatorId || indicatorId === '0' || indicatorId === '') {
            if (document.getElementById('indicatorInfo')) {
                document.getElementById('indicatorInfo').style.display = 'none';
            }
            if (document.getElementById('grafikContainer')) {
                document.getElementById('grafikContainer').style.display = 'none';
            }
            if (document.getElementById('loadingGrafik')) {
                document.getElementById('loadingGrafik').style.display = 'none';
            }
            resetSummaryCards();
            return;
        }

        if (document.getElementById('loadingGrafik')) {
            document.getElementById('loadingGrafik').style.display = 'block';
        }
        if (document.getElementById('grafikContainer')) {
            document.getElementById('grafikContainer').style.display = 'none';
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/grafik-impunit/data') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (document.getElementById('loadingGrafik')) {
                    document.getElementById('loadingGrafik').style.display = 'none';
                }
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    currentIndicatorData = response;
                    if (document.getElementById('grafikContainer')) {
                        document.getElementById('grafikContainer').style.display = 'block';
                    }
                    if (document.getElementById('indicatorInfo')) {
                        document.getElementById('indicatorInfo').style.display = 'block';
                    }
                    if (document.getElementById('indicatorName')) {
                        document.getElementById('indicatorName').textContent = response.indicator.indicator_element;
                    }
                    if (document.getElementById('indicatorTarget')) {
                        document.getElementById('indicatorTarget').textContent = response.indicator.indicator_target;
                    }
                    var units = response.indicator.indicator_units || '';
                    if (document.getElementById('indicatorUnits')) {
                        document.getElementById('indicatorUnits').textContent = units;
                    }
                    if (document.getElementById('indicatorUnitsLabel')) {
                        document.getElementById('indicatorUnitsLabel').textContent = units;
                    }
                    var statusBadge = document.getElementById('statusBadge');
                    var target = parseFloat(response.indicator.indicator_target || 0);
                    var nilai = response.tahunan.nilai || 0;

                    if (statusBadge) {
                        if (response.tahunan.tercap) {
                            statusBadge.textContent = 'TERCAPAI';
                            statusBadge.className = 'status-badge status-tercap';
                        } else {
                            statusBadge.textContent = 'TIDAK TERCAPAI';
                            statusBadge.className = 'status-badge status-tidak';
                        }
                    }

                    if (document.getElementById('summaryNilai')) {
                        document.getElementById('summaryNilai').textContent = nilai + ' ' + units;
                    }
                    if (document.getElementById('summaryTarget')) {
                        document.getElementById('summaryTarget').textContent = target + ' ' + units;
                    }

                    var perTahun = response.per_tahun;
                    var tahunKeys = Object.keys(perTahun).sort();
                    var lastYear = tahunKeys[tahunKeys.length - 2];
                    var trendEl = document.getElementById('summaryTrend');
                    var statusEl = document.getElementById('summaryStatus');

                    if (lastYear && perTahun[lastYear] && perTahun[lastYear].nilai) {
                        var currentYear = tahunKeys[tahunKeys.length - 1];
                        var diff = nilai - perTahun[lastYear].nilai;
                        var trendText = '';

                        if (diff > 0) {
                            trendEl.textContent = '⬆ +' + diff.toFixed(1) + '%';
                            trendEl.className = 'mb-1 text-success fw-bold';
                            trendText = 'mengalami peningkatan ' + diff.toFixed(1) + '%';
                        } else if (diff < 0) {
                            trendEl.textContent = '⬇ ' + Math.abs(diff).toFixed(1) + '%';
                            trendEl.className = 'mb-1 text-danger fw-bold';
                            trendText = 'mengalami penurunan ' + Math.abs(diff).toFixed(1) + '%';
                        } else {
                            trendEl.textContent = '➡ Stabil';
                            trendEl.className = 'mb-1 text-muted fw-bold';
                            trendText = 'stabil';
                        }

                        if (response.tahunan.tercap) {
                            statusEl.textContent = 'TERCAPAI ✓';
                            statusEl.className = 'mb-1 text-success fw-bold';
                            statusEl.parentElement.parentElement.classList.add('border-success');
                        } else {
                            statusEl.textContent = 'TIDAK TERCAPAI ✗';
                            statusEl.className = 'mb-1 text-danger fw-bold';
                            statusEl.parentElement.parentElement.classList.add('border-danger');
                        }

                        var lastYearNilai = perTahun[lastYear].nilai;
                        var analisasHtml = '<strong>Analisis:</strong><br><br>';

                        if (response.tahunan.tercap) {
                            analisasHtml += '<span class="text-success">';
                            analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' telah melampaui target ' + target + '% yang ditetapkan.<br><br>';

                            if (diff > 0) {
                                analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + '%), ';
                                analisasHtml += 'terdapat peningkatan sebesar ' + diff.toFixed(2) + '%.<br>';
                                analisasHtml += 'Peningkatan ini menunjukkan adanya perbaikan kinerja yang perlu dipertahankan.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Capaian indikator tetap baik dan berada di atas standar. ';
                                analisasHtml += 'Perlu dilakukan monitoring untuk menjaga konsistensi capaian indikator.</span>';
                            } else if (diff < 0) {
                                analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + '%), ';
                                analisasHtml += 'terdapat penurunan sebesar ' + Math.abs(diff).toFixed(2) + '%.<br>';
                                analisasHtml += 'Meskipun demikian, capaian masih berada di atas standar yang ditetapkan.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Capaian indikator masih dalam batas aman. ';
                                analisasHtml += 'Penurunan ini perlu dimonitor untuk menjaga konsistensi mutu pelayanan.</span>';
                            } else {
                                analisasHtml += 'Capaian relatif stabil dibandingkan tahun sebelumnya.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Capaian indikator tetap baik dan stabil. ';
                                analisasHtml += 'Perlu dilakukan monitoring untuk menjaga konsistensi capaian indikator.</span>';
                            }
                        } else {
                            analisasHtml += '<span class="text-danger">';
                            analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' belum mencapai target ' + target + '% yang ditetapkan.<br><br>';

                            if (diff > 0) {
                                analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + '%), ';
                                analisasHtml += 'terdapat peningkatan sebesar ' + diff.toFixed(2) + '%. ';
                                analisasHtml += 'Namun capaian belum memenuhi standar yang ditetapkan.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan evaluasi dan perencanaan perbaikan untuk meningkatkan capaian indikator.</span>';
                            } else if (diff < 0) {
                                analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + '%), ';
                                analisasHtml += 'terdapat penurunan sebesar ' + Math.abs(diff).toFixed(2) + '%.<br>';
                                analisasHtml += 'Penurunan ini memerlukan perhatian serius dan segera.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan analisis root cause dan rencana perbaikan segera untuk meningkatkan capaian indikator.</span>';
                            } else {
                                analisasHtml += 'Capaian stagnan dibandingkan tahun sebelumnya dan masih di bawah standar.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan analisis root cause dan rencana perbaikan untuk meningkatkan capaian indikator.</span>';
                            }
                        }
                        document.getElementById('keterangan').innerHTML = analisasHtml;
                    } else {
                        trendEl.textContent = '-';
                        trendEl.className = 'mb-1';
                        statusEl.textContent = '-';
                        statusEl.className = 'mb-1';
                        document.getElementById('keterangan').innerHTML = '<i class="bi bi-info-circle me-1"></i> Capaian indikator: ' + nilai + ' ' + units + ' | Target: ' + target + ' ' + units;
                    }

                    renderLineChart(response.bulanan, response.indicator);
                    renderTabelNumDenum(response.bulanan, response.indicator);
                    renderTriwulanChart(response.triwulan, response.indicator);
                    renderSemesterChart(response.semester, response.indicator);
                    renderPerTahunChart(response.per_tahun, response.indicator);
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
                    pointRadius: 6,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#28a745'
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
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxScale,
                        title: {
                            display: true,
                            text: 'Nilai ' + units
                        }
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
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxScale,
                        title: {
                            display: true,
                            text: 'Nilai ' + units
                        }
                    }
                }
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
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxScale,
                        title: {
                            display: true,
                            text: 'Nilai ' + units
                        }
                    }
                }
            }
        });
    }

    function renderTabelNumDenum(bulanan, indicator) {
        var bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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

            var tercap = false;
            var tidakAdaData = false;

            if (!item || denum === 0) {
                tidakAdaData = true;
            } else if (nilai !== null && nilai !== undefined) {
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

            var bgColor = '';
            var textColor = '';
            var statusBadge = '';

            if (tidakAdaData) {
                bgColor = 'background-color: #f5f5f5;';
                textColor = 'color: #999999;';
                statusBadge = '<span class="badge px-2 py-1" style="background-color: #e0e0e0; color: #757575; border: 1px solid #bdbdbd;">N/A</span>';
            } else if (tercap) {
                bgColor = 'background-color: #e8f5e9;';
                textColor = 'color: #2e7d32;';
                statusBadge = '<span class="badge px-2 py-1" style="background-color: #4caf50; color: white; border: 1px solid #388e3c;">✅ Tercapai</span>';
            } else {
                bgColor = 'background-color: #ffebee;';
                textColor = 'color: #c62828;';
                statusBadge = '<span class="badge px-2 py-1" style="background-color: #f44336; color: white; border: 1px solid #d32f2f;">❌ Tidak</span>';
            }

            html += '<tr style="' + bgColor + textColor + '">' +
                '<td class="text-center py-2" style="border: 1px solid #dee2e6;">' + bulanNames[i - 1] + '</td>' +
                '<td class="text-center py-2" style="border: 1px solid #dee2e6;">' + num + '</td>' +
                '<td class="text-center py-2" style="border: 1px solid #dee2e6;">' + denum + '</td>' +
                '<td class="text-center py-2" style="border: 1px solid #dee2e6;">' + (denum > 0 ? nilai.toFixed(2) : '0.00') + ' ' + units + '</td>' +
                '<td class="text-center py-2" style="border: 1px solid #dee2e6;">' + statusBadge + '</td></tr>';
        }

        var totalPersen = totalDenum > 0 ? (totalNum / totalDenum) * 100 : 0;
        var totalTercap = totalDenum > 0 && totalPersen >= target;

        var totalBg = totalTercap ? 'background: linear-gradient(135deg, #4caf50 0%, #43a047 100%); color: white;' : 'background: linear-gradient(135deg, #f44336 0%, #e53935 100%); color: white;';
        var totalStatusBadge = totalTercap ?
            '<span class="badge px-2 py-1" style="background-color: white; color: #2e7d32; text-transform: none; border: 1px solid #a5d6a7;">✅ Tercapai</span>' :
            '<span class="badge px-2 py-1" style="background-color: white; color: #c62828; text-transform: none; border: 1px solid #ef9a9a;">❌ Tidak Tercapai</span>';

        html += '<tr style="' + totalBg + '">' +
            '<td class="text-center py-2" style="border: 1px solid #dee2e6; font-weight: bold;">Total</td>' +
            '<td class="text-center py-2" style="border: 1px solid #dee2e6; font-weight: bold;">' + totalNum + '</td>' +
            '<td class="text-center py-2" style="border: 1px solid #dee2e6; font-weight: bold;">' + totalDenum + '</td>' +
            '<td class="text-center py-2" style="border: 1px solid #dee2e6; font-weight: bold;">' + totalPersen.toFixed(2) + ' ' + units + '</td>' +
            '<td class="text-center py-2" style="border: 1px solid #dee2e6; font-weight: bold;">' + totalStatusBadge + '</td></tr>';
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
                colors.push(hasData ? '#28a745' : '#6c757d');
            }
        }

        var target = parseFloat(indicator.indicator_target || 0);
        var units = indicator.indicator_units || '';
        var maxData = data.length > 0 ? Math.max.apply(null, data.filter(function(x) {
            return x > 0;
        })) : 0;
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
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxScale,
                        title: {
                            display: true,
                            text: 'Nilai ' + units
                        }
                    }
                }
            }
        });
    }

    <?php if ($indicatorId): ?>
        loadGrafik();
    <?php else: ?>
        if (document.getElementById('loadingGrafik')) {
            document.getElementById('loadingGrafik').style.display = 'none';
        }
    <?php endif; ?>
</script>