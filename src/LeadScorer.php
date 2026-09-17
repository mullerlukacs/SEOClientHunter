<?php
/**
 * SEO Client Hunter - 0-100 Lead Opportunity Scorer Engine
 */

namespace App;

class LeadScorer {
    /**
     * High-commercial intent niches where clients are worth $2,000 - $15,000+ each
     */
    private array $highIntentNiches = [
        'dentist', 'dental', 'orthodontist', 'cosmetic dentistry',
        'lawyer', 'attorney', 'legal', 'personal injury',
        'roofer', 'roofing', 'hvac', 'plumber', 'electrician',
        'plastic surgeon', 'medical spa', 'dermatology',
        'accountant', 'cpa', 'financial advisor',
        'chiropractor', 'veterinarian', 'optometrist'
    ];

    public function calculate(array $leadData, array $auditResults): array {
        $score = 50; // baseline
        $reasons = [];

        $seoScore = $auditResults['overall_score'] ?? 50;
        $categoryScores = $auditResults['scores'] ?? [];
        $category = strtolower($leadData['category'] ?? $leadData['niche'] ?? '');

        // 1. Inverted SEO Quality: Lower SEO score means MORE opportunity for an agency to pitch!
        if ($seoScore < 45) {
            $score += 25;
            $reasons[] = "Severe Technical & On-Page Deficiencies (SEO Score: {$seoScore}/100) provide high-leverage pitch points.";
        } elseif ($seoScore < 65) {
            $score += 15;
            $reasons[] = "Noticeable SEO weaknesses across local schema and on-page optimization create immediate retainer value.";
        } elseif ($seoScore > 85) {
            $score -= 15;
            $reasons[] = "Website is already well-optimized (SEO Score: {$seoScore}/100), reducing client receptivity to cold SEO pitches.";
        }

        // 2. High-Ticket Commercial Niche Fit
        $isHighIntent = false;
        foreach ($this->highIntentNiches as $niche) {
            if (str_contains($category, $niche)) {
                $isHighIntent = true;
                break;
            }
        }

        if ($isHighIntent) {
            $score += 15;
            $reasons[] = "High-ticket commercial category with substantial average customer lifetime value.";
        }

        // 3. Contact Discoverability (Can the agency actually reach them?)
        $hasEmail = !empty($leadData['email']) && $leadData['email'] !== 'Not found';
        $hasPhone = !empty($leadData['phone']) && $leadData['phone'] !== 'Not found';
        $hasContactPage = !empty($leadData['contact_page']) && $leadData['contact_page'] !== 'Not found';

        if ($hasEmail && $hasPhone) {
            $score += 15;
            $reasons[] = "Direct verified email and telephone contact channels identified for multi-touch outreach.";
        } elseif ($hasEmail || $hasContactPage) {
            $score += 8;
            $reasons[] = "Direct contact channel available for outreach pitch delivery.";
        } else {
            $score -= 15;
            $reasons[] = "Limited public contact channels detected; requires manual contact discovery.";
        }

        // 4. Local SEO Opportunity
        $localScore = $categoryScores['local'] ?? 50;
        if ($localScore < 50) {
            $score += 10;
            $reasons[] = "Missing Schema.org LocalBusiness markup and local map signals present quick-win ranking opportunities.";
        }

        // 5. Content Expansion Opportunity
        $contentScore = $categoryScores['content'] ?? 50;
        if ($contentScore < 50) {
            $score += 8;
            $reasons[] = "Thin service page copy and absence of topical content hub provide strong content marketing angles.";
        }

        // Clamp score 0 - 100
        $finalScore = max(10, min(100, $score));

        // Determine Opportunity Level
        if ($finalScore >= 75) {
            $opportunityLevel = 'High';
        } elseif ($finalScore >= 50) {
            $opportunityLevel = 'Medium';
        } else {
            $opportunityLevel = 'Low';
        }

        return [
            'lead_score' => $finalScore,
            'opportunity_level' => $opportunityLevel,
            'reasons' => $reasons,
            'reasons_text' => implode("\n• ", $reasons)
        ];
    }
}
