
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
            // Persen: target * 2 atau max data * 1.2
            var calcMax = target * 2;
            return Math.max(maxData * 1.2, calcMax);
        } else if (isTime) {
            // Waktu: max data * 1.3 atau target * 1.3
            return Math.max(maxData * 1.3, target * 1.3);
        } else if (isIndex) {
            // Indeks: gunakan max(data, target) * 1.3
            return Math.max(maxData, target) * 1.3;
        }
        // Default: max(data * 1.2, target * 1.3)
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

    function loadGrafik(isYearChange = false) {
        var tahun = document.getElementById('tahun').value;
        var indicatorId = document.getElementById('indicator_id').value;

        // When year changes, keep indicator selection and reload graph
        // Only hide graph if no indicator is selected
        if (!indicatorId || indicatorId === '') {
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
        var grafikContainer = document.getElementById('grafikContainer');
        if (grafikContainer) grafikContainer.style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/grafik-inm/data') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                var loadingGrafik = document.getElementById('loadingGrafik');
                if (loadingGrafik) loadingGrafik.style.display = 'none';
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    currentIndicatorData = response;
                    var grafikContainer = document.getElementById('grafikContainer');
                    var indicatorInfo = document.getElementById('indicatorInfo');
                    var indicatorName = document.getElementById('indicatorName');
                    var indicatorTarget = document.getElementById('indicatorTarget');
                    var indicatorUnits = document.getElementById('indicatorUnits');
                    var indicatorUnitsLabel = document.getElementById('indicatorUnitsLabel');
                    
                    if (grafikContainer) grafikContainer.style.display = 'block';
                    if (indicatorInfo) indicatorInfo.style.display = 'block';
                    if (indicatorName) indicatorName.textContent = response.indicator.indicator_element;
                    if (indicatorTarget) indicatorTarget.textContent = response.indicator.indicator_target;
                    var units = response.indicator.indicator_units || '';
                    if (indicatorUnits) indicatorUnits.textContent = units;
                    if (indicatorUnitsLabel) indicatorUnitsLabel.textContent = units;
                    var statusBadge = document.getElementById('statusBadge');
                    var target = parseFloat(response.indicator.indicator_target || 0);
                    var units = response.indicator.indicator_units || '';
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

                    // Update summary cards
                    var summaryNilaiEl = document.getElementById('summaryNilai');
                    var summaryTargetEl = document.getElementById('summaryTarget');
                    if (summaryNilaiEl) summaryNilaiEl.textContent = nilai + ' ' + units;
                    if (summaryTargetEl) summaryTargetEl.textContent = target + ' ' + units;

                    // Calculate trend (bandingkan dengan nilai tahun lalu)
                    var perTahun = response.per_tahun;
                    var tahunKeys = Object.keys(perTahun).sort();
                    var lastYear = tahunKeys[tahunKeys.length - 2]; // tahun lalu
                    var trendEl = document.getElementById('summaryTrend');
                    var statusEl = document.getElementById('summaryStatus');

                    var diff = 0;
                    var lastYearNilai = 0;
                    var hasLastYearData = !!(lastYear && perTahun[lastYear] && perTahun[lastYear].nilai);

                    if (hasLastYearData) {
                        var currentYear = tahunKeys[tahunKeys.length - 1];
                        diff = nilai - perTahun[lastYear].nilai;
                        lastYearNilai = perTahun[lastYear].nilai;
                    }

                    var trendText = '';

                    if (trendEl) {
                            if (hasLastYearData) {
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
                            } else {
                                trendEl.textContent = '📊 Baru';
                                trendEl.className = 'mb-1 text-info fw-bold';
                                trendText = 'data pertama';
                            }
                        }

                    // Status badge
                    if (statusEl) {
                        if (response.tahunan.tercap) {
                            statusEl.textContent = 'TERCAPAI ✓';
                            statusEl.className = 'mb-1 text-success fw-bold';
                            if (statusEl.parentElement && statusEl.parentElement.parentElement) {
                                statusEl.parentElement.parentElement.classList.add('border-success');
                            }
                        } else {
                            statusEl.textContent = 'TIDAK TERCAPAI ✗';
                            statusEl.className = 'mb-1 text-danger fw-bold';
                            if (statusEl.parentElement && statusEl.parentElement.parentElement) {
                                statusEl.parentElement.parentElement.classList.add('border-danger');
                            }
                        }
                    }

                    // Generate Keterangan (Bahasa PMKP)
                    var analisasHtml = '<strong>Analisis:</strong><br><br>';

                        if (response.tahunan.tercap) {
                            analisasHtml += '<span class="text-success">';
                            if (hasLastYearData) {
                                analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' telah melampaui target ' + target + ' ' + units + ' yang ditetapkan.<br><br>';
                                analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + ' ' + units + '), ';
                                if (diff > 0) {
                                    analisasHtml += 'terdapat peningkatan sebesar ' + diff.toFixed(2) + '%.<br>';
                                    analisasHtml += 'Peningkatan ini menunjukkan adanya perbaikan kinerja yang perlu dipertahankan.<br><br>';
                                    analisasHtml += '<strong>Kesimpulan:</strong> Capaian indikator tetap baik dan berada di atas standar. ';
                                    analisasHtml += 'Perlu dilakukan monitoring untuk menjaga konsistensi capaian indikator.</span>';
                                } else if (diff < 0) {
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
                                analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' telah melampaui target ' + target + ' ' + units + ' yang ditetapkan.<br><br>';
                                analisasHtml += 'Ini adalah data pertama untuk indikator ini.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Capaian indikator baik dan berada di atas standar. ';
                                analisasHtml += 'Perlu dilakukan monitoring untuk menjaga konsistensi capaian indikator.</span>';
                            }
                        } else {
                            analisasHtml += '<span class="text-danger">';
                            if (hasLastYearData) {
                                analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' belum mencapai target ' + target + ' ' + units + ' yang ditetapkan.<br><br>';
                                if (diff > 0) {
                                    analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + ' ' + units + '), ';
                                    analisasHtml += 'terdapat peningkatan sebesar ' + diff.toFixed(2) + '%. ';
                                    analisasHtml += 'Namun capaian belum memenuhi standar yang ditetapkan.<br><br>';
                                    analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan evaluasi dan perencanaan perbaikan untuk meningkatkan capaian indikator.</span>';
                                } else if (diff < 0) {
                                    analisasHtml += 'Jika dibandingkan dengan tahun sebelumnya (' + lastYearNilai.toFixed(2) + ' ' + units + '), ';
                                    analisasHtml += 'terdapat penurunan sebesar ' + Math.abs(diff).toFixed(2) + '%.<br>';
                                    analisasHtml += 'Penurunan ini memerlukan perhatian serius dan segera.<br><br>';
                                    analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan analisis root cause dan rencana perbaikan segera untuk meningkatkan capaian indikator.</span>';
                                } else {
                                    analisasHtml += 'Capaian stagnan dibandingkan tahun sebelumnya dan masih di bawah standar.<br><br>';
                                    analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan analisis root cause dan rencana perbaikan untuk meningkatkan capaian indikator.</span>';
                                }
                            } else {
                                analisasHtml += 'Capaian indikator sebesar ' + nilai.toFixed(2) + ' ' + units + ' belum mencapai target ' + target + ' ' + units + ' yang ditetapkan.<br><br>';
                                analisasHtml += 'Ini adalah data pertama untuk indikator ini.<br><br>';
                                analisasHtml += '<strong>Kesimpulan:</strong> Diperlukan evaluasi dan perencanaan perbaikan untuk meningkatkan capaian indikator.</span>';
                            }
                        }

                    var keterangan = document.getElementById('keterangan');
                    if (keterangan) keterangan.innerHTML = analisasHtml;
                    
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
                // Use gray for no data, green if achieved, red if not achieved
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
