<!-- begin::Container-->
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-graph-up-arrow mr-1"></i>
                        Trend Line Chart - Insiden
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="bi bi-dash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="filter-wrapper d-flex flex-wrap align-items-end justify-content-between gap-2 p-3 bg-light rounded">
                                <div class="d-flex flex-wrap align-items-end gap-2">
                                    <div class="filter-group">
                                        <label class="form-label mb-1 text-muted small fw-semibold">PERIODE</label>
                                        <select class="form-select form-select-sm border-primary" id="filterPeriode" style="min-width: 120px;">
                                            <option value="">Semua Tahun</option>
                                            <?php for ($t = $tahunIni; $t >= $tahunMulai; $t--): ?>
                                                <option value="<?= $t ?>" <?= (string)($filters['tahun'] ?? $tahunIni) === (string)$t ? 'selected' : '' ?>><?= $t ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="form-label mb-1 text-muted small fw-semibold">TRIWULAN</label>
                                        <select class="form-select form-select-sm" id="filterTriwulan" style="min-width: 140px;">
                                            <option value="">Semua Triwulan</option>
                                            <option value="1" <?= ($filters['triwulan'] ?? '') == '1' ? 'selected' : '' ?>>Triwulan I (Jan-Mar)</option>
                                            <option value="2" <?= ($filters['triwulan'] ?? '') == '2' ? 'selected' : '' ?>>Triwulan II (Apr-Jun)</option>
                                            <option value="3" <?= ($filters['triwulan'] ?? '') == '3' ? 'selected' : '' ?>>Triwulan III (Jul-Sep)</option>
                                            <option value="4" <?= ($filters['triwulan'] ?? '') == '4' ? 'selected' : '' ?>>Triwulan IV (Okt-Des)</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="form-label mb-1 text-muted small fw-semibold">SEMESTER</label>
                                        <select class="form-select form-select-sm" id="filterSemester" style="min-width: 140px;">
                                            <option value="">Semua Semester</option>
                                            <option value="1" <?= ($filters['semester'] ?? '') == '1' ? 'selected' : '' ?>>Semester I (Jan-Jun)</option>
                                            <option value="2" <?= ($filters['semester'] ?? '') == '2' ? 'selected' : '' ?>>Semester II (Jul-Des)</option>
                                        </select>
                                    </div>
                                    <div class="filter-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnApplyFilter">
                                            <i class="bi bi-funnel"></i> Terapkan
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearFilter" <?= empty($filters['tahun']) && empty($filters['triwulan']) && empty($filters['semester']) ? 'style="display:none"' : '' ?>>
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </button>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="button" class="btn btn-sm btn-success" id="btnReloadChart" title="Reload Data">
                                        <i class="bi bi-arrow-repeat"></i> Reload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="trendLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row (main row) -->
    
    <!--begin::Row-->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                        Grading Chart - Tingkat Bahaya (Risk Level)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="bi bi-dash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="gradingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!--begin::Row-->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-person-x-fill mr-1"></i>
                        Grafik Distribusi Insiden Keselamatan Pasien Berdasarkan Tingkat Akibat (Impact Severity)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="bi bi-dash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="akibatChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!--end::Container -->

<style>
    .filter-wrapper {
        border: 1px solid #e9ecef;
    }
    .filter-group .form-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-group select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .card-outline.card-primary {
        border-top: 3px solid #0d6efd;
    }
    .card-outline.card-danger {
        border-top: 3px solid #dc3545;
    }
    .card-outline.card-warning {
        border-top: 3px solid #ffc107;
    }
</style>

<script>
    const chartData = <?= json_encode($chartData) ?>;
    const xAxisType = '<?= $xAxisType ?>';
    
    const colors = {
        'KNC': '#0d6efd',
        'KTD': '#ffc107',
        'KTC': '#6c757d',
        'KPC': '#dc3545',
        'Sentinel': '#198754'
    };

    const fullNames = {
        'KNC': 'Near Miss (KNC)',
        'KTD': 'Adverse Event (KTD)',
        'KTC': 'Incident (KTC)',
        'KPC': 'Potentially Injurious Event (KPC)',
        'Sentinel': 'Sentinel Event'
    };

    const labels = chartData.labels;
    const datasets = chartData.datasets.map(ds => ({
        label: fullNames[ds.jenis] || ds.jenis,
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
                    text: xAxisType === 'bulan' ? 'Trend Insiden Bulanan' : 'Trend Insiden Tahunan'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: xAxisType === 'bulan' ? 'Bulan' : 'Tahun'
                    }
                }
            }
        }
    });

    // Grading Chart - Bar Chart
    const gradingChartData = <?= json_encode($gradingChartData) ?>;
    
    const gradingColors = {
        'HIJAU': 'rgba(25, 135, 84, 0.8)',
        'BIRU': 'rgba(13, 110, 253, 0.8)',
        'KUNING': 'rgba(255, 193, 7, 0.8)',
        'MERAH': 'rgba(220, 53, 69, 0.8)'
    };
    
    const gradingBorderColors = {
        'HIJAU': '#198754',
        'BIRU': '#0d6efd',
        'KUNING': '#ffc107',
        'MERAH': '#dc3545'
    };
    
    const gradingFullNames = {
        'HIJAU': 'Risiko Rendah (Hijau)',
        'BIRU': 'Risiko Sedang (Biru)',
        'KUNING': 'Risiko Tinggi (Kuning)',
        'MERAH': 'Risiko Tinggi Sekali (Merah)'
    };
    
    const gradingLabels = gradingChartData.labels;
    const gradingDatasets = gradingChartData.datasets.map(ds => ({
        label: gradingFullNames[ds.grading] || ds.grading,
        data: ds.data,
        backgroundColor: gradingColors[ds.grading] || 'rgba(100,100,100,0.8)',
        borderColor: gradingBorderColors[ds.grading] || '#333',
        borderWidth: 1,
        barPercentage: 0.6,
        categoryPercentage: 0.8
    }));
    
    const ctxGrading = document.getElementById('gradingChart').getContext('2d');
    new Chart(ctxGrading, {
        type: 'bar',
        data: {
            labels: gradingLabels,
            datasets: gradingDatasets
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
                    text: xAxisType === 'bulan' ? 'Grading Risk Level Bulanan' : 'Grading Risk Level Tahunan'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: xAxisType === 'bulan' ? 'Bulan' : 'Tahun'
                    }
                }
            }
        }
    });

    // Akibat Insiden Chart - Horizontal Bar (Total per Kategori)
    const akibatChartData = <?= json_encode($akibatChartData) ?>;
    
    // Calculate totals per category
    const categoryLabels = [];
    const categoryTotals = [];
    const categoryColors = [
        'rgba(108, 117, 125, 0.9)',
        'rgba(220, 53, 69, 0.9)',
        'rgba(255, 193, 7, 0.9)',
        'rgba(13, 110, 253, 0.9)',
        'rgba(25, 135, 84, 0.9)'
    ];
    
    const categoryFullNames = {
        'Kematian': 'Katastropik (Kematian)',
        'Cedera Irreversibel / Cedera Berat': 'Mayor (Cedera Berat/Irreversibel)',
        'Cedera Reversibel / Cedera Sedang': 'Moderat (Cedera Sedang/Reversibel)',
        'Cedera Ringan': 'Minor (Cedera Ringan)',
        'Tidak ada cedera': 'Tidak Signifikan (Tidak Ada Cedera)'
    };
    
    // If data is per month, sum them up; otherwise use direct values
    if (akibatChartData.datasets.length > 0 && akibatChartData.datasets[0].data.length > 1) {
        // Data is per period (month/year) - sum all values
        akibatChartData.datasets.forEach((ds, idx) => {
            categoryLabels.push(categoryFullNames[ds.akibat] || ds.akibat);
            const total = ds.data.reduce((a, b) => a + b, 0);
            categoryTotals.push(total);
        });
    } else {
        // Single value
        akibatChartData.datasets.forEach((ds, idx) => {
            categoryLabels.push(categoryFullNames[ds.akibat] || ds.akibat);
            categoryTotals.push(ds.data[0] || 0);
        });
    }
    
    const ctxAkibat = document.getElementById('akibatChart').getContext('2d');
    new Chart(ctxAkibat, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Jumlah',
                data: categoryTotals,
                backgroundColor: categoryColors,
                borderColor: ['#6c757d', '#dc3545', '#ffc107', '#0d6efd', '#198754'],
                borderWidth: 2,
                borderRadius: 6,
                barPercentage: 0.7
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Grafik Distribusi Insiden Keselamatan Pasien Berdasarkan Tingkat Akibat (Impact Severity)',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        bottom: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' Insiden';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Insiden',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.08)'
                    }
                },
                y: {
                    ticks: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        },
        plugins: [{
            id: 'valueLabels',
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;
                const xAxis = chart.scales.x;
                const yAxis = chart.scales.y;
                
                ctx.font = 'bold 16px sans-serif';
                ctx.textAlign = 'left';
                ctx.fillStyle = '#333';
                
                chart.data.datasets[0].data.forEach(function(value, i) {
                    if (value > 0) {
                        const xPos = xAxis.getPixelForValue(value) + 8;
                        const yPos = yAxis.getPixelForValue(i);
                        ctx.fillText(value, xPos, yPos + 5);
                    }
                });
            }
        }]
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

    $('#btnReloadChart').on('click', function() {
        window.location.reload();
    });

    $('#btnClearFilter').on('click', function() {
        $('#filterPeriode').val('<?= $tahunIni ?>');
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
