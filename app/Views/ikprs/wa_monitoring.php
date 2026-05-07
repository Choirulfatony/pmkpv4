<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring WhatsApp Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>📱 Monitoring WhatsApp Messages</h3>
        
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
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
                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc(substr($row['wa_error'],0, 50)) ?><?= strlen($row['wa_error']) > 50 ? '...' : '' ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-info"><?= esc($row['type']) ?></span></td>
                                <td>
                                    <?php
                                    switch($row['wa_status']) {
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
                                    <?php if ($row['wa_status'] == 'PENDING' && $row['retry_count'] < 3): ?>
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
                <?php if ($total_pages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= site_url('ikprs/wa-monitoring?page=' . $p . (!empty($current_status) ? '&status=' . $current_status : '')) ?>"><?= $p ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?= site_url('ikprs/menu') ?>" class="btn btn-secondary">← Back</a>
        </div>
    </div>
    
    <script>
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
                const retryBtn = (row.wa_status == 'PENDING' && row.retry_count < 3) ? 
                    '<button class="btn btn-sm btn-outline-primary" onclick="retryMessage(' + row.id + ')"><i class="bi bi-arrow-clockwise"></i> Retry</button>' : 
                    (row.wa_message_id ? '<small class="text-muted">' + row.wa_message_id.substring(0, 10) + '...</small>' : '');
                const date = new Date(row.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
                
                 html += '<tr>' +
                    '<td>' + ((<?= ($page-1)*20 ?>) + i + 1) + '</td>' +
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
            switch(status) {
                case 'SENT': return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Sent</span>';
                case 'PENDING': return '<span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>';
                case 'FAILED': return '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>';
                case 'NO_PHONE': return '<span class="badge bg-secondary"><i class="bi bi-phone-slash"></i> No Phone</span>';
                default: return '<span class="badge bg-light">-</span>';
            }
        }
        
        function retryMessage(id) {
            if (confirm('Retry sending this message?')) {
                alert('Retry function not implemented yet. Message ID: ' + id);
            }
        }
        
        // Add spin animation
        const style = document.createElement('style');
        style.textContent = '.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    </script>
</body>
</html>
