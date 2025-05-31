<?php session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    date_default_timezone_set('Asia/Kolkata');

    // IMPORT FILES
    include_once("users.php");
    include_once("../../dbcon.php");
    include_once("get_search_rows.php");
    include_once("upload_file.php");
    include_once('../../transactions.php');
    include_once("../../notifications.php");
    include_once("../../notification.php");
    include_once("../../data-functions.php");
    include_once("../../global-functions.php");
    include_once("tournments.php");

    // GET FROM
    $from = $_POST['from'];

    # LOGIN USER
    if ($from == 'login_user') {

        $username = $con->real_escape_string($_POST['username']);
        $password = $con->real_escape_string($_POST['password']);

        $users = new Users($con);

        $loginUser = $users->loginUser($username, $password);

        echo json_encode($loginUser);
    } else if ($from == 'get_users') {

        $userId = $con->real_escape_string($_POST['user_id']);
        $type = $con->real_escape_string($_POST['type']);

        $draw = $con->real_escape_string($_POST['draw']);
        $startPosition = $con->real_escape_string($_POST['start']);
        $rowPerPage = $con->real_escape_string($_POST['length']);
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);

        // set column names to search or filter into
        $getSearchRows->setSearchColumns("id, register_date, login_date, email, mobile, fullname, referral_code");

        // set sql WHERE condition
        if ($type != '') {
            $currentDate = date('Y-m-d');
            $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));

            if ($type == 'today_login') {
                $getSearchRows->getSelectCondition("login_date LIKE '%" . $currentDate . "%'");
            } else if ($type == 'yesterday_login') {
                $getSearchRows->getSelectCondition("login_date LIKE '%" . $yesterdayDate . "%'");
            }
        } else if ($userId != '') {
            $getSearchRows->getSelectCondition("id=" . $userId);
        }

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("users");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            // Create Buttons
            $sendNotification = "<a href='#' id = 'sendNotification' data-user_id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-primary'>Notification</a>";
            $transactions = "<a href='transactions.php?id=" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-primary'>View Transactions</a>";
            $updateAmount = "<a href='#' type='button' id = 'updateWalletBtn'  data-user_id='" . $dataRow['id'] . "' data-deposit_amount='" . $dataRow['deposit_amount'] . "' data-winning_amount='" . $dataRow['win_amount'] . "' data-bonus_amount='" . $dataRow['bonus_amount'] . "' class='m-1 badge  badge-primary'>Update Wallet</a>";

            $blockedStatus = "<a href='#' id = 'userStatus' data-user_id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-danger'>Block</a>";
            if ($dataRow['blocked'] == 1) {
                $blockedStatus = "<a href='#' id = 'userStatus' data-user_id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-success'>Un-Block</a>";
            }

            // add additional data to response
            $dataRow['transactions'] = $transactions;
            $dataRow['sponsor'] = $dataRow['sponsor_id'];
            $dataRow['action'] = $sendNotification . "<br>" . $blockedStatus . "<br>" . $updateAmount;
            $dataRow['profile_pic'] = '<img src="../' . $dataRow['profile_pic'] . '" style = "width:100px; height:auto;">';

            return $dataRow;

        });

    } else if ($from == 'update_user_wallet') {

        $depositAmount = $con->real_escape_string($_POST['deposit_amount']);
        $winningAmount = $con->real_escape_string($_POST['winning_amount']);
        $bonusAmount = $con->real_escape_string($_POST['bonus_amount']);
        $userId = $con->real_escape_string($_POST['user_id']);

        // $vars = $tableName, $dataArray, $whereCondition
        $response = updateDataIntoTable('users', array(
            "deposit_amount" => $depositAmount,
            "bonus_amount" => $bonusAmount,
            "win_amount" => $winningAmount,
        ), "id = '$userId'");

        echo json_encode($response);
    } else if ($from == 'change_user_status') {

        $response = array();

        $userId = $con->real_escape_string($_POST['user_id']);

        $status = getDataFromTable("users", "blocked", "id = '$userId'", true);

        if ($status == 1) {
            $response = updateDataIntoTable("users", array("blocked" => 0), "id = '$userId'");
        } else {
            $response = updateDataIntoTable("users", array("blocked" => 1), "id = '$userId'");
        }

        echo json_encode($response);
    } else if ($from == 'get_tournaments') {

        $tournamentStatus = $_SESSION['tournament_status'];

        $draw = $con->real_escape_string($_POST['draw']);
        $startPosition = $con->real_escape_string($_POST['start']);
        $rowPerPage = $con->real_escape_string($_POST['length']);
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);
        $tournaments = new Tournaments($con);

        // check tournaments timings
        $tournaments->checkTournamentTimings();

        // set column names to search or filter into
        $getSearchRows->setSearchColumns("id, name, entry_fees, prize_pool, total_players, map, type, mode, gameName");

        // set sql WHERE condition
        $getSearchRows->getSelectCondition("status = '" . $tournamentStatus . "'");

        // ADD INNER JOINS
        $getSearchRows->addInnerJoin("INNER JOIN (SELECT id AS gameId, name AS gameName FROM games) AS gameData");
        $getSearchRows->setInnerJoinONCondition(" ON (tournaments.game_id = gameData.gameId) ");

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("tournaments");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            global $tournaments;

            // getting tournament schedule
            $getSchedule = getDataFromTable("tournament_schedule", "*", "tournament_id = '" . $dataRow['id'] . "'", true, null);

            // generate tournament details string
            $tournamentDetails = $tournaments->tournamentDetailsToString(json_decode($dataRow['details'], true));

            // get winning screenshots
            $winningSS = getDataFromTable("win_screenshots", "*", "tournament_id = '" . $dataRow['id'] . "'");

            // create buttons
            $editBtn = "<button class='btn btn-sm btn-success editTournament' data-id='" . $dataRow['id'] . "'><i class='fa fa-edit'></i></button> ";
            $tournamentActions = "<button class='btn btn-sm btn-primary tournamentActions' data-id='" . $dataRow['id'] . "'><i class='fa fa-arrows-alt'></i></button> ";

            // create ancher tags
            $from = "<a href='#' type='button' class='m-1 badge  badge-success'>" . $getSchedule['start_date_time'] . "</a>";
            $to = "<a href='#' type='button' class='m-1 badge  badge-success'>" . $getSchedule['end_date_time'] . "</a>";

            if ($dataRow['youtube_video'] == '') {
                $dataRow['youtube_video'] = 'No Video Available';
            } else {
                $dataRow['youtube_video'] = "<a href='" . $dataRow['youtube_video'] . "' type='button' class='m-1 badge  badge-success'>Watch Video</a>";
            }
            $ssData = "";
            foreach ($winningSS as $winningS) {
                if ($ssData == '') {
                    $ssData = '<img src="../' . $winningS['ss_file'] . '" style = "width:100px; height:auto;">';
                } else {
                    $ssData = $ssData . '<br><img src="../' . $winningS['ss_file'] . '" style = "width:100px; height:auto;">';
                }
            }

            $dataRow['win_ss'] = $ssData;
            $dataRow['joined_players'] = "<a href='#' data-id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-primary seeJoinedPlayers'>See Players</a>";
            $dataRow['details'] = "<a href='#' data-id='" . $dataRow['id'] . "' data-details='" . $tournamentDetails . "' type='button' class='m-1 badge  badge-primary seeDetails'>See Details</a>";
            $dataRow['gameName'] = "<a href='#' type='button' class='m-1 badge  badge-primary'>" . $dataRow['gameName'] . "</a>";
            $dataRow['schedule'] = $from . "<br>To<br>" . $to;
            $dataRow['image'] = '<img src="../' . $dataRow['image'] . '" style = "width:100px; height:auto;">';
            $dataRow['action'] = $editBtn . " " . $tournamentActions;

            return $dataRow;
        });

    } else if ($from == 'get_joined_players') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);

        $tournamentJoinings = getDataFromTable("tournament_joinings", "*", "tournament_id = '$tournamentId'", false, function ($dataRow) {

            $dataRow['fullname'] = getDataFromTable("users", "fullname", "id = '" . $dataRow['user_id'] . "'", true);

            return $dataRow;
        });

        echo json_encode($tournamentJoinings);
    } else if ($from == 'remove_joined_user') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $userId = $con->real_escape_string($_POST['user_id']);
        $message = $con->real_escape_string($_POST['message']);
        $giveRefund = $con->real_escape_string($_POST['give_refund']);

        // creating Objects
        $tournaments = new Tournaments($con);

        echo json_encode($tournaments->removeJoinedUser($tournamentId, $userId, $giveRefund, $message));
    } else if ($from == 'update_details') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $details = $con->real_escape_string(str_replace("'", "", $_POST['details']));

        $tournamentDetails = explode(",", $details);

        $response = updateDataIntoTable("tournaments", array("details" => json_encode($tournamentDetails)), "id = '$tournamentId'");

        echo json_encode($response);
    } else if ($from == 'add_new_tournament') {

        $gameId = $con->real_escape_string($_POST['t_game_id']);
        $title = $con->real_escape_string(str_replace("'", "", $_POST['t_title']));
        $image = $_FILES['t_image'];
        $map = $con->real_escape_string($_POST['t_map']);
        $type = $con->real_escape_string($_POST['t_type']);
        $mode = $con->real_escape_string($_POST['t_mode']);
        $entryFees = $con->real_escape_string($_POST['t_entry_fees']);
        $prizePool = $con->real_escape_string($_POST['t_prize_pool']);
        $perKill = $con->real_escape_string($_POST['t_per_kill']);
        $fromBonus = $con->real_escape_string($_POST['t_from_bonus']);
        $totalPlayers = $con->real_escape_string($_POST['t_total_players']);
        $tournamentDetails = str_replace("'", "", $con->real_escape_string($_POST['t_details']));
        $tournamentDetails = explode(",", $tournamentDetails);
        $prizeCount = $con->real_escape_string($_POST['prizes_count']);
        $startDateEndDate = explode(" - ", $con->real_escape_string($_POST['t_schedule']));

        $logs = "";

        // create objects
        $tournaments = new Tournaments($con);

        $response = $tournaments->addNewTournament($gameId, $title, $image, $map, $type, $mode, $entryFees, $prizePool, $perKill, $fromBonus, $totalPlayers, $tournamentDetails);

        if ($response['status'] == 1) {
            $logs = $logs . $tournaments->addTournamentPrizes($response['id'], $prizeCount);
            $logs = $logs . $tournaments->addTournamentSchedule($response['id'], $startDateEndDate);
        }
        
        // create objects
        $notificatios = new Notifications ($con);

        // sending push notification to the user
        $notificatios->pushNotification("New Tournament Added", $title, "", "activity", "splash", "");

        $response['logs'] = $logs;

        echo json_encode($response);
    } else if ($from == 'update_tournament') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $image = $_FILES['t_image'];
        $prizeCount = $con->real_escape_string($_POST['prizes_count']);
        $startDateEndDate = explode(" - ", $con->real_escape_string($_POST['t_schedule']));
        $tournamentDetails = str_replace("'", "", $con->real_escape_string($_POST['t_details']));
        $tournamentDetails = explode(",", $tournamentDetails);

        $logs = "";

        // create objects
        $uploadFile = new UploadFile($con);
        $tournaments = new Tournaments($con);

        $tournamentImagePath = $tournaments->addTournamentImage($image);

        if ($tournamentImagePath == "") {
            $tournamentImagePath = $_POST['old_image'];
        }

        $tournamentData = array();
        $tournamentData['game_id'] = $con->real_escape_string($_POST['t_game_id']);
        $tournamentData['name'] = $con->real_escape_string(str_replace("'", "", $_POST['t_title']));
        $tournamentData['image'] = $tournamentImagePath;
        $tournamentData['map'] = $con->real_escape_string($_POST['t_map']);
        $tournamentData['type'] = $con->real_escape_string($_POST['t_type']);
        $tournamentData['mode'] = $con->real_escape_string($_POST['t_mode']);
        $tournamentData['entry_fees'] = $con->real_escape_string($_POST['t_entry_fees']);
        $tournamentData['prize_pool'] = $con->real_escape_string($_POST['t_prize_pool']);
        $tournamentData['per_kill'] = $con->real_escape_string($_POST['t_per_kill']);
        $tournamentData['from_bonus'] = $con->real_escape_string($_POST['t_from_bonus']);
        $tournamentData['total_players'] = $con->real_escape_string($_POST['t_total_players']);
        $tournamentData['details'] = json_encode($tournamentDetails);
        $tournamentData['status'] = "available";

        // Updating tournament
        $response = updateDataIntoTable("tournaments", $tournamentData, "id = '$tournamentId'");

        if ($response['status'] == 1) {

            // delete old tournament prizes
            deleteDataFromTable("tournament_prizes", "tournament_id = '$tournamentId'");

            $logs = $logs . $tournaments->addTournamentPrizes($tournamentId, $prizeCount);

            $tournamentScheduleData = array();
            $tournamentScheduleData['start_date_time'] = date("Y-m-d H:i:s", strtotime($startDateEndDate[0]));
            $tournamentScheduleData['end_date_time'] = date("Y-m-d H:i:s", strtotime($startDateEndDate[1]));

            // delete old schedule
            $scheduleResponse = updateDataIntoTable("tournament_schedule", $tournamentScheduleData, "tournament_id = '$tournamentId'");

            if ($scheduleResponse['status'] == 0) {
                $logs .= "Unable to set tournament timings = " . json_encode($tournamentScheduleData) . "\n\n";
            }
        }

        $response['logs'] = $logs;
        echo json_encode($response);

    } else if ($from == 'cancel_tournament') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $reason = $con->real_escape_string(str_replace("'", "", $_POST['reason']));

        // creating Objects
        $tournaments = new Tournaments($con);

        echo json_encode($tournaments->cancelTournament($tournamentId, $reason));
    } else if ($from == 'distribute_prize') {

        $tournamentId = $con->real_escape_string($_POST['tournament_id']);
        $ranks = $_POST['ranks'];

        // creating Objects
        $tournaments = new Tournaments($con);

        echo json_encode($tournaments->distributePrize($tournamentId, $ranks));

    } else if ($from == 'send_room_id') {

        $touranmentId = $con->real_escape_string($_POST['tournament_id']);
        $roomId = $con->real_escape_string($_POST['room_id']);
        $message = $con->real_escape_string(str_replace("'", "", $_POST['message']));
        $youTubeLink = $con->real_escape_string($_POST['youtube']);

        // creating Objects
        $tournaments = new Tournaments($con);

        echo json_encode($tournaments->sendRoomId($touranmentId, $roomId, $message, $youTubeLink));
    } else if ($from == 'send_notification') {

        $sendTo = $con->real_escape_string($_POST['to']); // single or all
        $userId = $con->real_escape_string($_POST['user_id']);

        $title = $con->real_escape_string($_POST['title']);
        
        $fcmToken = "";
        if(isset($_POST['fcm_token'])){
            $fcmToken = $con->real_escape_string($_POST['fcm_token']);
        }
        
        $body = $con->real_escape_string($_POST['body']);
        $clickAction = $con->real_escape_string($_POST['click_action']);
        $destination = $con->real_escape_string($_POST['destination']);
        $payloadData = $con->real_escape_string($_POST['payload']);

        // create objects
        $notificatios = new Notifications ($con);
        $uploadFile = new UploadFile($con);

        $imageURL = "";
        
        if($fcmToken != ""){
            // update FCM token in main data
            updateDataIntoTable("main_data", array("fcm_token" => $fcmToken), "id = '1'");
        }
        
        $fileDetails = $_FILES['img'];

        if (!empty($fileDetails['tmp_name'])) {

            $filePath = "NotificationImages";
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $uploadStatus = $uploadFile->uploadFile($filePath, $fileDetails);

            if ($uploadStatus['status'] == true) {
                $imageURL = currentDirPath() . $uploadStatus['filepath'];
            }
        }
        if ($sendTo == 'single') {
            $notificatios->pushNotificationToSingle($userId, $title, $body, $imageURL, $clickAction, $destination, $payloadData);
        } else {
            $notificatios->pushNotification($title, $body, $imageURL, $clickAction, $destination, $payloadData);
        }
        echo $imageURL;
    } else if ($from == 'update_main_data') {

        // selected payment gateway details
        $gatewayId = $con->real_escape_string($_POST['gateway_id']);
        $keyValue = $con->real_escape_string($_POST['key_value_' . $gatewayId]);
        $saltValue = $con->real_escape_string($_POST['salt_value_' . $gatewayId]);

        // collecting main data
        $mainData = array();
        $mainData['version'] = $con->real_escape_string($_POST['version']);
        $mainData['gateway_id'] = $gatewayId;
        $mainData['update_details'] = $con->real_escape_string($_POST['update_details']);
        $mainData['app_link'] = $con->real_escape_string($_POST['app_link']);
        $mainData['website_link'] = $con->real_escape_string($_POST['website_link']);
        $mainData['instagram'] = $con->real_escape_string($_POST['instagram']);
        $mainData['youtube'] = $con->real_escape_string($_POST['youtube']);
        $mainData['privacy_policy'] = $con->real_escape_string($_POST['privacy_policy']);
        $mainData['refer_amount'] = $con->real_escape_string($_POST['refer_amount']);
        $mainData['min_withdraw'] = $con->real_escape_string($_POST['min_withdraw']);
        $mainData['registration_bonus'] = $con->real_escape_string($_POST['registration_bonus']);
        $mainData['share_txt'] = $con->real_escape_string(str_replace("'", "", $_POST['share_txt']));
        $mainData['terms'] = $con->real_escape_string($_POST['terms']);
        $mainData['announcements'] = $con->real_escape_string($_POST['announcements']);
        $mainData['offline_payment_instructions'] = $_POST['offline_payment_instructions'];

        // update main data
        $updateMainData = updateDataIntoTable("main_data", $mainData, "id = 1");
        $logs = "";

        if ($updateMainData['status'] == 1) {

            // update admin details
            $updateAdmin = updateDataIntoTable("admins", array("username" => $con->real_escape_string($_POST['login_username']), "password" => $con->real_escape_string($_POST['login_password'])), "id = 1");

            if ($updateAdmin['status'] == 0) {
                $logs .= "Unable to update admin details\n\n";
            }

            $updateGateway = updateDataIntoTable("payment_gateways", array("key_value" => $keyValue, "salt_value" => $saltValue), "id = '$gatewayId'");

            if ($updateGateway['status'] == 0) {
                $logs .= "Unable to update gateway's key and salt values\n\n";
            }
        }

        $updateMainData['logs'] = $logs;

        echo json_encode($updateMainData);
    } else if ($from == 'withdraw_requests') {

        $draw = $con->real_escape_string($_POST['draw']);
        $startPosition = $con->real_escape_string($_POST['start']);
        $rowPerPage = $con->real_escape_string($_POST['length']);
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);

        // set column names to search or filter into
        $getSearchRows->setSearchColumns("userData.profileName, type, amount, mobile, account_no, fullname, bank_name, ifsc, status");

        // ADD INNER JOINS
        $getSearchRows->addInnerJoin("INNER JOIN (SELECT id AS userId, fullname AS profileName FROM users) AS userData");
        $getSearchRows->setInnerJoinONCondition(" ON (userData.userId = user_id)");

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("withdraw_requests");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            if ($dataRow['status'] == 'pending') {
                $sendNotification = "<a href='#' id = 'changeStatus' data-id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-warning'>" . $dataRow['status'] . "</a>";
            } else if ($dataRow['status'] == 'accepted') {
                $sendNotification = "<a href='#' data-id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-success'>" . $dataRow['status'] . "</a>";
            } else {
                $sendNotification = "<a href='#' data-id='" . $dataRow['id'] . "' type='button' class='m-1 badge  badge-danger'>" . $dataRow['status'] . "</a>";
            }

            $dataRow['type'] = "<a href='#' type='button' class='m-1 badge  badge-primary'>" . $dataRow['type'] . "</a>";
            $dataRow['status'] = $sendNotification;

            return $dataRow;
        });

    } else if ($from == 'get_transactions') {

        $userId = $con->real_escape_string($_POST['user_id']);

        $draw = $con->real_escape_string($_POST['draw']);
        $startPosition = $con->real_escape_string($_POST['start']);
        $rowPerPage = $con->real_escape_string($_POST['length']);
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);

        // set sql WHERE condition
        if ($userId != '0') {
            $getSearchRows->getSelectCondition('user_id = ' . $userId);
        }

        // set column names to search or filter into
        $getSearchRows->setSearchColumns("userData.fullname, title, amount, message, reciept_no, date, time");

        // ADD INNER JOINS
        $getSearchRows->addInnerJoin("INNER JOIN (SELECT id AS userId, fullname FROM users) AS userData");
        $getSearchRows->setInnerJoinONCondition(" ON (userData.userId = user_id)");

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("transactions");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            $dataRow['fullname'] = "<a href='users.php?id=" . $dataRow['user_id'] . "' type='button' class='m-1 badge  badge-success'>" . $dataRow['fullname'] . "</a>";
            $dataRow['date_time'] = $dataRow['date'] . " - " . $dataRow['time'];

            return $dataRow;
        });

    } else if ($from == 'get_games') {

        $draw = $con->real_escape_string($_POST['draw']);
        $startPosition = $con->real_escape_string($_POST['start']);
        $rowPerPage = $con->real_escape_string($_POST['length']);
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("games");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            $editBtn = "<button class='btn btn-sm btn-success editGame' data-id='" . $dataRow['id'] . "' data-name='" . $dataRow['name'] . "' data-image='../" . $dataRow['image'] . "' data-how_to_get_id='" . $dataRow['how_to_get_id'] . "'><i class='fa fa-edit'></i></button>";
            $deleteBtn = "<button class='btn btn-sm btn-danger deleteGame' data-id='" . $dataRow['id'] . "'><i class='fa fa-trash'></i></button>";
            $dataRow['image'] = '<img src="../' . $dataRow['image'] . '" style = "width:150px; height:auto;">';
            $dataRow['action'] = $editBtn . " " . $deleteBtn;

            return $dataRow;
        });

    } else if ($from == 'add_new_game') {

        $gameImagePath = "";
        $logs = "";

        // create objects
        $uploadFile = new UploadFile($con);
        $uploadFile->setPostName("game_image");
        
        $filePath = "../../GamesImages";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }
            
        // Save tournament image to memory
        $uploadStatus = $uploadFile->uploadFile($filePath, $_FILES['game_image']);

        if ($uploadStatus['status'] == 1) {
            $gameImagePath = str_replace('../', '', $uploadStatus['filepath']);
        } else {
            $logs .= "Unable to save game image\n\n";
        }

        $addNewGame = insertDataIntoTable("games", array("name" => $con->real_escape_string($_POST['game_name']), "image" => $gameImagePath, "how_to_get_id" => $con->real_escape_string($_POST['tutorials_link'])));

        if ($addNewGame['status'] == 1) {
            $addNewGame['msg'] = "Game Added";
        }

        $addNewGame['logs'] = $logs;

        echo json_encode($addNewGame);
    } else if ($from == 'delete_game') {

        $gameId = $con->real_escape_string($_POST['game_id']);

        $deleteGame = deleteDataFromTable("games", "id = $gameId");

        echo json_encode($deleteGame);
    } else if ($from == 'update_game') {

        $gameId = $con->real_escape_string($_POST['game_id']);

        // create objects
        $uploadFile = new UploadFile($con);
        $uploadFile->setPostName("game_image");
        
        $filePath = "../../GamesImages";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }
        
        // Save tournament image to memory
        $uploadStatus = $uploadFile->uploadFile($filePath, $_FILES['game_image']);

        if ($uploadStatus['status'] == 1) {
            $gameImagePath = str_replace('../', '', $uploadStatus['filepath']);

            // update game image
            updateDataIntoTable("games", array("image" => $gameImagePath), "id = $gameId");
        }

        $updateGame = updateDataIntoTable("games", array("name" => $con->real_escape_string($_POST['game_name']), "how_to_get_id" => $con->real_escape_string($_POST['how_to_get_game_id'])), "id = $gameId");
        echo json_encode($updateGame);
    } else if ($from == 'withdraw_action') {

        $withdrawId = $con->real_escape_string($_POST['withdraw_id']);
        $status = $con->real_escape_string($_POST['status']);
        $message = $con->real_escape_string($_POST['message']);
        $refundStatus = $con->real_escape_string($_POST['refund']);

        $updateWithdraw = updateDataIntoTable("withdraw_requests", array("status" => $status), "id = '$withdrawId'");
        $logs = "";

        if ($updateWithdraw['status'] == 1) {

            $withdrawDetails = getDataFromTable("withdraw_requests", "user_id, amount", "id = '$withdrawId'", true);
            
            if ($status == 'declined') {
                
                $title = "Withdraw Request Declined";
                    
                // refund user
                if ($refundStatus == 'true') {
    
                    $giveRefund = updateDataIntoTable("users", array("win_amount" => "win_amount + " . $withdrawDetails['amount']), "id = '" . $withdrawDetails['user_id'] . "'");
                    if ($giveRefund['status'] == 0) {
                        $logs .= "Unable to refund to the user\n\n";
                    }
                    
                    // create transaction
                    insertDataIntoTable("transactions", array("user_id" => $withdrawDetails['user_id'], "title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date("H:i:s"), "amount" => "+" . $withdrawDetails['amount'], "reciept_no" => ""));
                }
            
            } else {
                $title = "Withdraw Request accepted";
                
                if($message == ""){
                    $message = "Your withdraw request has been accepted";
                }
            }
            
            // create objects
            $notificatios = new Notifications ($con);
    
            // sending push notification to the user
            $notificatios->pushNotificationToSingle($withdrawDetails['user_id'], $title, $message, "", "activity", "splash", "");
        }

        $updateWithdraw['logs'] = $logs;

        echo json_encode($updateWithdraw);
    } else if ($from == 'get_offline_payments') {

        // how many rows
        $draw = $con->real_escape_string($_POST['draw']);
        // start of
        $startPosition = $con->real_escape_string($_POST['start']);
        // how many rows
        $rowPerPage = $con->real_escape_string($_POST['length']);
        // Column index
        $columnIndex = $con->real_escape_string($_POST['order'][0]['column']);
        // Column name
        $columnName = $con->real_escape_string($_POST['columns'][$columnIndex]['data']);
        // asc or desc
        $columnSortOrder = $con->real_escape_string($_POST['order'][0]['dir']);
        // Search value
        $searchValue = $con->real_escape_string($_POST['search']['value']);

        // Create Object      
        $getSearchRows = new getSearchRows($con);

        // set column names to search or filter into
        $getSearchRows->setSearchColumns("userData.fullname");

        // ADD INNER JOINS
        $getSearchRows->addInnerJoin("INNER JOIN (SELECT id AS userId, fullname FROM users) AS userData");
        $getSearchRows->setInnerJoinONCondition(" ON (userData.userId = user_id)");

        // set select column names
        $getSearchRows->setSelectColumns("*");

        //set table name which from get data
        $getSearchRows->setTableName("payment_ss");

        echo $getSearchRows->getSearchData($draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, function ($dataRow) {

            $accept = "<button class='btn btn-sm btn-success acceptBtn' data-id='" . $dataRow['id'] . "' data-user_id='" . $dataRow['user_id'] . "' ><i class='fa fa-check'></i></button> ";
            $decline = "<button class='btn btn-sm btn-danger declineBtn' data-id='" . $dataRow['id'] . "' data-user_id='" . $dataRow['user_id'] . "'><i class='fa fa-times'></i></button> ";

            if ($dataRow['status'] == 'pending') {
                $dataRow['status'] = "<a href='#' type='button' class='m-1 badge  badge-warning'>Pending</a>";
            } else if ($dataRow['status'] == 'approved') {
                $dataRow['status'] = "<a href='#' type='button' class='m-1 badge  badge-success'>Approved</a>";
            } else {
                $dataRow['status'] = "<a href='#' type='button' class='m-1 badge  badge-danger'>Declined</a>";
            }

            $dataRow['date_time'] = $dataRow['date'] . " " . $dataRow['time'];
            $dataRow['screenshot'] = '<img src="../' . $dataRow['ss_file'] . '" style = "width:200px; height:auto;">';
            $dataRow['action'] = $accept . " " . $decline;

            return $dataRow;
        });
    } else if ($from == 'update_payment_status') {

        $paymentId = $con->real_escape_string($_POST['payment_id']);
        $status = $con->real_escape_string($_POST['status']);
        $userId = $con->real_escape_string($_POST['user_id']);

        $response = array();

        $updatePayments = updateDataIntoTable("payment_ss", array("status" => $status), "id = '$paymentId'");

        if ($updatePayments['status'] == 1) {
            if ($status == 'approved') {

                $amount = $con->real_escape_string($_POST['deposit_amount']);
                updateDataIntoTable("users", array("deposit_amount" => "deposit_amount + $amount"), "id = '$userId'");

                $title = "Money added to your wallet";
                $message = "Your request for adding money has been accepted.";

                // make transaction
                insertDataIntoTable("transactions", array("user_id" => $userId, "title" => $title, "message" => $message, "date" => date("Y-m-d"), "time" => date('H:i:s'), "amount" => $amount, "reciept_no" => ""));

                // send notification
                $notifications = new Notifications($con);
                $notifications->pushNotificationToSingle($userId, $title, $message, "", "activity", "splash", "");
            } else {
                $reason = $con->real_escape_string($_POST['reason']);

                if ($reason != '') {

                    // send notification
                    $notifications = new Notifications($con);
                    $notifications->pushNotificationToSingle($userId, "Payment Declined", $reason, "", "activity", "splash", "");
                }
            }
        }

        echo json_encode($response);

    }
}

$con->close();
?>
