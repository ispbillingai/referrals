<?php
// index.php
require_once __DIR__ . '../functions/referral_functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Referral Leaderboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 text-gray-900 min-h-screen">

  <!-- Hero Header with Enhanced Gradient -->
  <header class="bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 text-white px-8 py-20 relative overflow-hidden">
    <div class="max-w-6xl mx-auto relative z-10">
      <h1 class="text-5xl font-bold mb-4 tracking-tight">Referral Leaderboard</h1>
      <p class="text-xl font-medium opacity-90">Track your impact and compete with fellow referrers</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-t-[3rem] transform translate-y-1"></div>
  </header>

  <!-- Main Container with Enhanced Styling -->
  <main class="max-w-6xl mx-auto -mt-12 px-4 relative z-20 mb-10">
    <!-- Quick Stats Cards with Enhanced Design -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
      <div class="bg-white rounded-2xl shadow-lg shadow-indigo-100 p-8 flex flex-col items-center transform hover:scale-105 transition-transform duration-300">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Referral Cost</h2>
        <p class="text-4xl font-bold text-indigo-600">Ksh 700</p>
      </div>
      <div class="bg-white rounded-2xl shadow-lg shadow-indigo-100 p-8 flex flex-col items-center transform hover:scale-105 transition-transform duration-300">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Referral Bonus</h2>
        <p class="text-4xl font-bold text-indigo-600">Ksh 140</p>
        <span class="text-sm text-gray-500 mt-2">20% Commission</span>
      </div>
      <div class="bg-white rounded-2xl shadow-lg shadow-indigo-100 p-8 flex flex-col items-center transform hover:scale-105 transition-transform duration-300">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Earning Per Referral</h2>
        <p class="text-4xl font-bold text-indigo-600">Ksh 140</p>
        <span class="text-sm text-gray-500 mt-2">Weekly Bonus</span>
      </div>
    </div>

    <!-- Enhanced Main Tabs -->
    <div class="flex justify-center space-x-2 mb-8">
      <button 
        id="weeklyMainTab"
        class="px-6 py-3 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 font-semibold transition-all duration-300 hover:bg-indigo-700"
      >
        Weekly
      </button>
      <button 
        id="monthlyMainTab"
        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl shadow-lg font-semibold transition-all duration-300 hover:bg-gray-300"
      >
        Monthly
      </button>
    </div>

    <!-- WEEKLY SECTION with Enhanced Design -->
    <section id="weeklySection">
      <h2 class="text-3xl font-bold mb-6 text-gray-800">Weekly Leaderboard</h2>

      <!-- Enhanced Sub-tabs -->
      <div class="flex space-x-3 mb-6 overflow-x-auto pb-2">
        <?php 
          for ($i = 0; $i < 8; $i++):
            $weekLabel = ($i === 0) ? "Current Week" : "{$i} Week(s) Ago";
        ?>
        <button 
          class="weekly-tab whitespace-nowrap px-4 py-2 rounded-lg shadow-md transition-all duration-300 <?php echo ($i === 0) ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>"
          data-week="<?php echo $i; ?>"
        >
          <?php echo $weekLabel; ?>
        </button>
        <?php endfor; ?>
      </div>

      <?php 
      for ($w = 0; $w < 8; $w++):
        $weeklyLeaders = getWeeklyLeaders($w); 
      ?>
      <div class="weekly-content <?php echo ($w === 0) ? '' : 'hidden'; ?>" id="week<?php echo $w; ?>">
        <div class="overflow-x-auto bg-white shadow-lg rounded-2xl mb-8">
          <table class="min-w-full table-auto">
            <thead class="bg-indigo-50">
              <tr>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Rank</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Referrer</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Companies Referred</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Referrals</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Amount Paid</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Bonus Earned</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Total Payout</th>
                <th class="py-4 px-6 text-left font-semibold text-indigo-600">Payout Number</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($weeklyLeaders)): ?>
                <?php $rank = 1; ?>
                <?php foreach ($weeklyLeaders as $leader): ?>
                  <?php
                    $bonus = $leader['number_of_referrals'] * 140;
                    $totalPayout = $leader['total_amount_paid'] + $bonus;

                    if     ($rank === 1) $medal = '🥇';
                    elseif ($rank === 2) $medal = '🥈';
                    elseif ($rank === 3) $medal = '🥉';
                    else                 $medal = $rank;

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

                    $rowClass = $rank <= 3 ? 'bg-gradient-to-r from-indigo-50/50 to-transparent' : '';
                  ?>
                  <tr class="border-b hover:bg-gray-50 transition-colors <?php echo $rowClass; ?>">
                    <td class="py-4 px-6 text-xl"><?php echo $medal; ?></td>
                    <td class="py-4 px-6 font-medium"><?php echo htmlspecialchars($leader['name']); ?></td>
                    <td class="py-4 px-6"><?php echo $companiesOutput; ?></td>
                    <td class="py-4 px-6"><?php echo $leader['number_of_referrals']; ?></td>
                    <td class="py-4 px-6">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                    <td class="py-4 px-6 text-green-600">Ksh <?php echo number_format($bonus, 2); ?></td>
                    <td class="py-4 px-6 font-medium">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                    <td class="py-4 px-6">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                  </tr>
                  <?php $rank++; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="py-8 px-6 text-center text-gray-500">No referrals data for this week offset.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endfor; ?>
    </section>

    <!-- MONTHLY SECTION with Enhanced Design -->
    <section id="monthlySection" class="hidden">
      <h2 class="text-3xl font-bold mb-6 text-gray-800">Monthly Leaderboard</h2>

      <!-- Enhanced Monthly Sub-tabs -->
      <div class="flex space-x-3 mb-6 overflow-x-auto pb-2">
        <?php 
          for ($i = 0; $i < 6; $i++):
            $monthLabel = ($i === 0) ? "Current Month" : "{$i} Month(s) Ago";
        ?>
        <button 
          class="monthly-tab whitespace-nowrap px-4 py-2 rounded-lg shadow-md transition-all duration-300 <?php echo ($i === 0) ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>"
          data-month="<?php echo $i; ?>"
        >
          <?php echo $monthLabel; ?>
        </button>
        <?php endfor; ?>
      </div>

      <?php 
      for ($m = 0; $m < 6; $m++):
        $monthlyLeaders = getMonthlyLeaders($m);
      ?>
      <div class="monthly-content <?php echo ($m === 0) ? '' : 'hidden'; ?>" id="month<?php echo $m; ?>">
        <div class="overflow-x-auto bg-white shadow-lg rounded-2xl mb-8">
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

  <script>
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

    const weekTabs = document.querySelectorAll('.weekly-tab');
    weekTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.weekly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-week');
        document.getElementById('week' + offset).classList.remove('hidden');

        weekTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        weekTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });

    const monthTabs = document.querySelectorAll('.monthly-tab');
    monthTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.monthly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-month');
        document.getElementById('month' + offset).classList.remove('hidden');

        monthTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        monthTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });
  </script>
</body>
</html>
