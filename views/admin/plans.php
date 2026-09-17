<?php
$pageTitle = "Plans & Quotas Configuration | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$auth = new App\Auth();
$db = App\Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = (int)$_POST['plan_id'];
    $price = (float)$_POST['price'];
    $searches = (int)$_POST['searches_per_month'];
    $leads = (int)$_POST['leads_per_month'];
    $audits = (int)$_POST['audits_per_month'];
    $ai = (int)$_POST['ai_pitches_per_month'];
    $exports = (int)$_POST['exports_per_month'];

    $stmt = $db->prepare("UPDATE plans SET price = ?, searches_per_month = ?, leads_per_month = ?, audits_per_month = ?, ai_pitches_per_month = ?, exports_per_month = ? WHERE id = ?");
    $stmt->execute([$price, $searches, $leads, $audits, $ai, $exports, $planId]);

    $_SESSION['flash_success'] = "Plan quotas and pricing updated successfully.";
    header("Location: $adminBase/plans");
    exit;
}

$plans = $auth->getAllPlans();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Plans & Usage Quotas</h3>
        <p class="text-secondary small mb-0">Configure monthly service boundaries, feature entitlements, and subscription pricing.</p>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($plans as $p): ?>
    <div class="col-lg-4">
        <div class="card-saas p-4 h-100">
            <h4 class="fw-bold text-dark mb-1"><?= e($p['name']) ?></h4>
            <p class="text-muted small mb-3">Tier ID: #<?= $p['id'] ?></p>

            <form method="POST">
                <input type="hidden" name="plan_id" value="<?= $p['id'] ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold">Price ($ / Month)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $p['price'] ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Searches / Month</label>
                    <input type="number" name="searches_per_month" class="form-control" value="<?= $p['searches_per_month'] ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Leads / Month</label>
                    <input type="number" name="leads_per_month" class="form-control" value="<?= $p['leads_per_month'] ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">Audits / Month</label>
                    <input type="number" name="audits_per_month" class="form-control" value="<?= $p['audits_per_month'] ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold">AI Pitches / Month</label>
                    <input type="number" name="ai_pitches_per_month" class="form-control" value="<?= $p['ai_pitches_per_month'] ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">CSV Exports / Month</label>
                    <input type="number" name="exports_per_month" class="form-control" value="<?= $p['exports_per_month'] ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Plan Quotas
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
