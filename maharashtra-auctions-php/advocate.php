<?php
// advocate.php
require_once 'config/db.php';
require_once 'includes/advocates_data.php';

$advocate_id = isset($_GET['id']) ? $_GET['id'] : '';

if (!isset($advocates_data[$advocate_id])) {
    header('Location: advisory.php');
    exit;
}

$adv = $advocates_data[$advocate_id];
$is_founding = isset($adv['founding']) && $adv['founding'];
$is_sponsored = isset($adv['sponsored']) && $adv['sponsored'];

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

  <!-- Back Link -->
  <a href="advisory.php" class="inline-flex items-center space-x-2 text-sm font-bold text-slate-500 hover:text-premium-emerald transition-colors">
    <i data-lucide="arrow-left" class="h-4 w-4"></i>
    <span>Back to Directory</span>
  </a>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- LEFT: Main Profile -->
    <div class="lg:col-span-2 space-y-8">
      
      <!-- Profile Header Card -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm relative overflow-hidden">
        
        <?php if ($is_founding): ?>
          <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-amber-400 text-slate-900 text-[10px] font-black px-4 py-2 rounded-bl-3xl uppercase tracking-wider shadow-md flex items-center space-x-1.5">
            <i data-lucide="crown" class="h-4 w-4 fill-slate-900"></i>
            <span>Founding Member</span>
          </div>
        <?php elseif ($is_sponsored): ?>
          <div class="absolute top-0 right-0 bg-slate-900 text-white text-[10px] font-extrabold px-4 py-2 rounded-bl-3xl uppercase tracking-wider shadow-md flex items-center space-x-1.5">
            <i data-lucide="zap" class="h-3.5 w-3.5 text-premium-gold shrink-0"></i>
            <span>Sponsored Rank</span>
          </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
          <img src="<?php echo htmlspecialchars($adv['image']); ?>" alt="Advocate avatar" class="h-32 w-32 rounded-3xl object-cover border-4 <?php echo $is_founding ? 'border-amber-100' : 'border-slate-50'; ?> shadow-lg shrink-0">
          
          <div class="space-y-4 flex-grow text-center sm:text-left">
            <div>
              <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center justify-center sm:justify-start space-x-2">
                <span><?php echo htmlspecialchars($adv['name']); ?></span>
                <?php if ($is_founding): ?>
                  <i data-lucide="shield-check" class="h-6 w-6 text-amber-500 fill-amber-500/20 shrink-0"></i>
                <?php endif; ?>
              </h1>
              <span class="text-sm font-bold <?php echo $is_founding ? 'text-amber-700 bg-amber-100/80' : 'text-premium-emerald bg-emerald-50'; ?> px-3 py-1 rounded-full mt-2 inline-block">
                <?php echo htmlspecialchars($adv['role']); ?>
              </span>
            </div>

            <div class="flex flex-wrap justify-center sm:justify-start gap-3 items-center">
              <div class="flex items-center space-x-1 text-amber-500 text-sm font-bold bg-amber-50 border border-amber-100 px-3 py-1 rounded-xl">
                <i data-lucide="star" class="h-4 w-4 fill-amber-500"></i>
                <span><?php echo htmlspecialchars($adv['rating']); ?> Rating</span>
              </div>
              <div class="flex items-center space-x-1.5 text-slate-600 text-sm font-bold bg-slate-50 border border-slate-100 px-3 py-1 rounded-xl">
                <i data-lucide="award" class="h-4 w-4 text-slate-400"></i>
                <span><?php echo htmlspecialchars($adv['experience']); ?></span>
              </div>
              <div class="flex items-center space-x-1.5 text-slate-600 text-sm font-bold bg-slate-50 border border-slate-100 px-3 py-1 rounded-xl">
                <i data-lucide="shield" class="h-4 w-4 text-slate-400"></i>
                <span>Bar Council Verified</span>
              </div>
            </div>
            
            <p class="text-sm font-bold text-slate-500 max-w-lg"><?php echo htmlspecialchars($adv['specialty']); ?></p>
          </div>
        </div>
      </div>

      <!-- Biography -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm space-y-4">
        <h2 class="text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="user" class="h-5 w-5 text-premium-emerald"></i>
          <span>About Advocate</span>
        </h2>
        <p class="text-slate-600 leading-relaxed font-medium">
          <?php echo htmlspecialchars($adv['bio']); ?>
        </p>
      </div>

      <!-- Case History Timeline -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/60 shadow-sm space-y-6">
        <h2 class="text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="scale" class="h-5 w-5 text-premium-emerald"></i>
          <span>Notable Case History & Track Record</span>
        </h2>
        
        <div class="space-y-6 pl-4 border-l-2 border-emerald-100">
          <?php foreach ($adv['history'] as $record): ?>
          <div class="relative">
            <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full bg-premium-emerald border-4 border-white shadow-sm"></div>
            <div class="space-y-1">
              <span class="text-xs font-black text-premium-emerald uppercase tracking-wider"><?php echo htmlspecialchars($record['year']); ?></span>
              <h4 class="font-bold text-slate-800"><?php echo htmlspecialchars($record['title']); ?></h4>
              <p class="text-sm text-slate-500 font-medium"><?php echo htmlspecialchars($record['desc']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- RIGHT: Booking Widget -->
    <div class="lg:col-span-1 space-y-6 sticky top-24">
      
      <!-- Fee Box -->
      <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-xl text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:2rem_2rem] opacity-20"></div>
        <div class="relative space-y-4 text-center">
          <div class="inline-flex items-center justify-center p-3 bg-white/10 rounded-2xl mb-2 backdrop-blur-sm">
            <i data-lucide="wallet" class="h-6 w-6 text-emerald-400"></i>
          </div>
          <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Initial Consultation Fee</h3>
          <p class="text-3xl font-black tracking-tight text-emerald-400"><?php echo htmlspecialchars($adv['fee_structure']); ?></p>
          <p class="text-xs text-slate-400 font-medium">Includes deep document review and pre-hearing strategy session.</p>
        </div>
      </div>

      <!-- Booking Form -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-sm space-y-5">
        <div class="space-y-1">
          <h3 class="text-lg font-black tracking-tight text-slate-800">Book an Appointment</h3>
          <p class="text-xs text-slate-500 font-medium">Schedule a direct sitting with <?php echo htmlspecialchars(explode(' ', $adv['name'])[1] ?? 'the advocate'); ?>.</p>
        </div>

        <div id="booking-error-msg" class="hidden text-xs text-red-600 bg-red-50 p-3 rounded-xl font-semibold border border-red-100"></div>

        <form id="consultation-booking-form" onsubmit="handleBookingSubmit(event)" class="space-y-4">
          <input type="hidden" id="book-advocate" value="<?php echo htmlspecialchars($adv['id']); ?>">
          
          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Your Full Name</label>
            <input type="text" id="book-name" required placeholder="John Doe" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800 placeholder-slate-400">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Address</label>
            <input type="email" id="book-email" required placeholder="john@example.com" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800 placeholder-slate-400">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Select Date</label>
            <input type="date" id="book-date" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800">
          </div>

          <button type="submit" id="book-btn" class="w-full bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white py-3 rounded-xl text-sm font-extrabold shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
            <i data-lucide="calendar-check" class="h-4 w-4"></i>
            <span>Confirm Booking</span>
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
function handleBookingSubmit(e) {
  e.preventDefault();
  
  // Need to be logged in logic... check session or just mock success
  const btn = document.getElementById('book-btn');
  btn.innerHTML = `<div class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div><span>Processing...</span>`;
  btn.disabled = true;

  // Mock API call
  setTimeout(() => {
    btn.innerHTML = `<i data-lucide="check-circle" class="h-4 w-4"></i><span>Booking Confirmed!</span>`;
    btn.classList.remove('from-premium-emerald', 'to-teal-600');
    btn.classList.add('bg-slate-800');
    document.getElementById('booking-error-msg').classList.add('hidden');
    
    // Reset after 3 secs
    setTimeout(() => {
      btn.innerHTML = `<i data-lucide="calendar-check" class="h-4 w-4"></i><span>Confirm Booking</span>`;
      btn.disabled = false;
      btn.classList.add('from-premium-emerald', 'to-teal-600');
      btn.classList.remove('bg-slate-800');
      e.target.reset();
      if(typeof lucide !== 'undefined') lucide.createIcons();
    }, 3000);
    
    if(typeof lucide !== 'undefined') lucide.createIcons();
  }, 1000);
}
</script>

<?php require_once 'includes/footer.php'; ?>
