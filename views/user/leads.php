<?php
$pageTitle = "Prospects CRM Pipeline | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$crm = new App\CRM();
$filterStatus = $_GET['status'] ?? null;
$filterSearch = $_GET['q'] ?? null;
$filterOpp = $_GET['opp'] ?? null;

$filters = [];
if ($filterStatus && $filterStatus !== 'all') $filters['status'] = $filterStatus;
if ($filterSearch) $filters['search'] = $filterSearch;
if ($filterOpp === 'high') $filters['min_opportunity'] = 70;

$leads = $crm->getLeads((int)$currentUser['id'], $filters);
$allStats = $crm->getDashboardStats((int)$currentUser['id']);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Prospects CRM Pipeline</h3>
        <p class="text-secondary small mb-0">Manage discovered business leads, track outreach engagement, and monitor closing status.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/search" class="btn btn-primary px-3 py-2 fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Hunt New Leads
        </a>
        <a href="/export<?= $filterStatus ? '?status=' . e($filterStatus) : '' ?>" class="btn btn-outline-secondary px-3 py-2">
            <i class="fa-solid fa-file-arrow-down me-1"></i> Export Filtered CSV
        </a>
    </div>
</div>

<?php if (isset($_GET['notice']) && $_GET['notice'] === 'search_completed'): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i> <strong>Search Complete!</strong> Discovered businesses have been audited and added to your CRM pipeline.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Status Filter Tabs -->
<div class="card-saas mb-4">
    <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="nav nav-pills flex-nowrap overflow-auto" style="gap: 6px;">
            <a href="/leads" class="nav-link btn-sm <?= (!$filterStatus || $filterStatus === 'all') ? 'active' : 'bg-light text-secondary' ?>">
                All (<span class="fw-bold"><?= $allStats['total_leads'] ?></span>)
            </a>
            <?php
            $statusLabels = [
                'new' => 'New',
                'qualified' => 'Qualified',
                'contacted' => 'Contacted',
                'replied' => 'Replied',
                'interested' => 'Interested',
                'proposal' => 'Proposal',
                'won' => 'Closed Won',
                'lost' => 'Lost'
            ];
            foreach ($statusLabels as $stKey => $stLabel):
                $active = ($filterStatus === $stKey);
            ?>
            <a href="/leads?status=<?= $stKey ?>" class="nav-link btn-sm <?= $active ? 'active' : 'bg-light text-secondary' ?>">
                <?= $stLabel ?>
            </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" action="/leads" class="d-flex gap-2" style="max-width: 320px;">
            <?php if ($filterStatus): ?>
            <input type="hidden" name="status" value="<?= e($filterStatus) ?>">
            <?php endif; ?>
            <div class="input-group input-group-sm">
                <input type="text" name="q" class="form-control" placeholder="Search company, niche, city..." value="<?= e($filterSearch ?? '') ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <!-- Leads Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-3" style="width: 250px;">Company & Domain</th>
                    <th>Niche & Location</th>
                    <th>SEO Score</th>
                    <th>Opportunity</th>
                    <th>Pipeline Stage</th>
                    <th>Direct Contact</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="mb-2"><i class="fa-solid fa-folder-open fa-3x opacity-25"></i></div>
                        <h5>No prospect leads found in this view.</h5>
                        <p class="small text-secondary mb-3">Adjust your status filter or launch a new lead hunt to fill your pipeline.</p>
                        <a href="/search" class="btn btn-primary btn-sm px-3 py-2"><i class="fa-solid fa-bolt me-1"></i> Launch Lead Hunter</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="<?= e($lead['company_name']) ?>">
                                <?= e($lead['company_name'] ?: 'Commercial Business') ?>
                            </div>
                            <a href="<?= e($lead['website']) ?>" target="_blank" class="text-muted small text-decoration-none">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1" style="font-size: 0.7rem;"></i><?= e(clean_domain($lead['website'])) ?>
                            </a>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark"><?= e($lead['niche'] ?: 'General') ?></div>
                            <div class="text-muted small"><?= e($lead['city'] ?: 'City') ?>, <?= e($lead['country'] ?: 'US') ?></div>
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
                                <?php foreach ($statusLabels as $k => $lbl): ?>
                                <option value="<?= $k ?>" <?= ($lead['status'] === $k) ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if (!empty($lead['email'])): ?>
                                    <a href="mailto:<?= e($lead['email']) ?>" class="btn btn-light btn-sm p-1 px-2 text-primary" title="<?= e($lead['email']) ?>"><i class="fa-solid fa-envelope"></i></a>
                                <?php endif; ?>

                                <?php if (!empty($lead['phone'])): ?>
                                    <a href="tel:<?= e($lead['phone']) ?>" class="btn btn-light btn-sm p-1 px-2 text-success" title="<?= e($lead['phone']) ?>"><i class="fa-solid fa-phone"></i></a>
                                <?php endif; ?>

                                <?php if (!empty($lead['contact_page_url'])): ?>
                                    <a href="<?= e($lead['contact_page_url']) ?>" target="_blank" class="btn btn-light btn-sm p-1 px-2 text-info" title="Contact Form"><i class="fa-solid fa-globe"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="/lead/<?= $lead['id'] ?>" class="btn btn-primary" title="Open Full Lead Dossier & Pitch Generator">
                                    <i class="fa-solid fa-folder-open me-1"></i> Profile & Pitch
                                </a>
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                    <li><a class="dropdown-item" href="/lead/<?= $lead['id'] ?>#tab-audit"><i class="fa-solid fa-chart-simple me-2 text-secondary"></i> View 6-Tier Audit</a></li>
                                    <li><a class="dropdown-item" href="/lead/<?= $lead['id'] ?>#tab-ai"><i class="fa-solid fa-wand-magic-sparkles me-2 text-warning"></i> Generate AI Pitch</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><button class="dropdown-item text-secondary btn-archive-lead" data-lead-id="<?= $lead['id'] ?>"><i class="fa-solid fa-box-archive me-2"></i> Archive Lead</button></li>
                                    <li><button class="dropdown-item text-danger btn-delete-lead" data-lead-id="<?= $lead['id'] ?>"><i class="fa-solid fa-trash me-2"></i> Delete Lead</button></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
