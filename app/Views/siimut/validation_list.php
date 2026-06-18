<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 py-2">
            <form class="d-flex flex-wrap align-items-center gap-2 mb-0" method="get" id="filterForm">
                <select name="tahun" id="filterTahun" class="form-select form-select-sm" style="width:85px">
                    <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <select name="bulan" id="filterBulan" class="form-select form-select-sm" style="width:120px">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad((string)$m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == $bulan ? 'selected' : '' ?>>
                            <?= $namaBulan[$m] ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
            </form>
            <span class="text-muted ms-auto"><?= $bulan ?>/<?= $tahun ?></span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="table-validation" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>Indikator</th>
                            <th style="width:150px">Unit</th>
                            <th class="text-center" style="width:70px">Record</th>
                            <th style="width:160px">Status Validasi</th>
                            <th class="text-center" style="width:80px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/responsive.bootstrap5.min.css') ?>">
<script src="<?= base_url('assets/adminlte/js/dataTables.responsive.min.js') ?>"></script>
<script>
    var table;

    $(document).ready(function() {
        table = $('#table-validation').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            paging: true,
            lengthChange: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: '<?= site_url('siimut/validation/' . $module . '/ajax-get-data') ?>',
                type: 'POST',
                data: function(d) {
                    d.tahun = $('#filterTahun').val();
                    d.bulan = $('#filterBulan').val();
                }
            },
            columns: [
                { data: 'no', className: 'text-center', orderable: false },
                { data: 'indicator_element', orderable: true },
                { data: 'department_name', orderable: true },
                { data: 'total_records', className: 'text-center', orderable: true },
                { data: 'status_html', orderable: false },
                { data: 'aksi_html', className: 'text-center', orderable: false }
            ],
            order: [[1, 'asc']]
        });

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });
    });
</script>

