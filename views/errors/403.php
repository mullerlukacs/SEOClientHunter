<?php
http_response_code(403);
$pageTitle = "403 - Access Forbidden | SEO Client Hunter";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">
    <div class="container text-center">
        <div class="card shadow-sm border-0 mx-auto p-5" style="max-width: 550px; border-radius: 16px;">
            <div class="mb-4">
                <i class="fa-solid fa-shield-halved text-danger fa-4x"></i>
            </div>
            <h1 class="display-5 fw-bold text-dark mb-2">403</h1>
            <h4 class="text-secondary mb-3">Access Denied</h4>
            <p class="text-muted mb-4">You do not have the required security permissions or role to view this administrative resource.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="/dashboard" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-gauge me-2"></i>User Dashboard</a>
                <a href="/logout" class="btn btn-outline-danger px-4 py-2"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign In As Admin</a>
            </div>
        </div>
    </div>
</body>
</html>
