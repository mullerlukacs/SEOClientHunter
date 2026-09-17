<?php
$pageTitle = "Outreach Campaigns | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$crm = new App\CRM();
$campaigns = $crm->getCampaigns((int)$currentUser['id']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Outreach Campaigns</h3>
        <p class="text-secondary small mb-0">Organize your prospect outreach sequences into categorized niche campaigns.</p>
    </div>
    <button class="btn btn-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#newCampaignModal">
        <i class="fa-solid fa-plus me-1"></i> New Campaign
    </button>
</div>

<div class="row g-4">
    <?php if (empty($campaigns)): ?>
    <div class="col-12">
        <div class="card-saas p-5 text-center text-muted">
            <i class="fa-solid fa-paper-plane fa-3x mb-3 text-secondary opacity-25"></i>
            <h5>No Campaigns Created Yet</h5>
            <p class="small text-secondary mb-3">Group your discovered leads by niche or city to track email conversion rates.</p>
            <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#newCampaignModal">Create First Campaign</button>
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($campaigns as $camp): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-saas p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary text-uppercase"><?= e($camp['status'] ?: 'Active') ?></span>
                    <small class="text-muted"><?= date('M j, Y', strtotime($camp['created_at'])) ?></small>
                </div>
                <h5 class="fw-bold text-dark mb-2"><?= e($camp['name']) ?></h5>
                <p class="text-secondary small flex-grow-1"><?= e($camp['description'] ?: 'Targeted prospect outreach campaign.') ?></p>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <a href="/leads" class="btn btn-sm btn-outline-primary">View Assigned Leads</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal: New Campaign -->
<div class="modal fade" id="newCampaignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/campaigns">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Outreach Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Campaign Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Q3 Florida Cosmetic Dentists" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description / Target Goal</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Notes on outreach angle, target volume, and offer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Save Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
