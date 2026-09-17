<?php
$pageTitle = "Menu Manager | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();
$db = App\Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_item') {
        $location = $_POST['location'];
        $label = $_POST['label'];
        $url = $_POST['url'];
        $target = $_POST['target'] ?? '_self';
        $order = (int)($_POST['sort_order'] ?? 0);

        $stmt = $db->prepare("INSERT INTO menu_items (location, label, url, target, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$location, $label, $url, $target, $order]);
        $_SESSION['flash_success'] = "Menu link added.";
        header("Location: $adminBase/menus");
        exit;
    }

    if ($action === 'delete_item') {
        $id = (int)$_POST['item_id'];
        $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = "Menu link deleted.";
        header("Location: $adminBase/menus");
        exit;
    }
}

$headerItems = $cms->getMenuItems('header');
$footerItems = $cms->getMenuItems('footer');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Navigation Menu Manager</h3>
        <p class="text-secondary small mb-0">Configure header and footer navigation links, labels, and targets.</p>
    </div>
    <button class="btn btn-primary px-3 py-2 btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="fa-solid fa-plus me-1"></i> Add Menu Link
    </button>
</div>

<div class="row g-4">
    <!-- Header Menu -->
    <div class="col-lg-6">
        <div class="card-saas p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-bars me-2 text-primary"></i>Main Header Menu</h5>
            <div class="list-group">
                <?php foreach ($headerItems as $item): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="fw-bold text-dark"><?= e($item['label']) ?></div>
                        <small class="text-muted"><?= e($item['url']) ?> (<?= e($item['target']) ?>)</small>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="return confirm('Remove link?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer Menu -->
    <div class="col-lg-6">
        <div class="card-saas p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shoe-prints me-2 text-secondary"></i>Footer Navigation Menu</h5>
            <div class="list-group">
                <?php foreach ($footerItems as $item): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="fw-bold text-dark"><?= e($item['label']) ?></div>
                        <small class="text-muted"><?= e($item['url']) ?> (<?= e($item['target']) ?>)</small>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="return confirm('Remove link?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_item">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Navigation Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Menu Location</label>
                        <select name="location" class="form-select">
                            <option value="header">Main Header</option>
                            <option value="footer">Footer Menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Label</label>
                        <input type="text" name="label" class="form-control" required placeholder="e.g. Case Studies">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">URL / Link</label>
                        <input type="text" name="url" class="form-control" required placeholder="/case-studies or https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Target</label>
                        <select name="target" class="form-select">
                            <option value="_self">Same Tab (_self)</option>
                            <option value="_blank">New Tab (_blank)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Add Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
