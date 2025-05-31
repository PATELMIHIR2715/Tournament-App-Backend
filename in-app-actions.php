<?php
class InAppActions{
    
    public $con;

    public function __construct($con) {
        $this->con = $con;
    }
    
    /*
    $type = type of like (Ex. module, comment, project, etc.)
    $typeId = type id means unique id of the type (Ex. module id, comment id, etc.)
    $byUserId = user id who perform like
    $getUserDetails = either true of false, if true fetch user details who perform like
    */
    function getLikes($type, $typeId, $byUserId, $getUserDetails){
        
        $response = array();
        
        if($byUserId != '' && $typeId != "" && $type != ""){
            $selectLikes = "SELECT * FROM in_app_likes WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$byUserId'";
        }
        else if($type != "" && $typeId != ""){
            $selectLikes = "SELECT * FROM in_app_likes WHERE type = '$type' AND type_id = '$typeId'";
        }
        else{
            $selectLikes = "SELECT * FROM in_app_likes WHERE user_id = '$byUserId'";
        }
        
        $likesResults = $this->con->query($selectLikes);
        
        while($likeRows = $likesResults->fetch_assoc()){
            $response[] = $likeRows;
        }
        
        return $response;
    }
    function addNewLike($type, $typeId, $userId){
        
        $response = array();
        $currentDateTime = date('Y-m-d H:i:s');
        
        $insertLike = "INSERT INTO in_app_likes(type, type_id, user_id, date_time) VALUES('$type', '$typeId', '$userId', '$currentDateTime')";
        $this->con->query($insertLike);
    }
    function removeLike($type, $typeId, $likedUserId){
        
        $response = array();
        $currentDateTime = date('Y-m-d H:i:s');
        
        $deleteLike = "DELETE FROM in_app_likes WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$likedUserId'";
        $this->con->query($deleteLike);
    }
    
    /*
    $type = type of like (Ex. module, comment, project, etc.)
    $typeId = type id means unique id of the type (Ex. module id, comment id, etc.)
    $dislikedUserId = user id who perform dislike
    $getUserDetails = either true of false, if true fetch user details who perform dislike
    */
    function getDislikes($type, $typeId, $dislikedUserId, $getUserDetails){
        
        $response = array();
        
        $selectDislikes = "SELECT * FROM in_app_dislikes WHERE type = '$type' AND type_id = '$typeId'";
        
        if($dislikedUserId != ''){
            $selectDislikes = "SELECT * FROM in_app_dislikes WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$dislikedUserId'";
        }
        
        $dislikesResults = $this->con->query($selectDislikes);
        
        while($dislikesRows = $dislikesResults->fetch_assoc()){
            $response[] = $dislikesRows;
        }
        
        return $response;
    }
    function addNewDislike($type, $typeId, $userId){
        
        $response = array();
        $currentDateTime = date('Y-m-d H:i:s');
        
        $insertLike = "INSERT INTO in_app_dislikes(type, type_id, user_id, date_time) VALUES('$type', '$typeId', '$userId', '$currentDateTime')";
        $this->con->query($insertLike);
    }
    function removeDislike($type, $typeId, $dislikedUserId){
        
        $response = array();
        $currentDateTime = date('Y-m-d H:i:s');
        
        $deleteDislike = "DELETE FROM in_app_dislikes WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$dislikedUserId'";
        $this->con->query($deleteDislike);
    }
    
    /*
    $type = type of like (Ex. module, comment, project, etc.)
    $typeId = type id means unique id of the type (Ex. module id, comment id, etc.)
    $viewdUserId = user id who perform view
    $getUserDetails = either true of false, if true fetch user details who perform view
    */
    function getViews($type, $typeId, $viewdUserId, $getUserDetails){
        
        $response = array();
        
        $selectViews = "SELECT * FROM in_app_views WHERE type = '$type' AND type_id = '$typeId'";
        
        if($viewdUserId != ''){
            $selectViews = "SELECT * FROM in_app_views WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$viewdUserId'";
        }
        
        $viewsResults = $this->con->query($selectViews);
        
        while($vieweRows = $viewsResults->fetch_assoc()){
            $response[] = $vieweRows;
        }
        
        return $response;
    }
    function addNewView($type, $typeId, $userId){
        
        $response = array();
        $currentDateTime = date('Y-m-d H:i:s');
        
        $insertViews = "INSERT INTO in_app_views(type, type_id, user_id, date_time) VALUES('$type', '$typeId', '$userId', '$currentDateTime')";
        $this->con->query($insertViews);
    }
    
    /*
    $type = type of like (Ex. module, comment, project, etc.)
    $typeId = type id means unique id of the type (Ex. module id, comment id, etc.)
    $commentedUserId = user id who perform comment
    $fetchUserDetails = either true of false, if true fetch user details who perform comment
    $codeData = either true of false, if true fetch code data (Ex. In discussion user can comment on discussion along with code)
    $currentPageNo = used to get number of comments at once not all
    $itemsCount = number of comments count to be fetched
    */
    function getComments($type, $typeId, $commentedUserId, $fetchUserDetails, $fetchCodeData, $currentPageNo, $itemsCount, $getCountOnly){
        
        $response = array();
        $pageQuery = "";
        
        if($currentPageNo >= 0 && $itemsCount > 0){
            $pageQuery = " LIMIT $itemsCount OFFSET ".($currentPageNo * $itemsCount);
        }
        
        $selectComments = "SELECT * FROM in_app_comments WHERE type = '$type' AND type_id = '$typeId' ORDER BY id DESC ".$pageQuery;
        
        if($commentedUserId != ''){
            $selectComments = "SELECT * FROM in_app_comments WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$commentedUserId'";
        }
        
        $commentResults = $this->con->query($selectComments);
        
        if($getCountOnly){
            return $commentResults->num_rows;
        }
        
        while($commentRows = $commentResults->fetch_assoc()){
            
            if($fetchUserDetails){
                $commentRows['user_details'] = userDetails($commentRows['user_id'], "*");
            }
            
            if($fetchCodeData){
                $codeObj = new Code($this->con);
                $commentRows['code_data'] = $codeObj->getCodeData("comment", $commentRows['id']);
            }
            
            $commentRows['likes'] = $this->getLikes("comment", $commentRows['id'], "", true);
            $commentRows['dislikes'] = $this->getDislikes("comment", $commentRows['id'], "", true);
            
            // creating full url for comment images
            $commentImages = json_decode($commentRows['images'], true);
            $commentImages2 = array();
            
            foreach($commentImages as $image){
                $commentImages2[] = currentDirPath().$image;
            }
            
            $commentRows['images'] = $commentImages2;
            
            $response[] = $commentRows;
        }
        
        return $response;
    }
    function getSingleComment($commentId, $parms, $fetchUserDetails, $fetchCodeData){
        
        $response = array();
    
        $paramsArry = explode(",", $parms);
        
        $selectComment = "SELECT $parms FROM in_app_comments WHERE id = '$commentId'";
        $commentResults = $this->con->query($selectComment);
        
        if($commentResults->num_rows == 0){
            return null;
        }
        else {
            
            $response = $commentResults->fetch_assoc();
            
            if($fetchUserDetails){
                $response['user_details'] = userDetails($response['user_id'], "*");
            }
            
            if($fetchCodeData){
                $codeObj = new Code($this->con);
                $response['code_data'] = $codeObj->getCodeData("comment", $response['id']);
            }
            
            if($parms != "*" && sizeof($paramsArry) == 1){
                $response = $response[trim($parms)];
            }
        }
        
        if(is_array($response)){
            if(array_key_exists('images', $response)){
                
                // creating full url for comment images
                $commentImages = json_decode($response['images'], true);
                $commentImages2 = array();
                
                foreach($commentImages as $image){
                    $commentImages2[] = currentDirPath().$image;
                }
                
                $response['images'] = $commentImages2;
            }
        }
        return $response;
    }
    function deleteComment($commentId, $type, $typeId, $userId){
        
        $response = array();
        
        $deleteComment = "DELETE FROM in_app_comments WHERE type = '$type' AND type_id = '$typeId' AND user_id = '$userId'";

        if($commentId != ""){
            $deleteComment = "DELETE FROM in_app_comments WHERE id = '$commentId'";
        }
        
        if($this->con->query($deleteComment)){
            $response['status'] = 1;
            $response['msg'] = "Comment deleted";
        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Unable to delete comment";
        }
        
        return $response;
    }
    function addNewComment($type, $typeId, $userId, $comment, $images, $codeData){
        
        $response = array();
        $codeObj = new Code($this->con);
        $currentDateTime = date('Y-m-d H:i:s');
        $imagesArray = array();
        
        $count = 0;
        foreach($images as $image){
            $count++;
            
            $filepath = "DiscussionCommentImages/".$count."_".time().".png";
            $imageSaved = file_put_contents($filepath, base64_decode($image));
                
            if($imageSaved){
                 $imagesArray[] = $filepath;   
            }
        }
        
        $insertComment = "INSERT INTO in_app_comments(type, type_id, user_id, comment, images, date_time) VALUES('$type', '$typeId', '$userId', '$comment', '".json_encode($imagesArray)."', '$currentDateTime')";
        if($this->con->query($insertComment)){
            
            $commentId = $this->con->insert_id;
            
            $response['status'] = 1;
            $response['comment_id'] = $commentId;
            $response['msg'] = "Comment Added";
            
            $attachWithCode = "/* Code From Learnoset - Learn Code Online Android App.*/\n/*Download Learnoset from PlayStore to learn coding*/";
            
            foreach($codeData as $code){

                $codee = $attachWithCode."\n\n".$code['code'];
                $language = $code['language'];
                $fileName = $code['filename'];
                
                // adding code data
                $response = $codeObj->addCodeData('comment', $commentId, $codee, $language, $fileName);
                $response['msg'] = "Comment Added";
                
                if($response['status'] == 0){
                    $response['msg'] = "Failed to add comment";
                    
                    // code data in not inserted properly hence delete added comment
                    $this->deleteComment("", $type, $typeId, $userId);
                    
                    // code data in not inserted properly hence delete all code data for this comment
                    // $vars = $codeId, $type, $typeId
                    $codeObj->deleteCodeData("", 'comment', $commentId);
                    
                    return $response;
                }
            }
        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Failed to add comment";
        }
        
        return $response;
    }
    function updateComment($commentId, $userId, $comment, $images, $codeData){
        
        $response = array();
        $codeObj = new Code($this->con);
        $currentDateTime = date('Y-m-d H:i:s');
        $imagesArray = array();
        
        $count = 0;
        foreach($images as $image){
            
            $count++;
            
            $filepath = "DiscussionCommentImages/".$count."_".time().".png";
            $imageSaved = file_put_contents($filepath, base64_decode($image));
                
            if($imageSaved){
                 $imagesArray[] = $filepath;   
            }
        }
        
        //$vars = $commentId, $parms, $fetchUserDetails, $fetchCodeData
        $singleComment = $this->getSingleComment($commentId, "*", false, false);
        
        $updateComment = "UPDATE in_app_comments SET comment = '$comment', images = '".json_encode($imagesArray)."' WHERE id = '$commentId'";
        if($this->con->query($updateComment)){
            
            $response['status'] = 1;
            $response['msg'] = "Comment Updated";
            
            // delete old code data
            // $vars = $codeId, $type, $typeId
            $codeObj->deleteCodeData("", "comment", $commentId);
                    
            //$attachWithCode = "/* Code From Learnoset - Learn Code Online Android App.*/\n/*Download Learnoset from PlayStore to learn coding*/";
            
            foreach($codeData as $code){
                
                $codee = $code['code'];
                $language = $code['language'];
                $fileName = $code['filename'];
                
                // adding code data
                $codeObj->addCodeData('comment', $commentId, $codee, $language, $fileName);
                $response['msg'] = "Comment Updated";
                
                if($response['status'] == 0){
                    $response['msg'] = "Failed to update comment -> Error while adding your code";
                    return $response;
                }
            }
        }
        else{
            $response['status'] = 0;
            $response['msg'] = "Failed to update comment";
        }
        
        return $response;
    }
}
?>