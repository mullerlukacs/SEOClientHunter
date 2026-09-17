<?php
$pageTitle = "Sign In | SEO Client Hunter";
$pageMetaDescription = "Sign in to your SEO Client Hunter dashboard to access prospect leads, audit reports, and cold outreach sequences.";

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
                    <h3 class="fw-bold text-dark">Welcome Back</h3>
                    <p class="text-muted small">Sign in to manage your SEO prospect pipeline</p>
                </div>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($_SESSION['flash_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php unset($_SESSION['flash_error']); endif; ?>

                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
                        <i class="fa-solid fa-circle-check me-1"></i> <?= e($_SESSION['flash_success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php unset($_SESSION['flash_success']); endif; ?>

                <form method="POST" action="/login">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" id="loginEmail" class="form-control" required placeholder="name@agency.com" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-secondary mb-0">Password</label>
                            <a href="#demo-accounts" class="text-decoration-none small text-primary" data-bs-toggle="collapse">Forgot password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" id="loginPassword" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In to Dashboard
                    </button>
                </form>

                <!-- One-Click Instant Test Credentials Helper -->
                <div class="p-3 bg-light rounded border mt-2 small">
                    <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-flask-vial text-warning me-1"></i> Instant Demo Credentials:</span>
                        <span class="badge bg-secondary">Demo Mode</span>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm text-start" onclick="fillCreds('demo@seoclienthunter.com', 'Demo123!')">
                            <strong>Agency Owner:</strong> <code>demo@seoclienthunter.com</code> / <code>Demo123!</code>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm text-start" onclick="fillCreds('admin@seoclienthunter.com', 'Admin123!')">
                            <strong>Super Admin:</strong> <code>admin@seoclienthunter.com</code> / <code>Admin123!</code>
                        </button>
                    </div>
                </div>

                <div class="text-center mt-4 text-secondary small">
                    Don't have an agency account yet? <a href="/register" class="fw-bold text-primary text-decoration-none">Start Free Trial</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillCreds(email, pass) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPassword').value = pass;
}
</script>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
