<?php

class Tournaments {

    public $con;
 
    public function __construct( $con ) {
        $this->con = $con;
    }

    function addNewTournament($gameId, $title, $image, $map, $type, $mode, $entryFees, $prizePool, $perKill, $fromBonus, $totalPlayers, $details){

        $tournamentImagePath = $this->addTournamentImage($image);
        
        if($tournamentImagePath == ""){
            return array("status" => 0, "msg" => "Unable to add tournament image");
        }
        
        $tournamentData = array();
        $tournamentData['game_id'] = $gameId;
        $tournamentData['name'] = $title;
        $tournamentData['image'] = $tournamentImagePath;
        $tournamentData['map'] = $map;
        $tournamentData['type'] = $type;
        $tournamentData['mode'] = $mode;
        $tournamentData['entry_fees'] = $entryFees;
        $tournamentData['prize_pool'] = $prizePool;
        $tournamentData['per_kill'] = $perKill;
        $tournamentData['from_bonus'] = $fromBonus;
        $tournamentData['total_players'] = $totalPlayers;
        $tournamentData['details'] = json_encode($details);
        $tournamentData['status'] = "available";

        // Inserting tournament
        return insertDataIntoTable("tournaments", $tournamentData);
    }

    function addTournamentImage($image){

        $tournamentImagePath = "";

        // create objects
        $uploadFile = new UploadFile( $this->con );
        $uploadFile->setPostName("t_image");
        
        $filePath = "../../TournamentImages";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }
        
        // Save tournament image to memory
        $uploadStatus = $uploadFile->uploadFile($filePath, $image );

        if ( $uploadStatus['status'] == 1 ) {
            $tournamentImagePath = str_replace('../', '', $uploadStatus['filepath']);
        }

        return $tournamentImagePath;
    }

    function addTournamentPrizes($tournamentId, $prizeCount){

        $logs = "";

        for($i = 1; $i <= $prizeCount; $i++){

            if(isset($_POST['start_rank_'.$i]) && isset($_POST['end_rank_'.$i]) && isset($_POST['amount_'.$i])){

                $tournamentPrizeData = array();

                $tournamentPrizeData['tournament_id'] = $tournamentId;
                $tournamentPrizeData['start_rank'] = $this->con->real_escape_string($_POST['start_rank_'.$i]);
                $tournamentPrizeData['end_rank'] = $this->con->real_escape_string($_POST['end_rank_'.$i]);
                $tournamentPrizeData['amount'] = $this->con->real_escape_string($_POST['amount_'.$i]);

                // Inserting tournament prizes
                $prizeResponse = insertDataIntoTable("tournament_prizes", $tournamentPrizeData);

                if($prizeResponse['status'] == 0){
                    $logs .= "Unable to add tournament prize = ".json_encode($tournamentPrizeData)."\n\n";
                }
            }
        }

        return $logs;
    }

    function addTournamentSchedule($tournamentId, $startDateEndDate){

        $logs = "";

        $tournamentScheduleData = array();
        $tournamentScheduleData['tournament_id'] = $tournamentId;
        $tournamentScheduleData['start_date_time'] = date("Y-m-d H:i:s", strtotime($startDateEndDate[0]));
        $tournamentScheduleData['end_date_time'] = date("Y-m-d H:i:s", strtotime($startDateEndDate[1]));

        // Inserting tournament schedule
        $scheduleResponse = insertDataIntoTable("tournament_schedule", $tournamentScheduleData);

        if($scheduleResponse['status'] == 0){
            $logs .= "Unable to set tournament timings = ".json_encode($tournamentScheduleData)."\n\n";
        }

        return $logs;
    }

    function updateTournament($tournamentId, $gameId, $title, $tournamentImage, $map, $type, $mode, $entryFees, $prizePool, $perKill, $fromBonus, $totalPlayers, $prizeDistributions, $schedule, $details){
        
        $response = array();
        $tournamentSchedule = array();
        
        $tournamentDetails = explode(",",$details);
        
        $startDateEndDate = explode(" - ",$schedule);

        $startDate = date("Y-m-d", strtotime($startDateEndDate[0]));
        $startTime = date("H:i:s", strtotime($startDateEndDate[0]));
         
        $endDate = date("Y-m-d", strtotime($startDateEndDate[1]));
        $endTime = date("H:i:s", strtotime($startDateEndDate[1]));

        $tournamentSchedule['start_date'] = $startDate;
        $tournamentSchedule['start_time'] = $startTime;
        $tournamentSchedule['end_date'] = $endDate;
        $tournamentSchedule['end_time'] = $endTime;
        
        $updateTournament = "UPDATE tournaments SET game_id = '$gameId', name = '$title', image = '$tournamentImage', map = '$map', type = '$type', mode = '$mode', entry_fees = '$entryFees', prize_pool = '$prizePool', per_kill = '$perKill', from_bonus = '$fromBonus', schedule = '".json_encode($tournamentSchedule)."', total_players = '$totalPlayers', details = '".json_encode($tournamentDetails)."' WHERE id = '$tournamentId'";
        
        if($this->con->query($updateTournament)){
            $response['status'] = 1;
            $response['msg'] = "Tournament Updated";
            
            // delete old prizes
            $deletePrizes = "DELETE FROM prize_distribution WHERE tournament_id = '$tournamentId'";
            
            if(sizeof($prizeDistributions) > 0){
                if($this->con->query($deletePrizes)){
                    for($i = 0; $i < sizeof($prizeDistributions); $i++){
                        $insertPrize = "INSERT INTO prize_distribution(tournament_id, rank, amount) VALUES('$tournamentId', '".$prizeDistributions[$i]['rank']."', '".$prizeDistributions[$i]['amount']."')";
                        $this->con->query($insertPrize);
                    }
                }
                else{
                    $response['status'] = 0;
                    $response['msg'] = "Tournament Updated but Pirzes not updates";
                }
            }
            else{
                $response['status'] = 1;
                $response['msg'] = "Tournament Updated";
            }
            
        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Failed To Update Tournament";
        }
        
        return $response;
    }
    
    function checkTournamentTimings(){
        
        // getting tournament ids those are live or available to check date and change status accordingly
        $tournaments = getDataFromTable("tournaments", "id, status", "status = 'available' OR status = 'live'");
        
        foreach($tournaments as $tournament){
            
            // getting tournament schedule
            $tournamentTimings = getDataFromTable("tournament_schedule", "*", "tournament_id = '".$tournament['id']."'", true);
        
            $newStatus = $tournament['status'];
            
            // checking tournament status and change status according to the end time
            if($tournament['status'] == 'available'){
                if(strtotime($tournamentTimings['start_date_time']) < time()){
                    $newStatus = "live";
                }
            }
            else if($tournament['status'] == 'live'){
                if(strtotime($tournamentTimings['end_date_time']) < time()){
                    $newStatus = "completed";
                }
            }
            
            if($newStatus != $tournament['status']){
                
                // update tournament status to live
                updateDataIntoTable("tournaments", array('status' => $newStatus), "id = '".$tournament['id']."'");
            }
            
        }
    }
    
    function tournamentDetailsToString($detailsArray){
        
        $generateDetails = "";
        
        for($i = 0; $i < sizeof($detailsArray); $i++){
            if($generateDetails == ""){
                $generateDetails = $detailsArray[$i];
            }
            else{
                $generateDetails = $generateDetails.",".$detailsArray[$i];
            }
        }
        
        return $generateDetails;
    }
    

    function getJoinedPlayers($needJsonForm, $tournamentId, $type){
        
        $response = array();
        
        $selectJoinePlayers = "SELECT joined_players, game_usernames FROM tournaments WHERE id = '$tournamentId'";
        $joinedPlayerResults = $this->con->query($selectJoinePlayers);
        $joinedPlayerRow = $joinedPlayerResults->fetch_assoc();
        $joinedPlayerArray = json_decode($joinedPlayerRow['joined_players'], true);
        $gameUsernameArray = json_decode($joinedPlayerRow['game_usernames'], true);
        
        $response['total'] = sizeof($joinedPlayerArray);
        
        for($i = 0; $i < sizeof($joinedPlayerArray); $i++){
            
            $selectUser = "SELECT id, fullname FROM users WHERE id = '".$joinedPlayerArray[$i]."'";
            $userResults = $this->con->query($selectUser);
            $userRow = $userResults->fetch_assoc();
            
            $data = array();
            
            $data['sno'] = $i + 1;
            
            if($type == 'distribute'){
                $data['action'] = '<input style="width:100px;" type="number" class="form-control" id="rank_no_'.$i.'" value = "0">';
                $data['win_amount'] = '<input style="width:100px;" type="number" class="form-control" id="win_amount_'.$i.'" value = "0">';
            }
            else{
                $data['action'] = "<button class='btn btn-sm btn-danger deletePlayer' onclick = '(showDeleteModal(".$userRow['id'].", ".$tournamentId."))'; data-user_id='".$userRow['id']."' data-tournament_id='".$tournamentId."'><i class='fa fa-trash'></i></button>";
            }
            
            $data['fullname'] = $userRow['fullname'];
            $data['game_username'] = $gameUsernameArray[$i];
            
            $response['data'][] = $data;
        }
        
        if($needJsonForm){
            return json_encode($response);
        }
        else{
            return $response;
        }
    }
    
    function removeJoinedUser($tournamentId, $userId, $giveRefund, $message){
        
        // remove joined player
        $removePlayer = deleteDataFromTable("tournament_joinings", "tournament_id = '$tournamentId' AND user_id = '$userId'");
        $logs = "";
        
        if($removePlayer['status'] == 1){
            
            $removePlayer['msg'] = "Removed";
            
            // send notification and make transaction
            $notifications = new Notifications($this->con);
            
            $title = "";
            $message = "";
            
            // give refund
            if($giveRefund == 1){
                
                // getting tournament entry fees
                $entryFees = getDataFromTable("tournaments", "entry_fees", "id = '$tournamentId'", true);
                    
                $giveRefund = updateDataIntoTable("users", array("deposit_amount" => "deposit_amount + $entryFees"), "id = '$userId'");
                
                if($giveRefund['status'] == 1){
                    
                    $title = "Tournament Refund";
                    $message = "You have been removed from a tournament. Your entry fees has been refunded to your account.\n\nReason:".$message;
                        
                    // make transaction
                    insertDataIntoTable("transactions", array("user_id" => $userId, "title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date("H:i:s"), "amount" => "+".$entryFees, "reciept_no" => ""));
                }
                else{
                    $logs = "Unable to give refind to the user";
                }
            }
            else{
                
                $title = "Tournament Refund";
                $message = "You have been removed from a tournament.\n\nReason:".$message;
            }
            
            // send push notification
            $notifications->pushNotificationToSingle($userId, $title, $message, "",  "activity", "splash", "");
        }
        else{
            $removePlayer['msg'] = "Unablt to remove user";
        }
        
        $removePlayer['logs'] = $logs;
        return $removePlayer;
    }
    
    function cancelTournament($tournamentId, $reason){
        
        $entryFees = getDataFromTable("tournaments", "entry_fees", "id = '$tournamentId'", true);
        $joinedPlayers = getDataFromTable("tournament_joinings", "user_id", "tournament_id = '$tournamentId'");
        
        // update tournament status to live
        $updateTournament = updateDataIntoTable("tournaments", array('status' => 'cancelled'), "id = '$tournamentId'");
        $logs = "";
        
        if($updateTournament['status'] == 1){
            $updateTournament['msg'] = "Cancelled";
            
            foreach($joinedPlayers as $userId){
                
                $giveRefund = updateDataIntoTable("users", array('deposit_amount' => "deposit_amount + $entryFees"), "id = '$userId'");
                
                if($giveRefund['status'] == 0){
                    
                    $userFullname = getDataFromTable("users", "fullname", "id = '$userId'", true);
                    $logs .= "Update to refund the prize of ".$userFullname."\n\n";
                }
                else{
                    
                    $title = "Tournament Cancellation Refund";
                    $msg = $reason;
            
                    // make transaction
                    insertDataIntoTable("transactions", array("user_id" => $userId, "title" => "$title", "message" => $msg, "date" => date('Y-m-d'), "time" => date('H:i:s'), "amount" => "+".$entryFees));
                    
                    $notifications = new Notifications($this->con);
                    $notifications->pushNotificationToSingle($userId, $title, $msg, "",  "activity", "splash", "");
                }
            }
        }
        
        $updateTournament['logs'] = $logs;
        
        return $updateTournament;
    }
    
    function sendRoomId($tournamentId, $roomId, $message, $youTubeLink){
        
        $response = array();
        
        // update room id in the tournament
        $updateTournament = updateDataIntoTable("tournaments", array("room_id" => $roomId, "message" => $message, "youtube_video" => $youTubeLink), "id = '$tournamentId'");
        
        if($updateTournament['status'] == 1){
            
            $updateTournament['msg'] = "Room Id sent";
            
            // create notification class object
            $notifications = new Notifications($this->con);
        
            // getting joined players
            $joinedPlayerIds = getDataFromTable("tournament_joinings", "user_id", "tournament_id = '$tournamentId'");
            
            $title = "Room Id Available";
            $message = "Room Id is = ".$roomId."\n".$message;
            
            foreach($joinedPlayerIds as $playerId){ 
                $notifications->pushNotificationToSingle($playerId, $title, $message, "",  "activity", "splash", "");
            }
        }
        
        return json_encode($response);
    }
    
    function distributePrize($tournamentId, $ranksArray){
        
        $response = array();
        $logs = "";
        
        $tournamentPrizes = getDataFromTable("tournament_prizes", "*", "tournament_id = '$tournamentId'");
        $tournamentPerKill = getDataFromTable("tournaments", "per_kill", "id = '$tournamentId'", true);
        
        foreach($ranksArray as $rank){
            
            $wonAmount = 0;
            
            foreach($tournamentPrizes as $prize){
                if($prize['start_rank'] <= $rank['rank'] && $prize['end_rank'] >= $rank['rank']){
                    $wonAmount = $prize['amount'];
                    break;
                }
            }
            
            if($tournamentPerKill > 0){
                $wonAmount += $rank['kills'] * $tournamentPerKill;
            }
            
            if($wonAmount == 0){
                continue;
            }
            
            $tournamentJoinings = getDataFromTable("tournament_joinings", "*", "id = '".$rank['id']."'", true);
            $userfullname = getDataFromTable("users", "fullname", "id = '".$tournamentJoinings['user_id']."'", true);
            

            // check if user didn't get the winning amount before
            if($tournamentJoinings['won_amount'] == 0){
            
                // update winning amount and rank into the tournament_joinings table
                $updateRank = updateDataIntoTable("tournament_joinings", array("rank" => $rank['rank'], "won_amount" => $wonAmount, "kills" => $rank['kills']), "id = '".$rank['id']."'");
                
                if($updateRank['status'] == 1){
                    
                    // update user wallet
                    $userUpdateStatus = updateDataIntoTable("users", array("win_amount" => "win_amount + $wonAmount", "won_tournaments" => "won_tournaments + 1", "lifetime_winning" => "lifetime_winning + $wonAmount"), "id = '".$tournamentJoinings['user_id']."'");
                    
                    if($userUpdateStatus['status'] == 0){
                        $logs .= "Unable to give prize to ( fullname = ".$userfullname.", game username = ".$tournamentJoinings['game_username']." )\n\n";
                    }
                    else{
                   
                        $title = "Won a Tournament";
                        $message = "You have won a tournament. Winning amount (".$wonAmount.") has been creditd to your account";
                        
                        // make transaction
                        $createTransaction = insertDataIntoTable("transactions", array("user_id" => $tournamentJoinings['user_id'], "title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $wonAmount, "reciept_no" => ""));
                        
                        // send notification
                        $notifications = new Notifications($this->con);
                        $notifications->pushNotificationToSingle($tournamentJoinings['user_id'], $title, $message, "",  "activity", "splash", "");
                
                        if($createTransaction['status'] == 0){
                            $logs .= "Unable to create transaction for user ( fullname = ".$userfullname.", game username = ".$tournamentJoinings['game_username']." )\n\n";
                        }
                    }
                }
                else{
                    $logs .= "Unable to give prize to ( fullname = ".$userfullname.", game username = ".$tournamentJoinings['game_username']." )\n\n";
                }
            }
        }
        
        $response['status'] = 1;
        $response['msg'] = "Success";
        $response['logs'] = $logs;
        
        return $response;
    }
}

?>
