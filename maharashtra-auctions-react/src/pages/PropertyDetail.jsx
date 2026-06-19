import { useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { properties, cities, agents } from '../data/mockData';
import { MapPin, Calendar, IndianRupee, Building, User, FileText, CheckCircle2, ShieldCheck, Sparkles, Languages, Clock, ArrowRight } from 'lucide-react';

export default function PropertyDetail() {
  const { propertyId } = useParams();
  const property = properties.find(p => p.id === propertyId);

  // Visit Scheduling States
  const [visitDate, setVisitDate] = useState('');
  const [visitTime, setVisitTime] = useState('11:00 AM');
  const [visitorPhone, setVisitorPhone] = useState('');
  const [isScheduled, setIsScheduled] = useState(false);

  // Newspaper Notice State
  const [noticeLang, setNoticeLang] = useState('EN'); // 'EN' | 'MR'

  if (!property) {
    return <div className="text-slate-800 text-center py-20 text-2xl font-bold">Property not found</div>;
  }

  const city = cities.find(c => c.id === property.cityId);
  const agent = agents.find(a => a.id === property.agentId) || agents[0];

  // Calculate discount percentage
  const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
  const discountPercent = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
  const savingsAmount = hasDiscount ? (property.numericGovValuation - property.numericPrice) : 0;

  // Format savings amount nicely
  const formatRupee = (num) => {
    if (num >= 10000000) return `₹ ${(num / 10000000).toFixed(2)} Cr`;
    if (num >= 100000) return `₹ ${(num / 100000).toFixed(2)} Lakhs`;
    return `₹ ${num.toLocaleString()}`;
  };

  const handleScheduleVisit = (e) => {
    e.preventDefault();
    if (!visitDate || !visitorPhone) return;
    setIsScheduled(true);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-premium-bg text-left">
      <Link to={`/city/${city.id}`} className="text-premium-emerald font-bold text-sm hover:underline mb-6 inline-block">
        &larr; Back to {city.name} Auctions
      </Link>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Left Column - Details */}
        <div className="lg:col-span-2 space-y-8">
          
          {/* Main Hero Image */}
          <div className="rounded-3xl overflow-hidden shadow-xl relative h-96 md:h-[500px] border border-slate-200">
            <img src={property.image} alt={property.title} className="w-full h-full object-cover" />
            
            <div className="absolute top-4 left-4 bg-slate-900/80 text-white font-black text-xs px-3 py-1.5 rounded-lg backdrop-blur border border-slate-700 shadow-sm">
              {property.listingId}
            </div>

            <div className="absolute top-4 right-4 bg-white/95 backdrop-blur text-slate-800 px-4 py-2 rounded-xl border border-slate-200 flex items-center shadow-md font-bold text-sm">
              <span className="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse mr-2"></span>
              {property.category === 'Auction' ? '🏦 STATUTORY AUCTION' : property.category.toUpperCase()}
            </div>
          </div>

          {/* Core Info & Title */}
          <div className="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 space-y-6">
            <div>
              <div className="text-premium-emerald text-xs font-black tracking-wider uppercase mb-2 flex items-center">
                <Sparkles className="h-4 w-4 mr-1" />
                {property.type} PROPERTY
              </div>
              <h1 className="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3 leading-tight">
                {property.title}
              </h1>
              <p className="text-slate-500 flex items-center text-base font-medium">
                <MapPin className="h-5 w-5 mr-2 text-slate-400" /> {property.address}
              </p>
            </div>

            {/* Heavy Deposit tag highlight if applicable */}
            {property.category === 'Heavy Deposit' && (
              <div className="bg-amber-50 rounded-2xl p-4 border border-amber-200 text-amber-900 text-xs font-semibold leading-relaxed">
                💡 <span className="font-extrabold uppercase">Heavy Deposit Special</span>: Under this contract style, you pay a one-time refundable security deposit of {property.deposit} at registration, and enjoy a ZERO monthly rental fee for the entire 2-year tenure. Excellent savings for visitors!
              </div>
            )}

            {/* GOVERNMENT VALUATION PRICE COMPARE WIDGET */}
            <div className="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 space-y-4">
              <div className="flex justify-between items-center">
                <div>
                  <h3 className="font-extrabold text-slate-900 text-sm flex items-center">
                    <ShieldCheck className="h-4.5 w-4.5 text-premium-emerald mr-1.5" />
                    Government Ready Reckoner Comparator
                  </h3>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Statutory valuation comparison audit</span>
                </div>

                {hasDiscount && (
                  <span className="bg-premium-emerald text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase shadow-sm">
                    {discountPercent}% Below Ready Reckoner
                  </span>
                )}
              </div>

              {/* Progress Slider representation */}
              <div className="space-y-2 pt-2">
                <div className="flex justify-between text-xs font-bold text-slate-600">
                  <span>MahaAuctions Reserve Price: {property.reservePrice}</span>
                  <span>Govt Ready Reckoner Valuation: {property.governmentValuation || 'N/A'}</span>
                </div>
                <div className="w-full h-3 bg-slate-200 rounded-full overflow-hidden relative border border-slate-300">
                  <div 
                    className="h-full bg-gradient-to-r from-premium-emerald to-teal-500 rounded-full" 
                    style={{ width: `${Math.min(100, (property.numericPrice / (property.numericGovValuation || 1)) * 100)}%` }}
                  />
                </div>
                
                {hasDiscount && (
                  <div className="text-[11px] text-slate-500 font-semibold italic text-right">
                    💡 Instantly save {formatRupee(savingsAmount)} below certified government valuation.
                  </div>
                )}
              </div>
            </div>

            {/* Property Description */}
            <div className="prose prose-slate max-w-none border-t border-slate-100 pt-6">
              <h4 className="text-lg font-bold text-slate-900 mb-3">Detailed Asset Overview</h4>
              <p className="text-slate-600 leading-relaxed text-sm">
                {property.details}
              </p>
            </div>
          </div>

          {/* MARATHI & ENGLISH BILINGUAL PAPER NOTICE CONTAINER */}
          {property.noticeEnglish && (
            <div className="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 space-y-6">
              <div className="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                  <h3 className="font-extrabold text-slate-900 text-lg flex items-center">
                    <FileText className="h-5 w-5 text-premium-emerald mr-2" />
                    Newspaper Publication Notice
                  </h3>
                  <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Official bilingual newspaper clippings</span>
                </div>

                {/* English/Marathi Toggle switches */}
                <div className="flex bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                  <button 
                    onClick={() => setNoticeLang('EN')}
                    className={`px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${noticeLang === 'EN' ? 'bg-premium-emerald text-white shadow' : 'text-slate-500 hover:text-slate-800'}`}
                  >
                    <Languages className="h-3 w-3" />
                    <span>English Notice</span>
                  </button>
                  <button 
                    onClick={() => setNoticeLang('MR')}
                    className={`px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${noticeLang === 'MR' ? 'bg-premium-emerald text-white shadow' : 'text-slate-500 hover:text-slate-800'}`}
                  >
                    <Languages className="h-3 w-3" />
                    <span>मराठी नोटीस</span>
                  </button>
                </div>
              </div>

              {/* Clippings Frame styled like a traditional printed news clip */}
              <div className="border-4 border-double border-slate-400 p-6 font-serif bg-white shadow-inner relative max-w-xl mx-auto">
                {/* Simulated newspaper borders */}
                <div className="absolute top-2 left-2 right-2 bottom-2 border border-slate-200 pointer-events-none" />

                {noticeLang === 'EN' ? (
                  <div className="space-y-3 text-[11px] text-slate-700 leading-relaxed text-justify">
                    <div className="text-center font-bold underline text-xs text-slate-900 tracking-wider">
                      PUBLIC AUCTION NOTICE - {property.bank !== 'N/A' ? property.bank.toUpperCase() : 'PRIVATE ASSET SALE'}
                    </div>
                    <div className="text-center text-[9px] font-semibold text-slate-500 uppercase">
                      Issued Under Rule 8(6) / Section 13(2) of SARFAESI Act, 2002
                    </div>
                    <p className="mt-2">
                      Notice is hereby given that the secured attached asset belonging to borrower **{property.borrower || 'Mortgager'}** scheduled below will be auctioned online on "AS IS WHERE IS" basis. Dues amount to be recovered.
                    </p>
                    <p>
                      <strong>Reserve Price:</strong> {property.reservePrice} | <strong>EMD:</strong> {property.emd}
                    </p>
                    <div className="bg-slate-50 p-2 border border-slate-200 font-sans text-[9px] leading-normal text-slate-600">
                      <strong>SCHEDULED PROPERTY:</strong> {property.address}
                    </div>
                    <div className="text-right font-sans text-[9px] font-bold text-slate-500 pt-2 uppercase">
                      Authorized Officer, {property.bank !== 'N/A' ? property.bank : 'Seller Representative'}
                    </div>
                  </div>
                ) : (
                  <div className="space-y-3 text-[11px] text-slate-700 leading-relaxed text-justify font-sans">
                    <div className="text-center font-bold underline text-xs text-slate-900 tracking-wider">
                      जाहीर लिलाव नोटीस - {property.bank !== 'N/A' ? property.bank : 'खाजगी मालमत्ता विक्री'}
                    </div>
                    <div className="text-center text-[9px] font-semibold text-slate-500 uppercase">
                      सरफेसी कायदा, २००२ च्या नियम ८(६) / कलम १३(२) अन्वये प्रसिद्ध नोटीस
                    </div>
                    <p className="mt-2">
                      याद्वारे जाहीर नोटीस देण्यात येते की कर्जदार **{property.borrower || 'हमीदार'}** यांच्या तारण मालमत्तेचा ऑनलाईन ई-लिलाव "आहे त्या स्थितीत" तत्वावर करण्यात येत आहे. बँकेची कर्ज वसुली केली जाणार आहे.
                    </p>
                    <p>
                      <strong>राखीव किंमत:</strong> {property.reservePrice} | <strong>इसारा रक्कम:</strong> {property.emd}
                    </p>
                    <div className="bg-slate-50 p-2 border border-slate-200 text-[9px] leading-normal text-slate-600">
                      <strong>मालमत्ता तपशील अनुसूची:</strong> {property.address}
                    </div>
                    <div className="text-right text-[9px] font-bold text-slate-500 pt-2">
                      प्राधिकृत अधिकारी, {property.bank !== 'N/A' ? property.bank : 'मालक प्रतिनिधी'}
                    </div>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>

        {/* Right Column - Actions Card */}
        <div className="space-y-6">
          
          {/* Main Reserve Price Card */}
          <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-xl space-y-6">
            <div className="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
              <span className="text-slate-500 text-xs font-bold uppercase tracking-wider block mb-1">
                {property.category === 'Rental' ? 'Monthly Rent' : 'Reserve Bidding Price'}
              </span>
              <h2 className="text-3xl font-extrabold text-premium-emerald flex items-center">
                <IndianRupee className="h-7 w-7 mr-0.5" />
                {property.reservePrice}
              </h2>
            </div>

            <div className="space-y-4 text-xs font-semibold text-slate-600">
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <span className="flex items-center text-slate-400"><IndianRupee className="h-4 w-4 mr-1.5"/> Earnest Money Deposit (EMD)</span>
                <span className="text-slate-900 font-extrabold">{property.emd}</span>
              </div>
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <span className="flex items-center text-slate-400"><Calendar className="h-4 w-4 mr-1.5"/> Auction inspection date</span>
                <span className="text-slate-900 font-extrabold">
                  {property.category === 'Auction' ? new Date(property.auctionDate).toLocaleDateString() : 'N/A (Ready to Rent)'}
                </span>
              </div>
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <span className="flex items-center text-slate-400"><Building className="h-4 w-4 mr-1.5"/> Mortgage Institution</span>
                <span className="text-slate-900 font-extrabold text-right">{property.bank !== 'N/A' ? property.bank : 'Direct Seller'}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="flex items-center text-slate-400"><User className="h-4 w-4 mr-1.5"/> Borrower Name</span>
                <span className="text-slate-900 font-extrabold text-right">{property.borrower || 'Private Seller'}</span>
              </div>
            </div>

            {property.category === 'Auction' ? (
              <button className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md">
                Register Tender & Bid Online
              </button>
            ) : (
              <button className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md">
                Contact Owner / Negotiate
              </button>
            )}

            <button className="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold py-3 rounded-xl transition-all flex items-center justify-center text-sm shadow-sm">
              <FileText className="h-4.5 w-4.5 mr-1.5 text-slate-400" />
              <span>Download Statutory Tender Rules</span>
            </button>
          </div>

          {/* SITE VISIT SCHEDULER (Purchaser visit / Rental visitor) */}
          <div className="bg-slate-900 rounded-3xl p-6 border border-slate-800 text-white shadow-xl space-y-5">
            <div className="border-b border-slate-800 pb-3">
              <h3 className="font-extrabold text-white text-base flex items-center">
                <Clock className="h-4.5 w-4.5 text-premium-emerald mr-2" />
                Schedule Physical Site Visit
              </h3>
              <p className="text-[10px] text-slate-400 mt-0.5">Purchaser inspections and rental viewings verified schedule.</p>
            </div>

            {isScheduled ? (
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                className="space-y-4 text-center py-4"
              >
                <div className="w-12 h-12 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
                  <CheckCircle2 className="h-7 w-7" />
                </div>
                <div>
                  <h4 className="font-bold text-white text-sm">Site Visit Arranged!</h4>
                  <p className="text-[10px] text-slate-400 mt-0.5">Date: {visitDate} at {visitTime}</p>
                </div>
                <div className="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60 text-left space-y-3">
                  <div className="flex items-center space-x-2.5">
                    <img src={agent.image} alt={agent.name} className="h-8 w-8 rounded-full object-cover border border-premium-emerald/50" />
                    <div>
                      <div className="text-[10px] text-slate-400 font-bold uppercase">Your Assigned Agent:</div>
                      <div className="text-xs font-bold text-white">{agent.name}</div>
                    </div>
                  </div>
                  <p className="text-[10px] text-slate-400 leading-normal border-t border-slate-700/60 pt-2">
                    Our verified partner agent will coordinate and meet you directly at the asset address. SMS alerts have been generated.
                  </p>
                </div>
              </motion.div>
            ) : (
              <form onSubmit={handleScheduleVisit} className="space-y-3 text-xs">
                <div>
                  <label className="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Select Viewing Date</label>
                  <input 
                    type="date" 
                    required
                    value={visitDate}
                    onChange={(e) => setVisitDate(e.target.value)}
                    className="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-white"
                  />
                </div>

                <div>
                  <label className="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Select Time Slot</label>
                  <select 
                    value={visitTime}
                    onChange={(e) => setVisitTime(e.target.value)}
                    className="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-slate-300"
                  >
                    <option value="10:00 AM">10:00 AM - Morning Slot</option>
                    <option value="11:30 AM">11:30 AM - Morning Slot</option>
                    <option value="02:00 PM">02:00 PM - Afternoon Slot</option>
                    <option value="04:30 PM">04:30 PM - Evening Slot</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Visitor Mobile Number</label>
                  <input 
                    type="tel" 
                    required
                    placeholder="e.g. +91 98989..."
                    value={visitorPhone}
                    onChange={(e) => setVisitorPhone(e.target.value)}
                    className="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-white"
                  />
                </div>

                <button 
                  type="submit"
                  className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3 rounded-xl transition-all shadow-md flex items-center justify-center space-x-1"
                >
                  <span>Book Free Inspector Visit</span>
                  <ArrowRight className="h-4 w-4" />
                </button>
              </form>
            )}
          </div>
        </div>

      </div>
    </div>
  );
}
