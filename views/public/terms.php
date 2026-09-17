<?php
$pageTitle = "Terms of Service | SEO Client Hunter";
$pageMetaDescription = "Terms and conditions governing the use of SEO Client Hunter software.";

$cms = new App\CMS();
$page = $cms->getPageBySlug('terms');
require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-saas p-4 p-md-5">
                <?php if ($page && !empty($page['content'])): ?>
                    <?= $page['content'] ?>
                <?php else: ?>
                    <h1 class="display-6 fw-bold text-dark mb-3">Terms of Service</h1>
                    <p class="text-muted small">Last updated: September 2026</p>
                    <hr class="my-4">
                    <h3>1. Acceptance of Terms</h3>
                    <p class="text-secondary">By creating an account or accessing SEO Client Hunter, you agree to comply with these terms, our acceptable use guidelines, and all applicable global laws.</p>
                    <h3>2. Acceptable Outreach & Anti-Spam Compliance</h3>
                    <p class="text-secondary">Users are solely responsible for ensuring that all outbound communications generated or conducted through our system comply with local anti-spam legislation, including CAN-SPAM (US), GDPR (EU), and CASL (Canada). You agree to provide clear opt-out mechanisms in all marketing messages.</p>
                    <h3>3. Disclaimer of Rankings</h3>
                    <p class="text-secondary">Lead opportunity scores and audit calculations are provided for diagnostic and agency sales assessment purposes only. SEO Client Hunter does not guarantee specific organic rankings or revenue outcomes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
