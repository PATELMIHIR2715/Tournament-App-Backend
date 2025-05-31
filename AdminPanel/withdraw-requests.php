<?php include_once("APIs/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Withdraw Requests</title>

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


        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Withdraw Requests</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Withdraw Requests</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="data_rable" class="table nowrap table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Status</th>
                                                <th>Profile Name</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Mobile Number</th>
                                                <th>Account No.</th>
                                                <th>Full Name</th>
                                                <th>Bank Name</th>
                                                <th>IFSC Code</th>
                                                <th>Date & Time</th>
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
        
        <!---Withdraw Request Modal-->
        <div class="modal fade" id="withdraw_action_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="main_data_form" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title">Withdraw Action</h4>
                        </div>
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <label>Message *</label>
                                <textarea class="form-control" id="message" rows="3">Your withdraw request has been accepted</textarea>
                            </div>
                            
                            <div class="form-group col">
                                <label>Refund *</label>
                                <select required id="refund_status" class="form-control m-b">
                                    <option value='true'>Yes</option>
                                    <option value='false'>No</option>
                                </select>
                                <p style="color: blue; font-size:14px;">Choose in case of declined withdraw request.</p>
                            </div>
                            
                            <div class = "row">
                                <div class="form-group col">
                                    <button type="button" id = 'declineBtn' class="btn btn-outline-danger form-control">Decline</button>
                                </div>
                                <div class="form-group col">
                                    <button type="button" id = 'acceptBtn' class="btn btn-outline-success form-control">Accept</button>
                                </div>
                            </div>
                          
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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
    <script src="dist/js/withdraw-requests.js"></script>
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
