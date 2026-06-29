<?php
// lawyer_dashboard.php
require_once 'config/db.php';
require_once 'includes/advocates_data.php';

// Check if user is logged in and has lawyer role
$is_authorized = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'lawyer';
$user_email = $is_authorized ? $_SESSION['user']['email'] : '';

$success_msg = '';
$error_msg = '';

// Fetch Consultations booked with this lawyer (matching by email for simplicity in MVP, or by a specific advocate ID if linked)
// In a real app, the lawyer user would be linked to an 'advocate_id'. For now, we fetch ALL consultations, or mock it if empty.
$consultations = [];
if ($is_authorized) {
    // Attempt to match advocate ID if the lawyer's name contains part of the advocate name
    // As a simple workaround for the MVP, let's just show all consultations or mock if none.
    $stmt = $pdo->query("SELECT * FROM consultations ORDER BY created_at DESC");
    $consultations = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

  <?php if (!$is_authorized): ?>
    <!-- Locked Unauthorized State -->
    <div class="bg-white rounded-[32px] border border-slate-100 p-12 text-center max-w-md mx-auto space-y-6 shadow-xl relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-32 h-32 bg-slate-500/5 rounded-full blur-2xl"></div>
      <div class="mx-auto h-20 w-20 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center text-slate-500 shadow-inner">
        <i data-lucide="scale" class="h-9 w-9"></i>
      </div>
      <div class="space-y-2">
        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Lawyer Dashboard Locked</h3>
        <p class="text-xs text-slate-500 font-semibold leading-relaxed px-4">Please log in or register as a certified Lawyer to manage your legal consultations and access the SARFAESI Draftsman.</p>
      </div>
      <button onclick="openAuthModal()" class="w-full bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-700 hover:to-slate-800 text-white py-3.5 rounded-2xl text-sm font-extrabold shadow-md transition-all duration-200">
        Sign In / Register As Lawyer
      </button>
    </div>

  <?php else: ?>
    <!-- Authorized Dashboard Layout -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 sm:gap-6 bg-white/40 backdrop-blur-md p-4 sm:p-6 rounded-3xl border border-slate-200/60 shadow-sm">
      <div class="space-y-1">
        <div class="flex items-center space-x-2.5">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-inner">
            <i data-lucide="briefcase" class="h-4 w-4 sm:h-5 sm:w-5"></i>
          </div>
          <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Legal Command Center</h1>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
              Dashboard for <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
              <?php if (!empty($_SESSION['user']['enrollment_id'])): ?>
                <span class="ml-2 px-2 py-0.5 bg-emerald-50 text-premium-emerald border border-emerald-100 rounded-md text-[10px] font-extrabold normal-case">
                  Bar ID: <?php echo htmlspecialchars($_SESSION['user']['enrollment_id']); ?>
                </span>
              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>
      
      <!-- Analytics Summary -->
      <div class="flex flex-wrap gap-3 w-full md:w-auto">
        <div class="bg-white flex-1 min-w-[120px] md:flex-none px-4 sm:px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3 sm:space-x-3.5 hover:shadow transition-shadow">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 shrink-0">
            <i data-lucide="eye" class="h-4 w-4 sm:h-5 sm:w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Profile Clicks</span>
            <span class="text-xl font-black text-slate-800">1,248</span>
          </div>
        </div>
        
        <div class="bg-white flex-1 min-w-[120px] md:flex-none px-4 sm:px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3 sm:space-x-3.5 hover:shadow transition-shadow">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald shrink-0">
            <i data-lucide="users" class="h-4 w-4 sm:h-5 sm:w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Service Requests</span>
            <span class="text-xl font-black text-slate-800"><?php echo count($consultations); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="-mx-4 sm:mx-0">
      <div class="flex overflow-x-auto scrollbar-hide border-b border-slate-200 px-4 sm:px-0">
        <button onclick="switchLawyerTab('overview')" id="btn-overview" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-black border-b-2 border-premium-emerald text-premium-emerald transition-all whitespace-nowrap">Command Center</button>
        <button onclick="switchLawyerTab('profile')" id="btn-profile" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all whitespace-nowrap">Public Profile Editor</button>
        <button onclick="switchLawyerTab('vault')" id="btn-vault" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all whitespace-nowrap">Document Vault</button>
      </div>
    </div>

    <div id="tab-overview" class="block space-y-10">

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mt-10">
      
      <!-- Left Column: Consultations Table -->
      <div class="lg:col-span-1 space-y-8">
        
        <!-- Legal Consultations Board -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-150 px-6 py-4 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="message-square" class="h-4.5 w-4.5 text-premium-emerald"></i>
              <span>Client Requests</span>
            </h3>
            <span class="text-[9px] bg-emerald-100 text-premium-emerald px-2 py-0.5 rounded-full font-bold uppercase tracking-wider"><?php echo count($consultations); ?> Active</span>
          </div>

          <div class="p-6">
            <?php if (count($consultations) === 0): ?>
              <div class="text-center py-6 space-y-2">
                <i data-lucide="inbox" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">No client service requests yet.</div>
                <p class="text-[10px] text-slate-400 max-w-sm mx-auto">When clients book consultations from your directory profile, they will appear here.</p>
              </div>
            <?php else: ?>
              <div class="divide-y divide-slate-100">
                <?php foreach ($consultations as $c): ?>
                  <div class="py-4 flex flex-col space-y-3 first:pt-0 last:pb-0">
                    <div class="flex justify-between items-start">
                      <div class="space-y-1">
                        <h4 class="text-xs font-black text-slate-800"><?php echo htmlspecialchars($c['topic']); ?></h4>
                        <div class="text-[10px] text-slate-400 font-bold tracking-wider">
                          Client: <?php echo htmlspecialchars($c['name']); ?>
                        </div>
                      </div>
                      <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-extrabold rounded-md uppercase">
                        Pending
                      </span>
                    </div>
                    
                    <div class="flex items-center justify-between text-[10px] font-semibold text-slate-500 bg-slate-50 p-2 rounded-lg border border-slate-100">
                      <div class="flex items-center space-x-1">
                        <i data-lucide="calendar" class="h-3 w-3 text-slate-400"></i>
                        <span><?php echo htmlspecialchars(date('d M Y', strtotime($c['booking_date']))); ?></span>
                      </div>
                      <a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" class="text-premium-emerald hover:underline flex items-center space-x-1">
                        <i data-lucide="mail" class="h-3 w-3"></i>
                        <span>Contact Client</span>
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- Right Column: Notice Draftsman -->
      <div class="lg:col-span-2">
        
        <!-- Notice Draftsman Section -->
        <div id="section-draftsman" class="grid grid-cols-1 gap-8 items-start">
          
          <!-- Draftsman Inputs Form -->
          <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 space-y-4">
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="edit-3" class="h-5 w-5 text-premium-emerald"></i>
              <span>Section 13(2) Notice Draftsman</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Financial Institution Name</label>
                <input type="text" id="draft-bank" value="UNION BANK OF INDIA" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
              </div>
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Primary Borrower Name</label>
                <input type="text" id="draft-borrower" value="Rajesh Sharma" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Total Outstanding (Rs.)</label>
                <input type="text" id="draft-dues" value="4,12,45,000" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
              </div>
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Notice Release Date</label>
                <input type="text" id="draft-date" value="15 June 2026" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Collateral Asset Address</label>
              <textarea id="draft-address" rows="3" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">A-Wing, 14th Floor, Sea View Heights, Worli, Mumbai</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-xs font-semibold text-slate-600 space-y-1">
              <div class="flex items-center space-x-1.5 text-amber-500 font-extrabold">
                <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                <span>Statutory Compliance Notice</span>
              </div>
              <p class="leading-relaxed">This wizard compiles drafts in standard accordance with Sec 13(2) of the Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest Act, 2002.</p>
            </div>
          </div>

          <!-- Output Canvas Sheet -->
          <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden flex flex-col min-h-[500px]">
            <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
              <h4 class="text-xs font-extrabold uppercase tracking-wider">Compiled Document Canvas</h4>
              
              <div class="flex items-center space-x-2">
                <!-- Language togglers -->
                <div class="flex bg-white/10 p-0.5 rounded-lg border border-white/10">
                  <button id="lang-en" onclick="toggleNoticeLang('en')" class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-white text-slate-900 shadow">English</button>
                  <button id="lang-mr" onclick="toggleNoticeLang('mr')" class="px-2.5 py-1 rounded-md text-[10px] font-bold text-slate-400 hover:text-white">मराठी</button>
                </div>
                <button onclick="openNoticePreviewModal()" class="p-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-lg text-white flex items-center space-x-1" title="Compile & Preview Notice">
                  <i data-lucide="eye" class="h-4 w-4"></i>
                  <span class="text-[10px] font-extrabold uppercase tracking-wider hidden sm:inline">Compile & Preview</span>
                </button>
              </div>
            </div>

            <div id="notice-canvas-sheet" class="p-8 bg-slate-50 flex-grow font-serif text-slate-800 leading-relaxed text-sm shadow-inner whitespace-pre-line">
              <!-- Rendered dynamically -->
            </div>
          </div>
        </div>

      </div>
    </div>
    
    </div> <!-- End overview tab -->

    <!-- Public Profile Editor Tab -->
    <div id="tab-profile" class="hidden space-y-8 mt-10">
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8 max-w-3xl mx-auto">
        <div class="space-y-1 mb-8">
          <h3 class="text-xl font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="user" class="h-6 w-6 text-premium-emerald"></i>
            <span>Public Profile Setup</span>
          </h3>
          <p class="text-sm font-semibold text-slate-500">Update how your credentials and specialities appear to clients in the Certified Advocates Directory.</p>
        </div>

        <form class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Consultation Fee (₹)</label>
              <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800" placeholder="e.g. 5000">
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Years of Experience</label>
              <input type="number" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800" placeholder="15">
            </div>
          </div>
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Professional Biography</label>
            <textarea rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800" placeholder="Briefly describe your expertise..."></textarea>
          </div>
          <button type="button" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-sm font-extrabold shadow-md transition-all">Save Profile Changes</button>
        </form>
      </div>
    </div>

    <!-- Document Vault Tab -->
    <div id="tab-vault" class="hidden space-y-8 mt-10">
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="folder-lock" class="h-6 w-6 text-indigo-500"></i>
              <span>Secure Document Vault</span>
            </h3>
            <p class="text-sm font-semibold text-slate-500">Upload and share drafted notices or legal opinions directly with clients.</p>
          </div>
          <button class="px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-bold transition-all flex items-center space-x-2">
            <i data-lucide="upload" class="h-4 w-4"></i>
            <span>Upload Document</span>
          </button>
        </div>

        <div class="text-center py-12 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl">
          <i data-lucide="file-x" class="h-10 w-10 text-slate-300 mx-auto mb-3"></i>
          <p class="text-sm font-bold text-slate-500">Your vault is empty.</p>
        </div>
      </div>
    </div>

  <?php endif; ?>

</div>

<script>
  function switchLawyerTab(tab) {
    const tabs = ['overview', 'profile', 'vault'];
    tabs.forEach(t => {
      const btn = document.getElementById('btn-' + t);
      const content = document.getElementById('tab-' + t);
      if (!btn || !content) return;

      if (t === tab) {
        btn.className = "shrink-0 px-5 sm:px-6 py-3 text-sm font-black border-b-2 border-premium-emerald text-premium-emerald transition-all whitespace-nowrap";
        content.classList.remove('hidden');
        content.classList.add('block');
      } else {
        btn.className = "shrink-0 px-5 sm:px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all whitespace-nowrap";
        content.classList.add('hidden');
        content.classList.remove('block');
      }
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab && ['overview', 'profile', 'vault'].includes(tab)) {
      switchLawyerTab(tab);
    }
  });


  let noticeLang = 'en';

  function toggleNoticeLang(lang) {
    noticeLang = lang;
    const btnEn = document.getElementById('lang-en');
    const btnMr = document.getElementById('lang-mr');

    if (lang === 'en') {
      btnEn.className = "px-2.5 py-1 rounded-md text-[10px] font-bold bg-white text-slate-900 shadow";
      btnMr.className = "px-2.5 py-1 rounded-md text-[10px] font-bold text-slate-400 hover:text-white";
    } else {
      btnMr.className = "px-2.5 py-1 rounded-md text-[10px] font-bold bg-white text-slate-900 shadow";
      btnEn.className = "px-2.5 py-1 rounded-md text-[10px] font-bold text-slate-400 hover:text-white";
    }
    compileDraft();
  }

  function compileDraft() {
    const bank = document.getElementById('draft-bank') ? document.getElementById('draft-bank').value : '';
    if(!bank) return; // Guard clause if not loaded

    const borrower = document.getElementById('draft-borrower').value;
    const dues = document.getElementById('draft-dues').value;
    const date = document.getElementById('draft-date').value;
    const address = document.getElementById('draft-address').value;
    
    const canvas = document.getElementById('notice-canvas-sheet');

    if (noticeLang === 'en') {
      canvas.textContent = `DEMAND NOTICE UNDER SECTION 13(2) OF THE SARFAESI ACT, 2002

Date: ${date}

To,
Mr./Mrs. ${borrower} (Borrower/Guarantor)

Subject: Notice under Section 13(2) of Securitisation and Reconstruction of Financial Assets and Enforcement of Security Interest Act, 2002 for recovery of outstanding dues.

Dear Sir/Madam,

You have availed credit facilities from ${bank}. Due to default in servicing interest/installment accounts, your liabilities were classified as Non-Performing Assets (NPA) in bank statements.

We hereby call upon you to discharge in full your liabilities of ₹ ${dues} (Rupees ${numberToWords(dues.replace(/,/g, ''))} Only) as of ${date} with further interest within 60 days.

Failing which, the Secured Creditor shall exercise enforcement rights over the mortgaged properties described below:
Description of Mortgaged Collateral:
${address}

For ${bank},
Authorized Officer`;
    } else {
      canvas.textContent = `सरफेसी कायदा, २००२ च्या कलम १३(२) अंतर्गत मागणी नोटीस

दिनांक: ${date}

प्रति,
श्री./श्रीमती ${borrower} (कर्जदार/हमीदार)

विषय: थकीत कर्जाच्या वसुलीसाठी कलम १३(२) अन्वये नोटीस.

महोदय/महोदया,

आपण ${bank} कडून कर्ज सुविधा घेतली होती. व्याज/हप्त्यांच्या परतफेडीमध्ये सतत कसूर केल्यामुळे, आपले कर्ज खाते बँकेच्या नियमांनुसार थकीत मालमत्ता (NPA) म्हणून वर्गीकृत करण्यात आले आहे.

या नोटीसीद्वारे आम्ही आपणास आवाहन करतो की, नोटीस मिळाल्यापासून ६० दिवसांच्या आत व्याज आणि अतिरिक्त शुल्कासह एकूण थकबाकी रु. ${dues}/- पूर्णपणे भरून कर्जमुक्त व्हावे.

असे न केल्यास, बँक खालील सुरक्षा तारण मालमत्तेचा ताबा घेण्यासाठी कायदेशीर कारवाई सुरू करेल:
तारण मालमत्तेचे वर्णन:
${address}

करिता ${bank},
प्राधिकृत अधिकारी`;
    }
  }

  function numberToWords(num) {
    if (isNaN(num)) return "";
    const n = parseInt(num);
    if (n >= 40000000) return "Four Crore Fifty Lakhs";
    if (n >= 10000000) return "One Crore Twenty Lakhs";
    return "Forty-One Lakhs Two Thousand";
  }

  function openNoticePreviewModal() {
    compileDraft();
    const canvasContent = document.getElementById('notice-canvas-sheet').textContent;
    document.getElementById('modal-notice-content').textContent = canvasContent;
    document.getElementById('notice-preview-modal').classList.remove('hidden');
    document.body.classList.add('modal-open');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeNoticePreviewModal() {
    document.getElementById('notice-preview-modal').classList.add('hidden');
    document.body.classList.remove('modal-open');
  }

  function copyNoticeToClipboard() {
    const content = document.getElementById('modal-notice-content').textContent;
    navigator.clipboard.writeText(content).then(() => {
      const btnText = document.getElementById('copy-btn-text');
      btnText.textContent = "Copied!";
      setTimeout(() => { btnText.textContent = "Copy Text"; }, 2000);
    });
  }

  function triggerPrintFromModal() {
    const content = document.getElementById('modal-notice-content').textContent;
    const win = window.open('', '_blank');
    win.document.write(`
      <html>
        <head>
          <title>Print SARFAESI Statutory Notice</title>
          <style>
            body { font-family: Georgia, serif; padding: 50px; line-height: 1.6; white-space: pre-wrap; font-size: 14px; color: #1e293b; }
            .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 30px; }
            .header h1 { font-size: 20px; margin: 0; font-weight: 900; letter-spacing: 1px; }
            .header p { font-size: 10px; margin: 5px 0 0 0; font-weight: bold; letter-spacing: 2px; color: #64748b; font-family: sans-serif; }
          </style>
        </head>
        <body onload="window.print();window.close();">
          <div class="header">
            <h1>STATUTORY LEGAL NOTICE</h1>
            <p>ISSUED UNDER SECTION 13(2) OF THE SARFAESI ACT, 2002</p>
          </div>
          ${content}
        </body>
      </html>
    `);
    win.document.close();
  }

  // Auto compile on load
  document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById('draft-bank')) compileDraft();
  });
</script>

<!-- Notice Preview Modal -->
<div id="notice-preview-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeNoticePreviewModal()"></div>
  <div class="relative bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4 text-white flex justify-between items-center shrink-0">
      <div>
        <h3 class="text-sm font-black uppercase tracking-wider">SARFAESI Section 13(2) Notice Draftsman</h3>
      </div>
      <button onclick="closeNoticePreviewModal()" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-full transition-colors">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <!-- A4 Letterhead Preview Container -->
    <div class="p-4 sm:p-6 bg-slate-100 overflow-y-auto max-h-[60vh] sm:max-h-[400px]">
      <div id="print-letterhead" class="bg-white p-8 shadow-md border border-slate-200 mx-auto max-w-[595px] min-h-[500px] font-serif text-slate-800 text-[11px] leading-relaxed whitespace-pre-line relative">
        <!-- Letterhead Banner -->
        <div class="border-b-2 border-slate-900 pb-4 mb-6 text-center">
          <h1 class="text-base font-black tracking-tight uppercase text-slate-900">STATUTORY LEGAL NOTICE</h1>
          <p class="text-[8px] uppercase tracking-widest text-slate-500 font-sans font-extrabold mt-1">ISSUED UNDER SECTION 13(2) OF THE SARFAESI ACT, 2002</p>
        </div>
        <!-- Dynamic Notice Content -->
        <div id="modal-notice-content"></div>
      </div>
    </div>

    <!-- Modal Actions -->
    <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 shrink-0">
      <span class="text-[10px] text-slate-400 font-bold uppercase flex items-center space-x-1">
        <i data-lucide="shield-check" class="h-4 w-4 text-emerald-500"></i>
        <span>Verified SARFAESI Act compliant draft</span>
      </span>
      <div class="flex space-x-3 w-full sm:w-auto justify-end">
        <button onclick="copyNoticeToClipboard()" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center space-x-1.5 touch-target">
          <i data-lucide="copy" class="h-4 w-4"></i>
          <span id="copy-btn-text">Copy Text</span>
        </button>
        <button onclick="triggerPrintFromModal()" class="px-5 py-2 bg-premium-emerald hover:bg-premium-emeraldHover text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 touch-target">
          <i data-lucide="printer" class="h-4 w-4"></i>
          <span>Print / Save PDF</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
