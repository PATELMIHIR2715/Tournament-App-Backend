<?php

include_once("../dbcon.php");
include_once("../global-functions.php");
include_once("../data-functions.php");

// getting fcm token from main_data
$firebaseKey = getDataFromTable("main_data", "fcm_token", "id = '1'", true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Send Notification</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/fonts/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
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
                            <h1 class="m-0">Send Notification</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Send Notification</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <div class="card card-primary">
                        <form id="send_notification" method="POST">
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label>Title *</label>
                                    <input required type="hidden" class="form-control" name="from" value = "send_notification">
                                    <input required type="hidden" class="form-control" name="to" value = "all">
                                    <input required type="text" class="form-control" name="title">
                                </div>
                                
                                <div class="form-group">
                                    <label>FCM Server Key</label>
                                    <input type="text" class="form-control" value = "<?php echo $firebaseKey; ?>" required name="fcm_token" placeholder="Enter FCM Token">
                                    <p style = 'color:orange;'>Note: Please replace above server key with your Firebase Token. How to get Firebase server key? <a href = "https://stackoverflow.com/questions/37427709/firebase-messaging-where-to-get-server-key#mainbar#answer-37427911">See Tutorials</a></p>
                                </div>
                            
                                <div class="form-group">
                                    <label>Message *</label>
                                    <textarea required class="form-control" name="body" rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Image</label>
                                    <div id="msg"></div>
                                        <input type="file" name="img[]" class="file" accept="image/*">
                                        <div class="input-group my-3">
                                            <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                            <div class="input-group-append">
                                                <button type="button" class="browse btn btn-primary">Browse...</button>
                                            </div>
                                        </div>
                                    <div class="ml-2 col-sm-6">
                                        <img src="" id="preview" class="img-thumbnail">
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group">
                                    <label>Click Action</label>
                                    <select required name="click_action" class="form-control m-b">
                                        <option value='url'>Open URL</option>
                                        <option value='activity'>Open Activity</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" class="form-control" required name="destination" placeholder="Enter Destination URL (If action is url)">
                                </div>
                                
                                <!--<div class="form-group">-->
                                <!--    <label>Payload</label>-->
                                <!--    <textarea class="form-control" name="payload" rows="3"></textarea>-->
                                <!--</div>-->
                            
                            <!-- /.card-body -->
                            </div>
                            
                            <div class="card-footer">
                                <button class="btn btn-primary" type='submit' id='save'>
                                    Send Notification
                                </button>
                            </div>
                        </form>
                    </div>

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
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
    <script src="dist/js/send-notifications.js"></script>
    <!--- My Functions-->
    <script src="dist/js/global-functions.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
</body>
</html>
