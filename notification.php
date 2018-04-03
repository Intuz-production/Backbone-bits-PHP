<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php
/*
 * page for seting notification status and setting notification message
 * also used to upload Passphrase files
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

if (isset($_REQUEST['save_notification'])) {

    $chk_agent = $dclass->select("*", "tbl_support_agent_allocate", " AND app_id='" . $_POST['app_id'] . "' ");

    if (!empty($_REQUEST['review_message'])) {
        if (empty($_REQUEST['notification'])) {
            $notification = 'inactive';
        } else {
            $notification = 'active';
        }

        $dclass->update("tblmember_apps", array("review_message" => $_REQUEST['review_message'], "notification" => $notification), " intid='" . $sel_app_id . "' ");
    }

    if (empty($chk_agent)) {
        $up['os_type'] = $_POST['os_type'];
        if ($up['os_type'] == 'android') {
            $up['api_key'] = $_POST['api_key'];
            //$up['project_id'] = $_POST['project_id'];
        } else {

            if (!empty($_FILES['pem_file']['name'])) {
                $filename = time() . $gnrl->makefilename($_FILES['pem_file']['name']);
            }
            if (!empty($_FILES['pem_prod_file']['name'])) {
                $filename_prod = uniqid() . $gnrl->makefilename($_FILES['pem_prod_file']['name']);
            }
            $des = "files/pem/" . $filename;
            $des_prod = "files/pem/" . $filename_prod;

            if (move_uploaded_file($_FILES['pem_file']['tmp_name'], $des)) {

                chmod($des . $filename, 0777);
            }
            if (move_uploaded_file($_FILES['pem_prod_file']['tmp_name'], $des_prod)) {

                chmod($des_prod . $filename_prod, 0777);
            }

            if (!empty($_FILES['pem_file']['name'])) {
                $up['passphrase'] = $_POST['passphrase'];
                $up['pem_file'] = $filename;
            }
            if (!empty($_FILES['pem_prod_file']['name'])) {
                $up['passphrase_prod'] = $_POST['passphrase_prod'];
                $up['pem_prod_file'] = $filename_prod;
            }
        }
        $up['app_id'] = $_POST['app_id'];
        $id = $dclass->insert('tbl_support_agent_allocate', $up);
    } else {
        $up1['os_type'] = $_POST['os_type'];
        if ($up1['os_type'] == 'android') {
            $up1['api_key'] = $_POST['api_key'];
        } else {

            if (!empty($_FILES['pem_file']['name'])) {
                $filename = time() . $gnrl->makefilename($_FILES['pem_file']['name']);
            }
            if (!empty($_FILES['pem_prod_file']['name'])) {
                $filename_prod = uniqid() . $gnrl->makefilename($_FILES['pem_prod_file']['name']);
            }

            $des = "files/pem/" . $filename;
            $des_prod = "files/pem/" . $filename_prod;

            move_uploaded_file($_FILES['pem_file']['tmp_name'], $des);
            move_uploaded_file($_FILES['pem_prod_file']['tmp_name'], $des_prod);

            if (!empty($_POST['passphrase'])) {
                $up1['passphrase'] = $_POST['passphrase'];
            }
            if (!empty($_FILES['pem_file']['name'])) {
                $up1['pem_file'] = $filename;
            }
            if (!empty($_POST['passphrase_prod'])) {
                $up1['passphrase_prod'] = $_POST['passphrase_prod'];
            }
            if (!empty($_FILES['pem_prod_file']['name'])) {
                $up1['pem_prod_file'] = $filename_prod;
            }
        }
        $dclass->update('tbl_support_agent_allocate', $up1, " app_id = '" . $_POST['app_id'] . "'");
    }
    header('location:notification?sel_app_id=' . $sel_app_id);
}

include INC . "header.php";

include INC . "left_sidebar.php";
?>
<!-- ============================================================================== -->
<!-- Right Side Bar -->
<!-- ============================================================================== -->

<aside class="right-side">
    <form action="notification?sel_app_id=<?php echo $sel_app_id ?>" method="post" enctype="multipart/form-data" id="notification_form">
        <?php
        $resd = $dclass->select("*", "tblmember_apps", " AND intid='" . $sel_app_id . "' ");
        ?>
        <div class="right_sidebar">
            <div class="add-apps features_setting">
                <div class="col-xs-12 col-md-12">
                    <h1 class="fl">Push Notification</h1>
                    <div class="fl notification_active_in" style="padding:0px 0 0 15px;">
                        <label id="click_not" onclick="change_status('<?php echo $resd[0]['intid'] ?>', 'tblmember_apps');" class="i-switchs i-switch-mds i-switch-mds-horizontal">
                            <input id="notification" name="notification" type="checkbox" <?php if ($resd[0]['notification'] == 'active') { ?>checked<?php } ?> value="<?php echo $resd[0]['notification'] ?>" >
                            <i></i> </label>
                    </div>

                    <!--								<button class="btn btn-primary fr button_submit" type="submit">Save</button>-->
                    <button type="submit" class="btn btn-primary fr button_submit save_all save" value="Save" data-loading-text="Loading..." type="button" name="save_notification" id="save_notification">
                        Save
                    </button>
                    <div class="cl"></div>

                    <p class="app-satting">Backbone bits server needs to have a certificate in order to communicate with Apple Push Notification Services (APNS) and project key for sending push notification through Google Cloud Messaging (GCM).</p>
                </div>
                <div class="cl"></div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="apps-satting" style="display:inline-block; padding:10px 0 0 0;">
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
                                                <?php endif; ?>
                                                <div class="form-group col-md-12" style="float:left; padding:0;">
                                                    <input type="hidden" value="<?php echo $app_type; ?>" id="os_type" name="os_type"  class="form-control wd" >
                                                    <input type="hidden" value="<?php echo $sel_app_id; ?>" id="app_id" name="app_id"  class="form-control wd" >
                                                    <?php if ($app_type == 'android') { ?>
                                                        <div class="col-xs-12 col-md-6  padding-left-0">           <!--																										<label for="api_key" >Api Key</label>-->
                                                            <div class="input-group">
                                                                <input type="text" value="<?php echo $get_support_selected_app[0]['api_key']; ?>" id="api_key" name="api_key" placeholder="Enter Api Key" class="form-control wd" >

                                                            </div>

                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="row">
                                                            <div class="col-md-6 passphrase_development">
                                                                <h4>Passphrase Development</h4>
                                                                <div class="cl height30"></div>
                                                                <div class="col-xs-12 col-md-8 padding-left-0"> 
                                                                    <!--<label for="passphrase" >Passphrase Development</label>-->
                                                                    <div class="input-group">
                                                                        <input type="text" value="<?php echo $get_support_selected_app[0]['passphrase']; ?>" id="passphrase" name="passphrase" placeholder="" class="form-control wd" >
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-12 col-md-4  padding-left-0"> 
                                                                    <!--	<label>PEM Development Upload</label>-->
                                                                    <div class="input-group" style="position:relative !important; overflow:hidden !important;">
                                                                        <div id="pem"> <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon1.png" alt=""> <span>Upload</span>
                                                                                <input type="file" value="" id="pem_file" name="pem_file" placeholder="" class="form-control wd" >
                                                                            </span> </span> </div>
                                                                    </div>
                                                                </div>
                                                                <div class="notifation__" id="rem_dev"> 

                                                                    <p class="show_text"><?php echo $get_support_selected_app[0]['pem_file']; ?></p>
                                                                    <?php if (!empty($get_support_selected_app[0]['pem_file'])): ?>																														<a onclick="remove_pem_file('tbl_support_agent_allocate', 'dev', '<?php echo $get_support_selected_app[0]['intid']; ?>', '<?php echo $get_support_selected_app[0]['pem_file']; ?>');" href="javascript:;"><i class="apps_i remove_icon_" title="Delete"></i></a>
                                                                    <?php endif; ?>																														
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 passphrase_production">
                                                                <h4>Passphrase Production</h4>
                                                                <div class="cl height30"></div>
                                                                <div class="col-xs-12 col-md-8 padding-left-0"> 
                                                                    <!--<label for="passphrase_prod" >Passphrase Production</label>-->
                                                                    <div class="input-group">
                                                                        <input type="text" value="<?php echo $get_support_selected_app[0]['passphrase_prod']; ?>" id="passphrase_prod" name="passphrase_prod" placeholder="" class="form-control wd" >
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-12 col-md-4  padding-left-0"> 
                                                                    <!--<label>PEM Production Upload</label>-->
                                                                    <div class="input-group" style="position:relative !important; overflow:hidden !important;">
                                                                        <div id="pem_prod"> <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon1.png" alt=""> <span>Upload</span>
                                                                                <input type="file" value="" id="pem_prod_file" name="pem_prod_file" placeholder="" class="form-control wd" >
                                                                            </span> </span> </div>
                                                                    </div>
                                                                </div>

                                                                <div class="notifation__" id="rem_prod"> 

                                                                    <p class="show_text"> <?php echo $get_support_selected_app[0]['pem_prod_file']; ?></p>
                                                                    <?php if (!empty($get_support_selected_app[0]['pem_prod_file'])): ?>																															<a  onclick="remove_pem_file('tbl_support_agent_allocate', 'prod', '<?php echo $get_support_selected_app[0]['intid']; ?>', '<?php echo $get_support_selected_app[0]['pem_prod_file']; ?>');" href="javascript:;"><i class="apps_i remove_icon_" title="Delete"></i></a>
                                                                    <?php endif; ?>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="cl height1"></div>
                                                    <div class="col-xs-12 col-md-12  padding-left-0"> </div>
                                                </div>
                                                <div class="cl height30"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 class="infometion" style="width:auto; float:left;">Auto Review Message </h3>
                                                      
                                                        <div class="cl height10"></div>
                                                        <p>Please use following variable keys to draft your auto review message <b> % ticket_creator_name % -</b> App user name <b> % app_name % -</b> App Name associated with the ticket <b>% appstore_url % -</b> App store URL <b> % app_company_name % -</b> Developer/company name</p>
                                                        <div class="cl height10"></div>
                                                        <div class="box-body pad textediter messages1" style="padding:0 !important;">
                                                            <textarea id="review_message" name="review_message" class="textarea" placeholder="Enter Description Here" style="width: 100%; height:130px; font-size: 16px; color:#555; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $resd[0]['review_message'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="cl height10"></div>

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
</div>
<div class="cl height30"></div>
<div class="cl height10"></div>

<!--[ Notifition ]-->

<div class="modal fade" id="send_notifition" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel" style="font-size:30px;">Send Test Notifition</h4>
            </div>
            <div class="modal-body text-center">
                <div class="notifition_box">
                    <h5>Just Keep training, you'|| get better</h5>
                    <p>The push token of the device to puch</p>
                    <input type="text" value="" id="" name="" placeholder="Enter Notifition" class="form-control" >
                    <div class="cl height10"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default fl">Cancel</button>
                <button type="button" class="btn btn-primary">Send to Development</button>
                <button type="button" class="btn btn-primary">Send to Production</button>
            </div>
        </div>
    </div>
</div>

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

        //same as selection manual logo(id)
    });


    var sec_error_msg = '<?php echo $sec_error_msg; ?>';

    function change_status(id, table_name) {

        $('input').on('ifChecked', function (e) {
            change_status_prompt(table_name, 'active', id);
            $('#notification').val('active');
            $('#pem').removeClass('disab');
            $('#pem_prod').removeClass('disab');
            $('#pem_file').removeAttr('disabled', true);
            $('#pem_prod_file').removeAttr('disabled', true);

        });

        $('input').on('ifUnchecked', function (e) {

            change_status_prompt(table_name, 'inactive', id);
            $('#notification').val('inactive');
            $('#pem').addClass('disab');
            $('#pem_prod').addClass('disab');
            $('#pem_file').attr('disabled', true);
            $('#pem_prod_file').attr('disabled', true);



        });

    }

<?php if ($resd[0]['notification'] == 'active') { ?>
        $('#pem_file').removeAttr('disabled', true);
        $('#pem_prod_file').removeAttr('disabled', true);
        $('#pem').removeClass('disab');
        $('#pem_prod').removeClass('disab');
<?php } else { ?>
        $('#pem_file').attr('disabled', true);
        $('#pem_prod_file').attr('disabled', true);
        $('#pem').addClass('disab');
        $('#pem_prod').addClass('disab');
<?php } ?>

    function change_status_prompt(table_name, noti, id) {

        $(".jqibox").remove();
        $.prompt("", {
            title: "Are you sure you want to " + noti + " the notification",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function (e, v, m, f) {
                if (v != false) {
                    $("#overlays").show();
                    $.ajax({
                        type: "POST",
                        url: "process-update.php",
                        dataType: "json",
                        async: false,
                        data: {"id": id, "table_name": table_name, type: 'change_notification', "noti": noti},
                        success: function (data) {

                            if (data['output'] == 'S') {
                                message(data['msg'], 'success');
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
                if (noti != 'active') {
                    $('input').iCheck('check');

                } else {
                    $('input').iCheck('uncheck');

                }
            }


        });
    }


    function remove_pem_file(table_name, noti, id, filename) {
        $(".jqibox").remove();
        $.prompt("", {
            title: "Are you sure you want to  remove file",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function (e, v, m, f) {
                if (v != false) {
                    $("#overlays").show();
                    $.ajax({
                        type: "POST",
                        url: "process-update.php",
                        dataType: "json",
                        async: false,
                        data: {"id": id, "table_name": table_name, type: 'remove_pem_file', "noti": noti, "filename": filename},
                        success: function (data) {

                            if (data['output'] == 'S') {
                                if (noti == 'dev') {
                                    $('#rem_dev').hide();
                                } else {
                                    $('#rem_prod').hide();
                                }

                                message(data['msg'], 'success');
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
                if (noti != 'active') {
                    $('input').iCheck('check');

                } else {
                    $('input').iCheck('uncheck');

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
    $('#notification_form').validate({
        onkeyup: function (element) {
            $(element).valid()
        },
        rules: {
        },
        messages: {
            passphrase: "",
            passphrase_prod: "",
            pem_file: "",
            pem_prod_file: "",
            api_key: "",
        }
    });

</script> 
