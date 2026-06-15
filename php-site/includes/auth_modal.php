<?php
// php-site/includes/auth_modal.php
?>
<!-- Secure Authentication Modal -->
<div id="auth-modal-wrapper" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <!-- Backdrop overlay -->
  <div id="auth-modal-close" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAuthModal()"></div>

  <!-- Modal Card Window -->
  <div class="relative bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
    
    <!-- Top banner header -->
    <div class="bg-gradient-to-r from-premium-emerald to-teal-600 px-6 py-8 text-white relative">
      <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition-colors">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
      <div class="inline-flex items-center space-x-1.5 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
        <i data-lucide="sparkles" class="h-3 w-3"></i>
        <span>Maharashtra Portal</span>
      </div>
      <h2 class="text-2xl font-extrabold tracking-tight">Secure Sign-In</h2>
      <p class="text-emerald-100 text-sm mt-1">Access instant auction alerts and legal guidance reports.</p>
    </div>

    <!-- Tab header controls -->
    <div class="flex border-b border-slate-100 bg-slate-50">
      <button class="auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-premium-emerald text-premium-emerald bg-white" onclick="switchAuthTab('email')">Email Login</button>
      <button class="auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700" onclick="switchAuthTab('google')">Google</button>
      <button class="auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700" onclick="switchAuthTab('qr')">Scan QR</button>
    </div>

    <!-- Main modal tab dynamic body -->
    <div id="auth-modal-content" class="p-6 bg-white min-h-[320px] flex flex-col justify-center">
      <!-- Renders dynamically -->
    </div>

    <!-- Legal tag footer -->
    <div class="bg-slate-50 px-6 py-4 flex items-center justify-center space-x-2 text-xs font-semibold text-slate-500 border-t border-slate-100">
      <i data-lucide="shield-alert" class="h-4 w-4 text-emerald-500"></i>
      <span>Fully compliant with SARFAESI portal encryption.</span>
    </div>
  </div>
</div>

<script>
  let activeTab = 'email';

  function openAuthModal() {
    document.getElementById('auth-modal-wrapper').classList.remove('hidden');
    renderAuthForm();
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeAuthModal() {
    document.getElementById('auth-modal-wrapper').classList.add('hidden');
  }

  function switchAuthTab(tabName) {
    activeTab = tabName;
    
    // Update tab styles
    const tabs = document.querySelectorAll('.auth-tab');
    tabs.forEach((tab, index) => {
      const tabTabs = ['email', 'google', 'qr'];
      if (tabTabs[index] === tabName) {
        tab.className = "auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-premium-emerald text-premium-emerald bg-white";
      } else {
        tab.className = "auth-tab flex-1 py-4 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700";
      }
    });

    renderAuthForm();
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function renderAuthForm() {
    const container = document.getElementById('auth-modal-content');
    if (activeTab === 'email') {
      container.innerHTML = `
        <form onsubmit="handleAuthSubmit(event)" class="space-y-4">
          <div id="auth-error-msg" class="hidden text-xs text-red-600 bg-red-50 p-3 rounded-lg font-semibold border border-red-200"></div>
          
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Select Role</label>
            <div class="grid grid-cols-3 gap-2">
              <label class="flex items-center justify-center space-x-1 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                <input type="radio" name="role" value="buyer" checked class="text-premium-emerald focus:ring-premium-emerald h-4.5 w-4.5">
                <span class="text-xs font-bold text-slate-700">Buyer</span>
              </label>
              <label class="flex items-center justify-center space-x-1 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                <input type="radio" name="role" value="seller" class="text-premium-emerald focus:ring-premium-emerald h-4.5 w-4.5">
                <span class="text-xs font-bold text-slate-700">Seller</span>
              </label>
              <label class="flex items-center justify-center space-x-1 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                <input type="radio" name="role" value="admin" class="text-premium-emerald focus:ring-premium-emerald h-4.5 w-4.5">
                <span class="text-xs font-bold text-slate-700">Admin</span>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
            <div class="relative">
              <i data-lucide="mail" class="absolute left-3 top-3 h-5 w-5 text-slate-400"></i>
              <input type="email" id="auth-email" required placeholder="name@domain.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
            </div>
          </div>

          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Password</label>
            <div class="relative">
              <i data-lucide="lock" class="absolute left-3 top-3 h-5 w-5 text-slate-400"></i>
              <input type="password" id="auth-password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
            </div>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-100 flex items-center justify-center space-x-2">
            <span>Verify & Enter Portal</span>
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
          </button>

          <p class="text-center text-xs text-slate-400 font-semibold">
            New user? The system will auto-register you on your first login!
          </p>
        </form>
      `;
    } else if (activeTab === 'google') {
      container.innerHTML = `
        <div class="flex flex-col items-center justify-center text-center py-6 space-y-4">
          <div class="h-16 w-16 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100 shadow-inner">
            <svg class="h-8 w-8" viewBox="0 0 24 24">
              <path fill="#EA4335" d="M12 5.04c1.62 0 3.08.56 4.22 1.65l3.15-3.15C17.45 1.73 14.96 1 12 1 7.35 1 3.37 3.67 1.39 7.56l3.69 2.87C6.01 7.5 8.79 5.04 12 5.04z"/>
              <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.4 3.58l3.68 2.85c2.14-1.98 3.75-4.9 3.75-8.58z"/>
              <path fill="#FBBC05" d="M5.08 14.71c-.24-.71-.37-1.47-.37-2.71s.13-2 .37-2.71L1.39 6.42C.5 8.2.01 10.1.01 12c0 1.9.49 3.8 1.38 5.58l3.69-2.87z"/>
              <path fill="#34A853" d="M12 23c3.24 0 5.97-1.09 7.96-2.96l-3.68-2.85c-1.19.8-2.71 1.28-4.28 1.28-3.21 0-5.99-2.46-6.92-5.39L1.39 16.1C3.37 19.99 7.35 22.99 12 22.99z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-800">Bypass OAuth Form</h3>
            <p class="text-xs font-semibold text-slate-500 mt-1 max-w-xs">Instantly authenticate using mock federated sandbox.</p>
          </div>
          <button onclick="handleOAuthSimulation('Sayali Patil', 'sayali.patil@outlook.com', 'buyer')" class="w-full max-w-xs bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all">
            Simulate Google SSO Login
          </button>
        </div>
      `;
    } else if (activeTab === 'qr') {
      container.innerHTML = `
        <div id="qr-scan-container" class="flex flex-col items-center justify-center text-center py-4 space-y-4">
          <div class="relative bg-slate-100 p-4 rounded-3xl border border-slate-200 shadow-inner flex items-center justify-center">
            <!-- Simulated QR code pattern -->
            <div class="h-36 w-36 bg-slate-900 rounded-xl relative overflow-hidden flex items-center justify-center">
              <div class="absolute inset-2 border-4 border-white flex flex-wrap justify-between p-2">
                <div class="h-6 w-6 border-4 border-white bg-slate-900"></div>
                <div class="h-6 w-6 border-4 border-white bg-slate-900"></div>
                <div class="h-6 w-6 border-4 border-white bg-slate-900"></div>
                <div class="h-8 w-8 bg-white/20 rounded self-end"></div>
              </div>
              <!-- Laser scan effect -->
              <div class="absolute left-0 right-0 h-1 bg-emerald-400 shadow-md shadow-emerald-500 animate-scan"></div>
            </div>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-800">Scan Mobile QR Code</h3>
            <p class="text-xs font-semibold text-slate-500 mt-1 max-w-xs">Synchronize login session via MahaApp companion app.</p>
          </div>
          <button onclick="simulateQRScan()" class="w-full max-w-xs bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all">
            Simulate Mobile Scan Detection
          </button>
        </div>
      `;
    }
  }

  function handleAuthSubmit(e) {
    e.preventDefault();
    const email = document.getElementById('auth-email').value;
    const password = document.getElementById('auth-password').value;
    const roleRadio = document.querySelector('input[name="role"]:checked');
    const role = roleRadio ? roleRadio.value : 'buyer';
    const errorEl = document.getElementById('auth-error-msg');
    
    errorEl.classList.add('hidden');

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role', role);
    formData.append('action', 'login');

    fetch('api/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        errorEl.textContent = data.message;
        errorEl.classList.remove('hidden');
      }
    })
    .catch(err => {
      errorEl.textContent = 'Server response error. Please try again.';
      errorEl.classList.remove('hidden');
    });
  }

  function handleOAuthSimulation(name, email, role) {
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('role', role);
    formData.append('action', 'oauth');

    fetch('api/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      }
    });
  }

  function simulateQRScan() {
    const container = document.getElementById('qr-scan-container');
    container.innerHTML = `
      <div class="flex flex-col items-center justify-center py-8 space-y-4">
        <div class="h-12 w-12 border-4 border-slate-200 border-t-premium-emerald rounded-full animate-spin"></div>
        <div>
          <h3 class="text-sm font-bold text-slate-800">Secure Token Received</h3>
          <p class="text-xs text-slate-400 font-semibold mt-1">Completing handshake encryption protocol...</p>
        </div>
      </div>
    `;

    setTimeout(() => {
      handleOAuthSimulation('Sayali Patil', 'sayali.patil@outlook.com', 'buyer');
    }, 1500);
  }
</script>
