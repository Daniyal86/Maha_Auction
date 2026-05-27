import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { properties, cities } from '../data/mockData';
import { Search, MapPin, Tag, Building, Percent, Landmark, Table, Grid, ArrowUpDown, ShieldCheck } from 'lucide-react';
import { motion } from 'framer-motion';

export default function SearchPortal() {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [selectedCity, setSelectedCity] = useState('All');
  const [selectedBank, setSelectedBank] = useState('All');
  const [selectedType, setSelectedType] = useState('All');
  const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'compare'
  const [filteredProperties, setFilteredProperties] = useState(properties);
  const [sortBy, setSortBy] = useState('price-desc'); // 'price-desc' | 'price-asc' | 'savings-desc'

  const categories = ['All', 'Auction', 'Rental', 'Heavy Deposit', 'Seller Listed'];
  const banks = ['All', 'State Bank of India', 'ICICI Bank', 'HDFC Bank', 'Bank of Baroda', 'Kotak Mahindra Bank', 'Central Bank of India', 'Union Bank of India'];
  const types = ['All', 'Residential', 'Commercial', 'Industrial', 'Agricultural'];

  // Apply filters
  useEffect(() => {
    let result = properties;

    // Search query matches title, address, listingId or borrower
    if (searchQuery.trim() !== '') {
      const q = searchQuery.toLowerCase();
      result = result.filter(p => 
        p.title.toLowerCase().includes(q) || 
        p.address.toLowerCase().includes(q) ||
        p.listingId.toLowerCase().includes(q) ||
        (p.borrower && p.borrower.toLowerCase().includes(q)) ||
        (p.bank && p.bank.toLowerCase().includes(q))
      );
    }

    // Category filter
    if (selectedCategory !== 'All') {
      result = result.filter(p => p.category === selectedCategory);
    }

    // City filter
    if (selectedCity !== 'All') {
      result = result.filter(p => p.cityId === selectedCity);
    }

    // Bank filter
    if (selectedBank !== 'All') {
      result = result.filter(p => p.bank === selectedBank);
    }

    // Property Type filter
    if (selectedType !== 'All') {
      result = result.filter(p => p.type === selectedType);
    }

    // Sort operations
    if (sortBy === 'price-desc') {
      result = [...result].sort((a, b) => b.numericPrice - a.numericPrice);
    } else if (sortBy === 'price-asc') {
      result = [...result].sort((a, b) => a.numericPrice - b.numericPrice);
    } else if (sortBy === 'savings-desc') {
      result = [...result].sort((a, b) => {
        const diffA = (a.numericGovValuation - a.numericPrice) / a.numericGovValuation;
        const diffB = (b.numericGovValuation - b.numericPrice) / b.numericGovValuation;
        return (diffB || 0) - (diffA || 0);
      });
    }

    setFilteredProperties(result);
  }, [searchQuery, selectedCategory, selectedCity, selectedBank, selectedType, sortBy]);

  // Calculate savings stats for the banner
  const averageSavingsPercent = filteredProperties.reduce((acc, curr) => {
    if (curr.numericGovValuation && curr.numericPrice && curr.numericGovValuation > curr.numericPrice) {
      const discount = ((curr.numericGovValuation - curr.numericPrice) / curr.numericGovValuation) * 100;
      return acc + discount;
    }
    return acc;
  }, 0) / (filteredProperties.filter(p => p.numericGovValuation > p.numericPrice).length || 1);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg">
      
      {/* Title Header */}
      <div className="mb-10 text-left">
        <h1 className="text-4xl font-extrabold text-slate-900 tracking-tight">
          Advanced <span className="text-premium-emerald">Data Surfing</span> Portal
        </h1>
        <p className="text-slate-500 text-lg mt-2">
          Surround-search thousands of registered government ready reckoner properties, heavy deposit flats, and premium court auctions.
        </p>
      </div>

      {/* Main Stats Banner */}
      <div className="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 border border-slate-700 shadow-xl">
        <div className="flex items-center space-x-4">
          <div className="bg-premium-emerald p-3.5 rounded-2xl">
            <Percent className="h-7 w-7 text-white" />
          </div>
          <div>
            <div className="text-2xl font-black text-premium-emerald">₹ Average 24.8% Below Market</div>
            <div className="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Government valuation compare savings rate</div>
          </div>
        </div>

        <div className="flex items-center space-x-6">
          <div className="text-center md:text-right">
            <div className="text-2xl font-extrabold">{filteredProperties.length} Matches</div>
            <div className="text-xs text-slate-400 font-semibold uppercase tracking-wider">Filtered Listings</div>
          </div>
          
          {/* Toggle buttons between standard grid and valuation comparison table */}
          <div className="flex bg-slate-800/80 p-1.5 rounded-xl border border-slate-700">
            <button 
              onClick={() => setViewMode('grid')}
              className={`p-2 rounded-lg transition-all ${viewMode === 'grid' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}`}
              title="Grid View"
            >
              <Grid className="h-5 w-5" />
            </button>
            <button 
              onClick={() => setViewMode('compare')}
              className={`p-2 rounded-lg transition-all ${viewMode === 'compare' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}`}
              title="Valuation Comparison Sheet"
            >
              <Table className="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>

      {/* Interactive Filters Grid */}
      <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm mb-10 space-y-6">
        
        {/* Search Input Box */}
        <div className="relative">
          <input 
            type="text" 
            placeholder="Search by Listing ID (e.g. MA-2026-001), keywords, bank name, borrower..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl py-4.5 pl-14 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all text-base font-semibold shadow-inner"
          />
          <Search className="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 h-5.5 w-5.5" />
        </div>

        {/* Categories Chips */}
        <div>
          <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Listing Category</label>
          <div className="flex flex-wrap gap-2.5">
            {categories.map(cat => (
              <button
                key={cat}
                onClick={() => setSelectedCategory(cat)}
                className={`px-4.5 py-2.5 rounded-xl text-sm font-bold border transition-all ${selectedCategory === cat ? 'bg-premium-emerald text-white border-premium-emerald shadow-md' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}`}
              >
                {cat === 'All' ? 'All Categories' : cat}
              </button>
            ))}
          </div>
        </div>

        {/* Cities badging (Major cities include list) */}
        <div>
          <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Major Cities In Maharashtra</label>
          <div className="flex flex-wrap gap-2">
            <button
              onClick={() => setSelectedCity('All')}
              className={`px-4 py-2 rounded-xl text-sm font-bold border transition-all ${selectedCity === 'All' ? 'bg-premium-emerald text-white border-premium-emerald shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}`}
            >
              All Cities ({properties.length})
            </button>
            {cities.map(city => {
              const count = properties.filter(p => p.cityId === city.id).length;
              return (
                <button
                  key={city.id}
                  onClick={() => setSelectedCity(city.id)}
                  className={`px-4 py-2 rounded-xl text-sm font-bold border transition-all flex items-center space-x-1.5 ${selectedCity === city.id ? 'bg-premium-emerald text-white border-premium-emerald shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}`}
                >
                  <MapPin className="h-3.5 w-3.5" />
                  <span>{city.name}</span>
                  <span className={`text-xs px-1.5 py-0.5 rounded-full ${selectedCity === city.id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'}`}>{count}</span>
                </button>
              );
            })}
          </div>
        </div>

        {/* Dropdowns Row (Bank, Property Type, Sort Options) */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
              <Landmark className="h-3.5 w-3.5 mr-1 text-slate-400" /> Authorized Banks
            </label>
            <select
              value={selectedBank}
              onChange={(e) => setSelectedBank(e.target.value)}
              className="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold"
            >
              <option value="All">All Banking Institutions</option>
              {banks.filter(b => b !== 'All').map(bank => (
                <option key={bank} value={bank}>{bank}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
              <Tag className="h-3.5 w-3.5 mr-1 text-slate-400" /> Property Classification
            </label>
            <select
              value={selectedType}
              onChange={(e) => setSelectedType(e.target.value)}
              className="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold"
            >
              <option value="All">All Classifications</option>
              {types.filter(t => t !== 'All').map(type => (
                <option key={type} value={type}>{type}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
              <ArrowUpDown className="h-3.5 w-3.5 mr-1 text-slate-400" /> Sort Grid List By
            </label>
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold"
            >
              <option value="price-desc">Price: High to Low</option>
              <option value="price-asc">Price: Low to High</option>
              <option value="savings-desc">Discounts: Highest Savings %</option>
            </select>
          </div>
        </div>
      </div>

      {/* Search Grid / Comparison Table View Router */}
      {filteredProperties.length === 0 ? (
        <div className="text-center py-20 bg-white rounded-3xl border border-slate-200 shadow-sm">
          <Landmark className="h-16 w-16 text-slate-300 mx-auto mb-4 animate-pulse" />
          <h3 className="text-2xl font-bold text-slate-800">No properties matched the criteria</h3>
          <p className="text-slate-500 mt-2">Try relaxing your search terms or expanding your geographic filter.</p>
        </div>
      ) : viewMode === 'grid' ? (
        /* GRID VIEW */
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {filteredProperties.map((property) => (
            <motion.div 
              key={property.id}
              layout
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              className="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col"
            >
              {/* Image banner */}
              <div className="relative h-60 overflow-hidden">
                <img 
                  src={property.image} 
                  alt={property.title} 
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                
                {/* Category tag */}
                <div className="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-black px-3 py-1.5 rounded-lg uppercase tracking-wider border border-slate-200 shadow-sm">
                  {property.category === 'Auction' ? '🏦 BANK AUCTION' : property.category}
                </div>

                <div className="absolute bottom-4 left-4">
                  <span className="bg-slate-900/80 text-white font-black text-xs px-2.5 py-1.5 rounded-lg backdrop-blur border border-slate-700 shadow-sm tracking-wider">
                    {property.listingId}
                  </span>
                </div>
              </div>

              {/* Body */}
              <div className="p-6 flex-grow flex flex-col">
                <h3 className="text-xl font-bold text-slate-900 mb-2 line-clamp-2">{property.title}</h3>
                <p className="text-slate-500 text-sm mb-4 line-clamp-1 flex items-center font-medium">
                  <MapPin className="h-4 w-4 mr-1 text-slate-400" /> {property.address}
                </p>

                {/* Price indicators */}
                <div className="space-y-3.5 mb-6 flex-grow">
                  <div className="bg-emerald-50/80 p-3 rounded-xl border border-emerald-100 flex justify-between items-center">
                    <div>
                      <span className="text-slate-500 text-[10px] font-bold uppercase tracking-wider block">Reserve / Listed Price</span>
                      <span className="text-premium-emerald text-lg font-extrabold">{property.reservePrice}</span>
                    </div>

                    {/* Government Val compare tag */}
                    {property.numericGovValuation && property.numericGovValuation > property.numericPrice && (
                      <div className="bg-premium-emerald text-white text-[10px] font-bold px-2 py-1 rounded-md text-right uppercase">
                        Save {Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100)}%
                      </div>
                    )}
                  </div>

                  <div className="flex justify-between items-center px-1 text-xs">
                    <span className="text-slate-500 font-semibold">Government Ready Reckoner:</span>
                    <span className="text-slate-800 font-extrabold">{property.governmentValuation || 'N/A'}</span>
                  </div>

                  <div className="flex justify-between items-center px-1 text-xs">
                    <span className="text-slate-500 font-semibold">Classification:</span>
                    <span className="text-slate-800 font-bold">{property.type}</span>
                  </div>
                </div>

                <Link 
                  to={`/property/${property.id}`}
                  className="w-full block text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-bold py-3.5 rounded-xl transition-all border-2 border-premium-emerald shadow-sm"
                >
                  Inspect Property Details &rarr;
                </Link>
              </div>
            </motion.div>
          ))}
        </div>
      ) : (
        /* GOVERNMENT COMPARISON COMPARATOR TABLE SPREADSHEET */
        <motion.div 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden"
        >
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-900 text-white border-b border-slate-800">
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Listing ID</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Property Name</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Auction / Seller Price</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Ready Reckoner Val.</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider text-center">Net Discount</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Assigned Institution</th>
                  <th className="py-5 px-6 font-extrabold text-sm uppercase tracking-wider text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredProperties.map((property) => {
                  const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
                  const discountPercent = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
                  
                  return (
                    <tr key={property.id} className="hover:bg-slate-50/80 transition-colors">
                      <td className="py-4 px-6 font-extrabold text-premium-emerald text-sm whitespace-nowrap">
                        {property.listingId}
                      </td>
                      <td className="py-4 px-6">
                        <div>
                          <span className="font-bold text-slate-800 block text-base line-clamp-1">{property.title}</span>
                          <span className="text-slate-400 text-xs flex items-center mt-0.5">
                            <MapPin className="h-3 w-3 mr-1" /> {property.address}
                          </span>
                        </div>
                      </td>
                      <td className="py-4 px-6 font-bold text-slate-800 whitespace-nowrap">
                        {property.reservePrice}
                      </td>
                      <td className="py-4 px-6 font-semibold text-slate-500 whitespace-nowrap">
                        {property.governmentValuation || 'N/A'}
                      </td>
                      <td className="py-4 px-6 text-center">
                        {hasDiscount ? (
                          <span className="inline-flex items-center space-x-1 bg-emerald-50 text-premium-emerald font-black text-xs px-2.5 py-1.5 rounded-lg border border-emerald-100 shadow-sm">
                            <Percent className="h-3 w-3" />
                            <span>{discountPercent}% OFF</span>
                          </span>
                        ) : (
                          <span className="text-slate-400 text-xs font-semibold">Standard Valuation</span>
                        )}
                      </td>
                      <td className="py-4 px-6">
                        <span className="inline-flex items-center bg-slate-100 text-slate-600 text-xs font-bold px-2 py-1 rounded-md">
                          {property.bank !== 'N/A' ? property.bank : 'Private Sale'}
                        </span>
                      </td>
                      <td className="py-4 px-6 text-right whitespace-nowrap">
                        <Link 
                          to={`/property/${property.id}`}
                          className="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold text-xs px-4.5 py-2.5 rounded-xl transition-all shadow-sm inline-block"
                        >
                          Details &rarr;
                        </Link>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </motion.div>
      )}
    </div>
  );
}
