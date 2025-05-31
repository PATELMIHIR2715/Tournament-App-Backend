<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define log file path
$logFile = __DIR__ . '/payment_errors.log';

// Helper function for logging
function writeToLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeToLog("=== New Payment Response Received ===");

include_once("../../dbcon.php");
if (!$con) {
    writeToLog("Database connection failed: " . mysqli_connect_error());
    header("Location: payment_status.php?type=payment_failed&error=db_connection");
    exit;
}

$postdata = $_POST;
writeToLog("POST Data: " . print_r($postdata, true));

if (!isset($postdata['txnid'])) {
    writeToLog("No payment data received in POST");
    header("Location: payment_status.php?type=payment_failed&error=no_data");
    exit;
}

// Get payment session data
$txnid = $postdata['txnid'];
$query = "SELECT * FROM payment_sessions WHERE txnid = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $txnid);
$stmt->execute();
$result = $stmt->get_result();
$paymentSession = $result->fetch_assoc();

if (!$paymentSession) {
    writeToLog("Payment session not found for txnid: $txnid");
    header("Location: payment_status.php?type=payment_failed&error=invalid_transaction");
    exit;
}

$salt = $paymentSession['salt'];
$userId = $paymentSession['user_id'];
$amount = number_format((float)$paymentSession['amount'], 2, '.', ''); // Format to 2 decimal places

writeToLog("Payment session found - UserID: $userId, Amount: $amount");

if (isset($postdata['key'])) {
    $key = $postdata['key'];
    $payuAmount = number_format((float)$postdata['amount'], 2, '.', ''); // Format to 2 decimal places
    $productInfo = $postdata['productinfo'];
    $firstname = $postdata['firstname'];
    $email = $postdata['email'];
    $udf5 = $postdata['udf5'];    
    $status = $postdata['status'];
    $resphash = $postdata['hash'];
    
    writeToLog("Payment Details - TxnID: $txnid, Amount: $payuAmount, Status: $status");
    
    // Verify amount matches with 2 decimal precision
    if ($payuAmount != $amount) {
        writeToLog("Amount mismatch - Session: $amount, PayU: $payuAmount");
        header("Location: payment_status.php?type=payment_failed&error=amount_mismatch");
        exit;
    }
    
    $keyString = $key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
    $keyArray = explode("|", $keyString);
    $reverseKeyArray = array_reverse($keyArray);
    $reverseKeyString = implode("|", $reverseKeyArray);
    $CalcHashString = strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString));
    
    writeToLog("Hash Verification - Expected: $resphash, Calculated: $CalcHashString");

    if ($status == 'success' && $resphash == $CalcHashString) {
        writeToLog("Payment validation successful - Processing wallet update");
        
        try {
            // Start transaction
            $con->begin_transaction();
            
            // Get current balance with row lock
            $query = "SELECT deposit_amount FROM users WHERE id = ? FOR UPDATE";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to lock user record: " . $stmt->error);
            }
            $result = $stmt->get_result();
            
            if (!$result || !($row = $result->fetch_assoc())) {
                throw new Exception("Failed to get current balance");
            }
            
            // Format numbers with 2 decimal precision
            $currentBalance = number_format((float)$row['deposit_amount'], 2, '.', '');
            $amount = number_format((float)$amount, 2, '.', '');
            $newBalance = number_format((float)$currentBalance + (float)$amount, 2, '.', '');
            
            writeToLog("Balance Update - Current: $currentBalance, Adding: $amount, New: $newBalance");
            
            // Update wallet balance using DECIMAL for precision
            $query = "UPDATE users SET deposit_amount = CAST(? AS DECIMAL(10,2)) WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("di", $newBalance, $userId);
            if (!$stmt->execute() || $stmt->affected_rows != 1) {
                throw new Exception("Failed to update wallet balance: " . $stmt->error);
            }
            
            // Verify the update with proper precision
            $query = "SELECT CAST(deposit_amount AS DECIMAL(10,2)) as deposit_amount FROM users WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            // Compare with 2 decimal precision
            if (number_format((float)$row['deposit_amount'], 2, '.', '') != $newBalance) {
                throw new Exception("Wallet balance verification failed");
            }
            
            writeToLog("Wallet balance verified after update: " . $row['deposit_amount']);
            
            // Add transaction record
            $title = "Deposit Amount";
            $message = "Amount ₹$amount added to wallet";
            $date = date("Y-m-d");
            $time = date("H:i:s");
            
            $query = "INSERT INTO transactions (user_id, title, message, date, time, amount, reciept_no) 
                     VALUES (?, ?, ?, ?, ?, CAST(? AS DECIMAL(10,2)), ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("issssds", $userId, $title, $message, $date, $time, $amount, $txnid);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to record transaction");
            }
            
            // Delete payment session
            $query = "DELETE FROM payment_sessions WHERE txnid = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $txnid);
            $stmt->execute();
            
            // Commit transaction
            $con->commit();
            writeToLog("Wallet update successful - All changes committed");
            
            header("Location: payment_status.php?type=payment_success");
            exit;
            
        } catch (Exception $e) {
            $con->rollback();
            writeToLog("Error processing payment: " . $e->getMessage());
            header("Location: payment_status.php?type=payment_failed&error=" . urlencode($e->getMessage()));
            exit;
        }
    } else {
        writeToLog("Payment validation failed - Status: $status, Hash match: " . ($resphash == $CalcHashString ? "Yes" : "No"));
        header("Location: payment_status.php?type=payment_failed&error=validation");
        exit;
    }
} else {
    writeToLog("Missing key in POST data");
    header("Location: payment_status.php?type=payment_failed&error=missing_key");
    exit;
}
?>