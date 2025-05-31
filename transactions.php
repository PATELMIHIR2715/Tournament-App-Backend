<?php
class Transactions{
    
    public $con;

    public function __construct($con) {
        $this->con = $con;
    }
    
    function getUserIp(){
        
        $ip;
        
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }
        
        elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }
        
        else
        {
            $ip = $remote;
        }
        return $ip;
        
    }
    
    function makeTransaction($userId, $title, $message, $amount, $receipt){
        
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        $insertTransaction = "INSERT INTO transactions(user_id, title, message, date, time, amount, reciept_no) VALUES('$userId', '$title', '$message', '$currentDate', '$currentTime', '$amount', '$receipt')";
        $this->con->query($insertTransaction);

    }
    
    function getTransactions($userId){
        
        $response = array();
        
        $selectTransactions = "SELECT * FROM transactions WHERE user_id = '$userId' ORDER BY id DESC";
        $transactionsResults = $this->con->query($selectTransactions);
        
        while($transactionsRows = $transactionsResults->fetch_assoc()){
            $response[] = $transactionsRows;
        }
        
        return json_encode($response);
    }
}
?>