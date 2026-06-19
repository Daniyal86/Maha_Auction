<?php
// agents.php
require_once 'config/db.php';

// Fetch agents from database
$stmt = $pdo->query("SELECT * FROM agents ORDER BY name ASC");
$agents = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  <!-- Page Header -->
  <div>
    <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
      <i data-lucide="users" class="h-8 w-8 text-premium-emerald"></i>
      <span>Verified Partner Agents</span>
    </h1>
    <p class="text-xs text-slate-500 font-semibold mt-1">Connect with certified specialists assigned to coordinate statutory foreclosure bids and physical site inspections.</p>
  </div>

  <!-- Agents Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($agents as $agent): ?>
      <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 flex flex-col justify-between items-center text-center space-y-6 hover:shadow-xl transition-shadow">
        
        <div class="relative">
          <img src="<?php echo htmlspecialchars($agent['image']); ?>" alt="Agent Portrait" class="h-28 w-28 rounded-full border-4 border-slate-50 object-cover shadow-md">
          <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-amber-500 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full flex items-center space-x-0.5 border border-amber-400">
            <i data-lucide="star" class="h-3 w-3 fill-white"></i>
            <span><?php echo htmlspecialchars($agent['rating']); ?></span>
          </div>
        </div>

        <div class="space-y-1">
          <h3 class="text-lg font-black text-slate-800"><?php echo htmlspecialchars($agent['name']); ?></h3>
          <span class="text-xs font-bold text-premium-emerald bg-emerald-50 px-2.5 py-0.5 rounded-full inline-block">
            <?php echo htmlspecialchars($agent['specialty']); ?>
          </span>
        </div>

        <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2 text-xs font-semibold text-slate-500">
          <div class="flex justify-between">
            <span>Contact Number</span>
            <span class="font-bold text-slate-800"><?php echo htmlspecialchars($agent['phone']); ?></span>
          </div>
          <div class="flex justify-between">
            <span>Email Registry</span>
            <span class="font-bold text-slate-800 truncate max-w-[150px]"><?php echo htmlspecialchars($agent['email']); ?></span>
          </div>
        </div>

        <button onclick="openAgentModal('<?php echo htmlspecialchars($agent['id']); ?>', '<?php echo htmlspecialchars($agent['name']); ?>')" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-all flex items-center justify-center space-x-1.5">
          <i data-lucide="message-square" class="h-4 w-4"></i>
          <span>Connect with Agent</span>
        </button>

      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
