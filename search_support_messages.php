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
$file_type_array = array("pdf" => "pdf.png",
    "doc" => "doc.png",
    "docx" => "docx.png",
    "txt" => "txt.png",
    "xls" => "xls.png",
    "xlsx" => "xlsx.png",
    "png" => "png.png",
    "jpg" => "jpg.png",
    "jpeg" => "jpeg.png",
    "gif" => "gif.png",
    "mp4" => "mp4.png");


$file_array_for_popup = array("png" => "png.png",
    "jpg" => "jpg.png",
    "jpeg" => "jpeg.png",
    "gif" => "gif.png",
    "mp4" => "mp4.png");

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
        return "<div class='days_ago_1'>Just now</div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
    }
    //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "<div class='days_ago_1'>1 min ago</div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$minutes min ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
    //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return "<div class='days_ago_1'>1 hr ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$hours hrs ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return "<div class='days_ago_1'>yesterday </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$days days ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return "<div class='days_ago_1'>1 week ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$weeks weeks ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
    //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return "<div class='days_ago_1'>1 month ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$months months ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
    //Years
    else {
        if ($years == 1) {
            return "<div class='days_ago_1'>1 yr ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        } else {
            return "<div class='days_ago_1'>$years yrs ago </div> <div class='days_ago_2'>" . date("D M d", $time_ago) . "<br>" . date("H:i", $time_ago) . "</div>";
        }
    }
}

if ($_REQUEST['action'] == 'search_support') {
    foreach ($_SESSION['app_id'] as $app_id_data) {
        $final_app_id .= " tblapp_support.app_id = '$app_id_data' OR";
    }
    if (!empty($_SESSION['status'])) {
        if ($_SESSION['status'] == 'All') {
            $status = '';
        } else if ($_SESSION['status'] == 'Close') {
            $status = " AND status = '" . strtolower($_SESSION['status']) . "'";
        } else {
            $status = " AND status != 'close'";
        }
    }
    if (!empty($_SESSION['request_type'])) {
        $request_type = " AND request_type = '" . strtolower($_SESSION['request_type']) . "'";
    }
    if (!empty($_SESSION['version'])) {
        $version = " AND version = '" . $_SESSION['version'] . "'";
    }
    if (!empty($_SESSION['app_version'])) {
        $app_version = " AND app_version = '" . $_SESSION['app_version'] . "'";
    }

    if (!empty($_SESSION['app_id'])) {
        $final_sub_str = 'AND (' . substr($final_app_id, 0, -3) . ')';
    }
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

    $app_name_distinct = $dclass->select('*, tblapp_support.intid as request_id, (select dtadd as new_time from tblapp_support as aaa where (aaa.request_id = tblapp_support.intid OR aaa.intid = tblapp_support.intid) ORDER BY dtadd DESC LIMIT 1) as new_time ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id ' . $final_sub_str . ' ' . $status . ' ' . $request_type . ' ' . $version . ' ' . $app_version . '  ' . $append_data . ' AND request_id = 0  order by new_time desc');


    if (!empty($app_name_distinct)) {
        $html = "<ul>";
        foreach ($app_name_distinct as $list_key => $list_value):



            $app_get_unread_count = $dclass->select('*, tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND is_read = "N" AND (tblapp_support.intid = "' . $list_value['request_id'] . '" OR tblapp_support.request_id = "' . $list_value['request_id'] . '") order by tblapp_support.dtadd desc');

            $getlatest_reply = $dclass->select('*, tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND (tblapp_support.intid = "' . $list_value['request_id'] . '" OR tblapp_support.request_id = "' . $list_value['request_id'] . '") order by tblapp_support.dtadd desc');


            $date1 = $list_value['dtadd'];
            $sec = gethours($getlatest_reply[0]['dtadd']);


            if ($list_value['request_type'] == 'bug') {
                $class_image = 'request_i1';
            } else if ($list_value['request_type'] == 'feedback') {
                $class_image = 'request_i2';
            } else if ($list_value['request_type'] == 'query') {
                $class_image = 'request_i3';
            }
            if ($list_value['app_type'] == 'ios') {
                $icon_mobile_os = 'iphone_i';
            } else if ($list_value['app_type'] == 'android') {
                $icon_mobile_os = 'android_i';
            }


            $html .= "<li";
            if (count($app_get_unread_count) > 0) {
                $html .= " class='unread'";
            }
            $html .= " >";
            $html .= "<div class='col-xs-12 col-md-1'> <i class='apps_i ";
            $html .= $class_image;
            $html .= "'></i> <span class='req_l_t'>";
            $html .= $sec;
            $html .= "</span> </div>
																			
                    <div class='col-xs-12 col-md-11'>
                      <div class='row'>
																						
																							<div class='col-xs-12 col-md-12 new_communicatr_list'>
																						
																						<div class='col-xs-12 col-md-8'>
																						
																						 <div class='col-xs-12 col-md-12 req_top'>
                          <div class='fl'><a onclick='unread_message(" . $list_value['request_id'] . ")' href='respond-detail?support_id=" . $list_value['request_id'] . "&sel_app_id=" . $list_value['app_id'] . "'><span> </span>";
            $html .= $list_value['request_id'];
            $html .= "</a></div>
																			
																			
																			
																			 <div class='request_top_icon_'>
                              <div class='col-xs-12 col-md-4'><i class='apps_i ";
            if (!empty($list_value['app_name'])) {
                $html .= $icon_mobile_os;
            }
            $html .= " fl' title=''> </i>&nbsp;";
            $html .= $list_value['app_name'];
            $html .= "</div>
                             
																													<div class='col-xs-12 col-md-4'>";
            if (!empty($list_value['app_version'])) {
                $html .= "<i class='apps_i ver_i fl' title=''> </i>";
            }
            $html .= $list_value['app_version'];
            $html .= "</div>
                            </div>
                        </div>
																								
																						 <div class='col-xs-12 col-md-12 req_middel'>";
            $html .= $getlatest_reply[0]['message'];
            $html .= "</div>		
																						
																						</div>






																						<div class='col-xs-12 col-md-2'>
																						
																						 <div class='col-xs-12 col-md-12 preview_class_img'>
";

            $get_attached_response_images = $dclass->select('*', 'tblapp_support_attachment', ' AND support_id = ' . $getlatest_reply[0]['support_id'] . ' ');

            if (!empty($get_attached_response_images)) {




                $i = 0;
                foreach ($get_attached_response_images as $value_attached_resp):

                    $info = new SplFileInfo($value_attached_resp['image']);


                    if (empty($file_type_array[$info->getExtension()])) {
                        $image_name = 'other.png';
                    } else {
                        $image_name = $file_type_array[$info->getExtension()];
                    }

                    if ($info->getExtension() == 'jpeg' || $info->getExtension() == 'jpg' || $info->getExtension() == 'png') {
                        $des = SUPPORT_IMG . '/thumb/' . $value_attached_resp['image'];
                    } else {
                        $des = 'img/communicatr-detail-icon/' . $image_name;
                    }

                    $image_path1 = SUPPORT_IMG . '/' . $value_attached_resp['image'];

                    if (!empty($file_array_for_popup[$info->getExtension()])) {


                        $html .= "<div class=\"attach_file1\"> <a  onclick=\"view_detail('" . $getlatest_reply[0]['support_id'] . '_' . $i . "','" . $image_path1 . "');\" data-dialog" . $getlatest_reply[0]['support_id'] . "_$i =\"somedialog" . $getlatest_reply[0]['support_id'] . "_" . $i . "\" class=\"trigger\"> 
            <dd><img src=\"" . $des . "\" style=\"height:50px;width:50px\"></dd>
            <div class=\"popover bottom\">
              <div class=\"arrow\"></div>
              <div class=\"pdf-file\">" . $value_attached_resp['image'] . "</div>
            </div>
            </a></div>
            
            <div id=\"somedialog" . $getlatest_reply[0]['support_id'] . "_" . $i . "\" class=\"dialog\">
  <div class=\"dialog__overlay\"></div>
  <div class=\"dialog__content communicatr-detail_pupep\">
    <div class=\"popap-header\">
      <!--<h3 class=\"fl\">View Detail</h3>-->
      <button class=\"action fr\" data-dialog-close>&nbsp;</button>
    </div>
    <div class=\"popap-content\" id=\"view-image" . $getlatest_reply[0]['support_id'] . '_' . $i . "\"></div>
  </div>
</div>
            <script>
            $( document ).ready(function() {
            
            //alert('data-dialog" . $getlatest_reply[0]['support_id'] . "_$i');
    (function() {


            var dlgtrigger = document.querySelector( \"[data-dialog" . $getlatest_reply[0]['support_id'] . "_$i]\" );
            
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog" . $getlatest_reply[0]['support_id'] . "_$i' ) );

            dlg = new DialogFx( somedialog );
            
            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            

    })();
				
	});		
				
</script>";
                    } else {




                        $html .= "<div class=\"col-xs-12 col-md-2 attach_file1\"> <a   href=\"files/support/attachment/" . $value_attached_resp['image'] . "\">  
            <dd><img src=\" $des \"></dd>
            <div class=\"popover bottom\">
              <div class=\"arrow\"></div>
              <div class=\"pdf-file\"> " . $value_attached_resp['image'] . "</div>
            </div>
            </a></div>";
                    }
                    break;
                endforeach;
                $html .= "<div class=\"cl\"></div>";
            }




            $html .= "</div>
																						
																						</div>														
																						

																						<div class='col-xs-12 col-md-2 communicatr_name_'>
																						
																						<div class='fr'>";
            if (!empty($list_value['name'])) {
                $html .="<dd class='fl'><span>By</span> ";
            }
            
            $replytext = "Reply";
            if($list_value['status'] == "close")
            {
                $replytext = "Re-open";
            } 
            
            
            $html .= $list_value['name'];
            $html .= "</dd>";
            if (!empty($list_value['region'])) {
                $html .= "<dd class='fr'><i class='apps_i location'></i>";
            }
            $html .= $list_value['region'];
            $html .= "</dd>
																			
																			 <div class='col-xs-12 col-md-4'>";

            $html .= "<i class='apps_i ios_i fl' title=''> </i>&nbsp;";
            if ($list_value['app_type'] == 'ios'):
                $list_value['app_type'] = lcfirst(strtoupper($list_value['app_type']));
            endif;
            $html .= $list_value['app_type'] . ' ';
            $html .= $list_value['version'] . ' ' . $list_value['device'];
            $html .= "</div>
                          </div>
																										
																						</div>
																						
																						</div>
  
                       
                        <div class='col-xs-12 col-md-12 req_bottom'>
                          <div class='row'  style='padding:0 15px;'>
                              <div class='col-xs-12 col-md-1'> <a onclick='unread_message(" . $list_value['request_id'] . ")' href='respond-detail?support_id=" . $list_value['request_id'] . "&sel_app_id=" . $list_value['app_id'] . "'><i class='apps_i reply_i fl' title=''> </i>&nbsp; ".$replytext."</a> </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>";
        endforeach;

        $html .= "</ul>";
    }else {
        $html = "<ul><div class='no-record-found' style='text-align:center'><img title='' alt='' src='img/no_apps_found.png'></div>";

        $html .= "</ul>";
    }

    echo $html;
} else if ($_REQUEST['action'] == 'unset_session') {

    unset($_SESSION['show_status']);
    unset($_SESSION['show_type']);
    unset($_SESSION['show_os_ver']);
    unset($_SESSION['show_app_ver']);
    unset($_SESSION['show_agent']);
    unset($_SESSION['unread_msg']);
} else if ($_REQUEST['action'] == 'hide_app') {


    if (isset($_REQUEST['app_id'])) {
        $_SESSION['app_id'][$_REQUEST['app_id']] = $_REQUEST['app_id'];
    }



    $res = $dclass->select("tblmember_apps.*", "tblmember_apps", " AND tblmember_apps.intid = '" . $_REQUEST['app_id'] . "' ");

    $res_versions = $dclass->select('distinct app_version as version ', 'tblapp_support', ' AND app_id = ('.  $_REQUEST['app_id'].')');
   
    
    $data = array("app_type" => $res[0]['app_type'], "app_name" => $res[0]['app_name'], "app_id" => $res[0]['intid'],"app_versions"=>$res_versions,"app_logo"=>$res[0]['app_logo']);
    echo json_encode($data);
} else if ($_REQUEST['action'] == 'unset_data') {


    if (isset($_REQUEST['columns']['show_status'])) {

        if ($_REQUEST['columns']['show_status'] == "removeall") {
            unset($_SESSION['show_status']);
        } else {
            $_SESSION['removed_show_status'][$_REQUEST['columns']['show_status']] = $_REQUEST['columns']['show_status'];
            unset($_SESSION['show_status'][$_REQUEST['columns']['show_status']]);
        }
    } else if (isset($_REQUEST['columns']['show_type'])) {

        if ($_REQUEST['columns']['show_type'] == "removeall") {
            unset($_SESSION['show_type']);
        } else {
            unset($_SESSION['show_type'][$_REQUEST['columns']['show_type']]);
        }
    } else if (isset($_REQUEST['columns']['show_os_ver'])) {

        if ($_REQUEST['columns']['show_os_ver'] == "removeall") {
            unset($_SESSION['show_os_ver']);
        } else {
            unset($_SESSION['show_os_ver'][$_REQUEST['columns']['show_os_ver']]);
        }
    } else if (isset($_REQUEST['columns']['show_app_ver'])) {

        if ($_REQUEST['columns']['show_app_ver'] == "removeall") {
            unset($_SESSION['show_app_ver']);
        } else {
            unset($_SESSION['show_app_ver'][$_REQUEST['columns']['show_app_ver']]);
        }
    } else if (isset($_REQUEST['columns']['show_agent'])) {

        if ($_REQUEST['columns']['show_agent'] == "removeall") {
            unset($_SESSION['show_agent']);
        } else {
            unset($_SESSION['show_agent'][$_REQUEST['columns']['show_agent']]);
        }
    }
} else if ($_REQUEST['action'] == 'search_app') {
    if (isset($_REQUEST['columns']['stat'])) {
        if (isset($_REQUEST['columns']['show_status'])) {
            $_SESSION[$_REQUEST['columns']['key_show_status']][$_REQUEST['columns']['show_status']] = $_REQUEST['columns']['show_status'];
            unset($_SESSION['removed_show_status'][$_REQUEST['columns']['show_status']]);
        } else if (isset($_REQUEST['columns']['show_type'])) {
            $_SESSION[$_REQUEST['columns']['key_show_type']][$_REQUEST['columns']['show_type']] = $_REQUEST['columns']['show_type'];
        } else if (isset($_REQUEST['columns']['show_os_ver'])) {
            $_SESSION[$_REQUEST['columns']['key_show_os_ver']][$_REQUEST['columns']['show_os_ver']] = $_REQUEST['columns']['show_os_ver'];
        } else if (isset($_REQUEST['columns']['show_app_ver'])) {
            $_SESSION[$_REQUEST['columns']['key_show_app_ver']][$_REQUEST['columns']['show_app_ver']] = $_REQUEST['columns']['show_app_ver'];
        } else if (isset($_REQUEST['columns']['show_agent'])) {
            $_SESSION[$_REQUEST['columns']['key_show_agent']][$_REQUEST['columns']['show_agent']] = $_REQUEST['columns']['show_agent'];
        }
    } else {
        if (isset($_REQUEST['columns']['show_status'])) {
            $_SESSION[$_REQUEST['columns']['key_show_status']] = $_REQUEST['columns']['show_status'];
        } else if (isset($_REQUEST['columns']['show_type'])) {
            $_SESSION[$_REQUEST['columns']['key_show_type']] = $_REQUEST['columns']['show_type'];
        } else if (isset($_REQUEST['columns']['show_os_ver'])) {
            $_SESSION[$_REQUEST['columns']['key_show_os_ver']] = $_REQUEST['columns']['show_os_ver'];
        } else if (isset($_REQUEST['columns']['show_app_ver'])) {
            $_SESSION[$_REQUEST['columns']['key_show_app_ver']] = $_REQUEST['columns']['show_app_ver'];
        } else if (isset($_REQUEST['columns']['show_agent'])) {
            $_SESSION[$_REQUEST['columns']['key_show_agent']] = $_REQUEST['columns']['show_agent'];
        }
    }
        
    
    $fstatus = '';
    if($_SESSION['show_status'])
    {
        if (!in_array("Test", $_SESSION['show_status']) && !$_SESSION['unread_msg'])
        {
                 $fstatus .= " is_live = 1 AND";
        }
        if(!in_array("Archive", $_SESSION['show_status']) && !$_SESSION['unread_msg'])
        {
                 $fstatus .= " is_archive = 0 AND";
        }        
        
    }    
    
    
    foreach ($_SESSION['show_status'] as $show_status_value) {

        if ($show_status_value == 'Open') {
            
            $status .= " status = 'due' OR status = 'replied' OR";
           
        }else if ($show_status_value == 'Close') {
            
                 $status .= " status = 'close' OR";
            
        }else if ($show_status_value == 'Archive') {
           
           $status .= " is_archive = '1' OR";
           
        }else if ($show_status_value == 'Test') {
           
            $status .= " is_live = '0' OR";
            
        }else if ($show_status_value == 'Live') {
            
            $status .= " is_live = '1' OR";
            
        } else {
            
            $status .= " status = '" . strtolower($show_status_value) . "' OR ";
        }
    }

    foreach ($_SESSION['show_type'] as $show_type_value) {


        $request_type .= " request_type = '" . strtolower($show_type_value) . "' OR ";
    }



    foreach ($_SESSION['show_os_ver'] as $show_os_ver_value) {

        $show_os_ver .= " version = '" . $show_os_ver_value . "' OR ";
    }



    foreach ($_SESSION['show_app_ver'] as $show_app_ver_value) {

        $show_app_ver .= " app_version = '" . $show_app_ver_value . "' OR ";
    }
    if ($member_id == $_SESSION['agents_cust_id']) {
        if (!empty($_SESSION['show_agent'])) {
            foreach ($_SESSION['show_agent'] as $show_agent_value) {
                $append_data .= ' tblapp_support.member_id = \'' . $show_agent_value . '\' OR ';
            }
        } else {
            $append_data = ' tblapp_support.parent_id = \'' . $member_id . '\'';
        }
    } else {
        if ($_SESSION['role'] == 'admin') {
            $append_data = ' tblapp_support.parent_id = \'' . $member_id . '\'';
        } else {
            $append_data = ' tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
        }
    }

    foreach ($_SESSION['app_id'] as $app_id_data) {
        $final_app_id .= " tblapp_support.app_id = '$app_id_data' OR";
    }

    if (!empty($_SESSION['show_status'])) {
        
        if($fstatus)
        {
            $final_status = 'AND '.substr($fstatus, 0, -4).' AND (' . substr($status, 0, -3) . ')';
        }
        else
        {
            $final_status = 'AND (' . substr($status, 0, -3) . ')';
        }    
        
      
    }

    if (!empty($_SESSION['show_type'])) {
        $final_type = 'AND (' . substr($request_type, 0, -3) . ')';
    }

    if (!empty($_SESSION['show_os_ver'])) {
        $final_show_os_ver = 'AND (' . substr($show_os_ver, 0, -3) . ')';
    }

    if (!empty($_SESSION['show_app_ver'])) {
        $final_show_app_ver = 'AND (' . substr($show_app_ver, 0, -3) . ')';
    }

    if (!empty($_SESSION['show_agent'])) {
        $final_show_agent = 'AND (' . substr($append_data, 0, -3) . ')';
    }else{
        $final_show_agent = 'AND '.$append_data;
    }

    if (!empty($_SESSION['app_id'])) {
        $final_sub_str = 'AND (' . substr($final_app_id, 0, -3) . ')';
    }
    
    if(!$final_status)
    {
        if($_SESSION['unread_msg'])
        { 
            $final_status = "";
       
        }
        else
        {
            $final_status .= "AND (status = 'due' OR status = 'replied') AND is_archive = 0 AND is_live = 1";
        }    
    }
    
    $unread_list = '';
    if($_SESSION['unread_msg']){
       
        $unread_list = ' AND is_read = "N" ';
    }
    
    
    $app_name_distinct = $dclass->select('*, tblapp_support.intid as request_id, (select dtadd as new_time from tblapp_support as aaa where (aaa.request_id = tblapp_support.intid OR aaa.intid = tblapp_support.intid) ORDER BY dtadd DESC LIMIT 1) as new_time ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id ' . $final_sub_str . ' ' . $final_status . ' ' . $final_type . ' ' . $final_show_os_ver . ' ' . $final_show_app_ver . '  ' . $final_show_agent . ' '.$unread_list.' AND request_id = 0 order by new_time desc');

    if (!empty($app_name_distinct)) {
        $html = "<ul>";
        foreach ($app_name_distinct as $list_key => $list_value):



            $app_get_unread_count = $dclass->select('*, tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND is_read = "N" AND (tblapp_support.intid = "' . $list_value['request_id'] . '" OR tblapp_support.request_id = "' . $list_value['request_id'] . '") order by tblapp_support.dtadd desc');

            $getlatest_reply = $dclass->select('*, tblapp_support.intid as support_id', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id AND (tblapp_support.intid = "' . $list_value['request_id'] . '" OR tblapp_support.request_id = "' . $list_value['request_id'] . '") order by tblapp_support.dtadd desc');

            
            $date1 = $list_value['dtadd'];

            $sec = gethours($getlatest_reply[0]['dtadd']);


            if ($list_value['request_type'] == 'bug') {
                $class_image = 'request_i1';
            } else if ($list_value['request_type'] == 'feedback') {
                $class_image = 'request_i2';
            } else if ($list_value['request_type'] == 'query') {
                $class_image = 'request_i3';
            }
            if ($list_value['app_type'] == 'ios') {
                $icon_mobile_os = 'iphone_i';
            } else if ($list_value['app_type'] == 'android') {
                $icon_mobile_os = 'android_i';
            }


            $html .= "<li id='".$list_value['request_id']."_liid'";
            if (count($app_get_unread_count) > 0) {
                $html .= " class='unread'";
            }else if($list_value['status'] == "close"){
                $html .= " class='sclose'";
            }
            $html .= " >";
            //}
            $html .= "<div class='col-xs-12 col-md-1'> <i class='apps_i ";
            $html .= $class_image;
            $html .= "'></i> <span class='req_l_t'>";
            $html .= $sec;
            $html .= "</span> </div>
																			
                    <div class='col-xs-12 col-md-11'>
                      <div class='row'>
																						
																							<div class='col-xs-12 col-md-12 new_communicatr_list'>
																						
																						<div class='col-xs-12 col-md-8'>
																						
																						 <div class='col-xs-12 col-md-12 req_top'>
                          <div class='fl'><a onclick='unread_message(" . $list_value['request_id'] . ")' href='respond-detail?support_id=" . $list_value['request_id'] . "&sel_app_id=" . $list_value['app_id'] . "'><span> </span>";
            $html .= $list_value['request_id'];
            $html .= "</a></div>
																			
																			
																			
																			 <div class='request_top_icon_'>
                              <div class='col-xs-12 col-md-4'><i class='apps_i ";
            if (!empty($list_value['app_name'])) {
                $html .= $icon_mobile_os;
            }
            $html .= " fl' title=''> </i>&nbsp;";
            $html .= $list_value['app_name'];
            $html .= "</div>
                             
																													<div class='col-xs-12 col-md-4'>";
            if (!empty($list_value['app_version'])) {
                $html .= "<i class='apps_i ver_i fl' title=''> </i>";
            }
            $html .= $list_value['app_version'];
            $html .= "</div>
                            </div>
                        </div>
																								
																						 <div class='col-xs-12 col-md-12 req_middel'>";
            $html .= $getlatest_reply[0]['message'];
            $html .= "</div>		
																						
																						</div>






																						<div class='col-xs-12 col-md-2'>
																						
																						 <div class='col-xs-12 col-md-12 preview_class_img'>
";

            $get_attached_response_images = $dclass->select('*', 'tblapp_support_attachment', ' AND support_id = ' . $getlatest_reply[0]['support_id'] . ' ');

            if (!empty($get_attached_response_images)) {




                $i = 0;
                foreach ($get_attached_response_images as $value_attached_resp):

                    $info = new SplFileInfo($value_attached_resp['image']);


                    if (empty($file_type_array[$info->getExtension()])) {
                        $image_name = 'other.png';
                    } else {
                        $image_name = $file_type_array[$info->getExtension()];
                    }

                    if ($info->getExtension() == 'jpeg' || $info->getExtension() == 'jpg' || $info->getExtension() == 'png') {
                        $des = SUPPORT_IMG . '/thumb/' . $value_attached_resp['image'];
                    } else {
                        $des = 'img/communicatr-detail-icon/' . $image_name;
                    }

                    //$des = 'img/communicatr-detail-icon/'.$image_name;

                    $image_path1 = SUPPORT_IMG . '/' . $value_attached_resp['image'];

                    if (!empty($file_array_for_popup[$info->getExtension()])) {


                        $html .= "<div class=\"attach_file1\"> <a  onclick=\"view_detail('" . $getlatest_reply[0]['support_id'] . '_' . $i . "','" . $image_path1 . "');\" data-dialog" . $getlatest_reply[0]['support_id'] . "_$i =\"somedialog" . $getlatest_reply[0]['support_id'] . "_" . $i . "\" class=\"trigger\"> 
            <dd><img src=\"" . $des . "\" style=\"height:50px;width:50px\"></dd>
            <div class=\"popover bottom\">
              <div class=\"arrow\"></div>
              <div class=\"pdf-file\">" . $value_attached_resp['image'] . "</div>
            </div>
            </a></div>
            
            <div id=\"somedialog" . $getlatest_reply[0]['support_id'] . "_" . $i . "\" class=\"dialog\">
  <div class=\"dialog__overlay\"></div>
  <div class=\"dialog__content communicatr-detail_pupep\">
    <div class=\"popap-header\">
      <!--<h3 class=\"fl\">View Detail</h3>-->
      <button class=\"action fr\" data-dialog-close>&nbsp;</button>
    </div>
    <div class=\"popap-content\" id=\"view-image" . $getlatest_reply[0]['support_id'] . '_' . $i . "\"></div>
  </div>
</div>
            <script>
            $( document ).ready(function() {
            
            //alert('data-dialog" . $getlatest_reply[0]['support_id'] . "_$i');
    (function() {


            var dlgtrigger = document.querySelector( \"[data-dialog" . $getlatest_reply[0]['support_id'] . "_$i]\" );
            
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog" . $getlatest_reply[0]['support_id'] . "_$i' ) );

            dlg = new DialogFx( somedialog );
            
            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            

    })();
				
	});		
				
</script>";
                    } else {




                        $html .= "<div class=\"col-xs-12 col-md-2 attach_file1\"> <a   href=\"files/support/attachment/" . $value_attached_resp['image'] . "\">  
            <dd><img src=\" $des \"></dd>
            <div class=\"popover bottom\">
              <div class=\"arrow\"></div>
              <div class=\"pdf-file\"> " . $value_attached_resp['image'] . "</div>
            </div>
            </a></div>";
                    }

                    break;
                endforeach;
                $html .= "<div class=\"cl\"></div>";
            }




            $html .= "</div>
																						
																						</div>														
																						

																						<div class='col-xs-12 col-md-2 communicatr_name_'>
																						
																						<div class='fr'>";
            if (!empty($list_value['name'])) {
                $html .="<dd class='fl'><span>By</span> ";
            }
            $html .= $list_value['name'];
            $html .= "</dd>";
            if (!empty($list_value['region'])) {
                $html .= "<dd class='fr'><i class='apps_i location'></i>";
            }
            $html .= $list_value['region'];
            $html .= "</dd>
																			
																			 <div class='col-xs-12 col-md-4'>";

            $html .= "<i class='apps_i ios_i fl' title=''> </i>&nbsp;";
            if ($list_value['app_type'] == 'ios'):
                $list_value['app_type'] = lcfirst(strtoupper($list_value['app_type']));
            endif;
            
            $replytext = "Reply";
            if($list_value['status'] == "close")
            {
                $replytext = "Re-open";
            }    
            $html .= $list_value['app_type'] . ' ';
            $html .= $list_value['version'] . ' ' . $list_value['device'];
            $html .= "</div>
                          </div>
                            <div class='col-xs-12 col-md-12 req_bottom'>
                                <div class='row'  style='padding:0 15px;'>
                                    <div class='col-xs-12 col-md-1'>
                                        <a onclick='unread_message(" . $list_value['request_id'] . ")' href='respond-detail?support_id=" . $list_value['request_id'] . "&sel_app_id=" . $list_value['app_id'] . "'><i class='apps_i reply_i fl' title=''> </i>&nbsp; ".$replytext."</a>
                                        <br/>";
                        
            if($list_value['is_archive'] == "1")
            {
                $html .= "<span id='archive_".$list_value['request_id']."'><a href='javascript:void(0);' onclick='unarchive_message(" . $list_value['request_id'] . ")'><i class='apps_i reply_i fl' title=''> </i>&nbsp;Un-Archive</a></span>";
            }    
            else
            {
                $html .= "<span id='archive_".$list_value['request_id']."'><a href='javascript:void(0);' onclick='archive_message(" . $list_value['request_id'] . ")'><i class='apps_i reply_i fl' title=''> </i>&nbsp;Archive</a></span>";
            }    
                                        
                                        
             $html .=               "</div>
                               </div>
                           </div>
                      </div>
                    </div>
                  </li>";
        endforeach;

        $html .= "</ul>";
    }else {
        $html = "<ul><div class='no-record-found' style='text-align:center'><img title='' alt='' src='img/no_apps_found.png'></div>";

        $html .= "</ul>";
    }

    echo $html;
}
