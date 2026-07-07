<?php
// index.php
require_once 'config/db.php';

// Fetch cities for map marker initialization
$cities_stmt = $pdo->query("SELECT * FROM cities ORDER BY name ASC");
$cities = $cities_stmt->fetchAll();

// Fetch all properties to populate hover popups on the district map
$all_prop_stmt = $pdo->query("SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id ORDER BY p.created_at DESC");
$all_properties = $all_prop_stmt->fetchAll();

// Featured properties (limit 3 for recent listings grid)
$properties = array_slice($all_properties, 0, 3);

require_once 'includes/header.php';
?>

<!-- Hero Section (Luxury Real Estate + Auction theme with 3D Carousels) -->
<div id="hero-section" class="relative bg-[#0F172A] text-white overflow-hidden py-8 md:py-14 lg:py-16 xl:py-20 min-h-[500px] lg:min-h-[600px] flex items-center">
  <!-- Subtle low opacity grid overlay -->
  <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30 pointer-events-none"></div>

  <!-- Subtle abstract blurred emerald lighting (vignette + glow) -->
  <div class="absolute -left-[10%] top-[10%] w-[40%] h-[60%] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>
  <div class="absolute -right-[10%] bottom-[10%] w-[40%] h-[60%] bg-teal-500/10 rounded-full blur-[120px] pointer-events-none"></div>

  <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
    <div class="grid grid-cols-1 md:grid-cols-12 lg:grid-cols-12 gap-8 lg:gap-6 items-center">
      
      <!-- Left Zone (3 columns out of 12) -->
      <div class="hidden lg:block lg:col-span-3 h-[460px] relative hero-3d-scene" aria-hidden="true">
        <div class="hero-track-container">
          <div class="card-track card-track-left space-y-8 py-4">
            <!-- Property 1 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">SARFAESI FORECLOSURE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80" alt="Worli Apartment" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Worli 3 BHK Sea Apartment</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Worli, Mumbai</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 4.50 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>
            <!-- Property 2 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0 pop-front">
              <div class="pop-badge bg-gradient-to-r from-amber-500 to-amber-600">PREMIUM OFFICE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80" alt="BKC Office" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Commercial Space in BKC</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">BKC, Mumbai</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 12.00 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">COMMERCIAL</span>
                </div>
              </div>
            </div>
            <!-- Property 3 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-teal-500 to-emerald-600">LUXURY VILLA</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80" alt="Koregaon Villa" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">4 BHK Koregaon Park Villa</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Koregaon Park, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 1.80 L / Mo</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RENTAL</span>
                </div>
              </div>
            </div>
            <!-- Property 4 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HIGH-RISE TOWERS</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80" alt="Trump Penthouse" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Trump Towers Penthouse</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Kalyani Nagar, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 8.20 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>
            <!-- Property 5 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HIGH-END ESTATE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80" alt="Hiranandani Flat" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Hiranandani Estate 3 BHK</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Ghodbunder Rd, Thane</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 2.10 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>

            <!-- Duplicate for Infinite Loop -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">SARFAESI FORECLOSURE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80" alt="Worli Apartment" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Worli 3 BHK Sea Apartment</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Worli, Mumbai</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 4.50 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0 pop-front">
              <div class="pop-badge bg-gradient-to-r from-amber-500 to-amber-600">PREMIUM OFFICE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80" alt="BKC Office" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Commercial Space in BKC</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">BKC, Mumbai</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 12.00 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">COMMERCIAL</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-teal-500 to-emerald-600">LUXURY VILLA</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80" alt="Koregaon Villa" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">4 BHK Koregaon Park Villa</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Koregaon Park, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 1.80 L / Mo</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RENTAL</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HIGH-RISE TOWERS</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80" alt="Trump Penthouse" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Trump Towers Penthouse</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Kalyani Nagar, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 8.20 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HIGH-END ESTATE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80" alt="Hiranandani Flat" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Hiranandani Estate 3 BHK</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Ghodbunder Rd, Thane</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 2.10 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">RESIDENTIAL</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Center Zone (6 columns out of 12 on desktop, 8 columns on tablet, 12 columns on mobile) -->
      <div class="col-span-1 md:col-span-8 lg:col-span-6 text-center space-y-6 z-30 px-4 md:px-6">
        <div class="inline-flex items-center space-x-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 sm:px-4 sm:py-1.5 rounded-full text-emerald-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider shadow-sm backdrop-blur-md">
          <i data-lucide="map-pin" class="h-3.5 w-3.5 sm:h-4 sm:w-4"></i>
          <span>Maharashtra District Council</span>
        </div>
        
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
          Premium Statutory <br class="hidden sm:inline" />
          Auction & <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent whitespace-nowrap">Property Portal</span>
        </h1>
        
        <p class="text-slate-350 max-w-2xl mx-auto text-xs sm:text-base font-medium leading-relaxed">
          Explore vetted court auctions, private seller listings, monthly rentals, and high-value heavy deposit flats verified under ready reckoner valuations.
        </p>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-3 pt-2 sm:pt-4 max-w-md mx-auto">
          <a href="search.php" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl text-sm font-extrabold shadow-lg shadow-emerald-500/20 transition-all touch-target">
            <i data-lucide="search" class="h-5 w-5"></i>
            <span>Explore Foreclosures</span>
          </a>
          <button onclick="openTrialModal('Hero Section Alert')" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 border border-white/10 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl text-sm font-extrabold backdrop-blur-md transition-all touch-target">
            <i data-lucide="bell" class="h-5 w-5"></i>
            <span>Setup Instant SMS Alerts</span>
          </button>
        </div>
      </div>
      
      <!-- Right Zone (3 columns out of 12 on desktop, 4 columns on tablet, hidden on mobile) -->
      <div class="hidden md:block md:col-span-4 lg:col-span-3 h-[460px] relative hero-3d-scene" aria-hidden="true">
        <div class="hero-track-container">
          <div class="card-track card-track-right space-y-8 py-4">
            <!-- Property 6 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-indigo-500 to-blue-600">AGRICULTURAL LAND</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1552594612-9c3f256a4276?auto=format&fit=crop&w=800&q=80" alt="Nashik Farmhouse" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Nashik Grape Vineyard</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Dindori, Nashik</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 3.20 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <!-- Property 7 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0 pop-front">
              <div class="pop-badge bg-gradient-to-r from-orange-500 to-red-600">INDUSTRIAL SHED</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1580982327559-c1202864eb05?auto=format&fit=crop&w=800&q=80" alt="Bhosari Shed" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">MIDC Bhosari Industrial Shed</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Bhosari MIDC, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 2.80 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <!-- Property 8 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HEAVY DEPOSIT</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" alt="Meadows Deposit" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">2 BHK Meadows Flat</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Hiranandani, Thane</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 25.00 Lakhs</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">DEPOSIT</span>
                </div>
              </div>
            </div>
            <!-- Property 9 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-emerald-500 to-cyan-600">LUXURY ESTATE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80" alt="Lonavala Villa" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Lonavala Valley View Villa</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Gold Valley, Lonavala</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 6.50 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <!-- Property 10 -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">RETAIL SHOWROOM</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?auto=format&fit=crop&w=800&q=80" alt="Viman Retail" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Retail Showroom space</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Viman Nagar, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 4.10 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">COMMERCIAL</span>
                </div>
              </div>
            </div>

            <!-- Duplicate for Infinite Loop -->
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-indigo-500 to-blue-600">AGRICULTURAL LAND</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1552594612-9c3f256a4276?auto=format&fit=crop&w=800&q=80" alt="Nashik Farmhouse" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Nashik Grape Vineyard</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Dindori, Nashik</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 3.20 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0 pop-front">
              <div class="pop-badge bg-gradient-to-r from-orange-500 to-red-600">INDUSTRIAL SHED</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1580982327559-c1202864eb05?auto=format&fit=crop&w=800&q=80" alt="Bhosari Shed" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">MIDC Bhosari Industrial Shed</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Bhosari MIDC, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 2.80 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">HEAVY DEPOSIT</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" alt="Meadows Deposit" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">2 BHK Meadows Flat</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Hiranandani, Thane</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 25.00 Lakhs</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">DEPOSIT</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge bg-gradient-to-r from-emerald-500 to-cyan-600">LUXURY ESTATE</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80" alt="Lonavala Villa" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Lonavala Valley View Villa</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Gold Valley, Lonavala</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 6.50 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">AUCTION</span>
                </div>
              </div>
            </div>
            <div class="hero-3d-card flex flex-col justify-between shrink-0">
              <div class="pop-badge">RETAIL SHOWROOM</div>
              <div class="hero-3d-card-img-container">
                <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?auto=format&fit=crop&w=800&q=80" alt="Viman Retail" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
              </div>
              <div class="hero-3d-card-details flex-grow flex flex-col justify-between">
                <div class="space-y-0.5">
                  <h4 class="text-xs font-black text-white truncate">Retail Showroom space</h4>
                  <p class="text-[9px] font-bold text-slate-400 flex items-center space-x-0.5">
                    <i data-lucide="map-pin" class="h-2.5 w-2.5 text-emerald-400 shrink-0"></i>
                    <span class="truncate">Viman Nagar, Pune</span>
                  </p>
                </div>
                <div class="flex justify-between items-center border-t border-white/5 pt-1.5 mt-1 text-[10px]">
                  <span class="font-extrabold text-emerald-400">₹ 4.10 Cr</span>
                  <span class="bg-white/10 px-1.5 py-0.5 rounded text-[8px] font-black text-slate-300 uppercase tracking-wider">COMMERCIAL</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bank Partners Marquee Section -->
<?php
$marquee_banks = [
    ['name' => 'State Bank of India', 'logo' => 'sbi.svg'],
    ['name' => 'Bank of Baroda', 'logo' => 'bob.svg'],
    ['name' => 'Punjab National Bank', 'logo' => 'pnb.svg'],
    ['name' => 'Axis Bank', 'logo' => 'axis_bank.svg'],
    ['name' => 'Union Bank of India', 'logo' => 'ubi.svg'],
    ['name' => 'Indian Bank', 'logo' => 'indian.svg'],
    ['name' => 'Saraswat Bank', 'logo' => 'saraswat.png'],
    ['name' => 'Punjab & Sind Bank', 'logo' => 'psb.svg'],
    ['name' => 'Axis Finance', 'logo' => 'axis_finance.svg'],
    ['name' => 'Ujjivan Small Finance', 'logo' => 'ujjivan.svg'],
];
?>
<div class="w-full bg-slate-50/50 border-y border-slate-200/80 py-8 overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
    <div class="text-center">
      <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Our Banking & Statutory Partners</p>
      <h3 class="text-sm sm:text-base font-black text-slate-700 mt-0.5">Properties Auctioned Directly by Leading Financial Institutions</h3>
    </div>
  </div>

  <div class="relative w-full space-y-4">
    <!-- Row 1: Flowing Left -->
    <div class="marquee-container overflow-hidden w-full relative flex">
      <div class="marquee-left-inner flex gap-8 whitespace-nowrap">
        <!-- Loop 1 -->
        <?php foreach ($marquee_banks as $mb): ?>
          <div class="inline-flex items-center justify-center bg-white border border-slate-100/90 px-5 py-3 rounded-2xl shadow-xs shrink-0 select-none h-14 sm:h-16 w-32 sm:w-40">
            <img src="assets/bank-logos/<?php echo $mb['logo']; ?>" alt="<?php echo $mb['name']; ?>" class="h-8 sm:h-9 max-w-full object-contain transition-transform duration-300 hover:scale-105">
          </div>
        <?php endforeach; ?>
        <!-- Loop 2 (Duplicate for seamless loop) -->
        <?php foreach ($marquee_banks as $mb): ?>
          <div class="inline-flex items-center justify-center bg-white border border-slate-100/90 px-5 py-3 rounded-2xl shadow-xs shrink-0 select-none h-14 sm:h-16 w-32 sm:w-40">
            <img src="assets/bank-logos/<?php echo $mb['logo']; ?>" alt="<?php echo $mb['name']; ?>" class="h-8 sm:h-9 max-w-full object-contain transition-transform duration-300 hover:scale-105">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Row 2: Flowing Right -->
    <div class="marquee-container overflow-hidden w-full relative flex">
      <div class="marquee-right-inner flex gap-8 whitespace-nowrap">
        <!-- Loop 1 -->
        <?php foreach ($marquee_banks as $mb): ?>
          <div class="inline-flex items-center justify-center bg-white border border-slate-100/90 px-5 py-3 rounded-2xl shadow-xs shrink-0 select-none h-14 sm:h-16 w-32 sm:w-40">
            <img src="assets/bank-logos/<?php echo $mb['logo']; ?>" alt="<?php echo $mb['name']; ?>" class="h-8 sm:h-9 max-w-full object-contain transition-transform duration-300 hover:scale-105">
          </div>
        <?php endforeach; ?>
        <!-- Loop 2 (Duplicate for seamless loop) -->
        <?php foreach ($marquee_banks as $mb): ?>
          <div class="inline-flex items-center justify-center bg-white border border-slate-100/90 px-5 py-3 rounded-2xl shadow-xs shrink-0 select-none h-14 sm:h-16 w-32 sm:w-40">
            <img src="assets/bank-logos/<?php echo $mb['logo']; ?>" alt="<?php echo $mb['name']; ?>" class="h-8 sm:h-9 max-w-full object-contain transition-transform duration-300 hover:scale-105">
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>


<!-- Interactive Leaflet Map Section (Mobile Edge-to-Edge with 12-16px outer padding) -->
<div class="w-full max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 pt-4 pb-2 sm:py-12">
  <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-lg overflow-hidden p-2 sm:p-6 space-y-3 sm:space-y-6">
    
    <!-- Cleaner Header (📍 Maharashtra Command Center) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 pb-1 sm:pb-3 border-b border-slate-100">
      <div>
        <h2 class="text-lg sm:text-2xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
          <i data-lucide="map-pin" class="h-5 w-5 sm:h-6 sm:w-6 text-premium-emerald shrink-0"></i>
          <span>Maharashtra Command Center</span>
        </h2>
        <div class="flex flex-wrap items-center gap-2 mt-1">
          <p class="text-[11px] sm:text-xs text-slate-500 font-semibold">Live property database by district</p>
          <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/50 uppercase tracking-wider animate-pulse select-none">
            Other States Coming Soon
          </span>
        </div>
      </div>
      <div class="hidden sm:flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 text-[10px] sm:text-xs font-bold text-slate-600">
        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></div>
        <span>Live Database Coordinates & Division Sync</span>
      </div>
    </div>

    <!-- Luxury District Filter Control Panel -->
    <?php
    $city_division_map = [
        'Amravati' => ['div' => 'अमरावती विभाग', 'color' => '#f87171'],
        'Akola' => ['div' => 'अमरावती विभाग', 'color' => '#f87171'],
        'Buldana' => ['div' => 'अमरावती विभाग', 'color' => '#f87171'],
        'Washim' => ['div' => 'अमरावती विभाग', 'color' => '#f87171'],
        'Yavatmal' => ['div' => 'अमरावती विभाग', 'color' => '#f87171'],
        'Aurangabad' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Jalna' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Parbhani' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Hingoli' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Bid' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Nanded' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Latur' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Osmanabad' => ['div' => 'छत्रपती संभाजीनगर विभाग', 'color' => '#818cf8'],
        'Mumbai' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Mumbai Suburban' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Thane' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Raigarh' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Ratnagiri' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Sindhudurg' => ['div' => 'कोकण विभाग', 'color' => '#9ca3af'],
        'Nagpur' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Wardha' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Bhandara' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Gondiya' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Chandrapur' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Garhchiroli' => ['div' => 'नागपूर विभाग', 'color' => '#e5a970'],
        'Nashik' => ['div' => 'नाशिक विभाग', 'color' => '#eab308'],
        'Nandurbar' => ['div' => 'नाशिक विभाग', 'color' => '#eab308'],
        'Dhule' => ['div' => 'नाशिक विभाग', 'color' => '#eab308'],
        'Jalgaon' => ['div' => 'नाशिक विभाग', 'color' => '#eab308'],
        'Ahmadnagar' => ['div' => 'नाशिक विभाग', 'color' => '#eab308'],
        'Pune' => ['div' => 'पुणे विभाग', 'color' => '#4ade80'],
        'Satara' => ['div' => 'पुणे विभाग', 'color' => '#4ade80'],
        'Solapur' => ['div' => 'पुणे विभाग', 'color' => '#4ade80'],
        'Sangli' => ['div' => 'पुणे विभाग', 'color' => '#4ade80'],
        'Kolhapur' => ['div' => 'पुणे विभाग', 'color' => '#4ade80'],
    ];
    ?>
    <div class="bg-slate-50/70 border border-slate-200/65 rounded-2xl p-4 sm:p-5 space-y-4 shadow-sm">
      <!-- First Row: Search Box & Dropdown Filters Side-by-Side -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        
        <!-- Left: Search Box -->
        <div class="relative w-full lg:w-72 group shrink-0">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <i class="h-4 w-4 text-slate-400 group-focus-within:text-premium-emerald transition-colors" data-lucide="search"></i>
          </span>
          <input type="text" id="city-search-input" oninput="filterDistricts()" placeholder="Search district by name..." class="w-full pl-10 pr-10 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-750 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-premium-emerald transition-all shadow-xs">
          <button onclick="clearSearch()" id="search-clear-btn" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 hidden">
            <i class="h-4 w-4" data-lucide="x-circle"></i>
          </button>
        </div>

        <!-- Right: Dropdown Filters (including Division, Category, Type, Activity) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 w-full flex-1">
          <!-- Administrative Division Filter -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i class="h-3.5 w-3.5 text-slate-400" data-lucide="map-pin"></i>
            </span>
            <select id="filter-division" onchange="filterDistricts()" class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-premium-emerald transition-all shadow-xs appearance-none cursor-pointer">
              <option value="all">All Divisions</option>
              <option value="कोकण विभाग">Konkan</option>
              <option value="पुणे विभाग">Pune</option>
              <option value="नाशिक विभाग">Nashik</option>
              <option value="छत्रपती संभाजीनगर विभाग">Aurangabad</option>
              <option value="अमरावती विभाग">Amravati</option>
              <option value="नागपूर विभाग">Nagpur</option>
            </select>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <i class="h-3 w-3 text-slate-400" data-lucide="chevron-down"></i>
            </span>
          </div>

          <!-- Property Category Filter -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i class="h-3.5 w-3.5 text-slate-400" data-lucide="tag"></i>
            </span>
            <select id="filter-category" onchange="filterDistricts()" class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-premium-emerald transition-all shadow-xs appearance-none cursor-pointer">
              <option value="all">All Categories</option>
              <option value="Auction">Auction listings only</option>
              <option value="Rental">Rental listings only</option>
              <option value="Heavy Deposit">Heavy Deposit listings only</option>
              <option value="Seller Listed">Seller Listed only</option>
            </select>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <i class="h-3 w-3 text-slate-400" data-lucide="chevron-down"></i>
            </span>
          </div>

          <!-- Property Type Filter -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i class="h-3.5 w-3.5 text-slate-400" data-lucide="home"></i>
            </span>
            <select id="filter-type" onchange="filterDistricts()" class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-premium-emerald transition-all shadow-xs appearance-none cursor-pointer">
              <option value="all">All Property Types</option>
              <option value="Residential">Residential properties</option>
              <option value="Commercial">Commercial properties</option>
              <option value="Industrial">Industrial properties</option>
              <option value="Agricultural">Agricultural properties</option>
            </select>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <i class="h-3 w-3 text-slate-400" data-lucide="chevron-down"></i>
            </span>
          </div>

          <!-- Activity Filter -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i class="h-3.5 w-3.5 text-slate-400" data-lucide="activity"></i>
            </span>
            <select id="filter-activity" onchange="filterDistricts()" class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-premium-emerald transition-all shadow-xs appearance-none cursor-pointer">
              <option value="all">All Districts (Include Empty)</option>
              <option value="active">Active Districts Only</option>
            </select>
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <i class="h-3 w-3 text-slate-400" data-lucide="chevron-down"></i>
            </span>
          </div>
        </div>
      </div>

      <!-- District Chips horizontal scroll container by default -->
      <div id="cities-chip-container" class="flex flex-row flex-nowrap overflow-x-auto pb-3 gap-2 pt-2 custom-scrollbar transition-all">
        <?php foreach ($cities as $c): 
          $name_key = $c['name'];
          $div_info = isset($city_division_map[$name_key]) ? $city_division_map[$name_key] : ['div' => 'इतर', 'color' => '#cbd5e1'];
        ?>
          <button type="button" 
                  data-city-name="<?php echo htmlspecialchars(strtolower($c['name'])); ?>"
                  data-division="<?php echo htmlspecialchars($div_info['div']); ?>"
                  data-active="<?php echo $c['property_count']; ?>"
                  onclick="focusDistrict('<?php echo urlencode($c['name']); ?>')" 
                  class="city-chip inline-flex items-center space-x-2 px-3 py-2 bg-white hover:bg-slate-50 text-slate-800 border <?php echo ($c['property_count'] > 0) ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200'; ?> rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-xs font-bold shrink-0">
            <span class="w-2 h-2 rounded-full shrink-0 shadow-inner" style="background-color: <?php echo $div_info['color']; ?>;"></span>
            <span class="font-extrabold text-slate-705 city-name-label"><?php echo htmlspecialchars($c['name']); ?></span>
            <span class="city-count-badge bg-emerald-500/10 text-premium-emerald font-black px-2 py-0.5 rounded-lg text-[9px] <?php echo ($c['property_count'] > 0) ? '' : 'hidden'; ?>">
              <?php echo $c['property_count']; ?>
            </span>
          </button>
        <?php endforeach; ?>
        <div id="city-no-match-msg" class="hidden text-slate-400 text-xs font-medium py-2 px-1">No matching districts found for selected filter</div>
      </div>

      <!-- Footer Statistics -->
      <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold border-t border-slate-200/50 pt-3 mt-1">
        <div id="filter-results-stats">Showing 35 of 35 districts</div>
        <div class="flex items-center space-x-2">
          <!-- Toggle View Button -->
          <button type="button" id="toggle-expand-btn" onclick="toggleChipsView()" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-lg transition-all flex items-center space-x-1 cursor-pointer text-[10px] select-none touch-target">
            <i class="h-3.5 w-3.5 text-slate-400" data-lucide="grid"></i>
            <span id="toggle-expand-text">Show All Grid</span>
          </button>
          <div class="h-3 w-[1px] bg-slate-250"></div>
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          <span>Click a district chip to highlight on map</span>
        </div>
      </div>
    </div>

    <!-- Map & Legend Wrapper -->
    <div class="relative w-full rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
      <!-- Leaflet Mount Div (Fluid responsive aspect-ratio) -->
      <div id="leaflet-landing-map" class="w-full z-10"></div>

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
<div id="district-bottom-sheet" class="fixed inset-x-0 bottom-0 z-[501] bg-white rounded-t-3xl p-5 border-t border-slate-200 shadow-2xl transition-transform duration-300 transform translate-y-full max-w-lg mx-auto md:hidden">
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

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
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
  // Filter and search logic for districts
  let currentDivisionFilter = 'all';
  let isChipsExpanded = false;

  function toggleChipsView() {
    const container = document.getElementById('cities-chip-container');
    const btn = document.getElementById('toggle-expand-btn');
    const textSpan = document.getElementById('toggle-expand-text');
    const icon = btn ? btn.querySelector('i') : null;

    if (!container) return;

    if (!isChipsExpanded) {
      // Switch to Grid View
      container.classList.remove('flex-row', 'flex-nowrap', 'overflow-x-auto', 'pb-3');
      container.classList.add('flex-wrap');
      if (textSpan) textSpan.textContent = 'Collapse to Scrollbar';
      isChipsExpanded = true;
      if (icon) {
        icon.setAttribute('data-lucide', 'arrow-left-right');
      }
    } else {
      // Switch back to Scroll View
      container.classList.remove('flex-wrap');
      container.classList.add('flex-row', 'flex-nowrap', 'overflow-x-auto', 'pb-3');
      if (textSpan) textSpan.textContent = 'Show All Grid';
      isChipsExpanded = false;
      if (icon) {
        icon.setAttribute('data-lucide', 'grid');
      }
    }
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }

  function setDivisionFilter(divName) {
    const select = document.getElementById('filter-division');
    if (select) {
      select.value = divName;
    }
    filterDistricts();
  }
  window.setDivisionFilter = setDivisionFilter;

  function clearSearch() {
    const searchInput = document.getElementById('city-search-input');
    if (searchInput) {
      searchInput.value = '';
    }
    filterDistricts();
  }

  function filterDistricts() {
    const searchInput = document.getElementById('city-search-input');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    // Get filter values
    const selectedDivision = document.getElementById('filter-division') ? document.getElementById('filter-division').value : 'all';
    const selectedCategory = document.getElementById('filter-category').value;
    const selectedType = document.getElementById('filter-type').value;
    const selectedActivity = document.getElementById('filter-activity').value;

    const clearBtn = document.getElementById('search-clear-btn');
    if (clearBtn) {
      if (query.length > 0) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }

    const chips = document.querySelectorAll('.city-chip');
    let visibleCount = 0;
    let activeCount = 0;

    chips.forEach(chip => {
      const cityName = chip.querySelector('.city-name-label').textContent.trim();
      const division = chip.getAttribute('data-division') || '';

      // Recalculate count of properties in this city that match active category and type filters
      const cityMatchingProps = dbAllProperties.filter(p => {
        const matchesCity = p.city_name.toLowerCase().includes(cityName.toLowerCase()) || 
                            cityName.toLowerCase().includes(p.city_name.toLowerCase());
        const matchesCategory = (selectedCategory === 'all' || p.category === selectedCategory);
        const matchesType = (selectedType === 'all' || p.type === selectedType);
        return matchesCity && matchesCategory && matchesType;
      });

      const currentCount = cityMatchingProps.length;

      // Update badge text and visibility
      const badge = chip.querySelector('.city-count-badge');
      if (badge) {
        badge.textContent = currentCount;
        if (currentCount > 0) {
          badge.classList.remove('hidden');
          chip.classList.remove('border-slate-200');
          chip.classList.add('border-emerald-300', 'bg-emerald-50/20');
        } else {
          badge.classList.add('hidden');
          chip.classList.remove('border-emerald-300', 'bg-emerald-50/20');
          chip.classList.add('border-slate-200');
        }
      }

      // Check search match
      const matchesSearch = cityName.toLowerCase().includes(query);

      // Check division match
      let matchesDivision = false;
      if (selectedDivision === 'all') {
        matchesDivision = true;
      } else {
        matchesDivision = (division === selectedDivision);
      }

      // Check activity match
      let matchesActivity = true;
      if (selectedActivity === 'active') {
        matchesActivity = (currentCount > 0);
      }

      if (matchesSearch && matchesDivision && matchesActivity) {
        chip.classList.remove('hidden');
        chip.style.opacity = '1';
        chip.style.transform = 'scale(1)';
        visibleCount++;
        if (currentCount > 0) {
          activeCount++;
        }
      } else {
        chip.classList.add('hidden');
        chip.style.opacity = '0';
        chip.style.transform = 'scale(0.9)';
      }
    });

    const noMatchMsg = document.getElementById('city-no-match-msg');
    if (noMatchMsg) {
      if (visibleCount > 0) {
        noMatchMsg.classList.add('hidden');
      } else {
        noMatchMsg.classList.remove('hidden');
      }
    }

    // Update stats text
    const statsText = document.getElementById('filter-results-stats');
    if (statsText) {
      statsText.textContent = `Showing ${visibleCount} of ${chips.length} districts (${activeCount} active)`;
    }
  }

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
    // Responsive view logic
    const isMobileView = () => window.innerWidth < 768;
    const getInitialZoom = () => isMobileView() ? 5.8 : 6.8;
    const getInitialCenter = () => isMobileView() ? [19.3000, 76.5000] : [19.6000, 75.8000];

    // Initialize Leaflet map with support for fractional zooms and responsive boundaries
    const map = L.map('leaflet-landing-map', {
      scrollWheelZoom: false,
      zoomSnap: 0.1,
      zoomDelta: 0.5,
      minZoom: 5,
      maxZoom: 10
    }).setView(getInitialCenter(), getInitialZoom());

    let geoLayer = null;
    let hoveredFeature = null;

    const fitMapToState = () => {
      if (geoLayer) {
        map.invalidateSize();
        map.fitBounds(geoLayer.getBounds(), {
          padding: isMobileView() ? [10, 10] : [20, 20],
          animate: false
        });
      }
    };

    // Invalidate map size to prevent gray zones and rendering glitches
    setTimeout(() => {
      map.invalidateSize();
      fitMapToState();
    }, 100);

    window.addEventListener('load', () => {
      setTimeout(() => {
        map.invalidateSize();
        fitMapToState();
      }, 200);
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        map.invalidateSize();
        fitMapToState();
        if (window.innerWidth >= 768) {
          if (typeof closeBottomSheet === 'function') closeBottomSheet();
        }
      }, 250);
    });

    // Clean CartoDB Voyager No-Labels tile layer so GeoJSON polygons and district titles stand out clearly
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
    }).addTo(map);

    // Fetch and render 35 District polygons with division styling
    fetch('./assets/maharashtra_districts.geojson')
      .then(res => res.json())
      .then(geojsonData => {
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
            
            // Helper function to get filtered tooltip content dynamically
            const getDynamicTooltipContent = () => {
              const selectedCategory = document.getElementById('filter-category').value;
              const selectedType = document.getElementById('filter-type').value;

              const filteredProps = dbAllProperties.filter(p => {
                const matchesCity = p.city_name.toLowerCase().includes(distName.toLowerCase()) || 
                                    distName.toLowerCase().includes(p.city_name.toLowerCase());
                const matchesCategory = (selectedCategory === 'all' || p.category === selectedCategory);
                const matchesType = (selectedType === 'all' || p.type === selectedType);
                return matchesCity && matchesCategory && matchesType;
              });

              const totalCount = filteredProps.length;

              let propsListHtml = '';
              if (totalCount > 0) {
                propsListHtml = `
                  <div class="mt-2 space-y-1.5 border-t border-slate-100 pt-2 text-left">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Matching Listings:</span>
                    ${filteredProps.slice(0, 2).map(p => `
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
                    <span>0 matching properties under selected filters.</span>
                  </div>
                `;
              }

              return `
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
            };

            // Bind initial tooltip content
            layer.bindTooltip(getDynamicTooltipContent(), {
              sticky: true,
              direction: 'auto',
              opacity: 1,
              offset: [15, 15],
              className: 'premium-district-tooltip'
            });

            // Hover & Touch interactions
            layer.on({
              mouseover: (e) => {
                const l = e.target;
                
                // Dynamically update tooltip content on hover
                l.setTooltipContent(getDynamicTooltipContent());

                if (window.innerWidth >= 768) {
                  if (hoveredFeature && hoveredFeature !== l) {
                    geoLayer.resetStyle(hoveredFeature);
                  }
                  hoveredFeature = l;

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
                  const l = e.target;
                  geoLayer.resetStyle(l);
                  if (hoveredFeature === l) {
                    hoveredFeature = null;
                  }
                }
              },
              click: () => {
                const targetId = info.cityId || 'mumbai';
                
                const selectedCategory = document.getElementById('filter-category').value;
                const selectedType = document.getElementById('filter-type').value;

                const filteredProps = dbAllProperties.filter(p => {
                  const matchesCity = p.city_name.toLowerCase().includes(distName.toLowerCase()) || 
                                      distName.toLowerCase().includes(p.city_name.toLowerCase());
                  const matchesCategory = (selectedCategory === 'all' || p.category === selectedCategory);
                  const matchesType = (selectedType === 'all' || p.type === selectedType);
                  return matchesCity && matchesCategory && matchesType;
                });

                if (window.innerWidth < 768) {
                  showBottomSheet(distName, info, filteredProps.length, filteredProps, targetId);
                } else {
                  window.location.href = `city.php?id=${targetId}`;
                }
              }
            });
          }
        }).addTo(map);

        // Auto-fit bounds of GeoJSON to center the state perfectly on all devices
        fitMapToState();

        // Ensure the hovered styles and tooltips clean up perfectly when mouse leaves the map container
        const resetHoverState = () => {
          if (hoveredFeature) {
            geoLayer.resetStyle(hoveredFeature);
            hoveredFeature = null;
          }
          map.closeTooltip();
        };

        // 1. Leaflet map-level mouseout event
        map.on('mouseout', resetHoverState);

        // 2. Native DOM mouseleave and mouseout events on map container
        const mapContainer = document.getElementById('leaflet-landing-map');
        if (mapContainer) {
          mapContainer.addEventListener('mouseleave', resetHoverState);
          mapContainer.addEventListener('mouseout', (e) => {
            if (!mapContainer.contains(e.relatedTarget)) {
              resetHoverState();
            }
          });
        }

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
  window.closeBottomSheet = closeBottomSheet;

  function focusDistrict(dName) {
    const info = districtDivisionMap[dName] || { cityId: 'mumbai' };
    const targetId = info.cityId || 'mumbai';
    window.location.href = `city.php?id=${targetId}`;
  }
  window.focusDistrict = focusDistrict;

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

  // 3D Parallax effect on mouse movement for the hero section
  const heroSection = document.getElementById('hero-section');
  if (heroSection) {
    let tick = false;
    heroSection.addEventListener('mousemove', (e) => {
      if (!tick) {
        window.requestAnimationFrame(() => {
          const rect = heroSection.getBoundingClientRect();
          const x = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2); // -1 to 1
          const y = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2); // -1 to 1
          heroSection.style.setProperty('--mouse-x', x.toFixed(3));
          heroSection.style.setProperty('--mouse-y', y.toFixed(3));
          tick = false;
        });
        tick = true;
      }
    });

    // Reset rotation on mouse leave
    heroSection.addEventListener('mouseleave', () => {
      heroSection.style.setProperty('--mouse-x', '0');
      heroSection.style.setProperty('--mouse-y', '0');
    });
  }
</script>

<?php
// Include auth modals and footer layout
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>

