<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // include files
    include_once("dbcon.php");
    include_once("global-functions.php");
    include_once("in-app-actions.php");
    include_once("data-functions.php");
    include_once("users.php");
    include_once("games.php");
    include_once("main-data.php");
    include_once("notification.php");
    include_once("notifications.php");

    $con->set_charset("utf8mb4");

    // set time zone for india
    date_default_timezone_set('Asia/Kolkata');

    $data = $_POST['d'];

    // decrypt data and get into an array
    $decryptedData = json_decode(decrypt($encryptionKey, $data), true);

    // pass all decrypted keys and values in $_POST array so it could be used further
    foreach ($decryptedData as $key => $value) {
        $_POST[$key] = $value;
    }

    // getting user ip
    $getUserIp = getUserIp();

    // getting Post Data to check update, validate user reeuest, etc.
    $appVersion = $con->real_escape_string($_POST['version']);
    $userId = $_POST['user_id'];
    $getSha = trim($_POST['sha']);
    $from = $con->real_escape_string($_POST['from']);

    // Validate User Request 
    //validateUserRequest($userId, $getSha);
    
    if($from == 'main_data'){
        echoData(getDataFromTable("main_data", "*", "id = '1'", true));
    }
    
    else if($from == 'login_user'){
        
        $fullName = $con->real_escape_string($_POST['fullname']);
        $email = $con->real_escape_string($_POST['email']);
        $mobile = $con->real_escape_string($_POST['mobile']);
        $password = $con->real_escape_string($_POST['password']);
        $profilePic = $con->real_escape_string($_POST['profile_pic']);
        
        $response = array();

        // creating object
        $users = new Users($con);
        
        // if mobile is empty it means user is not logging in using mobile or password. He is logging in vai google account
        if($mobile != ''){
            
            // login user via mobile and password
            $response = $users->loginUser($mobile, $password);
            
        } else if($email != ''){
            
            // check if email already exists
            $response = $users->checkEmail($email);
            
            if($response['status'] == 0){
                $response = $users->registerUser($fullName, $email, $mobile, $password, $profilePic);
            }
        }
        
        echoData($response);
    }
    
    else if($from == 'send_otp'){
        
        $mobile = $con->real_escape_string($_POST['mobile']);
        $email = $con->real_escape_string($_POST['email']);
        
        // creating object
        $users = new Users($con);

        // checking mobile status
        $mobileStatus = $users->checkMobile($mobile);

        if($mobileStatus['status']  == 1){
            $mobileStatus['status'] = 0;
            $mobileStatus['msg'] = "Mobile already exists";

            echoData($mobileStatus);
        }
        else if($email != ''){

            // checking email status
            $emailStatus = $users->checkEmail($email);

            if($emailStatus['status']  == 1){
                $emailStatus['status'] = 0;
                $emailStatus['msg'] = "Email already exists";

                echoData($emailStatus);
            }
            else{
                echoData($users->sendOTP($mobile));
            }
        }
        else{
            echoData($users->sendOTP($mobile));
        }
    }
    
    else if($from == 'resend_otp'){
        
        $mobile = $con->real_escape_string($_POST['mobile']);
        
        // creating object
        $users = new Users($con);
        
        echoData($users->sendOTP($mobile));
    }
    
    else if($from == 'verify_otp'){
        
        $otp = $con->real_escape_string($_POST['otp']);
        $mobile = $con->real_escape_string($_POST['mobile']);
        $fullName = $con->real_escape_string($_POST['fullname']);
        $email = $con->real_escape_string($_POST['email']);
        
        $profilePic = "";
        if(isset($_POST['profile_pic'])){
            $profilePic = $_POST['profile_pic'];
        }
        
        $password = $con->real_escape_string($_POST['password']);
  
        // creating object
        $users = new Users($con);
        
        $response = $users->verifyOTP($otp, $mobile);
        
        if($response['status'] == 1){
            
            //vars = $fullName, $email, $mobile, $password, $profilePic
            $response = $users->registerUser($fullName, $email, $mobile, $password, $profilePic);
        }
        
        echoData($response);
    }
    
    else if($from == 'home_data'){
        
        $userId = $con->real_escape_string($_POST['user_id']);

        $mainData = new MainData($con);

        echoData($mainData->getHomeData($userId));
    }
    
    else if($from == 'single_user'){
        
        $userId = $con->real_escape_string($_POST['user_id']);
        
        echoData(getDataFromTable("users", "*", "id = $userId", true));
    }
    
    else if($from== 'update_fcm'){
        
        $userId = $con->real_escape_string($_POST['user_id']);
        $token = $con->real_escape_string($_POST['token']);
        
        updateDataIntoTable("users", array("firebase_key" => $token), "id = '$userId'");
    }
    
    else if($from == 'get_games'){
        
        $games = new Games($con);
        $games->checkTournamentTimings();
        
        $games = getDataFromTable("games", "*", "1", false, function($dataRow){
            
            $gameId = $dataRow['id'];
            
            $tournaments = getDataFromTable("tournaments", "id", "game_id = '$gameId' AND status = 'available'");
            
            $dataRow['image'] = currentDirPath().$dataRow['image'];
            $dataRow['tournaments'] = sizeof($tournaments);
            
            return $dataRow;
        });
        
        echoData($games);
    }
    
    else if($from== 'get_tournaments'){
        
        $gameId = $con->real_escape_string($_POST['game_id']);
        $type = $con->real_escape_string($_POST['type']);
        
        $games = new Games($con);
        
        echoData($games->getTournaments($gameId, $type));
    }
    
    else if($from== 'get_joined_players'){
        
        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        
        $joinedPlayers = getDataFromTable("tournament_joinings", "user_id", "tournament_id = '$tournamentId'");
        
        echoData($joinedPlayers);
    }
    
    else if($from== 'join_tournament'){
        
        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $gameUsername = $con->real_escape_string($_POST['game_username']);
        $userId = $con->real_escape_string($_POST['user_id']);
        
        $games = new Games($con);
        $joinTournament = $games->joinTournament($tournamentId, $gameUsername, $userId);
        
        echoData($joinTournament);
    }
    
    else if($from== 'get_transactions'){
        
        $userId = $con->real_escape_string($_POST['user_id']);
        
        // $userTransactions = getDataFromTable("transactions", "*", "user_id = '$userId'", false, null, "DESC", "id");
        $userTransactions = getDataFromTable(
        "transactions",        // $table
        "*",                  // $selectColumns
        "user_id = '$userId'",// $whereClause
        false,                // $singleResult
        null,                 // $callback
        "id",                 // $orderBy
        "DESC",               // $orderDirection
        null,                 // $offset
        null,                 // $limit
        null,                 // $groupBy
        null                  // $having
    );        
        echoData($userTransactions);
    }
    
    else if($from == 'update_profile'){
        
        $userId = $con->real_escape_string($_POST['user_id']);
        $fullname = $con->real_escape_string($_POST['fullname']);
        $profilePic = $_POST['profile_pic'];
        
        $profilePicUrl = getDataFromTable("users", "profile_pic", "id = '$userId'", true);
            
        if($profilePic != ''){
            
            if (!file_exists("ProfilePics")) {
                mkdir("ProfilePics", 0755, true);
            }
            
            $profilePicUrl = "ProfilePics/".time().".png";
            file_put_contents($profilePicUrl, base64_decode($profilePic));
        }
        
        $updateUser = updateDataIntoTable("users", array("fullname" => $fullname, "profile_pic" => $profilePicUrl), "id = '$userId'");
        if($updateUser['status'] == 1){
            $updateUser['profile_pic'] = currentDirPath().$profilePicUrl;
        }
        
        echoData($updateUser);
    }
    
    else if($from == 'withdraw_request'){
        
        $userId = $con->real_escape_string($_POST['user_id']);
        $amount = $con->real_escape_string($_POST['amount']);
        $type = $con->real_escape_string($_POST['type']);
        $accountNumber = $con->real_escape_string($_POST['account_number']);
        $fullname = $con->real_escape_string($_POST['fullname']);
        $bankName = $con->real_escape_string($_POST['bank_name']);
        $ifscCode = $con->real_escape_string($_POST['ifsc_code']);
        $mobile = $con->real_escape_string($_POST['mobile']);
        
        $users = new Users($con);
        
        $makeWithdraw = $users->makeWithdrawRequest($userId, $amount, $type, $accountNumber, $fullname, $bankName, $ifscCode, $mobile);
        
        echoData($makeWithdraw);
    }
    
    else if($from == 'tournament_results'){
        
        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        
        $userDetails = function($dataRow){
            
            $userDetails = getDataFromtable("users", "profile_pic, fullname", "id = '".$dataRow['user_id']."'", true);
            
            if($userDetails['profile_pic'] != ''){
                $dataRow['profile_pic'] = currentDirPath().$userDetails['profile_pic'];
            }
            else{
                $dataRow['profile_pic'] = "";
            }
            
            $dataRow['fullname'] = $userDetails['fullname'];
            
            return $dataRow;
        };
        
        $tournamentResults = getDataFromTable("tournament_joinings", "*", "tournament_id = '$tournamentId' AND won_amount > 0", false,$userDetails, "won_amount","DESC");
        
        echoData($tournamentResults);
        
    }
    
    else if($from == 'upload_screenshot'){
        
        $screenshot = $_POST['screenshot'];
        $userId = $con->real_escape_string($_POST['user_id']);
        
        $screenshotUrl = "";
        
        if($screenshot != ''){
            
            $filePath = "PaymentSS";
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            
            $screenshotUrl = $filePath."/".time().".png";
            file_put_contents($screenshotUrl, base64_decode($screenshot));
        }
        
        $status = insertDataIntoTable("payment_ss", array("user_id" => $userId, "ss_file" => $screenshotUrl, "date" => date("Y-m-d"), "time" => date("H:i:s"), "status" => "pending"));
        
        if($status['status'] == 1){
            $status['title'] = "Success";
            $status['msg'] = "Thank you for sending the screenshot of your payment. We will verify the information and make sure to update your wallet with the received amount in a timely manner. Should you have any further questions, please let us know.";
        }
        
        echoData($status);
    }
    
    $con->close();
}

if(isset($_GET['from'])){
    
    // include files
    include_once("dbcon.php");
    include_once("global-functions.php");
    include_once("data-functions.php");
    
    $from = $_GET['from'];
    
    if($from == 'add_payment'){
        
        $userId = $_GET['user_id'];
        $amount = $_GET['amount'];
        
        $selectedPG_Id = getDataFromTable("main_data", "gateway_id", "id = '1'", true);
        $gatewayPath = getDataFromTable("payment_gateways", "path", "id = '$selectedPG_Id'", true);
        
        header("Location: $gatewayPath"."?user_id=".$userId."&amount=".$amount);
    }
    
    else if($from == 'refer_friend'){
        
        $referralCode = $_GET['referral_id'];
    
        // check if referral code belongs to any user
        $userId = getDataFromTable("users", "id", "referral_code = '$referralCode'", true);
        if($userId != null){
            insertDataIntoTable("refers", array("ip" => getUserIp(), "referral_code" => $referralCode, "date_time" => date("Y-m-d H:i:s"), "status" => 0));
        }
        
        header("Location: ".getDataFromTable("main_data", "app_link", "id = '1'", true));
    }
    
    $con->close();
}
?>