
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
                $bonus = $leader['number_of_referrals'] * 140;
                $totalPayout = $leader['total_amount_paid'] + $bonus;

                if     ($rank === 1) $medal = '🥇';
                elseif ($rank === 2) $medal = '🥈';
                elseif ($rank === 3) $medal = '🥉';
                else                 $medal = $rank;

                // New company display logic
                $companiesOutput = '<em class="text-gray-400">No companies</em>';
                if (isset($leader['company_name']) && !empty($leader['company_name'])) {
                  $companiesArr = explode(',', $leader['company_name']);
                  $totalCompanies = count($companiesArr);
                  $firstCompany = trim($companiesArr[0]);
                  
                  if (strtolower($firstCompany) === 'demo') {
                    $companiesOutput = '<div class="flex items-center gap-2">
                      <a href="http://demo.ispledger.com" class="text-blue-600 hover:underline" target="_blank">demo.ispledger.com</a>
                      <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-200 transition-colors view-companies" data-companies="' . htmlspecialchars(json_encode($companiesArr)) . '">
                        +' . ($totalCompanies - 1) . ' more
                      </button>
                    </div>';
                  } else {
                    $companiesOutput = '<div class="flex items-center gap-2">
                      <span>' . htmlspecialchars($firstCompany) . '</span>
                      <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-200 transition-colors view-companies" data-companies="' . htmlspecialchars(json_encode($companiesArr)) . '">
                        +' . ($totalCompanies - 1) . ' more
                      </button>
                    </div>';
                  }
                }
              ?>
              <tr class="border-b hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6"><?php echo $medal; ?></td>
                <td class="py-4 px-6"><?php echo htmlspecialchars($leader['name']); ?></td>
                <td class="py-4 px-6"><?php echo $companiesOutput; ?></td>
                <td class="py-4 px-6"><?php echo $leader['number_of_referrals']; ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($bonus, 2); ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                <td class="py-4 px-6"><?php echo isset($leader['payout_number']) ? $leader['payout_number'] : 'N/A'; ?></td>
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
  document.querySelectorAll('.view-companies').forEach(button => {
    button.addEventListener('click', function() {
      const companies = JSON.parse(this.dataset.companies);
      const companiesList = document.getElementById('companiesList');
      companiesList.innerHTML = companies.map(company => {
        company = company.trim();
        if (company.toLowerCase() === 'demo') {
          return `<div class="py-2 border-b last:border-0">
            <a href="http://demo.ispledger.com" class="text-blue-600 hover:underline" target="_blank">
              demo.ispledger.com
            </a>
          </div>`;
        }
        return `<div class="py-2 border-b last:border-0">${company}</div>`;
      }).join('');
      
      document.getElementById('companiesModal').classList.remove('hidden');
    });
  });
});
</script>

