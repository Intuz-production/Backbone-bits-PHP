<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/

require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];

if($_REQUEST['refresh']=='request_type'){
    unset($_SESSION['request_type']);
}
else if($_REQUEST['refresh']=='version'){
    unset($_SESSION['version']);
}
else if($_REQUEST['refresh']=='app_version'){
    unset($_SESSION['app_version']);
}
else if($_REQUEST['refresh']=='status'){
    unset($_SESSION['status']);
}
else if($_REQUEST['refresh']=='agent'){
    unset($_SESSION['support_agents']);
}
else{
    unset($_SESSION['app_id'][$_REQUEST['app_id']]);

    $res = $dclass->select("tblmember_apps.*", "tblmember_apps", " AND tblmember_apps.intid = '".$_REQUEST['app_id']."' ");

    if (!empty($_SESSION['app_id'])) {
        $string_id1 .= implode(',', $_SESSION['app_id']);
            
        $get_distinct_app_version_selected1 = $dclass->select('distinct app_version as version', 'tblapp_support', ' AND app_id IN (' . $string_id1 . ') ');
        $get_distinct_app_version_selected2 = array();
        foreach ($get_distinct_app_version_selected1 as $key => $value) {
            $get_distinct_app_version_selected2[] = $value['version'];
        }
        $get_distinct_app_version_selected1 = $get_distinct_app_version_selected2;
    }

    if($res[0]['app_logo'] != '')
        $app_img_path = "memimages.php?max_width=118&max_width=118&imgfile=".APP_LOGO."/".$res[$i]['app_logo'] ;
    else
        $app_img_path = "img/no_image_available.jpg";

    if($res[0]['app_status']=='active'){
        $icon_active = 'active_icon';
    }
    else {
        $icon_active = 'inactive_icon';
    }

    if($res[0]['app_type']=='ios'){
        $os_icon = 'iphone_i';
    }
    else {
        $os_icon = 'android_i';
    }

    $html = "<div onclick='hide_app(".$res[0]['intid'].")' id='".$res[0]['intid']."' class='col-xs-6 col-md-3'>
            <a href='javascript:;' class='thumbnail'>
                <div class='logo-icon'><img src='".$app_img_path."' ></div>
                <div class='logo-title'> 
                    <i class='apps_i ".$os_icon."' title=''>&nbsp;</i> 
                    <span class='".$icon_active."'></span>
                    <div class='cl'></div>";
                    $html .= $res[0]['app_name'];
                    $html .=  "</div>
            </a>
        </div>";

    $data = array('html' => $html, "selected_app_versions" => $get_distinct_app_version_selected1);
    echo json_encode($data);
}