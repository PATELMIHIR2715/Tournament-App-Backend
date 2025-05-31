<?php

class getSearchRows {

    public $con;
    public $columnNames;
    public $tableName;
    public $from;
    public $selectCondition;
    public $objects;
    public $curerntDate;
    public $selectColumns;
    public $innerJoins;
    public $ONCondition;
    public $position = 0;
    
    public function __construct( $con ) {
        $this->con = $con;
        $this->selectCondition = "1";
        $this->selectColumns = "*";
        $this->curerntDate = date('Y-m-d');
        $this->innerJoins = "";
        $this->ONCondition = "";
        $this->position = 0;
    }

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

    function getSelectCondition( $selectCondition ) {
        $this->selectCondition = $selectCondition;
    }

    function setTableName( $tableName ) {
        $this->tableName = $tableName;
    }

    function getSearchData( $draw, $startPosition, $rowPerPage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $getAdditionalData = null ) {

        $data = array();
        $sno = $startPosition;

        ## Total number of records without filtering
        $stmt = "SELECT id FROM $this->tableName  $this->innerJoins  $this->ONCondition WHERE $this->selectCondition "; 
        $results = $this->con->query( $stmt );
        $totalRecords = $results->num_rows;

        ## Total number of records with filtering
        $stmt = "SELECT id FROM $this->tableName $this->innerJoins  $this->ONCondition WHERE $this->selectCondition  " . $this->makeSearchQuery( $searchValue );    
        $results = $this->con->query( $stmt );
        $totalFilteredRecord = $results->num_rows;
        if ( $rowPerPage == "-1" ) {
            $rowPerPage = $totalRecords;
        }

        ## fetch record
        $stmt = "SELECT $this->selectColumns FROM $this->tableName $this->innerJoins  $this->ONCondition WHERE $this->selectCondition  " . $this->makeSearchQuery( $searchValue ) . "  ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT $rowPerPage  OFFSET $startPosition";
        $results = $this->con->query( $stmt );

        while ( $getRows = $results->fetch_assoc() ) {
            
            // add additional data like buttons ( html or css )
            if($getAdditionalData != null){
                $getRows = $getAdditionalData( $getRows );
                
                if($getRows == null){
                    continue;
                }
            }
            
            $getRows['id'] = ( $sno + 1 );
            $data[] = $getRows;
            $sno++;

        }

        ## Response
        $response = array( "draw" => intval( $draw ), "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalFilteredRecord, "aaData" => $data );
        return json_encode( $response );

    }

    function makeSearchQuery( $searchValue ) {

        if ( $searchValue != '' ) {

            $searchQuery = " AND (";

            if ( sizeof( $this->columnNames )>0 ) {
                for ( $i = 0; $i<sizeof( $this->columnNames );
                     $i++ ) {
                    if ( $i == 0 ) {
                        $searchQuery .= $this->columnNames[$i]." LIKE '%$searchValue%'";
                    } else {
                        $searchQuery .= " OR ".$this->columnNames[$i]." LIKE '%$searchValue%'";
                    }
                }
            }
            $searchQuery .= ")";
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
            
            // get winning screenshots
            $selectSS = "SELECT * FROM win_screenshots WHERE tournament_id = '".$getRows['id']."'";
            $ssRwsults = $this->con->query($selectSS);
            $ssData = "";
            
            while($ssROws = $ssRwsults->fetch_assoc()){
                
                if($ssData == ''){
                    $ssData = '<img src="../'.$ssROws['ss_file'].'" style = "width:100px; height:auto;">';
                }
                else{
                    $ssData = $ssData.'<br><img src="../'.$ssROws['ss_file'].'" style = "width:100px; height:auto;">';
                }
                
            }
            
            $getRows['win_ss'] = $ssData;
            $getRows['joined_players'] = $joinedPlayers;
            $getRows['details'] = $details;
            $getRows['gameName'] = "<a href='#' type='button' class='m-1 badge  badge-primary'>".$getRows['gameName']."</a>";
            $getRows['schedule'] = $from."<br>To<br>".$to;
            $getRows['image'] = '<img src="'.$getRows['image'].'" style = "width:100px; height:auto;">';
            $getRows['action'] = $editBtn." ".$tournamentActions;

        }
        
        else if($this->from == 'users'){
            
            
        }
        
        else if($this->from == 'withdraw_requests'){
            
            
            
        }
        
        else if($this->from == 'transactions'){
            
            $fullName = "<a href='users.php?id=".$getRows['user_id']."' type='button' class='m-1 badge  badge-success'>".$getRows['fullname']."</a>";
            
            $getRows['fullname'] = $fullName;
            $getRows['date_time'] = $getRows['date']." - ".$getRows['time'];
        }
        
        else if($this->from == 'games'){
            
            
        }
        
        else if($this->from == 'payment_ss'){
            
            $accept = "<button class='btn btn-sm btn-success acceptBtn' data-id='".$getRows['id']."' data-user_id='".$getRows['user_id']."' ><i class='fa fa-check'></i></button> ";
            $decline = "<button class='btn btn-sm btn-danger declineBtn' data-id='".$getRows['id']."' data-user_id='".$getRows['user_id']."'><i class='fa fa-times'></i></button> ";
            
            if($getRows['status'] == 'pending'){
                $getRows['status'] = "<a href='#' type='button' class='m-1 badge  badge-warning'>Pending</a>";
            }
            else if($getRows['status'] == 'approved'){
                $getRows['status'] = "<a href='#' type='button' class='m-1 badge  badge-success'>Approved</a>";
            }
            else {
                $getRows['status'] = "<a href='#' type='button' class='m-1 badge  badge-danger'>Declined</a>";
            }
            
            $getRows['date_time'] = $getRows['date']." ".$getRows['time'];
            $getRows['screenshot'] = '<img src="../'.$getRows['ss_file'].'" style = "width:200px; height:auto;">';
            $getRows['action'] = $accept." ".$decline;
        }
        
        
        $this->position++;
            
        return $getRows;
    }

}
?>
