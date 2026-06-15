<?php
// php-site/includes/modals.php
?>
<!-- 1. 7-Day Free Trial Lead Modal -->
<div id="trial-modal-wrapper" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <!-- Backdrop overlay -->
  <div id="trial-modal-close" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeTrialModal()"></div>

  <div class="relative bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-200 z-10 p-6 flex flex-col text-left">
    <button onclick="closeTrialModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>
    
    <div id="trial-modal-content">
      <div class="text-center py-4 space-y-4">
        <div class="mx-auto h-12 w-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-100">
          <i data-lucide="sparkles" class="h-6 w-6"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-slate-800">Claim 7-Day Free Trial</h3>
          <p class="text-xs text-slate-500 font-semibold mt-1">Get immediate SMS alerts for upcoming bank foreclosure sales.</p>
        </div>
        <form onsubmit="handleTrialSubmit(event)" class="space-y-3">
          <input type="hidden" id="trial-campaign" value="General Portal">
          
          <input type="text" id="trial-name" required placeholder="Full Name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          
          <input type="email" id="trial-email" required placeholder="Email Address" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          
          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all">
            Claim Free Trial
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- 2. Certified Agent Connect Lead Modal -->
<div id="agent-modal-wrapper" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <!-- Backdrop overlay -->
  <div id="agent-modal-close" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAgentModal()"></div>

  <div class="relative bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-200 z-10 p-6 flex flex-col text-left">
    <button onclick="closeAgentModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
      <i data-lucide="x" class="h-5 w-5"></i>
    </button>

    <div id="agent-modal-content">
      <div class="space-y-4">
        <div class="flex items-center space-x-3 pb-2 border-b border-slate-100">
          <div class="h-10 w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald">
            <i data-lucide="message-square" class="h-5 w-5"></i>
          </div>
          <div>
            <h3 class="text-base font-black text-slate-800">Connect with Partner</h3>
            <p id="agent-modal-title-name" class="text-xs text-slate-500 font-semibold"></p>
          </div>
        </div>
        <form onsubmit="handleAgentConnectSubmit(event)" class="space-y-3">
          <input type="hidden" id="agent-modal-id">
          
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Your Name</label>
            <input type="text" id="agent-connect-name" required placeholder="Aravind Kumar" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          </div>

          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Mobile Number</label>
            <input type="text" id="agent-connect-phone" required placeholder="+91 99999 99999" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          </div>

          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Message Description</label>
            <textarea id="agent-connect-msg" rows="3" required placeholder="I am interested in scheduling a site visit or bidding details." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-semibold"></textarea>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all flex items-center justify-center space-x-2">
            <span>Send Secure Inquiry</span>
            <i data-lucide="send" class="h-4 w-4"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openTrialModal(campaign) {
    document.getElementById('trial-campaign').value = campaign || 'General Portal';
    document.getElementById('trial-modal-wrapper').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeTrialModal() {
    document.getElementById('trial-modal-wrapper').classList.add('hidden');
  }

  function handleTrialSubmit(e) {
    e.preventDefault();
    const campaign = document.getElementById('trial-campaign').value;
    const name = document.getElementById('trial-name').value;
    const email = document.getElementById('trial-email').value;

    const formData = new FormData();
    formData.append('campaign', campaign);
    formData.append('name', name);
    formData.append('email', email);

    const content = document.getElementById('trial-modal-content');
    content.innerHTML = `
      <div class="flex flex-col items-center justify-center py-8 space-y-4">
        <div class="h-10 w-10 border-4 border-slate-200 border-t-premium-emerald rounded-full animate-spin"></div>
        <p class="text-xs font-semibold text-slate-400">Claiming your sandbox subscription...</p>
      </div>
    `;

    fetch('api/submit_lead.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        content.innerHTML = `
          <div class="text-center py-6 space-y-4">
            <div class="mx-auto h-12 w-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-premium-emerald border border-emerald-100">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-black text-slate-800">Trial Activated!</h3>
              <p class="text-xs text-slate-500 font-semibold mt-1">Check your inbox. Standard forensic alerts are now live.</p>
            </div>
            <button onclick="closeTrialModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold transition-all">
              Done
            </button>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    });
  }

  function openAgentModal(agentId, agentName) {
    document.getElementById('agent-modal-id').value = agentId;
    document.getElementById('agent-modal-title-name').textContent = "Inquiry assigned to: " + agentName;
    document.getElementById('agent-modal-wrapper').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeAgentModal() {
    document.getElementById('agent-modal-wrapper').classList.add('hidden');
  }

  function handleAgentConnectSubmit(e) {
    e.preventDefault();
    const agentId = document.getElementById('agent-modal-id').value;
    const name = document.getElementById('agent-connect-name').value;
    const phone = document.getElementById('agent-connect-phone').value;
    const message = document.getElementById('agent-connect-msg').value;

    const formData = new FormData();
    formData.append('agent_id', agentId);
    formData.append('name', name);
    formData.append('phone', phone);
    formData.append('message', message);

    const content = document.getElementById('agent-modal-content');
    content.innerHTML = `
      <div class="flex flex-col items-center justify-center py-8 space-y-4">
        <div class="h-10 w-10 border-4 border-slate-200 border-t-premium-emerald rounded-full animate-spin"></div>
        <p class="text-xs font-semibold text-slate-400">Sending secure lead inquiry...</p>
      </div>
    `;

    fetch('api/connect_agent.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        content.innerHTML = `
          <div class="text-center py-6 space-y-4">
            <div class="mx-auto h-12 w-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-premium-emerald border border-emerald-100">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-black text-slate-800">Message Dispatched!</h3>
              <p class="text-xs text-slate-500 font-semibold mt-1">The agent has been notified and will call you back shortly.</p>
            </div>
            <button onclick="closeAgentModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold transition-all">
              Done
            </button>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    });
  }
</script>
