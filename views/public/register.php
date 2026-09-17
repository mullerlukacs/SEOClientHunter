<?php
$pageTitle = "Start Your Free Trial | SEO Client Hunter";
$pageMetaDescription = "Create your SEO Client Hunter account today to discover high-value prospects and run deep 6-tier audits.";

require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card-saas p-4 p-md-5 border-0 shadow-sm">
                <div class="text-center mb-4">
                    <div class="brand-icon-box mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fa-solid fa-crosshairs"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Create Your Account</h3>
                    <p class="text-muted small">Start finding high-opportunity SEO clients in seconds</p>
                </div>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($_SESSION['flash_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php unset($_SESSION['flash_error']); endif; ?>

                <form method="POST" action="/register">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Full Name / Agency Owner</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="name" class="form-control" required placeholder="Alexander Wright" value="<?= e($_POST['name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Work Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required placeholder="alex@apexmarketing.com" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">Create Secure Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="At least 8 characters">
                        </div>
                        <div class="form-text text-muted small">Minimum 8 characters with letters and numbers recommended.</div>
                    </div>

                    <div class="mb-3 form-check small">
                        <input type="checkbox" class="form-check-input" id="termsCheck" required checked>
                        <label class="form-check-label text-secondary" for="termsCheck">
                            I agree to the <a href="/terms" target="_blank" class="text-decoration-none text-primary">Terms of Service</a> and <a href="/privacy" target="_blank" class="text-decoration-none text-primary">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">
                        <i class="fa-solid fa-rocket me-1"></i> Activate Free Trial Account
                    </button>
                </form>

                <div class="text-center mt-3 text-secondary small">
                    Already have an account? <a href="/login" class="fw-bold text-primary text-decoration-none">Sign In Here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
