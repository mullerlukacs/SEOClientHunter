<?php
/**
 * SEO Client Hunter - Database Connector
 * Robust PDO Singleton with dual MySQL 8+ & SQLite support
 */

namespace App;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private static string $driver = 'mysql';

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::connect();
        }
        return self::$instance;
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    private static function connect(): void {
        $dbType = DB_TYPE;
        $connected = false;

        // Try MySQL if DB_TYPE is mysql or auto
        if ($dbType === 'mysql' || $dbType === 'auto') {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_PORT,
                    DB_NAME,
                    DB_CHARSET
                );
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 2,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                self::$driver = 'mysql';
                $connected = true;
            } catch (PDOException $e) {
                if ($dbType === 'mysql') {
                    // Explicitly requested MySQL but failed
                    throw new PDOException("MySQL Connection Error: " . $e->getMessage());
                }
                // If auto, we will gracefully fallback to SQLite
            }
        }

        // Fallback or explicit SQLite
        if (!$connected) {
            if (!is_dir(DATABASE_DIR)) {
                mkdir(DATABASE_DIR, 0777, true);
            }
            $sqliteFile = SQLITE_PATH;
            $isNew = !file_exists($sqliteFile) || filesize($sqliteFile) === 0;

            $dsn = 'sqlite:' . $sqliteFile;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            self::$instance = new PDO($dsn, null, null, $options);
            self::$driver = 'sqlite';

            // SQLite performance and FK pragmas
            self::$instance->exec('PRAGMA foreign_keys = ON;');
            self::$instance->exec('PRAGMA journal_mode = WAL;');

            if ($isNew) {
                self::bootstrapSqliteSchema(self::$instance);
            }
        }
    }

    private static function bootstrapSqliteSchema(PDO $pdo): void {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS plans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            slug TEXT NOT NULL UNIQUE,
            price REAL DEFAULT 0.00,
            billing_period TEXT DEFAULT 'monthly',
            searches_per_month INTEGER DEFAULT 50,
            leads_per_month INTEGER DEFAULT 200,
            audits_per_month INTEGER DEFAULT 50,
            ai_generations_per_month INTEGER DEFAULT 100,
            exports_per_month INTEGER DEFAULT 20,
            campaigns_allowed INTEGER DEFAULT 5,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            status TEXT DEFAULT 'active',
            plan_id INTEGER DEFAULT 1,
            timezone TEXT DEFAULT 'UTC',
            language TEXT DEFAULT 'en',
            default_country TEXT DEFAULT 'US',
            default_niche TEXT DEFAULT 'Dentist',
            outreach_signature TEXT,
            ai_preferences TEXT,
            email_verified_at DATETIME,
            reset_token TEXT,
            reset_token_expires_at DATETIME,
            last_login_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS searches (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            keyword TEXT NOT NULL,
            country TEXT NOT NULL,
            city TEXT NOT NULL,
            language TEXT DEFAULT 'en',
            business_type TEXT,
            target_count INTEGER DEFAULT 20,
            min_lead_score INTEGER DEFAULT 0,
            results_found INTEGER DEFAULT 0,
            provider TEXT DEFAULT 'demo',
            is_demo INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            search_id INTEGER,
            business_name TEXT NOT NULL,
            website TEXT,
            domain TEXT,
            email TEXT,
            phone TEXT,
            address TEXT,
            city TEXT,
            state TEXT,
            country TEXT,
            postal_code TEXT,
            category TEXT,
            description TEXT,
            contact_page TEXT,
            about_page TEXT,
            facebook TEXT,
            instagram TEXT,
            linkedin TEXT,
            twitter TEXT,
            youtube TEXT,
            google_profile_url TEXT,
            source TEXT DEFAULT 'crawler',
            seo_score INTEGER DEFAULT 0,
            lead_score INTEGER DEFAULT 0,
            opportunity_level TEXT DEFAULT 'Medium',
            opportunity_reasons TEXT,
            status TEXT DEFAULT 'New',
            is_archived INTEGER DEFAULT 0,
            is_demo INTEGER DEFAULT 0,
            discovered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS lead_contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            name TEXT,
            title TEXT,
            email TEXT,
            phone TEXT,
            linkedin TEXT,
            source_url TEXT,
            is_verified INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS lead_audits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            url TEXT NOT NULL,
            technical_score INTEGER DEFAULT 0,
            onpage_score INTEGER DEFAULT 0,
            content_score INTEGER DEFAULT 0,
            local_score INTEGER DEFAULT 0,
            authority_score INTEGER DEFAULT 0,
            social_score INTEGER DEFAULT 0,
            overall_score INTEGER DEFAULT 0,
            raw_data TEXT,
            audited_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS audit_issues (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            audit_id INTEGER NOT NULL,
            category TEXT NOT NULL,
            title TEXT NOT NULL,
            severity TEXT NOT NULL,
            explanation TEXT NOT NULL,
            recommendation TEXT NOT NULL,
            affected_url TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS campaigns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            niche TEXT,
            target_country TEXT,
            suggested_service TEXT,
            template_id INTEGER,
            followup_schedule TEXT DEFAULT '3-days-interval',
            status TEXT DEFAULT 'Draft',
            total_leads INTEGER DEFAULT 0,
            contacted_count INTEGER DEFAULT 0,
            replied_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS campaign_leads (
            campaign_id INTEGER NOT NULL,
            lead_id INTEGER NOT NULL,
            status TEXT DEFAULT 'Queued',
            sent_at DATETIME,
            PRIMARY KEY (campaign_id, lead_id)
        );

        CREATE TABLE IF NOT EXISTS outreach_templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            type TEXT DEFAULT 'Cold Email',
            subject TEXT,
            body TEXT NOT NULL,
            is_system INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS outreach_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            lead_id INTEGER NOT NULL,
            campaign_id INTEGER,
            type TEXT NOT NULL,
            subject TEXT,
            content TEXT NOT NULL,
            recipient_email TEXT,
            status TEXT DEFAULT 'Draft',
            ai_generated INTEGER DEFAULT 1,
            pitch_angle TEXT,
            suggested_service TEXT,
            business_summary TEXT,
            sent_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS followups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            lead_id INTEGER NOT NULL,
            step_number INTEGER DEFAULT 1,
            scheduled_date TEXT NOT NULL,
            status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            color TEXT DEFAULT '#0d6efd'
        );

        CREATE TABLE IF NOT EXISTS lead_tags (
            lead_id INTEGER NOT NULL,
            tag_id INTEGER NOT NULL,
            PRIMARY KEY (lead_id, tag_id)
        );

        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            lead_id INTEGER,
            title TEXT NOT NULL,
            due_date TEXT,
            priority TEXT DEFAULT 'Medium',
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            message TEXT NOT NULL,
            type TEXT DEFAULT 'info',
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            content TEXT NOT NULL,
            featured_image TEXT,
            meta_title TEXT,
            meta_description TEXT,
            canonical_url TEXT,
            robots_setting TEXT DEFAULT 'index, follow',
            og_title TEXT,
            og_description TEXT,
            og_image TEXT,
            is_published INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS page_sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page_id INTEGER DEFAULT 1,
            section_key TEXT NOT NULL,
            title TEXT,
            subtitle TEXT,
            content TEXT,
            cta_text TEXT,
            cta_link TEXT,
            image_url TEXT,
            sort_order INTEGER DEFAULT 0,
            is_enabled INTEGER DEFAULT 1,
            UNIQUE(page_id, section_key)
        );

        CREATE TABLE IF NOT EXISTS menus (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            location TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS menu_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            menu_id INTEGER NOT NULL,
            label TEXT NOT NULL,
            url TEXT NOT NULL,
            target TEXT DEFAULT '_self',
            sort_order INTEGER DEFAULT 0,
            is_enabled INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS ads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            placement TEXT NOT NULL,
            html_code TEXT,
            image_url TEXT,
            link_url TEXT,
            start_date TEXT,
            end_date TEXT,
            is_active INTEGER DEFAULT 1,
            impressions INTEGER DEFAULT 0,
            clicks INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS ad_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ad_id INTEGER NOT NULL,
            event_type TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS seo_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT
        );

        CREATE TABLE IF NOT EXISTS api_keys (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            provider TEXT NOT NULL UNIQUE,
            api_key TEXT,
            api_secret TEXT,
            endpoint_url TEXT,
            model_name TEXT,
            extra_config TEXT,
            is_active INTEGER DEFAULT 0,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS usage_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            action_type TEXT NOT NULL,
            count INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS activity_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            action TEXT NOT NULL,
            description TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            type TEXT DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS system_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            level TEXT DEFAULT 'error',
            message TEXT NOT NULL,
            context TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
SQL;
        $pdo->exec($sql);

        // Seed initial roles and plans
        $pdo->exec("INSERT OR IGNORE INTO roles (id, name, description) VALUES 
            (1, 'Super Admin', 'Full system access and master configuration'),
            (2, 'Admin', 'Staff access to manage users, leads, and CMS'),
            (3, 'User', 'Standard SaaS customer access')");

        $pdo->exec("INSERT OR IGNORE INTO plans (id, name, slug, price, billing_period, searches_per_month, leads_per_month, audits_per_month, ai_generations_per_month, exports_per_month, campaigns_allowed, is_active) VALUES
            (1, 'Starter', 'starter', 0.00, 'monthly', 25, 100, 25, 50, 10, 3, 1),
            (2, 'Pro Agency', 'pro', 79.00, 'monthly', 200, 1500, 250, 500, 100, 25, 1),
            (3, 'Enterprise Scale', 'enterprise', 199.00, 'monthly', 1000, 10000, 1500, 3000, 500, 100, 1)");

        // Default admin user: admin@seoclienthunter.com / Admin123!
        $adminPass = password_hash('Admin123!', PASSWORD_BCRYPT);
        $pdo->exec("INSERT OR IGNORE INTO users (id, name, email, password_hash, role, status, plan_id) VALUES 
            (1, 'System Administrator', 'admin@seoclienthunter.com', '{$adminPass}', 'Super Admin', 'active', 3)");

        // Default demo user: demo@seoclienthunter.com / Demo123!
        $demoPass = password_hash('Demo123!', PASSWORD_BCRYPT);
        $pdo->exec("INSERT OR IGNORE INTO users (id, name, email, password_hash, role, status, plan_id) VALUES 
            (2, 'Alexander Wright (Agency Owner)', 'demo@seoclienthunter.com', '{$demoPass}', 'user', 'active', 2)");

        // Site Settings
        $pdo->exec("INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES
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
            ('announcement_bar', '⚡ High-Opportunity SEO Lead Scorer & AI Outreach Pitch Generator Live!'),
            ('announcement_enabled', '1'),
            ('header_code', ''),
            ('body_code', ''),
            ('footer_code', ''),
            ('footer_description', 'SEO Client Hunter is the all-in-one prospect intelligence engine for SEO agencies, consultants, and growth teams to uncover businesses with severe website weaknesses and convert them with data-backed pitch proposals.')");

        // SEO Settings
        $pdo->exec("INSERT OR IGNORE INTO seo_settings (setting_key, setting_value) VALUES
            ('site_title', 'SEO Client Hunter - Find High-Paying SEO Clients in Seconds'),
            ('meta_description', 'Discover local businesses with critical SEO flaws, perform automated technical audits, calculate lead opportunity scores, and generate hyper-personalized pitch emails.'),
            ('keywords', 'SEO lead generation, B2B agency leads, automated SEO audit, local SEO prospects, cold email outreach, client acquisition CRM'),
            ('canonical_url', 'http://localhost:3000'),
            ('robots_txt', 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nSitemap: http://localhost:3000/sitemap.xml'),
            ('og_title', 'SEO Client Hunter - B2B SEO Client Acquisition Software'),
            ('og_description', 'Automated website crawler, deep 6-tier SEO auditing, and personalized cold outreach generator for agencies.'),
            ('twitter_card', 'summary_large_image'),
            ('google_search_console', ''),
            ('google_analytics_id', ''),
            ('schema_json_ld', '{\"@context\":\"https://schema.org\",\"@type\":\"SoftwareApplication\",\"name\":\"SEO Client Hunter\"}')");

        // API keys default structure
        $pdo->exec("INSERT OR IGNORE INTO api_keys (provider, api_key, api_secret, endpoint_url, model_name, is_active) VALUES
            ('ai_provider', '', '', 'https://generativelanguage.googleapis.com/v1beta', 'gemini-2.5-flash', 1),
            ('search_api', '', '', 'https://customsearch.googleapis.com/v1', '', 0),
            ('business_data', '', '', 'https://api.places.google.com', '', 0),
            ('email_verification', '', '', 'https://api.hunter.io/v2', '', 0),
            ('smtp', '', '', 'smtp.example.com', '587', 0)");

        // Menus
        $pdo->exec("INSERT OR IGNORE INTO menus (id, location, name) VALUES
            (1, 'header', 'Main Header Navigation'),
            (2, 'footer', 'Footer Useful Links'),
            (3, 'user', 'User Dashboard Menu'),
            (4, 'admin', 'Admin Sidebar Navigation')");

        $pdo->exec("INSERT OR IGNORE INTO menu_items (menu_id, label, url, target, sort_order, is_enabled) VALUES
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
            (2, 'Support & FAQ', '/#faq', '_self', 5, 1)");

        // Seed initial Outreach Templates
        $pdo->exec("INSERT OR IGNORE INTO outreach_templates (id, user_id, name, type, subject, body, is_system) VALUES
            (1, 0, 'Technical SEO Defect Warning', 'Cold Email', 'Quick question regarding technical issues on {website}', 'Hi {contact_name},\n\nI was reviewing dental clinics in {city} and noticed a few critical technical issues on {website} that are likely preventing your practice from ranking on page 1 of Google for local searches.\n\nSpecifically: {seo_problems}.\n\nWe specialize in {suggested_service} for medical and dental practices. Would you be open to a 5-minute video breakdown of how to fix these before your competitors capture those patients?\n\nBest regards,\n{my_name}\n{my_company}', 1),
            (2, 0, 'LinkedIn Executive Pitch', 'LinkedIn Message', 'SEO & Local Patient Visibility for {business_name}', 'Hi {contact_name}, noticed your practice {business_name} in {city}. Great reputation, but your mobile page speed and local schema signals are currently suppressing your Google Maps visibility. We recently helped a nearby practice increase patient bookings by 64% with {suggested_service}. Worth a quick 3-minute chat?', 1),
            (3, 0, 'Short Direct Pitch', 'Short Pitch', 'Quick observation for {business_name}', 'Hey {contact_name}, checked out {website} today. You currently have {seo_problems}. This is directly hurting your organic search traffic in {city}. Let me know if you would like me to send over our free 1-page action plan to fix it.', 1),
            (4, 0, 'Gentle Follow-Up #1', 'Follow-up 1', 'Following up regarding {website} search rankings', 'Hi {contact_name},\n\nWanted to quickly follow up on my note regarding the search ranking issues on {website}.\n\nSince local patients in {city} search for treatments daily, fixing {seo_problems} would immediately boost your phone inquiries. Here is a quick link to review our case studies.\n\nBest,\n{my_name}', 1)");

        // Seed Homepage CMS Sections
        $sections = [
            ['hero', 'Uncover High-Value Local Clients With Critical SEO Weaknesses', 'Find, Audit, Score & Convert Local Businesses In Minutes', 'The all-in-one B2B lead generation engine for SEO agencies. Discover businesses with severe on-page & technical defects, calculate commercial opportunity scores, and generate hyper-personalized pitch proposals that close deals.', 'Find High-Intent Clients', '/register', ''],
            ['how_it_works', 'How SEO Client Hunter Converts Raw Prospects Into Retainers', 'A 3-step automated client acquisition pipeline', 'Our automated multi-tier engine searches public directories, crawls business domains safely, audits 40+ ranking factors, and crafts personalized client pitches tailored to their exact deficiencies.', 'Start Free Trial', '/register', ''],
            ['features', 'Engineered Specifically For High-Ticket SEO Agencies', 'Everything you need to scale client acquisition', 'From high-speed search discovery to deep technical crawlers, automated lead scoring, and integrated CRM pipeline tracking.', 'Explore All Features', '/features', ''],
            ['seo_audit', 'Deep 6-Tier Website Technical Audit', 'Comprehensive crawl analyzing over 40+ ranking factors', 'Examine technical infrastructure, on-page optimization, content depth, local citation signals, authority factors, and social brand presence with severity badges.', 'Run Instant Audit', '/seo-audit', ''],
            ['lead_scoring', '0-100 Lead Opportunity Score Algorithm', 'Focus your agency sales energy on high-probability deals', 'Our scoring engine pinpoints companies with active budgets but failing websites—the ultimate sweet spot for high-ticket retainer sales.', 'View Scoring Criteria', '/#features', ''],
            ['contact_discovery', 'Public Business Contact & Social Profiling', 'Direct communication channels gathered from accessible web pages', 'Safely identifies public business emails, phone numbers, contact pages, Google Business Profiles, and official social media accounts without fabrications.', 'Try Lead Hunter', '/search', ''],
            ['ai_outreach', 'AI-Driven Personalized Cold Outreach Engine', 'Reference actual discovered flaws—zero generic spam', 'Generate tailored Cold Emails, LinkedIn Messages, Contact Form pitches, and sequential follow-ups citing the prospect\'s actual technical bottlenecks.', 'Generate Pitches', '/outreach', ''],
            ['crm', 'Integrated Lead Pipeline & Deal CRM', 'Track prospect progression from Discovery to Closed Won', 'Manage stages, schedule follow-ups, attach client notes, add tags, and coordinate outreach campaigns directly inside your dashboard.', 'Access CRM', '/leads', ''],
            ['pricing', 'Transparent Pricing Designed To Scale With Your Agency', 'Choose the plan that matches your monthly client goals', 'No surprise fees, unlimited lead archiving, full CSV export rights, and cancel anytime flexibility.', 'Get Started Today', '/pricing', ''],
            ['faq', 'Frequently Asked Questions', 'Clear answers about crawler limits, data accuracy, and integrations', 'Everything you need to know about our data sources, search provider integrations, crawler safety, and agency workflows.', 'Contact Support', '/contact', ''],
            ['cta', 'Ready To Fill Your Agency Pipeline With Qualified Clients?', 'Stop sending generic cold emails that get ignored', 'Join hundreds of SEO agencies and consultants closing high-ticket retainers with data-backed technical audit pitches.', 'Start Hunting Clients Today', '/register', '']
        ];

        foreach ($sections as $s) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO page_sections (page_id, section_key, title, subtitle, content, cta_text, cta_link, image_url, sort_order, is_enabled) VALUES (1, ?, ?, ?, ?, ?, ?, ?, 0, 1)");
            $stmt->execute($s);
        }

        // Seed Sample CMS Pages
        $pages = [
            ['About Us', 'about', '<h1>About SEO Client Hunter</h1><p class="lead">We built SEO Client Hunter to solve the single greatest challenge facing digital agencies: finding qualified, high-intent local businesses that genuinely need SEO services and have the budget to pay for them.</p><p>Traditional cold prospecting is broken. Sending generic pitch emails to random email addresses yields dismal 0.5% response rates. Modern businesses expect agencies to demonstrate immediate, verifiable value from the very first interaction.</p><h3>Our Mission</h3><p>To empower growth agencies, independent SEO consultants, and marketing professionals with enterprise-grade prospect discovery, automated deep-crawl audits, and hyper-personalized pitch generators that turn cold prospects into eager retainer clients.</p>', 'About SEO Client Hunter - Agency Growth Platform', 'Learn about SEO Client Hunter, our mission, and our enterprise prospect discovery engine.'],
            ['Features Overview', 'features', '<h1>Platform Features & Architecture</h1><p class="lead">Explore the suite of tools that make SEO Client Hunter the indispensable client acquisition software for agencies.</p><div class="row g-4 my-4"><div class="col-md-6"><h4>1. Multi-Provider Lead Discovery</h4><p>Search by niche, country, city, and target prospect volume. Configurable provider architecture supporting demo data, custom search APIs, and live business directory sources.</p></div><div class="col-md-6"><h4>2. Deep 6-Tier Technical Crawler</h4><p>Analyzes title, meta descriptions, canonicals, H1/H2 structure, schema JSON-LD, Open Graph, mobile viewport, image alt attributes, robots.txt, and broken links.</p></div><div class="col-md-6"><h4>3. 0-100 Lead Opportunity Scoring</h4><p>Identifies prospects with commercial intent but noticeable technical SEO defects—the highest-converting client profile.</p></div><div class="col-md-6"><h4>4. AI Pitch Generator</h4><p>Produces personalized cold emails, LinkedIn inMails, contact form submissions, and 3-stage follow-up sequences citing real detected issues.</p></div></div>', 'Features - SEO Client Hunter', 'Comprehensive list of features including crawler, 6-tier audit, opportunity scoring, and outreach generator.'],
            ['Agency Pricing', 'pricing', '<h1>Simple, Predictable Agency Plans</h1><p class="lead">All plans include our complete 6-tier audit engine, lead scoring, and CSV export capabilities.</p>', 'Pricing - SEO Client Hunter', 'Choose the best plan for your agency growth.'],
            ['Contact & Support', 'contact', '<h1>Contact Our Team</h1><p class="lead">Have questions about SEO Client Hunter or need help configuring your search API keys? Our agency support team is available 24/7.</p><p>Email: <strong>support@seoclienthunter.com</strong><br>Phone: <strong>+1 (800) 555-SEOHUNT</strong></p>', 'Contact Support - SEO Client Hunter', 'Get in touch with the SEO Client Hunter support and customer success team.'],
            ['Privacy Policy', 'privacy', '<h1>Privacy Policy</h1><p>Last updated: September 2026</p><p>At SEO Client Hunter, we respect your privacy and are committed to protecting the personal data of our users. This privacy policy explains our practices regarding the collection, use, and disclosure of your information.</p><h3>Data Collection & Processing</h3><p>We collect information you provide directly to us when creating an account, running searches, or saving leads. Public business data collected through the crawler is sourced exclusively from publicly accessible web pages and publicly published business profiles.</p>', 'Privacy Policy - SEO Client Hunter', 'Our commitment to data privacy, GDPR compliance, and transparent practices.'],
            ['Terms of Service', 'terms', '<h1>Terms of Service</h1><p>Last updated: September 2026</p><p>By accessing or using SEO Client Hunter, you agree to be bound by these Terms of Service. If you do not agree, please do not use our service.</p><h3>Permitted Use</h3><p>You agree to use our platform in full compliance with all applicable laws and regulations, including anti-spam laws (CAN-SPAM, GDPR, CASL) when conducting email or message outreach. Automated crawlers must respect website terms and server load limits.</p>', 'Terms of Service - SEO Client Hunter', 'Terms and conditions governing the use of SEO Client Hunter software.']
        ];

        foreach ($pages as $p) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO pages (title, slug, content, meta_title, meta_description, is_published) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute($p);
        }

        // Seed 10 Realistic Demo Leads for User #2 & Demo Mode
        $demoLeads = [
            [
                'user_id' => 2,
                'business_name' => 'Metro Smiles Dental Group',
                'website' => 'https://metrosmilesdentalgroup.com',
                'domain' => 'metrosmilesdentalgroup.com',
                'email' => 'info@metrosmilesdentalgroup.com',
                'phone' => '+1 (212) 555-0182',
                'address' => '452 Lexington Ave, 4th Floor',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10017',
                'category' => 'Dentist',
                'description' => 'Comprehensive family and cosmetic dentistry practice located in Midtown Manhattan offering Invisalign, dental implants, and teeth whitening.',
                'contact_page' => 'https://metrosmilesdentalgroup.com/contact-us',
                'about_page' => 'https://metrosmilesdentalgroup.com/about-our-practice',
                'facebook' => 'https://facebook.com/metrosmilesdental',
                'instagram' => 'https://instagram.com/metrosmilesdental',
                'linkedin' => 'https://linkedin.com/company/metrosmilesdental',
                'twitter' => '',
                'youtube' => '',
                'google_profile_url' => 'https://maps.google.com/?cid=1092837482910',
                'source' => 'Demo Provider',
                'seo_score' => 48,
                'lead_score' => 88,
                'opportunity_level' => 'High',
                'opportunity_reasons' => "Critical: Missing LocalBusiness JSON-LD Schema\nHigh: 14 images missing alt tags\nHigh: Mobile LCP takes 4.8 seconds\nMedium: Missing H1 tag on homepage",
                'status' => 'New',
                'is_demo' => 1
            ],
            [
                'user_id' => 2,
                'business_name' => 'Empire State Orthodontics',
                'website' => 'https://empirestateorthonyc.com',
                'domain' => 'empirestateorthonyc.com',
                'email' => 'contact@empirestateorthonyc.com',
                'phone' => '+1 (212) 555-0391',
                'address' => '350 5th Ave, Suite 1200',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10118',
                'category' => 'Orthodontist',
                'description' => 'Specialized orthodontics clinic providing adult braces, clear aligners, and early orthodontic treatment in Manhattan.',
                'contact_page' => 'https://empirestateorthonyc.com/contact',
                'about_page' => 'https://empirestateorthonyc.com/meet-the-team',
                'facebook' => 'https://facebook.com/empireorthonyc',
                'instagram' => 'https://instagram.com/empireorthonyc',
                'linkedin' => '',
                'twitter' => '',
                'youtube' => '',
                'google_profile_url' => 'https://maps.google.com/?cid=837194829102',
                'source' => 'Demo Provider',
                'seo_score' => 54,
                'lead_score' => 82,
                'opportunity_level' => 'High',
                'opportunity_reasons' => "Critical: Meta description missing on 6 main service pages\nHigh: Non-responsive viewport on treatment booking flow\nMedium: Duplicate H1 tags on homepage",
                'status' => 'Qualified',
                'is_demo' => 1
            ],
            [
                'user_id' => 2,
                'business_name' => 'Tribeca Family Dental Care',
                'website' => 'https://tribecafamilydental.net',
                'domain' => 'tribecafamilydental.net',
                'email' => 'hello@tribecafamilydental.net',
                'phone' => '+1 (212) 555-0744',
                'address' => '88 Hudson St',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10013',
                'category' => 'Dentist',
                'description' => 'Gentle pediatric and adult dental practice serving Tribeca and lower Manhattan for over 15 years.',
                'contact_page' => 'https://tribecafamilydental.net/get-in-touch',
                'about_page' => 'https://tribecafamilydental.net/dr-smith',
                'facebook' => '',
                'instagram' => 'https://instagram.com/tribecadental',
                'linkedin' => '',
                'twitter' => '',
                'youtube' => '',
                'google_profile_url' => 'https://maps.google.com/?cid=918273645019',
                'source' => 'Demo Provider',
                'seo_score' => 61,
                'lead_score' => 76,
                'opportunity_level' => 'Medium',
                'opportunity_reasons' => "High: Thin content under 250 words on Dental Implants page\nMedium: Open Graph meta tags incomplete\nLow: Missing sitemap.xml link in robots.txt",
                'status' => 'Contacted',
                'is_demo' => 1
            ],
            [
                'user_id' => 2,
                'business_name' => 'Chelsea Aesthetic Dentistry',
                'website' => 'https://chelseaaestheticdentistry.com',
                'domain' => 'chelseaaestheticdentistry.com',
                'email' => 'appointments@chelseaaestheticdentistry.com',
                'phone' => '+1 (212) 555-0912',
                'address' => '210 W 23rd St',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10011',
                'category' => 'Cosmetic Dentist',
                'description' => 'Luxury boutique cosmetic dental studio specializing in porcelain veneers, smile makeovers, and full-mouth rehabilitation.',
                'contact_page' => 'https://chelseaaestheticdentistry.com/contact',
                'about_page' => 'https://chelseaaestheticdentistry.com/about',
                'facebook' => 'https://facebook.com/chelseadentistry',
                'instagram' => 'https://instagram.com/chelseasmiles',
                'linkedin' => 'https://linkedin.com/company/chelsea-aesthetic-dentistry',
                'twitter' => '',
                'youtube' => '',
                'google_profile_url' => 'https://maps.google.com/?cid=748291039481',
                'source' => 'Demo Provider',
                'seo_score' => 42,
                'lead_score' => 92,
                'opportunity_level' => 'High',
                'opportunity_reasons' => "Critical: Website not enforcing HTTPS redirects\nCritical: Page title default 'Home - My WordPress Website'\nHigh: Missing local NAP citations on service landing pages",
                'status' => 'Replied',
                'is_demo' => 1
            ],
            [
                'user_id' => 2,
                'business_name' => 'Upper East Pediatric Dentistry',
                'website' => 'https://uppereastpediatricdentist.com',
                'domain' => 'uppereastpediatricdentist.com',
                'email' => 'office@uppereastpediatricdentist.com',
                'phone' => '+1 (212) 555-0819',
                'address' => '1040 Park Ave',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10028',
                'category' => 'Pediatric Dentist',
                'description' => 'Child-friendly pediatric dentistry office providing compassionate dental examinations, preventive hygiene, and laser dentistry.',
                'contact_page' => 'https://uppereastpediatricdentist.com/contact',
                'about_page' => 'https://uppereastpediatricdentist.com/team',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'twitter' => '',
                'youtube' => '',
                'google_profile_url' => 'https://maps.google.com/?cid=384729104928',
                'source' => 'Demo Provider',
                'seo_score' => 67,
                'lead_score' => 70,
                'opportunity_level' => 'Medium',
                'opportunity_reasons' => "High: Missing city keyword in H1 and Title tags\nMedium: 4 internal 404 broken links detected\nLow: No XML sitemap declared",
                'status' => 'Interested',
                'is_demo' => 1
            ]
        ];

        $leadStmt = $pdo->prepare("INSERT OR IGNORE INTO leads (
            user_id, business_name, website, domain, email, phone, address, city, state, country,
            postal_code, category, description, contact_page, about_page, facebook, instagram,
            linkedin, twitter, youtube, google_profile_url, source, seo_score, lead_score,
            opportunity_level, opportunity_reasons, status, is_demo
        ) VALUES (
            :user_id, :business_name, :website, :domain, :email, :phone, :address, :city, :state, :country,
            :postal_code, :category, :description, :contact_page, :about_page, :facebook, :instagram,
            :linkedin, :twitter, :youtube, :google_profile_url, :source, :seo_score, :lead_score,
            :opportunity_level, :opportunity_reasons, :status, :is_demo
        )");

        foreach ($demoLeads as $lead) {
            $leadStmt->execute($lead);
            $leadId = $pdo->lastInsertId();

            // Insert audit record
            $auditStmt = $pdo->prepare("INSERT INTO lead_audits (lead_id, url, technical_score, onpage_score, content_score, local_score, authority_score, social_score, overall_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $auditStmt->execute([
                $leadId,
                $lead['website'],
                intval($lead['seo_score'] * 0.9),
                intval($lead['seo_score'] * 1.1),
                intval($lead['seo_score'] * 0.85),
                intval($lead['seo_score'] * 0.7),
                50,
                40,
                $lead['seo_score']
            ]);
            $auditId = $pdo->lastInsertId();

            // Insert audit issues
            $issues = [
                ['TECHNICAL SEO', 'Missing Canonical Link Tag', 'High', 'Without a self-referential canonical tag, search engines may index duplicate URL variations with and without trailing slashes.', 'Add <link rel="canonical" href="' . $lead['website'] . '"> to the <head> section of all pages.', $lead['website']],
                ['ON-PAGE SEO', 'Missing or Generic Meta Description', 'Critical', 'Search snippets display random text rather than an enticing call-to-action, drastically lowering click-through rates (CTR).', 'Craft a compelling 155-character meta description highlighting emergency dental care and local appointment availability.', $lead['website']],
                ['LOCAL SEO', 'Missing Schema.org LocalBusiness Markup', 'Critical', 'Google cannot easily parse your business hours, address, reviews, or geographical coordinates for local 3-pack inclusion.', 'Implement JSON-LD structured data conforming to schema.org/Dentist.', $lead['website']],
                ['CONTENT', 'Thin Content on Core Treatment Pages', 'High', 'Target service pages average under 300 words, which is insufficient for ranking against comprehensive competitor guides in ' . $lead['city'] . '.', 'Expand treatment pages to 800+ words with patient FAQs, procedure timelines, and before/after details.', $lead['website'] . '/services'],
                ['SOCIAL/BRAND', 'Missing Open Graph Image and Twitter Card Tags', 'Low', 'When prospective clients share your website link via SMS, WhatsApp, or Facebook, no rich preview image appears.', 'Add og:image and twitter:card meta tags pointing to high-resolution practice imagery.', $lead['website']],
                ['TECHNICAL SEO', 'Mobile Page Load Speed Under 50 Performance Score', 'High', 'Uncompressed image assets and render-blocking scripts delay interaction by 4.2 seconds on 4G connections.', 'Implement WebP image compression, browser caching, and defer non-critical JavaScript.', $lead['website']]
            ];

            $issueStmt = $pdo->prepare("INSERT INTO audit_issues (audit_id, category, title, severity, explanation, recommendation, affected_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($issues as $issue) {
                $issueStmt->execute(array_merge([$auditId], $issue));
            }

            // Insert sample notes
            $pdo->prepare("INSERT INTO notes (lead_id, user_id, content) VALUES (?, 2, ?)")
                ->execute([$leadId, "Initial automated crawl completed. Detected 4 critical technical SEO deficiencies and missing LocalBusiness schema. Prime candidate for local dental SEO retainer."]);

            // Insert sample task
            $pdo->prepare("INSERT INTO tasks (user_id, lead_id, title, due_date, priority, status) VALUES (2, ?, ?, ?, 'High', 'pending')")
                ->execute([$leadId, "Follow up with {$lead['business_name']} regarding technical audit pitch", date('Y-m-d', strtotime('+3 days'))]);
        }

        // Seed 1 active campaign
        $pdo->exec("INSERT OR IGNORE INTO campaigns (id, user_id, name, niche, target_country, suggested_service, template_id, status, total_leads, contacted_count, replied_count) VALUES
            (1, 2, 'NYC Dental Clinics - Q3 SEO Outreach', 'Dentist', 'USA', 'Local SEO & Schema Architecture Retainer', 1, 'Active', 5, 2, 1)");

        $pdo->exec("INSERT OR IGNORE INTO notifications (user_id, title, message, type, is_read) VALUES
            (2, 'Search Completed', 'Your search for Dentist prospects in New York returned 5 high-opportunity leads.', 'success', 0),
            (2, 'Audit Ready', 'Deep technical audit completed for Metro Smiles Dental Group (Score: 48/100).', 'info', 0),
            (2, 'Follow-up Due', 'Scheduled follow-up due today for Empire State Orthodontics.', 'warning', 0)");
    }
}
