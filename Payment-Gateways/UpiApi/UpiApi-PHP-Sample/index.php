<!DOCTYPE html>
<html>
<head>
<title>Payment Gateway - Test Demo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container p-5">
    
<div class="row">

<div class="col-md-7 mb-2">
 
<?php
if(isset($_POST['payment'])){
$token = "";	// Your Api Token https://upiapi.in/APIKeys
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://upiapi.in/order/create',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "token": '.$token.',
    "orderId": '.time().',
    "txnAmount": 1,
    "txnNote": "'.$_POST['customerName'].'",
    "customerName": "'.$_POST['customerName'].'",
    "customerEmail": "'.$_POST['customerEmail'].'",
    "customerMobile": "'.$_POST['customerMobile'].'",
    "callbackUrl": "https://yourdomain.com/response.php"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response,true);
if($result['status']==true){
echo '<script>location.href="'.$result['result']['payment_url'].'"</script>';
exit();
}

echo '<div class="alert alert-danger">'.$result['message'].'</div>';
}
?>
<h2>Test Demo</h2>
<span>Fill Payment Detail and Pay</span><hr>
<form action="" method="post">
<h4>Txn Amount:</h4>
<input type="text" name="txnAmount" value="1" class="form-control" placeholder="Enter Txn Amount" readonly><br>
<h4>Customer Name:</h4>
<input type="text" name="customerName" placeholder="Enter Customer Name" class="form-control" required><br>
<h4>Customer Mobile:</h4>
<input type="text" name="customerMobile" placeholder="Enter Customer Mobile"  maxlength="10" class="form-control" required><br>
<h4>Customer Email:</h4>
<input type="email" name="customerEmail" placeholder="Enter Customer Email"  class="form-control" required><br>
<input type="submit" name="payment" value="Payment" class="btn btn-primary">
</form>
</div> 

    
<div class="col-md-4 mb-2 card p-4">    
<img class="img-fluid mt-2" src="https://rechpay.com/images/step1-illus.svg">
</div>    
    

</div>

</div>    
</body>
</html>