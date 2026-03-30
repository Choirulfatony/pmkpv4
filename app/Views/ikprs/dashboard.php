<?php
$jenisInsidenText = [
    'KNC' => 'Kejadian Nyaris Cedera (Near Miss)',
    'KTD' => 'Kejadian Tidak Diharapkan (Adverse Event)',
    'KTC' => 'Kejadian Tidak Cedera',
    'KPC' => 'Kejadian Potensi Cedera',
    'Sentinel' => 'Kejadian Sentinel'
];

$labels = $chartData['labels'] ?? [];
$datasets = $chartData['datasets'] ?? [];
$totalAll = 0;
foreach ($datasets as $ds) {
    $totalAll += array_sum($ds['data']);
}
?>
<!-- begin::Container-->
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-graph-up"></i> Trend Insiden per Tahun (Status SELESAI)</h3>
                </div>
                <div class="card-body">
                    <?php if ($totalAll > 0): ?>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="chartTrendInsiden"></canvas>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">Ringkasan per Jenis Insiden</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle">Jenis Insiden</th>
                                        <?php foreach ($labels as $tahun): ?>
                                        <th class="text-center"><?= esc($tahun) ?></th>
                                        <?php endforeach; ?>
                                        <th class="text-center bg-light">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datasets as $ds): ?>
                                    <tr>
                                        <td><?= esc($jenisInsidenText[$ds['jenis']] ?? $ds['jenis']) ?></td>
                                        <?php 
                                        $rowTotal = 0;
                                        foreach ($ds['data'] as $jml): 
                                            $rowTotal += $jml;
                                        ?>
                                        <td class="text-center"><?= $jml > 0 ? '<span class="badge bg-primary">'.$jml.'</span>' : '-' ?></td>
                                        <?php endforeach; ?>
                                        <td class="text-center bg-light"><strong><?= $rowTotal ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Belum ada laporan dengan status SELESAI.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
</div>
<!--end::Container-->

<?php if ($totalAll > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartTrendInsiden').getContext('2d');
    const labels = <?= json_encode($labels) ?>;
    
    const rawDatasets = <?= json_encode(array_map(function($ds) use ($jenisInsidenText) {
        return [
            'label' => $jenisInsidenText[$ds['jenis']] ?? $ds['jenis'],
            'data' => $ds['data'],
            'jenis' => $ds['jenis']
        ];
    }, $datasets)) ?>;

    function matchColor(jenis, alpha = 1) {
        const colors = {
            'KNC': `rgba(13, 110, 253, ${alpha})`,
            'KTD': `rgba(220, 53, 69, ${alpha})`,
            'KTC': `rgba(25, 135, 84, ${alpha})`,
            'KPC': `rgba(255, 193, 7, ${alpha})`,
            'Sentinel': `rgba(111, 66, 193, ${alpha})`
        };
        return colors[jenis] || `rgba(108, 117, 125, ${alpha})`;
    }

    const datasets = rawDatasets.map(ds => ({
        label: ds.label,
        data: ds.data,
        borderColor: matchColor(ds.jenis),
        backgroundColor: matchColor(ds.jenis, 0.1),
        tension: 0.3,
        fill: false
    }));

    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Trend Insiden per Tahun' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
