<?php
// search.php
require_once 'config/db.php';

// Handle AJAX location requests
if (isset($_GET['ajax_location'])) {
    header('Content-Type: application/json');
    $type = $_GET['type'] ?? '';
    
    if ($type === 'districts') {
        $state = $_GET['state'] ?? '';
        $stmt = $pdo->prepare("SELECT DISTINCT district FROM properties WHERE state = ? AND district IS NOT NULL AND district != '' ORDER BY district ASC");
        $stmt->execute([$state]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    } elseif ($type === 'talukas') {
        $district = $_GET['district'] ?? '';
        $stmt = $pdo->prepare("SELECT DISTINCT taluka FROM properties WHERE district = ? AND taluka IS NOT NULL AND taluka != '' ORDER BY taluka ASC");
        $stmt->execute([$district]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    } elseif ($type === 'villages') {
        $taluka = $_GET['taluka'] ?? '';
        $stmt = $pdo->prepare("SELECT DISTINCT village FROM properties WHERE taluka = ? AND village IS NOT NULL AND village != '' ORDER BY village ASC");
        $stmt->execute([$taluka]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    }
    exit;
}

// Fetch unique states from database
$states_stmt = $pdo->query("SELECT DISTINCT state FROM properties WHERE state IS NOT NULL AND state != '' ORDER BY state ASC");
$states = $states_stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($states)) {
    $states = ['Maharashtra'];
}

// Retrieve search filters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$state_filter = isset($_GET['state']) ? trim($_GET['state']) : '';
$district_filter = isset($_GET['district']) ? trim($_GET['district']) : '';
$taluka_filter = isset($_GET['taluka']) ? trim($_GET['taluka']) : '';
$village_filter = isset($_GET['village']) ? trim($_GET['village']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$type_filter = isset($_GET['type']) ? trim($_GET['type']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

// Build dynamic query
$query = "SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id WHERE 1=1";
$params = [];

if (!empty($q)) {
    $query .= " AND (p.title LIKE :q OR p.address LIKE :q OR p.bank LIKE :q OR p.borrower LIKE :q)";
    $params['q'] = '%' . $q . '%';
}
if (!empty($state_filter)) {
    $query .= " AND p.state = :state";
    $params['state'] = $state_filter;
}
if (!empty($district_filter)) {
    $query .= " AND p.district = :district";
    $params['district'] = $district_filter;
}
if (!empty($taluka_filter)) {
    $query .= " AND p.taluka = :taluka";
    $params['taluka'] = $taluka_filter;
}
if (!empty($village_filter)) {
    $query .= " AND p.village = :village";
    $params['village'] = $village_filter;
}
if (!empty($category_filter)) {
    $query .= " AND p.category = :category";
    $params['category'] = $category_filter;
}
if (!empty($type_filter)) {
    $query .= " AND p.type = :type";
    $params['type'] = $type_filter;
}

// Sorting
if ($sort === 'price-asc') {
    $query .= " ORDER BY p.numeric_price ASC";
} elseif ($sort === 'price-desc') {
    $query .= " ORDER BY p.numeric_price DESC";
} elseif ($sort === 'date-asc') {
    // Put NULL date listings at the bottom
    $query .= " ORDER BY CASE WHEN p.auction_date IS NULL THEN 1 ELSE 0 END, p.auction_date ASC";
} else {
    $query .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-4 sm:space-y-6">
  
  <!-- Page Header Title (Reduced height top section by 25-35% + sticky toolbar) -->
  <div class="sticky top-0 z-30 bg-slate-50/95 backdrop-blur-md py-3 border-b border-slate-200/80 -mx-4 px-4 sm:mx-0 sm:px-0 sm:static sm:bg-transparent sm:backdrop-none sm:py-0 sm:border-b-0">
    <div class="flex justify-between items-center gap-2">
      <div>
        <h1 class="text-xl sm:text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="search" class="h-6 w-6 sm:h-8 sm:w-8 text-premium-emerald"></i>
          <span>Foreclosure Portal</span>
        </h1>
        <div class="flex items-center space-x-2 mt-0.5">
          <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">
            <?php echo count($properties); ?> SECURED ASSETS
          </span>
          <?php if (!empty($q) || !empty($state_filter) || !empty($district_filter) || !empty($taluka_filter) || !empty($village_filter) || !empty($category_filter) || !empty($type_filter)): ?>
            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md truncate max-w-[150px] sm:max-w-xs">
              Filtered
            </span>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Compact Right-Aligned View Toggle -->
      <div class="flex items-center space-x-1 bg-white p-1 rounded-xl border border-slate-200 shadow-sm shrink-0">
        <button id="btn-grid-view" onclick="toggleViewMode('grid')" title="Grid View" aria-label="Grid View" class="p-2 sm:px-3 sm:py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 bg-slate-900 text-white shadow-sm transition-all touch-target justify-center min-w-[40px] sm:min-w-0">
          <i data-lucide="layout-grid" class="h-4 w-4"></i>
          <span class="hidden sm:inline">Grid</span>
        </button>
        <button id="btn-sheet-view" onclick="toggleViewMode('sheet')" title="Compare Sheet" aria-label="Compare Sheet" class="p-2 sm:px-3 sm:py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 text-slate-600 hover:text-slate-900 transition-all touch-target justify-center min-w-[40px] sm:min-w-0">
          <i data-lucide="table" class="h-4 w-4"></i>
          <span class="hidden sm:inline">Sheet</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Main Search Panel Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
    
    <!-- Sidebar Filters -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-4 sm:p-6 space-y-4 sm:space-y-6">
      <div class="flex items-center justify-between cursor-pointer md:cursor-default" onclick="document.getElementById('mobile-filter-body').classList.toggle('hidden'); document.getElementById('filter-chevron')?.classList.toggle('rotate-180')">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
          <i data-lucide="filter" class="h-4 w-4 text-premium-emerald md:hidden"></i>
          <span>Search Filters</span>
        </h3>
        <button type="button" class="text-slate-400 hover:text-slate-600 md:hidden p-1 transition-transform" id="filter-chevron">
          <i data-lucide="chevron-down" class="h-4 w-4"></i>
        </button>
      </div>
      
      <form id="mobile-filter-body" method="GET" action="search.php" class="space-y-4 hidden md:block">
        <!-- Keyword -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Keyword</label>
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-slate-400"></i>
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Worli, SBI, etc..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          </div>
        </div>

        <!-- State Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">State</label>
          <select id="filter-state" name="state" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All States</option>
            <?php foreach ($states as $s): ?>
              <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $state_filter === $s ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($s); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- District Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">District</label>
          <select id="filter-district" name="district" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Districts</option>
            <?php
            if (!empty($state_filter)) {
                $dist_stmt = $pdo->prepare("SELECT DISTINCT district FROM properties WHERE state = ? AND district IS NOT NULL AND district != '' ORDER BY district ASC");
                $dist_stmt->execute([$state_filter]);
                $districts = $dist_stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($districts as $d) {
                    $selected = $district_filter === $d ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($d) . "\" {$selected}>" . htmlspecialchars($d) . "</option>";
                }
            }
            ?>
          </select>
        </div>

        <!-- Taluka Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Taluka</label>
          <select id="filter-taluka" name="taluka" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Talukas</option>
            <?php
            if (!empty($district_filter)) {
                $tal_stmt = $pdo->prepare("SELECT DISTINCT taluka FROM properties WHERE district = ? AND taluka IS NOT NULL AND taluka != '' ORDER BY taluka ASC");
                $tal_stmt->execute([$district_filter]);
                $talukas = $tal_stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($talukas as $t) {
                    $selected = $taluka_filter === $t ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($t) . "\" {$selected}>" . htmlspecialchars($t) . "</option>";
                }
            }
            ?>
          </select>
        </div>

        <!-- Village Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Village</label>
          <select id="filter-village" name="village" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Villages</option>
            <?php
            if (!empty($taluka_filter)) {
                $vil_stmt = $pdo->prepare("SELECT DISTINCT village FROM properties WHERE taluka = ? AND village IS NOT NULL AND village != '' ORDER BY village ASC");
                $vil_stmt->execute([$taluka_filter]);
                $villages = $vil_stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($villages as $v) {
                    $selected = $village_filter === $v ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($v) . "\" {$selected}>" . htmlspecialchars($v) . "</option>";
                }
            }
            ?>
          </select>
        </div>

        <!-- Category Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Listing Category</label>
          <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Categories</option>
            <option value="Auction" <?php echo $category_filter === 'Auction' ? 'selected' : ''; ?>>Foreclosure Auction</option>
            <option value="Rental" <?php echo $category_filter === 'Rental' ? 'selected' : ''; ?>>Premium Rental</option>
            <option value="Heavy Deposit" <?php echo $category_filter === 'Heavy Deposit' ? 'selected' : ''; ?>>Heavy Deposit</option>
            <option value="Seller Listed" <?php echo $category_filter === 'Seller Listed' ? 'selected' : ''; ?>>Seller Listed</option>
          </select>
        </div>

        <!-- Property Type Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Property Type</label>
          <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Types</option>
            <option value="Residential" <?php echo $type_filter === 'Residential' ? 'selected' : ''; ?>>Residential</option>
            <option value="Commercial" <?php echo $type_filter === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
            <option value="Industrial" <?php echo $type_filter === 'Industrial' ? 'selected' : ''; ?>>Industrial</option>
            <option value="Agricultural" <?php echo $type_filter === 'Agricultural' ? 'selected' : ''; ?>>Agricultural</option>
          </select>
        </div>

        <!-- Sort -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Sort Results</label>
          <select name="sort" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">Default (Recent)</option>
            <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="date-asc" <?php echo $sort === 'date-asc' ? 'selected' : ''; ?>>Auction Date: Earliest</option>
          </select>
        </div>

        <!-- Submit and Reset Buttons -->
        <div class="grid grid-cols-2 gap-2 pt-2">
          <button type="submit" class="bg-premium-emerald hover:bg-premium-emeraldHover text-white py-2.5 rounded-xl text-xs font-extrabold transition-all shadow-md touch-target">
            Apply Filters
          </button>
          <a href="search.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center touch-target">
            Reset All
          </a>
        </div>
      </form>
    </div>

    <!-- Listings Board (Grid & Compare views) -->
    <div class="lg:col-span-3 space-y-6">

      <!-- No Results State -->
      <?php if (count($properties) === 0): ?>
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4">
          <div class="mx-auto h-16 w-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400">
            <i data-lucide="database" class="h-8 w-8"></i>
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800">No Foreclosures Match Your Query</h3>
            <p class="text-xs text-slate-500 font-semibold mt-1">Try relaxing filters or broadening your keyword query.</p>
          </div>
        </div>
      <?php endif; ?>

      <!-- 1. Grid View Mount (2 Cards per row on mobile 320px-767px, 2-3 on desktop) -->
      <div id="grid-view-container" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-2.5 sm:gap-6">
        <?php foreach ($properties as $prop): ?>
          <div class="bg-white rounded-xl sm:rounded-3xl overflow-hidden border border-slate-200 shadow-sm sm:shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 active:scale-[0.99] flex flex-col justify-between h-full">
            
            <!-- Property Image (110-130px height on mobile, full-width, aspect ratio) -->
            <div class="relative h-28 sm:h-52 bg-slate-100 overflow-hidden rounded-t-xl sm:rounded-t-3xl">
              <img src="<?php echo htmlspecialchars($prop['image']); ?>" alt="<?php echo htmlspecialchars($prop['title']); ?>" loading="lazy" class="w-full h-full object-cover">
              
              <!-- Status Badges (compact, non-overlapping) -->
              <div class="absolute top-1.5 left-1.5 sm:top-3 sm:left-3 bg-slate-900/85 backdrop-blur-md text-white text-[9px] sm:text-xs font-bold px-2 py-0.5 sm:px-3 sm:py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center space-x-1 max-w-[46%]">
                <span class="truncate"><?php echo htmlspecialchars($prop['type']); ?></span>
              </div>
              <div class="absolute top-1.5 right-1.5 sm:top-3 sm:right-3 bg-emerald-500/90 backdrop-blur-md text-white text-[9px] sm:text-xs font-black px-2 py-0.5 sm:px-3 sm:py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center space-x-1 max-w-[46%]">
                <span class="truncate"><?php echo htmlspecialchars($prop['category']); ?></span>
              </div>
            </div>
            
            <!-- Card Content Body (Compact internal padding, clean spacing) -->
            <div class="p-2.5 sm:p-6 space-y-2.5 sm:space-y-4 flex-grow flex flex-col justify-between">
              
              <div class="space-y-2">
                <!-- Compare Row & Property ID Section (Single row layout) -->
                <div class="flex items-center justify-between bg-slate-50/90 p-1.5 sm:p-2 rounded-lg sm:rounded-xl border border-slate-100 gap-1">
                  <label class="flex items-center space-x-1 cursor-pointer shrink-0 min-h-[32px] sm:min-h-[40px] px-1 touch-target">
                    <input type="checkbox" class="compare-checkbox text-premium-emerald focus:ring-premium-emerald h-3 w-3 sm:h-4 sm:w-4 rounded border-slate-300" data-id="<?php echo htmlspecialchars($prop['id']); ?>" data-title="<?php echo htmlspecialchars($prop['title']); ?>" data-price="<?php echo htmlspecialchars($prop['reserve_price']); ?>" data-emd="<?php echo htmlspecialchars($prop['emd']); ?>" data-gov="<?php echo htmlspecialchars($prop['government_valuation'] ?: 'N/A'); ?>" data-bank="<?php echo htmlspecialchars($prop['bank']); ?>" data-address="<?php echo htmlspecialchars($prop['address']); ?>" data-type="<?php echo htmlspecialchars($prop['type']); ?>" data-category="<?php echo htmlspecialchars($prop['category']); ?>" data-possession="<?php echo htmlspecialchars($prop['possession'] ?: 'N/A'); ?>" data-image="<?php echo htmlspecialchars($prop['image']); ?>" onchange="updateCompareList(this)">
                    <span class="text-[9px] sm:text-xs font-bold text-slate-700">Compare</span>
                  </label>
                  <span class="bg-white border border-slate-200 text-slate-500 font-extrabold px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded text-[8px] sm:text-xs tracking-wider truncate max-w-full">
                    ID: <?php echo htmlspecialchars($prop['listing_id']); ?>
                  </span>
                </div>

                <!-- Property Title (2 lines clamp, 14px font size) -->
                <h3 class="text-xs sm:text-lg font-bold sm:font-black text-slate-800 leading-snug line-clamp-2">
                  <?php echo htmlspecialchars($prop['title']); ?>
                </h3>

                <!-- Address (Location icon, 1 line truncate, 11-12px font size) -->
                <p class="text-[11px] sm:text-sm font-semibold text-slate-500 flex items-center space-x-1 truncate">
                  <i data-lucide="map-pin" class="h-3 w-3 sm:h-4 sm:w-4 text-premium-emerald shrink-0"></i>
                  <span class="truncate"><?php echo htmlspecialchars($prop['address']); ?></span>
                </p>
              </div>

              <!-- Price & Institution Section (Reserve Price primary focus, 16-18px) -->
              <div class="space-y-2 sm:space-y-4 pt-1 sm:pt-2 border-t border-slate-100">
                <div class="bg-slate-50 rounded-lg sm:rounded-2xl p-2 sm:p-4 border border-slate-100">
                  <span class="block text-[9px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Reserve Price</span>
                  <span class="text-sm sm:text-2xl font-black text-slate-900 tracking-tight block mt-0.5 truncate">
                    <?php echo htmlspecialchars($prop['reserve_price']); ?>
                  </span>
                  <span class="block text-[10px] sm:text-xs font-extrabold text-slate-600 truncate mt-1 pt-1 border-t border-slate-200/60" title="<?php echo htmlspecialchars($prop['bank']); ?>">
                    <?php echo htmlspecialchars($prop['bank']); ?>
                  </span>
                </div>

                <!-- CTA Button (Full width, 38-42px height on mobile, rounded 10px) -->
                <a href="property.php?id=<?php echo htmlspecialchars($prop['id']); ?>" class="w-full h-10 sm:h-12 inline-flex items-center justify-center space-x-1.5 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white rounded-lg sm:rounded-xl text-xs sm:text-base font-extrabold shadow-sm sm:shadow-md transition-all touch-target">
                  <span>View Details</span>
                  <i data-lucide="arrow-up-right" class="h-3.5 w-3.5 sm:h-4 sm:w-4"></i>
                </a>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- 2. Compare Sheet Table View Mount (Hidden by default) -->
      <div id="sheet-view-container" class="hidden bg-white rounded-3xl border border-slate-200 shadow-lg responsive-table-wrapper">
        <table class="w-full text-left border-collapse min-w-[700px]">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] font-extrabold uppercase tracking-wider">
              <th class="px-6 py-4 text-center">Compare</th>
              <th class="px-6 py-4">Secured Asset</th>
              <th class="px-6 py-4">Classification</th>
              <th class="px-6 py-4">Bank/Source</th>
              <th class="px-6 py-4">Reserve Price</th>
              <th class="px-6 py-4">EMD</th>
              <th class="px-6 py-4">Gov Valuation</th>
              <th class="px-6 py-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
            <?php foreach ($properties as $prop): ?>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 text-center">
                  <input type="checkbox" class="compare-checkbox text-premium-emerald focus:ring-premium-emerald h-3.5 w-3.5 rounded" data-id="<?php echo htmlspecialchars($prop['id']); ?>" data-title="<?php echo htmlspecialchars($prop['title']); ?>" data-price="<?php echo htmlspecialchars($prop['reserve_price']); ?>" data-emd="<?php echo htmlspecialchars($prop['emd']); ?>" data-gov="<?php echo htmlspecialchars($prop['government_valuation'] ?: 'N/A'); ?>" data-bank="<?php echo htmlspecialchars($prop['bank']); ?>" data-address="<?php echo htmlspecialchars($prop['address']); ?>" data-type="<?php echo htmlspecialchars($prop['type']); ?>" data-category="<?php echo htmlspecialchars($prop['category']); ?>" data-possession="<?php echo htmlspecialchars($prop['possession'] ?: 'N/A'); ?>" data-image="<?php echo htmlspecialchars($prop['image']); ?>" onchange="updateCompareList(this)">
                </td>
                <td class="px-6 py-4 max-w-xs">
                  <div class="font-bold text-slate-800 truncate"><?php echo htmlspecialchars($prop['title']); ?></div>
                  <div class="text-[10px] text-slate-400 mt-0.5 truncate"><?php echo htmlspecialchars($prop['address']); ?></div>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-slate-100 border border-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] uppercase font-bold">
                    <?php echo htmlspecialchars($prop['type']); ?>
                  </span>
                  <span class="inline-block bg-emerald-50 text-premium-emerald px-2 py-0.5 rounded text-[10px] uppercase font-black ml-1">
                    <?php echo htmlspecialchars($prop['category']); ?>
                  </span>
                </td>
                <td class="px-6 py-4 font-bold text-slate-500">
                  <?php echo htmlspecialchars($prop['bank']); ?>
                </td>
                <td class="px-6 py-4 font-black text-slate-800">
                  <?php echo htmlspecialchars($prop['reserve_price']); ?>
                </td>
                <td class="px-6 py-4 text-slate-400 font-bold">
                  <?php echo htmlspecialchars($prop['emd']); ?>
                </td>
                <td class="px-6 py-4 text-slate-500 font-bold">
                  <?php echo htmlspecialchars($prop['government_valuation'] ?: 'N/A'); ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <a href="property.php?id=<?php echo htmlspecialchars($prop['id']); ?>" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold shadow transition-all inline-block touch-target">
                    Open
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
  // View Toggle Scripts
  let viewMode = 'grid';

  function toggleViewMode(mode) {
    viewMode = mode;
    const gridContainer = document.getElementById('grid-view-container');
    const sheetContainer = document.getElementById('sheet-view-container');
    const btnGrid = document.getElementById('btn-grid-view');
    const btnSheet = document.getElementById('btn-sheet-view');

    if (mode === 'grid') {
      gridContainer.classList.remove('hidden');
      sheetContainer.classList.add('hidden');
      btnGrid.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 bg-white text-slate-800 shadow-sm transition-all";
      btnSheet.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 text-slate-600 hover:text-slate-800 transition-all";
    } else {
      gridContainer.classList.add('hidden');
      sheetContainer.classList.remove('hidden');
      btnGrid.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 text-slate-600 hover:text-slate-800 transition-all";
      btnSheet.className = "px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 bg-white text-slate-800 shadow-sm transition-all";
    }
  }

  // Comparative Analysis Scripts
  let selectedProperties = [];

  function updateCompareList(chk) {
    const propId = chk.dataset.id;
    if (chk.checked) {
      if (selectedProperties.length >= 3) {
        alert("You can compare a maximum of 3 properties at a time.");
        chk.checked = false;
        return;
      }
      // Add property details
      selectedProperties.push({
        id: propId,
        title: chk.dataset.title,
        price: chk.dataset.price,
        emd: chk.dataset.emd,
        gov: chk.dataset.gov,
        bank: chk.dataset.bank,
        address: chk.dataset.address,
        type: chk.dataset.type,
        category: chk.dataset.category,
        possession: chk.dataset.possession,
        image: chk.dataset.image
      });
    } else {
      selectedProperties = selectedProperties.filter(p => p.id !== propId);
    }

    // Sync all checkboxes with this id
    document.querySelectorAll(`.compare-checkbox[data-id="${propId}"]`).forEach(c => {
      c.checked = chk.checked;
    });

    renderCompareBar();
  }

  function renderCompareBar() {
    const bar = document.getElementById('compare-bar');
    const txt = document.getElementById('compare-count-text');
    if (selectedProperties.length > 0) {
      bar.classList.remove('hidden');
      txt.textContent = `Compare ${selectedProperties.length} ${selectedProperties.length === 1 ? 'Property' : 'Properties'}`;
    } else {
      bar.classList.add('hidden');
    }
  }

  function clearCompareSelection() {
    selectedProperties = [];
    document.querySelectorAll('.compare-checkbox').forEach(c => {
      c.checked = false;
    });
    renderCompareBar();
  }

  function openCompareModal() {
    if (selectedProperties.length === 0) return;
    const body = document.getElementById('compare-modal-body');
    
    let html = `
      <div class="responsive-table-wrapper">
        <table class="w-full text-left border-collapse border border-slate-200 rounded-2xl overflow-hidden min-w-[600px]">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase w-1/4">Comparison Fields</th>
    `;
    
    selectedProperties.forEach(p => {
      html += `
        <th class="px-4 py-3 text-xs font-extrabold text-slate-700 uppercase w-1/4">
          <div class="space-y-2">
            <img src="${p.image}" class="h-20 w-full object-cover rounded-lg border border-slate-200">
            <div class="line-clamp-2 text-slate-800 font-black leading-tight">${p.title}</div>
          </div>
        </th>
      `;
    });
    
    // Fill empty columns to maintain grid width
    for (let i = selectedProperties.length; i < 3; i++) {
      html += `<th class="px-4 py-3 text-xs text-slate-300 w-1/4 text-center">Empty Slot</th>`;
    }
    
    html += `
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
          <tr>
            <td class="px-4 py-3 text-slate-400">Classification</td>
    `;
    selectedProperties.forEach(p => {
      html += `
        <td class="px-4 py-3">
          <span class="inline-block bg-slate-100 border border-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] uppercase font-bold">${p.type}</span>
          <span class="inline-block bg-emerald-50 text-premium-emerald px-2 py-0.5 rounded text-[10px] uppercase font-black ml-1">${p.category}</span>
        </td>
      `;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;
    
    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Reserve Price</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 font-black text-slate-800 text-sm">${p.price}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Earnest Money (EMD)</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 font-bold text-slate-600">${p.emd}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Gov. Valuation</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 font-bold text-slate-500">${p.gov}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Possession Status</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 font-bold text-slate-800">${p.possession}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Foreclosure Institution</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 font-extrabold text-premium-emerald">${p.bank}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr>
            <td class="px-4 py-3 text-slate-400">Asset Address</td>
    `;
    selectedProperties.forEach(p => {
      html += `<td class="px-4 py-3 text-slate-500 font-medium leading-normal">${p.address}</td>`;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-3"></td>`;

    html += `
          </tr>
          <tr class="bg-slate-50">
            <td class="px-4 py-4 text-slate-400">Action</td>
    `;
    selectedProperties.forEach(p => {
      html += `
        <td class="px-4 py-4">
          <a href="property.php?id=${p.id}" class="inline-flex items-center justify-center space-x-1 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow transition-all w-full text-center touch-target">
            <span>Open Directory</span>
            <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
          </a>
        </td>
      `;
    });
    for (let i = selectedProperties.length; i < 3; i++) html += `<td class="px-4 py-4"></td>`;

    html += `
          </tr>
        </tbody>
      </table>
      </div>
    `;
    
    body.innerHTML = html;
    document.getElementById('compare-modal').classList.remove('hidden');
    document.body.classList.add('modal-open');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeCompareModal() {
    document.getElementById('compare-modal').classList.add('hidden');
    document.body.classList.remove('modal-open');
  }

  // Chained Location dropdowns
  document.addEventListener('DOMContentLoaded', function() {
    const stateSel = document.getElementById('filter-state');
    const distSel = document.getElementById('filter-district');
    const talSel = document.getElementById('filter-taluka');
    const vilSel = document.getElementById('filter-village');

    if (stateSel) {
      stateSel.addEventListener('change', function() {
        const val = this.value;
        distSel.innerHTML = '<option value="">All Districts</option>';
        talSel.innerHTML = '<option value="">All Talukas</option>';
        vilSel.innerHTML = '<option value="">All Villages</option>';
        if (!val) return;

        fetch(`search.php?ajax_location=1&type=districts&state=${encodeURIComponent(val)}`)
          .then(res => res.json())
          .then(data => {
            data.forEach(d => {
              const opt = document.createElement('option');
              opt.value = d;
              opt.textContent = d;
              distSel.appendChild(opt);
            });
          });
      });
    }

    if (distSel) {
      distSel.addEventListener('change', function() {
        const val = this.value;
        talSel.innerHTML = '<option value="">All Talukas</option>';
        vilSel.innerHTML = '<option value="">All Villages</option>';
        if (!val) return;

        fetch(`search.php?ajax_location=1&type=talukas&district=${encodeURIComponent(val)}`)
          .then(res => res.json())
          .then(data => {
            data.forEach(t => {
              const opt = document.createElement('option');
              opt.value = t;
              opt.textContent = t;
              talSel.appendChild(opt);
            });
          });
      });
    }

    if (talSel) {
      talSel.addEventListener('change', function() {
        const val = this.value;
        vilSel.innerHTML = '<option value="">All Villages</option>';
        if (!val) return;

        fetch(`search.php?ajax_location=1&type=villages&taluka=${encodeURIComponent(val)}`)
          .then(res => res.json())
          .then(data => {
            data.forEach(v => {
              const opt = document.createElement('option');
              opt.value = v;
              opt.textContent = v;
              vilSel.appendChild(opt);
            });
          });
      });
    }
  });
</script>

<!-- Floating Comparison Bar -->
<div id="compare-bar" class="fixed bottom-4 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-800 flex items-center justify-between space-x-3 z-40 hidden transition-all max-w-[92vw] sm:max-w-lg w-full">
  <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
    <div class="bg-premium-emerald p-2 rounded-xl text-white shrink-0">
      <i data-lucide="git-compare" class="h-4 w-4 sm:h-5 sm:w-5"></i>
    </div>
    <div class="min-w-0">
      <h4 class="text-xs font-black tracking-tight truncate" id="compare-count-text">Compare 0 Properties</h4>
      <p class="text-[9px] sm:text-[10px] text-slate-400 font-semibold truncate">Up to 3 assets</p>
    </div>
  </div>
  <div class="flex items-center space-x-1.5 shrink-0">
    <button onclick="clearCompareSelection()" class="px-2.5 py-1.5 border border-white/10 hover:bg-white/5 rounded-xl text-[11px] font-bold transition-all touch-target">
      Clear
    </button>
    <button onclick="openCompareModal()" class="px-3 py-1.5 bg-premium-emerald hover:bg-premium-emeraldHover text-white rounded-xl text-[11px] font-extrabold shadow-lg shadow-emerald-500/25 transition-all flex items-center space-x-1 touch-target">
      <span>Analyze</span>
      <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
    </button>
  </div>
</div>

<!-- Comparison Modal -->
<div id="compare-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCompareModal()"></div>
  <div class="relative bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-6 text-white relative">
      <button onclick="closeCompareModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-full transition-colors">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
      <h2 class="text-xl font-extrabold tracking-tight flex items-center space-x-2">
        <i data-lucide="git-compare" class="text-premium-emerald h-6 w-6"></i>
        <span>Comparative Assets Analysis Panel</span>
      </h2>
      <p class="text-slate-400 text-xs mt-1">Cross-compare prices, classifications, locations, and foreclosure sources side by side.</p>
    </div>
    
    <!-- Modal Body -->
    <div class="p-6 overflow-x-auto" id="compare-modal-body">
      <!-- Render comparative table here -->
    </div>
  </div>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
