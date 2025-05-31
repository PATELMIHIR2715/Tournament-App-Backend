<?php include_once("APIs/session.php");

// Include Files
include_once("../dbcon.php");
include_once("../data-functions.php");
include_once("../global-functions.php");

// Getting Main Data
$mainData = getDataFromTable("main_data", "*", "id = 1", true);

// get payment gateways
$paymentGateways = getDataFromTable("payment_gateways");

// get admin username and password
$adminDetails = getDataFromTable("admins", "*", "id = 1", true);

// selected gateway details
$selectedGateway = null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournament App | Main Data</title>

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
                            <h1 class="m-0">Main Data</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Main Data</li>
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
                        <form id="main_data_form" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <input type='hidden' name="from" value='update_main_data'>
                                        <label>Login Username *</label>
                                        <input required type="text" class="form-control" name="login_username" value='<?php echo $adminDetails['username'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Login Password *</label>
                                        <input required type="text" class="form-control" name="login_password" value='<?php echo $adminDetails['password'];?>'>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <input type='hidden' name="from" value='update_main_data'>
                                        <label>App Version *</label>
                                        <input required type="number" class="form-control" name="version" value='<?php echo $mainData['version'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Update Details *</label>
                                        <textarea required class="form-control" name="update_details" rows="3">Bugs Fixed,Improvements,New Features</textarea>
                                        <p style="color: blue; font-size:14px;">Please enter update details separated with comma(,).</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    
                                    <div class="form-group col">
                                        <label>App Link *</label>
                                        <input required type="text" class="form-control" name="app_link" value='<?php echo $mainData['app_link'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Website Link *</label>
                                        <input required type="text" class="form-control" name="website_link" value='<?php echo $mainData['website_link'];?>'>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <label>You Tube *</label>
                                        <input required type="text" class="form-control" name="youtube" value='<?php echo $mainData['youtube'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Instagram *</label>
                                        <input required type="text" class="form-control" name="instagram" value='<?php echo $mainData['instagram'];?>'>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Privacy Policy *</label>
                                        <input required type="text" class="form-control" name="privacy_policy" value='<?php echo $mainData['privacy_policy'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Terms Link *</label>
                                        <input required type="text" class="form-control" name="terms" value='<?php echo $mainData['terms'];?>'>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <label>Referral Amount *</label>
                                        <input required type="number" class="form-control" name="refer_amount" value='<?php echo $mainData['refer_amount'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Minimum Withdraw *</label>
                                        <input required type="number" class="form-control" name="min_withdraw" value='<?php echo $mainData['min_withdraw'];?>'>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <label>Registration Bonus Amount *</label>
                                        <input required type="number" class="form-control" name="registration_bonus" value='<?php echo $mainData['registration_bonus'];?>'>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Share Text *</label>
                                        <textarea required class="form-control" name="share_txt" rows="5"><?php echo $mainData['share_txt'];?></textarea>
                                        <p style="color: blue; font-size:14px;">Use '%referral_link%' where to want to add user's referral link.</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col">
                                        <label>Announcements *</label>
                                        <textarea required class="form-control" name="announcements" rows="5"><?php echo $mainData['announcements'];?></textarea>
                                        <p style="color: blue; font-size:14px;">Please enter announcements separated with comma(,).</p>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <label>Offline Payment Instructions *</label>
                                        <textarea required class="form-control" name="offline_payment_instructions" rows="8"><?php echo $mainData['offline_payment_instructions'];?></textarea>
                                    </div>
                                </div>
                                
                                <div class = "row">
                                    <div class="form-group col">
                                        <label>Select Payment Gateway *</label>
                                        <select required name="gateway_id" id="payment_gateway" class="form-control m-b">
                                            <?php
                                                foreach($paymentGateways as $paymentGateway){
                                                    if($paymentGateway['id'] == $mainData['gateway_id']){
                                                        
                                                        $selectedGateway = $paymentGateway;
                                                        
                                                        echo "<option selected value='".$paymentGateway['id']."'>".$paymentGateway['name']."</option>";
                                                    }
                                                    else{
                                                        echo "<option value='".$paymentGateway['id']."'>".$paymentGateway['name']."</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col">
                                        <?php
                                            foreach($paymentGateways as $paymentGateway){
                                                
                                                $display = "none";
                                                
                                                if($paymentGateway['id'] == $mainData['gateway_id']){
                                                    $display = "";
                                                }
                                                
                                                ?>
                                                
                                                <div id = "payment_div_<?php echo $paymentGateway['id']; ?>" style = "display : <?php echo $display; ?>">
                                                    <label>Key*</label>
                                                    <input type="text" class="form-control" name="key_value_<?php echo $paymentGateway['id']; ?>" value='<?php echo $paymentGateway['key_value']; ?>'>
                                                    <br>
                                                    <label>Salt / ID*</label>
                                                    <input type="text" class="form-control" name="salt_value_<?php echo $paymentGateway['id']; ?>" value='<?php echo $paymentGateway['salt_value']; ?>'>
                                                </div>
                                                
                                                <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary" type='submit' id='save'>
                                    Submit
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
    
    <script>
    
        // selected payment gateway id
        var gatewayId = 1;
        
    </script>
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap-main.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!--- Users JavaScript-->
    <script src="dist/js/main-data.js"></script>
    <!--- My Functions-->
    <script src="dist/js/global-functions.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

</body>
</html>
