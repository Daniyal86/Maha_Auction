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

$rank_counter = 0;
$my_rank = 0;
foreach ($advocates_data as $key => $val) {
    if (!isset($val['founding']) || !$val['founding']) {
        $rank_counter++;
        if ($key === $advocate_id) {
            $my_rank = $rank_counter;
            break;
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Custom smooth scroll & styling tweaks -->
<style>
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  html { scroll-behavior: smooth; }
</style>

<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-3 pb-24 sm:py-10 space-y-3.5 sm:space-y-8">

  <!-- Navigation Breadcrumb -->
  <a href="advisory.php" class="inline-flex items-center space-x-2 text-xs sm:text-sm font-bold text-slate-500 hover:text-premium-emerald transition-colors px-1">
    <i data-lucide="arrow-left" class="h-4 w-4"></i>
    <span>Back to Legal Panel</span>
  </a>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-8 items-start">
    
    <!-- LEFT: Main Profile Body -->
    <div class="lg:col-span-2 space-y-5 sm:space-y-8">
      
      <!-- 1. Compact Hero Profile Card (Tighter Spacing, Subtle Pattern) -->
      <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 rounded-3xl p-4 sm:p-7 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <!-- Subtle 4% Opacity Legal Grid Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:1.5rem_1.5rem] opacity-[0.04]"></div>
        
        <?php if ($is_founding): ?>
          <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-400 to-amber-500 text-slate-950 text-[10px] sm:text-[11px] font-black px-4 py-1.5 rounded-bl-2xl rounded-tr-3xl uppercase tracking-wider shadow-lg flex items-center space-x-1.5 z-10">
            <i data-lucide="crown" class="h-3.5 w-3.5 fill-slate-950 shrink-0"></i>
            <span>Founding Member</span>
          </div>
        <?php elseif ($is_sponsored): ?>
          <?php if ($my_rank >= 1 && $my_rank <= 5): ?>
            <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 via-yellow-400 to-amber-600 text-slate-950 text-[10px] sm:text-[11px] font-black px-4 py-1.5 rounded-bl-2xl rounded-tr-3xl uppercase tracking-wider shadow-lg flex items-center space-x-1.5 z-10">
              <i data-lucide="crown" class="h-3.5 w-3.5 fill-slate-950 shrink-0"></i>
              <span>Rank #<?php echo $my_rank; ?> Gold Panelist</span>
            </div>
          <?php elseif ($my_rank >= 6 && $my_rank <= 10): ?>
            <div class="absolute top-0 right-0 bg-gradient-to-l from-slate-400 via-slate-350 to-slate-500 text-slate-800 text-[10px] sm:text-[11px] font-black px-4 py-1.5 rounded-bl-2xl rounded-tr-3xl uppercase tracking-wider shadow-md flex items-center space-x-1.5 z-10">
              <i data-lucide="award" class="h-3.5 w-3.5 fill-slate-700 shrink-0"></i>
              <span>Rank #<?php echo $my_rank; ?> Silver Panelist</span>
            </div>
          <?php elseif ($my_rank >= 11 && $my_rank <= 15): ?>
            <div class="absolute top-0 right-0 bg-gradient-to-l from-orange-400 via-orange-300 to-orange-500 text-white text-[10px] sm:text-[11px] font-black px-4 py-1.5 rounded-bl-2xl rounded-tr-3xl uppercase tracking-wider shadow-md flex items-center space-x-1.5 z-10">
              <i data-lucide="award" class="h-3.5 w-3.5 fill-orange-200 shrink-0"></i>
              <span>Rank #<?php echo $my_rank; ?> Bronze Panelist</span>
            </div>
          <?php else: ?>
            <div class="absolute top-0 right-0 bg-slate-900/90 border-b border-l border-emerald-500/40 text-emerald-400 text-[10px] sm:text-[11px] font-extrabold px-4 py-1.5 rounded-bl-2xl rounded-tr-3xl uppercase tracking-wider shadow-md flex items-center space-x-1.5 z-10">
              <i data-lucide="zap" class="h-3.5 w-3.5 text-emerald-400 shrink-0"></i>
              <span>Verified Panelist</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-3.5 sm:gap-6 text-center sm:text-left mt-1 sm:mt-0">
          <!-- 100px Profile Image with 2px Clean White Border & Soft Shadow -->
          <div class="relative shrink-0">
            <img src="<?php echo htmlspecialchars($adv['image']); ?>" alt="Advocate avatar" loading="lazy" class="h-[100px] w-[100px] sm:h-32 sm:w-32 rounded-2xl object-cover border-2 border-white shadow-lg">
          </div>
          
          <div class="space-y-2 flex-grow">
            <!-- Focal Point Advocate Name & Verification Shield -->
            <div>
              <h1 class="text-[28px] sm:text-3xl font-black tracking-tight text-white leading-tight flex items-center justify-center sm:justify-start space-x-2">
                <span><?php echo htmlspecialchars($adv['name']); ?></span>
                <i data-lucide="shield-check" class="h-6 w-6 sm:h-7 sm:w-7 text-emerald-400 fill-emerald-400/20 shrink-0"></i>
              </h1>
              
              <!-- Designation & Availability Status Badge -->
              <div class="mt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <span class="text-[11px] sm:text-xs font-extrabold px-3 py-0.5 rounded-full bg-white/10 text-emerald-300 border border-white/10 inline-block">
                  <?php echo htmlspecialchars($adv['role']); ?>
                </span>
                <?php if ($my_rank > 0): ?>
                  <?php 
                    $label_color = "text-emerald-300 bg-white/10 border-white/10";
                    $label_text = "Rank #$my_rank";
                    if ($my_rank >= 1 && $my_rank <= 5) {
                        $label_color = "text-yellow-400 bg-yellow-500/10 border-yellow-500/30";
                        $label_text = "Gold Rank #$my_rank";
                    } elseif ($my_rank >= 6 && $my_rank <= 10) {
                        $label_color = "text-slate-350 bg-slate-500/10 border-slate-500/30";
                        $label_text = "Silver Rank #$my_rank";
                    } elseif ($my_rank >= 11 && $my_rank <= 15) {
                        $label_color = "text-orange-400 bg-orange-500/10 border-orange-500/30";
                        $label_text = "Bronze Rank #$my_rank";
                    }
                  ?>
                  <span class="text-[11px] sm:text-xs font-extrabold px-3 py-0.5 rounded-full <?php echo $label_color; ?> border inline-block">
                    <?php echo $label_text; ?>
                  </span>
                <?php endif; ?>
                <span class="inline-flex items-center space-x-1 text-[10px] font-extrabold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2.5 py-0.5 rounded-full">
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span>Available Today for Consultation</span>
                </span>
              </div>
            </div>

            <!-- Enhanced Rating Chips with Increased Horizontal Padding -->
            <div class="flex flex-wrap justify-center sm:justify-start gap-2 items-center text-xs font-bold pt-0.5">
              <div class="flex items-center space-x-1 text-amber-300 bg-amber-400/10 border border-amber-400/20 px-3.5 py-1 rounded-xl">
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-300"></i>
                <span><?php echo htmlspecialchars($adv['rating']); ?> Rating</span>
              </div>
              <div class="flex items-center space-x-1.5 text-slate-300 bg-white/5 border border-white/10 px-3.5 py-1 rounded-xl">
                <i data-lucide="award" class="h-3.5 w-3.5 text-emerald-400"></i>
                <span><?php echo htmlspecialchars($adv['experience']); ?></span>
              </div>
              <div class="flex items-center space-x-1.5 text-slate-300 bg-white/5 border border-white/10 px-3.5 py-1 rounded-xl">
                <i data-lucide="check-circle" class="h-3.5 w-3.5 text-emerald-400"></i>
                <span>Bar Council Verified</span>
              </div>
            </div>
            
            <!-- Easily Scannable Practice Chips in Hero -->
            <div class="flex flex-wrap justify-center sm:justify-start gap-1.5 pt-1">
              <span class="text-[10px] sm:text-xs font-extrabold px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 border border-white/10">SARFAESI</span>
              <span class="text-[10px] sm:text-xs font-extrabold px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 border border-white/10">DRT Counsel</span>
              <span class="text-[10px] sm:text-xs font-extrabold px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 border border-white/10">Land Title</span>
              <span class="text-[10px] sm:text-xs font-extrabold px-2.5 py-0.5 rounded-md bg-white/10 text-slate-200 border border-white/10">Property Law</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Quick Action Buttons (50px height, 18px icons, equal spacing) -->
      <div class="grid grid-cols-4 gap-2 text-center">
        <a href="tel:+919820012345" class="h-[50px] bg-white hover:bg-slate-50 text-slate-800 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-center space-y-0.5 sm:space-y-0 sm:space-x-1.5 transition-all active:scale-95 touch-target">
          <i data-lucide="phone" class="h-[18px] w-[18px] text-emerald-600"></i>
          <span class="text-[10px] sm:text-xs font-extrabold">Call</span>
        </a>
        <a href="https://wa.me/919820012345?text=Hello%20Advocate,%20I%20need%20legal%20guidance%20for%20an%20auction%20property." target="_blank" class="h-[50px] bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-2xl border border-emerald-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-center space-y-0.5 sm:space-y-0 sm:space-x-1.5 transition-all active:scale-95 touch-target">
          <i data-lucide="message-circle" class="h-[18px] w-[18px] text-emerald-600"></i>
          <span class="text-[10px] sm:text-xs font-extrabold">WhatsApp</span>
        </a>
        <a href="#section-book" class="h-[50px] bg-slate-900 hover:bg-slate-800 text-white rounded-2xl shadow-xs flex flex-col sm:flex-row items-center justify-center space-y-0.5 sm:space-y-0 sm:space-x-1.5 transition-all active:scale-95 touch-target">
          <i data-lucide="calendar" class="h-[18px] w-[18px] text-emerald-400"></i>
          <span class="text-[10px] sm:text-xs font-extrabold">Book</span>
        </a>
        <a href="#section-office" class="h-[50px] bg-white hover:bg-slate-50 text-slate-800 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-center space-y-0.5 sm:space-y-0 sm:space-x-1.5 transition-all active:scale-95 touch-target">
          <i data-lucide="map-pin" class="h-[18px] w-[18px] text-emerald-600"></i>
          <span class="text-[10px] sm:text-xs font-extrabold">Office</span>
        </a>
      </div>

      <!-- 3. Interactive Statistics Cards (Larger Numbers, Active Scale Feedback) -->
      <div class="grid grid-cols-4 gap-2 sm:gap-4">
        <div class="bg-white rounded-2xl p-2.5 sm:p-4 border border-slate-200/80 shadow-xs text-center active:scale-95 transition-transform cursor-pointer">
          <span class="block text-lg sm:text-2xl font-black text-slate-900 leading-tight">20+</span>
          <span class="text-[9px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Years Exp</span>
        </div>
        <div class="bg-white rounded-2xl p-2.5 sm:p-4 border border-slate-200/80 shadow-xs text-center active:scale-95 transition-transform cursor-pointer">
          <span class="block text-lg sm:text-2xl font-black text-slate-900 leading-tight">500+</span>
          <span class="text-[9px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Cases Won</span>
        </div>
        <div class="bg-white rounded-2xl p-2.5 sm:p-4 border border-slate-200/80 shadow-xs text-center active:scale-95 transition-transform cursor-pointer">
          <span class="block text-lg sm:text-2xl font-black text-emerald-600 leading-tight">98%</span>
          <span class="text-[9px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Success</span>
        </div>
        <div class="bg-white rounded-2xl p-2.5 sm:p-4 border border-slate-200/80 shadow-xs text-center active:scale-95 transition-transform cursor-pointer">
          <span class="block text-lg sm:text-2xl font-black text-amber-500 leading-tight">4.9★</span>
          <span class="text-[9px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Rating</span>
        </div>
      </div>

      <!-- 4. Horizontally Scrollable Section Navigation Chips -->
      <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar py-1 text-xs font-bold -mx-1 px-1 sticky top-16 z-30 bg-slate-50/95 backdrop-blur-md rounded-2xl">
        <a href="#section-about" class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-full border border-slate-200 shrink-0 transition-colors shadow-2xs">About</a>
        <a href="#section-practice" class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-full border border-slate-200 shrink-0 transition-colors shadow-2xs">Practice Areas</a>
        <a href="#section-cases" class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-full border border-slate-200 shrink-0 transition-colors shadow-2xs">Case History</a>
        <a href="#section-reviews" class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-full border border-slate-200 shrink-0 transition-colors shadow-2xs">Client Reviews</a>
        <a href="#section-office" class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-full border border-slate-200 shrink-0 transition-colors shadow-2xs">Office Info</a>
        <a href="#section-book" class="px-3.5 py-2 bg-slate-900 text-white rounded-full shrink-0 shadow-2xs">Book Appointment</a>
      </div>

      <!-- 5. About Section with Expander -->
      <div id="section-about" class="bg-white rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs space-y-3 scroll-mt-24">
        <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="user-check" class="h-5 w-5 text-premium-emerald"></i>
          <span>About Advocate</span>
        </h2>
        <div class="relative">
          <p id="adv-bio-text" class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium line-clamp-4 sm:line-clamp-none transition-all">
            <?php echo htmlspecialchars($adv['bio']); ?>
          </p>
          <button type="button" id="bio-toggle-btn" onclick="toggleBio()" class="sm:hidden text-xs font-black text-premium-emerald mt-2 flex items-center space-x-1 focus:outline-none">
            <span id="bio-btn-text">Read More</span>
            <i data-lucide="chevron-down" id="bio-chevron" class="h-3.5 w-3.5 transition-transform"></i>
          </button>
        </div>
      </div>

      <!-- 6. Detailed Practice Areas Chips -->
      <div id="section-practice" class="bg-white rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
        <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="briefcase" class="h-5 w-5 text-premium-emerald"></i>
          <span>Practice Areas & Core Legal Specialties</span>
        </h2>
        <div class="flex flex-wrap gap-2 text-xs font-bold">
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="scale" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>SARFAESI Litigations</span>
          </span>
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="file-text" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>DRT & DRAT Appeals</span>
          </span>
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="search" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>Land Title Audits</span>
          </span>
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="building-2" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>Banking & Security Law</span>
          </span>
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="gavel" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>Arbitration & Dispute Resolution</span>
          </span>
          <span class="bg-slate-100 text-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/60 flex items-center space-x-1.5">
            <i data-lucide="shield" class="h-3.5 w-3.5 text-emerald-600"></i>
            <span>High Court Writ Petitions</span>
          </span>
        </div>
      </div>

      <!-- 7. Card-Style Case History Timeline -->
      <div id="section-cases" class="bg-white rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs space-y-5 scroll-mt-24">
        <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="trophy" class="h-5 w-5 text-premium-emerald"></i>
          <span>Landmark Case Victories & Track Record</span>
        </h2>
        
        <div class="space-y-4">
          <?php foreach ($adv['history'] as $record): ?>
          <div class="bg-slate-50 hover:bg-emerald-50/40 rounded-2xl p-4 border border-slate-200/80 transition-all flex items-start space-x-3.5">
            <div class="bg-slate-900 text-emerald-400 px-2.5 py-1 rounded-xl text-xs font-black shrink-0 mt-0.5">
              <?php echo htmlspecialchars($record['year']); ?>
            </div>
            <div class="space-y-1 flex-grow">
              <h4 class="text-xs sm:text-base font-black text-slate-900 flex items-center justify-between">
                <span><?php echo htmlspecialchars($record['title']); ?></span>
                <i data-lucide="arrow-up-right" class="h-4 w-4 text-slate-400"></i>
              </h4>
              <p class="text-xs text-slate-500 font-semibold leading-relaxed"><?php echo htmlspecialchars($record['desc']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 8. Client Reviews Section -->
      <div id="section-reviews" class="bg-white rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
        <div class="flex justify-between items-center">
          <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
            <i data-lucide="message-square" class="h-5 w-5 text-premium-emerald"></i>
            <span>Verified Client Reviews</span>
          </h2>
          <div class="flex items-center space-x-1 text-amber-500 text-xs font-extrabold bg-amber-50 px-2.5 py-1 rounded-full border border-amber-100">
            <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-500"></i>
            <span>4.9 / 5.0</span>
          </div>
        </div>

        <div class="space-y-3">
          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
            <div class="flex justify-between items-center">
              <div class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center">RK</div>
                <div>
                  <h4 class="text-xs font-bold text-slate-900">Rajesh Kulkarni</h4>
                  <span class="text-[10px] text-slate-400 font-semibold">Auction Investor, Mumbai</span>
                </div>
              </div>
              <div class="flex text-amber-400">
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
              </div>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">"Advocate provided flawless title verification before my bank auction bidding. Stopped me from bidding on an encumbered property!"</p>
          </div>

          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
            <div class="flex justify-between items-center">
              <div class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-800 font-black text-xs flex items-center justify-center">SM</div>
                <div>
                  <h4 class="text-xs font-bold text-slate-900">Sunil Mehta</h4>
                  <span class="text-[10px] text-slate-400 font-semibold">Commercial Buyer, Pune</span>
                </div>
              </div>
              <div class="flex text-amber-400">
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
                <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i>
              </div>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">"Extremely quick response and highly strategic advice during our DRT hearing. Worth every single rupee."</p>
          </div>
        </div>
      </div>

      <!-- 9. Professional Office Card -->
      <div id="section-office" class="bg-white rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs space-y-4 scroll-mt-24">
        <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-800 flex items-center space-x-2">
          <i data-lucide="map-pin" class="h-5 w-5 text-premium-emerald"></i>
          <span>Chambers & Office Information</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-medium">
          <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Chambers Address</span>
            <p class="font-bold text-slate-800">Suite 402, High Court Legal Chambers, Fort, Mumbai - 400001</p>
          </div>
          <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Consultation Hours</span>
            <p class="font-bold text-slate-800">Monday - Saturday: 10:00 AM - 7:00 PM</p>
          </div>
          <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Parking & Accessibility</span>
            <p class="font-bold text-slate-800">Reserved Client Parking Available</p>
          </div>
          <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Direct Contact</span>
            <p class="font-bold text-slate-800">+91 98200 12345 / advisory@mahaauctions.com</p>
          </div>
        </div>

        <a href="https://maps.google.com/?q=High+Court+Fort+Mumbai" target="_blank" class="w-full h-11 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl flex items-center justify-center space-x-2 transition-colors text-xs touch-target">
          <i data-lucide="navigation" class="h-4 w-4 text-emerald-600"></i>
          <span>Get Directions to Chambers</span>
        </a>
      </div>

    </div>

    <!-- RIGHT: Booking Widget (Sticky on Desktop) -->
    <div id="section-book" class="lg:col-span-1 space-y-6 lg:sticky lg:top-24 scroll-mt-24">
      
      <!-- Premium Consultation Fee Box -->
      <div id="section-fees" class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 shadow-xl text-white relative overflow-hidden border border-slate-800">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:2rem_2rem] opacity-20"></div>
        <div class="relative space-y-3 text-center">
          <div class="inline-flex items-center justify-center p-3 bg-white/10 rounded-2xl mb-1 backdrop-blur-sm">
            <i data-lucide="wallet" class="h-6 w-6 text-emerald-400"></i>
          </div>
          <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Initial Consultation Fee</h3>
          <p class="text-3xl sm:text-4xl font-black tracking-tight text-emerald-400"><?php echo htmlspecialchars($adv['fee_structure']); ?></p>
          <div class="inline-flex items-center space-x-1.5 text-xs text-slate-300 font-semibold bg-white/5 px-3 py-1 rounded-full border border-white/10">
            <i data-lucide="clock" class="h-3.5 w-3.5 text-emerald-400"></i>
            <span>60 Minutes Session</span>
          </div>
          <p class="text-[11px] text-slate-400 font-medium pt-1">Includes title verification review & DRT defense strategy.</p>
        </div>
      </div>

      <!-- High-Usability Booking Form (48px inputs) -->
      <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-md space-y-4">
        <div class="space-y-1">
          <h3 class="text-base sm:text-lg font-black tracking-tight text-slate-800">Schedule Consultation</h3>
          <p class="text-xs text-slate-500 font-medium">Book a direct sitting with <?php echo htmlspecialchars(explode(' ', $adv['name'])[1] ?? 'the advocate'); ?>.</p>
        </div>

        <div id="booking-error-msg" class="hidden text-xs text-red-600 bg-red-50 p-3 rounded-xl font-semibold border border-red-100"></div>

        <form id="consultation-booking-form" onsubmit="handleBookingSubmit(event)" class="space-y-3.5">
          <input type="hidden" id="book-advocate" value="<?php echo htmlspecialchars($adv['id']); ?>">
          
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Your Full Name</label>
            <div class="relative">
              <i data-lucide="user" class="absolute left-3.5 top-3.5 h-4 w-4 text-slate-400"></i>
              <input type="text" id="book-name" required placeholder="John Doe" class="w-full h-12 pl-10 pr-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800 placeholder-slate-400">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
            <div class="relative">
              <i data-lucide="mail" class="absolute left-3.5 top-3.5 h-4 w-4 text-slate-400"></i>
              <input type="email" id="book-email" required placeholder="john@example.com" class="w-full h-12 pl-10 pr-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800 placeholder-slate-400">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Preferred Date</label>
            <div class="relative">
              <input type="date" id="book-date" required class="w-full h-12 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-premium-emerald transition-colors font-medium text-slate-800">
            </div>
          </div>

          <button type="submit" id="book-btn" class="w-full h-12 bg-gradient-to-r from-premium-emerald to-teal-600 hover:from-premium-emeraldHover hover:to-teal-700 text-white rounded-xl text-sm font-extrabold shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2 touch-target">
            <i data-lucide="calendar-check" class="h-4 w-4"></i>
            <span>Confirm Booking Now</span>
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- 8. Sticky Mobile Bottom CTA Bar (70/30 Width Split, 52px Height, iOS Safe Area Support) -->
<div class="fixed bottom-0 inset-x-0 z-[49] bg-white/95 backdrop-blur-md border-t border-slate-200/90 p-2.5 px-3 flex gap-2.5 sm:hidden shadow-2xl" style="padding-bottom: max(10px, env(safe-area-inset-bottom, 10px));">
  <a href="https://wa.me/919820012345" target="_blank" class="w-[30%] h-[52px] bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold rounded-xl border border-emerald-200/90 flex items-center justify-center space-x-1 text-xs active:scale-95 transition-transform touch-target shrink-0">
    <i data-lucide="message-circle" class="h-5 w-5 text-emerald-600"></i>
    <span>WhatsApp</span>
  </a>
  <a href="#section-book" class="w-[70%] h-[52px] bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl flex items-center justify-center space-x-2 text-xs shadow-lg active:scale-95 transition-transform touch-target">
    <i data-lucide="calendar" class="h-5 w-5 text-emerald-400"></i>
    <span>Book Appointment</span>
  </a>
</div>

<script>
function toggleBio() {
  const bioText = document.getElementById('adv-bio-text');
  const btnText = document.getElementById('bio-btn-text');
  const chevron = document.getElementById('bio-chevron');

  if (bioText.classList.contains('line-clamp-4')) {
    bioText.classList.remove('line-clamp-4');
    btnText.innerText = 'Show Less';
    if(chevron) chevron.classList.add('rotate-180');
  } else {
    bioText.classList.add('line-clamp-4');
    btnText.innerText = 'Read More';
    if(chevron) chevron.classList.remove('rotate-180');
  }
}

function handleBookingSubmit(e) {
  e.preventDefault();
  
  const btn = document.getElementById('book-btn');
  btn.innerHTML = `<div class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div><span>Processing...</span>`;
  btn.disabled = true;

  setTimeout(() => {
    btn.innerHTML = `<i data-lucide="check-circle" class="h-4 w-4"></i><span>Booking Confirmed!</span>`;
    btn.classList.remove('from-premium-emerald', 'to-teal-600');
    btn.classList.add('bg-slate-800');
    document.getElementById('booking-error-msg').classList.add('hidden');
    
    setTimeout(() => {
      btn.innerHTML = `<i data-lucide="calendar-check" class="h-4 w-4"></i><span>Confirm Booking Now</span>`;
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
