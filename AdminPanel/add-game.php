<?php include_once("APIs/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Game</title>

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
                            <h1 class="m-0">Add New Game</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Add New Game</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <section class="content">
                <div class="container-fluid">
                   
                    <!---Content Card (Start)-->
                    <div class="card card-primary">
                       
                        <!---Add New Game Form-->
                        <form id="add_game_form" method="POST">
                            <div class="card-body">

                                <div class="form-group col">
                                    <label>Name * </label>
                                    <input type="hidden" class="form-control" name="from" value="add_new_game">
                                    <input required type="text" class="form-control" name="game_name">
                                </div>
                                
                                <div class="form-group col">
                                    <label>Image *</label>
                                    <div class="input-group my-3">
                                        <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                        <div class="input-group-append">
                                            <button type="button" class="browse btn btn-primary">Browse...</button>
                                        </div>
                                    </div>
                                    <input required type="file" name="game_image[]" class="file" accept="image/*">
                                    <div class="ml-2 col-sm-6">
                                        <img src="" id="preview" class="img-thumbnail">
                                    </div>
                                </div>
                                
                                <div class="form-group col">
                                    <label>Tutorials link * </label>
                                    <input required type="text" class="form-control" name="tutorials_link">
                                </div>

                                <div class="card-footer">
                                    <button class="btn btn-primary" type='submit' id='submitBtn'>
                                        Add New Game
                                    </button>
                                </div>
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
        <script src="dist/js/add-game.js"></script>
        <!--- My Functions-->
        <script src="dist/js/global-functions.js"></script>
        
</body>
</html>
