<?php
/**
 * SEO Client Hunter - Core HTTP Router & Request Dispatcher
 */

namespace App;

class Router {
    private Auth $auth;
    private CMS $cms;

    public function __construct() {
        $this->auth = new Auth();
        $this->cms = new CMS();
    }

    public function dispatch(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Normalize URI
        $path = '/' . trim($uri, '/');
        if ($path === '//') $path = '/';

        // Check configurable admin path
        $settings = $this->cms->getSiteSettings();
        $adminPath = '/' . trim($settings['admin_path'] ?? 'admin', '/');

        // Handle dynamic sitemap and robots
        if ($path === '/sitemap.xml') {
            header('Content-Type: application/xml; charset=utf-8');
            echo $this->cms->generateSitemapXml();
            exit;
        }

        if ($path === '/robots.txt') {
            header('Content-Type: text/plain; charset=utf-8');
            echo $this->cms->getRobotsTxt();
            exit;
        }

        // ==========================================
        // 1. API ROUTES
        // ==========================================
        if (str_starts_with($path, '/api/')) {
            $this->handleApiRoutes($path, $method);
            return;
        }

        // ==========================================
        // 2. ADMIN AREA
        // ==========================================
        if ($path === $adminPath || str_starts_with($path, $adminPath . '/')) {
            $this->auth->requireAdmin();
            $subPath = substr($path, strlen($adminPath));
            $subPath = '/' . trim($subPath, '/');
            $this->handleAdminRoutes($subPath, $method);
            return;
        }

        // ==========================================
        // 3. USER AREA (Authenticated)
        // ==========================================
        $userRoutes = [
            '/dashboard', '/search', '/leads', '/lead/new', '/campaigns',
            '/campaign', '/templates', '/export', '/profile', '/billing'
        ];

        if (in_array($path, $userRoutes, true) || str_starts_with($path, '/lead/') || str_starts_with($path, '/campaign/')) {
            $this->auth->requireLogin();
            $this->handleUserRoutes($path, $method);
            return;
        }

        // ==========================================
        // 4. PUBLIC WEBSITE
        // ==========================================
        $this->handlePublicRoutes($path, $method);
    }

    private function handlePublicRoutes(string $path, string $method): void {
        switch ($path) {
            case '/':
                require VIEWS_DIR . '/public/home.php';
                break;
            case '/features':
                require VIEWS_DIR . '/public/features.php';
                break;
            case '/pricing':
                require VIEWS_DIR . '/public/pricing.php';
                break;
            case '/about':
                require VIEWS_DIR . '/public/about.php';
                break;
            case '/contact':
                require VIEWS_DIR . '/public/contact.php';
                break;
            case '/privacy':
                require VIEWS_DIR . '/public/privacy.php';
                break;
            case '/terms':
                require VIEWS_DIR . '/public/terms.php';
                break;
            case '/quick-audit':
                require VIEWS_DIR . '/public/quick_audit.php';
                break;
            case '/login':
                if ($method === 'POST') {
                    $this->handleLoginPost();
                } else {
                    if ($this->auth->isLoggedIn()) {
                        header('Location: ' . BASE_URL . '/dashboard');
                        exit;
                    }
                    require VIEWS_DIR . '/public/login.php';
                }
                break;
            case '/register':
                if ($method === 'POST') {
                    $this->handleRegisterPost();
                } else {
                    if ($this->auth->isLoggedIn()) {
                        header('Location: ' . BASE_URL . '/dashboard');
                        exit;
                    }
                    require VIEWS_DIR . '/public/register.php';
                }
                break;
            case '/logout':
                $this->auth->logout();
                header('Location: ' . BASE_URL . '/login');
                exit;
            default:
                // Check if dynamic page exists in CMS
                if (str_starts_with($path, '/page/')) {
                    $slug = substr($path, 6);
                    $page = $this->cms->getPageBySlug($slug);
                    if ($page) {
                        require VIEWS_DIR . '/public/page.php';
                        return;
                    }
                }
                http_response_code(404);
                require VIEWS_DIR . '/errors/404.php';
                break;
        }
    }

    private function handleUserRoutes(string $path, string $method): void {
        $userId = (int)$this->auth->getUserId();
        $crm = new CRM();

        if ($path === '/dashboard') {
            require VIEWS_DIR . '/user/dashboard.php';
            return;
        }
        if ($path === '/search') {
            require VIEWS_DIR . '/user/search.php';
            return;
        }
        if ($path === '/leads') {
            require VIEWS_DIR . '/user/leads.php';
            return;
        }
        if ($path === '/lead/new') {
            if ($method === 'POST') {
                $leadId = $crm->createLeadManually($userId, $_POST);
                $_SESSION['flash_success'] = 'Lead created and technical audit completed successfully!';
                header('Location: ' . BASE_URL . '/lead/' . $leadId);
                exit;
            }
            require VIEWS_DIR . '/user/lead_new.php';
            return;
        }
        if (str_starts_with($path, '/lead/')) {
            $leadId = (int)substr($path, 6);
            $lead = $crm->getLead($leadId, $userId);
            if (!$lead) {
                http_response_code(404);
                require VIEWS_DIR . '/errors/404.php';
                return;
            }
            require VIEWS_DIR . '/user/lead_detail.php';
            return;
        }
        if ($path === '/campaigns') {
            require VIEWS_DIR . '/user/campaigns.php';
            return;
        }
        if ($path === '/templates') {
            require VIEWS_DIR . '/user/templates.php';
            return;
        }
        if ($path === '/export') {
            $export = new ExportService();
            $export->exportCsv($userId);
            return;
        }
        if ($path === '/profile') {
            require VIEWS_DIR . '/user/profile.php';
            return;
        }
        if ($path === '/billing') {
            require VIEWS_DIR . '/user/billing.php';
            return;
        }

        http_response_code(404);
        require VIEWS_DIR . '/errors/404.php';
    }

    private function handleAdminRoutes(string $subPath, string $method): void {
        switch ($subPath) {
            case '/':
            case '/dashboard':
                require VIEWS_DIR . '/admin/dashboard.php';
                break;
            case '/users':
                require VIEWS_DIR . '/admin/users.php';
                break;
            case '/plans':
                require VIEWS_DIR . '/admin/plans.php';
                break;
            case '/leads':
                require VIEWS_DIR . '/admin/leads.php';
                break;
            case '/cms':
                require VIEWS_DIR . '/admin/cms.php';
                break;
            case '/menus':
                require VIEWS_DIR . '/admin/menus.php';
                break;
            case '/ads':
                require VIEWS_DIR . '/admin/ads.php';
                break;
            case '/seo':
                require VIEWS_DIR . '/admin/seo.php';
                break;
            case '/settings':
                require VIEWS_DIR . '/admin/settings.php';
                break;
            case '/api-keys':
                require VIEWS_DIR . '/admin/api_keys.php';
                break;
            case '/logs':
                require VIEWS_DIR . '/admin/logs.php';
                break;
            default:
                http_response_code(404);
                require VIEWS_DIR . '/errors/404.php';
                break;
        }
    }

    private function handleApiRoutes(string $path, string $method): void {
        header('Content-Type: application/json; charset=utf-8');

        // Parse JSON input payload if present
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $jsonInput = json_decode(file_get_contents('php://input'), true);
            if (is_array($jsonInput)) {
                $_POST = array_merge($jsonInput, $_POST);
            }
        }

        if ($path === '/api/audit') {
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            $crawler = new Crawler();
            $auditor = new SEOAuditor();

            $crawl = $crawler->crawl($url);
            if (!$crawl['success'] && empty($crawl['signals'])) {
                echo json_encode(['success' => false, 'error' => $crawl['error'] ?? 'Audit failed.']);
                exit;
            }

            $audit = $auditor->audit($crawl['signals'], $url);
            echo json_encode([
                'success' => true,
                'url' => $url,
                'domain' => $crawl['domain'],
                'seo_score' => $audit['overall_score'],
                'scores' => $audit['scores'],
                'issues' => $audit['issues'],
                'signals' => $crawl['signals']
            ]);
            exit;
        }

        if ($path === '/api/search') {
            $this->auth->requireLogin();
            $hunter = new LeadHunter();
            $userId = (int)$this->auth->getUserId();
            $result = $hunter->search($userId, $_POST);
            echo json_encode($result);
            exit;
        }

        if ($path === '/api/ai-pitch') {
            $this->auth->requireLogin();
            $leadId = (int)($_POST['lead_id'] ?? 0);
            $userId = (int)$this->auth->getUserId();
            $crm = new CRM();
            $lead = $crm->getLead($leadId, $userId);

            if (!$lead) {
                echo json_encode(['success' => false, 'error' => 'Lead not found.']);
                exit;
            }

            $ai = new AIService();
            $analysis = $ai->analyzeLead($lead, ['issues' => $lead['issues'] ?? []]);

            echo json_encode(['success' => true, 'data' => $analysis]);
            exit;
        }

        if ($path === '/api/lead/status') {
            $this->auth->requireLogin();
            $leadId = (int)($_POST['lead_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $crm = new CRM();
            $res = $crm->updateStatus($leadId, (int)$this->auth->getUserId(), $status);
            echo json_encode(['success' => $res]);
            exit;
        }

        if ($path === '/api/lead/note') {
            $this->auth->requireLogin();
            $leadId = (int)($_POST['lead_id'] ?? 0);
            $note = trim($_POST['note'] ?? '');
            if (empty($note)) {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty.']);
                exit;
            }
            $crm = new CRM();
            $id = $crm->addNote($leadId, (int)$this->auth->getUserId(), $note);
            echo json_encode(['success' => true, 'note_id' => $id]);
            exit;
        }

        if ($path === '/api/lead/task') {
            $this->auth->requireLogin();
            $userId = (int)$this->auth->getUserId();
            $crm = new CRM();

            if (!empty($_POST['toggle_task_id'])) {
                $res = $crm->toggleTask((int)$_POST['toggle_task_id'], $userId);
                echo json_encode(['success' => $res]);
                exit;
            }

            $leadId = (int)($_POST['lead_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $dueDate = $_POST['due_date'] ?? null;
            $priority = $_POST['priority'] ?? 'Medium';

            $id = $crm->addTask($leadId, $userId, $title, $dueDate, $priority);
            echo json_encode(['success' => true, 'task_id' => $id]);
            exit;
        }

        if ($path === '/api/lead/archive') {
            $this->auth->requireLogin();
            $leadId = (int)($_POST['lead_id'] ?? 0);
            $crm = new CRM();
            $res = $crm->archiveLead($leadId, (int)$this->auth->getUserId());
            echo json_encode(['success' => $res]);
            exit;
        }

        if ($path === '/api/lead/delete') {
            $this->auth->requireLogin();
            $leadId = (int)($_POST['lead_id'] ?? 0);
            $crm = new CRM();
            $res = $crm->deleteLead($leadId, (int)$this->auth->getUserId());
            echo json_encode(['success' => $res]);
            exit;
        }

        echo json_encode(['error' => 'Endpoint not found']);
        exit;
    }

    private function handleLoginPost(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $csrf = $_POST['csrf_token'] ?? '';

        if (!verify_csrf($csrf)) {
            $_SESSION['flash_error'] = 'Security validation failed. Please try again.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $res = $this->auth->login($email, $password);
        if ($res['success']) {
            if ($this->auth->isAdmin()) {
                $settings = $this->cms->getSiteSettings();
                $adminPath = '/' . trim($settings['admin_path'] ?? 'admin', '/');
                header('Location: ' . BASE_URL . $adminPath);
            } else {
                header('Location: ' . BASE_URL . '/dashboard');
            }
            exit;
        }

        $_SESSION['flash_error'] = $res['message'];
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function handleRegisterPost(): void {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $csrf = $_POST['csrf_token'] ?? '';

        if (!verify_csrf($csrf)) {
            $_SESSION['flash_error'] = 'Security validation failed. Please try again.';
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $res = $this->auth->register($name, $email, $password);
        if ($res['success']) {
            $_SESSION['flash_success'] = $res['message'];
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $_SESSION['flash_error'] = $res['message'];
        header('Location: ' . BASE_URL . '/register');
        exit;
    }
}
