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
if (count($res) > 0) {
    $sel_pf_id = array();
    for ($i = 0; $i < count($res); $i++) {
        $sel_pf_id[$i] = $res[$i]['pf_id'];
        $feature_status[$i] = $res[$i]['feature_status'];
    }
    $final_total = $res[0]['total_payment'];

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
        $app_logo_path = SITE_URL . APP_LOGO . "/" . $app_logo;

    $company_logo = $res[0]['company_logo'];
    if ($company_logo != '')
        $company_logo_path = SITE_URL . COMPANY_LOGO . "/" . $company_logo;

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

$fres = $dclass->select("pf.feature_id,pf.intid as id,f.fedesc,f.fename,f.felogo,p.pcost,p.pintval,p.ptype ", "tblpackage_features pf INNER JOIN tblfeatures f ON pf.feature_id=f.intid INNER JOIN tblpackages p ON pf.package_id=p.intid", "  AND pf.status='active' AND p.status='active' AND f.status='active' ORDER BY pf.feature_id");


include(INC . "header.php");
include INC . "left_sidebar.php";

if ($sel_app_id) {
    //Get more app images
    $mres = $dclass->select("mi.*", "tblmore_app_images mi INNER JOIN tblmore_apps m ON mi.more_app_id=m.intid ", " AND m.parent_app_id='" . $sel_app_id . "' GROUP BY mi.intid");

    for ($i = 0; $i < count($mres); $i++) {
        $mintid[$i] = $mres[$i]['intid'];
        $mimg[$i] = $mres[$i]['image'];
        $mimg_path[$i] = MORE_APPS_IMG . "/" . $mimg[$i];
        $mimg_source[$i] = $mres[$i]['source'];
        $mimg_status[$i] = $mres[$i]['status'];
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
}
?>
<form id="addappfrm" method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" id="action" name="action" value="<?php echo 'save_app'; ?>" />
    <input type="hidden" id="ajax" name="ajax" value="1" />
    <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >
    <input type="hidden" name="app_count" id="app_count" value="1" >
    <input type="hidden" name="server_status" id="server_status" value="<?php echo $server_status; ?>" >
    <aside class="right-side">
        <div class="right_sidebar">
            <div class="add-apps">
                <div class="col-xs-12 col-md-12">
                    <h1 class="fl">App Info <a href="javascript:;" id="get_latest" <?php if ($server_status == "dev") { ?> style="display:none;" <?php } ?> ><i class="fa fa-fw fa-refresh"></i></a></button>
                        <a href="#"></a></h1>
                    <button class="btn btn-primary fr button_submit save_all save <?php echo $save_button_class; ?>" type="button" data-loading-text="Loading..." value="Save">Save & Publish</button>
                    <div class="cl height10"></div>
                    <p>If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp.</p>
                </div>
                <div class="cl"></div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="add_apps_box">
                            <h5 class="col-md-12">Original App Name <span> <?php echo $or_app_name; ?></span></h5>
                            <div class="col-xs-12 col-md-4">
                                <div class="col-xs-12 col-md-12 manually_chan">
                                    <div class="col-md-12 padding0 manually_chan1">
<?php if (count($mres) > 1) { ?>  
                                            <div id="mfile_logo" class="file_logo"> 
                                                <div class="apps_section">
                                                    <div class="fl app__img">
                                                        <div class="edit_img">
                                                            <input type="hidden" name="more_app_sel_img" id="more_app_sel_img" value="<?php echo $more_app_sel_img_id; ?>" >
                                                            <input type="hidden" name="old_more_app_sel_new_img" id="old_more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >
                                                            <input type="hidden" name="more_app_sel_new_img" id="more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >

                                                            <div class="edit__"  id="app_rlogo">
                                                                <img id="tempappimage" src="img/loading_img.gif" style="position:absolute;top:120px;left:65px;display:none;"/>
                                                                <img id="mainappimage" src="<?php echo $more_app_sel_img_path; ?>" width="126" height="221" alt="Add Images" title="Add Images" />
                                                                <div class="edit__hover">
                                                                    <a href="#" data-toggle="modal" data-target="#more_app_modal">
                                                                        <i class="apps_i edit_icon_" title="Edit"></i>
                                                                    </a>

                                                                    <span style="height:40px;width:100px;" class="editpopupforlargeimage" onclick="imagelarge('<?php echo $more_app_sel_img_path; ?>');">View</span>

                                                                </div>
                                                            </div>
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
                                                                    ?>" id="sfile_logo" <?php if ($cust_flag == 1) { ?> onclick="select_manual_logo(this.id)" <?php } ?> ><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                                                                            <input id="more_app_img" name="more_app_img" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                                        </span>
                                                                        <?php if ($cust_flag == 1) { ?>
                                                                            <input type="hidden" name="ssh-<?php echo $c; ?>"id="ssh-<?php echo $c; ?>" value="<?php echo $custom_id; ?>" >
                                                                            <div class="upload_img" id="more_inner_img">
                                                                                <div class="center-img"> <img  src="<?php echo $custom_path; ?> " style="height:auto;width: 230px;" ></div>
                                                                                <div class="over_img">
                                                                                    <a href="javascript:;" onclick="remove_manual_logo(2,<?php echo $mintid; ?>)" >
                                                                                        <i class="apps_i remove_icon_" title="Replace" style="font-size:24px"></i>
                                                                                    </a>

                                                                                </div>
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

                                            <div id="sfile_logo" class="file_logo"> 
    <?php if ($more_app_sel_img_path != '') { ?>
                                                    <span class="btn add-files fileinput-button"> 
                                                        <img title="" alt="" src="img/upload_icon.png" class="add-logo_plus"> <span>Upload Image</span>
                                                        <input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="more_app_logo" id="more_app_logo">

                                                    </span>
                                                    <div class="upload_img" id="more_upload_img">
                                                        <img id="tempappimage" src="img/loading_img.gif" style="position:absolute;top:120px;left:65px;display:none;"/>

                                                        <img id="mainappimage" src="<?php echo $more_app_sel_img_path; ?>" width="180" height="297" alt="" title="" />
                                                        <div class="over_img">
                                                            <a href="javascript:;" onclick="remove_manual_logo(2,<?php echo $more_app_sel_img_id; ?>)">
                                                                <i class="apps_i remove_icon_" title="Remove"></i>
                                                            </a>
                                                            <a href="javascript:void(0);" class="popupforlargeimage" onclick="imagelarge('<?php echo $more_app_sel_img_path; ?>');">View</a>

                                                        </div>

                                                    </div>
                                                    <input type="hidden" name="old_more_app_sel_new_img" id="old_more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >

    <?php } else { ?>
                                                    <span class="btn add-files fileinput-button"> 
                                                        <img title="" alt="" src="img/upload_icon.png" class="add-logo_plus"> <span>Upload Image</span>
                                                        <input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="more_app_logo" id="more_app_logo">

                                                    </span>
    <?php } ?>
                                                <input type="hidden" name="more_app_sel_img" id="more_app_sel_img" value="<?php echo $more_app_sel_img_id; ?>" >
                                                <input type="hidden" name="old_more_app_sel_new_img" id="old_more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >
                                                <input type="hidden" name="more_app_sel_new_img" id="more_app_sel_new_img" value="<?php echo $more_app_sel_new_img; ?>" >

                                            </div>


<?php } ?>
                                    </div>
                                    <div class="col-md-12 padding0 manually_chan2">
                                        <input type="hidden" name="script" id="script" value="<?php if ($sel_app_id) echo 'edit';
else echo 'add'; ?>" >
                                        <input type="hidden" name="intid" id="intid" value="<?php echo $sel_app_id; ?>" >
                                        <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id; ?>" >
                                        <input type="hidden" name="or_app_name" id="or_app_name" value="<?php echo $or_app_name; ?>" >
                                        <div id="file_logo">
<?php if ($app_logo != '') { ?>
                                                <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/upload_icon.png" alt="" title=""> <span>Upload LOGO</span>
                                                    <input id="app_logo" name="app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                </span>
                                                <div class="upload_img" id="upload_img"> <img src="<?php echo $app_logo_path; ?>" alt="" title="" />
                                                    <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1,<?php echo $sel_app_id; ?>)"><i class="apps_i remove_icon_" title="Delete"></i></a></div>
                                                </div>
                                            <?php } else { ?>
                                                <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt="" title=""> <span>Upload LOGO</span>
                                                    <input id="app_logo" name="app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                </span>
<?php } ?>
                                            <input id="app_store_logo" name="app_store_logo"  type="hidden" value="">
                                        </div>
                                    </div>
                                    <div class="cl height2"></div>




                                    <div class="row">
                                        <div class="col-xs-12  col-md-12">
                                            <h2 class="company_info">Company Info</h2>
                                            <div class="col-xs-12 col-md-3 padding0 Profile_logo">

                                                <div id="file_logo" class="comp">
<?php if ($company_logo != '') { ?>
                                                        <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Logo</span>
                                                            <input id="company_logo" name="company_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                        </span>
                                                        <div class="upload_img" id="remove_comp_logo"> <img src="<?php echo $company_logo_path; ?>" />
                                                            <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1,<?php echo $sel_app_id; ?>, 'c')"><i class="apps_i remove_icon_" title="Delete"></i></a></div>
                                                        </div>
                                                        <input type="hidden" id="old_company_logo" name="old_company_logo" value="<?php echo $company_logo; ?>" >
                                                        <input type="hidden" id="del_old_company_logo"  name="del_old_company_logo" value="" >
                                                    <?php } else { ?>
                                                        <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Company Logo</span>
                                                            <input id="company_logo" name="company_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                        </span>
<?php } ?>
                                                </div>
                                                <div class="cl height5"></div>
                                            </div>
                                            <div class="col-xs-12 col-md-7 company_name_">
                                                <div class="form-group">
                                                    <label>Company Name</label>
                                                    <div class="input-group" style="width:100%;">
                                                        <input placeholder="e.g. Google Inc." type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>" >
                                                    </div>
                                                    <!-- /.input group --> 
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>


                            <div class="col-xs-12 col-md-8 app-details_right">

                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label>Display</label>
                                        <div class="input-group">
                                            <input type="text" name="app_name" id="app_name" value="<?php echo $app_name; ?>" class="form-control" placeholder="e.g. Facebook">
                                        </div>
                                        <!-- /.input group --> 
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group ">
                                        <label>Bundle ID </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="app_store_id" id="app_store_id" value="<?php echo $app_store_id; ?>" <?php if ($app_store_id && $server_status == 'prod') { ?> readonly="true" <?php } ?>>
                                        </div>
                                        <!-- /.input group --> 
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label>App ID</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="track_id" id="track_id" value="<?php echo $track_id; ?>" <?php if ($track_id && $server_status == 'prod') { ?> readonly="true" <?php } ?>>
                                        </div>
                                        <!-- /.input group --> 
                                    </div>
                                </div>	

                                <div class="col-xs-12 col-md-6">			
                                    <div class="form-group">
                                        <label>App Status</label>
                                        <div class="input-group" style="margin:8px 0 0 0;"> <span id="dev" class="live_development label  <?php if ($server_status == 'dev') {
    echo 'label-dev';
} else {
    echo 'label-button cursor';
} ?>">In Development</span> <?php if ($server_status == 'dev') { ?><span id="live" class="label label-button cursor">Make it live</span><?php } else { ?> <span id="live" class="<?php if ($server_status == 'prod') {
        echo 'label label-success';
    } ?>">Live</span><?php } ?> </div>
                                        <!-- /.input group --> 

                                    </div>
                                </div>


                                <div class="cl"></div>									
                                <div class="col-xs-12 col-md-12">

                                    <div class="form-group" style="width:100%;">
                                        <label>App URL</label>
                                        <div class="input-group">
                                            <input type="text" placeholder="App URL" onClick="this.select();" class="form-control" name="app_url" id="app_url" value="<?php echo $app_url; ?>" <?php if ($app_url && $server_status == 'prod') { ?> readonly="true" <?php } ?>>

                                            <div class="input-group-addon app__url"><a href="#copy" id="copy-dynamic"><i id="copy-dynamic" class="apps_i copy_i"></a></i> 
                                                <div class="popover top">
                                                    <div class="arrow"></div>
                                                    <div class="pdf-file">Copy</div>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                            </script>
                                        </div>
                                        <!-- /.input group --> 
                                    </div>

                                    <div class="form-group" style="width:100%;">
                                        <label>API Key</label>
                                        <div class="input-group">
                                            <input onClick="this.select();" type="text" name="api_key" id="api_key" class="form-control" disabled="true" value="<?php echo $app_key; ?>">

                                            <div class="input-group-addon api__key"><a href="#copy" id="copy-dynamic2"><i class="apps_i copy_i"> </a></i> 
                                                <div class="popover top">
                                                    <div class="arrow"></div>
                                                    <div class="pdf-file" id="copy">Copy</div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.input group --> 
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

<?php if ($sel_app_id != '') { ?>
        <input type="hidden" name="pchanged" id ="pchanged" value="0" >
        <input type="hidden" name="achanged" id ="achanged" value="0" >
<?php } ?>
</form>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px;max-height:800px;width:100%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Image preview</h4>
            </div>
            <div class="modal-body">
                <div align="center">
                    <img src="" id="imagepreview" style="">
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================================== --> 
<!-- Footer --> 
<!-- ============================================================================== -->

<?php include(INC . "footer.php"); ?>
<script src="js/jquery.zclip.js"></script>
<script type="text/javascript">

<?php if (isset($sel_app_id) && $sel_app_id != '') { ?>
    var site_url = "add-apps?sel_app_id=<?php echo $sel_app_id; ?>";
    var script = 'edit';
<?php } else { ?>
    var site_url = "add-apps";
    var script = 'add';
<?php } ?>
    $('#imgLoader').show();
    $('#imgMain').load(function () {
        $('imgLoader').hide();
    });
    $('#tempappimage').show();

    $("#mainappimage").on('load', function () {
        $('#tempappimage').hide();
    }).each(function () {
        if (this.complete)
            $(this).load();
    });

    function imagelarge(src)
    {

        $('#imagepreview').attr('src', src);
        $('#imagemodal').modal('show');
    }

    function remove_logo(id) {
        var file_input_id = '';
        if (id == 'remove_preview_img') {
            file_input_id = 'logo';
        } else if (id == 'remove_preview_company_img') {
            file_input_id = 'company_logo';
        }
        $("#" + id).remove();
        reset_field($('#' + file_input_id));
    }

    $("#company_logo").change(function () {

        var iclass = 'comp';
        var imageType = /image.*/;
        var fileInput = document.getElementById("company_logo");
        var file = fileInput.files[0];
        if (file.type.match(imageType)) {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_company_img" onclick="remove_logo(this.id)"><div class="center-img"><img src="' + e.target.result + '" height="118" width="118" ></div><div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div></div>');
                    $('#app_type_error').remove();
                }
                reader.readAsDataURL(this.files[0]);
            }
        } else {
            reset_field($('#company_logo'));
            $('#app_type_error').remove();
            $('.' + iclass).after('<div class="error_msg" id="app_type_error">File not supported!</div>');
        }


    });

//Function defination for custom serialize form with file upload
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

    $("a#copy-dynamic").zclip({
        path: "img/ZeroClipboard.swf",
        copy: function () {
            return $("input#app_url").val();
        }
    });
    $("a#copy-dynamic2").zclip({
        path: "img/ZeroClipboard.swf",
        copy: function () {
            return $("input#api_key").val();}
    });
//select manual logo
    function select_manual_logo(id) {
        $('#sfile_logo').removeClass('active');
        $("#select-app-img").children('li').removeClass('active');
        $('#' + id).addClass('active');
        $('#' + id).children('div:last').addClass('active');

    }

//remove from the detail
    function remove_manual_logo(id, mid, type1) {
        var prompt_txt = "";
        var type = "";
        if (id == 1) {
            if (type1 != 'c') {
                prompt_txt = "app logo";
                type = "app_logo_detail";
            } else {
                prompt_txt = "company logo";
                type = "company_logo_detail";
            }
        } else {
            prompt_txt = "promotional image";
            type = "custom_app_image_detail";

        }
        if (id == 1) {
            $.prompt("", {
                title: "Are you sure you want to delete this " + prompt_txt + "?",
                buttons: {"Yes": true, "Cancel": false},
                submit: function (e, v, m, f) {

                    if (v == false) {

                    } else {

                        var data = "ajax=1&action=delete_image&intid=" + mid + "&type=" + type;
                        $("#overlays").show();
                        request = $.ajax({
                            type: "POST",
                            url: "ajax.php",
                            data: data,
                            dataType: 'json',
                            cache: false,
                            beforeSend: function () {

                            },
                            success: function (data) {
                                $("#overlays").hide();
                                if (id == '2') {
                                    $('#preview_more_img').remove();
                                    $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
                                    $('#more_upload_img').remove();
                                    var img = $('#old_company_logo').val();
                                    $('#del_old_company_logo').val(img);
                                    if ($('#sfile_logo').hasClass('active')) {
                                        $("#select-app-img").children('li').removeClass('active');
                                        $("#select-app-img").children('li').first().addClass('active');
                                    } else {
                                        var img = $("#select-app-img").children('li.active').children('img').attr('src');
                                    }

                                    $('#sfile_logo').removeAttr("onclick");
                                    $('#sfile_logo').removeClass('active');

                                    if ($('#more_inner_img').hasClass('upload_img')) {
                                        $('#more_inner_img').remove();
                                        $('#more_app_sel_img').val('');
                                        $('#more_app_sel_new_img').val(img);
                                    } else {
                                        $('#more_app_sel_img').val(img);
                                        $('#more_app_sel_new_img').val('');
                                    }
                                    $("#select-app-img").children('li:first').addClass("active");


                                } else if (id == '1') {
                                    if (type1 != 'c') {
                                        $('#preview_img').remove();
                                        $('.preview_small_img').remove();
                                        $('#upload_img').remove();
                                    } else {
                                        var img = $('#old_company_logo').val();
                                        $('#del_old_company_logo').val(img);
                                        $('#remove_comp_logo').remove();
                                        $('#remove_preview_company_img').remove();

                                    }
                                }
                            }
                        });


                    }
                }

            });
        } else if (id == 2) {
            $('#preview_more_img').remove();
            var data = "ajax=1&action=delete_image&intid=" + mid + "&type=" + type;
            $("#overlays").show();
            request = $.ajax({
                type: "POST",
                url: "ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    $("#overlays").hide();

                    if (id == '2') {
                        $('#preview_more_img').remove();
                        $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
                        $('#more_upload_img').remove();
                        var img = $('#old_company_logo').val();
                        $('#del_old_company_logo').val(img);
                        if ($('#sfile_logo').hasClass('active')) {
                            $("#select-app-img").children('li').removeClass('active');
                            $("#select-app-img").children('li').first().addClass('active');
                        } else {
                            var img = $("#select-app-img").children('li.active').children('img').attr('src');
                        }

                        $('#sfile_logo').removeAttr("onclick");
                        $('#sfile_logo').removeClass('active');

                        if ($('#more_inner_img').hasClass('upload_img')) {
                            $('#more_inner_img').remove();
                            $('#more_app_sel_img').val('');
                            $('#more_app_sel_new_img').val(img);
                        } else {
                            $('#more_app_sel_img').val(img);
                            $('#more_app_sel_new_img').val('');
                        }
                        $("#select-app-img").children('li:first').addClass("active");


                    } else if (id == '1') {
                        if (type1 != 'c') {
                            $('#preview_img').remove();
                            $('.preview_small_img').remove();
                            $('#upload_img').remove();
                        } else {
                            var img = $('#old_company_logo').val();
                            $('#del_old_company_logo').val(img);
                            $('#remove_comp_logo').remove();
                            $('#remove_preview_company_img').remove();

                        }
                    }
                }
            });
            $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
            $('#more_upload_img').remove();
            if ($('#sfile_logo').hasClass('active')) {
                $("#select-app-img").children('li').removeClass('active');
                $("#select-app-img").children('li').first().addClass('active');
            } else {
                var img = $("#select-app-img").children('li.active').children('img').attr('src');
            }

            $('#sfile_logo').removeAttr("onclick");
            $('#sfile_logo').removeClass('active');

            if ($('#more_inner_img').hasClass('upload_img')) {
                $('#more_inner_img').remove();
                $('#more_app_sel_img').val('');
                $('#more_app_sel_new_img').val(img);
            } else {
                $('#more_app_sel_img').val(img);
                $('#more_app_sel_new_img').val('');
            }
        }

    }

//remove preview image
    function remove_manual_logo_preview(id) {
        if (id == '2') {
            $('#preview_more_img').remove();
            $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
            $('#more_upload_img').remove();
            if ($('#sfile_logo').hasClass('active')) {
                $("#select-app-img").children('li').removeClass('active');
                $("#select-app-img").children('li').first().addClass('active');
            } else {
                var img = $("#select-app-img").children('li.active').children('img').attr('src');
            }

            $('#sfile_logo').removeAttr("onclick");
            $('#sfile_logo').removeClass('active');

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


        if (img) {
            $('#app_rlogo').children('img').attr('src', img);

            $('#app_rlogo').find('.editpopupforlargeimage').removeAttr('onclick');
            $('#app_rlogo').find('.editpopupforlargeimage').attr("onclick", "imagelarge('" + img + "')");
            $("#more_app_modal").modal("hide");
        }
    }


    $(function () {



        $('#app_status').on('ifToggled', function (event) {
            if ($('#app_status').parent('div').hasClass('checked')) {
                $('#app_status').iCheck('uncheck');
            } else {
                $('#app_status').iCheck('check');

            }
        });

        //form validate
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


        //Function to add/update apps   

        $('.save_all').on('click', function (e) {

            $('#noty_topCenter_layout_container').remove();
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
                var txt_status = $(this).text();



                var total_payment = '0';

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
                                location.href = "apps?list=apps";
                            } else if (data['output'] == 'F') {
                                message(data['msg'], 'error');
                            }


                        }
                    });
                    e.preventDefault();

                }


            } else {
                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
            }
        });


        //image preview
        $('input[type="file"]').on('change', function (e) {
            //e.preventDefault();
            var id = this.id;
            var fileInput = document.getElementById(id);

            var file = fileInput.files[0];
            var imageType = /image.*/;
            if (file.type.match(imageType)) {

                var reader = new FileReader();
                reader.onload = function (e) {

                    if (id == 'app_logo') {
                        $('#file_logo').children('span').after('<div id="preview_img" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(1)"><i class="apps_i edit_icon_"></i></a></div></div>');
                        $('#app_type_error').remove();
                    } else if (id == 'more_app_logo' || id == 'more_app_img') {


                        if (id == 'more_app_logo')
                        {
                            $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a><a onclick="imagelarge(\'' + reader.result + '\');" class="popupforlargeimage" href="javascript:void(0);">View</a></div></div>');
                        } else
                        {
                            $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div></div>');
                        }

                        $('#sfile_logo').addClass("active");
                        $('#sfile_logo').children('div').last().addClass("active");
                        $("#select-app-img").children('li').removeClass('active');
                        $('#more_app_sel_n\n\
\n\
\n\
\n\
\n\
ew_img').val(reader.result);
                        $('#more_app_type_error').remove();


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
            alert('test');
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

        //get latest info
        $('#get_latest').off('click').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
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

                        if (v == false) {
                        } else {
                            $("#overlays").show();
                            var btn = $(this);
                            var form_data = "ajax=1&action=get_latest_app_info&app_id=" + app_id + "&app_name=" + app_name + "&app_store_id=" + app_store_id + "&app_type=" + app_type;
                            request = $.ajax({
                                type: "POST",
                                url: "ajax.php",
                                data: form_data,
                                dataType: 'json',
                                cache: false,
                                beforeSend: function () {

                                    btn.button('loading');
                                },
                                success: function (data) {
                                    btn.button('reset');
                                    $("#overlays").hide();



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
                                            $('input[type="file"]').on('change', function (e) {
                                                var id = this.id;
                                                var fileInput = document.getElementById(id);

                                                var file = fileInput.files[0];
                                                var imageType = /image.*/;
                                                if (file.type.match(imageType)) {

                                                    var reader = new FileReader();
                                                    reader.onload = function (e) {

                                                        if (id == 'app_logo') {
                                                            $('#file_logo').children('span').after('<div id="preview_img" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(1)"><i class="apps_i edit_icon_"></i></a></div></div>');
                                                            $('#app_type_error').remove();
                                                        } else if (id == 'more_app_logo' || id == 'more_app_img') {
                                                            $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(2)" ><i class="apps_i edit_icon_" style="font-size:24px"></i></a></div></div>');

                                                            $("#select-app-img").children('li').removeClass('active');
                                                            $('#more_app_sel_new_img').val(reader.result);
                                                            $('#more_app_type_error').remove();


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



                                        }

                                        if (data['logo'] != '') {
                                            $('#upload_img').remove();
                                            $('.preview_small_img').remove();
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


        $("#live, #dev").on("click", function (e) {
            var id = this.id;
            if (id == 'live') {
                $('#get_latest').show();
                $(this).removeClass('label-button').removeClass('cursor').addClass('label-success');
                $(this).html('Live');
                $("#dev").removeClass('label-dev').addClass('label-button').addClass('cursor');
                $("#server_status").val("prod");

            } else if (id == 'dev') {
                $.prompt("", {
                    title: "Changing status to <b>In Development</b> will delete this app's promotional records in other apps.<br/><b> Are you sure you want to continue?</b>",
                    buttons: {"Yes": true, "Cancel": false},
                    submit: function (e, v, m, f) {

                        if (v == false) {
                        } else {
                            $('#get_latest').hide();
                            $("#dev").removeClass('label-button').removeClass('cursor').addClass('label-dev');
                            $("#live").removeClass('label-success').addClass('label-button').addClass('cursor');
                            $("#live").html('Make it live');
                            $("#server_status").val("dev");
                        }

                    }
                });
            }


        });


    });//doc.ready over


    $(document).ready(function () {
        $('.app__url').click(function () {
            $('input#app_url').addClass('highlight');
            setTimeout(function () {
                $('input#app_url').removeClass('highlight');
                //....and whatever else you need to do
            }, 500);
        });
    });

    $(document).ready(function () {
        $('.api__key').click(function () {
            $('input#api_key').addClass('highlight');
            setTimeout(function () {
                $('input#api_key').removeClass('highlight');
                //....and whatever else you need to do
            }, 500);
        });
    });
</script>