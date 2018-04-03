<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ 

/*
 * This file for handle new register member after register request and display form for user details
 */

require("config/configuration.php");

if ($gnrl->checkMemLogin()) {
    $gnrl->redirectTo("apps?list=apps");
}

//Register
$lang_id = 1;
if (isset($_POST['submit'])) {
    extract($_POST);
    $error = "";
    if ($username == "") {
        $username_error = 1;
    }
    if ($fname == "") {
        $fname_error = 1;
    }
    if ($lname == "") {
        $lname_error = 1;
    }
    if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $_POST['email']) && trim($_REQUEST['email']) != "") {
        $email_error = 1;
        $_SESSION['msg'] = $gnrl->getMessage('INVALID_EMAIL', $lang_id);
        $_SESSION['custom_err_msg'] = $gnrl->getMessage('INVALID_EMAIL', $lang_id);
        $_SESSION['type'] = "err";
        $gnrl->redirectTo("register");
    }
    if ($password == "") {
        $password_error = 1;
    }

    //Check for unique user name
    $chk_email = $dclass->select("email", "tblmember", " AND email = '" . $email . "' ");

    if (count($chk_email) > 0) {
        $user_exists_error = 1;
        $_SESSION['msg'] = $gnrl->getMessage('EMAIL_EXISTS', $lang_id);
        $_SESSION['type'] = "err";
        $gnrl->redirectTo("register");
        $user_exists_error = 1;
    }

    if ($fname_error == '' && $lname_error == '' && $email_error == "" && $error == "" && $user_exists_error == '' && $username_error == ''){
        $role = 'admin';
        $package_id = 1;
        $ins['lang_id'] = $lang_id;
        $ins['parent_id'] = 0;
        $ins['package_id'] = $package_id;
        $ins['package_type'] = 'subscription';
        $ins['fname'] = addslashes(stripslashes($fname));
        $ins['lname'] = addslashes(stripslashes($lname));
        $ins['username'] = $username;
        $ins['password'] = md5($password);
        $ins['email'] = $email;
        $ins['dtadd'] = date('Y-m-d h:i:s');
        $ins['status'] = 'active';
        $ins['role'] = $role;

        $id = $dclass->insert("tblmember", $ins);
        
        //------------Insert-----tblplan_log ---------------
        $insp['package_id'] = $package_id;
        $insp['member_id'] = $id;
        $insp['dtadd'] = date('Y-m-d h:i:s');
        $dclass->insert("tblplan_log", $insp);

        //INSERT member features
        $fres = $dclass->select("intid", "tblfeatures", " AND status='active'");
        for ($i = 0; $i < count($fres); $i++) {
            unset($insf);
            $insf['member_id'] = $id;
            $insf['feature_id'] = $fres[$i]['intid'];
            $insf['feature_status'] = 'running';
            $dclass->insert("tblmember_features", $insf);
        }
        $redirect_to = 'apps?list=apps';
        $_SESSION['custid'] = $id;
        $_SESSION['agents_cust_id'] = $id;
        $_SESSION['role'] = $role;
        $_SESSION['custuser'] = $username;
        $_SESSION['custname'] = ucfirst($fname) . " " . ucfirst($lname);
        $_SESSION['type'] = "succ";
        $_SESSION['msg'] = $gnrl->getMessage('REG_SUC', $lang_id);
        $gnrl->redirectTo($redirect_to);
    }
    else {
        $_SESSION['type'] = "err";
        $_SESSION['msg'] = $gnrl->getMessage('REG_FAIL', $lang_id);
        $gnrl->redirectTo("register");
    }
}

if ($_SESSION['type'] == 'err')
    $status_class = 'err-msg';
else
    $status_class = 'suc-msg';

//HEADER
include("inc/header-login.php");

?><body class="bg-black login-bg">
    <div class="login-page">
        <div class="col-md-12 padding0">
            <div class="col-md-12 padding0"><img src="img/login-logo.png" alt=""></div>
        </div>
        <div class="cl"></div>
        <div class="col-md-12 login_b_p register_page" id="login-box" style="padding:5% 5% 1% 5%;">
            <form id="regfrm" action="register?<?php echo $encrypt_id; ?>" method="post">
                <input type="hidden" name="package_id" id="package_id" value="<?php echo $package_id; ?>" >

                <div class="body bg-gray">
                    <div class="<?php echo $status_class; ?>"><?php echo $_SESSION['msg']; ?></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-envelope fl"></i>
                        <input type="text" id="email" name="email" class="required email form-control fl" placeholder="Email" value="<?php echo $email; ?>" autocomplete="off"/>
                    </div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-user fl"></i>
                        <input type="text" name="username" value="" class="required form-control fl" placeholder="User ID" autocomplete="off"/>
                    </div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-hand-o-right fl"></i>
                        <input type="text" name="fname" value=""  class="required form-control fl" placeholder="First name" autocomplete="off"/>
                    </div>
                    <div class="cl height"></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-hand-o-right fl"></i>
                        <input type="text" name="lname" value=""  class="required form-control fl" placeholder="Last name" autocomplete="off"/>
                    </div>
                    <div class="cl height"></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-unlock-alt fl"></i>
                        <input type="password" id="password" name="password" value="" class="required form-control fl" placeholder="Password" autocomplete="off"/>
                    </div>
                    <div class="cl height"></div>
                    <div class="form-group border"> 
                        <i class="fa fa-fw fa-unlock-alt fl"></i>
                        <input type="password" id="password_confirm" name="password_confirm" value="" class="required form-control fl" placeholder="Retype password" autocomplete="off"/>
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
    
    <!-- Jquery bxslider for custom responsive sliders -->
    <link rel="stylesheet" media="all" type="text/css" href="css/bxslider/jquery.bxslider.css" />
    <script type="text/javascript" src="js/plugins/bxslider/jquery.bxslider.min.js"></script>
    <script type="text/javascript">

        //function to change plan
        select_plan(1);
        function select_plan(id) {
            $('#select_plan_slider').children('div').removeClass('active');
            $('#plan_' + id).addClass('active');
            $('#package_id').val(id);
            $(".smallfont").hide();
            $("#splan_" + id).show();
            $.ajax({
                type: "POST",
                url: "getpackagedetails.php",
                async: false,
                data: {package_id: id},
                success: function (data) {
                    var obj = JSON.parse(data);
                    $('.price_box1').empty();

                    if (id == 2) {
                        var text = 'Pay monthly, get more';
                    } else if (id == 3) {
                        var text = 'Pay yearly, get more';
                    } else if (id == 4) {
                        var text = 'Pay monthly, get lot more';
                    } else if (id == 5) {
                        var text = 'Pay yearly, get lot more';
                    } else {
                        var text = 'Pay as you grow';
                    }

                    $('.price_box1').append("<h4>" + obj.pname + "</h4><p>" + text + "</p><h3>$" + obj.pcost + "</h3><p><span>" + numberWithCommas(obj.plimit) + "</span> actions</p><p><span>$" + obj.padditional_limit_cost + "</span> for each <span>" + numberWithCommas(obj.padditional_limit) + " </span>actions thereafter</p>");
                }
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(document).ready(function () {
            $('#regfrm').validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 5
                    },
                    password_confirm: {
                        minlength: 5,
                        equalTo: "#password"
                    }
                }
            });
            $('.success-message').delay(5000).fadeOut('slow');
            $('.error-message').delay(5000).fadeOut('slow');
            $('.warning-message').delay(5000).fadeOut('slow');
        });

        $(window).load(function () {
            var fslider = $('#select_plan_slider').bxSlider({
                slideWidth: 135,
                minSlides: 5,
                maxSlides: 9,
                moveSlides: 1,
                startSlide: parseInt(0),
                slideMargin: 10,
                infiniteLoop: false,
                hideControlOnEnd: true,
                pager: false
            });

            $(".sliderWrapperFont").show(0, "swing", function () {
                fslider.reloadSlider();
            });
        });
    </script>
</body>
</html>