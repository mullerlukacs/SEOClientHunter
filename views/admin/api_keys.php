<?php
$pageTitle = "API Keys & Third-Party Integrations | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = [
        'gemini_api_key' => $_POST['gemini_api_key'] ?? '',
        'gemini_model' => $_POST['gemini_model'] ?? 'gemini-2.5-flash',
        'ai_provider_active' => isset($_POST['ai_provider_active']) ? '1' : '0',

        'search_api_provider' => $_POST['search_api_provider'] ?? 'demo',
        'google_search_api_key' => $_POST['google_search_api_key'] ?? '',
        'google_search_cx' => $_POST['google_search_cx'] ?? '',
        'serpapi_key' => $_POST['serpapi_key'] ?? '',

        'places_api_key' => $_POST['places_api_key'] ?? '',

        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => $_POST['smtp_port'] ?? '587',
        'smtp_user' => $_POST['smtp_user'] ?? '',
        'smtp_pass' => $_POST['smtp_pass'] ?? '',
        'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls',
        'smtp_from_email' => $_POST['smtp_from_email'] ?? '',
        'smtp_from_name' => $_POST['smtp_from_name'] ?? 'SEO Client Hunter'
    ];

    $cms->updateApiKeys($keys);
    $_SESSION['flash_success'] = "API credentials and provider settings saved.";
    header("Location: $adminBase/api-keys");
    exit;
}

$api = $cms->getApiKeys();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">API Keys & External Integrations</h3>
        <p class="text-secondary small mb-0">Configure external credentials for AI pitch synthesis, real-time search discovery, and transactional SMTP email.</p>
    </div>
</div>

<form method="POST">
    <!-- 1. AI Provider Integration -->
    <div class="card-saas p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-warning bg-opacity-10 text-dark rounded fs-4">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">AI Pitch Generation Provider</h5>
                    <small class="text-muted">Powers automated cold email and LinkedIn outreach formulation.</small>
                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ai_provider_active" id="aiActiveSw" <?= (($api['ai_provider_active'] ?? '0') === '1') ? 'checked' : '' ?>>
                <label class="form-check-label small fw-bold" for="aiActiveSw">Live API Enabled</label>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Gemini API Key</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-key text-muted"></i></span>
                    <input type="password" name="gemini_api_key" class="form-control" value="<?= e($api['gemini_api_key'] ?? '') ?>" placeholder="AIzaSy...">
                </div>
                <small class="text-muted">Leave empty to use built-in rule-based heuristic generation in Demo Mode.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Gemini Model</label>
                <select name="gemini_model" class="form-select">
                    <option value="gemini-2.5-flash" <?= (($api['gemini_model'] ?? '') === 'gemini-2.5-flash') ? 'selected' : '' ?>>gemini-2.5-flash (Recommended)</option>
                    <option value="gemini-1.5-pro" <?= (($api['gemini_model'] ?? '') === 'gemini-1.5-pro') ? 'selected' : '' ?>>gemini-1.5-pro</option>
                    <option value="gemini-1.5-flash" <?= (($api['gemini_model'] ?? '') === 'gemini-1.5-flash') ? 'selected' : '' ?>>gemini-1.5-flash</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 2. Search Provider Integration -->
    <div class="card-saas p-4 mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="p-3 bg-primary bg-opacity-10 text-primary rounded fs-4">
                <i class="fa-solid fa-magnifying-glass-location"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">Search Engine Discovery API</h5>
                <small class="text-muted">Powers real-time business discovery across localized markets.</small>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Active Search Provider</label>
                <select name="search_api_provider" class="form-select">
                    <option value="demo" <?= (($api['search_api_provider'] ?? 'demo') === 'demo') ? 'selected' : '' ?>>Built-In Demo Mode (No API keys needed)</option>
                    <option value="google_custom_search" <?= (($api['search_api_provider'] ?? '') === 'google_custom_search') ? 'selected' : '' ?>>Google Custom Search JSON API</option>
                    <option value="serpapi" <?= (($api['search_api_provider'] ?? '') === 'serpapi') ? 'selected' : '' ?>>SerpApi Engine</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Google Custom Search API Key</label>
                <input type="password" name="google_search_api_key" class="form-control" value="<?= e($api['google_search_api_key'] ?? '') ?>" placeholder="AIzaSy...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Google Search Engine ID (cx)</label>
                <input type="text" name="google_search_cx" class="form-control" value="<?= e($api['google_search_cx'] ?? '') ?>" placeholder="0123456789...">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">SerpApi Key (Optional Alternative)</label>
                <input type="password" name="serpapi_key" class="form-control" value="<?= e($api['serpapi_key'] ?? '') ?>" placeholder="serpapi_secret_key...">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Places / Business Data API Key (Optional)</label>
                <input type="password" name="places_api_key" class="form-control" value="<?= e($api['places_api_key'] ?? '') ?>" placeholder="AIzaSy...">
            </div>
        </div>
    </div>

    <!-- 3. SMTP Mail Credentials -->
    <div class="card-saas p-4 mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="p-3 bg-success bg-opacity-10 text-success rounded fs-4">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">Transactional SMTP Email Server</h5>
                <small class="text-muted">Sends system alerts, password resets, and agency notifications.</small>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">SMTP Host</label>
                <input type="text" name="smtp_host" class="form-control" value="<?= e($api['smtp_host'] ?? '') ?>" placeholder="smtp.mailtrap.io or smtp.sendgrid.net">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">SMTP Port</label>
                <input type="text" name="smtp_port" class="form-control" value="<?= e($api['smtp_port'] ?? '587') ?>" placeholder="587">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">SMTP Username</label>
                <input type="text" name="smtp_user" class="form-control" value="<?= e($api['smtp_user'] ?? '') ?>" placeholder="api_key_or_user">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">SMTP Password</label>
                <input type="password" name="smtp_pass" class="form-control" value="<?= e($api['smtp_pass'] ?? '') ?>" placeholder="••••••••">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Encryption</label>
                <select name="smtp_encryption" class="form-select">
                    <option value="tls" <?= (($api['smtp_encryption'] ?? 'tls') === 'tls') ? 'selected' : '' ?>>TLS (Recommended)</option>
                    <option value="ssl" <?= (($api['smtp_encryption'] ?? '') === 'ssl') ? 'selected' : '' ?>>SSL</option>
                    <option value="none" <?= (($api['smtp_encryption'] ?? '') === 'none') ? 'selected' : '' ?>>None</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">From Sender Email</label>
                <input type="email" name="smtp_from_email" class="form-control" value="<?= e($api['smtp_from_email'] ?? 'noreply@seoclienthunter.com') ?>" placeholder="noreply@domain.com">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">From Sender Name</label>
                <input type="text" name="smtp_from_name" class="form-control" value="<?= e($api['smtp_from_name'] ?? 'SEO Client Hunter') ?>" placeholder="SEO Client Hunter">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary px-4 py-3 fw-bold shadow-sm">
        <i class="fa-solid fa-floppy-disk me-1"></i> Save API Credentials & Settings
    </button>
</form>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
