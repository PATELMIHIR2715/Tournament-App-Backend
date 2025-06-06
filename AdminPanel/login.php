<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournament App | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/fonts/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                
                <!---Sign In Form (Start)-->
                <form method="post" id='login_user'>
                    
                    <div class="input-group mb-3">
                        <input type="hidden" name="from" value="login_user">
                        <input type="text" class="form-control" name='username' placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name='password' placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col">
                            <button type="submit" id='sign_in_btn' class="btn btn-primary">Sign In</button>
                        </div>
                    </div>
                </form>
                <!---Sign In Form (End)-->
                
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min2.js"></script>
    <script src="plugins/bootstrap/js/bootstrap-main.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <!--- Login JavaScript File-->
    <script src="dist/js/login.js"></script>
    <!--- My Functions-->
    <script src="dist/js/global-functions.js"></script>
</body>
</html>
