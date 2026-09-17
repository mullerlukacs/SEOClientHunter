<?php
$pageTitle = "Master Leads Pool | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$db = App\Database::getInstance();
$leads = $db->query("SELECT l.*, u.name as user_name FROM leads l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.id DESC LIMIT 100")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Master Leads Pool</h3>
        <p class="text-secondary small mb-0">System-wide directory of prospects discovered across all registered user accounts.</p>
    </div>
</div>

<div class="card-saas">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-uppercase text-secondary">
                <tr>
                    <th class="ps-3">Company & Domain</th>
                    <th>Niche / City</th>
                    <th>Owner Account</th>
                    <th>SEO Score</th>
                    <th>Opportunity</th>
                    <th>Email</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $l): ?>
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark"><?= e($l['company_name']) ?></div>
                        <a href="<?= e($l['website']) ?>" target="_blank" class="text-muted small"><?= e(clean_domain($l['website'])) ?></a>
                    </td>
                    <td><?= e($l['niche']) ?> &bull; <?= e($l['city']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= e($l['user_name'] ?: 'System') ?></span></td>
                    <td><span class="score-pill score-med"><?= (int)$l['seo_score'] ?>/100</span></td>
                    <td><span class="badge badge-opp-high"><?= (int)$l['opportunity_score'] ?>/100</span></td>
                    <td><?= e($l['email'] ?: '—') ?></td>
                    <td class="text-muted"><?= date('M j, Y', strtotime($l['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
