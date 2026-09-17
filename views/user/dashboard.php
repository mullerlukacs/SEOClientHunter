<?php
$pageTitle = "Agency Dashboard | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$crm = new App\CRM();
$stats = $crm->getDashboardStats((int)$currentUser['id']);
$recentLeads = $crm->getLeads((int)$currentUser['id'], ['limit' => 6]);
$activities = $crm->getActivities((int)$currentUser['id'], 6);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Agency Prospecting Hub</h3>
        <p class="text-secondary small mb-0">Overview of client acquisition pipeline and automated technical audits</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/search" class="btn btn-primary px-3 py-2 fw-semibold">
            <i class="fa-solid fa-magnifying-glass-location me-1"></i> New Lead Hunt
        </a>
        <a href="/export" class="btn btn-outline-secondary px-3 py-2">
            <i class="fa-solid fa-file-arrow-down me-1"></i> Export CRM
        </a>
    </div>
</div>

<!-- Metric Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val"><?= number_format($stats['total_leads']) ?></div>
                <div class="metric-label">Total Prospects Discovered</div>
            </div>
            <div class="metric-icon bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-database"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-danger"><?= number_format($stats['high_opportunity']) ?></div>
                <div class="metric-label">High Opportunity Prospects</div>
            </div>
            <div class="metric-icon bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-bullseye"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-warning"><?= number_format($stats['contacted']) ?></div>
                <div class="metric-label">Outreach Initiated</div>
            </div>
            <div class="metric-icon bg-warning bg-opacity-10 text-warning">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="metric-card">
            <div>
                <div class="metric-val text-success"><?= number_format($stats['audits_run']) ?></div>
                <div class="metric-label">Audits Conducted</div>
            </div>
            <div class="metric-icon bg-success bg-opacity-10 text-success">
                <i class="fa-solid fa-file-waveform"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Hunt & Live Audit Tool Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-saas p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-magnifying-glass-location text-primary me-2"></i>Quick Lead Hunter</h5>
                <a href="/search" class="small text-decoration-none text-primary fw-semibold">Advanced Filters &rarr;</a>
            </div>
            <p class="text-secondary small mb-3">Launch a targeted crawl for local businesses with severe SEO deficiencies.</p>

            <form action="/search" method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="niche" class="form-control" placeholder="Niche (e.g. Dentists, Roofers)" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="city" class="form-control" placeholder="Target City (e.g. Austin, TX)" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-bolt me-1"></i> Hunt Leads
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-saas p-4 h-100">
            <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-bolt text-warning me-2"></i>Single Domain Audit</h5>
            <p class="text-secondary small mb-3">Audit any standalone business URL and save it into your pipeline.</p>
            <form action="/leads" method="POST" class="d-flex gap-2">
                <input type="hidden" name="action" value="quick_add">
                <input type="url" name="website" class="form-control" placeholder="https://example.com" required>
                <button type="submit" class="btn btn-dark text-nowrap">Audit & Save</button>
            </form>
        </div>
    </div>
</div>

<!-- Recent Leads Table -->
<div class="card-saas mb-4">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">High-Opportunity Prospects</h5>
        <a href="/leads" class="btn btn-sm btn-outline-secondary">View All Leads (<?= $stats['total_leads'] ?>)</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-3">Company & Domain</th>
                    <th>Location</th>
                    <th>SEO Score</th>
                    <th>Opportunity</th>
                    <th>Pipeline Status</th>
                    <th>Public Contacts</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentLeads)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No prospect leads found yet. <a href="/search" class="fw-bold text-primary">Execute your first search!</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentLeads as $lead): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark"><?= e($lead['company_name'] ?: 'Local Business') ?></div>
                            <a href="<?= e($lead['website']) ?>" target="_blank" class="text-muted small text-decoration-none">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1" style="font-size: 0.7rem;"></i><?= e(clean_domain($lead['website'])) ?>
                            </a>
                        </td>
                        <td class="small text-secondary">
                            <?= e($lead['city'] ?: 'Local') ?>, <?= e($lead['country'] ?: 'US') ?>
                        </td>
                        <td>
                            <?php $score = (int)($lead['seo_score'] ?? 0); ?>
                            <span class="score-pill <?= $score >= 70 ? 'score-high' : ($score >= 40 ? 'score-med' : 'score-low') ?>">
                                <?= $score ?>/100
                            </span>
                        </td>
                        <td>
                            <?php $opp = (int)($lead['opportunity_score'] ?? 0); ?>
                            <span class="badge <?= $opp >= 70 ? 'badge-opp-high' : ($opp >= 40 ? 'badge-opp-med' : 'badge-opp-low') ?>">
                                <?= $opp ?>/100 <?= $opp >= 70 ? '🔥 High' : ($opp >= 40 ? 'Medium' : 'Low') ?>
                            </span>
                        </td>
                        <td>
                            <select class="form-select form-select-sm select-lead-status" data-lead-id="<?= $lead['id'] ?>" style="width: 130px; font-size: 0.8rem;">
                                <?php foreach (['new' => 'New', 'qualified' => 'Qualified', 'contacted' => 'Contacted', 'replied' => 'Replied', 'interested' => 'Interested', 'proposal' => 'Proposal Sent', 'won' => 'Closed Won', 'lost' => 'Lost'] as $k => $lbl): ?>
                                <option value="<?= $k ?>" <?= ($lead['status'] === $k) ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if (!empty($lead['email'])): ?>
                                    <a href="mailto:<?= e($lead['email']) ?>" class="text-primary" title="<?= e($lead['email']) ?>"><i class="fa-solid fa-envelope"></i></a>
                                <?php else: ?>
                                    <span class="text-muted opacity-25"><i class="fa-solid fa-envelope"></i></span>
                                <?php endif; ?>

                                <?php if (!empty($lead['phone'])): ?>
                                    <a href="tel:<?= e($lead['phone']) ?>" class="text-success" title="<?= e($lead['phone']) ?>"><i class="fa-solid fa-phone"></i></a>
                                <?php else: ?>
                                    <span class="text-muted opacity-25"><i class="fa-solid fa-phone"></i></span>
                                <?php endif; ?>

                                <?php if (!empty($lead['contact_page_url'])): ?>
                                    <a href="<?= e($lead['contact_page_url']) ?>" target="_blank" class="text-info" title="Contact Page"><i class="fa-solid fa-globe"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <a href="/lead/<?= $lead['id'] ?>" class="btn btn-sm btn-primary px-2 py-1">
                                <i class="fa-solid fa-folder-open me-1"></i> Profile & Pitch
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Timeline & Quota Progress Row -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-saas p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Recent Pipeline Activity</h5>
            <?php if (empty($activities)): ?>
                <p class="text-muted small">No activity recorded yet.</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($activities as $act): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="small fw-semibold text-dark"><?= e($act['title']) ?></div>
                        <div class="text-muted" style="font-size: 0.8rem;"><?= e($act['details']) ?></div>
                        <div class="text-secondary" style="font-size: 0.72rem;"><?= date('M j, Y g:i A', strtotime($act['created_at'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-saas p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Monthly Plan Quota Status</h5>
            <?php
            $subService = new App\Auth();
            $limits = $subService->getUserPlanLimits((int)$currentUser['id']);
            $searchesPct = min(100, round(($limits['usage']['searches'] / max(1, $limits['limits']['searches_per_month'])) * 100));
            $leadsPct = min(100, round(($limits['usage']['leads'] / max(1, $limits['limits']['leads_per_month'])) * 100));
            $auditsPct = min(100, round(($limits['usage']['audits'] / max(1, $limits['limits']['audits_per_month'])) * 100));
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-semibold mb-1">
                    <span>Searches Used</span>
                    <span><?= $limits['usage']['searches'] ?> / <?= $limits['limits']['searches_per_month'] ?></span>
                </div>
                <div class="progress" style="height: 7px;">
                    <div class="progress-bar bg-primary" style="width: <?= $searchesPct ?>%"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-semibold mb-1">
                    <span>Leads Discovered</span>
                    <span><?= $limits['usage']['leads'] ?> / <?= $limits['limits']['leads_per_month'] ?></span>
                </div>
                <div class="progress" style="height: 7px;">
                    <div class="progress-bar bg-success" style="width: <?= $leadsPct ?>%"></div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between small fw-semibold mb-1">
                    <span>Audits Executed</span>
                    <span><?= $limits['usage']['audits'] ?> / <?= $limits['limits']['audits_per_month'] ?></span>
                </div>
                <div class="progress" style="height: 7px;">
                    <div class="progress-bar bg-info" style="width: <?= $auditsPct ?>%"></div>
                </div>
            </div>

            <a href="/billing" class="btn btn-outline-primary btn-sm w-100 py-2">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Manage Subscription & Upgrade Quotas
            </a>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
