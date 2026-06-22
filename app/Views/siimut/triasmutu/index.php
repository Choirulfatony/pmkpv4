<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-1">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Trias Mutu
                    </h5>
                    <small class="text-muted">Dokumen analisis indikator mutu per triwulan</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?= site_url('siimut/trias-mutu/pengukuran') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Buat Dokumen Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Unit</th>
                            <th>Indikator</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($documents as $doc): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($doc['unit_name'] ?? '-') ?></td>
                                <td><?= esc($doc['indicator_name'] ?? '-') ?></td>
                                <td>
                                    Triwulan <?= $doc['triwulan'] ?> / <?= $doc['tahun'] ?>
                                </td>
                                <td>
                                    <?php if ($doc['status'] === 'final'): ?>
                                        <span class="badge bg-success">Final</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($doc['created_by_name'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('siimut/trias-mutu/cetak?dokumen_id=' . $doc['id']) ?>"
                                           class="btn btn-info"
                                           title="Preview">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= site_url('siimut/trias-mutu/pengukuran?dokumen_id=' . $doc['id']) ?>"
                                           class="btn btn-outline-primary"
                                           title="Pengukuran">
                                            <i class="bi bi-bar-chart"></i>
                                        </a>
                                        <a href="<?= site_url('siimut/trias-mutu/analisis-penyebab?dokumen_id=' . $doc['id']) ?>"
                                           class="btn btn-outline-warning"
                                           title="Analisis Penyebab">
                                            <i class="bi bi-diagram-3"></i>
                                        </a>
                                        <a href="<?= site_url('siimut/trias-mutu/pdsa?dokumen_id=' . $doc['id']) ?>"
                                           class="btn btn-outline-success"
                                           title="PDSA">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada dokumen Trias Mutu.
                                    <a href="<?= site_url('siimut/trias-mutu/pengukuran') ?>">Buat baru</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
