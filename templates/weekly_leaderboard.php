
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
        
        // Calculate date range for this week
        $startDate = new DateTime();
        if ($i > 0) {
          $startDate->modify("-{$i} week");
        }
        // Get to Monday (start of week)
        $dayOfWeek = $startDate->format('N');
        $daysToSubtract = $dayOfWeek - 1;
        $startDate->modify("-{$daysToSubtract} day");
        
        // End date is 6 days later (Sunday)
        $endDate = clone $startDate;
        $endDate->modify('+6 days');
        
        $dateRangeLabel = $startDate->format('M d') . ' - ' . $endDate->format('M d, Y');
    ?>
    <button 
      class="weekly-tab whitespace-nowrap px-4 py-2 rounded-lg shadow-md transition-all duration-300 <?php echo ($i === 0) ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>"
      data-week="<?php echo $i; ?>"
    >
      <div class="flex flex-col items-center">
        <span><?php echo $weekLabel; ?></span>
        <span class="text-xs mt-1 font-normal"><?php echo $dateRangeLabel; ?></span>
      </div>
    </button>
    <?php endfor; ?>
  </div>

  <?php 
  for ($w = 0; $w < 8; $w++):
    $weeklyLeaders = getWeeklyLeaders($w);
    
    // Calculate date range for display in the content section
    $startDate = new DateTime();
    if ($w > 0) {
      $startDate->modify("-{$w} week");
    }
    // Get to Monday (start of week)
    $dayOfWeek = $startDate->format('N');
    $daysToSubtract = $dayOfWeek - 1;
    $startDate->modify("-{$daysToSubtract} day");
    
    // End date is 6 days later (Sunday)
    $endDate = clone $startDate;
    $endDate->modify('+6 days');
    
    $dateRangeLabel = $startDate->format('F d') . ' - ' . $endDate->format('F d, Y');
  ?>
  <div class="weekly-content <?php echo ($w === 0) ? '' : 'hidden'; ?>" id="week<?php echo $w; ?>">
    <div class="bg-white p-3 rounded-lg shadow-sm mb-4">
      <h3 class="text-gray-700 font-medium">
        <?php echo ($w === 0) ? 'Current Week' : $w . ' Week(s) Ago'; ?>: 
        <span class="font-normal text-gray-500"><?php echo $dateRangeLabel; ?></span>
      </h3>
    </div>
    
    <!-- Table with totals row -->
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
          <?php 
          if (!empty($weeklyLeaders)): 
            $rank = 1;
            $totalReferrals = 0;
            $totalAmountPaid = 0;
            $totalBonus = 0;
            $totalFinalPayout = 0;
            
            foreach ($weeklyLeaders as $leader): 
              // Calculate values
              $bonus = ($rank <= 3) ? $leader['number_of_referrals'] * 140 : 0;
              $totalPayout = $leader['total_amount_paid'] + $bonus;
              
              // Add to totals
              $totalReferrals += $leader['number_of_referrals'];
              $totalAmountPaid += $leader['total_amount_paid'];
              $totalBonus += $bonus;
              $totalFinalPayout += $totalPayout; // Fix: Use individual row's total payout
              
              if     ($rank === 1) $medal = '🥇';
              elseif ($rank === 2) $medal = '🥈';
              elseif ($rank === 3) $medal = '🥉';
              else                 $medal = $rank;

              // New company display logic with ispledger.com domain
              $companiesOutput = '<em class="text-gray-400">No companies</em>';
              if (isset($leader['companies']) && !empty($leader['companies'])) {
                $companiesArr = explode(',', $leader['companies']);
                $totalCompanies = count($companiesArr);
                
                $companiesOutput = '<div class="flex items-center gap-2">
                  <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-200 transition-colors view-companies" data-companies="' . htmlspecialchars(json_encode($companiesArr)) . '">
                    Click to view ' . $totalCompanies . ' companies
                  </button>
                </div>';
              }

              $rowClass = $rank <= 3 ? 'bg-gradient-to-r from-indigo-50/50 to-transparent' : '';
              
              // Display payout number instead of phone number
              $payoutNumber = isset($leader['payout_number']) ? htmlspecialchars($leader['payout_number']) : 'N/A';
            ?>
              <tr class="border-b hover:bg-gray-50 transition-colors <?php echo $rowClass; ?>">
                <td class="py-4 px-6 text-xl"><?php echo $medal; ?></td>
                <td class="py-4 px-6 font-medium"><?php echo htmlspecialchars($leader['name']); ?></td>
                <td class="py-4 px-6"><?php echo $companiesOutput; ?></td>
                <td class="py-4 px-6"><?php echo $leader['number_of_referrals']; ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                <td class="py-4 px-6 text-green-600">Ksh <?php echo number_format($bonus, 2); ?></td>
                <td class="py-4 px-6 font-medium">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                <td class="py-4 px-6"><?php echo $payoutNumber; ?></td>
              </tr>
            <?php 
              $rank++;
            endforeach; 
            ?>
            <!-- Totals Row -->
            <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
              <td colspan="3" class="py-3 px-6">TOTALS</td>
              <td class="py-3 px-6"><?php echo $totalReferrals; ?></td>
              <td class="py-3 px-6">Ksh <?php echo number_format($totalAmountPaid, 2); ?></td>
              <td class="py-3 px-6">Ksh <?php echo number_format($totalBonus, 2); ?></td>
              <td class="py-3 px-6">Ksh <?php echo number_format($totalFinalPayout, 2); ?></td>
              <td class="py-3 px-6">--</td>
            </tr>
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
  
  <!-- Companies Modal (shared with monthly section) -->
  <div id="weeklyCompaniesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-bold mb-4">Referred Companies</h3>
      <div id="weeklyCompaniesList" class="max-h-60 overflow-y-auto"></div>
      <button class="mt-4 w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors" onclick="document.getElementById('weeklyCompaniesModal').classList.add('hidden')">
        Close
      </button>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const weeklySection = document.getElementById('weeklySection');
  if (weeklySection) {
    // Set up tab switching
    weeklySection.querySelectorAll('.weekly-tab').forEach(tab => {
      tab.addEventListener('click', function() {
        // Hide all content sections
        weeklySection.querySelectorAll('.weekly-content').forEach(content => {
          content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        weeklySection.querySelectorAll('.weekly-tab').forEach(t => {
          t.classList.remove('bg-indigo-600', 'text-white', 'shadow-indigo-200');
          t.classList.add('bg-white', 'text-gray-700');
        });
        
        // Show the selected content
        const weekNum = this.dataset.week;
        const targetContent = document.getElementById('week' + weekNum);
        if (targetContent) {
          targetContent.classList.remove('hidden');
        }
        
        // Add active class to clicked tab
        this.classList.remove('bg-white', 'text-gray-700');
        this.classList.add('bg-indigo-600', 'text-white', 'shadow-indigo-200');
      });
    });
    
    // Set up company view buttons with ispledger.com domain
    weeklySection.querySelectorAll('.view-companies').forEach(button => {
      button.addEventListener('click', function() {
        const companies = JSON.parse(this.dataset.companies);
        const companiesList = document.getElementById('weeklyCompaniesList');
        companiesList.innerHTML = companies.map(company => {
          company = company.trim();
          const companyDomain = company.toLowerCase() + '.ispledger.com';
          return `<div class="py-2 border-b last:border-0">
            <a href="http://${companyDomain}" class="text-blue-600 hover:underline" target="_blank">
              ${company}.ispledger.com
            </a>
          </div>`;
        }).join('');
        
        document.getElementById('weeklyCompaniesModal').classList.remove('hidden');
      });
    });
  }
});
</script>
