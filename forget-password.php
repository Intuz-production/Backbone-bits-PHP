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
	if($dclass->numRows("SELECT NULL FROM tblmember WHERE email='".trim($email)."' ")>0){
		$r = $dclass->fetchArray($dclass->query("SELECT * FROM tblmember WHERE email='".trim($email)."'"));
		if($r['status'] == 'pending'){
                    $_SESSION['type'] = "err";
                    $_SESSION['msg'] = $gnrl->getMessage('EMAIL_SIGNUP_NOT_REGISTERED',$lang_id);
                    $gnrl->redirectTo("forget-password");
                  
                }else{
                    $newpass = $gnrl->generate_password();
                    $upd['password'] = md5($newpass);

                    $dclass->update("tblmember",$upd," email='".$r['email']."'");

                    $email_to = $r['email'];
                    $email_from = $gnrl->getSettings('varsenderemial');

                    $email_subject = "".BRAND." - Forgot Password? Revised Login Details";

                    if(!empty($r['fname'])){
                        $fullname = $r['fname'].' '.$r['lname'];
                    }else{
                        $fullname = $email_to;
                    }
                    $email_message .= "Hello ".$fullname.",";
                    $email_message .= "<p>Please find your revised login details below.<br /><br />";
                    $email_message.= "<b>Username</b>  ".$r['username']."<br />";
                    $email_message.= "<b>Password</b>  ".$newpass."<br /><br />";
                    $email_message.= "Regards,<br><strong>".BRAND." Support</strong>";		
                    $email_message .= "</p>";

                    $gnrl->email($email_from,$email_to,"","",$email_subject,$email_message,"html");
                     $_SESSION['type'] = "suc";
                     $_SESSION['msg'] = $gnrl->getMessage('PASS_SENT',$lang_id);
                     $gnrl->redirectTo("forget-password");
                }
		
	}else{ 
                 $_SESSION['type'] = "err";
                 $_SESSION['msg'] = $gnrl->getMessage('EMAIL_NOT_EXIST',$lang_id);
                 $gnrl->redirectTo("forget-password");
		$_REQUEST['msg'] = "emailnotexist";
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
     <input type="text" name="email"  class="form-control required email fl" placeholder="Email"/>
   </div>
   <div class="cl height"></div>
    <div class="footer">
   <button name="submit" value="1" type="submit" class="btn bg-olive btn-block login_but">Resend Password</button>
   
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
    $('#forgetfrm').validate();
  });
</script>
</body>
</html>