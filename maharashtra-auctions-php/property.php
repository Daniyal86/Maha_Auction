<?php
// property.php
require_once 'config/db.php';

$property_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($property_id)) {
    header('Location: index.php');
    exit;
}

// Handle subscription activation
if (isset($_POST['activate_trial']) && isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $ends_at = date('Y-m-d H:i:s', strtotime('+7 days'));
    try {
        $stmt = $pdo->prepare("UPDATE users SET subscription_ends_at = ? WHERE id = ?");
        $stmt->execute([$ends_at, $user_id]);
        $_SESSION['user']['subscription_ends_at'] = $ends_at;
        header("Location: property.php?id=" . urlencode($property_id));
        exit;
    } catch (PDOException $e) {
        // Handled silently
    }
}

// Fetch property, city, and agent details in a single query
$stmt = $pdo->prepare("
    SELECT p.*, c.name as city_name, 
           a.id as agent_uid, a.name as agent_name, a.phone as agent_phone, a.image as agent_image, a.specialty as agent_specialty
    FROM properties p 
    JOIN cities c ON p.city_id = c.id 
    LEFT JOIN agents a ON p.agent_id = a.id 
    WHERE p.id = ?
");
$stmt->execute([$property_id]);
$prop = $stmt->fetch();

if (!$prop) {
    header('Location: index.php');
    exit;
}

// Check subscription status
$is_subscribed = false;
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'seller') {
        $is_subscribed = true;
    } elseif (!empty($_SESSION['user']['subscription_ends_at'])) {
        $is_subscribed = (strtotime($_SESSION['user']['subscription_ends_at']) > time());
    }
}

// Default values for ready reckoner calculations
$reserve_price_val = $prop['numeric_price'] ?: 100000;
$gov_valuation_val = $prop['numeric_gov_valuation'] ?: 120000;

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
  <!-- Breadcrumb -->
  <div class="flex items-center space-x-2 text-xs font-bold text-slate-400">
    <a href="index.php" class="hover:text-premium-emerald transition-colors">Home</a>
    <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
    <a href="search.php" class="hover:text-premium-emerald transition-colors">Search</a>
    <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
    <span class="text-slate-600"><?php echo htmlspecialchars($prop['title']); ?></span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    <!-- Left Column: Details, Slider, and Newspaper Notices -->
    <div class="lg:col-span-2 space-y-8">
      
      <!-- Image & Core Specs -->
      <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-lg p-6 space-y-6">
        <div class="relative h-[320px] rounded-2xl overflow-hidden bg-slate-100">
          <img src="<?php echo htmlspecialchars($prop['image']); ?>" alt="Property Image" class="w-full h-full object-cover">
          <div class="absolute top-4 left-4 bg-slate-900/80 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            <?php echo htmlspecialchars($prop['type']); ?>
          </div>
          <div class="absolute top-4 right-4 bg-emerald-500 text-white text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">
            <?php echo htmlspecialchars($prop['category']); ?>
          </div>
        </div>

        <div class="space-y-3">
          <span class="inline-block bg-slate-100 border border-slate-200 text-slate-600 font-bold px-2.5 py-0.5 rounded text-xs uppercase tracking-wider">
            Registry Listing ID: <?php echo htmlspecialchars($prop['listing_id']); ?>
          </span>
          <h1 class="text-3xl font-black text-slate-800 leading-tight tracking-tight"><?php echo htmlspecialchars($prop['title']); ?></h1>
          <p class="text-sm font-bold text-slate-500 flex items-center space-x-1.5">
            <i data-lucide="map-pin" class="h-4 w-4 text-premium-emerald shrink-0"></i>
            <span><?php echo htmlspecialchars($prop['address']); ?></span>
          </p>
          
          <!-- Area and Square Foot (Always visible details) -->
          <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs font-bold text-slate-500">
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-150/40">
              <span class="block text-[9px] uppercase tracking-wider text-slate-400 mb-0.5">Approximate Area</span>
              <span class="text-sm font-black text-slate-800">
                <?php
                  $basic_area = "1,250 Sq Ft"; 
                  if (preg_match('/(\d+[,.]?\d*\s*(sq\s*ft|sqft|bhk|carpet|super\s*built))/i', $prop['title'] . ' ' . $prop['details'], $matches)) {
                      $basic_area = ucwords($matches[1]);
                  }
                  echo htmlspecialchars($basic_area);
                ?>
              </span>
            </div>
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-150/40">
              <span class="block text-[9px] uppercase tracking-wider text-slate-400 mb-0.5">Asset Classification</span>
              <span class="text-sm font-black text-slate-800"><?php echo htmlspecialchars($prop['type']); ?></span>
            </div>
          </div>
        </div>
      </div>

      <?php if ($is_subscribed): ?>
        <!-- Government Ready Reckoner Audit Slider -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-6">
          <div>
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="calculator" class="h-5 w-5 text-premium-emerald"></i>
              <span>Statutory Ready Reckoner Valuation Audit</span>
            </h3>
            <p class="text-xs text-slate-500 font-semibold mt-1">Adjust property valuation parameter to calculate comparative discount percentage.</p>
          </div>

          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 space-y-4">
            <!-- Slider control -->
            <div class="space-y-2">
              <div class="flex justify-between text-xs font-bold text-slate-500 uppercase">
                <span>MahaAuctions Price</span>
                <span id="slider-price-label"><?php echo htmlspecialchars($prop['reserve_price']); ?></span>
              </div>
              <input type="range" id="price-slider" min="<?php echo $reserve_price_val * 0.5; ?>" max="<?php echo $reserve_price_val * 1.5; ?>" value="<?php echo $reserve_price_val; ?>" step="100000" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-premium-emerald">
            </div>

            <!-- Comparison stats -->
            <div class="grid grid-cols-2 gap-4 pt-2 text-xs">
              <div class="bg-white border border-slate-200/50 p-4 rounded-xl shadow-sm text-center">
                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Valuation Discount</span>
                <span id="valuation-discount-pct" class="text-xl font-black text-premium-emerald">22% Below Ready Reckoner</span>
              </div>
              <div class="bg-white border border-slate-200/50 p-4 rounded-xl shadow-sm text-center">
                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Instant Saving Amount</span>
                <span id="valuation-savings-val" class="text-xl font-black text-slate-800">₹ 1.30 Cr</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Newspaper Statutory Notice Toggles -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-6">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
                <i data-lucide="file-text" class="h-5 w-5 text-premium-emerald"></i>
                <span>Official Newspaper Foreclosure Proclamation</span>
              </h3>
              <p class="text-xs text-slate-500 font-semibold mt-1">Review official notices released under Security Interest rules.</p>
            </div>
            
            <!-- Notice language tabs -->
            <div class="flex bg-slate-100 p-0.5 rounded-xl border border-slate-200">
              <button id="notice-en-btn" onclick="switchNoticeLanguage('en')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-900 shadow transition-all">English</button>
              <button id="notice-mr-btn" onclick="switchNoticeLanguage('mr')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-800 transition-all">मराठी</button>
            </div>
          </div>

          <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 font-serif text-slate-700 leading-relaxed text-sm whitespace-pre-line relative">
            <!-- Text canvases -->
            <div id="notice-en-text"><?php echo htmlspecialchars($prop['notice_english'] ?: 'English demand notice not available.'); ?></div>
            <div id="notice-mr-text" class="hidden"><?php echo htmlspecialchars($prop['notice_marathi'] ?: 'मराठी जाहीर नोटीस उपलब्ध नाही.'); ?></div>
          </div>
        </div>

        <!-- Asset specifications details -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-4">
          <h3 class="text-lg font-black text-slate-800">Detailed Asset Specifications</h3>
          <p class="text-sm font-medium text-slate-600 leading-relaxed"><?php echo htmlspecialchars($prop['details']); ?></p>
        </div>
      <?php else: ?>
        <!-- Beautiful Premium Glassmorphic Lockbox -->
        <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-slate-200/80 p-8 shadow-xl text-center space-y-6 relative overflow-hidden">
          <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl"></div>
          <div class="mx-auto h-20 w-20 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center justify-center text-premium-emerald shadow-inner">
            <i data-lucide="lock" class="h-9 w-9"></i>
          </div>
          <div class="space-y-3 max-w-lg mx-auto">
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Premium Forensic Auction Data Locked</h3>
            <p class="text-xs text-slate-500 font-semibold leading-relaxed">
              To maintain compliance and protect bidding data, Newspaper Foreclosure Notices, ready reckoner audits, EMD values, and physical inspector visits are restricted to subscribed bidders.
            </p>
          </div>
          
          <div class="bg-slate-50/50 border border-slate-200/60 rounded-2xl p-5 max-w-md mx-auto grid grid-cols-2 gap-4 text-left text-xs font-bold text-slate-600">
            <div class="flex items-center space-x-2">
              <i data-lucide="check" class="h-4 w-4 text-premium-emerald shrink-0"></i>
              <span>Official Proclamations</span>
            </div>
            <div class="flex items-center space-x-2">
              <i data-lucide="check" class="h-4 w-4 text-premium-emerald shrink-0"></i>
              <span>EMD Ledger Details</span>
            </div>
            <div class="flex items-center space-x-2">
              <i data-lucide="check" class="h-4 w-4 text-premium-emerald shrink-0"></i>
              <span>Ready Reckoner Audit</span>
            </div>
            <div class="flex items-center space-x-2">
              <i data-lucide="check" class="h-4 w-4 text-premium-emerald shrink-0"></i>
              <span>Guided Physical Tours</span>
            </div>
          </div>

          <div class="max-w-md mx-auto">
            <?php if (isset($_SESSION['user'])): ?>
              <form method="POST" action="property.php?id=<?php echo urlencode($prop['id']); ?>">
                <input type="hidden" name="activate_trial" value="1">
                <button type="submit" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-4 rounded-2xl text-sm font-extrabold shadow-md transition-all flex items-center justify-center space-x-2 active:scale-[0.98]">
                  <i data-lucide="zap" class="h-4 w-4"></i>
                  <span>Activate Free 7-Day Auction Pass</span>
                </button>
              </form>
            <?php else: ?>
              <button onclick="openAuthModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl text-sm font-extrabold shadow-md transition-all flex items-center justify-center space-x-2">
                <i data-lucide="user-check" class="h-4 w-4"></i>
                <span>Login to Start Free 7-Day Pass</span>
              </button>
            <?php endif; ?>
            <p class="text-[10px] text-slate-400 font-semibold mt-2.5">Free for first 7 days, cancel anytime. No credit card required during trial.</p>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- Right Column: Transactions & Inspection Scheduler -->
    <div class="space-y-6">
      
      <?php if ($is_subscribed): ?>
        <!-- Core Transaction Ledger -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-6">
          <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Transaction Ledger</h3>
          
          <div class="space-y-4 text-xs font-semibold text-slate-500">
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span>Reserve Price Valuation</span>
              <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['reserve_price']); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span>Earnest Money Deposit (EMD)</span>
              <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['emd']); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span>Foreclosing Institution</span>
              <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['bank']); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span>Primary Borrower Account</span>
              <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['borrower']); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span>Possession Status</span>
              <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['possession']); ?></span>
            </div>
            <?php if (!empty($prop['auction_date'])): ?>
              <div class="flex justify-between py-2 border-b border-slate-100">
                <span>Auction Scheduled Date</span>
                <span class="font-extrabold text-premium-emerald"><?php echo htmlspecialchars(date('d M Y - H:i A', strtotime($prop['auction_date']))); ?></span>
              </div>
            <?php endif; ?>
          </div>

          <button onclick="openTrialModal('Property Detail Alert')" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-sm font-bold shadow-md transition-all flex items-center justify-center space-x-1.5">
            <i data-lucide="bell" class="h-4 w-4"></i>
            <span>Add Foreclosure Tracking Alert</span>
          </button>
        </div>

        <!-- Stamp Duty & Cost Estimator Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-4">
          <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5">
            <i data-lucide="calculator" class="h-4.5 w-4.5 text-premium-emerald"></i>
            <span>Statutory Cost Estimator</span>
          </h3>
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Estimates based on Maharashtra registration standards (Stamp Duty: 6%, Registration: 1% capped).</p>
          
          <div class="space-y-3 pt-2 text-xs font-semibold text-slate-500">
            <div class="flex justify-between py-1.5 border-b border-slate-100">
              <span>Stamp Duty (6%)</span>
              <span id="est-stamp-duty" class="font-extrabold text-slate-800">₹ 0</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-100">
              <span>Registration Fee (1% cap)</span>
              <span id="est-reg-fee" class="font-extrabold text-slate-800">₹ 0</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-100">
              <span>Legal Advisory & Filings</span>
              <span class="font-extrabold text-slate-800">₹ 15,000</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100 bg-slate-50 p-2 rounded-xl font-bold">
              <span class="text-slate-800">Estimated Total Acquisition Cost</span>
              <span id="est-total-cost" class="font-black text-premium-emerald text-sm">₹ 0</span>
            </div>
          </div>
        </div>

        <!-- Schedule Site Visit Card -->
        <div class="bg-slate-900 text-white rounded-3xl border border-slate-850 p-6 shadow-xl space-y-6 relative overflow-hidden">
          <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-20"></div>
          
          <div id="scheduler-form-wrapper" class="relative space-y-4">
            <div class="flex items-center space-x-2">
              <i data-lucide="calendar" class="h-6 w-6 text-emerald-400"></i>
              <h3 class="text-base font-black tracking-tight">Schedule Physical Site Visit</h3>
            </div>
            <p class="text-xs text-slate-400 font-medium">Coordinate a free guided site viewing with the assigned certified inspector.</p>
            
            <div id="scheduler-error-msg" class="hidden text-xs text-red-400 bg-red-950/20 p-3 rounded-lg font-semibold border border-red-900/20"></div>

            <form onsubmit="handleSchedulerSubmit(event)" class="space-y-3">
              <input type="hidden" id="sched-property-id" value="<?php echo htmlspecialchars($prop['id']); ?>">
              <input type="hidden" id="sched-agent-id" value="<?php echo htmlspecialchars($prop['agent_uid'] ?: 'agt-1'); ?>">

              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Select Viewing Date</label>
                <input type="date" id="sched-date" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold text-slate-400">
              </div>

              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Select Time Slot</label>
                <select id="sched-time" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold">
                  <option value="10:00 AM - Morning Slot" class="bg-slate-900">10:00 AM - Morning Slot</option>
                  <option value="02:00 PM - Afternoon Slot" class="bg-slate-900">02:00 PM - Afternoon Slot</option>
                  <option value="05:00 PM - Evening Slot" class="bg-slate-900">05:00 PM - Evening Slot</option>
                </select>
              </div>

              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Visitor Mobile Number</label>
                <input type="text" id="sched-phone" required placeholder="9876543210" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold placeholder-slate-500">
              </div>

              <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-1.5">
                <span>Book Free Inspector Visit</span>
                <i data-lucide="arrow-right" class="h-4 w-4"></i>
              </button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <!-- Locked Mini Sidebar Box -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 text-center space-y-4">
          <div class="h-12 w-12 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 mx-auto">
            <i data-lucide="shield-alert" class="h-5 w-5"></i>
          </div>
          <div class="space-y-1">
            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Transaction Ledger Locked</h4>
            <p class="text-[10px] text-slate-400 font-semibold px-2">EMD amounts, bank foreclosure balances, and scheduler are hidden.</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-3 text-[10px] font-extrabold text-slate-400 flex items-center justify-center space-x-1">
            <i data-lucide="lock" class="h-3 w-3"></i>
            <span>Subscription Pass Required</span>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
  // Dynamic Ready Reckoner slider calculations
  const priceSlider = document.getElementById('price-slider');
  const priceLabel = document.getElementById('slider-price-label');
  const discountLabel = document.getElementById('valuation-discount-pct');
  const savingsLabel = document.getElementById('valuation-savings-val');
  
  // Estimator fields
  const stampDutyLabel = document.getElementById('est-stamp-duty');
  const regFeeLabel = document.getElementById('est-reg-fee');
  const totalCostLabel = document.getElementById('est-total-cost');

  const govValuation = <?php echo $gov_valuation_val; ?>;

  function updateCostEstimates(price) {
    const stampDuty = price * 0.06;
    const regFee = Math.min(price * 0.01, 30000); // capped at 30k in Maharashtra
    const legalFee = 15000;
    const totalCost = price + stampDuty + regFee + legalFee;
    
    if (stampDutyLabel) stampDutyLabel.textContent = "₹ " + formatCurrency(stampDuty);
    if (regFeeLabel) regFeeLabel.textContent = "₹ " + formatCurrency(regFee);
    if (totalCostLabel) totalCostLabel.textContent = "₹ " + formatCurrency(totalCost);
  }

  if (priceSlider) {
    // Initial call
    updateCostEstimates(parseFloat(priceSlider.value));

    priceSlider.addEventListener('input', (e) => {
      const activePrice = parseFloat(e.target.value);
      
      // Update price display label
      priceLabel.textContent = "₹ " + formatCurrency(activePrice);
      
      // Calculate savings and percentage difference
      const savings = govValuation - activePrice;
      const pct = Math.round((savings / govValuation) * 100);

      // Render savings metrics
      if (pct > 0) {
        discountLabel.textContent = `${pct}% Below Ready Reckoner`;
        discountLabel.className = "text-xl font-black text-premium-emerald";
        savingsLabel.textContent = "₹ " + formatCurrency(savings);
      } else {
        const premiumPct = Math.abs(pct);
        discountLabel.textContent = `${premiumPct}% Above Ready Reckoner`;
        discountLabel.className = "text-xl font-black text-premium-gold";
        savingsLabel.textContent = "₹ " + formatCurrency(Math.abs(savings)) + " Premium";
      }

      // Update cost estimates
      updateCostEstimates(activePrice);
    });
  }

  function formatCurrency(val) {
    if (val >= 10000000) {
      return (val / 10000000).toFixed(2) + " Cr";
    }
    return (val / 100000).toFixed(2) + " Lakhs";
  }

  // Newspaper Notice language toggling
  function switchNoticeLanguage(lang) {
    const enBtn = document.getElementById('notice-en-btn');
    const mrBtn = document.getElementById('notice-mr-btn');
    const enText = document.getElementById('notice-en-text');
    const mrText = document.getElementById('notice-mr-text');

    if (lang === 'en') {
      enBtn.className = "px-3.5 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-900 shadow transition-all";
      mrBtn.className = "px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-800 transition-all";
      enText.classList.remove('hidden');
      mrText.classList.add('hidden');
    } else {
      mrBtn.className = "px-3.5 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-900 shadow transition-all";
      enBtn.className = "px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-800 transition-all";
      mrText.classList.remove('hidden');
      enText.classList.add('hidden');
    }
  }

  // Site inspection booking AJAX handler
  function handleSchedulerSubmit(e) {
    e.preventDefault();
    const propertyId = document.getElementById('sched-property-id').value;
    const visitDate = document.getElementById('sched-date').value;
    const timeSlot = document.getElementById('sched-time').value;
    const phone = document.getElementById('sched-phone').value;
    const agentId = document.getElementById('sched-agent-id').value;
    const errorEl = document.getElementById('scheduler-error-msg');

    errorEl.classList.add('hidden');

    const formData = new FormData();
    formData.append('property_id', propertyId);
    formData.append('visit_date', visitDate);
    formData.append('time_slot', timeSlot);
    formData.append('phone', phone);
    formData.append('agent_id', agentId);

    const wrapper = document.getElementById('scheduler-form-wrapper');
    wrapper.innerHTML = `
      <div class="flex flex-col items-center justify-center py-12 space-y-4">
        <div class="h-10 w-10 border-4 border-slate-600 border-t-emerald-400 rounded-full animate-spin"></div>
        <p class="text-xs text-slate-400 font-semibold">Generating physical booking slot...</p>
      </div>
    `;

    fetch('api/schedule_visit.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Display assigned agent info from database variables
        const agentName = "<?php echo htmlspecialchars($prop['agent_name'] ?: 'Aniket Deshmukh'); ?>";
        const agentPhone = "<?php echo htmlspecialchars($prop['agent_phone'] ?: '+91 98230 12345'); ?>";
        const agentImage = "<?php echo htmlspecialchars($prop['agent_image'] ?: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80'); ?>";
        const agentSpecialty = "<?php echo htmlspecialchars($prop['agent_specialty'] ?: 'Bank Auctions Specialist'); ?>";

        wrapper.innerHTML = `
          <div class="text-center py-6 space-y-6">
            <div class="mx-auto h-12 w-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 border border-emerald-500/25">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            
            <div class="space-y-1">
              <h3 class="text-lg font-black">Inspector Assigned!</h3>
              <p class="text-xs text-slate-400 font-semibold">Your site viewing is registered on ${visitDate}.</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center space-x-3 text-left">
              <img src="${agentImage}" alt="Portrait" class="h-11 w-11 rounded-full border border-white/10 object-cover">
              <div>
                <h4 class="text-sm font-extrabold text-white">${agentName}</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${agentSpecialty}</p>
                <p class="text-[10px] font-bold text-emerald-400 mt-0.5">${agentPhone}</p>
              </div>
            </div>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      } else {
        errorEl.textContent = data.message;
        errorEl.classList.remove('hidden');
        window.location.reload();
      }
    })
    .catch(() => {
      errorEl.textContent = 'Server communications failed.';
      errorEl.classList.remove('hidden');
    });
  }
</script>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
