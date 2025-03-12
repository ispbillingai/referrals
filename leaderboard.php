<?php
// leaderboard.php
require_once 'config.php';
require_once 'referral_functions.php';

// Retrieve data
$weeklyLeaders = getWeeklyLeaders();
$monthlyLeaders = getMonthlyLeaders();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Referral Leaderboard</title>
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="max-w-5xl mx-auto py-10">
    <h1 class="text-3xl font-bold mb-8 text-center">Referral Leaderboard</h1>
    
    <!-- Weekly Leaderboard -->
    <div class="mb-10">
      <h2 class="text-2xl font-semibold mb-4 text-center">Weekly Leaderboard</h2>
      <p class="text-center text-gray-600 mb-6">
        Showing top referrers for the current week (resets every Monday).
      </p>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded">
          <thead>
            <tr>
              <th class="py-3 px-4 bg-blue-100 border-b text-left">Rank</th>
              <th class="py-3 px-4 bg-blue-100 border-b text-left">Referrer Name</th>
              <th class="py-3 px-4 bg-blue-100 border-b text-left"># of Referrals</th>
              <th class="py-3 px-4 bg-blue-100 border-b text-left">Total Amount Paid</th>
              <th class="py-3 px-4 bg-blue-100 border-b text-left">Total Bonuses</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($weeklyLeaders) {
              $rank = 1;
              foreach ($weeklyLeaders as $leader) {
                  // If you only want to show top 3, you could break after 3 rows
                  // if ($rank > 3) break; 
                  $highlight = ($rank <= 3) ? 'bg-yellow-100' : '';
                  echo "<tr class='$highlight'>";
                  echo "<td class='py-2 px-4 border-b'>$rank</td>";
                  echo "<td class='py-2 px-4 border-b'>{$leader['name']}</td>";
                  echo "<td class='py-2 px-4 border-b'>{$leader['number_of_referrals']}</td>";
                  echo "<td class='py-2 px-4 border-b'>$" . number_format($leader['total_amount_paid'], 2) . "</td>";
                  echo "<td class='py-2 px-4 border-b'>$" . number_format($leader['total_bonuses'], 2) . "</td>";
                  echo "</tr>";
                  $rank++;
              }
            } else {
              echo "<tr><td colspan='5' class='py-4 px-4 text-center'>No referrals found this week.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <!-- Show Weekly Prizes Info -->
      <div class="mt-4 text-center text-sm text-gray-600">
        <p>Weekly Prizes: Top 3 referrers are rewarded (exact reward structure is up to you).</p>
      </div>
    </div>
    
    <!-- Monthly Leaderboard -->
    <div class="mb-10">
      <h2 class="text-2xl font-semibold mb-4 text-center">Monthly Leaderboard</h2>
      <p class="text-center text-gray-600 mb-6">
        Showing top referrers for the current month (resets on the 1st).
      </p>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded">
          <thead>
            <tr>
              <th class="py-3 px-4 bg-green-100 border-b text-left">Rank</th>
              <th class="py-3 px-4 bg-green-100 border-b text-left">Referrer Name</th>
              <th class="py-3 px-4 bg-green-100 border-b text-left"># of Referrals</th>
              <th class="py-3 px-4 bg-green-100 border-b text-left">Total Amount Paid</th>
              <th class="py-3 px-4 bg-green-100 border-b text-left">Total Bonuses</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($monthlyLeaders) {
              $rank = 1;
              foreach ($monthlyLeaders as $leader) {
                  // If you only want top 5 for display, you could break after 5
                  // if ($rank > 5) break; 
                  $highlight = ($rank <= 5) ? 'bg-yellow-100' : '';
                  echo "<tr class='$highlight'>";
                  echo "<td class='py-2 px-4 border-b'>$rank</td>";
                  echo "<td class='py-2 px-4 border-b'>{$leader['name']}</td>";
                  echo "<td class='py-2 px-4 border-b'>{$leader['number_of_referrals']}</td>";
                  echo "<td class='py-2 px-4 border-b'>$" . number_format($leader['total_amount_paid'], 2) . "</td>";
                  echo "<td class='py-2 px-4 border-b'>$" . number_format($leader['total_bonuses'], 2) . "</td>";
                  echo "</tr>";
                  $rank++;
              }
            } else {
              echo "<tr><td colspan='5' class='py-4 px-4 text-center'>No referrals found this month.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <!-- Show Monthly Prizes Info -->
      <div class="mt-4 text-center text-sm text-gray-600">
        <p>Monthly Prizes Distribution:</p>
        <ul class="list-disc list-inside">
          <li>1st Place: $5000</li>
          <li>2nd Place: $2000</li>
          <li>3rd Place: $1000</li>
          <li>4th &amp; 5th Places: No monetary reward (unless changed)</li>
        </ul>
      </div>
    </div>

    <!-- Countdown or Next Reset Info (Optional) -->
    <div class="text-center mt-8 text-gray-500 text-sm">
      <p>Next Weekly Reset: Every Monday at 00:00</p>
      <p>Next Monthly Reset: 1st of each month</p>
    </div>
  </div>
</body>
</html>
