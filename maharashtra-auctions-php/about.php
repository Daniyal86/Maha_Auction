<?php
// about.php
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-16">

  <!-- Hero Section -->
  <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 rounded-3xl overflow-hidden p-10 md:p-16 text-white shadow-2xl">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-10"></div>
    <!-- Glow -->
    <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-teal-500/10 blur-3xl"></div>

    <div class="relative flex flex-col md:flex-row items-center gap-10">
      <div class="flex-1 space-y-5">
        <div class="inline-flex items-center space-x-2 bg-white/10 border border-white/20 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-emerald-300">
          <i data-lucide="landmark" class="h-3.5 w-3.5"></i>
          <span>Maharashtra's Statutory Auction Portal</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-tight">
          About <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">MahaAuctions</span>
        </h1>
        <p class="text-slate-300 text-sm md:text-base font-medium leading-relaxed max-w-xl">
          Maharashtra's first dedicated portal for SARFAESI & DRT statutory auctions, heavy-deposit lease opportunities, and NPA asset liquidations — built for buyers, banks, and legal professionals.
        </p>
        <div class="flex flex-wrap gap-3 pt-2">
          <a href="search.php" class="inline-flex items-center space-x-2 bg-premium-emerald hover:bg-premium-emeraldHover text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-900/40 transition-all hover:-translate-y-0.5 touch-target">
            <i data-lucide="search" class="h-4 w-4"></i>
            <span>Browse Auctions</span>
          </a>
          <a href="advisory.php" class="inline-flex items-center space-x-2 bg-white/10 border border-white/20 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 touch-target">
            <i data-lucide="shield-check" class="h-4 w-4"></i>
            <span>Legal Advisory</span>
          </a>
        </div>
      </div>

      <!-- Stats card -->
      <div class="bg-white/5 border border-white/10 rounded-2xl p-6 grid grid-cols-2 gap-5 shrink-0 w-full md:w-72 backdrop-blur-sm">
        <div class="text-center space-y-1">
          <div class="text-3xl font-black text-emerald-400">2,400+</div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Properties Listed</div>
        </div>
        <div class="text-center space-y-1">
          <div class="text-3xl font-black text-teal-400">₹4,800 Cr</div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Asset Value</div>
        </div>
        <div class="text-center space-y-1">
          <div class="text-3xl font-black text-amber-400">36</div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Districts Covered</div>
        </div>
        <div class="text-center space-y-1">
          <div class="text-3xl font-black text-rose-400">18+</div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Bank Partners</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mission Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <div class="space-y-5">
      <div class="inline-flex items-center space-x-2 text-premium-emerald text-xs font-bold uppercase tracking-widest">
        <i data-lucide="target" class="h-4 w-4"></i>
        <span>Our Mission</span>
      </div>
      <h2 class="text-3xl font-black text-slate-800 leading-tight">Democratizing Access to Statutory Auctions</h2>
      <p class="text-slate-500 text-sm font-medium leading-relaxed">
        Statutory auctions under SARFAESI Act, 2002 and DRT (Debt Recovery Tribunal) have historically been opaque and inaccessible to ordinary buyers. MahaAuctions was built to change that — by aggregating all published auction notices, legal documentation, and property data into one transparent, searchable portal.
      </p>
      <p class="text-slate-500 text-sm font-medium leading-relaxed">
        Whether you're a first-time buyer looking for below-market residential units, an investor tracking NPA commercial assets, or a lawyer managing client bidding strategies — MahaAuctions provides the tools, data, and professionals you need.
      </p>
    </div>
    <div class="grid grid-cols-1 gap-4">
      <?php
      $pillars = [
        ['icon' => 'shield', 'color' => 'emerald', 'title' => 'Legally Verified Listings', 'desc' => 'Every property is sourced directly from official bank auction notices under SARFAESI/DRT mandates.'],
        ['icon' => 'eye', 'color' => 'blue', 'title' => 'Full Transparency', 'desc' => 'Reserve prices, encumbrances, and due diligence documents — all publicly accessible.'],
        ['icon' => 'users', 'color' => 'amber', 'title' => 'Expert Network', 'desc' => 'Connect directly with Bar Council-verified advocates and licensed property agents.'],
        ['icon' => 'zap', 'color' => 'rose', 'title' => 'Real-Time Alerts', 'desc' => 'Get notified the moment new auctions matching your criteria are published by any bank.'],
      ];
      foreach ($pillars as $p): ?>
        <div class="flex items-start space-x-4 bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
          <div class="h-10 w-10 rounded-xl bg-<?= $p['color'] ?>-50 border border-<?= $p['color'] ?>-100 flex items-center justify-center shrink-0">
            <i data-lucide="<?= $p['icon'] ?>" class="h-5 w-5 text-<?= $p['color'] ?>-600"></i>
          </div>
          <div>
            <h4 class="text-sm font-black text-slate-800 mb-0.5"><?= $p['title'] ?></h4>
            <p class="text-xs text-slate-500 font-medium leading-relaxed"><?= $p['desc'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- How It Works -->
  <div class="space-y-8">
    <div class="text-center space-y-2">
      <div class="inline-flex items-center space-x-2 text-premium-emerald text-xs font-bold uppercase tracking-widest">
        <i data-lucide="workflow" class="h-4 w-4"></i>
        <span>How It Works</span>
      </div>
      <h2 class="text-3xl font-black text-slate-800">From Listing to Ownership</h2>
      <p class="text-slate-500 text-sm font-medium max-w-xl mx-auto">A streamlined, end-to-end process for statutory property acquisition.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php
      $steps = [
        ['step' => '01', 'icon' => 'search', 'color' => 'emerald', 'title' => 'Discover & Filter', 'desc' => 'Search all 36 Maharashtra districts by bank, reserve price, property type, and auction date.'],
        ['step' => '02', 'icon' => 'file-text', 'color' => 'blue', 'title' => 'Review Documents', 'desc' => 'Access possession notices, title search reports, and encumbrance certificates directly.'],
        ['step' => '03', 'icon' => 'scale', 'color' => 'amber', 'title' => 'Consult an Advocate', 'desc' => 'Book a consultation with a SARFAESI-specialist advocate before placing your EMD.'],
        ['step' => '04', 'icon' => 'gavel', 'color' => 'rose', 'title' => 'Bid & Acquire', 'desc' => 'Submit your EMD, participate in the bank-conducted auction, and receive the sale certificate.'],
      ];
      foreach ($steps as $i => $s): ?>
        <div class="relative bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 space-y-4 overflow-hidden group">
          <div class="absolute -top-3 -right-3 text-7xl font-black text-slate-50 group-hover:text-emerald-50/60 transition-colors select-none"><?= $s['step'] ?></div>
          <div class="relative h-12 w-12 rounded-2xl bg-<?= $s['color'] ?>-50 border border-<?= $s['color'] ?>-100 flex items-center justify-center">
            <i data-lucide="<?= $s['icon'] ?>" class="h-6 w-6 text-<?= $s['color'] ?>-600"></i>
          </div>
          <div class="relative space-y-1.5">
            <h3 class="text-sm font-black text-slate-800"><?= $s['title'] ?></h3>
            <p class="text-xs text-slate-500 font-medium leading-relaxed"><?= $s['desc'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Bank Partners -->
  <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-8 md:p-10 space-y-6">
    <div class="text-center space-y-2">
      <div class="inline-flex items-center space-x-2 text-premium-emerald text-xs font-bold uppercase tracking-widest">
        <i data-lucide="building-2" class="h-4 w-4"></i>
        <span>Bank & Institution Partners</span>
      </div>
      <h2 class="text-2xl font-black text-slate-800">Trusted by Maharashtra's Leading Lenders</h2>
      <p class="text-slate-500 text-sm font-medium">We source auction notices from these regulated financial institutions.</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
      <?php
      $banks = ['SBI', 'Bank of Maharashtra', 'Union Bank', 'Bank of Baroda', 'Canara Bank', 'HDFC Bank', 'ICICI Bank', 'Axis Bank', 'Punjab National Bank', 'Indian Bank', 'UCO Bank', 'IDBI Bank'];
      foreach ($banks as $bank): ?>
        <div class="bg-white border border-slate-100 rounded-2xl px-3 py-4 flex items-center justify-center text-center shadow-sm hover:shadow hover:border-emerald-100 transition-all">
          <span class="text-xs font-black text-slate-600 leading-tight"><?= $bank ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Legal Framework -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1 space-y-3">
      <div class="inline-flex items-center space-x-2 text-premium-emerald text-xs font-bold uppercase tracking-widest">
        <i data-lucide="book-open" class="h-4 w-4"></i>
        <span>Legal Framework</span>
      </div>
      <h2 class="text-2xl font-black text-slate-800 leading-tight">Acts & Regulations Governing Our Listings</h2>
      <p class="text-slate-500 text-sm font-medium leading-relaxed">All auctions listed on MahaAuctions are conducted under these statutory frameworks.</p>
    </div>
    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
      <?php
      $laws = [
        ['title' => 'SARFAESI Act, 2002', 'icon' => 'scale', 'color' => 'emerald', 'desc' => 'Securitisation and Reconstruction of Financial Assets & Enforcement of Security Interest Act — the primary mechanism enabling banks to auction secured assets without court intervention.'],
        ['title' => 'DRT Act, 1993', 'icon' => 'gavel', 'color' => 'blue', 'desc' => 'Recovery of Debts Due to Banks & Financial Institutions Act — governs the Debt Recovery Tribunals (DRTs) that adjudicate NPA recovery proceedings above ₹20 lakhs.'],
        ['title' => 'Transfer of Property Act', 'icon' => 'file-text', 'color' => 'amber', 'desc' => 'Governs the legal transfer of property title via Sale Certificate upon successful auction bid completion and full payment settlement.'],
        ['title' => 'IBC, 2016', 'icon' => 'landmark', 'color' => 'rose', 'desc' => 'Insolvency and Bankruptcy Code — applies to corporate NPA assets auctioned via NCLT-appointed Resolution Professionals or Liquidators.'],
      ];
      foreach ($laws as $law): ?>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3 hover:shadow-md transition-shadow">
          <div class="flex items-center space-x-3">
            <div class="h-9 w-9 rounded-xl bg-<?= $law['color'] ?>-50 border border-<?= $law['color'] ?>-100 flex items-center justify-center shrink-0">
              <i data-lucide="<?= $law['icon'] ?>" class="h-4 w-4 text-<?= $law['color'] ?>-600"></i>
            </div>
            <h4 class="text-sm font-black text-slate-800"><?= $law['title'] ?></h4>
          </div>
          <p class="text-xs text-slate-500 font-medium leading-relaxed"><?= $law['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- CTA -->
  <div class="bg-gradient-to-r from-premium-emerald to-teal-600 rounded-3xl p-8 md:p-12 text-white text-center space-y-5 shadow-xl shadow-emerald-200/50 relative overflow-hidden">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:2rem_2rem]"></div>
    <div class="relative">
      <h2 class="text-3xl font-black tracking-tight">Ready to Find Your Next Property?</h2>
      <p class="text-emerald-100 text-sm font-medium mt-2 max-w-lg mx-auto">Browse thousands of verified statutory auction listings across all 36 districts of Maharashtra — with complete legal transparency.</p>
      <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
        <a href="search.php" class="inline-flex items-center space-x-2 bg-white text-emerald-700 hover:bg-emerald-50 px-6 py-3 rounded-xl text-sm font-black shadow-lg transition-all hover:-translate-y-0.5 touch-target">
          <i data-lucide="search" class="h-4 w-4"></i>
          <span>Search Auctions</span>
        </a>
        <a href="advisory.php" class="inline-flex items-center space-x-2 bg-white/15 border border-white/30 hover:bg-white/25 text-white px-6 py-3 rounded-xl text-sm font-black transition-all hover:-translate-y-0.5 touch-target">
          <i data-lucide="user-check" class="h-4 w-4"></i>
          <span>Consult an Advocate</span>
        </a>
      </div>
    </div>
  </div>

</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
