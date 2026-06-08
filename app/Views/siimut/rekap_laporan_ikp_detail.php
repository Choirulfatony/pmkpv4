<!-- HEADER -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-success"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><strong>Detail Rekap Insiden Keselamatan Pasien (IKP)</strong></h5>
                    <p class="mb-0">Indikator: <strong><?= isset($detail->indicator_element) ? esc($detail->indicator_element) : '-' ?></strong></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- CARD DETAIL -->
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table me-2"></i>
                    Detail Per Ruangan
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <a href="<?= site_url('siimut/rekap-laporan-ikp?tahun=' . $tahun) ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <select class="form-select form-select-sm" id="filter_bulan" style="width:130px;" onchange="reloadDetail()">
                        <?php $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; ?>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= $m == $bulan ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="input-group input-group-sm" style="width:100px;">
                        <select class="form-select form-select-sm" id="tahun" onchange="reloadDetail()">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reloadDetail()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <div class="overlay-wrapper" id="loading_overlay" style="display:none; min-height:120px;">
                        <div class="overlay"><i class="loader"></i></div>
                    </div>
                    <table class="table table-bordered table-inm-inm mb-0" id="detailTable">
                        <thead>
                            <tr id="headerRow">
                                <th class="text-center" style="width:40px;">#</th>
                                <th style="min-width:200px;">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: rgba(41, 185, 92);"></span>
                        <small>Ada data</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="legend-dot me-2" style="background-color: #e2e3e5;"></span>
                        <small>Belum ada data</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var vtahun = <?= json_encode($tahun) ?>;
var indicatorId = <?= json_encode($indicatorId) ?>;

function reloadDetail() {
    vtahun = document.getElementById('tahun').value;
    var bulan = document.getElementById('filter_bulan').value;
    var daysInMonth = new Date(vtahun, bulan, 0).getDate();

    document.getElementById('loading_overlay').style.display = 'block';
    document.getElementById('detailTable').querySelector('thead tr').innerHTML = '';
    document.getElementById('tableBody').innerHTML = '';

    var headerHtml = '<th class="text-center" style="width:40px;">#</th>' +
        '<th style="min-width:200px;">Ruangan</th>';
    for (var d = 1; d <= daysInMonth; d++) {
        headerHtml += '<th class="text-center" style="width:50px;">' + d + '</th>';
    }
    document.getElementById('headerRow').innerHTML = headerHtml;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= site_url('siimut/rekap-laporan-ikp/ajax-detail-ikp') ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        document.getElementById('loading_overlay').style.display = 'none';
        if (xhr.status === 200) {
            try {
                var resp = JSON.parse(xhr.responseText);
                renderDetail(resp);
            } catch(e) {
                document.getElementById('tableBody').innerHTML = '<tr><td colspan="99" class="text-center text-danger">Gagal memproses data</td></tr>';
            }
        }
    };
    xhr.onerror = function() {
        document.getElementById('loading_overlay').style.display = 'none';
    };
    xhr.send('indicator_id=' + indicatorId + '&vtahun=' + vtahun + '&bulan=' + bulan);
}

function renderDetail(resp) {
    var data = resp.data || [];
    var bulan = parseInt(document.getElementById('filter_bulan').value);
    var daysInMonth = new Date(vtahun, bulan, 0).getDate();
    var tbody = '';

    if (data.length === 0) {
        tbody = '<tr><td colspan="99" class="text-center text-muted">Tidak ada data</td></tr>';
    } else {
        for (var i = 0; i < data.length; i++) {
            var cells = data[i];
            if (i === 0) continue;
            tbody += '<tr>';
            for (var c = 0; c < cells.length; c++) {
                var cellContent = cells[c];
                if (c === 0) {
                    tbody += '<td class="text-center fw-bold">' + cellContent + '</td>';
                } else if (c === 1) {
                    tbody += '<td class="text-start ps-2">' + cellContent + '</td>';
                } else {
                    var parsed = $('<div>').html(cellContent);
                    var num = parsed.find('.num-val').text() || '0';
                    var numInt = parseInt(num) || 0;
                    var cls = 'day-cell text-center';
                    if (numInt > 0) {
                        cls += ' cell-has-data cell-target';
                    } else {
                        cls += ' cell-empty';
                    }
                    tbody += '<td class="' + cls + '"><div class="fw-bold">' + (numInt > 0 ? numInt : '-') + '</div></td>';
                }
            }
            tbody += '</tr>';
        }
    }

    document.getElementById('tableBody').innerHTML = tbody;
}

document.addEventListener('DOMContentLoaded', function() {
    reloadDetail();
});
</script>
