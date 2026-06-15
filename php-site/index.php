<?php
// php-site/index.php
require_once 'config/db.php';

// Fetch cities for map marker initialization
$cities_stmt = $pdo->query("SELECT * FROM cities");
$cities = $cities_stmt->fetchAll();

// Fetch featured properties
$prop_stmt = $pdo->query("SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id ORDER BY p.created_at DESC LIMIT 3");
$properties = $prop_stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-slate-900 text-white overflow-hidden py-16 sm:py-24">
  <!-- Dynamic Grid Overlay -->
  <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30"></div>
  
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
    <div class="inline-flex items-center space-x-2 bg-emerald-500/10 border border-emerald-500/20 px-4 py-1.5 rounded-full text-emerald-400 text-xs font-bold uppercase tracking-wider">
      <i data-lucide="shield-check" class="h-4 w-4"></i>
      <span>Bilingual Foreclosure Draftsman Integrated</span>
    </div>
    
    <h1 class="text-4xl sm:text-6xl font-black tracking-tight max-w-4xl mx-auto leading-none">
      Maharashtra Statutory Auction & <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">Heavy Deposit</span> Portal
    </h1>
    
    <p class="text-slate-400 max-w-2xl mx-auto text-base sm:text-lg font-medium">
      Real-time verified listings compiled under SARFAESI foreclosure provisions. Fully client-driven draftsman tools and agent-assigned site inspectors.
    </p>

    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-4">
      <a href="search.php" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white px-8 py-4 rounded-2xl text-base font-extrabold shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-0.5">
        <i data-lucide="search" class="h-5 w-5"></i>
        <span>Explore Foreclosures</span>
      </a>
      <button onclick="openTrialModal('Hero Section Alert')" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white px-8 py-4 rounded-2xl text-base font-extrabold backdrop-blur-md transition-all hover:-translate-y-0.5">
        <i data-lucide="bell" class="h-5 w-5"></i>
        <span>Setup Instant SMS Alerts</span>
      </button>
    </div>
  </div>
</div>

<!-- Interactive Leaflet Map Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden p-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="map-pin" class="h-6 w-6 text-premium-emerald"></i>
          <span>Maharashtra Foreclosure Map Command Center</span>
        </h2>
        <p class="text-xs text-slate-500 font-semibold mt-1">Click city hot spots to filter current foreclosure auctions and rentals.</p>
      </div>
      <div class="flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 text-xs font-bold text-slate-600">
        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></div>
        <span>Live Database Coordinates Sync</span>
      </div>
    </div>

    <!-- Leaflet Mount Div -->
    <div id="leaflet-landing-map" class="h-[400px] rounded-2xl border border-slate-200 shadow-inner z-10"></div>
  </div>
</div>

<!-- Featured Foreclosures / Rentals -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  <div class="flex justify-between items-end">
    <div>
      <h2 class="text-3xl font-black text-slate-800 tracking-tight">Recent Database Listings</h2>
      <p class="text-xs text-slate-500 font-semibold mt-1">Verified properties recently listed by banks and certified sellers.</p>
    </div>
    <a href="search.php" class="inline-flex items-center space-x-1 text-sm font-bold text-premium-emerald hover:text-premium-emeraldHover transition-all">
      <span>View All</span>
      <i data-lucide="arrow-right" class="h-4 w-4"></i>
    </a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
            <h3 class="text-lg font-black text-slate-800 leading-snug line-clamp-1"><?php echo htmlspecialchars($prop['title']); ?></h3>
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
</div>

<!-- Builder / Developer Campaigns -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="bg-slate-900 text-white rounded-3xl overflow-hidden shadow-2xl relative border border-slate-800">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-25"></div>
    <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-8 p-8 sm:p-12 items-center">
      <div class="space-y-6">
        <span class="inline-flex items-center space-x-1.5 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full text-amber-400 text-xs font-bold uppercase tracking-wider">
          <i data-lucide="sparkles" class="h-3 w-3"></i>
          <span>Partner Builder Campaign</span>
        </span>
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-none">
          Godrej Horizon VIP Foreclosure Pre-Launch
        </h2>
        <p class="text-slate-400 text-sm font-medium">
          Download the certified regulatory prospectus report to lock in 15% pre-auction pricing. Valid for verified registered portal bidders only.
        </p>
        <div class="flex items-center space-x-4 text-xs font-bold text-slate-400">
          <span class="flex items-center space-x-1">
            <i data-lucide="file-text" class="h-4 w-4 text-emerald-400"></i>
            <span>34-Page Booklet PDF</span>
          </span>
          <span class="flex items-center space-x-1">
            <i data-lucide="check-circle" class="h-4 w-4 text-emerald-400"></i>
            <span>MAHARERA Certified</span>
          </span>
        </div>
      </div>

      <div class="bg-white/5 backdrop-blur-md p-6 rounded-2xl border border-white/10 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-slate-200">Secure Instant Access Prospectus</h3>
        <div id="campaign-error-msg" class="hidden text-xs text-red-400 bg-red-950/20 p-3 rounded-lg font-semibold border border-red-900/20 animate-pulse"></div>
        <form onsubmit="handleCampaignSubmit(event)" class="space-y-3">
          <input type="hidden" id="campaign-name-val" value="Godrej Horizon Campaign">
          
          <input type="text" id="campaign-user-name" required placeholder="Full Name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-400 transition-colors font-semibold">
          
          <input type="email" id="campaign-user-email" required placeholder="Email Address" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-400 transition-colors font-semibold">
          
          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold transition-all hover:shadow-lg hover:shadow-emerald-500/20 flex items-center justify-center space-x-2">
            <span>Download Certified Brochure</span>
            <i data-lucide="download" class="h-4 w-4"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Initialize Leaflet map with database cities
  const dbCities = <?php echo json_encode($cities); ?>;

  document.addEventListener('DOMContentLoaded', () => {
    // Map center at Maharashtra average coordinates from React component
    const map = L.map('leaflet-landing-map', {
      scrollWheelZoom: false
    }).setView([19.7515, 75.7139], 6.4);

    // Premium CartoDB Voyager Map Tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
    }).addTo(map);

    // Fetch and draw boundary layer with custom dashed border styling
    fetch('./assets/maharashtra.geojson')
      .then(res => res.json())
      .then(geojsonData => {
        L.geoJSON(geojsonData, {
          style: {
            color: '#10b981',
            weight: 3,
            opacity: 0.6,
            fillColor: '#10b981',
            fillOpacity: 0.08,
            dashArray: '6, 6'
          }
        }).addTo(map);
      });

    // Custom premium animated marker icon using DivIcon
    const customIcon = L.divIcon({
      className: 'custom-leaflet-marker',
      html: `
        <div class="relative flex flex-col items-center justify-center cursor-pointer" style="width: 40px; height: 40px;">
          <div class="w-8 h-8 rounded-full bg-premium-emerald/30 absolute animate-ping" style="top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
          <svg class="w-10 h-10 text-premium-emerald drop-shadow-lg transition-colors hover:text-premium-emerald relative z-10" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
          </svg>
        </div>
      `,
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
    });

    // Add dynamic markers from database
    dbCities.forEach(city => {
      const marker = L.marker([parseFloat(city.lat), parseFloat(city.lng)], { icon: customIcon }).addTo(map);
      
      const tooltipContent = `
        <div class="text-center p-4 w-44 bg-white rounded-2xl border border-slate-100 shadow-xl text-slate-800">
          <span class="font-extrabold text-premium-emerald text-xl block mb-1">${city.name}</span>
          <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-2 border-b border-slate-100 pb-2">
            ${city.property_count} Properties
          </span>
          <span class="w-full inline-block bg-emerald-50 text-premium-emerald font-bold py-1.5 rounded-lg text-sm">
            Click to View &rarr;
          </span>
        </div>
      `;
      
      marker.bindTooltip(tooltipContent, {
        direction: 'top',
        offset: [0, -40],
        opacity: 1,
        className: 'premium-tooltip'
      });

      marker.on('click', () => {
        window.location.href = `city.php?id=${city.id}`;
      });
    });
  });

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
