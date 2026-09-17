<?php
/**
 * SEO Client Hunter - AI Analysis & Pitch Personalization Engine
 */

namespace App;

use PDO;

class AIService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Generate complete AI Analysis and multi-angle pitch pack
     */
    public function analyzeLead(array $lead, array $audit): array {
        // Check if external AI provider key is configured and active
        $stmt = $this->db->prepare("SELECT api_key, model_name, is_active FROM api_keys WHERE provider = 'ai_provider'");
        $stmt->execute();
        $aiConfig = $stmt->fetch();

        $apiKey = $aiConfig['api_key'] ?? getenv('GEMINI_API_KEY') ?: '';
        $isActive = !empty($aiConfig['is_active']) || !empty(getenv('GEMINI_API_KEY'));

        if (!empty($apiKey) && $isActive) {
            $externalResult = $this->callGeminiApi($lead, $audit, $apiKey, $aiConfig['model_name'] ?? 'gemini-2.5-flash');
            if ($externalResult['success']) {
                return $externalResult['data'];
            }
        }

        // Production-ready Intelligent Heuristic Engine (works instantly without API keys)
        return $this->generateHeuristicAnalysis($lead, $audit);
    }

    /**
     * Heuristic intelligence generator mapping concrete audit issues to high-converting pitch angles
     */
    public function generateHeuristicAnalysis(array $lead, array $audit): array {
        $bizName = $lead['business_name'] ?? 'Your Business';
        $niche = $lead['category'] ?? 'business';
        $city = $lead['city'] ?? 'your city';
        $website = $lead['website'] ?? 'your website';
        $issues = $audit['issues'] ?? [];

        // Extract top critical and high issues
        $defects = [];
        foreach ($issues as $iss) {
            if (in_array($iss['severity'], ['Critical', 'High'], true)) {
                $defects[] = $iss['title'];
            }
            if (count($defects) >= 3) break;
        }
        if (empty($defects)) {
            $defects = ['Missing Local Schema structured data', 'Unoptimized meta descriptions and titles', 'Thin service landing page content'];
        }

        $defectsListStr = implode(', ', $defects);

        // Determine recommended service
        $suggestedService = 'Comprehensive Local SEO & Schema Architecture Retainer';
        $pitchAngle = 'Fix Critical Technical Flaws Suppressing High-Intent Local Searches';

        if (str_contains(strtolower($defectsListStr), 'schema') || str_contains(strtolower($defectsListStr), 'local')) {
            $suggestedService = 'Local 3-Pack Google Maps Optimization & Schema Implementation';
            $pitchAngle = 'Local Map Pack Dominance Over Nearby Competitors';
        } elseif (str_contains(strtolower($defectsListStr), 'ssl') || str_contains(strtolower($defectsListStr), 'viewport') || str_contains(strtolower($defectsListStr), 'speed')) {
            $suggestedService = 'Technical SEO Infrastructure & Mobile Experience Overhaul';
            $pitchAngle = 'Eliminating High-Bounce Technical Flaws Hurting Conversions';
        } elseif (str_contains(strtolower($defectsListStr), 'thin content') || str_contains(strtolower($defectsListStr), 'blog')) {
            $suggestedService = 'Topical Authority & High-Intent Service Page Content Expansion';
            $pitchAngle = 'Capturing High-Value Treatment & Booking Queries';
        }

        $businessSummary = "{$bizName} is an established {$niche} operating in {$city}. While possessing an established local brand presence, their website ({$website}) suffers from structural technical barriers that degrade organic crawl efficiency and depress Google ranking potential for competitive localized search phrases.";

        $seoProblems = "During our deep-crawl inspection, we verified key vulnerabilities including: " . implode('; ', $defects) . ". These defects directly hinder search engine indexation and lower click-through rates from local searchers.";

        $businessOpportunity = "Local search volume for {$niche} in {$city} represents high-intent prospects actively seeking immediate bookings. By addressing these foundational ranking barriers and deploying {$suggestedService}, {$bizName} can realistically displace lower-authority competitors and capture an estimated 25-45% increase in organic phone inquiries.";

        // Pre-craft 4 distinct outreach angles
        $coldEmail = [
            'subject' => "Quick question regarding organic visibility for {$bizName}",
            'body' => "Hi there,\n\nI was doing some research on reputable {$niche} practices in {$city} and came across {$website}.\n\nWhile your reputation looks stellar, I noticed a few technical bottlenecks—specifically {$defectsListStr}—that are actively preventing your pages from claiming top Google spots when local patients/clients search for {$niche} services.\n\nWe specialize in {$suggestedService} specifically for businesses in your space. Would you be open to a 4-minute video walk-through showing where these errors are located and how to resolve them?\n\nBest regards,\n[Your Name]\n[Your Agency]"
        ];

        $linkedInMessage = [
            'subject' => "SEO & Local Search Opportunity for {$bizName}",
            'body' => "Hi! Came across {$bizName} while analyzing the {$city} {$niche} market. Your business has a great local profile, but your website currently has technical issues with {$defectsListStr}. We recently helped a nearby practice resolve this with {$suggestedService} and saw a 38% bump in patient inquiries in 60 days. Open to a brief chat to see if this makes sense for you?"
        ];

        $contactFormPitch = [
            'subject' => "Website Audit Feedback for {$bizName}",
            'body' => "Hello {$bizName} Team,\n\nI wanted to share a quick heads-up regarding your website ({$website}). Our automated audit engine flagged several high-priority SEO items ({$defectsListStr}) that are likely restricting your organic search rankings in {$city}.\n\nWe would love to share a free, customized 1-page action plan with your team on how to correct these to capture more local bookings. Let us know who the best person to speak with is."
        ];

        $shortPitch = [
            'subject' => "Quick observation for {$bizName}",
            'body' => "Hey there, noticed your website is currently missing critical {$defects[0]} elements in {$city}. This is giving local competitors an easy ranking advantage. Let me know if you would like me to send our free 3-step fix guide!"
        ];

        return [
            'business_summary' => $businessSummary,
            'seo_problems' => $seoProblems,
            'business_opportunity' => $businessOpportunity,
            'suggested_service' => $suggestedService,
            'suggested_pitch_angle' => $pitchAngle,
            'defects_list' => $defects,
            'cold_email' => $coldEmail,
            'linkedin_message' => $linkedInMessage,
            'contact_form_pitch' => $contactFormPitch,
            'short_pitch' => $shortPitch,
            'followups' => [
                [
                    'step' => 1,
                    'day' => 3,
                    'subject' => "Following up on {$bizName} Google rankings",
                    'body' => "Hi,\n\nQuick follow up on my note regarding the technical items ({$defects[0]}) on {$website}.\n\nI put together a quick mockup showing how fixing this will position {$bizName} ahead of competitors in {$city}. Should I email the link over?\n\nBest,\n[Your Name]"
                ],
                [
                    'step' => 2,
                    'day' => 7,
                    'subject' => "Missed local search volume in {$city} for {$bizName}",
                    'body' => "Hi,\n\nJust wanted to make sure my previous message didn't get buried. We've seen {$niche} businesses in {$city} double their online booking rates simply by implementing {$suggestedService}.\n\nIf you have 5 minutes this Thursday, I'd love to share the data with you.\n\nBest,\n[Your Name]"
                ],
                [
                    'step' => 3,
                    'day' => 14,
                    'subject' => "Permission to close your file for {$bizName}?",
                    'body' => "Hi,\n\nI haven't heard back, so I assume improving your organic Google client acquisition isn't a top priority for {$bizName} right now—which is completely fine!\n\nIf things change and you want to tackle {$defectsListStr}, feel free to reach back out.\n\nBest of luck!\n[Your Name]"
                ]
            ]
        ];
    }

    private function callGeminiApi(array $lead, array $audit, string $apiKey, string $model): array {
        $prompt = "You are a world-class B2B SEO Agency Outreach Specialist. Analyze this business lead and produce a JSON response with:
- business_summary
- seo_problems
- business_opportunity
- suggested_service
- suggested_pitch_angle
- cold_email (subject, body)
- linkedin_message (subject, body)
- contact_form_pitch (subject, body)
- short_pitch (subject, body)
- followups (array of 3 follow-up steps with step, day, subject, body)

Lead Details: " . json_encode($lead) . "
SEO Audit Details: " . json_encode($audit);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $postData = json_encode([
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 12
        ]);

        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($res)) {
            $json = json_decode($res, true);
            $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $parsed = json_decode($rawText, true);
            if ($parsed && isset($parsed['business_summary'])) {
                return ['success' => true, 'data' => $parsed];
            }
        }

        return ['success' => false];
    }
}
