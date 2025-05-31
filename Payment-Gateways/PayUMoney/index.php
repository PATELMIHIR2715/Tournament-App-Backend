<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../../dbcon.php");
include_once("../../data-functions.php");
include_once("../../global-functions.php");

// Debug log file
$logFile = __DIR__ . '/payment_debug.log';
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("=== Starting Payment Process ===");

// Create payment_sessions table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS payment_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    txnid VARCHAR(50),
    user_id INT,
    amount DECIMAL(10,2),
    salt VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($query);

$gatewayDetails = getDataFromTable("payment_gateways", "*", "id = '4'", true);

$key = $gatewayDetails['key_value'];
$salt = $gatewayDetails['salt_value'];

$action = 'https://test.payu.in/_payment';

$userId = $_GET['user_id'];
$amount = $_GET['amount'];

writeLog("Received request - UserID: $userId, Amount: $amount");

$amount = number_format((float)$amount, 2, '.', ''); // Format amount to 2 decimal places

// Direct database query to check user details
$query = "SELECT fullname, email, mobile FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

writeLog("User details query result: " . print_r($userDetails, true));

$firstname = isset($userDetails['fullname']) ? trim($userDetails['fullname']) : '';
if(empty($firstname)){
    writeLog("Error: User's name is empty for user ID: $userId");
    die("Error: User's name is required for payment. Please update your profile first.");
}

$email = isset($userDetails['email']) ? trim($userDetails['email']) : '';
if(empty($email)){
    writeLog("Error: User's email is empty for user ID: $userId");
    die("Error: User's email is required for payment. Please update your profile first.");
}

$contact = isset($userDetails['mobile']) ? trim($userDetails['mobile']) : '';
if(empty($contact)){
    writeLog("Error: User's phone is empty for user ID: $userId");
    die("Error: User's phone number is required for payment. Please update your profile first.");
}

writeLog("User details validated - Name: $firstname, Email: $email, Phone: $contact");

$transactionId = "Txn" . rand(10000,99999999);
$tournament = "Tournament Join";
$udf5 = "PayUBiz_PHP7_Kit";

// Store payment data in database
$query = "INSERT INTO payment_sessions (txnid, user_id, amount, salt) VALUES (?, ?, ?, ?)";
$stmt = $con->prepare($query);
$stmt->bind_param("sids", $transactionId, $userId, $amount, $salt);
$stmt->execute();

$hash = hash('sha512', $key.'|'.$transactionId.'|'.$amount.'|'.$tournament.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||'.$salt);

// Use absolute URLs for callbacks
$responseUrl = 'https://clash27.com/back/Payment-Gateways/PayUMoney/response.php';

$html = '<form action="'.$action.'" id="payment_form_submit" method="post">
            <input type="hidden" id="udf5" name="udf5" value="'.$udf5.'" />
            <input type="hidden" id="surl" name="surl" value="'.$responseUrl.'" />
            <input type="hidden" id="furl" name="furl" value="'.$responseUrl.'" />
            <input type="hidden" id="curl" name="curl" value="'.$responseUrl.'" />
            <input type="hidden" id="key" name="key" value="'.$key.'" />
            <input type="hidden" id="txnid" name="txnid" value="'.$transactionId.'" />
            <input type="hidden" id="amount" name="amount" value="'.$amount.'" />
            <input type="hidden" id="productinfo" name="productinfo" value="'.$tournament.'" />
            <input type="hidden" id="firstname" name="firstname" value="'.$firstname.'" />
            <input type="hidden" id="email" name="email" value="'.$email.'" />
            <input type="hidden" id="phone" name="phone" value="'.$contact.'" />
            <input type="hidden" id="hash" name="hash" value="'.$hash.'" />
        </form>
        <script>document.getElementById("payment_form_submit").submit();</script>';
        
echo $html;
?>