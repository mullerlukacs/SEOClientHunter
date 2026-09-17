<?php
/**
 * SEO Client Hunter - Safe Website Crawler & HTML Signal Extractor
 */

namespace App;

use DOMDocument;
use DOMXPath;

class Crawler {
    private int $timeout = 6;
    private int $maxRedirects = 3;
    private string $userAgent = 'SEOClientHunterBot/2.4 (+https://seoclienthunter.com/crawler-policy; Safe Technical SEO Auditor)';

    /**
     * Safely crawl a URL and extract 40+ SEO and contact signals
     */
    public function crawl(string $url): array {
        $cleanUrl = $this->normalizeUrl($url);
        if (!$cleanUrl) {
            return [
                'success' => false,
                'error' => 'Invalid website URL format.',
                'signals' => []
            ];
        }

        $parsed = parse_url($cleanUrl);
        $domain = $parsed['host'] ?? '';
        $scheme = $parsed['scheme'] ?? 'http';

        // Check robots.txt
        $robotsAllowed = $this->checkRobotsTxt($scheme . '://' . $domain, $parsed['path'] ?? '/');

        // Fetch primary page
        $response = $this->fetchUrl($cleanUrl);
        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Could not connect to website.',
                'url' => $cleanUrl,
                'domain' => $domain,
                'signals' => $this->getDefaultEmptySignals($cleanUrl, $domain, $robotsAllowed)
            ];
        }

        $html = $response['body'];
        $httpCode = $response['http_code'];
        $effectiveUrl = $response['effective_url'];
        $loadTime = $response['total_time'];

        $signals = $this->analyzeHtml($html, $cleanUrl, $domain);
        $signals['http_code'] = $httpCode;
        $signals['effective_url'] = $effectiveUrl;
        $signals['load_time_seconds'] = round($loadTime, 2);
        $signals['is_https'] = str_starts_with(strtolower($effectiveUrl), 'https://');
        $signals['robots_allowed'] = $robotsAllowed;

        // Check sitemap presence
        $signals['has_sitemap'] = $this->checkSitemapPresence($scheme . '://' . $domain);

        return [
            'success' => true,
            'url' => $cleanUrl,
            'domain' => $domain,
            'signals' => $signals
        ];
    }

    private function fetchUrl(string $url): array {
        if (!function_exists('curl_init')) {
            // Fallback to stream context
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => $this->timeout,
                    'user_agent' => $this->userAgent,
                    'follow_location' => 1,
                    'max_redirects' => $this->maxRedirects,
                ]
            ]);
            $body = @file_get_contents($url, false, $ctx);
            if ($body === false) {
                return ['success' => false, 'error' => 'Network request failed.'];
            }
            return [
                'success' => true,
                'body' => $body,
                'http_code' => 200,
                'effective_url' => $url,
                'total_time' => 0.5
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $this->maxRedirects,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_SSL_VERIFYPEER => false, // Ensure crawl doesn't abort on self-signed client certs
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_ENCODING => '', // supports gzip, deflate
            CURLOPT_HEADER => false,
        ]);

        $body = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) ?: $url;
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);

        if ($err && empty($body)) {
            return ['success' => false, 'error' => 'CURL Error: ' . $err];
        }

        if ($httpCode >= 400) {
            return ['success' => false, 'error' => "Server responded with HTTP {$httpCode}.", 'http_code' => $httpCode];
        }

        return [
            'success' => true,
            'body' => $body ?: '',
            'http_code' => $httpCode,
            'effective_url' => $effectiveUrl,
            'total_time' => $totalTime
        ];
    }

    private function analyzeHtml(string $html, string $baseUrl, string $domain): array {
        $signals = [
            'title' => 'Not found',
            'title_length' => 0,
            'meta_description' => 'Not found',
            'meta_description_length' => 0,
            'h1_tags' => [],
            'h1_count' => 0,
            'h2_tags' => [],
            'h2_count' => 0,
            'canonical_url' => 'Not found',
            'robots_meta' => 'Not found',
            'has_viewport' => false,
            'viewport_content' => 'Not found',
            'images_total' => 0,
            'images_missing_alt' => 0,
            'internal_links_count' => 0,
            'external_links_count' => 0,
            'has_schema' => false,
            'schema_types' => [],
            'og_title' => 'Not found',
            'og_description' => 'Not found',
            'og_image' => 'Not found',
            'twitter_card' => 'Not found',
            'word_count' => 0,
            'emails_found' => [],
            'phones_found' => [],
            'contact_page_url' => 'Not found',
            'about_page_url' => 'Not found',
            'services_page_url' => 'Not found',
            'blog_page_url' => 'Not found',
            'facebook_url' => 'Not found',
            'instagram_url' => 'Not found',
            'linkedin_url' => 'Not found',
            'twitter_url' => 'Not found',
            'youtube_url' => 'Not found',
            'google_profile_url' => 'Not found',
            'broken_links_detected' => 0
        ];

        if (empty($html)) {
            return $signals;
        }

        // Suppress HTML5 parsing warnings in DOMDocument
        if (!class_exists('DOMDocument')) {
            // Regex fallback for environments without php-xml
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $signals['title'] = trim(strip_tags($m[1]));
                $signals['title_length'] = mb_strlen($signals['title']);
            }
            if (preg_match('/<meta[^>]+name=[\'"]description[\'"][^>]+content=[\'"](.*?)[\'"]/is', $html, $m)) {
                $signals['meta_description'] = trim($m[1]);
                $signals['meta_description_length'] = mb_strlen($signals['meta_description']);
            }
            if (preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $signals['h1_count'] = count($m[1]);
                foreach ($m[1] as $h) {
                    $txt = trim(strip_tags($h));
                    if (!empty($txt)) $signals['h1_tags'][] = substr($txt, 0, 150);
                }
            }
            return $signals;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        libxml_clear_errors();

        // 1. Title
        $titleNodes = $xpath->query('//title');
        if ($titleNodes->length > 0) {
            $signals['title'] = trim($titleNodes->item(0)->textContent);
            $signals['title_length'] = mb_strlen($signals['title']);
        }

        // 2. Meta Description & Canonical & Robots & Viewport
        $metaNodes = $xpath->query('//meta');
        foreach ($metaNodes as $meta) {
            $name = strtolower($meta->getAttribute('name'));
            $property = strtolower($meta->getAttribute('property'));
            $content = trim($meta->getAttribute('content'));

            if ($name === 'description' && !empty($content)) {
                $signals['meta_description'] = $content;
                $signals['meta_description_length'] = mb_strlen($content);
            } elseif ($name === 'robots') {
                $signals['robots_meta'] = $content;
            } elseif ($name === 'viewport') {
                $signals['has_viewport'] = true;
                $signals['viewport_content'] = $content;
            } elseif ($property === 'og:title') {
                $signals['og_title'] = $content;
            } elseif ($property === 'og:description') {
                $signals['og_description'] = $content;
            } elseif ($property === 'og:image') {
                $signals['og_image'] = $content;
            } elseif ($name === 'twitter:card') {
                $signals['twitter_card'] = $content;
            }
        }

        // Canonical link
        $canonicalNodes = $xpath->query('//link[@rel="canonical"]');
        if ($canonicalNodes->length > 0) {
            $signals['canonical_url'] = trim($canonicalNodes->item(0)->getAttribute('href'));
        }

        // 3. Headings H1, H2
        $h1Nodes = $xpath->query('//h1');
        $signals['h1_count'] = $h1Nodes->length;
        foreach ($h1Nodes as $h1) {
            $text = trim(preg_replace('/\s+/', ' ', $h1->textContent));
            if (!empty($text)) $signals['h1_tags'][] = substr($text, 0, 150);
        }

        $h2Nodes = $xpath->query('//h2');
        $signals['h2_count'] = $h2Nodes->length;
        foreach ($h2Nodes as $h2) {
            $text = trim(preg_replace('/\s+/', ' ', $h2->textContent));
            if (!empty($text) && count($signals['h2_tags']) < 8) {
                $signals['h2_tags'][] = substr($text, 0, 150);
            }
        }

        // 4. Images & Alt attributes
        $imgNodes = $xpath->query('//img');
        $signals['images_total'] = $imgNodes->length;
        foreach ($imgNodes as $img) {
            $alt = trim($img->getAttribute('alt'));
            if ($alt === '') {
                $signals['images_missing_alt']++;
            }
        }

        // 5. Schema Markup (JSON-LD)
        $scriptNodes = $xpath->query('//script[@type="application/ld+json"]');
        if ($scriptNodes->length > 0) {
            $signals['has_schema'] = true;
            foreach ($scriptNodes as $script) {
                $json = @json_decode($script->textContent, true);
                if ($json) {
                    if (isset($json['@type'])) {
                        $signals['schema_types'][] = is_array($json['@type']) ? implode(', ', $json['@type']) : $json['@type'];
                    } elseif (isset($json['@graph']) && is_array($json['@graph'])) {
                        foreach ($json['@graph'] as $node) {
                            if (isset($node['@type'])) {
                                $signals['schema_types'][] = is_array($node['@type']) ? implode(', ', $node['@type']) : $node['@type'];
                            }
                        }
                    }
                }
            }
            $signals['schema_types'] = array_unique($signals['schema_types']);
        }

        // 6. Word count estimation
        $bodyNodes = $xpath->query('//body');
        if ($bodyNodes->length > 0) {
            $bodyText = strip_tags($bodyNodes->item(0)->textContent);
            $words = str_word_count($bodyText);
            $signals['word_count'] = $words;
        }

        // 7. Links: Internal vs External, contact/about/service/social links
        $aNodes = $xpath->query('//a[@href]');
        foreach ($aNodes as $a) {
            $href = trim($a->getAttribute('href'));
            if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) {
                continue;
            }

            // Mailto detection
            if (str_starts_with(strtolower($href), 'mailto:')) {
                $email = strtolower(trim(substr($href, 7)));
                $email = explode('?', $email)[0];
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $signals['emails_found'], true)) {
                    $signals['emails_found'][] = $email;
                }
                continue;
            }

            // Tel detection
            if (str_starts_with(strtolower($href), 'tel:')) {
                $phone = trim(substr($href, 4));
                if (!in_array($phone, $signals['phones_found'], true)) {
                    $signals['phones_found'][] = $phone;
                }
                continue;
            }

            // Social URLs
            if (preg_match('/facebook\.com\/([a-zA-Z0-9\.\_\-]+)/i', $href)) {
                $signals['facebook_url'] = $href;
            } elseif (preg_match('/instagram\.com\/([a-zA-Z0-9\.\_\-]+)/i', $href)) {
                $signals['instagram_url'] = $href;
            } elseif (preg_match('/linkedin\.com\/(company|in)\/([a-zA-Z0-9\.\_\-]+)/i', $href)) {
                $signals['linkedin_url'] = $href;
            } elseif (preg_match('/(twitter\.com|x\.com)\/([a-zA-Z0-9\.\_]+)/i', $href)) {
                $signals['twitter_url'] = $href;
            } elseif (preg_match('/youtube\.com\/(channel|c|user|@)/i', $href)) {
                $signals['youtube_url'] = $href;
            } elseif (preg_match('/(google\.com\/maps|goo\.gl\/maps)/i', $href)) {
                $signals['google_profile_url'] = $href;
            }

            // Page detection
            $lowerHref = strtolower($href);
            if (preg_match('/(contact|get-in-touch|reach-us)/', $lowerHref) && $signals['contact_page_url'] === 'Not found') {
                $signals['contact_page_url'] = $this->resolveUrl($baseUrl, $href);
            }
            if (preg_match('/(about|our-team|who-we-are|our-story)/', $lowerHref) && $signals['about_page_url'] === 'Not found') {
                $signals['about_page_url'] = $this->resolveUrl($baseUrl, $href);
            }
            if (preg_match('/(services|treatments|practice-areas|what-we-do)/', $lowerHref) && $signals['services_page_url'] === 'Not found') {
                $signals['services_page_url'] = $this->resolveUrl($baseUrl, $href);
            }
            if (preg_match('/(blog|articles|news|insights)/', $lowerHref) && $signals['blog_page_url'] === 'Not found') {
                $signals['blog_page_url'] = $this->resolveUrl($baseUrl, $href);
            }

            // Internal vs External
            $host = parse_url($href, PHP_URL_HOST);
            if (!$host || strcasecmp($host, $domain) === 0 || str_ends_with(strtolower($host), '.' . strtolower($domain))) {
                $signals['internal_links_count']++;
            } else {
                $signals['external_links_count']++;
            }
        }

        // 8. Extract plain-text emails & phone numbers from body text if mailto was empty
        if (empty($signals['emails_found'])) {
            preg_match_all('/[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}/', $html, $matches);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $em) {
                    $em = strtolower(trim($em));
                    // Filter out image extensions erroneously matched
                    if (!preg_match('/\.(png|jpg|jpeg|gif|svg|webp|js|css)$/i', $em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
                        $signals['emails_found'][] = $em;
                        break; // take first valid contact
                    }
                }
            }
        }

        if (empty($signals['phones_found'])) {
            // Match US/International phone formats e.g. (212) 555-0192 or +1 212 555 0192
            preg_match_all('/(?:\+?1[-.\s]?)?\(?[0-9]{3}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/', $html, $matches);
            if (!empty($matches[0])) {
                $signals['phones_found'][] = trim($matches[0][0]);
            }
        }

        return $signals;
    }

    private function checkRobotsTxt(string $baseHost, string $path): bool {
        // Safe check if robots.txt exists
        $robotsUrl = rtrim($baseHost, '/') . '/robots.txt';
        $res = $this->fetchUrl($robotsUrl);
        if (!$res['success'] || empty($res['body'])) {
            return true; // No robots.txt, implicitly allowed
        }
        $body = $res['body'];
        if (preg_match('/User-agent:\s*\*\s*Disallow:\s*\/\s*$/im', $body)) {
            return false; // Whole site disallowed
        }
        return true;
    }

    private function checkSitemapPresence(string $baseHost): bool {
        $sitemapUrl = rtrim($baseHost, '/') . '/sitemap.xml';
        $res = $this->fetchUrl($sitemapUrl);
        return ($res['success'] && $res['http_code'] === 200 && str_contains($res['body'] ?? '', '<urlset'));
    }

    private function normalizeUrl(string $url): ?string {
        $url = trim($url);
        if (empty($url)) return null;
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'https://' . $url;
        }
        return filter_var($url, FILTER_VALIDATE_URL) ?: null;
    }

    private function resolveUrl(string $baseUrl, string $relUrl): string {
        if (parse_url($relUrl, PHP_URL_SCHEME) != '') return $relUrl;
        if (str_starts_with($relUrl, '//')) return 'https:' . $relUrl;
        if (str_starts_with($relUrl, '/')) {
            $parts = parse_url($baseUrl);
            return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '') . $relUrl;
        }
        return rtrim($baseUrl, '/') . '/' . $relUrl;
    }

    private function getDefaultEmptySignals(string $url, string $domain, bool $robotsAllowed): array {
        return [
            'http_code' => 0,
            'effective_url' => $url,
            'load_time_seconds' => 0,
            'is_https' => str_starts_with($url, 'https://'),
            'robots_allowed' => $robotsAllowed,
            'has_sitemap' => false,
            'title' => 'Not found',
            'title_length' => 0,
            'meta_description' => 'Not found',
            'meta_description_length' => 0,
            'h1_tags' => [],
            'h1_count' => 0,
            'h2_tags' => [],
            'h2_count' => 0,
            'canonical_url' => 'Not found',
            'robots_meta' => 'Not found',
            'has_viewport' => false,
            'viewport_content' => 'Not found',
            'images_total' => 0,
            'images_missing_alt' => 0,
            'internal_links_count' => 0,
            'external_links_count' => 0,
            'has_schema' => false,
            'schema_types' => [],
            'og_title' => 'Not found',
            'og_description' => 'Not found',
            'og_image' => 'Not found',
            'twitter_card' => 'Not found',
            'word_count' => 0,
            'emails_found' => [],
            'phones_found' => [],
            'contact_page_url' => 'Not found',
            'about_page_url' => 'Not found',
            'services_page_url' => 'Not found',
            'blog_page_url' => 'Not found',
            'facebook_url' => 'Not found',
            'instagram_url' => 'Not found',
            'linkedin_url' => 'Not found',
            'twitter_url' => 'Not found',
            'youtube_url' => 'Not found',
            'google_profile_url' => 'Not found',
            'broken_links_detected' => 0
        ];
    }
}
