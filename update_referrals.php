
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

// Include your database configuration (ensure this file sets up a PDO instance in $pdo)
require_once 'config.php';

try {
    // 1. Look up the referrer by company_name (using the 'name' column)
    $stmt = $pdo->prepare("SELECT id, name, phone_number FROM referrers WHERE name = :company_name LIMIT 1");
    $stmt->execute([':company_name' => $data['company_name']]);
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$referrer) {
        // No matching referrer found, so create a new referrer record
        try {
            $stmt = $pdo->prepare("
                INSERT INTO referrers (name, phone_number, total_referrals, total_amount_paid, total_bonuses) 
                VALUES (:name, :phone_number, 0, 0, 0)
            ");
            $stmt->execute([
                ':name'         => $data['company_name'],
                ':phone_number' => $data['phone_number']
            ]);
            // Get the new referrer's ID
            $referrerId = $pdo->lastInsertId();
            // Prepare a minimal referrer array for later use
            $referrer = ['id' => $referrerId, 'name' => $data['company_name'], 'phone_number' => $data['phone_number']];
        } catch (PDOException $e) {
            // If the error is a duplicate entry for phone_number, try to get the existing referrer
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'phone_number') !== false) {
                $stmt = $pdo->prepare("SELECT id, name, phone_number FROM referrers WHERE phone_number = :phone_number LIMIT 1");
                $stmt->execute([':phone_number' => $data['phone_number']]);
                $referrer = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$referrer) {
                    throw new Exception("Could not create or find referrer with phone number: " . $data['phone_number']);
                }
            } else {
                // If it's a different error, re-throw it
                throw $e;
            }
        }
    }
    
    $referrerId = $referrer['id'];

    // 2. Insert a new referral record
    $amountPaid   = 700.00;           // Each referral costs 700
    $referralDate = date('Y-m-d');      // Current date
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

    // 4. Return a success JSON response
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
