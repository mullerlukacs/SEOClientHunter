<?php
/**
 * SEO Client Hunter - Authentication & Authorization Service
 */

namespace App;

use PDO;

class Auth {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register(string $name, string $email, string $password, int $planId = 1): array {
        $email = strtolower(trim($email));
        $name = trim($name);

        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please provide a valid email address.'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long.'];
        }

        // Check if registration is allowed in settings
        $stmt = $this->db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'allow_registration'");
        $stmt->execute();
        $allowReg = $stmt->fetchColumn();
        if ($allowReg === '0') {
            return ['success' => false, 'message' => 'Registration is currently disabled by administrator.'];
        }

        // Check for existing user
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';

        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, role, status, plan_id) VALUES (?, ?, ?, ?, 'active', ?)");
        $stmt->execute([$name, $email, $passwordHash, $role, $planId]);
        $userId = (int)$this->db->lastInsertId();

        // Create subscription record
        $stmtSub = $this->db->prepare("INSERT INTO subscriptions (user_id, plan_id, status) VALUES (?, ?, 'active')");
        $stmtSub->execute([$userId, $planId]);

        // Auto login
        $this->setUserSession($userId, $name, $email, $role, $planId);

        Logger::logActivity($userId, 'User Registered', "New account registered with email {$email}", 'user');

        return ['success' => true, 'message' => 'Registration successful! Welcome to SEO Client Hunter.'];
    }

    public function login(string $email, string $password): array {
        $email = strtolower(trim($email));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Rate limiting: max 10 failed attempts per 15 minutes
        if ($this->isRateLimited($ip)) {
            return ['success' => false, 'message' => 'Too many failed login attempts. Please wait 15 minutes before trying again.'];
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->recordFailedAttempt($ip);
            Logger::logActivity(null, 'Failed Login', "Failed login attempt for email: {$email}", 'login');
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if ($user['status'] === 'suspended') {
            return ['success' => false, 'message' => 'Your account has been suspended. Please contact customer support.'];
        }

        // Reset failed attempts on success
        $this->clearFailedAttempts($ip);

        // Update last login
        $now = date('Y-m-d H:i:s');
        $this->db->prepare("UPDATE users SET last_login_at = ? WHERE id = ?")->execute([$now, $user['id']]);

        $this->setUserSession((int)$user['id'], $user['name'], $user['email'], $user['role'], (int)($user['plan_id'] ?? 1));

        Logger::logActivity((int)$user['id'], 'User Login', "User {$user['email']} successfully logged in", 'login');

        return ['success' => true, 'user' => $user];
    }

    public function logout(): void {
        $userId = $this->getUserId();
        if ($userId) {
            Logger::logActivity($userId, 'User Logout', 'User logged out', 'user');
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function setUserSession(int $id, string $name, string $email, string $role, int $planId): void {
        // Regenerate session ID for security against fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_plan_id'] = $planId;
    }

    public function isLoggedIn(): bool {
        return !empty($_SESSION['user_id']);
    }

    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUser(): ?array {
        $id = $this->getUserId();
        if (!$id) return null;
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function isAdmin(): bool {
        $role = strtolower($_SESSION['user_role'] ?? '');
        return in_array($role, ['admin', 'super admin', 'superadmin'], true);
    }

    public function isSuperAdmin(): bool {
        return ($_SESSION['user_role'] ?? '') === 'Super Admin';
    }

    public function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash_error'] = 'Please log in to access this page.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function requireAdmin(): void {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }

    private function isRateLimited(string $ip): bool {
        // Simple rate limit based on recent activity logs
        $fifteenMinutesAgo = date('Y-m-d H:i:s', time() - 900);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM activity_logs WHERE ip_address = ? AND action = 'Failed Login' AND created_at >= ?");
        $stmt->execute([$ip, $fifteenMinutesAgo]);
        return (int)$stmt->fetchColumn() >= 10;
    }

    private function recordFailedAttempt(string $ip): void {
        // Recorded via Logger::logActivity
    }

    private function clearFailedAttempts(string $ip): void {
        // Clear recent failed logs for this IP
        $this->db->prepare("DELETE FROM activity_logs WHERE ip_address = ? AND action = 'Failed Login'")->execute([$ip]);
    }
}
