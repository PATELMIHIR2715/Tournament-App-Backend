<?php session_start();

include_once('Razorpay.php');
include_once("../../dbcon.php");
include_once("../../data-functions.php");
include_once("../../global-functions.php");

// change these values
//$key = "rzp_test_wMyothloGDjsph";
//$secret = "idNaVY3b7caDsVGch2kOxRG6";

$gatewayDetails = getDataFromTable("payment_gateways", "*", "id = '1'", true);

$key = $gatewayDetails['key_value'];
$secret = $gatewayDetails['salt_value'];

use Razorpay\Api\Api;

$userId = $_GET['user_id'];
$amount = $_GET['amount'];
$amount = $amount * 100;

$userDetails = getDataFromTable("users", "fullname, email, mobile", "id = '$userId'", true);
$email = $userDetails['email'];
$fullName = $userDetails['fullname'];

if($email == ''){
    $email = 'testemail@gmail.com';
}

$contact = $userDetails['mobile'];

if($contact == ''){
    $contact = '9865489656';
}

$api = new Api($key, $secret);

$orderData = [
    'receipt'         => 30,
    'amount'          => $amount,
    'currency'        => 'INR',
    'payment_capture' => 1 
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['type'] = "pending";
$_SESSION['amount'] = ($amount / 100);
$_SESSION['user_id'] = $userId;
$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$data = [
    "key"               => $key,
    "amount"            => $amount,
    "name"              => $fullName,
    "description"       => "",
    "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
    "prefill"           => [
    "name"              => $fullName,
    "email"             => $email,
    "contact"           => $contact,
    ],
    "notes"             => [
    "address"           => "",
    "merchant_order_id" => $razorpayOrderId,
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

$json = json_encode($data);

require("checkout/manual.php");

?>