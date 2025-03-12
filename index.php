
<?php
// index.php - Main entry point
require_once __DIR__ . '/functions/referral_functions.php';
require_once __DIR__ . '/functions/telegram_utils.php';

// Retrieve leaderboard data for main page
$weeklyLeaders = getWeeklyLeaders();
$monthlyLeaders = getMonthlyLeaders();

require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/stats_cards.php';
require_once __DIR__ . '/templates/main_tabs.php';
require_once __DIR__ . '/templates/weekly_leaderboard.php';
require_once __DIR__ . '/templates/monthly_leaderboard.php';
require_once __DIR__ . '/templates/footer.php';
?>
