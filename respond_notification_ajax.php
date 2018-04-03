<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php

require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];

if($member_id == $_SESSION['agents_cust_id']) {      
    $append_data = 'AND tblapp_support.parent_id = \''.$member_id.'\'';
}
else {
    $append_data = 'AND tblapp_support.member_id = \''.$_SESSION['agents_cust_id'].'\'';
}

$app_name_distinct_data = $dclass->select('*, tblapp_support.intid as support_id, tblapp_support.request_id as request_id ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id  ' . $append_data . ' AND is_read = "N" AND is_notification="N"  order by tblapp_support.dtadd desc');


$app_count_notification = $dclass->select('*, tblapp_support.intid as support_id, tblapp_support.request_id as request_id ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id  ' . $append_data . ' AND is_read = "N" order by tblapp_support.dtadd desc');

foreach($app_name_distinct_data as $val_data_notification){
    $up['is_notification']='Y';
    $dclass->update('tblapp_support',$up," intid='".$val_data_notification['support_id']."' ");
}

$json = array("data"=>$app_name_distinct_data,"count"=>count($app_count_notification));
echo json_encode($json);