<?php

class Games
{

    public $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    function getGames($params, $whereCondition)
    {

        // $vars = $tableName, $params, $whereCondition, $additionalData = null
        return getDataFromTable("games", $params, $whereCondition, function ($dataRow) {

        });
    }

    function insertGame($name, $image, $howToGetId)
    {

        // $vars = $tableName, $dataArray
        return insertDataIntoTable("games", array("name" => $name, "image" => $image, "how_to_get_id" => $howToGetId));

    }

    function checkTournamentTimings()
    {

        // getting tournament ids those are live or available to check date and change status accordingly
        $tournaments = getDataFromTable("tournaments", "id, status", "status = 'available' OR status = 'live'");

        foreach ($tournaments as $tournament) {

            // getting tournament schedule
            $tournamentTimings = getDataFromTable("tournament_schedule", "*", "tournament_id = '" . $tournament['id'] . "'", true);

            $newStatus = $tournament['status'];

            // checking tournament status and change status according to the end time
            if ($tournament['status'] == 'available') {
                if (strtotime($tournamentTimings['start_date_time']) < time()) {
                    $newStatus = "live";
                }
            } else if ($tournament['status'] == 'live') {
                if (strtotime($tournamentTimings['end_date_time']) < time()) {
                    $newStatus = "completed";
                }
            }

            if ($newStatus != $tournament['status']) {

                // update tournament status to live
                updateDataIntoTable("tournaments", array('status' => $newStatus), "id = '" . $tournament['id'] . "'");
            }

        }
    }

    function getTournaments($gameId, $type)
    {

        $tournaments = array();

        // getting tournament schedule, joined player details, prize distributions
        $callbackFunction = function ($dataRow) {

            // getting tournament schedule
            $tournamentSchedule = getDataFromTable("tournament_schedule", "*", "tournament_id = '" . $dataRow['id'] . "'", true);

            // adding start datetime and end datetime
            $dataRow['start_date_time'] = $tournamentSchedule['start_date_time'];
            $dataRow['end_date_time'] = $tournamentSchedule['end_date_time'];

            // getting joined players
            $dataRow['joined_players'] = getDataFromTable("tournament_joinings", "user_id", "tournament_id = '" . $dataRow['id'] . "'");

            // getting joined players
            $dataRow['prizes'] = getDataFromTable("tournament_prizes", "start_rank, end_rank, amount", "tournament_id = '" . $dataRow['id'] . "'");

            $dataRow['image'] = currentDirPath() . $dataRow['image'];

            $dataRow['details'] = json_decode($dataRow['details'], true);

            return $dataRow;
        };

        if ($type == 'ongoing') {
            $tournaments = getDataFromTable("tournaments", "*", "status = 'live' AND game_id = '$gameId'", false, $callbackFunction);
        } else if ($type == 'upcoming') {
            $tournaments = getDataFromTable("tournaments", "*", "status = 'available' AND game_id = '$gameId'", false, $callbackFunction);
        } else if ($type == 'results') {
            $tournaments = getDataFromTable("tournaments", "*", "status = 'completed' AND game_id = '$gameId'", false, $callbackFunction,  "id" ,"DESC");
        }

        return $tournaments;
    }

    function joinTournament($tournamentId, $gameUsername, $userId)
    {

        $response = array();

        // getting tournament details
        $tournamentDetails = getDataFromTable("tournaments", "game_id, entry_fees, total_players, status, from_bonus", "id = '$tournamentId'", true);

        if ($tournamentDetails == null) {
            $response['status'] = 0;
            $response['msg'] = "Something went wrong!!!";
            return $response;
        }

        if ($tournamentDetails['status'] == 'cancelled') {
            $response['status'] = 0;
            $response['msg'] = "Tournament is cancelled";
            return $response;
        }

        if ($tournamentDetails['status'] != 'available') {
            $response['status'] = 0;
            $response['msg'] = "Tournament is not available";
            return $response;
        }

        // getting tournament joined players
        $joinedPlayers = getDataFromTable("tournament_joinings", "user_id", "tournament_id = '$tournamentId'");

        if (sizeof($joinedPlayers) == $tournamentDetails['total_players']) {
            $response['status'] = 0;
            $response['msg'] = "Match Full";
            return $response;
        }

        // getting user details
        $userDetails = getDataFromTable("users", "bonus_amount, deposit_amount, win_amount", "id = '$userId'", true);

        if ($userDetails == null) {
            $response['status'] = 0;
            $response['msg'] = "Something went wrong!!!";
            return $response;
        }

        $fromBonus = $tournamentDetails['from_bonus'];
        $entryFees = $tournamentDetails['entry_fees'];

        if ($fromBonus > 0) {
            if ($userDetails['bonus_amount'] < $fromBonus) {
                $fromBonus = $userDetails['bonus_amount'];
            }
        }

        if ($entryFees > ($userDetails['deposit_amount'] + $userDetails['win_amount'] + $fromBonus)) {
            $response['status'] = 0;
            $response['msg'] = "Insufficient Balance";
            return $response;
        }

        // user left amounts after joined the tournament
        $leftDepositAmount = $userDetails['deposit_amount'];
        $leftWinAmount = $userDetails['win_amount'];
        $leftBonusAmount = $userDetails['bonus_amount'];

        if ($leftDepositAmount >= $entryFees) {
            $leftDepositAmount = $leftDepositAmount - $entryFees;
        } else {

            $entryFees = $entryFees - $leftDepositAmount;
            $leftDepositAmount = 0;

            if ($leftWinAmount >= $entryFees) {
                $leftWinAmount = $leftWinAmount - $entryFees;
            } else {
                $entryFees = $entryFees - $leftWinAmount;
                $leftWinAmount = 0;

                $leftBonusAmount = $leftBonusAmount - $entryFees;
            }
        }

        $updateWallet = updateDataIntoTable("users", array("deposit_amount" => $leftDepositAmount, "win_amount" => $leftWinAmount, "bonus_amount" => $leftBonusAmount), "id = '$userId'");
        if ($updateWallet['status'] == 0) {
            $response['status'] = 0;
            $response['msg'] = "Unable to join tournament";
            return $response;
        }

        $joinPlayer = insertDataIntoTable("tournament_joinings", array("user_id" => $userId, "tournament_id" => $tournamentId, "game_username" => $gameUsername, "date_time" => date("Y-m-d H:i:s")));
        if ($joinPlayer['status'] == 0) {
            $response['status'] = 0;
            $response['msg'] = "Unable to join tournament";
            return $response;
        }

        // saved game username
        $savedUsername = getDataFromTable("games_usernames", "id, username", "user_id = '$userId' AND game_id = '" . $tournamentDetails['game_id'] . "'", true);

        if ($savedUsername == null) {
            insertDataIntoTable("games_usernames", array("user_id" => $userId, "game_id" => $tournamentDetails['game_id'], "username" => $gameUsername));
        } else {
            if ($savedUsername['username'] != $gameUsername) {
                updateDataIntoTable("games_usernames", array("username" => $gameUsername), "id = '" . $savedUsername['id'] . "'");
            }
        }

        // make transaction
        insertDataIntoTable("transactions", array("user_id" => $userId, "title" => "Joined a Tournament", "message" => "You had participated in the tournament", "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => "-" . $tournamentDetails['entry_fees'], "reciept_no" => ""));

        // update user played tournaments
        updateDataIntoTable("users", array("played_tournaments" => "played_tournaments + 1"), "id = '$userId'");

        $response['status'] = 1;
        $response['msg'] = "Successfully Joined";
        $response['user_details'] = getDataFromTable("users", "*", "id = $userId", true, function($dataRow){
            
            if($dataRow['profile_pic'] != ""){
                $dataRow['profile_pic'] = currentDirPath().$dataRow['profile_pic'];
            }

            return $dataRow;
            
        });
        $response['games_usernames'] = getDataFromTable("games_usernames", "*", "user_id = $userId");

        return $response;
    }
}

?>