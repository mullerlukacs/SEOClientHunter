<?php
$pageTitle = ($lead['company_name'] ?: 'Lead Profile') . " | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$crm = new App\CRM();
$audit = $lead['latest_audit'] ?? null;
$auditScores = $audit ? json_decode($audit['scores_json'] ?? '[]', true) : [];
$auditIssues = $audit ? json_decode($audit['issues_json'] ?? '[]', true) : [];
$aiPitch = $crm->getLatestAiPitch((int)$lead['id']);
$socialLinks = json_decode($lead['social_links_json'] ?? '[]', true) ?: [];

$notes = $crm->getNotes((int)$lead['id']);
$tasks = $crm->getTasks((int)$lead['id']);
?>

<!-- Lead Header Banner -->
<div class="card-saas p-4 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary text-uppercase"><?= e($lead['niche'] ?: 'Commercial') ?></span>
                <span class="badge <?= ($lead['opportunity_score'] >= 70) ? 'badge-opp-high' : 'badge-opp-med' ?>">
                    <?= (int)$lead['opportunity_score'] ?>/100 Opportunity Score
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1"><?= e($lead['company_name'] ?: 'Prospect Business') ?></h2>
            <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small">
                <a href="<?= e($lead['website']) ?>" target="_blank" class="text-primary text-decoration-none fw-semibold">
                    <i class="fa-solid fa-globe me-1"></i><?= e($lead['website']) ?>
                </a>
                <span>&bull;</span>
                <span><i class="fa-solid fa-location-dot me-1"></i><?= e($lead['city'] ?: 'City') ?>, <?= e($lead['country'] ?: 'US') ?></span>
                <span>&bull;</span>
                <span><i class="fa-solid fa-calendar me-1"></i>Added <?= date('M j, Y', strtotime($lead['created_at'])) ?></span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <select class="form-select select-lead-status" data-lead-id="<?= $lead['id'] ?>" style="min-width: 150px;">
                <?php foreach (['new' => 'New', 'qualified' => 'Qualified', 'contacted' => 'Contacted', 'replied' => 'Replied', 'interested' => 'Interested', 'proposal' => 'Proposal Sent', 'won' => 'Closed Won', 'lost' => 'Lost'] as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= ($lead['status'] === $k) ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-outline-secondary btn-archive-lead" data-lead-id="<?= $lead['id'] ?>" title="Archive Lead">
                <i class="fa-solid fa-box-archive"></i>
            </button>
            <button class="btn btn-outline-danger btn-delete-lead" data-lead-id="<?= $lead['id'] ?>" title="Delete Lead">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
</div>

<!-- 6-Tab Navigation Bar -->
<div class="card-saas mb-4">
    <div class="border-bottom">
        <ul class="nav nav-tabs border-0 px-3" id="leadTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active py-3 fw-semibold text-secondary" id="overview-tab" data-bs-toggle="tab" data-bs-target="#tab-overview">
                    <i class="fa-solid fa-circle-info me-2 text-primary"></i> 1. Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-semibold text-secondary" id="audit-tab" data-bs-toggle="tab" data-bs-target="#tab-audit">
                    <i class="fa-solid fa-chart-line me-2 text-success"></i> 2. 6-Tier SEO Audit
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-semibold text-secondary" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#tab-contacts">
                    <i class="fa-solid fa-address-book me-2 text-info"></i> 3. Contacts & Channels
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-semibold text-secondary" id="ai-tab" data-bs-toggle="tab" data-bs-target="#tab-ai">
                    <i class="fa-solid fa-wand-magic-sparkles me-2 text-warning"></i> 4. AI Pitch Generator
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-semibold text-secondary" id="notes-tab" data-bs-toggle="tab" data-bs-target="#tab-notes">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> 5. Notes & History (<?= count($notes) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-semibold text-secondary" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tab-tasks">
                    <i class="fa-solid fa-list-check me-2 text-secondary"></i> 6. Tasks & Reminders (<?= count($tasks) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-4" id="leadTabContent">
        <!-- ==================== TAB 1: OVERVIEW ==================== -->
        <div class="tab-pane fade show active" id="tab-overview">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h5 class="fw-bold text-dark mb-3">Commercial Opportunity Profile</h5>
                    <p class="text-secondary small mb-4">Calculated from business revenue potential, public contact channels, and verified ranking bottlenecks.</p>

                    <div class="d-flex align-items-center gap-4 p-3 bg-light rounded border mb-4">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-danger"><?= (int)$lead['opportunity_score'] ?></div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Opportunity</small>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center">
                            <div class="display-6 fw-bold text-primary"><?= (int)$lead['seo_score'] ?></div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">SEO Health</small>
                        </div>
                        <div class="vr"></div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small">Commercial Assessment:</div>
                            <div class="text-secondary small">
                                <?php if ($lead['opportunity_score'] >= 75): ?>
                                    🔥 <strong>Prime Agency Target:</strong> Severe SEO defects coupled with direct contact pathways and high-ticket service offering.
                                <?php else: ?>
                                    Moderate prospect opportunity. Pitch focused on local visibility and schema enhancements.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2">Company Metadata</h6>
                    <table class="table table-sm table-borderless small text-secondary">
                        <tr><th class="text-dark" style="width: 140px;">Company Name:</th><td><?= e($lead['company_name']) ?></td></tr>
                        <tr><th class="text-dark">Website:</th><td><a href="<?= e($lead['website']) ?>" target="_blank"><?= e($lead['website']) ?></a></td></tr>
                        <tr><th class="text-dark">Niche:</th><td><?= e($lead['niche'] ?: 'Commercial') ?></td></tr>
                        <tr><th class="text-dark">City / State:</th><td><?= e($lead['city']) ?>, <?= e($lead['country']) ?></td></tr>
                        <tr><th class="text-dark">Address:</th><td><?= e($lead['address'] ?: 'Not publicly listed') ?></td></tr>
                        <tr><th class="text-dark">Primary Email:</th><td><?= e($lead['email'] ?: 'Not detected') ?></td></tr>
                        <tr><th class="text-dark">Direct Phone:</th><td><?= e($lead['phone'] ?: 'Not detected') ?></td></tr>
                    </table>
                </div>

                <div class="col-lg-6">
                    <h5 class="fw-bold text-dark mb-3">6-Tier Audit Score Breakdown</h5>
                    <div class="p-3 bg-light rounded border mb-4">
                        <?php
                        $catLabels = [
                            'technical' => '1. Technical & Speed Health',
                            'onpage' => '2. On-Page Structure & Tags',
                            'content' => '3. Content Volume & Readability',
                            'local' => '4. Local SEO & Schema JSON-LD',
                            'authority' => '5. Authority & Link Architecture',
                            'social' => '6. Social Signals & Brand Presence'
                        ];
                        foreach ($catLabels as $cKey => $cTitle):
                            $cScore = (int)($auditScores[$cKey] ?? 50);
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span class="text-dark"><?= $cTitle ?></span>
                                <span class="text-muted"><?= $cScore ?> / 100</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar <?= $cScore >= 70 ? 'bg-success' : ($cScore >= 40 ? 'bg-warning' : 'bg-danger') ?>" style="width: <?= $cScore ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary w-50 py-2" onclick="document.getElementById('audit-tab').click()">
                            <i class="fa-solid fa-list-check me-1"></i> View All Issues
                        </button>
                        <button class="btn btn-primary w-50 py-2" onclick="document.getElementById('ai-tab').click()">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generate AI Pitch
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: 6-TIER SEO AUDIT ==================== -->
        <div class="tab-pane fade" id="tab-audit">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Comprehensive 40-Point Technical Diagnostic</h5>
                    <p class="text-secondary small mb-0">Every issue is graded by severity, business risk, and recommended remediation.</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6 px-3 py-2">Score: <?= (int)$lead['seo_score'] ?>/100</span>
                </div>
            </div>

            <?php if (empty($auditIssues)): ?>
                <div class="p-4 bg-light rounded text-center text-muted">
                    No audit records found. Click below to re-crawl this website.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($auditIssues as $iss):
                        $sev = $iss['severity'] ?? 'Medium';
                        $cardClass = 'issue-' . strtolower($sev);
                        $badgeClass = $sev === 'Critical' ? 'bg-danger' :
                                      ($sev === 'High' ? 'bg-warning text-dark' :
                                      ($sev === 'Medium' ? 'bg-info text-dark' :
                                      ($sev === 'Passed' ? 'bg-success' : 'bg-secondary')));
                    ?>
                    <div class="col-12">
                        <div class="issue-card <?= $cardClass ?>">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="badge <?= $badgeClass ?> text-uppercase"><?= e($sev) ?></span>
                                    <span class="text-secondary fw-semibold small ms-2"><?= e($iss['category'] ?? 'Technical') ?></span>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-1"><?= e($iss['title'] ?? 'SEO Defect') ?></h6>
                            <p class="text-secondary small mb-2"><?= e($iss['explanation'] ?? '') ?></p>
                            <div class="p-2 bg-light rounded text-dark small border">
                                <strong><i class="fa-solid fa-wrench text-primary me-1"></i>Fix Recommendation:</strong> <?= e($iss['recommendation'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ==================== TAB 3: CONTACTS & CHANNELS ==================== -->
        <div class="tab-pane fade" id="tab-contacts">
            <h5 class="fw-bold text-dark mb-3">Discovered Public Contact Vectors</h5>
            <p class="text-secondary small mb-4">Direct contact routes gathered from page text, mailto links, tel links, and social metadata.</p>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-white rounded text-primary fs-4 border">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Public Email Address</small>
                                <?php if (!empty($lead['email'])): ?>
                                    <a href="mailto:<?= e($lead['email']) ?>" class="fw-bold text-primary fs-6"><?= e($lead['email']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">No public email found</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-white rounded text-success fs-4 border">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Public Telephone Number</small>
                                <?php if (!empty($lead['phone'])): ?>
                                    <a href="tel:<?= e($lead['phone']) ?>" class="fw-bold text-success fs-6"><?= e($lead['phone']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">No public phone found</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-white rounded text-info fs-4 border">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Contact Form / Booking URL</small>
                                <?php if (!empty($lead['contact_page_url'])): ?>
                                    <a href="<?= e($lead['contact_page_url']) ?>" target="_blank" class="fw-bold text-info fs-6 text-break">
                                        <?= e($lead['contact_page_url']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No contact page detected</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-4 bg-light rounded border h-100">
                        <h6 class="fw-bold text-dark mb-3">Social & Digital Brand Profiles</h6>
                        <div class="d-grid gap-2">
                            <?php
                            $socialTypes = [
                                'facebook' => ['icon' => 'fa-brands fa-facebook', 'label' => 'Facebook Page'],
                                'instagram' => ['icon' => 'fa-brands fa-instagram', 'label' => 'Instagram Profile'],
                                'linkedin' => ['icon' => 'fa-brands fa-linkedin', 'label' => 'LinkedIn Company'],
                                'twitter' => ['icon' => 'fa-brands fa-x-twitter', 'label' => 'X / Twitter'],
                                'youtube' => ['icon' => 'fa-brands fa-youtube', 'label' => 'YouTube Channel']
                            ];
                            foreach ($socialTypes as $sKey => $sInfo):
                                $url = $socialLinks[$sKey] ?? null;
                            ?>
                            <div class="p-2 bg-white rounded border d-flex justify-content-between align-items-center">
                                <span class="text-secondary small"><i class="<?= $sInfo['icon'] ?> me-2 text-dark"></i> <?= $sInfo['label'] ?></span>
                                <?php if ($url): ?>
                                    <a href="<?= e($url) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">Open Link</a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">Not Found</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 4: AI PITCH GENERATOR ==================== -->
        <div class="tab-pane fade" id="tab-ai">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">AI Pitch & Cold Proposal Generator</h5>
                    <p class="text-secondary small mb-0">Generate personalized outreach referencing this specific company's verified SEO issues.</p>
                </div>
                <button class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm" id="btnGenerateAiPitch" data-lead-id="<?= $lead['id'] ?>">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> <?= $aiPitch ? 'Re-Generate Pitch' : 'Generate Outreach Pitch' ?>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div id="aiLoadingBox" class="d-none text-center my-4 py-4">
                <div class="spinner-border text-warning mb-2" role="status"></div>
                <div class="fw-bold text-dark">Analyzing audit vulnerabilities & crafting tailored outreach sequences...</div>
                <small class="text-muted">Synthesizing cold email, LinkedIn InMail, contact form pitch, and 3-step follow-ups.</small>
            </div>

            <!-- AI Pitch Results Box -->
            <div id="aiResultsBox" class="<?= $aiPitch ? '' : 'd-none' ?>">
                <?php
                $p = $aiPitch ? json_decode($aiPitch['pitch_data_json'] ?? '[]', true) : [];
                $coldEmail = $p['cold_email'] ?? [];
                $liMsg = $p['linkedin_message'] ?? [];
                $cfMsg = $p['contact_form_pitch'] ?? [];
                $shortPitch = $p['short_pitch'] ?? [];
                $followups = $p['followups'] ?? [];
                ?>
                <!-- Diagnostic Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted fw-bold text-uppercase d-block">1. Target Summary</small>
                            <span class="small text-dark fw-semibold" id="aiBusinessSummary"><?= e($p['business_summary'] ?? '') ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted fw-bold text-uppercase d-block">2. Primary Bottleneck</small>
                            <span class="small text-danger fw-semibold" id="aiSeoProblems"><?= e($p['seo_problems'] ?? '') ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted fw-bold text-uppercase d-block">3. Commercial Hook</small>
                            <span class="small text-success fw-semibold" id="aiBusinessOpp"><?= e($p['business_opportunity'] ?? '') ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted fw-bold text-uppercase d-block">4. Suggested Retainer</small>
                            <span class="small text-primary fw-semibold" id="aiSuggestedService"><?= e($p['suggested_service'] ?? '') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Pitch Formats Accordion / Tabs -->
                <div class="row g-4">
                    <!-- Format 1: Cold Email -->
                    <div class="col-lg-6">
                        <div class="card-saas p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-envelope text-primary me-2"></i>Cold Outreach Email</h6>
                                <button class="btn btn-sm btn-outline-secondary btn-copy" data-clipboard-target="#aiEmailBody">
                                    <i class="fa-regular fa-copy me-1"></i> Copy Email
                                </button>
                            </div>
                            <div class="mb-2">
                                <label class="small text-muted fw-bold">Subject Line:</label>
                                <input type="text" id="aiEmailSubject" class="form-control form-control-sm" readonly value="<?= e($coldEmail['subject'] ?? '') ?>">
                            </div>
                            <div class="copy-box" id="aiEmailBody"><?= e($coldEmail['body'] ?? '') ?></div>
                        </div>

                        <!-- Format 2: LinkedIn InMail -->
                        <div class="card-saas p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa-brands fa-linkedin text-info me-2"></i>LinkedIn Direct Message</h6>
                                <button class="btn btn-sm btn-outline-secondary btn-copy" data-clipboard-target="#aiLinkedInBody">
                                    <i class="fa-regular fa-copy me-1"></i> Copy Message
                                </button>
                            </div>
                            <div class="copy-box" id="aiLinkedInBody"><?= e($liMsg['body'] ?? '') ?></div>
                        </div>
                    </div>

                    <!-- Format 3: Contact Form & Followups -->
                    <div class="col-lg-6">
                        <div class="card-saas p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-window-restore text-success me-2"></i>Website Contact Form Pitch</h6>
                                <button class="btn btn-sm btn-outline-secondary btn-copy" data-clipboard-target="#aiContactFormBody">
                                    <i class="fa-regular fa-copy me-1"></i> Copy Form Pitch
                                </button>
                            </div>
                            <div class="copy-box" id="aiContactFormBody"><?= e($cfMsg['body'] ?? '') ?></div>
                        </div>

                        <!-- 3-Touch Follow Up Cadence -->
                        <div class="card-saas p-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calendar-days text-secondary me-2"></i>3-Step Follow-Up Sequence</h6>

                            <div class="accordion accordion-flush" id="accFollowups">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-2 small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#fu1">
                                            Step 1: 3-Day Soft Bump
                                        </button>
                                    </h2>
                                    <div id="fu1" class="accordion-collapse collapse" data-bs-parent="#accFollowups">
                                        <div class="accordion-body p-2 position-relative">
                                            <button class="btn btn-sm btn-outline-secondary btn-copy position-absolute top-0 end-0 m-1" data-clipboard-target="#aiFollowupBody_1">Copy</button>
                                            <div class="copy-box" id="aiFollowupBody_1"><?= e($followups[0]['body'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-2 small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#fu2">
                                            Step 2: 7-Day Value Add (Competitor Angle)
                                        </button>
                                    </h2>
                                    <div id="fu2" class="accordion-collapse collapse" data-bs-parent="#accFollowups">
                                        <div class="accordion-body p-2 position-relative">
                                            <button class="btn btn-sm btn-outline-secondary btn-copy position-absolute top-0 end-0 m-1" data-clipboard-target="#aiFollowupBody_2">Copy</button>
                                            <div class="copy-box" id="aiFollowupBody_2"><?= e($followups[1]['body'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-2 small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#fu3">
                                            Step 3: 14-Day Break-Up Note
                                        </button>
                                    </h2>
                                    <div id="fu3" class="accordion-collapse collapse" data-bs-parent="#accFollowups">
                                        <div class="accordion-body p-2 position-relative">
                                            <button class="btn btn-sm btn-outline-secondary btn-copy position-absolute top-0 end-0 m-1" data-clipboard-target="#aiFollowupBody_3">Copy</button>
                                            <div class="copy-box" id="aiFollowupBody_3"><?= e($followups[2]['body'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 5: NOTES & TIMELINE ==================== -->
        <div class="tab-pane fade" id="tab-notes">
            <h5 class="fw-bold text-dark mb-3">Internal Agency Notes & Prospect History</h5>

            <div class="card-saas p-3 mb-4 bg-light">
                <form id="addNoteForm">
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="mb-2">
                        <textarea name="note" class="form-control" rows="3" placeholder="Log sales call details, objection notes, or outreach response..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fa-solid fa-plus me-1"></i> Save Note
                    </button>
                </form>
            </div>

            <?php if (empty($notes)): ?>
                <p class="text-muted small">No notes logged for this prospect yet.</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($notes as $n): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="p-3 bg-white rounded border shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small">Agency Log</span>
                                <span class="text-muted" style="font-size: 0.72rem;"><?= date('M j, Y g:i A', strtotime($n['created_at'])) ?></span>
                            </div>
                            <div class="text-secondary small"><?= nl2br(e($n['note'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ==================== TAB 6: TASKS & REMINDERS ==================== -->
        <div class="tab-pane fade" id="tab-tasks">
            <h5 class="fw-bold text-dark mb-3">Pipeline Tasks & Action Items</h5>

            <div class="card-saas p-3 mb-4 bg-light">
                <form id="addTaskForm" class="row g-2 align-items-end">
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Task Title / Action</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Send LinkedIn InMail to Owner" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Due Date</label>
                        <input type="date" name="due_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2">
                            <i class="fa-solid fa-plus me-1"></i> Schedule Task
                        </button>
                    </div>
                </form>
            </div>

            <?php if (empty($tasks)): ?>
                <p class="text-muted small">No active tasks scheduled for this lead.</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($tasks as $t):
                        $done = (int)$t['is_completed'] === 1;
                    ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                        <div class="form-check">
                            <input class="form-check-input task-checkbox" type="checkbox" data-task-id="<?= $t['id'] ?>" id="task_<?= $t['id'] ?>" <?= $done ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-dark small ms-2 <?= $done ? 'text-decoration-line-through text-muted' : '' ?>" id="taskLabel_<?= $t['id'] ?>" for="task_<?= $t['id'] ?>">
                                <?= e($t['title']) ?>
                            </label>
                        </div>
                        <div class="text-secondary small">
                            <?php if (!empty($t['due_date'])): ?>
                                <i class="fa-solid fa-calendar me-1"></i> Due <?= date('M j, Y', strtotime($t['due_date'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
