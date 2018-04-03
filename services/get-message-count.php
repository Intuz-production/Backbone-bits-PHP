<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

/* Web service to get tutorial video/images/faq module parameters for apps
 *
 * Flag type = video,image,faq
 *  
 */
require_once 'service-configuration.php';

$api_key = $_REQUEST['api_key'];

$secret_key = $_REQUEST['secret_key'];

$app_id = $_REQUEST['app_id'];

$flag = 'list';

$request_id = $_REQUEST['request_id'];

$device_id = $_REQUEST['device_id'];

$feature_id = 3; //for communicatr

$res = $dclass->select("a.intid as app_id,a.member_id,a.app_store_id,a.app_logo,a.app_url as store_link", "tblmember_apps a", " AND a.app_key = '" . $secret_key . "' AND a.app_store_id='" . $app_id . "'  ");

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
                $request_id_query = ' AND request_id = ' . $request_id . ' AND device_id = \'' . $device_id . '\' ';
                $primary_id = ' OR intid = ' . $request_id;
            }

            $resc = $dclass->select("* ,(select dtadd as new_time from tblapp_support as aaa where (aaa.request_id = tblapp_support.intid OR aaa.intid = tblapp_support.intid) ORDER BY dtadd DESC LIMIT 1) as new_time ", "tblapp_support", " AND app_id='" . $res[0]['app_id'] . "' " . $request_id_query . " " . $primary_id . " ORDER BY new_time Desc  ");
            $total_count = 0;
            if (count($resc) > 0) {
                for ($i = 0; $i < count($resc); $i++) {
                    $getlatest_reply = $dclass->select('*, tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND (tblapp_support.intid = "' . $resc[$i]['intid'] . '" OR tblapp_support.request_id = "' . $resc[$i]['intid'] . '") order by tblapp_support.dtadd desc Limit 1');

                    $getunreadcount = $dclass->select('tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND (tblapp_support.intid = "' . $resc[$i]['intid'] . '" OR tblapp_support.request_id = "' . $resc[$i]['intid'] . '") AND is_read_mobile = \'N\' order by tblapp_support.dtadd desc');

                    $total_count = $total_count + count($getunreadcount);
                    $attachement_data = $dclass->select("*", "tblapp_support_attachment", " AND support_id = '" . $resc[$i]['intid'] . "'");
                }
                $result['total_unread_count'] = $total_count;
                $result['status'] = 1;
                $result['msg'] = $gnrl->getMessage('SUPPORT_CONFIG_SUC', $lang_id);
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
