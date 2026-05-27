import { useState } from 'react';
import { agents } from '../data/mockData';
import { User, Phone, Mail, Award, Star, MessageSquareCode, CheckCircle, Sparkles } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

export default function AgentsDirectory() {
  const [selectedAgent, setSelectedAgent] = useState(null);
  const [contactName, setContactName] = useState('');
  const [contactPhone, setContactPhone] = useState('');
  const [contactMessage, setContactMessage] = useState('I am interested in scheduling a physical site inspection for an auction property.');
  const [isSuccess, setIsSuccess] = useState(false);

  const handleContactSubmit = (e) => {
    e.preventDefault();
    if (!contactName || !contactPhone) return;
    setIsSuccess(true);
    setTimeout(() => {
      setIsSuccess(false);
      setSelectedAgent(null);
      setContactName('');
      setContactPhone('');
    }, 3000);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg text-left">
      
      {/* Title Header */}
      <div className="mb-10 text-left">
        <h1 className="text-4xl font-extrabold text-slate-900 tracking-tight">
          Maharashtra Certified <span className="text-premium-emerald">Auction Agents</span>
        </h1>
        <p className="text-slate-500 text-lg mt-2">
          Connect with vetted local domain experts who manage statutory documentation, DM registry orders, and physical possession clearances.
        </p>
      </div>

      {/* Grid of Agents */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {agents.map((agent) => (
          <div 
            key={agent.id}
            className="bg-white rounded-3xl p-6 border border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group"
          >
            {/* Top accent badge */}
            <div className="absolute top-0 right-0 bg-emerald-50 text-premium-emerald text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-bl-2xl border-l border-b border-emerald-100">
              Verified Partner
            </div>

            {/* Profile Info */}
            <div className="space-y-5">
              <div className="flex items-center space-x-4">
                <img 
                  src={agent.image} 
                  alt={agent.name} 
                  className="h-16 w-16 rounded-2xl object-cover border border-slate-200 shadow"
                />
                <div>
                  <h3 className="font-extrabold text-slate-900 text-lg group-hover:text-premium-emerald transition-colors">{agent.name}</h3>
                  <div className="flex items-center text-amber-500 space-x-1 mt-0.5">
                    <Star className="h-4 w-4 fill-current" />
                    <span className="text-xs font-black text-slate-700">{agent.rating} / 5.0</span>
                  </div>
                </div>
              </div>

              {/* Badging Specialty */}
              <div className="bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center space-x-2">
                <Award className="h-4 w-4 text-premium-emerald flex-shrink-0" />
                <span className="text-xs font-bold text-slate-700 uppercase tracking-wide">{agent.specialty}</span>
              </div>

              {/* Direct Details */}
              <div className="space-y-2.5 text-xs text-slate-500 font-semibold">
                <div className="flex items-center space-x-2">
                  <Phone className="h-3.5 w-3.5 text-slate-400" />
                  <span>{agent.phone}</span>
                </div>
                <div className="flex items-center space-x-2">
                  <Mail className="h-3.5 w-3.5 text-slate-400" />
                  <span>{agent.email}</span>
                </div>
              </div>
            </div>

            {/* Action Trigger */}
            <button 
              onClick={() => setSelectedAgent(agent)}
              className="mt-6 w-full bg-slate-900 hover:bg-premium-emerald text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center space-x-2 text-sm"
            >
              <MessageSquareCode className="h-4.5 w-4.5" />
              <span>Connect with Agent</span>
            </button>
          </div>
        ))}
      </div>

      {/* Trust Badge Banner */}
      <div className="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 md:p-8 mt-12 text-white border border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl">
        <div className="space-y-1">
          <div className="text-xl font-bold flex items-center">
            <Sparkles className="h-5 w-5 text-premium-emerald mr-2" />
            Are you a certified Maharashtra Real Estate Agent?
          </div>
          <p className="text-slate-400 text-xs font-medium">Join MahaAuctions panel to access exclusive bank distress listings, borrower settlements, and heavy deposit deals.</p>
        </div>
        <button className="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold px-6 py-3.5 rounded-xl transition-all shadow-md text-sm whitespace-nowrap">
          Register as Agent Panelist
        </button>
      </div>

      {/* CONTACT MODAL POPUP SIMULATION */}
      <AnimatePresence>
        {selectedAgent && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <div 
              onClick={() => {
                if (!isSuccess) setSelectedAgent(null);
              }}
              className="absolute inset-0 bg-slate-900/60 backdrop-blur-xs"
            />

            {/* Content box */}
            <motion.div 
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-white rounded-3xl max-w-md w-full p-6 md:p-8 border border-slate-200 shadow-2xl relative z-10 space-y-6"
            >
              {isSuccess ? (
                <div className="text-center py-6 space-y-4">
                  <div className="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
                    <CheckCircle className="h-10 w-10 animate-bounce" />
                  </div>
                  <div>
                    <h3 className="font-extrabold text-slate-900 text-lg">Request Dispatched!</h3>
                    <p className="text-slate-500 text-xs mt-1">SMS Alerts and Email alerts sent successfully.</p>
                  </div>
                  <div className="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                    Agent <span className="font-bold text-slate-800">{selectedAgent.name}</span> has been alerted and will contact you directly on your number within 15 minutes.
                  </div>
                </div>
              ) : (
                <div>
                  {/* Form */}
                  <div className="flex justify-between items-start mb-4 border-b border-slate-100 pb-4">
                    <div>
                      <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Direct Consultation</span>
                      <h3 className="font-extrabold text-slate-900 text-lg">Alert {selectedAgent.name}</h3>
                    </div>
                    <button 
                      onClick={() => setSelectedAgent(null)}
                      className="text-slate-400 hover:text-slate-600 font-black text-sm p-1"
                    >
                      &times;
                    </button>
                  </div>

                  <form onSubmit={handleContactSubmit} className="space-y-4">
                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Full Name</label>
                      <input 
                        type="text" 
                        required
                        value={contactName}
                        onChange={(e) => setContactName(e.target.value)}
                        placeholder="e.g. Rahul Deshmukh"
                        className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Phone Number</label>
                      <input 
                        type="tel" 
                        required
                        value={contactPhone}
                        onChange={(e) => setContactPhone(e.target.value)}
                        placeholder="e.g. +91 99999 88888"
                        className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Message Preference</label>
                      <textarea 
                        rows="3"
                        required
                        value={contactMessage}
                        onChange={(e) => setContactMessage(e.target.value)}
                        className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                      />
                    </div>

                    <button 
                      type="submit"
                      className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md text-sm text-center"
                    >
                      Send Direct SMS Notification
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
