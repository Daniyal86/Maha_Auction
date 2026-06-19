import { Link, useLocation } from 'react-router-dom';
import { Building2, Menu, LogOut, ShieldCheck, User } from 'lucide-react';
import { useState } from 'react';

export default function Navbar({ user, onOpenAuthModal, onLogout }) {
  const location = useLocation();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const isActive = (path) => location.pathname === path;

  return (
    <nav className="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-20">
          
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-2.5">
            <div className="bg-premium-emerald p-2 rounded-xl text-white shadow-md">
              <Building2 className="h-6 w-6" />
            </div>
            <span className="text-2xl font-extrabold text-premium-text tracking-tight">
              Maha<span className="text-premium-emerald">Auctions</span>
            </span>
          </Link>
          
          {/* Desktop Navigation Links */}
          <div className="hidden lg:flex items-center space-x-8">
            <Link 
              to="/" 
              className={`font-semibold text-sm transition-colors ${isActive('/') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}`}
            >
              Home
            </Link>
            <Link 
              to="/search" 
              className={`font-semibold text-sm transition-colors ${isActive('/search') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}`}
            >
              Data Surfing
            </Link>
            <Link 
              to="/advisory" 
              className={`font-semibold text-sm transition-colors ${isActive('/advisory') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}`}
            >
              Legal Guidance (Adv)
            </Link>
            <Link 
              to="/agents" 
              className={`font-semibold text-sm transition-colors ${isActive('/agents') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}`}
            >
              Verified Agents
            </Link>
          </div>
          
          {/* Login / Profile State */}
          <div className="hidden md:flex items-center space-x-4">
            {user ? (
              <div className="flex items-center space-x-3.5 bg-slate-50 border border-slate-200/60 pl-3 pr-2.5 py-1.5 rounded-full shadow-sm">
                <img 
                  src={user.avatar} 
                  alt={user.name} 
                  className="h-8 w-8 rounded-full border border-premium-emerald/50 object-cover"
                />
                <span className="text-sm font-extrabold text-slate-800 tracking-wide">{user.name}</span>
                <button 
                  onClick={onLogout}
                  title="Sign Out"
                  className="p-1.5 text-slate-400 hover:text-red-500 rounded-full hover:bg-slate-100 transition-colors"
                >
                  <LogOut className="h-4.5 w-4.5" />
                </button>
              </div>
            ) : (
              <button 
                onClick={onOpenAuthModal}
                className="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center space-x-1.5"
              >
                <User className="h-4 w-4" />
                <span>Register / Login</span>
              </button>
            )}
          </div>

          {/* Mobile Menu Button */}
          <div className="lg:hidden flex items-center">
            <button 
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="text-slate-600 hover:text-premium-emerald p-2 rounded-lg"
            >
              <Menu className="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Drawer Menu */}
      {mobileMenuOpen && (
        <div className="lg:hidden border-t border-slate-100 bg-white p-4 space-y-3 shadow-inner">
          <Link 
            to="/" 
            onClick={() => setMobileMenuOpen(false)}
            className={`block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('/') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}`}
          >
            Home
          </Link>
          <Link 
            to="/search" 
            onClick={() => setMobileMenuOpen(false)}
            className={`block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('/search') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}`}
          >
            Data Surfing
          </Link>
          <Link 
            to="/advisory" 
            onClick={() => setMobileMenuOpen(false)}
            className={`block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('/advisory') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}`}
          >
            Legal Guidance (Adv)
          </Link>
          <Link 
            to="/agents" 
            onClick={() => setMobileMenuOpen(false)}
            className={`block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('/agents') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}`}
          >
            Verified Agents
          </Link>

          <div className="border-t border-slate-100 pt-3">
            {user ? (
              <div className="flex items-center justify-between bg-slate-50 border border-slate-200/60 p-3 rounded-2xl">
                <div className="flex items-center space-x-3">
                  <img src={user.avatar} alt={user.name} className="h-8 w-8 rounded-full object-cover" />
                  <span className="text-sm font-bold text-slate-800">{user.name}</span>
                </div>
                <button 
                  onClick={() => {
                    onLogout();
                    setMobileMenuOpen(false);
                  }}
                  className="flex items-center space-x-1 text-xs font-bold text-red-500 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100"
                >
                  <LogOut className="h-3.5 w-3.5" />
                  <span>Logout</span>
                </button>
              </div>
            ) : (
              <button 
                onClick={() => {
                  onOpenAuthModal();
                  setMobileMenuOpen(false);
                }}
                className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3 rounded-xl transition-all shadow-md text-center"
              >
                Register / Login
              </button>
            )}
          </div>
        </div>
      )}
    </nav>
  );
}
