<?php session_start();

if (isset($_POST['from'])) {
    if ($_POST['from'] != 'login_user') {
        if (!isset($_SESSION['mainId'])) {
            session_destroy();
            echo "<script> window.location.href='login.php'</script>";
            die();
        } else {
            if (empty($_SESSION['mainId'])) {
                session_destroy();
                echo "<script> window.location.href='login.php'</script>";
                die();
            }
        }
    }
   
} else {
    if (!isset($_SESSION['mainId'])) {
        session_destroy();
        echo "<script> window.location.href='login.php'</script>";
        die();
    } else {
        if (empty($_SESSION['mainId'])) {
            session_destroy();
            echo "<script> window.location.href='login.php'</script>";
            die();
        }
    }
}
?>
