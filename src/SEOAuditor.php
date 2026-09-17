<?php
/**
 * SEO Client Hunter - Deep 6-Tier SEO Audit Engine
 */

namespace App;

class SEOAuditor {
    public function audit(array $signals, string $url): array {
        $issues = [];
        $scores = [
            'technical' => 100,
            'onpage' => 100,
            'content' => 100,
            'local' => 100,
            'authority' => 100,
            'social' => 100,
        ];

        // -------------------------------------------------------------
        // 1. TECHNICAL SEO
        // -------------------------------------------------------------
        // HTTPS check
        if (empty($signals['is_https'])) {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'Missing SSL / Insecure HTTP Protocol',
                'severity' => 'Critical',
                'explanation' => 'The website does not enforce HTTPS encryption. Google Chrome actively marks non-HTTPS sites as Not Secure, eroding visitor trust and triggering direct ranking penalties.',
                'recommendation' => 'Install an SSL certificate and configure permanent 301 redirects from HTTP to HTTPS across all URLs.',
                'affected_url' => $url
            ];
            $scores['technical'] -= 30;
        } else {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'HTTPS Protocol Enforced',
                'severity' => 'Passed',
                'explanation' => 'Website is secured with valid SSL encryption.',
                'recommendation' => 'Ensure SSL certificate auto-renews prior to expiration.',
                'affected_url' => $url
            ];
        }

        // Canonical Tag
        if (empty($signals['canonical_url']) || $signals['canonical_url'] === 'Not found') {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'Missing Canonical URL Tag',
                'severity' => 'High',
                'explanation' => 'Without a canonical link tag, search engines may treat www, non-www, HTTP, and HTTPS versions or URL parameter variants as duplicate content.',
                'recommendation' => 'Add a self-referential <link rel="canonical" href="' . $url . '"> in the <head> section.',
                'affected_url' => $url
            ];
            $scores['technical'] -= 20;
        } else {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'Canonical Tag Implemented',
                'severity' => 'Passed',
                'explanation' => 'Canonical URL tag specifies the preferred version of this page.',
                'recommendation' => 'Verify canonical matches the exact published URL.',
                'affected_url' => $signals['canonical_url']
            ];
        }

        // Mobile Viewport
        if (empty($signals['has_viewport'])) {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'Missing Mobile Viewport Meta Tag',
                'severity' => 'Critical',
                'explanation' => 'Mobile browsers cannot render the site responsively, failing Google Mobile-First Indexing standards and losing smartphone traffic.',
                'recommendation' => 'Add <meta name="viewport" content="width=device-width, initial-scale=1.0"> inside <head>.',
                'affected_url' => $url
            ];
            $scores['technical'] -= 25;
        } else {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'Mobile Viewport Configured',
                'severity' => 'Passed',
                'explanation' => 'Viewport tag properly directs mobile screen scaling.',
                'recommendation' => 'Keep responsive CSS media queries updated for new device dimensions.',
                'affected_url' => $url
            ];
        }

        // XML Sitemap
        if (empty($signals['has_sitemap'])) {
            $issues[] = [
                'category' => 'TECHNICAL SEO',
                'title' => 'XML Sitemap Not Detected at /sitemap.xml',
                'severity' => 'Medium',
                'explanation' => 'Search engine spiders require a structured XML sitemap to discover newly published service pages and updates quickly.',
                'recommendation' => 'Generate a dynamic sitemap.xml and submit it via Google Search Console.',
                'affected_url' => rtrim($url, '/') . '/sitemap.xml'
            ];
            $scores['technical'] -= 15;
        }

        // -------------------------------------------------------------
        // 2. ON-PAGE SEO
        // -------------------------------------------------------------
        // Title Tag
        $title = $signals['title'] ?? 'Not found';
        $titleLen = $signals['title_length'] ?? 0;
        if ($title === 'Not found' || empty($title)) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Missing <title> Tag',
                'severity' => 'Critical',
                'explanation' => 'Title tag is absent. This is the single most critical on-page ranking signal for search engines and SERP click-throughs.',
                'recommendation' => 'Add a unique, keyword-rich title between 50 and 65 characters.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 35;
        } elseif ($titleLen < 30) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Page Title Too Short (' . $titleLen . ' chars)',
                'severity' => 'Medium',
                'explanation' => "The page title '{$title}' is under-optimized and fails to target commercial location keywords.",
                'recommendation' => 'Expand title to 50-60 characters including target service and primary city.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 15;
        } elseif ($titleLen > 70) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Page Title Truncated in SERPs (' . $titleLen . ' chars)',
                'severity' => 'Low',
                'explanation' => 'Title exceeds 60 characters and will be cut off with an ellipsis in Google search results.',
                'recommendation' => 'Shorten title to under 60 characters while placing key phrases at the beginning.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 10;
        } else {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Title Tag Length Optimal (' . $titleLen . ' chars)',
                'severity' => 'Passed',
                'explanation' => 'Title fits comfortably within Google desktop and mobile character limits.',
                'recommendation' => 'Periodically A/B test click-through rates on search queries.',
                'affected_url' => $url
            ];
        }

        // Meta Description
        $metaDesc = $signals['meta_description'] ?? 'Not found';
        $metaDescLen = $signals['meta_description_length'] ?? 0;
        if ($metaDesc === 'Not found' || empty($metaDesc)) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Missing Meta Description',
                'severity' => 'High',
                'explanation' => 'No meta description tag provided. Google will pull random sentence snippets from the page, reducing click-through rates.',
                'recommendation' => 'Add a compelling 150-160 character meta description featuring a distinct call-to-action.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 25;
        } elseif ($metaDescLen < 70) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Meta Description Too Short (' . $metaDescLen . ' chars)',
                'severity' => 'Medium',
                'explanation' => 'The meta description does not utilize available snippet space to entice prospective clients.',
                'recommendation' => 'Expand description to 140-160 characters describing services and local value proposition.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 10;
        }

        // H1 Headings
        $h1Count = $signals['h1_count'] ?? 0;
        if ($h1Count === 0) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Missing H1 Heading Tag',
                'severity' => 'High',
                'explanation' => 'The page has no <h1> element, making it difficult for crawlers to understand the primary topic of the page.',
                'recommendation' => 'Add exactly one descriptive <h1> tag containing the core service and target city.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 20;
        } elseif ($h1Count > 1) {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Multiple H1 Headings Found (' . $h1Count . ' H1s)',
                'severity' => 'Medium',
                'explanation' => 'Having multiple H1 tags dilutes heading hierarchy and confuses search engine contextual understanding.',
                'recommendation' => 'Consolidate down to a single primary H1 heading and demote secondary headers to H2 or H3.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 10;
        } else {
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => 'Single H1 Heading Structurally Sound',
                'severity' => 'Passed',
                'explanation' => 'Page utilizes a clean primary H1 heading.',
                'recommendation' => 'Ensure H1 matches user search query intent.',
                'affected_url' => $url
            ];
        }

        // Image Alt Attributes
        $imagesTotal = $signals['images_total'] ?? 0;
        $imagesMissingAlt = $signals['images_missing_alt'] ?? 0;
        if ($imagesTotal > 0 && $imagesMissingAlt > 0) {
            $severity = ($imagesMissingAlt / $imagesTotal) > 0.4 ? 'High' : 'Medium';
            $issues[] = [
                'category' => 'ON-PAGE SEO',
                'title' => "Images Missing Descriptive Alt Text ({$imagesMissingAlt} of {$imagesTotal})",
                'severity' => $severity,
                'explanation' => 'Images lacking alt tags harm screen-reader accessibility and forfeit Google Image Search rankings.',
                'recommendation' => 'Add meaningful, contextual alt attributes describing the graphic and incorporating local keywords where natural.',
                'affected_url' => $url
            ];
            $scores['onpage'] -= 15;
        }

        // -------------------------------------------------------------
        // 3. CONTENT
        // -------------------------------------------------------------
        $wordCount = $signals['word_count'] ?? 0;
        if ($wordCount < 250) {
            $issues[] = [
                'category' => 'CONTENT',
                'title' => 'Thin Content Detected (' . $wordCount . ' words)',
                'severity' => 'Critical',
                'explanation' => 'Pages with fewer than 250 words struggle to rank against comprehensive competitor resources and are vulnerable to Google helpful content filters.',
                'recommendation' => 'Expand primary page copy to 800+ words with detailed service descriptions, FAQs, and client testimonials.',
                'affected_url' => $url
            ];
            $scores['content'] -= 40;
        } elseif ($wordCount < 600) {
            $issues[] = [
                'category' => 'CONTENT',
                'title' => 'Moderate Content Depth (' . $wordCount . ' words)',
                'severity' => 'Medium',
                'explanation' => 'Content volume is acceptable for basic scanning, but lacks in-depth topical authority.',
                'recommendation' => 'Introduce rich case studies, process breakdowns, and client FAQs.',
                'affected_url' => $url
            ];
            $scores['content'] -= 15;
        } else {
            $issues[] = [
                'category' => 'CONTENT',
                'title' => 'Strong Word Count & Depth (' . $wordCount . ' words)',
                'severity' => 'Passed',
                'explanation' => 'Page provides substantial content volume for algorithmic indexing.',
                'recommendation' => 'Keep information updated to maintain freshness signals.',
                'affected_url' => $url
            ];
        }

        if ($signals['blog_page_url'] === 'Not found') {
            $issues[] = [
                'category' => 'CONTENT',
                'title' => 'No Active Blog / Knowledge Base Detected',
                'severity' => 'Medium',
                'explanation' => 'Without an educational content hub, the business misses out on high-volume informational search queries and topical authority building.',
                'recommendation' => 'Launch a weekly or monthly blog addressing common client pain points, FAQs, and local guides.',
                'affected_url' => $url
            ];
            $scores['content'] -= 20;
        }

        // -------------------------------------------------------------
        // 4. LOCAL SEO
        // -------------------------------------------------------------
        if (empty($signals['has_schema'])) {
            $issues[] = [
                'category' => 'LOCAL SEO',
                'title' => 'Missing Schema.org Structured Data',
                'severity' => 'Critical',
                'explanation' => 'The website contains zero structured data markup. Search engines cannot programmatically identify opening hours, physical coordinates, reviews, or services.',
                'recommendation' => 'Implement JSON-LD structured data using LocalBusiness or specific industry sub-types (e.g. Dentist, LegalService).',
                'affected_url' => $url
            ];
            $scores['local'] -= 35;
        } else {
            $types = implode(', ', $signals['schema_types'] ?? []);
            $issues[] = [
                'category' => 'LOCAL SEO',
                'title' => 'Structured Data Present (' . ($types ?: 'JSON-LD') . ')',
                'severity' => 'Passed',
                'explanation' => 'Search engines can parse schema markup to display rich snippets.',
                'recommendation' => 'Validate schema with Google Rich Results Test to confirm zero syntax warnings.',
                'affected_url' => $url
            ];
        }

        if (empty($signals['phones_found']) || count($signals['phones_found']) === 0) {
            $issues[] = [
                'category' => 'LOCAL SEO',
                'title' => 'No Prominent Click-to-Call Phone Number Found',
                'severity' => 'High',
                'explanation' => 'Local clients and search engines expect a visible telephone number with tel: link protocol in the header or footer.',
                'recommendation' => 'Place a tap-to-call phone number in the top header and site footer.',
                'affected_url' => $url
            ];
            $scores['local'] -= 20;
        }

        if ($signals['google_profile_url'] === 'Not found') {
            $issues[] = [
                'category' => 'LOCAL SEO',
                'title' => 'No Google Business Profile Link or Map Embed',
                'severity' => 'Medium',
                'explanation' => 'Linking your website to your verified Google Business Profile strengthens local search proximity signals.',
                'recommendation' => 'Add an embedded Google Map or direct Google Review link to your contact section.',
                'affected_url' => $url
            ];
            $scores['local'] -= 15;
        }

        // -------------------------------------------------------------
        // 5. LINK / AUTHORITY SIGNALS
        // -------------------------------------------------------------
        $internalLinks = $signals['internal_links_count'] ?? 0;
        if ($internalLinks < 5) {
            $issues[] = [
                'category' => 'LINK/AUTHORITY SIGNALS',
                'title' => 'Weak Internal Link Architecture (' . $internalLinks . ' internal links)',
                'severity' => 'High',
                'explanation' => 'Search spiders cannot crawl deeper pages when navigation links and contextual cross-links are minimal.',
                'recommendation' => 'Build a comprehensive header menu, breadcrumbs, and cross-links between related service offerings.',
                'affected_url' => $url
            ];
            $scores['authority'] -= 25;
        } else {
            $issues[] = [
                'category' => 'LINK/AUTHORITY SIGNALS',
                'title' => 'Healthy Internal Link Count (' . $internalLinks . ' links)',
                'severity' => 'Passed',
                'explanation' => 'Search engines can crawl between pages across the site hierarchy.',
                'recommendation' => 'Use descriptive, keyword-rich anchor text for internal links.',
                'affected_url' => $url
            ];
        }

        // -------------------------------------------------------------
        // 6. SOCIAL / BRAND SIGNALS
        // -------------------------------------------------------------
        if ($signals['og_title'] === 'Not found' || $signals['og_image'] === 'Not found') {
            $issues[] = [
                'category' => 'SOCIAL/BRAND SIGNALS',
                'title' => 'Missing Open Graph Social Share Tags',
                'severity' => 'Medium',
                'explanation' => 'When your website link is shared on iMessage, Slack, LinkedIn, or Facebook, no card preview image appears.',
                'recommendation' => 'Add og:title, og:description, and og:image (1200x630px) tags in the <head>.',
                'affected_url' => $url
            ];
            $scores['social'] -= 25;
        } else {
            $issues[] = [
                'category' => 'SOCIAL/BRAND SIGNALS',
                'title' => 'Open Graph Social Sharing Enabled',
                'severity' => 'Passed',
                'explanation' => 'Social media platforms render branded card previews when pages are shared.',
                'recommendation' => 'Ensure preview image is branded and sharp on retina screens.',
                'affected_url' => $url
            ];
        }

        $socialLinksCount = 0;
        foreach (['facebook_url', 'instagram_url', 'linkedin_url', 'twitter_url', 'youtube_url'] as $k) {
            if (!empty($signals[$k]) && $signals[$k] !== 'Not found') {
                $socialLinksCount++;
            }
        }

        if ($socialLinksCount === 0) {
            $issues[] = [
                'category' => 'SOCIAL/BRAND SIGNALS',
                'title' => 'No Connected Public Social Media Profiles',
                'severity' => 'Low',
                'explanation' => 'Search algorithms look for official brand entities across reputable platforms to verify business legitimacy.',
                'recommendation' => 'Establish official profiles on LinkedIn, Facebook, and Instagram, and link them from your footer.',
                'affected_url' => $url
            ];
            $scores['social'] -= 15;
        }

        // Clamp all category scores between 10 and 100
        foreach ($scores as $cat => $val) {
            $scores[$cat] = max(10, min(100, $val));
        }

        // Weighted Overall Score
        $overallScore = intval(
            ($scores['technical'] * 0.25) +
            ($scores['onpage'] * 0.25) +
            ($scores['local'] * 0.20) +
            ($scores['content'] * 0.15) +
            ($scores['authority'] * 0.10) +
            ($scores['social'] * 0.05)
        );

        return [
            'overall_score' => $overallScore,
            'scores' => $scores,
            'issues' => $issues
        ];
    }
}
