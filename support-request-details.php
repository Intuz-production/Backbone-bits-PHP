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
    
    $feature_id = 3; //support request
    
    $member_id = $_SESSION['custid'];
   if(isset($_SESSION['sel_app_id'])){
         $_REQUEST['sel_app_id'] = $_SESSION['sel_app_id'];
    }
     
    if(isset($_REQUEST['sel_app_id']) && $_REQUEST['sel_app_id'] != '' ){
        $sel_app_id= $_REQUEST['sel_app_id'];
        $res = $dclass->select("s.*,a.app_type,a.app_name,m.email,m.fname,m.lname,m.status as user_status, f.feature_status, f.intid as id","tblmember_apps a INNER JOIN tblapp_support_config s ON a.intid=s.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id INNER JOIN tblmember m ON a.member_id=m.intid"," AND a.intid='".$sel_app_id."' AND a.app_status='active' AND f.feature_id='".$feature_id."' AND f.transaction_id != '0' ");
         
    }else{
        $sel_app_id = '';
        $res = $dclass->select("s.*,a.app_type,a.app_name,m.email,m.fname,m.lname,m.status as user_status, f.feature_status, f.intid as id","tblmember_apps a INNER JOIN tblapp_support_config s ON a.intid=s.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id INNER JOIN tblmember m ON a.member_id=m.intid INNER JOIN tblmore_app_settings t ON a.intid=t.app_id"," AND a.member_id='".$member_id."' AND a.app_status='active' AND f.feature_id='".$feature_id."' AND f.transaction_id != '0' order by s.dtadd DESC LIMIT 1");
        $sel_app_id = $res[0]['app_id'];
    }
    if(count($res) > 0){
            $intid = $res[0]['id'];
            $app_type = $res[0]['app_type'];
            $app_name = $res[0]['app_name'];
            if($app_type == 'ios')
                $ios_class = 'active';
            else if($app_type == 'android')        
                $android_class = 'active';
            else if($app_type == 'windows')        
                $windows_class = 'active';
       
            $status = $res[0]['status'];
            $feature_status = $res[0]['feature_status'];
            $app_logo = $res[0]['app_logo'];
           
            $app_logo_path = "memimages.php?max_width=118&max_width=118&imgfile=".APP_LOGO."/".$app_logo;
                
            if($status == 'save'){
                $save_button_class = 'active';
            }else if($status == 'publish'){       
                $publish_button_class = 'active';
                
            }else if($status == 'pause'){ 
            }
            
           $record_status = $res[0]['record_status'];
   }
    
    $sqltepm = "SELECT s.* FROM tblapp_support s INNER JOIN tblmember_apps a ON s.app_id=a.intid where 1 AND s.intid='".$_REQUEST['id']."' ";
    $restepm=$dclass->query($sqltepm);	
    
    include(INC."header.php"); 
?>


<!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side"> 
 <!-- Content Header (Page header) -->

 <?php include 'inc/app-navigate.php'; ?>
 
 <!-- Main content -->
 <section class="add-apps-page">
  <div class="col-md-12">
  <?php while($row=$dclass->fetchArray($restepm)){
      $num++; 
      
      $atach = $gnrl->get_attachment($row['intid']);
      
       if(count($atach) > 0)
            $attach_class = 'fa-paperclip';
       else
           $attach_class = '';
       
      
      ?>    
   <div class="support-request-bottom">
    <div class="request_top">
     <div class="col-md-6 request-date"><span>Request </span> #<?php echo $row['intid']; ?> On
      <time><?php echo $gnrl->get_usa_date($row['dtadd']); ?></time>
     </div>
     <div class="col-md-6 request-icon">
      <button class="btn fr delete_tiket" value="save" type="button">Delete Ticket</button>
      <a class="fr" href="#"><i class="fa fa-fw fa-edit"></i></a> <a class="fr" href="#"><i class="fa fa-fw <?php echo $attach_class; ?>"></i></a> </div>
    </div>
    <div class="cl"></div>
    <p><?php echo $row['message']; ?></p>
    <div class="cl"></div>
    <dd><?php if(count($atach) > 0){ 
        for($i=0;$i<count($atach); $i++){
            
           $attachment_path = "memimages.php?max_width=66&&imgfile=".SUPPORT_IMG."/".$atach[$i]['image'];
       
        ?>
        <img src="<?php echo $attachment_path; ?>" width="66" alt="" /> 
        <?php }} ?>
    </dd>
    <div class="cl"></div>
    <div class="icon_requst_p">
     <dd><i class="fa fa-fw fa-envelope" style="font-size:17px;"></i><span><?php echo $row['name']." (".$row['email'].")"; ?></span></dd>
     <dd><i class="fa fa-fw fa-fw fa-apple"></i> <span><?php echo $row['os'];?></span></dd>
     <dd><i class="fa fa-fw fa-map-marker"></i> <span><?php echo $row['region'];?></span></dd>
     <dd><i class="fa fa-fw fa-vimeo-square" style="font-size:18px;"></i> <span>Version <?php echo $row['version'];?></span></dd>
     <dd><i class="fa fa-fw fa-check"></i> <span><?php if ($row['status'] == 'replied') {echo 'Closed'; }else{ echo 'Open';} ?></span></dd>
    </div>
    <div class="cl height2"></div>
    <div class="col-md-12" >
     <label style="width:100%; font-size:18px; padding:0;">Reply</label>
     <textarea style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #eee; padding:15px; margin:5px 0;" class="textarea" placeholder="Message"></textarea>
     <div class="cl height1"></div>
     <div class="col-md-6">
      <label class="i-checks">
       <input type="checkbox" id="id[]" value="<?php echo $num; ?>">
       Close On Reply </label>
     </div>
     <div class="col-md-6">
      <button class="btn fr delete_tiket" value="save" type="button">Post Reply</button>
     </div>
    </div>
    <div class="cl height2"></div>
    <div class="reply_box">
     <div class="request_top" style="padding:5px 0 0 0;">
      <div class="request-date"><span>Request </span> #<?php echo $row['intid']; ?> On
       <time><?php echo $gnrl->get_usa_date($row['dtadd']); ?></time>
      </div>
     </div>
    </div>
   </div>
  <?php } ?>
   <div class="cl"></div>
  </div>
 </section>
 <!-- /.content --> 
</aside>
<!-- /.right-side -->
</div>
<div id="basic-modal-content">
 <h3 style="text-align:center; color:#333; font-size:28px;">Take Actions</h3>
 <div class="cl height1"></div>
 <div class="form-group">
  <label class="col-sm-12 control-label" style="text-align:right; padding:5px 0 0 0;" for="inputEmail3">Priority</label>
  <div class="col-sm-12">
   <select id="Types" class="my-dropdown form-control" name="Types">
    <option>Helvetica</option>
    <option>ArialMT</option>
    <option>TrebuchetMS</option>
    <option>Verdana</option>
    <option>Courier</option>
   </select>
  </div>
 </div>
 <div class="form-group">
  <label class="col-sm-12 control-label" style="text-align:right; padding:5px 0 0 0;" for="inputEmail3">Request Type</label>
  <div class="col-sm-12">
   <select id="Types" class="my-dropdown form-control" name="Types">
    <option>Helvetica</option>
    <option>ArialMT</option>
    <option>TrebuchetMS</option>
    <option>Verdana</option>
    <option>Courier</option>
   </select>
  </div>
 </div>
 <div class="form-group">
  <label class="col-sm-12 control-label" style="text-align:right; padding:5px 0 0 0;" for="inputEmail3">Assigned to</label>
  <div class="col-sm-12">
   <select id="Types" class="my-dropdown form-control" name="Types">
    <option>Helvetica</option>
    <option>ArialMT</option>
    <option>TrebuchetMS</option>
    <option>Verdana</option>
    <option>Courier</option>
   </select>
  </div>
 </div>
 <div class="cl"></div>
 <div class="col-md-12">
  <div class="col-md-6">
   <button style=" background:#00b7c5;color: #fff;font-size: 17px;visibility: inherit;width: 100%;" class="btn take_action " value="save" type="button">Delet Ticket</button>
  </div>
  <div class="col-md-6">
   <button style="background:#00b7c5;color: #fff;font-size: 17px;visibility: inherit;width: 100%;" class="btn take_action" value="save" type="button">Close Ticket</button>
  </div>
 </div>
 <div class="cl"></div>
</div>
<?php include(INC."footer.php"); ?>
<script type='text/javascript' src='js/support_popep.js'></script> 
<script type='text/javascript' src='js/basic.js'></script> 

<!-- Add fancyBox -->
</body></html>