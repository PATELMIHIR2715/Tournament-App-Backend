<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../../dbcon.php");

$logFile = __DIR__ . '/wallet_debug.log';
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("=== Testing Wallet Update ===");

// Get table schema
$query = "SHOW COLUMNS FROM users";
$result = $con->query($query);
if ($result) {
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'] . " (" . $row['Type'] . ")";
    }
    writeLog("Users table structure: " . print_r($columns, true));
} else {
    writeLog("Failed to get table structure: " . $con->error);
}

// Test user ID
$userId = 1;

// Get current balance
$query = "SELECT id, deposit_amount FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

writeLog("Current user data: " . print_r($user, true));

// Test update
try {
    $con->begin_transaction();
    
    // Lock the row
    $query = "SELECT deposit_amount FROM users WHERE id = ? FOR UPDATE";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to lock row: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    writeLog("Locked row data: " . print_r($row, true));
    
    // Test amount
    $amount = 1.00;
    $currentBalance = floatval($row['deposit_amount']);
    $newBalance = $currentBalance + $amount;
    
    writeLog("Update test - Current: $currentBalance, Adding: $amount, New: $newBalance");
    
    // Update balance
    $query = "UPDATE users SET deposit_amount = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("di", $newBalance, $userId);
    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }
    writeLog("Rows affected: " . $stmt->affected_rows);
    
    // Verify update
    $query = "SELECT deposit_amount FROM users WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    writeLog("After update: " . print_r($row, true));
    
    if ($row['deposit_amount'] != $newBalance) {
        throw new Exception("Balance verification failed");
    }
    
    $con->rollback(); // Don't actually update the balance
    writeLog("Test completed successfully (rolled back)");
    
} catch (Exception $e) {
    $con->rollback();
    writeLog("Error: " . $e->getMessage());
}

echo "Test completed. Check wallet_debug.log for results.";
?>
