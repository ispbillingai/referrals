
<?php
// templates/monthly_leaderboard.php
?>
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
