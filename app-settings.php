<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
/*
 * page for setting your app and assign Respond Responsible
 */
require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$feature_id = 6; //Analytics
$member_id = $_SESSION['custid'];

$wha = '';
if ($_SESSION['app_type'] && !isset($_REQUEST['sel_app_id'])) {
    $wha = " AND a.app_type='" . $_SESSION['app_type'] . "' ";
}

if (isset($_SESSION['sel_app_id']) && !isset($_REQUEST['sel_app_id'])) {
    $_REQUEST['sel_app_id'] = $_SESSION['sel_app_id'];
} else
    unset($_SESSION['sel_app_id']);

if (isset($_REQUEST['sel_app_id']) && $_REQUEST['sel_app_id'] != '') {
    $sel_app_id = $_REQUEST['sel_app_id'];
    $res = $dclass->select("f.feature_status,f.app_id, app_add_date, f.intid as id, a.app_type, a.review_message,a.app_name,m.company_logo", "tblmember_apps a INNER JOIN tblmember_app_features f ON a.intid=f.app_id INNER JOIN tblmember m ON a.member_id=m.intid", " AND a.intid='" . $sel_app_id . "'  AND f.feature_id='" . $feature_id . "' $wha ");
} else {
    $sel_app_id = '';
}

if (count($res) > 0 && $sel_app_id != '') {

    $feature_status_id = $res[0]['id'];
    $feature_status = $res[0]['feature_status'];
    $intid = $res[0]['ind'];
    $app_type = $res[0]['app_type'];
    $_SESSION['app_type'] = $app_type;
    $app_name = $res[0]['app_name'];
    if ($app_type == 'ios')
        $ios_class = 'active';
    else if ($app_type == 'android')
        $android_class = 'active';
    else if ($app_type == 'windows')
        $windows_class = 'active';
}
extract($_POST);

$promo_disable_flag = 0;
$analyzr_disable_flag = 0;
$upgradr_disable_flag = 0;
$communicatr_disable_flag = 0;

if (!empty($sel_app_id)) {
    $res = $dclass->select("intid, feature_status, feature_id", "tblmember_app_features", " AND member_id='" . $member_id . "' AND app_id = '" . $sel_app_id . "'");

    $resc = $dclass->select("intid, feature_status, feature_id", "tblmember_features", " AND member_id='" . $member_id . "' ");
    if (count($resc) > 0) {
        foreach ($resc as $key => $value):
            if ($value['feature_status'] == 'pause') {
                switch ($value['feature_id']):

                    case 3:
                        $communicatr_disable_flag = 1;
                        break;
                    case 4:
                        $helpr_disable_flag = 1;
                        break;

                    default:
                        break;
                endswitch;
            }

        endforeach;
        $sec_error_msg = $gnrl->getMessage('SEC_DISABLE_GEN_SETTINGS', $lang_id);
    }
}else {
    $res = $dclass->select("intid, feature_status, feature_id", "tblmember_features", " AND member_id='" . $member_id . "'");
}


$get_support_selected_app = $dclass->select("*", "tbl_support_agent_allocate", " AND app_id='" . $_REQUEST['sel_app_id'] . "' ");

$res_feature = $dclass->select("intid, feature_status, feature_id", "tblmember_features", " AND member_id='" . $member_id . "'");

$res_feature_individual = $dclass->select("intid, feature_status, feature_id", "tblmember_app_features", " AND member_id='" . $member_id . "' AND app_id = '" . $_REQUEST['sel_app_id'] . "' ");
foreach ($res_feature as $val_feature) {
    if ($val_feature['feature_id'] == 3 && $val_feature['feature_status'] == 'pause') {
        $pause = 'pause';
    }
}
foreach ($res_feature_individual as $val_ind_feature) {
    if ($val_ind_feature['feature_id'] == 3 && $val_ind_feature['feature_status'] == 'pause') {
        $pause_ind = 'pause';
    }
}
include INC . "header.php";

include INC . "left_sidebar.php";
?>
<!-- ============================================================================== -->
<!-- Right Side Bar -->
<!-- ============================================================================== -->
<aside class="right-side">
    <form action="app-settings?sel_app_id=<?php echo $sel_app_id ?>" method="post" enctype="multipart/form-data" id="notification_form">
        <div class="right_sidebar">
            <div class="add-apps features_setting">
                <div class="col-xs-12 col-md-12">
                    <h1 class="fl">Features </h1>
                    <div class="cl height10"></div>
                    <p class="app-satting">Customize individual app features. By default all the features are enabled. To reduce number of actions, you can disable desired feature within particular app. If a feature is disabled in system settings, it will disable in system settings, it will disable that feature for all the apps. In condition to control that feature on app level, you must enable that feature within system settings.</p>
                </div>
                <div class="cl"></div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="apps-satting">
                            <div class="col-xs-12 col-md-12">
                                <div class="apps_sattings">
                                    <div class="sidebar-menu"> 

                                    <?php
                                    foreach ($res as $key => $value):
                                        if ($value['feature_id'] == 3 || $value['feature_id'] == 4) {
                                            if ($value['feature_id'] == 3) {
                                                $class = "menu_i communicatr_icon";
                                                $name = "Respond";
                                                $id = "feature_status_communicatr";
                                            } else if ($value['feature_id'] == 4) {
                                                $class = "menu_i helpr_icon";
                                                $name = "Help";
                                                $id = "feature_status_helpr";
                                            }
                                            if (!empty($sel_app_id)) {
                                                $intid = $value['intid'];
                                                $feature_id = '';
                                            } else {
                                                $intid = '';
                                                $feature_id = $value['feature_id'];
                                            }
                                            ?>
                                            <?php
                                            if (($value['feature_status'] == 'pause' && $promo_disable_flag == 1) || ($value['feature_status'] == 'pause' && $analyzr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $helpr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $upgradr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $communicatr_disable_flag == 1)) {
                                                continue;
                                            } else {
                                                ?>
                                                <div class="col-xs-6 col-md-3">
                                                    <div class="fl"> <i class="<?php echo $class ?>">&nbsp;</i> <span class="menu_t"><?php echo $name ?></span> </div>

                                                    <div class="col-xs-6 col-md-12">
                                                    <?php if (($value['feature_status'] == 'pause' && $promo_disable_flag == 1) || ($value['feature_status'] == 'pause' && $analyzr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $helpr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $upgradr_disable_flag == 1) || ($value['feature_status'] == 'pause' && $communicatr_disable_flag == 1)) { ?>
                                                            <br/>
                                                            <p><?php echo $sec_error_msg; ?></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php
                                            }
                                        }
                                    endforeach;
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="apps-satting">
                            <div class="col-xs-6 col-md-12" id="agents_list" <?php if ($pause != 'pause' && $pause_ind != 'pause') { ?>style="display:block" <?php } else { ?>style="display:none" <?php } ?>>
                                <div class="apps_sattings">
                                    <div class="sidebar-menu">
                                        <?php
                                        if (isset($_REQUEST['sel_app_id'])):
                                            ?>
                                            <div class="cl"></div>
                                            <div class="form-group"> 


                                                <?php
                                                $agent_list = $dclass->select("*", "tblmember", " AND parent_id = '" . $_SESSION['custid'] . "' AND role = 'support' AND status = 'active' ");
                                                if (count($agent_list) != 0):
                                                    ?>
                                                    <div class="input-group">
                                                        <h3 class="infometion">Respond Responsible</h3>
                                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                                                        <div class="responsible_user">
                                                            <?php
                                                            for ($i = 0; $i < count($agent_list); $i++):

                                                                if ($agent_list[$i]['logo'] != '' && is_file(USER_LOGO . "/" . $agent_list[$i]['logo'])) {
                                                                    $logo_path = "memimages.php?max_width=80&max_width=80&imgfile=" . USER_LOGO . "/" . $agent_list[$i]['logo'];
                                                                } else {
                                                                    $logo_path = "img/responsible_user_uncheck.png";
                                                                }
                                                                ?>
                                                                <div class="responsible_user_box <?php if ($i == 0) { ?>first <?php } ?> <?php if ($get_support_selected_app[0]['member_id'] == $agent_list[$i]['intid']) { ?> checkd <?php } ?>" onclick="select_font('<?php echo $agent_list[$i]["intid"] ?>')" id="<?php echo $agent_list[$i]['intid'] ?>">
                                                                    <div class="user__img1"> <span class="user_check">&nbsp;</span> <span class="user__img"><img src="<?php echo $logo_path ?>" alt="" /></span> </div>
                                                                    <div class="cl"></div>
                                                                    <span class="user__name" style="margin:0 auto;overflow: hidden !important;text-overflow: ellipsis !important;white-space: nowrap !important;width:120px !important;"><?php echo $agent_list[$i]['fname'] . ' ' . $agent_list[$i]['lname'] ?></span> </div>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="cl height30"></div>


                                                <!-- /.input group --> 

                                            </div>
                                    <?php endif;
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</aside>

<!-- ============================================================================== --> 
<!-- Footer --> 
<!-- ============================================================================== -->

<?php
include 'inc/footer.php';
?>
<script type="text/javascript">
    $(function (e) {
        $("#pem_file").change(function () {
            var fileName = $(this).val();
            var ext = fileName.split('.').pop();
            if (ext != 'pem' && ext != '') {

                alert('Please upload proper pem file');
            }
        });

        $("#pem_prod_file").change(function () {
            var fileName = $(this).val();
            var ext = fileName.split('.').pop();
            if (ext != 'pem' && ext != '') {
                alert('Please upload proper pem file');
            }
        });
    });

    $("#save_notification").click(function (event) {
        var fileName = $("input:file").val();
        var ext = fileName.split('.').pop();
        if (ext != 'pem' && ext != '') {
            alert('Please upload proper pem file');
            event.preventDefault();
            return false;
        }
    });
</script> 
<script>
    function select_font(id) {
        $("#overlays").show();
        $.ajax({
            type: "POST",
            url: "process-add.php",
            async: false,
            data: {intid: id, table_name: 'tbl_support_agent_allocate', app_id: '<?php echo $_REQUEST['sel_app_id'] ?>'},
            success: function (data) {
                $("#overlays").hide();
                $('.responsible_user_box').removeClass('checkd');
                $('#' + id).toggleClass('checkd');
            }
        });
    }

    var slide_fnum = '<?php echo $slide_fnum; ?>';

    $(window).load(function () {
        var fslider = $('#select_font_slider').bxSlider({
            slideWidth: 135,
            minSlides: 5,
            maxSlides: 9,
            moveSlides: 1,
            startSlide: parseInt(slide_fnum),
            slideMargin: 10,
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false,
            onSlideBefore: function () {
                var current = fslider.getCurrentSlide();
                $('#slide_fnum').val(current);
            },
            onSliderLoad: function () {
            }
        });
        $(".sliderWrapperFont").show(0, "swing", function () {
            fslider.reloadSlider();
        });
    });
    var sec_error_msg = '<?php echo $sec_error_msg; ?>';
    function change_status(id, table_name, feature_id, divid) {
        var promo_flag = '<?php echo $promo_disable_flag; ?>';
        var analyzr_flag = '<?php echo $analyzr_disable_flag; ?>';

        var status;
        $('input').on('ifChecked', function (e) {
            if (divid == 'click_feature_status_communicatr') {
                $('#agents_list').show();
            }
            status = 'running';
            $("#noti_show_hide").show();
            change_status_prompt(status, id, table_name, feature_id, divid, 'enable');

        });

        $('input').on('ifUnchecked', function (e) {
            status = 'pause';
            if (divid == 'click_feature_status_communicatr') {
                $('#agents_list').hide();
            } else {
                $('#agents_list').show();
            }
            $("#noti_show_hide").hide();
            change_status_prompt(status, id, table_name, feature_id, divid, 'disable');
        });
    }
    function change_status_prompt(status, id, table_name, feature_id, divid, stat) {
        $(".jqibox").remove();
        $.prompt("", {
            title: "Are you sure you want to " + stat + " this feature?",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function (e, v, m, f) {
                if (v != false) {
                    $("#overlays").show();
                    $.ajax({
                        type: "POST",
                        url: "process-update.php",
                        dataType: "json",
                        async: false,
                        data: {"id": id, "table_name": table_name, "status": status, type: 'change_status', feature_id: feature_id},
                        success: function (data) {

                            if (data['output'] == 'S') {
                                message(data['msg'], 'success');

                                if (feature_id == 3) {
                                    $("#communicatr").remove();

                                    if (status == 'running') {
                                        $(".treeview-menu").append('<li id="communicatr"> <a href="respond" style="margin-left: 10px;"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span></a> </li>');
                                    }
                                }
                                if (feature_id == 4) {
                                    $("#helpr").remove();

                                    if (status == 'running') {
                                        $(".treeview-menu").append('<li id="helpr"> <a href="apps?list=help" style="margin-left: 10px;"><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help</span></a> </li>');
                                    }
                                }
                            } else if (data['output'] == 'F') {
                                message(data['msg'], 'error');
                            }
                            $("#overlays").hide();
                            $(".jqibox").remove();
                        }
                    });

                } else {
                    $.prompt.close();
                }
            },
            close: function (e, v, m, f) {
                var splitdata = divid.split('_');
                var finalstring = splitdata[1] + '_' + splitdata[2] + '_' + splitdata[3];
                if (status == 'pause') {
                    $('#' + finalstring).iCheck('check');

                } else {
                    $('#' + finalstring).iCheck('uncheck');

                }
            }
        });
    }
    $(document).ready(function (e) {
<?php if ($promo_disable_flag == 1) { ?>
            $("#feature_status_promotr").iCheck('disable');
<?php } ?>
<?php if ($analyzr_disable_flag == 1) { ?>
            $("#feature_status_analyzr").iCheck('disable');
<?php } ?>
    });
</script> 
