import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Link, useNavigate } from 'react-router-dom';
import MapInteractive from '../components/MapInteractive';
import { properties, cities } from '../data/mockData';
import { Search, MapPin, Building2, TrendingUp, ShieldCheck, Gavel, IndianRupee, ArrowRight, Sparkles, CheckCircle2, BadgeAlert } from 'lucide-react';

export default function Landing() {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState('');
  
  // 7-Day Trial Modal States
  const [trialEmail, setTrialEmail] = useState('');
  const [trialName, setTrialName] = useState('');
  const [isTrialModalOpen, setIsTrialModalOpen] = useState(false);
  const [isTrialSuccess, setIsTrialSuccess] = useState(false);

  // Builder Ad Contact State
  const [builderContacted, setBuilderContacted] = useState(false);

  const featuredProperties = properties.slice(0, 3);

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    if (searchQuery.trim() !== '') {
      navigate(`/search?q=${encodeURIComponent(searchQuery)}`);
    } else {
      navigate('/search');
    }
  };

  const handleTrialSubmit = (e) => {
    e.preventDefault();
    setIsTrialSuccess(true);
    setTimeout(() => {
      setIsTrialSuccess(false);
      setIsTrialModalOpen(false);
      setTrialEmail('');
      setTrialName('');
    }, 2500);
  };

  return (
    <div className="bg-white text-left">
      
      {/* 7-DAY FREE TRIAL PERSISTENT BANNER */}
      <div className="bg-gradient-to-r from-premium-gold to-amber-500 py-3 px-4 text-white text-center text-xs font-extrabold tracking-wide flex justify-center items-center space-x-2.5 shadow-md">
        <BadgeAlert className="h-4.5 w-4.5 animate-bounce" />
        <span>LIMITED PERIOD: Get direct DM registry alerts and legal valuations free for 7 days!</span>
        <button 
          onClick={() => setIsTrialModalOpen(true)}
          className="bg-slate-900 text-white hover:bg-black text-[10px] font-black uppercase px-4 py-1.5 rounded-lg shadow transition-all ml-2"
        >
          Claim 7-Day Trial
        </button>
      </div>

      {/* HERO SECTION */}
      <section className="relative min-h-[90vh] flex flex-col justify-center overflow-hidden pt-10 pb-20 bg-slate-50/50">
        <div className="absolute top-0 right-0 w-1/3 h-full bg-slate-50 skew-x-[-10deg] transform origin-top -z-10 border-l border-slate-100"></div>
        <div className="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-emerald-50 rounded-full blur-[120px] pointer-events-none"></div>

        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col xl:flex-row items-center gap-12 z-10">
          
          {/* Left Text */}
          <motion.div 
            initial={{ opacity: 0, x: -50 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            className="xl:w-[45%] flex flex-col space-y-8"
          >
            <div className="inline-flex items-center space-x-2 bg-emerald-50 text-premium-emerald px-5 py-2.5 rounded-full w-fit border border-emerald-100 shadow-sm">
              <MapPin className="h-4 w-4" />
              <span className="text-sm font-bold tracking-wide uppercase">Maharashtra District Council</span>
            </div>
            
            <h1 className="text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.15] tracking-tight">
              Premium Statutory <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-premium-emerald to-teal-500 drop-shadow-sm">Auction & Heavy Deposit</span> Portal
            </h1>
            
            <p className="text-xl text-slate-600 leading-relaxed font-light">
              Explore vetted court auctions, private seller listings, monthly rentals, and high-value heavy deposit flats verified under ready reckoner valuations.
            </p>
            
            <form onSubmit={handleSearchSubmit} className="relative max-w-xl w-full">
              <input 
                type="text" 
                placeholder="Search by city, Listing ID, bank name..." 
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full bg-white border border-slate-300 text-slate-800 rounded-2xl py-5 pl-14 pr-32 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all text-lg shadow-lg font-medium"
              />
              <Search className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 h-6 w-6" />
              <button 
                type="submit"
                className="absolute right-3 top-1/2 -translate-y-1/2 bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold px-8 py-3 rounded-xl transition-all shadow-md hover:shadow-lg"
              >
                Search
              </button>
            </form>

            {/* Quick stats */}
            <div className="flex gap-10 pt-6 border-t border-slate-200 mt-4">
              <div className="flex items-center space-x-4">
                <div className="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><Building2 className="text-premium-emerald h-7 w-7"/></div>
                <div>
                  <div className="text-3xl font-extrabold text-slate-900">1,500+</div>
                  <div className="text-sm text-slate-500 font-semibold uppercase tracking-wide">Live Notices</div>
                </div>
              </div>
              <div className="flex items-center space-x-4">
                <div className="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><TrendingUp className="text-premium-emerald h-7 w-7"/></div>
                <div>
                  <div className="text-3xl font-extrabold text-slate-900">₹ 8.5K Cr</div>
                  <div className="text-sm text-slate-500 font-semibold uppercase tracking-wide">Market Reserves</div>
                </div>
              </div>
            </div>
          </motion.div>

          {/* Right Map with district filters */}
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="xl:w-[55%] w-full flex flex-col items-center mt-12 xl:mt-0"
          >
            {/* Interactive city quick chips sidebar next to map */}
            <div className="mb-4 bg-white/80 p-3 rounded-2xl border border-slate-200 flex flex-wrap gap-2 justify-center shadow-sm w-full max-w-[800px]">
              <span className="text-xs font-bold text-slate-400 uppercase flex items-center mr-2">Quick Cities Highlight:</span>
              {cities.slice(0, 6).map(city => (
                <button
                  key={city.id}
                  onClick={() => navigate(`/city/${city.id}`)}
                  className="bg-slate-50 hover:bg-premium-emerald hover:text-white border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-black text-slate-700 transition-all flex items-center space-x-1"
                >
                  <MapPin className="h-3 w-3" />
                  <span>{city.name}</span>
                </button>
              ))}
            </div>

            <div className="w-full max-w-[800px]">
              <MapInteractive />
            </div>
          </motion.div>
        </div>
      </section>

      {/* FEATURED PROPERTIES SECTION */}
      <section className="py-20 bg-slate-50 border-t border-slate-200">
        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h2 className="text-4xl font-extrabold text-slate-900 mb-4">Featured Listings</h2>
              <p className="text-lg text-slate-600">Handpicked premium properties currently open for bidding or lease.</p>
            </div>
            <Link to="/search" className="hidden md:flex items-center text-premium-emerald font-bold hover:underline">
              Surround Data Search <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            {featuredProperties.map((property) => (
              <div key={property.id} className="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
                <div className="relative h-64 overflow-hidden">
                  <img src={property.image} alt={property.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  
                  <div className="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
                    {property.category === 'Auction' ? '🏦 Bank Auction' : property.category}
                  </div>

                  <div className="absolute bottom-4 left-4 bg-slate-900/80 text-white font-bold text-xs px-2 py-1 rounded backdrop-blur border border-slate-700">
                    {property.listingId}
                  </div>
                </div>
                <div className="p-6 flex-grow flex flex-col">
                  <h3 className="text-2xl font-bold text-slate-900 mb-2 line-clamp-2">{property.title}</h3>
                  <p className="text-slate-500 text-sm mb-6 line-clamp-1 flex items-center font-medium">
                    <MapPin className="h-4 w-4 mr-1 inline text-slate-400" /> {property.address}
                  </p>
                  
                  {/* Reserve price indicator */}
                  <div className="bg-emerald-50 p-4 rounded-xl border border-emerald-100 mb-6 flex justify-between items-center">
                    <div>
                      <span className="text-slate-500 text-[10px] font-bold uppercase tracking-wide block mb-1">Reserve / Listed Price</span>
                      <span className="text-premium-emerald text-xl font-extrabold flex items-center">
                        <IndianRupee className="h-5 w-5 mr-0.5" />{property.reservePrice}
                      </span>
                    </div>

                    {property.numericGovValuation && property.numericGovValuation > property.numericPrice && (
                      <span className="bg-premium-emerald text-white text-[9px] font-black px-2 py-1 rounded uppercase tracking-wider">
                        -{Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100)}% Below Market
                      </span>
                    )}
                  </div>

                  <Link to={`/property/${property.id}`} className="mt-auto w-full text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-extrabold py-3.5 rounded-xl transition-all border-2 border-premium-emerald shadow-sm">
                    Inspect Listing &rarr;
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* BUILDER ADS ON WEB PROMOTIONAL BLOCK */}
      <section className="py-16 bg-white border-t border-slate-200">
        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
          <div className="mb-8">
            <span className="text-xs font-black text-premium-gold uppercase tracking-wider flex items-center">
              <Sparkles className="h-4 w-4 mr-1 text-premium-gold" /> Sponsored Premium Launches
            </span>
            <h3 className="text-2xl font-black text-slate-900 mt-1">Builder Showcase Campaigns</h3>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Ad 1 */}
            <div className="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 md:p-8 text-white border border-slate-800 relative overflow-hidden shadow-xl flex flex-col justify-between min-h-[260px]">
              <div className="absolute top-0 right-0 bg-premium-gold text-slate-950 text-[10px] font-black uppercase px-5 py-1.5 rounded-bl-2xl">
                0% Pre-EMI Launch
              </div>

              <div className="space-y-3">
                <span className="text-slate-400 text-xs font-bold uppercase tracking-wider block">LODHA GROUP PRESENTS</span>
                <h4 className="text-2xl font-black tracking-tight text-white">Lodha Amara - Premium Thane Residency</h4>
                <p className="text-slate-300 text-sm font-light max-w-md">
                  Experience standard 2 & 3 BHK forest-themed homes in Thane West with pool, clubhouse, and private gardens. Starting ₹89 Lakhs.
                </p>
              </div>

              <div className="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-white/10 mt-6">
                <span className="text-premium-gold font-bold text-base">Booking Open | Pay 5% Now</span>
                
                {builderContacted ? (
                  <span className="text-emerald-400 font-extrabold text-xs flex items-center">
                    <CheckCircle2 className="h-4 w-4 mr-1" /> Call request received!
                  </span>
                ) : (
                  <button 
                    onClick={() => setBuilderContacted(true)}
                    className="bg-white hover:bg-slate-100 text-slate-900 font-black px-6 py-2.5 rounded-xl transition-all text-xs uppercase"
                  >
                    Receive Builder Brochure
                  </button>
                )}
              </div>
            </div>

            {/* Ad 2 */}
            <div className="bg-gradient-to-r from-slate-900 to-emerald-950 rounded-3xl p-6 md:p-8 text-white border border-slate-800 relative overflow-hidden shadow-xl flex flex-col justify-between min-h-[260px]">
              <div className="absolute top-0 right-0 bg-premium-emerald text-white text-[10px] font-black uppercase px-5 py-1.5 rounded-bl-2xl">
                Ready possession
              </div>

              <div className="space-y-3">
                <span className="text-slate-400 text-xs font-bold uppercase tracking-wider block">GODREJ PROPERTIES</span>
                <h4 className="text-2xl font-black tracking-tight text-white">Godrej Horizon - Luxury Pune Living</h4>
                <p className="text-slate-300 text-sm font-light max-w-md">
                  Signature sky-lounge residences located near prominent Pune IT hubs. Instant registry verified with zero developmental taxes.
                </p>
              </div>

              <div className="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-white/10 mt-6">
                <span className="text-premium-emerald font-extrabold text-base">Starting ₹ 1.20 Cr Only</span>
                <button 
                  onClick={() => setIsTrialModalOpen(true)}
                  className="bg-premium-gold hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl transition-all text-xs uppercase"
                >
                  Book Priority Tour
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* HOW IT WORKS SECTION */}
      <section className="py-24 bg-slate-50 border-t border-slate-200">
        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-4xl font-extrabold text-slate-900 mb-4">How It Works</h2>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-16">The transparent, secure, and straightforward process to acquiring your next high-value asset through bank auctions.</p>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <Search className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">1. Find Property</h3>
              <p className="text-slate-600 leading-relaxed text-sm">Search through thousands of verified properties across Maharashtra using our advanced map or filters.</p>
            </div>
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <ShieldCheck className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">2. Register & Pay EMD</h3>
              <p className="text-slate-600 leading-relaxed text-sm">Submit your KYC documents and pay the Earnest Money Deposit securely to participate in the bidding.</p>
            </div>
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <Gavel className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">3. Bid & Win</h3>
              <p className="text-slate-600 leading-relaxed text-sm">Participate in the live e-auction on the scheduled date. Win the property and take physical possession.</p>
            </div>
          </div>
        </div>
      </section>

      {/* STATEFUL 7-DAY TRIAL POPUP MODAL */}
      <AnimatePresence>
        {isTrialModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div 
              onClick={() => {
                if (!isTrialSuccess) setIsTrialModalOpen(false);
              }}
              className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
            />

            <motion.div 
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full border border-slate-200 shadow-2xl relative z-10 space-y-6 text-left"
            >
              {isTrialSuccess ? (
                <div className="text-center py-6 space-y-4">
                  <div className="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
                    <CheckCircle2 className="h-10 w-10 animate-bounce" />
                  </div>
                  <div>
                    <h3 className="font-extrabold text-slate-900 text-lg">7-Day Free Trial Activated!</h3>
                    <p className="text-slate-400 text-xs mt-1">Premium SMS & title reports alerts started successfully.</p>
                  </div>
                  <p className="text-xs text-slate-500 leading-relaxed">
                    Check your email inbox for your direct access key and immediate alerts for new bank auctions in your preferred districts.
                  </p>
                </div>
              ) : (
                <div>
                  <div className="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
                    <div>
                      <span className="text-[10px] text-premium-gold font-black uppercase tracking-wider block">Access Premium Suite</span>
                      <h3 className="font-extrabold text-slate-900 text-lg">Claim Your 7-Day Trial</h3>
                    </div>
                    <button onClick={() => setIsTrialModalOpen(false)} className="text-slate-400 hover:text-slate-600 font-black text-sm">&times;</button>
                  </div>

                  <form onSubmit={handleTrialSubmit} className="space-y-4 text-xs font-semibold text-slate-500">
                    <p className="text-slate-500 leading-normal">
                      Get premium legal valuations, ready reckoner reports, SMS notifications, and title alerts sent to your phone for 7 days.
                    </p>

                    <div>
                      <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Full Name</label>
                      <input 
                        type="text" 
                        required
                        placeholder="e.g. Vikram Shinde"
                        value={trialName}
                        onChange={(e) => setTrialName(e.target.value)}
                        className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                      />
                    </div>

                    <div>
                      <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Email Address</label>
                      <input 
                        type="email" 
                        required
                        placeholder="name@example.com"
                        value={trialEmail}
                        onChange={(e) => setTrialEmail(e.target.value)}
                        className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                      />
                    </div>

                    <button 
                      type="submit"
                      className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md text-sm text-center uppercase tracking-wider"
                    >
                      Start Free Trial Alerts
                    </button>
                  </form>
                </div>
              )}
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}
