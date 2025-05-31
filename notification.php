<?php
class Notification{
	private $title;
	private $message;
	private $image_url;
	private $action;
	private $action_destination;
	private $data;
	
	function __construct(){
         
	}
 
	public function setTitle($title){
		$this->title = $title;
	}
 
	public function setMessage($message){
		$this->message = $message;
	}
 
	public function setImage($imageUrl){
		$this->image_url = $imageUrl;
	}

	public function setAction($action){
		$this->action = $action;
	}
 
	public function setActionDestination($actionDestination){
		$this->action_destination = $actionDestination;
	}
 
	public function setPayload($data){
		$this->data = $data;
	}
	
	public function getNotificatin(){
		$notification = array();
		$notification['title'] = $this->title;
		$notification['message'] = $this->message;
		$notification['image'] = $this->image_url;
		$notification['action'] = $this->action;
		$notification['data'] = $this->data;
		$notification['action_destination'] = $this->action_destination;
		return $notification;
	}
}
?>
