import React, { useState } from 'react';
import { 
  Users, 
  Download, 
  Filter, 
  Search, 
  Sparkles, 
  FileText, 
  Trash2, 
  ExternalLink, 
  Phone, 
  Mail, 
  MapPin, 
  LayoutGrid, 
  Table as TableIcon,
  CheckCircle,
  Clock,
  Send,
  MessageSquare,
  DollarSign,
  Plus
} from 'lucide-react';
import { Lead } from '../types';
import { exportLeadsToCsv } from '../services/seoEngine';

interface CrmPipelineViewProps {
  leads: Lead[];
  onUpdateStatus: (leadId: string, status: Lead['status']) => Promise<void>;
  onDeleteLead: (leadId: string) => Promise<void>;
  onOpenAudit: (lead: Lead) => void;
  onOpenPitch: (lead: Lead) => void;
  onNavigateToHunter: () => void;
}

export const CrmPipelineView: React.FC<CrmPipelineViewProps> = ({
  leads,
  onUpdateStatus,
  onDeleteLead,
  onOpenAudit,
  onOpenPitch,
  onNavigateToHunter
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('All');
  const [oppFilter, setOppFilter] = useState<string>('All');
  const [viewMode, setViewMode] = useState<'table' | 'kanban'>('table');

  const statusOptions: Lead['status'][] = [
    'New',
    'Audited',
    'Contacted',
    'Replied',
    'Negotiating',
    'Closed Won',
    'Unresponsive'
  ];

  const filteredLeads = leads.filter(l => {
    const matchesSearch = 
      l.businessName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      l.domain.toLowerCase().includes(searchTerm.toLowerCase()) ||
      l.city.toLowerCase().includes(searchTerm.toLowerCase()) ||
      l.category.toLowerCase().includes(searchTerm.toLowerCase());

    const matchesStatus = statusFilter === 'All' || l.status === statusFilter;
    const matchesOpp = oppFilter === 'All' || l.opportunityLevel === oppFilter;

    return matchesSearch && matchesStatus && matchesOpp;
  });

  const highOppCount = leads.filter(l => l.opportunityLevel === 'High').length;
  const inPipelineCount = leads.filter(l => ['Contacted', 'Replied', 'Negotiating'].includes(l.status)).length;
  const closedCount = leads.filter(l => l.status === 'Closed Won').length;

  return (
    <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      {/* Top Banner & Stats */}
      <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
          <h2 className="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <Users className="w-7 h-7 text-blue-600" />
            B2B Prospects CRM & Outreach Pipeline
          </h2>
          <p className="text-sm text-slate-600 mt-1">
            Track leads synced directly to Cloud Firestore. Manage outreach stages and win retainers.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            id="btnExportCsv"
            onClick={() => exportLeadsToCsv(filteredLeads)}
            disabled={filteredLeads.length === 0}
            className="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
          >
            <Download className="w-4 h-4 text-slate-500" />
            <span>Export CSV</span>
          </button>

          <button
            id="btnDiscoverMore"
            onClick={onNavigateToHunter}
            className="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            <span>Hunt More Leads</span>
          </button>
        </div>
      </div>

      {/* Metric Cards */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
          <span className="text-xs font-semibold text-slate-500 uppercase block">Total Discovered</span>
          <span className="text-2xl font-black text-slate-900 mt-1 block">{leads.length}</span>
          <span className="text-[11px] text-slate-500">Live in Firestore</span>
        </div>

        <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
          <span className="text-xs font-semibold text-emerald-600 uppercase block">High Opportunity</span>
          <span className="text-2xl font-black text-emerald-700 mt-1 block">{highOppCount}</span>
          <span className="text-[11px] text-emerald-600/80">Severe SEO flaws</span>
        </div>

        <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
          <span className="text-xs font-semibold text-blue-600 uppercase block">Active Outreach</span>
          <span className="text-2xl font-black text-blue-700 mt-1 block">{inPipelineCount}</span>
          <span className="text-[11px] text-blue-600/80">Contacted / Negotiating</span>
        </div>

        <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
          <span className="text-xs font-semibold text-indigo-600 uppercase block">Closed Won Deals</span>
          <span className="text-2xl font-black text-indigo-700 mt-1 block">{closedCount}</span>
          <span className="text-[11px] text-indigo-600/80">Retainer Clients</span>
        </div>
      </div>

      {/* Filter and View Toggle Bar */}
      <div className="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div className="flex flex-wrap items-center gap-3 w-full md:w-auto">
          {/* Search box */}
          <div className="relative flex-grow md:w-64">
            <Search className="w-4 h-4 text-slate-400 absolute left-3 top-3" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Search business, domain, city..."
              className="w-full pl-9 pr-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          {/* Status filter */}
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white text-slate-700 focus:outline-none"
          >
            <option value="All">All Statuses</option>
            {statusOptions.map(st => (
              <option key={st} value={st}>{st}</option>
            ))}
          </select>

          {/* Opportunity filter */}
          <select
            value={oppFilter}
            onChange={(e) => setOppFilter(e.target.value)}
            className="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white text-slate-700 focus:outline-none"
          >
            <option value="All">All Opportunities</option>
            <option value="High">High Opportunity</option>
            <option value="Medium">Medium Opportunity</option>
            <option value="Low">Low Opportunity</option>
          </select>
        </div>

        {/* View Mode Toggle */}
        <div className="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-end md:self-auto">
          <button
            onClick={() => setViewMode('table')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5 ${
              viewMode === 'table' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <TableIcon className="w-3.5 h-3.5" />
            <span>Table</span>
          </button>

          <button
            onClick={() => setViewMode('kanban')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5 ${
              viewMode === 'kanban' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <LayoutGrid className="w-3.5 h-3.5" />
            <span>Kanban Pipeline</span>
          </button>
        </div>
      </div>

      {/* Main Content: Table or Kanban */}
      {viewMode === 'table' ? (
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                <tr>
                  <th className="py-3 px-4">Business & Domain</th>
                  <th className="py-3 px-4">Niche & City</th>
                  <th className="py-3 px-4">Scores</th>
                  <th className="py-3 px-4">Direct Contact</th>
                  <th className="py-3 px-4">Pipeline Status</th>
                  <th className="py-3 px-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredLeads.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="py-12 text-center text-slate-500">
                      No prospects match the filter criteria.
                    </td>
                  </tr>
                ) : (
                  filteredLeads.map((lead) => (
                    <tr key={lead.id} className="hover:bg-slate-50/70 transition-colors">
                      {/* Business & Domain */}
                      <td className="py-3.5 px-4">
                        <div className="font-bold text-slate-900 text-sm">{lead.businessName}</div>
                        <a
                          href={lead.website}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:underline flex items-center gap-1 mt-0.5"
                        >
                          <span>{lead.domain}</span>
                          <ExternalLink className="w-3 h-3 text-slate-400" />
                        </a>
                      </td>

                      {/* Niche & City */}
                      <td className="py-3.5 px-4">
                        <div className="font-semibold text-slate-800">{lead.category}</div>
                        <div className="text-slate-500 flex items-center gap-1 mt-0.5">
                          <MapPin className="w-3 h-3 text-slate-400" />
                          <span>{lead.city}, {lead.country}</span>
                        </div>
                      </td>

                      {/* Scores */}
                      <td className="py-3.5 px-4">
                        <div className="flex items-center gap-2">
                          <span className={`px-2 py-0.5 rounded font-bold ${
                            lead.seoScore < 60 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'
                          }`}>
                            SEO {lead.seoScore}
                          </span>
                          <span className="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">
                            Opp {lead.leadScore}
                          </span>
                        </div>
                      </td>

                      {/* Direct Contact */}
                      <td className="py-3.5 px-4">
                        <div className="space-y-0.5 font-mono">
                          <a href={`tel:${lead.phone}`} className="text-slate-700 hover:text-blue-600 flex items-center gap-1">
                            <Phone className="w-3 h-3 text-slate-400" />
                            <span>{lead.phone}</span>
                          </a>
                          <a href={`mailto:${lead.email}`} className="text-slate-700 hover:text-blue-600 flex items-center gap-1 truncate max-w-[170px]">
                            <Mail className="w-3 h-3 text-slate-400 shrink-0" />
                            <span className="truncate">{lead.email}</span>
                          </a>
                        </div>
                      </td>

                      {/* Pipeline Status Selector */}
                      <td className="py-3.5 px-4">
                        <select
                          value={lead.status}
                          onChange={(e) => onUpdateStatus(lead.id, e.target.value as Lead['status'])}
                          className={`text-xs font-bold px-2.5 py-1 rounded-lg border transition-colors cursor-pointer ${
                            lead.status === 'Closed Won' 
                              ? 'bg-emerald-50 text-emerald-700 border-emerald-300' 
                              : lead.status === 'Contacted' || lead.status === 'Negotiating'
                                ? 'bg-blue-50 text-blue-700 border-blue-300'
                                : 'bg-slate-50 text-slate-700 border-slate-300'
                          }`}
                        >
                          {statusOptions.map(st => (
                            <option key={st} value={st}>{st}</option>
                          ))}
                        </select>
                      </td>

                      {/* Actions */}
                      <td className="py-3.5 px-4 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => onOpenAudit(lead)}
                            title="View Audit Report"
                            className="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg border border-slate-200 transition-colors"
                          >
                            <FileText className="w-3.5 h-3.5" />
                          </button>

                          <button
                            onClick={() => onOpenPitch(lead)}
                            title="Generate AI Pitch"
                            className="p-1.5 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg border border-indigo-200 transition-colors"
                          >
                            <Sparkles className="w-3.5 h-3.5" />
                          </button>

                          <button
                            onClick={() => onDeleteLead(lead.id)}
                            title="Delete Lead"
                            className="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg border border-slate-200 transition-colors"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      ) : (
        /* Kanban Pipeline View */
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 overflow-x-auto pb-6">
          {['New', 'Audited', 'Contacted', 'Closed Won'].map((colStatus) => {
            const colLeads = filteredLeads.filter(l => l.status === colStatus);

            return (
              <div key={colStatus} className="bg-slate-100/70 p-3 rounded-2xl border border-slate-200">
                <div className="flex items-center justify-between px-2 py-1.5 mb-3">
                  <h4 className="font-bold text-xs uppercase tracking-wider text-slate-700">
                    {colStatus}
                  </h4>
                  <span className="text-xs font-bold px-2 py-0.5 bg-white text-slate-700 rounded-full border border-slate-200">
                    {colLeads.length}
                  </span>
                </div>

                <div className="space-y-3">
                  {colLeads.map((lead) => (
                    <div key={lead.id} className="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between">
                      <div>
                        <div className="flex items-center justify-between mb-1.5">
                          <span className="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded border border-emerald-200">
                            Opp {lead.leadScore}/100
                          </span>
                          <span className="text-[10px] font-bold text-red-600">
                            SEO: {lead.seoScore}
                          </span>
                        </div>
                        <h5 className="font-bold text-slate-900 text-xs line-clamp-1">
                          {lead.businessName}
                        </h5>
                        <p className="text-[11px] text-slate-500 truncate mb-2">
                          {lead.city} • {lead.category}
                        </p>
                      </div>

                      <div className="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <select
                          value={lead.status}
                          onChange={(e) => onUpdateStatus(lead.id, e.target.value as Lead['status'])}
                          className="text-[11px] font-bold px-1.5 py-0.5 rounded border border-slate-200 bg-slate-50 text-slate-700"
                        >
                          {statusOptions.map(st => (
                            <option key={st} value={st}>{st}</option>
                          ))}
                        </select>

                        <div className="flex items-center gap-1">
                          <button
                            onClick={() => onOpenPitch(lead)}
                            className="p-1 text-indigo-600 hover:bg-indigo-50 rounded"
                          >
                            <Sparkles className="w-3.5 h-3.5" />
                          </button>
                          <button
                            onClick={() => onOpenAudit(lead)}
                            className="p-1 text-blue-600 hover:bg-blue-50 rounded"
                          >
                            <FileText className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
};
