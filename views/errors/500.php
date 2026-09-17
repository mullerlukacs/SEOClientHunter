<?php
http_response_code(500);
$pageTitle = "500 - Internal System Error | SEO Client Hunter";
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
                <i class="fa-solid fa-triangle-exclamation text-warning fa-4x"></i>
            </div>
            <h1 class="display-5 fw-bold text-dark mb-2">500</h1>
            <h4 class="text-secondary mb-3">Internal System Error</h4>
            <p class="text-muted mb-4">Our server encountered an unexpected error while processing your request. The incident has been logged for review.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="/" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-house me-2"></i>Return Home</a>
                <a href="javascript:location.reload()" class="btn btn-outline-secondary px-4 py-2"><i class="fa-solid fa-rotate me-2"></i>Retry</a>
            </div>
        </div>
    </div>
</body>
</html>
