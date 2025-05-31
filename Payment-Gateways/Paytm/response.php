<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");

// following files need to be included
require_once("lib/config_paytm.php");
require_once("lib/encdec_paytm.php");

$paytmChecksum = "";
$paramList = array();
$isValidChecksum = "FALSE";

$paramList = $_POST;
$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application’s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.


if($isValidChecksum == "TRUE") {
	if ($_POST["STATUS"] == "TXN_SUCCESS") {
	    echo "<b>Transaction status is success</b>" . "<br/>";
	    
	    include_once("../../dbcon.php");
        include_once("../../transactions.php");
        include_once("../../notifications.php");
        include_once("../../notification.php");
        include_once("../../data-functions.php");
    
        $getUserId = $_GET['user_id'];
        $getAmount = $_GET['amount'];
        $orderId = $_GET['order_id'];
        $currentDate = date('Y-m-d');
        
        // create transactions object
        $transactions = new Transactions($con);
        $notifications = new Notifications($con);
        
        // create transactions object
        $notifications = new Notifications($con);
                
        updateDataIntoTable("users", array("deposit_amount" => "deposit_amount + $getAmount"), "id = '$getUserId'");
                
        $title = "Deposit Amount";
        $body = "You have successfully deposit into your account";
                
        // make transaction
        insertDataIntoTable("transactions", array("user_id" => $getUserId,"title" => $title, "message" => $body, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $getAmount, "reciept_no" => $orderId));
                        
        $notifications->pushNotificationToSingle($getUserId, $title, $body, "", "activity", "splash_screen", "");
      
        header("Location: payment_status.php?type=payment_success");
        exit;
	}
	else {
		echo "<b>Transaction status is failure</b>" . "<br/>";
		header("Location: payment_status.php?type=failed");
        exit;
	}

}
else {
	echo "<b>Checksum mismatched.</b>";
	header("Location: payment_status.php?type=failed");
    exit;
}

?>