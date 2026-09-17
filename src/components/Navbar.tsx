import React from 'react';
import { 
  Crosshair, 
  Search, 
  Users, 
  Gauge, 
  Globe, 
  LogOut, 
  LogIn, 
  UserPlus, 
  ShieldCheck, 
  Database,
  Download
} from 'lucide-react';
import { User as FirebaseUser } from 'firebase/auth';

interface NavbarProps {
  currentTab: 'home' | 'audit' | 'search' | 'leads';
  setCurrentTab: (tab: 'home' | 'audit' | 'search' | 'leads') => void;
  user: FirebaseUser | null;
  onOpenAuth: (mode: 'login' | 'register') => void;
  onLogout: () => void;
  onQuickDemoLogin: () => void;
}

export const Navbar: React.FC<NavbarProps> = ({
  currentTab,
  setCurrentTab,
  user,
  onOpenAuth,
  onLogout,
  onQuickDemoLogin
}) => {
  return (
    <header className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo & Brand */}
          <div 
            id="navBrand"
            className="flex items-center space-x-3 cursor-pointer"
            onClick={() => setCurrentTab('home')}
          >
            <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
              <Crosshair className="w-5 h-5" />
            </div>
            <div>
              <span className="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-1.5">
                SEO Client Hunter
                <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 border border-blue-200">
                  Firebase Cloud
                </span>
              </span>
              <p className="text-[11px] text-slate-500 hidden sm:block">B2B Prospect Discovery & CRM</p>
            </div>
          </div>

          {/* Navigation Links */}
          <nav className="hidden md:flex items-center space-x-1">
            <button
              id="tabNavHome"
              onClick={() => setCurrentTab('home')}
              className={`px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5 ${
                currentTab === 'home'
                  ? 'bg-blue-50 text-blue-700'
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
              }`}
            >
              <Globe className="w-4 h-4" />
              Instant Audit
            </button>

            <button
              id="tabNavSearch"
              onClick={() => setCurrentTab('search')}
              className={`px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5 ${
                currentTab === 'search'
                  ? 'bg-blue-50 text-blue-700'
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
              }`}
            >
              <Search className="w-4 h-4" />
              Lead Hunter
            </button>

            <button
              id="tabNavLeads"
              onClick={() => setCurrentTab('leads')}
              className={`px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5 ${
                currentTab === 'leads'
                  ? 'bg-blue-50 text-blue-700'
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
              }`}
            >
              <Users className="w-4 h-4" />
              CRM Pipeline
            </button>
          </nav>

          {/* User & Auth Controls */}
          <div className="flex items-center space-x-2">
            {user ? (
              <div className="flex items-center space-x-3">
                <div className="hidden sm:flex flex-col text-right">
                  <span className="text-xs font-semibold text-slate-900 truncate max-w-[150px]">
                    {user.displayName || user.email?.split('@')[0] || 'Agency Owner'}
                  </span>
                  <span className="text-[10px] text-emerald-600 font-medium flex items-center justify-end gap-1">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Firebase Active
                  </span>
                </div>
                <button
                  id="btnLogout"
                  onClick={onLogout}
                  title="Sign Out"
                  className="p-2 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 border border-slate-200 transition-colors"
                >
                  <LogOut className="w-4 h-4" />
                </button>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <button
                  id="btnQuickDemo"
                  onClick={onQuickDemoLogin}
                  className="px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors hidden sm:flex items-center gap-1"
                >
                  <ShieldCheck className="w-3.5 h-3.5 text-blue-600" />
                  Quick Demo Login
                </button>

                <button
                  id="btnLoginModal"
                  onClick={() => onOpenAuth('login')}
                  className="px-3.5 py-1.5 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center gap-1"
                >
                  <LogIn className="w-4 h-4" />
                  Login
                </button>

                <button
                  id="btnRegisterModal"
                  onClick={() => onOpenAuth('register')}
                  className="px-3.5 py-1.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors flex items-center gap-1"
                >
                  <UserPlus className="w-4 h-4" />
                  Get Started
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};
