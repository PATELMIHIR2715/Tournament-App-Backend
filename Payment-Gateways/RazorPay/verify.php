<?php session_start();

// change these values
//$key = "rzp_test_wMyothloGDjsph";
//$secret = "idNaVY3b7caDsVGch2kOxRG6";

include_once("../../dbcon.php");
include_once("../../global-functions.php");
include_once("../../data-functions.php");

// change these values
//$key = "rzp_test_wMyothloGDjsph";
//$secret = "idNaVY3b7caDsVGch2kOxRG6";

$gatewayDetails = getDataFromTable("payment_gateways", "*", "id = '1'", true);

$key = $gatewayDetails['key_value'];
$secret = $gatewayDetails['salt_value'];

date_default_timezone_set( 'Asia/Kolkata' );
 
require('Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($key, $secret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
    
    $getType = $_SESSION['type'];
    
    if($getType == 'pending'){
            
        $_SESSION['type'] = "success";
        
        include_once("../../dbcon.php");
        include_once("../../transactions.php");
        include_once("../../notifications.php");
        include_once("../../notification.php");
    
        $getUserId = $_SESSION['user_id'];
        $getAmount = $_SESSION['amount'];
        $currentDate = date('Y-m-d');
        
        // create transactions object
        $notifications = new Notifications($con);
        
        updateDataIntoTable("users", array("deposit_amount" => "deposit_amount + $getAmount"), "id = '$getUserId'");
        
        $title = "Deposit Amount";
        $body = "You have successfully deposit into your account";
        
        // make transaction
        insertDataIntoTable("transactions", array("user_id" => $getUserId,"title" => $title, "message" => $body, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $getAmount, "reciept_no" => $_POST['razorpay_payment_id']));
                
        $notifications->pushNotificationToSingle($getUserId, $title, $body, "", "activity", "splash_screen", "");
        header("Location: payment_status.php?type=payment_success");
        exit;
    }
}
else
{
    header("Location: payment_status.php?type=payment_failed");
    exit;
}