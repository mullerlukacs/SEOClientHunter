<?php
$pageTitle = "Master Control Dashboard | SEO Client Hunter Admin";
require VIEWS_DIR . '/layout/admin_header.php';

$db = App\Database::getInstance();

// Statistics
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalLeads = $db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$totalAudits = $db->query("SELECT COUNT(*) FROM audits")->fetchColumn();
$totalSearches = $db->query("SELECT COUNT(*) FROM search_logs")->fetchColumn();

// Recent Users
$recentUsers = $db->query("SELECT u.*, p.name as plan_name FROM users u LEFT JOIN plans p ON u.plan_id = p.id ORDER BY u.id DESC LIMIT 5")->fetchAll();

// Recent Searches
$recentSearches = $db->query("SELECT s.*, u.name as user_name FROM search_logs s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.id DESC LIMIT 5")->fetchAll();

// Recent System Logs
$recentLogs = $db->query("SELECT * FROM system_logs ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Platform Master Overview</h3>
        <p class="text-secondary small mb-0">System performance, agency registrations, and automated audit activity.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $adminBase ?>/settings" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa-solid fa-sliders me-1"></i> Global Settings
        </a>
        <a href="<?= $adminBase ?>/api-keys" class="btn btn-primary btn-sm px-3">
            <i class="fa-solid fa-key me-1"></i> API Credentials
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-primary"><?= number_format($totalUsers) ?></div>
                <div class="metric-label">Registered Agencies</div>
            </div>
            <div class="metric-icon bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-success"><?= number_format($totalLeads) ?></div>
                <div class="metric-label">Total Prospects Discovered</div>
            </div>
            <div class="metric-icon bg-success bg-opacity-10 text-success">
                <i class="fa-solid fa-database"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-info"><?= number_format($totalAudits) ?></div>
                <div class="metric-label">Audits Conducted</div>
            </div>
            <div class="metric-icon bg-info bg-opacity-10 text-info">
                <i class="fa-solid fa-file-waveform"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-warning"><?= number_format($totalSearches) ?></div>
                <div class="metric-label">Search Queries Executed</div>
            </div>
            <div class="metric-icon bg-warning bg-opacity-10 text-warning">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card-saas h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">Recent Agency Signups</h5>
                <a href="<?= $adminBase ?>/users" class="small text-primary text-decoration-none">View All Users &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Agency / User</th>
                            <th>Plan</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= e($u['name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= e($u['email']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= e($u['plan_name'] ?: 'Starter') ?></span></td>
                            <td><span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>"><?= e($u['role']) ?></span></td>
                            <td class="text-muted"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Searches -->
    <div class="col-lg-6">
        <div class="card-saas h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">Live Search Queries</h5>
                <a href="<?= $adminBase ?>/logs?tab=search" class="small text-primary text-decoration-none">Search Logs &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Niche / Query</th>
                            <th>Target City</th>
                            <th>User</th>
                            <th>Results</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSearches as $s): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= e($s['query']) ?></td>
                            <td class="text-secondary"><?= e($s['location']) ?></td>
                            <td><?= e($s['user_name'] ?: 'Agency') ?></td>
                            <td><span class="badge bg-success"><?= (int)$s['results_count'] ?> leads</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- System Status & Logs Snapshot -->
<div class="card-saas p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-server text-secondary me-2"></i>System Health & Activity Snapshot</h5>
        <a href="<?= $adminBase ?>/logs" class="btn btn-sm btn-outline-secondary">All System Logs</a>
    </div>
    <?php if (empty($recentLogs)): ?>
        <p class="text-muted small mb-0"><i class="fa-solid fa-circle-check text-success me-1"></i> All systems running nominally. No critical errors logged.</p>
    <?php else: ?>
        <div class="list-group list-group-flush small">
            <?php foreach ($recentLogs as $log): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                    <span class="badge <?= $log['level'] === 'critical' ? 'bg-danger' : ($log['level'] === 'error' ? 'bg-warning text-dark' : 'bg-secondary') ?> me-2">
                        <?= strtoupper($log['level']) ?>
                    </span>
                    <span class="text-dark"><?= e($log['message']) ?></span>
                </div>
                <small class="text-muted"><?= date('M j, g:i A', strtotime($log['created_at'])) ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
