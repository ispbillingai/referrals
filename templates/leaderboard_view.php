<!-- templates/leaderboard_view.php -->
<div class="bg-gray-50 min-h-screen">
  <header class="text-center py-8">
    <h1 class="text-3xl font-bold">Referral Rankers</h1>
    <p class="text-gray-600 mt-2">Track and compete in our referral program</p>
  </header>

  <!-- Info Cards Row -->
  <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
    <div class="p-4 bg-white shadow rounded text-center">
      <h2 class="text-sm text-gray-500">Referral Cost</h2>
      <p class="text-xl font-bold text-black">Ksh 700</p>
    </div>
    <div class="p-4 bg-white shadow rounded text-center">
      <h2 class="text-sm text-gray-500">Referral Bonus</h2>
      <p class="text-xl font-bold text-black">Ksh 140</p>
      <small class="block text-gray-400">(20% commission)</small>
    </div>
    <div class="p-4 bg-white shadow rounded text-center">
      <h2 class="text-sm text-gray-500">Earning Per Referral</h2>
      <p class="text-xl font-bold text-green-600">Ksh 5000</p>
      <small class="block text-gray-400">Monthly bonus</small>
    </div>
    <div class="p-4 bg-white shadow rounded text-center">
      <h2 class="text-sm text-gray-500">Weekly Leaderboard resets in:</h2>
      <p class="text-xl font-bold text-black">3d 12h 33m</p>
    </div>
  </div>

  <!-- Tabs -->
  <div class="max-w-5xl mx-auto px-4 mt-8">
    <ul class="flex border-b">
      <li class="-mb-px mr-1">
        <a href="#weekly" class="bg-white inline-block py-2 px-4 text-blue-500 font-semibold border-l border-t border-r border-gray-200 rounded-t">
          Weekly
        </a>
      </li>
      <li class="mr-1">
        <a href="#monthly" class="bg-gray-200 inline-block py-2 px-4 text-gray-500 font-semibold">
          Monthly
        </a>
      </li>
    </ul>
  </div>

  <!-- Weekly Leaderboard Table -->
  <div id="weekly" class="max-w-5xl mx-auto mt-4 p-4">
    <h2 class="text-xl font-bold mb-2">Current Week</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white shadow-md rounded">
        <thead>
          <tr class="bg-blue-50">
            <th class="py-3 px-4 text-left">Rank</th>
            <th class="py-3 px-4 text-left">Referrer</th>
            <th class="py-3 px-4 text-left">Referrals</th>
            <th class="py-3 px-4 text-left">Amount Paid</th>
            <th class="py-3 px-4 text-left">Bonuses Earned</th>
            <th class="py-3 px-4 text-left">Reward</th>
            <th class="py-3 px-4 text-left">Total Payout</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($weeklyLeaders)) : 
              $rank = 1;
              foreach ($weeklyLeaders as $leader):
                  // We can calculate total payout in code if needed
                  $bonus     = $leader['number_of_referrals'] * 140; 
                  $rankClass = ($rank == 1) ? 'bg-yellow-100' : (($rank == 2) ? 'bg-gray-100' : (($rank == 3) ? 'bg-orange-100' : '')); 
          ?>
          <tr class="<?php echo $rankClass; ?>">
            <td class="py-2 px-4 border-b"><?php echo $rank; ?></td>
            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($leader['name']); ?></td>
            <td class="py-2 px-4 border-b"><?php echo $leader['number_of_referrals']; ?></td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($bonus, 2); ?></td>
            <td class="py-2 px-4 border-b">
              <!-- If you have a special weekly reward for top 3, show it here. Example: -->
              <?php if ($rank == 1) echo "--"; // or "Top Prize"
                    elseif ($rank == 2) echo "--";
                    elseif ($rank == 3) echo "--";
                    else echo "--"; ?>
            </td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($bonus, 2); ?></td>
          </tr>
          <?php 
              $rank++;
              endforeach; 
            else: 
          ?>
          <tr>
            <td colspan="7" class="py-4 px-4 text-center text-gray-500">No referrals this week.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Monthly Leaderboard Table -->
  <div id="monthly" class="max-w-5xl mx-auto mt-4 p-4 hidden">
    <h2 class="text-xl font-bold mb-2">Current Month</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white shadow-md rounded">
        <thead>
          <tr class="bg-green-50">
            <th class="py-3 px-4 text-left">Rank</th>
            <th class="py-3 px-4 text-left">Referrer</th>
            <th class="py-3 px-4 text-left">Referrals</th>
            <th class="py-3 px-4 text-left">Amount Paid</th>
            <th class="py-3 px-4 text-left">Bonuses Earned</th>
            <th class="py-3 px-4 text-left">Prize</th>
            <th class="py-3 px-4 text-left">Total Payout</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($monthlyLeaders)) :
              $rank = 1;
              foreach ($monthlyLeaders as $leader):
                  $bonus     = $leader['number_of_referrals'] * 140;
                  $rankClass = ($rank <= 5) ? 'bg-yellow-100' : '';
                  // Determine monthly prize
                  switch($rank) {
                      case 1: $prize = "Ksh 5000"; break;
                      case 2: $prize = "Ksh 2000"; break;
                      case 3: $prize = "Ksh 1000"; break;
                      case 4: case 5: $prize = "No Monetary Reward"; break;
                      default: $prize = "--"; break;
                  }
          ?>
          <tr class="<?php echo $rankClass; ?>">
            <td class="py-2 px-4 border-b"><?php echo $rank; ?></td>
            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($leader['name']); ?></td>
            <td class="py-2 px-4 border-b"><?php echo $leader['number_of_referrals']; ?></td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($leader['total_amount_paid'], 2); ?></td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($bonus, 2); ?></td>
            <td class="py-2 px-4 border-b"><?php echo $prize; ?></td>
            <td class="py-2 px-4 border-b">Ksh <?php echo number_format($bonus, 2); ?></td>
          </tr>
          <?php 
              $rank++;
              endforeach;
            else:
          ?>
          <tr>
            <td colspan="7" class="py-4 px-4 text-center text-gray-500">No referrals this month.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Very basic tab switching with JavaScript
  const weeklyTabLink = document.querySelector('a[href="#weekly"]');
  const monthlyTabLink = document.querySelector('a[href="#monthly"]');
  const weeklyTab = document.getElementById('weekly');
  const monthlyTab = document.getElementById('monthly');
  
  weeklyTabLink.addEventListener('click', (e) => {
    e.preventDefault();
    weeklyTabLink.classList.remove('bg-gray-200','text-gray-500');
    weeklyTabLink.classList.add('bg-white','text-blue-500');
    monthlyTabLink.classList.remove('bg-white','text-blue-500');
    monthlyTabLink.classList.add('bg-gray-200','text-gray-500');
    weeklyTab.classList.remove('hidden');
    monthlyTab.classList.add('hidden');
  });
  
  monthlyTabLink.addEventListener('click', (e) => {
    e.preventDefault();
    monthlyTabLink.classList.remove('bg-gray-200','text-gray-500');
    monthlyTabLink.classList.add('bg-white','text-blue-500');
    weeklyTabLink.classList.remove('bg-white','text-blue-500');
    weeklyTabLink.classList.add('bg-gray-200','text-gray-500');
    monthlyTab.classList.remove('hidden');
    weeklyTab.classList.add('hidden');
  });
</script>
