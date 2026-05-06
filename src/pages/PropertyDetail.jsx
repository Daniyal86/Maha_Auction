import { useParams, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { properties, cities } from '../data/mockData';
import { MapPin, Calendar, IndianRupee, Building, User, FileText, CheckCircle2 } from 'lucide-react';

export default function PropertyDetail() {
  const { propertyId } = useParams();
  
  const property = properties.find(p => p.id === propertyId);
  
  if (!property) return <div className="text-slate-800 text-center py-20 text-2xl font-bold">Property not found</div>;
  
  const city = cities.find(c => c.id === property.cityId);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-premium-bg">
      <Link to={`/city/${city.id}`} className="text-premium-emerald font-semibold text-sm hover:underline mb-6 inline-block">
        &larr; Back to {city.name} Auctions
      </Link>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Left Column - Images and Details */}
        <div className="lg:col-span-2 space-y-8">
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            className="rounded-3xl overflow-hidden shadow-xl relative h-96 md:h-[500px] border border-slate-200"
          >
            <img src={property.image} alt={property.title} className="w-full h-full object-cover" />
            <div className="absolute top-4 right-4 bg-white/95 backdrop-blur text-slate-800 px-4 py-2 rounded-lg border border-slate-200 flex items-center shadow-md font-bold text-sm">
              <span className="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse mr-2.5"></span>
              Live Auction
            </div>
          </motion.div>

          <div className="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
            <div className="flex justify-between items-start mb-6">
              <div>
                <div className="text-premium-emerald text-sm font-bold tracking-wider uppercase mb-2">{property.type}</div>
                <h1 className="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3 leading-tight">{property.title}</h1>
                <p className="text-slate-500 flex items-center text-lg font-medium">
                  <MapPin className="h-5 w-5 mr-2 text-slate-400" /> {property.address}
                </p>
              </div>
            </div>

            <div className="prose prose-slate max-w-none">
              <h3 className="text-xl font-bold text-slate-900 mb-4 border-b border-slate-200 pb-3">Property Description</h3>
              <p className="text-slate-600 leading-relaxed text-lg">
                {property.details}
              </p>
            </div>
          </div>
        </div>

        {/* Right Column - Bidding Panel */}
        <motion.div 
          initial={{ opacity: 0, x: 50 }}
          animate={{ opacity: 1, x: 0 }}
          className="space-y-6"
        >
          <div className="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl sticky top-28">
            
            <div className="bg-emerald-50 rounded-2xl p-6 mb-8 border border-emerald-100">
              <p className="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Reserve Price</p>
              <h2 className="text-4xl font-extrabold text-premium-emerald flex items-center">
                <IndianRupee className="h-8 w-8 mr-1" />{property.reservePrice}
              </h2>
            </div>

            <div className="space-y-5 mb-8">
              <div className="flex justify-between items-center border-b border-slate-100 pb-4">
                <span className="text-slate-500 font-medium flex items-center"><IndianRupee className="h-4 w-4 mr-2"/> EMD Amount</span>
                <span className="text-slate-900 font-bold">{property.emd}</span>
              </div>
              <div className="flex justify-between items-center border-b border-slate-100 pb-4">
                <span className="text-slate-500 font-medium flex items-center"><Calendar className="h-4 w-4 mr-2"/> Auction Date</span>
                <span className="text-slate-900 font-bold">{new Date(property.auctionDate).toLocaleString()}</span>
              </div>
              <div className="flex justify-between items-center border-b border-slate-100 pb-4">
                <span className="text-slate-500 font-medium flex items-center"><Building className="h-4 w-4 mr-2"/> Bank</span>
                <span className="text-slate-900 font-bold text-right">{property.bank}</span>
              </div>
              <div className="flex justify-between items-center border-b border-slate-100 pb-4">
                <span className="text-slate-500 font-medium flex items-center"><User className="h-4 w-4 mr-2"/> Borrower</span>
                <span className="text-slate-900 font-bold text-right">{property.borrower}</span>
              </div>
              <div className="flex justify-between items-center pb-2">
                <span className="text-slate-500 font-medium flex items-center"><CheckCircle2 className="h-4 w-4 mr-2"/> Possession</span>
                <span className="text-slate-900 font-bold text-right">{property.possession}</span>
              </div>
            </div>

            <button className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold text-lg py-4 rounded-xl transition-all shadow-md hover:shadow-lg mb-4">
              Register to Bid
            </button>
            <button className="w-full bg-white hover:bg-slate-50 text-slate-700 font-bold py-4 rounded-xl transition-all flex items-center justify-center border-2 border-slate-200">
              <FileText className="h-5 w-5 mr-2 text-slate-400" /> Download Tender Form
            </button>
          </div>
        </motion.div>

      </div>
    </div>
  );
}
