<?php
$pageTitle = "Privacy Policy | SEO Client Hunter";
$pageMetaDescription = "Our commitment to data privacy, GDPR compliance, and transparent practices.";

$cms = new App\CMS();
$page = $cms->getPageBySlug('privacy');
require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-saas p-4 p-md-5">
                <?php if ($page && !empty($page['content'])): ?>
                    <?= $page['content'] ?>
                <?php else: ?>
                    <h1 class="display-6 fw-bold text-dark mb-3">Privacy Policy</h1>
                    <p class="text-muted small">Last updated: September 2026</p>
                    <hr class="my-4">
                    <h3>1. Information We Collect</h3>
                    <p class="text-secondary">We collect information provided directly by registered users (such as name, email address, password hash, and search preferences) and publicly available business contact details discovered via our website audit crawler.</p>
                    <h3>2. Use of Publicly Available Data</h3>
                    <p class="text-secondary">Our crawler processes only publicly accessible websites in strict accordance with standard robots.txt instructions and web protocols. We never circumvent authentication screens or anti-bot defenses.</p>
                    <h3>3. Data Security</h3>
                    <p class="text-secondary">All user passwords are encrypted using strong BCrypt hashing. Session tokens are protected by secure HTTP-only cookie headers and CSRF tokens across all form actions.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
