
<?php
// index.php - Main entry point
require_once __DIR__ . '/functions/referral_functions.php';
require_once __DIR__ . '/functions/telegram_utils.php';

// Retrieve leaderboard data
$weeklyLeaders = getWeeklyLeaders();
$monthlyLeaders = getMonthlyLeaders();

require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/leaderboard_view.php';
require_once __DIR__ . '/templates/footer.php';
?>
