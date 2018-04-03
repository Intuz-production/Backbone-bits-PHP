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
	if($dclass->numRows("SELECT NULL FROM tblmember WHERE email='$email'")>0){
		$error = "1";
		$gnrl->addMessage('sameemail','err');
	}
	if($error==''){
		$ins['lang_id'] = 1;
                $ins['fname'] = mysql_real_escape_string($fname);
		$ins['lname'] = mysql_real_escape_string($lname);
                $ins['username'] = mysql_real_escape_string($username);
                $ins['password'] =  md5($password);
		$ins['email'] = mysql_real_escape_string($email);
		$ins['phone'] = mysql_real_escape_string($phone);
                $ins['dtadd'] = date('Y-m-d');
                $ins['dtmod'] = date('Y-m-d');
		$ins['status'] = mysql_real_escape_string($status);
		
		$id = $dclass->insert("tblmember",$ins);
		$gnrl->addMessage('add','succ');
		$gnrl->redirectTo("members.php");
	}
}

// delete from the database
if(isset($_REQUEST['a']) && $_REQUEST['a']==3){
	if(isset($_REQUEST['id']) && $_REQUEST['id']!=""){	
		$id = mysql_real_escape_string($_REQUEST['id']);
		$del="DELETE FROM tblmember WHERE intid='".$id."' ";
		$del1=$dclass->query($del);
		$gnrl->addMessage('del','succ');
		$gnrl->redirectTo("members.php");
	}
}

// update from the database
$intparantlocation="";
$varpageheading="";
$varpagebrtitle="";
$txtcontents="";
$varkeywords="";
$varmetadescription="";
$varimgpath = "";
  
if(isset($_REQUEST['a']) && $_REQUEST['a']==2){
    
	if(isset($_REQUEST['id']) && $_REQUEST['id']!=""){
		$id= mysql_real_escape_string($_REQUEST['id']);
		$sqltepm="SELECT * FROM tblmember WHERE intid='$id'";
		$restepm=$dclass->query($sqltepm);
		while($row=$dclass->fetchArray($restepm)){
			extract($row);
		}
		if(isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Update'){
                    
			extract($_POST);
			if($dclass->numRows("SELECT NULL FROM tblmember WHERE email='$email' AND intid!='$id'")>0){
				$error = "1";
				$gnrl->addMessage('sameemail','err');
			}
			
			if($error==''){
                                $ins['fname'] = mysql_real_escape_string($fname);
                                $ins['lname'] = mysql_real_escape_string($lname);
				$ins['username'] = mysql_real_escape_string($username);
				if($password!=''){$ins['password'] = md5($password);}
				$ins['email'] = mysql_real_escape_string($email);
                                $ins['phone'] = mysql_real_escape_string($phone);
                                $ins['dtmod'] = date('Y-m-d');
                                $ins['status'] = mysql_real_escape_string($status);
				$dclass->update("tblmember",$ins," intid='$id'");
				$gnrl->addMessage('edit','succ');
				$gnrl->redirectTo("members.php");
			}
		}
		
	}
}

if(isset($_REQUEST['chk'])){
	if(isset($_REQUEST['delete'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$dclass->delete("tblmember"," intid='$v'");
		}
		$gnrl->addMessage('del','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makeactive'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['status'] = "active";
			$dclass->update("tblmember",$ins," intid='$v'");
		}
		$gnrl->addMessage('active','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makeinactive'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['status'] = "inactive";
			$dclass->update("tblmember",$ins," intid='$v'");
		}
		$gnrl->addMessage('inactive','succ');
		$gnrl->redirectTo($pageu);
	}
	else if(isset($_REQUEST['makebanned'])){
		foreach($_REQUEST['chk'] as $k=>$v){
			$v = mysql_real_escape_string($v);
			$ins['status'] = "banned";
			$dclass->update("tblmember",$ins," intid='$v'");
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
                        Members
                        
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Members</li>
                       
                    </ol>
                </section>
  <?php
	if(isset($_REQUEST['script']) && ($_REQUEST['script']=='add' || $_REQUEST['script']=='edit')){
	?><form id="frm" class="appnitro" enctype="multipart/form-data" method="post" action="">
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
                                                  <label for="exampleInput">User name</label>
                                                   <input type="text" name="username" id="username" value="<? echo $username;?>" class="form-control"  placeholder="Enter username" <?php if($_REQUEST['script']=='edit'){?> readonly="readonly" <?php }?> >
                                              </div>
                                               <div class="form-group">
                                                  <label for="exampleInputPassword1">Password</label>
                                                  <input name="password" type="password" id="password"  class="<?php if($_REQUEST['script']=='add'){?>required<?php }?> form-control" placeholder="Password">
                                              </div>
                                              <div class="form-group">
                                                  <label for="exampleInputEmail1">Email address</label>
                                                  <input name="email" type="text" id="email" value="<?php echo $email?>" class="required email form-control" placeholder="Enter email">
                                             </div>
                                             
                                              <div class="form-group">
                                                  <label for="exampleInputEmail1">First Name</label>
                                                  <input name="fname" type="text" id="fname" value="<?php echo $fname;?>" class="required form-control"  placeholder="Enter first name">
                                              </div>
                                              <div class="form-group">
                                                  <label for="exampleInputEmail1">Last Name</label>
                                                  <input name="lname" type="text" id="lname" value="<?php echo $lname;?>" class="required form-control" placeholder="Enter last name" />
                                              </div>
                                              <div class="form-group">
                                                  <label for="exampleInputEmail1">Phone</label>
                                                  <input name="phone" type="text" id="phone" value="<?php echo $phone;?>" class="form-control" placeholder="Enter phone" />
                                              </div>
                                              <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control">
                                                    <option value="active" <? if($status=="active"){echo "selected";}?>>Active</option>
                                                    <option value="inactive" <? if($status=="inactive"){echo "selected";}?>>Inactive</option>
                                                     <option value="banned" <? if($status=="banned"){echo "selected";}?>>Banned</option>
                                                </select>
                                            </div>
                                          </div><!-- /.box-body -->

                                          <div class="box-footer"> 
                                                <?php
                                               if($_REQUEST['script']=='add'){$value="Submit";}
                                               else if($_REQUEST['script']=='edit'){$value="Update";} ?>

                                              <button name="Submit" type="submit" value="<?php echo $value;?>" class="btn btn-primary">Submit</button>
                                               <button name="Submit2" type="reset" onClick="location.href='members.php';" value="Cancel"  class="btn btn-primary">Cancel</button>
                                         
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
			$wh.= " AND (email like '%$keyword%' OR username like '%$keyword%' OR fname like '%$keyword%' OR lname like '%$keyword%' OR CONCAT( fname, ' ', lname ) LIKE '%$keyword%')";
		}
		
		$sortby = ($_REQUEST['sb']=='') ? 'intid' : $_REQUEST['sb'];
		$sorttype = ($_REQUEST['st']=='0') ? 'ASC' : 'DESC';
		
		$sqltepm="SELECT * FROM tblmember where 1 $wh ORDER BY $sortby $sorttype ";
                
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
                                                <th>Full Name</th>
                                                <th>Email</th>
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
                                                <td><?php echo $row['fname']." ".$row['lname'];?></td>
                                                <td><?php echo $row['email'];?></td>
                                                <td><?php echo date('m-d-Y',strtotime($row['dtadd']));?></td>
                                                <td><?php if($row['status']=='active'){echo '<span class="tick"><i class="icon-ok-sign"></i></span>';}else if($row['status']=='inactive'){echo '<span class="deactive-status"><i class="icon-remove-sign"></i></span>';}else{echo '<span class="banned-status"><i class="icon-banned-sign"></i></span>';}?></td>
                                                <td><a href="members.php?a=2&amp;script=edit&amp;id=<?php echo $row['intid'];?>" class="gray"><div class="edit"><i class="icon-edit"></i></div></a></td>
                                                  <td>
                                                    <?php if($row['intid']!=$_SESSION['adminid']){?>
                                              <a href="members.php?a=3&amp;id=<?php echo $row['intid'];?>" class="gray" onclick="return confirm('Are you sure want to delete?');"><div class="delete"><i class="icon-remove-sign"></i></div></a>
                                                <?php } ?>
                                              </td>
                                            </tr>
                                            <?php }?>
                                           
                                        </tbody>
                                       
                                         <tfoot>
                                             <tr>
                                                <th>Full Name</th>
                                                <th>Email</th>
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

    </body>
</html>