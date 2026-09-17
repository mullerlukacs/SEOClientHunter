import React, { useState } from 'react';
import { 
  X, 
  Sparkles, 
  Copy, 
  Check, 
  Mail, 
  ExternalLink,
  Send
} from 'lucide-react';
import { Lead } from '../types';
import { generatePitchProposal } from '../services/seoEngine';

interface PitchModalProps {
  lead: Lead | null;
  onClose: () => void;
}

export const PitchModal: React.FC<PitchModalProps> = ({ lead, onClose }) => {
  const [copied, setCopied] = useState(false);

  if (!lead) return null;

  const pitch = generatePitchProposal(lead);

  const handleCopy = () => {
    const fullText = `Subject: ${pitch.subject}\n\n${pitch.body}`;
    navigator.clipboard.writeText(fullText);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const mailtoUrl = `mailto:${encodeURIComponent(lead.email)}?subject=${encodeURIComponent(pitch.subject)}&body=${encodeURIComponent(pitch.body)}`;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-fade-in">
      <div className="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
        {/* Header */}
        <div className="p-6 border-b border-slate-100 flex items-start justify-between bg-indigo-50/50">
          <div>
            <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">
              AI Audit-Backed Outreach Proposal
            </span>
            <h3 className="text-xl font-black text-slate-900 mt-1 flex items-center gap-2">
              Proposal for {lead.businessName}
            </h3>
            <p className="text-xs text-slate-500">
              Personalized cold email emphasizing {lead.domain}'s SEO defects in {lead.city}
            </p>
          </div>

          <button
            onClick={onClose}
            className="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-6 overflow-y-auto space-y-4">
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
              Subject Line
            </label>
            <input
              type="text"
              readOnly
              value={pitch.subject}
              className="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900"
            />
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
              Email Proposal Body
            </label>
            <textarea
              readOnly
              rows={12}
              value={pitch.body}
              className="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 leading-relaxed resize-none focus:outline-none"
            />
          </div>
        </div>

        {/* Footer */}
        <div className="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
          <span className="text-xs text-slate-500">
            Target Contact: <span className="font-mono text-slate-700">{lead.email}</span>
          </span>

          <div className="flex items-center gap-2">
            <button
              onClick={handleCopy}
              className="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer"
            >
              {copied ? (
                <>
                  <Check className="w-3.5 h-3.5 text-emerald-600" />
                  <span className="text-emerald-700">Copied!</span>
                </>
              ) : (
                <>
                  <Copy className="w-3.5 h-3.5" />
                  <span>Copy to Clipboard</span>
                </>
              )}
            </button>

            <a
              href={mailtoUrl}
              className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5"
            >
              <Send className="w-3.5 h-3.5" />
              <span>Open in Email App</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};
