import React, { useState } from 'react';
import { 
  Search, 
  MapPin, 
  Layers, 
  Sparkles, 
  Phone, 
  Mail, 
  ExternalLink, 
  CheckCircle2, 
  Globe, 
  Filter, 
  FileText, 
  Save, 
  BookmarkCheck,
  TrendingUp,
  AlertCircle
} from 'lucide-react';
import { Lead } from '../types';
import { generateProspectsForSearch } from '../services/seoEngine';

interface LeadDiscoveryViewProps {
  userId: string;
  onSaveLead: (lead: Lead) => Promise<void>;
  onBatchSaveLeads: (leads: Lead[]) => Promise<void>;
  onOpenAudit: (lead: Lead) => void;
  onOpenPitch: (lead: Lead) => void;
}

export const LeadDiscoveryView: React.FC<LeadDiscoveryViewProps> = ({
  userId,
  onSaveLead,
  onBatchSaveLeads,
  onOpenAudit,
  onOpenPitch
}) => {
  const [keyword, setKeyword] = useState('Cosmetic Dentist');
  const [city, setCity] = useState('Dallas');
  const [country, setCountry] = useState('United States');
  const [count, setCount] = useState<number>(6);
  const [searching, setSearching] = useState(false);
  const [progressStep, setProgressStep] = useState('');
  const [discoveredLeads, setDiscoveredLeads] = useState<Lead[]>([]);
  const [savedIds, setSavedIds] = useState<Set<string>>(new Set());

  const nichePresets = ['Cosmetic Dentist', 'Commercial Roofing', 'Personal Injury Lawyer', 'Emergency Plumber', 'HVAC Contractor', 'MedSpa Clinic'];
  const cityPresets = ['Dallas', 'New York', 'Chicago', 'Miami', 'Austin', 'London', 'Toronto', 'Dubai'];

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (!keyword.trim() || !city.trim()) return;

    setSearching(true);
    setDiscoveredLeads([]);
    setProgressStep('Connecting to search directory & geo-locating targets...');

    setTimeout(() => {
      setProgressStep(`Discovered ${count} prospective domains. Initiating 40-point technical crawl...`);
    }, 600);

    setTimeout(() => {
      setProgressStep('Analyzing Schema.org JSON-LD, meta tags, and mobile readiness...');
    }, 1200);

    setTimeout(() => {
      setProgressStep('Calculating 0-100 commercial conversion opportunity scores...');
    }, 1700);

    setTimeout(() => {
      const results = generateProspectsForSearch(userId, keyword.trim(), city.trim(), country.trim(), count);
      setDiscoveredLeads(results);
      setSearching(false);
      setProgressStep('');
    }, 2200);
  };

  const handleSaveSingle = async (lead: Lead) => {
    await onSaveLead(lead);
    setSavedIds(prev => new Set(prev).add(lead.id));
  };

  const handleSaveAll = async () => {
    if (discoveredLeads.length === 0) return;
    await onBatchSaveLeads(discoveredLeads);
    const newSet = new Set<string>();
    discoveredLeads.forEach(l => newSet.add(l.id));
    setSavedIds(newSet);
  };

  return (
    <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <h2 className="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
          <Search className="w-7 h-7 text-blue-600" />
          Lead Discovery & Technical Prospect Hunter
        </h2>
        <p className="text-sm text-slate-600 mt-1">
          Specify a commercial niche and target geographic market to discover high-value prospects with severe SEO defects.
        </p>
      </div>

      {/* Search Filter Card */}
      <div className="bg-white rounded-2xl p-6 border border-slate-200 shadow-md mb-8">
        <form onSubmit={handleSearch} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {/* Keyword / Niche */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Target Niche / Service
              </label>
              <div className="relative">
                <Search className="w-4 h-4 text-slate-400 absolute left-3 top-3.5" />
                <input
                  id="searchKeywordInput"
                  type="text"
                  value={keyword}
                  onChange={(e) => setKeyword(e.target.value)}
                  placeholder="e.g. Cosmetic Dentist"
                  className="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-900"
                  required
                />
              </div>
            </div>

            {/* City */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                City / Metro Area
              </label>
              <div className="relative">
                <MapPin className="w-4 h-4 text-slate-400 absolute left-3 top-3.5" />
                <input
                  id="searchCityInput"
                  type="text"
                  value={city}
                  onChange={(e) => setCity(e.target.value)}
                  placeholder="e.g. Dallas"
                  className="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-900"
                  required
                />
              </div>
            </div>

            {/* Country */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Country
              </label>
              <div className="relative">
                <Globe className="w-4 h-4 text-slate-400 absolute left-3 top-3.5" />
                <input
                  id="searchCountryInput"
                  type="text"
                  value={country}
                  onChange={(e) => setCountry(e.target.value)}
                  placeholder="e.g. United States"
                  className="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-900"
                  required
                />
              </div>
            </div>

            {/* Count & Submit */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Batch Count
              </label>
              <div className="flex gap-2">
                <select
                  id="searchCountSelect"
                  value={count}
                  onChange={(e) => setCount(Number(e.target.value))}
                  className="w-24 px-3 py-2.5 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900"
                >
                  <option value={5}>5 Leads</option>
                  <option value={8}>8 Leads</option>
                  <option value={12}>12 Leads</option>
                  <option value={20}>20 Leads</option>
                </select>
                <button
                  id="btnExecuteSearch"
                  type="submit"
                  disabled={searching}
                  className="flex-grow px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-75"
                >
                  <Search className="w-4 h-4" />
                  <span>Hunt Leads</span>
                </button>
              </div>
            </div>
          </div>

          {/* Quick Presets */}
          <div className="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider">Quick Niches:</span>
            {nichePresets.map((p) => (
              <button
                key={p}
                type="button"
                onClick={() => setKeyword(p)}
                className={`text-xs px-2.5 py-1 rounded-lg border transition-colors ${
                  keyword === p 
                    ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' 
                    : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'
                }`}
              >
                {p}
              </button>
            ))}
          </div>

          <div className="flex flex-wrap items-center gap-2">
            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider">Quick Cities:</span>
            {cityPresets.map((c) => (
              <button
                key={c}
                type="button"
                onClick={() => setCity(c)}
                className={`text-xs px-2.5 py-1 rounded-lg border transition-colors ${
                  city === c 
                    ? 'bg-blue-50 border-blue-300 text-blue-700 font-bold' 
                    : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'
                }`}
              >
                {c}
              </button>
            ))}
          </div>
        </form>
      </div>

      {/* Crawl In Progress Indicator */}
      {searching && (
        <div className="max-w-xl mx-auto my-12 p-8 bg-white border border-blue-200 rounded-2xl shadow-lg text-center animate-pulse">
          <div className="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <h4 className="text-lg font-bold text-slate-900 mb-1">
            Hunting & Crawling {count} Businesses in {city}...
          </h4>
          <p className="text-xs font-semibold text-blue-600">{progressStep}</p>
        </div>
      )}

      {/* Discovered Results */}
      {discoveredLeads.length > 0 && !searching && (
        <div>
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 mb-6 border-b border-slate-200 gap-3">
            <div>
              <h3 className="text-xl font-bold text-slate-900">
                Discovered Prospects ({discoveredLeads.length})
              </h3>
              <p className="text-xs text-slate-500">
                Targeted: {keyword} in {city}, {country}
              </p>
            </div>
            <button
              id="btnSaveAllFirestore"
              onClick={handleSaveAll}
              className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer"
            >
              <Save className="w-4 h-4" />
              Save All to Cloud CRM
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {discoveredLeads.map((lead) => {
              const isSaved = savedIds.has(lead.id);

              return (
                <div 
                  key={lead.id}
                  className="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all p-5 flex flex-col justify-between"
                >
                  <div>
                    {/* Top bar with opportunity badge and SEO score */}
                    <div className="flex items-center justify-between mb-3">
                      <span className={`text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full border ${
                        lead.opportunityLevel === 'High' 
                          ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                          : 'bg-amber-50 text-amber-700 border-amber-200'
                      }`}>
                        {lead.opportunityLevel} Opp ({lead.leadScore}/100)
                      </span>

                      <span className={`text-xs font-extrabold px-2.5 py-1 rounded-lg border ${
                        lead.seoScore < 60 
                          ? 'bg-red-50 text-red-700 border-red-200' 
                          : lead.seoScore < 75 
                            ? 'bg-amber-50 text-amber-700 border-amber-200' 
                            : 'bg-blue-50 text-blue-700 border-blue-200'
                      }`}>
                        SEO: {lead.seoScore}/100
                      </span>
                    </div>

                    {/* Business Title & Domain */}
                    <h4 className="text-base font-bold text-slate-900 line-clamp-1 mb-1">
                      {lead.businessName}
                    </h4>

                    <a
                      href={lead.website}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-3 truncate"
                    >
                      <Globe className="w-3.5 h-3.5 shrink-0" />
                      <span>{lead.domain}</span>
                      <ExternalLink className="w-3 h-3 shrink-0" />
                    </a>

                    <p className="text-xs text-slate-600 line-clamp-2 mb-4">
                      {lead.description}
                    </p>

                    {/* Contact Badges */}
                    <div className="space-y-1.5 mb-4 text-xs">
                      <div className="flex items-center gap-2 text-slate-700">
                        <Phone className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <span className="font-mono">{lead.phone}</span>
                      </div>
                      <div className="flex items-center gap-2 text-slate-700 truncate">
                        <Mail className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <span className="font-mono truncate">{lead.email}</span>
                      </div>
                      <div className="flex items-center gap-2 text-slate-500">
                        <MapPin className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <span className="truncate">{lead.address}</span>
                      </div>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <div className="grid grid-cols-2 gap-2">
                      <button
                        onClick={() => onOpenAudit(lead)}
                        className="px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1 cursor-pointer"
                      >
                        <FileText className="w-3.5 h-3.5 text-blue-600" />
                        Audit ({lead.issues.length})
                      </button>

                      <button
                        onClick={() => onOpenPitch(lead)}
                        className="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1 cursor-pointer"
                      >
                        <Sparkles className="w-3.5 h-3.5 text-indigo-600" />
                        Pitch
                      </button>
                    </div>

                    <button
                      onClick={() => handleSaveSingle(lead)}
                      disabled={isSaved}
                      className={`w-full py-2 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer ${
                        isSaved
                          ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-default'
                          : 'bg-blue-600 hover:bg-blue-700 text-white shadow-xs'
                      }`}
                    >
                      {isSaved ? (
                        <>
                          <BookmarkCheck className="w-4 h-4 text-emerald-600" />
                          <span>Saved in Firestore</span>
                        </>
                      ) : (
                        <>
                          <Save className="w-4 h-4" />
                          <span>Save to Pipeline</span>
                        </>
                      )}
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}
    </div>
  );
};
