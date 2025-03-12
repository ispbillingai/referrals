
<?php
// templates/weekly_leaderboard.php
?>
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
