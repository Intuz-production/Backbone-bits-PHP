<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
if (basename($_SERVER['REQUEST_URI']) != "respond") {
    unset($_SESSION['unread_msg']);
}

$url = $gnrl->getURL();
if ($url != 'add-apps') {
    unset($_SESSION['add_auto_app']);
    unset($_SESSION['add_manual_app']);
}

$get = http_build_query($_GET);

$qu_flag = 0;
$get = "";
$get_cnt = 0;
foreach ($_GET as $k => $value) {
    if ($get_cnt == 0)
        $op = '?';
    else
        $op = '';
    if (!$value) {
        $get .= $op . $k;
        $ad_key = $k;
    } else
        $get .= $op . "&" . $k . "=" . $value;

    $get_cnt++;
}

//check more apps access
if (!$gnrl->check_section_access($_SESSION['custid'], 2)) {
    $more_apps_deny_flag = 1;
} else {
    $more_apps_deny_flag = 0;
}

//Role Management
$access_list = array();
if (isset($_SESSION['only_payment_access']) == 1) {
    $access_list = array('profile');
} else {
    if ($_SESSION['parent_id'] != 0) {
        switch ($_SESSION['role']) {
            case "technical":
                $access_list = array('profile', 'dashboard', 'apps', 'app-details', 'add-app', 'help', 'help-faq-list', 'help-img-video', 'help-img-video-archive', 'documentation', 'app-settings', 'notification', 'unauthorize');
                break;
            case 'finance':
                $access_list = array('profile', 'dashboard', 'finance-report', 'documentation', 'unauthorize');
                break;
            case 'support':
                $access_list = array('profile', 'dashboard', 'documentation', 'unauthorize', 'respond', 'respond-detail');
                break;
            case 'marketing':
                $access_list = array('profile', 'dashboard', 'documentation', 'unauthorize');
                break;
            case 'admin':
                $access_list = array('profile', 'dashboard', 'agents', 'apps', 'app-settings', 'notification', 'add-apps', 'help', 'finance-report', 'documentation', 'rights-management', 'app-details', 'unauthorize', 'respond', 'respond-detail', 'help-faq-list', 'help-img-video', 'help-img-video-archive', 'access-log');
                break;
        }
    } else {
        $access_list = array('profile', 'dashboard', 'agents', 'apps', 'app-settings', 'notification', 'add-apps', 'help', 'finance-report', 'documentation', 'rights-management', 'app-details', 'unauthorize', 'respond', 'respond-detail', 'help-faq-list', 'help-img-video', 'help-img-video-archive', 'access-log');
    }
}

$mdata = $dclass->select("*", 'tblmember', " AND intid='" . $_SESSION['agents_cust_id'] . "' ");

if ($mdata[0]['logo'] != '' && is_file(USER_LOGO . "/" . $mdata[0]['logo'])) {
    $mpath = USER_LOGO . "/" . $mdata[0]['logo'];
} else {
    $mpath = "img/s_user_img.png";
}

$cc_status = $mdata[0]['cc_status'];
$intro_flag = $mdata[0]['intro'];

if (isset($_REQUEST['sel_app_id'])) {
    $checkaccess = $gnrl->checkuseraccess($_SESSION['custid'], $_REQUEST['sel_app_id']);
    if ($checkaccess <= 0) {
        $gnrl->redirectTo('unauthorize');
        die();
    }
}
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo '' . BRAND; ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="shortcut icon" href='img/favicon.png' type="image/x-con" />
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900,500italic,400italic,300italic,700italic' rel='stylesheet' type='text/css'>
        <link href="css/admin.css" rel="stylesheet" type="text/css" />
        <link href="css/custom-responsive.css" rel="stylesheet" type="text/css" />

        <!-- Add Apps popup -->
        <link rel="stylesheet" type="text/css" href="css/add-app-popap.css" />

        <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css" />
        <!-- Optional theme -->

        <!-- font Awesome -->
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <!--<link href="css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />-->
        <!-- Date Picker -->
        <link href="css/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="skin-blue <?php if ($url == 'dashboard') { ?>dashboardbody<?php } ?>">
        <!-- header logo: style can be found in header.less -->
        <header class="header"> <a href="dashboard" class="logo"> 
                <!-- Add the class icon to your logo image or logo icon to add the margining --> 
                <img alt="<?php echo BRAND; ?>" title="<?php echo BRAND; ?>" src="img/h_logo.png"></a> 
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation"> 
                <!-- Sidebar toggle button-->
                <div class="col-xs-12 col-md-9"><?php
//get credit card update prompt
if ($_SESSION['parent_id'] == 0 && $cc_status == 0 && $url != 'payment-method') {
    
}
?></div><?php
if (isset($_SESSION['only_payment_access']) && $url == 'payment-method') {
    $cost_disp = CUR . '' . $gnrl->get_number_format($_SESSION['cost'], 0, '.', ',');
    if ($_SESSION['package_interval'] == 'monthly')
        $header_plan_intval = 'Month';
    else if ($_SESSION['package_interval'] == 'yearly')
        $header_plan_intval = 'Year';
    ?><div class="col-xs-12 col-md-9">
                        <div class="ticker_message">
                            <dd><?php
                    if ($_SESSION['plan'] == 'Buzz') {
                        $pay_txt = "Pay " . $cost_disp . " For Next " . $gnrl->get_number_format($_SESSION['actions']) . ' Actions';
                    } else {
                        if ($_SESSION['plan_type'] == 'top-up') {
                            $pay_txt = "Pay " . $cost_disp . " For Next " . $gnrl->get_number_format($_SESSION['actions']) . ' Actions';
                        } else {
                            if ($_SESSION['payment_type'] == 'trial') {
                                $pay_txt = "Pay Due " . $cost_disp . " for " . $_SESSION['plan'] . " Plan To Proceed with " . $gnrl->get_number_format($_SESSION['actions']) . " Actions till end of the current month. The regular billing cycle shall continue from first day of next month";
                            } else {
                                $pay_txt = "Pay Due " . $cost_disp . "/" . $header_plan_intval . " for " . $_SESSION['plan'] . " Plan To Proceed";
                            }
                        }
                    }
                    ?></dd>
                        </div>
                    </div><?php
                            }
                            ?><a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav"><?php
                            if ($member_id == $_SESSION['agents_cust_id']) {
                                $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
                            } else {
                                if ($_SESSION['role'] == 'admin') {
                                    $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
                                } else {
                                    $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
                                }
                            }

                            $app_name_distinct = $dclass->select('*, tblapp_support.intid as support_id, tblapp_support.request_id as request_id ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id  ' . $append_data . ' AND is_read = "N"  order by tblapp_support.dtadd desc');

                            if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'support'):
                                ?><!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu messages_header" <?php if (count($app_name_distinct) == 0): ?> style="display:none" <?php endif; ?>>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-envelope"></i>
                                    <span class="label label-success"><?php echo count($app_name_distinct) ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu"><?php
                            foreach ($app_name_distinct as $list_value) {
                                if ($list_value['request_id'] == 0) {
                                    $support_id = $list_value['support_id'];
                                } else {
                                    $support_id = $list_value['request_id'];
                                }

                                if ($list_value['request_type'] == 'bug') {
                                    $class_image = 'request_i1';
                                } else if ($list_value['request_type'] == 'feedback') {
                                    $class_image = 'request_i2';
                                } else if ($list_value['request_type'] == 'query') {
                                    $class_image = 'request_i3';
                                }
                                ?><li><!-- start message -->
                                                    <a onclick="unread_message('<?php echo $support_id ?>')" href='respond-detail?support_id=<?php echo $support_id ?>&sel_app_id=<?php echo $list_value['app_id'] ?>'>
                                                        <div class="pull-left">
                                                            <i class='apps_i <?php echo $class_image ?>'></i>
                                                        </div>
                                                        <h4><?php echo $list_value['app_name'] ?></h4>
                                                        <p><?php echo $list_value['message'] ?></p>
                                                    </a>
                                                </li><!-- end message --><?php
                                            }
                                            ?></ul>
                                    </li>
                                    <li class="footer">
                                        <form action="dashboard" method="post" id="submitunreadmsg" name="submitunreadmsg">
                                            <input type="hidden" value="1" name="unread_msg" id="unread_msg">
                                            <input type="submit" value="See All Messages" class="btn btn-warning">
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                function unread_message(intid) {
                                    $.ajax({
                                        type: "POST",
                                        url: "process-update.php",
                                        async: false,
                                        data: {table_name: "tblapp_support", intid: intid},
                                        success: function (data) {

                                        }
                                    });
                                }
                            </script>
<?php endif; ?>

                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu"> 
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span><?php echo $_SESSION['custname']; ?> <i class="fa fa-fw fa-angle-down"></i></span> </a><?php
if ($mdata['company'] != '') {
    ?><div class="c_name"><?php echo $mdata['company']; ?></div><?php
}
?><ul class="dropdown-menu popover bottom">
                                <div class="arrow"></div>
                                <!-- User image -->
                                <li class="user-header bg-light-blue"> <img src="<?php echo $mpath; ?>" class="img-circle" alt="User Image" />
                                    <p> <small>Member since </small> </p>
                                    <p><?php echo date("j M Y", strtotime($mdata[0]['dtadd'])); ?></p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left"> <a  <?php if (in_array('profile', $access_list)) { ?> href="profile" <?php } else { ?> href="#" <?php } ?> class="btn btn-default btn-flat">Profile</a> </div>
                                    <div class="pull-right"> <a href="login?action=logout" class="btn btn-default btn-flat">Sign out</a> </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <input type="hidden" id="intro_flag" value="<?php echo $intro_flag; ?>" />   
