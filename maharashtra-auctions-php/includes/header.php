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
    /* Hide scrollbar for clean horizontal tab scroll on mobile */
    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }
    /* Mobile-friendly touch improvements */
    @media (max-width: 640px) {
      .dashboard-content {
        padding-left: 1rem;
        padding-right: 1rem;
      }
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
        <div class="hidden md:flex items-center space-x-6">
          <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'text-premium-emerald border-premium-emerald font-bold' : 'text-slate-655 border-transparent hover:text-slate-900 font-semibold'; ?> border-b-2 py-2 text-sm transition-all">Home</a>
          <a href="search.php" class="<?php echo $current_page == 'search.php' ? 'text-premium-emerald border-premium-emerald font-bold' : 'text-slate-655 border-transparent hover:text-slate-900 font-semibold'; ?> border-b-2 py-2 text-sm transition-all">Data Surfing</a>
          <a href="advisory.php" class="<?php echo $current_page == 'advisory.php' ? 'text-premium-emerald border-premium-emerald font-bold' : 'text-slate-655 border-transparent hover:text-slate-900 font-semibold'; ?> border-b-2 py-2 text-sm transition-all">Legal Guidance</a>
          <a href="agents.php" class="<?php echo $current_page == 'agents.php' ? 'text-premium-emerald border-premium-emerald font-bold' : 'text-slate-655 border-transparent hover:text-slate-900 font-semibold'; ?> border-b-2 py-2 text-sm transition-all">Verified Agents</a>
          <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'text-premium-emerald border-premium-emerald font-bold' : 'text-slate-655 border-transparent hover:text-slate-900 font-semibold'; ?> border-b-2 py-2 text-sm transition-all">About</a>
          
          <?php if (isset($_SESSION['user'])): ?>
            <!-- Dashboard Portal Dropdown -->
            <div class="relative group py-2">
              <button class="flex items-center space-x-1 text-slate-700 hover:text-slate-900 font-bold text-sm focus:outline-none transition-all">
                <i data-lucide="layout-dashboard" class="h-4 w-4 text-premium-emerald"></i>
                <span>
                  <?php 
                    if ($_SESSION['user']['role'] === 'admin') echo 'Admin Portal';
                    elseif ($_SESSION['user']['role'] === 'buyer') echo 'Buyer Portal';
                    elseif ($_SESSION['user']['role'] === 'seller') echo 'Seller Portal';
                    elseif ($_SESSION['user']['role'] === 'lawyer') echo 'Lawyer Portal';
                  ?>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 group-hover:rotate-180 transition-transform duration-200"></i>
              </button>
              
              <!-- Dropdown Menu -->
              <div class="absolute left-0 mt-2 w-56 rounded-2xl bg-white border border-slate-150 shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform translate-y-1 group-hover:translate-y-0">
                <?php if ($_SESSION['user']['role'] === 'buyer'): ?>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'overview', 'switchBuyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="layout" class="h-4 w-4 text-slate-400"></i>
                    <span>Overview</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'bids', 'switchBuyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="gavel" class="h-4 w-4 text-slate-400"></i>
                    <span>My Bids & Offers</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'settings', 'switchBuyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="settings" class="h-4 w-4 text-slate-400"></i>
                    <span>Settings & Billing</span>
                  </a>
                <?php elseif ($_SESSION['user']['role'] === 'seller'): ?>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'overview', 'switchSellerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="store" class="h-4 w-4 text-slate-400"></i>
                    <span>Command Center</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'analytics', 'switchSellerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="bar-chart-2" class="h-4 w-4 text-slate-400"></i>
                    <span>Property Analytics</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'kyc', 'switchSellerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="shield" class="h-4 w-4 text-slate-400"></i>
                    <span>Account & KYC</span>
                  </a>
                <?php elseif ($_SESSION['user']['role'] === 'lawyer'): ?>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'overview', 'switchLawyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="scale" class="h-4 w-4 text-slate-400"></i>
                    <span>Command Center</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'profile', 'switchLawyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="user-check" class="h-4 w-4 text-slate-400"></i>
                    <span>Profile Editor</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'vault', 'switchLawyerTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="folder-lock" class="h-4 w-4 text-slate-400"></i>
                    <span>Document Vault</span>
                  </a>
                <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'stats', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="pie-chart" class="h-4 w-4 text-slate-400"></i>
                    <span>Overview Stats</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'users', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="users" class="h-4 w-4 text-slate-400"></i>
                    <span>Users Registry</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'listings', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="home" class="h-4 w-4 text-slate-400"></i>
                    <span>Properties Directory</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'add_property', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="plus-circle" class="h-4 w-4 text-slate-400"></i>
                    <span>Post New Property</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'leads', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="calendar" class="h-4 w-4 text-slate-400"></i>
                    <span>Leads Board</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'consults', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="message-square" class="h-4 w-4 text-slate-400"></i>
                    <span>Consultations</span>
                  </a>
                  <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'agents', 'switchAdminTab')" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-premium-emerald rounded-lg mx-2 transition-colors">
                    <i data-lucide="users-2" class="h-4 w-4 text-slate-400"></i>
                    <span>Agents & Cities</span>
                  </a>
                <?php endif; ?>
              </div>
            </div>
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
      <a href="index.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'index.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Home</a>
      <a href="search.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'search.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Data Surfing</a>
      <a href="advisory.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'advisory.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Legal Guidance</a>
      <a href="agents.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'agents.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">Verified Agents</a>
      <a href="about.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold <?php echo $current_page == 'about.php' ? 'text-premium-emerald bg-emerald-50' : 'text-slate-700 hover:bg-slate-50'; ?>">About</a>
      
      <?php if (isset($_SESSION['user'])): ?>
        <!-- Mobile Portal Links -->
        <div class="border-t border-slate-100 pt-2 mt-2">
          <div class="px-3 py-1.5 text-xs font-extrabold uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
            <i data-lucide="layout-dashboard" class="h-4 w-4 text-premium-emerald"></i>
            <span>
              <?php 
                if ($_SESSION['user']['role'] === 'admin') echo 'Admin Portal';
                elseif ($_SESSION['user']['role'] === 'buyer') echo 'Buyer Portal';
                elseif ($_SESSION['user']['role'] === 'seller') echo 'Seller Portal';
                elseif ($_SESSION['user']['role'] === 'lawyer') echo 'Lawyer Portal';
              ?>
            </span>
          </div>
          <div class="pl-4 space-y-1 mt-1">
            <?php if ($_SESSION['user']['role'] === 'buyer'): ?>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'overview', 'switchBuyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="layout" class="h-4 w-4 text-slate-400"></i>
                <span>Overview</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'bids', 'switchBuyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="gavel" class="h-4 w-4 text-slate-400"></i>
                <span>My Bids & Offers</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('buyer_dashboard.php', 'settings', 'switchBuyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="settings" class="h-4 w-4 text-slate-400"></i>
                <span>Settings & Billing</span>
              </a>
            <?php elseif ($_SESSION['user']['role'] === 'seller'): ?>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'overview', 'switchSellerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="store" class="h-4 w-4 text-slate-400"></i>
                <span>Command Center</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'analytics', 'switchSellerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="bar-chart-2" class="h-4 w-4 text-slate-400"></i>
                <span>Property Analytics</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('seller_dashboard.php', 'kyc', 'switchSellerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="shield" class="h-4 w-4 text-slate-400"></i>
                <span>Account & KYC</span>
              </a>
            <?php elseif ($_SESSION['user']['role'] === 'lawyer'): ?>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'overview', 'switchLawyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="scale" class="h-4 w-4 text-slate-400"></i>
                <span>Command Center</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'profile', 'switchLawyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="user-check" class="h-4 w-4 text-slate-400"></i>
                <span>Profile Editor</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('lawyer_dashboard.php', 'vault', 'switchLawyerTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="folder-lock" class="h-4 w-4 text-slate-400"></i>
                <span>Document Vault</span>
              </a>
            <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'stats', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="pie-chart" class="h-4 w-4 text-slate-400"></i>
                <span>Overview Stats</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'users', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="users" class="h-4 w-4 text-slate-400"></i>
                <span>Users Registry</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'listings', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="home" class="h-4 w-4 text-slate-400"></i>
                <span>Properties Directory</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'add_property', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="plus-circle" class="h-4 w-4 text-slate-400"></i>
                <span>Post New Property</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'leads', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="calendar" class="h-4 w-4 text-slate-400"></i>
                <span>Leads Board</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'consults', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="message-square" class="h-4 w-4 text-slate-400"></i>
                <span>Consultations</span>
              </a>
              <a href="javascript:void(0)" onclick="navigateToDashboardTab('admin_dashboard.php', 'agents', 'switchAdminTab')" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-premium-emerald">
                <i data-lucide="users-2" class="h-4 w-4 text-slate-600"></i>
                <span>Agents & Cities</span>
              </a>
            <?php endif; ?>
          </div>
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
    // Tab routing and hot-switching helper
    function navigateToDashboardTab(page, tabName, switcherFunc) {
      // Clean path check for current page
      const currentPath = window.location.pathname;
      if (currentPath.endsWith(page) || (page === 'index.php' && (currentPath.endsWith('/') || currentPath === ''))) {
        if (typeof window[switcherFunc] === 'function') {
          window[switcherFunc](tabName);
          // Auto close mobile drawer
          const mobileMenu = document.getElementById('mobile-menu');
          if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
            document.body.classList.remove('modal-open');
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            if (mobileToggle) {
              const icon = mobileToggle.querySelector('i');
              if (icon) {
                icon.setAttribute('data-lucide', 'menu');
                if (typeof lucide !== 'undefined') lucide.createIcons();
              }
            }
          }
          return;
        }
      }
      window.location.href = page + '?tab=' + tabName;
    }

    // Toggle Mobile Navigation Drawer with animated icon swap
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileToggle && mobileMenu) {
      mobileToggle.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden');
        if (isOpen) {
          document.body.classList.remove('modal-open');
        } else {
          document.body.classList.add('modal-open');
        }
        // Swap icon
        const icon = mobileToggle.querySelector('i');
        if (icon) {
          icon.setAttribute('data-lucide', isOpen ? 'menu' : 'x');
          if (typeof lucide !== 'undefined') lucide.createIcons();
        }
      });
    }
  </script>
