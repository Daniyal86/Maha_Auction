import { useParams, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { properties, cities } from '../data/mockData';
import { MapPin, Calendar, IndianRupee, ArrowRight, Filter } from 'lucide-react';

export default function CityAuctions() {
  const { cityId } = useParams();
  
  const city = cities.find(c => c.id === cityId);
  const cityProperties = properties.filter(p => p.cityId === cityId);

  if (!city) return <div className="text-slate-800 text-center py-20 text-2xl font-bold">City not found</div>;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      {/* Header */}
      <div className="flex flex-col md:flex-row justify-between items-end mb-10 border-b border-slate-200 pb-6">
        <div>
          <Link to="/" className="text-premium-emerald font-medium text-sm hover:underline mb-2 inline-block">&larr; Back to Map</Link>
          <h1 className="text-4xl font-extrabold text-slate-900 flex items-center">
            <MapPin className="h-8 w-8 mr-3 text-premium-emerald" />
            Live Auctions in {city.name}
          </h1>
          <p className="text-slate-500 mt-2 text-lg font-medium">Found {cityProperties.length} premium properties</p>
        </div>
        
        <button className="mt-4 md:mt-0 flex items-center bg-white hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-lg border border-slate-300 shadow-sm transition-colors">
          <Filter className="h-4 w-4 mr-2" /> Filter Results
        </button>
      </div>

      {/* Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {cityProperties.map((property, index) => (
          <motion.div 
            key={property.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col"
          >
            <div className="relative h-60 overflow-hidden">
              <img 
                src={property.image} 
                alt={property.title} 
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div className="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
                {property.type}
              </div>
              <div className="absolute bottom-4 left-4 right-4 flex justify-between items-center">
                <span className="bg-slate-900/80 text-white font-medium text-xs px-3 py-1.5 rounded backdrop-blur border border-slate-700 shadow-sm">
                  {property.bank}
                </span>
              </div>
            </div>
            
            <div className="p-6 flex-grow flex flex-col">
              <h3 className="text-xl font-bold text-slate-900 mb-2 line-clamp-2">{property.title}</h3>
              <p className="text-slate-500 text-sm mb-5 line-clamp-1 flex items-center">
                <MapPin className="h-3 w-3 mr-1 inline" /> {property.address}
              </p>
              
              <div className="space-y-3 mb-6 flex-grow">
                <div className="flex justify-between items-center bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                  <span className="text-slate-600 text-xs font-bold uppercase tracking-wide">Reserve Price</span>
                  <span className="text-premium-emerald text-lg font-extrabold flex items-center">
                    <IndianRupee className="h-4 w-4 mr-1" />{property.reservePrice}
                  </span>
                </div>
                <div className="flex justify-between items-center px-1">
                  <span className="text-slate-500 text-sm font-medium">EMD Amount</span>
                  <span className="text-slate-800 text-sm font-bold">{property.emd}</span>
                </div>
                <div className="flex justify-between items-center px-1">
                  <span className="text-slate-500 text-sm font-medium">Auction Date</span>
                  <span className="text-slate-800 text-sm font-bold flex items-center">
                    <Calendar className="h-3.5 w-3.5 mr-1.5 text-premium-emerald" />
                    {new Date(property.auctionDate).toLocaleDateString()}
                  </span>
                </div>
              </div>
              
              <Link 
                to={`/property/${property.id}`}
                className="w-full block text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-bold py-3.5 rounded-xl transition-colors border-2 border-premium-emerald flex items-center justify-center group-hover:shadow-md"
              >
                View Details <ArrowRight className="h-5 w-5 ml-2" />
              </Link>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );
}
