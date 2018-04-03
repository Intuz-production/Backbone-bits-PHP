<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 
require("config/configuration.php");
    if(!$gnrl->checkMemLogin()){
	$gnrl->redirectTo("login?msg=logfirst");
    }
 
   $member_id = $_SESSION['custid']; 

$file_type_array = array("pdf"=>"pdf.png",
                         "doc"=>"doc.png",
                         "docx"=>"docx.png",
                         "txt"=>"txt.png",
                         "xls"=>"xls.png",
                         "xlsx"=>"xlsx.png",
                         "png"=>"png.png",
                         "jpg"=>"jpg.png",
                         "jpeg"=>"jpeg.png",
                         "gif"=>"gif.png",
                         "mp4"=>"mp4.png");


$file_array_for_popup = array("png"=>"png.png",
                         "jpg"=>"jpg.png",
                         "jpeg"=>"jpeg.png",
                         "gif"=>"gif.png",
                         "mp4"=>"mp4.png");
//print_r($_SESSION);

//$canned_text= $dclass->select('*', 'tbl_canned_response', ' AND parent_id='.$member_id.' ');
//print_r($canned_text);
$request_user_text = $dclass->select('*, tblapp_support.intid', 'tblapp_support, tblmember_apps', ' AND  (tblapp_support.intid = '.$_REQUEST['support_id'].' AND tblapp_support.request_id = 0) AND tblapp_support.app_id=tblmember_apps.intid order by tblapp_support.intid ASC');



$response_text_support = $dclass->select('*, tblapp_support.intid', 'tblapp_support, tblmember_apps', ' AND  tblapp_support.request_id = '.$_REQUEST['support_id'].' AND tblapp_support.app_id=tblmember_apps.intid order by tblapp_support.intid DESC');

//print_r($request_user_text);

$get_attached_images = $dclass->select('*', 'tblapp_support_attachment', ' AND support_id = '.$_REQUEST['support_id'].' ');
//print_r($get_attached_images);



function gethours($time_ago){
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        return "<div class='days_ago_1'>Just now</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "<div class='days_ago_1'>1 min ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
        else{
            return "<div class='days_ago_1'>$minutes min ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            return "<div class='days_ago_1'>1 hr ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }else{
            return "<div class='days_ago_1'>$hours hrs ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            return "<div class='days_ago_1'>yesterday</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }else{
            return "<div class='days_ago_1'>$days days ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return "<div class='days_ago_1'>1 week ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }else{
            return "<div class='days_ago_1'>$weeks weeks ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            return "<div class='days_ago_1'>1 month ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }else{
            return "<div class='days_ago_1'>$months months ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
    //Years
    else{
        if($years==1){
            return "<div class='days_ago_1'>1 yr ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }else{
            return "<div class='days_ago_1'>$years yrs ago</div> <div class='days_ago_2'>".date("H:i",$time_ago)."</div>";
        }
    }
}


//function gethours($date){
//
//$date1= $date;
//                     //  echo $value['json'];
//                       
//                           $date2=date("Y-m-d h:i:s");
// $diff = abs(strtotime($date2) - strtotime($date1)); 
//date('Y-m-d',$diff); 
// $years   = floor($diff / (365*60*60*24)); 
//$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
//$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
//
// $hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
//
//$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
//
//$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 
//
////if($seconds>60){
////   echo $time_var = $minuts.' minutes '.$seconds.' seconds';
////}
////else if($minuts>60){
////    $time_var = $hours.' hours '.$minuts.' minutes';
////}
////else if($hours>24){
////    $time_var = $days.' days ';
////}
////else if($days>365){
////    $time_var = $years. ' years';
////}
//print_r($months);
//if($years==0){
//    if($months==0)
//    {
//   if($days==0)
//   {
//       if($hours==0)
//       {
//           if($minuts==0)
//           {
//            $sec= $seconds.' sec ago';
//           }
//           else
//           {
//              $sec=  $minuts.' min ago';
//           }
//       }
//       else {
//           $sec=  $hours.' hr ago';
//       }
//   }
//   else {
//      $sec=  $days.' D ago';
//   }
//    }
//    else {
//      $sec=  $months.' M ago';
//    }
//}
//else
//{
//    $sec=  $years.' Y ago';
//}
////echo $sec;
//return $sec;
//}
?>

<div class="col-xs-12 col-md-12"> 
  <!-- The time line -->
  <ul class="timeline">
    <!-- timeline time label -->
    
    <!-- /.timeline-label --> 
    <!-- timeline item -->
    
    
        
      <?php 
                             
                              if(!empty($response_text_support)): 
                                  $datecomp = '';
                                  
                                  foreach($response_text_support as $res_support_value){
                                  //echo $res_support_value['status'];
                                      
                                  if($datecomp!=date('Y-m-d',strtotime($res_support_value['dtadd']))){
                                        echo '
    <li class="time-label"> <span class="">  '.date("d.M.Y",strtotime($res_support_value['dtadd'])).' </span></li>';
                                  }
                                  $datecomp = date('Y-m-d',strtotime($res_support_value['dtadd']));
                                  //echo $date[$res_support_value['dtadd']]=$res_support_value['dtadd'];
                                  
                                  echo "<li> ";
                                 
                                  if($res_support_value['type']=='user'){
                                      $class_reply_mail = 'apps_i mass_i';
                                      $rep_box = '';
                                      $class_start_thread = 'thread_start';
                                  }else{
                                      $class_reply_mail = 'apps_i repl';
                                      $rep_box = 'rep_box';
                                      $class_start_thread = 'reply_file';
                                  }
                                  ?>
      <div class="<?php echo $class_start_thread ?>"><span class="time repl"><?php echo gethours($res_support_value['dtadd']); ?></span> <i <?php if($res_support_value['status']=='review'){ ?>class="apps_i review_i" style="margin-left:20px" <?php }else if($res_support_value['status']=='close'){ ?>class="apps_i closed_i" style="margin-left:20px" <?php }else{ ?> class="<?php echo $class_reply_mail ?>" <?php } ?>></i></div>
      <div class="timeline-item popover right <?php if($res_support_value['status']=='review'){ ?> review_info_box <?php  }else if($res_support_value['status']=='close'){ ?> close_info_box <?php  } else {  echo $rep_box; } ?>" >
        <div class="arrow"></div>
        <h3 class="timeline-header"><?php echo ($res_support_value['name']) ? $res_support_value['name'] : "&nbsp;"; ?></h3>
        <div  class="timeline-body"> <?php echo $res_support_value['message'] ?></div>
      </div>
      <?php 
                            $get_attached_response_images = $dclass->select('*', 'tblapp_support_attachment', ' AND support_id = '.$res_support_value['intid'].' ');
                            
                            if(!empty($get_attached_response_images)){
                            
                             ?>
      <div class="attachment_file">
        <div class="boder_b">&nbsp;</div>
        <i class="fa fa-paperclip atta"></i></div>
      <div class="timeline-item popover right attachment_box">
        <div class="arrow"></div>
        <div class="timeline-body">
          <?php 
          $i=0;
          foreach($get_attached_response_images as $value_attached_resp): 
              
              $info = new SplFileInfo($value_attached_resp['image']);
          
          
          if(empty($file_type_array[$info->getExtension()])){
              $image_name= 'other.png';
          }else{
              $image_name= $file_type_array[$info->getExtension()];
          }
          
          if($info->getExtension()=='jpeg' || $info->getExtension()=='jpg' || $info->getExtension()=='png'){
          $des = SUPPORT_IMG.'/thumb/'.$value_attached_resp['image'];
          }else{
              $des = 'img/communicatr-detail-icon/'.$image_name;
          }
          
                //$des = 'img/communicatr-detail-icon/'.$image_name;
              
              $image_path1 = SUPPORT_IMG.'/'.$value_attached_resp['image'];
              
              if(!empty($file_array_for_popup[$info->getExtension()])){
            
              ?>
            <div class="col-xs-12 col-md-2 attach_file1"> <a  onclick="view_detail('<?php echo $res_support_value['intid'].'_'. $i ?>','<?php echo $image_path1 ?>');" data-dialog<?php echo $res_support_value['intid'].'_'. $i ?>="somedialog<?php echo $res_support_value['intid'].'_'. $i ?>" class="trigger"> 
            <dd><img src="<?php echo $des ?>" style="height:50px;width:50px"></dd>
            <div class="popover bottom">
              <div class="arrow"></div>
              <div class="pdf-file"><?php echo $value_attached_resp['image'] ?></div>
            </div>
            </a></div>
            
            <div id="somedialog<?php echo $res_support_value['intid'].'_'. $i ?>" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content communicatr-detail_pupep">
    <div class="popap-header">
      <!--<h3 class="fl">View Detail as</h3>-->
      <button class="action fr" data-dialog-close>&nbsp;</button>
    </div>
    <div class="popap-content" id="view-image<?php echo $res_support_value['intid'].'_'. $i ?>"></div>
  </div>
</div>
            <script>
    (function() {

            var dlgtrigger = document.querySelector( '[data-dialog<?php echo $res_support_value['intid'].'_'. $i ?>]' );
            
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog<?php echo $res_support_value['intid'].'_'. $i ?>' ) );

            dlg = new DialogFx( somedialog );
            
            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            

    })();
				
			
				
</script>

          <?php 
           
          }else{ 
              
              
              
              ?>
<div class="col-xs-12 col-md-2 attach_file1"> <a   href="files/support/attachment/<?php echo $value_attached_resp['image'] ?>">  
            <dd><img src="<?php echo $des ?>"></dd>
            <div class="popover bottom">
              <div class="arrow"></div>
              <div class="pdf-file"><?php echo $value_attached_resp['image'] ?></div>
            </div>
            </a></div>
         <?php }
         
         
          $i++;
          endforeach; ?>
          <div class="cl"></div>
        </div>
      </div>
      <?php }
                echo '</li>';
                            }
                  
                               
                            endif; 
                             echo '
    <li class="time-label"> <span class="">  '.date("d.M.Y",strtotime($request_user_text[0]["dtadd"])).' </span></li>';
                            
                            ?>
    
    <li>
      <div class="thread_start"><span class="time"><?php echo gethours($request_user_text[0]['dtadd']); ?></span> <i class="apps_i mass_i"></i></div>
      <div class="timeline-item popover right">
        <div class="arrow"></div>
        <h3 class="timeline-header"><?php echo $request_user_text[0]['name'] ?></h3>
        <div class="timeline-body"> <?php echo $request_user_text[0]['message'] ?></div>
      </div>
      <?php if(!empty($get_attached_images)){ ?>
      <div class="attachment_file">
        <div class="boder_b">&nbsp;</div>
        <i class="fa fa-paperclip atta"></i></div>
      <div class="timeline-item popover right attachment_box">
        <div class="arrow"></div>
        <div class="timeline-body">
          <?php 
          $j=0;
          foreach($get_attached_images as $value): 
             
              $info = new SplFileInfo($value['image']);
                 
                if(empty($file_type_array[$info->getExtension()])){
              $image_name= 'other.png';
          }else{
              $image_name= $file_type_array[$info->getExtension()];
          }
          
          if($info->getExtension()=='jpeg' || $info->getExtension()=='jpg' || $info->getExtension()=='png'){
          $des = SUPPORT_IMG.'/thumb/'.$value['image'];
          }else{
              $des = 'img/communicatr-detail-icon/'.$image_name;
          }
           $image_path = SUPPORT_IMG.'/'.$value['image'];
           if(!empty($file_array_for_popup[$info->getExtension()])){
              ?>
            <div class="col-xs-12 col-md-2 attach_file1"> <a onclick="view_detail('<?php echo $value['intid'].'_'. $j ?>','<?php echo $image_path ?>');" data-dialog<?php echo $value['intid'].'_'. $j ?>="somedialog<?php echo $value['intid'].'_'. $j ?>" class="trigger">
            <dd><img src="<?php echo $des ?>" style="height:50px;width: 50px"></dd>
            <div class="popover bottom">
              <div class="arrow"></div>
              <div class="pdf-file"><?php echo $value['image'] ?></div>
            </div>
            </a></div>
            <div id="somedialog<?php echo $value['intid'].'_'. $j ?>" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content communicatr-detail_pupep">
    <div class="popap-header">
<!--      <h3 class="fl">View Detail</h3>-->
      <button class="action fr" data-dialog-close>&nbsp;</button>
    </div>
    <div class="popap-content" id="view-image<?php echo $value['intid'].'_'. $j ?>"></div>
  </div>
</div>
            <script>
    (function() {

            var dlgtrigger = document.querySelector( '[data-dialog<?php echo $value['intid'].'_'. $j ?>]' );
            
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog<?php echo $value['intid'].'_'. $j ?>' ) );

            dlg = new DialogFx( somedialog );
            
            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            

    })();
				
			
				
</script>
            
          <?php 
           }else{             
              
               ?>
             <div class="col-xs-12 col-md-2 attach_file1"> <a href="files/support/attachment/<?php echo $value['image'] ?>">
            <dd><img src="<?php echo $des ?>"></dd>
            <div class="popover bottom">
              <div class="arrow"></div>
              <div class="pdf-file"><?php echo $value['image'] ?></div>
            </div>
            </a></div>  
          <?php }
          $j++;
          endforeach; ?>
          <div class="cl"></div>
        </div>
      </div>
      <?php } ?>
    </li>
<!--    <li> <i id="switch_close_close" class="fa fa-clock-o">dsafsadfsad</i> </li>-->
    
  </ul>
</div>



<?php 

if($request_user_text[0]['status']=='close'){ ?>
<script>
    
    $('#switch_close_close').switchClass('fa fa-clock-o','apps_i  closed_i');
    //$('.review_box').hide();
    //$('.slimScrollDiv').addClass('scroll_remove_sticky1');
	//			$('.slimScrollDiv > div.timeline_scroll').addClass('scroll_remove_sticky2');
    
    //$('#reply').hide();
    </script>
    
<?php }else{ ?>
    <script>
   // $('.review_box').css("display","none");
    </script>
<?php } ?>

    <script>
        function view_detail(id,image_path)
       {
        
        $.ajax({
                method: "POST",
                url: "view_image",
                data: {image_path:image_path},
                success: function(result){
                    $("#view-image"+id).html(result);
        
            }});
    }
    
    
    
    
    
        </script>
<style>

</style>
