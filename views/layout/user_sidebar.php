<?php
/**
 * SEO Client Hunter - User Sidebar Component
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$authService = new App\Auth();
$user = $authService->getUser();
$isAdmin = $authService->isAdmin();
?>
<aside class="dashboard-sidebar" id="userSidebar">
    <a href="/dashboard" class="sidebar-brand">
        <div class="brand-icon-box">
            <i class="fa-solid fa-crosshairs"></i>
        </div>
        <span>SEO Hunter</span>
    </a>

    <ul class="sidebar-menu">
        <div class="sidebar-heading">Prospecting & Audit</div>
        <li class="sidebar-item">
            <a href="/dashboard" class="sidebar-link <?= ($currentUri === '/dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i>
                <span>Overview</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/search" class="sidebar-link <?= ($currentUri === '/search') ? 'active' : '' ?>">
                <i class="fa-solid fa-magnifying-glass-location"></i>
                <span>Lead Hunter</span>
            </a>
        </li>

        <div class="sidebar-heading">CRM & Pipeline</div>
        <li class="sidebar-item">
            <a href="/leads" class="sidebar-link <?= (str_starts_with($currentUri, '/lead')) ? 'active' : '' ?>">
                <i class="fa-solid fa-address-book"></i>
                <span>Prospects CRM</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/campaigns" class="sidebar-link <?= (str_starts_with($currentUri, '/campaign')) ? 'active' : '' ?>">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Campaigns</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/templates" class="sidebar-link <?= ($currentUri === '/templates') ? 'active' : '' ?>">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>Outreach Templates</span>
            </a>
        </li>

        <div class="sidebar-heading">Data & Tools</div>
        <li class="sidebar-item">
            <a href="/export" class="sidebar-link">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Export CSV</span>
            </a>
        </li>

        <div class="sidebar-heading">Account</div>
        <li class="sidebar-item">
            <a href="/profile" class="sidebar-link <?= ($currentUri === '/profile') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-gear"></i>
                <span>Agency Settings</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/billing" class="sidebar-link <?= ($currentUri === '/billing') ? 'active' : '' ?>">
                <i class="fa-solid fa-credit-card"></i>
                <span>Plan & Quotas</span>
            </a>
        </li>

        <?php if ($isAdmin): ?>
        <div class="sidebar-heading text-warning">Administration</div>
        <li class="sidebar-item">
            <a href="/admin" class="sidebar-link text-warning">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Admin Panel</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="p-3 border-top border-secondary border-opacity-25">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; flex-shrink: 0;">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="text-truncate">
                    <div class="text-white small fw-bold text-truncate"><?= e($user['name'] ?? 'User') ?></div>
                    <div class="text-muted" style="font-size: 0.72rem;"><?= e($user['role'] ?? 'user') ?></div>
                </div>
            </div>
            <a href="/logout" class="text-secondary hover-text-white p-1" title="Sign Out">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>
</aside>
