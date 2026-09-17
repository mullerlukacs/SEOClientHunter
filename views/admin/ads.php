<?php
$pageTitle = "Ad Spaces & Banner Manager | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$cms = new App\CMS();
$db = App\Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_ad') {
        $name = $_POST['name'];
        $placement = $_POST['placement'];
        $html = $_POST['html_code'] ?? null;
        $img = $_POST['image_url'] ?? null;
        $link = $_POST['link_url'] ?? null;
        $active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $db->prepare("INSERT INTO ads (name, placement, html_code, image_url, link_url, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $placement, $html, $img, $link, $active]);
        $_SESSION['flash_success'] = "Ad space created.";
        header("Location: $adminBase/ads");
        exit;
    }

    if ($action === 'toggle_ad') {
        $id = (int)$_POST['ad_id'];
        $db->query("UPDATE ads SET is_active = NOT is_active WHERE id = $id");
        $_SESSION['flash_success'] = "Ad status updated.";
        header("Location: $adminBase/ads");
        exit;
    }

    if ($action === 'delete_ad') {
        $id = (int)$_POST['ad_id'];
        $db->query("DELETE FROM ads WHERE id = $id");
        $_SESSION['flash_success'] = "Ad deleted.";
        header("Location: $adminBase/ads");
        exit;
    }
}

$ads = $db->query("SELECT * FROM ads ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Ad Spaces & Sponsorship Banners</h3>
        <p class="text-secondary small mb-0">Monetize platform placements, run partner ads, or display affiliate banners.</p>
    </div>
    <button class="btn btn-primary btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#newAdModal">
        <i class="fa-solid fa-plus me-1"></i> Create Ad Banner
    </button>
</div>

<div class="card-saas">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-3">Ad Name</th>
                    <th>Placement Location</th>
                    <th>Impressions</th>
                    <th>Clicks</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ads)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No ads configured yet.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($ads as $ad): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark"><?= e($ad['name']) ?></div>
                            <small class="text-muted"><?= e($ad['link_url'] ?: 'HTML Snippet') ?></small>
                        </td>
                        <td><span class="badge bg-secondary text-uppercase"><?= e($ad['placement']) ?></span></td>
                        <td><?= number_format($ad['impressions']) ?></td>
                        <td><?= number_format($ad['clicks']) ?></td>
                        <td>
                            <span class="badge <?= $ad['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $ad['is_active'] ? 'Active' : 'Paused' ?>
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle_ad">
                                <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <?= $ad['is_active'] ? 'Pause' : 'Activate' ?>
                                </button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="delete_ad">
                                <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this ad?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: New Ad -->
<div class="modal fade" id="newAdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create_ad">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Sponsorship Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ad Title / Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Agency Hosting Partner" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Placement Zone</label>
                        <select name="placement" class="form-select">
                            <option value="header_banner">Top Header Banner</option>
                            <option value="footer_banner">Footer Partner Banner</option>
                            <option value="sidebar_banner">Dashboard Sidebar Banner</option>
                            <option value="lead_card_banner">Lead Card Sponsor Banner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Destination URL (optional)</label>
                        <input type="url" name="link_url" class="form-control" placeholder="https://partner.com/?ref=agency">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Image Banner URL (optional)</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://.../banner-728x90.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Or Custom HTML / AdSense Code</label>
                        <textarea name="html_code" class="form-control" rows="3" placeholder="<script>... or <div>..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="adActiveSw" checked>
                        <label class="form-check-label small fw-bold" for="adActiveSw">Enable immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Publish Ad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
