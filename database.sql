-- =======================================================
-- SEO CLIENT HUNTER - Production MySQL 8+ Database Schema
-- Compatible with MySQL 8.0+, MariaDB 10.5+, and SQLite 3
-- =======================================================

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10,2) DEFAULT 0.00,
    billing_period VARCHAR(20) DEFAULT 'monthly',
    searches_per_month INT DEFAULT 50,
    leads_per_month INT DEFAULT 200,
    audits_per_month INT DEFAULT 50,
    ai_generations_per_month INT DEFAULT 100,
    exports_per_month INT DEFAULT 20,
    campaigns_allowed INT DEFAULT 5,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) DEFAULT 'user',
    status VARCHAR(20) DEFAULT 'active', -- active, suspended, pending
    plan_id INT DEFAULT 1,
    timezone VARCHAR(50) DEFAULT 'UTC',
    language VARCHAR(10) DEFAULT 'en',
    default_country VARCHAR(10) DEFAULT 'US',
    default_niche VARCHAR(100) DEFAULT 'Dentist',
    outreach_signature TEXT NULL,
    ai_preferences TEXT NULL,
    email_verified_at TIMESTAMP NULL,
    reset_token VARCHAR(100) NULL,
    reset_token_expires_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    status VARCHAR(30) DEFAULT 'active',
    starts_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    renews_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sub_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    keyword VARCHAR(150) NOT NULL,
    country VARCHAR(10) NOT NULL,
    city VARCHAR(100) NOT NULL,
    language VARCHAR(10) DEFAULT 'en',
    business_type VARCHAR(100) NULL,
    target_count INT DEFAULT 20,
    min_lead_score INT DEFAULT 0,
    results_found INT DEFAULT 0,
    provider VARCHAR(50) DEFAULT 'demo',
    is_demo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_search_user (user_id),
    INDEX idx_search_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_id INT NULL,
    business_name VARCHAR(200) NOT NULL,
    website VARCHAR(255) NULL,
    domain VARCHAR(150) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(10) NULL,
    postal_code VARCHAR(30) NULL,
    category VARCHAR(100) NULL,
    description TEXT NULL,
    contact_page VARCHAR(255) NULL,
    about_page VARCHAR(255) NULL,
    facebook VARCHAR(255) NULL,
    instagram VARCHAR(255) NULL,
    linkedin VARCHAR(255) NULL,
    twitter VARCHAR(255) NULL,
    youtube VARCHAR(255) NULL,
    google_profile_url VARCHAR(255) NULL,
    source VARCHAR(100) DEFAULT 'crawler',
    seo_score INT DEFAULT 0,
    lead_score INT DEFAULT 0,
    opportunity_level VARCHAR(20) DEFAULT 'Medium', -- High, Medium, Low
    opportunity_reasons TEXT NULL,
    status VARCHAR(30) DEFAULT 'New', -- New, Qualified, Contacted, Replied, Interested, Proposal, Won, Lost
    is_archived TINYINT(1) DEFAULT 0,
    is_demo TINYINT(1) DEFAULT 0,
    discovered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_lead_user (user_id),
    INDEX idx_lead_domain (domain),
    INDEX idx_lead_status (status),
    INDEX idx_lead_score (lead_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    name VARCHAR(100) NULL,
    title VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    linkedin VARCHAR(255) NULL,
    source_url VARCHAR(255) NULL,
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    technical_score INT DEFAULT 0,
    onpage_score INT DEFAULT 0,
    content_score INT DEFAULT 0,
    local_score INT DEFAULT 0,
    authority_score INT DEFAULT 0,
    social_score INT DEFAULT 0,
    overall_score INT DEFAULT 0,
    raw_data LONGTEXT NULL,
    audited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audit_id INT NOT NULL,
    category VARCHAR(50) NOT NULL, -- TECHNICAL SEO, ON-PAGE SEO, CONTENT, LOCAL SEO, LINK/AUTHORITY, SOCIAL/BRAND
    title VARCHAR(200) NOT NULL,
    severity VARCHAR(20) NOT NULL, -- Critical, High, Medium, Low, Passed
    explanation TEXT NOT NULL,
    recommendation TEXT NOT NULL,
    affected_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_issue_audit (audit_id),
    INDEX idx_issue_severity (severity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    niche VARCHAR(100) NULL,
    target_country VARCHAR(10) NULL,
    suggested_service VARCHAR(150) NULL,
    template_id INT NULL,
    followup_schedule VARCHAR(100) DEFAULT '3-days-interval',
    status VARCHAR(30) DEFAULT 'Draft', -- Draft, Active, Paused, Completed
    total_leads INT DEFAULT 0,
    contacted_count INT DEFAULT 0,
    replied_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_campaign_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS campaign_leads (
    campaign_id INT NOT NULL,
    lead_id INT NOT NULL,
    status VARCHAR(30) DEFAULT 'Queued', -- Queued, Sent, Replied, Bounced
    sent_at TIMESTAMP NULL,
    PRIMARY KEY (campaign_id, lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS outreach_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- 0 for system global templates
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) DEFAULT 'Cold Email', -- Cold Email, LinkedIn Message, Contact Form Message, Short Pitch, Follow-up 1, Follow-up 2, Follow-up 3
    subject VARCHAR(200) NULL,
    body TEXT NOT NULL,
    is_system TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS outreach_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lead_id INT NOT NULL,
    campaign_id INT NULL,
    type VARCHAR(50) NOT NULL,
    subject VARCHAR(200) NULL,
    content TEXT NOT NULL,
    recipient_email VARCHAR(150) NULL,
    status VARCHAR(30) DEFAULT 'Draft', -- Draft, Generated, Sent
    ai_generated TINYINT(1) DEFAULT 1,
    pitch_angle VARCHAR(255) NULL,
    suggested_service VARCHAR(255) NULL,
    business_summary TEXT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_outreach_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS followups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lead_id INT NOT NULL,
    step_number INT DEFAULT 1,
    scheduled_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending', -- pending, completed, skipped
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_followup_date (scheduled_date),
    INDEX idx_followup_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notes_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(20) DEFAULT '#0d6efd',
    INDEX idx_tag_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_tags (
    lead_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (lead_id, tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lead_id INT NULL,
    title VARCHAR(200) NOT NULL,
    due_date DATE NULL,
    priority VARCHAR(20) DEFAULT 'Medium', -- High, Medium, Low
    status VARCHAR(20) DEFAULT 'pending', -- pending, completed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tasks_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(30) DEFAULT 'info', -- info, success, warning, danger
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notif_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    meta_title VARCHAR(200) NULL,
    meta_description TEXT NULL,
    canonical_url VARCHAR(255) NULL,
    robots_setting VARCHAR(50) DEFAULT 'index, follow',
    og_title VARCHAR(200) NULL,
    og_description TEXT NULL,
    og_image VARCHAR(255) NULL,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT DEFAULT 1, -- 1 for homepage
    section_key VARCHAR(50) NOT NULL,
    title VARCHAR(200) NULL,
    subtitle VARCHAR(255) NULL,
    content LONGTEXT NULL,
    cta_text VARCHAR(100) NULL,
    cta_link VARCHAR(255) NULL,
    image_url VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_enabled TINYINT(1) DEFAULT 1,
    UNIQUE KEY uq_section (page_id, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(50) NOT NULL UNIQUE, -- header, footer, user, admin
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target VARCHAR(20) DEFAULT '_self',
    sort_order INT DEFAULT 0,
    is_enabled TINYINT(1) DEFAULT 1,
    INDEX idx_menu_items (menu_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    placement VARCHAR(50) NOT NULL, -- homepage_top, homepage_middle, homepage_bottom, sidebar, dashboard, internal_page, footer
    html_code TEXT NULL,
    image_url VARCHAR(255) NULL,
    link_url VARCHAR(255) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_active TINYINT(1) DEFAULT 1,
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ad_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    event_type VARCHAR(20) NOT NULL, -- impression, click
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ad_stats (ad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seo_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(50) NOT NULL UNIQUE, -- ai_provider, search_api, business_data, email_verification, smtp
    api_key TEXT NULL,
    api_secret TEXT NULL,
    endpoint_url VARCHAR(255) NULL,
    model_name VARCHAR(100) NULL,
    extra_config LONGTEXT NULL,
    is_active TINYINT(1) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usage_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL, -- search, lead_discovered, audit_run, ai_pitch, export
    count INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usage_user (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    type VARCHAR(30) DEFAULT 'user', -- user, admin, system, login, search, audit, api, crawler
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_type (type),
    INDEX idx_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(20) DEFAULT 'error', -- error, warning, info
    message TEXT NOT NULL,
    context LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sys_level (level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================
-- INITIAL SEED DATA
-- =======================================================

INSERT INTO roles (id, name, description) VALUES 
(1, 'Super Admin', 'Full system access and master configuration'),
(2, 'Admin', 'Staff access to manage users, leads, and CMS'),
(3, 'User', 'Standard SaaS customer access')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO plans (id, name, slug, price, billing_period, searches_per_month, leads_per_month, audits_per_month, ai_generations_per_month, exports_per_month, campaigns_allowed, is_active) VALUES
(1, 'Starter', 'starter', 0.00, 'monthly', 25, 100, 25, 50, 10, 3, 1),
(2, 'Pro Agency', 'pro', 79.00, 'monthly', 200, 1500, 250, 500, 100, 25, 1),
(3, 'Enterprise Scale', 'enterprise', 199.00, 'monthly', 1000, 10000, 1500, 3000, 500, 100, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'SEO Client Hunter'),
('tagline', 'Automated B2B SEO Prospect Discovery, Deep Audits & AI Outreach CRM'),
('site_url', 'http://localhost:3000'),
('admin_path', 'admin'),
('demo_mode', '1'),
('primary_color', '#2563eb'),
('secondary_color', '#1e293b'),
('button_radius', '8px'),
('sidebar_theme', 'dark'),
('timezone', 'UTC'),
('allow_registration', '1'),
('require_email_verification', '0'),
('contact_email', 'contact@seoclienthunter.com'),
('announcement_bar', '⚡ Version 2.4 Live: High-Accuracy SEO Opportunity Lead Scorer & Instant Personalized AI Pitch Generator!'),
('announcement_enabled', '1'),
('header_code', ''),
('body_code', ''),
('footer_code', ''),
('footer_description', 'SEO Client Hunter is the all-in-one lead generation engine for SEO agencies, consultants, and growth teams to find high-value local businesses with severe website weaknesses and convert them with data-backed pitch proposals.')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO seo_settings (setting_key, setting_value) VALUES
('site_title', 'SEO Client Hunter - Find High-Paying SEO Clients in Seconds'),
('meta_description', 'Discover local businesses with critical SEO flaws, perform automated technical audits, calculate lead opportunity scores, and generate hyper-personalized pitch emails.'),
('keywords', 'SEO lead generation, B2B agency leads, automated SEO audit, local SEO prospects, cold email outreach, client acquisition CRM'),
('canonical_url', 'http://localhost:3000'),
('robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nSitemap: http://localhost:3000/sitemap.xml"),
('og_title', 'SEO Client Hunter - B2B SEO Client Acquisition Software'),
('og_description', 'Automated website crawler, deep 6-tier SEO auditing, and personalized cold outreach generator for agencies.'),
('twitter_card', 'summary_large_image'),
('google_search_console', ''),
('google_analytics_id', ''),
('schema_json_ld', '{\n  "@context": "https://schema.org",\n  "@type": "SoftwareApplication",\n  "name": "SEO Client Hunter",\n  "operatingSystem": "Web",\n  "applicationCategory": "BusinessApplication",\n  "description": "B2B SEO prospect discovery and automated audit software for agencies."\n}')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO api_keys (provider, api_key, api_secret, endpoint_url, model_name, is_active) VALUES
('ai_provider', '', '', 'https://generativelanguage.googleapis.com/v1beta', 'gemini-2.5-flash', 1),
('search_api', '', '', 'https://customsearch.googleapis.com/v1', '', 0),
('business_data', '', '', 'https://api.places.google.com', '', 0),
('email_verification', '', '', 'https://api.hunter.io/v2', '', 0),
('smtp', '', '', 'smtp.example.com', '587', 0)
ON DUPLICATE KEY UPDATE provider=VALUES(provider);

INSERT INTO menus (id, location, name) VALUES
(1, 'header', 'Main Header Navigation'),
(2, 'footer', 'Footer Useful Links'),
(3, 'user', 'User Dashboard Menu'),
(4, 'admin', 'Admin Sidebar Navigation')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO menu_items (menu_id, label, url, target, sort_order, is_enabled) VALUES
(1, 'Home', '/', '_self', 1, 1),
(1, 'Features', '/features', '_self', 2, 1),
(1, 'How It Works', '/#how-it-works', '_self', 3, 1),
(1, 'Pricing', '/pricing', '_self', 4, 1),
(1, 'About', '/about', '_self', 5, 1),
(1, 'Contact', '/contact', '_self', 6, 1),
(2, 'Privacy Policy', '/privacy', '_self', 1, 1),
(2, 'Terms of Service', '/terms', '_self', 2, 1),
(2, 'Features Overview', '/features', '_self', 3, 1),
(2, 'Agency Pricing', '/pricing', '_self', 4, 1),
(2, 'Support & FAQ', '/#faq', '_self', 5, 1)
ON DUPLICATE KEY UPDATE label=VALUES(label);
