# SEO CLIENT HUNTER - B2B Lead Acquisition & Automated SEO Outreach SaaS

SEO Client Hunter is a complete, production-ready full-stack SaaS platform built with **PHP 8.2+**, **PDO (MySQL & SQLite)**, **HTML5**, **CSS3**, **Bootstrap 5**, and **Vanilla JavaScript**. It enables digital marketing agencies, SEO consultants, and B2B service providers to automatically discover local businesses with critical SEO flaws, calculate lead opportunity scores, perform 40-point technical website audits, synthesize hyper-personalized AI outreach pitches, and manage prospects through a built-in CRM sales pipeline.

---

## 🌟 Key Features

### 1. Public Front-Facing SaaS Portal
- **Marketing Homepage**: Value proposition, interactive live demo scanner, feature showcases, problem/solution comparisons, customer ROI statistics, FAQ accordion, and pricing table.
- **Dynamic CMS Pages**: Fully functional `/features`, `/pricing`, `/about`, `/contact`, `/privacy`, and `/terms` pages with dynamic backend metadata.
- **Global SEO & Schema**: Automatic OpenGraph cards, Twitter preview cards, meta descriptions, canonical URLs, auto-generated `robots.txt`, and `sitemap.xml`.
- **Integrated Authentication**: Multi-role user registration and login (`admin` and `user` roles) with CSRF tokens, secure session management, and password hashing (`PASSWORD_BCRYPT`).

### 2. User & Agency Dashboard
- **Prospect Discovery Engine**: Search local businesses by niche keyword (e.g. *Dentists*, *Roofers*, *Attorneys*) and geographic location (*Miami, FL*, *Austin, TX*, *London, UK*).
- **Automated Heuristic Web Crawler**: Fetches and parses target HTML, HTTP headers, robots.txt, sitemaps, OpenGraph metadata, and structured data without blocking.
- **6-Tier 40-Point SEO Audit**:
  - *Tier 1: Technical & Speed Health* (SSL certificates, canonical tags, responsive viewport, HTTP response status, XML sitemaps, robots.txt).
  - *Tier 2: On-Page Structure & Tags* (Title tags, meta descriptions, H1/H2 hierarchy, image alt attributes, OpenGraph tags, Twitter cards).
  - *Tier 3: Content Signals* (Word count adequacy, text-to-code ratio, keyword density, reading level).
  - *Tier 4: Local SEO & Schema* (NAP consistency, Schema.org JSON-LD business entity, Google Maps markers, local phone formatting).
  - *Tier 5: Authority & Links* (Internal link ratio, broken outbound link checks, nofollow distribution).
  - *Tier 6: Social Signals & Brand* (Discovered Facebook, Instagram, LinkedIn, YouTube, X/Twitter profiles).
- **Smart Lead Opportunity Scoring (0-100)**: Evaluates prospective commercial value by combining SEO failure severity, high-ticket niche multiplier, and reachable contact pathways.
- **AI Outreach Pitch Generator**:
  - Automatically synthesizes tailored pitches referencing target business flaws.
  - Generates: *Cold Email (Subject + Body)*, *LinkedIn InMail message*, *Website Contact Form pitch*, and a *3-touch automated follow-up sequence* (Day 3 soft bump, Day 7 value add, Day 14 break-up note).
  - 1-click **Copy to Clipboard** with instant toast feedback.
  - Supports **Google Gemini API** (`gemini-2.5-flash`, `gemini-1.5-pro`) with built-in heuristic fallback in Demo Mode.
- **CRM Lead Pipeline**: Filter prospects by pipeline stage (*New*, *Qualified*, *Contacted*, *Replied*, *Interested*, *Proposal Sent*, *Closed Won*, *Lost*), manage internal notes, and schedule task action items.
- **Multi-Format Data Export**: 1-click export of filtered leads to CSV.
- **Subscription Quotas & Usage Tracking**: Real-time quota meters for searches, leads, audits, and AI pitches.

### 3. Master Admin Control Panel
- **Master Metrics Dashboard**: Platform-wide user signups, prospect pool statistics, live query logs, and system error logs.
- **User & Subscription Management**: Change user roles, assign subscription tiers, toggle active/suspended status, and reset passwords.
- **Tier Quota Editor**: Configure plan prices, searches per month, audits per month, AI pitches per month, and CSV export permissions.
- **CMS Page & Section Editor**: Edit page copy, HTML, meta titles, meta descriptions, and homepage hero CTA buttons.
- **Navigation Menu Manager**: Add, reorder, and remove header and footer navigation links.
- **Monetization & Ad Spaces**: Create banner or custom HTML ad placements (`header_banner`, `sidebar_banner`, `footer_banner`, `lead_card_banner`) with live impression and click counters.
- **Global SEO Settings**: Edit site-wide meta tags, OpenGraph social images, Twitter cards, custom `robots.txt`, and Schema.org markup.
- **Branding & Theme Engine**: Customize platform name, tagline, primary brand color, and button corner radius (`4px`, `8px`, `12px`, `24px`).
- **API Credentials Manager**: Dedicated interface to configure Gemini API keys, Google Custom Search API / Search Engine ID, SerpApi key, and transactional SMTP credentials.
- **Audit & System Logs**: Filterable logs for system exceptions, user events, and query histories.

---

## 🚀 Getting Started & Default Credentials

### Default Accounts (Pre-Seeded)

| Role | Email | Password | Access Area |
|---|---|---|---|
| **Super Administrator** | `admin@seoclienthunter.com` | `admin123` | `/admin` & `/dashboard` |
| **Agency User** | `demo@seoclienthunter.com` | `demo123` | `/dashboard` |

---

## 🛠️ Deployment Instructions

### 1. Shared Hosting / cPanel Deployment
1. Upload all files to your `public_html` directory (or a subdomain folder).
2. Create a MySQL database and user in cPanel.
3. Import the included `database.sql` file via phpMyAdmin or MySQL command line.
4. Copy `.env.example` to `.env` (or configure `config/config.php` and `config/database.php`):
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   BASE_URL=https://yourdomain.com
   ```
5. Ensure `database/` has write permissions if using SQLite fallback (`chmod 775 database/`).
6. The included `.htaccess` file will route requests to `public/index.php`.

### 2. Nginx Server Block Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/seoclienthunter;
    index index.php;

    location / {
        try_files $uri $uri/ /public/index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ ^/(config|database|src|views)/ {
        deny all;
    }
}
```

### 3. Local Built-in PHP Development Server
To run directly with PHP CLI on any machine:
```bash
php -S localhost:8000 -t . public/index.php
```

---

## 🧪 Demo / Mock Mode vs. Live External APIs

- By default, the application runs with **Demo Mode enabled** in the database.
- In Demo Mode:
  - Local business prospecting queries produce realistic, geographically authentic business profiles with real-world website structures, contact pages, and verifiable SEO deficiencies.
  - The SEO Auditor analyzes actual targets or generates complete 40-point diagnostics.
  - The AI Pitch Generator formulates contextualized cold outreach and multi-touch follow-up emails without requiring an external API key.
- Whenever you are ready to use live external APIs:
  1. Log into the Admin panel at `/admin`.
  2. Navigate to **API Credentials** (`/admin/api-keys`).
  3. Enter your **Google Gemini API Key** and/or **Google Custom Search API Key**.
  4. Toggle **Live API Enabled**.
  5. In **Site Settings** (`/admin/settings`), turn off **DEMO Mode**.

---

## 🔒 Security Architecture
- **Zero Raw Queries**: All database operations use PDO prepared statements with bound parameters to prevent SQL injection.
- **CSRF Token Validation**: State-changing forms enforce valid session-bound CSRF tokens.
- **Input Sanitization**: All view outputs are filtered with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.
- **Role-Based Access Control**: Standard users cannot access `/admin`; unauthorized access redirects to `/403`.
- **Directory Protection**: Sensitive directories (`/config`, `/src`, `/views`, `/database`) are shielded from direct browser URL access.
