<?php
// buyer_dashboard.php
require_once 'config/db.php';

// Check if user is logged in and has buyer role
$is_authorized = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'buyer';
$user_id = $is_authorized ? $_SESSION['user']['id'] : 0;

$success_msg = '';
$error_msg = '';

// Handle Profile Update / Subscription POST
if ($is_authorized && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        if (empty($name) || empty($email)) {
            $error_msg = 'Name and Email are required.';
        } else {
            try {
                // Update database
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $user_id]);
                
                // Update session
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['phone'] = $phone;
                
                $success_msg = 'Profile updated successfully! Inspections synced.';
            } catch (PDOException $e) {
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['activate_trial'])) {
        $ends_at = date('Y-m-d H:i:s', strtotime('+7 days'));
        try {
            $stmt = $pdo->prepare("UPDATE users SET subscription_ends_at = ? WHERE id = ?");
            $stmt->execute([$ends_at, $user_id]);
            $_SESSION['user']['subscription_ends_at'] = $ends_at;
            $success_msg = '7-Day Free Pass activated successfully! Enjoy full access to forensic data.';
        } catch (PDOException $e) {
            $error_msg = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch updated user details (to get phone number and subscription status)
$user_phone = '';
$user_email = '';
$user_name = '';
$subscription_ends_at = null;
if ($is_authorized) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $user_phone = $user['phone'] ?: '';
        $user_email = $user['email'];
        $user_name = $user['name'];
        $subscription_ends_at = $user['subscription_ends_at'];
        // Keep session in sync
        $_SESSION['user']['phone'] = $user_phone;
        $_SESSION['user']['subscription_ends_at'] = $subscription_ends_at;
    }
}

// Fetch Site Visits booked by this phone number
$visits = [];
if ($is_authorized && !empty($user_phone)) {
    $stmt = $pdo->prepare("
        SELECT v.*, p.title as property_title, p.listing_id, p.image as property_image, p.reserve_price,
               a.name as agent_name, a.phone as agent_phone, a.image as agent_image, a.specialty as agent_specialty
        FROM site_visits v
        JOIN properties p ON v.property_id = p.id
        LEFT JOIN agents a ON v.agent_id = a.id
        WHERE v.phone = ?
        ORDER BY v.created_at DESC
    ");
    $stmt->execute([$user_phone]);
    $visits = $stmt->fetchAll();
}

// Fetch Legal Consultations booked by this email
$consultations = [];
if ($is_authorized && !empty($user_email)) {
    $stmt = $pdo->prepare("
        SELECT c.*
        FROM consultations c
        WHERE c.email = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_email]);
    $consultations = $stmt->fetchAll();
}

// Fetch Campaign Leads claimed by this email
$leads = [];
if ($is_authorized && !empty($user_email)) {
    $stmt = $pdo->prepare("
        SELECT l.*
        FROM leads l
        WHERE l.email = ?
        ORDER BY l.created_at DESC
    ");
    $stmt->execute([$user_email]);
    $leads = $stmt->fetchAll();
}

// Fetch all agents for contacts listing
$agents = $pdo->query("SELECT * FROM agents ORDER BY rating DESC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

  <?php if (!$is_authorized): ?>
    <!-- Locked Unauthorized State -->
    <div class="bg-white rounded-[32px] border border-slate-100 p-12 text-center max-w-md mx-auto space-y-6 shadow-xl relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl"></div>
      <div class="mx-auto h-20 w-20 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center justify-center text-premium-emerald shadow-inner">
        <i data-lucide="user-check" class="h-9 w-9"></i>
      </div>
      <div class="space-y-2">
        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Buyer Dashboard Locked</h3>
        <p class="text-xs text-slate-500 font-semibold leading-relaxed px-4">Please log in or register as a buyer to view your scheduled inspections, legal consultations, and download claimed prospectus documents.</p>
      </div>
      <button onclick="openAuthModal()" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3.5 rounded-2xl text-sm font-extrabold shadow-md hover:shadow-emerald-500/10 transition-all duration-200">
        Sign In / Register As Buyer
      </button>
    </div>

  <?php else: ?>
    <!-- Authorized Dashboard Layout -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white/40 backdrop-blur-md p-6 rounded-3xl border border-slate-200/60 shadow-sm">
      <div class="space-y-1">
        <div class="flex items-center space-x-2.5">
          <div class="h-10 w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald shadow-inner animate-pulse">
            <i data-lucide="layout-dashboard" class="h-6 w-6"></i>
          </div>
          <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Buyer Control Center</h1>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Bidder Profile for <?php echo htmlspecialchars($user_name); ?></p>
          </div>
        </div>
      </div>
      
      <!-- Stats Summary -->
      <div class="flex space-x-4 w-full md:w-auto">
        <div class="bg-white flex-1 md:flex-none px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3.5 hover:shadow transition-shadow">
          <div class="h-10 w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald shrink-0">
            <i data-lucide="calendar" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Inspections</span>
            <span class="text-xl font-black text-slate-800"><?php echo count($visits); ?></span>
          </div>
        </div>
        
        <div class="bg-white flex-1 md:flex-none px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3.5 hover:shadow transition-shadow">
          <div class="h-10 w-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
            <i data-lucide="file-text" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Consultations</span>
            <span class="text-xl font-black text-slate-800"><?php echo count($consultations); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex space-x-1 border-b border-slate-200">
      <button onclick="switchBuyerTab('overview')" id="btn-overview" class="px-6 py-3 text-sm font-black border-b-2 border-premium-emerald text-premium-emerald transition-all">Overview</button>
      <button onclick="switchBuyerTab('bids')" id="btn-bids" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all">My Bids & Offers</button>
      <button onclick="switchBuyerTab('settings')" id="btn-settings" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all">Settings & Billing</button>
    </div>

    <div id="tab-overview" class="block space-y-10">
    <?php if (!empty($success_msg)): ?>
      <div class="bg-emerald-50 border border-emerald-100 text-premium-emerald px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center space-x-2.5 shadow-sm animate-fade-in">
        <i data-lucide="check-circle" class="h-5 w-5 text-emerald-500 shrink-0"></i>
        <span><?php echo htmlspecialchars($success_msg); ?></span>
      </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="bg-red-50 border border-red-100 text-red-600 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center space-x-2.5 shadow-sm">
        <i data-lucide="alert-circle" class="h-5 w-5 text-red-500 shrink-0"></i>
        <span><?php echo htmlspecialchars($error_msg); ?></span>
      </div>
    <?php endif; ?>

    <!-- Two-column Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- Left Column: Profile & stamp duty widget -->
      <div class="space-y-8">
        
        <!-- Subscription Status Card -->
        <div class="bg-slate-900 text-white rounded-3xl border border-slate-800 shadow-xl p-6 space-y-4 relative overflow-hidden">
          <div class="absolute -right-8 -top-8 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl"></div>
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <i data-lucide="award" class="h-5 w-5 text-emerald-400"></i>
              <h3 class="text-xs font-black uppercase tracking-wider">MahaAuctions Pass</h3>
            </div>
            <?php
              $active = false;
              if (!empty($subscription_ends_at)) {
                  $active = (strtotime($subscription_ends_at) > time());
              }
            ?>
            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase <?php echo $active ? 'bg-premium-emerald text-white' : 'bg-red-500 text-white'; ?>">
              <?php echo $active ? 'Active' : 'Inactive'; ?>
            </span>
          </div>
          
          <div class="space-y-1">
            <?php if ($active): ?>
              <p class="text-xs text-slate-300 font-medium">Your 7-Day Free Trial is fully active.</p>
              <div class="text-[10px] text-emerald-400 font-extrabold uppercase mt-1">Valid Until: <?php echo date('d M Y - h:i A', strtotime($subscription_ends_at)); ?></div>
            <?php else: ?>
              <p class="text-xs text-slate-400 font-medium">Unlock full access to newspaper notices, ready reckoner valuation audits, and guided inspector site visits.</p>
              <form method="POST" action="buyer_dashboard.php" class="pt-2">
                <input type="hidden" name="activate_trial" value="1">
                <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-2.5 rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center justify-center space-x-1.5 active:scale-[0.98]">
                  <i data-lucide="zap" class="h-3.5 w-3.5"></i>
                  <span>Activate 7-Day Free Pass</span>
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>

        <!-- Profile settings card -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 space-y-6">
          <div class="space-y-1">
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="user" class="h-5.5 w-5.5 text-premium-emerald"></i>
              <span>Profile Settings</span>
            </h3>
            <p class="text-xs text-slate-400 font-semibold leading-relaxed">Save your verified mobile number to automatically synchronize and load scheduled site inspections.</p>
          </div>

          <form method="POST" action="buyer_dashboard.php" class="space-y-4">
            <input type="hidden" name="update_profile" value="1">
            
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Full Name</label>
              <input type="text" name="name" required value="<?php echo htmlspecialchars($user_name); ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
              <input type="email" name="email" required value="<?php echo htmlspecialchars($user_email); ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Mobile Number (Inspections Sync)</label>
              <input type="text" name="phone" placeholder="98XXXXXXXX" value="<?php echo htmlspecialchars($user_phone); ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3 rounded-xl text-xs font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-500/10 flex items-center justify-center space-x-1.5 active:scale-[0.98]">
              <i data-lucide="save" class="h-4 w-4"></i>
              <span>Save & Sync Inspections</span>
            </button>
          </form>
        </div>

        <!-- Stamp Duty Widget -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 space-y-6">
          <div class="space-y-1">
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="calculator" class="h-5.5 w-5.5 text-premium-emerald"></i>
              <span>Statutory Cost Estimator</span>
            </h3>
            <p class="text-xs text-slate-400 font-semibold leading-relaxed">Calculate Stamp Duty & Registration costs based on Maharashtra statutory provisions.</p>
          </div>

          <div class="space-y-4">
            <div class="space-y-2">
              <label class="flex justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                <span>Property Valuation Price</span>
                <span id="cost-calc-price-lbl" class="text-slate-700">₹ 1.00 Cr</span>
              </label>
              <input type="range" id="cost-calc-slider" min="1000000" max="150000000" value="10000000" step="1000000" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-premium-emerald">
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50 space-y-2.5 text-xs font-semibold text-slate-500">
              <div class="flex justify-between pb-2 border-b border-slate-200/60">
                <span>Stamp Duty (6%)</span>
                <span id="cost-calc-stamp" class="font-extrabold text-slate-800">₹ 6,00,000</span>
              </div>
              <div class="flex justify-between pb-2 border-b border-slate-200/60">
                <span>Registration Fee (1% cap)</span>
                <span id="cost-calc-reg" class="font-extrabold text-slate-800">₹ 30,000</span>
              </div>
              <div class="flex justify-between pb-2 border-b border-slate-200/60">
                <span>Legal Filings & Advisory</span>
                <span class="font-extrabold text-slate-800">₹ 15,000</span>
              </div>
              <div class="flex justify-between pt-1 font-bold">
                <span class="text-slate-800">Total Acquisition Cost</span>
                <span id="cost-calc-total" class="font-black text-premium-emerald text-sm">₹ 1,06,45,000</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column: Site visits, legal consults, campaign leads -->
      <div class="lg:col-span-2 space-y-8">
        
        <!-- Site inspections cards -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-900 text-white px-6 py-4.5 flex justify-between items-center relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl"></div>
            <h3 class="text-sm font-black flex items-center space-x-2 relative z-10">
              <i data-lucide="compass" class="h-4.5 w-4.5 text-emerald-400 animate-pulse"></i>
              <span>My Scheduled Site Inspections</span>
            </h3>
            <span class="text-[9px] bg-white/10 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider text-slate-300"><?php echo count($visits); ?> Booked</span>
          </div>

          <div class="p-6 space-y-4">
            <?php if (empty($user_phone)): ?>
              <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-5 text-center space-y-2 text-xs font-semibold text-slate-500">
                <i data-lucide="alert-triangle" class="h-8 w-8 text-amber-500 mx-auto"></i>
                <p>Sync Profile Mobile Number: Please configure your phone number in the Profile Settings panel to display scheduled physical inspections.</p>
              </div>
            <?php elseif (count($visits) === 0): ?>
              <div class="text-center py-10 space-y-2">
                <i data-lucide="calendar" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">No scheduled site inspections found for phone number <?php echo htmlspecialchars($user_phone); ?>.</div>
                <p class="text-[10px] text-slate-400 max-w-sm mx-auto">To book an inspection, open any listed foreclosure property page and choose a guided schedule slot.</p>
              </div>
            <?php else: ?>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($visits as $v): ?>
                  <div class="border border-slate-200/70 rounded-2xl p-4 flex flex-col justify-between space-y-4 hover:border-slate-300 transition-colors bg-slate-50/20">
                    <div class="flex space-x-3">
                      <img src="<?php echo htmlspecialchars($v['property_image']); ?>" alt="Img" class="h-12 w-12 rounded-xl object-cover shrink-0 border border-slate-100">
                      <div class="space-y-0.5 truncate">
                        <h4 class="text-xs font-black text-slate-800 truncate"><?php echo htmlspecialchars($v['property_title']); ?></h4>
                        <div class="text-[9px] text-slate-400 font-extrabold tracking-wider uppercase">ID: <?php echo htmlspecialchars($v['listing_id']); ?> • <?php echo htmlspecialchars($v['reserve_price']); ?></div>
                      </div>
                    </div>

                    <div class="p-3 bg-white border border-slate-100 rounded-xl space-y-1.5 text-[11px] text-slate-500 font-semibold">
                      <div class="flex justify-between items-center">
                        <span class="flex items-center space-x-1">
                          <i data-lucide="calendar" class="h-3.5 w-3.5 text-slate-400"></i>
                          <span>Date:</span>
                        </span>
                        <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars(date('d M Y', strtotime($v['visit_date']))); ?></span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="flex items-center space-x-1">
                          <i data-lucide="clock" class="h-3.5 w-3.5 text-slate-400"></i>
                          <span>Slot:</span>
                        </span>
                        <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($v['time_slot']); ?></span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="flex items-center space-x-1">
                          <i data-lucide="tag" class="h-3.5 w-3.5 text-slate-400"></i>
                          <span>Status:</span>
                        </span>
                        <?php
                          $v_status = $v['status'];
                          $badge = 'bg-slate-100 text-slate-600 border-slate-200';
                          if ($v_status === 'Confirmed') $badge = 'bg-emerald-50 text-premium-emerald border-emerald-100';
                          elseif ($v_status === 'Completed') $badge = 'bg-blue-50 text-blue-600 border-blue-100';
                          elseif ($v_status === 'Cancelled') $badge = 'bg-red-50 text-red-500 border-red-100';
                        ?>
                        <span class="px-2 py-0.5 border text-[9px] font-extrabold rounded-md uppercase <?php echo $badge; ?>">
                          <?php echo htmlspecialchars($v_status); ?>
                        </span>
                      </div>
                    </div>

                    <!-- Assigned Agent Card -->
                    <div class="flex items-center space-x-2.5 border-t border-slate-100 pt-3">
                      <img src="<?php echo htmlspecialchars($v['agent_image']); ?>" alt="Portrait" class="h-9 w-9 rounded-full object-cover border border-slate-150">
                      <div>
                        <h5 class="text-[11px] font-black text-slate-800"><?php echo htmlspecialchars($v['agent_name'] ?: 'Aniket Deshmukh'); ?></h5>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider"><?php echo htmlspecialchars($v['agent_specialty'] ?: 'Bank Auctions Specialist'); ?></p>
                        <p class="text-[9px] text-premium-emerald font-extrabold mt-0.5"><?php echo htmlspecialchars($v['agent_phone']); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Saved / Bookmarked Properties -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-150 px-6 py-4 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="bookmark" class="h-4.5 w-4.5 text-premium-emerald"></i>
              <span>Saved Properties</span>
            </h3>
            <span class="text-[9px] bg-slate-200 px-2 py-0.5 rounded-full font-bold text-slate-600 uppercase tracking-wider">Local Session</span>
          </div>

          <div class="p-6">
            <div class="text-center py-6 space-y-2">
              <i data-lucide="bookmark-plus" class="h-8 w-8 text-slate-300 mx-auto"></i>
              <div class="text-xs font-bold text-slate-400">No properties saved yet.</div>
              <p class="text-[10px] text-slate-400 max-w-sm mx-auto">Browse the foreclosure map and click the bookmark icon on any property to save it here for quick access.</p>
              <a href="index.php" class="inline-block mt-4 px-4 py-2 bg-emerald-50 text-premium-emerald font-bold rounded-xl text-xs hover:bg-emerald-100 transition-colors">Browse Foreclosures</a>
            </div>
          </div>
        </div>

        <!-- Legal Consultations Board -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-150 px-6 py-4 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800">My Panel Advocate Consultations</h3>
            <span class="text-[9px] bg-slate-200 px-2 py-0.5 rounded-full font-bold text-slate-600 uppercase tracking-wider"><?php echo count($consultations); ?> Active</span>
          </div>

          <div class="p-6">
            <?php if (count($consultations) === 0): ?>
              <div class="text-center py-6 space-y-2">
                <i data-lucide="shield-check" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">No scheduled panel advisory sessions yet.</div>
                <p class="text-[10px] text-slate-400 max-w-sm mx-auto">Book direct sessions with legal experts on the advisory page to evaluate title clearances.</p>
              </div>
            <?php else: ?>
              <div class="divide-y divide-slate-100">
                <?php foreach ($consultations as $c): ?>
                  <div class="py-4 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                    <div class="space-y-1">
                      <h4 class="text-xs font-black text-slate-800"><?php echo htmlspecialchars($c['topic']); ?></h4>
                      <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        <span>Advocate ID: <?php echo htmlspecialchars($c['advocate_id']); ?></span>
                        <span>•</span>
                        <span class="flex items-center space-x-1">
                          <i data-lucide="calendar" class="h-3 w-3 text-slate-400"></i>
                          <span>Date: <?php echo htmlspecialchars(date('d M Y', strtotime($c['booking_date']))); ?></span>
                        </span>
                      </div>
                    </div>
                    
                    <span class="px-2 py-0.5 bg-emerald-50 text-premium-emerald border border-emerald-100 text-[9px] font-extrabold rounded-md uppercase">
                      Scheduled
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Campaign Claims & Brochures -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-150 px-6 py-4 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800">VIP Campaigns & Prospectus Downloads</h3>
            <span class="text-[9px] bg-slate-200 px-2 py-0.5 rounded-full font-bold text-slate-600 uppercase tracking-wider"><?php echo count($leads); ?> Unlocked</span>
          </div>

          <div class="p-6">
            <?php if (count($leads) === 0): ?>
              <div class="text-center py-6 space-y-2">
                <i data-lucide="download" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">No active brochure claims found.</div>
                <p class="text-[10px] text-slate-400 max-w-sm mx-auto">Claim partner prospectus brochures on our Godrej Horizon homepage campaign panel.</p>
              </div>
            <?php else: ?>
              <div class="divide-y divide-slate-100">
                <?php foreach ($leads as $l): ?>
                  <div class="py-4 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                    <div class="space-y-1">
                      <h4 class="text-xs font-black text-slate-800"><?php echo htmlspecialchars($l['campaign']); ?></h4>
                      <p class="text-[9px] text-slate-400 font-bold uppercase">Claimed on: <?php echo htmlspecialchars(date('d M Y', strtotime($l['created_at']))); ?></p>
                    </div>
                    
                    <button onclick="alert('Downloading brochure for <?php echo htmlspecialchars($l['campaign']); ?>...')" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-[10px] font-extrabold shadow-sm transition-all flex items-center space-x-1">
                      <i data-lucide="download" class="h-3.5 w-3.5"></i>
                      <span>Download PDF</span>
                    </button>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Verified Agents Directory -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 space-y-6">
          <div class="space-y-1">
            <h3 class="text-lg font-black text-slate-800">Verified Platform Agents</h3>
            <p class="text-xs text-slate-400 font-semibold leading-relaxed">Direct communication desk with our certified site inspectors and auction facilitators.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($agents as $agt): ?>
              <div class="bg-slate-50/50 border border-slate-150 rounded-2xl p-4 flex flex-col items-center text-center space-y-3 shadow-inner hover:border-slate-350 transition-colors">
                <img src="<?php echo htmlspecialchars($agt['image']); ?>" alt="Agent" class="h-16 w-16 rounded-full object-cover border-2 border-slate-200">
                <div>
                  <h4 class="text-xs font-black text-slate-800"><?php echo htmlspecialchars($agt['name']); ?></h4>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5"><?php echo htmlspecialchars($agt['specialty']); ?></p>
                </div>
                <div class="flex items-center space-x-1 text-[10px] font-extrabold text-amber-500 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-lg">
                  <i data-lucide="star" class="h-3 w-3 fill-amber-500"></i>
                  <span><?php echo htmlspecialchars($agt['rating']); ?></span>
                </div>
                <a href="tel:<?php echo htmlspecialchars($agt['phone']); ?>" class="w-full bg-white hover:bg-slate-50 border border-slate-200/80 text-slate-700 py-2 rounded-xl text-[10px] font-extrabold shadow-sm transition-all text-center block">
                  Contact Agent
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>

    </div> <!-- End overview tab -->

    <!-- My Bids & Offers Tab -->
    <div id="tab-bids" class="hidden space-y-8">
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-12 text-center max-w-3xl mx-auto mt-10">
        <div class="mx-auto h-20 w-20 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center text-slate-400 shadow-inner mb-6">
          <i data-lucide="gavel" class="h-10 w-10"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-800 tracking-tight">No Active Bids</h3>
        <p class="text-sm font-semibold text-slate-500 mt-2 max-w-md mx-auto">You have not submitted any bids for auction properties or heavy deposit offers yet.</p>
        <button onclick="window.location.href='index.php'" class="mt-8 px-8 py-3.5 bg-slate-900 text-white rounded-2xl text-sm font-black hover:bg-slate-800 transition-all shadow-md">Browse Marketplace</button>
      </div>
    </div>

    <!-- Settings & Billing Tab -->
    <div id="tab-settings" class="hidden space-y-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8 space-y-6">
          <div class="space-y-1">
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="credit-card" class="h-5.5 w-5.5 text-premium-emerald"></i>
              <span>Payment Methods</span>
            </h3>
            <p class="text-xs text-slate-400 font-semibold leading-relaxed">Manage your saved cards and bank accounts for instant EMD (Earnest Money Deposit) transfers.</p>
          </div>
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center text-slate-500 text-xs font-bold">
            No saved payment methods.
          </div>
          <button class="w-full px-4 py-3 border-2 border-dashed border-slate-200 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors text-xs font-black flex items-center justify-center space-x-2">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span>Add Payment Method</span>
          </button>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8 space-y-6">
          <div class="space-y-1">
            <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
              <i data-lucide="file-text" class="h-5.5 w-5.5 text-premium-emerald"></i>
              <span>Invoice History</span>
            </h3>
            <p class="text-xs text-slate-400 font-semibold leading-relaxed">Download receipts for your MahaAuctions Pass subscriptions and legal consultation bookings.</p>
          </div>
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center text-slate-500 text-xs font-bold">
            No past invoices found.
          </div>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<script>
  function switchBuyerTab(tab) {
    const tabs = ['overview', 'bids', 'settings'];
    tabs.forEach(t => {
      const btn = document.getElementById('btn-' + t);
      const content = document.getElementById('tab-' + t);
      if (!btn || !content) return;

      if (t === tab) {
        btn.className = "px-6 py-3 text-sm font-black border-b-2 border-premium-emerald text-premium-emerald transition-all";
        content.classList.remove('hidden');
        content.classList.add('block');
      } else {
        btn.className = "px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all";
        content.classList.add('hidden');
        content.classList.remove('block');
      }
    });
  }
  // Cost Calculator Calculations
  const calcSlider = document.getElementById('cost-calc-slider');
  const calcPriceLbl = document.getElementById('cost-calc-price-lbl');
  const calcStamp = document.getElementById('cost-calc-stamp');
  const calcReg = document.getElementById('cost-calc-reg');
  const calcTotal = document.getElementById('cost-calc-total');

  if (calcSlider) {
    calcSlider.addEventListener('input', (e) => {
      const price = parseFloat(e.target.value);
      
      // Update label
      calcPriceLbl.textContent = "₹ " + formatCalculatorCurrency(price);
      
      // Calculate
      const stampDuty = price * 0.06;
      const regFee = Math.min(price * 0.01, 30000); // capped at 30k in Maharashtra
      const legalFee = 15000;
      const total = price + stampDuty + regFee + legalFee;
      
      // Render
      calcStamp.textContent = "₹ " + formatCalculatorCurrency(stampDuty);
      calcReg.textContent = "₹ " + formatCalculatorCurrency(regFee);
      calcTotal.textContent = "₹ " + formatCalculatorCurrency(total);
    });
  }

  function formatCalculatorCurrency(val) {
    if (val >= 10000000) {
      return (val / 10000000).toFixed(2) + " Cr";
    }
    if (val >= 100000) {
      return (val / 100000).toFixed(2) + " Lakhs";
    }
    return val.toLocaleString('en-IN');
  }
</script>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
