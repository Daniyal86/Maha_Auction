<?php
// php-site/search.php
require_once 'config/db.php';

// Fetch cities for filters
$cities_stmt = $pdo->query("SELECT * FROM cities ORDER BY name ASC");
$cities = $cities_stmt->fetchAll();

// Retrieve search filters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$city_filter = isset($_GET['city']) ? trim($_GET['city']) : '';
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
if (!empty($city_filter)) {
    $query .= " AND p.city_id = :city";
    $params['city'] = $city_filter;
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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
  
  <!-- Page Header Title -->
  <div>
    <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
      <i data-lucide="search" class="h-8 w-8 text-premium-emerald"></i>
      <span>Foreclosure Data Surfing Portal</span>
    </h1>
    <p class="text-xs text-slate-500 font-semibold mt-1">Refine and query our secure databases. Toggle Grid or Sheet comparators.</p>
  </div>

  <!-- Main Search Panel Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
    
    <!-- Sidebar Filters -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 space-y-6">
      <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Search Filters</h3>
      
      <form method="GET" action="search.php" class="space-y-4">
        <!-- Keyword -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Keyword</label>
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-slate-400"></i>
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Worli, SBI, etc..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald transition-colors font-semibold">
          </div>
        </div>

        <!-- City Dropdown -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">City Location</label>
          <select name="city" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-premium-emerald font-semibold text-slate-700">
            <option value="">All Maharashtra</option>
            <?php foreach ($cities as $city): ?>
              <option value="<?php echo htmlspecialchars($city['id']); ?>" <?php echo $city_filter === $city['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($city['name']); ?>
              </option>
            <?php endforeach; ?>
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
          <button type="submit" class="bg-premium-emerald hover:bg-premium-emeraldHover text-white py-2 rounded-xl text-xs font-extrabold transition-all shadow-md">
            Apply Filters
          </button>
          <a href="search.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-xl text-xs font-bold transition-all flex items-center justify-center">
            Reset All
          </a>
        </div>
      </form>
    </div>

    <!-- Listings Board (Grid & Compare views) -->
    <div class="lg:col-span-3 space-y-6">
      <!-- Toolbar -->
      <div class="flex justify-between items-center bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">
          <?php echo count($properties); ?> SECURED ASSETS FOUND
        </span>
        <div class="flex items-center space-x-1.5 bg-slate-100 p-1 rounded-xl">
          <button id="btn-grid-view" onclick="toggleViewMode('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 bg-white text-slate-800 shadow-sm transition-all">
            <i data-lucide="layout-grid" class="h-3.5 w-3.5"></i>
            <span class="hidden sm:inline">Grid View</span>
          </button>
          <button id="btn-sheet-view" onclick="toggleViewMode('sheet')" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-1 text-slate-600 hover:text-slate-800 transition-all">
            <i data-lucide="table" class="h-3.5 w-3.5"></i>
            <span class="hidden sm:inline">Compare Sheet</span>
          </button>
        </div>
      </div>

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

      <!-- 1. Grid View Mount -->
      <div id="grid-view-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($properties as $prop): ?>
          <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-lg hover:shadow-xl transition-shadow flex flex-col justify-between">
            <div class="relative h-48 bg-slate-100 overflow-hidden">
              <img src="<?php echo htmlspecialchars($prop['image']); ?>" alt="Property Image" class="w-full h-full object-cover">
              <div class="absolute top-4 left-4 bg-slate-900/80 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <?php echo htmlspecialchars($prop['type']); ?>
              </div>
              <div class="absolute top-4 right-4 bg-emerald-500 text-white text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">
                <?php echo htmlspecialchars($prop['category']); ?>
              </div>
            </div>
            
            <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
              <div class="space-y-2">
                <div class="flex justify-between items-center bg-slate-50 p-2 rounded-xl border border-slate-100 mb-2">
                  <label class="flex items-center space-x-1.5 cursor-pointer">
                    <input type="checkbox" class="compare-checkbox text-premium-emerald focus:ring-premium-emerald h-3.5 w-3.5 rounded" data-id="<?php echo htmlspecialchars($prop['id']); ?>" data-title="<?php echo htmlspecialchars($prop['title']); ?>" data-price="<?php echo htmlspecialchars($prop['reserve_price']); ?>" data-emd="<?php echo htmlspecialchars($prop['emd']); ?>" data-gov="<?php echo htmlspecialchars($prop['government_valuation'] ?: 'N/A'); ?>" data-bank="<?php echo htmlspecialchars($prop['bank']); ?>" data-address="<?php echo htmlspecialchars($prop['address']); ?>" data-type="<?php echo htmlspecialchars($prop['type']); ?>" data-category="<?php echo htmlspecialchars($prop['category']); ?>" data-possession="<?php echo htmlspecialchars($prop['possession'] ?: 'N/A'); ?>" data-image="<?php echo htmlspecialchars($prop['image']); ?>" onchange="updateCompareList(this)">
                    <span class="text-[10px] font-bold text-slate-600">Compare</span>
                  </label>
                  <span class="inline-block bg-white border border-slate-200 text-slate-500 font-extrabold px-2 py-0.5 rounded text-[9px] uppercase tracking-wider">
                    ID: <?php echo htmlspecialchars($prop['listing_id']); ?>
                  </span>
                </div>
                <h3 class="text-base font-black text-slate-800 leading-snug line-clamp-1"><?php echo htmlspecialchars($prop['title']); ?></h3>
                <p class="text-xs text-slate-500 font-bold flex items-center space-x-1">
                  <i data-lucide="map-pin" class="h-3.5 w-3.5 text-premium-emerald shrink-0"></i>
                  <span class="truncate"><?php echo htmlspecialchars($prop['address']); ?></span>
                </p>
              </div>

              <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span class="block text-slate-400 font-semibold uppercase tracking-wider">Reserve Price</span>
                  <span class="font-extrabold text-slate-800"><?php echo htmlspecialchars($prop['reserve_price']); ?></span>
                </div>
                <div>
                  <span class="block text-slate-400 font-semibold uppercase tracking-wider">Institution</span>
                  <span class="font-extrabold text-slate-800 truncate block"><?php echo htmlspecialchars($prop['bank']); ?></span>
                </div>
              </div>

              <a href="property.php?id=<?php echo htmlspecialchars($prop['id']); ?>" class="w-full inline-flex items-center justify-center space-x-1.5 bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-all">
                <span>View Details</span>
                <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- 2. Compare Sheet Table View Mount (Hidden by default) -->
      <div id="sheet-view-container" class="hidden bg-white rounded-3xl border border-slate-200 shadow-lg overflow-x-auto">
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
                  <a href="property.php?id=<?php echo htmlspecialchars($prop['id']); ?>" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold shadow transition-all inline-block">
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
      <table class="w-full text-left border-collapse border border-slate-200 rounded-2xl overflow-hidden">
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
          <a href="property.php?id=${p.id}" class="inline-flex items-center justify-center space-x-1 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow transition-all w-full text-center">
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
    `;
    
    body.innerHTML = html;
    document.getElementById('compare-modal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function closeCompareModal() {
    document.getElementById('compare-modal').classList.add('hidden');
  }
</script>

<!-- Floating Comparison Bar -->
<div id="compare-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-800 flex items-center justify-between space-x-6 z-40 hidden transition-all max-w-lg w-full">
  <div class="flex items-center space-x-3">
    <div class="bg-premium-emerald p-2 rounded-xl text-white">
      <i data-lucide="git-compare" class="h-5 w-5"></i>
    </div>
    <div>
      <h4 class="text-xs font-black tracking-tight" id="compare-count-text">Compare 0 Properties</h4>
      <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Select up to 3 assets for analysis.</p>
    </div>
  </div>
  <div class="flex space-x-2">
    <button onclick="clearCompareSelection()" class="px-3 py-1.5 border border-white/10 hover:bg-white/5 rounded-xl text-xs font-bold transition-all">
      Clear
    </button>
    <button onclick="openCompareModal()" class="px-4 py-1.5 bg-premium-emerald hover:bg-premium-emeraldHover text-white rounded-xl text-xs font-extrabold shadow-lg shadow-emerald-500/25 transition-all flex items-center space-x-1">
      <span>Analyze Side-by-Side</span>
      <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
    </button>
  </div>
</div>

<!-- Comparison Modal -->
<div id="compare-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCompareModal()"></div>
  <div class="relative bg-white rounded-3xl w-full max-w-4xl overflow-hidden shadow-2xl border border-slate-200 z-10 flex flex-col text-left">
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
    <div class="p-6 overflow-x-auto max-h-[500px]" id="compare-modal-body">
      <!-- Render comparative table here -->
    </div>
  </div>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
