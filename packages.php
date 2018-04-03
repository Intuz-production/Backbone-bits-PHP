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
if(isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Submit'){
	extract($_POST);
	if($dclass->numRows("SELECT NULL FROM tblpackages WHERE pcost='$pcost'")>0){
		$error = "1";
		$gnrl->addMessage('sameemail','err');
	}
	if($error==''){
		$ins['pname'] = mysql_real_escape_string($pname);
		$ins['ptype'] =  $ptype;
		$ins['pcost'] = mysql_real_escape_string($pcost);
		$ins['pintval'] = mysql_real_escape_string($pintval);
		$ins['pdate'] = date('Y-m-d');
		$ins['pstatus'] = mysql_real_escape_string($pstatus);
                
		$id = $dclass->insert("tblpackages",$ins);
		$gnrl->addMessage('add','succ');
		$gnrl->redirectTo("packages.php");
	}
}

// delete from the database
if(isset($_REQUEST['a']) && $_REQUEST['a']==3){
	if(isset($_REQUEST['id']) && $_REQUEST['id']!=""){	
		$id = mysql_real_escape_string($_REQUEST['id']);
		$del="DELETE FROM tblpackages WHERE intid='".$id."'";
		$del1=$dclass->query($del);
		$gnrl->addMessage('del','succ');
		$gnrl->redirectTo("packages.php");
	}
}

  
if(isset($_REQUEST['a']) && $_REQUEST['a']==2){
    
	if(isset($_REQUEST['id']) && $_REQUEST['id']!=""){
		$id= mysql_real_escape_string($_REQUEST['id']);
		$sqltepm="SELECT * FROM tblpackages WHERE intid='$id'";
		$restepm=$dclass->query($sqltepm);
		while($row=$dclass->fetchArray($restepm)){
			extract($row);
		}
		if(isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Update'){
                    
			extract($_POST);
			if($dclass->numRows("SELECT NULL FROM tblpackages WHERE pcost='$pcost' AND intid!='$id'")>0){
				$error = "1";
				$gnrl->addMessage('sameemail','err');
			}
			
			if($error==''){
				$ins['pname'] = mysql_real_escape_string($pname);
				$ins['ptype'] = $ptype;
				$ins['pcost'] = mysql_real_escape_string($pcost);
                                $ins['pintval'] = mysql_real_escape_string($pintval);
				$ins['pstatus'] = mysql_real_escape_string($pstatus);
				$dclass->update("tblpackages",$ins," intid='$id'");
				$gnrl->addMessage('edit','succ');
				$gnrl->redirectTo("packages.php");
			}
		}
		
	}
}

if(isset($_REQUEST['chk'])){
	if(isset($_REQUEST['delete'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$dclass->delete("tblpackages"," intid='$v'");
		}
		$gnrl->addMessage('del','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makeactive'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['pstatus'] = "active";
			$dclass->update("tblpackages",$ins," intid='$v'");
		}
		$gnrl->addMessage('active','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makeinactive'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['pstatus'] = "inactive";
			$dclass->update("tblpackages",$ins," intid='$v'");
		}
		$gnrl->addMessage('inactive','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makebanned'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['pstatus'] = "banned";
			$dclass->update("tblpackages",$ins," intid='$v'");
		}
	}
	
}
    include(INC."header.php"); 
?>
 
<?php include(INC."left.php"); ?>

      
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Subscription Packages
                        
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Package</li>
                       
                    </ol>
                </section>
  <?php
	if(isset($_REQUEST['script']) && ($_REQUEST['script']=='add' || $_REQUEST['script']=='edit')){
	?><form id="pckfrm" class="appnitro" enctype="multipart/form-data" method="post" action="">
                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                      <div class="box-header">
                                          <h3 class="box-title"><?php echo ucfirst($_REQUEST['script']); ?></h3>
                                      </div><!-- /.box-header -->
                                      <!-- form start -->
                                      <form role="form">
                                          <div class="box-body">
                                              <div class="form-group">
                                                  <label for="exampleInput">Package name</label>
                                                   <input type="text" name="pname" id="pname" value="<? echo $pname;?>" class="required form-control"  placeholder="Enter Package name" >
                                              </div>
                                              <div class="form-group">
                                                <label>Package Type</label>
                                                <select name="ptype" class="form-control">
                                                    <option value="fixed" <?php if($ptype=="fixed"){echo "selected";}?>>Fixed</option>
                                                    <option value="recurring" <?php if($ptype=="recurring"){echo "selected";}?>>Recurring</option>
                                                   
                                                </select>
                                            </div>
                                              <div class="form-group">
                                                  <label for="exampleInputEmail1">Package Cost</label>
                                                  <input name="pcost" type="text" id="pcost" value="<?php echo $pcost?>" class="required number form-control" placeholder="Enter package cost">
                                             </div>
                                              <div class="form-group" id="div-pintval" <?php if($ptype=="fixed"){ ?> style="display:none;" <?php } ?> >
                                                  <label for="exampleInputPassword1">Package Interval(Monthly)</label>
                                                  <input name="pintval" type="text" id="pintval" value="<?php echo $pintval?>"  class="required number form-control" placeholder="pintval">
                                              </div>
                                              <div class="form-group">
                                                <label>Status</label>
                                                <select name="pstatus" class="form-control">
                                                    <option value="active" <? if($pstatus=="active"){echo "selected";}?>>Active</option>
                                                    <option value="inactive" <? if($pstatus=="inactive"){echo "selected";}?>>Inactive</option>
                                                     
                                                </select>
                                            </div>
                                              
                                          </div><!-- /.box-body -->

                                          <div class="box-footer"> 
                                                <?php
                                               if($_REQUEST['script']=='add'){$value="Submit";}
                                               else if($_REQUEST['script']=='edit'){$value="Update";} ?>

                                              <button name="Submit" type="submit" value="<?php echo $value;?>" class="btn btn-primary">Submit</button>
                                               <button name="Submit2" type="reset" onClick="location.href='packages.php';" value="Cancel"  class="btn btn-primary">Cancel</button>
                                         
                                          </div>
                                      </form>
                                  </div><!-- /.box -->
                    </div>   
                    </div>
               </section>
        </form>    
                
        <?php }else{
                //SET LIMIT FOR PAGING
	    if (isset($_REQUEST['pageno'])){
			  $limit = $_REQUEST['pageno'];
		}
		else{
			  $limit = $gnrl->getSettings('recordperpage');
			  if($limit==0){ $limit = 10;}else{ $limit = $limit;}
		}
		$gnrl->getSettings('recordperpage');
		$form = 'frm';
		
		if (isset($_REQUEST['limitstart'])){
			  $limitstart = $_REQUEST['limitstart'];
		}
		else{
			  $limitstart = 0;
		}
		
		if(isset($_REQUEST['keyword']) && $_REQUEST['keyword']!=''){
			$keyword =  mysql_real_escape_string($_REQUEST['keyword']);
			$wh.= " AND (pname like '%$keyword%' OR ptype like '%$keyword%' OR pcost like '%$keyword%' )";
		}
		
		$sortby = ($_REQUEST['sb']=='') ? 'intid' : $_REQUEST['sb'];
		$sorttype = ($_REQUEST['st']=='0') ? 'ASC' : 'DESC';
		
		$sqltepm="SELECT * FROM tblpackages where 1 $wh ORDER BY $sortby $sorttype ";
                
		$nototal = $dclass->numRows($sqltepm);
		
		$restepm=$dclass->query($sqltepm);
	  ?>
        <form id='frm' name="frm" method="get" enctype="multipart/form-data">
 
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Records</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                     <?php 
                                            if(mysql_num_rows($restepm) > 0){
                                            $num=0;
                                           
                                            ?>
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Package</th>
                                                <th>Type</th>
                                                <th>Cost</th>
                                                <th>Date Added</th>
                                                <th>Status</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                         
                                        <tbody>  
                                          <?php  while($row=$dclass->fetchArray($restepm)){
                                            $num++;
                                            $sr = $num+$limitstart;
                                            ?>
                                            <tr>
                                                <td><?php echo $row['pname'];?></td>
                                                <td><?php echo $row['ptype'];?></td>
                                                <td><?php echo $row['pcost']." $";?></td>
                                                <td><?php echo date('m-d-Y',strtotime($row['pdate']));?></td>
                                                <td><?php if($row['pstatus']=='active'){echo '<span class="tick"><i class="icon-ok-sign"></i></span>';}else{echo '<span class="deactive-pstatus"><i class="icon-remove-sign"></i></span>';}?></td>
                                                <td><a href="packages.php?a=2&amp;script=edit&amp;id=<?php echo $row['intid'];?>" class="gray"><div class="edit"><i class="icon-edit"></i></div></a></td>
                                                  <td>
                                                    <?php if($row['intid']!=$_SESSION['adminid']){?>
                                              <a href="packages.php?a=3&amp;id=<?php echo $row['intid'];?>" class="gray" onclick="return confirm('Are you sure want to delete?');"><div class="delete"><i class="icon-remove-sign"></i></div></a>
                                                <?php } ?>
                                              </td>
                                            </tr>
                                            <?php }?>
                                           
                                        </tbody>
                                       
                                         <tfoot>
                                             <tr>
                                               <th>Package</th>
                                                <th>Type</th>
                                                <th>Cost</th>
                                                <th>Date Added</th>
                                                <th>Status</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                     <?php }else{ ?>
                                            
                                            
                                         <tbody role="alert" aria-live="polite" aria-relevant="all"><tr class="odd"><td valign="top" colspan="6" class="dataTables_empty">No matching records found</td></tr></tbody>
                                        <?php }?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                            <!-- /.box -->
                        </div>
                    </div>

                </section><!-- /.content -->
                 <input type="hidden" name="a" value="<?php echo @$_REQUEST['a'];?>" />
                <input type="hidden" name="st" value="<?php echo @$_REQUEST['st'];?>" />
                <input type="hidden" name="sb" value="<?php echo @$_REQUEST['sb'];?>" />
                <input type="hidden" name="np" value="<?php //echo @$_SERVER['HTTP_REFERER'];?>" />
              </form>
        <?php } ?>
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->


        <?php include(INC."footer.php"); ?>
        
        <!-- DATA TABES SCRIPT -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <!-- page script -->
        <script type="text/javascript">
            $(function() {
                $('#pckfrm').validate();
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

    </body>
</html>