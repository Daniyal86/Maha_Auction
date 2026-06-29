<?php
// includes/auth_modal.php
?>
<!-- Login / Sign Up Modal -->
<div id="auth-modal-wrapper" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <!-- Dark background overlay -->
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAuthModal()"></div>

  <!-- Modal Card -->
  <div id="auth-modal-card" class="relative bg-white rounded-3xl w-full max-w-sm sm:max-w-md max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 z-10 transform scale-95 transition-all duration-300">

    <!-- Top colored header -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 px-6 py-7 text-white relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-white/10 blur-2xl"></div>
      <div class="absolute -left-6 -bottom-6 w-20 h-20 rounded-full bg-teal-300/10 blur-xl"></div>

      <!-- Close button -->
      <button onclick="closeAuthModal()" class="absolute top-4 right-4 bg-white/10 hover:bg-white/20 p-2 rounded-full transition-all hover:rotate-90">
        <i data-lucide="x" class="h-4 w-4 text-white"></i>
      </button>

      <!-- Heading -->
      <div id="auth-modal-heading">
        <p class="text-emerald-200 text-xs font-bold mb-1">👋 Welcome to MahaAuctions</p>
        <h2 class="text-xl font-black tracking-tight">Login or Create Account</h2>
        <p class="text-emerald-100/80 text-xs mt-1">Enter your details below to get started</p>
      </div>
    </div>

    <!-- Tab Switcher: Login / New Account -->
    <div class="flex bg-slate-50 border-b border-slate-100 p-1.5 gap-1.5">
      <button id="tab-login-btn" onclick="switchMode('login')"
        class="flex-1 py-2.5 rounded-xl text-xs font-extrabold transition-all bg-white text-premium-emerald shadow-sm border border-slate-100">
        Login
      </button>
      <button id="tab-register-btn" onclick="switchMode('register')"
        class="flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-700 transition-all">
        Create Account
      </button>
    </div>

    <!-- Form Body -->
    <div class="p-6">

      <!-- Google Identity Setup -->
      <?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com'): ?>
      <div id="g_id_onload"
           data-client_id="<?php echo htmlspecialchars(GOOGLE_CLIENT_ID); ?>"
           data-context="use"
           data-ux_mode="popup"
           data-callback="handleGoogleCallback"
           data-auto_prompt="false">
      </div>
      <?php endif; ?>

      <!-- Error message box -->
      <div id="auth-error-msg" class="hidden mb-4 text-xs text-red-600 bg-red-50 border border-red-100 p-3.5 rounded-2xl font-semibold flex items-start space-x-2">
        <i data-lucide="alert-circle" class="h-4 w-4 text-red-500 shrink-0 mt-0.5"></i>
        <span id="auth-error-text"></span>
      </div>

      <!-- Success message box -->
      <div id="auth-success-msg" class="hidden mb-4 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 p-3.5 rounded-2xl font-semibold flex items-start space-x-2">
        <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 shrink-0 mt-0.5"></i>
        <span id="auth-success-text"></span>
      </div>

      <!-- LOGIN FORM -->
      <form id="login-form" onsubmit="handleLoginSubmit(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Your Email</label>
          <div class="relative">
            <i data-lucide="mail" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="email" id="login-email" required placeholder="e.g. ramesh@gmail.com"
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Your Password</label>
          <div class="relative">
            <i data-lucide="lock" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="password" id="login-password" required placeholder="Enter your password"
              class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
            <button type="button" onclick="togglePass('login-password','login-eye')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
              <i data-lucide="eye" id="login-eye" class="h-4 w-4"></i>
            </button>
          </div>
        </div>

        <button type="submit" id="login-btn"
          class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3 rounded-xl text-sm font-extrabold shadow transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
          <i data-lucide="log-in" class="h-4 w-4"></i>
          <span>Login to My Account</span>
        </button>

        <div class="relative flex py-2 items-center">
          <div class="flex-grow border-t border-slate-200"></div>
          <span class="flex-shrink-0 mx-4 text-slate-400 text-[10px] uppercase font-bold tracking-wider">Or continue with</span>
          <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
          <button type="button" onclick="triggerSocialAuth('google')" class="flex items-center justify-center gap-2 px-3 py-2 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition-all">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M21.35 11.1H12v2.7h5.38C16.88 15.65 14.77 17 12 17a5 5 0 1 1 0-10 4.86 4.86 0 0 1 3.47 1.43l2-2A7.88 7.88 0 0 0 12 4 8 8 0 1 0 20 12c0-.3-.03-.6-.08-.9z" fill="#4285F4"/>
            </svg>
            <span>Google</span>
          </button>
          <button type="button" onclick="triggerSocialAuth('facebook')" class="flex items-center justify-center gap-2 px-3 py-2 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition-all">
            <svg class="h-5 w-5 text-[#1877F2] shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
          </button>
        </div>

        <p class="text-center text-xs text-slate-500">
          Don't have an account?
          <button type="button" onclick="switchMode('register')" class="text-premium-emerald font-bold hover:underline">Create one free →</button>
        </p>
      </form>

      <!-- REGISTER FORM -->
      <form id="register-form" onsubmit="handleRegisterSubmit(event)" class="space-y-4 hidden">

        <!-- Who are you? -->
        <div>
          <label class="block text-xs font-bold text-slate-600 mb-2">I want to join as a...</label>
          <div class="grid grid-cols-3 gap-2">
            <label class="cursor-pointer">
              <input type="radio" name="reg-role" value="buyer" checked onchange="handleRoleChange(this.value)" class="peer sr-only">
              <div class="flex flex-col items-center p-3 border border-slate-200 rounded-2xl transition-all text-slate-500 peer-checked:text-premium-emerald peer-checked:border-premium-emerald peer-checked:bg-emerald-50 hover:bg-slate-50 hover:border-slate-300">
                <i data-lucide="search" class="h-5 w-5"></i>
                <span class="text-[11px] font-bold mt-1">Buyer</span>
                <span class="text-[9px] text-slate-400 font-medium text-center leading-tight mt-0.5">I want to find properties</span>
              </div>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="reg-role" value="seller" onchange="handleRoleChange(this.value)" class="peer sr-only">
              <div class="flex flex-col items-center p-3 border border-slate-200 rounded-2xl transition-all text-slate-500 peer-checked:text-premium-emerald peer-checked:border-premium-emerald peer-checked:bg-emerald-50 hover:bg-slate-50 hover:border-slate-300">
                <i data-lucide="home" class="h-5 w-5"></i>
                <span class="text-[11px] font-bold mt-1">Seller</span>
                <span class="text-[9px] text-slate-400 font-medium text-center leading-tight mt-0.5">I want to list properties</span>
              </div>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="reg-role" value="lawyer" onchange="handleRoleChange(this.value)" class="peer sr-only">
              <div class="flex flex-col items-center p-3 border border-slate-200 rounded-2xl transition-all text-slate-500 peer-checked:text-premium-emerald peer-checked:border-premium-emerald peer-checked:bg-emerald-50 hover:bg-slate-50 hover:border-slate-300">
                <i data-lucide="scale" class="h-5 w-5"></i>
                <span class="text-[11px] font-bold mt-1">Lawyer</span>
                <span class="text-[9px] text-slate-400 font-medium text-center leading-tight mt-0.5">I provide legal advisory</span>
              </div>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Your Full Name</label>
          <div class="relative">
            <i data-lucide="user" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="text" id="reg-name" required placeholder="e.g. Ramesh Patil"
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Your Email Address</label>
          <div class="relative">
            <i data-lucide="mail" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="email" id="reg-email" required placeholder="e.g. ramesh@gmail.com"
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Mobile Number</label>
          <div class="relative">
            <i data-lucide="phone" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="tel" id="reg-phone" required placeholder="e.g. 9876543210"
              class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
          </div>
        </div>

        <!-- Bar Enrollment ID field (shown only for lawyer) -->
        <div id="reg-enrollment-wrapper" class="hidden">
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Bar Enrollment ID</label>
          <div class="flex rounded-xl bg-slate-50 border border-slate-200 focus-within:border-premium-emerald focus-within:bg-white transition-all overflow-hidden relative">
            <span class="bg-slate-100 border-r border-slate-200 px-3.5 py-2.5 text-sm font-bold text-slate-500 flex items-center select-none shrink-0">MAH/</span>
            <input type="text" id="reg-enrollment" placeholder="12345/2026"
              class="w-full px-3.5 py-2.5 bg-transparent border-0 focus:outline-none text-sm text-slate-800 font-semibold">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1.5">Choose a Password</label>
          <div class="relative">
            <i data-lucide="lock" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
            <input type="password" id="reg-password" required placeholder="Min. 6 characters"
              class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all text-slate-800">
            <button type="button" onclick="togglePass('reg-password','reg-eye')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
              <i data-lucide="eye" id="reg-eye" class="h-4 w-4"></i>
            </button>
          </div>
        </div>

        <button type="submit" id="register-btn"
          class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3 rounded-xl text-sm font-extrabold shadow transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
          <i data-lucide="user-plus" class="h-4 w-4"></i>
          <span>Create My Account</span>
        </button>

        <div class="relative flex py-2 items-center">
          <div class="flex-grow border-t border-slate-200"></div>
          <span class="flex-shrink-0 mx-4 text-slate-400 text-[10px] uppercase font-bold tracking-wider">Or continue with</span>
          <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
          <button type="button" onclick="triggerSocialAuth('google')" class="flex items-center justify-center gap-2 px-3 py-2 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition-all">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M21.35 11.1H12v2.7h5.38C16.88 15.65 14.77 17 12 17a5 5 0 1 1 0-10 4.86 4.86 0 0 1 3.47 1.43l2-2A7.88 7.88 0 0 0 12 4 8 8 0 1 0 20 12c0-.3-.03-.6-.08-.9z" fill="#4285F4"/>
            </svg>
            <span>Google</span>
          </button>
          <button type="button" onclick="triggerSocialAuth('facebook')" class="flex items-center justify-center gap-2 px-3 py-2 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition-all">
            <svg class="h-5 w-5 text-[#1877F2] shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
          </button>
        </div>

        <p class="text-center text-xs text-slate-500">
          Already have an account?
          <button type="button" onclick="switchMode('login')" class="text-premium-emerald font-bold hover:underline">Login →</button>
        </p>
      </form>

    </div>

    <!-- Footer note -->
    <div class="bg-slate-50 border-t border-slate-100 px-6 py-3 flex items-center justify-center space-x-2 text-[10px] text-slate-400 font-semibold">
      <i data-lucide="shield-check" class="h-3.5 w-3.5 text-premium-emerald"></i>
      <span>Your details are safe and secure with us</span>
    </div>

  </div>
</div>

<script>
  let authMode = 'login'; // 'login' or 'register'

  function openAuthModal() {
    const wrapper = document.getElementById('auth-modal-wrapper');
    const card = document.getElementById('auth-modal-card');
    wrapper.classList.remove('hidden');
    document.body.classList.add('modal-open');
    setTimeout(() => {
      card.classList.remove('scale-95');
      card.classList.add('scale-100');
    }, 10);
    switchMode('login');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeAuthModal() {
    const wrapper = document.getElementById('auth-modal-wrapper');
    const card = document.getElementById('auth-modal-card');
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
    document.body.classList.remove('modal-open');
    setTimeout(() => { wrapper.classList.add('hidden'); }, 200);
  }

  function switchMode(mode) {
    authMode = mode;
    const loginForm    = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const loginBtn     = document.getElementById('tab-login-btn');
    const registerBtn  = document.getElementById('tab-register-btn');
    const heading      = document.getElementById('auth-modal-heading');

    clearMessages();

    if (mode === 'login') {
      loginForm.classList.remove('hidden');
      registerForm.classList.add('hidden');
      loginBtn.className    = 'flex-1 py-2.5 rounded-xl text-xs font-extrabold transition-all bg-white text-premium-emerald shadow-sm border border-slate-100';
      registerBtn.className = 'flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-700 transition-all';
      heading.innerHTML = `
        <p class="text-emerald-200 text-xs font-bold mb-1">👋 Welcome Back!</p>
        <h2 class="text-xl font-black tracking-tight">Login to Your Account</h2>
        <p class="text-emerald-100/80 text-xs mt-1">Enter your email and password to continue</p>
      `;
    } else {
      loginForm.classList.add('hidden');
      registerForm.classList.remove('hidden');
      loginBtn.className    = 'flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-700 transition-all';
      registerBtn.className = 'flex-1 py-2.5 rounded-xl text-xs font-extrabold transition-all bg-white text-premium-emerald shadow-sm border border-slate-100';
      heading.innerHTML = `
        <p class="text-emerald-200 text-xs font-bold mb-1">🏠 New here?</p>
        <h2 class="text-xl font-black tracking-tight">Create Your Free Account</h2>
        <p class="text-emerald-100/80 text-xs mt-1">It only takes a few seconds!</p>
      `;
      const roleEl = document.querySelector('input[name="reg-role"]:checked');
      if (roleEl) handleRoleChange(roleEl.value);
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      input.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function showError(msg) {
    const el = document.getElementById('auth-error-msg');
    document.getElementById('auth-error-text').textContent = msg;
    el.classList.remove('hidden');
    document.getElementById('auth-success-msg').classList.add('hidden');
  }

  function showSuccess(msg) {
    const el = document.getElementById('auth-success-msg');
    document.getElementById('auth-success-text').textContent = msg;
    el.classList.remove('hidden');
    document.getElementById('auth-error-msg').classList.add('hidden');
  }

  function clearMessages() {
    document.getElementById('auth-error-msg').classList.add('hidden');
    document.getElementById('auth-success-msg').classList.add('hidden');
  }

  function setButtonLoading(btnId, loading, text) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.disabled = loading;
    btn.innerHTML = loading
      ? `<div class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div><span>Please wait...</span>`
      : text;
  }

  // ---- LOGIN ----
  function handleLoginSubmit(e) {
    e.preventDefault();
    clearMessages();
    const email    = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    if (!email || !password) { showError('Please enter your email and password.'); return; }

    setButtonLoading('login-btn', true);

    const data = new FormData();
    data.append('email', email);
    data.append('password', password);
    data.append('role', 'buyer');
    data.append('action', 'login');

    fetch('api/auth.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showSuccess('Login successful! Taking you in...');
          setTimeout(() => window.location.href = res.redirect_url, 800);
        } else {
          setButtonLoading('login-btn', false, '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>');
          showError(res.message || 'Wrong email or password. Please try again.');
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      })
      .catch(() => {
        setButtonLoading('login-btn', false, '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>');
        showError('Could not connect. Please check your internet and try again.');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      });
  }

  function handleRoleChange(val) {
    const wrapper = document.getElementById('reg-enrollment-wrapper');
    const input = document.getElementById('reg-enrollment');
    if (!wrapper || !input) return;
    if (val === 'lawyer') {
      wrapper.classList.remove('hidden');
      input.required = true;
    } else {
      wrapper.classList.add('hidden');
      input.required = false;
    }
  }

  // ---- REGISTER ----
  function handleRegisterSubmit(e) {
    e.preventDefault();
    clearMessages();
    const name     = document.getElementById('reg-name').value.trim();
    const email    = document.getElementById('reg-email').value.trim();
    const phone    = document.getElementById('reg-phone').value.trim();
    const password = document.getElementById('reg-password').value;
    const roleEl   = document.querySelector('input[name="reg-role"]:checked');
    const role     = roleEl ? roleEl.value : 'buyer';

    if (!name)           { showError('Please enter your full name.'); return; }
    if (!email)          { showError('Please enter your email address.'); return; }
    if (!phone)          { showError('Please enter your mobile number.'); return; }
    if (password.length < 6) { showError('Password must be at least 6 characters long.'); return; }

    let enrollmentId = '';
    if (role === 'lawyer') {
      enrollmentId = document.getElementById('reg-enrollment').value.trim();
      if (!enrollmentId) { showError('Please enter your Bar Enrollment ID.'); return; }
    }

    setButtonLoading('register-btn', true);

    const data = new FormData();
    data.append('name', name);
    data.append('email', email);
    data.append('phone', phone);
    data.append('password', password);
    data.append('role', role);
    data.append('enrollment_id', enrollmentId);
    data.append('action', 'register');

    fetch('api/auth.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showSuccess('Account created! Logging you in now...');
          setTimeout(() => window.location.href = res.redirect_url, 900);
        } else {
          setButtonLoading('register-btn', false, '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
          showError(res.message || 'This email may already be registered. Try logging in.');
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      })
      .catch(() => {
        setButtonLoading('register-btn', false, '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
        showError('Could not connect. Please check your internet and try again.');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      });
  }

  // ---- GOOGLE SIGN-IN & SOCIAL AUTH ----
  function triggerSocialAuth(provider) {
    let enrollmentId = '';
    let selectedRole = 'buyer';
    if (authMode === 'register') {
      const roleEl = document.querySelector('input[name="reg-role"]:checked');
      if (roleEl) selectedRole = roleEl.value;

      if (selectedRole === 'lawyer') {
        enrollmentId = document.getElementById('reg-enrollment').value.trim();
        if (!enrollmentId) {
          showError('Please enter your Bar Enrollment ID before signing up with social account.');
          document.getElementById('reg-enrollment').focus();
          return;
        }
      }
    }

    const mockDialogHtml = `
      <div id="mock-social-dialog-overlay" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeMockSocialDialog()"></div>
        <div id="mock-social-dialog-card" class="relative bg-white rounded-3xl w-full max-w-sm shadow-2xl border border-slate-100 p-6 z-10 space-y-6 text-center transform scale-95 transition-all duration-300">
          <div class="flex flex-col items-center">
            ${provider === 'google' ? `
              <div class="h-14 w-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center shadow-inner mb-3">
                <svg class="h-8 w-8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21.35 11.1H12v2.7h5.38C16.88 15.65 14.77 17 12 17a5 5 0 1 1 0-10 4.86 4.86 0 0 1 3.47 1.43l2-2A7.88 7.88 0 0 0 12 4 8 8 0 1 0 20 12c0-.3-.03-.6-.08-.9z" fill="#4285F4"/>
                </svg>
              </div>
              <h3 class="text-base font-black text-slate-800">Sign in with Google</h3>
              <p class="text-xs text-slate-400 font-semibold mt-1">Select a demo profile or enter custom details</p>
            ` : `
              <div class="h-14 w-14 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-center shadow-inner mb-3">
                <svg class="h-8 w-8 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </div>
              <h3 class="text-base font-black text-slate-800">Sign in with Facebook</h3>
              <p class="text-xs text-slate-400 font-semibold mt-1">Select a demo profile or enter custom details</p>
            `}
          </div>

          <div class="space-y-2 text-left">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Quick Select Profile</label>
            <div class="grid grid-cols-1 gap-2">
              <button type="button" onclick="selectMockProfile('Ramesh Patil', 'ramesh@gmail.com', 'buyer', '${provider}', '${selectedRole}', '${enrollmentId}')" class="flex items-center justify-between p-3 border border-slate-150 rounded-2xl hover:bg-slate-50 transition-all text-left w-full">
                <div>
                  <div class="text-xs font-black text-slate-800">Ramesh Patil (Buyer)</div>
                  <div class="text-[10px] text-slate-400 font-semibold">ramesh@gmail.com</div>
                </div>
                <span class="text-[9px] bg-emerald-50 text-premium-emerald border border-emerald-100 px-2 py-0.5 rounded-full font-bold uppercase">Buyer</span>
              </button>
              <button type="button" onclick="selectMockProfile('Suresh Kumar', 'suresh@gmail.com', 'seller', '${provider}', '${selectedRole}', '${enrollmentId}')" class="flex items-center justify-between p-3 border border-slate-150 rounded-2xl hover:bg-slate-50 transition-all text-left w-full">
                <div>
                  <div class="text-xs font-black text-slate-800">Suresh Kumar (Seller)</div>
                  <div class="text-[10px] text-slate-400 font-semibold">suresh@gmail.com</div>
                </div>
                <span class="text-[9px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded-full font-bold uppercase">Seller</span>
              </button>
              <button type="button" onclick="selectMockProfile('Adv. Sajid Kureshi', 'sajid@mahaauctions.com', 'lawyer', '${provider}', '${selectedRole}', '${enrollmentId}')" class="flex items-center justify-between p-3 border border-slate-150 rounded-2xl hover:bg-slate-50 transition-all text-left w-full">
                <div>
                  <div class="text-xs font-black text-slate-800">Adv. Sajid Kureshi (Lawyer)</div>
                  <div class="text-[10px] text-slate-400 font-semibold">sajid@mahaauctions.com</div>
                </div>
                <span class="text-[9px] bg-indigo-50 text-indigo-600 border border-indigo-100 px-2 py-0.5 rounded-full font-bold uppercase">Lawyer</span>
              </button>
            </div>
          </div>

          <div class="relative flex py-1.5 items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink-0 mx-3 text-slate-400 text-[10px] uppercase font-bold tracking-wider">Or custom</span>
            <div class="flex-grow border-t border-slate-200"></div>
          </div>

          <div class="space-y-3 text-left">
            <div>
              <label class="block text-[10px] font-bold text-slate-500 mb-1">Full Name</label>
              <input type="text" id="mock-custom-name" placeholder="e.g. John Doe" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-800">
            </div>
            <div>
              <label class="block text-[10px] font-bold text-slate-500 mb-1">Email Address</label>
              <input type="email" id="mock-custom-email" placeholder="e.g. john@example.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-800">
            </div>
          </div>

          <button type="button" onclick="submitCustomMockSocial('${provider}', '${selectedRole}', '${enrollmentId}')" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-extrabold shadow transition-all">
            Proceed to ${provider === 'google' ? 'Google' : 'Facebook'} Sign In
          </button>
        </div>
      </div>
    `;

    const container = document.createElement('div');
    container.id = 'mock-social-container';
    container.innerHTML = mockDialogHtml;
    document.body.appendChild(container);

    setTimeout(() => {
      const card = document.getElementById('mock-social-dialog-card');
      if (card) {
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
      }
    }, 10);
  }

  function closeMockSocialDialog() {
    const container = document.getElementById('mock-social-container');
    if (!container) return;
    const card = document.getElementById('mock-social-dialog-card');
    if (card) {
      card.classList.remove('scale-100');
      card.classList.add('scale-95');
    }
    setTimeout(() => { container.remove(); }, 200);
  }

  function selectMockProfile(name, email, role, provider, registerRole, enrollmentId) {
    let finalRole = role;
    if (authMode === 'register') {
      finalRole = registerRole;
    }
    submitMockSocialAuth(provider, name, email, finalRole, enrollmentId);
  }

  function submitCustomMockSocial(provider, role, enrollmentId) {
    const name = document.getElementById('mock-custom-name').value.trim();
    const email = document.getElementById('mock-custom-email').value.trim();
    if (!name || !email) {
      alert('Please fill out custom name and email address.');
      return;
    }
    submitMockSocialAuth(provider, name, email, role, enrollmentId);
  }

  function submitMockSocialAuth(provider, name, email, role, enrollmentId) {
    closeMockSocialDialog();
    clearMessages();
    setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', true);

    const action = provider === 'facebook' ? 'facebook_signin' : 'google_signin';
    const credential = `mock_${provider}_${encodeURIComponent(email)}_${encodeURIComponent(name)}`;

    const data = new FormData();
    data.append('credential', credential);
    data.append('role', role);
    data.append('enrollment_id', enrollmentId);
    data.append('action', action);

    fetch('api/auth.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showSuccess(res.is_new ? 'Social account registered! Logging in...' : 'Social login successful!');
          setTimeout(() => window.location.href = res.redirect_url, 800);
        } else {
          setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', false, 
            authMode === 'login' ? '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>' : '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
          showError(res.message || 'Social sign-in failed. Please try again.');
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      })
      .catch(() => {
        setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', false, 
          authMode === 'login' ? '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>' : '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
        showError('Could not connect. Please check your internet and try again.');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      });
  }

  // ---- GOOGLE CALLBACK (Real OAuth) ----
  function handleGoogleCallback(response) {
    clearMessages();
    setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', true);
    
    const data = new FormData();
    data.append('credential', response.credential);
    
    let role = 'buyer';
    let enrollmentId = '';
    if (authMode === 'register') {
      const roleEl = document.querySelector('input[name="reg-role"]:checked');
      if (roleEl) role = roleEl.value;

      if (role === 'lawyer') {
        enrollmentId = document.getElementById('reg-enrollment').value.trim();
      }
    }
    data.append('role', role);
    data.append('enrollment_id', enrollmentId);
    data.append('action', 'google_signin');

    fetch('api/auth.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showSuccess(res.is_new ? 'Account created! Logging you in...' : 'Login successful! Taking you in...');
          setTimeout(() => window.location.href = res.redirect_url, 900);
        } else {
          setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', false, 
            authMode === 'login' ? '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>' : '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
          showError(res.message || 'Google sign-in failed. Please try again.');
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      })
      .catch(() => {
        setButtonLoading(authMode === 'login' ? 'login-btn' : 'register-btn', false, 
          authMode === 'login' ? '<i data-lucide="log-in" class="h-4 w-4"></i><span>Login to My Account</span>' : '<i data-lucide="user-plus" class="h-4 w-4"></i><span>Create My Account</span>');
        showError('Could not connect. Please check your internet and try again.');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      });
  }
</script>
