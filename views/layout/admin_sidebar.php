<?php
/**
 * SEO Client Hunter - Admin Sidebar Component
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$cmsService = new App\CMS();
$siteSettings = $cmsService->getSiteSettings();
$adminBase = '/' . trim($siteSettings['admin_path'] ?? 'admin', '/');
?>
<aside class="dashboard-sidebar bg-dark text-white" id="adminSidebar" style="border-right: 1px solid #334155;">
    <a href="<?= $adminBase ?>" class="sidebar-brand text-white border-bottom border-secondary">
        <div class="brand-icon-box bg-danger text-white">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <span>Admin Panel</span>
    </a>

    <ul class="sidebar-menu">
        <div class="sidebar-heading text-secondary">Platform Control</div>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>" class="sidebar-link text-light <?= ($currentUri === $adminBase || $currentUri === $adminBase . '/dashboard') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Master Dashboard</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/users" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/users') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-users"></i>
                <span>Users & Subscriptions</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/plans" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/plans') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-tags"></i>
                <span>Plans & Quotas</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/leads" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/leads') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-database"></i>
                <span>Master Leads Pool</span>
            </a>
        </li>

        <div class="sidebar-heading text-secondary">Content & Marketing</div>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/cms" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/cms') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-file-lines"></i>
                <span>CMS Pages & Sections</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/menus" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/menus') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-bars-staggered"></i>
                <span>Menu Manager</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/ads" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/ads') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-rectangle-ad"></i>
                <span>Ad Spaces & Banners</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/seo" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/seo') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-globe"></i>
                <span>Global SEO & Metadata</span>
            </a>
        </li>

        <div class="sidebar-heading text-secondary">Configuration & Security</div>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/settings" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/settings') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-sliders"></i>
                <span>Site Settings & Branding</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/api-keys" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/api-keys') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-key"></i>
                <span>API Keys & Integrations</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= $adminBase ?>/logs" class="sidebar-link text-light <?= ($currentUri === $adminBase . '/logs') ? 'active bg-danger' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Audit & System Logs</span>
            </a>
        </li>

        <div class="sidebar-heading text-secondary">Switch View</div>
        <li class="sidebar-item">
            <a href="/dashboard" class="sidebar-link text-info">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Go to User Dashboard</span>
            </a>
        </li>
    </ul>

    <div class="p-3 border-top border-secondary">
        <span class="badge bg-danger w-100 py-2">
            <i class="fa-solid fa-user-shield me-1"></i> SUPER ADMIN MODE
        </span>
    </div>
</aside>
