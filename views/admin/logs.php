<?php
$pageTitle = "System & Activity Logs | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$db = App\Database::getInstance();
$tab = $_GET['tab'] ?? 'system';

$systemLogs = $db->query("SELECT * FROM system_logs ORDER BY id DESC LIMIT 50")->fetchAll();
$activityLogs = $db->query("SELECT a.*, u.name as user_name FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 50")->fetchAll();
$searchLogs = $db->query("SELECT s.*, u.name as user_name FROM search_logs s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.id DESC LIMIT 50")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Audit & Event Logs</h3>
        <p class="text-secondary small mb-0">Inspect technical system errors, user account events, and lead search requests.</p>
    </div>
</div>

<div class="card-saas mb-4">
    <div class="border-bottom px-3">
        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <a class="nav-link <?= ($tab === 'system') ? 'active fw-bold' : 'text-secondary' ?> py-3" href="?tab=system">
                    <i class="fa-solid fa-server me-2"></i> System & Error Logs (<?= count($systemLogs) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($tab === 'activity') ? 'active fw-bold' : 'text-secondary' ?> py-3" href="?tab=activity">
                    <i class="fa-solid fa-users me-2"></i> User Activity (<?= count($activityLogs) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($tab === 'search') ? 'active fw-bold' : 'text-secondary' ?> py-3" href="?tab=search">
                    <i class="fa-solid fa-magnifying-glass me-2"></i> Search History (<?= count($searchLogs) ?>)
                </a>
            </li>
        </ul>
    </div>

    <div class="p-4">
        <?php if ($tab === 'system'): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Level</th>
                            <th>Message</th>
                            <th>Context</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($systemLogs)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No system logs recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($systemLogs as $log): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $log['level'] === 'critical' ? 'bg-danger' : ($log['level'] === 'error' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                        <?= strtoupper($log['level']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark"><?= e($log['message']) ?></td>
                                <td><code class="small text-truncate d-inline-block" style="max-width: 300px;"><?= e($log['context']) ?></code></td>
                                <td class="text-muted"><?= date('M j, Y g:i:s A', strtotime($log['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($tab === 'activity'): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($activityLogs)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No user activity recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($activityLogs as $act): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= e($act['user_name'] ?: 'Guest') ?></td>
                                <td><span class="badge bg-light text-dark border"><?= e($act['action']) ?></span></td>
                                <td><?= e($act['details']) ?></td>
                                <td class="text-muted"><?= e($act['ip_address']) ?></td>
                                <td class="text-muted"><?= date('M j, Y g:i A', strtotime($act['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Query / Keyword</th>
                            <th>Target City</th>
                            <th>User</th>
                            <th>Results Count</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($searchLogs)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No searches logged yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($searchLogs as $s): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= e($s['query']) ?></td>
                                <td><?= e($s['location']) ?></td>
                                <td><?= e($s['user_name'] ?: 'Agency') ?></td>
                                <td><span class="badge bg-success"><?= (int)$s['results_count'] ?> leads</span></td>
                                <td class="text-muted"><?= date('M j, Y g:i A', strtotime($s['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
