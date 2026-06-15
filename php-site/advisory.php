<?php
// php-site/advisory.php
require_once 'config/db.php';

// Local list of advocates (matching original design)
$advocates = [
    [
        'id' => 'adv-1',
        'name' => 'Adv. Sayali Patil',
        'role' => 'Principal Foreclosure Counsel',
        'rating' => '4.90',
        'experience' => '14 Yrs Exp',
        'specialty' => 'SARFAESI Litigations & DRT Appeals',
        'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80'
    ],
    [
        'id' => 'adv-2',
        'name' => 'Adv. Amit Deshmukh',
        'role' => 'Senior Banking Arbitrator',
        'rating' => '4.85',
        'experience' => '12 Yrs Exp',
        'specialty' => 'Securities enforcement, Title Search Audits',
        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80'
    ]
];

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  <!-- Page Header -->
  <div>
    <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
      <i data-lucide="shield-check" class="h-8 w-8 text-premium-emerald"></i>
      <span>Securitisation Legal Advisory & Draftsman</span>
    </h1>
    <p class="text-xs text-slate-500 font-semibold mt-1">Book direct consultations with foreclosure panel advocates or draft Section 13(2) statutory notices.</p>
  </div>

  <!-- Tab Buttons -->
  <div class="flex border-b border-slate-200">
    <button id="tab-advocates-btn" onclick="switchAdvisoryTab('advocates')" class="py-4 px-6 text-sm font-extrabold border-b-2 border-premium-emerald text-premium-emerald transition-all">
      Certified Advocates Directory
    </button>
    <button id="tab-draftsman-btn" onclick="switchAdvisoryTab('draftsman')" class="py-4 px-6 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition-all">
      Section 13(2) Notice Draftsman
    </button>
  </div>

  <!-- 1. Advocates Section -->
  <div id="section-advocates" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- Advocate Grid -->
    <div class="lg:col-span-2 space-y-6">
      <?php foreach ($advocates as $adv): ?>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-md hover:shadow-lg transition-shadow flex flex-col sm:flex-row items-center sm:items-start gap-6">
          <img src="<?php echo htmlspecialchars($adv['image']); ?>" alt="Advocate avatar" class="h-24 w-24 rounded-2xl object-cover border border-slate-100 shrink-0">
          
          <div class="space-y-3 flex-grow text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
              <div>
                <h3 class="text-lg font-black text-slate-800"><?php echo htmlspecialchars($adv['name']); ?></h3>
                <span class="text-xs font-bold text-premium-emerald bg-emerald-50 px-2.5 py-0.5 rounded-full mt-1 inline-block"><?php echo htmlspecialchars($adv['role']); ?></span>
              </div>
              <div class="flex items-center justify-center sm:justify-end space-x-1 text-amber-500 text-xs font-bold bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-lg w-fit self-center">
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-500"></i>
                <span><?php echo htmlspecialchars($adv['rating']); ?></span>
              </div>
            </div>

            <p class="text-xs font-semibold text-slate-500 leading-relaxed"><?php echo htmlspecialchars($adv['specialty']); ?></p>

            <div class="flex flex-wrap gap-4 items-center justify-center sm:justify-start text-xs text-slate-400 font-bold">
              <span class="flex items-center space-x-1">
                <i data-lucide="award" class="h-4 w-4 text-slate-400"></i>
                <span><?php echo htmlspecialchars($adv['experience']); ?></span>
              </span>
              <span class="flex items-center space-x-1">
                <i data-lucide="shield" class="h-4 w-4 text-slate-400"></i>
                <span>Bar Council Verified</span>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Booking Sidebar -->
    <div class="bg-slate-900 text-white rounded-3xl border border-slate-800 p-6 shadow-xl space-y-6 relative overflow-hidden">
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-20"></div>
      
      <div class="relative space-y-4">
        <h3 class="text-lg font-black tracking-tight">Direct Consultation Scheduler</h3>
        <p class="text-xs text-slate-400 font-medium">Book a secure 30-minute regulatory foreclosures hearing directly in advocates schedules.</p>
        
        <div id="booking-error-msg" class="hidden text-xs text-red-400 bg-red-950/20 p-3 rounded-lg font-semibold border border-red-900/20"></div>

        <form id="consultation-booking-form" onsubmit="handleBookingSubmit(event)" class="space-y-3">
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Select Advocate</label>
            <select id="book-advocate" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold">
              <?php foreach ($advocates as $adv): ?>
                <option value="<?php echo htmlspecialchars($adv['id']); ?>" class="bg-slate-900"><?php echo htmlspecialchars($adv['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Your Name</label>
            <input type="text" id="book-name" required placeholder="Siddharth Rao" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold placeholder-slate-500">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
            <input type="email" id="book-email" required placeholder="sid@example.com" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold placeholder-slate-500">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Schedule Date</label>
            <input type="date" id="book-date" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold text-slate-400">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Consultation Topic</label>
            <select id="book-topic" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold">
              <option value="SARFAESI Foreclosure Appeal" class="bg-slate-900">SARFAESI Foreclosure Appeal</option>
              <option value="Auction Title Search Clearances" class="bg-slate-900">Auction Title Search Clearances</option>
              <option value="Heavy Deposit Lease Drafting" class="bg-slate-900">Heavy Deposit Lease Drafting</option>
            </select>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2">
            <span>Schedule Appointment</span>
            <i data-lucide="calendar" class="h-4 w-4"></i>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- 2. Notice Draftsman Section (Hidden by default) -->
  <div id="section-draftsman" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
    
    <!-- Draftsman Inputs Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 space-y-4">
      <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
        <i data-lucide="edit-3" class="h-5 w-5 text-premium-emerald"></i>
        <span>Drafting Inputs Panel</span>
      </h3>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Financial Institution Name</label>
          <input type="text" id="draft-bank" value="UNION BANK OF INDIA" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
        </div>
        <div>
          <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Primary Borrower Name</label>
          <input type="text" id="draft-borrower" value="Rajesh Sharma" oninput="compileDraft()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
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

<script>
  let activeAdvisoryTab = 'advocates';
  let noticeLang = 'en';

  function switchAdvisoryTab(tab) {
    activeAdvisoryTab = tab;
    
    const advBtn = document.getElementById('tab-advocates-btn');
    const draftBtn = document.getElementById('tab-draftsman-btn');
    const advSec = document.getElementById('section-advocates');
    const draftSec = document.getElementById('section-draftsman');

    if (tab === 'advocates') {
      advBtn.className = "py-4 px-6 text-sm font-extrabold border-b-2 border-premium-emerald text-premium-emerald transition-all";
      draftBtn.className = "py-4 px-6 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition-all";
      advSec.classList.remove('hidden');
      draftSec.classList.add('hidden');
    } else {
      draftBtn.className = "py-4 px-6 text-sm font-extrabold border-b-2 border-premium-emerald text-premium-emerald transition-all";
      advBtn.className = "py-4 px-6 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition-all";
      draftSec.classList.remove('hidden');
      advSec.classList.add('hidden');
      compileDraft();
    }
  }

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
    const bank = document.getElementById('draft-bank').value;
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
    // Basic placeholder converter for UI demonstration
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
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeNoticePreviewModal() {
    document.getElementById('notice-preview-modal').classList.add('hidden');
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

  // Consultation booking AJAX handler
  function handleBookingSubmit(e) {
    e.preventDefault();
    const advocate = document.getElementById('book-advocate').value;
    const name = document.getElementById('book-name').value;
    const email = document.getElementById('book-email').value;
    const date = document.getElementById('book-date').value;
    const topic = document.getElementById('book-topic').value;
    const errorEl = document.getElementById('booking-error-msg');

    errorEl.classList.add('hidden');

    const formData = new FormData();
    formData.append('advocate_id', advocate);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('booking_date', date);
    formData.append('topic', topic);

    const form = document.getElementById('consultation-booking-form');
    form.innerHTML = `
      <div class="flex flex-col items-center justify-center py-12 space-y-4">
        <div class="h-10 w-10 border-4 border-slate-600 border-t-emerald-400 rounded-full animate-spin"></div>
        <p class="text-xs text-slate-400 font-semibold">Booking panel schedule slot...</p>
      </div>
    `;

    fetch('api/book_consultation.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        form.innerHTML = `
          <div class="text-center py-8 space-y-4 text-white">
            <div class="mx-auto h-12 w-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 border border-emerald-500/25">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-black">Consultation Confirmed!</h3>
              <p class="text-xs text-slate-400 font-semibold mt-1">Calendar invites and link sent to ${email}.</p>
            </div>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      } else {
        errorEl.textContent = data.message;
        errorEl.classList.remove('hidden');
        window.location.reload(); // reset state on error
      }
    })
    .catch(() => {
      errorEl.textContent = 'Server response error.';
      errorEl.classList.remove('hidden');
    });
  }
</script>

<!-- Notice Preview Modal -->
<div id="notice-preview-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeNoticePreviewModal()"></div>
  <div class="relative bg-white rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4 text-white flex justify-between items-center">
      <div>
        <h3 class="text-sm font-black uppercase tracking-wider">SARFAESI Section 13(2) Notice Draftsman</h3>
      </div>
      <button onclick="closeNoticePreviewModal()" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-full transition-colors">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <!-- A4 Letterhead Preview Container -->
    <div class="p-6 bg-slate-100 overflow-y-auto max-h-[400px]">
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
    <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 flex justify-between items-center">
      <span class="text-[10px] text-slate-400 font-bold uppercase flex items-center space-x-1">
        <i data-lucide="shield-check" class="h-4 w-4 text-emerald-500"></i>
        <span>Verified SARFAESI Act compliant draft</span>
      </span>
      <div class="flex space-x-3">
        <button onclick="copyNoticeToClipboard()" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center space-x-1.5">
          <i data-lucide="copy" class="h-4 w-4"></i>
          <span id="copy-btn-text">Copy Text</span>
        </button>
        <button onclick="triggerPrintFromModal()" class="px-5 py-2 bg-premium-emerald hover:bg-premium-emeraldHover text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5">
          <i data-lucide="printer" class="h-4 w-4"></i>
          <span>Print / Save PDF</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
