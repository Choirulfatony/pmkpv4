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
            <select class="form-select form-select-sm" id="category_id">
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
                    <table class="table table-bordered" id="measurementTable">
                        <thead class="table-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Numerator</th>
                                <th>Denominator</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="table-warning">
                            <tr>
                                <th>Total Triwulan</th>
                                <th id="totalNum">0</th>
                                <th id="totalDenum">0</th>
                                <th id="totalNilai">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var dokumenId = <?= json_encode($selected['id'] ?? null) ?>;

    if (dokumenId) {
        $('#unit_id').val(<?= json_encode((string)($selected['unit_id'] ?? '')) ?>);
        $('#category_id').val(<?= json_encode((string)($selected['indicator_category_id'] ?? '4')) ?>);
        $('#tahun').val(<?= json_encode((string)($selected['tahun'] ?? '')) ?>);
        $('#triwulan').val(<?= json_encode((string)($selected['triwulan'] ?? '1')) ?>);
        loadPengukuran();
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
            alert('Silakan pilih semua filter terlebih dahulu');
            return;
        }

        $.ajax({
            url: '<?= site_url('siimut/trias-mutu/get-pengukuran-data') ?>',
            method: 'POST',
            data: {
                unit_id: unitId,
                category_id: categoryId,
                indicator_id: $('#indicator_id').val() || '',
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
            },
            error: function() {
                alert('Gagal memuat data');
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

        var html = '';
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

        var bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        var totalNum = 0, totalDenum = 0;
        if (m.bulanan) {
            m.bulanan.forEach(function(b) {
                html += '<tr>' +
                    '<td>' + (bulanNames[b.bulan] || 'Bulan ' + b.bulan) + '</td>' +
                    '<td>' + b.num + '</td>' +
                    '<td>' + b.denum + '</td>' +
                    '<td>' + (b.nilai !== null ? b.nilai + ' ' + (ind.indicator_units || '') : '-') + '</td>' +
                    '</tr>';
                totalNum += b.num;
                totalDenum += b.denum;
            });
        }

        $('#measurementTable tbody').html(html);
        $('#totalNum').text(totalNum);
        $('#totalDenum').text(totalDenum);
        $('#totalNilai').text(m.nilai_triwulan !== null ? m.nilai_triwulan + ' ' + (ind.indicator_units || '') : '-');

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

    $('#unit_id, #category_id').on('change', function() {
        var unitId = $('#unit_id').val();
        var categoryId = $('#category_id').val();

        if (unitId && categoryId) {
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
                }
            });
        }
    });
});
</script>
