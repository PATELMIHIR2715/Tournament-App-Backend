<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../../dbcon.php");

$logFile = __DIR__ . '/db_test.log';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("Starting database test");

// Test 1: Check database connection
if ($con) {
    writeLog("Database connection successful");
} else {
    writeLog("Database connection failed: " . mysqli_connect_error());
    die("DB Connection failed");
}

// Test 2: Try a simple select
$query = "SELECT 1";
$result = $con->query($query);
if ($result) {
    writeLog("Basic SELECT query successful");
} else {
    writeLog("Basic SELECT query failed: " . $con->error);
}

// Test 3: Check users table
$query = "SHOW COLUMNS FROM users";
$result = $con->query($query);
if ($result) {
    $columns = array();
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'] . " (" . $row['Type'] . ")";
    }
    writeLog("Users table structure: " . print_r($columns, true));
} else {
    writeLog("Failed to get users table structure: " . $con->error);
}

// Test 4: Check transactions table
$query = "SHOW COLUMNS FROM transactions";
$result = $con->query($query);
if ($result) {
    $columns = array();
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'] . " (" . $row['Type'] . ")";
    }
    writeLog("Transactions table structure: " . print_r($columns, true));
} else {
    writeLog("Failed to get transactions table structure: " . $con->error);
}

// Test 5: Try to update a test value
$testUserId = 1;
$testAmount = 0;

// First get current balance
$query = "SELECT deposit_amount FROM users WHERE id = '$testUserId'";
$result = $con->query($query);
if ($result && $row = $result->fetch_assoc()) {
    $currentBalance = $row['deposit_amount'];
    writeLog("Current balance for user $testUserId: $currentBalance");
    
    // Try to update with same value (no actual change)
    $query = "UPDATE users SET deposit_amount = '$currentBalance' WHERE id = '$testUserId'";
    if ($con->query($query)) {
        writeLog("Test update successful");
    } else {
        writeLog("Test update failed: " . $con->error);
    }
} else {
    writeLog("Failed to get current balance: " . $con->error);
}

echo "Tests completed. Check db_test.log for results.";
?>
