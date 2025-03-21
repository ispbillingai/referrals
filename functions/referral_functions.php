<?php
// functions/referral_functions.php
require_once __DIR__ . '/db.php'; // ensures $pdo is set

/**
 * Get monthly leaderboard data with offset
 * @param int $offset Number of months to go back (0 = current month)
 * @return array Array of leaders data
 */
function getMonthlyLeaders($offset = 0) {
    global $pdo;

    $sql = "
        SELECT r.id,
               r.name,
               r.phone_number AS payout_number,
               GROUP_CONCAT(DISTINCT ref.referred_user_name) AS company_name,
               COUNT(ref.id) AS number_of_referrals,
               SUM(ref.amount_paid) AS total_amount_paid,
               COUNT(ref.id) * 140 AS total_bonuses
        FROM referrers r
        LEFT JOIN referrals ref
               ON r.id = ref.referrer_id
               AND MONTH(ref.referral_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL :offset MONTH))
               AND YEAR(ref.referral_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL :offset MONTH))
        GROUP BY r.id, r.name, r.phone_number
        HAVING number_of_referrals > 0
        ORDER BY number_of_referrals DESC, total_amount_paid DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':offset' => $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get weekly leaderboard data with offset
 * @param int $offset Number of weeks to go back (0 = current week)
 * @return array Array of leaders data
 */
function getWeeklyLeaders($offset = 0) {
    global $pdo;

    $sql = "
        SELECT r.id,
               r.name,
               r.phone_number AS payout_number,
               GROUP_CONCAT(DISTINCT ref.referred_user_name) AS companies,
               COUNT(ref.id) AS number_of_referrals,
               SUM(ref.amount_paid) AS total_amount_paid,
               COUNT(ref.id) * 140 AS total_bonuses
        FROM referrers r
        LEFT JOIN referrals ref
               ON r.id = ref.referrer_id
               AND YEARWEEK(ref.referral_date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL :offset WEEK), 1)
        GROUP BY r.id, r.name, r.phone_number
        HAVING number_of_referrals > 0
        ORDER BY number_of_referrals DESC, total_amount_paid DESC
        LIMIT 10
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':offset' => $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch weekly referrals with offset
 * @param int $offset Number of weeks to go back (0 = current week)
 * @return array Array of referral data
 */
function getWeeklyReferrals($offset = 0) {
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
 * Fetch monthly referrals with offset
 * @param int $offset Number of months to go back (0 = current month)
 * @return array Array of referral data
 */
function getMonthlyReferrals($offset = 0) {
    global $pdo;
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

/**
 * Send WhatsApp message using the WhatsApp API
 * @param string $phoneNumber The phone number to send the message to
 * @param string $message The message content
 * @return bool True if successful, false otherwise
 */
function sendWhatsAppMessage($phoneNumber, $message) {
    // Format the phone number (remove any non-numeric characters)
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // Secret key for the WhatsApp API
    $secret = '9ce41efa34caca533c86cedb62f1f4b5';
    
    // Encode the message for URL 
    $encodedMessage = urlencode($message);
    
    // Construct the API URL
    $apiUrl = "https://whatsapp.ispledger.com/api/sendWA?to={$phoneNumber}&msg={$encodedMessage}&secret={$secret}";
    
    // Send the request
    $response = file_get_contents($apiUrl);
    
    // Log the request and response
    error_log("WhatsApp API Request: {$apiUrl}");
    error_log("WhatsApp API Response: {$response}");
    
    // Check if request was successful
    return ($response !== false);
}

/**
 * Add a new referral to the system
 * @param int $referrerId The ID of the referrer
 * @param string $referredUserName Name of the referred user/company
 * @return bool True if successful
 */
function addReferral($referrerId, $referredUserName) {
    global $pdo;

    // Each referral costs $700, so the amount_paid is 700.
    $amountPaid = 700.00;
    $date = date('Y-m-d'); // Current date

    // 1) Insert into referrals table
    $sqlInsert = "INSERT INTO referrals (referrer_id, referred_user_name, amount_paid, referral_date)
                  VALUES (:referrer_id, :referred_user_name, :amount_paid, :referral_date)";
    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute([
        ':referrer_id'      => $referrerId,
        ':referred_user_name' => $referredUserName,
        ':amount_paid'      => $amountPaid,
        ':referral_date'    => $date
    ]);

    // 2) Update referrer's totals
    //    Bonus is 20% of 700 = 140
    $bonus = 140.00;

    // Update total_referrals, total_amount_paid, total_bonuses in referrers
    $sqlUpdate = "UPDATE referrers
                  SET total_referrals = total_referrals + 1,
                      total_amount_paid = total_amount_paid + :amount_paid,
                      total_bonuses = total_bonuses + :bonus
                  WHERE id = :referrer_id";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute([
        ':amount_paid'  => $amountPaid,
        ':bonus'        => $bonus,
        ':referrer_id'  => $referrerId
    ]);
    
    // 3) Get referrer information for notification
    $sqlGetReferrer = "SELECT * FROM referrers WHERE id = :referrer_id";
    $stmt = $pdo->prepare($sqlGetReferrer);
    $stmt->execute([':referrer_id' => $referrerId]);
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 4) Send Telegram notification
    $telegramBotToken = '8185874928:AAEaroq3xdbngFVJHxcwLStAlFA6Pm620Iw'; 
    $telegramChatId = '-1002019374578'; // Chat ID
    $telegramTopicId = 1053; // Topic ID for forum threads
    
    $telegramMessage = formatReferralMessage($referrer, $referredUserName);
    sendTelegramMessage($telegramBotToken, $telegramChatId, $telegramMessage, $telegramTopicId);
    
    // 5) Send WhatsApp notification
    if (isset($referrer['phone_number']) && !empty($referrer['phone_number'])) {
        $whatsAppMessage = "Hello {$referrer['name']}, your referral {$referredUserName} has been successfully signed up! Check your position on the leaderboard at referrals.ispledger.com and follow our Telegram channel at t.me/freeispradius for payout status.";
        sendWhatsAppMessage($referrer['phone_number'], $whatsAppMessage);
    }
    
    return true;
}
