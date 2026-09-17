<?php
$pageTitle = "About SEO Client Hunter - Agency Growth Platform";
$pageMetaDescription = "Learn about SEO Client Hunter, our mission, and our enterprise prospect discovery engine.";

$cms = new App\CMS();
$page = $cms->getPageBySlug('about');
require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-saas p-4 p-md-5">
                <?php if ($page && !empty($page['content'])): ?>
                    <?= $page['content'] ?>
                <?php else: ?>
                    <h1 class="display-6 fw-bold text-dark mb-3">About SEO Client Hunter</h1>
                    <p class="lead text-secondary mb-4">We built SEO Client Hunter to solve the single greatest bottleneck facing digital agencies: identifying qualified businesses that have both noticeable SEO vulnerabilities and the commercial budget to hire an agency.</p>
                    <hr class="my-4">
                    <h3>The Problem With Generic Cold Outreach</h3>
                    <p class="text-secondary">Scraping random business directories and blasting identical email templates results in low deliverability, spam complaints, and abysmal 0.5% response rates. Modern prospective clients demand personalized evidence before granting an appointment.</p>
                    <h3>The Solution</h3>
                    <p class="text-secondary">By combining safe, automated multi-tier website crawling with our 0-100 Lead Opportunity Score and AI-driven defect pitch formulation, SEO Client Hunter equips agency sales professionals to send hyper-targeted, value-first proposals that actually close.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
