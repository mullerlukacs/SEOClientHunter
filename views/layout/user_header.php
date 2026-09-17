<?php
/**
 * SEO Client Hunter - User Header Layout & Topbar
 */
$cmsService = new App\CMS();
$siteSettings = $cmsService->getSiteSettings();
$authService = new App\Auth();
$currentUser = $authService->getUser();
$db = App\Database::getInstance();

// Unread notifications count
$stmtNotif = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmtNotif->execute([(int)$currentUser['id']]);
$unreadNotifCount = (int)$stmtNotif->fetchColumn();

// Fetch latest 5 notifications
$stmtNotifs = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$stmtNotifs->execute([(int)$currentUser['id']]);
$recentNotifs = $stmtNotifs->fetchAll();

$isDemoMode = ($siteSettings['demo_mode'] ?? '1') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard | SEO Client Hunter') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-wrapper">
    <?php require VIEWS_DIR . '/layout/user_sidebar.php'; ?>

    <div class="dashboard-main">
        <header class="dashboard-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="toggleSidebarBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-2 text-secondary small">
                    <i class="fa-solid fa-house"></i>
                    <span>/</span>
                    <span class="text-dark fw-semibold"><?= e($pageTitle ?? 'Dashboard') ?></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Demo Mode Status Indicator -->
                <?php if ($isDemoMode): ?>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold border border-warning">
                    <i class="fa-solid fa-flask-vial me-1"></i> DEMO MODE ACTIVE
                </span>
                <?php endif; ?>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown" style="width: 40px; height: 40px;">
                        <i class="fa-regular fa-bell"></i>
                        <?php if ($unreadNotifCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadNotifCount ?>
                        </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="width: 320px;">
                        <li class="p-2 border-bottom fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>Notifications</span>
                            <small class="text-muted fw-normal"><?= $unreadNotifCount ?> unread</small>
                        </li>
                        <?php if (empty($recentNotifs)): ?>
                            <li class="p-3 text-center text-muted small">No new notifications.</li>
                        <?php else: ?>
                            <?php foreach ($recentNotifs as $n): ?>
                            <li class="p-2 border-bottom">
                                <div class="fw-semibold text-dark small"><?= e($n['title']) ?></div>
                                <div class="text-muted" style="font-size: 0.78rem;"><?= e($n['message']) ?></div>
                                <div class="text-secondary" style="font-size: 0.7rem;"><?= date('M j, g:i A', strtotime($n['created_at'])) ?></div>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2 border-0 py-1 pe-2 ps-1 rounded-pill" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="d-none d-sm-inline fw-semibold text-dark small me-1"><?= e($currentUser['name'] ?? 'User') ?></span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="/profile"><i class="fa-solid fa-user-gear me-2 text-secondary"></i> Agency Profile</a></li>
                        <li><a class="dropdown-item py-2" href="/billing"><i class="fa-solid fa-credit-card me-2 text-secondary"></i> Plan & Quotas</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</a></li>
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
