<?php
/**
 * SEO Client Hunter - Admin Header Layout
 */
$cmsService = new App\CMS();
$siteSettings = $cmsService->getSiteSettings();
$authService = new App\Auth();
$currentUser = $authService->getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Control Panel | SEO Client Hunter') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="dashboard-wrapper">
    <?php require VIEWS_DIR . '/layout/admin_sidebar.php'; ?>

    <div class="dashboard-main">
        <header class="dashboard-topbar bg-white border-bottom">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="toggleAdminSidebarBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-flex align-items-center gap-2 text-secondary small">
                    <span class="badge bg-dark text-uppercase">Admin Portal</span>
                    <span>/</span>
                    <span class="text-dark fw-bold"><?= e($pageTitle ?? 'Overview') ?></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="/" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Site
                </a>
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2 border py-1 px-2 rounded-pill" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px;">
                            <i class="fa-solid fa-crown" style="font-size: 0.75rem;"></i>
                        </div>
                        <span class="small fw-bold text-dark me-1"><?= e($currentUser['name'] ?? 'Admin') ?></span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="/dashboard"><i class="fa-solid fa-gauge me-2 text-primary"></i> User Dashboard</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Log Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mx-4 mt-4 alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?= e($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mx-4 mt-4 alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> <?= e($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <main class="dashboard-content">
