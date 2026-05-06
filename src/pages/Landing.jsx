import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import MapInteractive from '../components/MapInteractive';
import { properties } from '../data/mockData';
import { Search, MapPin, Building2, TrendingUp, ShieldCheck, Clock, Gavel, IndianRupee, ArrowRight } from 'lucide-react';

export default function Landing() {
  const featuredProperties = properties.slice(0, 3);

  return (
    <div className="bg-white">
      {/* HERO SECTION */}
      <section className="relative min-h-[90vh] flex flex-col justify-center overflow-hidden pt-10 pb-20">
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
              <span className="text-sm font-bold tracking-wide uppercase">Exclusive to Maharashtra</span>
            </div>
            
            <h1 className="text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.15] tracking-tight">
              Discover Premium <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-premium-emerald to-teal-500 drop-shadow-sm">Auction</span> Properties
            </h1>
            
            <p className="text-xl text-slate-600 leading-relaxed font-light">
              Explore verified bank auction properties across Maharashtra. From luxury apartments in Mumbai to commercial spaces in Pune, find your next high-value investment safely.
            </p>
            
            <div className="relative max-w-xl w-full">
              <input 
                type="text" 
                placeholder="Search by city, property type, or bank..." 
                className="w-full bg-white border border-slate-300 text-slate-800 rounded-2xl py-5 pl-14 pr-32 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all text-lg shadow-lg"
              />
              <Search className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 h-6 w-6" />
              <button className="absolute right-3 top-1/2 -translate-y-1/2 bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold px-8 py-3 rounded-xl transition-all shadow-md hover:shadow-lg">
                Search
              </button>
            </div>

            <div className="flex gap-10 pt-6 border-t border-slate-200 mt-4">
              <div className="flex items-center space-x-4">
                <div className="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><Building2 className="text-premium-emerald h-7 w-7"/></div>
                <div>
                  <div className="text-3xl font-extrabold text-slate-900">1,500+</div>
                  <div className="text-sm text-slate-500 font-semibold uppercase tracking-wide">Active Properties</div>
                </div>
              </div>
              <div className="flex items-center space-x-4">
                <div className="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><TrendingUp className="text-premium-emerald h-7 w-7"/></div>
                <div>
                  <div className="text-3xl font-extrabold text-slate-900">₹ 8.5K Cr</div>
                  <div className="text-sm text-slate-500 font-semibold uppercase tracking-wide">Asset Value</div>
                </div>
              </div>
            </div>
          </motion.div>

          {/* Right Map (Made significantly larger) */}
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="xl:w-[55%] w-full flex justify-center mt-12 xl:mt-0"
          >
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
              <h2 className="text-4xl font-extrabold text-slate-900 mb-4">Featured Auctions</h2>
              <p className="text-lg text-slate-600">Handpicked premium properties currently open for bidding.</p>
            </div>
            <Link to="/" className="hidden md:flex items-center text-premium-emerald font-bold hover:underline">
              View All Properties <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            {featuredProperties.map((property) => (
              <div key={property.id} className="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
                <div className="relative h-64 overflow-hidden">
                  <img src={property.image} alt={property.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div className="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
                    {property.type}
                  </div>
                </div>
                <div className="p-6 flex-grow flex flex-col">
                  <h3 className="text-2xl font-bold text-slate-900 mb-2 line-clamp-2">{property.title}</h3>
                  <p className="text-slate-500 text-sm mb-6 line-clamp-1 flex items-center">
                    <MapPin className="h-4 w-4 mr-1 inline" /> {property.address}
                  </p>
                  <div className="bg-emerald-50 p-4 rounded-xl border border-emerald-100 mb-6">
                    <span className="text-slate-600 text-xs font-bold uppercase tracking-wide block mb-1">Reserve Price</span>
                    <span className="text-premium-emerald text-2xl font-extrabold flex items-center">
                      <IndianRupee className="h-5 w-5 mr-1" />{property.reservePrice}
                    </span>
                  </div>
                  <Link to={`/property/${property.id}`} className="mt-auto w-full text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-bold py-3.5 rounded-xl transition-colors border-2 border-premium-emerald">
                    View Details
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* HOW IT WORKS SECTION */}
      <section className="py-24 bg-white">
        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-4xl font-extrabold text-slate-900 mb-4">How It Works</h2>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-16">The transparent, secure, and straightforward process to acquiring your next high-value asset through bank auctions.</p>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <Search className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">1. Find Property</h3>
              <p className="text-slate-600 leading-relaxed">Search through thousands of verified properties across Maharashtra using our advanced map or filters.</p>
            </div>
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <ShieldCheck className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">2. Register & Pay EMD</h3>
              <p className="text-slate-600 leading-relaxed">Submit your KYC documents and pay the Earnest Money Deposit securely to participate in the bidding.</p>
            </div>
            <div className="flex flex-col items-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
                <Gavel className="h-10 w-10 text-premium-emerald" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-3">3. Bid & Win</h3>
              <p className="text-slate-600 leading-relaxed">Participate in the live e-auction on the scheduled date. Win the property and take physical possession.</p>
            </div>
          </div>
        </div>
      </section>

      {/* BANK PARTNERS SECTION */}
      <section className="py-16 bg-slate-50 border-t border-slate-200">
        <div className="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <p className="text-sm font-bold text-slate-500 uppercase tracking-widest mb-8">Trusted by Top Financial Institutions</p>
          <div className="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
            <h2 className="text-2xl font-extrabold text-slate-800">State Bank of India</h2>
            <h2 className="text-2xl font-extrabold text-slate-800">HDFC Bank</h2>
            <h2 className="text-2xl font-extrabold text-slate-800">ICICI Bank</h2>
            <h2 className="text-2xl font-extrabold text-slate-800">Bank of Baroda</h2>
            <h2 className="text-2xl font-extrabold text-slate-800">Kotak Mahindra</h2>
          </div>
        </div>
      </section>
    </div>
  );
}
