<?php
$pageTitle = "Users & Subscriptions | Admin Panel";
require VIEWS_DIR . '/layout/admin_header.php';

$auth = new App\Auth();
$db = App\Database::getInstance();
$plans = $auth->getAllPlans();

// Handle User Actions (Role update, Plan update, Status toggle, Password reset)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($action === 'update_user' && $userId) {
        $role = $_POST['role'] ?? 'user';
        $planId = (int)($_POST['plan_id'] ?? 1);
        $status = $_POST['status'] ?? 'active';

        $stmt = $db->prepare("UPDATE users SET role = ?, plan_id = ?, status = ? WHERE id = ?");
        $stmt->execute([$role, $planId, $status, $userId]);
        $_SESSION['flash_success'] = "User #$userId updated successfully.";
        header("Location: $adminBase/users");
        exit;
    }

    if ($action === 'reset_password' && $userId) {
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) >= 6) {
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $userId]);
            $_SESSION['flash_success'] = "Password for user #$userId was reset.";
        }
        header("Location: $adminBase/users");
        exit;
    }
}

$users = $db->query("SELECT u.*, p.name as plan_name FROM users u LEFT JOIN plans p ON u.plan_id = p.id ORDER BY u.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Users & Subscriptions</h3>
        <p class="text-secondary small mb-0">Manage registered agencies, subscription plans, access roles, and account security.</p>
    </div>
</div>

<div class="card-saas">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-3">ID & Agency Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark"><?= e($u['name']) ?></div>
                        <div class="text-muted small">ID: #<?= $u['id'] ?> &bull; <?= e($u['agency_name'] ?: 'Agency') ?></div>
                    </td>
                    <td><?= e($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= strtoupper($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <?= e($u['plan_name'] ?: 'Starter') ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $u['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($u['status']) ?>
                        </span>
                    </td>
                    <td class="small text-muted"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal_<?= $u['id'] ?>">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetPassModal_<?= $u['id'] ?>">
                            <i class="fa-solid fa-key me-1"></i> Reset Pass
                        </button>
                    </td>
                </tr>

                <!-- Modal: Edit User -->
                <div class="modal fade" id="editUserModal_<?= $u['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit User #<?= $u['id'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Account Name</label>
                                        <input type="text" class="form-control" value="<?= e($u['name']) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Role</label>
                                        <select name="role" class="form-select">
                                            <option value="user" <?= ($u['role'] === 'user') ? 'selected' : '' ?>>Standard User</option>
                                            <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>Super Administrator</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Subscription Plan</label>
                                        <select name="plan_id" class="form-select">
                                            <?php foreach ($plans as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= ($u['plan_id'] == $p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?> ($<?= $p['price'] ?>/mo)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Account Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" <?= ($u['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                            <option value="suspended" <?= ($u['status'] === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm px-3">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal: Reset Password -->
                <div class="modal fade" id="resetPassModal_<?= $u['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <input type="hidden" name="action" value="reset_password">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Reset Password for <?= e($u['name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Enter new password">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger btn-sm px-3">Confirm Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/admin_footer.php'; ?>
