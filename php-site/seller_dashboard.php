<?php
// php-site/seller_dashboard.php
require_once 'config/db.php';

// Check if user is logged in and has seller role
$is_authorized = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'seller';
$seller_id = $is_authorized ? $_SESSION['user']['id'] : 0;

$success_msg = '';
$error_msg = '';

// Handle POST actions
if ($is_authorized && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Publish Property Listing
    if (isset($_POST['submit_property'])) {
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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  
  <?php if (!$is_authorized): ?>
    <!-- Locked Unauthorized State -->
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-md mx-auto space-y-6 shadow-xl">
      <div class="mx-auto h-16 w-16 bg-red-50 border border-red-100 rounded-2xl flex items-center justify-center text-red-500">
        <i data-lucide="lock" class="h-8 w-8"></i>
      </div>
      <div>
        <h3 class="text-xl font-black text-slate-800">Seller Dashboard Locked</h3>
        <p class="text-xs text-slate-500 font-semibold mt-1">Please log in or register as a certified seller to access listing submissions and customer leads.</p>
      </div>
      <button onclick="openAuthModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-all">
        Sign In / Register
      </button>
    </div>

  <?php else: ?>
    <!-- Authorized Dashboard Layout -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="layout-dashboard" class="h-8 w-8 text-premium-emerald animate-pulse"></i>
          <span>Seller Control Command Center</span>
        </h1>
        <p class="text-xs text-slate-500 font-semibold mt-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>. Submit listings and review client leads.</p>
      </div>
      
      <!-- Stats Summary -->
      <div class="flex space-x-4">
        <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-3">
          <div class="h-9 w-9 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald">
            <i data-lucide="home" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase">My Listings</span>
            <span class="text-base font-black text-slate-800"><?php echo count($properties); ?></span>
          </div>
        </div>
        
        <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-3">
          <div class="h-9 w-9 bg-emerald-50 rounded-xl flex items-center justify-center text-premium-emerald">
            <i data-lucide="calendar" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase">Site Leads</span>
            <span class="text-base font-black text-slate-800"><?php echo count($visits); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($success_msg)): ?>
      <div class="bg-emerald-50 border border-emerald-200 text-premium-emerald px-4 py-3 rounded-2xl text-xs font-bold flex items-center space-x-2">
        <i data-lucide="check-circle" class="h-5 w-5"></i>
        <span><?php echo htmlspecialchars($success_msg); ?></span>
      </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-xs font-bold flex items-center space-x-2">
        <i data-lucide="alert-circle" class="h-5 w-5"></i>
        <span><?php echo htmlspecialchars($error_msg); ?></span>
      </div>
    <?php endif; ?>

    <!-- Two-column content: Left Form, Right listings list and leads -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- List new property form -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 space-y-6">
        <div>
          <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="plus-circle" class="h-5 w-5 text-premium-emerald"></i>
            <span>Submit Asset Listing</span>
          </h3>
          <p class="text-xs text-slate-500 font-semibold mt-1">Publish new foreclosures or rentals to the directory.</p>
        </div>

        <form method="POST" action="seller_dashboard.php" class="space-y-4">
          <input type="hidden" name="submit_property" value="1">
          
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Property Title</label>
            <input type="text" name="title" required placeholder="e.g. 2 BHK Modern Flat" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Address Details</label>
            <input type="text" name="address" required placeholder="e.g. Flat 302, Sector 12, Thane" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">City Location</label>
              <select name="city_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
                <?php foreach ($cities as $c): ?>
                  <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Property Type</label>
              <select name="type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
                <option value="Residential">Residential</option>
                <option value="Commercial">Commercial</option>
                <option value="Industrial">Industrial</option>
                <option value="Agricultural">Agricultural</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Listing Category</label>
              <select name="category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
                <option value="Seller Listed">Seller Listed</option>
                <option value="Auction">Foreclosure Auction</option>
                <option value="Rental">Premium Rental</option>
                <option value="Heavy Deposit">Heavy Deposit</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Reserve Price (₹)</label>
              <input type="text" name="price" required placeholder="e.g. 85,00,000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Earnest Money (EMD)</label>
              <input type="text" name="emd" placeholder="e.g. 8,50,000 (Optional)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Ready Reckoner Val</label>
              <input type="text" name="gov_val" placeholder="e.g. 1,00,00,000 (Optional)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Image URL Address</label>
            <input type="text" name="image" placeholder="https://domain.com/image.jpg (Optional)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Detailed Specifications</label>
            <textarea name="details" rows="3" required placeholder="Building amenities, carpet area, legal clearances descriptions..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold"></textarea>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all">
            Publish Property Listing
          </button>
        </form>
      </div>

      <!-- Listings and Leads lists -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Site visit leads board -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden">
          <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
            <h3 class="text-sm font-black flex items-center space-x-2">
              <i data-lucide="users" class="h-4.5 w-4.5 text-emerald-400 animate-pulse"></i>
              <span>Customer Site Visit Leads Board</span>
            </h3>
          </div>
          
          <div class="overflow-x-auto">
            <?php if (count($visits) === 0): ?>
              <div class="p-8 text-center text-xs font-semibold text-slate-400">
                No scheduled viewing leads recorded for your properties yet.
              </div>
            <?php else: ?>
              <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                    <th class="px-6 py-3">Property</th>
                    <th class="px-6 py-3">Schedule Date/Time</th>
                    <th class="px-6 py-3">Customer Phone</th>
                    <th class="px-6 py-3">Assigned Inspector</th>
                    <th class="px-6 py-3">Lead Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                  <?php foreach ($visits as $v): ?>
                    <tr class="hover:bg-slate-50/50">
                      <td class="px-6 py-3 font-bold text-slate-800">
                        <div class="truncate max-w-[180px]"><?php echo htmlspecialchars($v['property_title']); ?></div>
                        <div class="text-[9px] text-slate-400 mt-0.5"><?php echo htmlspecialchars($v['listing_id']); ?></div>
                      </td>
                      <td class="px-6 py-3">
                        <div><?php echo htmlspecialchars(date('d M Y', strtotime($v['visit_date']))); ?></div>
                        <div class="text-[9px] text-slate-400 mt-0.5"><?php echo htmlspecialchars($v['time_slot']); ?></div>
                      </td>
                      <td class="px-6 py-3 font-bold text-slate-500">
                        <?php echo htmlspecialchars($v['phone']); ?>
                      </td>
                      <td class="px-6 py-3 font-bold text-premium-emerald">
                        <?php echo htmlspecialchars($v['agent_name'] ?: 'Aniket Deshmukh'); ?>
                      </td>
                      <td class="px-6 py-3">
                        <form method="POST" action="seller_dashboard.php">
                          <input type="hidden" name="update_visit_status" value="1">
                          <input type="hidden" name="visit_id" value="<?php echo $v['id']; ?>">
                          <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-1 text-[11px] font-bold text-slate-600 focus:outline-none">
                            <option value="Pending" <?php echo $v['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Confirmed" <?php echo $v['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Completed" <?php echo $v['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $v['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
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
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden">
          <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
            <h3 class="text-sm font-black text-slate-800">My Listed Assets</h3>
          </div>
          
          <div class="divide-y divide-slate-100">
            <?php if (count($properties) === 0): ?>
              <div class="p-8 text-center text-xs font-semibold text-slate-400">
                You have not listed any properties yet.
              </div>
            <?php else: ?>
              <?php foreach ($properties as $p): ?>
                <div class="p-6 flex items-center justify-between gap-4">
                  <div class="flex items-center space-x-4">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="Img" class="h-12 w-12 rounded-xl object-cover border border-slate-100 shrink-0">
                    <div>
                      <h4 class="text-sm font-black text-slate-800 line-clamp-1"><?php echo htmlspecialchars($p['title']); ?></h4>
                      <span class="inline-block bg-slate-100 text-slate-600 text-[9px] font-bold px-1.5 py-0.5 rounded mt-1 uppercase">
                        <?php echo htmlspecialchars($p['type']); ?> • <?php echo htmlspecialchars($p['category']); ?>
                      </span>
                    </div>
                  </div>
                  <div class="flex items-center space-x-3 shrink-0">
                    <div class="text-right mr-2">
                      <div class="text-sm font-black text-slate-800"><?php echo htmlspecialchars($p['reserve_price']); ?></div>
                      <a href="property.php?id=<?php echo htmlspecialchars($p['id']); ?>" class="text-[10px] font-bold text-premium-emerald hover:underline mt-1 inline-block">
                        View Page
                      </a>
                    </div>
                    
                    <!-- Edit Button -->
                    <button onclick='openEditPropertyModal(<?php echo json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                      Edit
                    </button>
                    
                    <!-- Delete Form -->
                    <form method="POST" action="seller_dashboard.php" onsubmit="return confirm('Delete this property permanently?');">
                      <input type="hidden" name="delete_property" value="1">
                      <input type="hidden" name="property_id" value="<?php echo $p['id']; ?>">
                      <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold rounded-xl transition-all">
                        Delete
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

  <?php endif; ?>
</div>

<!-- ==================== PROPERTY EDIT MODAL ==================== -->
<div id="edit-property-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditPropertyModal()"></div>
  <div class="relative bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
    <div class="bg-gradient-to-r from-premium-emerald to-teal-600 px-6 py-6 text-white relative">
      <button onclick="closeEditPropertyModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition-colors">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
      <h2 class="text-xl font-extrabold tracking-tight">Edit Asset Specifications</h2>
      <p class="text-emerald-100 text-xs mt-1">Modify listed price, category tags, or specifications details.</p>
    </div>
    
    <form method="POST" action="seller_dashboard.php" class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
      <input type="hidden" name="edit_property" value="1">
      <input type="hidden" id="edit-prop-id" name="property_id">
      
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Title</label>
        <input type="text" id="edit-title" name="title" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Address Details</label>
        <input type="text" id="edit-address" name="address" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">City Location</label>
          <select id="edit-city-id" name="city_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
            <?php foreach ($cities as $c): ?>
              <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
          <select id="edit-type" name="type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
            <option value="Residential">Residential</option>
            <option value="Commercial">Commercial</option>
            <option value="Industrial">Industrial</option>
            <option value="Agricultural">Agricultural</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Listing Category</label>
          <select id="edit-category" name="category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
            <option value="Seller Listed">Seller Listed</option>
            <option value="Auction">Foreclosure Auction</option>
            <option value="Rental">Premium Rental</option>
            <option value="Heavy Deposit">Heavy Deposit</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Reserve Price (₹)</label>
          <input type="text" id="edit-price" name="price" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Earnest Money (EMD)</label>
          <input type="text" id="edit-emd" name="emd" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Ready Reckoner Val</label>
          <input type="text" id="edit-gov-val" name="gov_val" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
        </div>
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Image URL Address</label>
        <input type="text" id="edit-image" name="image" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Detailed Specifications</label>
        <textarea id="edit-details" name="details" rows="3" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold"></textarea>
      </div>

      <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all">
        Save Asset Changes
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

    document.getElementById('edit-property-modal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeEditPropertyModal() {
    document.getElementById('edit-property-modal').classList.add('hidden');
  }
</script>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
