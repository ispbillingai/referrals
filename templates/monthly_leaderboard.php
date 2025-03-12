<!-- MONTHLY SECTION with Enhanced Design -->
<section id="monthlySection" class="hidden">
  <h2 class="text-3xl font-bold mb-6 text-gray-800">Monthly Leaderboard</h2>

  <!-- Enhanced Monthly Sub-tabs -->
  <div class="flex space-x-3 mb-6 overflow-x-auto pb-2">
    <?php 
      for ($i = 0; $i < 6; $i++):
        $monthLabel = ($i === 0) ? "Current Month" : "{$i} Month(s) Ago";
        
        // Calculate date range for this month
        $currentDate = new DateTime();
        if ($i > 0) {
          $currentDate->modify("-{$i} month");
        }
        $monthStart = clone $currentDate;
        $monthStart->modify('first day of this month');
        $monthEnd = clone $currentDate;
        $monthEnd->modify('last day of this month');
        
        $dateRangeLabel = $monthStart->format('M d') . ' - ' . $monthEnd->format('M d, Y');
    ?>
    <button 
      class="monthly-tab whitespace-nowrap px-4 py-2 rounded-lg shadow-md transition-all duration-300 <?php echo ($i === 0) ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>"
      data-month="<?php echo $i; ?>"
    >
      <div class="flex flex-col items-center">
        <span><?php echo $monthLabel; ?></span>
        <span class="text-xs mt-1 font-normal"><?php echo $dateRangeLabel; ?></span>
      </div>
    </button>
    <?php endfor; ?>
  </div>

  <?php 
  for ($m = 0; $m < 6; $m++):
    $monthlyLeaders = getMonthlyLeaders($m);
    
    // Calculate date range for display in the content section
    $currentDate = new DateTime();
    if ($m > 0) {
      $currentDate->modify("-{$m} month");
    }
    $monthStart = clone $currentDate;
    $monthStart->modify('first day of this month');
    $monthEnd = clone $currentDate;
    $monthEnd->modify('last day of this month');
    
    $dateRangeLabel = $monthStart->format('F d') . ' - ' . $monthEnd->format('F d, Y');
  ?>
  <div class="monthly-content <?php echo ($m === 0) ? '' : 'hidden'; ?>" id="month<?php echo $m; ?>">
    <div class="bg-white p-3 rounded-lg shadow-sm mb-4">
      <h3 class="text-gray-700 font-medium">
        <?php echo ($m === 0) ? 'Current Month' : $m . ' Month(s) Ago'; ?>: 
        <span class="font-normal text-gray-500"><?php echo $dateRangeLabel; ?></span>
      </h3>
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
            <th class="py-3 px-4 text-left font-medium text-green-600">Total Payout</th>
            <th class="py-3 px-4 text-left font-medium text-green-600">Payout Number</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($monthlyLeaders)): 
            $rank = 1;
            $totalReferrals = 0;
            $totalPrize = 0;
            $totalFinalPayout = 0; // New variable to properly track total payout
            
            $totalEntries = count($monthlyLeaders); // Add total entries counter

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
              
              // Add to totals
              $totalReferrals += $leader['number_of_referrals'];
              $totalPrize += $prize;
              $totalFinalPayout += $prize; // Fix: This now properly sums the prize amount
              
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
              <td class="py-4 px-6">Ksh <?php echo number_format($prize, 2); ?></td>
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
            <td class="py-3 px-6">Ksh <?php echo number_format($totalFinalPayout, 2); ?></td>
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
  <?php endfor; ?>

  <!-- Companies Modal -->
  <div id="companiesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-bold mb-4">Referred Companies</h3>
      <div id="companiesList" class="max-h-60 overflow-y-auto"></div>
      <button class="mt-4 w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors" onclick="document.getElementById('companiesModal').classList.add('hidden')">
        Close
      </button>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Set up tab switching for monthly section
  const monthlyTabs = document.querySelectorAll('.monthly-tab');
  monthlyTabs.forEach(tab => {
    tab.addEventListener('click', function() {
      // Hide all content sections
      document.querySelectorAll('.monthly-content').forEach(content => {
        content.classList.add('hidden');
      });
      
      // Remove active class from all tabs
      monthlyTabs.forEach(t => {
        t.classList.remove('bg-indigo-600', 'text-white', 'shadow-indigo-200');
        t.classList.add('bg-white', 'text-gray-700');
      });
      
      // Show the selected content
      const monthNum = this.dataset.month;
      const targetContent = document.getElementById('month' + monthNum);
      if (targetContent) {
        targetContent.classList.remove('hidden');
      }
      
      // Add active class to clicked tab
      this.classList.remove('bg-white', 'text-gray-700');
      this.classList.add('bg-indigo-600', 'text-white', 'shadow-indigo-200');
    });
  });

  document.querySelectorAll('.view-companies').forEach(button => {
    button.addEventListener('click', function() {
      const companies = JSON.parse(this.dataset.companies);
      const companiesList = document.getElementById('companiesList');
      companiesList.innerHTML = companies.map(company => {
        company = company.trim();
        // Create a link with .ispledger.com for all companies
        const companyDomain = company.toLowerCase() + '.ispledger.com';
        return `<div class="py-2 border-b last:border-0">
          <a href="http://${companyDomain}" class="text-blue-600 hover:underline" target="_blank">
            ${company}.ispledger.com
          </a>
        </div>`;
      }).join('');
      
      document.getElementById('companiesModal').classList.remove('hidden');
    });
  });
});
</script>
