<?php
// functions/referral_functions.php
require_once __DIR__ . '/db.php'; // ensures $pdo is set


function getMonthlyLeaders() {
    global $pdo;

    $sql = "
        SELECT r.id,
               r.name,
               COUNT(ref.id) AS number_of_referrals,
               SUM(ref.amount_paid) AS total_amount_paid,
               COUNT(ref.id) * 140 AS total_bonuses
        FROM referrers r
        LEFT JOIN referrals ref
               ON r.id = ref.referrer_id
               AND MONTH(ref.referral_date) = MONTH(CURDATE())
               AND YEAR(ref.referral_date) = YEAR(CURDATE())
        GROUP BY r.id
        ORDER BY number_of_referrals DESC
        LIMIT 10
    ";
    // limit to 10 or top 5 for monthly

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getWeeklyLeaders() {
    global $pdo;

    $sql = "
        SELECT r.id,
               r.name,
               COUNT(ref.id) AS number_of_referrals,
               SUM(ref.amount_paid) AS total_amount_paid,
               COUNT(ref.id) * 140 AS total_bonuses  -- 20% of 700 = 140
        FROM referrers r
        LEFT JOIN referrals ref
               ON r.id = ref.referrer_id
               AND YEARWEEK(ref.referral_date, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY r.id
        ORDER BY number_of_referrals DESC
        LIMIT 10
    ";
    // limit to 10 or the top 3, your choice

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getWeeklyReferrals($offset = 0) {
    // Example logic: we want the (YEARWEEK(referral_date)) to match
    // the YEARWEEK of (CURDATE() - $offset weeks).
    // This approach uses MySQL's YEARWEEK(...) function with mode 1 (ISO).
    // Adjust if your environment differs.
    global $pdo;
    $sql = "
      SELECT *
      FROM referrals
      WHERE YEARWEEK(referral_date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL :offset WEEK), 1)
      ORDER BY id DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':offset' => $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch referrals for a given monthly offset
 * $offset = 0 => current month
 * $offset = 1 => 1 month ago
 */
function getMonthlyReferrals($offset = 0) {
    global $pdo;
    // We'll handle month offset by subtracting $offset months from CURDATE()
    // Then matching YEAR() and MONTH() to that date
    $sql = "
      SELECT *
      FROM referrals
      WHERE YEAR(referral_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL :offset MONTH))
        AND MONTH(referral_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL :offset MONTH))
      ORDER BY id DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':offset' => $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
