import { Link } from 'react-router-dom';
import { Building2, Menu } from 'lucide-react';

export default function Navbar() {
  return (
    <nav className="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-20">
          <Link to="/" className="flex items-center space-x-2">
            <Building2 className="h-8 w-8 text-premium-emerald" />
            <span className="text-2xl font-extrabold text-premium-text tracking-tight">
              Maha<span className="text-premium-emerald">Auctions</span>
            </span>
          </Link>
          
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/" className="text-premium-muted hover:text-premium-emerald font-medium transition-colors">Home</Link>
            <Link to="/" className="text-premium-muted hover:text-premium-emerald font-medium transition-colors">Live Auctions</Link>
            <Link to="/" className="text-premium-muted hover:text-premium-emerald font-medium transition-colors">How it Works</Link>
            
            <button className="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-semibold px-6 py-2.5 rounded-lg transition-all shadow-md">
              Register / Login
            </button>
          </div>

          <div className="md:hidden flex items-center">
            <button className="text-premium-muted hover:text-premium-text">
              <Menu className="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
}
