<?php
$pageTitle = "Outreach Templates Library | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$crm = new App\CRM();
$templates = $crm->getTemplates((int)$currentUser['id']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Outreach Templates Library</h3>
        <p class="text-secondary small mb-0">High-converting cold email and LinkedIn templates proven to generate agency client meetings.</p>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($templates as $tmpl): ?>
    <div class="col-lg-6">
        <div class="card-saas p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-secondary text-uppercase"><?= e($tmpl['category']) ?></span>
                <?php if ($tmpl['is_system']): ?>
                    <span class="badge bg-light text-muted border">System Default</span>
                <?php endif; ?>
            </div>
            <h5 class="fw-bold text-dark mb-2"><?= e($tmpl['name']) ?></h5>
            <?php if (!empty($tmpl['subject'])): ?>
                <div class="mb-2">
                    <small class="text-muted fw-bold">Subject Line:</small>
                    <input type="text" class="form-control form-control-sm" readonly value="<?= e($tmpl['subject']) ?>">
                </div>
            <?php endif; ?>
            <div class="copy-box flex-grow-1 mb-3" id="tmpl_<?= $tmpl['id'] ?>"><?= e($tmpl['body']) ?></div>
            <div class="text-end">
                <button class="btn btn-sm btn-outline-secondary btn-copy" data-clipboard-target="#tmpl_<?= $tmpl['id'] ?>">
                    <i class="fa-regular fa-copy me-1"></i> Copy Template
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
