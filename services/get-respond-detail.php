<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

/* Web service to get communicatr module parameters for apps
 * 
 * flag = list, listdetail
 * 
 */
require_once 'service-configuration.php';

$api_key = $_REQUEST['api_key'];

$secret_key = $_REQUEST['secret_key'];

$app_id = $_REQUEST['app_id'];

$flag = $_REQUEST['flag'];

$request_id = $_REQUEST['request_id'];

$device_id = $_REQUEST['device_id'];

$feature_id = 3; //for communicatr
$res = $dclass->select("a.intid as app_id,a.member_id,a.app_store_id,a.app_logo,a.app_url as store_link", "tblmember_apps a", " AND a.app_key = '" . $secret_key . "' AND a.app_store_id='" . $app_id . "'  ");

function gethours($time_ago) {
    $time_ago = strtotime($time_ago);
    $cur_time = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds = $time_elapsed;
    $minutes = round($time_elapsed / 60);
    $hours = round($time_elapsed / 3600);
    $days = round($time_elapsed / 86400);
    $weeks = round($time_elapsed / 604800);
    $months = round($time_elapsed / 2600640);
    $years = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return "Just now";
    }
    //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "1 min ago";
        } else {
            return "$minutes min ago";
        }
    }
    //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return "1 hr ago";
        } else {
            return "$hours hrs ago";
        }
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return "yesterday";
        } else {
            return "$days days ago";
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return "1 week ago";
        } else {
            return "$weeks weeks ago";
        }
    }
    //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return "1 month ago";
        } else {
            return "$months months ago";
        }
    }
    //Years
    else {
        if ($years == 1) {
            return "1 yr ago";
        } else {
            return "$years yrs ago";
        }
    }
}

if (count($res) > 0) {

    //Check feature availibility
    $feature_access = $gnrl->check_feature_avail($res[0]['member_id'], $res[0]['app_id'], $feature_id);

    if ($feature_access) {

        //get feature status
        $fres = $dclass->select("feature_status", "tblmember_app_features", " AND app_id='" . $res[0]['app_id'] . "' ");
        if ($fres[0]['feature_status'] == 'running') {

            if ($flag == 'list') {

                $request_id_query = 'AND request_id = 0 AND device_id = \'' . $device_id . '\' ';
            } else {
                $request_id_query = ' AND request_id = ' . $request_id . ' ';
                $primary_id = ' OR intid = ' . $request_id;
            }

            $resc = $dclass->select("*", "tblapp_support", " AND app_id='" . $res[0]['app_id'] . "' " . $request_id_query . " " . $primary_id . " ORDER BY intid Desc  ");

            if (count($resc) > 0) {
                for ($i = 0; $i < count($resc); $i++) {
                    if ($flag == 'list') {
                        $data[$i]['type'] = 'request';
                    } else {
                        $data[$i]['type'] = 'response';
                    }
                    $data[$i]['message_id'] = $resc[$i]['intid'];
                    $data[$i]['date'] = gethours($resc[$i]['dtadd']);
                    $data[$i]['timestamp'] = date('H:i', strtotime($resc[$i]['dtadd']));
                    $data[$i]['name'] = $resc[$i]['name'];
                    $data[$i]['request_by'] = $resc[$i]['type'];
                    $data[$i]['request_type'] = $resc[$i]['request_type'];
                    $data[$i]['os'] = $resc[$i]['os'];
                    $data[$i]['version'] = $resc[$i]['version'];
                    $data[$i]['message'] = $resc[$i]['message'];
                    $attachement_data = $dclass->select("*", "tblapp_support_attachment", " AND support_id = '" . $resc[$i]['intid'] . "'");
                    for ($k = 0; $k < count($attachement_data); $k++) {
                        if (!empty($attachement_data[$k]['image'])) {

                            $data[$i]['attachment_thumb'][] = SITE_URL . '/files/support/attachment/thumb/' . $attachement_data[$k]['image'];
                            $data[$i]['attachment_full'][] = SITE_URL . '/files/support/attachment/' . $attachement_data[$k]['image'];
                        } else {
                            $data[$i]['attachment_thumb'] = array();
                            $data[$i]['attachment_full'] = array();
                        }
                    }
                    $data1[date('Y-m-d', strtotime($resc[$i]['dtadd']))][] = $data[$i];
                }
                $data2 = array("request_type" => $resc[count($resc) - 1]['request_type'], "email" => $resc[count($resc) - 1]['email'], "name" => $resc[count($resc) - 1]['name'], "request_data" => $data1);
                $result['data'] = $data2;
                $result['status'] = 1;
                $result['server_place'] = date_default_timezone_get();
                $result['msg'] = $gnrl->getMessage('SUPPORT_CONFIG_SUC', $lang_id);
                $g = $dclass->query("UPDATE tblapp_support SET is_read_mobile = 'Y' WHERE request_id = '" . $request_id . "' OR intid = '" . $request_id . "' ");
            } else {
                $result['status'] = 0;
                $result['msg'] = $gnrl->getMessage('NO_ACTIVE_RECORD', $lang_id);
            }
        } else {
            $result['status'] = 0;
            $result['msg'] = $gnrl->getMessage('FEATURE_SAVED_OR_PAUSED', $lang_id);
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
