<?php include_once("APIs/session.php");

include_once("../dbcon.php");
include_once("../data-functions.php");
include_once("../global-functions.php");

$gamesArray = getDataFromTable("games", "*", "1", null);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Tournament</title>

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
                            <h1 class="m-0">Add New Tournament</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Add New Tournament</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <section class="content">
                <div class="container-fluid">
                   
                    <!---Content Card (Start)-->
                    <div class="card card-primary">
                       
                        <!---Add New Tournament Form-->
                        <form id="add_tournament_form" method="POST">
                            <div class="card-body">
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Select Game *</label>
                                        <input required type="hidden" class="form-control" name="from" value="add_new_tournament">
                                        <input required type="hidden" class="form-control" id = "prizes_count" name="prizes_count" value="1">
                                        <select required name="t_game_id" class="form-control m-b">
                                            <?php
                                                foreach($gamesArray as $game){
                                                    echo "<option value='".$game['id']."'>".$game['name']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Title *</label>
                                        <input required type="text" class="form-control" name="t_title">
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
                                        <input required type="file" name="t_image[]" class="file" accept="image/*">
                                        <div class="ml-2 col-sm-6">
                                            <img src="" id="preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Map </label>
                                        <input type="text" class="form-control" name="t_map">
                                        <p style="color: blue; font-size:14px;">Leave this field empty if you don't want this option to be shown in the App.</p>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Select Type *</label>
                                        <select required name="t_type" class="form-control m-b">
                                            <option value='solo'>Solo</option>
                                            <option value='duo'>Duo</option>
                                            <option value='squad'>Squad</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Select Mode *</label>
                                        <select required name="t_mode" class="form-control m-b">
                                            <option value='no_mode'>No Mode</option>
                                            <option value='tpp'>TPP</option>
                                            <option value='fpp'>FPP</option>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Entry Fees *</label>
                                        <input required type="number" class="form-control" name="t_entry_fees">
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Total Prize Pool *</label>
                                        <input required type="number" class="form-control" name="t_prize_pool">
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Per Kill *</label>
                                        <input required type="number" class="form-control" name="t_per_kill" value = "-1">
                                        <p style="color: blue; font-size:14px;">Use -1 value if you don't want this option to be shown in the App.</p>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>From Bonus *</label>
                                        <input required type="number" class="form-control" name="t_from_bonus">
                                        <p style="color: blue; font-size:14px;">Can user use his bonus amount to participate in this tournament in case of insufficient balance.</p>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Total Players *</label>
                                        <input required type="number" class="form-control" name="t_total_players">
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Prize Distributions *</label>
                                        <a class="float-right" onclick = "addNewPrize()">Add New Prize</a>
                                        <table class="table">
                                            <tbody id="prize_details">
                                                <tr>
                                                    <td><input required type="number" class="form-control" name="start_rank_1" placeholder = "Start Rank"></td>
                                                    <td><input required type="number" class="form-control" name="end_rank_1" placeholder = "End Rank"></td>
                                                    <td><input required type="number" class="form-control" name="amount_1" placeholder = "Amount"></td>
                                                    <td><a type='button' class='fas fa-times-circle'><i class='fa fa-close'></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <p style="color: blue; font-size:14px;">If you want distribution prize to be only for rank 1 then add 1 in start and end ranks.</p>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                      <label>Tournament Schedule *</label>
                    
                                      <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input required type="text" class="form-control float-right" id="t_schedule" name = "t_schedule">
                                      </div>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Tournament Details *</label>
                                        <textarea required class="form-control" name="t_details" rows="3">Tournament Details 1, Tournament Details 2, ......</textarea>
                                        <p style="color: blue; font-size:14px;">Please enter tournament details separated with comma(,).</p>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button class="btn btn-primary" type='submit' id='submitBtn'>
                                        Add New Tournament
                                    </button>
                                </div>
                        </form>
                    </div>
                </div>
                <!---Content Card (End)-->
            </section>

            <!-- ADD FOOTER -->
            <?php include("pages/layout/fixed-footer.php")?>

        </div>
        <!-- ./wrapper -->

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
        <script src="dist/js/add-tournament.js"></script>
        <!--- My Functions-->
        <script src="dist/js/global-functions.js"></script>
        
</body>
</html>
