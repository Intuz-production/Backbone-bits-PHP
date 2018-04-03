<?php

/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

/*
 * Ajax processing for all features/modules
 */
require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {

    //Switch Intro Helptip flag
    if ($_REQUEST['action'] == 'change_intro_flag') {
        extract($_REQUEST);
        $upd["intro"] = "1";
        $dclass->update("tblmember", $upd, "intid='" . $intid . "' ");
        $data['output'] = 'S';
        echo json_encode($data);
        die();
    }

    //Search Apps
    if ($_REQUEST['action'] == 'search_app') {
        extract($_REQUEST);

        $wh = '';
        if ($app_type != '') {
            $wh = " AND app_type = '" . $app_type . "' ";
            $order_by = "tblmember_apps.intid DESC ";
        } else {
            $order_by = "app_type,tblmember_apps.intid DESC";
        }

        if ($_REQUEST['list'] == 'help' || $_REQUEST['site_url'] == 'help-faq-list' || $_REQUEST['site_url'] == 'help-img-video') {
            $res = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='4' AND tblmember_apps.member_id='" . $member_id . "' AND tblmember_apps.intid!='" . $_REQUEST['sel_app_id'] . "' AND app_name LIKE '%" . $q . "%' $wh ORDER BY $order_by ");
        } else if ($_REQUEST['site_url'] == 'respond' || $_REQUEST['site_url'] == 'respond-detail') {
            if ($_REQUEST['site_url'] == 'respond') {
                foreach ($_SESSION['app_id'] as $val_app_id) {
                    $append_app_id .= " AND tblmember_apps.intid!='" . $val_app_id . "'";
                }
            }

            if ($member_id == $_SESSION['agents_cust_id']) {
                $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
            } else {
                $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
            }

            $res = $dclass->select("tblmember_apps.*, tblapp_support.intid as support_id", "tblmember_apps, tblapp_support, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='3' AND tblmember_apps.intid = tblapp_support.app_id " . $append_data . " and request_id = 0 AND tblmember_apps.intid!='" . $_REQUEST['sel_app_id'] . "' $append_app_id AND app_name LIKE '%" . $q . "%' $wh group by intid ORDER BY $order_by ");
        } else {
            $res = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' AND intid!='" . $_REQUEST['sel_app_id'] . "' AND app_name LIKE '%" . $q . "%' $wh ORDER BY $order_by ");
        }
        if ($_REQUEST['site_url'] == 'respond') {
            $data['apps'] = '<input type="hidden" id="app_count_data" value="' . count($res) . '">';
        } else {
            $data['apps'] = '';
        }

        if (count($res) > 0) {
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
                if ($_REQUEST['list'] == 'apps' || $_REQUEST['site_url'] == 'app-settings') {
                    $site_url_redirect = 'app-details';
                } else if ($_REQUEST['list'] == 'help' || $_REQUEST['site_url'] == 'help-faq-list' || $_REQUEST['site_url'] == 'help-img-video') {
                    $resupdd = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $res[$i]['intid'] . "' AND (record_status='running' OR record_status='prev') ");

                    if (count($resupdd) > 0) {
                        $site_url_redirect = 'help-img-video';
                        $append_live = '&sel=faq&live';
                    } else {
                        $site_url_redirect = 'help-img-video';
                        $append_live = '&sel=faq&live';
                    }
                } else if ($_REQUEST['site_url'] == 'respond-detail') {
                    $site_url_redirect = 'respond-detail';
                } else if ($_REQUEST['site_url'] == 'respond') {
                    $site_url_redirect = 'respond';
                }

                if ($_REQUEST['list'] != '')
                    $div_class = 'col-md-2';
                else
                    $div_class = 'col-md-3';

                if (!empty($_REQUEST['support_id'])) {
                    $support_id = '&support_id=' . $_REQUEST['support_id'];
                }

                if ($_REQUEST['site_url'] == 'respond') {
                    $data['apps'] .= '<div onclick="hide_app(\'' . $res[$i]['intid'] . '\')" id="' . $res[$i]['intid'] . '" class="col-xs-6 ' . $div_class . '"><a href="javascript:;" class="thumbnail">';
                    $data['apps'] .= '<div class="logo-icon"><img src="' . $app_img_path . '" width="110" height="110" alt="" title="" /></div>';
                    $data['apps'] .= '<div class="logo-title"> ';
                    $data['apps'] .= '<i class="' . $app_type_img_path . '" title="">&nbsp;</i>';
                    $data['apps'] .= '<span class="' . $active_class . '"></span>';
                    $data['apps'] .= '<div class="cl"></div>';
                    $data['apps'] .= $res[$i]['app_name'] . '</div>';
                    $data['apps'] .= '</a></div>';
                } else {
                    $data['apps'] .= '<div class="col-xs-6 ' . $div_class . '"><a href="' . $site_url_redirect . '?sel_app_id=' . $res[$i]["intid"] . $support_id . $append_live . '" class="thumbnail">';
                    $data['apps'] .= '<div class="logo-icon"><img src="' . $app_img_path . '" width="110" height="110" alt="" title="" /></div>';
                    $data['apps'] .= '<div class="logo-title"> ';
                    $data['apps'] .= '<i class="' . $app_type_img_path . '" title="">&nbsp;</i>';
                    $data['apps'] .= '<span class="' . $active_class . '"></span>';
                    $data['apps'] .= '<div class="cl"></div>';
                    $data['apps'] .= $res[$i]['app_name'] . '</div>';
                    $data['apps'] .= '</a></div>';
                }
            }//for loop over
        } else {
            if ($_REQUEST['list'] != '' && $_REQUEST['list'] == 'apps') {
                if (($no_record == 1 && $app_type != 'android') || ($no_ad_record == 1 && $app_type == 'android')) {
                    $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/click-plus-add-new-apps.png" class="image_left" alt="" title="" /></div></div></div></div>';
                } else {
                    $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
                }
            } else if ($_REQUEST['list'] != '' && $_REQUEST['list'] != 'apps') {
                $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
            } else {
                $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 img-center" style="color:red;font-size:18px"> <img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
            }
        }

        $data['output'] = 'S';
        echo json_encode($data);
        die();
    }

    if ($_REQUEST['action'] == 'send_contact') {
        extract($_REQUEST);
        $email_to = $gnrl->getSettings('varemailrecive');
        $email_from = $sender_email;
        $email_subject = "Feedback From " . $sender_name . "  For- " . BRAND . "";

        $email_message .= "Hello Admin,<br /><br />";
        $email_message .= "<p>A new feedback has been received from member <b>" . $sender_name . "</b> as mentioned below <br />";
        $email_message .= "" . $feedback . "<br />";
        $email_message .= "Regards,<br><strong>" . BRAND . "</strong>";
        $email_message .= "</p>";

        $gnrl->email($email_from, $email_to, "", "", $email_subject, $email_message, "html");
        $data['output'] = 'S';
        $data['msg'] = $gnrl->getMessage("CONTACT_FEEDBACK_SENT", $lang_id);
        echo json_encode($data);
        die();
    }

    if ($_REQUEST['action'] == 'add_manual_app' || $_REQUEST['action'] == 'add_auto_app') {
        extract($_REQUEST);
        if ($action == 'add_manual_app') {
            unset($_SESSION['add_auto_app']);
            $_SESSION['add_manual_app'] = 1;
        } else if ($action == 'add_auto_app') {
            unset($_SESSION['add_manual_app']);
            $_SESSION['add_auto_app'] = 1;
        }

        if ($app_type) {
            $_SESSION['app_type'] = $app_type;
        }

        $data['output'] = 'S';
        echo json_encode($data);
        die();
    }

    //Autocomplete search for app companies using ios/android feed
    if ($_REQUEST['action'] == 'auto_search_company_apps' && isset($_REQUEST['company_name']) && isset($_REQUEST['app_type'])) {
        extract($_REQUEST);

        //IOS
        $arr = array();
        $arr2 = array();
        if ($app_type == 'ios') {
            $url = "http://itunes.apple.com/search?term=" . urlencode($company_name) . "&entity=software";
            $obj = json_decode($gnrl->getimg($url));
            if ($flag == 'all' && $obj->resultCount > 0) {
                $fres = $dclass->select("pf.feature_id,pf.intid as id,f.fedesc,f.fename,f.felogo,p.pcost,p.pintval,p.ptype ", "tblpackage_features pf INNER JOIN tblfeatures f ON pf.feature_id=f.intid INNER JOIN tblpackages p ON pf.package_id=p.intid", "  AND pf.status='active' AND p.status='active' AND f.status='active' AND pf.feature_id='2' ORDER BY pf.feature_id");
                $data['total'] = $obj->resultCount;
                $data['res'] = '<input type="hidden" name="company_ajax_name" id="company_ajax_name" value="' . $company_name . '">';
            }
            $default_totalcost = 0;
            $data['app_preview'] = '';
            for ($i = 0; $i < $obj->resultCount; $i++) {
                $arr[$i] = $obj->results[$i]->trackName;
                $arr2[$i] = $obj->results[$i]->trackId;
                $data['res'] .= '<div class="col-xs-6 col-md-3"><a class="thumbnail" href="javascript:;" id="trackId-' . $obj->results[$i]->trackId . '" onclick="get_app_preview(' . $obj->results[$i]->trackId . ')" ><div class="logo-icon"><img width="110" height="110" src="' . $obj->results[$i]->artworkUrl512 . ' " alt="" title="' . $obj->results[$i]->trackName . '"></div><div class="logo-title">' . $obj->results[$i]->trackName . '</div></a></div>';
                $sscnt = 0;
                foreach ($obj->results[$i]->screenshotUrls as $ss) {
                    if ($sscnt < 6) {

                        $data['res'] .= '<input type="hidden" name="ssh-' . $obj->results[$i]->trackId . '-' . $i . '-' . $sscnt . '"id="ssh-' . $obj->results[$i]->trackId . '-' . $i . '-' . $sscnt . '" value="' . $ss . '" >';
                    }
                    $sscnt++;
                }

                if ($_REQUEST['trackid'] == $obj->results[$i]->trackId) {
                    $data['app_preview'] .= '<div class="row" id="' . $obj->results[$i]->trackId . '"  >';
                    $data['app_preview'] .= ' <input type="hidden" name="ssurlcount-' . $obj->results[$i]->trackId . '-' . $i . '" id="ssurlcount-' . $obj->results[$i]->trackId . '-' . $i . '" value="' . $sscnt . '" > <input type="hidden" id="app_name-' . $obj->results[$i]->trackId . '-' . $i . '" name="app_name-' . $obj->results[$i]->trackId . '-' . $i . '" value="' . $obj->results[$i]->trackName . '">  <input type="hidden" id="app_url-' . $obj->results[$i]->trackId . '-' . $i . '" name="app_url-' . $obj->results[$i]->trackId . '-' . $i . '"  value="' . $obj->results[$i]->trackViewUrl . '">
                        <input id="more_app_img-' . $obj->results[$i]->trackId . '-' . $i . '" name="more_app_img-' . $obj->results[$i]->trackId . '-' . $i . '"  type="hidden" value="' . $obj->results[$i]->screenshotUrls[0] . '">
                        <input id="more_app_new_img-' . $obj->results[$i]->trackId . '-' . $i . '" name="more_app_new_img-' . $obj->results[$i]->trackId . '-' . $i . '"  type="hidden" value=""><input type="hidden" id="bundleId' . '-' . $i . '" name="bundleId' . '-' . $i . '" value="' . $obj->results[$i]->bundleId . '" >
                   <input type="hidden" id="trackId' . '-' . $i . '" name="trackId' . '-' . $i . '" value="' . $obj->results[$i]->trackId . '" >   
                   <input id="app_logo-' . $obj->results[$i]->trackId . '-' . $i . '" name="app_logo-' . $obj->results[$i]->trackId . '-' . $i . '"  type="hidden" value="' . $obj->results[$i]->artworkUrl512 . '"> <input type="hidden" name="feature_id-' . $obj->results[$i]->trackId . '-' . $i . '" id="feature_id-' . $obj->results[$i]->trackId . '-' . $i . '" value="2"  />';

                    $data['app_preview'] .= '<div class="col-xs-12 col-md-4"><div class="col-xs-12 col-md-12"> <a class="thumbnail" href="javascript:;"><div class="logo-icon"><img width="110" height="110" src="' . $obj->results[$i]->artworkUrl512 . ' " alt="" title="' . $obj->results[$i]->trackName . '"></div><div class="logo-title"><ul class="all-apps-icon"><li class="ip">&nbsp;</li></ul></div></a> </div></div>';
                    $data['app_preview'] .= '<div class="col-xs-12 col-md-8">';
                    $data['app_preview'] .= '<div class="form-group"><label>App Name</label><div class="input-group" style="width:100%;"> ' . $obj->results[$i]->trackName . ' </div></div>';
                    $data['app_preview'] .= '<div class="form-group"><label>App URL</label><div class="input-group" style="width:100%;"> ' . $obj->results[$i]->trackViewUrl . ' </div></div>';
                    $data['app_preview'] .= '<div class="form-group"><label>Bundle ID</label><div class="input-group" style="width:100%;"> ' . $obj->results[$i]->bundleId . ' </div></div>';
                    $data['app_preview'] .= '<div class="form-group"><label>App ID</label><div class="input-group" style="width:100%;"> ' . $obj->results[$i]->trackId . ' </div></div>';
                    $data['app_preview'] .= '</div><script>get_app_preview(' . $obj->results[$i]->trackId . ')</script>';
                    $data['app_preview'] .= '</div>';
                }
            }
            if ($flag == 'all' && $obj->resultCount > 0) {
                for ($i = 0; $i < $obj->resultCount; $i++) {

                    $sscnt = 0;
                    foreach ($obj->results[$i]->screenshotUrls as $ss) {
                        if ($sscnt < 6) {
                            if ($sscnt == 0)
                                $sclass = 'class="active"';
                            else
                                $sclass = '';
                        }
                        $sscnt++;
                    }
                }
            }
            else if ($flag == 'all' && (!obj || $obj->resultCount == 0)) {
                $data['res'] .= '<h3><img src="img/no_apps_found.png" alt="" title="" /><br>Please Select Company Or App Name</h3>';
            }
            if (!$flag) {
                //get unique names from non-ordered duplicates  
                $names = array_map('unserialize', array_unique(array_map('serialize', $arr)));
                $trackids = array_map('unserialize', array_unique(array_map('serialize', $arr2)));
                $cnt = 0;
                foreach ($names as $key => $name) {
                    $data[$cnt]['name'] = $name;
                    $data[$cnt]['trackid'] = $trackids[$key];
                    $cnt++;
                }
            }
        } else if ($app_type == 'android') {
            include("android-api/proto/protocolbuffers.inc.php");
            include("android-api/proto/market.proto.php");
            include("android-api/Market/MarketSession.php");

            $session = new MarketSession();
            $session->login(GOOGLE_EMAIL, GOOGLE_PASSWD);
            $session->setAndroidId(ANDROID_DEVICEID);
            if ($flag == 'all') {
                
            } else {
                $ar = new AppsRequest();
                $ar->setQuery(urlencode($company_name));

                $ar->setStartIndex(0);
                $ar->setEntriesCount(10);

                $ar->setWithExtendedInfo(true);

                $reqGroup = new Request_RequestGroup();
                $reqGroup->setAppsRequest($ar);

                $response = $session->execute($reqGroup);

                $groups = $response->getResponsegroupArray();
                foreach ($groups as $rg) {

                    $appsResponse = $rg->getAppsResponse();
                    $obj = $appsResponse->getAppArray();
                    $data['total'] = count($obj);
                    for ($i = 0; $i < count($obj); $i++) {
                        $package_unique_id = $obj[$i]->getPackageName();
                        $data[$i]['name'] = $obj[$i]->getTitle();
                        $data[$i]['id'] = $obj[$i]->getPackageName();
                        $data[$i]['url'] = $url = "https://play.google.com/store/apps/details?id=" . $obj[$i]->getPackageName() . "&hl=en";
                        $data[$i]['link'] = '';
                    }
                }
            }

            if ($flag == 'all') {
                include('simple_html_dom.php');
                $data['res'] = '<input type="hidden" name="company_ajax_name" id="company_ajax_name" value="' . $company_name . '">';
                $data['total'] = 1;
                $i = 0;
                $package_unique_id = $app_store_id;
                $app_title = $company_name;
                $app_title_para = str_replace(" ", "_", $app_title);
                $package_unique_id_para = str_replace(".", "_", $package_unique_id);
                $app_url = "https://play.google.com/store/apps/details?id=" . $package_unique_id;
                $url = "https://play.google.com/store/apps/details?id=" . $package_unique_id;
                $str_url = $gnrl->getimg($url);
                $html = str_get_html($str_url); //put your app id here
                $imgobj = $html->find('.cover-container');
                $img = $imgobj[0]->find('img');
                $app_logo[0] = $img[0]->src;
                $data['logo'] = $app_logo[0];
                $html->clear();
                unset($html);
                //get screenshots
                for ($j = 1; $j < 6; $j++) {
                    $scnt = 0;
                    $imageId = $j;
                    //check for package based ss imgs over  
                    if (!is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png")) {

                        $gir = new GetImageRequest();
                        $gir->setImageUsage(GetImageRequest_AppImageUsage::SCREENSHOT);
                        $gir->setAppId($package_unique_id);
                        $gir->setImageId($imageId);
                        $reqGroup = new Request_RequestGroup();
                        $reqGroup->setImageRequest($gir);
                        $iresponse = $session->execute($reqGroup);
                        $igroups = $iresponse->getResponsegroupArray();
                        foreach ($igroups as $rg) {
                            $imageResponse = $rg->getImageResponse();
                            if ($imageResponse) {
                                file_put_contents(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png", $imageResponse->getImageData());
                            }
                            if (is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png")) {
                                $ss_imgs[$scnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png";
                                $scnt++;
                            }
                        }
                    } else {
                        $ss_imgs[$scnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png";
                        $scnt++;
                    }
                }
                $data['app_preview'] = '';
                $data['res'] .= '<div class="col-xs-6 col-md-3"><a class="thumbnail" href="javascript:;" id="trackId-' . $package_unique_id . '" onclick=\'get_app_preview("' . $package_unique_id_para . '")\' ><div class="logo-icon"><img width="110" height="110" src="' . $app_logo[0] . ' " alt="" title="' . $app_title . '"></div><div class="logo-title">' . $app_title . '</div></a></div>';
                $data['app_preview'] .= '<div class="row" id="' . $package_unique_id_para . '" style="display:none;">';
                $sscnt = 0;
                for ($j = 1; $j < 6; $j++) {
                    if (is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $j . ".png")) {
                        $appi_logo[$sscnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $j . ".png";
                        $data['res'] .= '<input type="hidden" name="ssh-' . $package_unique_id . '-' . $i . '-' . $sscnt . '"id="ssh-' . $package_unique_id . '-' . $i . '-' . $sscnt . '" value="' . $appi_logo[$sscnt] . '" >';

                        $sscnt++;
                    }
                }
                $data['app_preview'] .= '<input type="hidden" id="app_name-' . $package_unique_id . '-' . $i . '" name="app_name-' . $package_unique_id . '-' . $i . '" value="' . $app_title . '"><input type="hidden" id="app_url-' . $package_unique_id . '-' . $i . '" name="app_url-' . $package_unique_id . '-' . $i . '" value="' . $app_url . '"><input id="more_app_img-' . $package_unique_id . '-' . $i . '" name="more_app_img-' . $package_unique_id . '-' . $i . '"  type="hidden" value="' . $ss_imgs[0] . '"><input id="more_app_new_img-' . $package_unique_id . '-' . $i . '" name="more_app_new_img-' . $package_unique_id . '-' . $i . '"  type="hidden" value=""><input type="hidden" id="bundleId' . '-' . $i . '" name="bundleId' . '-' . $i . '" value="' . $package_unique_id . '" >';
                $data['app_preview'] .= '<input type="hidden" id="trackId' . '-' . $i . '" name="trackId' . '-' . $i . '" value="' . $package_unique_id . '" > <input id="app_logo-' . $package_unique_id . '-' . $i . '" name="app_logo-' . $package_unique_id . '-' . $i . '"  type="hidden" value="' . $app_logo[0] . '"> <input type="hidden" name="feature_id-' . $package_unique_id . '-' . $i . '" id="feature_id-' . $package_unique_id . '-' . $i . '" value="2"  /> <input type="hidden" name="ssurlcount-' . $package_unique_id . '-' . $i . '" id="ssurlcount-' . $package_unique_id . '-' . $i . '" value="' . $sscnt . '" >';
                $data['app_preview'] .= '<div class="col-xs-12 col-md-4"><div class="col-xs-12 col-md-12"> <a class="thumbnail" href="javascript:;"><div class="logo-icon"><img width="110" height="110" src="' . $app_logo[0] . ' " alt="" title="' . $app_title . '"></div><div class="logo-title"><ul class="all-apps-icon"><li class="ad">&nbsp;</li></ul></div></a> </div></div>';
                $data['app_preview'] .= '<div class="col-xs-12 col-md-8">';
                $data['app_preview'] .= '<div class="form-group"><label>App Name</label><div class="input-group" style="width:100%;"> ' . $app_title . ' </div></div>';
                $data['app_preview'] .= '<div class="form-group"><label>App URL</label><div class="input-group" style="width:100%;"> ' . $app_url . ' </div></div>';
                $data['app_preview'] .= '<div class="form-group"><label>Bundle ID</label><div class="input-group" style="width:100%;"> ' . $package_unique_id . ' </div></div>';

                $data['app_preview'] .= '</div><script>get_app_preview("' . $package_unique_id_para . '");</script>';
                $data['app_preview'] .= '</div>';
            }
            if ($flag == 'all') {
                
            } else if ($flag == 'all' && !obj) {
                $data['res'] .= '<h3><img src="img/no_apps_found.png" alt="" title="" /><br>Please Select Company Or App Name</h3>';
            }
            if (!$flag) {
                //get unique names from non-ordered duplicates  
                $names = array_map('unserialize', array_unique(array_map('serialize', $arr)));
                $cnt = 0;
                foreach ($names as $name) {
                    $data[$cnt]['name'] = $name;
                    $cnt++;
                }
            }
        } else {
            $data = '';
        }

        echo json_encode($data);
        die();
    }
    // android scraper 
    if ($_REQUEST['action'] == 'androidscraper' && isset($_REQUEST['appurl'])) {
        extract($_REQUEST);

        $appid = '';
        $appurl = explode("id=", trim($appurl));
        if ($appurl) {
            $appid = $appurl[1];
        }

        $url = SITE_URL . "googleapi/index.php?id=" . $appid;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($output);

        if ($data) {
            if ($data->status == 1) {
                $_SESSION['androidadddata'] = $data->data;
            }
        }
        echo $output;
        die;
    }

    // Save App
    if ($_REQUEST['action'] == 'save_android_google_data') {
        $appinfomation = (array) $_SESSION['androidadddata'];
        if (!$appinfomation) {
            echo "data not found.";
            die;
        }
        $member_id = $_SESSION['custid'];
        $app_type = 'android';
        $app_store_id = $appinfomation['id'];
        $app_url = $appinfomation['url'];
        $feature_status = 'active';
        $app_name = $appinfomation['title'];
        $wh = '';
        //Check unique app name
        $chk = $dclass->select("intid", "tblmember_apps", "AND member_id='" . $member_id . "' AND app_type='android' AND app_store_id='" . $app_store_id . "'");

        if (count($chk) > 0) {
            $data['output'] = 'F';
            $type = 'err';
            $app_msg_status = "APP_EXIST";
            $data['msg'] = $gnrl->getMessage($app_msg_status, $lang_id);
            echo json_encode($data);
            die();
        } else {
            $tid = explode(".", $appinfomation['id']);
            if ($tid[2])
                $app_para = $tid[2];
            else if ($tid[1])
                $app_para = $tid[1];

            $ins['member_id'] = $member_id;
            $ins['company_name'] = $appinfomation['author'];
            $ins['or_app_name'] = $appinfomation['title'];
            $ins['app_name'] = $gnrl->subString($appinfomation['title'], 30);
            $ins['app_type'] = 'android';
            if ($appinfomation['image']) {
                $content = $gnrl->getimg("http:" . $appinfomation['image']);
                $filename = time() . $app_para . '.png';
                $save_path = APP_LOGO . '/' . $filename;
                file_put_contents($save_path, $content);
                $ins['app_logo'] = $filename;
            }
            $ins['app_key'] = $mcrypt->encrypt($member_id . "-" . $app_para) . $gnrl->randomLoginToken(4);
            $ins['app_store_id'] = $appinfomation['id'];
            $ins['app_url'] = $appinfomation['url'];
            $ins['app_add_date'] = date("Y-m-d h:i:s");
            $ins['app_mod_date'] = date("Y-m-d h:i:s");
            $ins['app_status'] = 'active';
            $ins['server_status'] = 'prod';
            $ins['payment_status'] = "trial";
            $ins['track_id'] = uniqid();
            $id = $dclass->insert("tblmember_apps", $ins);
            $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], APP_ADDED . ' ' . $ins['or_app_name']);
            if ($id) {
                $fres = $dclass->select("intid", "tblfeatures", " AND status='active' ");
                for ($i = 0; $i < count($fres); $i++) {
                    unset($insf);
                    $insf['member_id'] = $member_id;
                    $insf['app_id'] = $id;
                    $insf['feature_id'] = $fres[$i]['intid'];
                    $insf['transaction_id'] = 0;
                    $insf['pf_id'] = 0;
                    $insf['payment_type'] = 'monthly';
                    $insf['payment_cost'] = 0;
                    $insf['feature_status'] = "running";
                    $fid = $dclass->insert("tblmember_app_features", $insf);
                    $feature_id = $insf['feature_id'];
                }
                unset($insp);
                $insp['member_id'] = $member_id;
                $insp['app_id'] = $id;
                $insp['logo_flag'] = 'no';
                $insp['title_text'] = "Our Other Apps";
                $insp['font_color'] = '000000';
                $insp['bck_color'] = 'ffffff';
                $insp['font_family'] = 'Helvetica';
                $insp['animation_id'] = 4;
                $insp['status'] = 'publish';

                $prid = $dclass->insert("tblmore_app_settings", $insp);
                $data['output'] = 'S';
                $app_msg_status = "APP_ADD";
            } else {
                $data['output'] = 'F';
                $app_msg_status = "APP_SAVE_FAIL";
            }
            $data['msg'] = $gnrl->getMessage($app_msg_status, $lang_id);
            $data['app_type'] = $app_type;

            unset($_SESSION['androidadddata']);
            echo json_encode($data);
            die();
        }
    }

    //Autocomplete search for apps using ios/android feed
    if ($_REQUEST['action'] == 'get_pimages' && isset($_REQUEST['intid']) && isset($_REQUEST['more_app_id'])) {
        extract($_REQUEST);
        $more = $dclass->select("r.intorder, r.intid as ind, r.more_app_id,r.more_app_img_id,r.more_app_custom_image,r.status as record_status, a.app_name, a.app_url", "tblmore_apps s  INNER JOIN tblapp_moreapp_rel r  ON s.intid=r.more_app_id INNER JOIN tblmember_apps a ON s.parent_app_id=a.intid", " AND r.intid='" . $intid . "'");
        //Get more app images
        $mres = $dclass->select("mi.*", "tblmore_app_images mi INNER JOIN tblmore_apps m ON mi.more_app_id=m.intid", " AND m.intid='" . $more_app_id . "' ");
        $data['icount'] = count($mres);
        $data['res'] = '';
        if (count($mres) > 1) {
            $data['res'] .= '<div class="modal fade" id="more_app_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel" style="font-size:30px;">Select Images</h4>
                        </div>
                        <div class="modal-body">
                            <div class="select_app_img">
                    	       <ul id="select-app-img">';

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

                if ($sel_more_app_img_id == $mintid) {
                    $sclass = 'class="active"';
                } else {
                    $sclass = '';
                }
                if ($mimg_source != 'custom') {
                    $data['res'] .= '<input type="hidden" name="ssh-' . $i . '"id="ssh-' . $i . '" value="' . $mimg . '" >
                        <li ' . $sclass . ' id="select_more_app_logo-' . $i . '" onclick="select_manual_logo(this.id)"><img src="' . $mimg_path . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                        <input type="hidden" name="sshid-' . $i . '"id="sshid-' . $i . '" value="' . $mintid . '" > ';
                }
            }  //for loop over

            if ($cust_flag == 1 || $more[0]['more_app_custom_image'] != '') {
                $click = 'onclick="select_manual_logo(this.id)"';
            }
            if (($more[0]['more_app_custom_image'] != '' && $sel_more_app_img_id == 0) || ($cust_flag == 1 && $custom_id == $sel_more_app_img_id)) {
                $dvclass = 'class="active"';
                $hflag = "active";
            }

            $data['res'] .= '<div class="file_logo ' . $hflag . '" id="sfile_logo" ' . $click . '><span class="btn add-files fileinput-button">     
                    <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                    <input id="more_app_img" name="more_app_img" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                </span>';

            if ($more[0]['more_app_custom_image'] != '') {
                $data['res'] .= ' <input type="hidden" name="ssh-' . (count($mres) + 1) . '"id="ssh-' . (count($mres) + 1) . '" value="0" >
                    <div class="upload_img" id="more_inner_img">
                        <div class="center-img"> <img  src="' . MORE_APPS_IMG . "/" . $more[0]['more_app_custom_image'] . '" style="height:auto;width: 230px;" ></div>
                        <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div>
                    </div>';
            } else if ($cust_flag == 1) {
                $data['res'] .= '<input type="hidden" name="ssh-' . $c . '"id="ssh-' . $c . '" value="' . $custom_id . '" >
                    <div class="upload_img" id="more_inner_img">
                        <div class="center-img"> <img  src="' . $custom_path . '" style="height:auto;width: 230px;" ></div>
                        <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div>
                    </div>';
            }

            $data['res'] .= '<div ' . $dvclass . '></div>';
            $data['res'] .= '</div>';
            $data['res'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" > 
                </div>
                </div>
                <div class="modal-footer">
                    <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                    <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                </div>
                </div>
                </div>
                </div><!-- END Popup -->';
        } else {
            
        }

        echo json_encode($data);
        die();
    }

    //Autocomplete search for apps using ios/android feed
    if ($_REQUEST['action'] == 'get_latest_app_info' && isset($_REQUEST['app_store_id']) && isset($_REQUEST['app_type'])) {
        extract($_REQUEST);

        $custi = $dclass->select("i.image,i.status", "tblmore_apps m INNER JOIN tblmember_apps s ON m.parent_app_id=s.intid INNER JOIN tblmore_app_images i ON m.intid=i.more_app_id", "AND m.parent_app_id='" . $app_id . "' AND i.source='custom' LIMIT 1");
        if (count($custi) > 0) {
            $custom_image = $custi[0]['image'];
            $custom_image_status = $custi[0]['status'];
        }
        //IOS
        if ($app_type == 'ios') {
            $url = "http://itunes.apple.com//lookup?id=" . urlencode($app_store_id);

            $obj = json_decode($gnrl->getimg($url));
            $total = $obj->resultCount;
            $data['res'] = '';
            $data['mres'] = '';
            $data['lres'] = '';
            for ($i = 0; $i < $total; $i++) {
                if ($i == 0) {
                    $data['name'] = $obj->results[$i]->trackName;
                    $data['id'] = $obj->results[$i]->bundleId;
                    $data['logo'] = $obj->results[$i]->artworkUrl512;
                    $data['url'] = $obj->results[$i]->trackViewUrl;
                    if ($custom_image != '' && $custom_image_status == 'cover')
                        $data['ss'] = MORE_APPS_IMG . "/" . $custom_image;
                    else
                        $data['ss'] = $obj->results[$i]->screenshotUrls[0];
                    $data['lres'] .= '<div class="preview_small_img"><div class="center-img"><img width="255" height="255" src="' . $data['logo'] . '"></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(1)"><i class="apps_i remove_icon_"></i></a></div></div>';

                    $data['mres'] .= '<input id="company_name" name="company_name"  type="hidden" value=""><div id="mfile_logo" class="file_logo"><input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="' . $data['ss'] . '"><div class="apps_section"><div class="fl app__img">
                                    <input id="more_app_sel_new_img" name="more_app_sel_new_img"  type="hidden" value="">        
                                <div class="edit_img">
                                <div class="edit__"  id="app_rlogo"><img src="' . $data['ss'] . '" width="126" height="221" alt="" />
                                 <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                                </div></div>';

                    $data['mres'] .= '      
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
                                            <ul id="select-app-img">';

                    $sscnt = 0;
                    foreach ($obj->results[$i]->screenshotUrls as $ss) {
                        if ($sscnt < 6) {
                            if ($sscnt == 0 && (($custom_image != '' && $custom_image_status != 'cover') || $custom_image == ''))
                                $sclass = 'class="active"';
                            else
                                $sclass = '';

                            $data['mres'] .= '<li id="select_more_app_logo-' . $sscnt . '" ' . $sclass . '  onclick="select_manual_logo(this.id)" ><img src="' . $ss . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                        <input type="hidden" name="ssh-' . $sscnt . '"id="ssh-' . $sscnt . '" value="' . $ss . '" >';
                        }
                        $sscnt++;
                    }

                    if ($custom_image != '' && $custom_image_status == 'cover') {
                        $sclass = 'active';
                    } else {
                        $sclass = '';
                    }

                    if ($custom_image != '') {
                        $js = 'onclick="select_manual_logo(this.id)"';
                    }

                    $data['mres'] .= ' <div class="or">OR  </div><div class="file_logo ' . $sclass . '" id="sfile_logo" ' . $js . ' ><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                        <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                        </span>';

                    if ($custom_image != '') {
                        $data['mres'] .= ' <input type="hidden" name="ssh-' . ($sscnt + 1) . '"id="ssh-' . ($sscnt + 1) . '" value="0" ><div class="upload_img" id="more_inner_img">
                              <div class="center-img"> <img  src="' . MORE_APPS_IMG . "/" . $custom_image . '" style="height:auto;width: 230px;" ></div>
                              <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div>
                            </div>';
                    }
                    $data['mres'] .= '<div class="' . $sclass . '"></div>';
                    $data['mres'] .= '</div><div class="display_msg"></div>';


                    $data['mres'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" > 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                               <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                            </div>
                        </div>
                        </div>
                        </div><!-- END Popup -->';
                }
            }
        } else if ($app_type == 'android') {

            $url = SITE_URL . "googleapi/index.php?id=" . $app_store_id;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            $output = curl_exec($ch);
            curl_close($ch);
            $odata = json_decode($output);
            if ($odata) {
                if ($odata->status == 1) {

                    $appinfomation = (array) $odata->data;
                    $tid = explode(".", $appinfomation['id']);
                    if ($tid[2])
                        $app_para = $tid[2];
                    else if ($tid[1])
                        $app_para = $tid[1];


                    if ($appinfomation['image']) {
                        $data['mres'] = "";
                        $data['logo'] = "http:" . $appinfomation['image'];

                        $data['lres'] .= '<div class="preview_small_img"><div class="center-img"><img width="255" height="255" src="http:' . $appinfomation['image'] . '"></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo_preview(1)"><i class="apps_i remove_icon_"></i></a></div></div>';

                        if (count($appinfomation['screenshots']) > 0) {
                            $i = 0;
                            $scnt = 0;
                            foreach ($appinfomation['screenshots'] as $value) {

                                if ($i == 5) {
                                    break;
                                }
                                $content = $gnrl->getimg("http:" . $value);
                                $filename = time() . $app_para . '-promo' . $i . '.png';
                                $save_path = ANDROID_TEMP . '/' . $filename;
                                $chk = file_put_contents($save_path, $content);
                                if ($chk) {
                                    $ss_imgs[$scnt] = $save_path;
                                    $scnt++;
                                }
                                $i++;
                            }
                        }
                        if ($custom_image != '' && $custom_image_status == 'cover')
                            $data['ss'] = MORE_APPS_IMG . "/" . $custom_image;
                        else
                            $data['ss'] = $ss_imgs[0];

                        if ($data['ss'] != '') {
                            $data['mres'] .= '<input id="company_name" name="company_name"  type="hidden" value=""><div id="mfile_logo" class="file_logo"><input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="' . $data['ss'] . '"><div class="apps_section"><div class="fl app__img">
                                        <input id="more_app_sel_new_img" name="more_app_sel_new_img"  type="hidden" value="">        
                                    <div class="edit_img">
                                    <div class="edit__"  id="app_rlogo"><img src="' . $data['ss'] . '" width="126" height="221" alt="" />
                                     <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                                    </div></div>';


                            $data['mres'] .= '      
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
                                         <ul id="select-app-img">';
                            $sscnt = 0;

                            foreach ($ss_imgs as $value) {

                                $app_logo[$sscnt] = $value;
                                $data['mres'] .= '<li id="ss-' . $sscnt . '" ' . $sclass . '  onclick="select_manual_logo(this.id)" ><img src="' . $app_logo[$sscnt] . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                                             <input type="hidden" name="ssh-' . $sscnt . '" ' . $sclass . '" id="ssh-' . $sscnt . '" ' . $sclass . '" value="' . $app_logo[$sscnt] . '" >';
                                $sscnt++;
                            }


                            if ($custom_image != '' && $custom_image_status == 'cover') {
                                $sclass = 'active';
                            } else
                                $sclass = '';

                            if ($custom_image != '') {
                                $js = 'onclick="select_manual_logo(this.id)"';
                            }

                            $data['mres'] .= ' <div class="or">OR  </div><div class="file_logo ' . $sclass . '" id="sfile_logo" ' . $js . ' ><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                                                <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                                </span>';
                            if ($custom_image != '') {
                                $data['mres'] .= ' <input type="hidden" name="ssh-' . ($sscnt + 1) . '"id="ssh-' . ($sscnt + 1) . '" value="0" >
                                                <div class="upload_img" id="more_inner_img">
                                                    <div class="center-img"> <img  src="' . MORE_APPS_IMG . "/" . $custom_image . '" style="height:auto;width: 230px;" ></div>
                                                    <div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div>
                                                </div>';
                            }
                            $data['mres'] .= '<div class="' . $sclass . '"></div>';
                            $data['mres'] .= '</div><div class="display_msg"></div>';

                            $data['mres'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" > 
                                   </div>
                                 </div>
                                 <div class="modal-footer">
                                    <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                                   <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                                 </div>
                               </div>
                             </div>
                           </div>';
                        }
                    }
                }
            }
        } else {
            $data = '';
        }

        $data['output'] = 'S';

        echo json_encode($data);
        die();
    }


    //Autocomplete search for apps using ios/android feed
    if ($_REQUEST['action'] == 'auto_search_apps' && isset($_REQUEST['app_name']) && isset($_REQUEST['app_type'])) {
        extract($_REQUEST);
        //IOS
        if ($app_type == 'ios') {
            $url = "http://itunes.apple.com/search?term=" . urlencode($app_name) . "&entity=software";

            $obj = json_decode($gnrl->getimg($url));
            $total = $obj->resultCount;
            if ($flag != 'all') {
                for ($i = 0; $i < $total; $i++) {

                    $data[$i]['name'] = $obj->results[$i]->trackName;
                    $data[$i]['id'] = $obj->results[$i]->bundleId;
                    $data[$i]['track_id'] = $obj->results[$i]->trackId;
                    $data[$i]['link'] = $obj->results[$i]->artworkUrl512;
                    $data[$i]['url'] = $obj->results[$i]->trackViewUrl;
                }
            }
            $data['res'] = '';
            $data['mres'] = '';
            $data['mires'] = '';
            if ($flag == all) {
                for ($i = 0; $i < $total; $i++) {
                    if ($i == 0) {

                        $data['res'] .= '<input id="company_name" name="company_name"  type="hidden" value="' . $obj->results[$i]->sellerName . '">  <div id="mfile_logo" class="file_logo"><input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="' . $obj->results[$i]->screenshotUrls[0] . '"><div class="apps_section"><div class="fl app__img">
                                 
                            <div class="edit_img">
                            <div class="edit__"  id="app_rlogo"><img src="' . $obj->results[$i]->screenshotUrls[0] . '" width="126" height="221" alt="" />
                             <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                            </div></div>';
                        $data['mires'] .= '<input id="company_name" name="company_name"  type="hidden" value="' . $obj->results[$i]->sellerName . '">  <div id="mfile_logo" class="file_logo"><div class="apps_section"><div class="fl app__img">
                               
                            <div class="edit_img">
                            <div class="edit__"  id="app_rlogo"><img src="' . $obj->results[$i]->screenshotUrls[0] . '" width="126" height="221" alt="" />
                             <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                            </div></div>';

                        $data['res'] .= '      
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
                                  <ul id="select-app-img">';

                        $data['mres'] .= '      
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
                                  <ul id="select-app-img">';
                        $sscnt = 0;
                        foreach ($obj->results[$i]->screenshotUrls as $ss) {
                            if ($sscnt < 6) {
                                if ($sscnt == 0)
                                    $sclass = 'class="active"';
                                else
                                    $sclass = '';

                                $data['res'] .= '<li id="select_more_app_logo-' . $sscnt . '" ' . $sclass . '  onclick="select_logo(this.id)" ><img src="' . $ss . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                        <input type="hidden" name="ssh-' . $sscnt . '"id="ssh-' . $sscnt . '" value="' . $ss . '" >';
                                $data['mres'] .= '<li id="select_more_app-' . $sscnt . '" ' . $sclass . '  onclick="select_logo(this.id)" ><img src="' . $ss . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                       <input type="hidden" name="sscnt" id="sscnt-' . $sscnt . '" value="' . $sscnt . '" >  <input type="hidden" name="ssh-' . $sscnt . '"id="ssh-' . $sscnt . '" value="' . $ss . '" >';
                            }
                            $sscnt++;
                        }
                        $data['res'] .= ' <div class="or">OR  </div><div class="file_logo" id="sfile_logo" onclick="select_logo(this.id)"><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                                            <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                            </span></div><div class="display_msg"></div>';

                        $data['mres'] .= ' <div class="or">OR  </div><div class="file_logo" id="sfile_logo" onclick="select_logo(this.id)"><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                                            <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                                            </span></div><div class="display_msg"></div>';

                        $data['res'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" > 
                                   </div>
                                 </div>
                                 <div class="modal-footer">
                                    <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                                   <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                                 </div>
                               </div>
                             </div>
                           </div><!-- END Popup -->';

                        $data['mres'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" > <input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="0"> 
                                       </div>
                                     </div>
                                     <div class="modal-footer">
                                       <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                                       <button onclick="replace_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                                     </div>
                                   </div>
                                 </div>
                               </div><!-- END Popup -->';
                    }
                }
            }
        }
        else if ($app_type == 'android') {
            include("android-api/proto/protocolbuffers.inc.php");
            include("android-api/proto/market.proto.php");
            include("android-api/Market/MarketSession.php");
            $session = new MarketSession();
            $session->login(GOOGLE_EMAIL, GOOGLE_PASSWD);
            $session->setAndroidId(ANDROID_DEVICEID);

            if ($flag != 'all') {

                $ar = new AppsRequest();
                $ar->setQuery(urlencode($app_name));

                $ar->setStartIndex(0);
                $ar->setEntriesCount(10);

                $ar->setWithExtendedInfo(true);

                $reqGroup = new Request_RequestGroup();
                $reqGroup->setAppsRequest($ar);

                $response = $session->execute($reqGroup);

                $groups = $response->getResponsegroupArray();
                foreach ($groups as $rg) {

                    $appsResponse = $rg->getAppsResponse();
                    $obj = $appsResponse->getAppArray();
                    $data['total'] = count($obj);
                    for ($i = 0; $i < count($obj); $i++) {
                        $package_unique_id = $obj[$i]->getPackageName();
                        $data[$i]['name'] = $obj[$i]->getTitle();
                        $data[$i]['id'] = $obj[$i]->getPackageName();
                        $data[$i]['url'] = $url = "https://play.google.com/store/apps/details?id=" . $obj[$i]->getPackageName() . "&hl=en";
                        $data[$i]['link'] = '';
                        $data[$i]['track_id'] = '';
                    }
                }
            } else {
                $package_unique_id = $app_store_id;
                if ($section != 'promotr') {
                    include('simple_html_dom.php');
                    $url = "https://play.google.com/store/apps/details?id=" . $package_unique_id;
                    $str_url = $gnrl->getimg($url);
                    $html = str_get_html($str_url); //put your app id here
                    $imgobj = $html->find('.cover-container');
                    $img = $imgobj[0]->find('img');
                    $app_logo[0] = $img[0]->src;
                    $data['logo'] = $app_logo[0];
                    $html->clear();
                    unset($html);
                } else {
                    $data['logo'] = '';
                }
                //get screenshots
                for ($j = 1; $j < 6; $j++) {
                    $scnt = 0;
                    $imageId = $j;

                    //check for package based ss imgs over  
                    if (!is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png")) {

                        $gir = new GetImageRequest();
                        $gir->setImageUsage(GetImageRequest_AppImageUsage::SCREENSHOT);
                        $gir->setAppId($package_unique_id);
                        $gir->setImageId($imageId);
                        $reqGroup = new Request_RequestGroup();
                        $reqGroup->setImageRequest($gir);
                        $iresponse = $session->execute($reqGroup);
                        $igroups = $iresponse->getResponsegroupArray();

                        foreach ($igroups as $rg) {
                            $imageResponse = $rg->getImageResponse();
                            if ($imageResponse != '') {
                                file_put_contents(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png", $imageResponse->getImageData());
                            }

                            if (is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png")) {
                                $ss_imgs[$scnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png";
                                $scnt++;
                            }
                        }
                    } else {
                        $ss_imgs[$scnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $imageId . ".png";
                        $scnt++;
                    }
                }

                //get screenshots over
                $data['res'] = '';
                if ($ss_imgs[0] != '') {

                    $data['res'] .= '<input id="company_name" name="company_name"  type="hidden" value=""><div id="mfile_logo" class="file_logo"><input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="' . $ss_imgs[0] . '"><div class="apps_section"><div class="fl app__img">
                                    <input id="more_app_sel_new_img" name="more_app_sel_new_img"  type="hidden" value="">        
                                <div class="edit_img">
                                <div class="edit__"  id="app_rlogo"><img src="' . $ss_imgs[0] . '" width="126" height="221" alt="" />
                                 <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                                </div></div>';

                    $data['mires'] .= '<input id="company_name" name="company_name"  type="hidden" value=""><div id="mfile_logo" class="file_logo"><div class="apps_section"><div class="fl app__img">
                                           
                                <div class="edit_img">
                                <div class="edit__"  id="app_rlogo"><img src="' . $ss_imgs[0] . '" width="126" height="221" alt="" />
                                 <div class="edit__hover"><a href="#" data-toggle="modal" data-target="#more_app_modal"><i class="apps_i edit_icon_"></i><i class="apps_i remove_icon_"></i></a></div>
                                </div></div>';

                    $data['res'] .= '      
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
                                     <ul id="select-app-img">';

                    $data['mres'] .= '      
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
                                     <ul id="select-app-img">';
                    $sscnt = 0;
                    for ($j = 1; $j < 6; $j++) {
                        if (is_file(ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $j . ".png")) {
                            $app_logo[$sscnt] = ANDROID_TEMP . "/" . $package_unique_id . "_" . $i . "_" . $j . ".png";
                            $data['res'] .= '<li id="ss-' . $sscnt . '" ' . $sclass . '  onclick="select_logo(this.id)" ><img src="' . $app_logo[$sscnt] . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                <input type="hidden" name="ssh-' . $sscnt . '" ' . $sclass . '" id="ssh-' . $sscnt . '" ' . $sclass . '" value="' . $app_logo[$sscnt] . '" >';

                            $data['mres'] .= '<li id="ss-' . $sscnt . '" ' . $sclass . '  onclick="select_logo(this.id)" ><img src="' . $app_logo[$sscnt] . '" width="126" height="221" alt="" /><div ' . $sclass . '></div></li>
                                <input type="hidden" name="sscnt" id="sscnt-' . $sscnt . '" value="' . $sscnt . '" > <input type="hidden" name="ssh-' . $sscnt . '" ' . $sclass . '" id="ssh-' . $sscnt . '" ' . $sclass . '" value="' . $app_logo[$sscnt] . '" >';

                            $sscnt++;
                        }
                    }
                    $data['res'] .= ' <div class="or">OR  </div><div class="file_logo" id="sfile_logo" onclick="select_logo(this.id)"><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                        <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                        </span></div><div class="display_msg"></div>';

                    $data['mres'] .= ' <div class="or">OR  </div><div class="file_logo" id="sfile_logo" onclick="select_logo(this.id)"><span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Image</span>
                        <input id="more_app_logo" name="more_app_logo" class="form-control tutorial-input" type="file" placeholder="Logo-file">
                        </span></div><div class="display_msg"></div>';

                    $data['res'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" >
                               </div>
                             </div>
                             <div class="modal-footer">
                                <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                               <button onclick="replace_manual_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                             </div>
                           </div>
                         </div>
                       </div>
                        <!-- END Popup -->';

                    $data['mres'] .= '</ul><input type="hidden" name="ssurlcount" id="ssurlcount" value="' . $sscnt . '" ><input id="more_app_sel_img" name="more_app_sel_img"  type="hidden" value="' . ($sscnt - 1) . '">  
                               </div>
                             </div>
                             <div class="modal-footer">
                                <div class="fl if_you_change">If you change any promotional images here, it will automatically change them in all other apps where this app is listed. You can make changes in an individual app by selecting this app within any individual app. System will always keep the latest changes based on the timestamp. Latest changes will be reflected within live system.</div>
                               <button onclick="replace_img()" class="btn btn-primary export save_changes" value="Save Changes" type="button" style=" width:120px;" data-loading-text="Loading...">Save changes</button>
                             </div>
                           </div>
                         </div>
                        </div>
                        <!-- END Popup -->';
                }
            }
        } else {
            $data = '';
        }
        $data['app_type'] = $app_type;
        if (empty($data['mres'])) {
            unset($data['mres']);
        }
        if (empty($data['res'])) {
            unset($data['res']);
        }
        if (empty($data['mires'])) {
            unset($data['mires']);
        }
        echo json_encode($data);
        die();
    }


    //Autocomplete more apps
    if ($_REQUEST['action'] == 'auto_more_apps' && isset($_REQUEST['more_app_name']) && isset($_REQUEST['app_type'])) {
        extract($_REQUEST);
        $res = $dclass->select("*", "tblmaster_more_apps", " AND more_app_name like '%" . $more_app_name . "%' AND member_id='" . $_SESSION['custid'] . "' AND os_type='" . $app_type . "' AND intid NOT IN(select more_app_id from tblmore_apps where  app_id = '" . $app_id . "') ");
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $data[$i]['name'] = $res[$i]['more_app_name'];
                $data[$i]['id'] = $res[$i]['intid'];
                $data[$i]['link'] = $res[$i]['more_app_lnk'];
            }
        } else {
            $data = '';
        }
        echo json_encode($data);
        die();
    }

    //Select app
    if ($_POST['action'] == 'select_app' && isset($_POST['sel_app_id'])) {
        extract($_POST);
        $_SESSION['sel_app_id'] = $sel_app_id;
        $_SESSION['app_type'] = $app_type;
        $_SESSION['slide_num'] = $slide_num;
        $data['output'] = 'S';
        $data['sel_app_id'] = $sel_app_id;

        echo json_encode($data);
        die();
    }

    //Select os
    if ($_POST['action'] == 'select_os' && isset($_POST['app_type'])) {

        extract($_POST);
        if ($app_type != 'all') {
            $_SESSION['app_type'] = $app_type;
            $data['type'] = 'app';
        } else {
            unset($_SESSION['app_type']);
            $data['type'] = 'all';
        }

        if ($site_url == 'apps' || $site_url == 'app-details' || $site_url == 'app-settings' || $site_url == 'notification' || $site_url == 'respond' || $site_url == 'respond-detail' || $site_url == 'help-faq-list' || $site_url == 'help-img-video' || $site_url == 'help-img-video-archive') {
            $who = '';
            if (isset($_SESSION['app_type'])) {
                $who .= "AND tblmember_apps.app_type='" . $_SESSION['app_type'] . "' ";
                $order_by = "tblmember_apps.intid DESC ";
            } else {
                $order_by = "app_type,tblmember_apps.intid DESC";
            }
            if ($q != '')
                $who .= "AND tblmember_apps.app_name LIKE '%" . $q . "%'";

            if ($_REQUEST['list'] == 'help' || $_REQUEST['site_url'] == 'help-faq-list' || $_REQUEST['site_url'] == 'help-img-video' || $_REQUEST['site_url'] == 'help-img-video-archive') {
                $res = $dclass->select("tblmember_apps.*", "tblmember_apps, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='4' AND tblmember_apps.member_id='" . $member_id . "' AND tblmember_apps.intid!='" . $_REQUEST['sel_app_id'] . "' $who ORDER BY $order_by ");
            } else if ($_REQUEST['site_url'] == 'respond' || $_REQUEST['site_url'] == 'respond-detail') {
                if ($_REQUEST['site_url'] == 'respond') {
                    foreach ($_SESSION['app_id'] as $val_app_id) {
                        $append_app_id .= " AND tblmember_apps.intid!='" . $val_app_id . "'";
                    }
                }

                if ($member_id == $_SESSION['agents_cust_id']) {
                    $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
                } else {
                    $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
                }

                $res = $dclass->select("tblmember_apps.*, tblapp_support.intid as support_id", "tblmember_apps, tblapp_support, tblmember_app_features", " AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status='running' AND feature_id='3' AND tblmember_apps.intid = tblapp_support.app_id " . $append_data . " and request_id = 0 $append_app_id AND tblmember_apps.intid!='" . $_REQUEST['sel_app_id'] . "' $who group by intid ORDER BY tblmember_apps.intid  DESC ");
            } else {
                $res = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' AND tblmember_apps.intid!='" . $_REQUEST['sel_app_id'] . "' $who ORDER BY $order_by  ");
            }
            if ($_REQUEST['site_url'] == 'respond') {
                $data['apps'] = '<input type="hidden" id="app_count_data" value="' . count($res) . '">';
            } else {
                $data['apps'] = '';
            }
            if (count($res) > 0) {
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
                    if ($_REQUEST['list'] == 'apps' || $_REQUEST['site_url'] == 'app-settings') {

                        $site_url_redirect = 'app-details';
                    } else if ($_REQUEST['list'] == 'help' || $_REQUEST['site_url'] == 'help-faq-list' || $_REQUEST['site_url'] == 'help-img-video' || $_REQUEST['site_url'] == 'help-img-video-archive') {

                        $resupdd = $dclass->select("version,intid, record_status", "tblapp_tutorial_settings", " AND app_id='" . $res[$i]['intid'] . "' AND (record_status='running' OR record_status='prev') ");

                        if (count($resupdd) > 0) {
                            $site_url_redirect = 'help-img-video';
                            $append_live = '&sel=faq&live';
                        } else {
                            $site_url_redirect = 'help-img-video';
                            $append_live = '&sel=faq&live';
                        }
                    } else if ($_REQUEST['site_url'] == 'respond-detail') {
                        $site_url_redirect = 'respond-detail';
                    } else if ($_REQUEST['site_url'] == 'respond') {
                        $site_url_redirect = 'respond';
                    }

                    if ($_REQUEST['list'] != '')
                        $div_class = 'col-md-2';
                    else
                        $div_class = 'col-md-3';

                    if (!empty($_REQUEST['support_id'])) {
                        $support_id = '&support_id=' . $_REQUEST['support_id'];
                    }
                    if ($_REQUEST['site_url'] == 'respond') {
                        $data['apps'] .= '<div onclick="hide_app(\'' . $res[$i]['intid'] . '\')" id="' . $res[$i]['intid'] . '" class="col-xs-6 ' . $div_class . '"><a href="javascript:;" class="thumbnail">';
                        $data['apps'] .= '<div class="logo-icon"><img src="' . $app_img_path . '" width="110" height="110" alt="" title="" /></div>';
                        $data['apps'] .= '<div class="logo-title"> ';
                        $data['apps'] .= '<i class="' . $app_type_img_path . '" title="">&nbsp;</i>';
                        $data['apps'] .= '<span class="' . $active_class . '"></span>';
                        $data['apps'] .= '<div class="cl"></div>';
                        $data['apps'] .= $res[$i]['app_name'] . '</div>';
                        $data['apps'] .= '</a></div>';
                    } else {
                        $data['apps'] .= '<div class="col-xs-6 ' . $div_class . '"><a href="' . $site_url_redirect . '?sel_app_id=' . $res[$i]["intid"] . $support_id . $append_live . '" class="thumbnail">';
                        $data['apps'] .= '<div class="logo-icon"><img src="' . $app_img_path . '" width="110" height="110" alt="" title="" /></div>';
                        $data['apps'] .= '<div class="logo-title"> ';
                        $data['apps'] .= '<i class="' . $app_type_img_path . '" title="">&nbsp;</i>';
                        $data['apps'] .= '<span class="' . $active_class . '"></span>';
                        $data['apps'] .= '<div class="cl"></div>';
                        $data['apps'] .= $res[$i]['app_name'] . '</div>';
                        $data['apps'] .= '</a></div>';
                    }
                }//for loop over
            } else {
                if ($_REQUEST['list'] != '' && $_REQUEST['list'] == 'apps') {
                    if (($no_record == 1 && $_SESSION['app_type'] != 'android') || ($no_ad_record == 1 && $_SESSION['app_type'] == 'android')) {
                        $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/click-plus-add-new-apps.png" alt="" title="" class="image_left" /></div></div></div></div>';
                    } else {
                        $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
                    }
                } else if ($_REQUEST['list'] != '' && $_REQUEST['list'] != 'apps') {
                    $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 click-to-add-apps"> <img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
                } else {
                    $data['apps'] .= '<div class="bk"><div class="col-lg-12"><div class="col-md-12 img-center" style="color:red;font-size:18px"><img src="img/no_apps_found.png" alt="" title="" /></div></div></div></div>';
                }
            }
        } else {
            unset($_SESSION['sel_app_id']);
            unset($_SESSION['slide_num']);
        }

        $data['site_url'] = $site_url;
        $data['output'] = 'S';
        echo json_encode($data);
        die();
    }

    // Save Multiple apps   
    if ($_POST['action'] == 'save_auto_apps') {
        extract($_POST);
        $wh = '';
        $chkf_enable_cnt = 0;
        for ($k = 0; $k < $_POST['app_count']; $k++) {

            $trackId = $_POST['trackId-' . $k];
            $app_store_id = $_POST['bundleId-' . $k];
            $company_name = $company_ajax_name;
            if ($app_type == 'ios') {
                $app_store_id_para = $trackId;
                $postfixs = $trackId;
                $postfix = $trackId . "-" . $k;
            } else {
                $app_store_id_para = str_replace(".", "_", $app_store_id);
                $postfixs = $app_store_id_para;
                $postfix = $app_store_id_para . "-" . $k;
            }
            if ($app_store_id_para == $sel_unique_id) {
                $pf_id = 0;
                $feature_id = 2;
                $payment_cost = 0;
                $payment_type = 'monthly';

                $feature_status = 'active';
                $app_name = $_POST['app_name-' . $postfix];
                $app_url = $_POST['app_url-' . $postfix];
                $app_store_logo = $_POST['app_logo-' . $postfix];

                //Check if both features are enabled
                if ($feature_status == 'active') {
                    $chkf_enable_cnt++;

                    //Check unique app name by store id
                    $chk = $dclass->select("intid", "tblmember_apps", "AND member_id='" . $member_id . "' AND app_type='" . $app_type . "' AND app_store_id='" . $app_store_id . "' $wh ");

                    //Check for same app
                    if (count($chk) > 0) {
                        $data['msg'] = $gnrl->getMessage('APP_EXIST', $lang_id);
                        $data['output'] = 'F';
                        echo json_encode($data);
                        die();
                    } else {
                        $tid = explode(".", $app_store_id);
                        if ($tid[2])
                            $app_para = $tid[2];
                        else if ($tid[1])
                            $app_para = $tid[1];
                        unset($ins);
                        $ins['member_id'] = $member_id;
                        $ins['company_name'] = $company_name;
                        $ins['or_app_name'] = $app_name;
                        $ins['app_name'] = $gnrl->subString($app_name, 30);
                        $ins['app_type'] = $app_type;

                        if ($app_store_logo) {
                            $content = $gnrl->getimg($app_store_logo);
                            $filename = time() . $app_para . '.png';
                            $save_path = APP_LOGO . '/' . $filename;
                            file_put_contents($save_path, $content);
                            $ins['app_logo'] = $filename;
                        }

                        $ins['app_key'] = $mcrypt->encrypt($member_id . "-" . $app_para) . $gnrl->randomLoginToken(4);
                        $ins['app_store_id'] = $app_store_id;
                        $ins['app_url'] = $app_url;
                        $ins['track_id'] = $trackId;
                        $ins['app_add_date'] = date("Y-m-d h:i:s");
                        $ins['app_mod_date'] = date("Y-m-d h:i:s");
                        $ins['app_status'] = 'active';
                        $ins['server_status'] = 'prod';

                        $id = $dclass->insert("tblmember_apps", $ins);
                        $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], APP_ADDED . ' ' . $ins['or_app_name']);

                        if ($id) {
                            //add more apps to this id
                            $add_more_app_to_id[$k] = $id;
                            unset($insf);
                            $fres = $dclass->select("intid", "tblfeatures", " AND status='active' ");

                            for ($f = 0; $f < count($fres); $f++) {
                                unset($insf);
                                $insf['member_id'] = $member_id;
                                $insf['app_id'] = $id;
                                $insf['feature_id'] = $fres[$f]['intid'];
                                $insf['transaction_id'] = 0;
                                $insf['pf_id'] = 0;
                                $insf['payment_type'] = 'monthly';
                                $insf['payment_cost'] = 0;
                                $insf['feature_status'] = "running";

                                $dclass->insert("tblmember_app_features", $insf);
                            }
                            $feature_id = 2;

                            //Promote other apps
                            unset($insp);
                            $insp['member_id'] = $member_id;
                            $insp['app_id'] = $id;

                            $insp['logo_flag'] = 'no';

                            $insp['title_text'] = "Our Other Apps";
                            $insp['font_color'] = '000000';
                            $insp['bck_color'] = 'ffffff';
                            $insp['font_family'] = 'Helvetica';
                            $insp['animation_id'] = 4;
                            $insp['status'] = 'publish';

                            $prid = $dclass->insert("tblmore_app_settings", $insp);
                            //if analytics enabled
                            if ($feature_status == 'active') {
                                //add this id as more app to other apps id
                                //add feature analytics
                                unset($insf);
                            }//analytics check over

                            if ($feature_status == 'active') {
                                $add_to_more_app_id[$k] = $id;
                            }

                            //check for existing same custom more app for the same member
                            $chkmr = $dclass->select("s.intid,i.image", "tblmore_apps s inner join tblmore_app_images i on s.intid=i.more_app_id", " AND s.member_id='" . $member_id . "' AND s.parent_app_id='0' AND s.more_app_lnk='" . $app_url . "' ");
                            if (count($chkmr) > 0) {
                                unset($insap);
                                $insap['parent_app_id'] = $id;
                                $insap['more_app_name'] = $gnrl->subString($app_name, 30);
                                ;
                                $insap['dtadd'] = date("Y-m-d h:i:s");
                                $insap['status'] = 'active';
                                $dclass->update("tblmore_apps", $insap, " intid='" . $chkmr[0]['intid'] . "' ");
                                $maid[$k] = $chkmr[0]['intid'];

                                //DELETE custom more app images of the same more app
                                for ($l = 0; $l < count($chkmr); $l++) {
                                    unlink(MORE_APPS_IMG . "/" . $chkmr[$l]['image']);
                                    unlink(MORE_APPS_IMG . "/thumbnails/" . $chkmr[$l]['image']);
                                }
                                $dclass->delete("tblmore_app_images", " more_app_id='" . $maid[$k] . "' ");
                                $dclass->delete("tblapp_moreapp_rel", " more_app_id='" . $maid[$k] . "' ");
                            } else {
                                //add to more apps 
                                //$add_as_more_app_id[$k] = $id;
                                //add master more apps
                                unset($insap);
                                $insap['member_id'] = $member_id;
                                $insap['parent_app_id'] = $id;
                                $insap['more_app_name'] = $gnrl->subString($app_name, 30);
                                $insap['more_app_lnk'] = $app_url;
                                $insap['dtadd'] = date("Y-m-d h:i:s");
                                $insap['status'] = 'active';

                                $maid[$k] = $dclass->insert("tblmore_apps", $insap);
                            }
                            if ($add_to_more_app_id[$k]) {
                                $more_app_id_for_analytics[$k] = $maid[$k];
                            }
                            //add store images   
                            $more_img_count = $_POST['ssurlcount-' . $postfix];
                            $more_app_img_active = $_POST['more_app_img-' . $postfix];

                            for ($j = 0; $j < $more_img_count; $j++) {
                                $ss_img[$j] = $_POST['ssh-' . $postfix . '-' . $j];

                                unset($insapi);
                                $insapi['more_app_id'] = $maid[$k];

                                if ($app_type == 'ios') {
                                    $content = $gnrl->getimg($ss_img[$j]);
                                    $filename = time() . 'more-app' . $app_para . $j . '.png';
                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                    $chku = file_put_contents($save_path, $content);
                                    if ($chku)
                                        $insapi['image'] = $filename;
                                }
                                else if ($app_type = 'android') {
                                    $chk = imagecreatefromjpeg($ss_img[$j]);
                                    $filename = time() . 'more-app' . $app_para . $j . '.png';
                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                    $chku = imagejpeg($chk, $save_path);

                                    if ($chku)
                                        $insapi['image'] = $filename;
                                }

                                $insapi['source'] = 'store';
                                if ($more_app_img_active != '' && $ss_img[$j] == $more_app_img_active) {
                                    $insapi['status'] = 'cover';
                                } else {
                                    $insapi['status'] = 'other';
                                }

                                $mrid = $dclass->insert("tblmore_app_images", $insapi);
                                if ($more_app_img_active != '' && $ss_img[$j] == $more_app_img_active) {
                                    $more_app_img_id[$maid[$k]] = $mrid;
                                }
                            }

                            //add more app image from file
                            $file_more_img = $_POST['more_app_new_img-' . $postfix];

                            //add more app image from file as cover image
                            if ($file_more_img != '') {
                                $more_upload_logo = $_FILES['more_app_logo-' . $postfix];
                                if ($more_upload_logo != '') {
                                    unset($insapi);
                                    $insapi['more_app_id'] = $maid[$k];
                                    $filename = time() . 'more-app' . $app_para . '.png';
                                    $des = MORE_APPS_IMG . "/" . $filename;
                                    if (move_uploaded_file($_FILES['more_app_logo-' . $postfix]['tmp_name'], $des)) {
                                        $insapi['image'] = $filename;
                                    }
                                    $insapi['source'] = 'custom';
                                    $insapi['status'] = 'cover';
                                    $insapi['cover_app_id'] = 0;
                                    $more_app_img_id[$maid[$k]] = $dclass->insert("tblmore_app_images", $insapi);
                                }
                            }
                        }
                    }
                }//Check for enabled features
            }//ACTIVE APP CHECK OVER
        }//For loop over
        //If all features disabled
        if ($chkf_enable_cnt == 0) {
            $data['msg'] = $gnrl->getMessage('ALL_FEATURES_DISABLED_APP_ADD_FAILED', $lang_id);
            $data['output'] = 'F';
        } else {
            $data['output'] = 'S';
            if ($app_type == 'ios')
                $data['no_record'] = 0;
            else
                $data['no_ad_record'] = 0;
            if ($script == 'add')
                $app_msg_status = "APP_ADD";
            else
                $app_msg_status = "APP_UPD";

            $data['msg'] = $gnrl->getMessage($app_msg_status, $lang_id);
        }
        $_SESSION['app_type'] = $app_type;
        $data['app_type'] = $app_type;
        echo json_encode($data);
        die();
    }

    // Save App
    if ($_POST['action'] == 'save_app') {

        extract($_POST);
        $wh = '';
        if ($script != 'add' && isset($_POST['intid'])) {
            $wh = " AND intid != '" . $intid . "' ";
        }

        //Check unique app name
        $chk = $dclass->select("intid", "tblmember_apps", "AND member_id='" . $member_id . "' AND app_type='" . $app_type . "' AND app_store_id='" . $app_store_id . "' $wh ");
        if (count($chk) > 0) {
            $data['output'] = 'F';
            $type = 'err';
            $app_msg_status = "APP_EXIST";
            $data['msg'] = $gnrl->getMessage($app_msg_status, $lang_id);
            echo json_encode($data);
            die();
        } 
        else {
            if ($script != 'add' && isset($_POST['intid'])) {
                //check for atleast one feature enabled
                $pf_id = array(2, 6, 8);
                $feature_status = 'active';
                if (in_array(2, $pf_id) || in_array(8, $pf_id) || $feature_status == 'active') {
                    $tid = explode(".", $app_store_id);
                    if ($tid[2])
                        $app_para = $tid[2];
                    else if ($tid[1])
                        $app_para = $tid[1];

                    unset($upd);
                    if ($company_name)
                        $upd['company_name'] = $company_name;
                    if ($or_app_name)
                        $upd['or_app_name'] = $or_app_name;
                    else
                        $upd['or_app_name'] = $app_name;
                    $upd['app_name'] = $gnrl->subString($app_name, 30);
                    $upd['app_type'] = $app_type;

                    if (isset($_FILES['app_logo'])) {
                        $filename = time() . $gnrl->makefilename($app_para) . ".jpg";
                        $des = APP_LOGO . "/" . $filename;
                        if (move_uploaded_file($_FILES['app_logo']['tmp_name'], $des)) {
                            unlink(APP_LOGO . "/" . $old_app_logo);
                            unlink(APP_LOGO . "/thumbnail/" . $old_app_logo);
                            $upd['app_logo'] = $filename;
                        }
                    }

                    if (isset($_FILES['company_logo'])) {
                        $expname = explode('.', $_FILES['company_logo']['name']);
                        $filename = time() . $gnrl->makefilename($expname[0]) . ".jpg";
                        $des = COMPANY_LOGO . "/" . $filename;
                        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $des)) {
                            unlink(COMPANY_LOGO . "/" . $old_app_logo);
                            unlink(COMPANY_LOGO . "/thumbnail/" . $old_app_logo);
                            $upd['company_logo'] = $filename;
                        }
                    }

                    if ($app_store_logo) {
                        unlink(APP_LOGO . "/" . $old_app_logo);
                        unlink(APP_LOGO . "/thumbnail/" . $old_app_logo);
                        $content = $gnrl->getimg($app_store_logo);
                        $filename = time() . $app_para . '.png';
                        $save_path = APP_LOGO . '/' . $filename;
                        file_put_contents($save_path, $content);
                        $upd['app_logo'] = $filename;
                    }
                    $upd['app_store_id'] = $app_store_id;
                    $upd['app_url'] = $app_url;
                    $upd['track_id'] = $track_id;
                    $upd['app_status'] = 'active';
                    $upd['server_status'] = $server_status;
                    if ($total_payment)
                        $upd['total_payment'] = trim($total_payment);
                    $upd['app_mod_date'] = date("Y-m-d h:i:s");

                    //if promotr subscription changed
                    if ($pchanged == 1) {
                        $chkb = $dclass->select("next_billing_date,total_payment", "tblmember_apps", "AND member_id='" . $member_id . "' AND intid='" . $intid . "' ");

                        //check if promotr enabled previously
                        $chkf = $dclass->select("1", "tblmember_app_features", "AND member_id='" . $member_id . "' AND app_id='" . $intid . "' AND (pf_id='2' OR pf_id='8') ");
                        //Promotr disabled
                        if (!in_array(2, $pf_id) && !in_array(8, $pf_id)) {
                            $upd['current_billing_end_date'] = date("Y-m-d", strtotime('-1 day', strtotime($chkb[0]['next_billing_date'])));
                            $upd['current_payment'] = $chkb[0]['total_payment'];
                        } else {
                            if ($payment_type == 'monthly' && in_array(2, $pf_id)) {
                                if (count($chkf) > 0) {
                                    $upd['current_billing_end_date'] = date("Y-m-d", strtotime('-1 day', strtotime($chkb[0]['next_billing_date'])));
                                    $upd['current_payment'] = $chkb[0]['total_payment'];
                                } else {
                                    $upd['next_billing_date'] = date("Y-m-d", strtotime("+1 month"));
                                }
                            } else if ($payment_type == 'yearly' && in_array(8, $pf_id)) {
                                if (count($chkf) > 0) {
                                    $upd['current_billing_end_date'] = date("Y-m-d", strtotime('-1 day', strtotime($chkb[0]['next_billing_date'])));
                                    $upd['current_payment'] = $chkb[0]['total_payment'];
                                } else {
                                    $upd['next_billing_date'] = date("Y-m-d", strtotime("+1 year"));
                                }
                            }
                        }
                    }
                    $dclass->update("tblmember_apps", $upd, " intid='" . $intid . "' ");
                    if (count($pf_id) > 0) { //delete disabled promotr features
                        if (!in_array(2, $pf_id)) {
                            $dclass->delete('tblmember_app_features', " member_id='" . $member_id . "' AND  app_id = '" . $intid . "' AND pf_id='2'  ");
                        }
                        if (!in_array(8, $pf_id)) {
                            $dclass->delete('tblmember_app_features', " member_id='" . $member_id . "' AND  app_id = '" . $intid . "' AND pf_id='8'  ");
                        }
                    }

                    //Delete analyzr feature if not active
                    if ($feature_status != 'active') {
                        $chkp = $dclass->delete('tblmember_app_features', " member_id='" . $member_id . "' AND  app_id = '" . $intid . "' AND feature_id='" . 6 . "' ");
                    }
                    for ($i = 0; $i < count($pf_id); $i++) {
                        $feature_id = $_POST['feature_id-' . $pf_id[$i]];
                        if ($pf_id[$i] != '' && $pf_id[$i] != 6 || ($pf_id[$i] == 6 && $feature_status == 'active')) {
                            $chkf = $dclass->select('intid', 'tblmember_app_features', " AND member_id='" . $member_id . "' AND app_id = '" . $intid . "'  AND pf_id='" . $pf_id[$i] . "'  ");
                            if (count($chkf) <= 0) {

                                unset($insf);
                                $insf['member_id'] = $member_id;
                                $insf['app_id'] = $intid;
                                $insf['feature_id'] = $feature_id;
                                $insf['transaction_id'] = 0;
                                $insf['pf_id'] = $pf_id[$i];
                                $insf['payment_type'] = $payment_type;
                                $insf['payment_cost'] = $_POST['price-' . $pf_id[$i]];
                                $insf['feature_status'] = "running";

                                $fid = $dclass->insert("tblmember_app_features", $insf);
                            }
                        }//analytics check over
                        //check if this app is more app
                        $chkm = $dclass->select("intid", "tblmore_apps", " AND member_id='" . $member_id . "' AND parent_app_id='" . $intid . "' ");

                        //check for promotr enabled
                        $chkp = $dclass->select("a.intid", "tblmember_apps a inner join tblmember_app_features f on a.intid=f.app_id", " AND  f.member_id='" . $member_id . "' AND  f.app_id = '" . $intid . "' AND (f.feature_id='2' OR f.feature_id='8')");

                        //If promotr enabled store its promotr settings(if not exist) and more app ids of other apps
                        if (count($chkp) > 0) {
                            //check for promotr general settings
                            $chkg = $dclass->select("intid", "tblmore_app_settings", " AND app_id='" . $intid . "' ");
                            if (count($chkg) <= 0) {
                                $inss['member_id'] = $member_id;
                                $inss['app_id'] = $intid;

                                $inss['logo_flag'] = 'no';

                                $inss['title_text'] = "Our Other Apps";
                                $inss['font_color'] = '000000';
                                $inss['bck_color'] = 'ffffff';
                                $inss['font_family'] = 'Helvetica';
                                $inss['animation_id'] = 4;
                                $inss['status'] = 'publish';

                                $prid = $dclass->insert("tblmore_app_settings", $inss);
                            }
                            $gnrl->add_more_app_for_new_app($intid, $member_id, $app_type);
                        } else { //delete relation records of this app if promotr disabled
                            $dres = $dclass->select("more_app_custom_image as img", "tblapp_moreapp_rel", " AND app_id='" . $intid . "'");
                            if (count($dres) > 0) {
                                for ($l = 0; $l < count($dres); $l++) {
                                    if ($dres[$l]['img'] != '') {
                                        unlink(MORE_APPS_IMG . "/" . $dres[$l]['img']);
                                    }
                                }
                            }
                            $dclass->delete("tblapp_moreapp_rel", " app_id='" . $intid . "' ");
                        }

                        //if promtr or analytics enabled store this app as more app for other apps
                        if (count($chkp) > 0 || $feature_status == 'active') {

                            //check if already added this as more app
                            if (count($chkm) > 0) {
                                //update more app name and link
                                unset($insap);
                                $insap['more_app_name'] = $gnrl->subString($app_name, 30);
                                $insap['more_app_lnk'] = $app_url;
                                $dclass->update("tblmore_apps", $insap, " intid='" . $chkm[0]['intid'] . "' ");


                                $more_app_id = $chkm[0]['intid'];

                                $mires = $dclass->select('intid,image', "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND source = 'store' ");

                                if (count($mires) > 0) {
                                    //update more app cover image from store
                                    if ($more_app_sel_img && is_numeric($more_app_sel_img) && $more_app_sel_img != 0) {
                                        $miires = $dclass->select('intid,source', "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND intid='" . $more_app_sel_img . "' ");

                                        if (count($miires) > 0) {
                                            $updallimg['status'] = 'other';
                                            $dclass->update("tblmore_app_images", $updallimg, " more_app_id='" . $more_app_id . "' ");

                                            if ($miires[0]['source'] == 'custom') {

                                                //Delete individual more apps custom images
                                                for ($m = 0; $m < count($chkr); $m++) {
                                                    unlink(MORE_APPS_IMG . "/" . $ckhr[$m]['img']);
                                                    unlink(MORE_APPS_IMG . "/thumbnails/" . $ckhr[$m]['img']);
                                                }
                                                $updmr['more_app_img_id'] = $miires[0]['intid'];
                                                $updmr['more_app_custom_image'] = '';
                                                $dclass->update("tblapp_moreapp_rel", $updmr, " member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "' ");
                                            } else {
                                                $updmr['more_app_img_id'] = $miires[0]['intid'];
                                                $dclass->update("tblapp_moreapp_rel", $updmr, " more_app_id='" . $more_app_id . "' ");
                                            }
                                            $updimg['status'] = 'cover';
                                            $dclass->update("tblmore_app_images", $updimg, " intid='" . $miires[0]['intid'] . "' ");
                                        }
                                    } else if ($more_app_sel_img && !is_numeric($more_app_sel_img)) {    //For refreshed images
                                        //UPDATE latest screenshot images
                                        if ($ssurlcount > 0) {
                                            for ($i = 0; $i < $ssurlcount; $i++) {
                                                $more_app_img = $_POST['ssh-' . $i];

                                                if ($app_type == 'ios') {
                                                    $content = $gnrl->getimg($more_app_img);
                                                    $filename = time() . $app_para . '-promo' . $i . '.png';
                                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                                    $chk = file_put_contents($save_path, $content);
                                                } else if ($app_type == 'android') {
                                                    $content = $gnrl->getimg(SITE_URL . $more_app_img);
                                                    $filename = time() . $app_para . '-promo' . $i . '.png';
                                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                                    $chk = file_put_contents($save_path, $content);
                                                }
                                                $maid[$i] = $mires[$i]['intid'];

                                                if ($maid[$i]) { //UPDATE
                                                    unlink(MORE_APPS_IMG . "/" . $mires[$i]['image']);
                                                    $updai['image'] = $filename;
                                                    if ($more_app_img == $more_app_sel_img) {
                                                        $more_app_img_id = $maid[$i];
                                                        $updai['status'] = 'cover';
                                                        unset($updaic);
                                                        $updaic['status'] = 'other';
                                                        $dclass->update("tblmore_app_images", $updaic, " more_app_id='" . $more_app_id . "' AND source='custom' ");
                                                    } else {
                                                        $updai['status'] = 'other';
                                                    }
                                                    $dclass->update("tblmore_app_images", $updai, " intid='" . $maid[$i] . "' ");
                                                } else { //INSERT
                                                    unset($insai);
                                                    $insai['more_app_id'] = $more_app_id;
                                                    $insai['image'] = $filename;
                                                    $insai['source'] = 'store';
                                                    if ($more_app_img == $more_app_sel_img) {
                                                        $insai['status'] = 'cover';
                                                        unset($updaic);
                                                        $updaic['status'] = 'other';
                                                        $dclass->update("tblmore_app_images", $updaic, " more_app_id='" . $more_app_id . "' AND source='custom' ");
                                                    } else {
                                                        $insai['status'] = 'other';
                                                    }
                                                    $maid[$i] = $dclass->insert("tblmore_app_images", $insai);
                                                    if ($more_app_img == $more_app_sel_img) {
                                                        $more_app_img_id = $maid[$i];
                                                    }
                                                }
                                            }//for loop over

                                            if (!$more_app_img_id) {
                                                $more_app_img_id = $maid[0];
                                            }

                                            //change feature more app image of this app for all other apps
                                            $updmr['more_app_img_id'] = $more_app_img_id;
                                            $dclass->update("tblapp_moreapp_rel", $updmr, " more_app_id='" . $more_app_id . "' ");
                                        }
                                    } else {
                                        //UPDATE latest screenshot images
                                        if ($ssurlcount > 0) {
                                            $cust_img_flg = 0;
                                            for ($i = 0; $i < $ssurlcount; $i++) {
                                                $more_app_img = $_POST['ssh-' . $i];

                                                if ($app_type == 'ios') {
                                                    $content = $gnrl->getimg($more_app_img);
                                                    $filename = time() . $app_para . '-promo' . $i . '.png';
                                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                                    $chk = file_put_contents($save_path, $content);
                                                } else if ($app_type == 'android') {
                                                    $content = $gnrl->getimg(SITE_URL . $more_app_img);
                                                    $filename = time() . $app_para . '-promo' . $i . '.png';
                                                    $save_path = MORE_APPS_IMG . '/' . $filename;
                                                    $chk = file_put_contents($save_path, $content);
                                                }
                                                $maid[$i] = $mires[$i]['intid'];

                                                if ($maid[$i]) { //UPDATE
                                                    unlink(MORE_APPS_IMG . "/" . $mires[$i]['image']);
                                                    $updai['image'] = $filename;
                                                    if ($more_app_img == $more_app_sel_img) {
                                                        $more_app_img_id = $more_app_sel_img;
                                                        $cust_img_flg = 1;
                                                        $updai['status'] = 'cover';
                                                    } else {
                                                        $updai['status'] = 'other';
                                                    }
                                                    $dclass->update("tblmore_app_images", $updai, " intid='" . $maid[$i] . "' ");
                                                } else { //INSERT
                                                    unset($insai);
                                                    $insai['more_app_id'] = $more_app_id;
                                                    $insai['image'] = $filename;
                                                    $insai['source'] = 'store';
                                                    if ($more_app_img == $more_app_sel_img) {
                                                        $more_app_img_id = $more_app_sel_img;
                                                        $cust_img_flg = 1;
                                                        $insai['status'] = 'cover';
                                                    } else {
                                                        $insai['status'] = 'other';
                                                    }
                                                    $maid[$i] = $dclass->insert("tblmore_app_images", $insai);
                                                }
                                            }//for loop over

                                            if ($more_app_sel_img == 0) {
                                                $rescus = $dclass->select("intid", "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND source='custom'");

                                                if (count($rescus) > 0) {
                                                    $more_app_img_id = $rescus[0]['intid'];
                                                    unset($updaic);
                                                    $updaic['status'] = 'cover';
                                                    $dclass->update("tblmore_app_images", $updaic, " more_app_id='" . $more_app_id . "' AND source='custom' ");
                                                } else
                                                    $more_app_img_id = 0;
                                            }

                                            if (!$more_app_img_id) {
                                                $more_app_img_id = $maid[0];
                                            }

                                            //change feature more app image of this app for all other apps
                                            $updmr['more_app_img_id'] = $more_app_img_id;
                                            $dclass->update("tblapp_moreapp_rel", $updmr, " more_app_id='" . $more_app_id . "' ");
                                        }
                                    }
                                } else {
                                    //more app images
                                    if ($ssurlcount > 0) {
                                        for ($i = 0; $i < $ssurlcount; $i++) {
                                            $more_app_img = $_POST['ssh-' . $i];
                                            if ($app_type == 'ios') {
                                                $content = $gnrl->getimg($more_app_img);
                                                $filename = time() . $app_para . '-promo' . $i . '.png';
                                                $save_path = MORE_APPS_IMG . '/' . $filename;
                                                $chk = file_put_contents($save_path, $content);
                                            } else if ($app_type == 'android') {
                                                $content = $gnrl->getimg(SITE_URL . $more_app_img);
                                                $filename = time() . $app_para . '-promo' . $i . '.png';
                                                $save_path = MORE_APPS_IMG . '/' . $filename;
                                                $chk = file_put_contents($save_path, $content);
                                            }
                                            if ($chk) {
                                                unset($insai);
                                                $insai['more_app_id'] = $more_app_id;
                                                $insai['image'] = $filename;
                                                $insai['source'] = 'store';
                                                if ($more_app_img == $more_app_sel_img) {
                                                    $insai['status'] = 'cover';
                                                } else {
                                                    $insai['status'] = 'other';
                                                }
                                                $maid[$i] = $dclass->insert("tblmore_app_images", $insai);
                                                if ($more_app_img == $more_app_sel_img) {
                                                    $more_app_img_id = $maid[$i];
                                                }
                                            }
                                        }//for loop over

                                        if (!$more_app_img_id) {
                                            $more_app_img_id = $maid[0];
                                        }
                                        $updallimg['status'] = 'other';
                                        $dclass->update("tblmore_app_images", $updallimg, " more_app_id='" . $more_app_id . "' AND source='custom' ");
                                    }
                                }//check if store based more app images exists
                                //delete more app custom image 
                                if ($more_app_store_logo && $old_more_app_sel_new_img) {
                                    
                                }
                                //update more app cover image custom from loan source
                                if ($more_app_sel_new_img && isset($_FILES['more_app_logo'])) {

                                    // add new custom image
                                    $filename = time() . $gnrl->makefilename($app_name . '-promo') . ".jpg";
                                    $des = MORE_APPS_IMG . "/" . $filename;

                                    if (move_uploaded_file($_FILES['more_app_logo']['tmp_name'], $des)) {

                                        $updallimgc['status'] = 'other';
                                        $dclass->update("tblmore_app_images", $updallimgc, " more_app_id='" . $more_app_id . "' ");
                                        //check if custom image exists
                                        $chkmr = $dclass->select("intid", "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND source='custom'");
                                        if (count($chkmr) <= 0) {
                                            unset($insai);
                                            $insai['more_app_id'] = $more_app_id;
                                            $insai['image'] = $filename;
                                            $insai['source'] = 'custom';
                                            if ($more_app_sel_new_img) {
                                                $insai['status'] = 'cover';
                                            } else {
                                                $insai['status'] = 'other';
                                            }
                                            $insai['cover_app_id'] = 0;
                                            $more_app_img_id = $dclass->insert("tblmore_app_images", $insai);

                                            if ($more_app_img_id && $server_status == 'prod') {
                                                $updor['more_app_img_id'] = $more_app_img_id;
                                                $dclass->update("tblapp_moreapp_rel", $updor, " more_app_id = '" . $more_app_id . "' ");
                                            }
                                        } else {
                                            unlink(MORE_APPS_IMG . "/" . $old_more_app_sel_new_img);
                                            unlink(MORE_APPS_IMG . "/thumbnails/" . $old_more_app_sel_new_img);

                                            unset($updai);
                                            $updai['image'] = $filename;
                                            if ($more_app_sel_new_img) {
                                                $updai['status'] = 'cover';
                                            } else {
                                                $updai['status'] = 'other';
                                            }
                                            $dclass->update("tblmore_app_images", $updai, " intid='" . $chkmr[0]['intid'] . "' ");

                                            $more_app_img_id = $chkmr[0]['intid'];

                                            $updor['more_app_img_id'] = $more_app_img_id;
                                            $dclass->update("tblapp_moreapp_rel", $updor, " more_app_id = '" . $more_app_id . "' ");
                                        }
                                    }//check for uploaded file
                                } else {
                                    //change feature more app image of this app for all other apps
                                    if ($more_app_img_id) {
                                        $updor['more_app_img_id'] = $more_app_img_id;
                                        $dclass->update("tblapp_moreapp_rel", $updor, " more_app_id = '" . $more_app_id . "' ");
                                    }
                                }
                                //update more app cover image custom from multiple images
                                if ($more_app_sel_new_img && isset($_FILES['more_app_img'])) {

                                    //check for existing custom image
                                    $mires = $dclass->select('intid', "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND source='custom' ");

                                    if (count($mires) > 0) {
                                        $filename = time() . $gnrl->makefilename($app_para - 'promo') . ".jpg";
                                        $des = MORE_APPS_IMG . "/" . $filename;
                                        if (move_uploaded_file($_FILES['more_app_img']['tmp_name'], $des)) {
                                            $updallimgc['status'] = 'other';
                                            $dclass->update("tblmore_app_images", $updallimgc, " more_app_id='" . $more_app_id . "' ");

                                            unlink(MORE_APPS_IMG . "/" . $old_more_app_sel_new_img);
                                            unlink(MORE_APPS_IMG . "/thumbnails/" . $old_more_app_sel_new_img);
                                            unset($updai);
                                            $updai['image'] = $filename;
                                            if ($more_app_sel_new_img) {
                                                $updai['status'] = 'cover';
                                            } else {
                                                $updai['status'] = 'other';
                                            }
                                            $dclass->update("tblmore_app_images", $updai, " intid='" . $mires[0]['intid'] . "'");
                                            $chkr = $dclass->select("intid,more_app_custom_image as img", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "'   ");
                                            if (count($chkr) <= 0 && $mires[0]['intid']) {

                                                $admr['member_id'] = $member_id;
                                                $admr['app_id'] = $app_id;
                                                $admr['more_app_id'] = $more_app_id;
                                                $admr['more_app_img_id'] = $mires[0]['intid'];
                                                $dclass->insert("tblapp_moreapp_rel", $admr);
                                            } else if (count($chkr) > 0 && $mires[0]['intid']) {
                                                //Delete individual more apps custom images
                                                for ($m = 0; $m < count($chkr); $m++) {
                                                    unlink(MORE_APPS_IMG . "/" . $ckhr[$m]['img']);
                                                }
                                                $updmr['more_app_img_id'] = $mires[0]['intid'];
                                                $updmr['more_app_custom_image'] = '';
                                                $dclass->update("tblapp_moreapp_rel", $updmr, " member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "' ");
                                            }
                                        }
                                    } else { // add new custom image
                                        $filename = time() . $gnrl->makefilename($app_para - 'promo') . ".jpg";
                                        $des = MORE_APPS_IMG . "/" . $filename;
                                        if (move_uploaded_file($_FILES['more_app_img']['tmp_name'], $des)) {
                                            unset($insai);
                                            $insai['more_app_id'] = $more_app_id;
                                            $insai['image'] = $filename;
                                            $insai['source'] = 'custom';
                                            if ($more_app_sel_new_img) {
                                                $insai['status'] = 'cover';
                                            } else {
                                                $insai['status'] = 'other';
                                            }
                                            $insai['cover_app_id'] = 0;
                                            $more_app_img_id = $dclass->insert("tblmore_app_images", $insai);
                                            $chkr = $dclass->select("intid,more_app_custom_image as img", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "'   ");
                                            if (count($chkr) <= 0 && $more_app_img_id) {

                                                $admr['member_id'] = $member_id;
                                                $admr['app_id'] = $app_id;
                                                $admr['more_app_id'] = $more_app_id;
                                                $admr['more_app_img_id'] = $more_app_img_id;
                                                $rel_id = $dclass->insert("tblapp_moreapp_rel", $admr);
                                                $updor['intorder'] = $rel_id;
                                                $dclass->update("tblapp_moreapp_rel", $updor, " intid = '" . $rel_id . "' ");
                                            } else if (count($chkr) > 0 && $more_app_img_id) {
                                                //Delete individual more apps custom images
                                                for ($m = 0; $m < count($chkr); $m++) {
                                                    unlink(MORE_APPS_IMG . "/" . $ckhr[$m]['img']);
                                                }
                                                $updmr['more_app_img_id'] = $more_app_img_id;
                                                $updmr['more_app_custom_image'] = '';
                                                $dclass->update("tblapp_moreapp_rel", $updmr, " member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "' ");
                                            }
                                        }//check for uploaded file
                                    }
                                } else {
                                    $chkr = $dclass->select("intid,more_app_custom_image as img", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "'   ");

                                    if (count($chkr) > 0 && $more_app_img_id) {
                                        unset($udmr);
                                        $udmr['more_app_img_id'] = $more_app_img_id;
                                        $dclass->update("tblapp_moreapp_rel", $udmr, " AND member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "'   ");
                                    } else {
                                        //change feature more app image of this app for all other apps
                                        if ($more_app_img_id) {
                                            $admr['member_id'] = $member_id;
                                            $admr['app_id'] = $app_id;
                                            $admr['more_app_id'] = $more_app_id;
                                            $admr['more_app_img_id'] = $more_app_img_id;
                                            $rel_id = $dclass->insert("tblapp_moreapp_rel", $admr);
                                            $updor['intorder'] = $rel_id;
                                            $dclass->update("tblapp_moreapp_rel", $updor, " intid = '" . $rel_id . "' ");
                                        }
                                    }
                                }

                                if ($server_status == 'prod') {
                                    //add this app as more app for other promotr enabled apps with production status
                                    $gnrl->add_more_app_to_new_app($more_app_id, $member_id, $app_type);
                                } else {
                                    //delete this more app for other promotr enabled apps with development status
                                    $gnrl->delete_more_app($more_app_id);
                                }
                            } else { //if no more app exists INSERT
                                //check for existing same custom more app for the same member
                                $chkmr = $dclass->select("s.intid,i.image", "tblmore_apps s inner join tblmore_app_images i on s.intid=i.more_app_id", " AND s.member_id='" . $member_id . "' AND s.parent_app_id='0' AND s.more_app_lnk='" . $app_url . "' ");
                                if (count($chkmr) > 0) {
                                    unset($insap);
                                    $insap['parent_app_id'] = $id;
                                    $insap['more_app_name'] = $gnrl->subString($app_name, 30);
                                    $insap['dtadd'] = date("Y-m-d h:i:s");
                                    $insap['status'] = 'active';
                                    $dclass->update("tblmore_apps", $insap, " intid='" . $chkmr[0]['intid'] . "' ");

                                    $more_app_id = $chkmr[0]['intid'];

                                    //DELETE custom more app images of the same more app
                                    for ($k = 0; $k < count($chkmr); $k++) {
                                        unlink(MORE_APPS_IMG . "/" . $chkmr[$k]['image']);
                                        unlink(MORE_APPS_IMG . "/thumbnails/" . $chkmr[$k]['image']);
                                    }
                                    $dclass->delete("tblmore_app_images", " more_app_id='" . $more_app_id . "' ");
                                    $dclass->delete("tblapp_moreapp_rel", " more_app_id='" . $more_app_id . "' ");
                                } else {
                                    unset($insa);
                                    $insa['member_id'] = $member_id;
                                    $insa['parent_app_id'] = $id;
                                    $insa['more_app_name'] = $gnrl->subString($app_name, 30);
                                    $insa['more_app_lnk'] = $app_url;
                                    $insa['dtadd'] = date("Y-m-d h:i:s");
                                    $insa['status'] = 'active';
                                    $more_app_id = $dclass->insert("tblmore_apps", $insa);
                                }

                                if ($ssurlcount > 0) {
                                    //more app images
                                    for ($i = 0; $i < $ssurlcount; $i++) {
                                        $more_app_img = $_POST['ssh-' . $i];
                                        if ($app_type == 'ios') {
                                            $content = $gnrl->getimg($more_app_img);
                                            $filename = time() . $app_para . '-promo' . $i . '.png';
                                            $save_path = MORE_APPS_IMG . '/' . $filename;
                                            $chk = file_put_contents($save_path, $content);
                                        } else if ($app_type == 'android') {
                                            $chk = imagecreatefromjpeg($more_app_img);
                                            $filename = time() . $app_para . '-promo' . $i . '.png';
                                            $save_path = MORE_APPS_IMG . '/' . $filename;
                                            imagejpeg($chk, $save_path);
                                        }
                                        if ($chk) {
                                            unset($insai);
                                            $insai['more_app_id'] = $more_app_id;
                                            $insai['image'] = $filename;
                                            $insai['source'] = 'store';
                                            if ($more_app_img == $more_app_sel_img) {
                                                $insai['status'] = 'cover';
                                            } else {
                                                $insai['status'] = 'other';
                                            }
                                            $maid = $dclass->insert("tblmore_app_images", $insai);
                                            if ($more_app_img == $more_app_sel_img) {
                                                $more_app_img_id = $maid;
                                            }
                                        }
                                    }
                                    if (!$more_app_img_id) {
                                        $more_app_img_id = $maid[0];
                                    }
                                }

                                //custom more app image

                                if ($more_app_sel_new_img && $_FILES['more_app_logo']) {
                                    $filename = time() . $gnrl->makefilename($app_para - 'promo') . ".jpg";
                                    $des = MORE_APPS_IMG . "/" . $filename;
                                    if (move_uploaded_file($_FILES['more_app_logo']['tmp_name'], $des)) {
                                        unset($insai);
                                        $insai['more_app_id'] = $more_app_id;
                                        $insai['image'] = $filename;
                                        $insai['source'] = 'custom';
                                        $insai['status'] = 'cover';
                                        $insai['cover_app_id'] = 0;
                                        $more_app_img_id = $dclass->insert("tblmore_app_images", $insai);
                                    }
                                }

                                $mires = $dclass->select("intid", "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' AND source='store' LIMIT 1 ");
                                $more_app_img_id = $mires[0]['intid'];

                                //add this newly added more app for all promotr enabled apps     
                                $mres = $dclass->select("app_id, more_app_id", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND app_id != '" . $intid . "' GROUP BY app_id ");
                                for ($i = 0; $i < count($mres); $i++) {
                                    $app_id = $mres[$i]['app_id'];

                                    unset($insaa);
                                    unset($updor);
                                    $insaa['member_id'] = $member_id;
                                    $insaa['app_id'] = $app_id;
                                    $insaa['more_app_id'] = $more_app_id;
                                    $insaa['more_app_img_id'] = $more_app_img_id;
                                    $rel_id = $dclass->insert("tblapp_moreapp_rel", $insaa);
                                    $updor['intorder'] = $rel_id;
                                    $dclass->update("tblapp_moreapp_rel", $updor, " intid = '" . $rel_id . "' ");
                                }
                            } //else part if not exist
                        }//more app plus analytics over
                    }
                    $data['output'] = 'S';

                    if ($script == 'add') {

                        $app_msg_status = "APP_ADD";
                    } else {
                        $app_msg_status = "APP_UPD";
                        $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], APP_UPDATED . ' ' . $upd['or_app_name']);
                    }
                } else {
                    $app_msg_status = 'ALL_FEATURES_DISABLED_APP_UPD_FAILED';
                    $data['output'] = 'F';
                }
            } else { // INSERT
                $pf_id = array(2, 8);
                $feature_status = 'active';

                //check for at least one feature enabled
                if (in_array(2, $pf_id) || in_array(8, $pf_id) || $feature_status == 'active') {

                    $tid = explode(".", $app_store_id);
                    if ($tid[2])
                        $app_para = $tid[2];
                    else if ($tid[1])
                        $app_para = $tid[1];

                    unset($ins);
                    $ins['member_id'] = $member_id;
                    if ($company_name)
                        $ins['company_name'] = $company_name;

                    $ins['or_app_name'] = $app_name;
                    $ins['app_name'] = $gnrl->subString($app_name, 30);
                    $ins['app_type'] = $app_type;

                    if (isset($_FILES['app_logo'])) {
                        $filename = time() . $gnrl->makefilename($app_para) . ".jpg";
                        $des = APP_LOGO . "/" . $filename;
                        if (move_uploaded_file($_FILES['app_logo']['tmp_name'], $des)) {
                            $ins['app_logo'] = $filename;
                        }
                    } else if ($app_store_logo) {
                        $content = $gnrl->getimg($app_store_logo);
                        $filename = time() . $app_para . '.png';
                        $save_path = APP_LOGO . '/' . $filename;
                        file_put_contents($save_path, $content);
                        $ins['app_logo'] = $filename;
                    }
                    $ins['app_key'] = $mcrypt->encrypt($member_id . "-" . $tid[2]) . $gnrl->randomLoginToken(4);
                    $ins['app_store_id'] = $app_store_id;
                    $ins['app_url'] = $app_url;
                    $ins['track_id'] = $track_id;
                    $ins['app_add_date'] = date("Y-m-d h:i:s");
                    $ins['app_mod_date'] = date("Y-m-d h:i:s");
                    $ins['app_status'] = 'active';

                    $ins['server_status'] = 'dev';
                    if ($total_payment)
                        $ins['total_payment'] = trim($total_payment);
                    $ins['payment_status'] = "trial";

                    $id = $dclass->insert("tblmember_apps", $ins);
                    $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], APP_ADDED . ' ' . $ins['or_app_name']);

                    if ($id) {

                        $fres = $dclass->select("intid", "tblfeatures", " AND status='active' ");

                        for ($i = 0; $i < count($fres); $i++) {
                            unset($insf);
                            $insf['member_id'] = $member_id;
                            $insf['app_id'] = $id;
                            $insf['feature_id'] = $fres[$i]['intid'];
                            $insf['transaction_id'] = 0;
                            $insf['pf_id'] = 0;
                            $insf['payment_type'] = 'monthly';
                            $insf['payment_cost'] = 0;
                            $insf['feature_status'] = "running";

                            $fid = $dclass->insert("tblmember_app_features", $insf);
                            $feature_id = $insf['feature_id'];
                        }
                        //Promote other apps
                        unset($insp);
                        $insp['member_id'] = $member_id;
                        $insp['app_id'] = $id;

                        $insp['logo_flag'] = 'no';

                        $insp['title_text'] = "Our Other Apps";
                        $insp['font_color'] = '000000';
                        $insp['bck_color'] = 'ffffff';
                        $insp['font_family'] = 'Helvetica';
                        $insp['animation_id'] = 4;
                        $insp['status'] = 'publish';

                        $prid = $dclass->insert("tblmore_app_settings", $insp);

                        //check for promotr enabled
                        $chkp = $dclass->select("a.intid", "tblmember_apps a inner join tblmember_app_features f on a.intid=f.app_id", " AND  f.member_id='" . $member_id . "' AND  f.app_id = '" . $id . "' AND (f.pf_id='2' OR f.pf_id='8')");


                        //If promotr enabled store more app ids of other apps
                        if (count($chkp) > 0) {
                            $gnrl->add_more_app_for_new_app($id, $member_id, $app_type);
                        }

                        //if promotr or analytics enabled store this app as more app for other promotr enabled apps
                        if (count($chkp) > 0 || $feature_status == 'active') {

                            //master more app
                            //check for existing same custom more app for the same member
                            $chkmr = $dclass->select("s.intid,i.image", "tblmore_apps s inner join tblmore_app_images i on s.intid=i.more_app_id", " AND s.member_id='" . $member_id . "' AND s.parent_app_id='0' AND s.more_app_lnk='" . $app_url . "' ");
                            if (count($chkmr) > 0) {
                                unset($insap);
                                $insap['parent_app_id'] = $id;
                                $insap['more_app_name'] = $gnrl->subString($app_name, 30);
                                $insap['dtadd'] = date("Y-m-d h:i:s");
                                $insap['status'] = 'active';
                                $dclass->update("tblmore_apps", $insap, " intid='" . $chkmr[0]['intid'] . "' ");
                                $more_app_id = $chkmr[0]['intid'];

                                //DELETE custom more app images of the same more app
                                for ($k = 0; $k < count($chkmr); $k++) {
                                    unlink(MORE_APPS_IMG . "/" . $chkmr[$k]['image']);
                                    unlink(MORE_APPS_IMG . "/thumbnails/" . $chkmr[$k]['image']);
                                }
                                $dclass->delete("tblmore_app_images", " more_app_id='" . $more_app_id . "' ");
                                $dclass->delete("tblapp_moreapp_rel", " more_app_id='" . $more_app_id . "' ");
                            } else {
                                unset($insa);
                                $insa['member_id'] = $member_id;
                                $insa['parent_app_id'] = $id;
                                $insa['more_app_name'] = $gnrl->subString($app_name, 30);
                                $insa['more_app_lnk'] = $app_url;
                                $insa['dtadd'] = date("Y-m-d h:i:s");
                                $insa['status'] = 'active';
                                $more_app_id = $dclass->insert("tblmore_apps", $insa);
                            }
                            if ($ssurlcount > 0) {
                                //more app images
                                for ($i = 0; $i < $ssurlcount; $i++) {
                                    $more_app_img = $_POST['ssh-' . $i];
                                    if ($app_type == 'ios') {
                                        $content = $gnrl->getimg($more_app_img);
                                        $filename = time() . $app_para . '-promo' . $i . '.png';
                                        $save_path = MORE_APPS_IMG . '/' . $filename;
                                        $chk = file_put_contents($save_path, $content);
                                    } else if ($app_type == 'android') {
                                        $chk = imagecreatefromjpeg($more_app_img);
                                        $filename = time() . $app_para . '-promo' . $i . '.png';
                                        $save_path = MORE_APPS_IMG . '/' . $filename;
                                        imagejpeg($chk, $save_path);
                                    }
                                    if ($chk) {
                                        unset($insai);
                                        $insai['more_app_id'] = $more_app_id;
                                        $insai['image'] = $filename;
                                        $insai['source'] = 'store';
                                        if ($more_app_img == $more_app_sel_img) {
                                            $insai['status'] = 'cover';
                                        } else {
                                            $insai['status'] = 'other';
                                        }
                                        $maid = $dclass->insert("tblmore_app_images", $insai);
                                        if ($more_app_img == $more_app_sel_img) {
                                            $more_app_img_id = $maid;
                                        }
                                    }
                                }
                                //if no selected more app img than take first as default
                                if (!$more_app_img_id) {
                                    $more_app_img_id = $maid[0];
                                }
                            }
                            //custom more app image
                            if ($more_app_sel_new_img && $_FILES['more_app_logo']) {
                                $filename = time() . $gnrl->makefilename($app_para - 'promo-single') . ".jpg";
                                $des = MORE_APPS_IMG . "/" . $filename;
                                if (move_uploaded_file($_FILES['more_app_logo']['tmp_name'], $des)) {
                                    unset($insai);
                                    $insai['more_app_id'] = $more_app_id;
                                    $insai['image'] = $filename;
                                    $insai['source'] = 'custom';
                                    $insai['status'] = 'cover';
                                    $insai['cover_app_id'] = 0;
                                    $more_app_img_id = $dclass->insert("tblmore_app_images", $insai);
                                }
                            }

                            if ($server_status == 'prod') {
                                //add this app as more app for other promotr enabled apps with production status
                                $gnrl->add_more_app_to_new_app($more_app_id, $member_id, $app_type);
                            } else {
                                //delete this more app for other promotr enabled apps with development status
                                $gnrl->delete_more_app($more_app_id);
                            }
                        }

                        $data['output'] = 'S';

                        if ($script == 'add')
                            $app_msg_status = "APP_ADD";
                        else
                            $app_msg_status = "APP_UPD";
                    }
                    else {
                        $data['output'] = 'F';
                        $app_msg_status = "APP_SAVE_FAIL";
                    }
                } else {
                    $app_msg_status = 'ALL_FEATURES_DISABLED_APP_ADD_FAILED';
                    $data['output'] = 'F';
                }
            }
        }

        $data['msg'] = $gnrl->getMessage($app_msg_status, $lang_id);

        if ($data['output'] == 'S') {
            if ($app_type == 'ios')
                $data['no_record'] = 0;
            else
                $data['no_ad_record'] = 0;

            if ($script != '') {
                $_SESSION['type'] = 'succ';
                $_SESSION['msg'] = $data['msg'];
            }
            $data['app_type'] = $app_type;
        }
        echo json_encode($data);
        die();
    }

    //Save feature status
    if ($_POST['action'] == 'change_status' && isset($_POST['feature_status_id'])) {
        extract($_POST);
        if ($feature_status)
            $upd['feature_status'] = 'running';
        else
            $upd['feature_status'] = 'pause';

        $dclass->update("tblmember_app_features", $upd, " intid='" . $feature_status_id . "'");
        $data['output'] = 'S';
        $data['msg'] = $gnrl->getMessage('FEATURE_STATUS_UPD', $lang_id);
        echo json_encode($data);
        die();
    }

    // Save Ratings
    if ($_POST['action'] == 'save_ratings' && isset($_POST['intid'])) {

        extract($_POST);
        unset($upd);
        $upd['content_yn'] = $content_yn;
        $upd['content_rate_short'] = $content_rate_short;
        $upd['content_rate_long'] = $content_rate_long;
        $upd['like_yes'] = $like_yes;
        $upd['like_no'] = $like_no;
        $upd['rate_this_app'] = $rate_this_app;
        $upd['remind_later'] = $remind_later;
        $upd['no_thanks'] = $no_thanks;
        $upd['like_yes_bck'] = $like_yes_bck_value;
        $upd['like_no_bck'] = $like_no_bck_value;
        $upd['like_yes_but'] = $like_yes_but_value;
        $upd['like_no_but'] = $like_no_but_value;
        $upd['rate_this_app_bck'] = $rate_this_app_bck_value;
        $upd['remind_later_bck'] = $remind_later_bck_value;
        $upd['no_thanks_bck'] = $no_thanks_bck_value;
        $upd['rate_this_app_but'] = $rate_this_app_but_value;
        $upd['remind_later_but'] = $remind_later_but_value;
        $upd['no_thanks_but'] = $no_thanks_but_value;
        $upd['que_select_font'] = $que_select_font;
        $upd['rate_select_font'] = $rate_select_font;
        $upd['like_no_action'] = $like_no_action;
        if ($like_no_email != '')
            $upd['like_no_email'] = $like_no_email;
        $upd['dtmod'] = date("Y-m-d h:i:s");
        $upd['status'] = $status;
        $dclass->update("tblapp_ratings", $upd, " intid='" . $intid . "' ");
        $data['output'] = 'S';

        if ($status == 'publish')
            $rating_status = "RATING_PUBLISH_UPDATE";
        else if ($pstatus == 'save')
            $rating_status = "RATING_SAVE_UPDATE";
        else
            $rating_status = "RATING_PAUSE_UPDATE";

        $data['msg'] = $gnrl->getMessage($rating_status, $lang_id);
        echo json_encode($data);
        die();
    }


    //Delete User
    if ($_POST['action'] == 'delete_user' && isset($_POST['intid'])) {
        extract($_POST);
        $res = $dclass->select("*", "tblmember", " AND intid='" . $intid . "' ");
        if (count($res) > 0) {
            unlink(USER_LOGO . "/" . $res[0]['logo']);
            unlink(USER_LOGO . "/thumbnail/" . $res[0]['logo']);
        }
        //Delete the relation record by default
        $st = $dclass->delete("tblmember", " intid='" . $intid . "'");
        if ($st) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $del_status = 'DELETE_USER_SUC';
        } else {
            $data['output'] = 'F';
            $del_status = 'DELETE_USER_FAIL';
        }

        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        echo json_encode($data);
        die();
    }

    if ($_POST['action'] == 'delete_log' && isset($_POST['intid'])) {
        extract($_POST);
        //Delete the relation record by default
        $st = $dclass->delete("tbl_access_log", " intid='" . $intid . "'");
        if ($st) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $del_status = 'SUCC_ACCESS_LOG_DELETE';
        } else {
            $data['output'] = 'F';
            $del_status = 'FAIL_ACCESS_LOG_DELETE';
        }

        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        echo json_encode($data);
        die();
    }

    //Save FAQ
    if ($_POST['action'] == 'save_faq') {
        extract($_POST);

        if (!$question && !$answer) {
            $data['msg'] = $gnrl->getMessage("FAQ_REQUIRED", $lang_id);
            $data['output'] = 'F';
            echo json_encode($data);
            die();
        } else {
            if (isset($_POST['intid']) && $_POST['intid'] != '') {
                $upd['question'] = $question;
                $upd['answer'] = $answer;
                $dclass->update("tblapp_faq", $upd, " intid='" . $intid . "'");
                $faq_status = "FAQ_UPDATE";
            } else {
                //FAQ INSERT
                $insf['member_id'] = $member_id;
                $insf['app_id'] = $app_id;
                $insf['question'] = $question;
                $insf['answer'] = $answer;
                $insf['dtadd'] = date("Y-m-d h:i:s");
                $faq_id = $dclass->insert("tblapp_faq", $insf);

                $faq_status = "FAQ_ADD";
            }

            //FAQ color settings change
            $upfaq['faq_font_color'] = $faq_font_color;
            $dclass->update("tblapp_tutorial_settings", $upfaq, " app_id='" . $app_id . "' ");
        }


        $data['output'] = 'S';
        $data['msg'] = $gnrl->getMessage($faq_status, $lang_id);
        $_SESSION['msg'] = $data['msg'];
        $_SESSION['type'] = 'succ';
        $_SESSION['section'] = 'faq';
        echo json_encode($data);
        die();
    }

    //Delete FAQ
    if ($_POST['action'] == 'delete_faq' && isset($_POST['intid'])) {
        extract($_POST);

        //Delete the relation record by default
        $st = $dclass->delete("tblapp_faq", " intid='" . $intid . "'");
        if ($st) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $del_status = 'DELETE_FAQ';
        } else {
            $data['output'] = 'F';
            $del_status = 'DELETE_FAQ_FAIL';
        }


        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        $_SESSION['msg'] = $data['msg'];
        $_SESSION['type'] = $type;
        $_SESSION['section'] = 'faq';
        echo json_encode($data);
        die();
    }

    //Make Tutorial Video LIVE
    if ($_POST['action'] == 'make_video_live' && isset($_POST['intid']) && isset($_POST['app_id'])) {
        extract($_POST);

        $upd['live_date'] = date("Y-m-d");
        $upd['pause_date'] = NULL;
        $st = $dclass->update("tblapp_videos", $upd, " intid='" . $intid . "'");

        $updo['live_date'] = NULL;
        $upd['pause_date'] = date("Y-m-d");
        $dclass->update("tblapp_videos", $updo, " intid!='" . $intid . "' AND app_id = '" . $app_id . "' ");

        if ($st) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $make_status = 'HELPR_MAKE_VIDEO_LIVE';
        } else {
            $data['output'] = 'F';
            $make_status = 'HELPR_MAKE_VIDEO_LIVE_FAIL';
        }

        $data['msg'] = $gnrl->getMessage($make_status, $lang_id);
        $_SESSION['msg'] = $data['msg'];
        $_SESSION['type'] = $type;
        $_SESSION['section'] = 'video';
        echo json_encode($data);
        die();
    }
    //Delete Video
    if ($_POST['action'] == 'delete_video' && isset($_POST['intid'])) {
        extract($_POST);
        //Delete the relation record by default
        $st = $dclass->delete("tblapp_videos", " intid='" . $intid . "'");
        if ($st) {
            $data['output'] = 'S';
            $type = 'succ';
            $data['intid'] = $intid;
            $del_status = 'DELETE_VIDEO';
        } else {
            $data['output'] = 'F';
            $del_status = 'DELETE_VIDEO_FAIL';
        }

        $data['msg'] = $gnrl->getMessage($del_status, $lang_id);
        $_SESSION['msg'] = $data['msg'];
        $_SESSION['type'] = $type;
        $_SESSION['section'] = 'video';
        echo json_encode($data);
        die();
    }

    //delete individual image
    if ($_POST['action'] == 'delete_image') {
        extract($_POST);
        if ($type == 'app_logo_detail') {
            $res = $dclass->select("app_logo", "tblmember_apps", " AND intid='" . $intid . "' ");
            if (count($res) > 0) {
                unlink(APP_LOGO . "/" . $res[0]['app_logo']);
                $upd['app_logo'] = '';
                $dclass->update("tblmember_apps", $upd, "intid='" . $intid . "' ");
            }
        } else if ($type == 'company_logo_detail') {
            $res = $dclass->select("company_logo", "tblmember_apps", " AND intid='" . $intid . "' ");
            if (count($res) > 0) {
                unlink(COMPANY_LOGO . "/" . $res[0]['company_logo']);
                $upd['company_logo'] = '';
                $dclass->update("tblmember_apps", $upd, "intid='" . $intid . "' ");
            }
        } else if ($type == 'custom_app_image_detail') {
            $res = $dclass->select("image", "tblmore_app_images", " AND intid='" . $intid . "' ");
            if (count($res) > 0) {
                unlink(MORE_APPS_IMG . "/" . $res[0]['image']);
                $dclass->delete("tblmore_app_images", "intid='" . $intid . "' ");
            }
        } else if ($type == 'custom_app_image') {
            $res = $dclass->select("r.more_app_id,i.image, r.more_app_custom_image, r.more_app_img_id", "tblapp_moreapp_rel r  inner join  tblmore_app_images i on r.more_app_id=i.more_app_id", " AND r.intid='" . $intid . "' ");
            if (count($res) > 0) {
                if ($res[0]['more_app_custom_image'] != '' && $res[0]['more_app_img_id'] == 0) {
                    unlink(MORE_APPS_IMG . "/" . $res[0]['more_app_custom_image']);
                    $upd['more_app_custom_image'] = '';
                    $dclass->update("tblapp_moreapp_rel", $upd, "intid='" . $intid . "' ");
                } else {
                    $resl = $dclass->select("intid", "tblmore_app_images", " AND more_app_id='" . $res[0]['more_app_id'] . "' AND source='store' ORDER BY intid ASC ");
                    if (count($resl) > 0) {
                        $more_app_img_id = $resl[0]['intid'];
                    } else
                        $more_app_img_id = 0;

                    $upd['more_app_img_id'] = $more_app_img_id;
                    $dclass->update("tblapp_moreapp_rel", $upd, "intid='" . $intid . "' ");
                }
            }
        }
        else if ($type == 'user') {
            $res = $dclass->select("logo", "tblmember", " AND intid='" . $intid . "' ");
            if (count($res) > 0) {
                unlink(USER_LOGO . "/" . $res[0]['logo']);
                $upd['logo'] = '';
                $dclass->update("tblmember", $upd, "intid='" . $intid . "' ");
            }
        } else if ($type == 'company') {
            $res = $dclass->select("company_logo", "tblmember", " AND intid='" . $intid . "' ");
            if (count($res) > 0) {
                unlink(COMPANY_LOGO . "/" . $res[0]['company_logo']);
                $upd['company_logo'] = '';
                $dclass->update("tblmember", $upd, "intid='" . $intid . "' ");
            }
        }

        $data['Output'] = 'S';
        echo json_encode($data);
        die();
    }

    if ($_REQUEST['action'] == 'check_help_version') {
        $res = $dclass->select("version", "tblapp_tutorial_settings", " AND app_id='" . $_REQUEST['app_id'] . "' AND version = '" . $_REQUEST['version'] . "' ");

        if (count($res) > 0) {
            $data['msg'] = 'Version Already Added';
            $data['output'] = 'F';
            echo json_encode($data);
            die();
        }
    }
}
?>
