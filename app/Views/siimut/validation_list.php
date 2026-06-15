<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad((string)$m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == $bulan ? 'selected' : '' ?>>
                            <?= $namaBulan[$m] ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
            </div>
        </form>

        <?php if (empty($data)): ?>
            <div class="alert alert-info mb-0">Tidak ada data menunggu validasi untuk periode ini</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Indikator</th>
                            <th>Unit</th>
                            <th>Jumlah Record</th>
                            <th>Status Validasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($row->indicator_element ?? '-') ?></td>
                                <td><?= esc($row->department_name ?? '-') ?></td>
                                <td><?= $row->total_records ?></td>
                                <td>
                                    <?php if (!empty($row->validation_result)): ?>
                                        <?php if ($row->validation_result === 'valid'): ?>
                                            <span class="badge bg-success">Tervalidasi (<?= number_format($row->validation_score, 1) ?>%)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Valid (<?= number_format($row->validation_score, 1) ?>%)</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Menunggu Validasi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('siimut/validation/' . $module . '/form?indicator_id=' . $row->result_indicator_id . '&department_id=' . $row->result_department_id . '&tahun=' . $tahun . '&bulan=' . $bulan) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-circle"></i> Validasi
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

