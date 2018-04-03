<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php

/*
 *Ajax processing for helpr module
 */

require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
  
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
 
    //Add version
    if($_POST['action'] == 'add_help_version'){
         $member_id = $_SESSION['custid'] ;
          extract($_POST);
          $ins['member_id'] = $member_id;
          $ins['app_id'] = $app_id;
          $ins['version'] = $version;
          $ins['faq_font_color'] = $_POST['faq_font_color'];
          $id = $dclass->insert("tblapp_tutorial_settings",$ins);
          $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_ADDED);
          if($id) {
                $updd['record_status'] = 'old';
                $dclass->update("tblapp_tutorial_settings",$updd,"  app_id='".$app_id."' AND intid!='".$id."' AND record_status!='running' ");
                
                $upd['record_status'] = 'prev';
                $dclass->update("tblapp_tutorial_settings",$upd,"  app_id='".$app_id."' AND intid!='".$id."' AND record_status='running' ");
                
                $data['output'] = 'S';
                $type = 'succ';
                $msg_status = "HELPR_VER_INSERT";
            }else {
                $data['output'] = 'F';
                $type = 'err';
                $msg_status = "HELPR_VER_INSERT_FAIL";
            }
            
            $data['msg'] = $gnrl->getMessage($msg_status, $lang_id);
       
            $_SESSION['msg'] = $data['msg'];
            $_SESSION['type'] = $type;
           
           echo json_encode($data);
           die();
       
    }
    
    
    if($_POST['action'] == 'add_help_version_copy'){
         $member_id = $_SESSION['custid'] ;
          extract($_POST);
          $ins['member_id'] = $member_id;
          $ins['app_id'] = $app_id;
          $ins['version'] = $version;
          $ins['faq_font_color'] = $_POST['faq_font_color'];
            
          $id = $dclass->insert("tblapp_tutorial_settings",$ins);
          $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_ADDED);
          if($id) {
                $updd['record_status'] = 'old';
                $dclass->update("tblapp_tutorial_settings",$updd,"  app_id='".$app_id."' AND intid!='".$id."' AND record_status!='running' ");
                
                
               $res_running_prod = $dclass->select("intid", "tblapp_tutorial_settings", " AND app_id='".$app_id."' AND intid!='".$id."' AND record_status='running' ");
               
               $copy_faq = "Insert Into tblapp_faq (`member_id`,`parent_id`,`app_id`,`ver_id`,`question`,`answer`,`status`,`intorder`,`is_canned`,`dtadd`) Select `member_id`,`parent_id`,`app_id`,'".$id."' as ver_id,`question`,`answer`,`status`,`intorder`,`is_canned`,'".date('Y-m-d H:i:s')."' as `dtadd` from tblapp_faq where ver_id = '".$res_running_prod[0]['intid']."'";
               $insert_copy = $dclass->query($copy_faq);
               
               $copy_image = "Insert Into tblapp_tutorial_images (`member_id`,`app_id`,`ver_id`,`title`,`image`,`intorder`) Select `member_id`,`app_id`,'".$id."' as ver_id,`title`,`image`,`intorder` from tblapp_tutorial_images where ver_id = '".$res_running_prod[0]['intid']."'";
               $insert_image = $dclass->query($copy_image);
               
               $copy_video = "Insert Into tblapp_tutorial_videos (`member_id`,`app_id`,`ver_id`,`video_name`,`video`,`video_type`,`live_date`,`pause_date`,`dtadd`,`status`) Select `member_id`,`app_id`,'".$id."' as ver_id,`video_name`,`video`,`video_type`,`live_date`,`pause_date`,'".date('Y-m-d H:i:s')."' as `dtadd`,`status` from tblapp_tutorial_videos where ver_id = '".$res_running_prod[0]['intid']."'";
               $insert_video = $dclass->query($copy_video);
                
                
                $upd['record_status'] = 'prev';
                $dclass->update("tblapp_tutorial_settings",$upd,"  app_id='".$app_id."' AND intid!='".$id."' AND record_status='running' ");
                
                
                
                
                
                $data['output'] = 'S';
                $type = 'succ';
                $msg_status = "HELPR_VER_INSERT";
            }else {
                $data['output'] = 'F';
                $type = 'err';
                $msg_status = "HELPR_VER_INSERT_FAIL";
            }
            
            $data['msg'] = $gnrl->getMessage($msg_status, $lang_id);
       
            $_SESSION['msg'] = $data['msg'];
            $_SESSION['type'] = $type;
           
           echo json_encode($data);
           die();
       
    }
    
    
    // Save helpr faq
    if ($_POST['action'] == 'update_help_faq') {
        $member_id = $_SESSION['custid'] ;
        extract($_POST);
      
            
        //FAQ INSERT
            if ($_POST['script'] == 'add') {
                
                  $ins['member_id'] = $member_id;
                  $ins['parent_id'] = 0;
                  $ins['app_id'] = $app_id;
                  $ins['ver_id'] = $ver_id;
                  $ins['question'] = $question;
                  $ins['answer'] = $answer;
                  $ins['status'] = 'active';
                  $ins['is_canned'] = 'N'; //FAQ ONLY
                  $ins['dtadd'] = date("Y-m-d h:i:s");
                  
                  $id = $dclass->insert("tblapp_faq",$ins);
                
                
            } else if($intid != ''){ // Edit
             
                   $upd['question'] = $question;
                   $upd['answer'] = $answer;
                   if($_POST['status'] == 'active')
                           $upd['status'] = 'active';
                   else {
                           $upd['status'] = 'inactive';
                   }
                   $dclass->update("tblapp_faq",$upd," intid='".$intid."' ");
            }


        if ($_POST['script'] != 'add') {
            unset($upd);
            $data['output'] = 'SU';
            $type = 'succ';
            $msg_status = "FAQ_UPDATE";
        }else if($_POST['script'] == 'add'){
            unset($ins);

            if ($id) {
                $data['output'] = 'S';
                $type = 'succ';
                $msg_status = "FAQ_ADD";
            }else {
                $data['output'] = 'F';
                $type = 'err';
                $msg_status = "FAQ_FAIL";
            }
        }
        $data['question'] = stripslashes($question);
        $data['answer'] = stripslashes($answer);
        $data['msg'] = $gnrl->getMessage($msg_status, $lang_id);
        
        if($status)
            $data['record_icon'] = "active_icon";
        else
             $data['record_icon'] = "inactive_icon";

        echo json_encode($data);
        die();
    }
   
       //Delete FAQ
    if ($_POST['action'] == 'delete_faq' && isset($_POST['intid'])) {
        extract($_POST);
        $member_id = $_SESSION['custid'];
     
        $dclass->delete("tblapp_faq"," intid='".$intid."'");    
        $del_status = "DELETE_FAQ";
        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        $data['output'] = 'S';
        $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_FAQ_DELETED);
        echo json_encode($data);
        die();
    }
    
    
     //Delete tutorial images
    if ($_POST['action'] == 'delete_tutorial_image' && isset($_POST['intid'])) {
        extract($_POST);
        $member_id = $_SESSION['custid'];
        
        $res = $dclass->select("image", "tblapp_tutorial_images", " AND intid='" . $intid . "' ");
        if (count($res) > 0) {
            if($res[0]['image'] != ''){
                 unlink(TUT_IMG . "/" . $res[0]['image']);
            }
           //Delete the relation record by default
            $st = $dclass->delete("tblapp_tutorial_images", " intid='" . $intid . "'");
            $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_IMAGE_DELETED);
            if ($st) {
                $data['output'] = 'S';
                $type = 'succ';
                $data['intid'] = $intid;
                $del_status = 'DELETE_TUT_IMG';
            } else {
                $data['output'] = 'F';
                $del_status = 'DELETE_TUT_IMG_FAIL';
            }
        }

        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        echo json_encode($data);
        die();
    }

        //Delete tutorial archive
    if ($_POST['action'] == 'delete_help_archive' && isset($_POST['intid'])) {
        extract($_POST);
        $member_id = $_SESSION['custid'];
        
        //Delete Images
        $res = $dclass->select("image", "tblapp_tutorial_images", " AND ver_id='" . $intid . "' ");
        if (count($res) > 0) {
            for($i=0;$i<count($res);$i++){
                if($res[$i]['image'] != ''){
                     unlink(TUT_IMG . "/" . $res[$i]['image']);
                }
               
            }
        }
        //Delete the relation record by default
        $sti = $dclass->delete("tblapp_tutorial_images", " ver_id='" . $intid . "'");
        $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_ARCHIVE_DELETED);
        
        //Delete Video
        $res = $dclass->select("video", "tblapp_tutorial_images", " AND ver_id='" . $intid . "' ");
        if (count($res) > 0) {
            for($i=0;$i<count($res);$i++){
                if($res[$i]['video'] != '' && $res[$i]['video_type'] == 'file'){
                     unlink(TUT_VIDEO . "/" . $res[$i]['video']);
                }
               
            }
        }
        //Delete the relation record by default
        $stv = $dclass->delete("tblapp_tutorial_videos", " ver_id='" . $intid . "'");
        
        
        //Delete version settings
        $st = $dclass->delete("tblapp_tutorial_settings", " intid='" . $intid . "'");
        
        
        if ($st && $sti && $stv) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $del_status = 'DELETE_TUT_ARCHIVE';
        } else {
            $data['output'] = 'F';
            $del_status = 'DELETE_TUT_ARCHIVE_FAIL';
                }
        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        echo json_encode($data);
        die();
    }

    
    
    //change tutorial image sequence
    if(isset($_GET['helpimgorder']) ){
       if(is_array($_POST['select_app_logo'])){
           $cnt = 1;
           foreach($_POST['select_app_logo'] as $img_id){
               $upds['intorder'] = $cnt;
               $dclass->update("tblapp_tutorial_images",$upds," intid='".$img_id."' ");
               $cnt++;
           }
       }
       $_SESSION['section'] = 'image';
    }
    
    //change tutorial faq sequence
    if(isset($_GET['helpfaqorder']) ){
       if(is_array($_POST['faq-list'])){
           $cnt = 1;
           foreach($_POST['faq-list'] as $faq_id){
               $upds['intorder'] = $cnt;
               $dclass->update("tblapp_faq",$upds," intid='".$faq_id."' ");
               $cnt++;
           }
       }
       $_SESSION['section'] = 'faq';
    }
    
    
    
     
    //save tutorial
    if ($_POST['action'] == 'save_tutorial') {
        $member_id = $_SESSION['custid'];
        extract($_POST);
        $pstatus = 'publish';
        $video_error = '';
        $image_error = '';
        
        
        //Check Video type
        if($video_type == 'file' && isset($_FILES['video']) &&  $_FILES['video']['type'] != 'video/mp4' ){
               $data['output'] = 'F';
                $type = 'err';
                $data['msg'] = $gnrl->getMessage("HELPR_WRONG_VIDEO_TYPE", $lang_id);
                echo json_encode($data);
                die();
        }
        
        
        //Check validation
        
        #Video
        if($video_name == '' || $video_type == '' || ($video_type == 'file' && !isset($_FILES['video'])) || ($video_type == 'youtube' && $youtube_video == '' ) || ($video_type == 'vimeo' && $vimeo_video == '' ) ){
            $video_error = 1;
        }
    
        $resi = $dclass->select("intid,title,image","tblapp_tutorial_images"," AND ver_id = '".$ver_id."' ");
       
        
        #Image
        if(($new_title == '' || !isset($_FILES['new_img'])) && count($resi) <= 0 ){
            $image_error = 1;
        }
        
        #Image Validation
        if(isset($_FILES['new_img'])){
            $allowedExts = array("gif", "jpeg", "jpg", "png");
            foreach($_FILES["new_img"]['type'] as $type){
                $extension = end(explode("/", $type));
               // echo $extension; 
                if(!in_array($extension, $allowedExts)){
                    $data['output'] = 'F';
                    $type = 'err';
                    $data['msg'] = $gnrl->getMessage("HELPR_WRONG_IMG_TYPE", $lang_id);
                    echo json_encode($data);
                    die();
                }
            }
        }
       
    
      //Video Fields Validated
      if($video_error == ''){
            
        //Video UPDATE
        if(isset($video_id) && $video_id != ''){
            unset($upd);
            $upd['video_name'] = $video_name;
            $upd['video_type'] = $video_type;

             if ($video_type == 'file' && isset($_FILES['video'])) {

                 $filename = time().$gnrl->makefilename($_FILES['video']['name']);
                            $des = TUT_VIDEO . "/" . $filename;
                            if (move_uploaded_file($_FILES['video']['tmp_name'], $des)) {
                                unlink(TUT_VIDEO . "/" . $old_video);
                                $upd['video'] = $filename;
                  }           
             }
             
             if($video_type != 'file'){
                 if($video_type == 'youtube')
                    $upd['video'] = $youtube_video;
                 else 
                    $upd['video'] = $vimeo_video;
                 
                 unlink(TUT_VIDEO . "/" . $old_video);
             }
            
             if($live_date != ''){
                 $upd['live_date'] = date("Y-m-d",  strtotime($live_date));
                 $upd['pause_date'] = NULL;
                 
                
             }else{
                 $upd['live_date'] = date("Y-m-d");
                 $upd['pause_date'] = NULL;
             }
         

            $upd['status'] = $pstatus;
            $dclass->update("tblapp_tutorial_videos", $upd, " intid='" . $video_id . "' ");
            $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_VIDEO_UPDATED);
            $data['output'] = 'S';
            $type = 'succ';
            if ($pstatus == 'publish')
                $video_status = "VIDEO_PUBLISH_UPDATE";
            else if ($pstatus == 'save')
                $video_status = "VIDEO_SAVE_UPDATE";
            else
                $video_status = "VIDEO_PAUSE_UPDATE";
        }else{ 
            
            //Video INSERT
            unset($ins);
            $ins['member_id'] = $member_id;
            $ins['app_id'] = $app_id;
            $ins['ver_id'] = $ver_id;
            $ins['video_name'] = $video_name;
            $ins['video_type'] = $video_type;
            
             if ($video_type == 'file' && isset($_FILES['video'])) {

                 $filename = time().$gnrl->makefilename($_FILES['video']['name']);
                 $des = TUT_VIDEO . "/" . $filename;
                  if (move_uploaded_file($_FILES['video']['tmp_name'], $des)) {
                   $ins['video'] = $filename;
                  }           
             }
             
             if($video_type != 'file'){
                 if($video_type == 'youtube')
                    $ins['video'] = $youtube_video;
                 else 
                    $ins['video'] = $vimeo_video;
             }
            
             if($live_date != ''){
                 $ins['live_date'] = date("Y-m-d",  strtotime($live_date));
                 
                
             }else{
                 $ins['live_date'] = date("Y-m-d");
             }

            $ins['dtadd'] = date("Y-m-d h:i:s");
            $ins['status'] = $pstatus;
            $video_id = $dclass->insert("tblapp_tutorial_videos", $ins);
            $gnrl->save_access_log($member_id,$_SESSION['agents_cust_id'],HELPR_VIDEO_ADDED);

            if ($video_id) {
                
                 //Make other videos pause
                 $updov['live_date'] = NULL;
                 $updov['pause_date'] = date("Y-m-d");
                 $dclass->update("tblapp_tutorial_videos",$updov," intid != '".$video_id."' AND app_id='".$app_id."' ");
                
                $data['output'] = 'S';

                if ($pstatus == 'publish')
                    $video_status = "VIDEO_PUBLISH_ADD";
                else if ($pstatus == 'save')
                    $video_status = "VIDEO_SAVE_ADD";
                else
                    $video_status = "VIDEO_PAUSE_ADD";
                
                $type = 'succ';
                
            }else {
                $data['output'] = 'F';
                $video_status = "VIDEO_PUBLISH_ADD_FAIL";
                $type = 'err';
            }
        
      
        }
        }
        
        //Image fields validate
        if($image_error == ''){
            //IMAGES UPDATE
           if(count($resi) > 0){
            for($i=0;$i<count($resi);$i++){
                $updi['title'] = $_POST['title_'.$resi[$i]['intid']];
                $dclass->update("tblapp_tutorial_images",$updi," intid='".$resi[$i]['intid']."' ");
            }
           }
            
            //IMAGE INSERT
            if(isset($_FILES['new_img'])){
                $img_cnt = 0;
                foreach($_FILES['new_img']['name'] as $name){
                    $insi['member_id'] = $member_id;
                    $insi['app_id'] = $app_id;
                    $insi['ver_id'] = $ver_id;
                    $insi['title'] = $_POST['new_title'][$img_cnt];
                    $filename = time().$name;
                    $des = TUT_IMG . "/" . $filename;
                    if (move_uploaded_file($_FILES['new_img']['tmp_name'][$img_cnt], $des)) {
                       $insi['image'] = $filename;
                       $image_id = $dclass->insert("tblapp_tutorial_images",$insi);
                    }       
                  $img_cnt++;  
                }
           }
            
           
        }
        
         //Update general settings
        unset($upds);
        $upds['image_animation'] = $animation_id;
        $dclass->update("tblapp_tutorial_settings",$upds," intid = '".$ver_id."' ");
        
        $data['output'] = 'S';
        $type = 'succ';
        $data['msg'] = $gnrl->getMessage('HELPR_UPDATE', $lang_id);
        $_SESSION['msg'] = $data['msg'];
        $_SESSION['type'] = $type;
        echo json_encode($data);
        die();
    }
    
 
    
 }

?>
