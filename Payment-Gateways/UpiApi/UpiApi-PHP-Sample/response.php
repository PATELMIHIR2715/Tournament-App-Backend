<?php
$secret = ""; // Your Api Secret https://upiapi.in/APIKeys
if(isset($_POST['status'])){	
$decrypt = openssl_decrypt($_POST['hash'],"AES-128-ECB",$secret);
$response = json_decode($decrypt,true);
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment Gateway - Test Response</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container p-5">
    
<div class="row">

<div class="col-md-8 mb-2">

<h2>Response</h2>
  <p>Payment Gateway - Test Response</p>            
<table class="table table-bordered">
<tr>
<th>Key</th>
<th>Value</th>
</tr>
<?php 
foreach($response as $key => $value){
?>	
<tr>
<td><?=$key?></td>
<td><?=$value?></td>
</tr>
<?php
}
?> 
</table>

</div> 

    
<div class="col-md-4 mb-2 card p-4">    
<img class="img-fluid mt-2" src="https://rechpay.com/images/step1-illus.svg">
</div>    
    

</div>

</div>    
</body>
</html>
<?php
}
?>