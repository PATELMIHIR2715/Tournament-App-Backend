<?php

class UploadFile{
    
    public $con;
    public $postName = "img";
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    function setPostName($postName){
        $this->postName = $postName;
    }
    
    function uploadFile($directoryName, $fileDetails){
        
        $response = array();
        
        if (!file_exists($directoryName)) {
			mkdir($directoryName, 0755, true);
		}
			
        if(!empty($fileDetails['tmp_name'])){
            
            $filename = time().$fileDetails['name'][0];
            
            $imageFileType = pathinfo($filename, PATHINFO_EXTENSION);

            $location = $directoryName."/".$filename;

            /* Valid Extensions */
            $valid_extensions = array("jpg","jpeg","png","pdf","csv", "CSV");
            
            
            /* Check file extension */
            if(in_array(strtolower($imageFileType), $valid_extensions) ) {

                if(move_uploaded_file($_FILES[$this->postName]['tmp_name'][0], $location)){
                    $response['status'] = 1;
                    $response['filepath'] = $location;
                }
                else{
                    $response['status'] = 0;
                    $response['msg'] =  "Upload Failed! Something went wrong with ".$location;
                }
            }
            else{
                $response['status'] = 0;
                $response['msg'] = "Upload Failed! Invalid file extention";
            }

        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Upload Failed! File not found";
        }
        
        return $response;
    }
}
?>