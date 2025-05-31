<?php
$getStatus = $_GET['type'];

if($getStatus == 'payment_success'){
    echo "Success. Please wait";
    echo $_GET['result'];
}
else if($getStatus == 'verification_failed'){
    echo "verification_failed. Please wait";
}

else if($getStatus == 'invalid_response'){
    echo "invalid_response. Please wait";
}

else if($getStatus == 'hash_mismatch'){
    echo "hash_mismatch. Please wait";
}
else if($getStatus == 'db_error'){
    echo "db_error. Please wait";
}
else if($getStatus == 'mihir'){
    echo "mihir. Please wait";
}


else{
    $html = "<p>Your payment failed</p><p>{$error}</p>";
    echo $html;
}
?>