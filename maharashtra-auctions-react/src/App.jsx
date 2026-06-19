import { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Landing from './pages/Landing';
import CityAuctions from './pages/CityAuctions';
import PropertyDetail from './pages/PropertyDetail';
import SearchPortal from './pages/SearchPortal';
import AdvisoryHub from './pages/AdvisoryHub';
import AgentsDirectory from './pages/AgentsDirectory';
import AuthModal from './components/AuthModal';

function App() {
  const [user, setUser] = useState(null);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);

  // Synchronize initial authenticated state with local storage
  useEffect(() => {
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      try {
        setUser(JSON.parse(savedUser));
      } catch (err) {
        localStorage.removeItem('user');
      }
    }
  }, []);

  const handleLogout = () => {
    localStorage.removeItem('user');
    setUser(null);
  };

  return (
    <Router>
      <div className="min-h-screen flex flex-col bg-premium-bg">
        <Navbar 
          user={user} 
          onOpenAuthModal={() => setIsAuthModalOpen(true)} 
          onLogout={handleLogout} 
        />
        
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<Landing />} />
            <Route path="/city/:cityId" element={<CityAuctions />} />
            <Route path="/property/:propertyId" element={<PropertyDetail />} />
            <Route path="/search" element={<SearchPortal />} />
            <Route path="/advisory" element={<AdvisoryHub />} />
            <Route path="/agents" element={<AgentsDirectory />} />
          </Routes>
        </main>
        
        <footer className="bg-slate-900 border-t border-slate-800 py-8 text-center text-slate-400">
          <div className="max-w-7xl mx-auto px-4 text-xs font-semibold uppercase tracking-wider space-y-2">
            <div>MahaAuctions © 2026 Maharashtra statutory portal. All rights reserved.</div>
            <div className="text-slate-600">SARFAESI Securities enforcement division | Registered DM certified partners</div>
          </div>
        </footer>

        {/* Global stateful login modal */}
        <AuthModal 
          isOpen={isAuthModalOpen}
          onClose={() => setIsAuthModalOpen(false)}
          onLoginSuccess={(userData) => setUser(userData)}
        />
      </div>
    </Router>
  );
}

export default App;
