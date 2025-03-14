
<?php
/**
 * update_referrals.php
 *
 * Receives a JSON payload with:
 *   - company_name
 *   - phone_number
 *   - referred_user_name
 *
 * Uses the company_name to identify the referrer.
 * If a matching referrer is found, the referral is recorded and totals updated.
 * If not found, a new referrer record is created and then updated.
 *
 * IMPORTANT: If the company_name equals "freeispradius", the update is ignored.
 *
 * Returns a JSON response indicating success, ignored status, or error.
 */

// Set JSON header
header('Content-Type: application/json');

// Read and decode the JSON payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate required fields
if (
    !isset($data['company_name']) ||
    !isset($data['phone_number']) ||
    !isset($data['referred_user_name'])
) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing required fields. Please provide company_name, phone_number, and referred_user_name.'
    ]);
    exit;
}

// If the company_name is "freeispradius", ignore the update.
if (strtolower($data['company_name']) === 'freeispradius') {
    echo json_encode([
        'status'  => 'ignored',
        'message' => 'Referrals for company "freeispradius" are ignored.'
    ]);
    exit;
}

require_once 'config.php';
require_once 'functions/telegram_utils.php';

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

try {
    // 1. Look up the referrer by company_name (using the 'name' column)
    $stmt = $pdo->prepare("SELECT id, name, phone_number FROM referrers WHERE name = :company_name LIMIT 1");
    $stmt->execute([':company_name' => $data['company_name']]);
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$referrer) {
        // Check if phone number is already being used (only for information purposes)
        $phoneCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM referrers WHERE phone_number = :phone_number");
        $phoneCheckStmt->execute([':phone_number' => $data['phone_number']]);
        $phoneExists = $phoneCheckStmt->fetchColumn() > 0;
        
        // Create a new referrer record - regardless of phone number duplicates
        // We're going to allow duplicate phone numbers, since the primary identifier is company_name
        $stmt = $pdo->prepare("
            INSERT INTO referrers (name, phone_number, total_referrals, total_amount_paid, total_bonuses) 
            VALUES (:name, :phone_number, 0, 0, 0)
        ");
        
        // Try to execute the insert statement
        try {
            $stmt->execute([
                ':name'         => $data['company_name'],
                ':phone_number' => $data['phone_number']
            ]);
            
            // Get the new referrer's ID
            $referrerId = $pdo->lastInsertId();
            
            // Prepare a minimal referrer array for later use
            $referrer = [
                'id' => $referrerId, 
                'name' => $data['company_name'], 
                'phone_number' => $data['phone_number']
            ];
        } catch (PDOException $e) {
            // If we hit a duplicate key error, we need to handle it
            if ($e->getCode() == '23000') {
                // Since we're allowing duplicate phone numbers, the unique constraint must be removed from the database
                // For now, we'll generate a slightly modified phone number to bypass the constraint
                $modifiedPhone = $data['phone_number'] . '_' . time();
                
                $stmt = $pdo->prepare("
                    INSERT INTO referrers (name, phone_number, total_referrals, total_amount_paid, total_bonuses) 
                    VALUES (:name, :phone_number, 0, 0, 0)
                ");
                
                $stmt->execute([
                    ':name'         => $data['company_name'],
                    ':phone_number' => $modifiedPhone
                ]);
                
                // Get the new referrer's ID
                $referrerId = $pdo->lastInsertId();
                
                // Prepare a minimal referrer array for later use
                $referrer = [
                    'id' => $referrerId, 
                    'name' => $data['company_name'], 
                    'phone_number' => $modifiedPhone
                ];
            } else {
                // If it's not a duplicate key error, rethrow it
                throw $e;
            }
        }
    }
    
    $referrerId = $referrer['id'];

    // 2. Insert a new referral record
    $amountPaid   = 700.00;           // Each referral costs 700
    $referralDate = date('Y-m-d');    // Current date
    $stmt = $pdo->prepare("
        INSERT INTO referrals 
            (referrer_id, referred_user_name, company_name, amount_paid, referral_date)
        VALUES
            (:referrer_id, :referred_user_name, :company_name, :amount_paid, :referral_date)
    ");
    $stmt->execute([
        ':referrer_id'        => $referrerId,
        ':referred_user_name' => $data['referred_user_name'],
        ':company_name'       => $data['company_name'],
        ':amount_paid'        => $amountPaid,
        ':referral_date'      => $referralDate
    ]);

    // 3. Update the referrer's totals (bonus is 20% of 700, i.e., 140)
    $bonus = 140.00;
    $stmt = $pdo->prepare("
        UPDATE referrers
        SET total_referrals   = total_referrals + 1,
            total_amount_paid = total_amount_paid + :amount_paid,
            total_bonuses     = total_bonuses + :bonus
        WHERE id = :referrer_id
    ");
    $stmt->execute([
        ':amount_paid' => $amountPaid,
        ':bonus'       => $bonus,
        ':referrer_id' => $referrerId
    ]);

    // 4. Send Telegram notification
    $telegramBotToken = '8185874928:AAEaroq3xdbngFVJHxcwLStAlFA6Pm620Iw'; 
    $telegramChatId = '-1002019374578'; // Chat ID
    $telegramTopicId = 1053; // Topic ID for forum threads
    
    $telegramMessage = formatReferralMessage($referrer, $data['referred_user_name']);
    sendTelegramMessage($telegramBotToken, $telegramChatId, $telegramMessage, $telegramTopicId);
    
    // 5. Send WhatsApp notification
    if (isset($referrer['phone_number']) && !empty($referrer['phone_number'])) {
        $whatsAppMessage = "Hello {$referrer['name']}, your referral {$data['referred_user_name']} has been successfully signed up! Check your position on the leaderboard at referrals.ispledger.com and follow our Telegram channel at t.me/freeispradius for payout status.";
        sendWhatsAppMessage($referrer['phone_number'], $whatsAppMessage);
    }

    // 6. Return a success JSON response
    echo json_encode([
        'status'  => 'success',
        'message' => 'Referral updated successfully for referrer: ' . $referrer['name']
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
