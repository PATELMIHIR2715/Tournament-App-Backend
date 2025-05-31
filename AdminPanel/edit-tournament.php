<?php include_once("APIs/session.php");

include_once("../dbcon.php");
include_once("APIs/tournments.php");
include_once("../data-functions.php");
include_once("../global-functions.php");

$tournamentId = $_GET['id'];

// create objects
$tournaments = new Tournaments($con);

// tournament details
$tournamentData = getDataFromTable("tournaments", "*", "id = '$tournamentId'", true);

// tournament schedule
$tournamentScheduleData = getDataFromTable("tournament_schedule", "*", "tournament_id = '$tournamentId'", true);
$scheduleString = $tournamentScheduleData['start_date_time']." - ".$tournamentScheduleData['end_date_time'];

// tournament details
$tournamentDetails = $tournaments->tournamentDetailsToString(json_decode($tournamentData['details'], true));

// select prize distributions
$tournamentPrizeData = getDataFromTable("tournament_prizes", "*", "tournament_id = '$tournamentId'");

// select all games 
$gamesArray = getDataFromTable("games");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Tournament</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/fonts/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- TOP NAVIGATION BAR -->
        <?php include("pages/layout/top-nav.php")?>


        <!-- ADD NAVIGATION BAR -->
        <?php include("pages/layout/fixed-sidebar-custom.php")?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Tournament</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Tournament</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <section class="content">
                <div class="container-fluid">

                    <div class="card card-primary">
                        <form id="add_tournament_form" method="POST">
                            <div class="card-body">
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Select Game *</label>
                                        <input required type="hidden" class="form-control" name="from" value="update_tournament">
                                        <input required type="hidden" class="form-control" name="tournament_id" value="<?php echo $tournamentId;?>">
                                        <input required type="hidden" class="form-control" name="old_image" value="<?php echo $tournamentData['image'];?>">
                                        <input required type="hidden" class="form-control" id = "prizes_count" name="prizes_count" value="">
                                        <select required name="t_game_id" class="form-control m-b">
                                            <?php
                                                foreach($gamesArray as $game){
                                                    if($tournamentData['game_id'] == $game['id']){
                                                        echo "<option selected value='".$game['id']."'>".$game['name']."</option>";
                                                    }
                                                    else{
                                                        echo "<option value='".$game['id']."'>".$game['name']."</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Title *</label>
                                        <input required type="text" class="form-control" name="t_title" value = "<?php echo $tournamentData['name'];?>">
                                    </div>
                                </div>

                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Image *</label>
                                        <div class="input-group my-3">
                                            <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                            <div class="input-group-append">
                                                <button type="button" class="browse btn btn-primary">Browse...</button>
                                            </div>
                                        </div>
                                        <input type="file" name="t_image[]" class="file" accept="image/*">
                                        <div class="ml-2 col-sm-6">
                                            <img src= "../<?php echo $tournamentData['image'];?>" id="preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Map </label>
                                        <input type="text" class="form-control" name="t_map" value = "<?php echo $tournamentData['map'];?>">
                                        <p style="color: blue; font-size:14px;">Leave this field empty if you don't want this option to be shown in the App.</p>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Select Type *</label>
                                        <select required name="t_type" class="form-control m-b">
                                            <?php
                                                if($tournamentData['type'] == 'solo'){
                                                    echo '<option selected value="solo">Solo</option>';
                                                }
                                                else{
                                                    echo '<option value="solo">Solo</option>';
                                                }
                                                
                                                if($tournamentData['type'] == 'duo'){
                                                    echo '<option selected value="duo">Duo</option>';
                                                }
                                                else{
                                                    echo '<option value="duo">Duo</option>';
                                                }
                                                
                                                if($tournamentData['type'] == 'squad'){
                                                    echo '<option selected value="squad">Squad</option>';
                                                }
                                                else{
                                                    echo '<option value="squad">Squad</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Select Mode *</label>
                                        <select required name="t_mode" class="form-control m-b">
                                            <?php
                                                if($tournamentData['mode'] == 'no_mode'){
                                                    echo "<option selected value='no_mode'>No Mode</option>";
                                                }
                                                else{
                                                    echo "<option value='no_mode'>No Mode</option>";
                                                }
                                                if($tournamentData['mode'] == 'tpp'){
                                                    echo "<option selected value='tpp'>TPP</option>";
                                                }
                                                else{
                                                    echo "<option value='tpp'>TPP</option>";
                                                }
                                                
                                                if($tournamentData['mode'] == 'fpp'){
                                                    echo "<option selected value='fpp'>FPP</option>";
                                                }
                                                else{
                                                    echo "<option value='fpp'>FPP</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Entry Fees *</label>
                                        <input required type="number" class="form-control" name="t_entry_fees" value = "<?php echo $tournamentData['entry_fees'];?>">
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Total Prize Pool *</label>
                                        <input required type="number" class="form-control" name="t_prize_pool" value = "<?php echo $tournamentData['prize_pool'];?>">
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Per Kill *</label>
                                        <input required type="number" class="form-control" name="t_per_kill" value = "<?php echo $tournamentData['per_kill'];?>">
                                        <p style="color: blue; font-size:14px;">Use -1 value if you don't want this option to be shown in the App.</p>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>From Bonus *</label>
                                        <input required type="number" class="form-control" name="t_from_bonus" value = "<?php echo $tournamentData['from_bonus'];?>">
                                        <p style="color: blue; font-size:14px;">Can user use his bonus amount to participate in this tournament in case of insufficient balance.</p>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Total Players *</label>
                                        <input required type="number" class="form-control" name="t_total_players" value = "<?php echo $tournamentData['total_players'];?>">
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Prize Distributions *</label>
                                        <a class="float-right" onclick = "addNewPrize()">Add New Prize</a>
                                        <table class="table">
                                            <tbody id="prize_details">
                                                <?php
                                                    $prizeCount = 1;
                                                    
                                                    foreach($tournamentPrizeData as $tournamentPrize){
                                                        echo '<tr  id = "im'.$prizeCount.'">';
                                                        echo '<td><input value = "'.$tournamentPrize['start_rank'].'" required type="text" class="form-control" name="start_rank_'.$prizeCount.'" placeholder = "Start Rank"></td>';
                                                        echo '<td><input value = "'.$tournamentPrize['end_rank'].'" required type="text" class="form-control" name="end_rank_'.$prizeCount.'" placeholder = "End Rank"></td>';
                                                        echo '<td><input value = "'.$tournamentPrize['amount'].'" required type="number" class="form-control" name="amount_'.$prizeCount.'" placeholder = "Amount"></td>';
                                                        echo '<td><a type="button" onclick= "removeRow(' . $prizeCount . ')" class="fas fa-times-circle"><i class="fa fa-close"></i></a></td>';
                                                        echo '</tr>';
                                                        $prizeCount++;
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                      <label>Tournament Schedule *</label>
                    
                                      <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input required value = "<?php echo $scheduleString;?>" type="text" class="form-control float-right" id="t_schedule" name = "t_schedule">
                                      </div>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Tournament Details *</label>
                                        <textarea required class="form-control" name="t_details" rows="3"><?php echo $tournamentDetails;?></textarea>
                                        <p style="color: blue; font-size:14px;">Please enter tournament details separated with comma(,).</p>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button class="btn btn-primary" type='submit' id='submitBtn'>
                                        Update Now
                                    </button>
                                </div>
                        </form>
                    </div>

                </div><!-- /.container-fluid -->
            </section>

            <!-- ADD FOOTER -->
            <?php include("pages/layout/fixed-footer.php")?>

        </div>
        <!-- ./wrapper -->
        
        <!-- Data Variables -->
        <script>var prizesCount = <?php echo $prizeCount;?>;</script>
        
        <!-- jQuery -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="plugins/bootstrap/js/bootstrap-main.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.js"></script>
        <!-- daterangepicker -->
        <script src="plugins/moment/moment.min.js"></script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <!-- SweetAlert2 -->
        <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
        <!--- Login JavaScript File-->
        <script src="dist/js/edit-tournament.js"></script>
        <!--- My Functions-->
        <script src="dist/js/global-functions.js"></script>
</body>
</html>
