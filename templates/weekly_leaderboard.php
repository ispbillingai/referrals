
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
            <th class="py-4 px-6 text-left font-semibold text-indigo-600">Phone Number</th>
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

                // New company display logic with click to view
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
                
                // Display phone number
                $phoneNumber = isset($leader['phone_number']) ? htmlspecialchars($leader['phone_number']) : 'N/A';
              ?>
              <tr class="border-b hover:bg-gray-50 transition-colors <?php echo $rowClass; ?>">
                <td class="py-4 px-6 text-xl"><?php echo $medal; ?></td>
                <td class="py-4 px-6 font-medium"><?php echo htmlspecialchars($leader['name']); ?></td>
                <td class="py-4 px-6"><?php echo $phoneNumber; ?></td>
                <td class="py-4 px-6"><?php echo $companiesOutput; ?></td>
                <td class="py-4 px-6"><?php echo $leader['number_of_referrals']; ?></td>
                <td class="py-4 px-6">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
                <td class="py-4 px-6 text-green-600">Ksh <?php echo number_format($bonus, 2); ?></td>
                <td class="py-4 px-6 font-medium">Ksh <?php echo number_format($totalPayout, 2); ?></td>
                <td class="py-4 px-6">N/A</td>
              </tr>
              <?php $rank++; ?>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="py-8 px-6 text-center text-gray-500">No referrals data for this week offset.</td>
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
    
    // Set up company view buttons
    weeklySection.querySelectorAll('.view-companies').forEach(button => {
      button.addEventListener('click', function() {
        const companies = JSON.parse(this.dataset.companies);
        const companiesList = document.getElementById('weeklyCompaniesList');
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
        
        document.getElementById('weeklyCompaniesModal').classList.remove('hidden');
      });
    });
  }
});
</script>
