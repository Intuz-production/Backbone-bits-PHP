<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 
    require("../config/configuration.php");
    if(!$gnrl->checkLogin()){
	$gnrl->redirectTo("login.php?msg=logfirst");
    }
    $adtype=$_SESSION['admintype'];
    $adid=$_SESSION['adminid'];

if(isset($_POST['Submit']) && $_POST['Submit']=='Update'){
   
	foreach($_POST['options'] as $k1=>$v1){
		$group = $k1;
		foreach($v1 as $k=>$v){
			
			if($dclass->numRows("SELECT NULL FROM tblsettings WHERE option_name='".$k."'")>0){
								
					$upd['value'] = $v;
					$upd['option_group'] = $group;
			
				
				$dclass->update("tblsettings", $upd, " `option_name` = '".$k."'");
			}else{
				$dclass->query("INSERT INTO tblsettings SET `option_name`='".$k."', value='".$v."', option_group='".$group."'");
			}
		}
	}
	
	$gnrl->addMessage('edit','succ');
	$gnrl->redirectTo($pageu);
}
$row_temp = $dclass->select("*","tblsettings");
$row = array();
for($i=0;$i<count($row_temp);$i++){
	$row[$row_temp[$i]['option_group']][$row_temp[$i]['option_name']] = $row_temp[$i]['value'];
}
    include(INC."header.php"); 
?>
<?php include(INC."left.php"); ?>

<!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side"> 
 <!-- Content Header (Page header) -->
 <section class="content-header">
  <h1> Global Settings </h1>
  <ol class="breadcrumb">
   <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
   <li class="active">Settings</li>
  </ol>
 </section>
 <form id="frm" class="appnitro" enctype="multipart/form-data" method="post" action="">
 <section class="content">
 <div class="row">
 <div class="col-md-6">
 <div class="box box-primary">
 <div class="box-header">
  <h3 class="box-title"><?php echo ucfirst($_REQUEST['script']); ?></h3>
 </div>
 <!-- /.box-header --> 
 <!-- form start -->
 
 <form role="form" method="post" enctype="multipart/form-data" name="frmsettings" id="frmsettings" class="appnitro">
  <div class="box-body">
  <div class="form-group">
   <label class="description" for="element_2">Sending Email</label>
   <input name="options[general][varsenderemial]" type="text" id="varsenderemial" value="<?php echo $row['general']['varsenderemial']?>" class="required email form-control" />
   <b>*</b> </div>
  <div class="form-group">
   <label class="description" for="element_2">Receiving Email</label>
   <div>
    <input name="options[general][varemailrecive]" type="text" id="varemailrecive" value="<?php echo $row['general']['varemailrecive']?>" class="required email form-control" />
    <b>*</b></div>
  </div>
  <div class="form-group">
   <label class="description" for="element_2">No. of Rows in Page</label>
   <select name="options[general][recordperpage]" id="recordperpage" class="form-control element">
    <?php for($i=5;$i<=50; $i+=5){?>
    <option value="<?php echo $i;?>" <?php if($row['general']['recordperpage']==$i){echo "selected";}?>><?php echo $i;?></option>
    <?php }?>
   </select>
   <b>*</b> </div>
  <div class="form-group">
   <label class="description" for="element_2">Google Analytics Code</label>
   <textarea name="options[general][google_analytics_code]" rows="5" class="form-control element"><?php echo $row['general']['google_analytics_code'];?></textarea>
  </div>
  <div class="form-group">
   <label class="description" for="element_2">Page Browser Title</label>
   <input name="options[seo][title]" type="text" value="<?php echo $row['seo']['title']?>" class="form-control" />
  </div>
  <div class="form-group">
   <label class="description" for="element_8">Page Meta Keywords</label>
   <textarea name="options[seo][s_keyword]" cols="30" rows="3" id="s_keyword" class="form-control"><?php echo $row['seo']['s_keyword']?></textarea>
  </div>
  <div class="form-group">
   <label class="description" for="element_8">Page Meta Description </label>
   <textarea name="options[seo][s_metadesc]" cols="30" rows="3" id="s_metadesc" class="form-control"><?php echo $row['seo']['s_metadesc']?></textarea>
  </div>
  <div class="form-group">
   <label class="description" for="element_2">Facebook Link</label>
   <input name="options[social][facebook]" type="text" value="<?php echo $row['social']['facebook']?>" class="form-control" />
   <small>( Please add full URL including "http://" or "https://" &nbsp;&nbsp; for e.g.: https://facebook.com/Google )</small> </div>
  <div class="form-group">
   <label class="description" for="element_2">Twitter Link</label>
   <div>
    <input name="options[social][twitter]" type="text" value="<?php echo $row['social']['twitter']?>" class="form-control" />
    <small>( Please add full URL including "http://" or "https://" &nbsp;&nbsp; for e.g.: https://twitter.com/google )</small> </div>
   <div class="form-group">
    <label class="description" for="element_2">Google+ Link</label>
    <input name="options[social][google]" type="text" value="<?php echo $row['social']['google']?>" class="form-control" />
    <small>( Please add full URL including "http://" or "https://" &nbsp;&nbsp; for e.g.: https://plus.google.com/+google/posts )</small> </div>
   <div class="box-footer">
    <button name="Submit" type="submit" value="Update" class="btn btn-primary">Submit</button>
   </div>
  </div>
 </form>
 </div>
 <!-- /.box -->
 </div>
 </div>
 </section>
</aside>
<!-- /.right-side -->
</div>
<!-- ./wrapper -->

<?php include(INC."footer.php"); ?>

<!-- DATA TABES SCRIPT --> 
<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script> 
<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script> 

<!-- page script --> 
<script type="text/javascript">
            $(function() {
                $('#frm').validate();
                $("#example1").dataTable();
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });
        </script>
</body></html>