<?php
// city.php
require_once 'config/db.php';

$city_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($city_id)) {
    header('Location: index.php');
    exit;
}

// Fetch city details
$city_stmt = $pdo->prepare("SELECT * FROM cities WHERE id = ?");
$city_stmt->execute([$city_id]);
$city = $city_stmt->fetch();

if (!$city) {
    header('Location: index.php');
    exit;
}

// Fetch properties in this city
$prop_stmt = $pdo->prepare("SELECT p.*, c.name as city_name FROM properties p JOIN cities c ON p.city_id = c.id WHERE p.city_id = ? ORDER BY p.created_at DESC");
$prop_stmt->execute([$city_id]);
$properties = $prop_stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
  <!-- Breadcrumb -->
  <div class="flex items-center space-x-2 text-xs font-bold text-slate-400">
    <a href="index.php" class="hover:text-premium-emerald transition-colors">Home</a>
    <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
    <span>Cities</span>
    <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
    <span class="text-slate-600"><?php echo htmlspecialchars($city['name']); ?></span>
  </div>

  <!-- City Header Banner -->
  <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-xl relative overflow-hidden border border-slate-800">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-25"></div>
    <div class="relative space-y-3">
      <div class="inline-flex items-center space-x-1.5 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full text-emerald-400 text-xs font-bold uppercase tracking-wider">
        <i data-lucide="map" class="h-3 w-3"></i>
        <span>City Foreclosures</span>
      </div>
      <h1 class="text-3xl font-black tracking-tight"><?php echo htmlspecialchars($city['name']); ?> Foreclosure Listings</h1>
      <p class="text-xs text-slate-400 font-medium max-w-xl">Browse foreclosures, rentals, and heavy deposits synced directly from banking portals and private owners in <?php echo htmlspecialchars($city['name']); ?>.</p>
    </div>
  </div>

  <!-- Results Count -->
  <div class="flex justify-between items-center text-xs font-extrabold text-slate-500 uppercase tracking-wider">
    <span><?php echo count($properties); ?> PROPERTIES IN <?php echo htmlspecialchars($city['name']); ?></span>
  </div>

  <!-- Properties Grid -->
  <?php if (count($properties) === 0): ?>
    <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4">
      <div class="mx-auto h-16 w-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400">
        <i data-lucide="info" class="h-8 w-8"></i>
      </div>
      <div>
        <h3 class="text-lg font-black text-slate-800">No properties available in <?php echo htmlspecialchars($city['name']); ?> yet</h3>
        <p class="text-xs text-slate-500 font-semibold mt-1">Check back later as banking institutions update their foreclosure catalogs weekly.</p>
      </div>
    </div>
  <?php else: ?>
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
  <?php endif; ?>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>