<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

/* Web service to save support request parameters for apps
 * 
 */
require_once 'service-configuration.php';


$secret_key = $_REQUEST['secret_key'];

$app_id = $_REQUEST['app_id'];

$feature_id = 3; //for communicatr

$request_id = $_REQUEST['request_id'];

$request_type = $_REQUEST['request_type'];

$name = $_REQUEST['name'];

$email = $_REQUEST['email'];

$message = $_REQUEST['message'];

$region = $_REQUEST['region'];

$device_id = $_REQUEST['device_id'];

$device_token = $_REQUEST['device_token'];

$version = $_REQUEST['version'];

$app_version = $_REQUEST['app_version'];

$device = $_REQUEST['device'];

if (isset($_REQUEST['subject']))
    $subject = $_REQUEST['subject'];

if (isset($_REQUEST['phone']))
    $phone = $_REQUEST['phone'];

$os_type = $_REQUEST['os_type'];

$attachments = array();
if (isset($_FILES['attachments'])) {
    $attachments = $_FILES['attachments'];
}


if (!isset($_REQUEST['is_live'])) {
    $_REQUEST['is_live'] = 1;
}

$res = $dclass->select("f.*,a.member_id,a.app_store_id,a.app_status, m.company_logo,m.package_id", " tblmember_apps a  INNER JOIN tblmember m ON a.member_id = m.intid inner join tblmember_app_features f on a.intid=f.app_id", "  AND a.app_key = '" . $secret_key . "' AND a.app_store_id='" . $app_id . "' AND f.feature_id='" . $feature_id . "' GROUP BY f.app_id ");

if (count($res) > 0) {

    //Check feature availibility
    $feature_access = $gnrl->check_feature_avail($res[0]['member_id'], $res[0]['app_id'], $feature_id);

    if ($feature_access) {

        //check agent allocation
        $chk = $dclass->select("member_id", "tbl_support_agent_allocate", " AND app_id='" . $res[0]['app_id'] . "' ");

        if (count($chk) > 0) {
            if ($chk[0]['member_id'] != 0) {
                $member_id = $chk[0]['member_id'];
                $parent_id = $res[0]['member_id'];
            } else {
                $member_id = $res[0]['member_id'];
                $parent_id = $res[0]['member_id'];
            }
        } else {
            $member_id = $res[0]['member_id'];
            $parent_id = $res[0]['member_id'];
        }

        #INSERT
        $ins['member_id'] = $member_id;
        $ins['parent_id'] = $parent_id;
        $ins['request_id'] = $request_id;
        $ins['app_id'] = $res[0]['app_id'];
        $ins['type'] = 'user';
        $ins['request_type'] = $request_type;
        $ins['priority'] = 'medium';
        $ins['name'] = $name;
        $ins['email'] = $email;
        $ins['subject'] = $subject;
        $ins['message'] = $message;
        $ins['region'] = $region;
        $ins['device_id'] = $device_id;
        $ins['device_token'] = $device_token;
        $ins['os'] = $os_type;
        $ins['version'] = $version;
        $ins['device'] = $device;
        $ins['app_version'] = $app_version;
        $ins['is_read'] = 'N';
        $ins['is_read_mobile'] = 'Y';
        $ins['phone'] = $phone;
        $ins['dtadd'] = date("Y-m-d H:i:s");
        $ins['status'] = 'due';
        $ins['is_live'] = $_REQUEST['is_live'];
        $id = $dclass->insert('tblapp_support', $ins);


        if ($request_id) {
            $reopencheck = $dclass->select("intid,status", "tblapp_support", " AND intid='" . $request_id . "'");
            if ($reopencheck) {
                if ($reopencheck[0]['status'] == "close") {
                    $dclass->query("UPDATE tblapp_support SET status = 'due' WHERE intid = '" . $request_id . "' ");
                }
            }
        }


        if ($id) {
            //Attachment for ios/android
            if ($os_type == 'ios' || $os_type == 'android') {

                if (is_array($_FILES['attachments']) && count($_FILES['attachments']['name']) > 0) {

                    $cnt = 0;

                    for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                        $filename = time() . $gnrl->makefilename($_FILES['attachments']['name'][$i]);
                        $des = SUPPORT_IMG . "/" . $filename;

                        if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $des)) {
                            $uparray['image'][$i] = $filename;
                            chmod(SUPPORT_IMG . "/" . $filename, 0777);

                            $gnrl->createThumb1(SUPPORT_IMG . "/" . $filename, SUPPORT_IMG . "/thumb/" . $filename, 100, 100);
                        }
                        $insa["support_id"] = $id;
                        $insa["image"] = $filename;
                        $aid = $dclass->insert("tblapp_support_attachment", $insa);
                    }
                }
            } else { //for windows
                $attachments = json_decode(stripcslashes($_POST['attachments']));
                if (is_array($attachments) && count($attachments) > 0) {
                    $cnt = 0;
                    for ($i = 0; $i < count($attachments); $i++) {

                        $file_name = date('YmdHis') . $attachments[$i]->name;
                        $file_path = '../upload/support/attachment/' . $file_name;
                        $file_ext = $attachments[$i]->type;
                        $file = base64_decode($attachments[$i]->strimage);
                        if ($file_ext == 'jpg' || $file_ext == 'jpeg' || $file_ext == 'png' || $file_ext == 'gif') {

                            $img = imagecreatefromstring($file);

                            if ($img != false) {
                                imagejpeg($img, $file_path);
                                $insa['support_id'] = $id;
                                $insa["image"] = $file_name;
                                $aid = $dclass->insert("tblapp_support_attachment", $insa);
                            }

                            if (!$aid) {
                                $result['attach_msg'] = "attachment for " . $file_name . " failed";
                            }
                        }
                    }
                }//check for attachment over
            }

            //ADD ACTION
            if ($request_id == 0 && $_REQUEST['is_live'] == 1) {

                $wha = "actions = (actions+1) ";
                $what = "total_actions = (total_actions+1) ";

                //date and app wise check
                $chka = $dclass->select("intid", "tblmember_stats", " AND member_id='" . $res[0]['member_id'] . "' AND app_id='" . $res[0]['app_id'] . "'  AND package_id = '" . $res[0]['package_id'] . "' AND feature_id='" . $feature_id . "' AND dtadd = '" . date("Y-m-d") . "' ");
                if (count($chka) > 0) {
                    #UPDATE
                    $dclass->query("UPDATE tblmember_stats SET " . $wha . " WHERE intid = '" . $chka[0]['intid'] . "' ");
                    $id1 = $chka[0]['intid'];
                } else {
                    #INSERT
                    $insa['member_id'] = $res[0]['member_id'];
                    $insa['package_id'] = $res[0]['package_id'];
                    $insa['feature_id'] = $feature_id;
                    $insa['app_id'] = $res[0]['app_id'];
                    $insa['actions'] = 1;
                    $insa['dtadd'] = date("Y-m-d");
                    $id1 = $dclass->insert('tblmember_stats', $insa);
                }

                //Update Total actions
                if ($id1) {
                    $updmsql = "UPDATE tblmember SET " . $what . " WHERE intid = '" . $res[0]['member_id'] . "' ";
                    $dclass->query($updmsql);
                }
            }


            if ($_REQUEST['is_live'] == 1) {
                //UPDATE App support count  
                $chk = $dclass->select("intid", "tblapp_analytics", " AND member_id='" . $res[0]['member_id'] . "' AND app_id='" . $res[0]['app_id'] . "' ");
                if (count($chk) > 0) {
                    #UPDATE
                    $dclass->query("UPDATE tblapp_analytics SET app_support_count = (app_support_count+1) WHERE intid = '" . $chk[0]['intid'] . "' ");
                    $cid = $chk[0]['intid'];
                } else {
                    #INSERT
                    $insc['member_id'] = $res[0]['member_id'];
                    $insc['app_id'] = $res[0]['app_id'];
                    $insc['os_type'] = $os_type;
                    $insc['app_support_count'] = 1;
                    $insc['dtadd'] = date("Y-m-d h:i:s");
                    $cid = $dclass->insert('tblapp_analytics', $insc);
                }
            }


            $result['id'] = $id;
            $result['status'] = 1;
            $result['msg'] = $gnrl->getMessage('SUPPORT_SAVE_SUC', $lang_id);
        } else {
            $result['status'] = 0;
            $result['msg'] = $gnrl->getMessage('SUPPORT_SAVE_FAIL', $lang_id);
        }
    } else {
        $result['status'] = 0;
        $result['msg'] = $gnrl->getMessage('FEATURE_NOT_AVAILABLE', $lang_id);
    }
} else {
    $result['status'] = 0;
    $result['msg'] = $gnrl->getMessage('NO_SUPPORT_CONFIG', $lang_id);
}
header('Content-type: application/json');
echo json_encode($result);
?>
