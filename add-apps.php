<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
require("config/configuration.php");
if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

$member_id = $_SESSION['custid'];

$_SESSION["uploadDirectory"] = dirname(__FILE__) . '/files/apps/';
$_SESSION["uploadURL"] = SITE_URL . "files/apps/";
$_SESSION["uploadmDirectory"] = dirname(__FILE__) . '/files/more-apps/';
$_SESSION["uploadmURL"] = SITE_URL . "files/more-apps/";

if (isset($_SESSION['add_manual_app']) || $_REQUEST['sel_app_id'] != '') {
    $app_class = 'add_auto';
    $add_app_btn_value = "Add App Automatically";
    $action = 'save_app';
    $_SESSION['app_type'] = 'android';
} else {
    $app_class = 'add_manual';
    $add_app_btn_value = "Add App Manually";
    $action = 'save_multiple_apps';
    $_SESSION['app_type'] = 'ios';
}


if (isset($_REQUEST['sel_app_id']) && $_REQUEST['sel_app_id'] != '') {
    $sel_app_id = $_REQUEST['sel_app_id'];
    $res = $dclass->select("a.*,f.payment_type,f.payment_cost,f.feature_id,f.pf_id, f.feature_status, f.intid as id, f.pf_id", "tblmember_app_features f  INNER JOIN tblmember_apps  a ON f.app_id=a.intid", " AND a.intid='" . $sel_app_id . "' ");
} else {
    $sel_app_id = '';
}
//    }else{
//        $sel_app_id = '';
//        $res = $dclass->select("w.*,a.app_type,a.app_name,a.app_logo","tblmember_apps a INNER JOIN tblapp_whatsnew w ON a.intid=w.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id"," AND a.member_id='".$member_id."' AND a.app_status='active' AND f.feature_id='".$feature_id."' AND f.transaction_id != '0' order by a.app_add_date LIMIT 1");
//        $sel_app_id = $res[0]['app_id'];
//    }
//echo $dclass->_sql; exit;
if (count($res) > 0) {
    $sel_pf_id = array();
    for ($i = 0; $i < count($res); $i++) {
        $sel_pf_id[$i] = $res[$i]['pf_id'];
        $feature_status[$i] = $res[$i]['feature_status'];
    }
    $final_total = $res[0]['total_payment'];

    //print_r($sel_pf_id); die();
    //$intid = $res[0]['id'];
    //$feature_status = $res[0]['feature_status'];
    $app_type = $res[0]['app_type'];
    $_SESSION['app_type'] = $app_type;
    $app_name = $res[0]['app_name'];
    $or_app_name = $res[0]['or_app_name'];
    $company_name = $res[0]['company_name'];
    $app_store_id = $res[0]['app_store_id'];
    $track_id = $res[0]['track_id'];
    $app_key = $res[0]['app_key'];
    $app_logo = $res[0]['app_logo'];
    $app_url = $res[0]['app_url'];
    $app_add_date = $res[0]['app_add_date'];
    $next_billing_date = $res[0]['next_billing_date'];
    $current_billing_end_date = $res[0]['current_billing_end_date'];
    $current_payment = $res[0]['current_payment'];

    if ($app_logo != '')
        $app_logo_path = "memimages.php?max_width=75&imgfile=" . APP_LOGO . "/" . $app_logo;

    if ($app_type == 'ios')
        $ios_class = 'active';
    else if ($app_type == 'android')
        $android_class = 'active';
    else if ($app_type == 'windows')
        $windows_class = 'active';
    $app_status = $res[0]['app_status'];
    $server_status = $res[0]['server_status'];
    $pf_id = $res[0]['pf_id'];

    if ($res[0]['payment_type'] != '')
        $payment_type = $res[0]['payment_type'];

    if ($status == 'save') {
        $save_button_class = 'active';
    } else if ($status == 'publish') {
        $publish_button_class = 'active';
    } else if ($status == 'pause') {
        //$publish_button_class = 'active';
    }
} else {
    if ($_SESSION['add_manual_app']) {
        $ios_class = '';
        $android_class = 'active';
        $windows_class = '';
        $app_type = 'android';
        $final_total = '0.00';
    } else {
        $ios_class = 'active';
        $android_class = '';
        $windows_class = '';
        $app_type = 'ios';
        $final_total = '0.00';
    }
}
//echo $payment_type; 
//   if(!$payment_type)
//       $payment_type = 'monthly';

$fres = $dclass->select("pf.feature_id,pf.intid as id,f.fedesc,f.fename,f.felogo,p.pcost,p.pintval,p.ptype ", "tblpackage_features pf INNER JOIN tblfeatures f ON pf.feature_id=f.intid INNER JOIN tblpackages p ON pf.package_id=p.intid", "  AND pf.status='active' AND p.status='active' AND f.status='active' ORDER BY pf.feature_id");

//echo $dclass->_sql; exit;

include(INC . "header.php");



if ($sel_app_id) {
    //Get more app images
    $mres = $dclass->select("mi.*", "tblmore_app_images mi INNER JOIN tblmore_apps m ON mi.more_app_id=m.intid ", " AND m.parent_app_id='" . $sel_app_id . "' GROUP BY mi.intid");
    //echo $dclass->_sql;exit;
    for ($i = 0; $i < count($mres); $i++) {
        $mintid[$i] = $mres[$i]['intid'];
        $mimg[$i] = $mres[$i]['image'];
        $mimg_path[$i] = MORE_APPS_IMG . "/" . $mimg[$i];
        $mimg_source[$i] = $mres[$i]['source'];
        $mimg_status[$i] = $mres[$i]['status'];
        //echo $mimg_status[$i]; exit;
        if ($mimg_status[$i] == 'cover' && $mimg_source[$i] == 'store') {
            $more_app_sel_img_id = $mintid[$i];
            $more_app_sel_img = $mimg[$i];
            $more_app_sel_img_path = $mimg_path[$i];
        }

        if ($mimg_status[$i] == 'cover' && $mimg_source[$i] == 'custom') {
            $more_app_sel_new_img_id = $more_app_sel_img_id = $mintid[$i];
            $more_app_sel_img = $more_app_sel_new_img = $mimg[$i];
            $more_app_sel_img_path = $mimg_path[$i];
        } else if ($mimg_status[$i] != 'cover' && $mimg_source[$i] == 'custom') {
            $more_app_sel_new_img = $mimg[$i];
        }
    }

    if (!$more_app_sel_img && !$more_app_sel_new_img) {
        if ($mimg_source[0] == 'store') {
            $more_app_sel_img_id = $mintid[0];
            $more_app_sel_img = $mimg[0];
        } else {
            $more_app_sel_new_img = $mimg[0];
            $more_app_sel_new_img_id = $more_app_sel_img_id = $mintid[0];
        }
        $more_app_sel_img_path = $mimg_path[0];
    }

    //echo $more_app_sel_new_img; exit;
}
//echo $more_app_sel_img_id;exit;
//echo $more_app_sel_img_path; exit;
//set yearly as default
if ($sel_app_id == "") {
    $payment_type = 'yearly';
}
?>

<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">


    <!-- Content Header (Page header) -->

<?php include 'inc/app-navigate.php'; ?>
    <style type="text/css">

    </style>

    <!-- Main content -->

    <form id="addappfrm" method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" id="action" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" id="ajax" name="ajax" value="1" />
        <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >
        <input type="hidden" name="app_count" id="app_count" value="1" >
<?php if ($sel_app_id == '') { ?>
    <?php if (!isset($_SESSION['add_manual_app'])) { ?>
                <div id="new-add-apps">
                    <div class="col-md-12">
                        <div class="company_name">
                            <div class="col-md-2 padding0 fl">
                                <div style="margin:0;" class="col-md-12">
                                    <label style="margin:0; font-size:13px;" class="control-label fl" for="Platform">Platform</label>
                                    <ul class="all-apps-icon inputos fl">
                                        <li title="IOS" class="ip <?php echo $ios_class; ?> " id="ios">&nbsp;</li>
                                        <li title="Android" class="ad  <?php echo $android_class; ?> " id="and">&nbsp;</li>
                                        <!--<li id="wn" class="wn  ">&nbsp;</li>-->
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="col-md-12 padding0">
                                    <label class="col-sm-12 control-label fl padding0" style="margin:0; font-size:13px;" for="inputPassword">Company Name</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="text" value="" id="company_name" name="company_name" class="form-control wd ui-autocomplete-input serach__apps" placeholder="Company Name" autocomplete="off" data-provide="typeahead">
                                    </div>
                                    <label class="col-sm-12 control-label fl padding0" style="margin:-5px 0 0 0; font-size:12px; font-weight:lighter;" for="inputPassword">Locate your company and <?php echo BRAND; ?> will automatically list your apps</label>
                                </div>
                            </div>

        <!--        <div class="col-md-1 padding0" style="text-align:center;"><strong style=" display: inline-block;padding: 31px 0;text-align: center">OR</strong></div>

        <div class="col-md-4">
        <div class="col-md-12 padding0">
        <label class="col-sm-12 control-label fl padding0" style="margin:0;" for="inputPassword">&nbsp;</label>
        <div class="cl"></div>
        <div class="col-sm-12 padding0">
        <button class="btn btn-primary export <?php echo $app_class; ?> search__button" value="<?php echo $add_app_btn_value; ?>" type="button" style="" data-loading-text="Loading..."><?php echo $add_app_btn_value; ?></button>
        </div>
        </div>
        </div>    -->

                            <div class="cl"></div>
                        </div>
                    </div>
                    <div class="col-md-12" id="app_list">
                        <div class="search__list"> </div>
                    </div>
        <?php //}   ?>
                    <div class="cl"></div>
                </div>
                <div class="cl"></div>
    <?php } ?>
<?php } ?>
        <?php if ($sel_app_id != '' && count($fres) > 0 || (isset($_SESSION['add_manual_app']))) {
            ?>
            <div class="col-lg-12">
                <div class="col-md-12 padding0">
                    <div class="col-md-12 add_apps_left">
                        <h3 style="float:left; margin:0;">App Info</h3>
    <?php if ($sel_app_id != '') { ?>
                            <div class="fl" style="padding-left:30px !important;">
                                <button data-loading-text="Loading..." id="get_latest"  class="btn btn-primary  <?php echo $get_button_class; ?>" <?php if ($server_status == 'dev') { ?> disabled="true"<?php } ?> type="button" value="get_latest">Get Latest App Info</button>
                            </div>
    <?php } ?>
                        <div class="add_apps_bottom" style="padding:0 !important;">
                            <div class="fr promote_platform_top">
                                <div class="fr">
                                    <button data-loading-text="Loading..."  class="btn btn-primary save_all save <?php echo $save_button_class; ?>" type="button" value="save">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="cl"></div>
                        <div class="small_font">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp.</div>
                        <div class="cl height2"></div>
    <?php if ($sel_app_id != '') { ?>
                            <h5>Original App Name &nbsp;&nbsp;<span id="or_app_name_label" style="color:#999"><?php echo $or_app_name; ?></span> </h5>
                        <?php } ?>
                        <!-- Left -->
                        <div class="col-md-2 padding0 manually_chan">
                            <div class="col-md-12 padding0 manually_chan1">
    <?php if (count($mres) > 1) { ?>
                                    <div id="mfile_logo" class="file_logo">
                                        <div class="apps_section">
                                            <div class="fl app__img">
                                                <div class="edit_img">
                                                    <input type="hidden" name="more_app_sel_img" id="more_app_sel_img" value="<?php echo $more_app_sel_img_id; ?>" >
                                                    <input type="hidden" name="old_more_app_sel_new_img" id="old_more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >
                                                    <input type="hidden" name="more_app_sel_new_img" id="more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >
        <?php //if(count($mres) > 1){   ?>
                                                    <div class="edit__"  id="app_rlogo"><img src="<?php echo $more_app_sel_img_path; ?>" width="126" height="221" alt="" title="Add Images" />
                                                        <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal">Click to Edit</a></div>
                                                    </div>
        <?php //}else{   ?>
                                                    <!--                    <div class="upload_img" id="more_img">
                                                                                          <div class="center-img"> <img  src="<?php echo $more_app_sel_img_path; ?> " style="max-height: 255px;max-width: 255px;" alt="" title="Add Images" ></div>
                                                                                          <div class="over_img"><a href="javascript:;"><i class="fa fa-fw fa-trash-o" title="Delete"></i></a></div>
                                                                                        </div>-->
        <?php //}   ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- edit popup -->

                                    <div class="modal fade" id="more_app_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="myModalLabel" style="font-size:30px;">Select Images</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="select_app_img">
                                                        <ul id="select-app-img">
        <?php
        $cust_flag = 0;
        for ($i = 0; $i < count($mres); $i++) {
            $mintid = $mres[$i]['intid'];
            $mimg = $mres[$i]['image'];
            $mimg_path = MORE_APPS_IMG . "/" . $mimg;
            $mimg_source = $mres[$i]['source'];
            $mimg_status = $mres[$i]['status'];
            if ($mimg_source == 'custom') {
                $cust_flag = 1;
                $c = $i;
                $custom_path = $mimg_path;
                $custom_id = $mintid;
            }
            if ($more_app_sel_img_id == $mintid || $more_app_sel_new_img_id == $mintid) {
                $sclass = 'class="active"';
            } else {
                $sclass = '';
            }
            if ($mimg_source != 'custom') {
                ?>
                                                                    <input type="hidden" name="ssh-<?php echo $i; ?>"id="ssh-<?php echo $i; ?>" value="<?php echo $mimg; ?>" >
                                                                    <li <?php echo $sclass; ?> id="select_more_app_logo-<?php echo $i; ?>" onclick="select_manual_logo(this.id)"><img src="<?php echo $mimg_path; ?>" width="126" height="221" alt="" title="" />
                                                                        <div <?php echo $sclass; ?>></div>
                                                                    </li>
                                                                    <input type="hidden" name="sshid-<?php echo $i; ?>"id="sshid-<?php echo $i; ?>" value="<?php echo $mintid; ?>" >
            <?php } ?>
        <?php } ?>
                                                            <div class="or">OR </div>
                                                            <div class="file_logo <?php
                                                            if ($cust_flag == 1 && $custom_id == $more_app_sel_new_img_id) {
                                                                echo 'active';
                                                            }
                                                            ?>" id="sfile_logo" <?php if ($cust_flag == 1) { ?> onclick="select_manual_logo(this.id)" <?php } ?> ><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/plus_icon.png" alt=""> <span>Add Promotional Image</span>
                                                                    <input id="more_app_img" name="more_app_img" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                                </span>
                                                                <?php if ($cust_flag == 1) { ?>
                                                                    <input type="hidden" name="ssh-<?php echo $c; ?>"id="ssh-<?php echo $c; ?>" value="<?php echo $custom_id; ?>" >
                                                                    <div class="upload_img" id="more_inner_img">
                                                                        <div class="center-img"> <img  src="<?php echo $custom_path; ?> " style="height:auto;width: 230px;" ></div>
                                                                        <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="fa fa-fw fa-trash-o" style="font-size:24px"></i></a></div>
                                                                    </div>
                                                                <?php } ?>
                                                                <div <?php
                                                                if ($cust_flag == 1 && $custom_id == $more_app_sel_new_img_id) {
                                                                    echo 'class="active"';
                                                                }
                                                                ?>></div>
                                                            </div>
                                                            <div class="display_msg"></div>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                                                    <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- END Popup -->
                                    <?php } else { ?>
                                    <div id="sfile_logo">
        <?php if ($more_app_sel_img_path != '') { ?>
                                            <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/plus_icon.png" alt="" title=""> <span>Promotional Image</span>
                                                <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                            </span>
                                            <div class="upload_img" id="more_upload_img"> <img src="<?php echo $more_app_sel_img_path; ?>" width="126" height="221" alt="" title="" />
                                                <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)"><i class="fa fa-fw fa-trash-o"></i></a></div>
                                            </div>
                                            <input type="hidden" name="old_more_app_sel_new_img" id="old_more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >
        <?php } else { ?>
                                            <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/plus_icon.png" alt="" title=""> <span>Promotional Image</span>
                                                <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                            </span>
        <?php } ?>
                                        <input type="hidden" name="more_app_sel_img" id="more_app_sel_img" value="<?php echo $more_app_sel_img; ?>" >
                                        <input type="hidden" name="more_app_sel_new_img" id="more_app_sel_new_img" value="" >
                                        <input id="more_app_store_logo" name="more_app_store_logo"  type="hidden" value="">
                                    </div>
    <?php } ?>
                            </div>
                            <div class="col-md-12 padding0 manually_chan2">
                                <input type="hidden" name="script" id="script" value="<?php if ($sel_app_id)
        echo 'edit';
    else
        echo 'add';
    ?>" >
                                <!--            <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >-->
                                <input type="hidden" name="intid" id="intid" value="<?php echo $sel_app_id; ?>" >
                                <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id; ?>" >
                                <input type="hidden" name="or_app_name" id="or_app_name" value="<?php echo $or_app_name; ?>" >
                                <div id="file_logo">
    <?php if ($app_logo != '') { ?>
                                        <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/plus_icon.png" alt="" title=""> <span>App LOGO</span>
                                            <input id="app_logo" name="app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                        </span>
                                        <div class="upload_img" id="upload_img"> <img src="<?php echo $app_logo_path; ?>" alt="" title="" />
                                            <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1)"><i class="fa fa-fw fa-trash-o"></i></a></div>
                                        </div>
                                    <?php } else { ?>
                                        <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/plus_icon.png" alt="" title=""> <span>App LOGO</span>
                                            <input id="app_logo" name="app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                        </span>
    <?php } ?>
                                    <input id="app_store_logo" name="app_store_logo"  type="hidden" value="">
                                </div>
                                <div class="cl"></div>
                                <div class="col-md-12 padding0">
                                    <div class="col-md-12 padding0">
                                        <label for="inputPassword" class="col-sm-12 control-label fl padding0">Display Name 
                                            <!--            <a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Enter your app name" class="red-tooltip">
                                                                        <i class="fa fa-fw fa-question"></i>
                                                                    </a>--> 
                                        </label>
                                        <div class="cl"></div>
                                        <div class="col-sm-12 padding0">
                                            <input type="text" placeholder="App Name" class="form-control wd" name="app_name" id="app_name" value="<?php echo $app_name; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="cl height1"></div>
                                <div class="col-md-12 padding0">
                                    <div class="col-md-12 padding0">
                                        <label for="inputPassword" class="col-sm-12 control-label fl padding0">App URL 
                                            <!--            <a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Enter your app name" class="red-tooltip">
                                                                        <i class="fa fa-fw fa-question"></i>
                                                                    </a>--> 
                                        </label>
                                        <div class='cl '></div>
                                        <div class="col-sm-12 padding0">
                                            <input type="text" placeholder="App URL" class="form-control wd" name="app_url" id="app_url" value="<?php echo $app_url; ?>" <?php if ($app_url && $server_status == 'prod') { ?> readonly="true" <?php } ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cl height2"></div>
                        </div>

                        <!-- Right -->

                        <div class="col-md-7 padding0">
                            <section class="apps_cont_box1">
                                <div class="col-md-5 padding0">
                                    <div class="col-md-12" style="margin:0;">
                                        <label for="Platform" class="control-label fl" style="padding:0 10px 0 0;">Platform</label>
                                        <ul class="all-apps-icon inputos fl" style="width:100%;">
                                            <li id="ios" title="IOS" class="ip <?php echo $ios_class; ?> ">&nbsp;</li>
                                            <li id="and" title="Android" class="ad <?php echo $android_class; ?> ">&nbsp;</li>
                                            <!--<li id="wn" class="wn  ">&nbsp;</li>-->
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="inputPassword" class="col-sm-12 control-label fl padding0">App Status</label>
                                    <div class="cl"></div>
                                    <div class="col-sm-12 padding0">
                                        <div class="status_live_develop" style="font-size:14px; padding:5px 0 0 0;">
                                            <input type="radio" name="server_status" id="server_status1" value="dev" <?php
                                            if ($server_status == 'dev') {
                                                echo 'checked';
                                            }
                                            ?>   >
                                            In Development &nbsp;&nbsp;
                                            <input type="radio" name="server_status" id="server_status2" value="prod" <?php
                                            if ($server_status == 'prod') {
                                                echo 'checked';
                                            }
                                            ?> >
                                            Live </div>
                                    </div>
                                    <div class="cl height1"></div>
                                    <div class="small_font">Only LIVE app will be displayed in other apps promo list</div>
                                </div>
                                <!-- The global progress bar --> 
                                <!--          <div id="progress" class="progress">        
                                                <div class="progress-bar progress-bar-success"></div>  
                                                      </div>-->

                                <div class="cl height3"></div>
                                <div class="col-md-5 padding0">
                                    <div class="col-md-12">
                                        <label for="inputPassword" class="col-sm-12 control-label fl padding0">Bundle ID 
                                            <!--            <a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Enter your bundle code" class="red-tooltip">
                                                                        <i class="fa fa-fw fa-question"></i>
                                                                    </a>--> 
                                        </label>
                                        <div class="cl"></div>
                                        <div class="col-sm-12 padding0">
                                            <input type="text"placeholder="Bundle ID" class="form-control wd" name="app_store_id" id="app_store_id" value="<?php echo $app_store_id; ?>" <?php if ($app_store_id && $server_status == 'prod') { ?> readonly="true" <?php } ?>>
                                        </div>
                                    </div>
                                </div>
    <?php if ($sel_app_id != '') { ?>
                                    <div class="col-md-5 padding0">
                                        <div class="col-md-12" style="color:#999; line-height:28px;">
                                            <label for="inputPassword" class="col-sm-12 control-label fl padding0">API Key</label>
                                            <span class="api-key"> <?php echo $app_key; ?></span> </div>
                                    </div>
    <?php } ?>
                                <div class="cl height1"></div>
                                <div class="col-md-5 padding0">
                                    <div class="col-md-12">
                                        <label for="inputPassword" class="col-sm-12 control-label fl padding0">App ID 
                                            <!--            <a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Enter your bundle code" class="red-tooltip">
                                                                        <i class="fa fa-fw fa-question"></i>
                                                                    </a>--> 
                                        </label>
                                        <div class="cl"></div>
                                        <div class="col-sm-12 padding0">
                                            <input type="text"placeholder="App ID " class="form-control wd" name="track_id" id="track_id" value="<?php echo $track_id; ?>" <?php if ($track_id && $server_status == 'prod') { ?> readonly="true" <?php } ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="inputPassword" style="padding:0 10px 0 0; color: #999;" class="control-label fl">
    <?php
    if ($app_status == 'active' || $sel_app_id == '') {
        echo 'Active';
    } else {
        echo 'Inactive';
    }
    ?>
                                    </label>
                                    <div class="cl"></div>
                                    <div class="fl">
                                        <label class="i-switchs i-switch-mds i-switch-mds-horizontal">
                                            <input id="app_status" name="app_status" type="checkbox" value="active" <?php if ($app_status == 'active') echo 'checked'; ?> >
                                            <i></i> </label>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="cl height3"></div>
                    <div class="col-lg-12">
                        <div class="new_add_app">
                            <input type="hidden" name="payment_type" id ="payment_type" value="<?php echo $payment_type; ?>" >
                                    <?php if ($sel_app_id != '') { ?>
                                <input type="hidden" name="pchanged" id ="pchanged" value="0" >
                                <input type="hidden" name="achanged" id ="achanged" value="0" >
                                    <?php } ?>
                            <h3 style="padding:0px 0; margin:0;" class="fl"> <br />
                                Features</h3>
                            <div class="cl height3"></div>
                            <ul>
                                <li>
                                    <?php
                                    //$pflag = 0;
                                    $pcnt = 0;
                                    $acnt = 0;
                                    for ($i = 0; $i < count($fres); $i++) {
                                        $feature_id = $fres[$i]['feature_id'];
                                        $intid = $fres[$i]['id'];
                                        if ($sel_app_id && in_array($intid, $sel_pf_id)) {
                                            switch ($feature_id) {
                                                case 2:

                                                    $purl[2] = "#";
                                                    break;
                                                case 6:

                                                    $purl[6] = "#";
                                                    break;
                                            }
                                        } else if ($pcnt == 0) {
                                            $purl[2] = "#";
                                            $purl[6] = "#";
                                        }
                                    }
                                    //echo $purl[2];

                                    for ($i = 0; $i < count($fres); $i++) {
                                        $fename = $fres[$i]['fename'];
                                        $fedesc = $fres[$i]['fedesc'];
                                        $pcost = $fres[$i]['pcost'];
                                        $pintval = $fres[$i]['pintval'];
                                        $feature_id = $fres[$i]['feature_id'];

                                        if ($pintval == 'monthly') {
                                            $ptype = 'Month';
                                        } else {
                                            $ptype = 'Year';
                                        }
                                        $felogo = $fres[$i]['felogo'];
                                        if ($felogo)
                                            $felogo_path = FEATURE_LOGO . "/" . $felogo;
                                        else {
                                            $felogo_path = "img/iphone_platform_logo_icon.jpg";
                                        }

                                        $intid = $fres[$i]['id'];

                                        if (in_array($intid, $sel_pf_id)) {
                                            $hidden_pf_id = $intid;
                                            $ac_class = 'active';
                                            if ($feature_id == 6) {
                                                $feature_status = 'active';
                                            }
                                        } else {
                                            $ac_class = '';
                                            $hidden_pf_id = '';
                                            if ($feature_id == 6) {
                                                $feature_status = '';
                                            }
                                        }

                                        //set yearly as default for add
                                        if ($intid == 8 && $sel_app_id == '') {
                                            $final_total = $pcost;
                                            $hidden_pf_id = $intid;
                                        }

                                        //set analytics by default for add
                                        if ($feature_id == 6 && $sel_app_id == '') {
                                            $hidden_pf_id = $intid;
                                        }
                                        if ($hidden_pf_id && $sel_app_id != '') {
                                            $para_date[$pintval] = $next_billing_date;
                                        } else {
                                            if ($pintval == 'monthly') {
                                                if ($next_billing_date != '') {

                                                    $para_date[$pintval] = date("Y-m-d", strtotime($next_billing_date));
                                                } else
                                                    $para_date[$pintval] = date("Y-m-d", strtotime("+1 month"));
                                            }else {
                                                if ($next_billing_date != '')
                                                    $para_date[$pintval] = date("Y-m-d", strtotime($next_billing_date));
                                                else
                                                    $para_date[$pintval] = date("Y-m-d", strtotime("+1 year"));
                                            }
                                        }


                                        if ($pintval == 'yearly') {
                                            $extra_save = '<label>Save 20% </label>';
                                        } else {
                                            $extra_save = '';
                                        }
                                        ?>
                                        <input type="hidden" name="price-<?php echo $intid; ?>" id="price-<?php echo $intid; ?>" value="<?php echo $pcost; ?>"  />
                                        <input type="hidden" name="feature_id-<?php echo $intid; ?>" id="feature_id-<?php echo $intid; ?>" value="<?php echo $feature_id; ?>"  />
                                        <input type="hidden" id="pf_id<?php echo $pintval . "_" . $fename; ?>" name="pf_id[]"  value="<?php echo $hidden_pf_id; ?>" >
        <?php if ($fres[$i]['feature_id'] != $fres[$i - 1]['feature_id']) { ?>


                                            <div class="new_promotr">
                                                <div class="col-md-2 padding0"><a href="<?php echo $purl[$feature_id]; ?>"><img src="<?php echo $felogo_path; ?>" alt="" title="" /><br/>
                                                        <span id="feature_name_<?php echo $feature_id; ?>"><?php echo $fename; ?></span></a></div>
                                                <?php } ?>
                                                <?php if ($feature_id == 6) { ?>
                                                      <!--                     <div class="col-md-3"><strong>Free</strong></div>-->
                                                <div class="fl">
                                                    <label class="i-switchs i-switch-mds">
                                                        <input id="feature_status" name="feature_status" type="checkbox" value="active" >
                                                        <i></i> </label>
                                                </div>
                                                <label for="inputPassword" style="padding:5px 0px 0 15px; color: #999;" class="control-label fl">
            <?php
            if ($feature_status == 'active' || $sel_app_id == '') {
                echo 'Enabled';
            } else {
                echo 'Disabled';
            }
            ?>
                                                </label>
                                            </div>

                                            <div class="new_promotr" <?php if (!$sel_app_id) { ?> style="visibility:hidden;" <?php } ?> >
                                                <div class="col-md-2 padding0"><a href="<?php if (in_array(5, $sel_pf_id) && in_array('upgradr', $access_list)) { ?> upgradr?sel_app_id=<?php echo $sel_app_id; ?> <?php } else {
                echo '#';
            } ?>" > <img src="img/upgradr_app_icon.png" width="95" height="95" alt="" /> <span id="feature_name_<?php echo $feature_id; ?>">Prompt</span></a></div>
        <?php } ?>
        <?php if ($feature_id == 6) { ?>

                                                <div class="fl slider_bar">
                                                    <div class="fl slider_mi">
                                                        <div class="slider__box">
                                                            <div action="" class="slider_handle" id="upgradr">
                                                                <input type="radio" id="Upgradr1" name="Upgradr1" <?php if (!in_array(5, $sel_pf_id)) {
                echo 'checked="checked"';
            } ?> value="Upgradr" disabled="disabled">
                                                                <input type="radio" id="Upgradr2" name="Upgradr1" <?php if (in_array(5, $sel_pf_id)) {
                echo 'checked="checked"';
            } ?> value="monthly" disabled="disabled">
                                                                <input type="radio" id="Upgradr3" name="Upgradr1" value="yearly" disabled="disabled">
                                                                <div class="handle_hendle <?php if (in_array(5, $sel_pf_id)) {
                echo 'monthly';
            } ?>"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fl slider_title"> 
                                                        <span class="disabled"><?php if (in_array(5, $sel_pf_id)) {
                echo 'Enabled';
            } else {
                echo 'Disabled';
            } ?></span> 
                                                        <span class="year">$0.00/Month</span> 
                                                        <span class="year">$9.99/Year
                                                            <label>Save 20% </label>
                                                        </span> </div>
                                                </div>

                                            </div>

                                            <div class="new_promotr" <?php if (!$sel_app_id) { ?> style="visibility:hidden;" <?php } ?> >
                                                <div class="col-md-2 padding0"><a href="<?php if (in_array(4, $sel_pf_id) && in_array('help', $access_list)) { ?> help?sel_app_id=<?php echo $sel_app_id; ?> <?php } else {
                echo '#';
            } ?>" "> <img src="img/helpr_app_icon.png" width="95" height="95" alt="" /> <span id="feature_name_<?php echo $feature_id; ?>">Help</span></a></div>
        <?php } ?>
        <?php if ($feature_id == 6) { ?>

                                                <div class="fl slider_bar">
                                                    <div class="fl slider_mi">
                                                        <div class="slider__box">
                                                            <div action="" class="slider_handle" id="helpr">
                                                                <input type="radio" id="Helpr1" name="Helpr1" <?php if (!in_array(4, $sel_pf_id)) {
                echo 'checked="checked"';
            } ?> value="Helpr" disabled="disabled">
                                                                <input type="radio" id="Helpr2" name="Helpr1" <?php if (in_array(4, $sel_pf_id)) {
                echo 'checked="checked"';
            } ?> value="monthly" disabled="disabled">
                                                                <input type="radio" id="Helpr3" name="Helpr1" value="yearly" disabled="disabled">
                                                                <div class="handle_hendle <?php if (in_array(4, $sel_pf_id)) {
                                                echo 'monthly';
                                            } ?>"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fl slider_title"> 
                                                        <span class="disabled"><?php if (in_array(4, $sel_pf_id)) {
                                                echo 'Enabled';
                                            } else {
                                                echo 'Disabled';
                                            } ?></span> 
                                                        <span class="year">$0.00/Month</span> 
                                                        <span class="year">$9.99/Year
                                                            <label>Save 20% </label>
                                                        </span> </div>
                                                </div>
                                            </div>


                                            <div class="new_promotr" style="width:10% !important">
                                                <dd class="fr total_apps"> <span>Total for this app</span><br/>
                                                    Monthly <span class="pricing" ><?php echo CUR; ?><font id="total_monthly">
                                            <?php
                                            if ($payment_type == 'monthly') {
                                                echo $final_total;
                                            } else {
                                                echo '0.00';
                                            }
                                            ?>
                                                        </font></span> <br/>
                                                    Yearly <span class="pricing" ><?php echo CUR; ?><font  id="total_yearly">
                                            <?php
                                            if ($payment_type == 'yearly') {
                                                echo $final_total;
                                            } else {
                                                echo '0.00';
                                            }
                                            ?>
                                                        </font></span></dd>
                                            </div>

        <?php } else if ($fres[$i]['feature_id'] != $fres[$i - 1]['feature_id']) { ?>
                                            <div class="promotr_section">
                                                <div class="fl slider_mi">
                                                    <div class="slider__box">
                                                        <div action="" class="slider_handle" id="promotr">
                                                            <input type="radio" id="pay_type-<?php echo $fename; ?>"  name="pay_type" value="">
                                                            <input type="radio" id="pay_type-<?php echo $fename; ?>-2" name="pay_type" value="monthly">
                                                            <input type="radio" id="pay_type-<?php echo $fename; ?>-8" name="pay_type" value="yearly">
                                                            <div class="handle_hendle"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="fl slider_title"> <span class="disabled">Enabled</span>
        <?php } ?>
        <?php if ($feature_id != 6) { ?>
                                                    <span class="year"> <?php echo CUR . "" . $pcost; ?>/<?php echo $ptype . $extra_save; ?> </span>
        <?php } ?>
        <?php if ($pintval == 'yearly') { ?>
                                                </div>
                                            </div>
            <?php if (in_array(2, $sel_pf_id) && $current_billing_end_date != '') { ?>
                                                <div  class="col-md-3" id="<?php echo 'current_monthly_' . $fename; ?>">Current Deal Ends ON<br/>
                                                    <strong><?php echo date("j M, Y", strtotime($current_billing_end_date)); ?></strong></div>
            <?php } ?>
            <?php if ($hidden_pf_id == 8 && $current_billing_end_date != '') { ?>
                                                <div  class="col-md-3" id="<?php echo 'current_yearly_' . $fename; ?>">Current Deal Ends ON<br/>
                                                    <strong><?php echo date("j M, Y", strtotime($current_billing_end_date)); ?></strong></div>
            <?php } ?>
            <?php if (!in_array(2, $sel_pf_id) && !in_array(8, $sel_pf_id) && $current_billing_end_date != '') { ?>
                                                <div  class="col-md-3" id="<?php echo 'current_disabled_' . $fename; ?>">Current Deal Ends ON<br/>
                                                    <strong><?php echo date("j M, Y", strtotime($current_billing_end_date)); ?></strong></div>
            <?php } ?>
                                            <div <?php if (in_array(2, $sel_pf_id)) { ?> style="display:block;" <?php } else { ?>  style="display:none;" <?php } ?>class="col-md-3" id="<?php echo 'billing_monthly_' . $fename; ?>">
                                                Next Billing Date<br/>
                                                <strong><?php echo date("j M, Y", strtotime($para_date['monthly'])); ?></strong>
                                            </div>
                                            <div <?php if ($hidden_pf_id == 8) { ?>  style="display:block;" <?php } else { ?>  style="display:none;" <?php } ?> class="col-md-3" id="<?php echo 'billing_' . $pintval . "_" . $fename; ?>">
                                                Next Billing Date<br/>
                                                <strong><?php echo date("j M, Y", strtotime($para_date['yearly'])); ?></strong>
                                            </div>
        <?php } ?>
        <?php if ($pintval == 'monthly') { ?>

        <?php } ?>
        <?php if ($fres[$i]['feature_id'] == $fres[$i - 1]['feature_id']) { ?>
                                            </div>
        <?php } ?>



    <?php } ?>
                                </li>
                            </ul>

                            <!-- ================================================================================================================================ --> 
                            <!-- Start Add new --> 
                            <!-- ================================================================================================================================ -->

                            <!-- ================================================================================================================================ --> 
                            <!-- End Add new --> 
                            <!-- ================================================================================================================================ -->

                        </div>
                    </div>
                </div>
            </div>
            <div class="cl"></div>
            </section>
<?php } ?>
        <div class="cl"></div>

        <!-- /.content -->
</aside>
<!-- /.right-side -->
</div>
</form>
<!-- ./wrapper -->
<?php include(INC . "footer.php"); ?>

<script type="text/javascript">
<?php if (isset($sel_app_id) && $sel_app_id != '') { ?>
        var site_url = "add-apps?sel_app_id=<?php echo $sel_app_id; ?>";
        var script = 'edit';
<?php } else { ?>
        var site_url = "add-apps";
        var script = 'add';
<?php } ?>

    (function ($) {
        $.fn.serializefiles = function () {
            var obj = $(this);
            /* ADD FILE TO PARAM AJAX */
            var formData = new FormData();
            $.each($(obj).find("input[type='file']"), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            var params = $(obj).serializeArray();
            $.each(params, function (i, val) {
                formData.append(val.name, val.value);
            });
            return formData;
        };
    })(jQuery);


    //select manual logo
    function select_manual_logo(id) {
        $('#sfile_logo').removeClass('active');
        $("#select-app-img").children('li').removeClass('active');
        $('#' + id).addClass('active');
        $('#' + id).children('div:last').addClass('active');

    }

    //select logo
    function select_logo(id, track_id, count) {

        $("#select-app-img-" + track_id + "-" + count).children('li').removeClass('active');
        $('#' + id).addClass('active');

    }

    function remove_logo(track_id, count) {
        $('#preview-' + track_id + "-" + count).remove();
        $("#select-app-img-" + track_id + "-" + count).children('li').first().addClass('active');
        var img = $("#select-app-img-" + track_id + "-" + count).children('li').children('img').attr('src');
        $('#more_app_img-' + track_id + '-' + count).val(img);
        $('#more_app_new_img-' + track_id + '-' + count).val('');

    }

    function remove_manual_logo(id) {

        if (id == '2') {
            $('#preview_more_img').remove();
            $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
            $('#more_upload_img').remove();
            $("#select-app-img").children('li').first().addClass('active');
            var img = $("#select-app-img").children('li').children('img').attr('src');
            if ($('#more_inner_img').hasClass('upload_img')) {
                $('#more_inner_img').remove();
                $('#more_app_sel_img').val('');
                $('#more_app_sel_new_img').val(img);
            } else {
                $('#more_app_sel_img').val(img);
                $('#more_app_sel_new_img').val('');
            }


        } else if (id == '1') {
            $('#preview_img').remove();
            $('.preview_small_img').remove();
            $('#upload_img').remove();
        }
    }


    function replace_img(track_id, count) {
        var img = '';


        $("#select-app-img-" + track_id + "-" + count).children('li').each(function () {
            //alert($(this).hasClass('active'));
            if ($(this).hasClass('active')) {
                img = $(this).children('img').attr('src');
                $('#more_app_img-' + track_id + '-' + count).val(img);
                $('#more_app_new_img-' + track_id + '-' + count).val('');
            }
        });

        if (img == '' && $('#preview-' + track_id + "-" + count).hasClass('preview_small_img')) {
            img = $('#preview-' + track_id + "-" + count).children('div').children('img').attr('src');
            $('#more_app_new_img-' + track_id + '-' + count).val(img);
            $('#more_app_img-' + track_id + '-' + count).val('');
        }


        $('#app_rlogo-' + track_id + '-' + count).children('img').attr('src', img);

        $("#myModal" + count).modal("hide");
    }

    //replace manual more app image
    function replace_manual_img() {
        var img = '';

        $("#select-app-img").children('li').each(function () {
            if ($(this).hasClass('active')) {
                img = $(this).children('img').attr('src');
                var id = $(this).next('input').val();
                $('#more_app_sel_img').val(id);
                $('#more_app_sel_new_img').val('');

            }
        });

        if (img == '' && $('#preview_more_img').hasClass('preview_inner_img')) {
            img = $('#preview_more_img').children('div').children('img').attr('src');
            var id = $('#preview_more_img').prev('input').val();
            $('#more_app_sel_new_img').val(1);
            $('#more_app_sel_img').val('');
        }

        if (img == '' && $('#more_inner_img').hasClass('upload_img')) {
            img = $('#more_inner_img').children('div').children('img').attr('src');
            var id = $('#more_inner_img').prev('input').val();
            $('#more_app_sel_img').val(id);
            $('#more_app_sel_new_img').val('');
        }

        $('#app_rlogo').children('img').attr('src', img);

        $("#more_app_modal").modal("hide");
    }


    $(function () {

        var add_manual_app = '<?php echo $_SESSION['add_manual_app']; ?>';

<?php if ($hide_feature_class) { ?>
            $('.<?php echo $hide_feature_class ?>').hide();

<?php } ?>

        var app_type = 'ios';

<?php if ($app_status == 'yes') { ?>
            $('#app_status').iCheck('check');
<?php } ?>

        $('#app_status').on('ifToggled', function (event) {
            if ($('#app_status').parent('div').hasClass('checked')) {
                $('#app_status').iCheck('uncheck');
            } else {
                $('#app_status').iCheck('check');

            }
        });


        $('.inputos >li').on('click', function () {
            $('.inputos >li').removeClass('active');
            $(this).addClass('active');
            if ($('#ios').hasClass('active')) {
                var app_type = 'ios';
            } else if ($('#and').hasClass('active')) {
                var app_type = 'android';
            } else if ($('#wn').hasClass('active')) {
                var app_type = 'windows';
            }
            $('#app_type').val(app_type);

            //redirect to manual app section if android
            if (script == 'add' && add_manual_app == "" && app_type == 'android') {
                // var btn = $(this);

                var action = 'add_manual_app';
                var data = "ajax=1&action=" + action + "&app_type=" + app_type;
                //alert(data); return false;
                request = $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        //btn.button('loading');

                    },
                    success: function (data) {
                        //btn.button('reset');

                        if (data['output'] == 'S') {
                            location.href = site_url;
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }


                    }
                });
            }

        });


        $('#addappfrm').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                app_name: {
                    required: true,
                    maxlength: 100
                },
                app_store_id: "required",
                app_url: {
                    required: true,
                    url: true
                }
            },
            messages: {
                app_name: {
                    required: "",
                    maxlength: "max 25 characters"
                },
                app_store_id: "",
                app_url: {
                    required: "",
                    url: ""
                }
            }

        });

        if (script == 'add') {
            $('#app_status').iCheck('check');
            $('#server_status1').iCheck('check');
        }

        var feature_status = '<?php echo $feature_status; ?>';
        if (script != 'add' && feature_status == 'active') {
            $('#feature_status').iCheck('check');
        } else if (script == 'add') {
            $('#feature_status').iCheck('check');
        }


        $('.subscrip_ul >li').on('click', function () {
            $('.subscrip_ul >li>a').removeClass('active');
            $(this).children('a').addClass('active');
            if ($(this).hasClass('mn')) {
                $('#payment_type').val('monthly');
            } else {
                $('#payment_type').val('yearly');
            }
        });



        var filesList = [],
                paramNames = [],
                elem = $("form");

        'use strict';
// Change this to the location of your server-side upload handler:
        var url = 'ajax.php';

//Function to add/update apps   

        $('.save_all').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
            //e.preventDefault();  
            $('#addappfrm').valid();

            var go = 1;
            $('#addappfrm').find('input').each(function () {
                if ($(this).hasClass('error')) {
                    go = 0;
                    return false;
                }
            });



            if (go == 1) {

                var status = $(this).val();
                $('.save_all').removeClass('active');
                $('.' + status).addClass('active');
                $('#pstatus').val(status);
                var txt_status = $(this).text();

                var package_feature_id = [];
                var feature_price = [];

                $('input[name="pf_id[]"]').each(function () {
                    var val = $(this).val();

                    if (val != '') {
                        package_feature_id.push(val);
                    }
                });

                var total_payment = $('#total_' + $('#payment_type').val()).text();

                var wh = '';
                if (script != 'add') {
                    var intid = $('#intid').val();
                    wh = "&intid=" + intid;
                }

                var pchanged = $('#pchanged').val();
                var achanged = $('#achanged').val();


                //check if any feature subscription changed
                if (pchanged == 1 || achanged == 1) {
                    $.prompt("", {
                        title: "You are changing one or more of your feature subscription. <br/><b> Are you sure you want to continue?</b>",
                        buttons: {"Yes": true, "Cancel": false},
                        submit: function (e, v, m, f) {

                            if (v == false) {

                            } else {

                                var btn = $(this);

                                var data = $("#addappfrm").serializefiles();
                                data.append("status", status);
                                data.append("package_feature_id", package_feature_id);
                                data.append("total_payment", total_payment);

                                //alert(data); return false;
                                request = $.ajax({
                                    type: "POST",
                                    url: "ajax.php",
                                    data: data,
                                    dataType: 'json',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    beforeSend: function () {
                                        btn.button('loading');

                                    },
                                    success: function (data) {
                                        btn.button('reset');

                                        if (data['output'] == 'S') {
                                            location.href = "apps";
                                        } else if (data['output'] == 'F') {
                                            message(data['msg'], 'error');
                                        }


                                    }
                                });

                            }
                        }
                    });

                } else {
                    var btn = $(this);
                    var data = $("#addappfrm").serializefiles();
                    data.append("status", status);
                    data.append("package_feature_id", package_feature_id);
                    data.append("total_payment", total_payment);

                    request = $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            btn.button('loading');

                        },
                        success: function (data) {
                            btn.button('reset');

                            if (data['output'] == 'S') {
                                location.href = "apps";
                            } else if (data['output'] == 'F') {
                                message(data['msg'], 'error');
                            }


                        }
                    });

                }


            } else {
                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
            }
        });


        $("a").tooltip();

//filter for edit
        if (script != 'edit') {

            //App name autocomplete
            $("#app_name").autocomplete({
                source: function (request, response) {
                    var app_type = $('#app_type').val();
                    $.ajax({
                        dataType: "json",
                        type: 'POST',
                        url: 'ajax.php?ajax=1&action=auto_search_apps&app_name=' + $('#app_name').val() + '&app_type=' + app_type,
                        success: function (data) {
                            $('.ui-helper-hidden-accessible').hide();
                            $('#more_app_name').removeClass('ui-autocomplete-loading');  // hide loading image

                            response($.map(data, function (item) {
                                // your operation on data

                                return {
                                    label: item.name,
                                    value: item.id + "||" + item.track_id,
                                    desc: item.link + "||" + item.url
                                }
                            }));
                        },
                        error: function (data) {
                            $('#app_name').removeClass('ui-autocomplete-loading');
                        }
                    });
                },

                minLength: 2,
                open: function () {

                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    // alert('test');
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                },
                focus: function (event, ui) {
                },
                select: function (event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    $(this).attr('value', ui.item.label);
                    $('#app_name').val(ui.item.label);
                    $('#or_app_name').val(ui.item.label);

                    var app_store_id = ui.item.value.split('||')[0];
                    var track_id = ui.item.value.split('||')[1];

                    $('#app_store_id').val(app_store_id);
                    $('#app_store_id').attr('readonly', 'true');

                    $('#track_id').val(track_id);
                    $('#track_id').attr('readonly', 'true');


                    var app_logo = ui.item.desc.split('||')[0];
                    var app_url = ui.item.desc.split('||')[1];

                    $('#app_url').val('');
                    $('#app_url').val(app_url);
                    $('#app_url').attr('readonly', 'true');

                    if (app_type == 'ios') {
                        $('#app_store_logo').val('');
                        $('#app_store_logo').val(app_logo);
                        $('.preview_small_img').remove();

                        if (script != 'add') {
                            $('.upload_img').remove();
                        }
                        $('#file_logo').children('span').after('<div class="preview_small_img"><div class="center-img"><img src="' + app_logo + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1);"><i class="fa fa-fw fa-trash-o"></i></a></div></div>');

                    }

                    $.ajax({
                        dataType: "json",
                        type: 'POST',
                        cache: false,
                        url: 'ajax.php?ajax=1&action=auto_search_apps&flag=all&app_name=' + ui.item.label + '&app_store_id=' + app_store_id + '&app_type=' + $('#app_type').val(),
                        success: function (data) {
                            $('.ui-helper-hidden-accessible').hide();
                            $('#app_name').removeClass('ui-autocomplete-loading');  // hide loading image
                            //get all the data
                            if (data['app_type'] == 'android') {
                                $('#app_store_logo').val('');
                                $('#app_store_logo').val(data['logo']);
                                $('.preview_small_img').remove();
                                if (script != 'add') {
                                    $('.upload_img').remove();
                                }
                                $('#file_logo').children('span').after('<div class="preview_small_img"><div class="center-img"><img src="' + data['logo'] + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1)"><i class="fa fa-fw fa-trash-o"></i></a></div></div>');

                            }
                            if ($('#mfile_logo').hasClass('file_logo')) {
                                var object = $('#mfile_logo').parent('div');
                                $('#mfile_logo').remove();
                                object.html(data['res']);
                            } else {
                                var object = $('#sfile_logo').parent('div');
                                $('#sfile_logo').remove();
                                object.html(data['res']);
                            }

                            $('#more_app_logo').on('change', function (e) {
                                //e.preventDefault();
                                var id = this.id;
                                var fileInput = document.getElementById(id);

                                var file = fileInput.files[0];
                                var imageType = /image.*/;

                                var reader = new FileReader();
                                reader.onload = function (e) {

                                    $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img onclick="select_manual_logo(this.id)"  id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div id="remove_more_img" class="over_img">\n\
<a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="fa fa-fw fa-trash-o" style="font-size:24px"></i></a></div></div>');

                                }
                                reader.readAsDataURL(file);

                                $("#select-app-img").children('li').removeClass('active');


                            });

                            //remove image from manual app popup
                            $('#remove_more_img').on('click', function () {
                                $('#preview_small_img').remove();
                                $("#select-app-img").children('li').first().addClass('active');
                                var img = $("#select-app-img").children('li').children('img').attr('src');
                                $('#more_app_sel_img').val(img);
                                $('#more_app_sel_new_img').val('');
                            });

                        },
                        error: function (data) {
                            $('#app_name').removeClass('ui-autocomplete-loading');
                        }
                    });

                }
            });

<?php if ($server_status == 'dev' || $sel_app_id == '') { ?>
                $("#app_name").autocomplete("disable");
<?php } ?>

            //To check dev or prod based check
            $('input:radio[value="dev"]').on('ifChecked', function (event) {
                //alert('test');
                $("#app_name").autocomplete("disable");

                $('#app_store_id').removeAttr('readonly');
                $('#app_url').removeAttr('readonly');
                $('#track_id').removeAttr('readonly');

            });

            $('input:radio[value="prod"]').on('ifChecked', function (event) {
                $("#app_name").autocomplete("enable");
                $("#app_name").autocomplete("search", $("#app_name").val());
                $('#app_store_id').attr('readonly', 'true');
                $('#app_url').attr('readonly', 'true');
                $('#track_id').attr('readonly', 'true');


            });


        } else {

            //To check dev or prod based check
            $('input:radio[value="dev"]').on('ifChecked', function (event) {
                $('#get_latest').attr('disabled', 'true');

            });

            $('input:radio[value="prod"]').on('ifChecked', function (event) {

                $('#get_latest').removeAttr('disabled');

            });


        }



        $('.search__button').on('click', function () {
            var btn = $(this);
            if ($(this).hasClass('add_manual')) {
                var action = 'add_manual_app';
            } else {
                var action = 'add_auto_app';
            }

            var data = "ajax=1&action=" + action;
            //alert(data); return false;
            request = $.ajax({
                type: "POST",
                url: "ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    btn.button('loading');

                },
                success: function (data) {
                    btn.button('reset');

                    if (data['output'] == 'S') {
                        //message(data['msg'],'success');
                        location.href = site_url;
                    } else if (data['output'] == 'F') {
                        message(data['msg'], 'error');
                        //location.href="add-apps";
                    }


                }
            });
        });

        //for disabled/monthly/yearly toggle 
        $('input:radio').on('ifChecked', function (event) {
            //alert($(this).val());
            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('monthly');
            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('yearly');
            $(this).parent('div').parent('div').children('.handle_hendle').addClass($(this).val());
        });



        //Company name autocomplete
        $("#company_name").autocomplete({
            source: function (request, response) {
                var app_type = $('#app_type').val();

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: 'ajax.php?ajax=1&action=auto_search_company_apps&company_name=' + $('#company_name').val() + '&app_type=' + app_type,
                    success: function (data) {
                        $('.ui-helper-hidden-accessible').hide();
                        $('#company_name').removeClass('ui-autocomplete-loading');  // hide loading image

                        response($.map(data, function (item) {
                            // your operation on data

                            return {
                                label: item.name,
                                value: item.id,
                                desc: item.link
                            }
                        }));
                    },
                    error: function (data) {
                        $('#company_name').removeClass('ui-autocomplete-loading');
                    }
                });
            },
            minLength: 2,
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            },
            focus: function (event, ui) {
                //$("#more_app_name").val(ui.item.label);
                //return false;
            },
            select: function (event, ui) {
                event.preventDefault();
                //alert("Selected: " + ui.item.label + ui.item.value);
                $(this).val(ui.item.label);
                $(this).attr('value', ui.item.label);
                $('#company_name').val(ui.item.label);
                $('#company_name').addClass('ui-autocomplete-loading')
                //alert(ui.item.label);
                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: 'ajax.php?ajax=1&action=auto_search_company_apps&flag=all&company_name=' + ui.item.label + '&app_store_id=' + app_store_id + '&app_type=' + $('#app_type').val(),
                    success: function (data) {
                        $('.ui-helper-hidden-accessible').hide();
                        $('#company_name').removeClass('ui-autocomplete-loading');  // hide loading image

                        //get all the data
                        $('.search__list').html(data['res']);
                        $('#app_count').val(data['total']);
//                            
//                            $('.search_list_middel').slimScroll({
//                                height: '250px'
//                            });
//                                
                        $('input').iCheck({
                            checkboxClass: 'icheckbox_square-red',
                            radioClass: 'iradio_square-red',
                            increaseArea: '50%'
                        });

                        //analzr enabled by default    
                        $('.appnalytics_section').find('input:checkbox').iCheck('check');


                        $('.slider_handle').find('input:radio[value="yearly"]').iCheck('check');

                        // $('#pf_id'+price_suffix).val('9.99');

                        $('.appnalytics_section').find('input:checkbox').on('ifChecked', function (e) {
                            $(this).parent('div').parent('label').parent('div').next('.control-label').text('Enabled');
                        });

                        $('.appnalytics_section').find('input:checkbox').on('ifUnchecked', function (e) {
                            $(this).parent('div').parent('label').parent('div').next('.control-label').text('Disabled');
                        });

                        $('.slider_handle').find('input:radio').on('ifChecked', function (e) {
                            var val = $(this).val();
                            if (val != '') {
                                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Enabled');
                            } else {
                                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Disabled');
                            }
                        });


                        // window.onload = function() {
                        $('input[type="file"]').on('change', function (e) {
                            var id = this.id;
                            var fileInput = document.getElementById(id);
                            var parent_id = id.split("-")[1];
                            var inc = id.split("-")[2];

                            var file = fileInput.files[0];
                            //
                            var imageType = /image.*/;
                            if (file.type.match(imageType)) {


                                var reader = new FileReader();
                                reader.onload = function (e) {

                                    //alert(reader.result);
                                    $('#sfile_logo-' + parent_id + '-' + inc).children('span').after('<div id="preview-' + parent_id + '-' + inc + '" class="preview_small_img"><div class="center-img"><img id="img-' + parent_id + '-' + inc + '" onclick="select_logo(this.id,' + parent_id + ',' + inc + ')"  src="' + reader.result + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_logo(' + parent_id + ',' + inc + ');"><i class="fa fa-fw fa-trash-o" style="font-size:24px"></i></a></div></div>');
                                    $('#' + parent_id + '-' + inc).remove();
                                }
                                reader.readAsDataURL(file);

                                $("#select-app-img-" + parent_id + "-" + inc).children('li').removeClass('active');
                                $('#sfile_logo-' + parent_id + '-' + inc).closest('.error_msg').remove();
                            } else {
                                $('#preview-' + parent_id + '-' + inc).remove();
                                $('#sfile_logo-' + parent_id + '-' + inc).after('<div class="error_msg" id="' + parent_id + '-' + inc + '">File not supported!</div>');
                            }

                        });


                        //for disabled/monthly/yearly toggle 
                        $('input:radio').on('ifChecked', function (event) {
                            //alert($(this).val());
                            var pay_type = $(this).val();
                            var id = this.id;
                            var price_suffix = id.split("-")[1]; // unique app id
                            //var price_suffix_2 = id.split("-")[2]; //increatmental id
                            var price_suffix_3 = id.split("-")[3]; //package feature id

                            //alert(id);
                            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('monthly');
                            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('yearly');
                            $(this).parent('div').parent('div').children('.handle_hendle').addClass(pay_type);

                            //store package feature id
                            $('#pf_id' + price_suffix).val(price_suffix_3);


                            if (pay_type == 'monthly')
                                var price_id = '2';
                            else if (pay_type == 'yearly')
                                var price_id = '8';

                            $('#sel_price' + price_suffix).val($("#price-" + price_id + "-" + price_suffix).val());

                            if (pay_type != '')
                                $('#priced-' + price_suffix).empty().text($("#price-" + price_id + "-" + price_suffix).val());
                            else
                                $('#priced-' + price_suffix).empty().text('0.00');


                            if (pay_type != '') {
                                $('#priced-' + price_suffix).removeClass('pr_monthly');
                                $('#priced-' + price_suffix).removeClass('pr_yearly');
                                $('#priced-' + price_suffix).addClass('pr_' + pay_type);
                            }

                            var price_monthly = '0';
                            var price_yearly = '0';
                            $('.pr_monthly').each(function (e) {
                                //alert($(this).html());
                                price_monthly = parseFloat(price_monthly) + parseFloat($(this).html());
                            });
                            $('.pr_yearly').each(function (e) {
                                //alert($(this).html());
                                price_yearly = parseFloat(price_yearly) + parseFloat($(this).html());
                            });

                            $('#total_monthly').text(parseFloat(price_monthly).toFixed(2));
                            $('#total_yearly').text(parseFloat(price_yearly).toFixed(2));

                            var price = parseFloat(price_monthly) + parseFloat(price_yearly);

                            $('#total_payment').val(parseFloat(price).toFixed(2));
                        });

                        //save multiple apps  
                        $('.pay_now').on('click', function (e) {
                            //alert('test'); return false;
                            $('#noty_topCenter_layout_container').remove();
                            //e.preventDefault();  
                            $('#addappfrm').valid();

                            var go = 1;
                            $('#addappfrm').find('input').each(function () {
                                if ($(this).hasClass('error')) {
                                    go = 0;
                                    return false;
                                }
                            });

                            if (go == 1) {

                                var status = $(this).val();
                                //$('.pay_now').removeClass('active');
                                //$('.'+status).addClass('active');
                                $('#pstatus').val(status);
                                var txt_status = $(this).text();

                                var package_feature_id = [];
                                var feature_price = [];
                                //var price = {};

                                //                                  $('input[name="pf_id[]"]').each(function(){
                                //                                      var val = $(this).val();
                                //
                                //                                      if(val != ''){
                                //                                       package_feature_id.push(val);
                                //                                      }
                                //                                   });
                                //alert(package_feature_id); return false;

                                //var total_payment = $('#total_'+ $('#payment_type').val()).text();

                                var wh = '';
                                if (script != 'add') {
                                    var intid = $('#intid').val();
                                    wh = "&intid=" + intid;
                                }

                                //var formData = new FormData( $('input[type="file"]'));
                                // alert(formData); 

                                var btn = $(this);
                                var data = $("#addappfrm").serializefiles();
                                data.append("status", status);
                                // console.log(form_data); return false;
                                //alert(form_data); return false;
                                // var data =     "status="+status+wh+"&"+form_data;
                                //alert(data); return false;
                                request = $.ajax({
                                    type: "POST",
                                    url: "ajax.php",
                                    data: data,
                                    dataType: 'json',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    beforeSend: function () {
                                        btn.button('loading');

                                    },
                                    success: function (data) {
                                        btn.button('reset');

                                        if (data['output'] == 'S') {
                                            //message(data['msg'],'success');
                                            location.href = "apps";
                                        } else if (data['output'] == 'F') {
                                            message(data['msg'], 'error');
                                            //location.href="add-apps";
                                        }

                                    }
                                });
                            } else {
                                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
                            }
                        });



                    },
                    error: function (data) {
                        $('#company_name').removeClass('ui-autocomplete-loading');
                    }
                    //,
                    // complete: function(data){
//                           $('.search__top_middel').jscroll({
//                                loadingHtml: '<img src="img/ajax-loader.gif" alt="Loading" /> Loading...',
//                                nextSelector: 'a.jscroll-next:last',
//                                contentSelector: 'li'
//                            });
                    //}
                });


            }
        });


        $('input[type="file"]').on('change', function (e) {
            //e.preventDefault();
            var id = this.id;
            //alert(id); return false;
            var fileInput = document.getElementById(id);

            var file = fileInput.files[0];
            //alert(file); return false;
            var imageType = /image.*/;
            if (file.type.match(imageType)) {

                var reader = new FileReader();
                reader.onload = function (e) {

                    if (id == 'app_logo') {
                        $('#file_logo').children('span').after('<div id="preview_img" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1)"><i class="fa fa-fw fa-trash-o"></i></a></div></div>');
                        $('#app_type_error').remove();
                    } else if (id == 'more_app_logo' || id == 'more_app_img') {
                        $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="fa fa-fw fa-trash-o" style="font-size:24px"></i></a></div></div>');

                        $("#select-app-img").children('li').removeClass('active');
                        //if($('#server_status1').parent('div').hasClass('checked') ){
                        $('#more_app_sel_new_img').val(reader.result);
                        $('#more_app_type_error').remove();
                        //}      


                    }
                }
                reader.readAsDataURL(file);
            } else {
                if (id == 'app_logo') {
                    $('#preview_img').remove();
                    $('#app_type_error').remove();
                    $('#file_logo').after('<div class="error_msg" id="app_type_error">File not supported!</div>');

                } else if (id == 'more_app_logo' || id == 'more_app_img') {
                    $('#preview_more_img').remove();
                    $('#more_app_type_error').remove();
                    $('#sfile_logo').after('<div class="error_msg" id="more_app_type_error">File not supported!</div>');

                }
            }





        });

        //add active image in popup
        $('#preview_more_img').on('click', function () {
            $("#select-app-img").children('li').removeClass('active');
            $(this).addClass('active');
        });

        //remove image from manual app popup
        $('#remove_more_img').on('click', function () {
            $('#preview_small_img').remove();
            $("#select-app-img").children('li').first().addClass('active');
            var img = $("#select-app-img").children('li').children('img').attr('src');
            $('#more_app_sel_img').val(img);
            $('#more_app_sel_new_img').val('');
        });

        //for analytics
        var feature_name = $('#feature_name_6').text();

        if (script == 'add') {
            $('#pf_id_' + feature_name).val('6');
        }

        // alert(feature_status);

        $('#feature_status').on('ifChecked', function (event) {
            if (feature_status == '') {
                $("#achanged").val(1);
            } else {
                $("#achanged").val(0);
            }

            $('#pf_id_' + feature_name).val('6');
            $(this).parent('div').parent('label').parent('div').next('.control-label').text('Enabled');

        });

        $('#feature_status').on('ifUnchecked', function (event) {
            if (feature_status == 'active') {
                $("#achanged").val(1);
            } else {
                $("#achanged").val(0);
            }

            $('#pf_id_' + feature_name).val('');
            $(this).parent('div').parent('label').parent('div').next('.control-label').text('Disabled');
        });

        $('#app_status').on('ifChecked', function (event) {
            $(this).parent('div').parent('label').parent('div').prev('div').prev('.control-label').text('Active');
        });

        $('#app_status').on('ifUnchecked', function (event) {
            $(this).parent('div').parent('label').parent('div').prev('div').prev('.control-label').text('Inactive');
        });



        //get latest info
        $('#get_latest').off('click').on('click', function (e) {
            var app_type = $('#app_type').val();
            var app_name = $('#app_name').val();
            if (app_type == 'ios')
                var app_store_id = $('#track_id').val();
            else
                var app_store_id = $('#app_store_id').val();

            var app_id = '<?php echo $sel_app_id; ?>';

            if (app_type == 'ios' && app_store_id == '') {
                message('<?php echo $gnrl->getMessage('APP_ID_REQUIRED', $lang_id); ?>', 'error');
                $('#track_id').focus();
            } else if (app_name != '' && app_store_id != '') {
                $.prompt("", {
                    title: "Except from your custom image, all other app information will be fetched again from the app store and it will replace existing name, icon and images.<br/><b> Are you sure you want to continue?</b>",
                    buttons: {"Yes": true, "Cancel": false},
                    submit: function (e, v, m, f) {
                        // use e.preventDefault() to prevent closing when needed or return false. 
                        // e.preventDefault(); 

                        //console.log("Value clicked was: "+ v);

                        if (v == false) {
                            //e.preventDefault(); 
                        } else {

                            var btn = $(this);
                            var form_data = "ajax=1&action=get_latest_app_info&app_id=" + app_id + "&app_name=" + app_name + "&app_store_id=" + app_store_id + "&app_type=" + app_type;
                            //alert(form_data); return false;
                            request = $.ajax({
                                type: "POST",
                                url: "ajax.php",
                                data: form_data,
                                dataType: 'json',
                                cache: false,
                                beforeSend: function () {
                                    //                                                                       $("#ovelays").show();
                                    //                                                                       $('#loading-image').show();
                                    //                                                                       $('.preload-bg').show();
                                    btn.button('loading');
                                },
                                success: function (data) {
                                    btn.button('reset');
                                    //                                                                       $("#ovelays").hide();
                                    //                                                                       $('#loading-image').hide();
                                    //                                                                       $('.preload-bg').hide();

                                    if (data['output'] == 'S') {
                                        if (data['mres'] != '') {
                                            $('#more_app_sel_img').val('');
                                            $('#more_app_sel_new_img').val('');
                                            $('#more_app_modal').remove();
                                            if ($('#mfile_logo').hasClass('file_logo')) {
                                                var object = $('#mfile_logo').parent('div');
                                                $('#mfile_logo').remove();
                                                object.html(data['mres']);
                                            } else {
                                                var object = $('#sfile_logo').parent('div');
                                                $('#sfile_logo').remove();
                                                object.html(data['mres']);
                                            }
                                            // $('#app_rlogo').children('img').attr('src',data['ss']);


                                        }

                                        if (data['logo'] != '') {
                                            $('#upload_img').remove();
                                            $('#app_store_logo').val(data['logo']);
                                            $('#file_logo').children('span').after(data['lres']);

                                        }

                                    } else if (data['output'] == 'F') {
                                        message(data['msg'], 'error');
                                    }



                                }
                            });

                        }
                    }
                });
            } else {
                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
            }

        });


        if (script == 'add') {

            $('.slider_handle').find('input:radio[value="yearly"]').iCheck('check');
        } else {
<?php if (in_array(2, $sel_pf_id)) { ?>
                $('#promotr').find('input:radio[value="monthly"]').iCheck('check');
                var old_payment_type = 'monthly';
<?php } else if (in_array(8, $sel_pf_id)) { ?>
                $('#promotr').find('input:radio[value="yearly"]').iCheck('check');
                var old_payment_type = 'yearly';
<?php } else if (in_array(4, $sel_pf_id)) { ?>
                $('#helpr').find('input:radio[value="monthly"]').iCheck('check');
                var old_payment_type = 'yearly';
<?php } else if (in_array(5, $sel_pf_id)) { ?>
                $('#upgradr').find('input:radio[value="monthly"]').iCheck('check');
                var old_payment_type = 'yearly';
<?php } else { ?>
                $('.slider_handle').find('input:radio').closest('.slider_mi').next('.slider_title').children('.disabled').text('Disabled');
                var old_payment_type = '';
<?php } ?>
        }

        var current_billing_end_date = '<?php echo $current_billing_end_date; ?>';


        $('#promotr').find('input:radio').on('ifChecked', function (e) {
            var val = $(this).val();
            if (val != '') {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Enabled');
            } else {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Disabled');
            }
        });

        $('#helpr').find('input:radio').on('ifChecked', function (e) {
            var val = $(this).val();
            if (val != '') {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Enabled');
            } else {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Disabled');
            }
        });

        $('#upgradr').find('input:radio').on('ifChecked', function (e) {
            var val = $(this).val();
            if (val != '') {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Enabled');
            } else {
                $(this).closest('.slider_mi').next('.slider_title').children('.disabled').text('Disabled');
            }
        });



        //for disabled/monthly/yearly toggle 
        $('.slider_handle').find('input:radio').on('ifChecked', function (event) {
            //alert(old_payment_type);

            var pay_type = $(this).val();

            if (pay_type != old_payment_type) {
                $('#pchanged').val(1);
            } else {
                $('#pchanged').val(0);
            }

            if (pay_type != '') {
                $('#payment_type').val(pay_type);
            } else {

            }
            var id = this.id;
            var price_suffix = id.split("-")[1]; // package feature name
            var price_suffix_2 = id.split("-")[2]; //package feature id
            var price = parseFloat($('#price-' + price_suffix_2).val());

            //alert(price_suffix);
            //alert(id);
            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('monthly');
            $(this).parent('div').parent('div').children('.handle_hendle').removeClass('yearly');
            $(this).parent('div').parent('div').children('.handle_hendle').addClass(pay_type);

            //store package feature id
            $('#pf_id' + pay_type + "_" + price_suffix).val(price_suffix_2);
            $('#billing_' + pay_type + '_' + price_suffix).show('fade');

            // alert(pay_type);
            if (script != 'add') {
                if (current_billing_end_date == '') {


                    if (pay_type == '' && pay_type != old_payment_type) {

<?php if (in_array(2, $sel_pf_id)) { ?>
                            $('#billing_monthly_' + price_suffix).before('<div  class="col-md-3" id="current_monthly_' + price_suffix + '">Current Deal Ends ON<br/><strong><?php echo date("j M, Y", strtotime('-1 day', strtotime($para_date['monthly']))); ?></strong></div>');
                            $('#current_yearly_' + price_suffix).remove();
<?php } ?>
<?php if (in_array(8, $sel_pf_id)) { ?>
                            $('#billing_yearly_' + price_suffix).before('<div  class="col-md-3" id="current_yearly_' + price_suffix + '">Current Deal Ends ON<br/><strong><?php echo date("j M, Y", strtotime('-1 day', strtotime($para_date['yearly']))); ?></strong></div>');
                            $('#current_monthly_' + price_suffix).remove();
<?php } ?>
                    } else if (pay_type == old_payment_type || old_payment_type == '') {

                        //alert('test');
                        $('#current_monthly_' + price_suffix).remove();
                        $('#current_yearly_' + price_suffix).remove();

                    } else {
                        $('#billing_' + pay_type + '_' + price_suffix).before('<div  class="col-md-3" id="current_' + pay_type + '_' + price_suffix + '">Current Deal Ends ON<br/><strong><?php echo date("j M, Y", strtotime('-1 day', strtotime($para_date['yearly']))); ?></strong></div>');

                    }
                } else {
                    if (old_payment_type == '' && pay_type != old_payment_type) {
                        $('#current_disabled_' + price_suffix).hide();
                    } else {
                        $('#current_disabled_' + price_suffix).show();
                    }
                    $('#billing_' + pay_type + '_' + price_suffix).before('<div  class="col-md-3" id="current_' + pay_type + '_' + price_suffix + '">Current Deal Ends ON<br/><strong><?php echo date("j M, Y", strtotime('-1 day', strtotime($para_date['yearly']))); ?></strong></div>');

                }
            }
            // $('#current_' + pay_type + '_' + price_suffix).show('fade');

            if (pay_type == 'monthly') {

                //reset other pf_id
                $('#pf_idyearly_' + price_suffix).val('');
                $('#billing_yearly_' + price_suffix).hide();
                $('#current_yearly_' + price_suffix).remove();
                $('#total_yearly').text('0.00');
                //assign price for display
                $('#total_monthly').text(parseFloat(price).toFixed(2));

            } else if (pay_type == 'yearly') {
                //reset other pf_id
                $('#pf_idmonthly_' + price_suffix).val('');
                $('#billing_monthly_' + price_suffix).hide();
                $('#current_monthly_' + price_suffix).remove();
                $('#total_monthly').text('0.00');
                //assign price for display
                $('#total_yearly').text(parseFloat(price).toFixed(2));
            } else {
                //reset all for disabled
                $('#pf_idyearly_' + price_suffix).val('');
                $('#pf_idmonthly_' + price_suffix).val('');
                $('#billing_yearly_' + price_suffix).hide();
                $('#billing_monthly_' + price_suffix).hide();
                if (pay_type == old_payment_type || old_payment_type == '') {
                    $('#current_yearly_' + price_suffix).remove();
                    $('#current_monthly_' + price_suffix).remove();
                }
                $('#total_monthly').text('0.00');
                $('#total_yearly').text('0.00');
            }

            //$('#sel_price'+price_suffix).val($("#price-"+price_id+"-"+price_suffix).val());

//                        if(pay_type != '')
//                            $('#priced-'+price_suffix).empty().text($("#price-"+price_id+"-"+price_suffix).val());
//                         else
//                            $('#priced-'+price_suffix).empty().text('0.00');
//                          
//                       
//                          if(pay_type != ''){
//                            $('#priced-'+price_suffix).removeClass('pr_monthly');
//                            $('#priced-'+price_suffix).removeClass('pr_yearly');
//                            $('#priced-'+price_suffix).addClass('pr_'+pay_type);
//                          }




            $('#total_payment').val(parseFloat(price).toFixed(2));
        });


    });//doc.ready over


</script>
</body></html>
