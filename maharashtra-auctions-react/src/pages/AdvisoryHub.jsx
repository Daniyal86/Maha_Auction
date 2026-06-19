import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Scale, FileText, CheckCircle, HelpCircle, Calendar, ShieldCheck, Printer, Languages, ArrowRight, ArrowLeft, Briefcase, GraduationCap, UserCheck, Sparkles, Award, Star, MapPin, Mail, Phone, Clock, Bookmark } from 'lucide-react';

const advocatesList = [
  {
    id: 'sajid',
    name: 'Adv. Sajid Kureshi',
    role: 'Chief Founder & Senior Counsel',
    image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&h=150&q=80',
    barReg: 'MAH/2084/2007',
    education: 'LL.M (Banking & Finance), GLC Mumbai',
    experience: '18+ Years Practice',
    office: 'Chamber 12, Court Building Suites (CBS), Opp. District Court, Nashik - 422002',
    phone: '+91 96044 96521',
    email: 'sajid.kureshi@mahaauctions-adv.in',
    rating: '4.95 (142 reviews)',
    specialties: 'SARFAESI 13(2)/13(4) disputes, Physical DM Possession recovery, Banking Arbitration',
    trustScore: '99.4% Success Rate',
    bio: 'Adv. Sajid Kureshi is the chief legal architect of MahaAuctions. He has successfully cleared over 500+ statutory bank recovery disputes before the DRT and High Court.'
  },
  {
    id: 'smita',
    name: 'Adv. Smita Patil',
    role: 'Premium Sponsored Partner',
    image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80',
    barReg: 'MAH/5829/2014',
    education: 'LL.B, ILS Law College, Pune',
    experience: '12+ Years Practice',
    office: 'Office 12, Gold Plaza, Senapati Bapat Road, Pune - 411016',
    phone: '+91 99230 58291',
    email: 'smita.patil@patilchambers.com',
    rating: '4.90 (89 reviews)',
    specialties: 'RERA Compliance, Heavy Deposit Lease covenants, Title Search clearance',
    trustScore: '98.1% Clearance Rate',
    bio: 'Adv. Smita Patil is a leading property arbitrator in Pune, assisting auction buyers in conducting comprehensive pre-purchase title searches.'
  },
  {
    id: 'rohan',
    name: 'Adv. Rohan Mehta',
    role: 'Panel Advocate',
    image: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=150&q=80',
    barReg: 'MAH/1092/2017',
    education: 'LL.B, Symbiosis Law School, Pune',
    experience: '9+ Years Practice',
    office: 'Chamber 3B, Court Chambers, Shivaji Nagar, Pune - 411005',
    phone: '+91 97662 10928',
    email: 'rohan.mehta@mehtalegal.in',
    rating: '4.85 (56 reviews)',
    specialties: 'DRT Appeals, EMD Disputes, Banking Settlements',
    trustScore: '97.6% Recovery Rate',
    bio: 'Adv. Rohan Mehta specializes in securing quick EMD refund approvals from public sector banks when legal procedures get stalled.'
  },
  {
    id: 'neha',
    name: 'Adv. Neha Kulkarni',
    role: 'Panel Advocate',
    image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=150&q=80',
    barReg: 'MAH/4028/2015',
    education: 'B.A. LL.B, GLC Mumbai',
    experience: '11+ Years Practice',
    office: 'Chamber 104, High Court Lane, Fort, Mumbai - 400001',
    phone: '+91 98920 40281',
    email: 'neha.kulkarni@kulkarnilegal.com',
    rating: '4.88 (72 reviews)',
    specialties: 'Asset diligence search, Commercial title reports, Society tax clearances',
    trustScore: '98.5% Accuracy Rate',
    bio: 'Adv. Neha Kulkarni is known for her microscopic inspection of land registries, preventing unexpected municipal or cooperative liabilities.'
  },
  {
    id: 'amit',
    name: 'Adv. Amit Deshmukh',
    role: 'Panel Advocate',
    image: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80',
    barReg: 'MAH/6721/2018',
    education: 'LL.B, ILS Pune',
    experience: '8+ Years Practice',
    office: 'Room 5, District Court Chambers, Nashik - 422002',
    phone: '+91 94220 67219',
    email: 'amit.deshmukh@deshmukhchambers.in',
    rating: '4.82 (44 reviews)',
    specialties: 'SARFAESI Rule 8(6) / 13(4) challenges, DM possession applications',
    trustScore: '96.9% Attachment Clearance',
    bio: 'Adv. Amit Deshmukh coordinates directly with local collector offices in Nashik and Aurangabad to resolve DM attachment administrative delays.'
  }
];

export default function AdvisoryHub() {
  const [activeTab, setActiveTab] = useState('guidance'); // 'guidance' | 'draftsman'
  const [language, setLanguage] = useState('EN'); // 'EN' | 'MR'
  
  // Notice Draftsman States
  const [bankName, setBankName] = useState('STATE BANK OF INDIA');
  const [bankBranch, setBankBranch] = useState('STRESS ASSETS MANAGEMENT BRANCH, MUMBAI GP');
  const [borrowerName, setBorrowerName] = useState('M/S PRECISION ENGINEERING PVT. LTD.');
  const [noticeDate, setNoticeDate] = useState('2026-05-26');
  const [outstandingDues, setOutstandingDues] = useState('₹ 4,12,50,670');
  const [propertyDetails, setPropertyDetails] = useState('PLOT NO. J-24, BHOSARI MIDC, PUNE - 411026 MEASURING 10,000 SQ. FT.');
  const [isDraftGenerated, setIsDraftGenerated] = useState(false);

  // Counselor Booking States
  const [selectedAdvocate, setSelectedAdvocate] = useState(advocatesList[0]);
  const [activeProfile, setActiveProfile] = useState(null);
  const [consultName, setConsultName] = useState('');
  const [consultEmail, setConsultEmail] = useState('');
  const [consultTopic, setConsultTopic] = useState('SARFAESI Title Verification');
  const [appointmentDate, setAppointmentDate] = useState('');
  const [isConsultBooked, setIsConsultBooked] = useState(false);

  const handleDraftSubmit = (e) => {
    e.preventDefault();
    setIsDraftGenerated(true);
  };

  const handleBookingSubmit = (e) => {
    e.preventDefault();
    if (!consultName || !consultEmail || !appointmentDate) return;
    setIsConsultBooked(true);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg text-left">
      
      {/* Page Header */}
      <div className="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-slate-200 pb-6">
        <div>
          <div className="inline-flex items-center space-x-2 bg-emerald-50 text-premium-emerald px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider mb-3">
            <Scale className="h-4 w-4" />
            <span>Professional Advisory & Legal Bureau</span>
          </div>
          <h1 className="text-4xl font-extrabold text-slate-900 tracking-tight">
            Legal Guidance & <span className="text-premium-emerald">SARFAESI Draftsman</span>
          </h1>
          <p className="text-slate-500 text-lg mt-1">Get authentic legal guidance and compile compliant statutory notices immediately.</p>
        </div>

        {/* Tab Controls */}
        <div className="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200 mt-6 md:mt-0 shadow-inner">
          <button 
            onClick={() => setActiveTab('guidance')}
            className={`px-5 py-2.5 rounded-xl text-sm font-bold transition-all ${activeTab === 'guidance' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-500 hover:text-slate-800'}`}
          >
            Legal Advisory & Scheduling
          </button>
          <button 
            onClick={() => setActiveTab('draftsman')}
            className={`px-5 py-2.5 rounded-xl text-sm font-bold transition-all ${activeTab === 'draftsman' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-500 hover:text-slate-800'}`}
          >
            Section 13(2) Notice Draftsman
          </button>
        </div>
      </div>      {/* TAB 1: LEGAL ADVISORY AND CONSULTATION */}
      {activeTab === 'guidance' && (
        activeProfile ? (
          <div className="space-y-8">
            
            {/* Elegant Back Navigation Bar */}
            <div className="flex items-center justify-between border-b border-slate-200 pb-4">
              <button 
                onClick={() => setActiveProfile(null)}
                className="flex items-center gap-2 text-slate-700 hover:text-slate-900 font-extrabold text-sm transition-all bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm"
              >
                <ArrowLeft className="h-4 w-4" /> Back to Panel Directory
              </button>
              <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest bg-slate-50 border border-slate-100 px-3 py-1 rounded-md">
                Verified Chamber Profile
              </span>
            </div>

            {/* DEDICATED PROFILE DETAIL PAGE HERO CARD */}
            <div className="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden border border-slate-800 shadow-xl">
              <div className="absolute top-0 right-0 bg-premium-emerald text-white text-[9px] font-black uppercase px-6 py-2.5 rounded-bl-3xl tracking-widest flex items-center gap-1.5 shadow-md z-10">
                <Award className="h-4 w-4 text-premium-gold animate-pulse" /> Certified BAR Panelist
              </div>
              
              {/* Decorative scales watermarks */}
              <Scale className="absolute -bottom-12 -right-12 w-48 h-48 text-white/5 pointer-events-none" />
              
              <div className="flex flex-col md:flex-row items-center gap-6 md:gap-8 relative z-10">
                <div className="p-1 bg-gradient-to-tr from-premium-emerald to-premium-gold rounded-3xl shadow-lg flex-shrink-0">
                  <img 
                    src={activeProfile.image} 
                    alt={activeProfile.name} 
                    className="w-24 h-24 md:w-28 md:h-28 rounded-3xl object-cover border border-white"
                  />
                </div>
                <div className="text-center md:text-left space-y-2.5">
                  <div className="flex flex-wrap justify-center md:justify-start items-center gap-2">
                    <h2 className="text-2xl md:text-3xl font-black tracking-tight">{activeProfile.name}</h2>
                    {activeProfile.id === 'sajid' && (
                      <span className="bg-premium-gold/25 border border-premium-gold/30 text-premium-gold text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">
                        VIP FOUNDER
                      </span>
                    )}
                  </div>
                  <p className="text-emerald-400 font-extrabold text-xs uppercase tracking-widest">{activeProfile.role}</p>
                  
                  <div className="flex flex-wrap justify-center md:justify-start items-center gap-2.5 mt-3">
                    <span className="bg-slate-800 text-slate-200 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-700">
                      Reg ID: {activeProfile.barReg}
                    </span>
                    <span className="bg-slate-800 text-slate-200 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-700">
                      {activeProfile.experience}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            {/* DETAIL COLUMNS */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              
              {/* LEFT PROFILE PANELS (2/3 Col) */}
              <div className="lg:col-span-2 space-y-6">
                
                {/* PRACTICE METRICS / STATS */}
                <div className="grid grid-cols-2 gap-4">
                  <div className="bg-white p-5 rounded-2xl border border-slate-200 text-center shadow-sm">
                    <div className="text-3xl font-black text-premium-emerald">{activeProfile.trustScore.split(' ')[0]}</div>
                    <div className="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-1">Court litigation success</div>
                  </div>
                  <div className="bg-white p-5 rounded-2xl border border-slate-200 text-center shadow-sm">
                    <div className="text-3xl font-black text-amber-500">{activeProfile.rating.split(' ')[0]} / 5.0</div>
                    <div className="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-1">Client Consultation rating</div>
                  </div>
                </div>

                {/* PHYSICAL CHAMBERS AND DIRECT CONTACT */}
                <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
                  <h3 className="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                    <MapPin className="h-5 w-5 text-premium-emerald mr-2" /> Physical Chambers & Contact details
                  </h3>
                  <div className="space-y-4">
                    <div className="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-start gap-4">
                      <div className="p-3 bg-emerald-50 text-premium-emerald rounded-xl">
                        <MapPin className="h-5 w-5" />
                      </div>
                      <div>
                        <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Office chamber Address</span>
                        <p className="text-slate-800 text-sm font-semibold mt-1">{activeProfile.office}</p>
                      </div>
                    </div>
                    
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div className="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                        <div className="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                          <Phone className="h-5 w-5" />
                        </div>
                        <div>
                          <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Direct Hotline</span>
                          <p className="text-slate-800 text-sm font-black mt-0.5">{activeProfile.phone}</p>
                        </div>
                      </div>
                      
                      <div className="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                        <div className="p-3 bg-teal-50 text-teal-600 rounded-xl">
                          <Mail className="h-5 w-5" />
                        </div>
                        <div>
                          <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Official Email</span>
                          <p className="text-slate-800 text-sm font-semibold mt-0.5">{activeProfile.email}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* BIOGRAPHY & CASE STUDY */}
                <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                  <h3 className="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                    <Briefcase className="h-5 w-5 text-premium-emerald mr-2" /> practice bio & track record
                  </h3>
                  <p className="text-slate-600 text-sm leading-relaxed font-semibold">
                    {activeProfile.bio}
                  </p>
                  <p className="text-slate-500 text-xs leading-relaxed font-medium mt-2">
                    Practicing extensively in SARFAESI Debt Recovery Tribunal (DRT) appeals, municipal and co-operative housing society clearance codes, and secure heavy deposit lease covenants across Maharashtra.
                  </p>
                </div>

                {/* ACADEMICS & QUALIFICATIONS */}
                <div className="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                  <h3 className="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                    <GraduationCap className="h-5 w-5 text-premium-emerald mr-2" /> Academic credentials & Qualifications
                  </h3>
                  <div className="space-y-3.5 text-xs font-semibold text-slate-600">
                    <div className="flex items-center gap-3">
                      <CheckCircle className="h-4.5 w-4.5 text-premium-emerald flex-shrink-0" />
                      <span>Statutory Degree: {activeProfile.education}</span>
                    </div>
                    <div className="flex items-center gap-3">
                      <CheckCircle className="h-4.5 w-4.5 text-premium-emerald flex-shrink-0" />
                      <span>Specialties: {activeProfile.specialties}</span>
                    </div>
                    <div className="flex items-center gap-3">
                      <CheckCircle className="h-4.5 w-4.5 text-premium-emerald flex-shrink-0" />
                      <span>Bar Association: Active Certified Bar panelist in Maharashtra & Goa High Court</span>
                    </div>
                  </div>
                </div>

              </div>

              {/* RIGHT SCHEDULER BLOCK (1/3 Col) */}
              <div>
                <div id="scheduler-card" className="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl sticky top-28 space-y-6">
                  
                  {/* DIRECT HIGH-TRUST BLOCK */}
                  <div className="bg-slate-950 text-white rounded-2xl p-6 border border-slate-800 space-y-5 shadow-inner relative overflow-hidden">
                    <div className="absolute top-0 right-0 bg-premium-emerald text-white text-[9px] font-black uppercase px-4 py-1.5 rounded-bl-xl tracking-wider shadow">
                      Verified Bar Council Registry
                    </div>

                    <div className="flex items-center gap-4">
                      <img 
                        src={activeProfile.image} 
                        alt={activeProfile.name} 
                        className="w-14 h-14 rounded-xl object-cover border border-slate-700 shadow" 
                      />
                      <div>
                        <h4 className="font-black text-white text-lg">{activeProfile.name}</h4>
                        <span className="text-xs text-premium-emerald font-black uppercase tracking-wider block mt-0.5">
                          Reg ID: {activeProfile.barReg}
                        </span>
                      </div>
                    </div>

                    <div className="space-y-3.5 border-t border-slate-800/80 pt-4 text-xs font-semibold leading-normal text-slate-300">
                      <div className="flex items-start gap-2.5">
                        <MapPin className="h-4.5 w-4.5 text-slate-400 flex-shrink-0 mt-0.5" />
                        <div>
                          <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Office Chamber</span>
                          <span className="text-slate-200 text-sm font-semibold">{activeProfile.office}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Booking Form */}
                  <div className="space-y-4">
                    <div className="text-center pb-2">
                      <h3 className="font-extrabold text-slate-900 text-base">Schedule Appointment</h3>
                      <p className="text-slate-400 text-[10px] mt-0.5">Secure direct counsel on the verified panel.</p>
                    </div>

                    {isConsultBooked ? (
                      <motion.div 
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        className="p-5 bg-emerald-50 rounded-2xl border border-emerald-100 text-center space-y-3"
                      >
                        <div className="w-10 h-10 bg-premium-emerald/10 text-premium-emerald rounded-full flex items-center justify-center mx-auto">
                          <CheckCircle className="h-5 w-5" />
                        </div>
                        <div>
                          <h4 className="font-bold text-slate-900 text-sm">Consultation Scheduled!</h4>
                          <p className="text-xs text-slate-500 mt-1">Our advocate chambers will connect with you shortly.</p>
                        </div>
                      </motion.div>
                    ) : (
                      <form onSubmit={(e) => { e.preventDefault(); setIsConsultBooked(true); }} className="space-y-4 text-xs font-semibold text-slate-700">
                        <div>
                          <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Your Name</label>
                          <input 
                            type="text" 
                            required 
                            value={consultName} 
                            onChange={(e) => setConsultName(e.target.value)} 
                            placeholder="e.g. Anand Patil" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" 
                          />
                        </div>
                        <div>
                          <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Email Address</label>
                          <input 
                            type="email" 
                            required 
                            value={consultEmail} 
                            onChange={(e) => setConsultEmail(e.target.value)} 
                            placeholder="name@example.com" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" 
                          />
                        </div>
                        <div className="grid grid-cols-2 gap-3">
                          <div>
                            <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Preferred Date</label>
                            <input 
                              type="date" 
                              required 
                              value={appointmentDate} 
                              onChange={(e) => setAppointmentDate(e.target.value)} 
                              className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800 text-slate-600" 
                            />
                          </div>
                          <div>
                            <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Advice Topic</label>
                            <select 
                              value={consultTopic} 
                              onChange={(e) => setConsultTopic(e.target.value)} 
                              className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-700 bg-white"
                            >
                              <option value="SARFAESI Title Verification">SARFAESI Title Verification</option>
                              <option value="Delayed Physical Possession Dues">Delayed Physical Possession Dues</option>
                              <option value="EMD Refund Dispute Settlement">EMD Refund Dispute Settlement</option>
                              <option value="Heavy Deposit Lease Verification">Heavy Deposit Lease Verification</option>
                            </select>
                          </div>
                        </div>

                        <button 
                          type="submit" 
                          className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center text-sm"
                        >
                          Book Confirmed Consultation <ArrowRight className="h-4 w-4 ml-1.5" />
                        </button>
                      </form>
                    )}

                    <div className="text-[10px] text-slate-400 text-center font-medium border-t border-slate-100 pt-3">
                      🛡️ **MahaAuctions Client Escrow Policy**: Consultation fees are held securely in client dispute trust accounts until legal session is successfully rendered.
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        ) : (
          <div className="space-y-8">
            
            {/* HIGH-TRUST STATISTICS ROW */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-900 text-white rounded-3xl p-6 border border-slate-800 shadow-lg">
              <div className="text-center md:border-r border-slate-800">
                <div className="text-2xl font-black text-premium-emerald">99.4%</div>
                <div className="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Court Success Rate</div>
              </div>
              <div className="text-center md:border-r border-slate-800">
                <div className="text-2xl font-black text-premium-gold">15+ Mins</div>
                <div className="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Avg Response Time</div>
              </div>
              <div className="text-center md:border-r border-slate-800">
                <div className="text-2xl font-black text-emerald-400">MAH Bar</div>
                <div className="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Certified Advocates</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-black text-teal-400">Secured Escrow</div>
                <div className="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Client Fee Protection</div>
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              
              {/* FAQ Accordion, Guide Info, and Panel Directory */}
              <div className="lg:col-span-2 space-y-6">
                
                {/* ADVOCATES DIRECTORY - MOVED TO THE TOP FOR MAXIMUM VISUAL IMPACT */}
                <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-6">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                      <h3 className="text-xl font-extrabold text-slate-900 flex items-center">
                        <Scale className="h-5.5 w-5.5 text-premium-emerald mr-2" />
                        Panel of Certified Legal Advisors
                      </h3>
                      <p className="text-xs text-slate-400 mt-0.5 font-medium">Connect directly with Maharashtra's leading statutory auction advocates.</p>
                    </div>
                    <span className="bg-slate-100 text-slate-600 text-[10px] font-black uppercase px-3 py-1 rounded-md border border-slate-200 self-start sm:self-center">
                      Live Panelist Direct Booking
                    </span>
                  </div>

                  <div className="space-y-6">
                    {advocatesList.map((advocate) => {
                      const isSajid = advocate.id === 'sajid';
                      
                      if (isSajid) {
                        {/* SAJID KURESHI HIGH-PRESTIGE FOUNDER VIP CARD */}
                        return (
                          <div 
                            key={advocate.id}
                            onClick={() => {
                              setSelectedAdvocate(advocate);
                              setActiveProfile(advocate);
                              setConsultTopic(advocate.specialties.split(',')[0]);
                            }}
                            className={`cursor-pointer relative overflow-hidden p-6 rounded-3xl transition-all border-2 flex flex-col md:flex-row md:items-center gap-6 justify-between ${selectedAdvocate.id === advocate.id ? 'bg-gradient-to-br from-emerald-500/10 via-white to-slate-50 border-premium-emerald shadow-md' : 'bg-gradient-to-br from-emerald-50/40 via-white to-slate-50 border-slate-200 hover:border-premium-emerald/60 shadow-sm'}`}
                          >
                            {/* Top Right Founder Ribbon */}
                            <div className="absolute top-0 right-0 bg-premium-emerald text-white text-[8px] font-black uppercase px-4 py-1.5 rounded-bl-2xl tracking-widest shadow-sm flex items-center gap-1 z-10">
                              <Award className="h-3 w-3 text-premium-gold animate-pulse" /> FOUNDER & CHIEF COUNSEL
                            </div>

                            {/* Decorative Background Scale Watermark */}
                            <Scale className="absolute -bottom-8 -right-8 w-32 h-32 text-emerald-500/5 pointer-events-none" />

                            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-5 z-10">
                              <div className="relative flex-shrink-0">
                                <div className="p-0.5 bg-gradient-to-tr from-premium-emerald to-premium-gold rounded-2xl shadow-md">
                                  <img 
                                    src={advocate.image} 
                                    alt={advocate.name} 
                                    className="w-20 h-20 rounded-2xl object-cover border border-white"
                                  />
                                </div>
                                <span className="absolute -bottom-1 -right-1 bg-premium-gold text-slate-950 text-[7px] font-black uppercase px-1.5 py-0.5 rounded-md border border-white shadow">
                                  VIP Rank #1
                                </span>
                              </div>

                              <div className="space-y-1.5">
                                <div className="flex flex-wrap items-center gap-2">
                                  <h4 className="font-black text-slate-900 text-lg group-hover:text-premium-emerald transition-colors">{advocate.name}</h4>
                                  <span className="bg-emerald-50 border border-emerald-100 text-premium-emerald text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">
                                    Chief Counsel
                                  </span>
                                </div>
                                
                                <div className="flex flex-wrap items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                  <span>LL.M (GLC Mumbai)</span>
                                  <span>•</span>
                                  <span className="text-premium-emerald">18+ Yrs Court Seniority</span>
                                  <span>•</span>
                                  <span className="bg-slate-100 text-slate-600 px-2 py-0.5 rounded">CBS Chambers Nashik</span>
                                </div>

                                <p className="text-xs text-slate-600 font-semibold leading-relaxed max-w-md">
                                  {advocate.bio}
                                </p>
                              </div>
                            </div>

                            <button 
                              onClick={(e) => {
                                e.stopPropagation();
                                setSelectedAdvocate(advocate);
                                setActiveProfile(advocate);
                                setConsultTopic(advocate.specialties.split(',')[0]);
                              }}
                              className={`md:self-center font-black text-xs px-6 py-3.5 rounded-2xl transition-all shadow-md whitespace-nowrap z-10 ${selectedAdvocate.id === advocate.id ? 'bg-premium-emerald text-white hover:bg-premium-emeraldHover' : 'bg-slate-900 text-white hover:bg-premium-emerald'}`}
                            >
                              {selectedAdvocate.id === advocate.id ? 'View Chamber Profile' : 'Select & View Profile'}
                            </button>
                          </div>
                        );
                      }

                      {/* OTHER ADVOCATES STANDARD ROW LAYOUT */}
                      return (
                        <div 
                          key={advocate.id}
                          onClick={() => {
                            setSelectedAdvocate(advocate);
                            setActiveProfile(advocate);
                            setConsultTopic(advocate.specialties.split(',')[0]);
                          }}
                          className={`cursor-pointer pt-6 flex flex-col md:flex-row md:items-center gap-5 justify-between group transition-all p-3 rounded-2xl border ${selectedAdvocate.id === advocate.id ? 'bg-emerald-50/30 border-emerald-100/80 shadow-xs' : 'border-transparent hover:border-slate-100'}`}
                        >
                          <div className="flex items-start gap-4">
                            <div className="relative">
                              <img 
                                src={advocate.image} 
                                alt={advocate.name} 
                                className={`w-16 h-16 rounded-2xl object-cover shadow ${selectedAdvocate.id === advocate.id ? 'border-2 border-premium-emerald' : 'border border-slate-200'}`}
                              />
                              <div className="absolute -bottom-1.5 -right-1 bg-amber-500 text-slate-950 text-[7px] font-black uppercase px-1.5 py-0.5 rounded shadow-sm border border-white">
                                Partner
                              </div>
                            </div>
                            <div>
                              <div className="flex flex-wrap items-center gap-2">
                                <h4 className="font-extrabold text-slate-900 text-base group-hover:text-premium-emerald transition-colors">{advocate.name}</h4>
                                <span className="bg-slate-50 text-slate-600 text-[9px] font-black px-2 py-0.5 rounded border border-slate-200 uppercase tracking-wider">
                                  Verified Panelist
                                </span>
                              </div>
                              <div className="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{advocate.education} | {advocate.experience}</div>
                              <p className="text-xs text-slate-600 mt-2 font-medium leading-relaxed max-w-lg">
                                {advocate.bio}
                              </p>
                            </div>
                          </div>
                          <button 
                            onClick={(e) => {
                              e.stopPropagation();
                              setSelectedAdvocate(advocate);
                              setActiveProfile(advocate);
                              setConsultTopic(advocate.specialties.split(',')[0]);
                            }}
                            className={`md:self-center font-extrabold text-xs px-5 py-3 rounded-xl transition-all shadow-sm whitespace-nowrap ${selectedAdvocate.id === advocate.id ? 'bg-premium-emerald text-white' : 'bg-slate-100 hover:bg-premium-emerald text-slate-700 hover:text-white border border-slate-200'}`}
                          >
                            {selectedAdvocate.id === advocate.id ? 'View Profile' : 'Select & View Profile'}
                          </button>
                        </div>
                      );
                    })}
                  </div>

                  {/* DIRECTORY PLACEMENT CALL TO ACTION */}
                  <div className="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-5 text-white border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6">
                    <div>
                      <h4 className="font-extrabold text-base flex items-center">
                        <Sparkles className="h-4.5 w-4.5 text-premium-gold mr-1.5 animate-pulse" />
                        Are you a practicing Advocate in Maharashtra?
                      </h4>
                      <p className="text-slate-400 text-xs font-semibold mt-0.5">Promote your legal chamber in our verified panel. Connect with 5,000+ monthly auction buyers.</p>
                    </div>
                    <button className="bg-premium-gold hover:bg-amber-500 text-slate-950 font-black text-xs px-5 py-3 rounded-xl transition-all shadow whitespace-nowrap uppercase">
                      Apply for Panelist Spot
                    </button>
                  </div>
                </div>

                {/* Essential Sarfaesi Intro */}
                <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm">
                  <h2 className="text-2xl font-extrabold text-slate-900 mb-4 flex items-center">
                    <ShieldCheck className="h-6 w-6 text-premium-emerald mr-2" />
                    SARFAESI Act Property Buyers Guide
                  </h2>
                  <div className="prose prose-slate max-w-none text-slate-600 space-y-4 text-base">
                    <p>
                      Purchasing real estate through bank auctions requires deep compliance knowledge under the **SARFAESI Act, 2002** (Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest Act).
                    </p>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                      <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span className="font-bold text-slate-800 block text-sm mb-1">Section 13(2) Demand Notice</span>
                        <p className="text-xs text-slate-500">Issued by banks giving default borrowers a mandatory 60 days duration to discharge full outstanding liability before asset attachment.</p>
                      </div>
                      <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span className="font-bold text-slate-800 block text-sm mb-1">Section 13(4) Possession Notice</span>
                        <p className="text-xs text-slate-500">Issued once the 60-day period expires, authorizing the secure attachment and auctioning of symbolic or physical possession of the mortgaged asset.</p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Quick Accordion FAQs */}
                <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-4">
                  <h3 className="text-xl font-bold text-slate-900 mb-4 flex items-center">
                    <HelpCircle className="h-5 w-5 text-premium-emerald mr-2" /> Frequently Asked Questions
                  </h3>

                  <div className="space-y-4 divide-y divide-slate-100">
                    <div className="pt-3">
                      <h4 className="font-bold text-slate-800 text-sm">Q: What happens if the physical possession is delayed by the bank?</h4>
                      <p className="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Under the SARFAESI guidelines, if a bank is only offering "Symbolic Possession", it is up to the purchaser to obtain full physical possession via filing an application with the District Magistrate (DM) or Chief Metropolitan Magistrate (CMM) under Section 14 of the Act.
                      </p>
                    </div>
                    <div className="pt-4">
                      <h4 className="font-bold text-slate-800 text-sm">Q: Is the EMD (Earnest Money Deposit) refundable if I do not win?</h4>
                      <p className="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Yes. The EMD is 100% refundable by the bank within 2 to 5 business days after the auction is closed if your bid was not successful or you did not win the auction. No deduction is made.
                      </p>
                    </div>
                    <div className="pt-4">
                      <h4 className="font-bold text-slate-800 text-sm">Q: Are there any hidden liabilities like municipal taxes or society charges?</h4>
                      <p className="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Bank auctions are generally sold on an "As-Is-Where-Is" basis. It is extremely critical to perform a Title Search Report and check with local societies or municipal offices for outstanding dues, which the buyer may otherwise be legally required to clear.
                      </p>
                    </div>
                  </div>
                </div>

              </div>

              {/* Consultation Booking form & HIGH TRUST CARD SIDEBAR */}
              <div>
                <div id="scheduler-card" className="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl sticky top-28 space-y-6">
                  
                  {/* SELECTED ADVOCATE HIGH-TRUST SPOTLIGHT BLOCK */}
                  <div className="bg-slate-950 text-white rounded-2xl p-6 border border-slate-800 space-y-5 shadow-inner relative overflow-hidden">
                    <div className="absolute top-0 right-0 bg-premium-emerald text-white text-[9px] font-black uppercase px-4 py-1.5 rounded-bl-xl tracking-wider shadow">
                      Verified Bar Council Registry
                    </div>

                    <div className="flex items-center gap-4">
                      <img 
                        src={selectedAdvocate.image} 
                        alt={selectedAdvocate.name} 
                        className="w-14 h-14 rounded-xl object-cover border border-slate-700 shadow" 
                      />
                      <div>
                        <h4 className="font-black text-white text-lg">{selectedAdvocate.name}</h4>
                        <span className="text-xs text-premium-emerald font-black uppercase tracking-wider block mt-0.5">
                          Reg ID: {selectedAdvocate.barReg}
                        </span>
                      </div>
                    </div>

                    {/* TRUST DETAILS TABLE (Office, Hotline, Email, Metrics) */}
                    <div className="space-y-3.5 border-t border-slate-800/80 pt-4 text-xs font-semibold leading-normal text-slate-300">
                      <div className="flex items-start gap-2.5">
                        <MapPin className="h-4.5 w-4.5 text-slate-400 flex-shrink-0 mt-0.5" />
                        <div>
                          <span className="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Office Chamber</span>
                          <span className="text-slate-200 text-sm font-semibold">{selectedAdvocate.office}</span>
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
                        <div className="flex items-center gap-2">
                          <Phone className="h-4 w-4 text-slate-400" />
                          <div>
                            <span className="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Hotline</span>
                            <span className="text-slate-200 text-xs font-bold">{selectedAdvocate.phone}</span>
                          </div>
                        </div>
                        <div className="flex items-center gap-2">
                          <Mail className="h-4 w-4 text-slate-400" />
                          <div>
                            <span className="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Email</span>
                            <span className="truncate block max-w-[150px] text-slate-200 text-xs font-semibold">{selectedAdvocate.email}</span>
                          </div>
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
                        <div className="flex items-center gap-2">
                          <Star className="h-4 w-4 text-amber-500 fill-current" />
                          <div>
                            <span className="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Rating</span>
                            <span className="text-amber-400 font-black text-sm">{selectedAdvocate.rating.split(' ')[0]} / 5.0</span>
                          </div>
                        </div>
                        <div className="flex items-center gap-2">
                          <Award className="h-4 w-4 text-premium-emerald" />
                          <div>
                            <span className="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Success Rate</span>
                            <span className="text-emerald-400 font-black text-sm">{selectedAdvocate.trustScore.split(' ')[0]}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* BOOKING FORM inputs */}
                  <div className="space-y-4">
                    <div className="text-center pb-2">
                      <h3 className="font-extrabold text-slate-900 text-base">Schedule Appointment</h3>
                      <p className="text-slate-400 text-[10px] mt-0.5">Secure direct counsel on the verified panel.</p>
                    </div>

                    {isConsultBooked ? (
                      <motion.div 
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        className="space-y-4 text-center py-4"
                      >
                        <div className="w-12 h-12 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto shadow-sm border border-emerald-100">
                          <UserCheck className="h-6 w-6" />
                        </div>
                        <div>
                          <h4 className="font-extrabold text-slate-900 text-sm">Consultation Reserved!</h4>
                          <p className="text-[10px] text-slate-400 mt-0.5">Ref ID: MA-ADV-8910</p>
                        </div>
                        <div className="bg-slate-50 p-4 rounded-xl border border-slate-200/60 text-left text-xs">
                          <div className="font-bold text-slate-800">Your Appointed Counsel:</div>
                          <div className="text-premium-emerald font-extrabold mt-0.5">{selectedAdvocate.name}</div>
                          <div className="text-slate-400 mt-1.5 leading-normal">
                            Your appointment has been registered at {selectedAdvocate.office} for **{appointmentDate}**. A confirmation checklist has been sent to your mobile hotline.
                          </div>
                        </div>
                      </motion.div>
                    ) : (
                      <form onSubmit={handleBookingSubmit} className="space-y-4 text-xs font-semibold text-slate-500">
                        <div>
                          <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Your Name</label>
                          <input 
                            type="text" 
                            required
                            placeholder="e.g. Anand Patil"
                            value={consultName}
                            onChange={(e) => setConsultName(e.target.value)}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                          />
                        </div>

                        <div>
                          <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Email Address</label>
                          <input 
                            type="email" 
                            required
                            placeholder="name@example.com"
                            value={consultEmail}
                            onChange={(e) => setConsultEmail(e.target.value)}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                          />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div>
                            <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Preferred Date</label>
                            <input 
                              type="date" 
                              required
                              value={appointmentDate}
                              onChange={(e) => setAppointmentDate(e.target.value)}
                              className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                            />
                          </div>
                          <div>
                            <label className="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Advice Topic</label>
                            <select 
                              value={consultTopic}
                              onChange={(e) => setConsultTopic(e.target.value)}
                              className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-700 bg-white"
                            >
                              <option value="SARFAESI Title Verification">SARFAESI Title Verification</option>
                              <option value="Delayed Physical Possession Dues">Delayed Physical Possession Dues</option>
                              <option value="EMD Refund Dispute Settlement">EMD Refund Dispute Settlement</option>
                              <option value="Heavy Deposit Lease Verification">Heavy Deposit Lease Verification</option>
                            </select>
                          </div>
                        </div>

                        <button 
                          type="submit"
                          className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center text-sm"
                        >
                          Book Confirmed Consultation <ArrowRight className="h-4 w-4 ml-1.5" />
                        </button>

                        <div className="text-[10px] text-slate-400 text-center font-medium border-t border-slate-100 pt-3">
                          🛡️ **MahaAuctions Client Escrow Policy**: Consultation fees are held securely in client dispute trust accounts until legal session is successfully rendered.
                        </div>
                      </form>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        )
      )}

      {/* TAB 2: SARFAESI DRAFTSMAN WIZARD */}
      {activeTab === 'draftsman' && (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Draft Inputs */}
          <div className="lg:col-span-5 bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-6">
            <div>
              <h3 className="font-extrabold text-slate-900 text-xl flex items-center">
                <FileText className="h-5.5 w-5.5 text-premium-emerald mr-2" /> Notice Configuration
              </h3>
              <p className="text-xs text-slate-400 mt-0.5">Input outstanding dues and property descriptions to generate compliant templates.</p>
            </div>

            <form onSubmit={handleDraftSubmit} className="space-y-4">
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Financial Institution Name</label>
                <input 
                  type="text" 
                  value={bankName}
                  onChange={(e) => setBankName(e.target.value.toUpperCase())}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                />
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Bank Branch Address</label>
                <input 
                  type="text" 
                  value={bankBranch}
                  onChange={(e) => setBankBranch(e.target.value.toUpperCase())}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                />
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Borrower / Guarantor Name</label>
                <input 
                  type="text" 
                  value={borrowerName}
                  onChange={(e) => setBorrowerName(e.target.value.toUpperCase())}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Notice Date</label>
                  <input 
                    type="date" 
                    value={noticeDate}
                    onChange={(e) => setNoticeDate(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Outstanding Debt Dues</label>
                  <input 
                    type="text" 
                    value={outstandingDues}
                    onChange={(e) => setOutstandingDues(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Mortgaged Property Description</label>
                <textarea 
                  rows="3"
                  value={propertyDetails}
                  onChange={(e) => setPropertyDetails(e.target.value.toUpperCase())}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800"
                />
              </div>

              <button 
                type="submit"
                className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md text-center text-sm"
              >
                Compile Draft Document &rarr;
              </button>
            </form>
          </div>

          {/* Live Draft Preview with Marathi & English Toggles */}
          <div className="lg:col-span-7 bg-slate-900 rounded-3xl p-6 md:p-8 border border-slate-800 text-white relative flex flex-col min-h-[500px]">
            
            {/* Toolbar inside preview */}
            <div className="flex justify-between items-center border-b border-slate-800 pb-4 mb-6">
              <span className="flex items-center text-slate-400 text-sm font-semibold">
                <span className="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse mr-2"></span>
                Bilingual Notice Canvas
              </span>
              
              <div className="flex items-center space-x-3">
                {/* Language Toggle (Marathi & English paper notice) */}
                <div className="flex bg-slate-800 rounded-lg p-0.5 border border-slate-700">
                  <button 
                    onClick={() => setLanguage('EN')}
                    className={`px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${language === 'EN' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}`}
                  >
                    <Languages className="h-3 w-3" />
                    <span>English</span>
                  </button>
                  <button 
                    onClick={() => setLanguage('MR')}
                    className={`px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${language === 'MR' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}`}
                  >
                    <Languages className="h-3 w-3" />
                    <span>मराठी (Marathi)</span>
                  </button>
                </div>

                <button 
                  onClick={() => window.print()}
                  className="p-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white border border-slate-700 transition-colors"
                  title="Print Notice"
                >
                  <Printer className="h-4 w-4" />
                </button>
              </div>
            </div>

            {/* Document body rendering */}
            <div className="bg-white text-slate-900 rounded-2xl p-6 md:p-8 flex-grow font-serif shadow-inner border border-slate-100 overflow-y-auto max-h-[500px]">
              
              {/* ENGLISH NOTICE TEMPLATE */}
              {language === 'EN' ? (
                <div className="space-y-4 text-xs leading-relaxed">
                  <div className="text-center font-bold underline text-sm tracking-wide">
                    DEMAND NOTICE UNDER SECTION 13(2) OF SARFAESI ACT, 2002
                  </div>
                  
                  <div className="flex justify-between font-bold text-[10px]">
                    <span>REF: MA-DRAFT-13(2)/2026</span>
                    <span>DATE: {noticeDate}</span>
                  </div>

                  <div>
                    <span className="font-bold block">TO,</span>
                    <span className="font-bold">{borrowerName}</span>
                    <span className="text-[10px] text-slate-500 block">Maharashtra, India.</span>
                  </div>

                  <p>
                    WHEREAS, the undersigned being the Authorized Officer of **{bankName}**, under the Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest (SARFAESI) Act, 2002 and in exercise of powers conferred under Section 13(12) read with Rule 3 of the Security Interest (Enforcement) Rules, 2002 issued demand notices to you.
                  </p>

                  <p>
                    You have failed to clear your liabilities and outstanding dues towards the secured credit facility. The outstanding sum amounts to **{outstandingDues}** as of the date of notice.
                  </p>

                  <p>
                    Notice is hereby given to you to discharge in full the liabilities within **60 days** from the date of this notice, failing which the bank will exercise all rights under Section 13(4) of the SARFAESI Act, including taking physical/symbolic possession of the secured asset scheduled below:
                  </p>

                  <div className="p-3 bg-slate-50 rounded border border-slate-200 font-sans text-[10px]">
                    <span className="font-bold block uppercase text-slate-700">Schedule of Secure Attached Asset:</span>
                    <p className="mt-1 text-slate-600 font-medium">{propertyDetails}</p>
                  </div>

                  <div className="pt-6 flex justify-between font-sans text-[10px]">
                    <div>
                      <span className="block font-bold">Authorized Officer</span>
                      <span className="text-slate-500">{bankName}</span>
                    </div>
                    <div className="text-right">
                      <span className="block italic text-slate-400">Digital Signature Attached</span>
                      <span className="font-bold">{bankBranch}</span>
                    </div>
                  </div>
                </div>
              ) : (
                /* MARATHI NOTICE TEMPLATE (BILINGUAL) */
                <div className="space-y-4 text-xs leading-relaxed font-sans">
                  <div className="text-center font-bold underline text-sm tracking-wide">
                    सरफेसी कायदा, २००२ च्या कलम १३(२) अंतर्गत मागणी नोटीस
                  </div>
                  
                  <div className="flex justify-between font-bold text-[10px]">
                    <span>संदर्भ: एमए-ड्राफ्ट-१३(२)/२०२६</span>
                    <span>दिनांक: {noticeDate}</span>
                  </div>

                  <div>
                    <span className="font-bold block">प्रति,</span>
                    <span className="font-bold">{borrowerName}</span>
                    <span className="text-[10px] text-slate-500 block">महाराष्ट्र, भारत.</span>
                  </div>

                  <p>
                    ज्याअर्थी, **{bankName}** चे प्राधिकृत अधिकारी म्हणून स्वाक्षरी करणाऱ्यांनी, वित्तीय मालमत्तांचे सिक्युरिटायझेशन आणि पुनर्रचना आणि सुरक्षा हितसंबंधांची अंमलबजावणी (सरफेसी) कायदा, २००२ च्या तरतुदींनुसार व कलम १३(१२) वाचता सुरक्षा हितसंबंध (अंमलबजावणी) नियम, २००२ च्या नियम ३ अन्वये प्रदान केलेल्या अधिकारांचा वापर करून मागणी नोटीस बजावली आहे.
                  </p>

                  <p>
                    आपण बँकेच्या कर्जाची व थकबाकीची परतफेड करण्यात अयशस्वी ठरला आहात. सदर नोटीसच्या दिनांकापर्यंत एकूण थकबाकी **{outstandingDues}** इतकी आहे.
                  </p>

                  <p>
                    याद्वारे आपल्याला नोटीस देण्यात येते की, सदर नोटीसच्या दिनांकापासून **६० दिवसांच्या आत** संपूर्ण थकीत रकमेची परतफेड करावी, अन्यथा बँक सरफेसी कायद्याच्या कलम १३(४) अंतर्गत खालील अनुसूचित तारण मालमत्तेचा प्रत्यक्ष किंवा प्रतिकात्मक ताबा घेण्यासह कायदेशीर कारवाई करेल:
                  </p>

                  <div className="p-3 bg-slate-50 rounded border border-slate-200 text-[10px]">
                    <span className="font-bold block uppercase text-slate-700">सुरक्षित मालमत्तेची अनुसूची:</span>
                    <p className="mt-1 text-slate-600 font-medium">{propertyDetails}</p>
                  </div>

                  <div className="pt-6 flex justify-between text-[10px]">
                    <div>
                      <span className="block font-bold">प्राधिकृत अधिकारी</span>
                      <span className="text-slate-500">{bankName}</span>
                    </div>
                    <div className="text-right">
                      <span className="block text-slate-400">डिजिटल स्वाक्षरी</span>
                      <span className="font-bold">{bankBranch}</span>
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Print disclaimer footer */}
            <div className="mt-4 text-[10px] text-slate-400 text-center">
              * The generated document is draft legal template for general guidance purposes. Consult advocate for registry.
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
