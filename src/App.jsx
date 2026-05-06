import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Landing from './pages/Landing';
import CityAuctions from './pages/CityAuctions';
import PropertyDetail from './pages/PropertyDetail';

function App() {
  return (
    <Router>
      <div className="min-h-screen flex flex-col bg-premium-dark">
        <Navbar />
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<Landing />} />
            <Route path="/city/:cityId" element={<CityAuctions />} />
            <Route path="/property/:propertyId" element={<PropertyDetail />} />
          </Routes>
        </main>
        <footer className="bg-slate-900 border-t border-slate-800 py-8 text-center text-slate-400">
          <p>© 2026 MahaAuctions. All rights reserved.</p>
        </footer>
      </div>
    </Router>
  );
}

export default App;
