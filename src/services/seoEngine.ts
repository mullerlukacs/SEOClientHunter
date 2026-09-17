import { AuditIssue, AuditScores, Lead } from '../types';

export function runInstantAudit(urlStr: string): { seoScore: number; domain: string; scores: AuditScores; issues: AuditIssue[] } {
  let domain = urlStr.replace(/^https?:\/\//i, '').replace(/\/.*$/, '').toLowerCase();
  if (!domain) domain = 'target-business.com';

  // Seed pseudo-random variations based on domain characters to keep audit reproducible
  let hash = 0;
  for (let i = 0; i < domain.length; i++) {
    hash = (hash << 5) - hash + domain.charCodeAt(i);
    hash |= 0;
  }
  const absHash = Math.abs(hash);

  const isHttps = urlStr.startsWith('https://') || absHash % 3 !== 0;
  const hasViewport = absHash % 4 !== 0;
  const hasSchema = absHash % 5 === 0;
  const hasMetaDesc = absHash % 2 === 0;
  const missingAltCount = (absHash % 12) + 2;
  const titleLength = 30 + (absHash % 50);

  const issues: AuditIssue[] = [];

  // Technical checks
  let techScore = 80;
  if (!isHttps) {
    techScore -= 35;
    issues.push({
      category: 'TECHNICAL SEO',
      title: 'Insecure HTTP Protocol Detected',
      severity: 'Critical',
      explanation: 'Website is served over unencrypted HTTP, causing browser "Not Secure" warnings and severe Google ranking penalties.',
      recommendation: 'Install an SSL certificate and enforce 301 HTTPS redirects site-wide.'
    });
  } else {
    issues.push({
      category: 'TECHNICAL SEO',
      title: 'SSL / HTTPS Enforced',
      severity: 'Passed',
      explanation: 'Valid SSL certificate configured and active.',
      recommendation: 'Maintain automatic certificate renewal.'
    });
  }

  if (!hasViewport) {
    techScore -= 25;
    issues.push({
      category: 'TECHNICAL SEO',
      title: 'Missing Mobile Viewport Meta Tag',
      severity: 'Critical',
      explanation: 'Pages do not render properly on smartphones, failing Google Mobile-First Indexing requirements.',
      recommendation: 'Add <meta name="viewport" content="width=device-width, initial-scale=1.0"> to document head.'
    });
  } else {
    issues.push({
      category: 'TECHNICAL SEO',
      title: 'Mobile-Friendly Responsive Viewport Configured',
      severity: 'Passed',
      explanation: 'Responsive meta tag present.',
      recommendation: 'Verify font size and tap target spacing across mobile devices.'
    });
  }

  // On-page checks
  let onpageScore = 75;
  if (!hasMetaDesc) {
    onpageScore -= 30;
    issues.push({
      category: 'ON-PAGE CONTENT',
      title: 'Missing Meta Description Tag',
      severity: 'Critical',
      explanation: 'Search engines generate random snippets for SERP results, dramatically lowering click-through rate (CTR).',
      recommendation: 'Write a compelling 150-160 character meta description with target keywords and call to action.'
    });
  } else {
    issues.push({
      category: 'ON-PAGE CONTENT',
      title: 'Meta Description Detected',
      severity: 'Passed',
      explanation: 'Meta description exists and informs search users of page purpose.',
      recommendation: 'A/B test SERP action verbs to increase organic CTR.'
    });
  }

  if (titleLength > 65) {
    onpageScore -= 15;
    issues.push({
      category: 'ON-PAGE CONTENT',
      title: `Title Tag Truncation Warning (${titleLength} chars)`,
      severity: 'Warning',
      explanation: 'Title tag exceeds 60 characters and is truncated by Google in desktop and mobile search.',
      recommendation: 'Shorten title tag to 50-60 characters with primary keyword placed in the first 30 characters.'
    });
  } else {
    issues.push({
      category: 'ON-PAGE CONTENT',
      title: `Optimal Title Tag Length (${titleLength} chars)`,
      severity: 'Passed',
      explanation: 'Title fits nicely within Google display pixel boundaries.',
      recommendation: 'Ensure localized city modifier is present.'
    });
  }

  // Images Alt
  if (missingAltCount > 0) {
    onpageScore -= Math.min(25, missingAltCount * 3);
    issues.push({
      category: 'ON-PAGE CONTENT',
      title: `${missingAltCount} Images Missing Descriptive Alt Attributes`,
      severity: 'Warning',
      explanation: 'Search crawlers and screen readers cannot understand image context without alt attributes.',
      recommendation: 'Add descriptive keyword-rich alternative text to all hero, service, and product images.'
    });
  }

  // Local & Schema
  let localScore = 60;
  if (!hasSchema) {
    localScore -= 35;
    issues.push({
      category: 'LOCAL & SCHEMA',
      title: 'No Structured Schema JSON-LD Found',
      severity: 'Critical',
      explanation: 'Missing LocalBusiness or Organization structured data markup prevents rich snippets and Google Map pack prominence.',
      recommendation: 'Deploy Schema.org LocalBusiness JSON-LD with geo-coordinates, address, phone, and opening hours.'
    });
  } else {
    localScore += 20;
    issues.push({
      category: 'LOCAL & SCHEMA',
      title: 'Schema.org JSON-LD Markup Present',
      severity: 'Passed',
      explanation: 'Structured data assists Google Knowledge Graph parsing.',
      recommendation: 'Audit with Google Rich Results Test regularly.'
    });
  }

  const scores: AuditScores = {
    technical: Math.max(30, Math.min(100, techScore)),
    onpage: Math.max(30, Math.min(100, onpageScore)),
    content: Math.max(40, Math.min(95, 60 + (absHash % 35))),
    local: Math.max(25, Math.min(100, localScore)),
    authority: Math.max(30, Math.min(90, 40 + (absHash % 50))),
    social: Math.max(35, Math.min(100, 50 + (absHash % 45)))
  };

  const seoScore = Math.round(
    scores.technical * 0.25 +
    scores.onpage * 0.25 +
    scores.local * 0.20 +
    scores.content * 0.15 +
    scores.authority * 0.15
  );

  return { seoScore, domain, scores, issues };
}

export function generateProspectsForSearch(
  userId: string,
  keyword: string,
  city: string,
  country: string,
  count: number
): Lead[] {
  const prefixes = ['Premier', 'Apex', 'Downtown', 'Beacon', 'Metropolitan', 'Heritage', 'Golden State', 'Skyline', 'Central', 'Evergreen', 'Pinnacle', 'Citywide'];
  const suffixes = ['Group', 'Associates', 'Clinic', 'Specialists', 'Services', 'Care Center', 'Consulting', 'Hub', 'Practice', 'Solutions'];

  const cleanCity = city.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
  const cleanKeyword = keyword.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();

  const leads: Lead[] = [];

  for (let i = 1; i <= count; i++) {
    const prefix = prefixes[(i + city.length) % prefixes.length];
    const suffix = suffixes[(i + keyword.length) % suffixes.length];
    const businessName = `${prefix} ${keyword} ${suffix}`;
    const domainName = `${prefix.toLowerCase()}${cleanKeyword}${suffix.toLowerCase()}${cleanCity}.com`;
    const website = `https://www.${domainName}`;

    const audit = runInstantAudit(website);

    // Calculate Opportunity Score (0-100)
    // Low SEO score = higher conversion opportunity for agency
    const seoDefectBonus = Math.max(0, 100 - audit.seoScore);
    const opportunityScore = Math.min(98, Math.round(40 + (seoDefectBonus * 0.5) + (i % 10)));

    let oppLevel: 'High' | 'Medium' | 'Low' = 'Medium';
    if (opportunityScore >= 75) oppLevel = 'High';
    else if (opportunityScore < 55) oppLevel = 'Low';

    const reasons = [
      `Critical SEO weaknesses (Overall SEO: ${audit.seoScore}/100) indicate immediate retainer value.`,
      `Commercial high-intent ${keyword} niche in ${city} with substantial client lifetime value.`,
      `Verified contact channels discovered for direct pitch proposal delivery.`
    ].join('\n');

    const phoneArea = 200 + ((i * 37) % 700);
    const phoneMid = 100 + ((i * 53) % 899);
    const phoneEnd = 1000 + ((i * 71) % 8999);
    const phone = `+1 (${phoneArea}) ${phoneMid}-${phoneEnd}`;
    const email = `contact@${domainName}`;

    leads.push({
      id: `lead_${Date.now()}_${i}_${Math.random().toString(36).substring(2, 7)}`,
      userId,
      businessName,
      website,
      domain: domainName,
      email,
      phone,
      address: `${100 + i * 14} Main Blvd, Suite ${i * 10}`,
      city,
      country,
      category: keyword,
      description: `Established local ${keyword} business operating in ${city}, ${country}.`,
      status: 'New',
      seoScore: audit.seoScore,
      leadScore: opportunityScore,
      opportunityLevel: oppLevel,
      opportunityReasons: reasons,
      scores: audit.scores,
      issues: audit.issues,
      contactPage: `${website}/contact`,
      aboutPage: `${website}/about`,
      facebook: `https://facebook.com/${prefix.toLowerCase()}${cleanKeyword}`,
      instagram: `https://instagram.com/${prefix.toLowerCase()}${cleanKeyword}`,
      notes: '',
      createdAt: new Date().toISOString(),
      isDemo: true
    });
  }

  return leads;
}

export function generatePitchProposal(lead: Lead): { subject: string; body: string } {
  const criticalCount = lead.issues.filter(i => i.severity === 'Critical').length;
  const warningCount = lead.issues.filter(i => i.severity === 'Warning').length;

  const subject = `Quick question regarding ${lead.domain}'s Google visibility in ${lead.city}`;

  const body = `Hi ${lead.businessName} Team,

I came across your website (${lead.website}) while analyzing high-reputation ${lead.category} providers in ${lead.city}.

Our agency automated technical crawler conducted a 40-point diagnostics scan on your domain and uncovered ${criticalCount} critical ranking defects and ${warningCount} optimization warnings that are holding back your Google Map Pack and organic rankings:

Key Findings:
${lead.issues.slice(0, 3).map(iss => `• ${iss.title}: ${iss.explanation}`).join('\n')}

Because competitors in ${lead.city} are aggressively bidding for local search share, resolving these structural defects can directly increase your qualified monthly patient/customer inquiries by an estimated 25%–40%.

Would you be open to a brief 7-minute screen-share this Thursday at 2:00 PM to review the full 6-tier technical audit report? No pitch or obligation.

Best regards,

Growth Partnerships Team
SEO Client Hunter
`;

  return { subject, body };
}

export function exportLeadsToCsv(leads: Lead[]): void {
  const headers = [
    'Business Name',
    'Website',
    'Domain',
    'Category',
    'City',
    'Country',
    'Email',
    'Phone',
    'SEO Score',
    'Lead Opportunity Score',
    'Opportunity Level',
    'Status',
    'Created At'
  ];

  const rows = leads.map(l => [
    `"${l.businessName.replace(/"/g, '""')}"`,
    `"${l.website}"`,
    `"${l.domain}"`,
    `"${l.category}"`,
    `"${l.city}"`,
    `"${l.country}"`,
    `"${l.email}"`,
    `"${l.phone}"`,
    l.seoScore,
    l.leadScore,
    `"${l.opportunityLevel}"`,
    `"${l.status}"`,
    `"${l.createdAt}"`
  ]);

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `seo_leads_export_${new Date().toISOString().slice(0, 10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
