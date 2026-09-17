<?php
$pageTitle = "Lead Hunter Engine | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$prefillNiche = $_GET['niche'] ?? '';
$prefillCity = $_GET['city'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Lead Hunter Discovery Engine</h3>
        <p class="text-secondary small mb-0">Search any commercial niche to discover businesses with low SEO scores and high agency conversion potential.</p>
    </div>
    <div>
        <a href="/leads" class="btn btn-outline-secondary px-3 py-2">
            <i class="fa-solid fa-address-book me-1"></i> Go to CRM Pipeline
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card-saas p-4">
            <form id="leadSearchForm" method="POST" action="/api/search">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Target Niche / Keyword <span class="text-danger">*</span></label>
                        <input type="text" name="keyword" class="form-control" placeholder="e.g. Dentists, Roofing Contractors, Cosmetic Surgeons" required value="<?= e($prefillNiche) ?>">
                        <div class="form-text small">Commercial niches with $1k-$10k customer lifetime value yield highest close rates.</div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">Target City / Metro <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" placeholder="e.g. Miami, FL or Austin, TX" required value="<?= e($prefillCity) ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Country</label>
                        <select name="country" class="form-select">
                            <option value="United States" selected>United States (US)</option>
                            <option value="United Kingdom">United Kingdom (UK)</option>
                            <option value="Canada">Canada (CA)</option>
                            <option value="Australia">Australia (AU)</option>
                            <option value="Germany">Germany (DE)</option>
                            <option value="France">France (FR)</option>
                            <option value="Global">Worldwide</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">Lead Batch Size</label>
                        <select name="limit" class="form-select">
                            <option value="10" selected>10 Target Prospects</option>
                            <option value="25">25 Target Prospects</option>
                            <option value="50">50 Target Prospects</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center pt-2 border-top">
                        <div class="text-muted small">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i> Engine will automatically execute 6-tier technical audits and calculate opportunity scores for each discovered lead.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-bolt me-1"></i> Execute Lead Hunt
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Animation State -->
<div id="searchLoadingState" class="d-none text-center my-5 py-5">
    <div class="spinner-border text-primary mb-3" style="width: 3.5rem; height: 3.5rem;" role="status"></div>
    <h4 class="fw-bold text-dark mb-1">Hunting Local Leads & Executing Deep Audits...</h4>
    <p class="text-secondary small mx-auto" style="max-width: 550px;">
        Extracting public business records, inspecting technical on-page ranking factors, evaluating schema JSON-LD, and calculating commercial opportunity scores.
    </p>
    <div class="progress mx-auto mt-3" style="max-width: 350px; height: 6px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%"></div>
    </div>
</div>

<!-- Search Results Display Area -->
<div id="searchResultsBox" class="d-none">
    <!-- Populated dynamically via JS or redirects to /leads -->
</div>

<!-- Quick Niche Recommendations Box -->
<div class="card-saas p-4 mt-4 bg-light border">
    <h5 class="fw-bold text-dark mb-2"><i class="fa-solid fa-lightbulb text-warning me-2"></i>High-Converting Recommended Niches for SEO Outreach:</h5>
    <div class="row g-3 mt-1">
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded border shadow-sm h-100">
                <span class="badge bg-primary mb-1">Healthcare</span>
                <div class="fw-bold text-dark small">Cosmetic Dentists & Orthodontists</div>
                <div class="text-muted" style="font-size: 0.75rem;">Avg Retainer: $2k-$5k/mo</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded border shadow-sm h-100">
                <span class="badge bg-danger mb-1">Legal</span>
                <div class="fw-bold text-dark small">Personal Injury Lawyers</div>
                <div class="text-muted" style="font-size: 0.75rem;">Avg Retainer: $3k-$10k/mo</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded border shadow-sm h-100">
                <span class="badge bg-warning text-dark mb-1">Home Services</span>
                <div class="fw-bold text-dark small">Roofing & HVAC Contractors</div>
                <div class="text-muted" style="font-size: 0.75rem;">Avg Retainer: $1.5k-$4k/mo</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded border shadow-sm h-100">
                <span class="badge bg-success mb-1">Aesthetics</span>
                <div class="fw-bold text-dark small">MedSpas & Plastic Surgery</div>
                <div class="text-muted" style="font-size: 0.75rem;">Avg Retainer: $2k-$6k/mo</div>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
