<?php
// index.php
require_once __DIR__ . '../functions/referral_functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Referral Leaderboard</title>
  <!-- Tailwind CSS official CDN via script -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- Hero Header with Gradient -->
  <header class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white px-8 py-16 relative overflow-hidden">
    <div class="max-w-6xl mx-auto relative z-10">
      <h1 class="text-4xl font-bold mb-3">Referral Leaderboard</h1>
      <p class="text-lg font-medium">Explore your weekly and monthly referral stats</p>
    </div>
    <!-- Decorative White Arc at Bottom -->
    <div class="absolute bottom-0 left-0 right-0 h-12 bg-white rounded-t-3xl"></div>
  </header>

  <!-- Main Container -->
  <main class="max-w-6xl mx-auto -mt-10 px-4 relative z-20 mb-10">
    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
        <h2 class="text-lg font-semibold text-gray-700">Referral Cost</h2>
        <p class="text-3xl font-bold text-indigo-600">Ksh 700</p>
      </div>
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
        <h2 class="text-lg font-semibold text-gray-700">Referral Bonus</h2>
        <p class="text-3xl font-bold text-indigo-600">Ksh 140</p>
        <span class="text-sm text-gray-500">20% Commission</span>
      </div>
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
        <h2 class="text-lg font-semibold text-gray-700">Earning Per Referral</h2>
        <p class="text-3xl font-bold text-indigo-600">Ksh 140</p>
        <span class="text-sm text-gray-500">Weekly Bonus</span>
      </div>
    </div>

    <!-- Main Tabs (Weekly / Monthly) -->
    <div class="flex justify-center space-x-2 mb-6">
      <button 
        id="weeklyMainTab"
        class="px-4 py-2 bg-indigo-600 text-white rounded-l shadow font-semibold"
      >
        Weekly
      </button>
      <button 
        id="monthlyMainTab"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-r shadow font-semibold"
      >
        Monthly
      </button>
    </div>

    <!-- WEEKLY SECTION -->
    <section id="weeklySection">
      <h2 class="text-2xl font-bold mb-4">Weekly Leaderboard</h2>

      <!-- Sub-tabs for the last 8 weeks -->
      <div class="flex space-x-2 mb-4">
        <?php 
          // 0..7 => 8 weeks total
          for ($i = 0; $i < 8; $i++):
            $weekLabel = ($i === 0) ? "Current Week" : "{$i} Week(s) Ago";
        ?>
        <button 
          class="weekly-tab px-3 py-2 rounded shadow <?php echo ($i === 0) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'; ?>"
          data-week="<?php echo $i; ?>"
        >
          <?php echo $weekLabel; ?>
        </button>
        <?php endfor; ?>
      </div>

      <?php 
      // Render 8 different tables, one for each offset
      for ($w = 0; $w < 8; $w++):
        $weeklyLeaders = getWeeklyLeaders($w); 
      ?>
      <div class="weekly-content <?php echo ($w === 0) ? '' : 'hidden'; ?>" id="week<?php echo $w; ?>">
        <div class="overflow-x-auto bg-white shadow rounded-xl mb-8">
          <table class="min-w-full table-auto">
            <thead class="bg-indigo-50">
              <tr>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Rank</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Referrer</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Companies Referred</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Referrals</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Amount Paid</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Bonus Earned</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Total Payout</th>
                <th class="py-3 px-4 text-left font-medium text-indigo-600">Payout Number</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($weeklyLeaders)): ?>
                <?php $rank = 1; ?>
                <?php foreach ($weeklyLeaders as $leader): ?>
                  <?php
                    // Calculate bonus & total
                    $bonus       = $leader['number_of_referrals'] * 140;
                    $totalPayout = $leader['total_amount_paid'] + $bonus;

                    // Medal for top 3
                    if     ($rank === 1) $medal = '🥇';
                    elseif ($rank === 2) $medal = '🥈';
                    elseif ($rank === 3) $medal = '🥉';
                    else                 $medal = $rank;

                    // Convert "companies" to clickable links if "demo"
                    $companiesOutput = '<em>No companies</em>';
                    if (isset($leader['companies']) && !empty($leader['companies'])) {
                      $companiesArr = explode(',', $leader['companies']);
                      $companyLinks = [];
                      foreach ($companiesArr as $c) {
                        $c = trim($c);
                        if (strtolower($c) === 'demo') {
                          $companyLinks[] = '<a href="http://demo.ispledger.com" class="text-blue-600 hover:underline" target="_blank">demo</a>';
                        } else {
                          $companyLinks[] = htmlspecialchars($c);
                        }
                      }
                      $companiesOutput = implode(', ', $companyLinks);
                    }
                  ?>
                  <tr class="border-b">
                    <td class="py-2 px-4"><?php echo $medal; ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($leader['name']); ?></td>
                    <td class="py-2 px-4"><?php echo $companiesOutput; ?></td>
                    <td class="py-2 px-4"><?php echo $leader['number_of_referrals']; ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($bonus, 2); ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                    <!-- Rename the last column to "Payout Number" -->
                    <td class="py-2 px-4">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                  </tr>
                  <?php $rank++; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="py-4 px-4 text-center text-gray-500">No referrals data for this week offset.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endfor; ?>
    </section>

    <!-- MONTHLY SECTION -->
    <section id="monthlySection" class="hidden">
      <h2 class="text-2xl font-bold mb-4">Monthly Leaderboard</h2>

      <!-- Sub-tabs for the last 6 months -->
      <div class="flex space-x-2 mb-4">
        <?php 
          // 0..5 => 6 months total
          for ($i = 0; $i < 6; $i++):
            $monthLabel = ($i === 0) ? "Current Month" : "{$i} Month(s) Ago";
        ?>
        <button 
          class="monthly-tab px-3 py-2 rounded shadow <?php echo ($i === 0) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'; ?>"
          data-month="<?php echo $i; ?>"
        >
          <?php echo $monthLabel; ?>
        </button>
        <?php endfor; ?>
      </div>

      <?php 
      // Render 6 different tables, one for each offset
      for ($m = 0; $m < 6; $m++):
        $monthlyLeaders = getMonthlyLeaders($m);
      ?>
      <div class="monthly-content <?php echo ($m === 0) ? '' : 'hidden'; ?>" id="month<?php echo $m; ?>">
        <div class="overflow-x-auto bg-white shadow rounded-xl mb-8">
          <table class="min-w-full table-auto">
            <thead class="bg-green-50">
              <tr>
                <th class="py-3 px-4 text-left font-medium text-green-600">Rank</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Referrer</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Companies Referred</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Referrals</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Amount Paid</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Bonus Earned</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Total Payout</th>
                <th class="py-3 px-4 text-left font-medium text-green-600">Payout Number</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($monthlyLeaders)): ?>
                <?php $rank = 1; ?>
                <?php foreach ($monthlyLeaders as $leader): ?>
                  <?php
                    $bonus       = $leader['number_of_referrals'] * 140;
                    $totalPayout = $leader['total_amount_paid'] + $bonus;

                    if     ($rank === 1) $medal = '🥇';
                    elseif ($rank === 2) $medal = '🥈';
                    elseif ($rank === 3) $medal = '🥉';
                    else                 $medal = $rank;

                    // "Companies" fallback
                    $companiesOutput = '<em>No companies</em>';
                    if (isset($leader['companies']) && !empty($leader['companies'])) {
                      $companiesArr = explode(',', $leader['companies']);
                      $companyLinks = [];
                      foreach ($companiesArr as $c) {
                        $c = trim($c);
                        if (strtolower($c) === 'demo') {
                          $companyLinks[] = '<a href="http://demo.ispledger.com" class="text-blue-600 hover:underline" target="_blank">demo</a>';
                        } else {
                          $companyLinks[] = htmlspecialchars($c);
                        }
                      }
                      $companiesOutput = implode(', ', $companyLinks);
                    }
                  ?>
                  <tr class="border-b">
                    <td class="py-2 px-4"><?php echo $medal; ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($leader['name']); ?></td>
                    <td class="py-2 px-4"><?php echo $companiesOutput; ?></td>
                    <td class="py-2 px-4"><?php echo $leader['number_of_referrals']; ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($bonus, 2); ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                    <td class="py-2 px-4">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                  </tr>
                  <?php $rank++; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="py-4 px-4 text-center text-gray-500">No referrals data for this month offset.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endfor; ?>
    </section>
  </main>

  <!-- JS for Main Tab & Sub-Tab Switching -->
  <script>
    // Main tabs
    const weeklyMainTab = document.getElementById('weeklyMainTab');
    const monthlyMainTab = document.getElementById('monthlyMainTab');
    const weeklySection  = document.getElementById('weeklySection');
    const monthlySection = document.getElementById('monthlySection');

    weeklyMainTab.addEventListener('click', () => {
      weeklySection.classList.remove('hidden');
      monthlySection.classList.add('hidden');
      weeklyMainTab.classList.add('bg-indigo-600','text-white');
      monthlyMainTab.classList.remove('bg-indigo-600','text-white');
      monthlyMainTab.classList.add('bg-gray-200','text-gray-700');
    });

    monthlyMainTab.addEventListener('click', () => {
      monthlySection.classList.remove('hidden');
      weeklySection.classList.add('hidden');
      monthlyMainTab.classList.add('bg-indigo-600','text-white');
      weeklyMainTab.classList.remove('bg-indigo-600','text-white');
      weeklyMainTab.classList.add('bg-gray-200','text-gray-700');
    });

    // Weekly sub-tabs
    const weekTabs = document.querySelectorAll('.weekly-tab');
    weekTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.weekly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-week');
        document.getElementById('week' + offset).classList.remove('hidden');

        // Update styles
        weekTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        weekTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });

    // Monthly sub-tabs
    const monthTabs = document.querySelectorAll('.monthly-tab');
    monthTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.monthly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-month');
        document.getElementById('month' + offset).classList.remove('hidden');

        // Update styles
        monthTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        monthTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });
  </script>
</body>
</html>
