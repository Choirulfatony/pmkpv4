<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <a href="<?= site_url('siimut/validation/' . $module) ?>?tahun=<?= $tahun ?>&bulan=<?= $bulan ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Info Indikator -->
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width:150px;">Indikator</th>
                        <td>: <?= esc($info->indicator_element ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Unit</th>
                        <td>: <?= esc($deptInfo->department_name ?? $departmentId) ?></td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>: <?= $namaBulan[(int)$bulan] ?> <?= $tahun ?></td>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <td>: <?= esc($info->indicator_target ?? '-') ?> <?= esc($info->indicator_units ?? '') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if (!empty($existing)): ?>
            <div class="alert alert-info">
                <strong>Validasi sebelumnya:</strong>
                <?php if ($existing->validation_result === 'valid'): ?>
                    <span class="badge bg-success">Tervalidasi</span>
                <?php else: ?>
                    <span class="badge bg-danger">Tidak Valid</span>
                <?php endif; ?>
                — Skor: <?= number_format($existing->validation_score, 1) ?>%,
                Tanggal: <?= $existing->validation_date ?>
                <?php if ($existing->validation_note): ?>
                    <br><small>Catatan: <?= esc($existing->validation_note) ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Daftar Record -->
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Numerator</th>
                        <th>Denominator</th>
                        <th>Hasil (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data draft untuk periode ini</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($records as $r): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($r->result_period)) ?></td>
                                <td><?= esc($r->result_numerator_value) ?></td>
                                <td><?= esc($r->result_denumerator_value) ?></td>
                                <td><?= number_format((float)$r->result_percentage, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Form Validasi -->
        <form id="formValidation">
            <input type="hidden" name="module" value="<?= $module ?>">
            <input type="hidden" name="indicator_id" value="<?= $indicatorId ?>">
            <input type="hidden" name="department_id" value="<?= $departmentId ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="sample_count" value="<?= count($records) ?>">

            <div class="card mb-3">
                <div class="card-header fw-bold">Checklist Validasi</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:60%;">Komponen</th>
                                <th style="width:40%;">Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Data Lengkap</td>
                                <td>
                                    <select name="data_lengkap" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ya">Ya</option>
                                        <option value="tidak">Tidak</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Sumber Data Sesuai</td>
                                <td>
                                    <select name="sumber_data_sesuai" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ya">Ya</option>
                                        <option value="tidak">Tidak</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Numerator Sesuai</td>
                                <td>
                                    <select name="numerator_sesuai" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ya">Ya</option>
                                        <option value="tidak">Tidak</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Denominator Sesuai</td>
                                <td>
                                    <select name="denominator_sesuai" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ya">Ya</option>
                                        <option value="tidak">Tidak</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Perhitungan Benar</td>
                                <td>
                                    <select name="perhitungan_benar" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ya">Ya</option>
                                        <option value="tidak">Tidak</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Catatan Validator</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Catatan jika ada..."></textarea>
            </div>

            <!-- Skor Otomatis -->
            <div class="alert alert-secondary" id="skorContainer" style="display:none;">
                <strong>Skor Validitas: <span id="skorValue">0</span>%</strong>
                <br>
                <span id="skorKriteria"></span>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan Validasi
                </button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checks = document.querySelectorAll('#formValidation select[name]');
        checks.forEach(function(el) {
            el.addEventListener('change', hitungSkor);
        });
    });

    function hitungSkor() {
        var fields = ['data_lengkap', 'sumber_data_sesuai', 'numerator_sesuai', 'denominator_sesuai', 'perhitungan_benar'];
        var total = fields.length;
        var sesuai = 0;

        fields.forEach(function(f) {
            var val = document.querySelector('[name="' + f + '"]').value;
            if (val === 'ya') sesuai++;
        });

        var skor = (sesuai / total) * 100;
        document.getElementById('skorValue').textContent = skor.toFixed(1);
        document.getElementById('skorContainer').style.display = skor >= 0 ? '' : 'none';

        var kriteria = '';
        if (skor >= 90) {
            kriteria = '<span class="text-success fw-bold">VALID</span>';
        } else if (skor >= 80) {
            kriteria = '<span class="text-warning fw-bold">PERLU PERBAIKAN</span>';
        } else {
            kriteria = '<span class="text-danger fw-bold">TIDAK VALID</span>';
        }
        document.getElementById('skorKriteria').innerHTML = kriteria;
    }

    document.getElementById('formValidation').addEventListener('submit', function(e) {
        e.preventDefault();

        var selects = this.querySelectorAll('select[required]');
        for (var i = 0; i < selects.length; i++) {
            if (!selects[i].value) {
                Swal.fire({ title: 'Info', text: 'Semua checklist harus diisi', icon: 'warning' });
                return;
            }
        }

        var formData = new FormData(this);
        var params = new URLSearchParams();
        for (var pair of formData.entries()) {
            params.append(pair[0], pair[1]);
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= site_url('siimut/validation/' . $module . '/save') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.status) {
                    var icon = resp.result === 'valid' ? 'success' : 'warning';
                    var title = resp.result === 'valid' ? 'Validasi Berhasil' : 'Validasi Dicatat';
                    Swal.fire({ title: title, text: resp.message + ' (Skor: ' + resp.score + '%)', icon: icon });
                } else {
                    Swal.fire({ title: 'Gagal', text: resp.message, icon: 'error' });
                }
            }
        };
        xhr.send(params.toString());
    });
</script>

