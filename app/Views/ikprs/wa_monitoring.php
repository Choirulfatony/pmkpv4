<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= base_url('assets/login/img/rssm.ico') ?> " type="image/x-icon" />
    <title>Monitoring WhatsApp Messages</title>
    <link href="<?= base_url('assets/adminlte/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/adminlte/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
</head>

<body>
    <?php
    if (!function_exists('buildQueryString')) {
        function buildQueryString($status, $search, $per_page) {
            $params = [];
            if (!empty($status)) $params[] = 'status=' . urlencode($status);
            if (!empty($search)) $params[] = 'search=' . urlencode($search);
            if ($per_page && $per_page != 20) $params[] = 'per_page=' . $per_page;
            return count($params) > 0 ? '&' . implode('&', $params) : '';
        }
    }
    ?>
    <div class="container mt-4">
        <h3>📱 Monitoring WhatsApp Messages</h3>

        <div class="card mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <span><strong>Filter Status</strong></span>
                    <div>
                        <button onclick="reloadData()" class="btn btn-sm btn-outline-primary me-2" id="btnReload">
                            <i class="bi bi-arrow-clockwise"></i> <span id="reloadText">Reload</span>
                        </button>
                        <a href="<?= site_url('ikprs/wa-monitoring') ?>" class="btn btn-sm <?= empty($current_status) ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
                        <a href="<?= site_url('ikprs/wa-monitoring?status=SENT') ?>" class="btn btn-sm <?= $current_status == 'SENT' ? 'btn-success' : 'btn-outline-success' ?>">Sent</a>
                        <a href="<?= site_url('ikprs/wa-monitoring?status=PENDING') ?>" class="btn btn-sm <?= $current_status == 'PENDING' ? 'btn-warning' : 'btn-outline-warning' ?>">Pending</a>
                        <a href="<?= site_url('ikprs/wa-monitoring?status=FAILED') ?>" class="btn btn-sm <?= $current_status == 'FAILED' ? 'btn-danger' : 'btn-outline-danger' ?>">Failed</a>
                        <a href="<?= site_url('ikprs/wa-monitoring?status=NO_PHONE') ?>" class="btn btn-sm <?= $current_status == 'NO_PHONE' ? 'btn-secondary' : 'btn-outline-secondary' ?>">No Phone</a>
                    </div>
                </div>

                <!-- Search & Per Page -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <form method="GET" action="<?= site_url('ikprs/wa-monitoring') ?>" class="d-flex gap-2">
                        <?php if (!empty($current_status)): ?>
                            <input type="hidden" name="status" value="<?= esc($current_status) ?>">
                        <?php endif; ?>
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, pesan, type..." value="<?= esc($current_search ?? '') ?>">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                            <?php if (!empty($current_search)): ?>
                                <a href="<?= site_url('ikprs/wa-monitoring' . (!empty($current_status) ? '?status=' . $current_status : '')) ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Tampilkan:</label>
                        <select id="perPageSelect" class="form-select form-select-sm" style="width: 80px;" onchange="changePerPage(this.value)">
                            <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $per_page == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $per_page == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <span class="small text-muted">per halaman</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>WA Status</th>
                                <th>Retry</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No data found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data as $i => $row): ?>
                                    <tr>
                                        <td><?= ($page - 1) * 20 + $i + 1 ?></td>
                                        <td>
                                            <strong><?= esc($row['nama'] ?? 'User ' . $row['hris_user_id']) ?></strong><br>
                                            <small class="text-muted">ID: <?= $row['hris_user_id'] ?></small>
                                        </td>
                                        <td style="max-width: 300px;">
                                            <div class="text-wrap">
                                                <?= esc($row['pesan']) ?>
                                            </div>
                                            <?php if (!empty($row['wa_error'])): ?>
                                                <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc(substr($row['wa_error'], 0, 50)) ?><?= strlen($row['wa_error']) > 50 ? '...' : '' ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-info"><?= esc($row['type']) ?></span></td>
                                        <td>
                                            <?php
                                            switch ($row['wa_status']) {
                                                case 'SENT':
                                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Sent</span>';
                                                    break;
                                                case 'PENDING':
                                                    echo '<span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>';
                                                    break;
                                                case 'FAILED':
                                                    echo '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>';
                                                    break;
                                                case 'NO_PHONE':
                                                    echo '<span class="badge bg-secondary"><i class="bi bi-phone-slash"></i> No Phone</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-light">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $row['retry_count'] > 0 ? 'warning' : 'light' ?>"><?= $row['retry_count'] ?></span>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if (in_array($row['wa_status'], ['PENDING', 'FAILED']) && $row['retry_count'] < 3): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="retryMessage(<?= $row['id'] ?>)">
                                                    <i class="bi bi-arrow-clockwise"></i> Retry
                                                </button>
                                            <?php elseif (!empty($row['wa_message_id'])): ?>
                                                <small class="text-muted"><?= esc(substr($row['wa_message_id'], 0, 10)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <div class="small text-muted">
                        Total: <strong><?= $total ?></strong> data
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=' . ($page - 1) . buildQueryString($current_status, $current_search, $per_page)) ?>">&laquo;</a>
                                    </li>
                                <?php endif; ?>
                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);
                                if ($start > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=1' . buildQueryString($current_status, $current_search, $per_page)) ?>">1</a></li>
                                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                                <?php endif; ?>
                                <?php for ($p = $start; $p <= $end; $p++): ?>
                                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=' . $p . buildQueryString($current_status, $current_search, $per_page)) ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($end < $total_pages): ?>
                                    <?php if ($end < $total_pages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=' . $total_pages . buildQueryString($current_status, $current_search, $per_page)) ?>"><?= $total_pages ?></a></li>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=' . ($page + 1) . buildQueryString($current_status, $current_search, $per_page)) ?>">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="<?= site_url('ikprs/menu') ?>" class="btn btn-secondary">← Back</a>
        </div>
    </div>

    <script src="<?= base_url('assets/adminlte/plugins/bootstrap.bundle.min.js') ?>"></script>
    <script>
        function buildQueryString(status, search, per_page) {
            let params = [];
            if (status) params.push('status=' + encodeURIComponent(status));
            if (search) params.push('search=' + encodeURIComponent(search));
            if (per_page && per_page != 20) params.push('per_page=' + per_page);
            return params.length > 0 ? '&' + params.join('&') : '';
        }

        function changePerPage(val) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('per_page', val);
            urlParams.set('page', '1');
            window.location.href = '<?= site_url('ikprs/wa-monitoring') ?>?' + urlParams.toString();
        }

        function reloadData() {
            const btn = document.getElementById('btnReload');
            const reloadText = document.getElementById('reloadText');
            const icon = btn.querySelector('i');

            // Show loading state
            btn.disabled = true;
            icon.classList.add('spin');
            reloadText.textContent = 'Loading...';

            // Get current URL params
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('format', 'json');

            fetch('<?= site_url('ikprs/wa-monitoring') ?>?' + urlParams.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        updateTable(data.data);
                        // Update pagination info
                        console.log('Reloaded: ' + data.total + ' records');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to reload data');
                })
                .finally(() => {
                    btn.disabled = false;
                    icon.classList.remove('spin');
                    reloadText.textContent = 'Reload';
                });
        }

        // Search on Enter key
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.form.submit();
                    }
                });
            }
        });

        function updateTable(data) {
            const tbody = document.querySelector('table tbody');
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No data found</td></tr>';
                return;
            }

            let html = '';
            data.forEach((row, i) => {
                const statusBadge = getStatusBadge(row.wa_status);
                const userName = row.nama ? row.nama : 'User ' + row.hris_user_id;
                const message = row.pesan.length > 50 ? row.pesan.substring(0, 50) + '...' : row.pesan;
                const errorInfo = row.wa_error ? '<br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> ' + row.wa_error.substring(0, 30) + '...</small>' : '';
                const retryBtn = (['PENDING', 'FAILED'].includes(row.wa_status) && row.retry_count < 3) ?
                    '<button class="btn btn-sm btn-outline-primary" onclick="retryMessage(' + row.id + ')"><i class="bi bi-arrow-clockwise"></i> Retry</button>' :
                    (row.wa_message_id ? '<small class="text-muted">' + row.wa_message_id.substring(0, 10) + '...</small>' : '');
                const date = new Date(row.created_at).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                html += '<tr>' +
                    '<td>' + ((<?= ($page - 1) * 20 ?>) + i + 1) + '</td>' +
                    '<td><strong>' + userName + '</strong><br><small class="text-muted">ID: ' + row.hris_user_id + '</small></td>' +
                    '<td>' + message + errorInfo + '</td>' +
                    '<td><span class="badge bg-info">' + row.type + '</span></td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td class="text-center"><span class="badge bg-' + (row.retry_count > 0 ? 'warning' : 'light') + '">' + row.retry_count + '</span></td>' +
                    '<td><small>' + date + '</small></td>' +
                    '<td>' + retryBtn + '</td>' +
                    '</tr>';
            });

            tbody.innerHTML = html;
        }

        function getStatusBadge(status) {
            switch (status) {
                case 'SENT':
                    return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Sent</span>';
                case 'PENDING':
                    return '<span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>';
                case 'FAILED':
                    return '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>';
                case 'NO_PHONE':
                    return '<span class="badge bg-secondary"><i class="bi bi-phone-slash"></i> No Phone</span>';
                default:
                    return '<span class="badge bg-light">-</span>';
            }
        }

        function retryMessage(id) {
            if (!confirm('Kirim ulang WA ini?')) return;

            const btn = document.querySelector(`button[onclick="retryMessage(${id})"]`);
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';

            fetch('<?= site_url('ikprs/wa-retry') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'notif_id=' + id + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    showToast('success', 'WA berhasil dikirim ulang');
                } else {
                    showToast('error', data.message || 'Gagal mengirim WA');
                }
                setTimeout(() => reloadData(), 1000);
            })
            .catch(err => {
                showToast('error', 'Network error: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Retry';
            });
        }

        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast.querySelector('.toast'));
            bsToast.show();
            setTimeout(() => toast.remove(), 3000);
        }

        // Add spin animation
        const style = document.createElement('style');
        style.textContent = '.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    </script>
</body>

</html>