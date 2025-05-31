<?php
$getStatus = $_GET['type'];

if($getStatus == 'payment_success'){
    echo "Success. Please wait";
}
else{
    $html = "<p>Your payment failed</p><p>{$error}</p>";
    echo $html;
}
?>