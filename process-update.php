<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];
extract($_REQUEST);

$values = $_REQUEST;

if (($_REQUEST['table_name'] == 'tblmember') && ($_REQUEST['type'] == '')) {

    $chk = $dclass->select("intid", $_REQUEST['table_name'], " AND username ='" . strtolower($values['username']) . "' AND intid!='" . $values['intid'] . "' ");
    $res = $dclass->select("*", "tblmember", " AND intid='" . $values['intid'] . "' ");
    if (count($chk) > 0) {
        $msg = $gnrl->getMessage("USER_EXISTS", $lang_id);
        $data['output'] = 'F';
        $data['msg'] = $msg;
    } else {
        if ($values['password'] == "" && $values['new_password'] != '') {

            $msg = $gnrl->getMessage("CURRENT_PASSWORD_REQ", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        } else if (md5($values['password']) != $res[0]['password'] && $values['password'] != "") {

            $msg = $gnrl->getMessage("CURR_PASS_NOT_MATCH", $lang_id);
            $data['output'] = 'F';
            $data['msg'] = $msg;
        } else {
            $t = explode(" ", $values['name']);
            $up['fname'] = $values['fname'];
            $up['lname'] = $values['lname'];
            $up['timezone'] = $values['timezone'];
            if ($values['url'] == 'profile') {
                $_SESSION['custname'] = $up['fname'] . ' ' . $up['lname'];
            }
            $up['username'] = $values['username'];
            $up['email'] = $values['email'];
            $up['intid'] = $values['intid'];
            if ($values['new_password'] != '')
                $up['password'] = md5($values['new_password']);
            if ($values['new_password_i'] != '') {
                $up['password'] = md5($values['new_password_i']);
            }
            if ($del_old_logo != '') {
                unlink(USER_LOGO . "/" . $old_logo);
                $up['logo'] = "";
            }

            if ($_FILES['logo']['name'] != '') {

                $filename = time() . $gnrl->makefilename($_FILES['logo']['name']);
                $des = USER_LOGO . "/" . $filename;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $des)) {
                    $up['logo'] = $filename;
                    $data['logo'] = 'Y';
                }
            }
            $up['company'] = $company;

            if ($del_old_company_logo != '') {
                unlink(COMPANY_LOGO . "/" . $del_old_company_logo);
                $up['company_logo'] = "";
            }

            if ($_FILES['company_logo']['name'] != '') {
                $filename = time() . $gnrl->makefilename($_FILES['company_logo']['name']);
                $des = COMPANY_LOGO . "/" . $filename;
                if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $des)) {
                    $up['company_logo'] = $filename;
                    $data['company_logo'] = 'Y';
                }
            }


            $up['dtmod'] = date("Y-m-d h:i:s");
            if ($values['status'] != 'waiting') {
                if ($values['status'])
                    $up['status'] = 'active';
                else
                    $up['status'] = 'inactive';
            }else {
                $up['status'] = 'waiting';
            }
            $up['role'] = $values['role'];

            $up['company'] = @$values['company'];

            $dclass->update("tblmember", $up, " intid='" . $values['intid'] . "' ");

            if ($up['logo'] != '' && $values['old_logo'] == '') {
                $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=" . USER_LOGO . "/" . $up['logo'];
            } else if ($up['logo'] != '' && $values['old_logo'] != '') {


                $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=" . USER_LOGO . "/" . $up['logo'];
            } else if ($up['logo'] == '' && $values['old_logo'] != '') {

                if ($values['old_logo'] != 'img/s_user_img.png') {
                    $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=" . USER_LOGO . "/" . $values['old_logo'];
                    if (!file_exists($logo_path)) {
                        $logo_path = "img/s_user_img.png";
                    }
                } else {
                    $logo_path = "img/s_user_img.png";
                }
            } else if ($up['logo'] == '' && $values['old_logo'] == '') {
                $logo_path = "img/s_user_img.png";
            }




            $html .= "
                <td class='agent_img'><img src='" . $logo_path . "' alt='' height='80' width='80' /></td>
                <td>";
            $html .= '<div class="tital">' . $up['fname'] . " " . $up['lname'] . '</div><div class="role_s">' . ucfirst($up['role']) . '</div>';
            $html .= "</td>";
            $html .= "<td>";
            $html .= $up['username'];
            $html .= "</td>
                <td>";
            $html .= $up['email'];
            $html .= "</td>
                
                <td>";
            if ($up['status'] == 'active') {
                $html .= '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
            } else if ($up['status'] == 'waiting') {
                $html .= '<span class="waiting_icon" title="Active"><i class="fa active"></i></span>';
            } else {
                $html .= '<span class="inactive_icon" title="Inactive"></span>';
            }
            $html .= "</td>";
            $html .= '
                
<td class="print- print-t"><div class="generate"> <a onclick="editrow(' . $up['intid'] . ');" href="javascript:;" class="print trigger" data-dialog="somedialog" >
<i class="apps_i edit_icon_"  title=""></i>
                      <div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Edit</div>
                      </div>
                      </a> 
                      <!--<a href="javascript:;"  onclick="delete_agent(' . $up['intid'] . ');" class="pdf"><i class="apps_i remove_icon_" title="" ></i>
                      <div class="popover top">
                        <div class="arrow"><i class="fa fa-fw fa-caret-up"></i></div>
                        <div class="pdf-file">Delete</div>
                      </div>
                      </a>-->
                      </div></td>';

            if ($_REQUEST['url'] == 'profile') {
                $msg = $gnrl->getMessage("PROFILE_SUC", $lang_id);
            } else {
                $msg = $gnrl->getMessage("AGENT_UPD", $lang_id);
            }
            $data['output'] = 'S';
            $data['msg'] = $msg;
            $data['intid'] = $up['intid'];
            $data['html'] = $html;
            $data['name'] = $up['fname'] . ' ' . $up['lname'];
            $data['email'] = $up['email'];
        }
    }
    echo (json_encode($data));
}
if ($_REQUEST['type'] == 'change_notification') {

    $up['notification'] = $_REQUEST['noti'];
    $dclass->update($_REQUEST['table_name'], $up, " intid='" . $_REQUEST['id'] . "'");


    $data['output'] = 'S';
    $data['msg'] = 'Record Updated Successfully';
    echo (json_encode($data));
}
if ($_REQUEST['type'] == 'remove_pem_file') {

    if ($_REQUEST['noti'] == 'dev') {
        $up['pem_file'] = '';
        unlink('files/pem/' . $_REQUEST['filename']);
    } else {
        $up['pem_prod_file'] = '';
        unlink('files/pem/' . $_REQUEST['filename']);
    }

    $dclass->update($_REQUEST['table_name'], $up, " intid='" . $_REQUEST['id'] . "' ");


    $data['output'] = 'S';
    $data['msg'] = 'File Removed Successfully';
    echo (json_encode($data));
}

if ($_REQUEST['type'] == 'change_status') {

    if ($_REQUEST['table_name'] == 'tblmember_app_features' || $_REQUEST['table_name'] == 'tblmember_features') {
        $up = array('feature_status' => $_REQUEST['status']);
    }
    if (!empty($_REQUEST['id'])) {
        $dclass->update($_REQUEST['table_name'], $up, " intid='" . $_REQUEST['id'] . "' AND member_id = '" . $member_id . "'");
    } else {

        $dclass->update('tblmember_features', $up, " member_id = '" . $member_id . "' AND feature_id = '" . $_REQUEST['feature_id'] . "'");
        $dclass->update($_REQUEST['table_name'], $up, " member_id = '" . $member_id . "' AND feature_id = '" . $_REQUEST['feature_id'] . "'");
    }

    $data['output'] = 'S';
    $data['msg'] = 'Record Updated Successfully';
    echo (json_encode($data));
}
if ($_REQUEST['table_name'] == 'tblapp_support') {

    if ($_REQUEST['type'] == "archive") {
        $up['is_archive'] = '1';
        $dclass->update($_REQUEST['table_name'], $up, "intid='" . $_REQUEST['intid'] . "'");
        $res = $dclass->select("status", "tblapp_support", " AND intid='" . $values['intid'] . "' ");
        if (in_array("Archive", $_SESSION['show_status'])) {
            echo "modify";
        } else {
            echo "hide";
        }
        die;
    } else if ($_REQUEST['type'] == "unarchive") {

        $up['is_archive'] = '0';
        $dclass->update($_REQUEST['table_name'], $up, "intid='" . $_REQUEST['intid'] . "'");
        $res = $dclass->select("status", "tblapp_support", " AND intid='" . $values['intid'] . "' ");
        $laststatus = $res[0]['status'];
        if ($laststatus == "due" OR $laststatus == "replied") {
            $laststatus = "Open";
        } else if ($laststatus == "close") {
            $laststatus = "Close";
        }


        if (in_array($laststatus, $_SESSION['show_status'])) {
            echo "modify";
        } else {
            echo "hide";
        }
        die;
    } else {
        $up['is_read'] = 'Y';
        $dclass->update($_REQUEST['table_name'], $up, " (intid='" . $_REQUEST['intid'] . "' OR request_id = '" . $_REQUEST['intid'] . "') ");
        echo "success";
        die;
    }
}
?>
