<!-- begin::Container-->
<div class="container-fluid">
    <!--begin::Row - Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-top: 3px solid #6c757d !important;">
                <div class="card-header bg-light">
                    <h3 class="card-title text-dark">
                        <i class="bi bi-funnel-fill me-2 text-secondary"></i>
                        Filter Periode
                    </h3>
                    <!-- <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="bi bi-fullscreen"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="bi bi-dash"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div> -->
                </div>
                <div class="card-body">
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="bi bi-calendar3 me-1"></i>PERIODE
                            </label>
                            <select class="form-select form-select-sm border-primary" id="filterPeriode">
                                <option value="">Semua Tahun</option>
                                <?php for ($t = $tahunIni; $t >= $tahunMulai; $t--): ?>
                                    <option value="<?= $t ?>" <?= (string)($filters['tahun'] ?? $tahunIni) === (string)$t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="bi bi-calendar-range me-1"></i>TRIWULAN
                            </label>
                            <select class="form-select form-select-sm" id="filterTriwulan">
                                <option value="">Semua Triwulan</option>
                                <option value="1" <?= ($filters['triwulan'] ?? '') == '1' ? 'selected' : '' ?>>Triwulan I (Jan-Mar)</option>
                                <option value="2" <?= ($filters['triwulan'] ?? '') == '2' ? 'selected' : '' ?>>Triwulan II (Apr-Jun)</option>
                                <option value="3" <?= ($filters['triwulan'] ?? '') == '3' ? 'selected' : '' ?>>Triwulan III (Jul-Sep)</option>
                                <option value="4" <?= ($filters['triwulan'] ?? '') == '4' ? 'selected' : '' ?>>Triwulan IV (Okt-Des)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="bi bi-calendar2-range me-1"></i>SEMESTER
                            </label>
                            <select class="form-select form-select-sm" id="filterSemester">
                                <option value="">Semua Semester</option>
                                <option value="1" <?= ($filters['semester'] ?? '') == '1' ? 'selected' : '' ?>>Semester I (Jan-Jun)</option>
                                <option value="2" <?= ($filters['semester'] ?? '') == '2' ? 'selected' : '' ?>>Semester II (Jul-Des)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm flex-grow-1" id="btnApplyFilter">
                                    <i class="bi bi-funnel"></i> Terapkan
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearFilter" <?= empty($filters['tahun']) && empty($filters['triwulan']) && empty($filters['semester']) ? 'style="display:none"' : '' ?>>
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="btnReloadChart" title="Reload Data">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!--begin::Row - Trend Line Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #0d6efd !important;">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title text-white">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Trend Line Chart - Insiden
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="maximize">
                            <i class="bi bi-fullscreen text-white"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="bi bi-dash text-white"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="remove">
                            <i class="bi bi-x-lg text-white"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="trendLineChart"></canvas>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrendTable">
                            <i class="bi bi-table me-1"></i> Tampilkan Detail Data
                        </button>
                        <div class="collapse mt-3" id="collapseTrendTable">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover table-striped" id="trendTable">
                                    <thead class="table-primary" id="trendTableHeader">
                                    </thead>
                                    <tbody id="trendTableBody">
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr id="trendTableFooter">
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Row - Grading Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #dc3545 !important;">
                <div class="card-header bg-gradient-danger">
                    <h3 class="card-title text-white">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Grading Chart - Tingkat Bahaya
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="maximize">
                            <i class="bi bi-fullscreen text-white"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="bi bi-dash text-white"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="remove">
                            <i class="bi bi-x-lg text-white"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 280px;">
                        <canvas id="gradingChart"></canvas>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGradingTable">
                            <i class="bi bi-table me-1"></i> Tampilkan Detail Grading
                        </button>
                        <div class="collapse mt-3" id="collapseGradingTable">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-striped" id="gradingTable" style="min-width: 600px;">
                                    <thead class="table-danger">
                                        <tr>
                                            <th class="text-left align-middle" style="width: 250px;">Grading</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th class="text-left">Total</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Container -->

<style>
    .card {
        border-radius: 8px;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
    }
    
    .chart-container {
        position: relative;
        width: 100%;
    }
    
    #trendLineChart, #gradingChart {
        cursor: pointer;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .collapse:not(.show) {
        display: none;
    }
    
    .btn-outline-primary, .btn-outline-danger {
        border-width: 2px;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
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
        backgroundColor: colors[ds.jenis] + '20' || '#333',
        tension: 0.3,
        fill: true,
        borderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6
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
            onClick: function(evt, elements) {
                if (elements.length > 0) {
                    const collapseEl = document.getElementById('collapseTrendTable');
                    if (collapseEl.classList.contains('show')) {
                        collapseEl.classList.remove('show');
                    } else {
                        collapseEl.classList.add('show');
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' kasus';
                        }
                    }
                },
                title: {
                    display: true,
                    text: xAxisType === 'bulan' ? '📊 Trend Insiden Keselamatan Pasien Bulanan (Klik chart untuk lihat detail)' : '📊 Trend Insiden Keselamatan Pasien Tahunan (Klik chart untuk lihat detail)',
                    font: {
                        size: 13,
                        weight: 'normal'
                    },
                    padding: {
                        bottom: 10
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Insiden',
                        font: {
                            size: 11,
                            weight: 'normal'
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: xAxisType === 'bulan' ? 'Bulan' : 'Tahun',
                        font: {
                            size: 11,
                            weight: 'normal'
                        }
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Populate Trend Table
    const trendTableBody = document.getElementById('trendTableBody');
    const trendTableFooter = document.getElementById('trendTableFooter');

    if (trendTableBody && chartData.labels && chartData.datasets) {
        const trendTableHeader = document.getElementById('trendTableHeader');

        const fullNames = {
            'KNC': 'Near Miss (KNC)',
            'KTD': 'Adverse Event (KTD)',
            'KTC': 'Incident (KTC)',
            'KPC': 'Potentially Injurious (KPC)',
            'Sentinel': 'Sentinel Event'
        };

        const headerColors = {
            'KNC': 'bg-primary',
            'KTD': 'bg-warning',
            'KTC': 'bg-secondary',
            'KPC': 'bg-danger',
            'Sentinel': 'bg-success'
        };

        // Build header row
        let headerHtml = '<tr><th class="text-center align-middle">Jenis Insiden</th>';
        chartData.labels.forEach(label => {
            headerHtml += `<th class="text-center align-middle">${label}</th>`;
        });
        headerHtml += '<th class="text-center align-middle bg-dark text-white">Total</th></tr>';
        trendTableHeader.innerHTML = headerHtml;

        // Build body rows (each insiden type as a row)
        let rowTotals = new Array(chartData.labels.length).fill(0);

        chartData.datasets.forEach((ds, dsIdx) => {
            const labelName = fullNames[ds.jenis] || ds.jenis;
            const badgeClass = headerColors[ds.jenis] || 'bg-secondary';
            let rowHtml = `<tr><td class="fw-bold"><span class="badge ${badgeClass} me-2">${ds.jenis}</span>${labelName}</td>`;

            let rowTotal = 0;
            chartData.labels.forEach((label, idx) => {
                const value = ds.data[idx] || 0;
                rowTotal += value;
                rowTotals[idx] += value;
                rowHtml += `<td class="text-center">${value}</td>`;
            });

            rowHtml += `<td class="text-center fw-bold">${rowTotal}</td></tr>`;
            trendTableBody.innerHTML += rowHtml;
        });

        // Build footer row
        let footerHtml = '<td class="text-center fw-bold">TOTAL</td>';
        let grandTotal = 0;
        rowTotals.forEach(total => {
            footerHtml += `<td class="text-center">${total}</td>`;
            grandTotal += total;
        });
        footerHtml += `<td class="text-center fw-bold">${grandTotal}</td>`;

        trendTableFooter.innerHTML = footerHtml;
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
        borderWidth: 1
    }));

    const ctxGrading = document.getElementById('gradingChart').getContext('2d');
    new Chart(ctxGrading, {
        type: 'doughnut',
        data: {
            labels: gradingDatasets.map(ds => ds.label),
            datasets: [{
                data: gradingDatasets.map(ds => ds.data.reduce((a, b) => a + b, 0)),
                backgroundColor: gradingDatasets.map(ds => ds.backgroundColor),
                borderColor: gradingDatasets.map(ds => ds.borderColor),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return {
                                        text: `${label}: ${value} (${percentage})`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].borderColor[i],
                                        hidden: false,
                                        index: i,
                                        pointStyle: 'circle'
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                title: {
                    display: true,
                    text: '⚠️ Distribusi Tingkat Bahaya (Risk Level)',
                    font: {
                        size: 13,
                        weight: 'normal'
                    },
                    padding: {
                        bottom: 10
                    }
                }
            }
        }
    });

    // Grading Table - Transposed
    const gradingTableHead = document.querySelector('#gradingTable thead tr');
    const gradingTableBody = document.querySelector('#gradingTable tbody');
    const gradingTableFoot = document.querySelector('#gradingTable tfoot tr');

    const gradingTableLabels = gradingChartData.labels;
    const gradingTableDatasets = gradingChartData.datasets;

    gradingTableLabels.forEach(label => {
        const th = document.createElement('th');
        th.className = 'text-center align-middle';
        th.textContent = label;
        gradingTableHead.appendChild(th);
    });

    const totalThHead = document.createElement('th');
    totalThHead.className = 'text-center align-middle';
    totalThHead.textContent = 'Total';
    gradingTableHead.appendChild(totalThHead);

    let grandTotalGrading = 0;
    const columnTotals = new Array(gradingTableLabels.length).fill(0);

    gradingTableDatasets.forEach(ds => {
        const tr = document.createElement('tr');

        const tdGrading = document.createElement('td');
        tdGrading.className = 'text-left fw-bold align-middle';
        tdGrading.style.backgroundColor = gradingColors[ds.grading] + '20';

        const colorBadge = document.createElement('span');
        colorBadge.style.display = 'inline-block';
        colorBadge.style.width = '14px';
        colorBadge.style.height = '14px';
        colorBadge.style.backgroundColor = gradingColors[ds.grading];
        colorBadge.style.border = '1px solid ' + gradingBorderColors[ds.grading];
        colorBadge.style.borderRadius = '3px';
        colorBadge.style.marginRight = '8px';
        colorBadge.style.verticalAlign = 'middle';

        tdGrading.appendChild(colorBadge);
        tdGrading.appendChild(document.createTextNode(gradingFullNames[ds.grading] || ds.grading));
        tr.appendChild(tdGrading);

        let rowTotal = 0;
        ds.data.forEach((value, index) => {
            const td = document.createElement('td');
            td.className = 'text-center';
            td.textContent = value;
            rowTotal += value;
            columnTotals[index] += value;
            tr.appendChild(td);
        });

        const tdTotal = document.createElement('td');
        tdTotal.className = 'text-center fw-bold';
        tdTotal.textContent = rowTotal;
        tdTotal.style.backgroundColor = gradingColors[ds.grading] + '30';
        tr.appendChild(tdTotal);

        gradingTableBody.appendChild(tr);
        grandTotalGrading += rowTotal;
    });

    columnTotals.forEach(colTotal => {
        const th = document.createElement('th');
        th.className = 'text-center';
        th.textContent = colTotal;
        gradingTableFoot.appendChild(th);
    });

    const totalFootTh = document.createElement('th');
    totalFootTh.className = 'text-center';
    totalFootTh.textContent = grandTotalGrading;
    gradingTableFoot.appendChild(totalFootTh);

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

    // Card Tools - Minimize, Maximize, Remove
    $(document).on('click', '[data-card-widget="collapse"]', function() {
        const card = $(this).closest('.card');
        const body = card.find('.card-body');
        if (body.is(':visible')) {
            body.slideUp(200);
            $(this).find('i').removeClass('bi-dash').addClass('bi-plus');
        } else {
            body.slideDown(200);
            $(this).find('i').removeClass('bi-plus').addClass('bi-dash');
        }
    });

    $(document).on('click', '[data-card-widget="maximize"]', function() {
        const card = $(this).closest('.card');
        if (!card.hasClass('card-fullscreen')) {
            card.addClass('card-fullscreen');
            card.css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100vw',
                'height': '100vh',
                'z-index': '9999',
                'margin': '0',
                'border-radius': '0'
            });
            $(this).find('i').removeClass('bi-fullscreen').addClass('bi-fullscreen-exit');
        } else {
            card.removeClass('card-fullscreen');
            card.css({
                'position': '',
                'top': '',
                'left': '',
                'width': '',
                'height': '',
                'z-index': '',
                'margin': '',
                'border-radius': '8px'
            });
            $(this).find('i').removeClass('bi-fullscreen-exit').addClass('bi-fullscreen');
        }
    });

    $(document).on('click', '[data-card-widget="remove"]', function() {
        const card = $(this).closest('.card');
        card.fadeOut(200, function() {
            $(this).remove();
        });
    });
</script>