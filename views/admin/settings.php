<?php
$pageTitle = "Site Settings & Branding | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? 'SEO Client Hunter',
        'site_tagline' => $_POST['site_tagline'] ?? '',
        'primary_color' => $_POST['primary_color'] ?? '#2563eb',
        'button_radius' => $_POST['button_radius'] ?? '8px',
        'demo_mode' => isset($_POST['demo_mode']) ? '1' : '0',
        'registration_enabled' => isset($_POST['registration_enabled']) ? '1' : '0',
        'admin_path' => $_POST['admin_path'] ?? 'admin',
        'announcement_bar' => $_POST['announcement_bar'] ?? '',
        'announcement_enabled' => isset($_POST['announcement_enabled']) ? '1' : '0',
        'header_code' => $_POST['header_code'] ?? '',
        'footer_code' => $_POST['footer_code'] ?? '',
        'body_code' => $_POST['body_code'] ?? '',
        'footer_description' => $_POST['footer_description'] ?? ''
    ];

    $cms->updateSiteSettings($settings);
    $_SESSION['flash_success'] = "Platform settings & branding saved.";
    header("Location: $adminBase/settings");
    exit;
}

$site = $cms->getSiteSettings();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Site Settings & Branding</h3>
        <p class="text-secondary small mb-0">Brand styling, demo mode control, security paths, and script injection.</p>
    </div>
</div>

<form method="POST">
    <div class="row g-4">
        <!-- Brand Identity -->
        <div class="col-lg-6">
            <div class="card-saas p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Branding & Visual Theme</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Platform Brand Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?= e($site['site_name'] ?? 'SEO Client Hunter') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Brand Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="<?= e($site['site_tagline'] ?? 'Automated SEO Prospect Discovery') ?>">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Primary Brand Color</label>
                        <div class="input-group">
                            <input type="color" name="primary_color" class="form-control form-control-color" value="<?= e($site['primary_color'] ?? '#2563eb') ?>">
                            <input type="text" class="form-control" value="<?= e($site['primary_color'] ?? '#2563eb') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Button Border Radius</label>
                        <select name="button_radius" class="form-select">
                            <option value="4px" <?= (($site['button_radius'] ?? '') === '4px') ? 'selected' : '' ?>>4px (Subtle)</option>
                            <option value="8px" <?= (($site['button_radius'] ?? '8px') === '8px') ? 'selected' : '' ?>>8px (Modern Default)</option>
                            <option value="12px" <?= (($site['button_radius'] ?? '') === '12px') ? 'selected' : '' ?>>12px (Rounded)</option>
                            <option value="24px" <?= (($site['button_radius'] ?? '') === '24px') ? 'selected' : '' ?>>24px (Pill)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Footer Description</label>
                    <textarea name="footer_description" class="form-control" rows="3"><?= e($site['footer_description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="card-saas p-4">
                <h5 class="fw-bold text-dark mb-3">Announcement Top Bar</h5>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="announcement_enabled" id="annSw" <?= (($site['announcement_enabled'] ?? '') === '1') ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-bold" for="annSw">Display Announcement Bar Globally</label>
                </div>
                <input type="text" name="announcement_bar" class="form-control" value="<?= e($site['announcement_bar'] ?? '🚀 New: Automated 6-Tier SEO Diagnostic Engine Live!') ?>" placeholder="Announcement banner text">
            </div>
        </div>

        <!-- Engine & Security Modes -->
        <div class="col-lg-6">
            <div class="card-saas p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Operation Mode & Security</h5>

                <!-- Demo Mode Toggle -->
                <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="demo_mode" id="demoSw" <?= (($site['demo_mode'] ?? '1') === '1') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-dark" for="demoSw">
                            <i class="fa-solid fa-flask-vial text-warning me-1"></i> Enable DEMO / MOCK Mode
                        </label>
                    </div>
                    <small class="text-secondary d-block mt-2">
                        When enabled, search discovery and AI pitch formulation run in simulated heuristic mode without consuming real external API credits. Recommended for immediate testing and staging.
                    </small>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="registration_enabled" id="regSw" <?= (($site['registration_enabled'] ?? '1') === '1') ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-bold" for="regSw">Allow Public User Registrations</label>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Admin Portal URL Slug</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted small"><?= BASE_URL ?>/</span>
                        <input type="text" name="admin_path" class="form-control" value="<?= e($site['admin_path'] ?? 'admin') ?>" required>
                    </div>
                    <small class="text-muted">You can customize this to obscure your administrative path.</small>
                </div>
            </div>

            <!-- Custom Injections -->
            <div class="card-saas p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Custom Code & Tracking Injections</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Header Scripts (<code>&lt;head&gt;</code>)</label>
                    <textarea name="header_code" class="form-control font-monospace small" rows="2" placeholder="<!-- Google Analytics / Meta Pixel -->"><?= e($site['header_code'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Footer Scripts (Before <code>&lt;/body&gt;</code>)</label>
                    <textarea name="footer_code" class="form-control font-monospace small" rows="2" placeholder="<!-- Chat widget or conversion scripts -->"><?= e($site['footer_code'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Platform Settings
            </button>
        </div>
    </div>
</form>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
