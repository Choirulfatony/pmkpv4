<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Rekap Laporan Insiden Tahun <?= $tahun ?></h4>
            <p class="text-muted">Data insiden berdasarkan tingkat kejadian</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Grafik Rekap Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="rekapChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tabel Rekap per Bulan</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Bulan</th>
                                <th class="text-center">Total</th>
                                <th class="text-center text-danger">Kritis</th>
                                <th class="text-center text-warning">Berat</th>
                                <th class="text-center text-info">Sedang</th>
                                <th class="text-center text-success">Ringan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; $bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des']; ?>
                            <?php if (empty($rekap)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rekap as $r): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="text-center"><?= $bulanNama[$r->bulan] ?></td>
                                        <td class="text-center"><?= $r->total ?></td>
                                        <td class="text-center text-danger fw-bold"><?= $r->kritis ?></td>
                                        <td class="text-center text-warning fw-bold"><?= $r->berat ?></td>
                                        <td class="text-center text-info fw-bold"><?= $r->sedang ?></td>
                                        <td class="text-center text-success fw-bold"><?= $r->ringan ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('rekapChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= $dataBulan ?>,
            datasets: [
                {
                    label: 'Kritis',
                    data: <?= $dataKritis ?>,
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    borderWidth: 1
                },
                {
                    label: 'Berat',
                    data: <?= $dataBerat ?>,
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1
                },
                {
                    label: 'Sedang',
                    data: <?= $dataSedang ?>,
                    backgroundColor: '#0dcaf0',
                    borderColor: '#0dcaf0',
                    borderWidth: 1
                },
                {
                    label: 'Ringan',
                    data: <?= $dataRingan ?>,
                    backgroundColor: '#198754',
                    borderColor: '#198754',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: { stacked: true },
                y: { 
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
