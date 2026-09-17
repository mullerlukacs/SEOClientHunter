import React from 'react';
import { 
  X, 
  ExternalLink, 
  CheckCircle2, 
  AlertTriangle, 
  XCircle, 
  Globe, 
  Sparkles,
  Phone,
  Mail
} from 'lucide-react';
import { Lead } from '../types';

interface AuditReportModalProps {
  lead: Lead | null;
  onClose: () => void;
  onOpenPitch: (lead: Lead) => void;
}

export const AuditReportModal: React.FC<AuditReportModalProps> = ({
  lead,
  onClose,
  onOpenPitch
}) => {
  if (!lead) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-fade-in">
      <div className="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
        {/* Header */}
        <div className="p-6 border-b border-slate-100 flex items-start justify-between bg-slate-50/50">
          <div>
            <div className="flex items-center gap-2 mb-1">
              <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-700">
                Technical Crawl Report
              </span>
              <span className={`text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded ${
                lead.seoScore < 60 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'
              }`}>
                Score: {lead.seoScore}/100
              </span>
            </div>
            <h3 className="text-xl font-black text-slate-900 flex items-center gap-2">
              {lead.businessName}
              <a
                href={lead.website}
                target="_blank"
                rel="noopener noreferrer"
                className="text-slate-400 hover:text-blue-600 transition-colors"
              >
                <ExternalLink className="w-4 h-4" />
              </a>
            </h3>
            <p className="text-xs text-slate-500">{lead.domain} • {lead.city}, {lead.country}</p>
          </div>

          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-6 overflow-y-auto space-y-6">
          {/* Sub Scores */}
          <div>
            <h4 className="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
              6-Tier Technical SEO Health Breakdown
            </h4>
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
              {Object.entries(lead.scores || {}).map(([key, val]) => (
                <div key={key} className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                  <span className="text-[11px] font-bold text-slate-500 uppercase block mb-1">
                    {key}
                  </span>
                  <div className="flex items-center justify-between">
                    <span className="text-lg font-black text-slate-900">{val}%</span>
                    <span className="text-[10px] text-slate-400 font-semibold">Weight: 20%</span>
                  </div>
                  <div className="w-full bg-slate-200 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div className="bg-blue-600 h-1.5 rounded-full" style={{ width: `${val}%` }}></div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Audit Issues */}
          <div>
            <h4 className="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
              Detected Ranking Defects & Recommendations ({lead.issues?.length || 0})
            </h4>
            <div className="space-y-2.5">
              {lead.issues?.map((iss, idx) => (
                <div
                  key={idx}
                  className={`p-3.5 rounded-2xl border flex items-start gap-3 ${
                    iss.severity === 'Critical'
                      ? 'bg-red-50/50 border-red-200'
                      : iss.severity === 'Warning'
                        ? 'bg-amber-50/50 border-amber-200'
                        : 'bg-emerald-50/50 border-emerald-200'
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
                      <span className="text-xs font-bold text-slate-900">{iss.title}</span>
                      <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded ${
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

        {/* Footer */}
        <div className="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
          <div className="flex items-center gap-3 text-xs font-mono text-slate-600">
            <span className="flex items-center gap-1">
              <Phone className="w-3.5 h-3.5 text-slate-400" />
              {lead.phone}
            </span>
            <span className="flex items-center gap-1">
              <Mail className="w-3.5 h-3.5 text-slate-400" />
              {lead.email}
            </span>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={onClose}
              className="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 rounded-xl"
            >
              Close
            </button>
            <button
              onClick={() => {
                onClose();
                onOpenPitch(lead);
              }}
              className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5"
            >
              <Sparkles className="w-3.5 h-3.5" />
              Generate Pitch Email
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
