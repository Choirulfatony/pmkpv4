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
                    
                    <?php
                    $filterDesc = "Tampilan grafik di bawah ini menunjukkan data akumulasi untuk ";
                    if (!empty($filters['tahun'])) {
                        $filterDesc .= "Tahun " . $filters['tahun'];
                    } else {
                        $filterDesc .= "Tahun " . $tahunIni;
                    }
                    
                    if (!empty($filters['triwulan'])) {
                        $triwulanLabels = ['', 'Januari - Maret', 'April - Juni', 'Juli - September', 'Oktober - Desember'];
                        $filterDesc .= ", Triwulan " . $filters['triwulan'] . " (" . $triwulanLabels[$filters['triwulan']] . ")";
                    } elseif (!empty($filters['semester'])) {
                        $semesterLabels = ['', 'Januari - Juni', 'Juli - Desember'];
                        $filterDesc .= ", Semester " . $filters['semester'] . " (" . $semesterLabels[$filters['semester']] . ")";
                    } else {
                        $filterDesc .= " (Bulan Januari - Desember)";
                    }
                    
                    $filterDesc .= ". Kategori disusun berdasarkan jenis insiden.";
                    ?>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <?= $filterDesc ?>
                    </div>
                    
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="trendLineChart"></canvas>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Legend:</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-primary me-2" style="width: 20px; height: 20px;"></span>
                                    <span>Near Miss (KNC)</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge me-2" style="background-color: #ffc107; width: 20px; height: 20px;"></span>
                                    <span>Adverse Event (KTD)</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-secondary me-2" style="width: 20px; height: 20px;"></span>
                                    <span>Incident (KTC)</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-danger me-2" style="width: 20px; height: 20px;"></span>
                                    <span>Potentially Injurious Event (KPC)</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
                                    <span>Sentinel Event</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Ringkasan:</h6>
                            <div id="trendSummary">
                                <p class="text-muted">Total insiden berdasarkan jenis:</p>
                                <ul class="list-unstyled" id="trendTotalList">
                                </ul>
                            </div>
                        </div>
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
                        Chart Akibat Insiden
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

    // Calculate and display totals for Trend Line Chart
    const trendTotalList = document.getElementById('trendTotalList');
    if (trendTotalList) {
        const fullNames = {
            'KNC': 'Near Miss (KNC)',
            'KTD': 'Adverse Event (KTD)',
            'KTC': 'Incident (KTC)',
            'KPC': 'Potentially Injurious Event (KPC)',
            'Sentinel': 'Sentinel Event'
        };
        
        const colorBadges = {
            'KNC': 'bg-primary',
            'KTD': 'bg-warning',
            'KTC': 'bg-secondary',
            'KPC': 'bg-danger',
            'Sentinel': 'bg-success'
        };
        
        let summaryHtml = '';
        datasets.forEach(ds => {
            const total = ds.data.reduce((a, b) => a + b, 0);
            const labelName = fullNames[ds.label] || ds.label;
            const badgeClass = colorBadges[ds.label] || 'bg-secondary';
            summaryHtml += `<li class="mb-2">
                <span class="badge ${badgeClass} me-2" style="width: 15px; height: 15px; display: inline-block;"></span>
                <strong>${labelName}:</strong> ${total} insiden
            </li>`;
        });
        trendTotalList.innerHTML = summaryHtml;
    }

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

    // Akibat Insiden Chart - Horizontal Bar
    const akibatChartData = <?= json_encode($akibatChartData) ?>;
    
    const akibatColors = {
        'Kematian': 'rgba(108, 117, 125, 0.9)',
        'Cedera Irreversibel / Cedera Berat': 'rgba(220, 53, 69, 0.9)',
        'Cedera Reversibel / Cedera Sedang': 'rgba(255, 193, 7, 0.9)',
        'Cedera Ringan': 'rgba(13, 110, 253, 0.9)',
        'Tidak ada cedera': 'rgba(25, 135, 84, 0.9)'
    };
    
    const akibatBorderColors = {
        'Kematian': '#6c757d',
        'Cedera Irreversibel / Cedera Berat': '#dc3545',
        'Cedera Reversibel / Cedera Sedang': '#ffc107',
        'Cedera Ringan': '#0d6efd',
        'Tidak ada cedera': '#198754'
    };
    
    const akibatLabels = akibatChartData.labels;
    const akibatDatasets = akibatChartData.datasets.map(ds => ({
        label: ds.akibat,
        data: ds.data,
        backgroundColor: akibatColors[ds.akibat] || 'rgba(100,100,100,0.8)',
        borderColor: akibatBorderColors[ds.akibat] || '#333',
        borderWidth: 1,
        barPercentage: 0.6,
        categoryPercentage: 0.8
    }));
    
    const ctxAkibat = document.getElementById('akibatChart').getContext('2d');
    new Chart(ctxAkibat, {
        type: 'bar',
        data: {
            labels: akibatLabels,
            datasets: akibatDatasets
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: xAxisType === 'bulan' ? 'Akibat Insiden Bulanan' : 'Akibat Insiden Tahunan'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: xAxisType === 'bulan' ? 'Bulan' : 'Tahun'
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
