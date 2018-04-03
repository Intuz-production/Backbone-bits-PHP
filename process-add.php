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

extract($_REQUEST);

$values = $_REQUEST;
function ios_notification($message, $device_token, $passphrase, $pemfile,$server_status,$json) {
        // Put your device token here (without spaces):
        $deviceToken = $device_token;

        // Put your private key's passphrase here:
        $passphrase = $passphrase;

        // Put your alert message here:
        $message = $message;

        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfile);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        if($server_status=='prod'){
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        }else{
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        }

        
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default',
            'data'=>$json,
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
    }
    
    function android_notification($message, $device_token, $api_key){
        // API access key from Google API's Console
                define('API_ACCESS_KEY', $api_key);
                $registrationIds = array($device_token);
        // prep the bundle
                $msg = array
                    (
                    'backbone_message' => $message,
                    'title' => BRAND . ' Respond response',
                    'vibrate' => 1,
                    'sound' => 1,
                    'largeIcon' => 'large_icon',
                    'smallIcon' => 'small_icon'
                );
                $fields = array
                    (
                    'registration_ids' => $registrationIds,
                    'data' => $msg
                );

                $headers = array
                    (
                    'Authorization: key=' . API_ACCESS_KEY,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
    }


if ($_REQUEST['table_name'] == 'tblmember') {
    extract($_POST);

    $chk = $dclass->select("intid", $_REQUEST['table_name'], " AND email ='" . strtolower($values['email']) . "' ");
    if (count($chk) > 0) {

        $msg = $gnrl->getMessage("EMAIL_EXISTS", $lang_id);
        $data['output'] = 'F';
        $data['msg'] = $msg;
    } else {

        $t = explode(" ", $values['name']);
        $up['lang_id'] = 1;
        $up['parent_id'] = $_SESSION['custid'];
        $up['fname'] = $values['fname'];
        $up['lname'] = $values['lname'];
        $up['email'] = $values['email'];

        if ($_FILES['logo']['name'] != '') {

            $filename = time() . $gnrl->makefilename($_FILES['logo']['name']);
            $des = USER_LOGO . "/" . $filename;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $des)) {
                $up['logo'] = $filename;
            }
        }

        $up['dtadd'] = date("Y-m-d h:i:s");
        $up['dtmod'] = date("Y-m-d h:i:s");
            $up['status'] = 'waiting';

        $up['role'] = $values['role'];
        
        
        
        $id = $dclass->insert("tblmember", $up);
        $url = SITE_URL.'register-agent?reg&token_id='.  base64_encode($id);

        if ($id) {
            //Send Email to Agents
            $email_to = $up['email'];
            $email_from = $gnrl->getSettings('varsenderemial');

            $email_subject = "" . BRAND . " - Registration";

            $email_message .= "Hello " . $up['fname'] .' '.$up['lname'].  ", <br/>";
            $email_message .= "<p> You have been added as an agent in ".BRAND.'. To complete the registration process <a href="'.$url.'">activate your account</a> or copy following url in your browser.</p>';
            $email_message .= '<b>'.$url.'</b>';
            $email_message .= '<p>Thanks,<br><strong>'.BRAND.'</strong></p>';
            $gnrl->email($email_from, $email_to, "", "", $email_subject, $email_message, "html");
            
            //--------------Send email to agents--------------
            
            $msg = $gnrl->getMessage("AGENT_ADD", $lang_id);
            $data['output'] = 'S';
            $data['msg'] = $msg;
        } else {

            $msg = $gnrl->getMessage("AGENT_FAIL", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        }
    }
    $data['startlimit'] = 0;
    $data['limit'] = 4;
    echo (json_encode($data));
} else if ($_REQUEST['table_name'] == 'tblapp_faq') {
    extract($_POST);
    $up['parent_id'] = $_REQUEST['parent_id'];
    $up['member_id'] = $_REQUEST['member_id'];
    $up['app_id'] = $_REQUEST['app_id'];
    $up['status'] = $_REQUEST['status'];
    $up['dtadd'] = $_REQUEST['dtadd'];
    $up['question'] = $_REQUEST['question'];
    $up['answer'] = $_REQUEST['answer'];
    $up['is_canned'] = $_REQUEST['is_canned'][0];

    $app_tutorial_settings = $dclass->select("intid", "tblapp_tutorial_settings", " AND app_id='" . $_REQUEST['app_id'] . "' AND record_status = 'running' ");
    $up['ver_id']=$app_tutorial_settings[0]['intid'];
    $id = $dclass->insert($_REQUEST['table_name'], $up);

    if ($id) {
        if ($up['is_canned'] == 'Y') {
            $msg = $gnrl->getMessage("CANNED_SUCC", $lang_id);
            $data['output'] = 'S';
            $data['msg'] = $msg;
        } else {
            $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], COMMUNICATR_FAQ_ADDED);
            $msg = $gnrl->getMessage("MESS_FAQ_SUCC", $lang_id);
            $data['output'] = 'S';
            $data['msg'] = $msg;
        }
    } else {


        if ($up['is_canned'] == 'Y') {
            $msg = $gnrl->getMessage("CANNED_ERR", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        } else {
            $msg = $gnrl->getMessage("MESS_FAQ_ERR", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        }
    }

    $data['startlimit'] = 0;
    $data['limit'] = 10;
    echo (json_encode($data));
} else if ($_REQUEST['table_name'] == 'tbl_ask_for_review') {
    
    extract($_POST);
    $member_apps = $dclass->select("*", "tblmember_apps", " AND intid='" . $_POST['app_id'] . "' ");
    $all_device_token = $dclass->select("*", "tblapp_support", " AND app_id='" . $_POST['app_id'] . "' AND intid = '".$_REQUEST['support_id']."' AND request_id = 0 ");
    $message_details = str_replace("%appstore_url%",'<a target="_blank" href="'.$member_apps[0]['app_url'].'">Appstore review</a>' , $member_apps[0]['review_message']);
    $message_details = str_replace("%app_name%", $member_apps[0]['or_app_name'], $message_details);
    $message_details = str_replace("%ticket_creator_name%", $all_device_token[0]['name'], $message_details);
    $message_details = str_replace("%app_company_name%", ucfirst($member_apps[0]['company_name']), $message_details);
    
    
    $up['parent_id'] = $_REQUEST['parent_id'];
    $up['member_id'] = $_REQUEST['member_id'];
    $up['app_id'] = $_REQUEST['app_id'];
    $up['support_id'] = $_REQUEST['support_id'];
    $up['dtadd'] = date('Y-m-d H:i:s');



    $up1['member_id'] = $_REQUEST['member_id'];
    $up1['parent_id'] = $_REQUEST['parent_id'];
    $up1['app_id'] = $_REQUEST['app_id'];
    $up1['request_id'] = $_REQUEST['support_id'];
    $up1['version'] = $_REQUEST['version'];
    $up1['app_version'] = $_REQUEST['app_version'];
    $up1['type'] = $_SESSION['role'];
    $up1['name'] = $_SESSION['custname'];
    $up1['dtadd'] = date('Y-m-d H:i:s');
    $up1['is_read'] = 'Y';
    $up1['status'] = 'review';
    $up1['message'] = $message_details;



    $app_name_distinct = $dclass->select('*', $_REQUEST['table_name'], ' AND support_id= \'' . $up['support_id'] . '\' ');
    $get_user_detail = $dclass->select('*', 'tblapp_support', ' AND support_id= \'' . $up['support_id'] . '\' ');

    if (count($app_name_distinct) == 0) {
        $id1 = $dclass->insert('tblapp_support', $up1);
        $id = $dclass->insert($_REQUEST['table_name'], $up);
        if ($id) {
            $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], ASK_FOR_REVIEW);
$json_data = json_encode(array("request_id"=>$up1['request_id'],"sdk_name"=>BRAND));
            
        $getpem_files = $dclass->select("*", "tbl_support_agent_allocate", " AND app_id='" . $_POST['app_id'] . "' ");
        

        if ($member_apps[0]['notification'] == 'active') {

            if ($member_apps[0]['app_type'] == 'ios') {

                if ($member_apps[0]['server_status'] == 'prod') {
                    $pemfile = 'files/pem/' . $getpem_files[0]['pem_prod_file'];
                    ios_notification('Please rate us', $all_device_token[0]['device_token'], $getpem_files[0]['passphrase_prod'], $pemfile,$member_apps[0]['server_status'],$json_data);
                } else {
                    $pemfile = 'files/pem/' . $getpem_files[0]['pem_file'];
                    ios_notification('Please rate us', $all_device_token[0]['device_token'], $getpem_files[0]['passphrase'], $pemfile,$member_apps[0]['server_status'],$json_data);
                }
            } else {

                android_notification('Please rate us', $all_device_token[0]['device_token'], $getpem_files[0]['api_key']);
                
            }
        }

            $msg = $gnrl->getMessage("ASK_REVIEW_SUCC", $lang_id);
            $data['output'] = 'S';
            $data['msg'] = $msg;


            //Send Email Response to person
            $email_to = $get_user_detail[0]['email'];
            $email_from = $gnrl->getSettings('varsenderemial');

            $email_subject = "" . BRAND . " - Review and Rating";

            
            
            $email_message = $message_details;
            $gnrl->email($email_from, $email_to, "", "", $email_subject, $email_message, "html");
        } else {

            $msg = $gnrl->getMessage("ASK_REVIEW_ERR", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        }

    } else {
        $msg = $gnrl->getMessage("REVIEW_SENT_ERR", $lang_id);
        $data['output'] = 'F';
        $data['msg'] = $msg;
    }

    echo (json_encode($data));
} else if ($_REQUEST['table_name'] == 'tblapp_support') {
    extract($_POST);

    

    $up['member_id'] = $_REQUEST['member_id'];
    $up['parent_id'] = $_REQUEST['parent_id'];
    $up['app_id'] = $_REQUEST['app_id'];
    $up['request_id'] = $_REQUEST['request_id'];
    $up['version'] = $_REQUEST['version'];
    $up['app_version'] = $_REQUEST['app_version'];
    $up['type'] = $_REQUEST['type'];
    $up['name'] = $_REQUEST['name'];
    $up['dtadd'] = $_REQUEST['dtadd'];
    $up['is_read'] = 'Y';
    $up['is_notification'] = 'Y';

    $up['status'] = $_REQUEST['status'];
    $up['message'] = $_REQUEST['message'];
    if ($_FILES['app_logo']['name'] != '') {
        for ($i = 0; $i < count($_FILES['app_logo']['name']); $i++) {
            $filename = time() . $gnrl->makefilename($_FILES['app_logo']['name'][$i]);
            $des = SUPPORT_IMG . "/" . $filename;
            if (move_uploaded_file($_FILES['app_logo']['tmp_name'][$i], $des)) {
                //unlink(USER_LOGO."/".$old_logo);
                $uparray['image'][$i] = $filename;
                $gnrl->createThumb(SUPPORT_IMG . "/" . $filename, SUPPORT_IMG . "/thumb/" . $filename, 100, 100);
            }
        }
    }
    if ($up['status'] == 'close') {
        $up1['status'] = 'close';
        $dclass->update($_REQUEST['table_name'], $up1, " intid = '" . $up['request_id'] . "'");
    }
    
        
    if ($up['status'] == 'reopen') {
        $up1['status'] = 'due';
        $dclass->update($_REQUEST['table_name'], $up1, " intid = '" . $up['request_id'] . "'");
        
    }
    
    $get_user_name = $dclass->select('name', $_REQUEST['table_name'], ' AND intid = \'' . $up['request_id'] . '\' ');
    
    $get_last_three_message = $dclass->select('name,message,dtadd', $_REQUEST['table_name'], ' AND ( intid = \'' . $up['request_id'] . '\' OR request_id = \'' . $up['request_id'] . '\' ) order by intid Desc limit 0, 3 ');
    
    $get_url_app = $dclass->select('app_url', 'tblmember_apps', ' AND intid = \'' . $up['app_id'] . '\' ');
    $id = $dclass->insert($_REQUEST['table_name'], $up);
    $gnrl->save_access_log($member_id, $_SESSION['agents_cust_id'], COMMUNICATR_ADDED);
    if ($id) {
        //Send Email Response to person
        $email_to = $_REQUEST['email'];
        $email_from = $gnrl->getSettings('varsenderemial');

        $email_subject = "" . BRAND . " - New reply has been received for request #".$_REQUEST['request_id'];
        
        $email_message .= '      
            <p>Hello ' . $get_user_name[0]['name'] . ',</p>';
            foreach($get_last_three_message as $val_last_three){
          $email_message .= "<p> <b>".$val_last_three['name']."</b>  ".date('F d, Y',strtotime($val_last_three['dtadd']))." </br>".$val_last_three['message']." </p>";  
        }
            $email_message .= '<!-- button -->
            <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <a style="background: #33cccc;border: none !important;border-radius: 3px;box-shadow: none;color: #fff !important;font-size: 14px !important;font-weight: 500 !important;padding: 6px 15px !important;text-shadow: none !important;" href="'.$get_url_app[0]['app_url'].'" target="_blank">View</a>
                </td>
              </tr>
            </table>
            <!-- /button -->
            
        ';
        $gnrl->email_communicatr($email_from, $email_to, "", "", $email_subject, $email_message, "html");
    }

    //echo $dclass->_sql;exit;
    if ($_FILES['app_logo']['name'] != '') {
        $up1['support_id'] = $id;
        foreach ($uparray['image'] as $value_up) {
            $up1['image'] = $value_up;
            $id1 = $dclass->insert('tblapp_support_attachment', $up1);
        }
    }
    if ($id) {
        $member_apps = $dclass->select("*", "tblmember_apps", " AND intid='" . $_POST['app_id'] . "' ");
        $getpem_files = $dclass->select("*", "tbl_support_agent_allocate", " AND app_id='" . $_POST['app_id'] . "' ");
        $all_device_token = $dclass->select("*", "tblapp_support", " AND app_id='" . $_POST['app_id'] . "' AND intid = '".$up['request_id']."' AND request_id = 0 ");
        $json_data = json_encode(array("request_id"=>$up['request_id'],"sdk_name"=>BRAND));
        if ($member_apps[0]['notification'] == 'active') {
            $message_custom = 'New reply has been received for request #'.$_REQUEST['request_id'];
            if ($member_apps[0]['app_type'] == 'ios') {

                if ($member_apps[0]['server_status'] == 'prod') {
                    $pemfile = 'files/pem/' . $getpem_files[0]['pem_prod_file'];
                    ios_notification($message_custom, $all_device_token[0]['device_token'], $getpem_files[0]['passphrase_prod'], $pemfile,$member_apps[0]['server_status'],$json_data);
                } else {
                    $pemfile = 'files/pem/' . $getpem_files[0]['pem_file'];
                    ios_notification($message_custom, $all_device_token[0]['device_token'], $getpem_files[0]['passphrase'], $pemfile,$member_apps[0]['server_status'],$json_data);
                }
            } else {

                android_notification($message_custom, $all_device_token[0]['device_token'], $getpem_files[0]['api_key']);
                
            }
        }
        $msg = $gnrl->getMessage("SUPPORT_SUCC", $lang_id);
        $data['output'] = 'S';
        $data['msg'] = $msg;
    } else {

        $msg = $gnrl->getMessage("SUPPORT_ERR", $lang_id);
        $data['output'] = 'F';
        $data['msg'] = $msg;
    }


    $data['startlimit'] = 0;
    $data['limit'] = 10;
    echo (json_encode($data));
} else if ($_REQUEST['table_name'] == 'tbl_support_agent_allocate') {

    $chk_agent = $dclass->select("*", "tbl_support_agent_allocate", " AND app_id='" . $_POST['app_id'] . "' ");
    if (empty($chk_agent)) {
        $up['member_id'] = $_POST['intid'];
        $up['app_id'] = $_POST['app_id'];
        $id = $dclass->insert('tbl_support_agent_allocate', $up);
        $gnrl->save_access_log($_SESSION['custid'], $_SESSION['agents_cust_id'], AGENT_ALLOCATED);
    } else {
        $up1['member_id'] = $_POST['intid'];
        $dclass->update('tbl_support_agent_allocate', $up1, " app_id = '" . $_POST['app_id'] . "'");
        $gnrl->save_access_log($_SESSION['custid'], $_SESSION['agents_cust_id'], AGENT_ALLOCATED);
    }
}
?>
