
<!-- MONTHLY SECTION with Enhanced Design -->
<section id="monthlySection" class="hidden">
  <h2 class="text-3xl font-bold mb-6 text-gray-800">Monthly Leaderboard</h2>

  <!-- Enhanced Monthly Sub-tabs -->
  <?php 
  require_once 'templates/components/monthly_tabs.php';
  echo generateMonthlyTabs(6); 
  ?>

  <?php 
  require_once 'templates/components/monthly_tab_content.php';
  
  for ($m = 0; $m < 6; $m++):
    $monthlyLeaders = getMonthlyLeaders($m);
    displayMonthlyTabContent($m, $monthlyLeaders);
  endfor; 
  ?>

  <!-- Companies Modal -->
  <?php require_once 'templates/components/companies_modal.php'; ?>
</section>

<!-- Include JavaScript file -->
<script src="assets/js/monthly_leaderboard.js"></script>
