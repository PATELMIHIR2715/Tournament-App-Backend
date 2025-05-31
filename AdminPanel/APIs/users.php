<?php

class Users {

    public $con;

    public function __construct( $con ) {
        $this->con = $con;
    }
    
    function loginUser($username, $password){

        $response = array();

        $loginUser = getDataFromTable("admins", "*", "username = '$username' AND password = '$password'", true);

        if($loginUser != null){
            $response['status'] = 1;
            $response['msg'] = "Success";
            $_SESSION['userName'] = "Admin";
            $_SESSION['mainId'] = $loginUser['id'];
        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Failed";
        }

        return $response;
    }
    
}
?>
