<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 

require("config/configuration.php");


if(isset($_POST['submit'])){
	extract($_POST);
	
		$r = $dclass->fetchArray($dclass->query("SELECT * FROM tblmember WHERE LOWER(username) ='".$_REQUEST['username']."'"));
                
		if(!empty($r)){
                    $_SESSION['type'] = "err";
                    $_SESSION['msg'] = $gnrl->getMessage('USER_EXISTS',$lang_id);
                    $gnrl->redirectTo("register-agent?reg&token_id=".  base64_encode($_REQUEST['member_id']));
                  
                }else{
                    
                    $ra = $dclass->fetchArray($dclass->query("SELECT * FROM tblmember WHERE intid ='".$_REQUEST['member_id']."'"));
                    
                    if($ra['status']=='waiting'){
                    $upd['password'] = md5($_REQUEST['password']);
                    $upd['username'] = $_REQUEST['username'];
                    $upd['status'] = 'active';

                    $dclass->update("tblmember",$upd," intid='".$_REQUEST['member_id']."'");

                    $email_to = $ra['email'];
                    $email_from = $gnrl->getSettings('varsenderemial');

                    $email_subject = "".BRAND." - Registration Successfull";

                    if(!empty($ra['fname'])){
                        $fullname = $ra['fname'].' '.$ra['lname'];
                    }else{
                        $fullname = $email_to;
                    }
                    $email_message .= "Hello ".$fullname.",";
                    $email_message .= "<p>You account has been activated successfully.<br /><br />Click on the following link to login.</br><a href='".SITE_URL."login'>".SITE_URL."login</a></p>";
                    $email_message.= "Regards,<br><strong>".BRAND."</strong>";		
                    $email_message .= "</p>";

                    $gnrl->email($email_from,$email_to,"","",$email_subject,$email_message,"html");
                     $_SESSION['type'] = "suc";
                     $_SESSION['msg'] = $gnrl->getMessage('REG_SUC',$lang_id);
                     $gnrl->redirectTo("login");
                }else{
                    $_SESSION['type'] = "suc";
                     $_SESSION['msg'] = $gnrl->getMessage('ALREADY_ACTIVATED',$lang_id);
                     $gnrl->redirectTo("login");
                }
                }
		
	
}

if( $_SESSION['type'] == 'err') $status_class = 'err-msg'; else $status_class = 'suc-msg';

//HEADER  
include(INC."header-login.php");

?>
<body class="bg-black login-bg">

<div class="login-page">
  <div class="col-md-12 padding0">
   <div class="col-md-12 padding0"><img src="img/login-logo.png" alt=""></div>
  </div>
  <div class="cl"></div>
  <div class="col-md-12 login_b_p" id="login-box">
  <div class="<?php echo $status_class; ?>">
  <?php     
  

                            if($_SESSION['msg']!=''){echo $_SESSION['msg'];unset($_SESSION['msg']);}
                           ?>
 </div>
 <form id="forgetfrm" action="" method="post">
  <div class="body bg-gray">
   <div class="form-group border"> 
			<i class="fa fa-fw fa-user fl"></i>      
     <input type="text" name="username" id="username" class="form-control required fl" placeholder="Username"/>
     <input type="hidden" name="member_id" id="member_id" class="form-control required fl" value="<?php echo base64_decode($_REQUEST['token_id']) ?>"/>
     
   </div>
      <div class="form-group border"> 
						<i class="fa fa-fw fa-unlock-alt fl"></i> 
      <input type="password" name="password" id="password" class="form-control required fl" placeholder="Password"/>
      </div>
      <div class="form-group border">  
						<i class="fa fa-fw fa-unlock-alt fl"></i>
     <input type="password" name="conf_password" id="conf_password" class="form-control required fl" placeholder="Confirm Password"/>
      </div>
   <div class="cl height"></div>
    <div class="footer">
   <button name="submit" value="1" type="submit" class="btn bg-olive btn-block login_but">Register</button>
   
   <p class="signup"><a href="login.php">Back to login</a></p>
  </div>
  </div>
 </form>
  </div>
<div class="cl"></div>
  </div>
<?php include(INC."footer-login.php"); ?>
<script type="text/javascript">
  $(document).ready(function(){ 
    $('#forgetfrm').validate({
                     onkeyup: function(element) {$(element).valid()},
                    rules: {
                        
                        username: "required",
                        password: "required",
                        conf_password: "required",
                        email : {
                                required : true ,
                                email: true        
                        },
                       password: {
                                minlength : 5
                        },
                       conf_password: {
                                  minlength : 5,
                                 equalTo : "#password"
                        }
                       
                    },
                    messages: {
                       
                        username: "",
                        new_password:"",
                        conf_new_password:"",
                    }
           });
    
    
  });
</script>
</body>
</html>