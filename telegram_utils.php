
<?php
/**
 * telegram_utils.php
 * 
 * Utility functions for sending notifications to Telegram
 */

/**
 * Sends a message to a Telegram chat using the Telegram Bot API
 * 
 * @param string $botToken The Telegram bot token
 * @param string $chatId The chat ID to send the message to
 * @param string $message The message to send
 * @return bool True if successful, false otherwise
 */
function sendTelegramMessage($botToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($postData)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        error_log("Error sending Telegram message: " . error_get_last()['message']);
        return false;
    }
    
    $response = json_decode($result, true);
    return isset($response['ok']) && $response['ok'] === true;
}

/**
 * Formats a referral notification message for Telegram
 * 
 * @param array $referrerData The referrer data
 * @param string $referredUserName The name of the referred user
 * @return string The formatted message
 */
function formatReferralMessage($referrerData, $referredUserName) {
    $message = "<b>📣 New Referral Update!</b>\n\n";
    $message .= "Referrer <b>{$referrerData['name']}</b> has referred <b>{$referredUserName}</b>.\n\n";
    $message .= "Check your standing on the leaderboard: <a href='https://referrals.ispledger.com'>referrals.ispledger.com</a>";
    
    return $message;
}
