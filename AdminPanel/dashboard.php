<?php include_once("APIs/session.php");

include_once("../dbcon.php");
include_once("../data-functions.php");
include_once("../global-functions.php");

date_default_timezone_set("Asia/Calcutta");

// getting today date and yesterday date, and current time
$todayDate = date('Y-m-d');
$yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($todayDate)));

 $totalUsers = sizeof(getDataFromTable("users", "id", "1"));
 $todayRegister = sizeof(getDataFromTable("users", "id", "register_date LIKE '%$todayDate%'"));
 $yesterdayRegister = sizeof(getDataFromTable("users", "id", "register_date LIKE '%$yesterdayDate%'"));
 $todayActive = sizeof(getDataFromTable("users", "id", "login_date LIKE '%$todayDate%'"));
 $yesterdayActive = sizeof(getDataFromTable("users", "id", "login_date LIKE '%$yesterdayDate%'"));

 $ongoingTournaments = sizeof(getDataFromTable("tournaments", "id", "status = 'live'"));
 $upcomingTournaments = sizeof(getDataFromTable("tournaments", "id", "status = 'available'"));
 $tournamentResults = sizeof(getDataFromTable("tournaments", "id", "status = 'completed'"));

 $games = sizeof(getDataFromTable("games", "id"));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERB Tournament App | Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/fonts/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
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
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
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
                        
                        <!--Total Users -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $totalUsers;?></h3>
                                    <p style="font-weight:600;">Total Users</p>
                                    <div class="row" style="margin-bottom: -18px;">
                                        <div class="col">
                                            <p style="text-align: center;color: #00000069;font-size: 14px;font-weight: 600;">Today : <?php echo $todayRegister;?></p>
                                        </div>
                                        <div class="col">
                                            <p style="text-align: center;color: #00000069;font-size: 14px;font-weight: 600;">Yesterday : <?php echo $yesterdayRegister;?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!--Today's Active Users -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $todayActive;?></h3>
                                    <p style="font-weight:600;">Today's Active</p>
                                </div>
                                <a href="users.php?type=today_login" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!--Yesterday's Active Users -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $yesterdayActive;?></h3>
                                    <p style="font-weight:600;">Yesterday's Active</p>
                                </div>
                                <a href="users.php?type=yesterday_login" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!--Total Available games -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $games;?></h3>
                                    <p style="font-weight:600;">Games</p>
                                </div> 
                                <a href="games.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!--On-going tournaments -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $ongoingTournaments;?></h3>
                                    <p style="font-weight:600;">Ongoing Tournaments</p>
                                </div>
                                <a href="tournaments.php?t_status=live" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!--Upcoming tournaments -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $upcomingTournaments;?></h3>
                                    <p style="font-weight:600;">Upcoming Tournaments</p>
                                </div>
                                <a href="tournaments.php?t_status=available" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!-- Tournament Results -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $tournamentResults; ?></h3>
                                    <p style="font-weight:600;">Tournament Results</p>
                                </div>
                                <a href="tournaments.php?t_status=completed" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ADD FOOTER -->
        <?php include("pages/layout/fixed-footer.php")?>

    </div>
    
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min2.js"></script>
    <script src="plugins/bootstrap/js/bootstrap-main.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
</body>
</html>
