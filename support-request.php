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
            $feature_status_id = $res[0]['id'];
            $feature_status = $res[0]['feature_status'];
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
   
   extract($_GET);
   
   if(isset($request_type)){
       if($request_type == 'feedback')
           $request_txt = 'Feedback';
       else if($request_type == 'bug')
           $request_txt = "Bug";
       else if($request_type == 'change')
           $request_txt = "Change Request";
       else
           $request_txt = 'All Request Types';
   }else
        $request_txt = 'Al Request Types';
    
    if(isset($priority)){
        if($priority != 'all')
            $priority_txt = ucfirst($priority);
        else
            $priority_txt = 'All Priorities';
    }else
        $priority_txt = 'All Priorities';
   
    include(INC."header.php"); 
?>


<!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side"> 

 <?php include 'inc/app-navigate.php'; ?>
<form id="supportlistfrm" name="supportlistfrm" method="get" enctype="mtabletipart/form-data">
 <!-- Main content -->
 <section class="add-apps-page">
 	<div class="col-md-12">
  	<div class="support-request-top">
   	
  <div class="fr col-md-4">  
  <div class="col-md-4">
   <select id="request_type" name="request_type" class="my-dropdown form-control">
        <option value="all" <?php if($request_type == 'all')  echo 'selected'; ?> >All Request Types</option>      
        <option value="feedback" <?php if($request_type == 'feedback')  echo 'selected'; ?>>Feedback</option>
        <option value="bug" <?php if($request_type == 'bug')  echo 'selected'; ?>>Bug</option>
        <option value="change" <?php if($request_type == 'change')  echo 'selected'; ?> >Change-Request</option>
    </select>
    </div>
    <div class="col-md-4">
    <select id="priority" name="priority" class="my-dropdown form-control">
    <option value="all" <?php if($priority == 'all')  echo 'selected'; ?>>All Priorities</option>      
    <option value="high" <?php if($priority == 'high')  echo 'selected'; ?>>High</option>
    <option value="medium" <?php if($priority == 'medium')  echo 'selected'; ?>>Medium</option>
    <option value="low" <?php if($priority == 'low')  echo 'selected'; ?>>Low</option>
    </select>
   </div>
   <div class="col-md-4">
    <input type="text" placeholder="Search" class="form-control wd valid" name="Search" id="Search"></div>
    </div>
    
   </div>
   <div class="cl"></div>
   <div role="grid" class="support-request-middel dataTables_wrapper form-inline">
     <table id="support-table" class="table table-bordered table-hover dataTable display" aria-describedby="support-table_info"> 
       <thead>
             <tr role="row">
                <th></th>
             </tr>
            </thead>
         <tbody role="alert" aria-live="polite" aria-relevant="all">
    <?php 
    
    //SET LIMIT FOR PAGING
	    if (isset($_REQUEST['pageno'])){
			  $limit = $_REQUEST['pageno'];
		}
		else{
			  $limit = 10;
		}
		$form = 'frm';
		
		if (isset($_REQUEST['limitstart'])){
			  $limitstart = $_REQUEST['limitstart'];
		}
		else{
			  $limitstart = 0;
		}
		
		if(isset($_REQUEST['button4']) || $_REQUEST['button4'] == "Search") {
			$limit = 10;
			$limitstart = 0;
		}
		if(isset($_REQUEST['Search']) && $_REQUEST['Search']!=''){
			$keyword = mysql_real_escape_string(trim($_REQUEST['Search']));
			$wh.= " AND (s.name like '%$keyword%' OR s.email like '%$keyword%' OR s.message like '%$keyword%')";
		}
                
                if(isset($_REQUEST['request_type']) && $_REQUEST['request_type'] !='' && $_REQUEST['request_type'] !='all'){
			$keyword = mysql_real_escape_string(trim($_REQUEST['request_type']));
			$wh.= " AND (s.request_type = '".$keyword."')";
		}
                
                 if(isset($_REQUEST['priority']) && $_REQUEST['priority']!='' && $_REQUEST['priority'] !='all'){
			$keyword = mysql_real_escape_string(trim($_REQUEST['priority']));
			$wh.= " AND (s.priority = '".$keyword."')";
		}
		
		$ssql="SELECT s.* FROM tblapp_support s INNER JOIN tblmember_apps a ON s.app_id=a.intid where 1 AND s.request_id='0' AND s.app_id='".$sel_app_id."' AND a.app_status='active' $wh";
		
		$sortby = ($_REQUEST['sb']=='') ? 's.intid' : $_REQUEST['sb'];
		$sorttype = ($_REQUEST['st']=='0') ? 'ASC' : 'DESC';
		
		$pagen = new vmPageNav($nototal, $limitstart, $limit, $form ,"black");
		$sqltepm=$ssql." ORDER BY $sortby $sorttype ";
		
		$restepm=$dclass->query($sqltepm);
                
            if(mysql_num_rows($restepm) <= 0){?>
     
            <tr><td>
                <div>No Record found</div>

         </td></tr>
    
   
        <?php }else{ 
            $num=0;
             while($row=$dclass->fetchArray($restepm)){
                       $num++;
                       $sr = $num+$limitstart;
                       
                       if($num % 2 == 0)
                           $class = 'event';
                       else
                           $class = 'odd';
                       
                       $chkatch = $gnrl->check_attachment($row['intid']);
                       if($chkatch)
                           $attach_class = 'fa-paperclip';
                       else
                           $attach_class = '';
                       
                       if($row['request_type'] == 'feedback'){
                           $request_type_class = 'fa-bullhorn';
                       }else if($row['request_type'] == 'bug'){
                           $request_type_class = "fa-bug";
                       }else if($row['request_type'] == 'edit'){
                           $request_type_class = "fa-edit";
                       }
                       
                       if($row['priority'] == 'high'){
                           $pr_type_class = 'fa-exclamation-circle';
                       }else if($row['request_type'] == 'medium'){
                           $pr_type_class = "fa-bug";
                       }else if($row['request_type'] == 'low'){
                           $pr_type_class = "fa-edit";
                       }
                       
            ?> 
   	
     
             <tr><td>
                <div class="request_top">
               <a href="support-request-details?id=<?php echo $row['intid']; ?>"> 
                  <div class="col-md-6 request-date">
                      <span>Request </span> #<?php echo $row['intid']; ?> On<time><?php echo $gnrl->get_usa_date($row['dtadd']); ?></time>
                  </div>
               </a>
               <div class="col-md-6 request-icon">
                   <a href="javascript:;" class="fr"><i class="fa <?php echo $pr_type_class; ?>"></i></a>
                  <a href="javascript:;" class="fr"><i class="fa fa-fw <?php echo $request_type_class; ?>"></i></a>
                  <a href="javascript:;" class="fr"><i class="fa fa-fw <?php echo $attach_class; ?>"></i></a>


               </div>
              </div>
              <div class="cl"></div>
              <div class="request_middel">
                        <div class="col-md-12">
              <?php echo $row['message']; ?> </div>
              </div>
              <div class="cl"></div>
              <div class="request_bottom">
                <div class="col-md-6 request-titel"> <?php echo $row['name']." (".$row['email'].")"; ?></div>
               <div class="col-md-6 request-button">
                <button class="btn take_action basic" value="save" type="button">Take Action</button>
               </div>
              </div>
              <div class="cl"></div>

             </td></tr>
        <?php }
             }
       ?>

      </tbody>        
    </table>
   </div>
   <div class="cl"></div>
   
  </div>
 </section>
</form>
 <!-- /.content --> 
</aside>
<!-- /.right-side -->
</div>

<div id="basic-modal-content">
         <h3 style="text-align:center; color:#333; padding:0 0; margin:0 0 0 0; font-size:28px;">Take Actions</h3>
         <label style="width:100%; text-align:center; color:#666; padding:10px 0 0 0;">Request #12345 On 10/08/2014</label>
         <div class="cl height1"></div>
         <div class="form-group">
          <label class="col-sm-12 control-label" style="text-align:right; padding:5px 0 0 0;" for="inputEmail3">Priority</label>
          <div class="col-sm-12"> 
          <select id="Types" class="my-dropdown form-control" name="Types">
          <option>Helvetica</option>
          <option>ArialMT</option>
          <option>TrebuchetMS</option>
          <option>Verdana</option>
          <option>Courier</option></select> 
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
          <option>Courier</option></select> 
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
          <option>Courier</option></select> 
          </div>
         </div>
         <div class="cl"></div>
         <div class="col-md-12">
         	<div class="col-md-6"><button style="color: #666;font-size: 17px;visibility: inherit;width: 100%;" class="btn take_action " value="save" type="button">Delet Ticket</button></div>
          <div class="col-md-6"><button style="color: #666;font-size: 17px;visibility: inherit;width: 100%;" class="btn take_action" value="save" type="button">Close Ticket</button></div>
          
         </div>
         <div class="cl"></div>
							</div>

<?php include(INC."footer.php"); ?>

<script type='text/javascript' src='js/support_popep.js'></script>
<script type='text/javascript' src='js/basic.js'></script>
<!-- DATA TABES SCRIPT -->
<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type='text/javascript' >
$(function(){
    $('#support-table').dataTable({
                    "bFilter": false,
                    "aSearchable": false,
                    "aoColumnDefs": [
                            { 'bSortable': false, 'aTargets': [ -1 ] }
                     ],
                    "language": {
                        "lengthMenu": "Display _MENU_ records per page",
                        "zeroRecords": "No Record Found",
                        "info": "Showing page _PAGE_ of _PAGES_",
                        "infoEmpty": "No records available",
                        "infoFiltered": "(filtered from _MAX_ total records)"
                    }
                });
    $('#request_type').off('change').on('change',function(){
        $('#supportlistfrm').submit();
    });
     $('#priority').off('change').on('change',function(){
        $('#supportlistfrm').submit();
    });
    <?php if(isset($_REQUEST['Search'])){ ?>
        $('#request_type').parent('div').find('.selectedTxt').text('<?php echo $request_txt; ?>');
        $('#priority').parent('div').find('.selectedTxt').text('<?php echo $priority_txt; ?>');
        $('#Search').val('<?php echo $_REQUEST['Search']; ?>');
        
    <?php }else{ ?>
          $('#request_type').parent('div').find('.selectedTxt').text('<?php echo $request_txt; ?>');
        $('#priority').parent('div').find('.selectedTxt').text('<?php echo $priority_txt; ?>');
       
    <?php } ?>
        
 });
</script>

</body></html>