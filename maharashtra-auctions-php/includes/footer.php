<?php
// includes/footer.php
?>
  <!-- Global Footer -->
  <footer class="bg-gradient-to-b from-slate-900 via-slate-950 to-black text-slate-400 border-t border-slate-800/80 pt-16 pb-8 relative overflow-hidden font-sans mt-auto">
    <!-- Top Decorative Line with Glow -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Main Grid Layout -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 pb-12 border-b border-slate-800/60">
        
        <!-- Brand Info Column -->
        <div class="space-y-4 col-span-1 lg:col-span-2">
          <a href="index.php" class="flex items-center space-x-2 group">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-900/30 group-hover:scale-105 transition-transform duration-300">
              <i data-lucide="building-2" class="h-6 w-6"></i>
            </div>
            <span class="text-2xl font-black tracking-tight text-white">Maha<span class="text-emerald-500">Auctions</span></span>
          </a>
          <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
            Maharashtra's premier statutory auction portal for bank-enforced assets, commercial properties, and residential lands under the SARFAESI Act.
          </p>
          
          <!-- Social Icons -->
          <div class="flex items-center space-x-3 pt-3">
            <!-- Instagram -->
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" 
               class="w-10 h-10 rounded-xl bg-slate-800/40 border border-slate-700/40 flex items-center justify-center text-slate-400 hover:text-white hover:border-transparent hover:bg-gradient-to-tr hover:from-amber-500 hover:via-pink-500 hover:to-purple-600 transition-all duration-300 shadow-md group/social" 
               title="Instagram">
              <svg class="w-5 h-5 stroke-current fill-none group-hover/social:scale-110 transition-transform duration-300" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
              </svg>
            </a>
            <!-- LinkedIn -->
            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" 
               class="w-10 h-10 rounded-xl bg-slate-800/40 border border-slate-700/40 flex items-center justify-center text-slate-400 hover:text-white hover:border-transparent hover:bg-[#0a66c2] transition-all duration-300 shadow-md group/social" 
               title="LinkedIn">
              <svg class="w-5 h-5 fill-current group-hover/social:scale-110 transition-transform duration-300" viewBox="0 0 24 24">
                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"></path>
              </svg>
            </a>
            <!-- Facebook -->
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" 
               class="w-10 h-10 rounded-xl bg-slate-800/40 border border-slate-700/40 flex items-center justify-center text-slate-400 hover:text-white hover:border-transparent hover:bg-[#1877f2] transition-all duration-300 shadow-md group/social" 
               title="Facebook">
              <svg class="w-5 h-5 fill-current group-hover/social:scale-110 transition-transform duration-300" viewBox="0 0 24 24">
                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"></path>
              </svg>
            </a>
            <!-- Twitter / X -->
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" 
               class="w-10 h-10 rounded-xl bg-slate-800/40 border border-slate-700/40 flex items-center justify-center text-slate-400 hover:text-white hover:border-transparent hover:bg-black transition-all duration-300 shadow-md group/social" 
               title="Twitter / X">
              <svg class="w-4 h-4 fill-current group-hover/social:scale-110 transition-transform duration-300" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
              </svg>
            </a>
          </div>
        </div>

        <!-- Explore Columns -->
        <div>
          <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4 relative after:content-[''] after:absolute after:bottom-[-6px] after:left-0 after:w-8 after:h-[2px] after:bg-emerald-500">Explore</h3>
          <ul class="space-y-2.5 text-sm pt-2">
            <li>
              <a href="index.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Home</span>
              </a>
            </li>
            <li>
              <a href="search.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Data Surfing</span>
              </a>
            </li>
            <li>
              <a href="advisory.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Legal Guidance</span>
              </a>
            </li>
            <li>
              <a href="agents.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Verified Agents</span>
              </a>
            </li>
            <li>
              <a href="about.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>About Portal</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Portals Columns -->
        <div>
          <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4 relative after:content-[''] after:absolute after:bottom-[-6px] after:left-0 after:w-8 after:h-[2px] after:bg-emerald-500">Portals</h3>
          <ul class="space-y-2.5 text-sm pt-2">
            <li>
              <a href="buyer_dashboard.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Buyer Console</span>
              </a>
            </li>
            <li>
              <a href="seller_dashboard.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Seller Desk</span>
              </a>
            </li>
            <li>
              <a href="lawyer_dashboard.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Legal Panel</span>
              </a>
            </li>
            <li>
              <a href="admin_dashboard.php" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>Admin Dashboard</span>
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-400 hover:text-emerald-400 hover:translate-x-1 inline-flex items-center transition-all duration-300 group">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 mr-1 text-slate-600 group-hover:text-emerald-400 transition-colors"></i>
                <span>SARFAESI Rules</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Contact Support Column -->
        <div>
          <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4 relative after:content-[''] after:absolute after:bottom-[-6px] after:left-0 after:w-8 after:h-[2px] after:bg-emerald-500">Contact Details</h3>
          <ul class="space-y-4 text-sm text-slate-400 pt-2">
            <li class="flex items-start space-x-3 group">
              <i data-lucide="map-pin" class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5 group-hover:scale-110 transition-transform"></i>
              <span>Office No. 8, First Floor, Charudataa Chambers, Kanhere Wadi, Near C.B.S. Signal, Nashik, Maharashtra, India - 422001</span>
            </li>
            <li class="flex items-center space-x-3 group">
              <i data-lucide="phone" class="w-4.5 h-4.5 text-emerald-500 shrink-0 group-hover:scale-110 transition-transform"></i>
              <span>+91 96044 96521</span>
            </li>
            <li class="flex items-center space-x-3 group">
              <i data-lucide="mail" class="w-4.5 h-4.5 text-emerald-500 shrink-0 group-hover:scale-110 transition-transform"></i>
              <span>support@mahaauctions.gov.in</span>
            </li>
            <li class="flex items-center space-x-3 group">
              <i data-lucide="clock" class="w-4.5 h-4.5 text-emerald-500 shrink-0 group-hover:scale-110 transition-transform"></i>
              <span>24x7 We Provide Service</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Bottom Credits and Certifications -->
      <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
        <div class="text-center md:text-left space-y-1">
          <p class="font-medium text-slate-400">MahaAuctions © 2026 Maharashtra statutory portal. All rights reserved.</p>
          <p class="text-slate-600">SARFAESI Securities enforcement division | Registered DM certified partners</p>
        </div>
        <div class="flex items-center space-x-6">
          <a href="#" class="hover:text-emerald-500 transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-emerald-500 transition-colors">Terms of Service</a>
          <a href="#" class="hover:text-emerald-500 transition-colors">Disclaimer</a>
        </div>
      </div>

    </div>
  </footer>

  <!-- Leaflet Map Library Javascript dependency -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  
  <script>
    // Initialize Lucide Icons on load
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  </script>
</body>
</html>
