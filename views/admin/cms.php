<?php
$pageTitle = "CMS Pages & Homepage Sections | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();
$db = App\Database::getInstance();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_page') {
        $id = (int)$_POST['page_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $metaTitle = $_POST['meta_title'];
        $metaDesc = $_POST['meta_description'];
        $canonical = $_POST['canonical_url'];
        $robots = $_POST['robots_setting'];
        $status = $_POST['status'];

        $stmt = $db->prepare("UPDATE pages SET title = ?, content = ?, meta_title = ?, meta_description = ?, canonical_url = ?, robots_setting = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $content, $metaTitle, $metaDesc, $canonical, $robots, $status, $id]);

        $_SESSION['flash_success'] = "Page updated successfully.";
        header("Location: $adminBase/cms");
        exit;
    }

    if ($action === 'save_section') {
        $id = (int)$_POST['section_id'];
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $content = $_POST['content'];
        $ctaText = $_POST['cta_text'];
        $ctaLink = $_POST['cta_link'];
        $enabled = isset($_POST['is_enabled']) ? 1 : 0;

        $stmt = $db->prepare("UPDATE homepage_sections SET title = ?, subtitle = ?, content = ?, cta_text = ?, cta_link = ?, is_enabled = ? WHERE id = ?");
        $stmt->execute([$title, $subtitle, $content, $ctaText, $ctaLink, $enabled, $id]);

        $_SESSION['flash_success'] = "Homepage section updated.";
        header("Location: $adminBase/cms?tab=sections");
        exit;
    }
}

$pages = $cms->getAllPages();
$sections = $cms->getHomepageSections();
$activeTab = $_GET['tab'] ?? 'pages';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">CMS Pages & Homepage Sections</h3>
        <p class="text-secondary small mb-0">Control web pages, dynamic content blocks, on-page SEO meta tags, and hero copy.</p>
    </div>
</div>

<div class="card-saas mb-4">
    <div class="border-bottom px-3">
        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'pages') ? 'active fw-bold' : 'text-secondary' ?> py-3" href="?tab=pages">
                    <i class="fa-solid fa-file-lines me-2"></i> Standard Pages (<?= count($pages) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'sections') ? 'active fw-bold' : 'text-secondary' ?> py-3" href="?tab=sections">
                    <i class="fa-solid fa-layer-group me-2"></i> Homepage Sections (<?= count($sections) ?>)
                </a>
            </li>
        </ul>
    </div>

    <div class="p-4">
        <?php if ($activeTab === 'pages'): ?>
            <div class="row g-4">
                <?php foreach ($pages as $pg): ?>
                <div class="col-lg-6">
                    <div class="card-saas p-4 h-100 border">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary text-uppercase"><?= e($pg['slug']) ?></span>
                            <span class="badge <?= $pg['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst($pg['status']) ?></span>
                        </div>
                        <h5 class="fw-bold text-dark mb-3"><?= e($pg['title']) ?></h5>

                        <form method="POST">
                            <input type="hidden" name="action" value="save_page">
                            <input type="hidden" name="page_id" value="<?= $pg['id'] ?>">

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Page Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" value="<?= e($pg['title']) ?>" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">SEO Meta Title</label>
                                <input type="text" name="meta_title" class="form-control form-control-sm" value="<?= e($pg['meta_title'] ?? '') ?>">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">SEO Meta Description</label>
                                <textarea name="meta_description" class="form-control form-control-sm" rows="2"><?= e($pg['meta_description'] ?? '') ?></textarea>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Robots Tag</label>
                                    <input type="text" name="robots_setting" class="form-control form-control-sm" value="<?= e($pg['robots_setting'] ?? 'index, follow') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="published" <?= ($pg['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                                        <option value="draft" <?= ($pg['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Canonical URL (Optional)</label>
                                <input type="text" name="canonical_url" class="form-control form-control-sm" value="<?= e($pg['canonical_url'] ?? '') ?>" placeholder="https://...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Page Content (HTML)</label>
                                <textarea name="content" class="form-control form-control-sm" rows="5"><?= e($pg['content']) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Page Changes
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($sections as $sec): ?>
                <div class="col-lg-6">
                    <div class="card-saas p-4 h-100 border">
                        <form method="POST">
                            <input type="hidden" name="action" value="save_section">
                            <input type="hidden" name="section_id" value="<?= $sec['id'] ?>">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-secondary text-uppercase"><?= e($sec['section_key']) ?></span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_enabled" id="sw_<?= $sec['id'] ?>" <?= $sec['is_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold" for="sw_<?= $sec['id'] ?>">Section Enabled</label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Headline / Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" value="<?= e($sec['title'] ?? '') ?>">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control form-control-sm" value="<?= e($sec['subtitle'] ?? '') ?>">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Body Content</label>
                                <textarea name="content" class="form-control form-control-sm" rows="3"><?= e($sec['content'] ?? '') ?></textarea>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">CTA Text</label>
                                    <input type="text" name="cta_text" class="form-control form-control-sm" value="<?= e($sec['cta_text'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">CTA Link</label>
                                    <input type="text" name="cta_link" class="form-control form-control-sm" value="<?= e($sec['cta_link'] ?? '') ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Section
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
