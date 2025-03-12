<?php
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
