export interface UserProfile {
  uid: string;
  email: string;
  name: string;
  role: 'admin' | 'user';
  createdAt: string;
}

export interface AuditIssue {
  category: 'TECHNICAL SEO' | 'ON-PAGE CONTENT' | 'LOCAL & SCHEMA' | 'CONVERSION & UX';
  title: string;
  severity: 'Passed' | 'Warning' | 'Critical';
  explanation: string;
  recommendation: string;
  affectedUrl?: string;
}

export interface AuditScores {
  technical: number;
  onpage: number;
  content: number;
  local: number;
  authority: number;
  social: number;
}

export interface Lead {
  id: string;
  userId: string;
  businessName: string;
  website: string;
  domain: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state?: string;
  country: string;
  postalCode?: string;
  category: string;
  description: string;
  status: 'New' | 'Audited' | 'Contacted' | 'Replied' | 'Negotiating' | 'Closed Won' | 'Unresponsive';
  seoScore: number;
  leadScore: number;
  opportunityLevel: 'High' | 'Medium' | 'Low';
  opportunityReasons: string;
  scores: AuditScores;
  issues: AuditIssue[];
  contactPage?: string;
  aboutPage?: string;
  facebook?: string;
  instagram?: string;
  linkedin?: string;
  twitter?: string;
  notes?: string;
  createdAt: string;
  updatedAt?: string;
  isDemo?: boolean;
}

export interface SearchQuery {
  keyword: string;
  city: string;
  country: string;
  targetCount: number;
  minLeadScore?: number;
}
