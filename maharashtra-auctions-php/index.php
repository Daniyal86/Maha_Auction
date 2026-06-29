<?php
// index.php
require_once 'config/db.php';

// Fetch cities for map marker initialization
$cities_stmt = $pdo->query("SELECT * FROM cities");
$cities = $cities_stmt->fetchAll();

// Fetch all properties to populate hover popups on the district map
$all_prop_stmt = $pdo->query("SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id ORDER BY p.created_at DESC");
$all_properties = $all_prop_stmt->fetchAll();

// Featured properties (limit 3 for recent listings grid)
$properties = array_slice($all_properties, 0, 3);

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-slate-900 text-white overflow-hidden py-10 sm:py-16 md:py-24">
  <!-- Dynamic Grid Overlay -->
  <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30"></div>
  
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4 sm:space-y-6">
    <div class="inline-flex items-center space-x-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 sm:px-4 sm:py-1.5 rounded-full text-emerald-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider">
      <i data-lucide="shield-check" class="h-3.5 w-3.5 sm:h-4 sm:w-4"></i>
      <span>Bilingual Foreclosure Draftsman Integrated</span>
    </div>
    
    <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight max-w-4xl mx-auto leading-tight sm:leading-none">
      Maharashtra Statutory Auction & <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">Heavy Deposit</span> Portal
    </h1>
    
    <p class="text-slate-400 max-w-2xl mx-auto text-xs sm:text-base md:text-lg font-medium leading-relaxed">
      Real-time verified listings compiled under SARFAESI foreclosure provisions. Fully client-driven draftsman tools and agent-assigned site inspectors.
    </p>

    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 pt-2 sm:pt-4">
      <a href="search.php" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-extrabold shadow-lg shadow-emerald-500/20 transition-all touch-target">
        <i data-lucide="search" class="h-4 w-4 sm:h-5 sm:w-5"></i>
        <span>Explore Foreclosures</span>
      </a>
      <button onclick="openTrialModal('Hero Section Alert')" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl text-sm sm:text-base font-extrabold backdrop-blur-md transition-all touch-target">
        <i data-lucide="bell" class="h-4 w-4 sm:h-5 sm:w-5"></i>
        <span>Setup Instant SMS Alerts</span>
      </button>
    </div>
  </div>
</div>

<!-- Interactive Leaflet Map Section (Mobile Edge-to-Edge with 12-16px outer padding) -->
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-4 pb-2 sm:py-12">
  <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-lg overflow-hidden p-3 sm:p-6 space-y-3 sm:space-y-6">
    
    <!-- Cleaner Header (📍 Maharashtra Command Center) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 pb-1 sm:pb-3 border-b border-slate-100">
      <div>
        <h2 class="text-lg sm:text-2xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="map-pin" class="h-5 w-5 sm:h-6 sm:w-6 text-premium-emerald shrink-0"></i>
          <span>Maharashtra Command Center</span>
        </h2>
        <p class="text-[11px] sm:text-xs text-slate-500 font-semibold mt-0.5">Live property database by district</p>
      </div>
      <div class="hidden sm:flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 text-[10px] sm:text-xs font-bold text-slate-600">
        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></div>
        <span>Live Database Coordinates & Division Sync</span>
      </div>
    </div>

    <!-- Horizontally Scrollable District Statistics Chips -->
    <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar py-1 text-xs font-bold -mx-1 px-1">
      <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider shrink-0 mr-1">Active:</span>
      <?php foreach ($cities as $c): if($c['property_count'] > 0): ?>
        <button type="button" onclick="focusDistrict('<?php echo urlencode($c['name']); ?>')" class="inline-flex items-center space-x-1.5 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 px-2.5 py-1 rounded-full border border-slate-200/80 shrink-0 transition-colors text-[11px] sm:text-xs">
          <span><?php echo htmlspecialchars($c['name']); ?></span>
          <span class="bg-emerald-500 text-white font-extrabold px-1.5 py-0.2 rounded-full text-[9px]"><?php echo $c['property_count']; ?></span>
        </button>
      <?php endif; endforeach; ?>
    </div>

    <!-- Map & Legend Wrapper -->
    <div class="relative w-full rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
      <!-- Leaflet Mount Div (340px on mobile) -->
      <div id="leaflet-landing-map" class="h-[340px] sm:h-[420px] md:h-[580px] w-full z-10"></div>

      <!-- Division Color Legend overlay (Collapsible floating panel on mobile) -->
      <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 z-[400] bg-white/95 backdrop-blur-md p-2 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200/90 shadow-xl text-[9px] sm:text-xs space-y-1 sm:space-y-2 max-w-[140px] sm:max-w-[240px]">
        <div class="font-black text-slate-800 border-b border-slate-100 pb-0.5 sm:pb-1 flex items-center justify-between cursor-pointer sm:cursor-default" onclick="document.getElementById('legend-color-list').classList.toggle('hidden'); document.getElementById('legend-chevron')?.classList.toggle('rotate-180')">
          <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider">प्रशासकीय विभाग</span>
          <i data-lucide="chevron-down" id="legend-chevron" class="h-3.5 w-3.5 text-slate-400 sm:hidden transition-transform"></i>
        </div>
        <div id="legend-color-list" class="hidden sm:grid grid-cols-1 gap-0.5 sm:gap-1 font-bold text-slate-700 text-[8px] sm:text-[11px]">
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #f87171;"></span><span class="truncate">अमरावती</span></div>
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #818cf8;"></span><span class="truncate">संभाजीनगर</span></div>
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #9ca3af;"></span><span class="truncate">कोकण</span></div>
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #e5a970;"></span><span class="truncate">नागपूर</span></div>
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #eab308;"></span><span class="truncate">नाशिक</span></div>
          <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-sm shrink-0 shadow-sm" style="background-color: #4ade80;"></span><span class="truncate">पुणे</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Mobile Bottom Sheet for District Selection -->
<div id="district-bottom-sheet-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[500] hidden transition-opacity duration-300" onclick="closeBottomSheet()"></div>
<div id="district-bottom-sheet" class="fixed inset-x-0 bottom-0 z-[501] bg-white rounded-t-3xl p-5 border-t border-slate-200 shadow-2xl transition-transform duration-300 transform translate-y-full max-w-lg mx-auto sm:hidden">
  <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-pointer" onclick="closeBottomSheet()"></div>
  <div class="flex justify-between items-start mb-3">
    <div>
      <h3 id="sheet-district-title" class="text-xl font-black text-slate-900 leading-tight">District</h3>
      <span id="sheet-division-badge" class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md text-white mt-1">Division</span>
    </div>
    <div class="text-right">
      <span id="sheet-prop-count" class="text-2xl font-black text-emerald-600 block">0</span>
      <span class="text-[10px] text-slate-400 uppercase font-bold">Properties</span>
    </div>
  </div>
  <div id="sheet-props-list" class="space-y-2 mb-4 max-h-48 overflow-y-auto"></div>
  <a id="sheet-view-btn" href="#" class="w-full h-12 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-extrabold flex items-center justify-center space-x-2 shadow-lg touch-target">
    <span>View All Listings</span>
    <i data-lucide="arrow-right" class="h-4 w-4"></i>
  </a>
</div>

<!-- Featured Foreclosures / Rentals (Reduced gap to ~20px) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-3 pb-6 sm:py-8 space-y-6">
  <div class="flex justify-between items-end">
    <div>
      <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Recent Database Listings</h2>
      <p class="text-[11px] sm:text-xs text-slate-500 font-semibold mt-0.5">Verified properties recently listed by banks and certified sellers.</p>
    </div>
    <a href="search.php" class="inline-flex items-center space-x-1 text-xs sm:text-sm font-bold text-premium-emerald hover:text-premium-emeraldHover transition-all touch-target">
      <span>View All</span>
      <i data-lucide="arrow-right" class="h-3.5 w-3.5 sm:h-4 sm:w-4"></i>
    </a>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-3 gap-2 sm:gap-6">
    <?php foreach ($properties as $prop): ?>
      <div class="bg-white rounded-xl sm:rounded-3xl overflow-hidden border border-slate-200 shadow-sm sm:shadow-md hover:shadow-xl transition-shadow flex flex-col justify-between">
        <div class="relative h-28 sm:h-44 bg-slate-100 overflow-hidden">
          <img src="<?php echo htmlspecialchars($prop['image']); ?>" alt="Property Image" class="w-full h-full object-cover">
          <div class="absolute top-1.5 left-1.5 sm:top-4 sm:left-4 bg-slate-900/80 backdrop-blur text-white text-[7px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-3 sm:py-1 rounded-full uppercase tracking-wider truncate max-w-[45%]">
            <?php echo htmlspecialchars($prop['type']); ?>
          </div>
          <div class="absolute top-1.5 right-1.5 sm:top-4 sm:right-4 bg-emerald-500 text-white text-[7px] sm:text-xs font-black px-1.5 py-0.5 sm:px-3 sm:py-1 rounded-full uppercase tracking-wider truncate max-w-[45%]">
            <?php echo htmlspecialchars($prop['category']); ?>
          </div>
        </div>
        <div class="p-2 sm:p-6 space-y-2 sm:space-y-4 flex-grow flex flex-col justify-between">
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-xs sm:text-lg font-black text-slate-800 leading-tight line-clamp-2 sm:line-clamp-1"><?php echo htmlspecialchars($prop['title']); ?></h3>
            <p class="text-[9px] sm:text-xs text-slate-500 font-bold flex items-center space-x-0.5 sm:space-x-1">
              <i data-lucide="map-pin" class="h-2.5 w-2.5 sm:h-3.5 sm:w-3.5 text-premium-emerald shrink-0"></i>
              <span class="truncate"><?php echo htmlspecialchars($prop['address']); ?></span>
            </p>
          </div>

          <div class="bg-slate-50 rounded-lg sm:rounded-2xl p-1.5 sm:p-4 border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-1 sm:gap-2 text-[9px] sm:text-xs">
            <div>
              <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[7px] sm:text-[10px]">Reserve Price</span>
              <span class="font-extrabold text-slate-800 text-[10px] sm:text-sm block truncate"><?php echo htmlspecialchars($prop['reserve_price']); ?></span>
            </div>
            <div class="hidden sm:block">
              <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[7px] sm:text-[10px]">Institution</span>
              <span class="font-extrabold text-slate-800 truncate block text-[10px] sm:text-sm"><?php echo htmlspecialchars($prop['bank']); ?></span>
            </div>
          </div>

          <a href="property.php?id=<?php echo htmlspecialchars($prop['id']); ?>" class="w-full inline-flex items-center justify-center space-x-1 bg-slate-900 hover:bg-slate-800 text-white py-2 sm:py-3 rounded-lg sm:rounded-xl text-[10px] sm:text-sm font-bold shadow-md transition-all touch-target">
            <span>View Details</span>
            <i data-lucide="arrow-up-right" class="h-3 w-3 sm:h-4 sm:w-4"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Builder / Developer Campaigns -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12">
  <div class="bg-slate-900 text-white rounded-3xl overflow-hidden shadow-2xl relative border border-slate-800">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-25"></div>
    <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 p-5 sm:p-8 md:p-12 items-center">
      <div class="space-y-4 sm:space-y-6">
        <span class="inline-flex items-center space-x-1.5 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full text-amber-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider">
          <i data-lucide="sparkles" class="h-3 w-3"></i>
          <span>Partner Builder Campaign</span>
        </span>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight leading-snug sm:leading-none">
          Godrej Horizon VIP Foreclosure Pre-Launch
        </h2>
        <p class="text-slate-400 text-xs sm:text-sm font-medium leading-relaxed">
          Download the certified regulatory prospectus report to lock in 15% pre-auction pricing. Valid for verified registered portal bidders only.
        </p>
        <div class="flex items-center space-x-4 text-[11px] sm:text-xs font-bold text-slate-400">
          <span class="flex items-center space-x-1">
            <i data-lucide="file-text" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-emerald-400"></i>
            <span>34-Page Booklet PDF</span>
          </span>
          <span class="flex items-center space-x-1">
            <i data-lucide="check-circle" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-emerald-400"></i>
            <span>MAHARERA Certified</span>
          </span>
        </div>
      </div>

      <div class="bg-white/5 backdrop-blur-md p-4 sm:p-6 rounded-2xl border border-white/10 shadow-xl space-y-3 sm:space-y-4">
        <h3 class="text-xs sm:text-sm font-bold text-slate-200">Secure Instant Access Prospectus</h3>
        <div id="campaign-error-msg" class="hidden text-xs text-red-400 bg-red-950/20 p-2.5 rounded-lg font-semibold border border-red-900/20 animate-pulse"></div>
        <form onsubmit="handleCampaignSubmit(event)" class="space-y-3">
          <input type="hidden" id="campaign-name-val" value="Godrej Horizon Campaign">
          
          <input type="text" id="campaign-user-name" required placeholder="Full Name" class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-white/5 border border-white/10 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-400 transition-colors font-semibold">
          
          <input type="email" id="campaign-user-email" required placeholder="Email Address" class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-white/5 border border-white/10 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-400 transition-colors font-semibold">
          
          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-2.5 sm:py-3 rounded-xl text-xs sm:text-sm font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-500/20 flex items-center justify-center space-x-2 touch-target">
            <span>Download Certified Brochure</span>
            <i data-lucide="download" class="h-4 w-4"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Initialize Leaflet map with database cities and properties
  const dbCities = <?php echo json_encode($cities); ?>;
  const dbAllProperties = <?php echo json_encode($all_properties); ?>;

  // Map of 35 Maharashtra districts to Administrative Divisions & colors matching user reference image
  const districtDivisionMap = {
    'Amravati': { divName: 'अमरावती विभाग', color: '#f87171', marathi: 'अमरावती', cityId: 'amravati' },
    'Akola': { divName: 'अमरावती विभाग', color: '#f87171', marathi: 'अकोला', cityId: 'akola' },
    'Buldana': { divName: 'अमरावती विभाग', color: '#f87171', marathi: 'बुलढाणा', cityId: 'buldana' },
    'Washim': { divName: 'अमरावती विभाग', color: '#f87171', marathi: 'वाशिम', cityId: 'washim' },
    'Yavatmal': { divName: 'अमरावती विभाग', color: '#f87171', marathi: 'यवतमाळ', cityId: 'yavatmal' },

    'Aurangabad': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'छत्रपती संभाजीनगर', cityId: 'aurangabad' },
    'Jalna': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'जालना', cityId: 'jalna' },
    'Parbhani': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'परभणी', cityId: 'parbhani' },
    'Hingoli': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'हिंगोली', cityId: 'hingoli' },
    'Bid': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'बीड', cityId: 'bid' },
    'Nanded': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'नांदेड', cityId: 'nanded' },
    'Latur': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'लातूर', cityId: 'latur' },
    'Osmanabad': { divName: 'छत्रपती संभाजीनगर विभाग', color: '#818cf8', marathi: 'धाराशिव', cityId: 'osmanabad' },

    'Mumbai': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'मुंबई शहर', cityId: 'mumbai' },
    'Mumbai Suburban': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'मुंबई उपनगर', cityId: 'mumbai' },
    'Thane': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'ठाणे', cityId: 'thane' },
    'Raigarh': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'रायगड', cityId: 'raigarh' },
    'Ratnagiri': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'रत्नागिरी', cityId: 'ratnagiri' },
    'Sindhudurg': { divName: 'कोकण विभाग', color: '#9ca3af', marathi: 'सिंधुदुर्ग', cityId: 'sindhudurg' },

    'Nagpur': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'नागपूर', cityId: 'nagpur' },
    'Wardha': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'वर्धा', cityId: 'wardha' },
    'Bhandara': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'भंडारा', cityId: 'bhandara' },
    'Gondiya': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'गोंदिया', cityId: 'gondiya' },
    'Chandrapur': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'चंद्रपूर', cityId: 'chandrapur' },
    'Garhchiroli': { divName: 'नागपूर विभाग', color: '#e5a970', marathi: 'गडचिरोली', cityId: 'garhchiroli' },

    'Nashik': { divName: 'नाशिक विभाग', color: '#eab308', marathi: 'नाशिक', cityId: 'nashik' },
    'Nandurbar': { divName: 'नाशिक विभाग', color: '#eab308', marathi: 'नंदुरबार', cityId: 'nandurbar' },
    'Dhule': { divName: 'नाशिक विभाग', color: '#eab308', marathi: 'धुळे', cityId: 'dhule' },
    'Jalgaon': { divName: 'नाशिक विभाग', color: '#eab308', marathi: 'जळगाव', cityId: 'jalgaon' },
    'Ahmadnagar': { divName: 'नाशिक विभाग', color: '#eab308', marathi: 'अहिल्यानगर', cityId: 'ahmadnagar' },

    'Pune': { divName: 'पुणे विभाग', color: '#4ade80', marathi: 'पुणे', cityId: 'pune' },
    'Satara': { divName: 'पुणे विभाग', color: '#4ade80', marathi: 'सातारा', cityId: 'satara' },
    'Solapur': { divName: 'पुणे विभाग', color: '#4ade80', marathi: 'सोलापूर', cityId: 'solapur' },
    'Sangli': { divName: 'पुणे विभाग', color: '#4ade80', marathi: 'सांगली', cityId: 'sangli' },
    'Kolhapur': { divName: 'पुणे विभाग', color: '#4ade80', marathi: 'कोल्हापूर', cityId: 'kolhapur' }
  };

  // Precise centroid coordinates for permanent district text labels
  const districtCentroids = {
    "Garhchiroli": { "lat": 19.8109, "lng": 80.315 },
    "Gondiya": { "lat": 21.2222, "lng": 80.2537 },
    "Latur": { "lat": 18.361, "lng": 76.7301 },
    "Pune": { "lat": 18.5168, "lng": 74.1257 },
    "Sindhudurg": { "lat": 16.1585, "lng": 73.7671 },
    "Thane": { "lat": 19.48, "lng": 73.16 },
    "Wardha": { "lat": 20.8068, "lng": 78.5778 },
    "Washim": { "lat": 20.2761, "lng": 77.2516 },
    "Yavatmal": { "lat": 20.0364, "lng": 78.041 },
    "Kolhapur": { "lat": 16.4361, "lng": 74.1265 },
    "Nagpur": { "lat": 21.1957, "lng": 79.0275 },
    "Parbhani": { "lat": 19.3068, "lng": 76.6941 },
    "Ahmadnagar": { "lat": 19.171, "lng": 74.7551 },
    "Akola": { "lat": 20.7373, "lng": 77.0931 },
    "Aurangabad": { "lat": 20.0883, "lng": 75.3139 },
    "Bid": { "lat": 18.934, "lng": 75.7703 },
    "Buldana": { "lat": 20.5285, "lng": 76.3825 },
    "Chandrapur": { "lat": 20.0967, "lng": 79.3217 },
    "Dhule": { "lat": 21.2329, "lng": 74.651 },
    "Nanded": { "lat": 19.2201, "lng": 77.6733 },
    "Nandurbar": { "lat": 21.5821, "lng": 74.2834 },
    "Hingoli": { "lat": 19.6032, "lng": 77.119 },
    "Nashik": { "lat": 20.1666, "lng": 74.03 },
    "Osmanabad": { "lat": 18.1953, "lng": 75.9951 },
    "Raigarh": { "lat": 18.4563, "lng": 73.2845 },
    "Ratnagiri": { "lat": 17.2062, "lng": 73.4642 },
    "Solapur": { "lat": 17.8382, "lng": 75.4216 },
    "Sangli": { "lat": 17.1559, "lng": 74.7028 },
    "Satara": { "lat": 17.6745, "lng": 74.1743 },
    "Amravati": { "lat": 21.1538, "lng": 77.6836 },
    "Bhandara": { "lat": 21.1035, "lng": 79.7873 },
    "Mumbai Suburban": { "lat": 19.1278, "lng": 72.8554 },
    "Jalgaon": { "lat": 20.8872, "lng": 75.5544 },
    "Jalna": { "lat": 19.995, "lng": 76.0179 },
    "Mumbai": { "lat": 18.9648, "lng": 72.8385 }
  };

  document.addEventListener('DOMContentLoaded', () => {
    // Map center at Maharashtra average coordinates
    const map = L.map('leaflet-landing-map', {
      scrollWheelZoom: false
    }).setView([19.6000, 75.8000], 6.8);

    // Clean CartoDB Voyager No-Labels tile layer so GeoJSON polygons and district titles stand out clearly
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
    }).addTo(map);

    // Fetch and render 35 District polygons with division styling
    fetch('./assets/maharashtra_districts.geojson')
      .then(res => res.json())
      .then(geojsonData => {
        let geoLayer;

        geoLayer = L.geoJSON(geojsonData, {
          style: (feature) => {
            const distName = feature.properties.district;
            const info = districtDivisionMap[distName] || { color: '#10b981' };
            return {
              color: '#ffffff',
              weight: 1.5,
              opacity: 0.9,
              fillColor: info.color,
              fillOpacity: 0.7
            };
          },
          onEachFeature: (feature, layer) => {
            const distName = feature.properties.district;
            const info = districtDivisionMap[distName] || { divName: 'महाराष्ट्र', color: '#10b981', marathi: distName, cityId: distName.toLowerCase() };
            
            // Find registered properties for this city/district
            const matchingProps = dbAllProperties.filter(p => 
              p.city_name.toLowerCase().includes(distName.toLowerCase()) || 
              distName.toLowerCase().includes(p.city_name.toLowerCase())
            );

            // Find city total count record
            const cityRecord = dbCities.find(c => 
              c.name.toLowerCase().includes(distName.toLowerCase()) || 
              distName.toLowerCase().includes(c.name.toLowerCase())
            );
            const totalCount = cityRecord ? cityRecord.property_count : matchingProps.length;

            // Generate property preview HTML inside hover card
            let propsListHtml = '';
            if (matchingProps.length > 0) {
              propsListHtml = `
                <div class="mt-2 space-y-1.5 border-t border-slate-100 pt-2 text-left">
                  <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Featured Listings:</span>
                  ${matchingProps.slice(0, 2).map(p => `
                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 text-xs shadow-sm">
                      <div class="font-black text-slate-800 truncate">${p.title}</div>
                      <div class="flex justify-between items-center mt-1 text-[11px]">
                        <span class="font-bold text-emerald-600">${p.reserve_price}</span>
                        <span class="text-[9px] bg-slate-200 text-slate-700 font-bold px-1.5 py-0.5 rounded uppercase">${p.category}</span>
                      </div>
                    </div>
                  `).join('')}
                </div>
              `;
            } else {
              propsListHtml = `
                <div class="mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center text-xs font-semibold text-slate-500">
                  <span>${totalCount} active verified database auctions in this district.</span>
                </div>
              `;
            }

            const tooltipContent = `
              <div class="p-4 w-72 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-2xl text-slate-800 font-sans">
                <div class="flex justify-between items-start border-b border-slate-100 pb-2 mb-2">
                  <div>
                    <h4 class="font-black text-slate-900 text-base leading-tight">${distName} <span class="text-xs text-slate-500 font-bold">(${info.marathi})</span></h4>
                    <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md text-white mt-1" style="background-color: ${info.color}">
                      ${info.divName}
                    </span>
                  </div>
                  <div class="text-right">
                    <span class="text-sm font-black text-emerald-600 block">${totalCount}</span>
                    <span class="text-[9px] text-slate-400 uppercase font-bold">Properties</span>
                  </div>
                </div>
                ${propsListHtml}
                <div class="mt-3 pt-2 border-t border-slate-100 text-center">
                  <span class="text-xs font-extrabold text-emerald-600 hover:text-emerald-700 inline-flex items-center space-x-1">
                    <span>Click to View All Auctions</span> &rarr;
                  </span>
                </div>
              </div>
            `;

            if (window.innerWidth >= 768) {
              layer.bindTooltip(tooltipContent, {
                sticky: true,
                direction: 'auto',
                opacity: 1,
                className: 'premium-district-tooltip'
              });
            }

            // Hover & Touch interactions
            layer.on({
              mouseover: (e) => {
                if (window.innerWidth >= 768) {
                  const l = e.target;
                  l.setStyle({
                    weight: 3,
                    color: '#ffffff',
                    fillOpacity: 0.95
                  });
                  if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    l.bringToFront();
                  }
                }
              },
              mouseout: (e) => {
                if (window.innerWidth >= 768) {
                  geoLayer.resetStyle(e.target);
                }
              },
              click: () => {
                const targetId = info.cityId || 'mumbai';
                if (window.innerWidth < 768) {
                  showBottomSheet(distName, info, totalCount, matchingProps, targetId);
                } else {
                  window.location.href = `city.php?id=${targetId}`;
                }
              }
            });
          }
        }).addTo(map);

        // Render permanent high-visibility Marathi text labels for all 35 districts directly from computed centroids
        Object.keys(districtCentroids).forEach(dName => {
          const info = districtDivisionMap[dName] || { marathi: dName };
          const coords = districtCentroids[dName];
          
          // Custom CSS div icon with crisp text halo
          const textIcon = L.divIcon({
            className: 'district-centroid-label',
            html: `<div style="font-family: system-ui, -apple-system, sans-serif; font-weight: 900; font-size: 12px; color: #0f172a; text-shadow: 0px 0px 4px #ffffff, 0px 0px 4px #ffffff, 0px 0px 4px #ffffff, 0px 0px 6px #ffffff; white-space: nowrap; pointer-events: none; user-select: none; text-align: center; transform: translate(-50%, -50%); cursor: default;">${info.marathi}</div>`,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
          });

          L.marker([coords.lat, coords.lng], { icon: textIcon, interactive: false, zIndexOffset: 1000 }).addTo(map);
        });
      });
  });

  // Mobile Bottom Sheet Handlers
  function showBottomSheet(distName, info, totalCount, matchingProps, targetId) {
    document.getElementById('sheet-district-title').innerText = `${distName} (${info.marathi})`;
    const badge = document.getElementById('sheet-division-badge');
    badge.innerText = info.divName;
    badge.style.backgroundColor = info.color;
    document.getElementById('sheet-prop-count').innerText = totalCount;
    document.getElementById('sheet-view-btn').href = `city.php?id=${targetId}`;

    const propsContainer = document.getElementById('sheet-props-list');
    if (matchingProps.length > 0) {
      propsContainer.innerHTML = matchingProps.slice(0, 3).map(p => `
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex justify-between items-center text-xs">
          <div class="truncate mr-2">
            <div class="font-black text-slate-800 truncate">${p.title}</div>
            <div class="text-[10px] text-slate-500 truncate">${p.address}</div>
          </div>
          <div class="text-right shrink-0">
            <div class="font-black text-emerald-600 text-xs">${p.reserve_price}</div>
            <span class="text-[8px] bg-slate-200 text-slate-700 font-bold px-1.5 py-0.5 rounded uppercase">${p.category}</span>
          </div>
        </div>
      `).join('');
    } else {
      propsContainer.innerHTML = `<div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center text-xs font-semibold text-slate-500">Verified database auctions available in ${distName}.</div>`;
    }

    document.getElementById('district-bottom-sheet-backdrop').classList.remove('hidden');
    document.getElementById('district-bottom-sheet').classList.remove('translate-y-full');
  }

  function closeBottomSheet() {
    document.getElementById('district-bottom-sheet-backdrop').classList.add('hidden');
    document.getElementById('district-bottom-sheet').classList.add('translate-y-full');
  }

  function focusDistrict(dName) {
    const info = districtDivisionMap[dName] || { cityId: 'mumbai' };
    const targetId = info.cityId || 'mumbai';
    window.location.href = `city.php?id=${targetId}`;
  }

  // Campaign Lead Submission
  function handleCampaignSubmit(e) {
    e.preventDefault();
    const campaign = document.getElementById('campaign-name-val').value;
    const name = document.getElementById('campaign-user-name').value;
    const email = document.getElementById('campaign-user-email').value;
    const errorEl = document.getElementById('campaign-error-msg');

    errorEl.classList.add('hidden');

    const formData = new FormData();
    formData.append('campaign', campaign);
    formData.append('name', name);
    formData.append('email', email);

    fetch('api/submit_lead.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const parent = e.target.parentElement;
        parent.innerHTML = `
          <div class="text-center py-6 space-y-4 text-white">
            <div class="mx-auto h-12 w-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 border border-emerald-500/25">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            <div>
              <h3 class="text-base font-black">Brochure Unlocked!</h3>
              <p class="text-xs text-slate-400 font-semibold mt-1">Sent download token directly to ${email}.</p>
            </div>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      } else {
        errorEl.textContent = data.message;
        errorEl.classList.remove('hidden');
      }
    })
    .catch(() => {
      errorEl.textContent = 'Server communications failed.';
      errorEl.classList.remove('hidden');
    });
  }
</script>

<?php
// Include auth modals and footer layout
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>

