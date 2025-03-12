
<?php
// index.php - Main entry point
require_once __DIR__ . '/functions/referral_functions.php';
require_once __DIR__ . '/functions/telegram_utils.php';

// Retrieve leaderboard data for main page
$weeklyLeaders = getWeeklyLeaders();
$monthlyLeaders = getMonthlyLeaders();

// Include the leaderboard view
require_once __DIR__ . '/templates/leaderboard_view.php';
?>
