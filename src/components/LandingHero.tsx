import React, { useState } from 'react';
import { 
  Search, 
  Sparkles, 
  CheckCircle2, 
  AlertTriangle, 
  XCircle, 
  Globe, 
  ArrowRight, 
  ShieldCheck, 
  Mail, 
  TrendingUp, 
  Zap,
  BarChart3,
  ExternalLink
} from 'lucide-react';
import { runInstantAudit } from '../services/seoEngine';
import { AuditIssue, AuditScores } from '../types';

interface LandingHeroProps {
  onStartSearch: () => void;
  onViewPipeline: () => void;
  onOpenPitch: (domain: string, score: number, issues: AuditIssue[]) => void;
}

export const LandingHero: React.FC<LandingHeroProps> = ({
  onStartSearch,
  onViewPipeline,
  onOpenPitch
}) => {
  const [urlInput, setUrlInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [auditResult, setAuditResult] = useState<{
    seoScore: number;
    domain: string;
    scores: AuditScores;
    issues: AuditIssue[];
  } | null>(null);

  const handleAudit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!urlInput.trim()) return;

    setLoading(true);
    setTimeout(() => {
      const res = runInstantAudit(urlInput.trim());
      setAuditResult(res);
      setLoading(false);
    }, 700);
  };

  const getScoreColor = (score: number) => {
    if (score >= 80) return 'text-emerald-600 bg-emerald-50 border-emerald-200';
    if (score >= 60) return 'text-amber-600 bg-amber-50 border-amber-200';
    return 'text-red-600 bg-red-50 border-red-200';
  };

  return (
    <div className="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      {/* Hero Headline */}
      <div className="text-center max-w-3xl mx-auto mb-10">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-semibold mb-4 shadow-xs">
          <Zap className="w-3.5 h-3.5 text-blue-600" />
          <span>Real-Time B2B Prospect Intelligence & Technical SEO Auditor</span>
        </div>
        <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
          Uncover Local Clients With <span className="text-blue-600">Critical SEO Weaknesses</span>
        </h1>
        <p className="text-lg text-slate-600 leading-relaxed mb-6">
          Instantly audit any commercial website, uncover high-impact ranking flaws, calculate agency conversion opportunity scores, and generate hyper-personalized pitch proposals that win retainers.
        </p>
      </div>

      {/* Instant Audit Input Card */}
      <div className="max-w-2xl mx-auto bg-white rounded-2xl p-3 sm:p-4 shadow-xl shadow-slate-200/50 border border-slate-200 mb-12">
        <form onSubmit={handleAudit} className="flex flex-col sm:flex-row gap-2">
          <div className="relative flex-grow">
            <Globe className="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" />
            <input
              id="heroAuditInput"
              type="text"
              value={urlInput}
              onChange={(e) => setUrlInput(e.target.value)}
              placeholder="Enter target website (e.g. apexcosmeticdentistry.com)"
              className="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-slate-900 font-medium placeholder-slate-400"
              required
            />
          </div>
          <button
            id="btnRunAuditHero"
            type="submit"
            disabled={loading}
            className="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer disabled:opacity-75"
          >
            {loading ? (
              <>
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Scanning 40+ Signals...</span>
              </>
            ) : (
              <>
                <Zap className="w-4 h-4" />
                <span>Run Free Audit</span>
              </>
            )}
          </button>
        </form>
        <p className="text-xs text-slate-500 mt-2 px-1 flex items-center gap-1.5">
          <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
          Safe crawler checks schema JSON-LD, SSL encryption, mobile viewport, H1/H2, meta description & contact discoverability.
        </p>
      </div>

      {/* Audit Result Display */}
      {auditResult && (
        <div id="instantAuditResultsBox" className="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-lg p-6 mb-12 animate-fade-in">
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
              <span className="text-xs font-bold uppercase tracking-wider text-slate-500">Live Crawl Report</span>
              <h3 className="text-2xl font-bold text-slate-900 mt-0.5 flex items-center gap-2">
                {auditResult.domain}
                <a 
                  href={`https://${auditResult.domain}`} 
                  target="_blank" 
                  rel="noopener noreferrer" 
                  className="text-slate-400 hover:text-blue-600 transition-colors"
                >
                  <ExternalLink className="w-4 h-4" />
                </a>
              </h3>
            </div>
            <div className="flex items-center gap-3">
              <div className={`px-4 py-2 rounded-xl border text-center font-bold ${getScoreColor(auditResult.seoScore)}`}>
                <div className="text-2xl">{auditResult.seoScore}/100</div>
                <div className="text-[10px] uppercase tracking-wide">Overall SEO Score</div>
              </div>
              <button
                onClick={() => onOpenPitch(auditResult.domain, auditResult.seoScore, auditResult.issues)}
                className="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer"
              >
                <Sparkles className="w-3.5 h-3.5" />
                Generate Cold Pitch
              </button>
            </div>
          </div>

          {/* Sub-Score Breakdown */}
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 my-6">
            {Object.entries(auditResult.scores).map(([key, val]) => (
              <div key={key} className="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                <span className="text-[11px] font-semibold text-slate-500 uppercase block mb-1">
                  {key}
                </span>
                <span className="text-lg font-extrabold text-slate-900">
                  {val}%
                </span>
                <div className="w-full bg-slate-200 rounded-full h-1.5 mt-1.5 overflow-hidden">
                  <div 
                    className="bg-blue-600 h-1.5 rounded-full" 
                    style={{ width: `${val}%` }}
                  ></div>
                </div>
              </div>
            ))}
          </div>

          {/* Issues List */}
          <div>
            <h4 className="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3 flex items-center gap-2">
              <AlertTriangle className="w-4 h-4 text-amber-500" />
              Detailed Technical Findings & Recommendations ({auditResult.issues.length})
            </h4>
            <div className="space-y-2.5">
              {auditResult.issues.map((iss, idx) => (
                <div 
                  key={idx} 
                  className={`p-3.5 rounded-xl border flex items-start gap-3 ${
                    iss.severity === 'Critical' 
                      ? 'bg-red-50/60 border-red-200' 
                      : iss.severity === 'Warning' 
                        ? 'bg-amber-50/60 border-amber-200' 
                        : 'bg-emerald-50/60 border-emerald-200'
                  }`}
                >
                  {iss.severity === 'Critical' ? (
                    <XCircle className="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                  ) : iss.severity === 'Warning' ? (
                    <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                  ) : (
                    <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                  )}
                  <div className="flex-grow">
                    <div className="flex items-center justify-between">
                      <span className="text-sm font-bold text-slate-900">{iss.title}</span>
                      <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded-md ${
                        iss.severity === 'Critical' 
                          ? 'bg-red-100 text-red-800' 
                          : iss.severity === 'Warning' 
                            ? 'bg-amber-100 text-amber-800' 
                            : 'bg-emerald-100 text-emerald-800'
                      }`}>
                        {iss.severity}
                      </span>
                    </div>
                    <p className="text-xs text-slate-600 mt-1">{iss.explanation}</p>
                    <p className="text-xs text-slate-800 font-semibold mt-1">
                      <span className="text-blue-700 font-bold">Fix:</span> {iss.recommendation}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Feature Teasers */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mt-6">
        <div 
          onClick={onStartSearch}
          className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        >
          <div className="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
            <Search className="w-6 h-6" />
          </div>
          <h3 className="text-lg font-bold text-slate-900 mb-1 flex items-center justify-between">
            Multi-Metro Lead Discovery
            <ArrowRight className="w-4 h-4 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" />
          </h3>
          <p className="text-sm text-slate-600">
            Target high-ticket local niches (Cosmetic Dentists, Roofing, Lawyers, MedSpas) across any city worldwide.
          </p>
        </div>

        <div 
          onClick={onViewPipeline}
          className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        >
          <div className="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
            <BarChart3 className="w-6 h-6" />
          </div>
          <h3 className="text-lg font-bold text-slate-900 mb-1 flex items-center justify-between">
            0-100 Opportunity Scorer
            <ArrowRight className="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" />
          </h3>
          <p className="text-sm text-slate-600">
            Identifies businesses with the highest commercial need so your outreach closes at 3x higher reply rates.
          </p>
        </div>

        <div 
          onClick={onViewPipeline}
          className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        >
          <div className="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
            <Mail className="w-6 h-6" />
          </div>
          <h3 className="text-lg font-bold text-slate-900 mb-1 flex items-center justify-between">
            AI Pitch & Proposal CRM
            <ArrowRight className="w-4 h-4 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" />
          </h3>
          <p className="text-sm text-slate-600">
            Manage your pipeline status, export leads to CSV, and send personalized audit-backed proposals in 1 click.
          </p>
        </div>
      </div>
    </div>
  );
};
