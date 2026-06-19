import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { X, Mail, Lock, ShieldAlert, Sparkles, Smartphone, CheckCircle, ArrowRight } from 'lucide-react';

export default function AuthModal({ isOpen, onClose, onLoginSuccess }) {
  const [activeTab, setActiveTab] = useState('email'); // 'email' | 'google' | 'qr'
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [qrScanned, setQrScanned] = useState(false);

  useEffect(() => {
    if (isOpen) {
      // Reset state on open
      setIsLoading(false);
      setSuccess(false);
      setQrScanned(false);
    }
  }, [isOpen]);

  if (!isOpen) return null;

  const handleEmailLogin = (e) => {
    e.preventDefault();
    if (!email || !password) return;
    
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      setSuccess(true);
      setTimeout(() => {
        const username = email.split('@')[0];
        const userData = { name: username, email, avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' };
        localStorage.setItem('user', JSON.stringify(userData));
        onLoginSuccess(userData);
        onClose();
      }, 1200);
    }, 1500);
  };

  const handleGoogleLogin = () => {
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      setSuccess(true);
      setTimeout(() => {
        const userData = { name: 'Rohan Deshmukh', email: 'rohan.deshmukh@gmail.com', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80' };
        localStorage.setItem('user', JSON.stringify(userData));
        onLoginSuccess(userData);
        onClose();
      }, 1200);
    }, 1800);
  };

  const handleQRScanSimulation = () => {
    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      setQrScanned(true);
      setSuccess(true);
      setTimeout(() => {
        const userData = { name: 'Sayali Patil', email: 'sayali.patil@yahoo.com', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80' };
        localStorage.setItem('user', JSON.stringify(userData));
        onLoginSuccess(userData);
        onClose();
      }, 1200);
    }, 2500);
  };

  return (
    <AnimatePresence>
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
        {/* Backdrop */}
        <motion.div 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          onClick={onClose}
          className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        />

        {/* Modal Window */}
        <motion.div 
          initial={{ scale: 0.95, y: 20, opacity: 0 }}
          animate={{ scale: 1, y: 0, opacity: 1 }}
          exit={{ scale: 0.95, y: 20, opacity: 0 }}
          transition={{ duration: 0.3 }}
          className="relative bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col"
        >
          {/* Header Banner */}
          <div className="bg-gradient-to-r from-premium-emerald to-teal-600 px-6 py-8 text-white relative">
            <button 
              onClick={onClose}
              className="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition-colors"
            >
              <X className="h-5 w-5" />
            </button>
            <div className="inline-flex items-center space-x-1.5 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
              <Sparkles className="h-3 w-3" />
              <span>Maharashtra Portal</span>
            </div>
            <h2 className="text-2xl font-extrabold tracking-tight">Secure Sign-In</h2>
            <p className="text-emerald-100 text-sm mt-1">Access instant auction alerts and legal guidance reports.</p>
          </div>

          {/* Navigation Tabs */}
          <div className="flex border-b border-slate-100 bg-slate-50">
            <button 
              onClick={() => setActiveTab('email')}
              className={`flex-1 py-4 text-sm font-semibold border-b-2 transition-all ${activeTab === 'email' ? 'border-premium-emerald text-premium-emerald bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
            >
              Email Login
            </button>
            <button 
              onClick={() => setActiveTab('google')}
              className={`flex-1 py-4 text-sm font-semibold border-b-2 transition-all ${activeTab === 'google' ? 'border-premium-emerald text-premium-emerald bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
            >
              Google
            </button>
            <button 
              onClick={() => setActiveTab('qr')}
              className={`flex-1 py-4 text-sm font-semibold border-b-2 transition-all ${activeTab === 'qr' ? 'border-premium-emerald text-premium-emerald bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
            >
              Scan QR
            </button>
          </div>

          {/* Main Body */}
          <div className="p-6 bg-white min-h-[300px] flex flex-col justify-center">
            {success ? (
              <motion.div 
                initial={{ opacity: 0, scale: 0.8 }}
                animate={{ opacity: 1, scale: 1 }}
                className="flex flex-col items-center justify-center text-center space-y-4 py-8"
              >
                <div className="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center border border-emerald-100 shadow-md">
                  <CheckCircle className="h-10 w-10 animate-bounce" />
                </div>
                <div>
                  <h3 className="text-xl font-bold text-slate-900">Successfully Signed In!</h3>
                  <p className="text-slate-500 text-sm mt-1">Redirecting you to dashboard...</p>
                </div>
              </motion.div>
            ) : isLoading ? (
              <div className="flex flex-col items-center justify-center space-y-4 py-8">
                <div className="w-12 h-12 border-4 border-premium-emerald border-t-transparent rounded-full animate-spin"></div>
                <div className="text-center">
                  <h4 className="font-bold text-slate-700">
                    {activeTab === 'email' && 'Verifying credentials...'}
                    {activeTab === 'google' && 'Connecting Google Account...'}
                    {activeTab === 'qr' && 'Simulating scanner decryption...'}
                  </h4>
                  <p className="text-xs text-slate-400 mt-1">Please keep this window active.</p>
                </div>
              </div>
            ) : (
              <div>
                {/* EMAIL TAB */}
                {activeTab === 'email' && (
                  <form onSubmit={handleEmailLogin} className="space-y-4">
                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                      <div className="relative">
                        <input 
                          type="email" 
                          required
                          value={email}
                          onChange={(e) => setEmail(e.target.value)}
                          placeholder="name@example.com"
                          className="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all font-medium"
                        />
                        <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 h-4.5 w-4.5" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Password</label>
                      <div className="relative">
                        <input 
                          type="password" 
                          required
                          value={password}
                          onChange={(e) => setPassword(e.target.value)}
                          placeholder="••••••••"
                          className="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all font-medium"
                        />
                        <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 h-4.5 w-4.5" />
                      </div>
                    </div>

                    <button 
                      type="submit"
                      className="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center"
                    >
                      Sign In &rarr;
                    </button>
                  </form>
                )}

                {/* GOOGLE TAB */}
                {activeTab === 'google' && (
                  <div className="flex flex-col items-center justify-center space-y-6 py-6 text-center">
                    <div className="p-4 bg-slate-50 rounded-full border border-slate-100 shadow-inner">
                      <svg className="h-10 w-10 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.24 10.285V13.4h6.887C18.2 15.614 15.645 18 12.24 18c-3.3 0-6-2.7-6-6s2.7-6 6-6c1.55 0 2.97.585 4.07 1.635l2.43-2.43C16.995 3.525 14.775 2.5 12.24 2.5c-5.24 0-9.5 4.26-9.5 9.5s4.26 9.5 9.5 9.5c5.07 0 9.27-3.525 9.27-9.5 0-.585-.045-1.155-.135-1.715H12.24z"/>
                      </svg>
                    </div>
                    <div>
                      <h4 className="text-lg font-bold text-slate-900">One-click Google Auth</h4>
                      <p className="text-slate-500 text-sm mt-1">Securely sign in using your pre-authorized Google Account.</p>
                    </div>
                    <button 
                      onClick={handleGoogleLogin}
                      className="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center space-x-2"
                    >
                      <span>Continue with Google</span>
                      <ArrowRight className="h-4 w-4" />
                    </button>
                  </div>
                )}

                {/* QR TAB */}
                {activeTab === 'qr' && (
                  <div className="flex flex-col items-center justify-center space-y-6 text-center">
                    <div className="relative p-4 bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden w-40 h-40 flex items-center justify-center">
                      {/* Grid representation of QR */}
                      <div className="grid grid-cols-5 gap-1.5 w-28 h-28 opacity-90">
                        {/* Blocks to look like a QR code */}
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-transparent"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                        <div className="bg-slate-900 rounded-sm"></div>
                      </div>

                      {/* Moving laser scanner line */}
                      <motion.div 
                        animate={{ top: ['10%', '90%', '10%'] }}
                        transition={{ repeat: Infinity, duration: 2, ease: "linear" }}
                        className="absolute left-[5%] right-[5%] h-0.5 bg-emerald-500 shadow-[0_0_8px_#10b981]"
                      />
                    </div>
                    
                    <div className="px-4">
                      <h4 className="text-base font-bold text-slate-900 flex items-center justify-center">
                        <Smartphone className="h-4.5 w-4.5 mr-2 text-premium-emerald" />
                        Scan with Mobile App
                      </h4>
                      <p className="text-slate-500 text-xs mt-1">Open MahaAuctions Mobile App and aim scanner at this QR code.</p>
                    </div>

                    <button 
                      onClick={handleQRScanSimulation}
                      className="w-full bg-emerald-50 hover:bg-emerald-100 text-premium-emerald border border-emerald-200 font-bold py-3 rounded-xl transition-all flex items-center justify-center space-x-1 text-sm"
                    >
                      <span>Simulate Mobile Scan Detection</span>
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Footer security tag */}
          <div className="bg-slate-50 px-6 py-4 flex items-center justify-center space-x-2 text-xs font-semibold text-slate-500 border-t border-slate-100">
            <ShieldAlert className="h-4 w-4 text-emerald-500" />
            <span>Fully compliant with SARFAESI portal encryption.</span>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
}
