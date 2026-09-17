import React, { useState, useEffect } from 'react';
import { User as FirebaseUser } from 'firebase/auth';
import { Navbar } from './components/Navbar';
import { LandingHero } from './components/LandingHero';
import { LeadDiscoveryView } from './components/LeadDiscoveryView';
import { CrmPipelineView } from './components/CrmPipelineView';
import { AuditReportModal } from './components/AuditReportModal';
import { PitchModal } from './components/PitchModal';
import { AuthModal } from './components/AuthModal';
import { Lead, AuditIssue } from './types';
import { 
  subscribeToAuth, 
  logoutUser, 
  fetchLeadsFromFirestore, 
  saveLeadToFirestore, 
  saveBatchLeadsToFirestore, 
  updateLeadStatusInFirestore, 
  deleteLeadFromFirestore 
} from './services/firebaseService';
import { generateProspectsForSearch, runInstantAudit } from './services/seoEngine';

export default function App() {
  const [currentTab, setCurrentTab] = useState<'home' | 'audit' | 'search' | 'leads'>('home');
  const [user, setUser] = useState<FirebaseUser | null>(null);
  const [leads, setLeads] = useState<Lead[]>([]);
  const [loadingLeads, setLoadingLeads] = useState(false);

  // Modals state
  const [authModalOpen, setAuthModalOpen] = useState(false);
  const [authMode, setAuthMode] = useState<'login' | 'register'>('login');
  const [activeAuditLead, setActiveAuditLead] = useState<Lead | null>(null);
  const [activePitchLead, setActivePitchLead] = useState<Lead | null>(null);

  // Listen to Firebase Auth state
  useEffect(() => {
    const unsubscribe = subscribeToAuth(async (currentUser) => {
      setUser(currentUser);
      if (currentUser) {
        setLoadingLeads(true);
        const data = await fetchLeadsFromFirestore(currentUser.uid);
        setLeads(data);
        setLoadingLeads(false);
      } else {
        // Fallback demo leads for guest session
        const demoLeads = generateProspectsForSearch('demo_guest', 'Cosmetic Dentist', 'Dallas', 'United States', 6);
        setLeads(demoLeads);
      }
    });

    return () => unsubscribe();
  }, []);

  // Quick Demo Login (simulates instant guest session without credentials)
  const handleQuickDemoLogin = () => {
    // Already loaded with demo prospects
    setCurrentTab('leads');
  };

  const handleSaveLead = async (newLead: Lead) => {
    setLeads(prev => [newLead, ...prev.filter(l => l.id !== newLead.id)]);
    if (user) {
      await saveLeadToFirestore(newLead);
    }
  };

  const handleBatchSaveLeads = async (newLeads: Lead[]) => {
    const existingIds = new Set(newLeads.map(l => l.id));
    setLeads(prev => [...newLeads, ...prev.filter(l => !existingIds.has(l.id))]);
    if (user) {
      await saveBatchLeadsToFirestore(newLeads);
    }
    setCurrentTab('leads');
  };

  const handleUpdateStatus = async (leadId: string, status: Lead['status']) => {
    setLeads(prev => prev.map(l => l.id === leadId ? { ...l, status } : l));
    if (user) {
      await updateLeadStatusInFirestore(leadId, status);
    }
  };

  const handleDeleteLead = async (leadId: string) => {
    setLeads(prev => prev.filter(l => l.id !== leadId));
    if (user) {
      await deleteLeadFromFirestore(leadId);
    }
  };

  const handleOpenPitchFromDomain = (domain: string, score: number, issues: AuditIssue[]) => {
    const tempLead: Lead = {
      id: `temp_${Date.now()}`,
      userId: user?.uid || 'guest',
      businessName: domain.split('.')[0].toUpperCase() + ' Solutions',
      website: `https://${domain}`,
      domain,
      email: `contact@${domain}`,
      phone: '+1 (555) 234-5678',
      address: 'Commercial Center',
      city: 'Local Metro Area',
      country: 'United States',
      category: 'Commercial Business',
      description: 'Prospective business analyzed via live technical crawler.',
      status: 'New',
      seoScore: score,
      leadScore: Math.min(95, 100 - score + 30),
      opportunityLevel: score < 70 ? 'High' : 'Medium',
      opportunityReasons: 'Direct crawl report indicates critical SEO issues.',
      scores: {
        technical: score,
        onpage: score,
        content: 70,
        local: 65,
        authority: 50,
        social: 60
      },
      issues,
      createdAt: new Date().toISOString()
    };
    setActivePitchLead(tempLead);
  };

  return (
    <div className="min-h-screen bg-slate-50 flex flex-col font-sans text-slate-900 selection:bg-blue-100 selection:text-blue-900">
      {/* Top Navbar */}
      <Navbar
        currentTab={currentTab}
        setCurrentTab={setCurrentTab}
        user={user}
        onOpenAuth={(mode) => {
          setAuthMode(mode);
          setAuthModalOpen(true);
        }}
        onLogout={async () => {
          await logoutUser();
        }}
        onQuickDemoLogin={handleQuickDemoLogin}
      />

      {/* Main View Area */}
      <main className="flex-grow">
        {currentTab === 'home' && (
          <LandingHero
            onStartSearch={() => setCurrentTab('search')}
            onViewPipeline={() => setCurrentTab('leads')}
            onOpenPitch={handleOpenPitchFromDomain}
          />
        )}

        {currentTab === 'search' && (
          <LeadDiscoveryView
            userId={user?.uid || 'guest'}
            onSaveLead={handleSaveLead}
            onBatchSaveLeads={handleBatchSaveLeads}
            onOpenAudit={(l) => setActiveAuditLead(l)}
            onOpenPitch={(l) => setActivePitchLead(l)}
          />
        )}

        {currentTab === 'leads' && (
          <CrmPipelineView
            leads={leads}
            onUpdateStatus={handleUpdateStatus}
            onDeleteLead={handleDeleteLead}
            onOpenAudit={(l) => setActiveAuditLead(l)}
            onOpenPitch={(l) => setActivePitchLead(l)}
            onNavigateToHunter={() => setCurrentTab('search')}
          />
        )}
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-slate-200 py-8 px-4 sm:px-6 lg:px-8 mt-12">
        <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <div className="flex items-center gap-2">
            <span className="font-extrabold text-slate-900">SEO Client Hunter</span>
            <span>•</span>
            <span>Automated B2B Lead Engine & CRM</span>
            <span className="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
              Firebase Synced
            </span>
          </div>
          <div>
            Built for modern SEO agencies and freelance technical consultants.
          </div>
        </div>
      </footer>

      {/* Modals */}
      {authModalOpen && (
        <AuthModal
          initialMode={authMode}
          onClose={() => setAuthModalOpen(false)}
          onSuccess={() => {
            setCurrentTab('leads');
          }}
          onQuickDemo={() => {
            setCurrentTab('leads');
          }}
        />
      )}

      {activeAuditLead && (
        <AuditReportModal
          lead={activeAuditLead}
          onClose={() => setActiveAuditLead(null)}
          onOpenPitch={(l) => {
            setActivePitchLead(l);
          }}
        />
      )}

      {activePitchLead && (
        <PitchModal
          lead={activePitchLead}
          onClose={() => setActivePitchLead(null)}
        />
      )}
    </div>
  );
}
