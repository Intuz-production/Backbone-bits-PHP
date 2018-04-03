<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 
    require("config/configuration.php");
   
    
    //Register
    if(isset($_POST['submit'])){
        extract($_POST);
        $error="";

       if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $_POST['email']) && trim($_REQUEST['email'])!="") 							
	 {
		  $email_error = 1;		 
		  $_SESSION['msg'] = $gnrl->getMessage('INVALID_EMAIL',$lang_id);
		  $_SESSION['custom_err_msg'] = $gnrl->getMessage('INVALID_EMAIL',$lang_id);
		  $_SESSION['type'] = "err";	
      }
       
       //Check for unique email
       $chk_user = $dclass->select("intid, email, status","tblmember"," AND email = '".$email."'  ");

       if(count($chk_user) > 0 && $chk_user[0]['status'] != 'pending'){
                  $user_exists_error = 1;		 
		  $_SESSION['msg'] = $gnrl->getMessage('EMAIL_EXISTS',$lang_id);
		  $_SESSION['type'] = "err";	
                  $gnrl->redirectTo("signup");
       }else if($chk_user[0]['status'] == 'pending') {
                 $id = $chk_user[0]['intid'];
       }        
       
        if($user_exists_error == '') {
           if(!$id){
                $ins['lang_id'] = 1;  
                $ins['email'] = $email;
                $ins['dtadd'] = date('Y-m-d h:i:s');
                $ins['dtmod'] = date('Y-m-d h:i:s');
                $ins['status'] = 'pending';

               $id = $dclass->insert("tblmember",$ins);
           }
           if($id){
                    $email_to = $email;
                    $email_from = $gnrl->getSettings('varsenderemial');

                    $email_subject = "Verify your email for - ".BRAND." ";
                    $vlink = SITE_URL."register?".$mcrypt->encrypt($id);
                    
                    $email_message = "<h1 style='color:#C80000; font-size:16px; font-weight:bold; padding:0px 0 10px 0;'>Activate Registration</h1>";		
                    $email_message.= "<p style='font-size:13px; color:#333;'>";			
                    $email_message .= "Hello ".$email_to.", <br/><br/>";
                    $email_message .= "Please find your registration activation link below:<br /><br />";
                    $email_message.= "<a href='".$vlink."'>Click here</a> to complete registration process<br /><br />";
                    $email_message.= "Regards,<br><strong>".BRAND." Support</strong>";		
                    $email_message .= "</p>";
                 $gnrl->email($email_from,$email_to,"","",$email_subject,$email_message,"html");
                $_SESSION['type'] = "suc";	
                $_SESSION['msg'] = $gnrl->getMessage('REG_VER_SUC',$lang_id);
                 $gnrl->redirectTo("signup");
           }else{
               $_SESSION['type'] = "err";	
               $_SESSION['msg'] = $gnrl->getMessage('REG_VER_FAIL',$lang_id);
               $gnrl->redirectTo("signup");

           }
       }
            
    }
    
if( $_SESSION['type'] == 'err') $status_class = 'err-msg'; else $status_class = 'suc-msg';
    
//HEADER  
include(INC."header-login.php");
?>
<body class="bg-black">


<div class="login-page">
  <div class="col-md-12 padding0">
   <div class="col-md-12 padding0"><img src="img/login-logo.png" alt=""></div>
  </div>
  <div class="cl"></div>
  <div class="col-md-12 login_b_p" id="login-box">
  <form id="regfrm" action="signup" method="post">
  <div class="body bg-gray">
 		<div class="<?php echo $status_class; ?>">
    <?php                   
                            if($_SESSION['msg']!=''){echo $_SESSION['msg'];unset($_SESSION['msg']);}
                           ?>
   </div>
 
   <div class="form-group border">
   <i class="fa fa-fw fa-user fl"></i>
     <input type="text" id="email" name="email" class="required email form-control fl" placeholder="Email"/>
   </div>
   <div class="cl height"></div>
    <div class="footer">

   <button type="submit" name="submit" value="1" class="btn bg-olive btn-block login_but wd">Sign Up</button>
   
   <p class="signup member"><a href="login" class="text-center">I already have a membership</a></p>
  </div>
  </div>
 </form>
  </div>
<div class="cl"></div>
  </div>
<?php include("inc/footer-login.php"); ?>
<script type="text/javascript">
        $(document).ready(function(){  
          $('#regfrm').validate({
               rules : {
                password : {
                    required:true,
                    minlength : 5
                },
                password_confirm : {
                    minlength : 5,
                    equalTo : "#password"
                }
               }
          });
           $('.success-message').delay(5000).fadeOut('slow');
              $('.error-message').delay(5000).fadeOut('slow');
              $('.warning-message').delay(5000).fadeOut('slow');
        });
      </script>
</body>
</html>