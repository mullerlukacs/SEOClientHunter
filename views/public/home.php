<?php
/**
 * SEO Client Hunter - High-Converting Public Homepage
 */
$pageTitle = "SEO Client Hunter - Automated B2B SEO Prospect Discovery & AI Outreach";
$pageMetaDescription = "Discover local businesses with critical SEO flaws, perform automated technical audits, calculate lead opportunity scores, and generate hyper-personalized pitch emails.";

$cms = new App\CMS();
$sections = $cms->getHomepageSections();
$secMap = [];
foreach ($sections as $s) {
    $secMap[$s['section_key']] = $s;
}

$hero = $secMap['hero'] ?? [
    'title' => 'Uncover High-Value Local Clients With Critical SEO Weaknesses',
    'subtitle' => 'Find, Audit, Score & Convert Local Businesses In Minutes',
    'content' => 'The all-in-one B2B lead generation engine for SEO agencies. Discover businesses with severe on-page & technical defects, calculate commercial opportunity scores, and generate hyper-personalized pitch proposals that close deals.',
    'cta_text' => 'Find High-Intent Clients',
    'cta_link' => '/register'
];

require VIEWS_DIR . '/layout/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="fa-solid fa-bolt me-1"></i> B2B SEO Agency Lead Discovery Engine
                </span>
                <h1 class="display-4 fw-extrabold text-dark mb-3 tracking-tight" style="font-weight: 800;">
                    <?= e($hero['title']) ?>
                </h1>
                <p class="lead text-secondary mx-auto mb-4" style="max-width: 780px;">
                    <?= e($hero['content']) ?>
                </p>

                <!-- Live Audit Interactive Teaser -->
                <div class="card shadow-sm border-0 mx-auto p-3 mb-4" style="max-width: 680px; border-radius: 16px; background: #ffffff;">
                    <form id="quickAuditForm" class="d-flex flex-column flex-sm-row gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-globe"></i></span>
                            <input type="text" id="auditUrlInput" class="form-control border-start-0 py-3" placeholder="Enter target business website (e.g. exampledentist.com)" required>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-3 fw-bold text-nowrap">
                            <i class="fa-solid fa-bolt me-1"></i> Run Free Audit
                        </button>
                    </form>
                    <div class="form-text text-start text-muted mt-2 small ps-2">
                        <i class="fa-solid fa-shield-check text-success me-1"></i> Safe crawl checks 40+ ranking factors, schema JSON-LD, title, meta, mobile readiness & contact channels.
                    </div>
                </div>

                <!-- Loading State -->
                <div id="auditLoadingSpinner" class="d-none my-4 text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="fw-semibold text-dark">Crawling target domain & analyzing 6-tier SEO signals...</div>
                    <small class="text-muted">Extracting title, meta tags, schema JSON-LD, images, and public contact channels</small>
                </div>

                <!-- Results Box Container -->
                <div id="auditResultsContainer" class="d-none text-start card-saas p-4 my-4 shadow-sm mx-auto" style="max-width: 780px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <span class="badge bg-secondary text-uppercase mb-1">Instant Crawl Report</span>
                            <h5 class="fw-bold text-dark mb-0" id="auditDomainValue">Domain</h5>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Overall SEO Score</small>
                            <span class="fs-3 fw-bold text-primary" id="auditScoreValue">--/100</span>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">Technical SEO</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" id="scoreBar_technical" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_technical">--%</span>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">On-Page SEO</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" id="scoreBar_onpage" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_onpage">--%</span>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">Content Signals</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-info" id="scoreBar_content" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_content">--%</span>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">Local SEO / Schema</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" id="scoreBar_local" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_local">--%</span>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">Authority Signals</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-danger" id="scoreBar_authority" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_authority">--%</span>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">Social / Brand</small>
                                <div class="progress my-1" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" id="scoreBar_social" style="width: 0%"></div>
                                </div>
                                <span class="fw-bold small" id="scoreVal_social">--%</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">Detected Audit Issues & Flaws:</h6>
                    <div id="auditIssuesList"></div>

                    <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark">Want to hunt hundreds of leads like this automatically?</div>
                            <small class="text-secondary">Discover commercial businesses, generate personalized AI pitches, and manage outreach.</small>
                        </div>
                        <a href="/register" class="btn btn-primary btn-sm px-3 py-2 text-nowrap">Start Free Trial</a>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="/register" class="btn btn-primary px-4 py-3 fs-6"><i class="fa-solid fa-magnifying-glass-location me-2"></i><?= e($hero['cta_text']) ?></a>
                    <a href="#how-it-works" class="btn btn-outline-secondary px-4 py-3 fs-6">See How It Works</a>
                </div>

                <div class="mt-4 text-muted small d-flex justify-content-center align-items-center gap-4">
                    <span><i class="fa-solid fa-check text-success me-1"></i> No Credit Card Required</span>
                    <span><i class="fa-solid fa-check text-success me-1"></i> Instant 6-Tier Audit</span>
                    <span><i class="fa-solid fa-check text-success me-1"></i> 100% Safe Web Crawler</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Automated Pipeline</span>
            <h2 class="display-6 fw-bold text-dark">How SEO Client Hunter Converts Prospects Into Retainers</h2>
            <p class="text-secondary mx-auto" style="max-width: 650px;">A 3-step automated client acquisition pipeline built exclusively for digital agencies and SEO specialists.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-saas p-4 h-100 text-center">
                    <div class="brand-icon-box mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fa-solid fa-crosshairs"></i>
                    </div>
                    <span class="badge bg-light text-secondary mb-2">Step 01</span>
                    <h5 class="fw-bold text-dark mb-2">1. Pinpoint Weak Websites</h5>
                    <p class="text-muted small mb-0">Select your high-ticket niche (Dentists, Lawyers, Roofers, MedSpas) and target city. Our engine scans public directories and web sources to identify active local companies with sub-optimal search presence.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-saas p-4 h-100 text-center">
                    <div class="brand-icon-box mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.5rem; background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <span class="badge bg-light text-secondary mb-2">Step 02</span>
                    <h5 class="fw-bold text-dark mb-2">2. Deep 6-Tier Technical Audit</h5>
                    <p class="text-muted small mb-0">Our safe crawler conducts an exhaustive 40-point diagnostic examining technical structure, schema JSON-LD, meta tags, mobile readiness, and contact discoverability. Leads receive an opportunity score from 0 to 100.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-saas p-4 h-100 text-center">
                    <div class="brand-icon-box mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <span class="badge bg-light text-secondary mb-2">Step 03</span>
                    <h5 class="fw-bold text-dark mb-2">3. Deploy Personalized Pitch</h5>
                    <p class="text-muted small mb-0">Generate hyper-personalized cold emails, LinkedIn messages, and follow-ups citing their exact audited flaws. Move prospects through your built-in CRM pipeline from Discovery to Closed Won.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid Section -->
<section class="py-5 bg-light border-bottom" id="features">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Engineered For Growth</span>
            <h2 class="display-6 fw-bold text-dark">Built Specifically For High-Ticket SEO Agencies</h2>
            <p class="text-secondary mx-auto" style="max-width: 650px;">Every tool and module is designed to help you find qualified buyers with real budgets and verified website problems.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="fa-solid fa-magnifying-glass-chart"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Multi-Provider Discovery</h5>
                    </div>
                    <p class="text-muted small mb-0">Search by niche, country, city, and volume. Built-in support for simulated demo search, Google Custom Search API, SerpApi, and direct business data feeds.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="fa-solid fa-code-compare"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Deep 6-Tier Website Audit</h5>
                    </div>
                    <p class="text-muted small mb-0">Inspects Technical infrastructure, On-Page elements, Content volume, Local SEO signals, Schema JSON-LD, and Social presence with categorized severity ratings.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">0-100 Lead Opportunity Score</h5>
                    </div>
                    <p class="text-muted small mb-0">Our proprietary algorithm calculates commercial intent, contact feasibility, and ranking deficits to highlight leads with the highest probability of hiring an agency.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-danger bg-opacity-10 rounded text-danger fs-4">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">AI Cold Pitch Generator</h5>
                    </div>
                    <p class="text-muted small mb-0">Formulates persuasive Cold Emails, LinkedIn InMails, Contact Form submissions, and 3-touch follow-up cadences addressing actual website weaknesses.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="fa-solid fa-address-book"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">Agency CRM Pipeline</h5>
                    </div>
                    <p class="text-muted small mb-0">Track prospect deal stages (New, Qualified, Contacted, Replied, Interested, Proposal, Won, Lost), log client notes, create tasks, and tag high-value leads.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-secondary bg-opacity-10 rounded text-secondary fs-4">
                            <i class="fa-solid fa-file-csv"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-0">1-Click CSV Data Export</h5>
                    </div>
                    <p class="text-muted small mb-0">Export filtered or bulk lead lists containing full business data, direct emails, phone numbers, audit scores, problems detected, and outreach pitches.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-5 bg-white border-bottom" id="pricing">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Predictable Pricing</span>
            <h2 class="display-6 fw-bold text-dark">Transparent Plans Built To Scale Your Agency</h2>
            <p class="text-secondary mx-auto" style="max-width: 650px;">Choose the subscription tier that matches your monthly client acquisition targets.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Starter Plan -->
            <div class="col-lg-4 col-md-6">
                <div class="card-saas p-4 h-100 d-flex flex-column border">
                    <h5 class="fw-bold text-dark mb-1">Starter</h5>
                    <p class="text-muted small mb-3">Ideal for solo consultants and freelancers starting client acquisition.</p>
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
                    <h5 class="fw-bold text-dark mb-1">Pro Agency</h5>
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
                    <h5 class="fw-bold text-dark mb-1">Enterprise Scale</h5>
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
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light border-bottom" id="faq">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-3 py-2 rounded-pill mb-2">Got Questions?</span>
            <h2 class="display-6 fw-bold text-dark">Frequently Asked Questions</h2>
            <p class="text-secondary mx-auto" style="max-width: 650px;">Clear answers about our data sources, search providers, and crawler safety.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush card-saas p-3" id="accordionFaq">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Does this work immediately in Demo Mode without API keys?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-secondary small">
                                Yes! The application is fully equipped with an intelligent mock/demo provider and heuristic AI analysis engine. You can search any niche, run deep audits, view opportunity scores, and generate customized cold outreach pitches immediately without configuring any external API keys.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How does the automated website crawler handle robots.txt and security?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-secondary small">
                                Our crawler strictly adheres to web safety standards: it checks for <code>robots.txt</code> restrictions, enforces a 5-second request timeout, limits redirects to a maximum of 3 hops, and accesses only publicly available web pages. It never attempts to bypass CAPTCHAs, paywalls, or authentication systems.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                How is the 0-100 Lead Opportunity Score calculated?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-secondary small">
                                The Opportunity Score looks for the agency "sweet spot": businesses with active commercial intent (e.g. high-ticket dental or legal practices) and verified public contact channels, but suffering from severe technical and on-page SEO deficiencies. A lower website SEO score results in a higher Lead Opportunity Score because it provides substantial pitch leverage.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Can I connect external search APIs or the Gemini AI API?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-secondary small">
                                Yes! In the Admin Panel under <strong>API Keys & Integrations</strong>, you can add credentials for AI Providers (Google Gemini / OpenAI), Google Custom Search API, Places APIs, and custom SMTP servers whenever you are ready.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container py-4">
        <h2 class="display-6 fw-bold mb-3">Ready To Fill Your Agency Pipeline With Qualified Clients?</h2>
        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 650px;">Stop sending generic cold emails that get ignored. Deliver data-backed audit proposals citing real technical bottlenecks.</p>
        <a href="/register" class="btn btn-light text-primary px-4 py-3 fw-bold fs-6 shadow">
            <i class="fa-solid fa-rocket me-2"></i> Start Hunting Clients Free
        </a>
    </div>
</section>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
