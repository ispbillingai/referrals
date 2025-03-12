
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
    
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    // Debug post data
    error_log("POST data: " . json_encode($postData));
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($postData)
        ]
    ];
    
    $context = stream_context_create($options);
    
    // Try to send the message and capture any response
    $result = @file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        $error = error_get_last();
        error_log("Error sending Telegram message: " . ($error ? $error['message'] : 'Unknown error'));
        
        // Additional debugging - try to get response headers
        $http_response_header_debug = isset($http_response_header) ? implode("\n", $http_response_header) : 'No response headers';
        error_log("Response headers: " . $http_response_header_debug);
        
        return false;
    }
    
    // Log the actual response
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
    $message .= "Referrer <b>{$referrerData['name']}</b> has referred <b>{$referredUserName}</b>.\n\n";
    $message .= "Check your standing on the leaderboard: <a href='https://referrals.ispledger.com'>referrals.ispledger.com</a>";
    
    return $message;
}

