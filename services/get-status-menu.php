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
$secret_key = $_REQUEST['secret_key'];
$flag = $_REQUEST['flag'];
$app_id = $_REQUEST['app_id'];
$device_id = @$_REQUEST['device_id'];

$feature_id = 4; //1 for tutorial video

$res = $dclass->select("a.member_id,a.app_store_id,v.display_first,v.intid,v.app_id,v.image_animation", "tblmember_apps a INNER JOIN tblapp_tutorial_settings v ON a.intid=v.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id", " AND a.app_key = '" . $secret_key . "' AND a.app_store_id='" . $app_id . "' ORDER BY v.record_status LIMIT 1");

$getapp_id = $dclass->select("intid", "tblmember_apps", " AND app_key = '" . $secret_key . "'");

$getcount = $dclass->select("intid", "tblapp_support", " AND device_id = '" . $device_id . "' AND request_id = 0 AND app_id = '" . $getapp_id[0]['intid'] . "' ");

$result['message_count'] = count($getcount);
if (count($res) > 0) {

    //Check feature availibility
    $feature_access = $gnrl->check_feature_avail($res[0]['member_id'], $res[0]['app_id'], $feature_id);

    if ($feature_access) {

        //get feature status
        $fres = $dclass->select("feature_status", "tblmember_app_features", " AND app_id='" . $res[0]['app_id'] . "' ");
        //Check admin configured access
        if ($fres[0]['feature_status'] == 'running') {
            $cnt = 0;
            $no_cnt = 0;
            //Get LIVE Tutorial Video if it exists

            $vres = $dclass->select("*", "tblapp_tutorial_videos", " AND app_id='" . $res[0]['app_id'] . "' AND live_date IS NOT NULL AND ver_id='" . $res[0]['intid'] . "' ");

            if (count($vres) > 0) {
                $result['video']['status'] = 1;
            } else {
                $result['video']['status'] = 0;
            }
            
            //Get Tutorial Images if exists

            $ires = $dclass->select("intid,title,image", "tblapp_tutorial_images", " AND app_id = '" . $res[0]['app_id'] . "' AND ver_id='" . $res[0]['intid'] . "' ORDER BY intorder ASC");

            if (count($ires) > 0) {
                $result['image']['status'] = 1;
            } else {
                $result['image']['status'] = 0;
            }

            $data['image_animation'] = $res[0]['image_animation'];


            //Get faq latest record
            $fres = $dclass->select("*", "tblapp_faq", " AND app_id='" . $res[0]['app_id'] . "' AND is_canned='N' AND status='active' ORDER BY intorder ASC");

            if (count($fres) > 0) {
                $result['faq']['status'] = 1;
            } else {
                $result['faq']['status'] = 0;
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
    $result['status'] = 2;
    $result['msg'] = $gnrl->getMessage('KEY_NOT_AVAILABLE', $lang_id);
}
header('Content-type: application/json');
echo json_encode($result);
?>
