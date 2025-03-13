
<?php
/**
 * Displays monthly leaderboard content for a specific month
 * 
 * @param int $monthOffset The month offset from current (0 = current month)
 * @param array $monthlyLeaders Array of monthly leaders data
 */
function displayMonthlyTabContent($monthOffset, $monthlyLeaders) {
    // Calculate date range for display in the content section
    $currentDate = new DateTime();
    if ($monthOffset > 0) {
      $currentDate->modify("-{$monthOffset} month");
    }
    $monthStart = clone $currentDate;
    $monthStart->modify('first day of this month');
    $monthEnd = clone $currentDate;
    $monthEnd->modify('last day of this month');
    
    $dateRangeLabel = $monthStart->format('F d') . ' - ' . $monthEnd->format('F d, Y');
    ?>
    <div class="monthly-content <?php echo ($monthOffset === 0) ? '' : 'hidden'; ?>" id="month<?php echo $monthOffset; ?>">
      <div class="bg-white p-3 rounded-lg shadow-sm mb-4">
        <h3 class="text-gray-700 font-medium">
          <?php echo ($monthOffset === 0) ? 'Current Month' : $monthOffset . ' Month(s) Ago'; ?>: 
          <span class="font-normal text-gray-500"><?php echo $dateRangeLabel; ?></span>
        </h3>
      </div>
      
      <!-- Prize sharing note -->
      <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">
              <strong>Note about ties:</strong> If multiple referrers tie for a position, the prize amount will be shared equally among them. For example, if 4 referrers tie for a position with a prize of Ksh 5,000, each will receive Ksh 1,250.
            </p>
          </div>
        </div>
      </div>
      
      <div class="overflow-x-auto bg-white shadow-lg rounded-2xl mb-8">
        <table class="min-w-full table-auto">
          <thead class="bg-green-50">
            <tr>
              <th class="py-3 px-4 text-left font-medium text-green-600">Rank</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Referrer</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Companies Referred</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Referrals</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Prize</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Total Earnings</th>
              <th class="py-3 px-4 text-left font-medium text-green-600">Payout Number</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($monthlyLeaders)): 
              $rank = 1;
              $totalReferrals = 0;
              $totalPrize = 0;
              $totalEarnings = 0;
              
              $totalEntries = count($monthlyLeaders);

              foreach ($monthlyLeaders as $leader):
                // Prize money based on rank
                if ($rank === 1) { 
                  $medal = '🥇';
                  $prize = 5000;
                } elseif ($rank === 2) { 
                  $medal = '🥈';
                  $prize = 3000;
                } elseif ($rank === 3) { 
                  $medal = '🥉';
                  $prize = 2000;
                } elseif ($rank === 4 || $rank === 5) {
                  $medal = $rank;
                  $prize = 500;
                } else {
                  $medal = $rank;
                  $prize = 0;
                }
                
                // Calculate total earnings (referrals × 700 + prize)
                $referralEarnings = $leader['number_of_referrals'] * 700;
                $totalEarning = $referralEarnings + $prize;
                
                // Add to totals
                $totalReferrals += $leader['number_of_referrals'];
                $totalPrize += $prize;
                $totalEarnings += $totalEarning;
                
                // New company display logic
                $companiesOutput = '<em class="text-gray-400">No companies</em>';
                if (isset($leader['company_name']) && !empty($leader['company_name'])) {
                  $companiesArr = explode(',', $leader['company_name']);
                  $totalCompanies = count($companiesArr);
                  
                  $companiesOutput = '<div class="flex items-center gap-2">
                    <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-200 transition-colors view-companies" data-companies="' . htmlspecialchars(json_encode($companiesArr)) . '">
                      Click to view ' . $totalCompanies . ' companies
                    </button>
                  </div>';
                }
                
                // Display payout number instead of phone number
                $payoutNumber = isset($leader['payout_number']) ? htmlspecialchars($leader['payout_number']) : 'N/A';
              ?>
              <tr class="border-b hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6"><?php echo $medal; ?></td>
                <td class="py-4 px-6"><?php echo htmlspecialchars($leader['name']); ?></td>
                <td class="py-4 px-6"><?php echo $companiesOutput; ?></td>
                <td class="py-4 px-6"><?php echo $leader['number_of_referrals']; ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($prize, 2); ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($totalEarning, 2); ?></td>
                <td class="py-4 px-6"><?php echo $payoutNumber; ?></td>
              </tr>
              <?php $rank++; ?>
            <?php endforeach; ?>
            <!-- Totals Row -->
            <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
              <td colspan="1" class="py-3 px-6">TOTALS</td>
              <td class="py-3 px-6"><?php echo $totalEntries; ?> entries</td>
              <td class="py-3 px-6">--</td>
              <td class="py-3 px-6"><?php echo $totalReferrals; ?></td>
              <td class="py-3 px-6">Ksh <?php echo number_format($totalPrize, 2); ?></td>
              <td class="py-3 px-6">Ksh <?php echo number_format($totalEarnings, 2); ?></td>
              <td class="py-3 px-6">--</td>
            </tr>
          <?php else: ?>
            <tr>
              <td colspan="7" class="py-4 px-4 text-center text-gray-500">No referrals data for this month offset.</td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
}
?>

