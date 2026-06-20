<?php
// includes/header.php

// Determine active page
$current_page = basename($_SERVER['PHP_SELF']);

$is_dashboard = in_array($current_page, [
  'admin_dashboard.php',
  'buyer_dashboard.php',
  'seller_dashboard.php',
  'lawyer_dashboard.php'
]);

// Load Google Config
require_once __DIR__ . '/../config/google.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MahaAuctions - Maharashtra Premium Statutory Auction & Heavy Deposit Portal</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="./assets/favicon.svg">

  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  
  <!-- Local Custom CSS -->
  <link rel="stylesheet" href="./assets/index.css">

  <!-- Tailwind Play CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            premium: {
              light: '#ffffff',
              bg: '#f8fafc',
              text: '#0f172a',
              muted: '#64748b',
              emerald: '#059669',
              emeraldHover: '#047857',
              gold: '#d97706',
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Lucide Icons Library -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Google Identity Services -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <style>
    @keyframes scanEffect {
      0% { top: 10%; }
      50% { top: 90%; }
      100% { top: 10%; }
    }
    .animate-scan {
      animation: scanEffect 2s infinite ease-in-out;
    }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col antialiased">

  <!-- Global Header Navigation -->
  <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20">
        <!-- Logo and brand -->
        <div class="flex items-center">
          <a href="index.php" class="flex items-center space-x-2 bg-gradient-to-r from-premium-emerald to-teal-600 bg-clip-text text-transparent group">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-premium-emerald to-teal-600 text-white shadow-md shadow-emerald-200/50 group-hover:scale-105 transition-transform">
              <i data-lucide="building-2" class="h-6 w-6"></i>
            </div>
            <span class="text-2xl font-black tracking-tight">Maha<span class="text-slate-900">Auctions</span></span>
          </a>
        </div>

        <!-- Desktop Navigation Items -->
        <div class="hidden md:flex items-center space-x-8">
          <?php if (!$is_dashboard): ?>
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Home</a>
            <a href="search.php" class="<?php echo $current_page == 'search.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Data Surfing</a>
            <a href="advisory.php" class="<?php echo $current_page == 'advisory.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Legal Guidance (Adv)</a>
            <a href="agents.php" class="<?php echo $current_page == 'agents.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Verified Agents</a>
            <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">About</a>
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'buyer'): ?>
              <a href="buyer_dashboard.php" class="<?php echo $current_page == 'buyer_dashboard.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Buyer Dashboard</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'seller'): ?>
              <a href="seller_dashboard.php" class="<?php echo $current_page == 'seller_dashboard.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Seller Dashboard</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'lawyer'): ?>
              <a href="lawyer_dashboard.php" class="<?php echo $current_page == 'lawyer_dashboard.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Lawyer Dashboard</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
              <a href="admin_dashboard.php" class="<?php echo $current_page == 'admin_dashboard.php' ? 'text-premium-emerald border-premium-emerald' : 'text-slate-600 border-transparent hover:text-slate-900'; ?> border-b-2 py-2 text-sm font-semibold transition-all">Admin Dashboard</a>
            <?php endif; ?>
          <?php else: ?>
            <a href="index.php" class="text-slate-650 hover:text-slate-900 border-b-2 border-transparent hover:border-slate-300 py-2 text-sm font-bold transition-all flex items-center space-x-1.5">
              <i data-lucide="arrow-left" class="h-4 w-4"></i>
              <span>Back to Main Site</span>
            </a>
            <span class="text-premium-emerald border-premium-emerald border-b-2 py-2 text-sm font-extrabold uppercase tracking-wider transition-all">
              <?php 
                if ($current_page === 'admin_dashboard.php') echo 'Admin Portal';
                elseif ($current_page === 'buyer_dashboard.php') echo 'Buyer Portal';
                elseif ($current_page === 'seller_dashboard.php') echo 'Seller Portal';
                elseif ($current_page === 'lawyer_dashboard.php') echo 'Lawyer Portal';
              ?>
            </span>
          <?php endif; ?>
        </div>

        <!-- Auth button / Profile Info -->
        <div class="hidden md:flex items-center space-x-4">
          <?php if (isset($_SESSION['user'])): ?>
            <div class="flex items-center space-x-3 bg-slate-50 border border-slate-100 rounded-full py-1.5 pl-3 pr-4 shadow-sm hover:shadow transition-shadow">
              <img src="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80'); ?>" alt="Avatar" class="h-8 w-8 rounded-full border border-slate-200">
              <span class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
              <span class="text-xs text-slate-400 font-semibold uppercase bg-slate-200 px-2 py-0.5 rounded"><?php echo htmlspecialchars($_SESSION['user']['role']); ?></span>
              <a href="logout.php" class="text-slate-400 hover:text-red-500 transition-colors ml-2" title="Sign Out">
                <i data-lucide="log-out" class="h-4 w-4"></i>
              </a>
            </div>
          <?php else: ?>
            <button onclick="openAuthModal()" class="inline-flex items-center space-x-1.5 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-slate-200 transition-all hover:-translate-y-0.5">
              <i data-lucide="user" class="h-4 w-4"></i>
              <span>Register / Login</span>
            </button>
          <?php endif; ?>
        </div>

        <!-- Mobile menu toggler -->
        <div class="flex items-center md:hidden">
          <button id="mobile-menu-toggle" class="text-slate-600 p-2 rounded-lg hover:bg-slate-50 focus:outline-none">
            <i data-lucide="menu" class="h-6 w-6"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2 shadow-inner">
      <?php if (!$is_dashboard): ?>
        <a href="index.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'index.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Home</a>
        <a href="search.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'search.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Data Surfing</a>
        <a href="advisory.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'advisory.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Legal Guidance (Adv)</a>
        <a href="agents.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'agents.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Verified Agents</a>
        <a href="about.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'about.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">About</a>
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'buyer'): ?>
          <a href="buyer_dashboard.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'buyer_dashboard.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Buyer Dashboard</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'seller'): ?>
          <a href="seller_dashboard.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'seller_dashboard.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Seller Dashboard</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'lawyer'): ?>
          <a href="lawyer_dashboard.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'lawyer_dashboard.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Lawyer Dashboard</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
          <a href="admin_dashboard.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'admin_dashboard.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Admin Dashboard</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="index.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-slate-50">
          <i data-lucide="arrow-left" class="inline-block h-4 w-4 mr-1.5 align-middle"></i>
          <span class="align-middle">Back to Main Site</span>
        </a>
        <div class="block px-3 py-2.5 rounded-lg text-base font-bold text-premium-emerald bg-emerald-50">
          <?php 
            if ($current_page === 'admin_dashboard.php') echo 'Admin Portal';
            elseif ($current_page === 'buyer_dashboard.php') echo 'Buyer Portal';
            elseif ($current_page === 'seller_dashboard.php') echo 'Seller Portal';
            elseif ($current_page === 'lawyer_dashboard.php') echo 'Lawyer Portal';
          ?>
        </div>
      <?php endif; ?>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
        <?php if (isset($_SESSION['user'])): ?>
          <div class="flex items-center space-x-3">
            <img src="<?php echo htmlspecialchars($_SESSION['user']['avatar'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80'); ?>" alt="Avatar" class="h-8 w-8 rounded-full border border-slate-200">
            <div>
              <div class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></div>
              <div class="text-xs text-slate-400 font-semibold uppercase"><?php echo htmlspecialchars($_SESSION['user']['role']); ?></div>
            </div>
          </div>
          <a href="logout.php" class="inline-flex items-center space-x-1 text-sm font-bold text-red-500 bg-red-50 px-3 py-2 rounded-lg">
            <i data-lucide="log-out" class="h-4 w-4"></i>
            <span>Logout</span>
          </a>
        <?php else: ?>
          <button onclick="openAuthModal()" class="w-full inline-flex items-center justify-center space-x-1.5 bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl text-sm font-bold shadow-md">
            <i data-lucide="user" class="h-4 w-4"></i>
            <span>Register / Login</span>
          </button>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <script>
    // Toggle Mobile Navigation Drawer
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileToggle && mobileMenu) {
      mobileToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }
  </script>
