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
                <h5 class="card-title" id="indicatorName"></h5>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Target</small>
                        <strong id="targetLabel">-</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Satuan</small>
                        <strong id="unitsLabel">-</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Nilai Triwulan</small>
                        <strong id="triwulanNilai">-</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Status</small>
                        <strong id="statusLabel">-</strong>
                    </div>
                </div>
            </div>
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
                <i class="bi bi-bar-chart me-2"></i>Grafik Nilai Bulanan
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="lineChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.register({
    id: 'customPk',
    afterDraw: function(c) {
        var cfg = c.config.options.custom || {};
        var tgt = parseFloat(cfg.target) || 0;
        var ctx = c.ctx;
        var meta = c.getDatasetMeta(0);
        if (meta && meta.data) {
            meta.data.forEach(function(pt, i) {
                var v = c.data.datasets[0].data[i];
                if (v === null || v === undefined) return;
                ctx.fillStyle = '#333';
                ctx.font = '10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(v + '%', pt.x, pt.y - 10);
            });
        }
        var yTgt = c.scales.y.getPixelForValue(tgt);
        ctx.save();
        ctx.setLineDash([6, 4]);
        ctx.strokeStyle = '#e74c3c';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(c.chartArea.left, yTgt);
        ctx.lineTo(c.chartArea.right, yTgt);
        ctx.stroke();
        ctx.restore();
        ctx.fillStyle = '#e74c3c';
        ctx.font = 'bold 10px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('Standar: ' + tgt + '%', c.chartArea.right - 4, yTgt - 6);
    }
});

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
                renderChart(res.measurement);
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
            var btnHtml = '<a href="<?= site_url('siimut/trias-mutu/analisis-penyebab') ?>?dokumen_id=' + dokumenId + '" class="btn btn-warning btn-sm ms-2"><i class="bi bi-diagram-3"></i> Analisis Penyebab</a>' +
                '<a href="<?= site_url('siimut/trias-mutu/pdsa') ?>?dokumen_id=' + dokumenId + '" class="btn btn-success btn-sm ms-2"><i class="bi bi-arrow-repeat"></i> PDSA</a>' +
                '<a href="<?= site_url('siimut/trias-mutu/cetak') ?>?dokumen_id=' + dokumenId + '" class="btn btn-info btn-sm ms-2"><i class="bi bi-printer"></i> Cetak</a>';
            $('#indicatorInfo .card-body .row').first().append(
                '<div class="col-12 mt-2">' + btnHtml + '</div>'
            );
        }
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
    function renderChart(m) {
        if (!m || !m.bulanan || !m.bulanan.length) {
            $('#grafikSection').hide();
            return;
        }
        $('#grafikSection').show();

        var bulanSingkat = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var labels = [], values = [];
        m.bulanan.forEach(function(b) {
            labels.push(bulanSingkat[b.bulan] || 'B' + b.bulan);
            values.push(b.nilai);
        });

        if (chart) chart.destroy();
        var ctx = document.getElementById('lineChart');
        if (!ctx) return;

        var target = m.target || 0;
        var avgTriwulan = m.nilai_triwulan;
        var maxVal = Math.max(...values, target, avgTriwulan || 0) * 1.3 || 100;

        var datasets = [{
            label: 'Nilai (v2 ' + (m.indicator?.indicator_units || '') + ')',
            data: values,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointRadius: 4
        }];

        if (avgTriwulan !== null && avgTriwulan !== undefined && !isNaN(avgTriwulan)) {
            var avgData = labels.map(function() { return avgTriwulan; });
            datasets.push({
                label: 'Rata-Rata Triwulan: ' + (avgTriwulan % 1 === 0 ? avgTriwulan : avgTriwulan.toFixed(2)) + '%',
                data: avgData,
                borderColor: '#3498db',
                borderWidth: 2,
                borderDash: [4, 4],
                fill: false,
                tension: 0,
                pointRadius: 0,
                pointHitRadius: 0
            });
        }

        chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                custom: { target: target, avgTriwulan: avgTriwulan },
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: maxVal, title: { display: true, text: 'Capaian (%)' } } }
            }
        });
    }

    $('#unit_id, #category_id').on('change', function() {
        loadIndicators(function() {
            toastWarning('Silakan pilih Indikator terlebih dahulu');
        });
    });
});
</script>
