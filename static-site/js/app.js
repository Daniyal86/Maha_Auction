import { cities, properties, agents, advocatesList } from './mockData.js';

// --- GLOBAL STATE ---
const state = {
  user: JSON.parse(localStorage.getItem('user')) || null,
  currentPage: 'home',
  currentParams: {},
  
  // Search state
  searchQuery: '',
  selectedCategory: 'All',
  selectedCity: 'All',
  selectedBank: 'All',
  selectedType: 'All',
  viewMode: 'grid', // 'grid' | 'compare'
  sortBy: 'price-desc',

  // Advisory hub state
  advisoryTab: 'guidance', // 'guidance' | 'draftsman'
  selectedAdvocateProfile: null,
  selectedAdvocateForBooking: advocatesList[0],
  bookingState: {
    name: '',
    email: '',
    date: '',
    topic: 'SARFAESI Title Verification',
    success: false
  },
  draftsmanState: {
    bankName: 'STATE BANK OF INDIA',
    bankBranch: 'STRESS ASSETS MANAGEMENT BRANCH, MUMBAI GP',
    borrowerName: 'M/S PRECISION ENGINEERING PVT. LTD.',
    noticeDate: new Date().toISOString().split('T')[0],
    outstandingDues: '₹ 4,12,50,670',
    propertyDetails: 'PLOT NO. J-24, BHOSARI MIDC, PUNE - 411026 MEASURING 10,000 SQ. FT.',
    language: 'EN' // 'EN' | 'MR'
  },

  // Agent contact modal state
  selectedAgentForContact: null,
  agentContactForm: {
    name: '',
    phone: '',
    message: 'I am interested in scheduling a physical site inspection for an auction property.',
    success: false
  },

  // 7-day trial state
  trialForm: {
    name: '',
    email: '',
    success: false
  },

  // Builder sponsor leads
  builderContacted: false,

  // Property detail scheduler state
  propertySchedule: {
    date: '',
    timeSlot: '11:00 AM',
    phone: '',
    success: false,
    noticeLang: 'EN' // 'EN' | 'MR'
  }
};

// --- ROUTER SYSTEM ---
function navigateTo(hash) {
  window.location.hash = hash;
}

function router() {
  const hash = window.location.hash || '#/';
  
  // Parse route parameters, e.g. #/city/mumbai or #/property/prop-1
  let page = 'home';
  let params = {};
  
  if (hash.startsWith('#/city/')) {
    page = 'city';
    params.cityId = hash.replace('#/city/', '');
  } else if (hash.startsWith('#/property/')) {
    page = 'property';
    params.propertyId = hash.replace('#/property/', '');
  } else if (hash === '#/search') {
    page = 'search';
  } else if (hash === '#/advisory') {
    page = 'advisory';
  } else if (hash === '#/agents') {
    page = 'agents';
  }
  
  state.currentPage = page;
  state.currentParams = params;
  
  // Scroll to top
  window.scrollTo(0, 0);
  
  // Render navbar & active page
  renderNavbar();
  
  if (page === 'home') {
    renderLanding();
  } else if (page === 'search') {
    renderSearchPortal();
  } else if (page === 'advisory') {
    renderAdvisoryHub();
  } else if (page === 'agents') {
    renderAgentsDirectory();
  } else if (page === 'city') {
    renderCityAuctions(params.cityId);
  } else if (page === 'property') {
    renderPropertyDetail(params.propertyId);
  }

  // Refresh Lucide Icons after DOM updates
  if (window.lucide) {
    window.lucide.createIcons();
  }
}

window.addEventListener('hashchange', router);
window.addEventListener('DOMContentLoaded', () => {
  router();
  initGlobalModals();
});

// --- HELPER UTILITIES ---
function formatRupee(num) {
  if (num >= 10000000) return `₹ ${(num / 10000000).toFixed(2)} Cr`;
  if (num >= 100000) return `₹ ${(num / 100000).toFixed(2)} Lakhs`;
  return `₹ ${num.toLocaleString()}`;
}

// --- NAVBAR RENDERING & LOGIC ---
function renderNavbar() {
  const navContainer = document.getElementById('global-navbar');
  if (!navContainer) return;

  const isActive = (pageName) => state.currentPage === pageName;

  navContainer.innerHTML = `
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <!-- Logo -->
        <a href="#/" class="flex items-center space-x-2.5">
          <div class="bg-premium-emerald p-2 rounded-xl text-white shadow-md">
            <i data-lucide="building-2" class="h-6 w-6"></i>
          </div>
          <span class="text-2xl font-extrabold text-premium-text tracking-tight">
            Maha<span class="text-premium-emerald">Auctions</span>
          </span>
        </a>
        
        <!-- Desktop Navigation Links -->
        <div class="hidden lg:flex items-center space-x-8">
          <a href="#/" class="font-semibold text-sm transition-colors ${isActive('home') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}">Home</a>
          <a href="#/search" class="font-semibold text-sm transition-colors ${isActive('search') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}">Data Surfing</a>
          <a href="#/advisory" class="font-semibold text-sm transition-colors ${isActive('advisory') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}">Legal Guidance (Adv)</a>
          <a href="#/agents" class="font-semibold text-sm transition-colors ${isActive('agents') ? 'text-premium-emerald font-bold' : 'text-slate-600 hover:text-premium-emerald'}">Verified Agents</a>
        </div>
        
        <!-- Login / Profile State -->
        <div class="hidden md:flex items-center space-x-4">
          ${state.user ? `
            <div class="flex items-center space-x-3.5 bg-slate-50 border border-slate-200/60 pl-3 pr-2.5 py-1.5 rounded-full shadow-sm">
              <img src="${state.user.avatar}" alt="${state.user.name}" class="h-8 w-8 rounded-full border border-premium-emerald/50 object-cover" />
              <span class="text-sm font-extrabold text-slate-800 tracking-wide">${state.user.name}</span>
              <button id="nav-logout-btn" title="Sign Out" class="p-1.5 text-slate-400 hover:text-red-500 rounded-full hover:bg-slate-100 transition-colors">
                <i data-lucide="log-out" class="h-4.5 w-4.5"></i>
              </button>
            </div>
          ` : `
            <button id="nav-login-btn" class="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center space-x-1.5">
              <i data-lucide="user" class="h-4 w-4"></i>
              <span>Register / Login</span>
            </button>
          `}
        </div>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex items-center">
          <button id="mobile-menu-toggle" class="text-slate-600 hover:text-premium-emerald p-2 rounded-lg">
            <i data-lucide="menu" class="h-6 w-6"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-drawer" class="hidden lg:hidden border-t border-slate-100 bg-white p-4 space-y-3 shadow-inner">
      <a href="#/" class="mobile-nav-link block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('home') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}">Home</a>
      <a href="#/search" class="mobile-nav-link block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('search') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}">Data Surfing</a>
      <a href="#/advisory" class="mobile-nav-link block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('advisory') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}">Legal Guidance (Adv)</a>
      <a href="#/agents" class="mobile-nav-link block py-2.5 px-4 rounded-xl text-base font-semibold ${isActive('agents') ? 'bg-emerald-50 text-premium-emerald' : 'text-slate-600 hover:bg-slate-50'}">Verified Agents</a>

      <div class="border-t border-slate-100 pt-3">
        ${state.user ? `
          <div class="flex items-center justify-between bg-slate-50 border border-slate-200/60 p-3 rounded-2xl">
            <div class="flex items-center space-x-3">
              <img src="${state.user.avatar}" alt="${state.user.name}" class="h-8 w-8 rounded-full object-cover" />
              <span class="text-sm font-bold text-slate-800">${state.user.name}</span>
            </div>
            <button id="mobile-logout-btn" class="flex items-center space-x-1 text-xs font-bold text-red-500 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">
              <i data-lucide="log-out" class="h-3.5 w-3.5"></i>
              <span>Logout</span>
            </button>
          </div>
        ` : `
          <button id="mobile-login-btn" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3 rounded-xl transition-all shadow-md text-center">
            Register / Login
          </button>
        `}
      </div>
    </div>
  `;

  // Bind Navbar Events
  if (!state.user) {
    const loginBtn = document.getElementById('nav-login-btn');
    if (loginBtn) loginBtn.addEventListener('click', showAuthModal);
    
    const mLoginBtn = document.getElementById('mobile-login-btn');
    if (mLoginBtn) mLoginBtn.addEventListener('click', () => {
      toggleMobileMenu(false);
      showAuthModal();
    });
  } else {
    const logoutBtn = document.getElementById('nav-logout-btn');
    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);

    const mLogoutBtn = document.getElementById('mobile-logout-btn');
    if (mLogoutBtn) mLogoutBtn.addEventListener('click', handleLogout);
  }

  const menuToggle = document.getElementById('mobile-menu-toggle');
  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      const drawer = document.getElementById('mobile-drawer');
      drawer.classList.toggle('hidden');
    });
  }

  // Auto-close drawer on link click
  document.querySelectorAll('.mobile-nav-link').forEach(link => {
    link.addEventListener('click', () => toggleMobileMenu(false));
  });
}

function toggleMobileMenu(show) {
  const drawer = document.getElementById('mobile-drawer');
  if (drawer) {
    if (show) drawer.classList.remove('hidden');
    else drawer.classList.add('hidden');
  }
}

function handleLogout() {
  localStorage.removeItem('user');
  state.user = null;
  renderNavbar();
  // If we are on a protected view, redirect to home, else re-render page
  router();
}

// --- GLOBAL MODALS ---
function initGlobalModals() {
  // Bind 7-day trial claims
  const trialClaimBtns = document.querySelectorAll('.claim-trial-trigger');
  trialClaimBtns.forEach(btn => btn.addEventListener('click', showTrialModal));

  // Bind close buttons
  document.getElementById('auth-modal-close').addEventListener('click', hideAuthModal);
  document.getElementById('trial-modal-close').addEventListener('click', hideTrialModal);
  document.getElementById('agent-modal-close').addEventListener('click', hideAgentModal);
  
  // Dynamic tab switcher for auth modal
  const authTabs = document.querySelectorAll('.auth-tab');
  authTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      const activeTab = e.target.getAttribute('data-tab');
      renderAuthModalContent(activeTab);
    });
  });
}

function showAuthModal() {
  document.getElementById('auth-modal-wrapper').classList.remove('hidden');
  renderAuthModalContent('email');
}

function hideAuthModal() {
  document.getElementById('auth-modal-wrapper').classList.add('hidden');
}

function renderAuthModalContent(activeTab = 'email') {
  // Update Tab Header Styles
  document.querySelectorAll('.auth-tab').forEach(tab => {
    if (tab.getAttribute('data-tab') === activeTab) {
      tab.className = "auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-premium-emerald text-premium-emerald bg-white";
    } else {
      tab.className = "auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700";
    }
  });

  const contentBox = document.getElementById('auth-modal-content');
  if (!contentBox) return;

  let html = '';
  if (activeTab === 'email') {
    html = `
      <form id="email-login-form" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
          <div class="relative">
            <input type="email" id="auth-email-input" required placeholder="name@example.com" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all font-medium" />
            <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 h-4.5 w-4.5"></i>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Password</label>
          <div class="relative">
            <input type="password" id="auth-password-input" required placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all font-medium" />
            <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 h-4.5 w-4.5"></i>
          </div>
        </div>
        <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center">
          Sign In &rarr;
        </button>
      </form>
    `;
  } else if (activeTab === 'google') {
    html = `
      <div class="flex flex-col items-center justify-center space-y-6 py-6 text-center">
        <div class="p-4 bg-slate-50 rounded-full border border-slate-100 shadow-inner">
          <svg class="h-10 w-10 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12.24 10.285V13.4h6.887C18.2 15.614 15.645 18 12.24 18c-3.3 0-6-2.7-6-6s2.7-6 6-6c1.55 0 2.97.585 4.07 1.635l2.43-2.43C16.995 3.525 14.775 2.5 12.24 2.5c-5.24 0-9.5 4.26-9.5 9.5s4.26 9.5 9.5 9.5c5.07 0 9.27-3.525 9.27-9.5 0-.585-.045-1.155-.135-1.715H12.24z"/>
          </svg>
        </div>
        <div>
          <h4 class="text-lg font-bold text-slate-900">One-click Google Auth</h4>
          <p class="text-slate-500 text-sm mt-1">Securely sign in using your pre-authorized Google Account.</p>
        </div>
        <button id="google-login-trigger" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center space-x-2">
          <span>Continue with Google</span>
          <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </button>
      </div>
    `;
  } else if (activeTab === 'qr') {
    html = `
      <div class="flex flex-col items-center justify-center space-y-6 text-center">
        <div class="relative p-4 bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden w-40 h-40 flex items-center justify-center">
          <div class="grid grid-cols-5 gap-1.5 w-28 h-28 opacity-90">
            <div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div>
            <div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div>
            <div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div>
            <div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div>
            <div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-transparent"></div><div class="bg-slate-900 rounded-sm"></div><div class="bg-slate-900 rounded-sm"></div>
          </div>
          <!-- Moving scanning line -->
          <div class="absolute left-[5%] right-[5%] h-0.5 bg-emerald-500 shadow-[0_0_8px_#10b981]" style="animation: scanEffect 2s infinite ease-in-out;"></div>
        </div>
        <div class="px-4">
          <h4 class="text-base font-bold text-slate-900 flex items-center justify-center">
            <i data-lucide="smartphone" class="h-4.5 w-4.5 mr-2 text-premium-emerald"></i>
            Scan with Mobile App
          </h4>
          <p class="text-slate-500 text-xs mt-1">Open MahaAuctions Mobile App and aim scanner at this QR code.</p>
        </div>
        <button id="qr-login-trigger" class="w-full bg-emerald-50 hover:bg-emerald-100 text-premium-emerald border border-emerald-200 font-bold py-3 rounded-xl transition-all flex items-center justify-center space-x-1 text-sm">
          <span>Simulate Mobile Scan Detection</span>
        </button>
      </div>
    `;
  }

  contentBox.innerHTML = html;
  if (window.lucide) window.lucide.createIcons();

  // Attach login triggers
  if (activeTab === 'email') {
    document.getElementById('email-login-form').addEventListener('submit', (e) => {
      e.preventDefault();
      const email = document.getElementById('auth-email-input').value;
      const username = email.split('@')[0];
      simulateLoginLoading('Verifying credentials...', {
        name: username,
        email: email,
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80'
      });
    });
  } else if (activeTab === 'google') {
    document.getElementById('google-login-trigger').addEventListener('click', () => {
      simulateLoginLoading('Connecting Google Account...', {
        name: 'Rohan Deshmukh',
        email: 'rohan.deshmukh@gmail.com',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80'
      });
    });
  } else if (activeTab === 'qr') {
    document.getElementById('qr-login-trigger').addEventListener('click', () => {
      simulateLoginLoading('Simulating scanner decryption...', {
        name: 'Sayali Patil',
        email: 'sayali.patil@yahoo.com',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80'
      });
    });
  }
}

function simulateLoginLoading(message, userData) {
  const contentBox = document.getElementById('auth-modal-content');
  if (!contentBox) return;

  // Loader state
  contentBox.innerHTML = `
    <div class="flex flex-col items-center justify-center space-y-4 py-8">
      <div class="w-12 h-12 border-4 border-premium-emerald border-t-transparent rounded-full animate-spin"></div>
      <div class="text-center">
        <h4 class="font-bold text-slate-700">${message}</h4>
        <p class="text-xs text-slate-400 mt-1">Please keep this window active.</p>
      </div>
    </div>
  `;

  setTimeout(() => {
    // Success State
    contentBox.innerHTML = `
      <div class="flex flex-col items-center justify-center text-center space-y-4 py-8">
        <div class="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center border border-emerald-100 shadow-md">
          <i data-lucide="check-circle" class="h-10 w-10 text-premium-emerald"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-slate-900">Successfully Signed In!</h3>
          <p class="text-slate-500 text-sm mt-1">Redirecting you to dashboard...</p>
        </div>
      </div>
    `;
    if (window.lucide) window.lucide.createIcons();

    setTimeout(() => {
      state.user = userData;
      localStorage.setItem('user', JSON.stringify(userData));
      renderNavbar();
      hideAuthModal();
      router();
    }, 1200);
  }, 1500);
}

// 7-Day Trial Modal Trigger
function showTrialModal() {
  document.getElementById('trial-modal-wrapper').classList.remove('hidden');
  renderTrialModalContent();
}

function hideTrialModal() {
  document.getElementById('trial-modal-wrapper').classList.add('hidden');
  state.trialForm.success = false;
  state.trialForm.name = '';
  state.trialForm.email = '';
}

function renderTrialModalContent() {
  const contentBox = document.getElementById('trial-modal-content');
  if (!contentBox) return;

  if (state.trialForm.success) {
    contentBox.innerHTML = `
      <div class="text-center py-6 space-y-4">
        <div class="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
          <i data-lucide="check-circle" class="h-10 w-10 text-premium-emerald"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-slate-900 text-lg">7-Day Free Trial Activated!</h3>
          <p class="text-slate-400 text-xs mt-1">Premium SMS & title reports alerts started successfully.</p>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed">
          Check your email inbox for your direct access key and immediate alerts for new bank auctions in your preferred districts.
        </p>
      </div>
    `;
  } else {
    contentBox.innerHTML = `
      <div>
        <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
          <div>
            <span class="text-[10px] text-premium-gold font-black uppercase tracking-wider block">Access Premium Suite</span>
            <h3 class="font-extrabold text-slate-900 text-lg">Claim Your 7-Day Trial</h3>
          </div>
          <button id="trial-close-x" class="text-slate-400 hover:text-slate-600 font-black text-sm">&times;</button>
        </div>

        <form id="trial-request-form" class="space-y-4 text-xs font-semibold text-slate-500">
          <p class="text-slate-500 leading-normal">
            Get premium legal valuations, ready reckoner reports, SMS notifications, and title alerts sent to your phone for 7 days.
          </p>

          <div>
            <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Full Name</label>
            <input type="text" id="trial-name-input" required placeholder="e.g. Vikram Shinde" value="${state.trialForm.name}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <div>
            <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Email Address</label>
            <input type="email" id="trial-email-input" required placeholder="name@example.com" value="${state.trialForm.email}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md text-sm text-center uppercase tracking-wider">
            Start Free Trial Alerts
          </button>
        </form>
      </div>
    `;

    document.getElementById('trial-close-x').addEventListener('click', hideTrialModal);
    document.getElementById('trial-request-form').addEventListener('submit', (e) => {
      e.preventDefault();
      state.trialForm.name = document.getElementById('trial-name-input').value;
      state.trialForm.email = document.getElementById('trial-email-input').value;
      state.trialForm.success = true;
      renderTrialModalContent();
      
      setTimeout(() => {
        hideTrialModal();
      }, 2500);
    });
  }
  if (window.lucide) window.lucide.createIcons();
}

// Agent Connection Modal
function showAgentModal(agent) {
  state.selectedAgentForContact = agent;
  document.getElementById('agent-modal-wrapper').classList.remove('hidden');
  renderAgentModalContent();
}

function hideAgentModal() {
  document.getElementById('agent-modal-wrapper').classList.add('hidden');
  state.agentContactForm.success = false;
  state.agentContactForm.name = '';
  state.agentContactForm.phone = '';
}

function renderAgentModalContent() {
  const contentBox = document.getElementById('agent-modal-content');
  if (!contentBox) return;

  const agent = state.selectedAgentForContact;
  if (!agent) return;

  if (state.agentContactForm.success) {
    contentBox.innerHTML = `
      <div class="text-center py-6 space-y-4">
        <div class="w-16 h-16 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
          <i data-lucide="check-circle" class="h-10 w-10 text-premium-emerald"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-slate-900 text-lg">Request Dispatched!</h3>
          <p class="text-slate-500 text-xs mt-1">SMS Alerts and Email alerts sent successfully.</p>
        </div>
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
          Agent <span class="font-bold text-slate-800">${agent.name}</span> has been alerted and will contact you directly on your number within 15 minutes.
        </div>
      </div>
    `;
  } else {
    contentBox.innerHTML = `
      <div>
        <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-4">
          <div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Direct Consultation</span>
            <h3 class="font-extrabold text-slate-900 text-lg">Alert ${agent.name}</h3>
          </div>
          <button id="agent-close-x" class="text-slate-400 hover:text-slate-600 font-black text-sm p-1">&times;</button>
        </div>

        <form id="agent-contact-form" class="space-y-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Full Name</label>
            <input type="text" id="agent-contact-name" required value="${state.agentContactForm.name}" placeholder="e.g. Rahul Deshmukh" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Phone Number</label>
            <input type="tel" id="agent-contact-phone" required value="${state.agentContactForm.phone}" placeholder="e.g. +91 99999 88888" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Message Preference</label>
            <textarea id="agent-contact-message" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800">${state.agentContactForm.message}</textarea>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md text-sm text-center">
            Send Direct SMS Notification
          </button>
        </form>
      </div>
    `;

    document.getElementById('agent-close-x').addEventListener('click', hideAgentModal);
    document.getElementById('agent-contact-form').addEventListener('submit', (e) => {
      e.preventDefault();
      state.agentContactForm.name = document.getElementById('agent-contact-name').value;
      state.agentContactForm.phone = document.getElementById('agent-contact-phone').value;
      state.agentContactForm.message = document.getElementById('agent-contact-message').value;
      state.agentContactForm.success = true;
      renderAgentModalContent();

      setTimeout(() => {
        hideAgentModal();
      }, 3000);
    });
  }
  if (window.lucide) window.lucide.createIcons();
}

// --- MAP COMPONENT IN VANILLA ---
let activeMapInstance = null;
function initMap(mapContainerId = 'map-container') {
  const el = document.getElementById(mapContainerId);
  if (!el) return;

  // Cleanup old map instance if page changes
  if (activeMapInstance) {
    activeMapInstance.remove();
    activeMapInstance = null;
  }

  // Centering Maharashtra
  const center = [19.7515, 75.7139];
  const zoom = 6.4;

  const map = L.map(mapContainerId, {
    scrollWheelZoom: false,
    zoomControl: true
  }).setView(center, zoom);

  activeMapInstance = map;

  // Add CartoDB voyager tiles
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
  }).addTo(map);

  // Custom icon markup
  const customIconMarkup = `
    <div class="relative flex flex-col items-center justify-center group cursor-pointer" style="width: 40px; height: 40px;">
      <div class="w-8 h-8 rounded-full bg-premium-emerald/30 absolute animate-ping" style="top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
      <svg class="w-10 h-10 text-premium-emerald drop-shadow-lg transition-colors hover:text-emerald-500 relative z-10" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
      </svg>
    </div>
  `;

  const customMarkerIcon = new L.DivIcon({
    className: 'custom-leaflet-marker',
    html: customIconMarkup,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
  });

  // Load geojson boundary
  fetch('./maharashtra.geojson')
    .then(res => res.json())
    .then(geoData => {
      if (activeMapInstance === map) {
        L.geoJSON(geoData, {
          style: {
            color: '#10b981',
            weight: 3,
            opacity: 0.6,
            fillColor: '#10b981',
            fillOpacity: 0.08,
            dashArray: '6, 6'
          }
        }).addTo(map);
      }
    })
    .catch(err => console.error("Error loading boundary geojson:", err));

  // Add markers
  cities.forEach(city => {
    const marker = L.marker([city.coordinates.lat, city.coordinates.lng], { icon: customMarkerIcon }).addTo(map);
    
    // Custom premium tooltip
    const tooltipContent = `
      <div class="text-center p-1 w-42 bg-white rounded-xl shadow-lg border border-slate-100">
        <span class="font-extrabold text-premium-emerald text-xl block mb-1">${city.name}</span>
        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block mb-2 border-b border-slate-100 pb-2">
          ${city.propertyCount} Properties
        </span>
        <span class="w-full inline-block bg-emerald-50 text-premium-emerald font-bold py-1.5 rounded-lg text-xs hover:bg-premium-emerald hover:text-white transition-all">
          Click to View &rarr;
        </span>
      </div>
    `;

    marker.bindTooltip(tooltipContent, {
      direction: 'top',
      offset: [0, -25],
      opacity: 1,
      className: 'premium-tooltip'
    });

    marker.on('click', () => {
      navigateTo(`#/city/${city.id}`);
    });
  });
}

// --- LANDING PAGE RENDERING ---
function renderLanding() {
  const app = document.getElementById('app-content');
  if (!app) return;

  const featured = properties.slice(0, 3);

  app.className = "bg-white text-left";
  app.innerHTML = `
    <!-- 7-DAY FREE TRIAL PERSISTENT BANNER -->
    <div class="bg-gradient-to-r from-premium-gold to-amber-500 py-3 px-4 text-white text-center text-xs font-extrabold tracking-wide flex justify-center items-center space-x-2.5 shadow-md">
      <i data-lucide="badge-alert" class="h-4.5 w-4.5 animate-bounce"></i>
      <span>LIMITED PERIOD: Get direct DM registry alerts and legal valuations free for 7 days!</span>
      <button class="claim-trial-trigger bg-slate-900 text-white hover:bg-black text-[10px] font-black uppercase px-4 py-1.5 rounded-lg shadow transition-all ml-2">
        Claim 7-Day Trial
      </button>
    </div>

    <!-- HERO SECTION -->
    <section class="relative min-h-[90vh] flex flex-col justify-center overflow-hidden pt-10 pb-20 bg-slate-50/50">
      <div class="absolute top-0 right-0 w-1/3 h-full bg-slate-50 skew-x-[-10deg] transform origin-top -z-10 border-l border-slate-100"></div>
      <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-emerald-50 rounded-full blur-[120px] pointer-events-none"></div>

      <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col xl:flex-row items-center gap-12 z-10">
        
        <!-- Left Text -->
        <div class="xl:w-[45%] flex flex-col space-y-8">
          <div class="inline-flex items-center space-x-2 bg-emerald-50 text-premium-emerald px-5 py-2.5 rounded-full w-fit border border-emerald-100 shadow-sm">
            <i data-lucide="map-pin" class="h-4 w-4"></i>
            <span class="text-sm font-bold tracking-wide uppercase">Maharashtra District Council</span>
          </div>
          
          <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.15] tracking-tight">
            Premium Statutory <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-premium-emerald to-teal-500 drop-shadow-sm">Auction & Heavy Deposit</span> Portal
          </h1>
          
          <p class="text-xl text-slate-600 leading-relaxed font-light">
            Explore vetted court auctions, private seller listings, monthly rentals, and high-value heavy deposit flats verified under ready reckoner valuations.
          </p>
          
          <form id="hero-search-form" class="relative max-w-xl w-full">
            <input type="text" id="hero-search-input" placeholder="Search by city, Listing ID, bank name..." class="w-full bg-white border border-slate-300 text-slate-800 rounded-2xl py-5 pl-14 pr-32 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all text-lg shadow-lg font-medium" />
            <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 h-6 w-6"></i>
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold px-8 py-3 rounded-xl transition-all shadow-md hover:shadow-lg">
              Search
            </button>
          </form>

          <!-- Quick stats -->
          <div class="flex gap-10 pt-6 border-t border-slate-200 mt-4">
            <div class="flex items-center space-x-4">
              <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><i data-lucide="building-2" class="text-premium-emerald h-7 w-7"></i></div>
              <div>
                <div class="text-3xl font-extrabold text-slate-900">1,500+</div>
                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wide">Live Notices</div>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-100"><i data-lucide="trending-up" class="text-premium-emerald h-7 w-7"></i></div>
              <div>
                <div class="text-3xl font-extrabold text-slate-900">₹ 8.5K Cr</div>
                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wide">Market Reserves</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Map with district filters -->
        <div class="xl:w-[55%] w-full flex flex-col items-center mt-12 xl:mt-0">
          <div class="mb-4 bg-white/80 p-3 rounded-2xl border border-slate-200 flex flex-wrap gap-2 justify-center shadow-sm w-full max-w-[800px]">
            <span class="text-xs font-bold text-slate-400 uppercase flex items-center mr-2">Quick Cities Highlight:</span>
            ${cities.slice(0, 6).map(city => `
              <a href="#/city/${city.id}" class="bg-slate-50 hover:bg-premium-emerald hover:text-white border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-black text-slate-700 transition-all flex items-center space-x-1">
                <i data-lucide="map-pin" class="h-3 w-3"></i>
                <span>${city.name}</span>
              </a>
            `).join('')}
          </div>

          <div class="w-full max-w-[800px]">
            <div id="map-container" class="relative w-full aspect-[4/3] max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-50 group">
              <!-- Leaflet mounts here -->
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED PROPERTIES SECTION -->
    <section class="py-20 bg-slate-50 border-t border-slate-200">
      <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
          <div>
            <h2 class="text-4xl font-extrabold text-slate-900 mb-4">Featured Listings</h2>
            <p class="text-lg text-slate-600">Handpicked premium properties currently open for bidding or lease.</p>
          </div>
          <a href="#/search" class="hidden md:flex items-center text-premium-emerald font-bold hover:underline">
            Surround Data Search <i data-lucide="arrow-right" class="ml-2 h-5 w-5"></i>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
          ${featured.map(property => {
            const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
            const discountPct = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
            return `
              <div class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
                <div class="relative h-64 overflow-hidden">
                  <img src="${property.image}" alt="${property.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div class="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
                    ${property.category === 'Auction' ? '🏦 Bank Auction' : property.category}
                  </div>
                  <div class="absolute bottom-4 left-4 bg-slate-900/80 text-white font-bold text-xs px-2 py-1 rounded backdrop-blur border border-slate-700">
                    ${property.listingId}
                  </div>
                </div>
                <div class="p-6 flex-grow flex flex-col">
                  <h3 class="text-2xl font-bold text-slate-900 mb-2 line-clamp-2">${property.title}</h3>
                  <p class="text-slate-500 text-sm mb-6 line-clamp-1 flex items-center font-medium">
                    <i data-lucide="map-pin" class="h-4 w-4 mr-1 inline text-slate-400"></i> ${property.address}
                  </p>
                  
                  <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 mb-6 flex justify-between items-center">
                    <div>
                      <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wide block mb-1">Reserve / Listed Price</span>
                      <span class="text-premium-emerald text-xl font-extrabold flex items-center">
                        <i data-lucide="indian-rupee" class="h-5 w-5 mr-0.5"></i>${property.reservePrice}
                      </span>
                    </div>
                    ${hasDiscount ? `
                      <span class="bg-premium-emerald text-white text-[9px] font-black px-2 py-1 rounded uppercase tracking-wider">
                        -${discountPct}% Below Market
                      </span>
                    ` : ''}
                  </div>

                  <a href="#/property/${property.id}" class="mt-auto w-full text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-extrabold py-3.5 rounded-xl transition-all border-2 border-premium-emerald shadow-sm">
                    Inspect Listing &rarr;
                  </a>
                </div>
              </div>
            `;
          }).join('')}
        </div>
      </div>
    </section>

    <!-- BUILDER ADS ON WEB PROMOTIONAL BLOCK -->
    <section class="py-16 bg-white border-t border-slate-200">
      <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
          <span class="text-xs font-black text-premium-gold uppercase tracking-wider flex items-center">
            <i data-lucide="sparkles" class="h-4 w-4 mr-1 text-premium-gold"></i> Sponsored Premium Launches
          </span>
          <h3 class="text-2xl font-black text-slate-900 mt-1">Builder Showcase Campaigns</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Ad 1 -->
          <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 md:p-8 text-white border border-slate-800 relative overflow-hidden shadow-xl flex flex-col justify-between min-h-[260px]">
            <div class="absolute top-0 right-0 bg-premium-gold text-slate-950 text-[10px] font-black uppercase px-5 py-1.5 rounded-bl-2xl">
              0% Pre-EMI Launch
            </div>

            <div class="space-y-3">
              <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">LODHA GROUP PRESENTS</span>
              <h4 class="text-2xl font-black tracking-tight text-white">Lodha Amara - Premium Thane Residency</h4>
              <p class="text-slate-300 text-sm font-light max-w-md">
                Experience standard 2 & 3 BHK forest-themed homes in Thane West with pool, clubhouse, and private gardens. Starting ₹89 Lakhs.
              </p>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-white/10 mt-6">
              <span class="text-premium-gold font-bold text-base">Booking Open | Pay 5% Now</span>
              
              <div id="builder-lead-action-box">
                ${state.builderContacted ? `
                  <span class="text-emerald-400 font-extrabold text-xs flex items-center">
                    <i data-lucide="check-circle-2" class="h-4 w-4 mr-1"></i> Call request received!
                  </span>
                ` : `
                  <button id="builder-brochure-btn" class="bg-white hover:bg-slate-100 text-slate-900 font-black px-6 py-2.5 rounded-xl transition-all text-xs uppercase">
                    Receive Builder Brochure
                  </button>
                `}
              </div>
            </div>
          </div>

          <!-- Ad 2 -->
          <div class="bg-gradient-to-r from-slate-900 to-emerald-950 rounded-3xl p-6 md:p-8 text-white border border-slate-800 relative overflow-hidden shadow-xl flex flex-col justify-between min-h-[260px]">
            <div class="absolute top-0 right-0 bg-premium-emerald text-white text-[10px] font-black uppercase px-5 py-1.5 rounded-bl-2xl">
              Ready possession
            </div>

            <div class="space-y-3">
              <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">GODREJ PROPERTIES</span>
              <h4 class="text-2xl font-black tracking-tight text-white">Godrej Horizon - Luxury Pune Living</h4>
              <p class="text-slate-300 text-sm font-light max-w-md">
                Signature sky-lounge residences located near prominent Pune IT hubs. Instant registry verified with zero developmental taxes.
              </p>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-white/10 mt-6">
              <span class="text-premium-emerald font-extrabold text-base">Starting ₹ 1.20 Cr Only</span>
              <button class="claim-trial-trigger bg-premium-gold hover:bg-amber-600 text-slate-950 font-black px-6 py-2.5 rounded-xl transition-all text-xs uppercase">
                Book Priority Tour
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS SECTION -->
    <section class="py-24 bg-slate-50 border-t border-slate-200">
      <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-extrabold text-slate-900 mb-4">How It Works</h2>
        <p class="text-lg text-slate-600 max-w-2xl mx-auto mb-16">The transparent, secure, and straightforward process to acquiring your next high-value asset through bank auctions.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
              <i data-lucide="search" class="h-10 w-10 text-premium-emerald"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-3">1. Find Property</h3>
            <p class="text-slate-600 leading-relaxed text-sm">Search through thousands of verified properties across Maharashtra using our advanced map or filters.</p>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
              <i data-lucide="shield-check" class="h-10 w-10 text-premium-emerald"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-3">2. Register & Pay EMD</h3>
            <p class="text-slate-600 leading-relaxed text-sm">Submit your KYC documents and pay the Earnest Money Deposit securely to participate in the bidding.</p>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 border border-emerald-100 shadow-sm">
              <i data-lucide="gavel" class="h-10 w-10 text-premium-emerald"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-3">3. Bid & Win</h3>
            <p class="text-slate-600 leading-relaxed text-sm">Participate in the live e-auction on the scheduled date. Win the property and take physical possession.</p>
          </div>
        </div>
      </div>
    </section>
  `;

  // Bind Events on Landing
  // Search Form Submit
  const searchForm = document.getElementById('hero-search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const q = document.getElementById('hero-search-input').value.trim();
      state.searchQuery = q;
      navigateTo('#/search');
    });
  }

  // Builder brochure submit
  const brochureBtn = document.getElementById('builder-brochure-btn');
  if (brochureBtn) {
    brochureBtn.addEventListener('click', () => {
      state.builderContacted = true;
      const box = document.getElementById('builder-lead-action-box');
      box.innerHTML = `
        <span class="text-emerald-400 font-extrabold text-xs flex items-center">
          <i data-lucide="check-circle" class="h-4 w-4 mr-1"></i> Call request received!
        </span>
      `;
      if (window.lucide) window.lucide.createIcons();
    });
  }

  // Re-bind trial buttons in case page just loaded
  const trialClaimBtns = app.querySelectorAll('.claim-trial-trigger');
  trialClaimBtns.forEach(btn => btn.addEventListener('click', showTrialModal));

  // Initialize Map
  initMap('map-container');
}

// --- SEARCH PORTAL RENDERING ---
function renderSearchPortal() {
  const app = document.getElementById('app-content');
  if (!app) return;

  // Filter listings
  let filtered = properties.filter(p => {
    // Keyword match
    if (state.searchQuery.trim() !== '') {
      const q = state.searchQuery.toLowerCase();
      const match = p.title.toLowerCase().includes(q) || 
                    p.address.toLowerCase().includes(q) ||
                    p.listingId.toLowerCase().includes(q) ||
                    (p.borrower && p.borrower.toLowerCase().includes(q)) ||
                    (p.bank && p.bank.toLowerCase().includes(q));
      if (!match) return false;
    }
    // Category match
    if (state.selectedCategory !== 'All' && p.category !== state.selectedCategory) return false;
    // City match
    if (state.selectedCity !== 'All' && p.cityId !== state.selectedCity) return false;
    // Bank match
    if (state.selectedBank !== 'All' && p.bank !== state.selectedBank) return false;
    // Property Type match
    if (state.selectedType !== 'All' && p.type !== state.selectedType) return false;

    return true;
  });

  // Sort operations
  if (state.sortBy === 'price-desc') {
    filtered.sort((a, b) => b.numericPrice - a.numericPrice);
  } else if (state.sortBy === 'price-asc') {
    filtered.sort((a, b) => a.numericPrice - b.numericPrice);
  } else if (state.sortBy === 'savings-desc') {
    filtered.sort((a, b) => {
      const diffA = (a.numericGovValuation - a.numericPrice) / a.numericGovValuation;
      const diffB = (b.numericGovValuation - b.numericPrice) / b.numericGovValuation;
      return (diffB || 0) - (diffA || 0);
    });
  }

  app.className = "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg";
  app.innerHTML = `
    <!-- Title Header -->
    <div class="mb-10 text-left">
      <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
        Advanced <span class="text-premium-emerald">Data Surfing</span> Portal
      </h1>
      <p class="text-slate-500 text-lg mt-2">
        Surround-search thousands of registered government ready reckoner properties, heavy deposit flats, and premium court auctions.
      </p>
    </div>

    <!-- Main Stats Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 border border-slate-700 shadow-xl">
      <div class="flex items-center space-x-4">
        <div class="bg-premium-emerald p-3.5 rounded-2xl">
          <i data-lucide="percent" class="h-7 w-7 text-white"></i>
        </div>
        <div class="text-left">
          <div class="text-2xl font-black text-premium-emerald">₹ Average 24.8% Below Market</div>
          <div class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Government valuation compare savings rate</div>
        </div>
      </div>

      <div class="flex items-center space-x-6">
        <div class="text-center md:text-right">
          <div class="text-2xl font-extrabold">${filtered.length} Matches</div>
          <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Filtered Listings</div>
        </div>
        
        <!-- Toggle buttons -->
        <div class="flex bg-slate-800/80 p-1.5 rounded-xl border border-slate-700">
          <button id="view-mode-grid-btn" class="p-2 rounded-lg transition-all ${state.viewMode === 'grid' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}" title="Grid View">
            <i data-lucide="grid" class="h-5 w-5"></i>
          </button>
          <button id="view-mode-compare-btn" class="p-2 rounded-lg transition-all ${state.viewMode === 'compare' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}" title="Valuation Comparison Sheet">
            <i data-lucide="table" class="h-5 w-5"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Interactive Filters Grid -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm mb-10 space-y-6 text-left">
      
      <!-- Search Input Box -->
      <div class="relative">
        <input type="text" id="search-portal-input" value="${state.searchQuery}" placeholder="Search by Listing ID (e.g. MA-2026-001), keywords, bank name, borrower..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl py-4.5 pl-14 pr-4 focus:outline-none focus:border-premium-emerald focus:ring-4 focus:ring-emerald-50 transition-all text-base font-semibold shadow-inner" />
        <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 h-5.5 w-5.5"></i>
      </div>

      <!-- Categories Chips -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Listing Category</label>
        <div class="flex flex-wrap gap-2.5">
          ${['All', 'Auction', 'Rental', 'Heavy Deposit', 'Seller Listed'].map(cat => `
            <button class="category-chip-btn px-4.5 py-2.5 rounded-xl text-sm font-bold border transition-all ${state.selectedCategory === cat ? 'bg-premium-emerald text-white border-premium-emerald shadow-md' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}" data-cat="${cat}">
              ${cat === 'All' ? 'All Categories' : cat}
            </button>
          `).join('')}
        </div>
      </div>

      <!-- Cities badging -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Major Cities In Maharashtra</label>
        <div class="flex flex-wrap gap-2">
          <button class="city-chip-btn px-4 py-2 rounded-xl text-sm font-bold border transition-all ${state.selectedCity === 'All' ? 'bg-premium-emerald text-white border-premium-emerald shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}" data-city="All">
            All Cities (${properties.length})
          </button>
          ${cities.map(city => {
            const count = properties.filter(p => p.cityId === city.id).length;
            return `
              <button class="city-chip-btn px-4 py-2 rounded-xl text-sm font-bold border transition-all flex items-center space-x-1.5 ${state.selectedCity === city.id ? 'bg-premium-emerald text-white border-premium-emerald shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200/80 hover:bg-slate-100'}" data-city="${city.id}">
                <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                <span>${city.name}</span>
                <span class="text-xs px-1.5 py-0.5 rounded-full ${state.selectedCity === city.id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'}">${count}</span>
              </button>
            `;
          }).join('')}
        </div>
      </div>

      <!-- Dropdowns Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
            <i data-lucide="landmark" class="h-3.5 w-3.5 mr-1 text-slate-400"></i> Authorized Banks
          </label>
          <select id="bank-filter-select" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold">
            <option value="All">All Banking Institutions</option>
            ${['State Bank of India', 'ICICI Bank', 'HDFC Bank', 'Bank of Baroda', 'Kotak Mahindra Bank', 'Central Bank of India', 'Union Bank of India'].map(bank => `
              <option value="${bank}" ${state.selectedBank === bank ? 'selected' : ''}>${bank}</option>
            `).join('')}
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
            <i data-lucide="tag" class="h-3.5 w-3.5 mr-1 text-slate-400"></i> Property Classification
          </label>
          <select id="type-filter-select" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold">
            <option value="All">All Classifications</option>
            ${['Residential', 'Commercial', 'Industrial', 'Agricultural'].map(type => `
              <option value="${type}" ${state.selectedType === type ? 'selected' : ''}>${type}</option>
            `).join('')}
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center">
            <i data-lucide="arrow-up-down" class="h-3.5 w-3.5 mr-1 text-slate-400"></i> Sort Grid List By
          </label>
          <select id="sort-filter-select" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl py-3 px-4 focus:outline-none focus:border-premium-emerald font-semibold">
            <option value="price-desc" ${state.sortBy === 'price-desc' ? 'selected' : ''}>Price: High to Low</option>
            <option value="price-asc" ${state.sortBy === 'price-asc' ? 'selected' : ''}>Price: Low to High</option>
            <option value="savings-desc" ${state.sortBy === 'savings-desc' ? 'selected' : ''}>Discounts: Highest Savings %</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Listings output -->
    <div id="search-listings-container">
      ${renderSearchListings(filtered)}
    </div>
  `;

  // Bind Search Portal Events
  // Search input typing
  const searchInput = document.getElementById('search-portal-input');
  searchInput.addEventListener('input', (e) => {
    state.searchQuery = e.target.value;
    // Re-filter and re-render only the listings box
    const container = document.getElementById('search-listings-container');
    const newFiltered = properties.filter(applyAllFilters);
    sortFilteredList(newFiltered);
    container.innerHTML = renderSearchListings(newFiltered);
    if (window.lucide) window.lucide.createIcons();
  });

  // View switchers
  document.getElementById('view-mode-grid-btn').addEventListener('click', () => {
    state.viewMode = 'grid';
    renderSearchPortal();
  });
  document.getElementById('view-mode-compare-btn').addEventListener('click', () => {
    state.viewMode = 'compare';
    renderSearchPortal();
  });

  // Category chips click
  document.querySelectorAll('.category-chip-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      state.selectedCategory = e.currentTarget.getAttribute('data-cat');
      renderSearchPortal();
    });
  });

  // City chips click
  document.querySelectorAll('.city-chip-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      state.selectedCity = e.currentTarget.getAttribute('data-city');
      renderSearchPortal();
    });
  });

  // Dropdown selections
  document.getElementById('bank-filter-select').addEventListener('change', (e) => {
    state.selectedBank = e.target.value;
    renderSearchPortal();
  });

  document.getElementById('type-filter-select').addEventListener('change', (e) => {
    state.selectedType = e.target.value;
    renderSearchPortal();
  });

  document.getElementById('sort-filter-select').addEventListener('change', (e) => {
    state.sortBy = e.target.value;
    renderSearchPortal();
  });
}

function applyAllFilters(p) {
  if (state.searchQuery.trim() !== '') {
    const q = state.searchQuery.toLowerCase();
    const match = p.title.toLowerCase().includes(q) || 
                  p.address.toLowerCase().includes(q) ||
                  p.listingId.toLowerCase().includes(q) ||
                  (p.borrower && p.borrower.toLowerCase().includes(q)) ||
                  (p.bank && p.bank.toLowerCase().includes(q));
    if (!match) return false;
  }
  if (state.selectedCategory !== 'All' && p.category !== state.selectedCategory) return false;
  if (state.selectedCity !== 'All' && p.cityId !== state.selectedCity) return false;
  if (state.selectedBank !== 'All' && p.bank !== state.selectedBank) return false;
  if (state.selectedType !== 'All' && p.type !== state.selectedType) return false;
  return true;
}

function sortFilteredList(list) {
  if (state.sortBy === 'price-desc') {
    list.sort((a, b) => b.numericPrice - a.numericPrice);
  } else if (state.sortBy === 'price-asc') {
    list.sort((a, b) => a.numericPrice - b.numericPrice);
  } else if (state.sortBy === 'savings-desc') {
    list.sort((a, b) => {
      const diffA = (a.numericGovValuation - a.numericPrice) / a.numericGovValuation;
      const diffB = (b.numericGovValuation - b.numericPrice) / b.numericGovValuation;
      return (diffB || 0) - (diffA || 0);
    });
  }
}

function renderSearchListings(filtered) {
  if (filtered.length === 0) {
    return `
      <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 shadow-sm">
        <i data-lucide="landmark" class="h-16 w-16 text-slate-300 mx-auto mb-4 animate-pulse"></i>
        <h3 class="text-2xl font-bold text-slate-800">No properties matched the criteria</h3>
        <p class="text-slate-500 mt-2">Try relaxing your search terms or expanding your geographic filter.</p>
      </div>
    `;
  }

  if (state.viewMode === 'grid') {
    return `
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        ${filtered.map(property => {
          const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
          const discountPercent = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
          return `
            <div class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col text-left">
              <div class="relative h-60 overflow-hidden">
                <img src="${property.image}" alt="${property.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-black px-3 py-1.5 rounded-lg uppercase tracking-wider border border-slate-200 shadow-sm">
                  ${property.category === 'Auction' ? '🏦 BANK AUCTION' : property.category}
                </div>
                <div class="absolute bottom-4 left-4">
                  <span class="bg-slate-900/80 text-white font-black text-xs px-2.5 py-1.5 rounded-lg backdrop-blur border border-slate-700 shadow-sm tracking-wider">
                    ${property.listingId}
                  </span>
                </div>
              </div>

              <div class="p-6 flex-grow flex flex-col">
                <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2">${property.title}</h3>
                <p class="text-slate-500 text-sm mb-4 line-clamp-1 flex items-center font-medium">
                  <i data-lucide="map-pin" class="h-4 w-4 mr-1 text-slate-400"></i> ${property.address}
                </p>

                <div class="space-y-3.5 mb-6 flex-grow">
                  <div class="bg-emerald-50/80 p-3 rounded-xl border border-emerald-100 flex justify-between items-center">
                    <div>
                      <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider block">Reserve / Listed Price</span>
                      <span class="text-premium-emerald text-lg font-extrabold">${property.reservePrice}</span>
                    </div>
                    ${hasDiscount ? `
                      <div class="bg-premium-emerald text-white text-[10px] font-bold px-2 py-1 rounded-md text-right uppercase">
                        Save ${discountPercent}%
                      </div>
                    ` : ''}
                  </div>

                  <div class="flex justify-between items-center px-1 text-xs font-semibold">
                    <span class="text-slate-500">Government Ready Reckoner:</span>
                    <span class="text-slate-800 font-extrabold">${property.governmentValuation || 'N/A'}</span>
                  </div>

                  <div class="flex justify-between items-center px-1 text-xs font-semibold">
                    <span class="text-slate-500">Classification:</span>
                    <span class="text-slate-800 font-bold">${property.type}</span>
                  </div>
                </div>

                <a href="#/property/${property.id}" class="w-full block text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-bold py-3.5 rounded-xl transition-all border-2 border-premium-emerald shadow-sm">
                  Inspect Property Details &rarr;
                </a>
              </div>
            </div>
          `;
        }).join('')}
      </div>
    `;
  } else {
    // COMPARE SHEET TABLE VIEW
    return `
      <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden text-left">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-900 text-white border-b border-slate-800">
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Listing ID</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Property Name</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Auction / Seller Price</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Ready Reckoner Val.</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider text-center">Net Discount</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider">Assigned Institution</th>
                <th class="py-5 px-6 font-extrabold text-sm uppercase tracking-wider text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              ${filtered.map(property => {
                const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
                const discountPercent = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
                
                return `
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-4 px-6 font-extrabold text-premium-emerald text-sm whitespace-nowrap">
                      ${property.listingId}
                    </td>
                    <td class="py-4 px-6">
                      <div>
                        <span class="font-bold text-slate-800 block text-base line-clamp-1">${property.title}</span>
                        <span class="text-slate-400 text-xs flex items-center mt-0.5">
                          <i data-lucide="map-pin" class="h-3 w-3 mr-1"></i> ${property.address}
                        </span>
                      </div>
                    </td>
                    <td class="py-4 px-6 font-bold text-slate-800 whitespace-nowrap">
                      ${property.reservePrice}
                    </td>
                    <td class="py-4 px-6 font-semibold text-slate-500 whitespace-nowrap">
                      ${property.governmentValuation || 'N/A'}
                    </td>
                    <td class="py-4 px-6 text-center">
                      ${hasDiscount ? `
                        <span class="inline-flex items-center space-x-1 bg-emerald-50 text-premium-emerald font-black text-xs px-2.5 py-1.5 rounded-lg border border-emerald-100 shadow-sm">
                          <i data-lucide="percent" class="h-3 w-3"></i>
                          <span>${discountPercent}% OFF</span>
                        </span>
                      ` : `
                        <span class="text-slate-400 text-xs font-semibold">Standard Valuation</span>
                      `}
                    </td>
                    <td class="py-4 px-6">
                      <span class="inline-flex items-center bg-slate-100 text-slate-600 text-xs font-bold px-2 py-1 rounded-md">
                        ${property.bank !== 'N/A' ? property.bank : 'Private Sale'}
                      </span>
                    </td>
                    <td class="py-4 px-6 text-right whitespace-nowrap">
                      <a href="#/property/${property.id}" class="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold text-xs px-4.5 py-2.5 rounded-xl transition-all shadow-sm inline-block">
                        Details &rarr;
                      </a>
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  }
}

// --- ADVISORY HUB RENDERING ---
function renderAdvisoryHub() {
  const app = document.getElementById('app-content');
  if (!app) return;

  app.className = "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg text-left";

  let headerHtml = `
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-slate-200 pb-6">
      <div>
        <div class="inline-flex items-center space-x-2 bg-emerald-50 text-premium-emerald px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider mb-3">
          <i data-lucide="scale" class="h-4 w-4"></i>
          <span>Professional Advisory & Legal Bureau</span>
        </div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
          Legal Guidance & <span class="text-premium-emerald">SARFAESI Draftsman</span>
        </h1>
        <p class="text-slate-500 text-lg mt-1">Get authentic legal guidance and compile compliant statutory notices immediately.</p>
      </div>

      <!-- Tab Controls -->
      <div class="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200 mt-6 md:mt-0 shadow-inner">
        <button id="adv-tab-guidance-btn" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all ${state.advisoryTab === 'guidance' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-500 hover:text-slate-800'}">
          Legal Advisory & Scheduling
        </button>
        <button id="adv-tab-draftsman-btn" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all ${state.advisoryTab === 'draftsman' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-500 hover:text-slate-800'}">
          Section 13(2) Notice Draftsman
        </button>
      </div>
    </div>
  `;

  if (state.advisoryTab === 'guidance') {
    if (state.selectedAdvocateProfile) {
      // Advocate full chamber details view
      const adv = state.selectedAdvocateProfile;
      app.innerHTML = `
        <div class="space-y-8">
          <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <button id="adv-back-btn" class="flex items-center gap-2 text-slate-700 hover:text-slate-900 font-extrabold text-sm transition-all bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
              <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Panel Directory
            </button>
            <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest bg-slate-50 border border-slate-100 px-3 py-1 rounded-md">
              Verified Chamber Profile
            </span>
          </div>

          <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden border border-slate-800 shadow-xl">
            <div class="absolute top-0 right-0 bg-premium-emerald text-white text-[9px] font-black uppercase px-6 py-2.5 rounded-bl-3xl tracking-widest flex items-center gap-1.5 shadow-md z-10">
              <i data-lucide="award" class="h-4 w-4 text-premium-gold"></i> Certified BAR Panelist
            </div>
            
            <i data-lucide="scale" class="absolute -bottom-12 -right-12 w-48 h-48 text-white/5 pointer-events-none"></i>
            
            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-8 relative z-10">
              <div class="p-1 bg-gradient-to-tr from-premium-emerald to-premium-gold rounded-3xl shadow-lg flex-shrink-0">
                <img src="${adv.image}" alt="${adv.name}" class="w-24 h-24 md:w-28 md:h-28 rounded-3xl object-cover border border-white" />
              </div>
              <div class="text-center md:text-left space-y-2.5">
                <div class="flex flex-wrap justify-center md:justify-start items-center gap-2">
                  <h2 class="text-2xl md:text-3xl font-black tracking-tight">${adv.name}</h2>
                  ${adv.id === 'sajid' ? `<span class="bg-premium-gold/25 border border-premium-gold/30 text-premium-gold text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">VIP FOUNDER</span>` : ''}
                </div>
                <p class="text-emerald-400 font-extrabold text-xs uppercase tracking-widest">${adv.role}</p>
                <div class="flex flex-wrap justify-center md:justify-start items-center gap-2.5 mt-3">
                  <span class="bg-slate-800 text-slate-200 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-700">Reg ID: ${adv.barReg}</span>
                  <span class="bg-slate-800 text-slate-200 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-700">${adv.experience}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center shadow-sm">
                  <div class="text-3xl font-black text-premium-emerald">${adv.trustScore.split(' ')[0]}</div>
                  <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-1">Court litigation success</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center shadow-sm">
                  <div class="text-3xl font-black text-amber-500">${adv.rating.split(' ')[0]} / 5.0</div>
                  <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-1">Client Consultation rating</div>
                </div>
              </div>

              <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                  <i data-lucide="map-pin" class="h-5 w-5 text-premium-emerald mr-2"></i> Physical Chambers & Contact details
                </h3>
                <div class="space-y-4">
                  <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-start gap-4">
                    <div class="p-3 bg-emerald-50 text-premium-emerald rounded-xl"><i data-lucide="map-pin" class="h-5 w-5"></i></div>
                    <div>
                      <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Office chamber Address</span>
                      <p class="text-slate-800 text-sm font-semibold mt-1">${adv.office}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                      <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i data-lucide="phone" class="h-5 w-5"></i></div>
                      <div>
                        <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Direct Hotline</span>
                        <p class="text-slate-800 text-sm font-black mt-0.5">${adv.phone}</p>
                      </div>
                    </div>
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                      <div class="p-3 bg-teal-50 text-teal-600 rounded-xl"><i data-lucide="mail" class="h-5 w-5"></i></div>
                      <div>
                        <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest">Official Email</span>
                        <p class="text-slate-800 text-sm font-semibold mt-0.5">${adv.email}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                  <i data-lucide="briefcase" class="h-5 w-5 text-premium-emerald mr-2"></i> practice bio & track record
                </h3>
                <p class="text-slate-600 text-sm leading-relaxed font-semibold">${adv.bio}</p>
                <p class="text-slate-500 text-xs leading-relaxed font-medium mt-2">
                  Practicing extensively in SARFAESI Debt Recovery Tribunal (DRT) appeals, municipal and co-operative housing society clearance codes, and secure heavy deposit lease covenants across Maharashtra.
                </p>
              </div>

              <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                  <i data-lucide="graduation-cap" class="h-5 w-5 text-premium-emerald mr-2"></i> Academic credentials & Qualifications
                </h3>
                <div class="space-y-3.5 text-xs font-semibold text-slate-600">
                  <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="h-4.5 w-4.5 text-premium-emerald flex-shrink-0"></i>
                    <span>Statutory Degree: ${adv.education}</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="h-4.5 w-4.5 text-premium-emerald flex-shrink-0"></i>
                    <span>Specialties: ${adv.specialties}</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="h-4.5 w-4.5 text-premium-emerald flex-shrink-0"></i>
                    <span>Bar Association: Active Certified Bar panelist in Maharashtra & Goa High Court</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Booking Side Card -->
            <div>
              <div id="booking-side-card" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl sticky top-28 space-y-6">
                ${renderAdvocateBookingBox(adv)}
              </div>
            </div>
          </div>
        </div>
      `;

      // Back navigation binding
      document.getElementById('adv-back-btn').addEventListener('click', () => {
        state.selectedAdvocateProfile = null;
        renderAdvisoryHub();
      });

      // Bind Booking Form
      bindBookingFormEvents(adv);

    } else {
      // Advocates panel list view
      app.innerHTML = `
        ${headerHtml}

        <div class="space-y-8">
          <!-- Statistics block -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-900 text-white rounded-3xl p-6 border border-slate-800 shadow-lg text-center">
            <div class="md:border-r border-slate-800">
              <div class="text-2xl font-black text-premium-emerald">99.4%</div>
              <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Court Success Rate</div>
            </div>
            <div class="md:border-r border-slate-800">
              <div class="text-2xl font-black text-premium-gold">15+ Mins</div>
              <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Avg Response Time</div>
            </div>
            <div class="md:border-r border-slate-800">
              <div class="text-2xl font-black text-emerald-400">MAH Bar</div>
              <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Certified Advocates</div>
            </div>
            <div>
              <div class="text-2xl font-black text-teal-400">Secured Escrow</div>
              <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-1">Client Fee Protection</div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel directory & FAQs -->
            <div class="lg:col-span-2 space-y-6">
              
              <!-- Advocates List -->
              <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                  <div>
                    <h3 class="text-xl font-extrabold text-slate-900 flex items-center">
                      <i data-lucide="scale" class="h-5.5 w-5.5 text-premium-emerald mr-2"></i>
                      Panel of Certified Legal Advisors
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Connect directly with Maharashtra's leading statutory auction advocates.</p>
                  </div>
                  <span class="bg-slate-100 text-slate-600 text-[10px] font-black uppercase px-3 py-1 rounded-md border border-slate-200 self-start sm:self-center">
                    Live Panelist Direct Booking
                  </span>
                </div>

                <div class="space-y-6">
                  ${advocatesList.map(advocate => {
                    const isSajid = advocate.id === 'sajid';
                    if (isSajid) {
                      return `
                        <div class="advocate-list-item cursor-pointer relative overflow-hidden p-6 rounded-3xl transition-all border-2 flex flex-col md:flex-row md:items-center gap-6 justify-between ${state.selectedAdvocateForBooking.id === advocate.id ? 'bg-gradient-to-br from-emerald-500/10 via-white to-slate-50 border-premium-emerald shadow-md' : 'bg-gradient-to-br from-emerald-50/40 via-white to-slate-50 border-slate-200 hover:border-premium-emerald/60 shadow-sm'}" data-adv-id="${advocate.id}">
                          <div class="absolute top-0 right-0 bg-premium-emerald text-white text-[8px] font-black uppercase px-4 py-1.5 rounded-bl-2xl tracking-widest shadow-sm flex items-center gap-1 z-10">
                            <i data-lucide="award" class="h-3 w-3 text-premium-gold"></i> FOUNDER & CHIEF COUNSEL
                          </div>
                          
                          <i data-lucide="scale" class="absolute -bottom-8 -right-8 w-32 h-32 text-emerald-500/5 pointer-events-none"></i>

                          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 z-10">
                            <div class="relative flex-shrink-0">
                              <div class="p-0.5 bg-gradient-to-tr from-premium-emerald to-premium-gold rounded-2xl shadow-md">
                                <img src="${advocate.image}" alt="${advocate.name}" class="w-20 h-20 rounded-2xl object-cover border border-white" />
                              </div>
                              <span class="absolute -bottom-1 -right-1 bg-premium-gold text-slate-950 text-[7px] font-black uppercase px-1.5 py-0.5 rounded-md border border-white shadow">VIP Rank #1</span>
                            </div>
                            <div class="space-y-1.5">
                              <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-black text-slate-900 text-lg">${advocate.name}</h4>
                                <span class="bg-emerald-50 border border-emerald-100 text-premium-emerald text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Chief Counsel</span>
                              </div>
                              <div class="flex flex-wrap items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <span>LL.M (GLC Mumbai)</span>
                                <span>•</span>
                                <span class="text-premium-emerald">18+ Yrs Court Seniority</span>
                                <span>•</span>
                                <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded">CBS Chambers Nashik</span>
                              </div>
                              <p class="text-xs text-slate-600 font-semibold leading-relaxed max-w-md">${advocate.bio}</p>
                            </div>
                          </div>
                          <button class="advocate-select-btn md:self-center font-black text-xs px-6 py-3.5 rounded-2xl transition-all shadow-md whitespace-nowrap z-10 ${state.selectedAdvocateForBooking.id === advocate.id ? 'bg-premium-emerald text-white hover:bg-premium-emeraldHover' : 'bg-slate-900 text-white hover:bg-premium-emerald'}" data-adv-id="${advocate.id}">
                            Select & View Profile
                          </button>
                        </div>
                      `;
                    }
                    return `
                      <div class="advocate-list-item cursor-pointer pt-6 flex flex-col md:flex-row md:items-center gap-5 justify-between group transition-all p-3 rounded-2xl border ${state.selectedAdvocateForBooking.id === advocate.id ? 'bg-emerald-50/30 border-emerald-100/80 shadow-xs' : 'border-transparent hover:border-slate-100'}" data-adv-id="${advocate.id}">
                        <div class="flex items-start gap-4">
                          <div class="relative">
                            <img src="${advocate.image}" alt="${advocate.name}" class="w-16 h-16 rounded-2xl object-cover shadow ${state.selectedAdvocateForBooking.id === advocate.id ? 'border-2 border-premium-emerald' : 'border border-slate-200'}" />
                            <div class="absolute -bottom-1.5 -right-1 bg-amber-500 text-slate-950 text-[7px] font-black uppercase px-1.5 py-0.5 rounded shadow-sm border border-white">Partner</div>
                          </div>
                          <div>
                            <div class="flex flex-wrap items-center gap-2">
                              <h4 class="font-extrabold text-slate-900 text-base group-hover:text-premium-emerald transition-colors">${advocate.name}</h4>
                              <span class="bg-slate-50 text-slate-600 text-[9px] font-black px-2 py-0.5 rounded border border-slate-200 uppercase tracking-wider">Verified Panelist</span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">${advocate.education} | ${advocate.experience}</div>
                            <p class="text-xs text-slate-600 mt-2 font-medium leading-relaxed max-w-lg">${advocate.bio}</p>
                          </div>
                        </div>
                        <button class="advocate-select-btn md:self-center font-extrabold text-xs px-5 py-3 rounded-xl transition-all shadow-sm whitespace-nowrap ${state.selectedAdvocateForBooking.id === advocate.id ? 'bg-premium-emerald text-white' : 'bg-slate-100 hover:bg-premium-emerald text-slate-700 hover:text-white border border-slate-200'}" data-adv-id="${advocate.id}">
                          Select & View Profile
                        </button>
                      </div>
                    `;
                  }).join('')}
                </div>
              </div>

              <!-- SARFAESI Intro Guide -->
              <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm text-slate-600">
                <h2 class="text-2xl font-extrabold text-slate-900 mb-4 flex items-center">
                  <i data-lucide="shield-check" class="h-6 w-6 text-premium-emerald mr-2"></i>
                  SARFAESI Act Property Buyers Guide
                </h2>
                <p class="text-base">
                  Purchasing real estate through bank auctions requires deep compliance knowledge under the **SARFAESI Act, 2002** (Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest Act).
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                  <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="font-bold text-slate-800 block text-sm mb-1">Section 13(2) Demand Notice</span>
                    <p class="text-xs text-slate-500">Issued by banks giving default borrowers a mandatory 60 days duration to discharge full outstanding liability before asset attachment.</p>
                  </div>
                  <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="font-bold text-slate-800 block text-sm mb-1">Section 13(4) Possession Notice</span>
                    <p class="text-xs text-slate-500">Issued once the 60-day period expires, authorizing the secure attachment and auctioning of symbolic or physical possession of the mortgaged asset.</p>
                  </div>
                </div>
              </div>

              <!-- FAQs -->
              <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                  <i data-lucide="help-circle" class="h-5 w-5 text-premium-emerald mr-2"></i> Frequently Asked Questions
                </h3>
                <div class="space-y-4 divide-y divide-slate-100">
                  <div class="pt-3">
                    <h4 class="font-bold text-slate-800 text-sm">Q: What happens if the physical possession is delayed by the bank?</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                      Under the SARFAESI guidelines, if a bank is only offering "Symbolic Possession", it is up to the purchaser to obtain full physical possession via filing an application with the District Magistrate (DM) or Chief Metropolitan Magistrate (CMM) under Section 14 of the Act.
                    </p>
                  </div>
                  <div class="pt-4">
                    <h4 class="font-bold text-slate-800 text-sm">Q: Is the EMD (Earnest Money Deposit) refundable if I do not win?</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                      Yes. The EMD is 100% refundable by the bank within 2 to 5 business days after the auction is closed if your bid was not successful or you did not win the auction. No deduction is made.
                    </p>
                  </div>
                  <div class="pt-4">
                    <h4 class="font-bold text-slate-800 text-sm">Q: Are there any hidden liabilities like municipal taxes or society charges?</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                      Bank auctions are generally sold on an "As-Is-Where-Is" basis. It is extremely critical to perform a Title Search Report and check with local societies or municipal offices for outstanding dues, which the buyer may otherwise be legally required to clear.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Sidebar Scheduling Card -->
            <div>
              <div id="booking-side-card" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl sticky top-28 space-y-6">
                ${renderAdvocateBookingBox(state.selectedAdvocateForBooking)}
              </div>
            </div>
          </div>
        </div>
      `;

      // Bind Advocate click selectors
      document.querySelectorAll('.advocate-list-item').forEach(card => {
        card.addEventListener('click', (e) => {
          const advId = card.getAttribute('data-adv-id');
          const advocate = advocatesList.find(a => a.id === advId);
          state.selectedAdvocateForBooking = advocate;
          state.selectedAdvocateProfile = advocate; // open profile
          renderAdvisoryHub();
        });
      });

      // Bind click triggers for select buttons
      document.querySelectorAll('.advocate-select-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const advId = btn.getAttribute('data-adv-id');
          const advocate = advocatesList.find(a => a.id === advId);
          state.selectedAdvocateForBooking = advocate;
          state.selectedAdvocateProfile = advocate;
          renderAdvisoryHub();
        });
      });

      // Bind Booking Form
      bindBookingFormEvents(state.selectedAdvocateForBooking);
    }
  } else if (state.advisoryTab === 'draftsman') {
    // TAB 2: SARFAESI DRAFTSMAN WIZARD
    app.innerHTML = `
      ${headerHtml}
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Draft Inputs -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm space-y-6 text-left">
          <div>
            <h3 class="font-extrabold text-slate-900 text-xl flex items-center">
              <i data-lucide="file-text" class="h-5.5 w-5.5 text-premium-emerald mr-2"></i> Notice Configuration
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Input outstanding dues and property descriptions to generate compliant templates.</p>
          </div>

          <form id="draftsman-form" class="space-y-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Financial Institution Name</label>
              <input type="text" id="draft-bank-name" value="${state.draftsmanState.bankName}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Bank Branch Address</label>
              <input type="text" id="draft-bank-branch" value="${state.draftsmanState.bankBranch}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Borrower / Guarantor Name</label>
              <input type="text" id="draft-borrower-name" value="${state.draftsmanState.borrowerName}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Notice Date</label>
                <input type="date" id="draft-notice-date" value="${state.draftsmanState.noticeDate}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Outstanding Debt Dues</label>
                <input type="text" id="draft-outstanding" value="${state.draftsmanState.outstandingDues}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Mortgaged Property Description</label>
              <textarea id="draft-property" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800">${state.draftsmanState.propertyDetails}</textarea>
            </div>

            <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md text-center text-sm">
              Compile Draft Document &rarr;
            </button>
          </form>
        </div>

        <!-- Live Draft Canvas -->
        <div class="lg:col-span-7 bg-slate-900 rounded-3xl p-6 md:p-8 border border-slate-800 text-white relative flex flex-col min-h-[500px]">
          <div class="flex justify-between items-center border-b border-slate-800 pb-4 mb-6">
            <span class="flex items-center text-slate-400 text-sm font-semibold">
              <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse mr-2"></span>
              Bilingual Notice Canvas
            </span>
            
            <div class="flex items-center space-x-3">
              <div class="flex bg-slate-800 rounded-lg p-0.5 border border-slate-700">
                <button id="draft-lang-en-btn" class="px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${state.draftsmanState.language === 'EN' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}">
                  <i data-lucide="languages" class="h-3 w-3"></i>
                  <span>English</span>
                </button>
                <button id="draft-lang-mr-btn" class="px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${state.draftsmanState.language === 'MR' ? 'bg-premium-emerald text-white shadow' : 'text-slate-400 hover:text-white'}">
                  <i data-lucide="languages" class="h-3 w-3"></i>
                  <span>मराठी</span>
                </button>
              </div>

              <button id="draft-print-btn" class="p-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-300 hover:text-white border border-slate-700 transition-colors" title="Print Notice">
                <i data-lucide="printer" class="h-4 w-4"></i>
              </button>
            </div>
          </div>

          <!-- Document Canvas Sheet -->
          <div class="bg-white text-slate-900 rounded-2xl p-6 md:p-8 flex-grow font-serif shadow-inner border border-slate-100 overflow-y-auto max-h-[500px] text-left">
            ${renderCompiledNoticeTemplate()}
          </div>

          <div class="mt-4 text-[10px] text-slate-400 text-center">
            * The generated document is draft legal template for general guidance purposes. Consult advocate for registry.
          </div>
        </div>

      </div>
    `;

    // Bind Draftsman events
    document.getElementById('draftsman-form').addEventListener('submit', (e) => {
      e.preventDefault();
      state.draftsmanState.bankName = document.getElementById('draft-bank-name').value.toUpperCase();
      state.draftsmanState.bankBranch = document.getElementById('draft-bank-branch').value.toUpperCase();
      state.draftsmanState.borrowerName = document.getElementById('draft-borrower-name').value.toUpperCase();
      state.draftsmanState.noticeDate = document.getElementById('draft-notice-date').value;
      state.draftsmanState.outstandingDues = document.getElementById('draft-outstanding').value;
      state.draftsmanState.propertyDetails = document.getElementById('draft-property').value.toUpperCase();
      
      renderAdvisoryHub();
    });

    document.getElementById('draft-lang-en-btn').addEventListener('click', () => {
      state.draftsmanState.language = 'EN';
      renderAdvisoryHub();
    });
    document.getElementById('draft-lang-mr-btn').addEventListener('click', () => {
      state.draftsmanState.language = 'MR';
      renderAdvisoryHub();
    });
    document.getElementById('draft-print-btn').addEventListener('click', () => {
      window.print();
    });
  }

  // Bind Page Tab switchers
  document.getElementById('adv-tab-guidance-btn').addEventListener('click', () => {
    state.advisoryTab = 'guidance';
    state.selectedAdvocateProfile = null;
    renderAdvisoryHub();
  });
  document.getElementById('adv-tab-draftsman-btn').addEventListener('click', () => {
    state.advisoryTab = 'draftsman';
    renderAdvisoryHub();
  });
}

function renderAdvocateBookingBox(advocate) {
  return `
    <div class="bg-slate-950 text-white rounded-2xl p-6 border border-slate-800 space-y-5 shadow-inner relative overflow-hidden text-left">
      <div class="absolute top-0 right-0 bg-premium-emerald text-white text-[9px] font-black uppercase px-4 py-1.5 rounded-bl-xl tracking-wider shadow">
        Verified Bar Council Registry
      </div>

      <div class="flex items-center gap-4">
        <img src="${advocate.image}" alt="${advocate.name}" class="w-14 h-14 rounded-xl object-cover border border-slate-700 shadow" />
        <div>
          <h4 class="font-black text-white text-lg">${advocate.name}</h4>
          <span class="text-xs text-premium-emerald font-black uppercase tracking-wider block mt-0.5">Reg ID: ${advocate.barReg}</span>
        </div>
      </div>

      <div class="space-y-3.5 border-t border-slate-800/80 pt-4 text-xs font-semibold leading-normal text-slate-300">
        <div class="flex items-start gap-2.5">
          <i data-lucide="map-pin" class="h-4.5 w-4.5 text-slate-400 flex-shrink-0 mt-0.5"></i>
          <div>
            <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Office Chamber</span>
            <span class="text-slate-200 text-sm font-semibold">${advocate.office}</span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
          <div class="flex items-center gap-2">
            <i data-lucide="phone" class="h-4 w-4 text-slate-400"></i>
            <div>
              <span class="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Hotline</span>
              <span class="text-slate-200 text-xs font-bold">${advocate.phone}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
            <div>
              <span class="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Email</span>
              <span class="truncate block max-w-[120px] text-slate-200 text-[10px] font-semibold">${advocate.email}</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-slate-800/50 pt-3">
          <div class="flex items-center gap-2">
            <i data-lucide="star" class="h-4 w-4 text-amber-500 fill-current"></i>
            <div>
              <span class="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Rating</span>
              <span class="text-amber-400 font-black text-sm">${advocate.rating.split(' ')[0]} / 5.0</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <i data-lucide="award" class="h-4 w-4 text-premium-emerald"></i>
            <div>
              <span class="text-[9px] text-slate-400 font-extrabold block uppercase tracking-widest mb-0.5">Success Rate</span>
              <span class="text-emerald-400 font-black text-sm">${advocate.trustScore.split(' ')[0]}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="space-y-4 text-left">
      <div class="text-center pb-2">
        <h3 class="font-extrabold text-slate-900 text-base">Schedule Appointment</h3>
        <p class="text-slate-400 text-[10px] mt-0.5">Secure direct counsel on the verified panel.</p>
      </div>

      ${state.bookingState.success ? `
        <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100 text-center space-y-3">
          <div class="w-10 h-10 bg-premium-emerald/10 text-premium-emerald rounded-full flex items-center justify-center mx-auto">
            <i data-lucide="user-check" class="h-5 w-5"></i>
          </div>
          <div>
            <h4 class="font-bold text-slate-900 text-sm">Consultation Reserved!</h4>
            <p class="text-[10px] text-slate-400 mt-0.5">Ref ID: MA-ADV-8910</p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60 text-left text-xs">
            <div class="font-bold text-slate-800">Your Appointed Counsel:</div>
            <div class="text-premium-emerald font-extrabold mt-0.5">${advocate.name}</div>
            <div class="text-slate-400 mt-1.5 leading-normal">
              Your appointment has been registered for **${state.bookingState.date}**. A confirmation checklist has been sent to your mobile hotline.
            </div>
          </div>
        </div>
      ` : `
        <form id="advocate-booking-form" class="space-y-4 text-xs font-semibold text-slate-500">
          <div>
            <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Your Name</label>
            <input type="text" id="book-name-input" required value="${state.bookingState.name}" placeholder="e.g. Anand Patil" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <div>
            <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Email Address</label>
            <input type="email" id="book-email-input" required value="${state.bookingState.email}" placeholder="name@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Preferred Date</label>
              <input type="date" id="book-date-input" required value="${state.bookingState.date}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-800 text-slate-600" />
            </div>
            <div>
              <label class="block text-slate-600 font-bold mb-1 uppercase tracking-wider text-[9px]">Advice Topic</label>
              <select id="book-topic-select" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-premium-emerald text-sm font-semibold text-slate-700 bg-white">
                <option value="SARFAESI Title Verification" ${state.bookingState.topic === 'SARFAESI Title Verification' ? 'selected' : ''}>SARFAESI Title Verification</option>
                <option value="Delayed Physical Possession Dues" ${state.bookingState.topic === 'Delayed Physical Possession Dues' ? 'selected' : ''}>Delayed Physical Possession Dues</option>
                <option value="EMD Refund Dispute Settlement" ${state.bookingState.topic === 'EMD Refund Dispute Settlement' ? 'selected' : ''}>EMD Refund Dispute Settlement</option>
                <option value="Heavy Deposit Lease Verification" ${state.bookingState.topic === 'Heavy Deposit Lease Verification' ? 'selected' : ''}>Heavy Deposit Lease Verification</option>
              </select>
            </div>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center text-sm">
            Book Confirmed Consultation <i data-lucide="arrow-right" class="h-4 w-4 ml-1.5"></i>
          </button>
        </form>
      `}
      
      <div class="text-[10px] text-slate-400 text-center font-medium border-t border-slate-100 pt-3">
        🛡️ **MahaAuctions Client Escrow Policy**: Consultation fees are held securely in client dispute trust accounts until legal session is successfully rendered.
      </div>
    </div>
  `;
}

function bindBookingFormEvents(adv) {
  const form = document.getElementById('advocate-booking-form');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      state.bookingState.name = document.getElementById('book-name-input').value;
      state.bookingState.email = document.getElementById('book-email-input').value;
      state.bookingState.date = document.getElementById('book-date-input').value;
      state.bookingState.topic = document.getElementById('book-topic-select').value;
      state.bookingState.success = true;
      renderAdvisoryHub();
    });
  }
}

function renderCompiledNoticeTemplate() {
  const ds = state.draftsmanState;
  if (ds.language === 'EN') {
    return `
      <div class="space-y-4 text-xs leading-relaxed font-serif text-slate-900">
        <div class="text-center font-bold underline text-sm tracking-wide">
          DEMAND NOTICE UNDER SECTION 13(2) OF SARFAESI ACT, 2002
        </div>
        
        <div class="flex justify-between font-bold text-[10px]">
          <span>REF: MA-DRAFT-13(2)/2026</span>
          <span>DATE: ${ds.noticeDate}</span>
        </div>

        <div>
          <span class="font-bold block">TO,</span>
          <span class="font-bold">${ds.borrowerName}</span>
          <span class="text-[10px] text-slate-500 block">Maharashtra, India.</span>
        </div>

        <p>
          WHEREAS, the undersigned being the Authorized Officer of **${ds.bankName}**, under the Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest (SARFAESI) Act, 2002 and in exercise of powers conferred under Section 13(12) read with Rule 3 of the Security Interest (Enforcement) Rules, 2002 issued demand notices to you.
        </p>

        <p>
          You have failed to clear your liabilities and outstanding dues towards the secured credit facility. The outstanding sum amounts to **${ds.outstandingDues}** as of the date of notice.
        </p>

        <p>
          Notice is hereby given to you to discharge in full the liabilities within **60 days** from the date of this notice, failing which the bank will exercise all rights under Section 13(4) of the SARFAESI Act, including taking physical/symbolic possession of the secured asset scheduled below:
        </p>

        <div class="p-3 bg-slate-50 rounded border border-slate-200 font-sans text-[10px]">
          <span class="font-bold block uppercase text-slate-700 font-sans">Schedule of Secure Attached Asset:</span>
          <p class="mt-1 text-slate-600 font-medium">${ds.propertyDetails}</p>
        </div>

        <div class="pt-6 flex justify-between font-sans text-[10px]">
          <div>
            <span class="block font-bold">Authorized Officer</span>
            <span class="text-slate-500">${ds.bankName}</span>
          </div>
          <div class="text-right">
            <span class="block italic text-slate-400">Digital Signature Attached</span>
            <span class="font-bold">${ds.bankBranch}</span>
          </div>
        </div>
      </div>
    `;
  } else {
    return `
      <div class="space-y-4 text-xs leading-relaxed font-sans text-slate-900">
        <div class="text-center font-bold underline text-sm tracking-wide">
          सरफेसी कायदा, २००२ च्या कलम १३(२) अंतर्गत मागणी नोटीस
        </div>
        
        <div class="flex justify-between font-bold text-[10px]">
          <span>संदर्भ: एमए-ड्राफ्ट-१३(२)/२०२६</span>
          <span>दिनांक: ${ds.noticeDate}</span>
        </div>

        <div>
          <span class="font-bold block">प्रति,</span>
          <span class="font-bold">${ds.borrowerName}</span>
          <span class="text-[10px] text-slate-500 block">महाराष्ट्र, भारत.</span>
        </div>

        <p>
          ज्याअर्थी, **${ds.bankName}** चे प्राधिकृत अधिकारी म्हणून स्वाक्षरी करणाऱ्यांनी, वित्तीय मालमत्तांचे सिक्युरिटायझेशन आणि पुनर्रचना आणि सुरक्षा हितसंबंधांची अंमलबजावणी (सरफेसी) कायदा, २००२ च्या तरतुदींनुसार व कलम १३(१२) वाचता सुरक्षा हितसंबंध (अंमलबजावणी) नियम, २००२ च्या नियम ३ अन्वये प्रदान केलेल्या अधिकारांचा वापर करून मागणी नोटीस बजावली आहे.
        </p>

        <p>
          आपण बँकेच्या कर्जाची व थकबाकीची परतफेड करण्यात अयशस्वी ठरला आहात. सदर नोटीसच्या दिनांकापर्यंत एकूण थकबाकी **${ds.outstandingDues}** इतकी आहे.
        </p>

        <p>
          याद्वारे आपल्याला नोटीस देण्यात येते की, सदर नोटीसच्या दिनांकापासून **६० दिवसांच्या आत** संपूर्ण थकीत रकमेची परतफेड करावी, अन्यथा बँक सरफेसी कायद्याच्या कलम १३(४) अंतर्गत खालील अनुसूचित तारण मालमत्तेचा प्रत्यक्ष किंवा प्रतिकात्मक ताबा घेण्यासह कायदेशीर कारवाई करेल:
        </p>

        <div class="p-3 bg-slate-50 rounded border border-slate-200 text-[10px]">
          <span class="font-bold block uppercase text-slate-700">सुरक्षित मालमत्तेची अनुसूची:</span>
          <p class="mt-1 text-slate-600 font-medium">${ds.propertyDetails}</p>
        </div>

        <div class="pt-6 flex justify-between text-[10px]">
          <div>
            <span class="block font-bold">प्राधिकृत अधिकारी</span>
            <span class="text-slate-500">${ds.bankName}</span>
          </div>
          <div class="text-right">
            <span class="block text-slate-400">डिजिटल स्वाक्षरी</span>
            <span class="font-bold">${ds.bankBranch}</span>
          </div>
        </div>
      </div>
    `;
  }
}

// --- AGENTS DIRECTORY RENDERING ---
function renderAgentsDirectory() {
  const app = document.getElementById('app-content');
  if (!app) return;

  app.className = "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 bg-premium-bg text-left";
  app.innerHTML = `
    <!-- Title Header -->
    <div class="mb-10 text-left">
      <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
        Maharashtra Certified <span class="text-premium-emerald">Auction Agents</span>
      </h1>
      <p class="text-slate-500 text-lg mt-2">
        Connect with vetted local domain experts who manage statutory documentation, DM registry orders, and physical possession clearances.
      </p>
    </div>

    <!-- Grid of Agents -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      ${agents.map(agent => `
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
          <div class="absolute top-0 right-0 bg-emerald-50 text-premium-emerald text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-bl-2xl border-l border-b border-emerald-100">
            Verified Partner
          </div>

          <div class="space-y-5">
            <div class="flex items-center space-x-4">
              <img src="${agent.image}" alt="${agent.name}" class="h-16 w-16 rounded-2xl object-cover border border-slate-200 shadow" />
              <div>
                <h3 class="font-extrabold text-slate-900 text-lg group-hover:text-premium-emerald transition-colors">${agent.name}</h3>
                <div class="flex items-center text-amber-500 space-x-1 mt-0.5">
                  <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                  <span class="text-xs font-black text-slate-700">${agent.rating} / 5.0</span>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center space-x-2">
              <i data-lucide="award" class="h-4 w-4 text-premium-emerald flex-shrink-0"></i>
              <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">${agent.specialty}</span>
            </div>

            <div class="space-y-2.5 text-xs text-slate-500 font-semibold">
              <div class="flex items-center space-x-2">
                <i data-lucide="phone" class="h-3.5 w-3.5 text-slate-400"></i>
                <span>${agent.phone}</span>
              </div>
              <div class="flex items-center space-x-2">
                <i data-lucide="mail" class="h-3.5 w-3.5 text-slate-400"></i>
                <span>${agent.email}</span>
              </div>
            </div>
          </div>

          <button class="agent-connect-btn mt-6 w-full bg-slate-900 hover:bg-premium-emerald text-white font-bold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center space-x-2 text-sm" data-agt-id="${agent.id}">
            <i data-lucide="message-square-code" class="h-4.5 w-4.5"></i>
            <span>Connect with Agent</span>
          </button>
        </div>
      `).join('')}
    </div>

    <!-- Trust Badge Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 md:p-8 mt-12 text-white border border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl">
      <div class="space-y-1">
        <div class="text-xl font-bold flex items-center">
          <i data-lucide="sparkles" class="h-5 w-5 text-premium-emerald mr-2"></i>
          Are you a certified Maharashtra Real Estate Agent?
        </div>
        <p class="text-slate-400 text-xs font-medium">Join MahaAuctions panel to access exclusive bank distress listings, borrower settlements, and heavy deposit deals.</p>
      </div>
      <button class="bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold px-6 py-3.5 rounded-xl transition-all shadow-md text-sm whitespace-nowrap">
        Register as Agent Panelist
      </button>
    </div>
  `;

  // Bind click listeners for connection triggers
  document.querySelectorAll('.agent-connect-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const agtId = e.currentTarget.getAttribute('data-agt-id');
      const agent = agents.find(a => a.id === agtId);
      showAgentModal(agent);
    });
  });
}

// --- CITY AUCTIONS RENDERING ---
function renderCityAuctions(cityId) {
  const app = document.getElementById('app-content');
  if (!app) return;

  const city = cities.find(c => c.id === cityId);
  const cityProperties = properties.filter(p => p.cityId === cityId);

  if (!city) {
    app.innerHTML = `<div class="text-slate-800 text-center py-20 text-2xl font-bold">City not found</div>`;
    return;
  }

  app.className = "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-left";
  app.innerHTML = `
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 border-b border-slate-200 pb-6">
      <div>
        <a href="#/" class="text-premium-emerald font-medium text-sm hover:underline mb-2 inline-block">&larr; Back to Map</a>
        <h1 class="text-4xl font-extrabold text-slate-900 flex items-center">
          <i data-lucide="map-pin" class="h-8 w-8 mr-3 text-premium-emerald"></i>
          Live Auctions in ${city.name}
        </h1>
        <p class="text-slate-500 mt-2 text-lg font-medium">Found ${cityProperties.length} premium properties</p>
      </div>
      
      <button class="mt-4 md:mt-0 flex items-center bg-white hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-lg border border-slate-300 shadow-sm transition-colors">
        <i data-lucide="filter" class="h-4 w-4 mr-2"></i> Filter Results
      </button>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      ${cityProperties.map(property => `
        <div class="bg-white rounded-2xl overflow-hidden shadow-md border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
          <div class="relative h-60 overflow-hidden">
            <img src="${property.image}" alt="${property.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur text-premium-emerald text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
              ${property.type}
            </div>
            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center">
              <span class="bg-slate-900/80 text-white font-medium text-xs px-3 py-1.5 rounded backdrop-blur border border-slate-700 shadow-sm">
                ${property.bank}
              </span>
            </div>
          </div>
          
          <div class="p-6 flex-grow flex flex-col">
            <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2">${property.title}</h3>
            <p class="text-slate-500 text-sm mb-5 line-clamp-1 flex items-center">
              <i data-lucide="map-pin" class="h-3 w-3 mr-1 inline"></i> ${property.address}
            </p>
            
            <div class="space-y-3 mb-6 flex-grow">
              <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100 flex justify-between items-center">
                <span class="text-slate-600 text-xs font-bold uppercase tracking-wide">Reserve Price</span>
                <span class="text-premium-emerald text-lg font-extrabold flex items-center">
                  <i data-lucide="indian-rupee" class="h-4 w-4 mr-1"></i>${property.reservePrice}
                </span>
              </div>
              <div class="flex justify-between items-center px-1 font-semibold text-xs">
                <span class="text-slate-500">EMD Amount</span>
                <span class="text-slate-800 font-bold">${property.emd}</span>
              </div>
              <div class="flex justify-between items-center px-1 font-semibold text-xs">
                <span class="text-slate-500">Auction Date</span>
                <span class="text-slate-800 font-bold flex items-center">
                  <i data-lucide="calendar" class="h-3.5 w-3.5 mr-1.5 text-premium-emerald"></i>
                  ${new Date(property.auctionDate).toLocaleDateString()}
                </span>
              </div>
            </div>
            
            <a href="#/property/${property.id}" class="w-full block text-center bg-white hover:bg-premium-emerald text-premium-emerald hover:text-white font-bold py-3.5 rounded-xl transition-colors border-2 border-premium-emerald flex items-center justify-center group-hover:shadow-md">
              View Details <i data-lucide="arrow-right" class="h-5 w-5 ml-2"></i>
            </a>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

// --- PROPERTY DETAIL PAGE RENDERING ---
function renderPropertyDetail(propertyId) {
  const app = document.getElementById('app-content');
  if (!app) return;

  const property = properties.find(p => p.id === propertyId);

  if (!property) {
    app.innerHTML = `<div class="text-slate-800 text-center py-20 text-2xl font-bold">Property not found</div>`;
    return;
  }

  const city = cities.find(c => c.id === property.cityId);
  const agent = agents.find(a => a.id === property.agentId) || agents[0];

  // Calculations
  const hasDiscount = property.numericGovValuation && property.numericGovValuation > property.numericPrice;
  const discountPercent = hasDiscount ? Math.round(((property.numericGovValuation - property.numericPrice) / property.numericGovValuation) * 100) : 0;
  const savingsAmount = hasDiscount ? (property.numericGovValuation - property.numericPrice) : 0;

  app.className = "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-premium-bg text-left";
  app.innerHTML = `
    <a href="#/city/${city.id}" class="text-premium-emerald font-bold text-sm hover:underline mb-6 inline-block">
      &larr; Back to ${city.name} Auctions
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-8">
        
        <!-- Hero Image -->
        <div class="rounded-3xl overflow-hidden shadow-xl relative h-96 md:h-[500px] border border-slate-200">
          <img src="${property.image}" alt="${property.title}" class="w-full h-full object-cover" />
          <div class="absolute top-4 left-4 bg-slate-900/80 text-white font-black text-xs px-3 py-1.5 rounded-lg backdrop-blur border border-slate-700 shadow-sm">
            ${property.listingId}
          </div>
          <div class="absolute top-4 right-4 bg-white/95 backdrop-blur text-slate-800 px-4 py-2 rounded-xl border border-slate-200 flex items-center shadow-md font-bold text-sm">
            <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse mr-2"></span>
            ${property.category === 'Auction' ? '🏦 STATUTORY AUCTION' : property.category.toUpperCase()}
          </div>
        </div>

        <!-- Description Card -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 space-y-6">
          <div>
            <div class="text-premium-emerald text-xs font-black tracking-wider uppercase mb-2 flex items-center">
              <i data-lucide="sparkles" class="h-4 w-4 mr-1"></i>
              ${property.type} PROPERTY
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3 leading-tight">
              ${property.title}
            </h1>
            <p class="text-slate-500 flex items-center text-base font-medium">
              <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-slate-400"></i> ${property.address}
            </p>
          </div>

          ${property.category === 'Heavy Deposit' ? `
            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 text-amber-900 text-xs font-semibold leading-relaxed">
              💡 <span class="font-extrabold uppercase">Heavy Deposit Special</span>: Under this contract style, you pay a one-time refundable security deposit of ${property.deposit} at registration, and enjoy a ZERO monthly rental fee for the entire 2-year tenure. Excellent savings for visitors!
            </div>
          ` : ''}

          <!-- READY RECKONER COMPARATOR -->
          <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 space-y-4">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center">
                  <i data-lucide="shield-check" class="h-4.5 w-4.5 text-premium-emerald mr-1.5"></i>
                  Government Ready Reckoner Comparator
                </h3>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Statutory valuation comparison audit</span>
              </div>
              ${hasDiscount ? `
                <span class="bg-premium-emerald text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase shadow-sm">
                  ${discountPercent}% Below Ready Reckoner
                </span>
              ` : ''}
            </div>

            <div class="space-y-2 pt-2">
              <div class="flex justify-between text-xs font-bold text-slate-600">
                <span>MahaAuctions Reserve Price: ${property.reservePrice}</span>
                <span>Govt Ready Reckoner Valuation: ${property.governmentValuation || 'N/A'}</span>
              </div>
              <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden relative border border-slate-300">
                <div class="h-full bg-gradient-to-r from-premium-emerald to-teal-500 rounded-full" style="width: ${Math.min(100, (property.numericPrice / (property.numericGovValuation || 1)) * 100)}%"></div>
              </div>
              ${hasDiscount ? `
                <div class="text-[11px] text-slate-500 font-semibold italic text-right">
                  💡 Instantly save ${formatRupee(savingsAmount)} below certified government valuation.
                </div>
              ` : ''}
            </div>
          </div>

          <!-- Description -->
          <div class="prose prose-slate max-w-none border-t border-slate-100 pt-6">
            <h4 class="text-lg font-bold text-slate-900 mb-3">Detailed Asset Overview</h4>
            <p class="text-slate-600 leading-relaxed text-sm">${property.details}</p>
          </div>
        </div>

        <!-- NEWSPAPER NOTICE -->
        ${property.noticeEnglish ? `
          <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 space-y-6">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
              <div>
                <h3 class="font-extrabold text-slate-900 text-lg flex items-center">
                  <i data-lucide="file-text" class="h-5 w-5 text-premium-emerald mr-2"></i>
                  Newspaper Publication Notice
                </h3>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Official bilingual newspaper clippings</span>
              </div>

              <!-- Notice Language Switches -->
              <div class="flex bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                <button id="notice-lang-en-btn" class="px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${state.propertySchedule.noticeLang === 'EN' ? 'bg-premium-emerald text-white shadow' : 'text-slate-500 hover:text-slate-800'}">
                  <i data-lucide="languages" class="h-3 w-3"></i>
                  <span>English Notice</span>
                </button>
                <button id="notice-lang-mr-btn" class="px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 ${state.propertySchedule.noticeLang === 'MR' ? 'bg-premium-emerald text-white shadow' : 'text-slate-500 hover:text-slate-800'}">
                  <i data-lucide="languages" class="h-3 w-3"></i>
                  <span>मराठी नोटीस</span>
                </button>
              </div>
            </div>

            <!-- Clipping Frame -->
            <div class="newspaper-clip p-6 font-serif bg-white shadow-inner relative max-w-xl mx-auto border-4 border-double border-slate-400">
              <div class="absolute top-2 left-2 right-2 bottom-2 border border-slate-200 pointer-events-none"></div>
              
              <div id="newspaper-notice-body">
                ${renderNewspaperNotice(property)}
              </div>
            </div>
          </div>
        ` : ''}
      </div>

      <!-- Right Column Action Cards -->
      <div class="space-y-6">
        
        <!-- Reserve Price Bidding Card -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-xl space-y-6">
          <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
            <span class="text-slate-500 text-xs font-bold uppercase tracking-wider block mb-1">
              ${property.category === 'Rental' ? 'Monthly Rent' : 'Reserve Bidding Price'}
            </span>
            <h2 class="text-3xl font-extrabold text-premium-emerald flex items-center">
              <i data-lucide="indian-rupee" class="h-7 w-7 mr-0.5"></i>
              ${property.reservePrice}
            </h2>
          </div>

          <div class="space-y-4 text-xs font-semibold text-slate-600">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
              <span class="flex items-center text-slate-400"><i data-lucide="indian-rupee" class="h-4 w-4 mr-1.5"></i> Earnest Money Deposit (EMD)</span>
              <span class="text-slate-900 font-extrabold">${property.emd}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
              <span class="flex items-center text-slate-400"><i data-lucide="calendar" class="h-4 w-4 mr-1.5"></i> Auction inspection date</span>
              <span class="text-slate-900 font-extrabold">
                ${property.category === 'Auction' ? new Date(property.auctionDate).toLocaleDateString() : 'N/A (Ready to Rent)'}
              </span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
              <span class="flex items-center text-slate-400"><i data-lucide="building" class="h-4 w-4 mr-1.5"></i> Mortgage Institution</span>
              <span class="text-slate-900 font-extrabold text-right">${property.bank !== 'N/A' ? property.bank : 'Direct Seller'}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="flex items-center text-slate-400"><i data-lucide="user" class="h-4 w-4 mr-1.5"></i> Borrower Name</span>
              <span class="text-slate-900 font-extrabold text-right">${property.borrower || 'Private Seller'}</span>
            </div>
          </div>

          ${property.category === 'Auction' ? `
            <button class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md">
              Register Tender & Bid Online
            </button>
          ` : `
            <button class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md">
              Contact Owner / Negotiate
            </button>
          `}

          <button class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold py-3 rounded-xl transition-all flex items-center justify-center text-sm shadow-sm">
            <i data-lucide="file-text" class="h-4.5 w-4.5 mr-1.5 text-slate-400"></i>
            <span>Download Statutory Tender Rules</span>
          </button>
        </div>

        <!-- SITE VISIT SCHEDULER -->
        <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 text-white shadow-xl space-y-5">
          <div class="border-b border-slate-800 pb-3">
            <h3 class="font-extrabold text-white text-base flex items-center">
              <i data-lucide="clock" class="h-4.5 w-4.5 text-premium-emerald mr-2"></i>
              Schedule Physical Site Visit
            </h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Purchaser inspections and rental viewings verified schedule.</p>
          </div>

          <div id="site-visit-form-box">
            ${renderSiteVisitScheduler(agent)}
          </div>
        </div>
      </div>

    </div>
  `;

  // Bind Property detail events
  if (property.noticeEnglish) {
    document.getElementById('notice-lang-en-btn').addEventListener('click', () => {
      state.propertySchedule.noticeLang = 'EN';
      renderPropertyDetail(propertyId);
    });
    document.getElementById('notice-lang-mr-btn').addEventListener('click', () => {
      state.propertySchedule.noticeLang = 'MR';
      renderPropertyDetail(propertyId);
    });
  }

  // Bind site visit submission
  bindSiteVisitForm(agent, propertyId);
}

function renderNewspaperNotice(property) {
  if (state.propertySchedule.noticeLang === 'EN') {
    return `
      <div class="space-y-3 text-[11px] text-slate-700 leading-relaxed text-justify">
        <div class="text-center font-bold underline text-xs text-slate-900 tracking-wider">
          PUBLIC AUCTION NOTICE - ${property.bank !== 'N/A' ? property.bank.toUpperCase() : 'PRIVATE ASSET SALE'}
        </div>
        <div class="text-center text-[9px] font-semibold text-slate-500 uppercase">
          Issued Under Rule 8(6) / Section 13(2) of SARFAESI Act, 2002
        </div>
        <p class="mt-2">
          Notice is hereby given that the secured attached asset belonging to borrower **${property.borrower || 'Mortgager'}** scheduled below will be auctioned online on "AS IS WHERE IS" basis. Dues amount to be recovered.
        </p>
        <p>
          <strong>Reserve Price:</strong> ${property.reservePrice} | <strong>EMD:</strong> ${property.emd}
        </p>
        <div class="bg-slate-50 p-2 border border-slate-200 font-sans text-[9px] leading-normal text-slate-600">
          <strong>SCHEDULED PROPERTY:</strong> ${property.address}
        </div>
        <div class="text-right font-sans text-[9px] font-bold text-slate-500 pt-2 uppercase">
          Authorized Officer, ${property.bank !== 'N/A' ? property.bank : 'Seller Representative'}
        </div>
      </div>
    `;
  } else {
    return `
      <div class="space-y-3 text-[11px] text-slate-700 leading-relaxed text-justify font-sans">
        <div class="text-center font-bold underline text-xs text-slate-900 tracking-wider">
          जाहीर लिलाव नोटीस - ${property.bank !== 'N/A' ? property.bank : 'खाजगी मालमत्ता विक्री'}
        </div>
        <div class="text-center text-[9px] font-semibold text-slate-500 uppercase">
          सरफेसी कायदा, २००२ च्या नियम ८(६) / कलम १३(२) अन्वये प्रसिद्ध नोटीस
        </div>
        <p class="mt-2">
          याद्वारे जाहीर नोटीस देण्यात येते की कर्जदार **${property.borrower || 'हमीदार'}** यांच्या तारण मालमत्तेचा ऑनलाईन ई-लिलाव "आहे त्या स्थितीत" तत्वावर करण्यात येत आहे. बँकेची कर्ज वसुली केली जाणार आहे.
        </p>
        <p>
          <strong>राखीव किंमत:</strong> ${property.reservePrice} | <strong>इसारा रक्कम:</strong> ${property.emd}
        </p>
        <div class="bg-slate-50 p-2 border border-slate-200 text-[9px] leading-normal text-slate-600">
          <strong>मालमत्ता तपशील अनुसूची:</strong> ${property.address}
        </div>
        <div class="text-right text-[9px] font-bold text-slate-500 pt-2">
          प्राधिकृत अधिकारी, ${property.bank !== 'N/A' ? property.bank : 'मालक प्रतिनिधी'}
        </div>
      </div>
    `;
  }
}

function renderSiteVisitScheduler(agent) {
  if (state.propertySchedule.success) {
    return `
      <div class="space-y-4 text-center py-4">
        <div class="w-12 h-12 bg-emerald-50 text-premium-emerald rounded-full flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
          <i data-lucide="check-circle-2" class="h-7 w-7 text-premium-emerald"></i>
        </div>
        <div>
          <h4 class="font-bold text-white text-sm">Site Visit Arranged!</h4>
          <p class="text-[10px] text-slate-400 mt-0.5">Date: ${state.propertySchedule.date} at ${state.propertySchedule.timeSlot}</p>
        </div>
        <div class="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/60 text-left space-y-3">
          <div class="flex items-center space-x-2.5">
            <img src="${agent.image}" alt="${agent.name}" class="h-8 w-8 rounded-full object-cover border border-premium-emerald/50" />
            <div>
              <div class="text-[10px] text-slate-400 font-bold uppercase">Your Assigned Agent:</div>
              <div class="text-xs font-bold text-white">${agent.name}</div>
            </div>
          </div>
          <p class="text-[10px] text-slate-400 leading-normal border-t border-slate-700/60 pt-2">
            Our verified partner agent will coordinate and meet you directly at the asset address. SMS alerts have been generated.
          </p>
        </div>
      </div>
    `;
  }

  return `
    <form id="property-visit-form" class="space-y-3 text-xs text-left">
      <div>
        <label class="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Select Viewing Date</label>
        <input type="date" id="visit-date-input" required value="${state.propertySchedule.date}" class="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-white" />
      </div>

      <div>
        <label class="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Select Time Slot</label>
        <select id="visit-time-select" class="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-slate-300">
          <option value="10:00 AM" ${state.propertySchedule.timeSlot === '10:00 AM' ? 'selected' : ''}>10:00 AM - Morning Slot</option>
          <option value="11:30 AM" ${state.propertySchedule.timeSlot === '11:30 AM' ? 'selected' : ''}>11:30 AM - Morning Slot</option>
          <option value="02:00 PM" ${state.propertySchedule.timeSlot === '02:00 PM' ? 'selected' : ''}>02:00 PM - Afternoon Slot</option>
          <option value="04:30 PM" ${state.propertySchedule.timeSlot === '04:30 PM' ? 'selected' : ''}>04:30 PM - Evening Slot</option>
        </select>
      </div>

      <div>
        <label class="block font-bold text-slate-400 mb-1 uppercase tracking-wider text-[9px]">Visitor Mobile Number</label>
        <input type="tel" id="visit-phone-input" required placeholder="e.g. +91 98989..." value="${state.propertySchedule.phone}" class="w-full bg-slate-800 border border-slate-700 rounded-xl py-2 px-3 focus:outline-none focus:border-premium-emerald font-semibold text-white" />
      </div>

      <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white font-extrabold py-3 rounded-xl transition-all shadow-md flex items-center justify-center space-x-1">
        <span>Book Free Inspector Visit</span>
        <i data-lucide="arrow-right" class="h-4 w-4"></i>
      </button>
    </form>
  `;
}

function bindSiteVisitForm(agent, propertyId) {
  const form = document.getElementById('property-visit-form');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      state.propertySchedule.date = document.getElementById('visit-date-input').value;
      state.propertySchedule.timeSlot = document.getElementById('visit-time-select').value;
      state.propertySchedule.phone = document.getElementById('visit-phone-input').value;
      state.propertySchedule.success = true;
      renderPropertyDetail(propertyId);
    });
  }
}
