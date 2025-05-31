<?php

class MainData{

    public $con;

    public function __construct($con) {
        $this->con = $con;
    }
    
    
    function getHomeData($userId){

        $response = array();
        $accountBlocked = array();

        $singleUser = getDataFromTable("users", "*", "id = $userId", true, function($dataRow){

            if($dataRow['profile_pic'] != ""){
                $dataRow['profile_pic'] = currentDirPath().$dataRow['profile_pic'];
            }

            return $dataRow;

        });

        if($singleUser['blocked'] == 1){
            $accountBlocked['blocked'] = 1;
            $accountBlocked['msg'] = "Your account has been blocked";
            $response['account_blocked'] = $accountBlocked;
        }

        $response['single_user'] = $singleUser;
        $response['games_usernames'] = getDataFromTable("games_usernames", "*", "user_id = $userId");

        $innerJoin = generateInnerJoin("tournament_joinings", "tournament_id", "user_id = '$userId'");
        // $response['room_ids'] = getDataFromTable("tournaments", "room_id, message, id", "(status = 'available' OR status = 'live') AND room_id != ''", false, null, null, null, null, null, $innerJoin, "id = tournament_id");
        $response['room_ids'] = getDataFromTable(
            "tournaments t INNER JOIN tournament_joinings tj ON t.id = tj.tournament_id",
            "t.room_id, t.message, t.id",
            "tj.user_id = '$userId' AND (t.status = 'available' OR t.status = 'live') AND t.room_id != ''",
            false
        );
        //   $response['room_ids'] = getDataFromTable("tournaments","room_id, message, id","(status = 'available' OR status = 'live') AND room_id != ''",false,null,null,null,null,null,$innerJoin,"id = tournament_id");
        // $response['room_ids'] = getDataFromTable("tournaments", "room_id, message, id", "(status = 'available' OR status = 'live') AND room_id != ''", false, null, null, null, null, null, null, "id = tournament_id");


        updateDataIntoTable("users", array("login_date" => date("Y-m-d H:i:s")), "id = '$userId'");
            
        if($singleUser['got_first_reward'] == 0){

            $registrationBonus = getDataFromTable("main_data", "registration_bonus", "id = '1'", true);
            $response['single_user']['bonus_amount'] = ($registrationBonus + $singleUser['bonus_amount']);
            
            updateDataIntoTable("users", array("got_first_reward" => 1, "bonus_amount" => "bonus_amount + $registrationBonus"), "id = '$userId'");

            $title = "Registration Bonus";
            $message = "You have got your first registration bonus of ".$registrationBonus;

            // make transaction
            insertDataIntoTable("transactions", array("user_id" => $userId,"title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $registrationBonus, "reciept_no" => ""));

            // send notification
            $notifications = new Notifications($this->con);
            $notifications->pushNotificationToSingle($userId, $title, $message, "",  "activity", "splash", "");
        }

        return $response;
    }
}
?>