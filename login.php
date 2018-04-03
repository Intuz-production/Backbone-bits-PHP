<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/

require("config/configuration.php");

//Logout
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
    $affected_row = $gnrl->save_checkin_checkout($_SESSION['custid'], $_SESSION['agents_cust_id'], LOGOUT, 'logout', '');
    session_destroy();
	$gnrl->redirectTo("login?msg=logout");
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'expire') {   
    $gnrl->save_checkin_checkout($_SESSION['custid'], $_SESSION['agents_cust_id'], LOGOUT, 'logout', '');
    unset($_SESSION['sel_app_id']);
    unset($_SESSION['custid']);
	unset($_SESSION['custuser']);
    unset($_SESSION['custname']);
	$gnrl->redirectTo("login?msg=sessionexpire");
}
 
// Login
if(isset($_POST['submit'])) {
    extract($_POST);
    $error = "";

    if($userid == "") {
        $error = 1;
    }
    if($password == "") {
        $error = 1;
    }

    if($error == "") {
        $res = $dclass->select("*", "tblmember", " AND username='" . $userid . "' AND password='" . md5($password) . "' AND status='active' ");
        
        if(count($res) > 0) {            
            if(isset($_POST['remember_me']) && $_POST['remember_me'] == 'on'){ 
				setcookie ('cook_admin_username', $userid, time() + 100000);
				setcookie ('cook_admin_password', $password, time() + 100000);
				setcookie ('Cust_RememberMe', "Yes", time() + 100000);
		    }
            else {
				setcookie ('cook_admin_username', '', time() - 100000);
				setcookie ('cook_admin_password', '', time() - 100000);
				setcookie ('Cust_RememberMe', '', time() - 100000);
            }

            $_SESSION['custname'] = ucfirst($res[0]['fname'])." ".  ucfirst($res[0]['lname']);
            $_SESSION['custid'] = $res[0]['intid'];
            $_SESSION['agents_cust_id'] = $res[0]['intid'];
            $_SESSION['custuser'] = $res[0]['username'];
            $_SESSION['role'] = $res[0]['role'];
            $_SESSION['parent_id'] = $res[0]['parent_id'];

            $gnrl->redirectTo("profile");
        }
        else {
            setcookie ('cook_admin_username', '', time()-100000);
    		setcookie ('cook_admin_password', '', time()-100000);
    		setcookie ('Cust_RememberMe', '', time()-100000);
            $_SESSION['type'] = "err";	
            $_SESSION['msg'] = $gnrl->getMessage('LOGIN_INVALID',$lang_id);
            $gnrl->redirectTo("login");
        }
    }
}

if( $_SESSION['type'] == 'err') $status_class = 'err-msg'; else $status_class = 'suc-msg';

//HEADER  
include(INC."header-login.php");

?><body class="bg-black login-bg">
    <div class="login-page">
        <div class="col-md-12 padding0">
            <div class="col-md-12"><img src="img/login-logo.png" alt=""></div>
        </div>
        <div class="cl"></div>
        <div class="col-md-12 login_b_p" id="login-box">
            <form id="loginfrm" action="" method="post">
                <div class="body bg-gray">
                    <div class="<?php echo $status_class; ?>"><?php                   
                        if($_SESSION['msg'] != '') {
                            echo $_SESSION['msg'];unset($_SESSION['msg']);
                        }
                        if(isset($_COOKIE['cook_admin_username'])) { 
                            $userid = $_COOKIE['cook_admin_username']; 
                        } 
                        else {
                            $userid = '';
                        }
                        if(isset($_COOKIE['cook_admin_password'])) { 
                            $password = $_COOKIE['cook_admin_password']; 
                        }
                        else {
                            $password = '';
                        }
                        if(isset($_COOKIE['Cust_RememberMe'])) { 
                            $checked = "checked"; 
                        }
                        else { 
                            $checked = "";
                        }
                    ?></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-user fl"></i>
                        <input type="text" name="userid" value="<?php echo $userid; ?>"  class="form-control required fl" placeholder="User ID"/>
                    </div>
                    <div class="cl height"></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-unlock-alt fl"></i>
                        <input type="password" name="password" value="<?php echo $password; ?>" class="form-control required fl" placeholder="Password"/>
                    </div>
                    <div class="cl height"></div>
                    <div class="form-group bg_none remember_me">
                        <div class="fl">
                            <input type="checkbox" name="remember_me" value="on" <?php echo $checked; ?>/>Remember me 
                        </div>
                        <div class="fr"><a href="forget-password">I forgot my password</a></div>
                        <div class="cl"></div>
                    </div>
                    <div class="cl height"></div>
                    <div class="footer">
                        <button name="submit" value="1" type="submit" class="btn bg-olive btn-block login_but">Sign In</button>
                        <p class="signup"><a href="register">Create Account Now</a></p>
                    </div>
                </div>
            </form>
        </div>
        <div class="cl"></div>
    </div>
    <div class="cl"></div>

    <?php include(INC."footer-login.php"); ?>
    <script type="text/javascript">
        $(document).ready(function(){  
            $('#loginfrm').validate();
            $('.success-message').delay(5000).fadeOut('slow');
            $('.error-message').delay(5000).fadeOut('slow');
            $('.warning-message').delay(5000).fadeOut('slow');
        });
    </script>
</body>
</html>
