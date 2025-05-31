<?php

class Notifications{

    public $con;

    public function __construct($con) {
        $this->con = $con;
    }
    
    function pushNotification($title, $body, $imageURL, $clickAction, $destination, $payloadData){
        
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setMessage($body);
        $notification->setAction($clickAction);
        $notification->setPayload(time().",,,,".$payloadData);
        $notification->setActionDestination($destination);
        
        // getting fcm token from main_data
        $firebaseKey = getDataFromTable("main_data", "fcm_token", "id = '1'", true);
        
        if($imageURL != ''){
           $notification->setImage($imageURL); 
        }
        
        $requestData = $notification->getNotificatin();
        $fields = array('to' => '/topics/gamers_baazi_3', 'data' => $requestData,);
        
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array('Authorization: key=' . $firebaseKey, 'Content-Type: application/json');
        
        // Open connection
        $ch = curl_init();
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabling SSL Certificate support temporarily
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
    }
    
    function pushNotificationToSingle($userId, $title, $body, $imageURL, $clickAction, $destination, $payloadData){
        
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setMessage($body);
        $notification->setAction($clickAction);
        $notification->setPayload(time().",,,,".$payloadData);
        $notification->setActionDestination($destination);
        
        // getting fcm token from main_data
        $firebaseKey = getDataFromTable("main_data", "fcm_token", "id = '1'", true);
        
        if($imageURL != ''){
           $notification->setImage($imageURL); 
        }
        
        $requestData = $notification->getNotificatin();
        $fields  = array('to' => getDataFromTable("users", "firebase_key", "id = '$userId'", true), 'data' => $requestData);
        
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array('Authorization: key=' . $firebaseKey, 'Content-Type: application/json');
        
        // Open connection
        $ch = curl_init();
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabling SSL Certificate support temporarily
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
    }
}
?>