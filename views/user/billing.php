<?php
$pageTitle = "Subscription & Quotas | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$auth = new App\Auth();
$limits = $auth->getUserPlanLimits((int)$currentUser['id']);
$allPlans = $auth->getAllPlans();
$currentPlanId = $limits['plan']['id'] ?? 1;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Subscription & Monthly Quotas</h3>
        <p class="text-secondary small mb-0">Track your search consumption and upgrade your plan to unlock more prospecting volume.</p>
    </div>
</div>

<!-- Current Plan Overview Card -->
<div class="card-saas p-4 mb-4 bg-primary text-white">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <span class="badge bg-white text-primary text-uppercase fw-bold mb-2">Current Active Plan</span>
            <h2 class="fw-bold text-white mb-1"><?= e($limits['plan']['name'] ?? 'Starter') ?></h2>
            <div class="text-white-50 small">
                $<?= number_format((float)($limits['plan']['price'] ?? 0), 2) ?> / month &bull; Billed Monthly
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success bg-opacity-25 text-white border border-light border-opacity-25 px-3 py-2 fs-6">
                <i class="fa-solid fa-circle-check me-1"></i> Account Status: Active
            </span>
        </div>
    </div>
</div>

<!-- Plan Limits & Usage Status -->
<div class="row g-4 mb-5">
    <?php
    $usages = [
        ['label' => 'Searches / Month', 'used' => $limits['usage']['searches'], 'limit' => $limits['limits']['searches_per_month'], 'icon' => 'fa-magnifying-glass'],
        ['label' => 'Leads / Month', 'used' => $limits['usage']['leads'], 'limit' => $limits['limits']['leads_per_month'], 'icon' => 'fa-database'],
        ['label' => 'Audits / Month', 'used' => $limits['usage']['audits'], 'limit' => $limits['limits']['audits_per_month'], 'icon' => 'fa-chart-line'],
        ['label' => 'AI Pitches / Month', 'used' => $limits['usage']['ai_pitches'], 'limit' => $limits['limits']['ai_pitches_per_month'], 'icon' => 'fa-wand-magic-sparkles'],
    ];

    foreach ($usages as $u):
        $pct = min(100, round(($u['used'] / max(1, $u['limit'])) * 100));
    ?>
    <div class="col-xl-3 col-sm-6">
        <div class="card-saas p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold"><?= $u['label'] ?></span>
                <i class="fa-solid <?= $u['icon'] ?> text-muted"></i>
            </div>
            <div class="display-6 fw-bold text-dark mb-2"><?= $u['used'] ?> <span class="fs-6 text-muted fw-normal">/ <?= $u['limit'] ?></span></div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar <?= $pct > 80 ? 'bg-danger' : ($pct > 50 ? 'bg-warning' : 'bg-primary') ?>" style="width: <?= $pct ?>%"></div>
            </div>
            <small class="text-muted mt-2 d-block"><?= $pct ?>% utilized this billing period</small>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<h4 class="fw-bold text-dark mb-3">Available Agency Upgrades</h4>
<div class="row g-4">
    <?php foreach ($allPlans as $plan):
        $isCurrent = ($plan['id'] == $currentPlanId);
    ?>
    <div class="col-lg-4 col-md-6">
        <div class="card-saas p-4 h-100 d-flex flex-column <?= $isCurrent ? 'border-primary border-2 shadow-sm' : '' ?>">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold text-dark mb-0"><?= e($plan['name']) ?></h5>
                <?php if ($isCurrent): ?>
                    <span class="badge bg-primary">Current Plan</span>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <span class="display-6 fw-bold text-dark">$<?= number_format((float)$plan['price']) ?></span>
                <span class="text-muted">/ month</span>
            </div>
            <ul class="list-unstyled text-secondary small mb-4 flex-grow-1">
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong><?= number_format($plan['searches_per_month']) ?></strong> Searches / mo</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong><?= number_format($plan['leads_per_month']) ?></strong> Leads / mo</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong><?= number_format($plan['audits_per_month']) ?></strong> Audits / mo</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong><?= number_format($plan['ai_pitches_per_month']) ?></strong> AI Pitches / mo</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong><?= number_format($plan['exports_per_month']) ?></strong> CSV Exports / mo</li>
            </ul>

            <form method="POST" action="/billing">
                <input type="hidden" name="action" value="upgrade_plan">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                <button type="submit" class="btn <?= $isCurrent ? 'btn-outline-secondary' : 'btn-primary' ?> w-100" <?= $isCurrent ? 'disabled' : '' ?>>
                    <?= $isCurrent ? 'Active Subscription' : 'Upgrade to ' . e($plan['name']) ?>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
