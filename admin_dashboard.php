<?php
// admin_dashboard.php
require_once 'config/db.php';

// Check if user is logged in and has admin role
$is_admin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';

$success_msg = '';
$error_msg = '';

if ($is_admin) {
    // Handle Admin Post Requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Delete User
        if (isset($_POST['delete_user'])) {
            $user_id = (int)$_POST['user_id'];
            if ($user_id !== $_SESSION['user']['id']) { // Don't delete self
                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $success_msg = "User successfully removed.";
                } catch (PDOException $e) {
                    $error_msg = "Failed to delete user: " . $e->getMessage();
                }
            } else {
                $error_msg = "You cannot delete your own admin account.";
            }
        }
        
        // 2. Change User Role
        if (isset($_POST['change_role'])) {
            $user_id = (int)$_POST['user_id'];
            $new_role = trim($_POST['role']);
            if (in_array($new_role, ['buyer', 'seller', 'agent', 'admin'])) {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$new_role, $user_id]);
                    $success_msg = "User role updated to " . ucfirst($new_role) . ".";
                } catch (PDOException $e) {
                    $error_msg = "Failed to update user role: " . $e->getMessage();
                }
            }
        }

        // 3. Delete Property
        if (isset($_POST['delete_property'])) {
            $property_id = trim($_POST['property_id']);
            try {
                // Fetch city_id first to decrement count
                $p_stmt = $pdo->prepare("SELECT city_id FROM properties WHERE id = ?");
                $p_stmt->execute([$property_id]);
                $p = $p_stmt->fetch();
                if ($p) {
                    $pdo->prepare("UPDATE cities SET property_count = GREATEST(0, property_count - 1) WHERE id = ?")->execute([$p['city_id']]);
                }
                
                $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
                $stmt->execute([$property_id]);
                $success_msg = "Property listing deleted successfully.";
            } catch (PDOException $e) {
                $error_msg = "Failed to delete property: " . $e->getMessage();
            }
        }

        // 4. Update Property Agent
        if (isset($_POST['assign_agent'])) {
            $property_id = trim($_POST['property_id']);
            $agent_id = trim($_POST['agent_id']);
            if (empty($agent_id)) $agent_id = null;
            try {
                $stmt = $pdo->prepare("UPDATE properties SET agent_id = ? WHERE id = ?");
                $stmt->execute([$agent_id, $property_id]);
                $success_msg = "Property agent assignment updated.";
            } catch (PDOException $e) {
                $error_msg = "Failed to assign agent: " . $e->getMessage();
            }
        }

        // 5. Delete Lead
        if (isset($_POST['delete_lead'])) {
            $lead_id = (int)$_POST['lead_id'];
            $type = trim($_POST['lead_type']);
            try {
                if ($type === 'site_visit') {
                    $stmt = $pdo->prepare("DELETE FROM site_visits WHERE id = ?");
                } elseif ($type === 'agent_connect') {
                    $stmt = $pdo->prepare("DELETE FROM agent_connections WHERE id = ?");
                } elseif ($type === 'consultation') {
                    $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
                } else {
                    $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
                }
                $stmt->execute([$lead_id]);
                $success_msg = "Lead log deleted.";
            } catch (PDOException $e) {
                $error_msg = "Failed to delete lead: " . $e->getMessage();
            }
        }

        // 6. Update Lead Status
        if (isset($_POST['update_lead_status'])) {
            $visit_id = (int)$_POST['visit_id'];
            $new_status = trim($_POST['status']);
            try {
                $stmt = $pdo->prepare("UPDATE site_visits SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $visit_id]);
                $success_msg = "Inspection status updated to: " . $new_status;
            } catch (PDOException $e) {
                $error_msg = "Failed to update inspection status: " . $e->getMessage();
            }
        }

        // 7. Add Verified Agent
        if (isset($_POST['add_agent'])) {
            $id = 'agt-' . rand(100, 999);
            $name = trim($_POST['name']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);
            $specialty = trim($_POST['specialty']);
            $rating = (float)$_POST['rating'] ?: 4.80;
            $image = trim($_POST['image']) ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80';

            if (!empty($name) && !empty($phone) && !empty($email)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO agents (id, name, phone, email, rating, specialty, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$id, $name, $phone, $email, $rating, $specialty, $image]);
                    $success_msg = "Agent '{$name}' registered successfully.";
                } catch (PDOException $e) {
                    $error_msg = "Failed to add agent: " . $e->getMessage();
                }
            } else {
                $error_msg = "Please fill in all agent fields.";
            }
        }
        
        // 8. Admin Post Property
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

            if (empty($title) || empty($address) || empty($city_id) || empty($price) || empty($details)) {
                $error_msg = 'Please complete all required fields.';
            } else {
                try {
                    $listing_id = 'MAHA-' . rand(100000, 999999);
                    $numeric_price = (int)preg_replace('/[^0-9]/', '', $price) ?: 1000000;
                    $numeric_gov_val = (int)preg_replace('/[^0-9]/', '', $gov_val) ?: ($numeric_price * 1.2);

                    $stmt = $pdo->prepare("
                        INSERT INTO properties (
                            listing_id, seller_id, title, address, city_id, type, category, 
                            reserve_price, numeric_price, emd, government_valuation, 
                            numeric_gov_valuation, details
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $listing_id, $_SESSION['user']['id'], $title, $address, $city_id, $type, $category, 
                        $price, $numeric_price, $emd ?: 'N/A', 
                        $gov_val ? "₹ {$gov_val}" : "₹ " . ($numeric_gov_val / 10000000) . " Cr", 
                        $numeric_gov_val, $details
                    ]);

                    // Update city property count
                    $pdo->prepare("UPDATE cities SET property_count = property_count + 1 WHERE id = ?")->execute([$city_id]);

                    $success_msg = 'Property successfully posted to the platform!';
                } catch (PDOException $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }

    // Fetch Stats
    $total_properties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_visits = $pdo->query("SELECT COUNT(*) FROM site_visits")->fetchColumn();
    $total_consults = $pdo->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
    $total_connections = $pdo->query("SELECT COUNT(*) FROM agent_connections")->fetchColumn();
    $total_general_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();

    // Fetch Users
    $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

    // Fetch Listings
    $listings = $pdo->query("
        SELECT p.*, c.name as city_name, u.name as seller_name, u.email as seller_email 
        FROM properties p 
        JOIN cities c ON p.city_id = c.id 
        LEFT JOIN users u ON p.seller_id = u.id 
        ORDER BY p.created_at DESC
    ")->fetchAll();

    // Fetch Leads
    $visits = $pdo->query("
        SELECT v.*, p.title as property_title, p.listing_id, a.name as agent_name 
        FROM site_visits v 
        JOIN properties p ON v.property_id = p.id 
        LEFT JOIN agents a ON v.agent_id = a.id 
        ORDER BY v.created_at DESC
    ")->fetchAll();

    $connections = $pdo->query("
        SELECT c.*, a.name as agent_name 
        FROM agent_connections c 
        JOIN agents a ON c.agent_id = a.id 
        ORDER BY c.created_at DESC
    ")->fetchAll();

    $consults = $pdo->query("
        SELECT * FROM consultations ORDER BY created_at DESC
    ")->fetchAll();

    $general_leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();

    // Fetch Agents and Cities
    $agents = $pdo->query("SELECT * FROM agents ORDER BY name ASC")->fetchAll();
    $cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  <?php if (!$is_admin): ?>
    <!-- Unauthorized locked view -->
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-md mx-auto space-y-6 shadow-xl">
      <div class="mx-auto h-16 w-16 bg-red-50 border border-red-100 rounded-2xl flex items-center justify-center text-red-500">
        <i data-lucide="shield-alert" class="h-8 w-8"></i>
      </div>
      <div>
        <h3 class="text-xl font-black text-slate-800">Admin Area Access Denied</h3>
        <p class="text-xs text-slate-500 font-semibold mt-1">This dashboard is restricted to authorized platform administrators only.</p>
      </div>
      <button onclick="openAuthModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-all">
        Admin Verification Login
      </button>
    </div>
  <?php else: ?>
    
    <!-- Admin Header Banner -->
    <div class="flex flex-col gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="shield-check" class="h-7 w-7 sm:h-8 sm:w-8 text-premium-emerald animate-pulse shrink-0"></i>
          <span>Global Administration Command Center</span>
        </h1>
        <p class="text-xs text-slate-500 font-semibold mt-1">
          Authorized Admin: <?php echo htmlspecialchars($_SESSION['user']['name']); ?> (<?php echo htmlspecialchars($_SESSION['user']['email']); ?>)
        </p>
      </div>
      
      <!-- Mobile-friendly horizontally scrollable tab navigation -->
      <div class="-mx-4 sm:mx-0">
        <div class="flex overflow-x-auto scrollbar-hide gap-2 px-4 sm:px-0 pb-1">
          <button onclick="switchAdminTab('stats')" class="admin-nav-btn shrink-0 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm transition-all whitespace-nowrap" data-tab="stats">Overview</button>
          <button onclick="switchAdminTab('users')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="users">Users</button>
          <button onclick="switchAdminTab('listings')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="listings">Properties</button>
          <button onclick="switchAdminTab('add_property')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="add_property">Post Property</button>
          <button onclick="switchAdminTab('leads')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="leads">Leads Board</button>
          <button onclick="switchAdminTab('consults')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="consults">Consultations</button>
          <button onclick="switchAdminTab('agents')" class="admin-nav-btn shrink-0 px-4 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all whitespace-nowrap" data-tab="agents">Agents & Cities</button>
        </div>
      </div>
    </div>

    <!-- Feedback messages -->
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

    <!-- ==================== TABS CONTAINER ==================== -->
    
    <!-- TAB 1: OVERVIEW STATS -->
    <div id="tab-stats" class="admin-tab-content space-y-8">
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Card 1 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-emerald-50 text-premium-emerald rounded-xl flex items-center justify-center">
            <i data-lucide="home" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Properties</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_properties; ?></span>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center">
            <i data-lucide="users" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Users</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_users; ?></span>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center">
            <i data-lucide="calendar" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Site Visits</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_visits; ?></span>
          </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
            <i data-lucide="gavel" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Consultations</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_consults; ?></span>
          </div>
        </div>
        <!-- Card 5 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
            <i data-lucide="message-square" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Agent Queries</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_connections; ?></span>
          </div>
        </div>
        <!-- Card 6 -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
          <div class="h-9 w-9 bg-cyan-50 text-cyan-500 rounded-xl flex items-center justify-center">
            <i data-lucide="mail" class="h-5 w-5"></i>
          </div>
          <div>
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Brochure leads</span>
            <span class="text-2xl font-black text-slate-800"><?php echo $total_general_leads; ?></span>
          </div>
        </div>
      </div>

      <!-- Quick Summary Tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users Quick View -->
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Recent Users Registry</h3>
            <button onclick="switchAdminTab('users')" class="text-[10px] font-bold text-premium-emerald hover:underline">View All</button>
          </div>
          <div class="p-6 divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
            <?php foreach (array_slice($users, 0, 5) as $u): ?>
              <div class="py-2.5 flex justify-between items-center text-xs">
                <div class="flex items-center space-x-3">
                  <img src="<?php echo htmlspecialchars($u['avatar'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80'); ?>" class="h-8 w-8 rounded-full">
                  <div>
                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars($u['name']); ?></div>
                    <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($u['email']); ?></div>
                  </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase <?php 
                  echo $u['role'] === 'admin' ? 'bg-red-50 text-red-600' : ($u['role'] === 'seller' ? 'bg-emerald-50 text-premium-emerald' : 'bg-slate-100 text-slate-600');
                ?>"><?php echo htmlspecialchars($u['role']); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Recent Inspections View -->
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Recent Site Inspection Leads</h3>
            <button onclick="switchAdminTab('leads')" class="text-[10px] font-bold text-premium-emerald hover:underline">View All</button>
          </div>
          <div class="p-6 divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
            <?php if (count($visits) === 0): ?>
              <div class="p-6 text-center text-slate-400 font-semibold text-xs">No site visits scheduled yet.</div>
            <?php else: ?>
              <?php foreach (array_slice($visits, 0, 5) as $v): ?>
                <div class="py-2.5 flex justify-between items-center text-xs">
                  <div>
                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars($v['property_title']); ?></div>
                    <div class="text-[10px] text-slate-400">Date: <?php echo date('d M Y', strtotime($v['visit_date'])); ?> | Phone: <?php echo htmlspecialchars($v['phone']); ?></div>
                  </div>
                  <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase <?php 
                    echo $v['status'] === 'Confirmed' ? 'bg-emerald-50 text-premium-emerald' : ($v['status'] === 'Cancelled' ? 'bg-red-50 text-red-500' : 'bg-amber-50 text-amber-600');
                  ?>"><?php echo htmlspecialchars($v['status']); ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: USERS MANAGER -->
    <div id="tab-users" class="admin-tab-content hidden bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
      <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-sm font-black text-slate-800">User Accounts Management</h3>
      </div>
      <div class="responsive-table-wrapper">
        <table class="w-full text-left border-collapse min-w-[700px]">
          <thead>
            <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
              <th class="px-6 py-3">User Profile</th>
              <th class="px-6 py-3">Email Address</th>
              <th class="px-6 py-3">Current Role</th>
              <th class="px-6 py-3">Subscription / KYC</th>
              <th class="px-6 py-3">Registered At</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
            <?php foreach ($users as $u): ?>
              <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-3 flex items-center space-x-3">
                  <img src="<?php echo htmlspecialchars($u['avatar'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80'); ?>" class="h-9 w-9 rounded-full border border-slate-100 shadow-sm shrink-0">
                  <span class="font-bold text-slate-800"><?php echo htmlspecialchars($u['name']); ?></span>
                </td>
                <td class="px-6 py-3 text-slate-500"><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="px-6 py-3">
                  <form method="POST" action="admin_dashboard.php" class="flex items-center space-x-2">
                    <input type="hidden" name="change_role" value="1">
                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                    <select name="role" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-1 text-[11px] font-bold text-slate-600 focus:outline-none">
                      <option value="buyer" <?php echo $u['role'] === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                      <option value="seller" <?php echo $u['role'] === 'seller' ? 'selected' : ''; ?>>Seller</option>
                      <option value="lawyer" <?php echo $u['role'] === 'lawyer' ? 'selected' : ''; ?>>Lawyer</option>
                      <option value="agent" <?php echo $u['role'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
                      <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                  </form>
                </td>
                <td class="px-6 py-3">
                  <?php if ($u['role'] === 'seller'): ?>
                    <div class="flex items-center space-x-2">
                      <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-600">Pending KYC</span>
                      <button class="text-[10px] text-premium-emerald hover:underline font-bold" onclick="alert('Subscription approval logic would execute here for seller KYC.')">Approve</button>
                    </div>
                  <?php elseif ($u['role'] === 'lawyer'): ?>
                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-premium-emerald">Verified</span>
                  <?php else: ?>
                    <span class="text-[10px] text-slate-400 font-semibold">N/A</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-3 text-slate-400 text-[10px]"><?php echo date('d M Y H:i', strtotime($u['created_at'])); ?></td>
                <td class="px-6 py-3 text-right">
                  <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                    <input type="hidden" name="delete_user" value="1">
                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1 <?php echo $u['id'] === $_SESSION['user']['id'] ? 'opacity-30 cursor-not-allowed' : ''; ?>" <?php echo $u['id'] === $_SESSION['user']['id'] ? 'disabled' : ''; ?>>
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: LISTINGS MANAGER -->
    <div id="tab-listings" class="admin-tab-content hidden bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
      <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
        <h3 class="text-sm font-black text-slate-800">Global Property Directory</h3>
      </div>
      <div class="responsive-table-wrapper">
        <table class="w-full text-left border-collapse min-w-[800px]">
          <thead>
            <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
              <th class="px-6 py-3">Property details</th>
              <th class="px-6 py-3">Location & Type</th>
              <th class="px-6 py-3">Price Specification</th>
              <th class="px-6 py-3">Seller / Owner</th>
              <th class="px-6 py-3">Assigned Agent</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
            <?php foreach ($listings as $l): ?>
              <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-3 flex items-center space-x-3">
                  <img src="<?php echo htmlspecialchars($l['image']); ?>" class="h-10 w-10 rounded-xl object-cover shrink-0">
                  <div>
                    <div class="font-bold text-slate-800 line-clamp-1 max-w-[200px]"><?php echo htmlspecialchars($l['title']); ?></div>
                    <div class="text-[9px] text-slate-400 mt-0.5"><?php echo htmlspecialchars($l['listing_id']); ?></div>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <div class="font-bold text-slate-800"><?php echo htmlspecialchars($l['city_name']); ?></div>
                  <div class="text-[9px] text-slate-500 font-bold bg-slate-100 rounded px-1.5 py-0.5 mt-0.5 inline-block uppercase"><?php echo htmlspecialchars($l['type']); ?> • <?php echo htmlspecialchars($l['category']); ?></div>
                </td>
                <td class="px-6 py-3">
                  <div class="font-bold text-slate-800"><?php echo htmlspecialchars($l['reserve_price']); ?></div>
                  <div class="text-[9px] text-slate-400">EMD: <?php echo htmlspecialchars($l['emd']); ?></div>
                </td>
                <td class="px-6 py-3">
                  <div class="font-bold text-slate-800"><?php echo htmlspecialchars($l['seller_name'] ?: 'System'); ?></div>
                  <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($l['seller_email'] ?: 'admin@mahaauctions.com'); ?></div>
                </td>
                <td class="px-6 py-3">
                  <form method="POST" action="admin_dashboard.php">
                    <input type="hidden" name="assign_agent" value="1">
                    <input type="hidden" name="property_id" value="<?php echo $l['id']; ?>">
                    <select name="agent_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-1 text-[11px] font-semibold text-slate-600 focus:outline-none">
                      <option value="">No Agent</option>
                      <?php foreach ($agents as $a): ?>
                        <option value="<?php echo $a['id']; ?>" <?php echo $l['agent_id'] === $a['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td class="px-6 py-3 text-right">
                  <div class="flex items-center justify-end space-x-3">
                    <a href="property.php?id=<?php echo htmlspecialchars($l['id']); ?>" target="_blank" class="text-premium-emerald hover:underline font-bold p-1">View</a>
                    <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Are you sure you want to delete this property listing? This cannot be undone.');">
                      <input type="hidden" name="delete_property" value="1">
                      <input type="hidden" name="property_id" value="<?php echo $l['id']; ?>">
                      <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 4: LEADS MANAGER -->
    <div id="tab-leads" class="admin-tab-content hidden space-y-8">
      
      <!-- Site Visits Log -->
      <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-800">Physical & Symbolic Site Inspection Schedules</h3>
        </div>
        <div class="responsive-table-wrapper">
          <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
              <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                <th class="px-6 py-3">Property</th>
                <th class="px-6 py-3">Inspection Date</th>
                <th class="px-6 py-3">Time Slot</th>
                <th class="px-6 py-3">Client Phone</th>
                <th class="px-6 py-3">Assigned Inspector</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
              <?php foreach ($visits as $v): ?>
                <tr class="hover:bg-slate-50/50">
                  <td class="px-6 py-3">
                    <div class="font-bold text-slate-800 line-clamp-1 max-w-[200px]"><?php echo htmlspecialchars($v['property_title']); ?></div>
                    <div class="text-[9px] text-slate-400 mt-0.5"><?php echo htmlspecialchars($v['listing_id']); ?></div>
                  </td>
                  <td class="px-6 py-3"><?php echo date('d M Y', strtotime($v['visit_date'])); ?></td>
                  <td class="px-6 py-3 text-slate-500"><?php echo htmlspecialchars($v['time_slot']); ?></td>
                  <td class="px-6 py-3 font-bold text-slate-600"><?php echo htmlspecialchars($v['phone']); ?></td>
                  <td class="px-6 py-3 text-premium-emerald"><?php echo htmlspecialchars($v['agent_name'] ?: 'System'); ?></td>
                  <td class="px-6 py-3">
                    <form method="POST" action="admin_dashboard.php">
                      <input type="hidden" name="update_lead_status" value="1">
                      <input type="hidden" name="visit_id" value="<?php echo $v['id']; ?>">
                      <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-0.5 text-[10px] font-bold text-slate-600 focus:outline-none">
                        <option value="Pending" <?php echo $v['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Confirmed" <?php echo $v['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="Completed" <?php echo $v['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $v['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                      </select>
                    </form>
                  </td>
                  <td class="px-6 py-3 text-right">
                    <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Delete inspection log?');">
                      <input type="hidden" name="delete_lead" value="1">
                      <input type="hidden" name="lead_type" value="site_visit">
                      <input type="hidden" name="lead_id" value="<?php echo $v['id']; ?>">
                      <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- General Inbound leads & agent messages -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Agent Connections -->
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Partner Agent Message Inquiries</h3>
          </div>
          <div class="responsive-table-wrapper">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                  <th class="px-6 py-3">Sender Details</th>
                  <th class="px-6 py-3">Target Agent</th>
                  <th class="px-6 py-3">Inbound Query</th>
                  <th class="px-6 py-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                <?php foreach ($connections as $c): ?>
                  <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-3">
                      <div class="font-bold text-slate-800"><?php echo htmlspecialchars($c['name']); ?></div>
                      <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($c['phone']); ?></div>
                    </td>
                    <td class="px-6 py-3 text-premium-emerald"><?php echo htmlspecialchars($c['agent_name']); ?></td>
                    <td class="px-6 py-3 text-slate-500 max-w-[150px] truncate" title="<?php echo htmlspecialchars($c['message']); ?>"><?php echo htmlspecialchars($c['message']); ?></td>
                    <td class="px-6 py-3 text-right">
                      <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Delete agent connection query?');">
                        <input type="hidden" name="delete_lead" value="1">
                        <input type="hidden" name="lead_type" value="agent_connect">
                        <input type="hidden" name="lead_id" value="<?php echo $c['id']; ?>">
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- General Campaigns Leads -->
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Free Trials & Brochure Download Submissions</h3>
          </div>
          <div class="responsive-table-wrapper">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                  <th class="px-6 py-3">Client</th>
                  <th class="px-6 py-3">Campaign Channel</th>
                  <th class="px-6 py-3">Submitted At</th>
                  <th class="px-6 py-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                <?php foreach ($general_leads as $gl): ?>
                  <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-3">
                      <div class="font-bold text-slate-800"><?php echo htmlspecialchars($gl['name']); ?></div>
                      <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($gl['email']); ?></div>
                    </td>
                    <td class="px-6 py-3"><span class="bg-cyan-50 text-cyan-600 rounded px-1.5 py-0.5 text-[9px] font-bold uppercase"><?php echo htmlspecialchars($gl['campaign']); ?></span></td>
                    <td class="px-6 py-3 text-slate-400 text-[10px]"><?php echo date('d M Y', strtotime($gl['created_at'])); ?></td>
                    <td class="px-6 py-3 text-right">
                      <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Delete lead entry?');">
                        <input type="hidden" name="delete_lead" value="1">
                        <input type="hidden" name="lead_type" value="general">
                        <input type="hidden" name="lead_id" value="<?php echo $gl['id']; ?>">
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: LEGAL CONSULTATIONS -->
    <div id="tab-consults" class="admin-tab-content hidden bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
      <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-sm font-black text-slate-800">Legal Advocates Consulting Bookings</h3>
      </div>
      <div class="responsive-table-wrapper">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
              <th class="px-6 py-3">Consulting Client</th>
              <th class="px-6 py-3">Requested Advocate</th>
              <th class="px-6 py-3">Meeting Topic</th>
              <th class="px-6 py-3">Scheduled Date</th>
              <th class="px-6 py-3">Submitted At</th>
              <th class="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
            <?php foreach ($consults as $cs): ?>
              <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-3">
                  <div class="font-bold text-slate-800"><?php echo htmlspecialchars($cs['name']); ?></div>
                  <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($cs['email']); ?></div>
                </td>
                <td class="px-6 py-3 text-premium-emerald font-bold"><?php 
                  $adv_name = 'Adv. Sayali Patil';
                  if ($cs['advocate_id'] === 'adv-2') $adv_name = 'Adv. Altamash Khan';
                  if ($cs['advocate_id'] === 'adv-3') $adv_name = 'Adv. Rajendra Mane';
                  echo $adv_name; 
                ?></td>
                <td class="px-6 py-3 text-slate-600 font-bold"><?php echo htmlspecialchars($cs['topic']); ?></td>
                <td class="px-6 py-3 font-bold text-slate-700"><?php echo date('d M Y', strtotime($cs['booking_date'])); ?></td>
                <td class="px-6 py-3 text-slate-400 text-[10px]"><?php echo date('d M Y', strtotime($cs['created_at'])); ?></td>
                <td class="px-6 py-3 text-right">
                  <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Delete consultation booking record?');">
                    <input type="hidden" name="delete_lead" value="1">
                    <input type="hidden" name="lead_type" value="consultation">
                    <input type="hidden" name="lead_id" value="<?php echo $cs['id']; ?>">
                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold hover:underline p-1">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 6: AGENTS & CITIES CONFIG -->
    <div id="tab-agents" class="admin-tab-content hidden">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- List & Add Agents Form -->
      <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-6 shadow-sm">
        <div>
          <h3 class="text-sm font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="user-plus" class="h-4.5 w-4.5 text-premium-emerald"></i>
            <span>Register Verified Agent</span>
          </h3>
          <p class="text-[10px] text-slate-400 font-semibold mt-1">Enroll partner DM statutory agents in the directory catalog.</p>
        </div>
        <form method="POST" action="admin_dashboard.php" class="space-y-4">
          <input type="hidden" name="add_agent" value="1">
          <div>
            <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Agent Full Name</label>
            <input type="text" name="name" required placeholder="e.g. Ramesh Kadam" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Phone Number</label>
              <input type="text" name="phone" required placeholder="+91 99000 00000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
            <div>
              <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
              <input type="email" name="email" required placeholder="name@mahaauctions.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Specialty Sector</label>
              <select name="specialty" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-600">
                <option value="Bank Auctions">Bank Auctions</option>
                <option value="Premium Rentals">Premium Rentals</option>
                <option value="Heavy Deposit">Heavy Deposit</option>
                <option value="Commercial properties">Commercial properties</option>
              </select>
            </div>
            <div>
              <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Initial Rating</label>
              <input type="number" step="0.1" min="1" max="5" name="rating" placeholder="4.8" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
            </div>
          </div>
          <div>
            <label class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Avatar Image URL</label>
            <input type="text" name="image" placeholder="https://unsplash.com/... (Optional)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold">
          </div>
          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-2.5 rounded-xl text-xs font-extrabold shadow-md transition-all">
            Enroll Verified Agent
          </button>
        </form>
      </div>

      <!-- Agent Directory List -->
      <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-800">Verified Agents Catalog</h3>
        </div>
        <div class="divide-y divide-slate-100 p-6">
          <?php foreach ($agents as $a): ?>
            <div class="py-4 flex justify-between items-center text-xs">
              <div class="flex items-center space-x-4">
                <img src="<?php echo htmlspecialchars($a['image']); ?>" class="h-10 w-10 rounded-full object-cover border border-slate-100">
                <div>
                  <h4 class="font-bold text-slate-800"><?php echo htmlspecialchars($a['name']); ?></h4>
                  <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($a['email']); ?> | <?php echo htmlspecialchars($a['phone']); ?></p>
                  <p class="text-[9px] text-slate-500 font-bold bg-slate-100 rounded px-1.5 py-0.5 mt-1 inline-block uppercase"><?php echo htmlspecialchars($a['specialty']); ?></p>
                </div>
              </div>
              <div class="text-right flex items-center space-x-2">
                <span class="inline-flex items-center space-x-0.5 text-amber-500 font-bold bg-amber-50 px-2 py-1 rounded">
                  <i data-lucide="star" class="h-3 w-3 fill-amber-500"></i>
                  <span><?php echo number_format($a['rating'], 2); ?></span>
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      </div><!-- End inner grid (tab-agents) -->
    </div>

    <!-- TAB 7: POST PROPERTY -->
    <div id="tab-add_property" class="admin-tab-content hidden">
      <div class="grid grid-cols-1 gap-8 items-start">
      <div class="bg-white border border-slate-200 rounded-3xl p-6 space-y-6 shadow-sm max-w-3xl mx-auto w-full">
        <div class="space-y-1">
          <h3 class="text-lg font-black text-slate-800 flex items-center space-x-2">
            <i data-lucide="plus-circle" class="h-5.5 w-5.5 text-premium-emerald"></i>
            <span>Post Bank Foreclosure Property (Admin override)</span>
          </h3>
          <p class="text-xs text-slate-400 font-semibold">Post official foreclosure listings directly to the global directory.</p>
        </div>
        
        <form method="POST" action="admin_dashboard.php" class="space-y-4">
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
              <select name="type" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
                <option value="Residential">Residential</option>
                <option value="Commercial">Commercial</option>
                <option value="Land">Land / Plot</option>
                <option value="Industrial">Industrial</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3.5">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Listing Category</label>
              <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-bold text-slate-600">
                <option value="Bank Auction">Bank Auction</option>
                <option value="Heavy Deposit">Heavy Deposit</option>
                <option value="Direct Sale">Direct Sale</option>
                <option value="Premium Rental">Premium Rental</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Reserve Price (₹)</label>
              <input type="text" name="price" required placeholder="e.g. ₹ 45,00,000" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3.5">
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">EMD Amount</label>
              <input type="text" name="emd" placeholder="e.g. 10%" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>
            <div>
              <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Government Valuation (Optional)</label>
              <input type="text" name="gov_val" placeholder="e.g. 52,00,000" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800">
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Description & Key Features</label>
            <textarea name="details" rows="3" required placeholder="Describe the property condition, area, nearby amenities..." class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald focus:bg-white transition-all font-semibold text-slate-800 leading-relaxed"></textarea>
          </div>

          <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center justify-center space-x-1.5 active:scale-[0.98]">
            <i data-lucide="check-circle" class="h-4 w-4"></i>
            <span>Publish Admin Listing</span>
          </button>
        </form>
      </div>
      </div><!-- End inner grid (tab-add_property) -->
    </div>

    <!-- Tab switcher JS script -->
    <script>
      function switchAdminTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.admin-tab-content').forEach(el => {
          el.classList.add('hidden');
        });
        // Show target tab content
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        // Reset button active styles
        const btns = document.querySelectorAll('.admin-nav-btn');
        btns.forEach(btn => {
          if (btn.dataset.tab === tabName) {
            btn.className = "admin-nav-btn px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm transition-all";
          } else {
            btn.className = "admin-nav-btn px-4 py-2 bg-white text-slate-655 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all";
          }
        });
      }

      window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && ['stats', 'users', 'listings', 'add_property', 'leads', 'consults', 'agents'].includes(tab)) {
          switchAdminTab(tab);
        }
      });
    </script>
  <?php endif; ?>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
