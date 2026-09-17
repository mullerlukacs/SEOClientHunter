<?php
$pageTitle = "Agency Pricing & Subscription Plans | SEO Client Hunter";
$pageMetaDescription = "Simple, transparent pricing designed to scale with your digital agency. Choose from Starter, Pro Agency, and Enterprise Scale.";
require VIEWS_DIR . '/layout/header.php';
?>

<div class="py-5 bg-white border-bottom text-center">
    <div class="container py-4">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">Straightforward Investment</span>
        <h1 class="display-5 fw-bold text-dark mb-3">Predictable Plans Built For ROI</h1>
        <p class="lead text-secondary mx-auto" style="max-width: 650px;">Closing just one new $2,500/month SEO client pays for the Pro Agency plan for over 2.5 years.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <!-- Starter Plan -->
        <div class="col-lg-4 col-md-6">
            <div class="card-saas p-4 h-100 d-flex flex-column border">
                <h4 class="fw-bold text-dark mb-1">Starter</h4>
                <p class="text-muted small mb-3">For freelancers and solo consultants validating local outreach.</p>
                <div class="mb-4">
                    <span class="display-5 fw-extrabold text-dark">$0</span>
                    <span class="text-muted">/ month (Free Trial)</span>
                </div>
                <ul class="list-unstyled text-secondary small mb-4 flex-grow-1">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>25</strong> Prospect Searches / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>100</strong> Discovered Leads / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>25</strong> Deep 6-Tier Technical Audits</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>50</strong> AI Pitch Generations</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>10</strong> CSV File Exports</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Agency CRM Pipeline</li>
                </ul>
                <a href="/register" class="btn btn-outline-primary w-100 py-2">Get Started Free</a>
            </div>
        </div>

        <!-- Pro Agency Plan -->
        <div class="col-lg-4 col-md-6">
            <div class="card-saas p-4 h-100 d-flex flex-column border-primary position-relative shadow" style="border-width: 2px;">
                <span class="position-absolute top-0 start-50 translate-middle badge bg-primary px-3 py-1 text-uppercase fw-bold">Most Popular</span>
                <h4 class="fw-bold text-dark mb-1">Pro Agency</h4>
                <p class="text-muted small mb-3">For growing digital agencies scaling outreach to close retainers.</p>
                <div class="mb-4">
                    <span class="display-5 fw-extrabold text-dark">$79</span>
                    <span class="text-muted">/ month</span>
                </div>
                <ul class="list-unstyled text-secondary small mb-4 flex-grow-1">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>200</strong> Prospect Searches / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>1,500</strong> Discovered Leads / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>250</strong> Deep 6-Tier Technical Audits</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>500</strong> AI Pitch Generations</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>100</strong> CSV File Exports</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>25</strong> Multi-Stage Campaigns</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Priority Live Support</li>
                </ul>
                <a href="/register" class="btn btn-primary w-100 py-2 shadow-sm">Start 7-Day Free Trial</a>
            </div>
        </div>

        <!-- Enterprise Plan -->
        <div class="col-lg-4 col-md-6">
            <div class="card-saas p-4 h-100 d-flex flex-column border">
                <h4 class="fw-bold text-dark mb-1">Enterprise Scale</h4>
                <p class="text-muted small mb-3">For mature agencies running high-volume outbound prospecting.</p>
                <div class="mb-4">
                    <span class="display-5 fw-extrabold text-dark">$199</span>
                    <span class="text-muted">/ month</span>
                </div>
                <ul class="list-unstyled text-secondary small mb-4 flex-grow-1">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>1,000</strong> Prospect Searches / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>10,000</strong> Discovered Leads / month</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>1,500</strong> Deep 6-Tier Technical Audits</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>3,000</strong> AI Pitch Generations</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>500</strong> CSV File Exports</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> <strong>100</strong> Multi-Stage Campaigns</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Custom API Integrations</li>
                </ul>
                <a href="/register" class="btn btn-outline-dark w-100 py-2">Contact Enterprise</a>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
