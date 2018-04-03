<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

if ($_POST['unread_msg']) {
    $_SESSION['unread_msg'] = 1;
    $gnrl->redirectTo("respond");
    exit;
}

$member_id = $_SESSION['custid'];
include(INC . "header.php");

if (!$app_type) {
    if (isset($_SESSION['app_type'])) {
        $app_type = $_SESSION['app_type'];
        if ($app_type == 'ios') {
            $ios_class = 'active';
            $android_class = '';
            $all_class = '';
        } else if ($app_type == 'android') {
            $android_class = 'active';
            $ios_class = '';
            $all_class = '';
        } else {
            $all_class = "active";
        }
    } else {
        if ($url == 'apps') {
            $all_class = "active";
            $ios_class = '';
            $android_class = '';
        } else {
            $all_class = '';
            $android_class = '';
            $app_type = 'ios';
            $ios_class = 'active';
        }
    }
}

if ($sel_app_id) {
    $append = "?sel_app_id=" . $sel_app_id;
} else {
    $append = "";
}

$who = '';
if (isset($app_type))
    $who = "AND tblmember_apps.app_type='" . $app_type . "' AND tblmember_apps.intid != '" . $sel_app_id . "' ";

if ($url == 'help' || $url == 'help-faq-list' || $url == 'help-img-video-archive' || $url == 'help-img-video') {
    $res1 = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='4' AND tblmember_apps.member_id='" . $member_id . "' $who  ORDER BY tblmember_apps.intid DESC");
} else if ($url == 'respond' || $url == 'respond-detail') {
    if ($url == 'respond') {
        foreach ($_SESSION['app_id'] as $val_app_id) {
            $append_app_id .= " AND tblmember_apps.intid!='" . $val_app_id . "'";
        }
        $support_agents = $dclass->select("*", "tblmember", " AND parent_id = '" . $_SESSION['custid'] . "' AND role = 'support' AND status = 'active' ");
    }

    if ($member_id == $_SESSION['agents_cust_id']) {
        $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
    } else {
        $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
    }
    if (isset($_SESSION['app_type'])) {
        $who = "AND tblmember_apps.app_type='" . $_SESSION['app_type'] . "' AND tblmember_apps.intid != '" . $sel_app_id . "' ";
    } else {
        $who = "AND tblmember_apps.intid != '" . $sel_app_id . "' ";
    }

    $res1 = $dclass->select("tblmember_apps.*, tblapp_support.intid as support_id", "tblmember_apps, tblapp_support, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='3' AND tblmember_apps.intid = tblapp_support.app_id " . $append_app_id . " " . $append_data . " and request_id = 0 $who group by intid ORDER BY tblmember_apps.intid  DESC ");
} else {

    $res1 = $dclass->select("*", "tblmember_apps", " AND tblmember_apps.member_id='" . $member_id . "' $who  ORDER BY tblmember_apps.intid DESC");
}
$res_popup = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "'  ORDER BY intid DESC");

$res_seft_sidebar = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' AND intid = '" . $sel_app_id . "'");

$res_feature = $dclass->select("intid, feature_status, feature_id", "tblmember_features", " AND member_id='" . $member_id . "' ORDER BY feature_id ASC ");

for ($i = 0; $i < count($res_feature); $i++) {
    $feature[$res_feature[$i]['feature_id']] = $res_feature[$i]['feature_status'];
}

if ($res_seft_sidebar[0]['app_logo'] != '')
    $app_icon_path = "memimages.php?max_width=125&max_width=125&imgfile=" . APP_LOGO . "/" . $res_seft_sidebar[0]['app_logo'];
else
    $app_icon_path = "img/no_image_available.jpg";

$from_date = date("Y-m-01");
$to_date = date("Y-m-t", strtotime(date("Y-m-d")));

$resc = $dclass->select("count(intid) as total", "tblapp_support ", " AND request_id='0' AND is_archive = 0 AND is_live = 1 AND member_id in(select intid from tblmember where parent_id='" . $member_id . "' OR intid = '" . $member_id . "') ");

$rescc = $dclass->select("count(intid) as total", "tblapp_support ", " AND request_id='0' AND is_archive = 0 AND is_live = 1 AND member_id in(select intid from tblmember where parent_id='" . $member_id . "' OR intid = '" . $member_id . "') AND status='close' ");

$total_prv_visit = $dclass->select("a.app_name, s.dtadd as dt, SUM(s.promote_prompt_count) as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", "   AND (s.dtadd BETWEEN '" . $from_date . "'  AND '" . $to_date . "') AND s.member_id='" . $_SESSION['custid'] . "' ");

$total_prv_vi = 0;
for ($i = 0; $i < count($total_prv_visit); $i++) {
    $total_prv_vi = $total_prv_vi + $total_prv_visit[$i]['download_count'];
}
?><!-- Right side column. Contains the navbar and content of the page -->

<div class="dashboardpage">
    <div class="row">
        <div class="boardtop">
            <div class="col-md-12">
                <h3 class="fl">Respond</h3>
                <div class="cl"></div>
                <ul class="respond_board">
                    <li><div class="activibox"><?php echo $gnrl->get_number_format($resc[0]['total']) ?></div><span>Open</span></li>
                    <li><div class="activibox"><?php echo $gnrl->get_number_format($rescc[0]['total']) ?></div><span>Closed</span></li>
                    <li><div class="activibox"><?php echo count($app_name_distinct) ?></div><span>New</span></li>
                </ul>
                <div class="cl"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12">
        <div class="row">
            <div class="boardmenu">
                <ul>
<?php if (in_array('apps', $access_list)) { ?>
                        <li class="board_app"  data-step="1" data-intro="Here you can manage apps for all features!" ><a href="apps?list=apps"><i class="boardmenuicon">&nbsp;</i> <span>Apps</span> </a> </li>
                    <?php } ?>
                    <?php if ($feature[3] == 'running') { ?>
                        <?php if (in_array('respond', $access_list)) { ?>
                            <li class="board_respond"  data-position='right'><a href="respond"> <i class="boardmenuicon">&nbsp;</i> <span>Respond</span></a> </li>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($feature[4] == 'running') { ?>
                        <?php if (in_array('help', $access_list)) { ?>
                            <li class="board_help"  data-position='right'><a href="apps?list=help"> <i class="boardmenuicon">&nbsp;</i> <span>Help</span></a> </li>
                        <?php } ?>
                    <?php } ?>  
                    <li class="board_setting"><a href="profile"> <i class="boardmenuicon">&nbsp;</i> <span>Settings</span></a> </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12">
        <div class="row">
            <div class="board_dwonload_btn">
                <a class="download_" href="javascript:void(0);" onclick="$('#nav-overlays').fadeIn();
                        closebottom();">Download SDK
                    <div class="popover top"><img src="img/download_sdk_text.png" alt=""></div>
                </a>
                <a href="documentation/index.html" target="_blank">Documentation
                    <div class="popover top"><img src="img/document_text.png" alt=""></div>
                </a>                
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12">
        <div class="row"><div class="boardcopyright">&copy; 2018  Intuz. | Sunnyvale, California (USA)</div></div>
    </div>
</div>
<!-- /.right-side -->
</div>
<!-- ./wrapper -->

<?php include(INC . "footer.php"); ?>
</script>
</body></html>
<style>
    body.dashboardbody .ferromenu-controller.desktop, body.dashboardbody .ferro_menu_bottom{
        display: none !important;
    }
</style>
<script>
    $("#nav-overlays").on('click', function () {
        $('#DownloadSDK').hide();
        $('#nav-overlays').fadeOut();
    });
    $(".close").on('click', function () {
        $('#DownloadSDK').hide();
        $('#nav-overlays').fadeOut();
    });
</script>