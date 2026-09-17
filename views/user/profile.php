<?php
$pageTitle = "Agency Profile & Settings | SEO Client Hunter";
require VIEWS_DIR . '/layout/user_header.php';

$auth = new App\Auth();
$userProfile = $auth->getUser();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Agency Profile & Settings</h3>
        <p class="text-secondary small mb-0">Configure your agency branding, email sender details, and account security.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-saas p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">Agency Details</h5>
            <form method="POST" action="/profile">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold">Your Name / Agency Owner</label>
                    <input type="text" name="name" class="form-control" value="<?= e($userProfile['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Agency Brand Name</label>
                    <input type="text" name="agency_name" class="form-control" value="<?= e($userProfile['agency_name'] ?? 'Apex SEO Media') ?>" placeholder="e.g. Apex SEO Media">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Account Email Address</label>
                    <input type="email" class="form-control" value="<?= e($userProfile['email'] ?? '') ?>" readonly disabled>
                    <small class="text-muted">Contact support if you need to alter your login email address.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Default Email Signature</label>
                    <textarea name="signature" class="form-control" rows="3" placeholder="Best regards,&#10;Alexander Wright | Apex SEO Media&#10;Book a call: calendly.com/apex"><?= e($userProfile['email_signature'] ?? '') ?></textarea>
                    <small class="text-muted">Appended to generated pitch proposals.</small>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Agency Profile
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-saas p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">Change Account Password</h5>
            <form method="POST" action="/profile">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="8">
                </div>

                <button type="submit" class="btn btn-outline-danger w-100 py-2">
                    <i class="fa-solid fa-lock me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/user_footer.php'; ?>
