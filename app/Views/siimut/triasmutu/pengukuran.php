<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-bar-chart me-2"></i>
                        Pengukuran Indikator
                    </h5>
                    <small class="text-muted">Data hasil pengukuran indikator mutu per triwulan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Jenis Indikator</label>
            <select class="form-select form-select-sm" id="category_id" autocomplete="off">
                <option value="">Pilih Jenis...</option>
                <option value="4">INM</option>
                <option value="5">IMPRS</option>
                <option value="6">IMP Unit</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit</label>
            <select class="form-select form-select-sm" id="unit_id">
                <option value="">Pilih Unit...</option>
                <?php foreach ($units as $u): ?>
                    <option value="<?= $u['department_id'] ?>"><?= esc($u['department_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tahun</label>
            <select class="form-select form-select-sm" id="tahun">
                <?php foreach ($tahunList as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Triwulan</label>
            <select class="form-select form-select-sm" id="triwulan">
                <option value="1">Triwulan I (Jan-Mar)</option>
                <option value="2">Triwulan II (Apr-Jun)</option>
                <option value="3">Triwulan III (Jul-Sep)</option>
                <option value="4">Triwulan IV (Okt-Des)</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <button class="btn btn-primary" id="btnLoadIndicator">
                <i class="bi bi-search me-1"></i> Tampilkan Data
            </button>
        </div>
    </div>

    <div id="indicatorInfo" style="display:none;">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title text-break" id="indicatorName"></h5>
                <div class="card border-0 bg-light mt-4">
                    <div class="card-body py-3">
                        <div class="d-inline-block me-4">
                            <small class="text-muted d-block">Target</small>
                            <strong id="targetLabel">-</strong>
                        </div>
                        <div class="d-inline-block me-4">
                            <small class="text-muted d-block">Satuan</small>
                            <strong id="unitsLabel">-</strong>
                        </div>
                        <div class="d-inline-block me-4">
                            <small class="text-muted d-block">Nilai Triwulan</small>
                            <strong id="triwulanNilai">-</strong>
                        </div>
                        <div class="d-inline-block">
                            <small class="text-muted d-block">Status</small>
                            <strong id="statusLabel">-</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 btn-aksi" style="display:none;"></div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <i class="bi bi-table me-2"></i>Hasil Pengukuran Bulanan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="measurementTable" style="white-space:nowrap;">
                        <thead class="table-secondary" id="tableHeader"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-3" id="grafikSection" style="display:none;">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Grafik Tren Pengukuran Indikator Mutu
            </div>
            <div class="card-body">
                <h5 class="text-center fw-bold text-uppercase mb-3" id="chartTitle"></h5>
                <div class="chart-container" style="height:350px"><canvas id="lineChart"></canvas></div>
            </div>
        </div>

        <div class="card shadow-sm mt-3" id="aiAnalisisSection" style="display:none;">
            <div class="card-header d-flex align-items-center flex-wrap gap-2">
                <span><i class="bi bi-robot me-2"></i>Analisis AI</span>
                <button class="btn btn-sm btn-outline-primary ms-auto" id="btnAnalisisAI">
                    <i class="bi bi-stars"></i> Analisis dengan AI
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="btnTulisManual">
                    <i class="bi bi-pencil"></i> Tulis Manual
                </button>
            </div>
            <div class="card-body">
                <div id="aiAnalisisLoading" style="display:none;" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Menganalisis data dengan AI...</p>
                </div>
                <div id="aiAnalisisContent" class="py-2" style="white-space:pre-wrap;line-height:1.8;"></div>
                <div id="aiAnalisisError" class="alert alert-danger py-2" style="display:none;"></div>
            </div>
            <div id="aiManualForm" class="card-body border-top" style="display:none;">
                <label class="form-label fw-semibold"><i class="bi bi-pencil me-1"></i>Analisis Manual</label>
                <textarea class="form-control" id="manualAnalisisText" rows="8" placeholder="Tulis analisis Anda di sini..."></textarea>
                <button class="btn btn-sm btn-primary mt-2" id="btnGunakanManual">
                    <i class="bi bi-check-lg"></i> Gunakan Analisis Ini
                </button>
            </div>
            <div class="card-footer py-2">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Akurasi data 100% untuk perhitungan matematis (nilai, selisih, tren, fluktuasi) karena langsung dari database. Untuk kualitas analisis dan rekomendasi, tidak sebagus AI karena hanya berdasarkan aturan statis.
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
$(document).ready(function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;
    var userRole = <?= json_encode($userRole) ?>;
    var isKendali = !['ADMINISTRATOR', 'KOMITE'].includes(userRole);

    if (isKendali) {
        if (dokumenId) {
            $('#unit_id').val(<?= json_encode((string)($selected['unit_id'] ?? '')) ?>);
        } else {
            var firstReal = $('#unit_id option:first').next();
            if (firstReal.length) {
                $('#unit_id').val(firstReal.val());
            }
        }
    }

    $('#unit_id').select2({
        theme: 'bootstrap-5', width: '100%',
        placeholder: 'Pilih Unit...', allowClear: true,
        disabled: isKendali
    });
    $('#tahun').select2({ theme: 'bootstrap-5', width: '100%', minimumResultsForSearch: -1 });

    if (dokumenId) {
        if (!isKendali) {
            $('#unit_id').val(<?= json_encode((string)($selected['unit_id'] ?? '')) ?>).trigger('change.select2');
        }
        $('#category_id').val(<?= json_encode((string)($selected['indicator_category_id'] ?? '4')) ?>);
        $('#tahun').val(<?= json_encode((string)($selected['tahun'] ?? '')) ?>).trigger('change.select2');
        $('#triwulan').val(<?= json_encode((string)($selected['triwulan'] ?? '1')) ?>);
        loadIndicators(function() {
            <?php if ($selected): ?>
            $('#indicator_id').val(<?= json_encode((string)($selected['indicator_id'] ?? '')) ?>).trigger('change.select2');
            <?php endif; ?>
            loadPengukuran();
        });
    } else {
        if (!isKendali) {
            $('#unit_id').val('').trigger('change');
        }
        $('#category_id').val('');
        $('#indicator_id').remove();
        $('#indicatorInfo').hide();
    }

    $('#btnLoadIndicator').on('click', function() {
        loadPengukuran();
    });

    function loadPengukuran() {
        var unitId = $('#unit_id').val();
        var categoryId = $('#category_id').val();
        var tahun = $('#tahun').val();
        var triwulan = $('#triwulan').val();

        if (!unitId || !categoryId || !tahun || !triwulan) {
            toastWarning('Silakan pilih Unit, Tahun, dan Triwulan terlebih dahulu');
            return;
        }

        var indicatorId = $('#indicator_id').val();
        if (!indicatorId) {
            toastWarning('Silakan pilih Indikator terlebih dahulu');
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/get-pengukuran-data') ?>',
            method: 'POST',
            data: {
                unit_id: unitId,
                category_id: categoryId,
                indicator_id: indicatorId,
                tahun: tahun,
                triwulan: triwulan
            },
            beforeSend: function() {
                $('#btnLoadIndicator').prop('disabled', true).html('<i class="bi bi-hourglass"></i> Loading...');
            },
            success: function(res) {
                if (res.selected) {
                    dokumenId = res.selected.id;
                }
                renderPengukuran(res);
                renderChart(res.measurement, res.selected?.triwulan, res.selected?.tahun);
                if (dokumenId && res.selected?.analisis_ai) {
                    $('#aiAnalisisContent').html(res.selected.analisis_ai.replace(/\n/g, '<br>'));
                    $('#aiAnalisisSection').show();
                }
            },
            error: function() {
                toastError('Gagal memuat data');
            },
            complete: function() {
                $('#btnLoadIndicator').prop('disabled', false).html('<i class="bi bi-search me-1"></i> Tampilkan Data');
            }
        });
    }

    function renderPengukuran(res) {
        var m = res.measurement;
        if (!m || !m.indicator) {
            $('#indicatorInfo').hide();
            return;
        }

        var ind = m.indicator;
        $('#indicatorName').text(ind.indicator_element);
        $('#targetLabel').text(ind.indicator_target + ' ' + (ind.indicator_target_unit || ind.indicator_units || ''));
        $('#unitsLabel').text(ind.indicator_units || '-');

        var triwulanVal = m.nilai_triwulan !== null ? m.nilai_triwulan + ' ' + (ind.indicator_units || '') : 'Tidak ada data';
        $('#triwulanNilai').text(triwulanVal);

        var target = parseFloat(ind.indicator_target) || 0;
        var nilai = m.nilai_triwulan;

        if (nilai !== null) {
            var tercapai = nilai >= target;
            $('#statusLabel').html(tercapai
                ? '<span class="badge bg-success">Tercapai</span>'
                : '<span class="badge bg-danger">Belum Tercapai</span>');
        } else {
            $('#statusLabel').html('<span class="badge bg-secondary">Tidak ada data</span>');
        }

        var triwulanLabels = ['', 'I', 'II', 'III', 'IV'];
        var bulanSingkat = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var triwulanText = 'Triwulan ' + (triwulanLabels[$('#triwulan').val()] || '') + ' Tahun ' + $('#tahun').val();

        var bulanData = [];
        if (m.bulanan) {
            m.bulanan.forEach(function(b) {
                bulanData[b.bulan] = b;
            });
        }

        var bulanArr = [];
        var triwulanVal = parseInt($('#triwulan').val()) || 1;
        var startMonth = (triwulanVal - 1) * 3 + 1;
        var months = [];
        for (var i = 0; i < 3; i++) {
            months.push(startMonth + i);
        }

        months.forEach(function(b) {
            if (!bulanData[b]) bulanData[b] = { num: 0, denum: 0, nilai: null };
        });

        var numTotal = 0, denumTotal = 0, capaianTotal = 0, capaianCount = 0;
        months.forEach(function(b) {
            numTotal += bulanData[b].num;
            denumTotal += bulanData[b].denum;
            if (bulanData[b].nilai !== null) {
                capaianTotal += bulanData[b].nilai;
                capaianCount++;
            }
        });

        var avgNum = numTotal / 3;
        var avgDenum = denumTotal / 3;
        var avgCapaian = capaianCount > 0 ? (capaianTotal / capaianCount) : null;

        var units = ind.indicator_units || '';

        function fmt(v) {
            return v !== null && v !== undefined ? v : '-';
        }

        function fmtPct(v) {
            return v !== null && v !== undefined ? v.toFixed(2) + '%' : '-';
        }

        var colspanBulan = 6;
        var colspanRata = 2;

        var theadHtml =
            '<tr>' +
                '<th rowspan="2" colspan="2" style="vertical-align:middle;">Hasil Pengukuran</th>' +
                '<th colspan="' + colspanBulan + '">Bulan</th>' +
                '<th colspan="' + colspanRata + '">Rata-Rata ' + triwulanText + '</th>' +
            '</tr>' +
            '<tr>';
        months.forEach(function(b) {
            theadHtml += '<th colspan="2">' + (bulanSingkat[b] || 'B' + b) + '</th>';
        });
        theadHtml += '<th>Nilai rata-rata</th><th>Persentase rata-rata</th></tr>';

        var subHeaderHtml = '<tr class="table-light">' +
            '<th colspan="2"></th>';
        months.forEach(function() {
            subHeaderHtml += '<th>Num</th><th>%</th>';
        });
        subHeaderHtml += '<th></th><th></th></tr>';

        var bodyNumHtml = '<tr><th class="text-start">Num</th>';
        bodyNumHtml += '<th rowspan="2" style="vertical-align:middle;width:40px;"><div style="writing-mode:vertical-rl;transform:rotate(180deg);font-weight:bold;margin:0 auto;">Capaian</div></th>';
        months.forEach(function(b) {
            bodyNumHtml += '<td>' + fmt(bulanData[b].num) + '</td>' +
                '<td>' + fmtPct(bulanData[b].nilai) + '</td>';
        });
        bodyNumHtml += '<td>' + fmt(avgNum.toFixed(2)) + '</td>' +
            '<td>' + fmtPct(avgCapaian) + '</td></tr>';

        var bodyDenumHtml = '<tr><th class="text-start">Denum</th>';
        months.forEach(function(b) {
            bodyDenumHtml += '<td>' + fmt(bulanData[b].denum) + '</td>' +
                '<td>' + fmtPct(bulanData[b].nilai) + '</td>';
        });
        bodyDenumHtml += '<td>' + fmt(avgDenum.toFixed(2)) + '</td>' +
            '<td>' + fmtPct(avgCapaian) + '</td></tr>';

        $('#tableHeader').html(theadHtml + subHeaderHtml);
        $('#tableBody').html(bodyNumHtml + bodyDenumHtml);

        $('#indicatorInfo').show();

        if (dokumenId) {
            var btnHtml = '<div class="d-flex flex-wrap gap-2">' +
                '<a href="<?= site_url('siimut/trias-mutu/analisis-penyebab') ?>?dokumen_id=' + dokumenId + '" class="btn btn-warning btn-sm"><i class="bi bi-diagram-3"></i> Analisis Penyebab</a>' +
                '<a href="<?= site_url('siimut/trias-mutu/pdsa') ?>?dokumen_id=' + dokumenId + '" class="btn btn-success btn-sm"><i class="bi bi-arrow-repeat"></i> PDSA</a>' +
                '<a href="<?= site_url('siimut/trias-mutu/cetak') ?>?dokumen_id=' + dokumenId + '" class="btn btn-info btn-sm"><i class="bi bi-printer"></i> Cetak</a>' +
                '</div>';
            $('#indicatorInfo .card-footer.btn-aksi').html(btnHtml).show();
        } else {
            $('#indicatorInfo .card-footer.btn-aksi').hide();
        }

        showAiSection();
    }

    function loadIndicators(callback) {
        var unitId = $('#unit_id').val();
        var categoryId = $('#category_id').val();
        if (!unitId || !categoryId) return;

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/get-indicators') ?>',
            method: 'POST',
            data: {
                unit_id: unitId,
                category_id: categoryId,
                tahun: $('#tahun').val()
            },
            success: function(indicators) {
                var sel = $('#indicator_id');
                if (!sel.length) {
                    sel = $('<select class="form-select form-select-sm" id="indicator_id" style="display:none;">');
                    $('#triwulan').closest('.col-md-3').before(
                        '<div class="col-md-3"><label class="form-label">Indikator</label></div>'
                    );
                    $('#triwulan').closest('.col-md-3').prev('.col-md-3').append(sel);
                }
                sel.empty().append('<option value="">Pilih Indikator...</option>');
                indicators.forEach(function(ind) {
                    sel.append('<option value="' + ind.indicator_id + '">' + ind.indicator_element + '</option>');
                });
                sel.show();
                sel.select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Pilih Indikator...', allowClear: true });
                if (typeof callback === 'function') callback(indicators);
            }
        });
    }

    var chart = null;
    function renderChart(m, triwulan, tahun) {
        if (!m || !m.bulanan || !m.bulanan.length) {
            $('#grafikSection').hide();
            return;
        }
        $('#grafikSection').show();

        var twLabels = ['', 'I', 'II', 'III', 'IV'];
        var twLabel = twLabels[triwulan] || '';
        var bulanPendek = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
        var labels = [
            bulanPendek[m.bulanan[0].bulan - 1],
            bulanPendek[m.bulanan[1].bulan - 1],
            bulanPendek[m.bulanan[2].bulan - 1],
            'RATA-RATA TW ' + twLabel + ' ' + (tahun || '')
        ];
        var standarVal = parseFloat(m.target) || 0;
        var standarData = [standarVal, standarVal, standarVal, standarVal];
        var avgVal = (m.nilai_triwulan !== null && m.nilai_triwulan !== undefined && !isNaN(m.nilai_triwulan))
            ? Math.round(m.nilai_triwulan * 100) / 100 : 0;
        var capaianData = [
            m.bulanan[0].nilai || 0,
            m.bulanan[1].nilai || 0,
            m.bulanan[2].nilai || 0,
            avgVal
        ];
        var maxY = Math.max(standarVal, ...capaianData) * 1.3 || 12;
        maxY = Math.ceil(maxY / 2) * 2;
        if (maxY < 2) maxY = 2;

        $('#chartTitle').text(m.indicator?.indicator_element || '');

        if (chart) chart.destroy();
        var ctx = document.getElementById('lineChart');
        if (!ctx) return;

        chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'STANDAR',
                        data: standarData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        borderDash: [],
                        fill: false,
                        tension: 0,
                        pointStyle: 'diamond',
                        pointRadius: 5,
                        pointBackgroundColor: '#3498db'
                    },
                    {
                        label: 'CAPAIAN',
                        data: capaianData,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 2,
                        borderDash: [],
                        fill: false,
                        tension: 0,
                        pointStyle: 'rect',
                        pointRadius: 5,
                        pointBackgroundColor: '#e74c3c'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: { size: 10, weight: 'bold' },
                        formatter: function(v) { return v + '%'; }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    },
                    y: {
                        min: 0,
                        max: maxY,
                        ticks: { stepSize: 2, callback: function(v) { return v + '%'; } },
                        grid: { display: true, drawBorder: false }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    $('#unit_id, #category_id').on('change', function() {
        loadIndicators(function() {
            toastWarning('Silakan pilih Indikator terlebih dahulu');
        });
    });

    function getFilterParams() {
        return {
            unit_id: $('#unit_id').val(),
            category_id: $('#category_id').val(),
            indicator_id: $('#indicator_id').val(),
            tahun: $('#tahun').val(),
            triwulan: $('#triwulan').val()
        };
    }

    function showAiSection() {
        var p = getFilterParams();
        if (p.unit_id && p.category_id && p.indicator_id && p.tahun && p.triwulan && dokumenId) {
            $('#aiAnalisisSection').show();
            $('#aiAnalisisContent').html('');
            $('#aiAnalisisError').hide();
        } else {
            $('#aiAnalisisSection').hide();
        }
    }

    function saveAnalisisAi(text) {
        if (!dokumenId) return;
        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/save-analisis-ai') ?>',
            method: 'POST',
            data: { dokumen_id: dokumenId, analisis_ai: text },
            success: function(res) {
                if (res.success) toastSuccess('Analisis tersimpan');
            }
        });
    }

    $('#btnAnalisisAI').on('click', function() {
        var p = getFilterParams();
        if (!p.unit_id || !p.category_id || !p.indicator_id || !p.tahun || !p.triwulan) {
            toastWarning('Silakan pilih semua filter terlebih dahulu');
            return;
        }

        $('#aiManualForm').hide();
        $('#aiAnalisisLoading').show();
        $('#aiAnalisisContent').html('');
        $('#aiAnalisisError').hide();
        $('#btnAnalisisAI').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/analisis-ai') ?>',
            method: 'POST',
            data: p,
            success: function(res) {
                $('#aiAnalisisLoading').hide();
                if (res.success) {
                    $('#aiAnalisisContent').html(res.analisis);
                    saveAnalisisAi(res.analisis);
                } else {
                    $('#aiAnalisisError').text(res.message).show();
                }
            },
            error: function() {
                $('#aiAnalisisLoading').hide();
                $('#aiAnalisisError').text('Gagal terhubung ke server').show();
            },
            complete: function() {
                $('#btnAnalisisAI').prop('disabled', false);
            }
        });
    });

    $('#btnTulisManual').on('click', function() {
        var existingText = $('#aiAnalisisContent').text().trim();
        $('#aiManualForm').toggle();
        if ($('#aiManualForm').is(':visible')) {
            $('#manualAnalisisText').val(existingText || $('#aiAnalisisContent').text() || '');
            $('#btnAnalisisAI').prop('disabled', true);
        } else {
            $('#btnAnalisisAI').prop('disabled', false);
        }
    });

    $('#btnGunakanManual').on('click', function() {
        var text = $('#manualAnalisisText').val().trim();
        if (!text) {
            toastWarning('Tulis analisis terlebih dahulu');
            return;
        }
        $('#aiAnalisisContent').html(text.replace(/\n/g, '<br>'));
        $('#aiManualForm').hide();
        $('#btnAnalisisAI').prop('disabled', false);
        saveAnalisisAi(text);
    });
});
</script>
