<?php session_start();
if ( !isset( $_SESSION['mainId'] ) ) {
    session_destroy();
    echo "<script> window.location.href='login.php'</script>";
    die();
} else {
    if ( empty( $_SESSION['mainId'] ) ) {
        session_destroy();
        echo "<script> window.location.href='login.php'</script>";
        die();
    } else {
        echo "<script> window.location.href = 'dashboard.php'</script>";
    }
}

?>
