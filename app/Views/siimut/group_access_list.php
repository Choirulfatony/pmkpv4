<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-diagram-3"></i> Akses Departemen per Grup
            </h6>
            <p class="text-muted small mb-0 mt-1">Atur departemen mana saja yang bisa diakses oleh masing-masing grup.</p>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama Grup</th>
                            <th style="width:200px">Jumlah Departemen</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($groups as $g): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($g->group_name) ?></td>
                                <td id="dept-count-<?= $g->group_id ?>">
                                    <span class="badge bg-info"><i class="bi bi-hourglass-split"></i> Memuat...</span>
                                </td>
                                <td>
                                    <a href="<?= site_url('siimut/group-access/edit/' . $g->group_id) ?>" class="btn btn-sm btn-outline-primary" title="Atur Departemen">
                                        <i class="bi bi-gear"></i> Atur
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($groups)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada grup</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    <?php foreach ($groups as $g): ?>
        $.ajax({
            url: '<?= site_url('siimut/group-access/ajax-get-departments/' . $g->group_id) ?>',
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    var count = res.data.length;
                    var html = count > 0
                        ? '<span class="badge bg-success">' + count + ' departemen</span>'
                        : '<span class="badge bg-secondary">Belum diatur</span>';
                    $('#dept-count-<?= $g->group_id ?>').html(html);
                }
            }
        });
    <?php endforeach; ?>
});
</script>
