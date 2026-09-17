<?php
/**
 * SEO Client Hunter - Dynamic CMS Page Renderer
 */
$pageTitle = ($page['meta_title'] ?? $page['title']) . " | SEO Client Hunter";
$pageMetaDescription = $page['meta_description'] ?? '';
$canonicalUrl = $page['canonical_url'] ?? null;
$robotsSetting = $page['robots_setting'] ?? 'index, follow';

require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-saas p-4 p-md-5">
                <h1 class="display-6 fw-bold text-dark mb-4"><?= e($page['title']) ?></h1>
                <div class="content-body text-secondary lh-lg">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
