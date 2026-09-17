<?php
/**
 * SEO Client Hunter - Public Header Layout
 */
$cmsService = new App\CMS();
$siteSettings = $cmsService->getSiteSettings();
$seoSettings = $cmsService->getSeoSettings();
$menuItems = $cmsService->getMenuItems('header');
$authService = new App\Auth();
$isLoggedIn = $authService->isLoggedIn();
$topAd = $cmsService->getActiveAd('header_banner');

$title = $pageTitle ?? ($seoSettings['site_title'] ?? 'SEO Client Hunter - Find High-Paying SEO Clients in Seconds');
$metaDesc = $pageMetaDescription ?? ($seoSettings['meta_description'] ?? 'Discover businesses with critical SEO flaws, perform automated technical audits, and generate personalized pitch proposals.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta name="keywords" content="<?= e($seoSettings['keywords'] ?? '') ?>">
    <link rel="canonical" href="<?= e($canonicalUrl ?? BASE_URL . $_SERVER['REQUEST_URI']) ?>">
    <meta name="robots" content="<?= e($robotsSetting ?? 'index, follow') ?>">

    <!-- Open Graph & Social Cards -->
    <meta property="og:title" content="<?= e($ogTitle ?? $title) ?>">
    <meta property="og:description" content="<?= e($ogDesc ?? $metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(BASE_URL . $_SERVER['REQUEST_URI']) ?>">
    <meta property="og:site_name" content="<?= e($siteSettings['site_name'] ?? 'SEO Client Hunter') ?>">
    <meta name="twitter:card" content="<?= e($seoSettings['twitter_card'] ?? 'summary_large_image') ?>">

    <!-- Schema.org JSON-LD -->
    <?php if (!empty($seoSettings['schema_json_ld'])): ?>
    <script type="application/ld+json">
    <?= $seoSettings['schema_json_ld'] ?>
    </script>
    <?php endif; ?>

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">

    <!-- Custom Injected Header Scripts -->
    <?php if (!empty($siteSettings['header_code'])) echo $siteSettings['header_code']; ?>
</head>
<body>
    <?php if (!empty($siteSettings['body_code'])) echo $siteSettings['body_code']; ?>

    <!-- Top Announcement Bar -->
    <?php if (!empty($siteSettings['announcement_enabled']) && $siteSettings['announcement_enabled'] === '1' && !empty($siteSettings['announcement_bar'])): ?>
    <div class="announcement-bar text-center py-2">
        <span><?= e($siteSettings['announcement_bar']) ?></span>
    </div>
    <?php endif; ?>

    <!-- Header Ad Placement -->
    <?php if ($topAd): ?>
    <div class="container my-2">
        <div class="ad-banner-box">
            <span class="ad-badge">Sponsored</span>
            <?php if (!empty($topAd['html_code'])): ?>
                <?= $topAd['html_code'] ?>
            <?php elseif (!empty($topAd['image_url'])): ?>
                <a href="<?= e($topAd['link_url'] ?: '#') ?>" target="_blank" rel="nofollow">
                    <img src="<?= e($topAd['image_url']) ?>" alt="<?= e($topAd['name']) ?>" class="img-fluid rounded">
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="/">
                <div class="brand-icon-box">
                    <i class="fa-solid fa-crosshairs"></i>
                </div>
                <span><?= e($siteSettings['site_name'] ?? 'SEO Client Hunter') ?></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <?php foreach ($menuItems as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 text-secondary" href="<?= e($item['url']) ?>" target="<?= e($item['target']) ?>"><?= e($item['label']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <a href="/dashboard" class="btn btn-outline-primary px-3 py-2"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                        <a href="/logout" class="btn btn-light px-3 py-2 text-secondary"><i class="fa-solid fa-right-from-bracket"></i></a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-link text-decoration-none fw-semibold text-dark px-3">Sign In</a>
                        <a href="/register" class="btn btn-primary px-4 py-2 shadow-sm"><i class="fa-solid fa-rocket me-1"></i> Start Free Trial</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
