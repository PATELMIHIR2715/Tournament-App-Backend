<?php include_once("APIs/session.php");

// Getting tournament type (ongoing, upcoming, results)
$tournamentStatus = $_GET['t_status'];
$_SESSION['tournament_status'] = $tournamentStatus;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournaments</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/fonts/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">


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
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Tournaments</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Tournaments</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="data_rable" class="table nowrap table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Action</th>
                                                <th>Image</th>
                                                <th>Win Screenshots</th>
                                                <th>Title</th>
                                                <th>Game</th>
                                                <th>Map</th>
                                                <th>Type</th>
                                                <th>Mode</th>
                                                <th>Entry Fees</th>
                                                <th>Prize Pool</th>
                                                <th>Per Kill</th>
                                                <th>From Bonus</th>
                                                <th>Schedule</th>
                                                <th>Total Players</th>
                                                <th>Joined Players</th>
                                                <th>Details</th>
                                                <th>Room Id</th>
                                                <th>Message</th>
                                                <th>YouTube Video</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!---Joined Players Modal-->
        <div class="modal fade" id="joined_players_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Joined Players</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>Id</th>
                              <th>Action</th>
                              <th>Joined On</th>
                              <th>Fullname</th>
                              <th>Game Username</th>
                            </tr>
                          </thead>
                          <tbody id="joined_players_table">
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Delete / Remove Player Modal-->
        <div class="modal fade" id="delete_player_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type='hidden' id='user_id'>
                        <input type='hidden' id='tournament_id'>
                        <h4 class="modal-title">Delete Player</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure want to remove the user?</p>
                        
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea class="form-control" id="remove_message" rows="3"></textarea>
                            <p style="color: blue; font-size:14px;">Please specify why you wanna remove this player so he will get notified about it.</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" id = 'removeNoRefund' class="btn btn-outline-danger" onclick = "removePlayer(0)">Remove & Don't Refund</button>
                        <button type="button" id = 'removeRefund' class="btn btn-outline-danger" onclick = "removePlayer(1)">Remove & Refund</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Tournament Details Modal-->
        <div class="modal fade" id="details_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tournament Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class = "row">
                            <div class="form-group col">
                                <textarea required class="form-control" id="t_details" rows="6"></textarea>
                                <p style="color: blue; font-size:14px;">Please enter tournament details separated with comma(,).</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" id = 'updateDetailsBtn' class="btn btn-outline-danger">Update Details</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Tournament Actions Modal like Cancel, Prize Distrinution, Send Room Id, etc-->
        <div class="modal fade" id="actions_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tournament Actions</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class = "row">
                            <div class="form-group col">
                                <button type="button" id = 'cancelTournamentBtn' class="btn btn-outline-danger form-control">Cancel Tournament</button>
                            </div>
                        </div>
                        <div class = "row">
                            <div class="form-group col">
                                <button type="button" id = 'prizeDistributeBtn' class="btn btn-outline-success form-control">Distribute Prize</button>
                            </div>
                        </div>
                        
                        <div class = "row">
                            <div class="form-group col">
                                <button type="button" id = 'roomIdBtn' class="btn btn-outline-primary form-control">Send Room Id</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Prize Distribution Modal-->
        <div class="modal fade" id="distribute_prize_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Distribute Prize</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <p>Info : You just need to assign the rank to the user. Prizes will automatically be distributed according to the rank.</p>
                        <!--<div class = "row">-->
                        <!--    <input type="text" class="form-control" id = "cancel_reason" style="width: 30%;" placeholder = "Search Player">-->
                        <!--    <button style="margin-left:10px;" type="button" id = 'searchPlayerBtn' class="btn btn-success">Search Player</button>-->
                        <!--</div>-->
                        
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>Id</th>
                              <th>Rank</th>
                              <th>Kills</th>
                              <th>Won Amount</th>
                              <th>Fullname</th>
                              <th>Game Username</th>
                            </tr>
                          </thead>
                          <tbody id="prizes_table">
                          </tbody>
                        </table>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" id = 'distributeNowBtn' class="btn btn-outline-success">Distribute Now</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Cancel Tournament Modal-->
        <div class="modal fade" id="cancel_tournament_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Cancel Tournament</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure want to cancel this tournament?.</p>
                        <p>Entry fees will automatically be refunded to joined players.</p>
                        
                        <div class="form-group col">
                            <label>Reason *</label>
                            <input type="text" class="form-control" id = 'cancel_reason'>
                            <p style="color: blue; font-size:14px;">Please type the reason here to send message to the joined players.</p>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">No</button>
                        <button type="button" id = 'yesBtn' class="btn btn-outline-danger">Yes</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!---Send Room Id Modal-->
        <div class="modal fade" id="room_id_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Room Id</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col">
                            <label>Room Id *</label>
                            <input type="text" class="form-control" id = 't_room_id'>
                            <p style="color: blue; font-size:14px;">Please enter Room Id carefully.</p>
                        </div>
                        
                        <div class="form-group col">
                            <label>Message *</label>
                            <textarea placeholder = "Example : Password : 798654158" required class="form-control" id="t_message" rows="3"></textarea>
                            <p style="color: blue; font-size:14px;">This message will show to the users along with Room Id. Eg. Don't share room id etc. You can also add password for the Room Id if any. </p>
                        </div>
                        
                        <div class="form-group col">
                            <label>YouTube Video</label>
                            <input type="text" class="form-control" id = 't_youtube_link'>
                            <p style="color: blue; font-size:14px;">If you are going to live this tournament then paste URL here.</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">No</button>
                        <button type="button" id = 'sendRoomIdBtn' class="btn btn-outline-success">Send Room Id</button>
                    </div>
                </div>
            </div>
        </div>

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
    <!--- Users JavaScript-->
    <script src="dist/js/tournaments.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!--- My Functions-->
    <script src="dist/js/global-functions.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

</body>
</html>
