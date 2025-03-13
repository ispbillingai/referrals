
<?php
// referral_functions.php

require_once 'config.php';
require_once __DIR__ . '/functions/telegram_utils.php';

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
}
