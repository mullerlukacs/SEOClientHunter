<?php
/**
 * SEO Client Hunter - CSV Export Engine
 */

namespace App;

use PDO;

class ExportService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function exportCsv(int $userId, array $leadIds = []): void {
        // Enforce export limit
        $stmtPlan = $this->db->prepare("SELECT p.exports_per_month FROM users u JOIN plans p ON u.plan_id = p.id WHERE u.id = ?");
        $stmtPlan->execute([$userId]);
        $maxExports = (int)($stmtPlan->fetchColumn() ?: 20);

        // Check usage in last 30 days
        $thirtyDaysAgo = date('Y-m-d H:i:s', time() - (30 * 86400));
        $stmtUsage = $this->db->prepare("SELECT COUNT(*) FROM usage_logs WHERE user_id = ? AND action_type = 'export' AND created_at >= ?");
        $stmtUsage->execute([$userId, $thirtyDaysAgo]);
        $currentUsage = (int)$stmtUsage->fetchColumn();

        if ($currentUsage >= $maxExports && $maxExports < 9999) {
            header('Content-Type: text/plain');
            echo "Export limit reached for your plan ({$maxExports} exports/month). Please upgrade your subscription.";
            exit;
        }

        // Fetch leads
        if (!empty($leadIds)) {
            $inClause = implode(',', array_fill(0, count($leadIds), '?'));
            $sql = "SELECT l.*, a.overall_score as audited_seo_score FROM leads l LEFT JOIN lead_audits a ON l.id = a.lead_id WHERE l.id IN ($inClause) AND (l.user_id = ? OR l.is_demo = 1)";
            $params = array_merge($leadIds, [$userId]);
        } else {
            $sql = "SELECT l.*, a.overall_score as audited_seo_score FROM leads l LEFT JOIN lead_audits a ON l.id = a.lead_id WHERE (l.user_id = ? OR l.is_demo = 1) AND l.is_archived = 0 ORDER BY l.id DESC";
            $params = [$userId];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $leads = $stmt->fetchAll();

        // Record usage
        $this->db->prepare("INSERT INTO usage_logs (user_id, action_type, count) VALUES (?, 'export', 1)")->execute([$userId]);
        Logger::logActivity($userId, 'Export Generated', "Exported " . count($leads) . " leads to CSV", 'export');

        // Stream CSV headers
        $filename = 'seo_client_hunter_leads_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM for Excel UTF-8 compatibility
        fputs($out, "\xEF\xBB\xBF");

        // 14 Required Columns
        fputcsv($out, [
            'Business Name',
            'Website',
            'Email',
            'Phone',
            'Country',
            'City',
            'Social',
            'SEO Score',
            'Lead Score',
            'SEO Problems',
            'Suggested Service',
            'Status',
            'Notes',
            'Source'
        ]);

        foreach ($leads as $l) {
            // Collect social links
            $socials = [];
            if (!empty($l['facebook']) && $l['facebook'] !== 'Not found') $socials[] = 'FB: ' . $l['facebook'];
            if (!empty($l['instagram']) && $l['instagram'] !== 'Not found') $socials[] = 'IG: ' . $l['instagram'];
            if (!empty($l['linkedin']) && $l['linkedin'] !== 'Not found') $socials[] = 'LI: ' . $l['linkedin'];
            if (!empty($l['google_profile_url']) && $l['google_profile_url'] !== 'Not found') $socials[] = 'Maps: ' . $l['google_profile_url'];
            $socialStr = implode(' | ', $socials);

            // Fetch latest note
            $stmtNote = $this->db->prepare("SELECT content FROM notes WHERE lead_id = ? ORDER BY id DESC LIMIT 1");
            $stmtNote->execute([$l['id']]);
            $latestNote = $stmtNote->fetchColumn() ?: '';

            fputcsv($out, [
                $l['business_name'],
                $l['website'],
                $l['email'] ?: 'Not found',
                $l['phone'] ?: 'Not found',
                $l['country'] ?: 'USA',
                $l['city'] ?: '',
                $socialStr ?: 'None',
                $l['seo_score'] ?? 50,
                $l['lead_score'] ?? 50,
                str_replace("\n", "; ", $l['opportunity_reasons'] ?? 'None specified'),
                'Local SEO & Technical Remediation',
                $l['status'] ?? 'New',
                $latestNote,
                $l['source'] ?? 'Lead Hunter'
            ]);
        }

        fclose($out);
        exit;
    }
}
