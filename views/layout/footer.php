<?php
/**
 * SEO Client Hunter - Public Footer Layout
 */
$footerMenuItems = $cmsService->getMenuItems('footer');
$footerAd = $cmsService->getActiveAd('footer_banner');
?>
    <!-- Footer Ad Placement -->
    <?php if ($footerAd): ?>
    <div class="container my-4">
        <div class="ad-banner-box">
            <span class="ad-badge">Sponsored Partner</span>
            <?php if (!empty($footerAd['html_code'])): ?>
                <?= $footerAd['html_code'] ?>
            <?php elseif (!empty($footerAd['image_url'])): ?>
                <a href="<?= e($footerAd['link_url'] ?: '#') ?>" target="_blank" rel="nofollow">
                    <img src="<?= e($footerAd['image_url']) ?>" alt="<?= e($footerAd['name']) ?>" class="img-fluid rounded">
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-5 border-top border-secondary">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-icon-box">
                            <i class="fa-solid fa-crosshairs"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-white"><?= e($siteSettings['site_name'] ?? 'SEO Client Hunter') ?></h5>
                    </div>
                    <p class="text-secondary small pe-lg-4">
                        <?= e($siteSettings['footer_description'] ?? 'Automated prospect intelligence and 6-tier technical audit platform for SEO agencies, consultants, and growth marketers to discover businesses with ranking vulnerabilities and convert them into retainers.') ?>
                    </p>
                    <div class="d-flex gap-3 text-secondary fs-5 mt-3">
                        <a href="#" class="text-secondary hover-text-white"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="text-secondary hover-text-white"><i class="fa-brands fa-linkedin"></i></a>
                        <a href="#" class="text-secondary hover-text-white"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="text-secondary hover-text-white"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold text-uppercase text-light small mb-3">Platform</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="/features" class="text-secondary text-decoration-none">Lead Hunter</a></li>
                        <li class="mb-2"><a href="/#how-it-works" class="text-secondary text-decoration-none">Technical Crawler</a></li>
                        <li class="mb-2"><a href="/#audit" class="text-secondary text-decoration-none">6-Tier Audit</a></li>
                        <li class="mb-2"><a href="/#scoring" class="text-secondary text-decoration-none">Opportunity Scorer</a></li>
                        <li class="mb-2"><a href="/pricing" class="text-secondary text-decoration-none">Agency Pricing</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="fw-bold text-uppercase text-light small mb-3">Company & Legal</h6>
                    <ul class="list-unstyled text-secondary small">
                        <?php foreach ($footerMenuItems as $item): ?>
                        <li class="mb-2"><a href="<?= e($item['url']) ?>" target="<?= e($item['target']) ?>" class="text-secondary text-decoration-none"><?= e($item['label']) ?></a></li>
                        <?php endforeach; ?>
                        <li class="mb-2"><a href="/contact" class="text-secondary text-decoration-none">Contact Support</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-uppercase text-light small mb-3">Crawler Policy & Ethics</h6>
                    <p class="text-secondary small">
                        Our automated audit crawler strictly respects website <code>robots.txt</code> instructions, honors request rate limits, and processes only publicly accessible web data in accordance with international web standards.
                    </p>
                    <div class="p-2 bg-secondary bg-opacity-25 rounded border border-secondary text-light small">
                        <i class="fa-solid fa-shield-check me-1 text-success"></i> <strong>Safe Crawler Engine</strong>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-secondary small">
                <div>
                    &copy; <?= date('Y') ?> <?= e($siteSettings['site_name'] ?? 'SEO Client Hunter') ?>. All rights reserved.
                </div>
                <div class="mt-2 mt-md-0">
                    Built for B2B SEO Agencies & Growth Consultants.
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>

    <!-- Custom Injected Footer Scripts -->
    <?php if (!empty($siteSettings['footer_code'])) echo $siteSettings['footer_code']; ?>
</body>
</html>
