
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
    
    // Add debug logging
    error_log("Sending Telegram message to chat ID: {$chatId}");
    error_log("Message content: " . substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''));
    
    // Ensure chat_id is an integer if it's numeric
    if (is_numeric($chatId) && strpos($chatId, '.') === false) {
        $chatId = intval($chatId);
    }
    
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    // Debug post data
    error_log("POST data: " . json_encode($postData));
    
    // Use cURL instead of file_get_contents for better error handling
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Log curl errors
    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Log the actual response and HTTP code
    error_log("Telegram API HTTP Code: " . $httpCode);
    error_log("Telegram API response: " . $result);
    
    $response = json_decode($result, true);
    
    if (!isset($response['ok']) || $response['ok'] !== true) {
        // Log the error details from Telegram
        error_log("Telegram API error: " . json_encode($response));
        return false;
    }
    
    return true;
}

/**
 * Formats a referral notification message for Telegram
 * 
 * @param array $referrerData The referrer data
 * @param string $referredUserName The name of the referred user
 * @return string The formatted message
 */
function formatReferralMessage($referrerData, $referredUserName) {
    // Debug the referrer data to see what we're working with
    error_log("Referrer data for message: " . json_encode($referrerData));
    
    $message = "<b>📣 New Referral Update!</b>\n\n";
    $message .= "Hello <b>{$referrerData['name']}</b>, your referral <b>{$referredUserName}</b> has been successfully signed up!</b>.\n\n";
    $message .= "Check your standing and payout amount on the leaderboard: <a href='https://referrals.ispledger.com'>referrals.ispledger.com</a>";
    
    return $message;
}
