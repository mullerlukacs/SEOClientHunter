<?php
/**
 * SEO Client Hunter - CMS, Ad Manager, Menu Manager & Global SEO Service
 */

namespace App;

use PDO;

class CMS {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // --- Pages ---
    public function getPages(): array {
        return $this->db->query("SELECT * FROM pages ORDER BY id ASC")->fetchAll();
    }

    public function getPageBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function getHomepageSections(): array {
        return $this->db->query("SELECT * FROM page_sections WHERE page_id = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
    }

    public function updateHomepageSection(string $key, array $data): bool {
        $stmt = $this->db->prepare("UPDATE page_sections SET title = ?, subtitle = ?, content = ?, cta_text = ?, cta_link = ?, is_enabled = ? WHERE section_key = ? AND page_id = 1");
        return $stmt->execute([
            $data['title'] ?? '',
            $data['subtitle'] ?? '',
            $data['content'] ?? '',
            $data['cta_text'] ?? '',
            $data['cta_link'] ?? '',
            isset($data['is_enabled']) ? 1 : 0,
            $key
        ]);
    }

    public function updatePage(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, canonical_url = ?, is_published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['meta_title'] ?? '',
            $data['meta_description'] ?? '',
            $data['canonical_url'] ?? '',
            isset($data['is_published']) ? 1 : 0,
            $id
        ]);
    }

    // --- Menus ---
    public function getMenuItems(string $location = 'header'): array {
        $stmt = $this->db->prepare("SELECT mi.* FROM menu_items mi JOIN menus m ON mi.menu_id = m.id WHERE m.location = ? AND mi.is_enabled = 1 ORDER BY mi.sort_order ASC, mi.id ASC");
        $stmt->execute([$location]);
        return $stmt->fetchAll();
    }

    public function getAllMenuItems(): array {
        return $this->db->query("SELECT mi.*, m.name as menu_name, m.location FROM menu_items mi JOIN menus m ON mi.menu_id = m.id ORDER BY m.id ASC, mi.sort_order ASC")->fetchAll();
    }

    public function addMenuItem(int $menuId, string $label, string $url, string $target = '_self', int $sortOrder = 0): bool {
        $stmt = $this->db->prepare("INSERT INTO menu_items (menu_id, label, url, target, sort_order, is_enabled) VALUES (?, ?, ?, ?, ?, 1)");
        return $stmt->execute([$menuId, $label, $url, $target, $sortOrder]);
    }

    public function deleteMenuItem(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // --- Ads ---
    public function getActiveAd(string $placement): ?array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT * FROM ads WHERE placement = ? AND is_active = 1 AND (start_date IS NULL OR start_date <= ?) AND (end_date IS NULL OR end_date >= ?) ORDER BY id DESC LIMIT 1");
        $stmt->execute([$placement, $today, $today]);
        $ad = $stmt->fetch();
        if ($ad) {
            // Increment impression count
            $this->db->prepare("UPDATE ads SET impressions = impressions + 1 WHERE id = ?")->execute([$ad['id']]);
        }
        return $ad ?: null;
    }

    public function recordAdClick(int $adId): void {
        $this->db->prepare("UPDATE ads SET clicks = clicks + 1 WHERE id = ?")->execute([$adId]);
    }

    public function getAllAds(): array {
        return $this->db->query("SELECT * FROM ads ORDER BY id DESC")->fetchAll();
    }

    // --- Settings & SEO ---
    public function getSiteSettings(): array {
        $rows = $this->db->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        return $settings;
    }

    public function getSeoSettings(): array {
        $rows = $this->db->query("SELECT setting_key, setting_value FROM seo_settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        return $settings;
    }

    public function updateSiteSettings(array $data): bool {
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($data as $key => $val) {
            $stmt->execute([$key, (string)$val]);
        }
        return true;
    }

    public function updateSeoSettings(array $data): bool {
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO seo_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($data as $key => $val) {
            $stmt->execute([$key, (string)$val]);
        }
        return true;
    }

    // --- Dynamic Sitemap & Robots ---
    public function generateSitemapXml(): string {
        $pages = $this->db->query("SELECT slug, updated_at FROM pages WHERE is_published = 1")->fetchAll();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Home
        $xml .= "  <url>\n    <loc>" . BASE_URL . "/</loc>\n    <changefreq>daily</changefreq>\n    <priority>1.0</priority>\n  </url>\n";
        $xml .= "  <url>\n    <loc>" . BASE_URL . "/features</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
        $xml .= "  <url>\n    <loc>" . BASE_URL . "/pricing</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";

        foreach ($pages as $p) {
            $lastMod = date('Y-m-d', strtotime($p['updated_at'] ?: 'now'));
            $xml .= "  <url>\n    <loc>" . BASE_URL . "/page/" . urlencode($p['slug']) . "</loc>\n    <lastmod>{$lastMod}</lastmod>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }

    public function getRobotsTxt(): string {
        $seo = $this->getSeoSettings();
        if (!empty($seo['robots_txt'])) {
            return $seo['robots_txt'];
        }
        return "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nSitemap: " . BASE_URL . "/sitemap.xml\n";
    }
}
