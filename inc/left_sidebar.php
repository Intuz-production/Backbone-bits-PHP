<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

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

if ($sel_app_id)
    $append = "?sel_app_id=" . $sel_app_id;
else {
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
?>

<aside id="leftside_bar" class="left-side sidebar-offcanvas"> 
    <!-- sidebar: style can be found in sidebar.less -->
    <section id="leftside_bar1" class="sidebar"> 

        <!-- ======================================================= --><?php
        if ($url == 'dashboard') {
            ?><ul class="sidebar-menu dashboard_menu"><?php
            if (in_array('apps', $access_list)) {
                
            }
            if ($feature[3] == 'running') {
                if (in_array('respond', $access_list)) {
                    ?><li class="data-block second_element_to_target"  data-position='right'><a href="respond"> <i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond <i class="fa fa-fw "></i></span></a> </li><?php
                    }
                }

                if ($feature[4] == 'running') {
                    if (in_array('help', $access_list)) {
                        ?><li class="data-block second_element_to_target"  data-position='right'><a href="apps?list=help"> <i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help <i class="fa fa-fw "></i></span></a> </li><?php
                                }
                            }
                            ?><li><a href="profile"> <i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings <i class="fa fa-fw "></i></span></a> </li>
            </ul><?php
        }

        if ($url == 'apps') {
            ?><ul class="sidebar-menu"><?php
            if ($_REQUEST['list'] != 'analyzr' && $_REQUEST['list'] != 'help' && $_REQUEST['list'] != 'respond'):
                ?><li id="allappss" class="app-menu anchor_menu_sub" style="border-bottom:1px solid #ebebeb !important;"> <a class="anchor_menu" href="apps?list=apps"><i class="menu_i apps_icon">&nbsp;</i> <span class="menu_t">Apps </span> </a>
                        <div class="treeview new_menu_1"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                            <ul class="treeview-menu popover right">
                                <div class="arrow"></div><?php
                                if ($feature[3] == 'running') {
                                    if (in_array('respond', $access_list)) {
                                        ?><li> <a href="respond"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span></a> </li><?php
                                    }
                                }

                                if ($feature[4] == 'running') {
                                    if (in_array('help', $access_list)) {
                                        ?><li> <a href="apps?list=help"><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help</span></a> </li><?php
                                        }
                                    }
                                    if (in_array('app-settings', $access_list)) {
                                        ?><li> <a href="app-settings<?php echo $append; ?>"><i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings</span></a> </li><?php
                                    }
                                    ?></ul>
                        </div>
                    </li><?php
                endif;

                if ($_REQUEST['list'] == 'help'):
                    ?><li id="allappss" class="app-menu anchor_menu_sub" style="border-bottom:1px solid #ebebeb !important;"> <a class="anchor_menu" href="apps?list=help"><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help </span> </a>
                        <div class="treeview new_menu_3"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                            <ul class="treeview-menu popover right">
                                <div class="arrow"></div><?php
                                if (in_array('apps', $access_list)) {
                                    ?><li> <a href="apps?list=apps"> <i class="menu_i apps_icon">&nbsp;</i> <span class="menu_t">Apps</span></a> </li><?php
                                }

                                if ($feature[3] == 'running') {
                                    if (in_array('respond', $access_list)) {
                                        ?><li> <a href="respond"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span></a> </li><?php
                                        }
                                    }

                                    if (in_array('app-settings', $access_list)) {
                                        ?><li> <a href="app-settings<?php echo $append; ?>"><i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings</span></a> </li><?php
                                    }
                                    ?></ul>
                        </div>
                    </li><?php
                endif;
                ?><li class="arrow_bottom-aap"><i class="fa fa-fw fa-caret-down"></i></li>
            </ul><?php
            if ($_REQUEST['list'] == 'apps') {
                if ($_SESSION['role'] == 'technical' || $_SESSION['role'] == 'admin') {
                    ?><dd class="plus_icon fl fourth_element_to_target" data-step="1" data-intro="Start adding your apps"><a data-dialog="somedialog" class="trigger" href="javascript:;"><img alt="" src="img/plus_icon.png"></a></dd><?php
                } else {
                    ?><dd class="plus_icon fl"></dd><?php
                        }
                    } else {
                        ?><dd class="plus_icon fl"></dd><?php
            }
            ?><div class="cl"></div>
            <form action="javascript:;" method="get" class="sidebar-form">
                <input type="hidden" name="no_record" id="no_record" value="<?php echo $no_record; ?>"/>
                <input type="hidden" name="no_ad_record" id="no_ad_record" value="<?php echo $no_ad_record; ?>"/>
                <div class="input-group"> <span class="input-group-btn">
                        <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                    </span>
                    <input type="text" name="q" id="q" class="form-control" placeholder="Search..."/>
                </div>
            </form>
            <div class="left_platform">
                <ul class="all-apps-icon">
                    <li class="all_apps button_bg <?php echo $all_class; ?>" title="All Apps">All</li>
                    <li class="ip <?php echo $ios_class; ?>">&nbsp;</li>
                    <li class="ad <?php echo $android_class; ?>">&nbsp;</li>
                </ul>
                <div class="cl height10"></div>
            </div><?php
        }
        if ($url == 'app-details' || $url == 'notification' || $url == 'help-faq-list' || $url == 'help-img-video' || $url == 'help-img-video-archive' || ($url == 'app-settings' && !empty($append))) {
            ?>
            <ul class="sidebar-menu"><?php
                if ($url == 'app-settings') {
                    $class_icon = 'menu_i apps_icon';
                    $name_class = 'Apps';
                    $url_link = 'apps?list=apps';
                    $gen_class_leftbar = 'menu_1_apps';
                } else if ($url == 'notification') {
                    $class_icon = 'menu_i apps_icon';
                    $name_class = 'Apps';
                    $url_link = 'apps?list=apps';
                    $gen_class_leftbar = 'menu_1_apps';
                } else if ($url == 'app-details') {
                    $class_icon = 'menu_i apps_icon';
                    $name_class = 'Apps';
                    $url_link = 'apps?list=apps';
                    $gen_class_leftbar = 'menu_1_apps';
                } else if ($url == 'apps?list=help' || $url == 'help-faq-list' || $url == 'help-img-video' || $url == 'help-img-video-archive') {
                    $class_icon = 'menu_i helpr_icon';
                    $name_class = 'Help';
                    $url_link = 'apps?list=help';
                    $gen_class_leftbar = 'menu_1_helpr';
                }
                ?><li class="app-menu anchor_menu_sub" id="topmenu"> <a class="anchor_menu" href="<?php echo $url_link ?>"><i class="<?php echo $class_icon ?>">&nbsp;</i> <span class="menu_t"><?php echo $name_class ?></span> </a>
                    <div class="treeview new_menu_1_1 <?php echo $gen_class_leftbar ?>" onclick="closebottommenu();"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                        <ul class="treeview-menu popover right" id="popup_top">
                                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                            <?php if (($url != 'apps' && $url != 'app-details' && $url != 'app-settings' && $url != 'notification')) { ?>
                                <?php if (in_array('apps', $access_list)) { ?>
                                    <li> <a <?php if (isset($_REQUEST['sel_app_id'])) { ?> href="app-details?sel_app_id=<?php echo $_REQUEST['sel_app_id'] ?>" <?php } else { ?> href="apps?list=apps" <?php } ?> ><i class="menu_i apps_icon">&nbsp;</i> <span class="menu_t">Apps</span></a> </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($url != 'respond' && $url != 'respond-detail' && $feature[3] == 'running') { ?>
                                <?php if (in_array('respond', $access_list)) { ?>
                                    <li id="promotr"> <a href="respond"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span></a> </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($url != 'help-faq-list' && $url != 'help-img-video-archive' && $url != 'help-img-video' && $feature[4] == 'running') { ?>
                                <?php if (in_array('help', $access_list)) { ?>
                                    <li id="helpr"> <a <?php if (isset($_REQUEST['sel_app_id'])) { ?> href="help-img-video?sel_app_id=<?php echo $_REQUEST['sel_app_id'] ?>&sel=faq&live" <?php } else { ?> href="apps?list=help" <?php } ?> ><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help</span></a> </li>
                                <?php } ?>
                            <?php } ?>
                            <?php if (in_array('app-settings', $access_list)) { ?>
                                <li> <a href="app-settings"><i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings</span></a> </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>

            </ul>
            <ul class="sidebar-menu add_app_m all_datials_p" id="bottommenu" >
                <li class="app_li" style="border-bottom:1px solid #ebebeb !important;">
                    <div class="logo-icon logo-icon_m"><img width="60" height="60" title="" alt="" src="<?php echo $app_icon_path ?>"></div>
                    <div class="cl"></div>
                    <span class="menu_t app_menu_n padding-right-15">
                        <P>
                            <?php if ($app_type == 'ios') { ?>
                                <i class="apps_i iphone_i" title=""></i>
                            <?php } else { ?>
                                <i class="apps_i android_i" title=""></i>
                            <?php } ?>
                        </P>
                    </span> </a>
                    <div class="treeview new_menu_1_2"  onclick="closetopmenu();"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                        <?php if (count($res_popup) > 1) { ?>
                            <ul id="popup_bottom" class="treeview-menu all-apps_m popover right communicatr_all slimScrollDiv">
                                <div class="arrow"></div>
                                <div class="col-xs-12 col-md-12">
                                    <div class="col-xs-12 col-md-6 left_search">
                                        <form action="javascript:;" method="get" class="sidebar-form">
                                            <div class="input-group"> <span class="input-group-btn">
                                                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                                                </span>
                                                <input type="text" name="q" id="q" class="form-control" placeholder="Search..."/>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xs-12 col-md-6 right_apps">
                                        <div class="left_platform">
                                            <ul class="all-apps-icon analyzr_icon">
                                                <li class="all_apps button_bg <?php echo $all_class; ?>" title="All Apps">All</li>
                                                <li class="ip <?php echo $ios_class; ?>">&nbsp;</li>
                                                <li class="ad <?php echo $android_class; ?> ">&nbsp;</li>
                                            </ul>
                                            <div class="cl height10"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chat-box" class="col-xs-12 col-md-12 ap-sectoin">
                                    <div class="apps_list">
                                        <div class="leftside_listing_app">
                                            <?php if (count($res1) > 0) { ?>
                                                <div class="row rowdata">
                                                    <?php
                                                    for ($i = 0; $i < count($res1); $i++) {
                                                        if ($res1[$i]['app_logo'] != '')
                                                            $app_img_path = "memimages.php?max_width=118&max_width=118&imgfile=" . APP_LOGO . "/" . $res1[$i]['app_logo'];
                                                        else
                                                            $app_img_path = "img/no_image_available.jpg";

                                                        if ($res1[$i]['app_status'] == 'active')
                                                            $active_class = 'active_icon';
                                                        else
                                                            $active_class = 'inactive_icon';

                                                        if ($res1[$i]['app_type'] == 'ios') {
                                                            $app_type_img_path = "apps_i iphone_i";
                                                        } else {
                                                            $app_type_img_path = "apps_i android_i";
                                                        }



                                                        if ($_REQUEST['list'] == 'apps' || $url == 'app-settings') {

                                                            $site_url_redirect = 'app-details';
                                                        } else if ($_REQUEST['list'] == 'help' || $url == 'help-faq-list' || $url == 'help-img-video' || $url == 'help-img-video-archive') {
                                                            $resupdd1 = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $res1[$i]['intid'] . "' AND (record_status='running' OR record_status='prev') ");

                                                            if (count($resupdd1) > 0) {
                                                                $site_url_redirect = 'help-img-video';
                                                                $append_live = '&sel=faq&live';
                                                            } else {
                                                                $site_url_redirect = 'help-img-video';
                                                                $append_live = '&sel=faq&live';
                                                            }
                                                        }
                                                        ?>
                                                        <div class="col-xs-6 col-md-3"> <a href="<?php echo $site_url_redirect ?>?sel_app_id=<?php echo $res1[$i]['intid'] . $append_live; ?>" class="thumbnail">
                                                                <div class="logo-icon"><img src="<?php echo $app_img_path; ?> " width="110" height="110" alt="" title="" /></div>
                                                                <div class="logo-title"> <i class="<?php echo $app_type_img_path; ?>" title="">&nbsp;</i> <span class="<?php echo $active_class; ?>"></span>
                                                                    <div class="cl"></div>
                                                                    <?php echo $res1[$i]['app_name']; ?> </div>
                                                            </a> </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <div class="bk rowdata">
                                                    <div class="col-lg-12">
                                                        <div class="col-md-12 img-center"> <img src="img/no_apps_found.png" alt="" title="" /> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--  No Apps Found-->
                                        <?php } ?>
                                    </div>
                                </div>
                            </ul>
                        <?php } ?>
                    </div>
                </li>
                <li class="arrow_bottom"><i class="fa fa-fw fa-caret-down"></i></li>
            </ul>

            <div class="cl"></div>

            <?php
            if ($url == 'help-faq-list' || $url == 'help-img-video' || $url == 'help-img-video-archive'):

                //Find live and prev upgradr versions
                $resup1 = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $sel_app_id . "' AND record_status='old' ");

                $resup = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $sel_app_id . "' AND (record_status='running' OR record_status='prev') ");
                for ($i = 0; $i < count($resup); $i++) {
                    switch ($resup[$i]['record_status']) {
                        case 'running':
                            $version['live'] = $resup[$i]['version'];
                            break;
                        case 'prev':
                            $version['prev'] = $resup[$i]['version'];
                            break;
                        default:
                            break;
                    }
                }
                ?>
                <ul class="sidebar-menu">
                    <li>
                    <dd class="plus_icon fl" style="width:220px"><a data-dialog="somedialog" class="trigger" href="javascript:;"><img alt="" src="img/plus_icon.png"></a></dd>
                    <?php
                    if ($url == 'help-img-video-archive') {

                        $chk = $dclass->select("w.*,a.app_type,a.app_name,a.app_logo,f.feature_status, f.intid as id", "tblmember_apps a INNER JOIN tblapp_whatsnew w ON a.intid=w.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id", " AND a.intid='" . $sel_app_id . "'  AND a.app_status='active' AND f.feature_id='5' AND w.record_status='old' ORDER BY w.record_status DESC  LIMIT 0,2");
                        if (!empty($chk)) {
                            ?>
                            <div class="cl height1"></div>
                            <form style="width:183px;" action="javascript:;" method="get" class="sidebar-form">
                                <div class="input-group"> <span class="input-group-btn agents_ser">
                                        <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                                    </span>
                                    <input class="form-control" type="Search" name="keyword" id="keyword" placeholder="Search" onkeyup="search(this.value, 'tblapp_tutorial_settings', ['s.version', 'v.video_name'], 0, 5, '', '')">
                                </div>
                            </form>
                            <?php
                        }
                    }
                    ?>
                    </li>
                </ul>
                <ul class="sidebar-menu">
                    <li class="treeview app-menu" style="border-bottom:1px solid #ebebeb !important;">&nbsp;</li>
                    <li class="arrow_bottom-aap" ><i class="fa fa-fw fa-caret-down"></i></li>
                </ul>
                <ul class="plan-menu">
                    <li class="">
                        <?php if (count($resup) <= 0) { ?>
                            <a <?php if ($url == 'help-img-video-archive') { ?> class="active" <?php } ?> class="" href="help-img-video-archive?sel_app_id=<?php echo $sel_app_id; ?>"><span  class="menu_t">Video & Images <i class="fa fa-fw fa-angle-right"></i></span> </a>
                        <?php } else { ?>
                            <a  class="" href="help-img-video?sel_app_id=<?php echo $sel_app_id; ?>&sel=faq&live"><span  class="menu_t">Video & Images <i class="fa fa-fw fa-angle-right"></i></span> </a>
                        <?php } ?>
                        <ul class="inv-m">
                            <?php if ($version['live']) { ?>
                                <li><a <?php if (isset($_GET['live'])) { ?> class="active" <?php } ?> href="help-img-video?sel_app_id=<?php echo $sel_app_id; ?>&sel=faq&live"><?php echo $version['live']; ?> (Live)</a></li>
                            <?php } ?>
                            <?php if ($version['prev']) { ?>
                                <li><a <?php if (isset($_GET['prev'])) { ?> class="active" <?php } ?> href="help-img-video?sel_app_id=<?php echo $sel_app_id; ?>&sel=faq&prev"><?php echo $version['prev']; ?> (Previous)</a></li>
                            <?php } ?>
                            <?php if (count($resup1) > 0) { ?>
                                <li><a <?php if ($url == 'help-img-video-archive') { ?> class="active" <?php } ?> href="help-img-video-archive?sel_app_id=<?php echo $sel_app_id; ?>">Archive</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li <?php if ($url == 'help-faq-list') { ?> class="active" <?php } ?>>        
                </ul>
            <?php endif; ?>
            <?php
            if ($url == 'app-details' || $url == 'notification' || ($url == 'app-settings' && !empty($append))) {
                if ($url == 'app-settings') {
                    $class_app_set = 'fa-angle-right';
                    $active_set = 'active';
                } else if ($url == 'notification') {
                    $class_app_not = 'fa-angle-right';
                    $active_not = 'active';
                } else if ($url == 'app-details') {
                    $class_app_det = 'fa-angle-right';
                    $active_det = 'active';
                }


                $res_feature_individual1 = $dclass->select("intid, feature_status, feature_id", "tblmember_app_features", " AND member_id='" . $member_id . "' AND app_id = '" . $_REQUEST['sel_app_id'] . "' ");

                foreach ($res_feature_individual1 as $val_feature1) {

                    if ($val_feature1['feature_id'] == 3 && $val_feature1['feature_status'] == 'pause') {
                        $style_data = 'style = \'display:none\'';
                    }
                }
                ?>
                <ul class="inner_sub">
                    <li class=""><a class="<?php echo $active_det ?>" href="app-details<?php echo $append; ?>"><span class="menu_t">About <i class="fa fa-fw <?php echo $class_app_det ?>"></i></span> </a></li>
                    <li class=""><a class="<?php echo $active_set ?>" href="app-settings<?php echo $append; ?>"><span class="menu_t">Settings <i class="fa fa-fw <?php echo $class_app_set ?>"></i></span> </a></li>
                    <li id="noti_show_hide" class="" <?php echo $style_data ?>><a class="<?php echo $active_not ?>" href="notification<?php echo $append; ?>"><span class="menu_t" >Notification <i class="fa fa-fw <?php echo $class_app_not ?>"></i></span> </a></li>
                </ul>
            <?php }
            ?>
        <?php } ?>
        <?php
        if ($url == 'respond' || $url == 'respond-detail') {
            ?>
            <ul class="sidebar-menu comm">
                <li id="hide_communicatr" class="app-menu anchor_menu_sub" <?php if ($url == 'respond') { ?> style="border-bottom:1px solid #ebebeb !important;" <?php } ?>> <a class="anchor_menu" href="respond"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span> </a>
                    <div class="treeview new_menu_6"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                        <ul class="treeview-menu popover right" id="popup_top">
                                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                            <?php if (in_array('apps', $access_list)) { ?>
                                <li> <a href="apps?list=apps"><i class="menu_i apps_icon">&nbsp;</i> <span class="menu_t">Apps</span></a> </li>
                            <?php } ?>


                            <?php if ($feature[4] == 'running') { ?>
                                <?php if (in_array('help', $access_list)) { ?>
                                    <li> <a href="apps?list=help"><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help</span></a> </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if (in_array('app-settings', $access_list)) { ?>
                                <li> <a href="app-settings"><i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings</span></a> </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
                <?php if ($url == 'respond') { ?>
                    <li class="arrow_bottom"><i class="fa fa-fw fa-caret-down"></i></li>
                <?php } ?>
            </ul>
            <?php
            if (isset($_REQUEST['support_id'])) {
                ?>
                <ul class="sidebar-menu add_app_m all_datials_p">
                    <li class="app_li" style="border-bottom:1px solid #ebebeb !important;">
                        <div class="logo-icon logo-icon_m"><img width="60" height="60" title="" alt="" src="<?php echo $app_icon_path ?>"></div>
                        <div class="cl"></div>
                        <span class="menu_t app_menu_n padding-right-15">
                            <p>
                                <?php if ($app_type == 'ios') { ?>
                                    <i class="apps_i iphone_i" title=""></i>
                                <?php } else { ?>
                                    <i class="apps_i android_i" title=""></i>
                                <?php } ?>
                            </p>
                        </span>
                        <div class="treeview new_menu_1_2" id="hide_app_communicatr"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                            <ul class="treeview-menu all-apps_m popover right communicatr_all" id="popup_bottom">
                                <div class="arrow"></div>
                                <div class="col-xs-12 col-md-12">
                                    <div class="col-xs-12 col-md-6 left_search">
                                        <form action="#" method="get" class="sidebar-form">
                                            <div class="input-group"> <span class="input-group-btn">
                                                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                                                </span>
                                                <input type="text" name="q" id="q" class="form-control" placeholder="Search..."/>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xs-12 col-md-6 right_apps">
                                        <div class="left_platform">
                                            <ul class="all-apps-icon">
                                                <li class="all_apps button_bg active" title="All Apps">All</li>
                                                <li class="ip">&nbsp;</li>
                                                <li class="ad">&nbsp;</li>
                                            </ul>
                                            <div class="cl height10"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chat-box" class="col-xs-12 col-md-12 ap-sectoin">
                                    <div class="apps_list ">
                                        <div class="leftside_listing_app">
                                            <?php if (count($res1) > 0) { ?>
                                                <div class="row rowdata">
                                                    <?php
                                                    for ($i = 0; $i < count($res1); $i++) {
                                                        if ($res1[$i]['app_logo'] != '')
                                                            $app_img_path = "memimages.php?max_width=118&max_width=118&imgfile=" . APP_LOGO . "/" . $res1[$i]['app_logo'];
                                                        else
                                                            $app_img_path = "img/no_image_available.jpg";

                                                        if ($res1[$i]['app_status'] == 'active')
                                                            $active_class = 'active_icon';
                                                        else
                                                            $active_class = 'inactive_icon';

                                                        if ($res1[$i]['app_type'] == 'ios') {
                                                            $app_type_img_path = "apps_i iphone_i";
                                                        } else {
                                                            $app_type_img_path = "apps_i android_i";
                                                        }



                                                        if ($_REQUEST['list'] == 'apps' || $url == 'app-settings') {

                                                            $site_url_redirect = 'app-details';
                                                        } else if ($url == 'respond') {
                                                            $site_url_redirect = 'respond';
                                                        } else if ($url == 'respond-detail') {
                                                            $site_url_redirect = 'respond-detail';
                                                        }
                                                        ?>
                                                        <div class="col-xs-6 col-md-3"> <a href="<?php echo $site_url_redirect ?>?support_id=<?php echo $res1[$i]['support_id'] ?>&sel_app_id=<?php echo $res1[$i]['intid']; ?>" class="thumbnail">
                                                                <div class="logo-icon"><img src="<?php echo $app_img_path; ?> " width="110" height="110" alt="" title="" /></div>
                                                                <div class="logo-title"> <i class="<?php echo $app_type_img_path; ?>" title="">&nbsp;</i> <span class="<?php echo $active_class; ?>"></span>
                                                                    <div class="cl"></div>
                                                                    <?php echo $res1[$i]['app_name']; ?> </div>
                                                            </a> </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <div class="bk rowdata">
                                                    <div class="col-lg-12">
                                                        <div class="col-md-12 img-center"> <img src="img/no_apps_found.png" alt="" title="" /> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--  No Apps Found-->
                                        <?php } ?>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </li>
                    <li class="arrow_bottom"><i class="fa fa-fw fa-caret-down"></i></li>
                </ul>
                <div class="cl"></div>
                <?php if ($url == 'respond-detail') { ?>
                    <ul>
                        <li>
                            <form name="submitselapp" id="submitselapp" method="post" action="respond">
                                <input id="sel_app_id" name="sel_app_id" type="hidden" value="<?php echo $_REQUEST['sel_app_id'] ?>"/>
                                <input type="submit" class="btn btn-warning"  value="All Requests">
                            </form>
                    </ul>
                <?php } ?>
                <?php
                if ($member_id == $_SESSION['agents_cust_id']) {
                    if (!empty($_SESSION['support_agents'])) {
                        $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['support_agents'] . '\'';
                    } else {
                        $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
                    }
                } else {
                    if ($_SESSION['role'] == 'admin') {
                        $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
                    } else {
                        $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
                    }
                }

                $support_id = $_REQUEST['support_id'];
                $prevresult = $dclass->select("intid,app_id", "tblapp_support", " AND intid < " . $support_id . " AND request_id = 0 " . $append_data . " ORDER BY intid DESC LIMIT 1 ");
                ;

                $nextresult = $dclass->select("intid,app_id", "tblapp_support", " AND intid > " . $support_id . " AND request_id = 0 " . $append_data . " ORDER BY intid ASC LIMIT 1 ");
                ?>
                <div class="slider_m">
                    <?php if (!empty($prevresult)) { ?>
                        <a href="respond-detail?support_id=<?php echo $prevresult[0]['intid'] ?>&sel_app_id=<?php echo $prevresult[0]['app_id'] ?>"><i class="fa fa-fw fa-angle-left"></i></a>
                    <?php } ?>
                    Request# <span class="request_class"><?php echo $support_id ?> </span>
                    <?php if (!empty($nextresult)) { ?>
                        <a href="respond-detail?support_id=<?php echo $nextresult[0]['intid'] ?>&sel_app_id=<?php echo $nextresult[0]['app_id'] ?>"><i class="fa fa-fw fa-angle-right"></i></a>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="communicatr_m"> <a href="javascript:;" onclick="clear_all_data();" class="btn btn-default btn-flat">Clear All</a>
                    <ul class="left_apps_menu" id="append_apps_detail">
                        <li class="all_ap treeview"><a href="javascript:;" class="all_apps_search"><span class="menu_t">Apps </span> <i class="fa fa-fw fa-angle-down"></i></a>
                            <ul class="treeview-menu all-apps_m popover right communicatr_all">
                                            <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                                <div class="col-xs-12 col-md-12">
                                    <div class="col-xs-12 col-md-6 left_search">
                                        <form action="javascript:;" method="get" class="sidebar-form">
                                            <div class="input-group"> <span class="input-group-btn">
                                                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                                                </span>
                                                <input type="text" name="q" id="q" class="form-control" placeholder="Search..."/>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xs-12 col-md-6 right_apps">
                                        <div class="left_platform">
                                            <ul class="all-apps-icon">

                                                <li class="all_apps button_bg <?php if (empty($_SESSION['app_type'])): ?> active <?php endif; ?>"  title="All Apps">All</li>

                                                <li class="ip button_bg<?php if ($_SESSION['app_type'] == 'ios'): ?> active<?php endif; ?>">&nbsp;</li>

                                                <li class="ad button_bg<?php if ($_SESSION['app_type'] == 'android'): ?> active<?php endif; ?>" >&nbsp;</li>

                                            </ul>
                                            <div class="cl height10"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chat-box" class="col-xs-12 col-md-12 ap-sectoin">
                                    <div class="apps_list ">
                                        <div class="leftside_listing_app">
                                            <?php if (count($res1) > 0) { ?>
                                                <input type="hidden" id="total_app_count_data" value="<?php echo count($res1) ?>">
                                                <div class="row rowdata">
                                                    <input type="hidden" id="app_count_data" value="<?php echo count($res1) ?>">
                                                    <?php
                                                    for ($i = 0; $i < count($res1); $i++) {
                                                        if ($res1[$i]['app_logo'] != '')
                                                            $app_img_path = "memimages.php?max_width=118&max_width=118&imgfile=" . APP_LOGO . "/" . $res1[$i]['app_logo'];
                                                        else
                                                            $app_img_path = "img/no_image_available.jpg";

                                                        if ($res1[$i]['app_status'] == 'active')
                                                            $active_class = 'active_icon';
                                                        else
                                                            $active_class = 'inactive_icon';

                                                        if ($res1[$i]['app_type'] == 'ios') {
                                                            $app_type_img_path = "apps_i iphone_i";
                                                        } else {
                                                            $app_type_img_path = "apps_i android_i";
                                                        }



                                                        if ($_REQUEST['list'] == 'apps' || $url == 'app-settings') {

                                                            $site_url_redirect = 'app-details';
                                                        } else if ($url == 'respond') {
                                                            $site_url_redirect = 'respond';
                                                        } else if ($url == 'respond-detail') {
                                                            $site_url_redirect = 'respond-detail';
                                                        }

                                                        if ($url == 'respond') {
                                                            ?>
                                                            <div onclick="hide_app('<?php echo $res1[$i]['intid']; ?>')" id="<?php echo $res1[$i]['intid']; ?>" class="col-xs-6 col-md-3"> <a href="javascript:;" class="thumbnail">
                                                                    <div class="logo-icon"><img src="<?php echo $app_img_path; ?> " width="110" height="110" alt="" title="" /></div>
                                                                    <div class="logo-title"> <i class="<?php echo $app_type_img_path; ?>" title="">&nbsp;</i> <span class="<?php echo $active_class; ?>"></span>
                                                                        <div class="cl"></div>
                                                                        <?php echo $res1[$i]['app_name']; ?> </div>
                                                                </a> </div>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <div class="col-xs-6 col-md-3"> <a href="<?php echo $site_url_redirect ?>?support_id=<?php echo $res1[$i]['support_id'] ?>&sel_app_id=<?php echo $res1[$i]['intid']; ?>" class="thumbnail">
                                                                    <div class="logo-icon"><img src="<?php echo $app_img_path; ?> " width="110" height="110" alt="" title="" /></div>
                                                                    <div class="logo-title"> <i class="<?php echo $app_type_img_path; ?>" title="">&nbsp;</i> <span class="<?php echo $active_class; ?>"></span>
                                                                        <div class="cl"></div>
                                                                        <?php echo $res1[$i]['app_name']; ?> </div>
                                                                </a> </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            <?php } else { ?>
                                                <div class="bk rowdata">
                                                    <input type="hidden" id="app_count_data" value="0">
                                                    <div class="col-lg-12">
                                                        <div class="col-md-12 img-center"> <img src="img/no_apps_found.png" alt="" title="" /> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--  No Apps Found-->
                                        <?php } ?>
                                    </div>
                                </div>
                            </ul>
                        </li>
                        <?php
                        foreach ($_SESSION['app_id'] as $get_app_id):
                            $res_app_id = $dclass->select("tblmember_apps.*", "tblmember_apps", " AND tblmember_apps.intid = '" . $get_app_id . "' ");                            //print_r($res_app_id);
                            if ($res_app_id[0]['app_type'] == 'ios') {
                                $class_apply = 'iphone_i';
                            } else {
                                $class_apply = 'android_i';
                            }
                            ?>
                            <li class="app_sat" id="left_<?php echo $res_app_id[0]['intid'] ?>"><i class="apps_i <?php echo $class_apply ?>" title=""></i> <span class="menu_t"><?php echo $res_app_id[0]['app_name'] ?></span> <a href="javascript:;"  onclick="remove_app('<?php echo $res_app_id[0]['intid'] ?>', 'refresh', '<?php echo $app_type ?>')"><i class="apps_i fa close_i"></i></a></li>
                            <?php
                        endforeach;
                        ?>

                    </ul>
                    <ul class="left_apps_menu" id="status_close">
                        <li class="status all_ap treeview"  ><a href="javascript:;"><span <?php if (isset($_SESSION['show_status'])) { ?> style="color:#00CCCC !important" <?php } ?> class="menu_t">Show </span> <i class="fa fa-fw fa-angle-down" <?php if (isset($_SESSION['show_status'])) { ?> style="color:#00CCCC !important" <?php } ?> ></i></a>
                            <ul class="treeview-menu all-apps_m popover right communicatr_all">
                                            <div class="arrow" ><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                                <ul class="status-ul append_status_list" id="show_status_list">
                                    <?php if (count($_SESSION['show_status']) == 4) { ?>
                                        <li class="show_all_data1" id="Remove_show_status" onclick="remove_swap_text('Remove', this.id, 'show_status')"><a href="javascript:;">Remove All</a></li>
                                    <?php } else { ?>
                                        <li class="show_all_data1" id="All_show_status" onclick="swap_text('All', this.id, 'show_status')"><a href="javascript:;">All</a></li>
                                        <?php if ($_SESSION['show_status']['Open'] != 'Open') { ?>
                                            <li class="show_all_data1" id="Open_show_status" onclick="swap_text('Open', this.id, 'show_status')"><a href="javascript:;">Open</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['show_status']['Close'] != 'Close') { ?>
                                            <li class="show_all_data1" id="Close_show_status" onclick="swap_text('Close', this.id, 'show_status')"><a href="javascript:;">Close</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['show_status']['Archive'] != 'Archive') { ?>   
                                            <li class="show_all_data1" id="Archive_show_status" onclick="swap_text('Archive', this.id, 'show_status')"><a href="javascript:;">Archive</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['show_status']['Test'] != 'Test') { ?>   
                                            <li class="show_all_data1" id="Test_show_status" onclick="swap_text('Test', this.id, 'show_status')"><a href="javascript:;">Test</a></li>
                                        <?php } ?>      

                                    <?php } ?>
                                </ul>
                                <div class="cl"></div>
                            </ul>
                            <ul id="show_status">
                                <?php
                                if (isset($_SESSION['show_status'])) {
                                    foreach ($_SESSION['show_status'] as $val_show_status) {
                                        ?>
                                        <li><span class="menu_t"><?php echo $val_show_status ?></span><input type="hidden" id="<?php echo $val_show_status ?>_show_status" value="<?php echo $val_show_status ?>"><i class="fa"><a href="javascript:;" onclick="remove_swap_text('<?php echo $val_show_status ?>', '<?php echo $val_show_status ?>_show_status', 'show_status')"><i class="apps_i fa close_i"></i></a></i></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                    <ul class="left_apps_menu" id="type_close">
                        <li class="status all_ap treeview"><a href="javascript:;"><span <?php if (isset($_SESSION['show_type'])) { ?> style="color:#00CCCC" <?php } ?> class="menu_t">Type </span> <i class="fa fa-fw fa-angle-down" <?php if (isset($_SESSION['show_type'])) { ?> style="color:#00CCCC !important" <?php } ?>></i></a>
                            <ul class="treeview-menu all-apps_m popover right communicatr_all">
                                            <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                                <ul class="status-ul" id="show_type_list">
                                    <?php if (count($_SESSION['show_type']) == 3) { ?>
                                        <li class="show_all_data1" id="Remove_show_type" onclick="remove_swap_text('Remove', this.id, 'show_type')"><a href="javascript:;">Remove All</a></li>
                                    <?php } else { ?>
                                        <li class="show_all_data1" id="All_show_type" onclick="swap_text('All', this.id, 'show_type')"><a href="javascript:;">All</a></li>
                                        <?php if ($_SESSION['show_type']['Query'] != 'Query') { ?>
                                            <li class="show_all_data1" id="Query_show_type"  onclick="swap_text('Query', this.id, 'show_type')"><a href="javascript:;"  >Query</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['show_type']['Bug'] != 'Bug') { ?>
                                            <li class="show_all_data1" id="Bug_show_type"  onclick="swap_text('Bug', this.id, 'show_type')"><a href="javascript:;" >Bug</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['show_type']['Feedback'] != 'Feedback') { ?>
                                            <li class="show_all_data1" id="Feedback_show_type"  onclick="swap_text('Feedback', this.id, 'show_type')"><a href="javascript:;" >Feedback</a></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                                <div class="cl"></div>
                            </ul>
                            <ul id="show_type">
                                <?php
                                if (isset($_SESSION['show_type'])) {

                                    foreach ($_SESSION['show_type'] as $val_show_type) {
                                        ?>
                                        <li><span class="menu_t"><?php echo $val_show_type ?></span><input type="hidden" id="<?php echo $val_show_type ?>_show_type" value="<?php echo $val_show_type ?>"><i class="fa"><a href="javascript:;" onclick="remove_swap_text('<?php echo $val_show_type ?>', '<?php echo $val_show_type ?>_show_type', 'show_type')"><i class="apps_i fa close_i"></i></a></i></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                    <ul class="left_apps_menu" id="os_ver_close">
                        <li class="status all_ap <?php if (count($get_distinct_os_version) > 1) { ?>treeview<?php } ?>"><a href="javascript:;"><span <?php if (isset($_SESSION['show_os_ver'])) { ?> style="color:#00CCCC" <?php } ?> class="menu_t">OS Ver </span> <i class="fa fa-fw fa-angle-down" <?php if (isset($_SESSION['show_os_ver'])) { ?> style="color:#00CCCC !important" <?php } ?>></i></a>
                            <ul class="<?php if (count($get_distinct_os_version) > 1) { ?> treeview-menu <?php } ?> all-apps_m  popover  right communicatr_all">
                                <div class="arrow"></div>
                                <?php if (count($get_distinct_os_version) > 0) { ?>
                                    <ul class="status-ul" id="show_os_ver_list">
                                        <?php if (count($_SESSION['show_os_ver']) == count($get_distinct_os_version)) { ?>
                                            <li class="show_all_data1" id="Remove_show_os_ver" onclick="remove_swap_text('Remove', this.id, 'show_os_ver')"><a href="javascript:;">Remove All</a></li>
                                        <?php } else { ?>
                                            <li class="show_all_data1" id="All_show_os_ver" onclick="swap_text('All', this.id, 'show_os_ver')"><a href="javascript:;">All</a></li>
                                            <?php
                                            $i = 0;

                                            foreach ($get_distinct_os_version as $os_ver_val):

                                                if ($_SESSION['show_os_ver'][$os_ver_val['version']] != $os_ver_val['version']) {
                                                    ?>
                                                    <li class="show_all_data1" id="<?php echo $os_ver_val['version'] ?>_show_os_ver" onclick="swap_text('<?php echo $os_ver_val['version'] ?>', this.id, 'show_os_ver')"><a href="javascript:;"><?php echo $os_ver_val['version'] ?></a></li>
                                                    <?php
                                                }
                                                $i++;

                                            endforeach;
                                        }
                                        ?>
                                    </ul>
                                <?php } ?>
                                <div class="cl"></div>
                            </ul>
                            <ul id="show_os_ver">
                                <?php
                                if (isset($_SESSION['show_os_ver'])) {
                                    foreach ($_SESSION['show_os_ver'] as $val_show_os_ver) {
                                        ?>
                                        <li><span class="menu_t"><?php echo $val_show_os_ver ?></span><input type="hidden" id="<?php echo $val_show_os_ver ?>_show_os_ver" value="<?php echo $val_show_os_ver ?>"><i class="fa"><a href="javascript:;" onclick="remove_swap_text('<?php echo $val_show_os_ver ?>', '<?php echo $val_show_os_ver ?>_show_os_ver', 'show_os_ver')"><i class="apps_i fa close_i"></i></a></i></li>
                                        <?php
                                    }
                                }
                                ?>
                                <?php if (count($get_distinct_os_version) == 1) { ?>
                                    <li><span class="menu_t"><?php echo $get_distinct_os_version[0]['version'] ?></span></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>  
                    <ul class="left_apps_menu" id="app_ver_close">
                        <li class="status all_ap <?php if (count($get_distinct_app_version) > 1) { ?>treeview<?php } ?>"><a href="javascript:;"><span <?php if (isset($_SESSION['show_app_ver'])) { ?> style="color:#00CCCC" <?php } ?> class="menu_t">App Ver </span> <i class="fa fa-fw fa-angle-down" <?php if (isset($_SESSION['show_app_ver'])) { ?> style="color:#00CCCC !important" <?php } ?>></i></a>
                            <ul class="<?php if (count($get_distinct_app_version) > 1) { ?> treeview-menu <?php } ?> all-apps_m popover right communicatr_all">
                                <div class="arrow"></div>
                                <?php if (count($get_distinct_app_version) > 0) { ?>
                                    <ul class="status-ul" id="show_app_ver_list">
                                        <?php if (count($_SESSION['show_app_ver']) == count($get_distinct_app_version)) { ?>
                                            <li class="show_all_data1" id="Remove_show_app_ver" onclick="remove_swap_text('Remove', this.id, 'show_app_ver')"><a href="javascript:;">Remove All</a></li>
                                        <?php } else {
                                            ?>
                                            <li class="show_all_data1" id="All_show_app_ver" onclick="swap_text('All', this.id, 'show_app_ver')"><a style="color:#E1E1E1 !important" href="javascript:;">All</a></li>
                                            <?php
                                            $i = 0;
                                            foreach ($get_distinct_app_version as $app_ver_val):

                                                if ($_SESSION['show_app_ver'][$app_ver_val['app_version']] != $app_ver_val['app_version']) {
                                                    foreach ($get_publish_date as $publish_app_ver_val):
                                                        if ($publish_app_ver_val['version'] == $app_ver_val['app_version']) {
                                                            if ($publish_app_ver_val['vcount'] == 2) {
                                                                $val_pub_date = '';
                                                            } else {
                                                                $val_pub_date = date('M, d Y', strtotime($publish_app_ver_val['start_date']));
                                                            }
                                                        }
                                                    endforeach;
                                                    ?>
                                                    <?php
                                                    ?>
                                                    <li class="show_all_data1" id="<?php echo $app_ver_val['app_version'] ?>_show_app_ver" onclick="swap_text('<?php echo $app_ver_val['app_version'] ?>', this.id, 'show_app_ver')"><a style="color:#E1E1E1 !important" href="javascript:;"><?php echo $app_ver_val['app_version']; ?></a></li><span id="<?php echo $app_ver_val['app_version'] ?>_publish_date" class="show_all_data2_"><small><?php $val_pub_date; ?></small></span> <?php ?>


                                                    <?php
                                                }
                                                $i++;

                                            endforeach;
                                        }
                                        ?>
                                    </ul>
                                    <div class="cl"></div>
                                </ul>
                            <?php } ?>
                            <ul id="show_app_ver">
                                <?php
                                if (isset($_SESSION['show_app_ver'])) {
                                    foreach ($_SESSION['show_app_ver'] as $val_show_app_ver) {
                                        foreach ($get_publish_date as $app_ver_val):
                                            if ($app_ver_val['version'] == $val_show_app_ver) {
                                                ?>
                                                <li><span class="menu_t"><?php echo $val_show_app_ver ?></span><input type="hidden" id="<?php echo $val_show_app_ver ?>_show_app_ver" value="<?php echo $val_show_app_ver ?>"><i class="fa"><a href="javascript:;" onclick="remove_swap_text('<?php echo $val_show_app_ver ?>', '<?php echo $val_show_app_ver ?>_show_app_ver', 'show_app_ver')"><i class="apps_i fa close_i"></i></a></i></li><span id="<?php echo $val_show_app_ver ?>_publish_date_selected" class="show_all_data2_"><small><?php
                                                        if ($app_ver_val['vcount'] == 1) {

                                                            echo date('M, d Y', strtotime($app_ver_val['start_date']));
                                                            ?>
                                                        <?php }
                                                        ?>
                                                    </small></span>
                                                <?php
                                            }
                                        endforeach;
                                    }
                                }
                                ?>

                                <?php if (count($get_distinct_app_version) == 1) { ?>
                                    <li><span class="menu_t"><?php echo $get_distinct_app_version[0]['app_version'] ?></span></li>
                                <?php } ?>         
                            </ul>
                        </li>
                    </ul>
                    <?php if ($_SESSION['custid'] == $_SESSION['agents_cust_id']) { ?>
                        <?php if (count($support_agents) > 0) { ?>
                            <ul class="left_apps_menu" id="agent_close">
                                <li class="status all_ap <?php if (count($support_agents) > 1) { ?>treeview<?php } ?>"><a href="javascript:;"><span <?php if (!empty($_SESSION['show_agent'])) { ?> style="color:#00CCCC" <?php } ?> class="menu_t">Agents </span> <i class="fa fa-fw fa-angle-down" <?php if (!empty($_SESSION['show_agent'])) { ?> style="color:#00CCCC !important" <?php } ?>></i></a>
                                    <ul class="<?php if (count($support_agents) > 1) { ?> treeview-menu <?php } ?> all-apps_m popover right communicatr_all">
                                        <div class="arrow"></div>
                                        <?php if (count($support_agents) > 0) { ?>
                                            <ul class="status-ul" id="show_agent_list">
                                                <?php if (count($_SESSION['show_agent']) == count($support_agents)) { ?>
                                                    <li class="show_all_data1" id="Remove_show_agent" onclick="remove_swap_text('Remove', this.id, 'show_agent')"><a href="javascript:;">Remove All</a></li>
                                                <?php } else { ?>
                                                    <li class="show_all_data1" id="All_show_agent" onclick="swap_text('All', this.id, 'show_agent')"><a href="javascript:;">All</a></li>
                                                    <?php
                                                    $i = 0;
                                                    foreach ($support_agents as $agent_val):

                                                        if ($_SESSION['show_agent'][$agent_val['intid']] != $agent_val['intid']) {
                                                            ?>
                                                            <li class="show_all_data1"  id="<?php echo $agent_val['intid'] . '@~' . $agent_val['fname'] . ' ' . $agent_val['lname'] ?>_show_agent" onclick="swap_text('<?php echo $agent_val['intid'] . '@~' . $agent_val['fname'] . ' ' . $agent_val['lname'] ?>', this.id, 'show_agent')"><a href="javascript:;"><?php echo $agent_val['fname'] . ' ' . $agent_val['lname'] ?></a></li>
                                                            <?php
                                                        }
                                                        $i++;

                                                    endforeach;
                                                }
                                                ?>
                                            </ul>
                                            <div class="cl"></div>
                                        </ul>
                                    <?php } ?>
                                    <ul id="show_agent">
                                        <?php
                                        if (!empty($_SESSION['show_agent'])) {
                                            foreach ($_SESSION['show_agent'] as $val_show_agent) {

                                                $support_agents_detail = $dclass->select("*", "tblmember", " AND intid = '" . $val_show_agent . "' AND role = 'support' AND status = 'active' ");
                                                ?>
                                                <li><span class="menu_t"><?php echo $support_agents_detail[0]['fname'] . ' ' . $support_agents_detail[0]['lname'] ?></span><input name="show_agent[]" type="hidden" id="<?php echo $val_show_agent ?>_show_agent" value="<?php echo $val_show_agent ?>"><i class="fa"><a href="javascript:;" onclick="remove_swap_text('<?php echo $val_show_agent ?>', '<?php echo $val_show_agent ?>_show_agent', 'show_agent')"><i class="apps_i fa close_i"></i></a></i></li>

                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php if (count($support_agents) == 1) { ?>
                                            <li><span class="menu_t"><?php echo $support_agents[0]['fname'] . ' ' . $support_agents[0]['lname'] ?></span></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>
        <?php if ($url == 'invoice' || $url == 'upgrade-plan' || $url == 'agents' || $url == 'profile' || $url == 'rights-management' || $url == 'payment-method' || $url == 'access-log' || ($url == 'app-settings' && empty($append))) {
            ?>
            <ul class="sidebar-menu plan_ul">
                <li id="allappss" class="app-menu anchor_menu_sub" style="border-bottom:1px solid #ebebeb !important;"> <a class="anchor_menu" href="<?php $url ?>"><i class="menu_i settings_icon">&nbsp;</i> <span class="menu_t">Settings</span> </a>
                    <div class="treeview new_menu_7"> <a href="javascript:;" class="add__"><span class="menu_t new_i"><i class="fa fa-fw fa-angle-down"></i></span> </a>
                        <ul class="<?php if ($_SESSION['role'] != 'finance') { ?>treeview-menu <?php } ?> popover right">
                            <div class="arrow"></div>
                            <?php if (in_array('apps', $access_list)) { ?>

                            <?php } ?>

                            <?php if ($feature[3] == 'running') { ?>
                                <?php if (in_array('respond', $access_list)) { ?>
                                    <li id="communicatr"> <a href="respond"><i class="menu_i communicatr_icon">&nbsp;</i> <span class="menu_t">Respond</span></a> </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($feature[4] == 'running') { ?>
                                <?php if (in_array('help', $access_list)) { ?>
                                    <li id="helpr"> <a href="apps?list=help"><i class="menu_i helpr_icon">&nbsp;</i> <span class="menu_t">Help</span></a> </li>
                                <?php } ?>
                            <?php } ?>


                        </ul>
                    </div>
                </li>
                <li class="arrow_bottom-aap"><i class="fa fa-fw fa-caret-down"></i></li>
            </ul>
            <ul class="plan-menu">
                <?php if ($url == 'invoice') { ?>
                    <form style="width:183px;" action="javascript:;" method="get" class="sidebar-form">
                        <div class="input-group"> <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                            </span>
                            <input class="form-control" type="Search" name="keyword" id="keyword" placeholder="Search" onkeyup="search(this.value, 'tbltransactions', ['invoice_id', 'p.pname'], 0, 10, '', '')">
                        </div>
                    </form>

                <?php } ?>
                <?php if ((($url == 'agents') && ($_REQUEST['script'] != 'edit'))) { ?>
                    <dd class="plus_icon fl" style="width:220px"><a data-dialog="somedialog" class="trigger" href="javascript:;"><img alt="" src="img/plus_icon.png"></a></dd>
                    <div class="cl height1"></div>
                    <form style="width:183px;" action="javascript:;" method="get" class="sidebar-form">
                        <div class="input-group"> <span class="input-group-btn agents_ser">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="apps_i fa-search_i"></i></button>
                            </span>
                            <input class="form-control" type="Search" name="keyword" id="keyword" placeholder="Search" onkeyup="search(this.value, 'tblmember', ['fname', 'lname', 'email', 'company'], 0, 10, '', '')">
                        </div>
                    </form>
                <?php } ?>
                <?php if (in_array('profile', $access_list)) { ?>
                    <li><a <?php if ($url == 'profile') { ?> class="active" <?php } ?> href="profile"><span class="menu_t">Profile <i class="fa fa-fw <?php if ($url == 'profile') { ?> fa-angle-right <?php } ?>"></i></span> </a></li>
                <?php } ?>
                <?php if (in_array('apps', $access_list)) { ?>
                    <li> <a href="apps?list=apps"><span class="menu_t">Apps <i class="fa fa-fw "></i></span></a> </li>
                <?php } ?>
                <?php if (in_array('agents', $access_list)) { ?>
                    <li><a <?php if ($url == 'agents') { ?> class="active" <?php } ?> href="agents"><span class="menu_t agents_m">Agents <i class="fa fa-fw <?php if ($url == 'agents' || $url == 'rights-management' || $url == 'access-log') { ?> fa-angle-right <?php } ?>"></i></span></a>
                        <ul class="inv-m">
                            <?php if (in_array('rights-management', $access_list)) { ?>
                                <li><a <?php if ($url == 'rights-management') { ?> class="active" <?php } ?> href="rights-management">Access Level</a></li>
                            <?php } ?>
                            <?php if (in_array('access-log', $access_list)) { ?>
                                <li><a <?php if ($url == 'access-log') { ?> class="active" <?php } ?> href="access-log">Activity log</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (in_array('app-settings', $access_list)) { ?>
                    <li class=""><a href="app-settings" <?php if ($url == 'app-settings') { ?> class="active" <?php } ?>><span class="menu_t">General Settings <i class="fa fa-fw <?php if ($url == 'app-settings') { ?> fa-angle-right <?php } ?>"></i></span> </a></li>
                <?php } ?>
                <?php if (in_array('invoice', $access_list)) { ?>

                <?php } ?>
            </ul>
        <?php } ?>
        <?php if ($url == 'documentation') { ?>
            <div class="document-left fl">
                <div class="col-md-12 padding0">
                    <div class="doc_left">
                        <h3>Topics</h3>
                        <nav id="navigation-menu">
                            <ul>
                                <li><a href="#section-1"><i class="fa fa-fw fa-dot-circle-o"></i>Getting Started</a>
                                <li><a href="#section-2"><i class="fa fa-fw fa-dot-circle-o"></i>Start Using Admin</a>
                                    <ul>
                                        <li><a href="#section-2-1"><i class="fa fa-fw fa-dot-circle-o"></i>Add Apps</a></li>

                                    </ul>
                                </li>
                                <li><a href="#section-3"><i class="fa fa-fw fa-dot-circle-o"></i>SDK Configuration</a>
                                    <ul>
                                        <li><a href="#section-3-1"><i class="fa fa-fw fa-dot-circle-o"></i>iOS</a></li>
                                        <li><a href="#section-3-2"><i class="fa fa-fw fa-dot-circle-o"></i>Android</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
    <!-- /.sidebar --> 
</aside>