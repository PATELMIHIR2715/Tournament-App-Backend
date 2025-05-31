<?php session_start();
include_once("../../dbcon.php");
include_once("../../data-functions.php");
include_once("../../global-functions.php");
require_once ("lib/config_paytm.php");
require_once ("lib/encdec_paytm.php");

$getUserId = $_GET['user_id'];
$getAmount = $_GET['amount'];

$_SESSION['user_id'] = $getUserId;
$_SESSION['amount'] = $getAmount;

$userDetails = getDataFromTable("users", "fullname, email, mobile", "id = '$getUserId'");

$name = $userDetails['fullname'];
$email = $userDetails['email'];

if ($email == '') {
    $email = 'testemail@gmail.com';
}
$contact = $userDetails['mobile'];
if ($contact == '') {
    $contact = '9865489656';
}

$orderId = time();
$custId = "cust123";
$callbackUrl2 = PAYTM_CALLBACK_URL."?user_id=".$getUserId."&amount=".$getAmount."&order_id=".$orderId;
$paytmParams = array();
$paytmParams["ORDER_ID"] = $orderId;
$paytmParams["CUST_ID"] = $custId;
$paytmParams["MOBILE_NO"] = $contact;
$paytmParams["EMAIL"] = $email;
$paytmParams["TXN_AMOUNT"] = $getAmount;
$paytmParams["MID"] = PAYTM_MERCHANT_MID;
$paytmParams["CHANNEL_ID"] = PAYTM_CHANNEL_ID;
$paytmParams["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
$paytmParams["INDUSTRY_TYPE_ID"] = PAYTM_INDUSTRY_TYPE_ID;
$paytmParams["CALLBACK_URL"] = $callbackUrl2;
$paytmChecksum = getChecksumFromArray($paytmParams, PAYTM_MERCHANT_KEY);
$transactionURL = PAYTM_TXN_URL;

?>
<html>
    <head>
        <title>Merchant Checkout Page</title>
    </head>
    <body>
        <center><h1>Please do not refresh this page...</h1></center>
        <form method='post' action='<?php echo $transactionURL; ?>' name='f1'>
            <?php
                foreach($paytmParams as $name => $value) {
                    echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
                }
            ?>
            <input type="hidden" name="CHECKSUMHASH" value="<?php echo $paytmChecksum ?>">
            <input style = "display:none;" type="submit" value="submit" />
        </form>
        < <script type="text/javascript">
            document.f1.submit();
        </script>
    </body>
</html>




