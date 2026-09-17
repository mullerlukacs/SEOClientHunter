<?php
/**
 * SEO Client Hunter - System & Activity Logger
 */

namespace App;

use PDO;

class Logger {
    public static function logActivity(?int $userId, string $action, string $description, string $type = 'user'): void {
        try {
            $db = Database::getInstance();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI', 0, 255);

            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $description, $ip, $ua, $type]);
        } catch (\Throwable $e) {
            // Silently fallback to file log if DB issue
            self::logFile("Activity Log Error: " . $e->getMessage());
        }
    }

    public static function logSystem(string $level, string $message, ?array $context = null): void {
        try {
            $db = Database::getInstance();
            $contextJson = $context ? json_encode($context) : null;

            $stmt = $db->prepare("INSERT INTO system_logs (level, message, context) VALUES (?, ?, ?)");
            $stmt->execute([$level, $message, $contextJson]);
        } catch (\Throwable $e) {
            self::logFile("[{$level}] {$message}");
        }
    }

    public static function logFile(string $message): void {
        $logDir = LOGS_DIR;
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/app.log';
        $timestamp = date('Y-m-d H:i:s');
        @file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    }
}
