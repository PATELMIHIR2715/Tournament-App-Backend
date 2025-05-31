<?php
$getStatus = $_GET['type'];

if($getStatus == 'payment_success'){
    echo "Success. Please wait";
}
else{
   echo "Failed. Please wait";
}
?>