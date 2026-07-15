<?php
// seller_dashboard.php
require_once 'config/db.php';

// Check if user is logged in and has seller role
$is_authorized = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'seller';
$seller_id = $is_authorized ? $_SESSION['user']['id'] : 0;

$success_msg = '';
$error_msg = '';

$is_seller_subscribed = false;
$listing_count = 0;

if ($is_authorized) {
    try {
        // Fetch subscription status
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$seller_id]);
        $user = $stmt->fetch();
        if ($user && !empty($user['subscription_ends_at'])) {
            $is_seller_subscribed = (strtotime($user['subscription_ends_at']) > time());
        }

        // Fetch current property listings count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE seller_id = ?");
        $stmt->execute([$seller_id]);
        $listing_count = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $error_msg = 'Initialization error: ' . $e->getMessage();
    }
}

// Handle POST actions
if ($is_authorized && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Activate Seller Subscription
    if (isset($_POST['activate_seller_subscription'])) {
        $ends_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        try {
            $stmt = $pdo->prepare("UPDATE users SET subscription_ends_at = ? WHERE id = ?");
            $stmt->execute([$ends_at, $seller_id]);
            $_SESSION['user']['subscription_ends_at'] = $ends_at;
            $is_seller_subscribed = true;
            $success_msg = 'Seller Premium Subscription activated successfully! You can now post up to 10 properties.';
            
            // Re-fetch count
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE seller_id = ?");
            $stmt->execute([$seller_id]);
            $listing_count = $stmt->fetchColumn();
        } catch (PDOException $e) {
            $error_msg = 'Database error: ' . $e->getMessage();
        }
    }

    // 1. Publish Property Listing
    if (isset($_POST['submit_property'])) {
        if ($listing_count >= 1 && !$is_seller_subscribed) {
            $error_msg = 'Listing blocked: Subsequent listings require a Seller Premium Subscription. Your first posting was free. Please upgrade to post more properties.';
        } else {
            $title = trim($_POST['title']);
        $address = trim($_POST['address']);
        $city_id = trim($_POST['city_id']);
        $type = trim($_POST['type']);
        $category = trim($_POST['category']);
        $price = trim($_POST['price']);
        $emd = trim($_POST['emd']);
        $gov_val = trim($_POST['gov_val']);
        $details = trim($_POST['details']);
        $image = trim($_POST['image']);

        if (empty($image)) {
            $image = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80';
        }

        if (empty($title) || empty($address) || empty($city_id) || empty($price) || empty($details)) {
            $error_msg = 'Please complete all required fields.';
        } else {
            try {
                $id = 'prop-' . time();
                $listing_id = 'MA-2026-' . rand(100, 999);
                $numeric_price = (int)preg_replace('/[^0-9]/', '', $price) ?: 1000000;
                $numeric_gov_val = (int)preg_replace('/[^0-9]/', '', $gov_val) ?: ($numeric_price * 1.2);

                $notice_en = "DIRECT SALE NOTICE: This property is listed by a private seller on MahaAuctions. Reserve Price: ₹ {$price}. Contact assigned agent for details.";
                $notice_mr = "थेट विक्री नोटीस: ही मालमत्ता महालिलाव पोर्टलवर थेट मालकाद्वारे विक्रीसाठी उपलब्ध आहे. राखीव किंमत: रु. {$price}.";

                $stmt = $pdo->prepare("
                    INSERT INTO properties (id, listing_id, city_id, title, type, category, address, reserve_price, numeric_price, emd, government_valuation, numeric_gov_valuation, agent_id, image, details, notice_english, notice_marathi, seller_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $id, $listing_id, $city_id, $title, $type, $category, $address, $price, $numeric_price, $emd ?: 'N/A', $gov_val ? "₹ {$gov_val}" : "₹ " . ($numeric_gov_val / 10000000) . " Cr", $numeric_gov_val, 'agt-1', $image, $details, $notice_en, $notice_mr, $seller_id
                ]);

                // Update city count
                $pdo->prepare("UPDATE cities SET property_count = property_count + 1 WHERE id = ?")->execute([$city_id]);

                $success_msg = 'Property listing published successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }
      }
    }

    // 2. Edit Property Listing
    if (isset($_POST['edit_property'])) {
        $property_id = trim($_POST['property_id']);
        $title = trim($_POST['title']);
        $address = trim($_POST['address']);
        $city_id = trim($_POST['city_id']);
        $type = trim($_POST['type']);
        $category = trim($_POST['category']);
        $price = trim($_POST['price']);
        $emd = trim($_POST['emd']);
        $gov_val = trim($_POST['gov_val']);
        $details = trim($_POST['details']);
        $image = trim($_POST['image']);

        if (empty($title) || empty($address) || empty($city_id) || empty($price) || empty($details)) {
            $error_msg = 'Please complete all required fields for editing.';
        } else {
            try {
                // Verify ownership
                $check = $pdo->prepare("SELECT city_id FROM properties WHERE id = ? AND seller_id = ?");
                $check->execute([$property_id, $seller_id]);
                $old_p = $check->fetch();

                if ($old_p) {
                    $numeric_price = (int)preg_replace('/[^0-9]/', '', $price) ?: 1000000;
                    $numeric_gov_val = (int)preg_replace('/[^0-9]/', '', $gov_val) ?: ($numeric_price * 1.2);

                    $stmt = $pdo->prepare("
                        UPDATE properties SET 
                            title = ?, address = ?, city_id = ?, type = ?, category = ?, 
                            reserve_price = ?, numeric_price = ?, emd = ?, 
                            government_valuation = ?, numeric_gov_valuation = ?, 
                            image = ?, details = ?
                        WHERE id = ? AND seller_id = ?
                    ");
                    $stmt->execute([
                        $title, $address, $city_id, $type, $category, 
                        $price, $numeric_price, $emd ?: 'N/A', 
                        $gov_val ? "₹ {$gov_val}" : "₹ " . ($numeric_gov_val / 10000000) . " Cr", $numeric_gov_val, 
                        $image ?: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80', $details,
                        $property_id, $seller_id
                    ]);

                    // Update city counts if changed
                    if ($old_p['city_id'] !== $city_id) {
                        $pdo->prepare("UPDATE cities SET property_count = GREATEST(0, property_count - 1) WHERE id = ?")->execute([$old_p['city_id']]);
                        $pdo->prepare("UPDATE cities SET property_count = property_count + 1 WHERE id = ?")->execute([$city_id]);
                    }

                    $success_msg = 'Property listing updated successfully!';
                } else {
                    $error_msg = 'Unauthorized operation.';
                }
            } catch (PDOException $e) {
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }
    }

    // 3. Delete Property Listing
    if (isset($_POST['delete_property'])) {
        $property_id = trim($_POST['property_id']);
        try {
            // Verify ownership
            $check = $pdo->prepare("SELECT city_id FROM properties WHERE id = ? AND seller_id = ?");
            $check->execute([$property_id, $seller_id]);
            $p = $check->fetch();

            if ($p) {
                $pdo->prepare("UPDATE cities SET property_count = GREATEST(0, property_count - 1) WHERE id = ?")->execute([$p['city_id']]);
                
                $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ? AND seller_id = ?");
                $stmt->execute([$property_id, $seller_id]);
                
                $success_msg = "Property listing deleted successfully.";
            } else {
                $error_msg = "Unauthorized operation.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    }

    // 4. Update Site Visit Lead Status
    if (isset($_POST['update_visit_status'])) {
        $visit_id = (int)$_POST['visit_id'];
        $new_status = trim($_POST['status']);
        try {
            // Verify ownership of the property for the visit
            $check = $pdo->prepare("SELECT v.id FROM site_visits v JOIN properties p ON v.property_id = p.id WHERE v.id = ? AND p.seller_id = ?");
            $check->execute([$visit_id, $seller_id]);
            
            if ($check->fetch()) {
                $stmt = $pdo->prepare("UPDATE site_visits SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $visit_id]);
                $success_msg = "Inspection status updated to: " . $new_status;
            } else {
                $error_msg = "Unauthorized operation.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch dashboard data
$properties = [];
$visits = [];
if ($is_authorized) {
    // 1. Listings
    $prop_stmt = $pdo->prepare("SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC");
    $prop_stmt->execute([$seller_id]);
    $properties = $prop_stmt->fetchAll();

    // 2. Site Visit Leads
    $visit_stmt = $pdo->prepare("
        SELECT v.*, p.title as property_title, p.listing_id, a.name as agent_name 
        FROM site_visits v 
        JOIN properties p ON v.property_id = p.id 
        LEFT JOIN agents a ON v.agent_id = a.id 
        WHERE p.seller_id = ? 
        ORDER BY v.created_at DESC
    ");
    $visit_stmt->execute([$seller_id]);
    $visits = $visit_stmt->fetchAll();
}

// Fetch all cities for dropdown
$cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
  
  <?php if (!$is_authorized): ?>
    <!-- Locked Unauthorized State -->
    <div class="bg-white rounded-[32px] border border-slate-100 p-12 text-center max-w-md mx-auto space-y-6 shadow-xl relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-32 h-32 bg-red-500/5 rounded-full blur-2xl"></div>
      <div class="mx-auto h-20 w-20 bg-red-50 border border-red-100 rounded-3xl flex items-center justify-center text-red-500 shadow-inner">
        <i data-lucide="lock" class="h-9 w-9"></i>
      </div>
      <div class="space-y-2">
        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Seller Dashboard Locked</h3>
        <p class="text-xs text-slate-500 font-semibold leading-relaxed px-4">Please log in or register as a certified seller to publish assets, list foreclosures, and coordinate viewings.</p>
      </div>
      <button onclick="openAuthModal()" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3.5 rounded-2xl text-sm font-extrabold shadow-md hover:shadow-emerald-500/10 transition-all duration-200">
        Sign In / Register As Seller
      </button>
    </div>

  <?php else: ?>
    <!-- Authorized Dashboard Layout -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 sm:gap-6 bg-white/40 backdrop-blur-md p-4 sm:p-6 rounded-3xl border border-slate-200/60 shadow-sm">
      <div class="space-y-1">
        <div class="flex items-center space-x-2.5">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald shadow-inner">
            <i data-lucide="store" class="h-5 w-5 sm:h-6 sm:w-6"></i>
          </div>
          <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Seller Command Center</h1>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Dashboard for <?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
          </div>
        </div>
      </div>
      
      <!-- Stats Summary -->
      <div class="flex flex-wrap gap-3 w-full md:w-auto">
        <div class="bg-white flex-1 min-w-[120px] md:flex-none px-4 sm:px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3 sm:space-x-3.5 hover:shadow transition-shadow">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald shrink-0">
            <i data-lucide="home" class="h-4 w-4 sm:h-5 sm:w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">My Listings</span>
            <span class="text-xl font-black text-slate-800"><?php echo count($properties); ?></span>
          </div>
        </div>
        
        <div class="bg-white flex-1 min-w-[120px] md:flex-none px-4 sm:px-5 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-3 sm:space-x-3.5 hover:shadow transition-shadow">
          <div class="h-9 w-9 sm:h-10 sm:w-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
            <i data-lucide="calendar" class="h-4 w-4 sm:h-5 sm:w-5"></i>
          </div>
          <div>
            <span class="block text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Site Leads</span>
            <span class="text-xl font-black text-slate-800"><?php echo count($visits); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="-mx-4 sm:mx-0">
      <div class="flex overflow-x-auto scrollbar-hide border-b border-slate-200 px-4 sm:px-0">
        <button onclick="switchSellerTab('overview')" id="btn-overview" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-black border-b-2 border-premium-emerald text-premium-emerald transition-all whitespace-nowrap">Command Center</button>
        <button onclick="switchSellerTab('analytics')" id="btn-analytics" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all whitespace-nowrap">Property Analytics</button>
        <button onclick="switchSellerTab('kyc')" id="btn-kyc" class="shrink-0 px-5 sm:px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all whitespace-nowrap">Account & KYC</button>
      </div>
    </div>

    <div id="tab-overview" class="block space-y-10">
    
    <!-- Notifications -->
    <?php if (!empty($success_msg)): ?>
      <div class="bg-emerald-50 border border-emerald-100 text-premium-emerald px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center space-x-2.5 shadow-sm">
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

    <!-- Two-column content: Left Form, Right listings list and leads -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- List new property form -->
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 space-y-6">
        <div class="space-y-1">
          <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="plus-circle" class="h-5.5 w-5.5 text-premium-emerald"></i>
            <span>Submit Asset Listing</span>
          </h3>
          <p class="text-xs text-slate-400 font-semibold">Publish new foreclosures, rentals or direct sales.</p>
        </div>

        <!-- Seller Subscription status banner -->
        <div class="p-4 rounded-2xl border <?php echo $is_seller_subscribed ? 'bg-emerald-50 border-emerald-100 text-premium-emerald' : 'bg-slate-50 border-slate-200 text-slate-700'; ?> space-y-2.5 text-xs font-semibold">
          <div class="flex justify-between items-center">
            <span class="text-[9px] uppercase tracking-wider font-extrabold text-slate-400">Seller Tier</span>
            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase <?php echo $is_seller_subscribed ? 'bg-premium-emerald text-white animate-pulse' : 'bg-slate-200 text-slate-655'; ?>">
              <?php echo $is_seller_subscribed ? 'Premium Active' : 'Free Tier'; ?>
            </span>
          </div>
          <div class="flex justify-between items-center text-slate-800">
            <span>Listings Count:</span>
            <span class="font-extrabold"><?php echo $listing_count; ?> / <?php echo $is_seller_subscribed ? '10' : '1'; ?></span>
          </div>
          <?php if (!$is_seller_subscribed): ?>
            <p class="text-[10px] text-slate-400 font-medium">Your first posting is free. Subscriptions allow up to 10 listings.</p>
            <form method="POST" action="seller_dashboard.php" class="pt-1">
              <input type="hidden" name="activate_seller_subscription" value="1">
              <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2 rounded-xl text-[10px] font-extrabold transition-all flex items-center justify-center space-x-1">
                <i data-lucide="zap" class="h-3 w-3 text-premium-gold"></i>
                <span>Upgrade to Premium Seller</span>
              </button>
            </form>
          <?php else: ?>
            <p class="text-[10px] text-emerald-600 font-medium">Premium Active. Post up to 10 listings.</p>
          <?php endif; ?>
        </div>

        <?php if ($listing_count >= 1 && !$is_seller_subscribed): ?>
          <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-5 text-center space-y-4 text-xs font-semibold">
            <div class="h-12 w-12 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-center text-amber-500 mx-auto">
              <i data-lucide="shield-alert" class="h-6 w-6"></i>
            </div>
            <div class="space-y-1">
              <h4 class="text-slate-850 font-black">Free Posting Limit Reached</h4>
              <p class="text-[10px] text-slate-450 leading-relaxed">MahaAuctions allows 1 free property listing per seller. Upgrade your account to unlock up to 10 listings.</p>
            </div>
            <form method="POST" action="seller_dashboard.php">
              <input type="hidden" name="activate_seller_subscription" value="1">
              <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3.5 rounded-xl text-xs font-extrabold shadow-sm transition-all flex items-center justify-center space-x-1.5 active:scale-[0.98]">
                <i data-lucide="zap" class="h-3.5 w-3.5 text-premium-gold"></i>
                <span>Activate Premium Seller Pass</span>
              </button>
            </form>
          </div>
        <?php else: ?>
          <form method="POST" action="seller_dashboard.php" class="space-y-4">
            <input type="hidden" name="submit_property" value="1">
            
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Title</label>
              <input type="text" name="title" required placeholder="e.g. 2 BHK Modern Flat in Thane" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Address Details</label>
              <input type="text" name="address" required placeholder="e.g. Flat 302, Sector 12, Ghodbunder Road" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">City Location</label>
                <select name="city_id" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
                  <?php foreach ($cities as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
                  <option value="Residential">Residential</option>
                  <option value="Commercial">Commercial</option>
                  <option value="Industrial">Industrial</option>
                  <option value="Agricultural">Agricultural</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
                  <option value="Seller Listed">Seller Listed</option>
                  <option value="Auction">Foreclosure Auction</option>
                  <option value="Rental">Premium Rental</option>
                  <option value="Heavy Deposit">Heavy Deposit</option>
                </select>
              </div>
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Reserve Price (₹)</label>
                <input type="text" name="price" required placeholder="e.g. 85.00 Lakhs" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Deposit (EMD)</label>
                <input type="text" name="emd" placeholder="e.g. 8.50 Lakhs" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
              </div>
              <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Govt Ready Reckoner</label>
                <input type="text" name="gov_val" placeholder="e.g. 1.00 Cr" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Image URL Address</label>
              <input type="url" name="image" placeholder="https://images.unsplash.com/photo-..." class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>

            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Detailed Specifications</label>
              <textarea name="details" rows="3" required placeholder="Amenities, Carpet area, Legal clearances, Possession terms..." class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800"></textarea>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3 rounded-xl text-xs font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-500/10 flex items-center justify-center space-x-2">
              <i data-lucide="check" class="h-4 w-4"></i>
              <span>Publish Property Listing</span>
            </button>
          </form>
        <?php endif; ?>
      </div>

      <!-- Listings and Leads lists -->
      <div class="lg:col-span-2 space-y-8">
        
        <!-- Site visit leads board -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-900 text-white px-6 py-4.5 flex justify-between items-center relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl"></div>
            <h3 class="text-sm font-black flex items-center space-x-2 relative z-10">
              <i data-lucide="users" class="h-4.5 w-4.5 text-emerald-400 animate-pulse"></i>
              <span>Customer Site Visit Leads Board</span>
            </h3>
            <span class="text-[9px] bg-white/10 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider text-slate-300"><?php echo count($visits); ?> Active</span>
          </div>
          
          <div class="responsive-table-wrapper">
            <?php if (count($visits) === 0): ?>
              <div class="p-12 text-center space-y-2">
                <i data-lucide="calendar-off" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">No scheduled viewing leads recorded yet.</div>
              </div>
            <?php else: ?>
              <table class="w-full text-left border-collapse min-w-[550px]">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                    <th class="px-6 py-4">Property</th>
                    <th class="px-6 py-4">Schedule Date/Time</th>
                    <th class="px-6 py-4">Customer Phone</th>
                    <th class="px-6 py-4">Assigned Inspector</th>
                    <th class="px-6 py-4 text-right">Lead Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700 bg-white">
                  <?php foreach ($visits as $v): ?>
                    <tr class="hover:bg-slate-50/40 transition-colors">
                      <td class="px-6 py-4">
                        <div class="font-black text-slate-800 truncate max-w-[180px]"><?php echo htmlspecialchars($v['property_title']); ?></div>
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">ID: <?php echo htmlspecialchars($v['listing_id']); ?></div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="font-extrabold text-slate-800"><?php echo htmlspecialchars(date('d M Y', strtotime($v['visit_date']))); ?></div>
                        <div class="text-[9px] text-slate-400 font-bold mt-0.5 uppercase"><?php echo htmlspecialchars($v['time_slot']); ?></div>
                      </td>
                      <td class="px-6 py-4 text-slate-500 font-extrabold">
                        <?php echo htmlspecialchars($v['phone']); ?>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex items-center space-x-1.5">
                          <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                          <span class="font-bold text-slate-700"><?php echo htmlspecialchars($v['agent_name'] ?: 'Aniket Deshmukh'); ?></span>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-right">
                        <form method="POST" action="seller_dashboard.php" class="inline-block">
                          <input type="hidden" name="update_visit_status" value="1">
                          <input type="hidden" name="visit_id" value="<?php echo $v['id']; ?>">
                          
                          <?php
                            $status = $v['status'];
                            $badge_class = 'border-slate-200 text-slate-700 bg-slate-50';
                            if ($status === 'Confirmed') $badge_class = 'border-emerald-200 text-premium-emerald bg-emerald-50/50';
                            elseif ($status === 'Completed') $badge_class = 'border-blue-200 text-blue-600 bg-blue-50/50';
                            elseif ($status === 'Cancelled') $badge_class = 'border-red-200 text-red-600 bg-red-50/50';
                          ?>
                          
                          <select name="status" onchange="this.form.submit()" class="border rounded-xl px-2.5 py-1.5 text-[10px] font-extrabold focus:outline-none transition-all <?php echo $badge_class; ?> cursor-pointer shadow-sm">
                            <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Confirmed" <?php echo $status === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $status === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                          </select>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>

        <!-- My submitted properties -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-100 px-6 py-4.5 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800">My Listed Assets</h3>
            <span class="text-[9px] bg-slate-200 px-2 py-0.5 rounded-full font-bold text-slate-600 uppercase tracking-wider"><?php echo count($properties); ?> Total</span>
          </div>
          
          <div class="divide-y divide-slate-100 bg-white">
            <?php if (count($properties) === 0): ?>
              <div class="p-12 text-center space-y-2">
                <i data-lucide="home" class="h-8 w-8 text-slate-300 mx-auto"></i>
                <div class="text-xs font-bold text-slate-400">You have not published any properties yet.</div>
              </div>
            <?php else: ?>
              <?php foreach ($properties as $p): ?>
                <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/20 transition-colors">
                  <div class="flex items-center space-x-4">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="Img" class="h-14 w-14 rounded-2xl object-cover border border-slate-100 shrink-0 shadow-sm">
                    <div class="space-y-1">
                      <h4 class="text-sm font-black text-slate-800 line-clamp-1"><?php echo htmlspecialchars($p['title']); ?></h4>
                      <div class="flex flex-wrap gap-1.5 items-center">
                        <span class="inline-block bg-slate-100 text-slate-600 text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">
                          <?php echo htmlspecialchars($p['type']); ?>
                        </span>
                        <span class="inline-block bg-emerald-50 text-premium-emerald text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">
                          <?php echo htmlspecialchars($p['category']); ?>
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold flex items-center space-x-1 ml-1">
                          <i data-lucide="map-pin" class="h-3 w-3 text-slate-400"></i>
                          <span><?php echo htmlspecialchars($p['city_name']); ?></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center justify-between sm:justify-end space-x-4 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0">
                    <div class="text-left sm:text-right mr-1.5">
                      <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Reserve Price</div>
                      <div class="text-sm font-black text-slate-800"><?php echo htmlspecialchars($p['reserve_price']); ?></div>
                      <a href="property.php?id=<?php echo htmlspecialchars($p['id']); ?>" class="text-[10px] font-extrabold text-premium-emerald hover:text-premium-emeraldHover hover:underline mt-0.5 inline-flex items-center space-x-0.5">
                        <span>View Page</span>
                        <i data-lucide="arrow-up-right" class="h-3 w-3"></i>
                      </a>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                      <!-- Edit Button -->
                      <button onclick='openEditPropertyModal(<?php echo json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center space-x-1">
                        <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                        <span>Edit</span>
                      </button>
                      
                      <!-- Delete Form -->
                      <form method="POST" action="seller_dashboard.php" onsubmit="return confirm('Delete this property permanently?');" class="inline-block">
                        <input type="hidden" name="delete_property" value="1">
                        <input type="hidden" name="property_id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="px-3 py-2 bg-red-50 hover:bg-red-100 border border-red-100 text-red-600 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center space-x-1">
                          <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                          <span>Delete</span>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div> <!-- End lg:col-span-2 right column -->
    </div> <!-- End grid -->
    </div> <!-- End tab-overview -->

    <!-- Property Analytics Tab -->
    <div id="tab-analytics" class="hidden space-y-8 mt-10">
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8">
        <div class="space-y-1 mb-8">
          <h3 class="text-xl font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="bar-chart-2" class="h-6 w-6 text-blue-600"></i>
            <span>Property Performance Analytics</span>
          </h3>
          <p class="text-sm font-semibold text-slate-500">Track views, impressions, and click-through rates across your entire portfolio.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
            <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mb-2">Total Impressions</div>
            <div class="text-3xl font-black text-slate-800">42,509</div>
            <div class="text-xs font-bold text-premium-emerald mt-2 flex items-center space-x-1">
              <i data-lucide="trending-up" class="h-3 w-3"></i>
              <span>+14.2% from last week</span>
            </div>
          </div>
          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
            <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mb-2">Unique Views</div>
            <div class="text-3xl font-black text-slate-800">8,102</div>
            <div class="text-xs font-bold text-premium-emerald mt-2 flex items-center space-x-1">
              <i data-lucide="trending-up" class="h-3 w-3"></i>
              <span>+8.7% from last week</span>
            </div>
          </div>
          <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
            <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mb-2">Lead Conversion Rate</div>
            <div class="text-3xl font-black text-slate-800">3.4%</div>
            <div class="text-xs font-bold text-red-500 mt-2 flex items-center space-x-1">
              <i data-lucide="trending-down" class="h-3 w-3"></i>
              <span>-0.2% from last week</span>
            </div>
          </div>
        </div>

        <div class="h-64 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center">
          <div class="text-center">
            <i data-lucide="line-chart" class="h-10 w-10 text-slate-300 mx-auto mb-2"></i>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chart rendering engine loading...</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Account & KYC Tab -->
    <div id="tab-kyc" class="hidden space-y-8 mt-10">
      <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-8 max-w-2xl mx-auto">
        <div class="space-y-1 mb-8">
          <h3 class="text-xl font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="shield" class="h-6 w-6 text-amber-500"></i>
            <span>KYC & Verification</span>
          </h3>
          <p class="text-sm font-semibold text-slate-500">Government ID verification is required to receive auction payouts and platform clearance.</p>
        </div>

        <div class="bg-amber-50 border border-amber-100 text-amber-700 px-5 py-4 rounded-2xl text-xs font-bold flex items-start space-x-3 shadow-sm mb-6">
          <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5"></i>
          <p>Your account is currently in <strong>Provisional Mode</strong>. You can list properties and receive leads, but payouts will be held until KYC is approved by the admin team.</p>
        </div>

        <form class="space-y-5">
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Business Name / Legal Entity</label>
            <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-amber-400 focus:bg-white transition-all font-semibold text-slate-800" placeholder="e.g. Sharma Properties LLC">
          </div>
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">GST Number (Optional)</label>
            <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-amber-400 focus:bg-white transition-all font-semibold text-slate-800" placeholder="27XXXXX0000X1Z5">
          </div>
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Upload PAN Card (PDF/JPEG)</label>
            <div class="w-full px-4 py-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl text-center cursor-pointer hover:bg-slate-100 transition-colors">
              <i data-lucide="upload-cloud" class="h-6 w-6 text-slate-400 mx-auto mb-2"></i>
              <span class="text-xs font-bold text-slate-500">Click to upload document</span>
            </div>
          </div>
          <button type="button" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-sm font-extrabold shadow-md transition-all">Submit Documents for Review</button>
        </form>
      </div>
    </div>

  <?php endif; ?>
</div>

<!-- ==================== PROPERTY EDIT MODAL ==================== -->
<div id="edit-property-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden transition-all duration-300">
  <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeEditPropertyModal()"></div>
  <div class="relative bg-white/95 backdrop-blur rounded-[32px] w-full max-w-lg max-h-[90vh] overflow-hidden shadow-2xl border border-slate-100 z-10 flex flex-col text-left transform scale-95 transition-transform duration-300">
    
    <div class="bg-gradient-to-br from-emerald-600 to-teal-800 px-6 py-6 text-white relative overflow-hidden shrink-0">
      <div class="absolute -right-8 -top-8 w-24 h-24 bg-teal-400/20 rounded-full blur-xl"></div>
      <button onclick="closeEditPropertyModal()" class="absolute top-4 right-4 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-full transition-all hover:rotate-90">
        <i data-lucide="x" class="h-4.5 w-4.5"></i>
      </button>
      <h2 class="text-xl font-black tracking-tight leading-none">Edit Asset Specifications</h2>
      <p class="text-emerald-100/80 text-xs mt-1.5 font-medium">Modify listed pricing structures, categories, or details.</p>
    </div>
    
    <form method="POST" action="seller_dashboard.php" class="p-6 space-y-4 overflow-y-auto bg-white">
      <input type="hidden" name="edit_property" value="1">
      <input type="hidden" id="edit-prop-id" name="property_id">
      
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Title</label>
        <input type="text" id="edit-title" name="title" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Address Details</label>
        <input type="text" id="edit-address" name="address" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
      </div>

      <div class="grid grid-cols-2 gap-3.5">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">City Location</label>
          <select id="edit-city-id" name="city_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
            <?php foreach ($cities as $c): ?>
              <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
          <select id="edit-type" name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
            <option value="Residential">Residential</option>
            <option value="Commercial">Commercial</option>
            <option value="Industrial">Industrial</option>
            <option value="Agricultural">Agricultural</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3.5">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Listing Category</label>
          <select id="edit-category" name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
            <option value="Seller Listed">Seller Listed</option>
            <option value="Auction">Foreclosure Auction</option>
            <option value="Rental">Premium Rental</option>
            <option value="Heavy Deposit">Heavy Deposit</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Reserve Price (₹)</label>
          <input type="text" id="edit-price" name="price" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3.5">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Earnest Money (EMD)</label>
          <input type="text" id="edit-emd" name="emd" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Ready Reckoner Val</label>
          <input type="text" id="edit-gov-val" name="gov_val" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
        </div>
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Image URL Address</label>
        <input type="text" id="edit-image" name="image" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold">
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Detailed Specifications</label>
        <textarea id="edit-details" name="details" rows="3" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold"></textarea>
      </div>

      <button type="submit" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3.5 rounded-xl text-xs font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-500/10 flex items-center justify-center space-x-1.5 active:scale-[0.98] touch-target">
        <i data-lucide="save" class="h-4.5 w-4.5"></i>
        <span>Save Asset Changes</span>
      </button>
    </form>
  </div>
</div>

<script>
  function openEditPropertyModal(prop) {
    document.getElementById('edit-prop-id').value = prop.id;
    document.getElementById('edit-title').value = prop.title;
    document.getElementById('edit-address').value = prop.address;
    document.getElementById('edit-city-id').value = prop.city_id;
    document.getElementById('edit-type').value = prop.type;
    document.getElementById('edit-category').value = prop.category;
    
    // strip Rupee symbol if present to keep it easy to input
    let cleanPrice = prop.reserve_price.replace('₹', '').trim();
    document.getElementById('edit-price').value = cleanPrice;
    
    let cleanEmd = prop.emd.replace('₹', '').trim();
    document.getElementById('edit-emd').value = cleanEmd;
    
    let cleanGov = prop.government_valuation ? prop.government_valuation.replace('₹', '').trim() : '';
    document.getElementById('edit-gov-val').value = cleanGov;
    
    document.getElementById('edit-image').value = prop.image;
    document.getElementById('edit-details').value = prop.details;

    const modal = document.getElementById('edit-property-modal');
    const modalContent = modal.querySelector('.relative.bg-white\\/95');
    modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
    setTimeout(() => {
      modalContent.classList.remove('scale-95');
      modalContent.classList.add('scale-100');
    }, 10);
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeEditPropertyModal() {
    const modal = document.getElementById('edit-property-modal');
    const modalContent = modal.querySelector('.relative.bg-white\\/95');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.classList.remove('modal-open');
    }, 150);
  }
</script>

<script>
  function switchSellerTab(tab) {
    const tabs = ['overview', 'analytics', 'kyc'];
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
    if (tab && ['overview', 'analytics', 'kyc'].includes(tab)) {
      switchSellerTab(tab);
    }
  });
</script>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
