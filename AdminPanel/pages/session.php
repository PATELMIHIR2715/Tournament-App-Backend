<?php session_start();   
date_default_timezone_set('Asia/Kolkata');
if(!isset($_SESSION['userId'])){
   session_destroy();
  echo "<script> window.location.href='login'</script>";
  die();
}else{
    if(empty($_SESSION['userId'])){
         session_destroy();
        echo "<script> window.location.href='login'</script>";
         die();
    }
    include_once 'APIs/functionn.php'; 
    include_once 'APIs/dbcon.php'; 
    $myFun=new MyFun($con);
    $userCon="id=".$_SESSION['userId'];
    $userInfo=$myFun->selectData('admins','*',$userCon);
    if(empty($userInfo)){
              session_destroy();
        echo "<script> window.location.href='login'</script>";
         die();
    }
    $arrPermission=explode(",,",$userInfo[0]['permission']);
}
?>