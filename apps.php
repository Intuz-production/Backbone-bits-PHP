<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

/*
 * page for list all apps
 */

require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

$member_id = $_SESSION['custid'];
if (isset($_SESSION['sel_app_id'])) {
    $_REQUEST['sel_app_id'] = $_SESSION['sel_app_id'];
}

$who = '';
if (isset($_SESSION['app_type'])) {
    $who = "AND app_type='" . $_SESSION['app_type'] . "' ";
    $order_by = "tblmember_apps.intid DESC ";
} else {
    $order_by = "app_type,tblmember_apps.intid DESC";
}

if ($_REQUEST['list'] == 'help') {
    $res = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND tblmember_app_features.feature_id='4' AND tblmember_apps.member_id='" . $member_id . "' $who  ORDER BY $order_by");
} else {
    $res = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' $who  ORDER BY $order_by");
}

$resios = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='6' AND tblmember_apps.member_id='" . $member_id . "' AND tblmember_apps.app_type='ios'  ORDER BY $order_by");
$resad = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='6' AND tblmember_apps.member_id='" . $member_id . "'  AND tblmember_apps.app_type='android'   ORDER BY $order_by");

if (count($resios) <= 0) {
    $no_record = 1;
}

if (count($resad) <= 0) {
    $no_ad_record = 1;
}
include INC . "header.php";
include INC . "left_sidebar.php";
?><aside class="right-side">
    <div class="right_sidebar">
        <div class="apps_list">
            <div class="col-md-12"><?php
if (count($res) > 0) {
    ?><div class="row"><?php
    for ($i = 0; $i < count($res); $i++) {
        if ($res[$i]['app_logo'] != '')
            $app_img_path = "memimages.php?max_width=118&max_width=118&imgfile=" . APP_LOGO . "/" . $res[$i]['app_logo'];
        else
            $app_img_path = "img/no_image_available.jpg";

        if ($res[$i]['app_status'] == 'active')
            $active_class = 'active_icon';
        else
            $active_class = 'inactive_icon';

        if ($res[$i]['app_type'] == 'ios') {
            $app_type_img_path = "apps_i iphone_i";
        } else {
            $app_type_img_path = "apps_i android_i";
        }

        if ($_REQUEST['list'] == 'respond') {
            $list_url = 'respond';
        } else if ($_REQUEST['list'] == 'help') {

            $resupdd = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $res[$i]['intid'] . "' AND (record_status='running' OR record_status='prev') ");

            if (count($resupdd) > 0) {
                $list_url = 'help-img-video';
                $append_live = '&sel=faq&live';
            } else {
                $list_url = 'help-img-video';
                $append_live = '&sel=faq&live';
            }
        } else {
            $list_url = 'app-details';
        }
        ?><div class="col-xs-6 col-md-2">
            <a href="<?php echo $list_url ?>?sel_app_id=<?php echo $res[$i]['intid'] . $append_live; ?>" class="thumbnail">
                <div class="logo-icon">
                    <img src="<?php echo $app_img_path; ?> " width="110" height="110" alt="" title="" />
                </div>
                <div class="logo-title"> 
                    <i class="<?php echo $app_type_img_path; ?>" title="">&nbsp;</i> 
                    <span class="<?php echo $active_class; ?>"></span>
                    <div class="cl"></div>
        <?php echo $res[$i]['app_name']; ?>
                </div>
            </a>
        </div><?php
    }
    ?></div><?php
} else {
    if ($_REQUEST['list'] == 'apps') {
        ?><div class="bk row">
            <div class="col-lg-12">
                <div class="col-md-12 click-to-add-apps"> <img class="image_left" src="img/click-plus-add-new-apps.png" alt="" title="" /> </div>
            </div>
        </div><?php
    } else {
        ?><div class="bk row">
            <div class="col-lg-12">
                <div class="col-md-12 click-to-add-apps"> <img src="img/no_apps_found.png" alt="" title="" /> </div>
            </div>
        </div><?php
    }
    ?></div>
<!--  No Apps Found--><?php
}
?></div>
    </div>
</div>
</aside>


<!-- ================================================================================================================== --> 
<!-- Add Apps Popup --> 
<!-- ================================================================================================================== -->
<div id="somedialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
        <div class="popap-header">
            <h3 class="fl">Register App</h3>
            <button class="action fr" data-dialog-close>&nbsp;</button>
        </div>
        <div class="popap-content" id="add-app-page"></div>
    </div>
</div>
<!-- ============================================================================== --> 
<!-- Footer --> 
<!-- ============================================================================== -->

<?php include INC . '/footer.php'; ?>

<script type="text/javascript">
    $(document).ready(function () {
        $(".plus_icon a.trigger").on('click', function () {
            $.ajax({url: "add-app", success: function (result) {
                    $("#add-app-page").html(result);
                }});
        });

        $('#q').on('keyup', function () {
            var q = encodeURIComponent($(this).val());
            var app_type = '';
            if ($('.all-apps-icon >li.all_apps').hasClass('active')) {
                app_type = '';
            } else if ($('.all-apps-icon >li.ip').hasClass('active')) {
                app_type = 'ios';
            } else if ($('.all-apps-icon >li.ad').hasClass('active')) {
                app_type = 'android';
            }

            $("#overlays").show();
            $.ajax({
                dataType: "json",
                url: "ajax.php?ajax=1&action=search_app&q=" + q + "&member_id=<?php echo $member_id; ?>&site_url=<?php echo $url; ?>&list=<?php echo $_REQUEST['list']; ?>&app_type=" + app_type + "&no_record=" + $("#no_record").val() + "&no_ad_record=" + $("#no_ad_record").val(),
                success: function (data) {
                    $("#overlays").hide();
                    $('.row').empty().html(data['apps']);
                }
            });
        });

    });
</script>