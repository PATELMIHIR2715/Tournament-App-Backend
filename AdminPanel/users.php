<?php include_once("APIs/session.php");
$type = "";
$userId = "";

// to filter users according to today or yesterday (from dashboard)
if(isset($_GET['type'])){
    $type = $_GET['type'];
}

if(isset($_GET['id'])){
    $userId = $_GET['id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournaments App | Users</title>
    
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
                            <h1 class="m-0">Users</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Users</li>
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
                                    <table id="users_data_rable" class="table nowrap table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Action</th>
                                                <th>Name</th>
                                                <th>Transactions</th>
                                                <th>Profile Pic</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Password</th>
                                                <th>Register Date</th>
                                                <th>Login Date</th>
                                                <th>Referral Code</th>
                                                <th>Sponsor</th>
                                                <th>Bonus Amount</th>
                                                <th>Deposit Amount</th>
                                                <th>Winning Amount</th>
                                                <th>Lifetime Amount</th>
                                                <th>Played Tournaments</th>
                                                <th>Won Tournaments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        
        <!---Push Notification Modal-->
        <div class="modal fade" id="push_noti_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    
                    <form id="send_notification_form" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title">Send Push Notification</h4>
                        </div>
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <label>Title *</label>
                                <input required type="hidden" class="form-control" name="from" value = "send_notification">
                                <input required type="hidden" class="form-control" name="to" value = "single">
                                <input required type="hidden" class="form-control" id = "user_id" name="user_id" value = "">
                                <input required type="text" class="form-control" name="title">
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
                                <input type="text" class="form-control" required name="destination" placeholder="Enter Destination">
                            </div>
                                
                            <div class="form-group">
                                <label>Payload</label>
                                <textarea class="form-control" name="payload" rows="3"></textarea>
                            </div>
                            
                            <!--<div class="form-group">-->
                            <!--    <label>Title *</label>-->
                            <!--    <input required type="hidden" class="form-control" name="from" value = "send_notification">-->
                            <!--    <input required type="hidden" class="form-control" name="to" value = "single">-->
                            <!--    <input required type="hidden" class="form-control" id = "user_id" name="user_id" value = "">-->
                            <!--    <input required type="text" class="form-control" name="title">-->
                            <!--</div>-->

                            <!--<div class="form-group">-->
                            <!--    <label>Message *</label>-->
                            <!--    <textarea required class="form-control" name="body" rows="3"></textarea>-->
                            <!--</div>-->

                            <!--<div class="form-group">-->
                            <!--    <label>Image</label>-->
                            <!--    <div id="msg"></div>-->
                            <!--        <input type="file" name="img[]" class="file" accept="image/*">-->
                            <!--        <div class="input-group my-3">-->
                            <!--            <input type="text" class="form-control" disabled placeholder="Upload File" id="file">-->
                            <!--            <div class="input-group-append">-->
                            <!--                <button type="button" class="browse btn btn-primary">Browse...</button>-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--    <div class="ml-2 col-sm-6">-->
                            <!--        <img src="" id="preview" class="img-thumbnail">-->
                            <!--    </div>-->
                            <!--</div>-->

                            <!--<div class="form-group">-->
                            <!--    <label>URL</label>-->
                            <!--    <input type="text" class="form-control" name="des_url" placeholder="Enter Destination URL">-->
                            <!--</div>-->

                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id='save' class="btn btn-outline-success">Send Notification</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        <!---Update wallet Modal-->
        <div class="modal fade" id="update_wallet_modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update User Wallet</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col">
                            <label>Deposit Amount *</label>
                            <input type="number" class="form-control" id = 'deposit_amount'>
                        </div>
                        
                        <div class="form-group col">
                            <label>Winning Amount *</label>
                            <input type="number" class="form-control" id = 'winning_amount'>
                        </div>
                        
                        <div class="form-group col">
                            <label>Bonus Amount *</label>
                            <input type="number" class="form-control" id = 'bonus_amount'>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" id = 'updateWalletNowBtn' class="btn btn-outline-success">Update Now</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ADD FOOTER -->
        <?php include("pages/layout/fixed-footer.php")?>

    </div>
    <script>
        var userId = '<?php echo $userId;?>';
        var type = '<?php echo $type;?>';
    </script>
    
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap-main.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!--- Users JavaScript-->
    <script src="dist/js/users.js"></script>
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
