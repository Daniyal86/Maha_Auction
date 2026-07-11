<?php
// advisory.php
require_once 'config/db.php';
require_once 'includes/advocates_data.php';

// Convert associative array to indexed for the loop, maintaining order
$advocates = array_values($advocates_data);

require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
  <!-- Page Header -->
  <div>
    <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center space-x-2">
      <i data-lucide="shield-check" class="h-8 w-8 text-premium-emerald"></i>
      <span>Securitisation Legal Advisory & Draftsman</span>
    </h1>
    <p class="text-xs text-slate-500 font-semibold mt-1">Book direct consultations with foreclosure panel advocates or draft Section 13(2) statutory notices.</p>
  </div>



  <!-- 1. Advocates Section -->
  <div id="section-advocates" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- Advocate Grid -->
    <div class="lg:col-span-2 space-y-6">
      <?php foreach ($advocates as $adv): ?>
        <?php 
          $is_founding = isset($adv['founding']) && $adv['founding'];
          $is_sponsored = isset($adv['sponsored']) && $adv['sponsored'];
          
          $card_class = "bg-white rounded-3xl border border-slate-200 p-6 shadow-md hover:shadow-lg transition-shadow flex flex-col sm:flex-row items-center sm:items-start gap-6 relative overflow-hidden";
          if ($is_founding) {
              $card_class = "bg-gradient-to-br from-white via-white to-amber-50/20 rounded-3xl border-2 border-amber-400 p-6 shadow-lg hover:shadow-xl transition-shadow flex flex-col sm:flex-row items-center sm:items-start gap-6 relative overflow-hidden";
          }
        ?>
        <div onclick="window.location.href='advocate.php?id=<?php echo urlencode($adv['id']); ?>'" class="block group cursor-pointer hover:-translate-y-1 transition-all duration-300 <?php echo $card_class; ?>">
          <?php if ($is_founding): ?>
            <!-- Founding Member gold ribbon -->
            <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-amber-400 text-slate-900 text-[9px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow flex items-center space-x-1">
              <i data-lucide="crown" class="h-3 w-3 fill-slate-900"></i>
              <span>Founding Member</span>
            </div>
          <?php elseif ($is_sponsored): ?>
            <!-- Sponsored rank badge -->
            <div class="absolute top-0 right-0 bg-slate-900 text-white text-[8px] font-extrabold px-3 py-1 rounded-bl-xl uppercase tracking-wider flex items-center space-x-1">
              <i data-lucide="zap" class="h-2.5 w-2.5 text-premium-gold shrink-0"></i>
              <span>Sponsored Rank</span>
            </div>
          <?php endif; ?>

          <img src="<?php echo htmlspecialchars($adv['image']); ?>" alt="Advocate avatar" class="h-24 w-24 rounded-2xl object-cover border <?php echo $is_founding ? 'border-amber-300' : 'border-slate-100'; ?> shrink-0">
          
          <div class="space-y-3 flex-grow text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
              <div>
                <h3 class="text-lg font-black text-slate-800 flex items-center justify-center sm:justify-start space-x-1.5">
                  <span><?php echo htmlspecialchars($adv['name']); ?></span>
                  <?php if ($is_founding): ?>
                    <i data-lucide="shield-check" class="h-4 w-4 text-amber-500 fill-amber-500/20 shrink-0"></i>
                  <?php endif; ?>
                </h3>
                <span class="text-xs font-bold <?php echo $is_founding ? 'text-amber-700 bg-amber-100/80' : 'text-premium-emerald bg-emerald-50'; ?> px-2.5 py-0.5 rounded-full mt-1 inline-block"><?php echo htmlspecialchars($adv['role']); ?></span>
              </div>
              <div class="flex items-center justify-center sm:justify-end space-x-1 text-amber-500 text-xs font-bold bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-lg w-fit self-center">
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-500"></i>
                <span><?php echo htmlspecialchars($adv['rating']); ?></span>
              </div>
            </div>

            <p class="text-xs font-semibold text-slate-500 leading-relaxed"><?php echo htmlspecialchars($adv['specialty']); ?></p>

            <div class="flex flex-wrap gap-4 items-center justify-center sm:justify-start text-xs text-slate-400 font-bold">
              <span class="flex items-center space-x-1">
                <i data-lucide="award" class="h-4 w-4 text-slate-400"></i>
                <span><?php echo htmlspecialchars($adv['experience']); ?></span>
              </span>
              <span class="flex items-center space-x-1">
                <i data-lucide="shield" class="h-4 w-4 text-slate-400"></i>
                <span>Bar Council Verified</span>
              </span>
            </div>
            
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-center sm:justify-start">
              <a href="advocate.php?id=<?php echo urlencode($adv['id']); ?>" class="inline-flex items-center space-x-1.5 text-[11px] font-extrabold uppercase tracking-wider text-premium-emerald group-hover:text-emerald-700 transition-colors touch-target">
                <span>View Full Profile & Case History</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5 transform group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Paid Ranking Informative Box -->
      <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-5 flex items-start space-x-3 text-xs text-slate-500 font-medium leading-relaxed">
        <i data-lucide="info" class="h-5 w-5 text-slate-450 shrink-0 mt-0.5"></i>
        <div>
          <span class="font-extrabold text-slate-800 block mb-0.5">Advocate Directory Ordering Policy</span>
          Adv. Sajid Kureshi is permanently pinned as the #1 Founding Member. Ranks 2 and below are sponsored placements based on paid subscription slots. Legal caucuses interested in rank sponsorships may contact <a href="mailto:legal@maharashtraauctions.com" class="text-premium-emerald font-bold hover:underline">legal@maharashtraauctions.com</a>.
        </div>
      </div>
    </div>

    <!-- Booking Sidebar -->
    <div class="bg-slate-900 text-white rounded-3xl border border-slate-800 p-6 shadow-xl space-y-6 relative overflow-hidden">
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-20"></div>
      
      <div class="relative space-y-4">
        <h3 class="text-lg font-black tracking-tight">Direct Consultation Scheduler</h3>
        <p class="text-xs text-slate-400 font-medium">Book a secure 30-minute regulatory foreclosures hearing directly in advocates schedules.</p>
        
        <div id="booking-error-msg" class="hidden text-xs text-red-400 bg-red-950/20 p-3 rounded-lg font-semibold border border-red-900/20"></div>

        <form id="consultation-booking-form" onsubmit="handleBookingSubmit(event)" class="space-y-3">
          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Select Advocate</label>
            <select id="book-advocate" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold">
              <?php foreach ($advocates as $adv): ?>
                <option value="<?php echo htmlspecialchars($adv['id']); ?>" class="bg-slate-900"><?php echo htmlspecialchars($adv['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Your Name</label>
            <input type="text" id="book-name" required placeholder="Siddharth Rao" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold placeholder-slate-500">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
            <input type="email" id="book-email" required placeholder="sid@example.com" class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold placeholder-slate-500">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Schedule Date</label>
            <input type="date" id="book-date" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold text-slate-400">
          </div>

          <div>
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Consultation Topic</label>
            <select id="book-topic" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-400 font-semibold">
              <option value="SARFAESI Foreclosure Appeal" class="bg-slate-900">SARFAESI Foreclosure Appeal</option>
              <option value="Auction Title Search Clearances" class="bg-slate-900">Auction Title Search Clearances</option>
              <option value="Heavy Deposit Lease Drafting" class="bg-slate-900">Heavy Deposit Lease Drafting</option>
            </select>
          </div>

          <button type="submit" class="w-full bg-premium-emerald hover:bg-premium-emeraldHover text-white py-3 rounded-xl text-sm font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2 touch-target">
            <span>Schedule Appointment</span>
            <i data-lucide="calendar" class="h-4 w-4"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Consultation booking AJAX handler
  function handleBookingSubmit(e) {
    e.preventDefault();
    const advocate = document.getElementById('book-advocate').value;
    const name = document.getElementById('book-name').value;
    const email = document.getElementById('book-email').value;
    const date = document.getElementById('book-date').value;
    const topic = document.getElementById('book-topic').value;
    const errorEl = document.getElementById('booking-error-msg');

    errorEl.classList.add('hidden');

    const formData = new FormData();
    formData.append('advocate_id', advocate);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('booking_date', date);
    formData.append('topic', topic);

    const form = document.getElementById('consultation-booking-form');
    form.innerHTML = `
      <div class="flex flex-col items-center justify-center py-12 space-y-4">
        <div class="h-10 w-10 border-4 border-slate-600 border-t-emerald-400 rounded-full animate-spin"></div>
        <p class="text-xs text-slate-400 font-semibold">Booking panel schedule slot...</p>
      </div>
    `;

    fetch('api/book_consultation.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        form.innerHTML = `
          <div class="text-center py-8 space-y-4 text-white">
            <div class="mx-auto h-12 w-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 border border-emerald-500/25">
              <i data-lucide="check" class="h-6 w-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-black">Consultation Confirmed!</h3>
              <p class="text-xs text-slate-400 font-semibold mt-1">Calendar invites and link sent to ${email}.</p>
            </div>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      } else {
        errorEl.textContent = data.message;
        errorEl.classList.remove('hidden');
        window.location.reload(); // reset state on error
      }
    })
    .catch(() => {
      errorEl.textContent = 'Server response error.';
      errorEl.classList.remove('hidden');
    });
  }
</script>



<?php
require_once 'includes/auth_modal.php';
require_once 'includes/modals.php';
require_once 'includes/footer.php';
?>
