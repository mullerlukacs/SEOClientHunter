<?php
$pageTitle = "Global SEO & Metadata | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seoData = [
        'site_title' => $_POST['site_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'keywords' => $_POST['keywords'] ?? '',
        'og_image' => $_POST['og_image'] ?? '',
        'twitter_card' => $_POST['twitter_card'] ?? 'summary_large_image',
        'robots_txt' => $_POST['robots_txt'] ?? '',
        'schema_json_ld' => $_POST['schema_json_ld'] ?? ''
    ];

    $cms->updateSeoSettings($seoData);
    $_SESSION['flash_success'] = "Global SEO settings updated.";
    header("Location: $adminBase/seo");
    exit;
}

$seo = $cms->getSeoSettings();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Global SEO & Structured Data</h3>
        <p class="text-secondary small mb-0">Manage global search engine metadata, OpenGraph cards, robots.txt, and Schema.org markup.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/robots.txt" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Test robots.txt
        </a>
        <a href="/sitemap.xml" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-sitemap me-1"></i> View sitemap.xml
        </a>
    </div>
</div>

<form method="POST">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-saas p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Primary Meta Tags</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Default Site Title</label>
                    <input type="text" name="site_title" class="form-control" value="<?= e($seo['site_title'] ?? '') ?>" required>
                    <small class="text-muted">Target 50-60 characters for optimal SERP display.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Global Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3"><?= e($seo['meta_description'] ?? '') ?></textarea>
                    <small class="text-muted">Target 150-160 characters describing the SaaS value proposition.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Target SEO Keywords</label>
                    <input type="text" name="keywords" class="form-control" value="<?= e($seo['keywords'] ?? '') ?>" placeholder="seo lead generator, b2b client finder, agency audit tool">
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">OpenGraph Social Share Image URL</label>
                        <input type="text" name="og_image" class="form-control" value="<?= e($seo['og_image'] ?? '') ?>" placeholder="https://.../og-banner-1200x630.png">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Twitter Card Type</label>
                        <select name="twitter_card" class="form-select">
                            <option value="summary_large_image" <?= (($seo['twitter_card'] ?? '') === 'summary_large_image') ? 'selected' : '' ?>>summary_large_image</option>
                            <option value="summary" <?= (($seo['twitter_card'] ?? '') === 'summary') ? 'selected' : '' ?>>summary</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-saas p-4">
                <h5 class="fw-bold text-dark mb-3">robots.txt Directives</h5>
                <p class="text-secondary small">Controls how search engine spiders and web crawlers index your domain.</p>
                <textarea name="robots_txt" class="form-control font-monospace small" rows="7"><?= e($seo['robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nDisallow: /api/\nSitemap: " . BASE_URL . "/sitemap.xml") ?></textarea>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-saas p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Schema.org JSON-LD</h5>
                <p class="text-secondary small">Injected globally into the HTML <code>&lt;head&gt;</code> for rich snippet discovery.</p>
                <textarea name="schema_json_ld" class="form-control font-monospace small" rows="12"><?= e($seo['schema_json_ld'] ?? "{\n  \"@context\": \"https://schema.org\",\n  \"@type\": \"SoftwareApplication\",\n  \"name\": \"SEO Client Hunter\",\n  \"applicationCategory\": \"BusinessApplication\",\n  \"operatingSystem\": \"Web\"\n}") ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save All Global SEO Settings
            </button>
        </div>
    </div>
</form>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
