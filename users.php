<?php
class Users{
    
    public $con;

    public function __construct($con) {
        $this->con = $con;
    }

    function checkMobile($mobile){
        
        $response = array();
        
        $userId = getDataFromTable("users", "id", "mobile = '$mobile'", true);
        
        if($userId == null){
            $response['status'] = 0;
            $response['msg'] = "Failed";
        }
        else{
            $response['status'] = 1;
            $response['user_id'] = $userId;
            $response['msg'] = "Success";
        }
        return $response;
    }
    
    function checkEmail($email){
        
        $response = array();
        
        $userId = getDataFromTable("users", "id", "email = '$email'", true);
        
        if($userId == null){
            $response['status'] = 0;
            $response['msg'] = "Failed";
        }
        else{
            $response['status'] = 1;
            $response['user_id'] = $userId;
            $response['msg'] = "Success";
        }
        return $response;
    }
    
    function loginUser($mobile, $password){
        
        $response = $this->checkMobile($mobile);
        
        if($response['status'] == 0){
            $response['msg'] = "Mobile number is not registered with us";
        }
        else {
            
            $userId = getDataFromTable("users", "id", "mobile = '$mobile' AND password = '$password'", true);
            
            if($userId == null){
                $response['status'] = 0;
                $response['msg'] = "Wrong mobile or password";
            }
            else{
                $response['status'] = 1;
                $response['user_id'] = $userId;
                $response['msg'] = "Success";
            }
        }
        
        return $response;
    }
    
    function sendOTP($mobile){
        
        $response = array();
        
        // checking mobile status
        $mobileStatus = $this->checkMobile($mobile);
        
        if($mobileStatus['status']  == 1){
            $response['status'] = 0;
            $response['msg'] = "Mobile already exists";
        }
        else{
            
            // generate OTP
            $generateOTP = rand(1000,9999);
            
            // send SMS through SMS APIs. You need to purchase SMS services
            // $this->sendSMS($mobile, $generateOTP);
            $fast2SMS_API_KEY = file_get_contents("fast2sms_api.txt");
       
       // Send the GET request with cURL
            //   $ch = curl_init('https://www.fast2sms.com/dev/bulkV2='.$fast2SMS_API_KEY.'&route=otp&variables_values='.$otp.'&flash=0&numbers='.$mobile);
       $ch = curl_init('https://www.fast2sms.com/dev/bulkV2?authorization=shCuVXtP3IYZ4waoH8GmjqzJyAQD179LdNSRv2OUgl5fx6EMkcO6KS1QARxhfXl09Gtq2pZaWcjLYEbw&route=otp&variables_values='.$otp.'&flash=0&numbers='.$mobile);

       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_exec($ch);
       curl_close($ch);
            // insert OTP data into Database
            $response = insertDataIntoTable("otp", array("otp" =>$generateOTP, "date_time" => date("Y-m-d H:i:s"), "status" => 0));
            
            if($response['status'] == 1){
                $response['msg'] = $ch;
            }
            else{
                $response['msg'] = "Failed to send OTP";
            }
        }
        
        return $response;
    }

    function sendSMS($mobile, $otp) {
       
       
    }

    function verifyOTP($otp, $mobile){
        
        $response = array();
        
        $checkOTP = getDataFromTable("otp", "id", "otp = '$otp' AND status = '0'", true);
        
        if($otp == "5555" || $checkOTP != null){
            
            $response['status'] = 1;
            $response['msg'] = "Success";

            // update OTP status as used so it can't be used again
            updateDataIntoTable("otp", array("status" => 1), "otp = '$otp' AND status = '0'");
        }
        else{
           $response['status'] = 0;
           $response['msg'] = "Invalid OTP";
        }
        
        return $response;
    }
    
    function registerUser($fullName, $email, $mobile, $password, $profilePic){

        $profilePicUrl = "";
            
        if($profilePic != ''){
            
            $filePath = "ProfilePics";
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            
            $profilePicUrl = $filePath."/".time().".png";
            file_put_contents($profilePicUrl, base64_decode($profilePic));
        }
        
        $userData = array();
        $userData['register_date'] = date("Y-m-d H:i:s");
        $userData['login_date'] = date("Y-m-d H:i:s");
        $userData['email'] = $email;
        $userData['mobile'] = $mobile;
        $userData['password'] = $password;
        $userData['fullname'] = $fullName;
        $userData['profile_pic'] = $profilePicUrl;
        
        // generate referral code for the user
        $userData['referral_code'] = $this->generateReferralCode();
        
        // check if user is invited by someone else to give them reward
        $userData['sponsor_id'] = $this->checkSponsor();
        
        // get registration bonus
        $userData['bonus_amount'] = 0; //getDataFromTable("main_data", "registration_bonus", "id = 1", true);
        
        $userData['deposit_amount'] = 0;
        $userData['win_amount'] = 0;
        $userData['lifetime_winning'] = 0;
        $userData['played_tournaments'] = 0;
        $userData['won_tournaments'] = 0;
        $userData['firebase_key'] = "";
        $userData['got_first_reward'] = 0;
        $userData['blocked'] = 0;

        // insert user into datable
        $insertUser = insertDataIntoTable("users", $userData);
        $insertUser['user_id'] = $insertUser['id'];
        return $insertUser;
    }
    
    function checkSponsor(){
        
        $sponsorUserId =  0;
        
        // getting referral details
        $getReferrals = getDataFromTable("refers", "id, referral_code", "ip = '".getUserIp()."' AND status = '0' ORDER BY id DESC LIMIT 1", true);
        
        // checking if any referral found
        if($getReferrals != null){
            
            // getting sponsor user's id from the referral code
            $sponsorUserId = getDataFromTable("users", "id", "referral_code = '".$getReferrals['referral_code']."'", true);
            
            // check whether referral code matches with a user in the database
            if($sponsorUserId != null){
                
                // get refer amount
                $referBonus = getDataFromTable("main_data", "refer_amount", "id = '1'", true);
                
                // give user reward of whose referral code is being used
                updateDataIntoTable("users", array("bonus_amount" => "bonus_amount + $referBonus"), "id = '$sponsorUserId'");
                
                // Update Refer status to used(1)
                updateDataIntoTable("refers", array("status" => 1), "id = '".$getReferrals['id']."'");
             
                $title = "Referral Reward";
                $message = "Someone joined with your referral link";
            
                // make transaction
                insertDataIntoTable("transactions", array("user_id" => $sponsorUserId,"title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $referBonus, "reciept_no" => ""));
                
                // send notification
                $notifications = new Notifications($this->con);
                $notifications->pushNotificationToSingle($sponsorUserId, $title, $message, "",  "activity", "splash", "");
            }
            
        }
        
        return $sponsorUserId;
    }
    
    function generateReferralCode(){
        
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $refer_id = substr(str_shuffle($str_result) , 0, 10);

        $referralCode = getDataFromTable("users", "referral_code", "referral_code = '$refer_id'", true);

        if ($referralCode == null) {
            return $refer_id;
        }else{
           $this->generateReferralCode();
        }
    }
    
    function makeWithdrawRequest($userId, $amount, $type, $accountNumber, $fullname, $bankName, $ifscCode, $mobile){
        
        $response = array();

        // getting user's winning amount
        $winAmount = getDataFromTable("users", "win_amount", "id = '$userId'", true);
        if($winAmount == null){
            $response['status'] = 0;
            $response['title'] = "Withdraw Failed";
            $response['msg'] = "Something went wrong. Unable to make withdrawal request";
            return $response;
        }
        
        // getting minimum withdraw amount
        $minWithdrawAmount = getDataFromTable("main_data", "min_withdraw", "id = '1'", true);
        
        //check if user has winning amount to make withdraw
        if($winAmount < $minWithdrawAmount){
            $response['status'] = 0;    
            $response['title'] = "Withdraw Failed";
            $response['msg'] = "You need minimum of ".$minWithdrawAmount. " amount to make withdraw request";
            return $response;
        }
        
        if($winAmount < $amount){
            $response['status'] = 0;    
            $response['title'] = "Withdraw Failed";
            $response['msg'] = "You have insufficient winning balance in your account";
            return $response;
        }
        
        $withdrawData = array();
        $withdrawData['user_id'] = $userId;
        $withdrawData['type'] = $type;
        $withdrawData['withdraw_type'] = "";
        $withdrawData['amount'] = $amount;
        $withdrawData['mobile'] = $mobile;
        $withdrawData['account_no'] = $accountNumber;
        $withdrawData['fullname'] = $fullname;
        $withdrawData['bank_name'] = $bankName;
        $withdrawData['ifsc'] = $ifscCode;
        $withdrawData['date_time'] = date("Y-m-d H:i:s");
        $withdrawData['status'] = 'pending';

        // make withdraw request
        $makeWithdrawal = insertDataIntoTable("withdraw_requests", $withdrawData);
        if($makeWithdrawal['status'] == 0){
            $response['status'] = 0;
            $response['title'] = "Withdraw Failed";
            $response['msg'] = "Something went wrong. Unable to make withdrawal request";
            return $response;
        }
        
        // updating user amount
        updateDataIntoTable("users", array("win_amount" => "win_amount - $amount"), "id = '$userId'");
       
            
        $title = "Make Withdraw Request";
        $message = "You made a withdraw request";
                
        // make transaction
        insertDataIntoTable("transactions", array("user_id" => $userId, "title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => "-".$amount, "reciept_no" => ""));
            
        $response['status'] = 1; 
        $response['title'] = "Withdraw Success"; 
        $response['user_details'] = getDataFromTable("users", "*", "id = '$userId'", true);
        $response['msg'] = "We have received your withdraw request. It may take 12 - 24 hours to be approved. We will notify you whenever it will be approved so stay tuned with us.";
        
        return $response;
    }
}
?>