<!-- begin::Container-->
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Periode</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-auto">
                            <label class="form-label">Periode</label>
                            <select class="form-select form-select-sm" id="filterPeriode" style="width: 150px;">
                                <option value="">Semua Tahun</option>
                                <?php for ($t = $tahunIni; $t >= $tahunMulai; $t--): ?>
                                    <option value="<?= $t ?>" <?= ($filters['tahun'] ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">Triwulan</label>
                            <select class="form-select form-select-sm" id="filterTriwulan" style="width: 150px;">
                                <option value="">Semua Triwulan</option>
                                <option value="1" <?= ($filters['triwulan'] ?? '') == '1' ? 'selected' : '' ?>>Triwulan 1</option>
                                <option value="2" <?= ($filters['triwulan'] ?? '') == '2' ? 'selected' : '' ?>>Triwulan 2</option>
                                <option value="3" <?= ($filters['triwulan'] ?? '') == '3' ? 'selected' : '' ?>>Triwulan 3</option>
                                <option value="4" <?= ($filters['triwulan'] ?? '') == '4' ? 'selected' : '' ?>>Triwulan 4</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">Semester</label>
                            <select class="form-select form-select-sm" id="filterSemester" style="width: 150px;">
                                <option value="">Semua Semester</option>
                                <option value="1" <?= ($filters['semester'] ?? '') == '1' ? 'selected' : '' ?>>Semester 1</option>
                                <option value="2" <?= ($filters['semester'] ?? '') == '2' ? 'selected' : '' ?>>Semester 2</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-primary" id="btnApplyFilter">
                                <i class="bi bi-funnel"></i> Terapkan
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearFilter" <?= empty($filters['tahun']) && empty($filters['triwulan']) && empty($filters['semester']) ? 'style="display:none"' : '' ?>>
                                <i class="bi bi-x-lg"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Trend Line Chart - Insiden</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendLineChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row (main row) -->
</div>
<!--end::Container -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = <?= json_encode($chartData) ?>;
    
    const colors = {
        'KNC': '#0d6efd',
        'KTD': '#ffc107',
        'KTC': '#6c757d',
        'KPC': '#dc3545',
        'Sentinel': '#198754'
    };

    const labels = chartData.labels;
    const datasets = chartData.datasets.map(ds => ({
        label: ds.jenis,
        data: ds.data,
        borderColor: colors[ds.jenis] || '#333',
        backgroundColor: colors[ds.jenis] || '#333',
        tension: 0.3,
        fill: false,
        borderWidth: 2
    }));

    const ctx = document.getElementById('trendLineChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Trend Insiden per Kategori'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    function applyFilters() {
        const tahun = $('#filterPeriode').val();
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();

        const params = new URLSearchParams();
        if (tahun) params.set('tahun', tahun);
        if (triwulan) params.set('triwulan', triwulan);
        if (semester) params.set('semester', semester);

        const queryString = params.toString();
        const url = "<?= site_url('ikprs') ?>" + (queryString ? '?' + queryString : '');
        window.location.href = url;
    }

    $('#btnApplyFilter').on('click', function() {
        applyFilters();
    });

    $('#btnClearFilter').on('click', function() {
        $('#filterPeriode').val('');
        $('#filterTriwulan').val('');
        $('#filterSemester').val('');
        window.location.href = "<?= site_url('ikprs') ?>";
    });

    $('#filterPeriode, #filterTriwulan, #filterSemester').on('change', function() {
        const tahun = $('#filterPeriode').val();
        const triwulan = $('#filterTriwulan').val();
        const semester = $('#filterSemester').val();

        if (tahun || triwulan || semester) {
            $('#btnClearFilter').show();
        } else {
            $('#btnClearFilter').hide();
        }
    });
</script>
