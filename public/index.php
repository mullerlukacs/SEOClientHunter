<?php
/**
 * SEO Client Hunter - Front Controller Entry Point
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Class Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Handle serving static assets when running via built-in PHP server
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (str_starts_with($uri, '/assets/')) {
    $filePath = realpath(__DIR__ . '/..' . $uri);
    if ($filePath && file_exists($filePath) && str_starts_with($filePath, realpath(__DIR__ . '/..'))) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'json' => 'application/json',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf'
        ];
        header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
        readfile($filePath);
        exit;
    }
}

// Global Exception Handler
set_exception_handler(function (\Throwable $e) {
    App\Logger::logSystem('critical', $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    if (ini_get('display_errors')) {
        echo "<div style='font-family:sans-serif;padding:24px;background:#fee2e2;color:#991b1b;margin:20px;border-radius:8px;'>";
        echo "<h3 style='margin-top:0;'>Application Exception</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
        echo "<pre style='background:#fef2f2;padding:12px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        require_once VIEWS_DIR . '/errors/500.php';
    }
});

// Dispatch router
$router = new App\Router();
$router->dispatch();
