<?php
/**
 * SEO Client Hunter - CRM Pipeline & Lead Management Engine
 */

namespace App;

use PDO;

class CRM {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDashboardStats(int $userId): array {
        $stmt = $this->db->prepare("SELECT 
            COUNT(*) as total_leads,
            SUM(CASE WHEN status = 'New' OR status = 'new' THEN 1 ELSE 0 END) as new_leads,
            SUM(CASE WHEN status = 'Qualified' OR status = 'qualified' THEN 1 ELSE 0 END) as qualified_leads,
            SUM(CASE WHEN status = 'Contacted' OR status = 'contacted' THEN 1 ELSE 0 END) as contacted_leads,
            SUM(CASE WHEN status = 'Replied' OR status = 'replied' THEN 1 ELSE 0 END) as replied_leads,
            SUM(CASE WHEN status = 'Interested' OR status = 'interested' THEN 1 ELSE 0 END) as interested_leads,
            SUM(CASE WHEN status = 'Proposal' OR status = 'proposal' THEN 1 ELSE 0 END) as proposal_leads,
            SUM(CASE WHEN status = 'Won' OR status = 'won' THEN 1 ELSE 0 END) as won_leads,
            SUM(CASE WHEN status = 'Lost' OR status = 'lost' THEN 1 ELSE 0 END) as lost_leads,
            SUM(CASE WHEN lead_score >= 70 THEN 1 ELSE 0 END) as high_opp_count,
            AVG(lead_score) as avg_opp_score,
            AVG(seo_score) as avg_seo_score
        FROM leads WHERE (user_id = ? OR is_demo = 1) AND is_archived = 0");
        $stmt->execute([$userId]);
        $row = $stmt->fetch() ?: [];

        return [
            'total_leads' => (int)($row['total_leads'] ?? 0),
            'new_leads' => (int)($row['new_leads'] ?? 0),
            'qualified_leads' => (int)($row['qualified_leads'] ?? 0),
            'contacted_leads' => (int)($row['contacted_leads'] ?? 0),
            'replied_leads' => (int)($row['replied_leads'] ?? 0),
            'interested_leads' => (int)($row['interested_leads'] ?? 0),
            'proposal_leads' => (int)($row['proposal_leads'] ?? 0),
            'won_leads' => (int)($row['won_leads'] ?? 0),
            'lost_leads' => (int)($row['lost_leads'] ?? 0),
            'high_opportunity' => (int)($row['high_opp_count'] ?? 0),
            'avg_opportunity' => round((float)($row['avg_opp_score'] ?? 0), 1),
            'avg_seo_score' => round((float)($row['avg_seo_score'] ?? 0), 1)
        ];
    }

    public function normalizeLead(array $lead): array {
        $lead['company_name'] = $lead['business_name'] ?? $lead['company_name'] ?? 'Prospect Business';
        $lead['opportunity_score'] = (int)($lead['lead_score'] ?? $lead['opportunity_score'] ?? 50);
        $lead['niche'] = $lead['category'] ?? $lead['niche'] ?? 'Commercial';
        $lead['contact_page_url'] = $lead['contact_page'] ?? $lead['contact_page_url'] ?? '';
        $lead['created_at'] = $lead['discovered_at'] ?? $lead['created_at'] ?? date('Y-m-d H:i:s');
        $lead['status'] = strtolower($lead['status'] ?? 'new');

        // Extract social profiles
        $lead['social_links_json'] = json_encode([
            'facebook' => $lead['facebook'] ?? null,
            'instagram' => $lead['instagram'] ?? null,
            'linkedin' => $lead['linkedin'] ?? null,
            'twitter' => $lead['twitter'] ?? null,
            'youtube' => $lead['youtube'] ?? null
        ]);

        return $lead;
    }

    public function getLatestAiPitch(int $leadId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM outreach_messages WHERE lead_id = ? AND ai_generated = 1 ORDER BY id DESC LIMIT 1");
        $stmt->execute([$leadId]);
        $row = $stmt->fetch();
        if (!$row) return null;

        if (!empty($row['content'])) {
            $parsed = json_decode($row['content'], true);
            if (is_array($parsed) && isset($parsed['business_summary'])) {
                $row['pitch_data_json'] = $row['content'];
                return $row;
            }
        }

        // Structure from columns
        $row['pitch_data_json'] = json_encode([
            'business_summary' => $row['business_summary'] ?? 'Local service provider with growth opportunity.',
            'seo_problems' => $row['pitch_angle'] ?? 'Technical and local ranking deficiencies.',
            'business_opportunity' => 'Commercial optimization for search visibility.',
            'suggested_service' => $row['suggested_service'] ?? 'Comprehensive SEO & Retainer Optimization',
            'cold_email' => [
                'subject' => $row['subject'] ?? 'Quick question regarding your website visibility',
                'body' => $row['content'] ?? ''
            ],
            'linkedin_message' => [
                'body' => "Hi there,\n\nNoticed a few critical ranking signals on your site holding back local search discovery. Would love to share a 2-minute diagnostic if helpful."
            ],
            'contact_form_pitch' => [
                'body' => "Hello team,\n\nOur agency conducted an automated audit of your domain and identified 3 key conversion bottlenecks. Let us know if you'd like the complete remediation breakdown."
            ],
            'followups' => [
                ['body' => "Following up on my previous note. Did you get a chance to review the audit issues?"],
                ['body' => "Saw your competitor ranking #1 for your target service. We put together a comparison report."],
                ['body' => "Assuming your plate is full, so I will bow out for now. Feel free to reach out anytime."]
            ]
        ]);

        return $row;
    }

    public function getNotes(int $leadId): array {
        $stmt = $this->db->prepare("SELECT id, lead_id, user_id, content as note, created_at FROM notes WHERE lead_id = ? ORDER BY id DESC");
        $stmt->execute([$leadId]);
        return $stmt->fetchAll();
    }

    public function getTasks(int $leadId): array {
        $stmt = $this->db->prepare("SELECT id, lead_id, user_id, title, due_date, priority, status, (CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as is_completed, created_at FROM tasks WHERE lead_id = ? ORDER BY due_date ASC");
        $stmt->execute([$leadId]);
        return $stmt->fetchAll();
    }

    public function getCampaigns(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE user_id = ? OR user_id = 1 ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getTemplates(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM outreach_templates WHERE is_system = 1 OR user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['category'] = $r['type'] ?? 'Cold Email';
        }
        return $rows;
    }

    public function getLeads(int $userId, array $filters = []): array {
        $sql = "SELECT l.*, a.overall_score as audited_seo_score, a.id as audit_id 
                FROM leads l 
                LEFT JOIN lead_audits a ON l.id = a.lead_id 
                WHERE (l.user_id = ? OR l.is_demo = 1)";
        $params = [$userId];

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= " AND l.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['opportunity_level']) && $filters['opportunity_level'] !== 'all') {
            $sql .= " AND l.opportunity_level = ?";
            $params[] = $filters['opportunity_level'];
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND l.category LIKE ?";
            $params[] = '%' . $filters['category'] . '%';
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (l.business_name LIKE ? OR l.domain LIKE ? OR l.city LIKE ? OR l.email LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (isset($filters['archived'])) {
            $sql .= " AND l.is_archived = ?";
            $params[] = (int)$filters['archived'];
        } else {
            $sql .= " AND l.is_archived = 0";
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        $orderSql = match ($sort) {
            'lead_score_desc' => ' ORDER BY l.lead_score DESC',
            'lead_score_asc' => ' ORDER BY l.lead_score ASC',
            'seo_score_asc' => ' ORDER BY l.seo_score ASC',
            'seo_score_desc' => ' ORDER BY l.seo_score DESC',
            'name_asc' => ' ORDER BY l.business_name ASC',
            default => ' ORDER BY l.id DESC'
        };

        $sql .= $orderSql;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return array_map([$this, 'normalizeLead'], $rows);
    }

    public function getLead(int $leadId, int $userId): ?array {
        $stmt = $this->db->prepare("SELECT l.*, a.id as audit_id, a.overall_score as audited_seo_score, a.technical_score, a.onpage_score, a.content_score, a.local_score, a.authority_score, a.social_score, a.raw_data
                                   FROM leads l
                                   LEFT JOIN lead_audits a ON l.id = a.lead_id
                                   WHERE l.id = ? AND (l.user_id = ? OR l.is_demo = 1)");
        $stmt->execute([$leadId, $userId]);
        $lead = $stmt->fetch();
        if (!$lead) return null;

        $lead = $this->normalizeLead($lead);

        // Fetch Audit Issues
        if (!empty($lead['audit_id'])) {
            $stmtIssues = $this->db->prepare("SELECT * FROM audit_issues WHERE audit_id = ? ORDER BY CASE severity WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 WHEN 'Low' THEN 4 ELSE 5 END");
            $stmtIssues->execute([$lead['audit_id']]);
            $lead['issues'] = $stmtIssues->fetchAll();
        } else {
            $lead['issues'] = [];
        }

        // Attach latest_audit structure expected by views
        $lead['latest_audit'] = [
            'id' => $lead['audit_id'] ?? null,
            'overall_score' => $lead['audited_seo_score'] ?? $lead['seo_score'] ?? 50,
            'scores_json' => json_encode([
                'technical' => (int)($lead['technical_score'] ?? 60),
                'onpage' => (int)($lead['onpage_score'] ?? 55),
                'content' => (int)($lead['content_score'] ?? 50),
                'local' => (int)($lead['local_score'] ?? 45),
                'authority' => (int)($lead['authority_score'] ?? 65),
                'social' => (int)($lead['social_score'] ?? 50)
            ]),
            'issues_json' => json_encode($lead['issues'])
        ];

        // Fetch Notes
        $stmtNotes = $this->db->prepare("SELECT * FROM notes WHERE lead_id = ? ORDER BY id DESC");
        $stmtNotes->execute([$leadId]);
        $lead['notes'] = $stmtNotes->fetchAll();

        // Fetch Tasks
        $stmtTasks = $this->db->prepare("SELECT * FROM tasks WHERE lead_id = ? ORDER BY due_date ASC");
        $stmtTasks->execute([$leadId]);
        $lead['tasks'] = $stmtTasks->fetchAll();

        // Fetch Tags
        $stmtTags = $this->db->prepare("SELECT t.* FROM tags t JOIN lead_tags lt ON t.id = lt.tag_id WHERE lt.lead_id = ?");
        $stmtTags->execute([$leadId]);
        $lead['tags'] = $stmtTags->fetchAll();

        // Fetch Outreach Messages
        $stmtMsgs = $this->db->prepare("SELECT * FROM outreach_messages WHERE lead_id = ? ORDER BY id DESC");
        $stmtMsgs->execute([$leadId]);
        $lead['outreach_messages'] = $stmtMsgs->fetchAll();

        return $lead;
    }

    public function updateStatus(int $leadId, int $userId, string $status): bool {
        $valid = ['New', 'Qualified', 'Contacted', 'Replied', 'Interested', 'Proposal', 'Won', 'Lost'];
        if (!in_array($status, $valid, true)) return false;

        $stmt = $this->db->prepare("UPDATE leads SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND (user_id = ? OR is_demo = 1)");
        $res = $stmt->execute([$status, $leadId, $userId]);

        $this->addNote($leadId, $userId, "Lead status updated to: {$status}");
        return $res;
    }

    public function addNote(int $leadId, int $userId, string $content): int {
        $stmt = $this->db->prepare("INSERT INTO notes (lead_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$leadId, $userId, trim($content)]);
        return (int)$this->db->lastInsertId();
    }

    public function addTask(int $leadId, int $userId, string $title, ?string $dueDate = null, string $priority = 'Medium'): int {
        $stmt = $this->db->prepare("INSERT INTO tasks (lead_id, user_id, title, due_date, priority, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$leadId, $userId, trim($title), $dueDate, $priority]);
        return (int)$this->db->lastInsertId();
    }

    public function toggleTask(int $taskId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE tasks SET status = CASE WHEN status = 'completed' THEN 'pending' ELSE 'completed' END WHERE id = ? AND user_id = ?");
        return $stmt->execute([$taskId, $userId]);
    }

    public function archiveLead(int $leadId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE leads SET is_archived = 1 WHERE id = ? AND (user_id = ? OR is_demo = 1)");
        return $stmt->execute([$leadId, $userId]);
    }

    public function deleteLead(int $leadId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM leads WHERE id = ? AND (user_id = ? OR is_demo = 1)");
        return $stmt->execute([$leadId, $userId]);
    }

    public function createLeadManually(int $userId, array $data): int {
        $crawler = new Crawler();
        $auditor = new SEOAuditor();
        $scorer = new LeadScorer();

        $website = trim($data['website'] ?? '');
        $domain = parse_url($website, PHP_URL_HOST) ?: $website;

        // Perform live crawl if URL provided
        $crawl = $crawler->crawl($website);
        $signals = $crawl['signals'] ?? [];
        $audit = $auditor->audit($signals, $website);
        $scoreRes = $scorer->calculate($data, $audit);

        $stmt = $this->db->prepare("INSERT INTO leads (
            user_id, business_name, website, domain, email, phone, address, city, state, country,
            postal_code, category, description, status, seo_score, lead_score, opportunity_level,
            opportunity_reasons, source, is_demo
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?,
            ?, 'Manual Entry', 0
        )");

        $email = $data['email'] ?: ($signals['emails_found'][0] ?? 'Not found');
        $phone = $data['phone'] ?: ($signals['phones_found'][0] ?? 'Not found');

        $stmt->execute([
            $userId,
            $data['business_name'],
            $website,
            $domain,
            $email,
            $phone,
            $data['address'] ?? 'Not found',
            $data['city'] ?? 'Not found',
            $data['state'] ?? '',
            $data['country'] ?? 'USA',
            $data['postal_code'] ?? 'Not found',
            $data['category'] ?? 'General Business',
            $data['description'] ?? '',
            $data['status'] ?? 'New',
            $audit['overall_score'],
            $scoreRes['lead_score'],
            $scoreRes['opportunity_level'],
            implode("\n", $scoreRes['reasons'])
        ]);

        $leadId = (int)$this->db->lastInsertId();

        // Save audit
        $auditStmt = $this->db->prepare("INSERT INTO lead_audits (lead_id, url, technical_score, onpage_score, content_score, local_score, authority_score, social_score, overall_score, raw_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $auditStmt->execute([
            $leadId,
            $website,
            $audit['scores']['technical'],
            $audit['scores']['onpage'],
            $audit['scores']['content'],
            $audit['scores']['local'],
            $audit['scores']['authority'],
            $audit['scores']['social'],
            $audit['overall_score'],
            json_encode($signals)
        ]);
        $auditId = (int)$this->db->lastInsertId();

        // Save issues
        $issueStmt = $this->db->prepare("INSERT INTO audit_issues (audit_id, category, title, severity, explanation, recommendation, affected_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($audit['issues'] as $iss) {
            $issueStmt->execute([
                $auditId,
                $iss['category'],
                $iss['title'],
                $iss['severity'],
                $iss['explanation'],
                $iss['recommendation'],
                $iss['affected_url']
            ]);
        }

        return $leadId;
    }
}
