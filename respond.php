<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php
require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}


if(!isset($_SESSION['show_status']) && !isset($_SESSION['unread_msg']))
{
   
    $_SESSION['show_status']['Open']='Open';
}     


$member_id = $_SESSION['custid'];

$app_name_distinct = $dclass->select('*, tblapp_support.intid as request_id ', 'tblapp_support, tblmember_apps', ' AND tblmember_apps.intid = tblapp_support.app_id AND tblapp_support.member_id = ' . $member_id . ' AND request_id = 0  order by tblapp_support.dtadd desc');

$get_app_ids = $dclass->select('intid', 'tblmember_apps', ' AND member_id = \'' . $member_id . '\' ');

foreach ($get_app_ids as $value_get_app_ids) {
    $string_id .= $value_get_app_ids['intid'] . ',';
}
$get_distinct_os_version = $dclass->select('distinct version', 'tblapp_support', ' AND app_id IN (' . substr($string_id, 0, -1) . ') ');

$get_distinct_app_version = $dclass->select('distinct app_version', 'tblapp_support', ' AND app_id IN (' . substr($string_id, 0, -1) . ') ');

$get_publish_date = $dclass->select('start_date, app_id, version, count( * ) AS vcount', 'tblapp_whatsnew', ' AND app_id IN (' . substr($string_id, 0, -1) . ') group by version ');
if (!empty($_SESSION['app_id'])) {

    foreach ($_SESSION['app_id'] as $val_app_ver_id) {
        $string_id1 .= $val_app_ver_id . ',';
    }

    $get_distinct_app_version_selected = $dclass->select('distinct app_version as version', 'tblapp_support', ' AND app_id IN (' . substr($string_id1, 0, -1) . ') ');
}


include(INC . "header.php");
include INC . "left_sidebar.php";
?>

<style>
    li.status ul li a.forcered{
        color:#828282 !important;
    }
    li.status ul li a.unselected{
        color:#E1E1E1 !important;
    }
</style>


<aside class="right-side">
    <div class="right_sidebar">
        <div class="add-apps">
            <div class="col-xs-12 col-md-12"> 
            </div>

            <div class="cl"></div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="communicatr">
                        <div class="col-xs-12 col-md-12">

                            <div class="communicatr_list">


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- ============================================================================== --> 
<!-- Footer --> 
<!-- ============================================================================== -->

<?php include 'inc/footer.php'; ?>

<script>

    function unread_message(intid) {
        $.ajax({
            type: "POST",
            url: "process-update.php",
            async: false,
            data: {table_name: "tblapp_support", intid: intid},
            success: function (data) {

            }
        });
    }

    function archive_message(intid) {
        $.ajax({
            type: "POST",
            url: "process-update.php",
            async: false,
            data: {table_name: "tblapp_support", intid: intid, type: "archive"},
            success: function (data) {
               
               if(data == "hide")
               {
                   $("#" + intid + "_liid").fadeOut(300, function() { $(this).remove(); });
                   var licount = $(".communicatr_list").find("li").length;
                   if(licount == "1")
                   {
                        $(".communicatr_list").find("ul").html('<div style="text-align:center" class="no-record-found"><img src="img/no_apps_found.png" alt="" title=""></div>');
                   }
               }
               else
               {
                    var archivetext = "<a href='javascript:void(0);' onclick='unarchive_message("+intid+")'>Un-Archive</a>";
                    $("#archive_" + intid).html(archivetext);
                    
               }

            }
        });
    }

    function unarchive_message(intid) {
        

         $.ajax({
            type: "POST",
            url: "process-update.php",
            async: false,
            //dataType: "json",
            data: {table_name: "tblapp_support", intid: intid, type:"unarchive"},
            success: function (data) {
                if(data == "hide")
                {
                    
                    $("#" + intid + "_liid").fadeOut(300, function() { $(this).remove(); });
                    var licount = $(".communicatr_list").find("li").length;
                    if(licount == "1")
                    {
                        $(".communicatr_list").find("ul").html('<div style="text-align:center" class="no-record-found"><img src="img/no_apps_found.png" alt="" title=""></div>');
                    }    
                   
                }   
                else
                {
                    var archivetext = "<a href='javascript:void(0);' onclick='archive_message("+intid+")'>Archive</a>";
                    $("#archive_" + intid).html(archivetext);
                    
                }    
            }

         });
         
    }

<?php if (count($get_distinct_os_version) <= 1) { ?>

<?php } ?>
    var show_status = {};
    var show_type = {};
    var show_os_ver = {};
    var show_app_ver = {};
    var show_agent = {};

    var unset_show_status = {};
    var unset_show_type = {};
    var unset_show_os_ver = {};
    var unset_show_app_ver = {};
    var unset_show_agent = {};
    function swap_text(type, id, flag) {

var type_publish = type.replace(/\./g, "\\.");
        if (id == 'All_' + flag) {

            $("#" + flag + "_list > li").each(function () {
                var splitid = $(this).attr("id");
                var split_str = splitid.split("_");
                if ($(this).attr("id") != 'All_' + flag) {
                    if (flag == 'show_agent') {
                        var slpt = split_str[0].split('@~');
                        split_str[0] = slpt[0];
                        $("#" + flag).append('<li><span class="menu_t">' + slpt[1] + '</span><input name="' + flag + '[]" type="hidden" id="' + split_str[0] + '_' + flag + '" value="' + split_str[0] + '"><i class="fa"><a href="javascript:;" onclick="remove_swap_text(\'' + split_str[0] + '\',\'' + split_str[0] + '_' + flag + '\', \'' + flag + '\')"><i class="apps_i fa close_i"></i></a></i></li>');
                    } else {
                        if (flag == 'show_app_ver') {
                            var type_publish = split_str[0].replace(/\./g, "\\.");
                            if ($(this).find('a').hasClass("forcered")) {
                                $("#" + flag).append('<li><span class="menu_t">' + split_str[0] + '</span><input name="' + flag + '[]" type="hidden" id="' + split_str[0] + '_' + flag + '" value="' + split_str[0] + '"><i class="fa"><a href="javascript:;" onclick="remove_swap_text(\'' + split_str[0] + '\',\'' + split_str[0] + '_' + flag + '\', \'' + flag + '\')"><i class="apps_i fa close_i"></i></a></i></li><span id="'+split_str[0]+'_publish_date_selected" class="show_all_data2_"><small>'+$("#" + type_publish+'_publish_date').text()+'</small></span>');
                                //alert(split_str[0] + '_' + flag);    
                                var flag_id_app_ver = split_str[0] + '_' + flag;
                                var appver_id = flag_id_app_ver.replace(/\./g, "\\.");
                                //alert(appver_id);
                                $("#" + appver_id).remove();
                                $("#All_" + flag).remove();
                                $("#" + type_publish+'_publish_date').remove();
                            }
                        } else {
                            $("#" + flag).append('<li><span class="menu_t">' + split_str[0] + '</span><input name="' + flag + '[]" type="hidden" id="' + split_str[0] + '_' + flag + '" value="' + split_str[0] + '"><i class="fa"><a href="javascript:;" onclick="remove_swap_text(\'' + split_str[0] + '\',\'' + split_str[0] + '_' + flag + '\', \'' + flag + '\')"><i class="apps_i fa close_i"></i></a></i></li>');
                        }
                    }
                    if (flag == 'show_status') {
                        show_status[split_str[0]] = split_str[0];
                    } else if (flag == 'show_type') {
                        show_type[split_str[0]] = split_str[0];
                    } else if (flag == 'show_os_ver') {
                        show_os_ver[split_str[0]] = split_str[0];
                    } else if (flag == 'show_app_ver') {
                        if ($(this).find('a').hasClass("forcered")) {
                            show_app_ver[split_str[0]] = split_str[0];
                        }
                    } else if (flag == 'show_agent') {
                        show_agent[split_str[0]] = split_str[0];
                    }

                }
            });
            if (flag == 'show_status') {
                var obj = {show_status: show_status, key_show_status: flag};
                $('#status_close > li > a > span').css('color', '#00CCCC');
                $('#status_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_type') {
                var obj = {show_type: show_type, key_show_type: flag};
                $('#type_close > li > a > span').css('color', '#00CCCC');
                $('#type_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_os_ver') {
                var obj = {show_os_ver: show_os_ver, key_show_os_ver: flag};
                $('#os_ver_close > li > a > span').css('color', '#00CCCC');
                $('#os_ver_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_app_ver') {
                var obj = {show_app_ver: show_app_ver, key_show_app_ver: flag};
                $('#app_ver_close > li > a > span').css('color', '#00CCCC');
                $('#app_ver_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_agent') {
                var obj = {show_agent: show_agent, key_show_agent: flag};
                $('#agent_close > li > a > span').css('color', '#00CCCC');
                $('#agent_close > li > a > i').css('color', '#00CCCC');
            }
            search_data(obj);

            if (flag != 'show_app_ver') {
                $("#" + flag + "_list > li").remove();
            }
            $("#" + flag + "_list").append('<li class="show_all_data1" id="Remove_' + flag + '" onclick="remove_swap_text(\'Remove\',\'Remove_' + flag + '\', \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">Remove All</a></li>');


        }
        else {

            if (flag == 'show_agent') {
                var slpt = type.split('@~');
                type = slpt[0];
                $("#" + flag).append('<li><span class="menu_t">' + slpt[1] + '</span><input type="hidden" id="' + type + '_' + flag + '" value="' + type + '"><i class="fa"><a href="javascript:;" onclick="remove_swap_text(\'' + type + '\',\'' + id + '\', \'' + flag + '\')"><i class="apps_i fa close_i"></i></a></i></li>');
            } else {
                $("#" + flag).append('<li><span class="menu_t">' + type + '</span><input type="hidden" id="' + type + '_' + flag + '" value="' + type + '"><i class="fa"><a href="javascript:;" onclick="remove_swap_text(\'' + type + '\',\'' + id + '\', \'' + flag + '\')"><i class="apps_i fa close_i"></i></a></i></li><span id="'+type+'_publish_date_selected" class="show_all_data2_"><small>'+$("#" + type_publish+'_publish_date').text()+'</small></span>');
            }


            var d = id.replace(/\./g, "\\.");
            d = d.replace("@", "\\@");
            d = d.replace("~", "\\~");
            d = d.replace(/\ /g, "\\ ");
            
            $("#" + d).remove();
            $("#" + type_publish+'_publish_date').remove();


            if (flag == 'show_status') {
                show_status[type] = type;
                var obj = {show_status: type, stat: "stat", key_show_status: flag};
                $('#status_close > li > a > span').css('color', '#00CCCC');
                $('#status_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_type') {
                show_type[type] = type;
                var obj = {show_type: type, stat: "stat", key_show_type: flag};
                $('#type_close > li > a > span').css('color', '#00CCCC');
                $('#type_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_os_ver') {
                show_os_ver[type] = type;
                var obj = {show_os_ver: type, stat: "stat", key_show_os_ver: flag};
                $('#os_ver_close > li > a > span').css('color', '#00CCCC');
                $('#os_ver_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_app_ver') {
                show_app_ver[type] = type;
                var obj = {show_app_ver: type, stat: "stat", key_show_app_ver: flag};
                $('#app_ver_close > li > a > span').css('color', '#00CCCC');
                $('#app_ver_close > li > a > i').css('color', '#00CCCC');
            } else if (flag == 'show_agent') {
                show_agent[type] = type;
                var obj = {show_agent: type, stat: "stat", key_show_agent: flag};
                $('#agent_close > li > a > span').css('color', '#00CCCC');
                $('#agent_close > li > a > i').css('color', '#00CCCC');
            }

            if ($("#" + flag + '_list > li').size() == 1) {
                $("#" + flag + "_list > li").remove();
                $("#" + flag + "_list").append('<li class="show_all_data1" id="Remove_' + flag + '" onclick="remove_swap_text(\'Remove\',\'Remove_' + flag + '\', \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">Remove All</a></li>');




            }
            search_data(obj);

        }



    }

    function search_data(columns,msg) {
       
        $("#overlays").show();
        $.ajax({
            type: "POST",
            url: "search_support_messages.php",
            async: false,
            //dataType: "json",
            data: {action: "search_app", columns: columns,'msg':msg},
            success: function (data) {
                $(".communicatr_list").html(data);
                $("#overlays").hide();

            }
        });
    }

    function remove_swap_text(type, id, flag) {
var type_publish = type.replace(/\./g, "\\.");
        if (type == 'Remove') {
            //$("#"+flag+"_list").empty();
            $("#" + flag + "_list").prepend('<li class="show_all_data1" id="All_' + flag + '" onclick="swap_text(\'All\', this.id, \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">All</a></li>');
            $("#" + id).remove();

            $("#" + flag + " > li input").each(function () {


                var splitid = $(this).attr("id");
                var split_str = splitid.split("_");
                if ($(this).attr("id") != 'All_' + flag) {
                    if (flag == 'show_agent') {
                        var slpt = split_str[0].split('@~');
                        split_str[0] = slpt[0];
                        $("#" + flag + "_list").append('<li class="show_all_data1" id="' + split_str[0] + '@~' + $("#" + splitid).prev().text() + '" onclick="swap_text(\'' + split_str[0] + '@~' + $("#" + splitid).prev().text() + '\', this.id, \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">' + $("#" + splitid).prev().text() + '</a></li>');
                    } else {
                        var type_publish = split_str[0].replace(/\./g, "\\.");
                        
                        $("#" + flag + "_list").append('<li class="show_all_data1" id="' + split_str[0] + '_' + flag + '" onclick="swap_text(\'' + split_str[0] + '\', this.id, \'' + flag + '\')"><a class="forcered" href="javascript:;" style="margin-left: 10px;">' + split_str[0] + '</a></li><span id="'+split_str[0]+'_publish_date" class="show_all_data2_"><small>'+$("#" + type_publish+'_publish_date_selected').text()+'</small></span>');
                    }
                    $("#" + type_publish+'_publish_date_selected').remove();

                }
            });
            $("#" + flag).empty();

            if (flag == 'show_status') {
                var obj = {show_status: "removeall", key_show_status: flag};
                $('#status_close > li > a > span').css('color', '#828282');
                $('#status_close > li > a > i').css('color', '#828282');
                show_status = {};
            } else if (flag == 'show_type') {
                var obj = {show_type: "removeall", key_show_type: flag};
                $('#type_close > li > a > span').css('color', '#828282');
                $('#type_close > li > a > i').css('color', '#828282');
            } else if (flag == 'show_os_ver') {
                var obj = {show_os_ver: "removeall", key_show_os_ver: flag};
                $('#os_ver_close > li > a > span').css('color', '#828282');
                $('#os_ver_close > li > a > i').css('color', '#828282');
            } else if (flag == 'show_app_ver') {
                var obj = {show_app_ver: "removeall", key_show_app_ver: flag};
                $('#app_ver_close > li > a > span').css('color', '#828282');
                $('#app_ver_close > li > a > i').css('color', '#828282');
            } else if (flag == 'show_agent') {
                var obj = {show_agent: "removeall", key_show_agent: flag};
                $('#agent_close > li > a > span').css('color', '#828282');
                $('#agent_close > li > a > i').css('color', '#828282');
            }

            unset_data(obj);

        } else {


            if (flag == 'show_agent') {
                var d = type.replace(/\./g, "\\.");

                d = d.replace("@", "\\@");
                d = d.replace("~", "\\~");
                d = d.replace(/\ /g, "\\ ");
                var splitid = $("#" + d + '_' + flag).attr("id");
                //alert(splitid);
                var split_str = splitid.split("_");
                $("#" + flag + "_list").append('<li class="show_all_data1" id="' + split_str[0] + '@~' + $("#" + splitid).prev().text() + '" onclick="swap_text(\'' + split_str[0] + '@~' + $("#" + splitid).prev().text() + '\', this.id, \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">' + $("#" + splitid).prev().text() + '</a></li>');
                $("#" + d + '_' + flag).parent().remove();
            } else {

                var dv = type.replace(/\./g, "\\.");
                $("#" + dv + '_' + flag).parent().remove();
                $("#" + flag + "_list").append('<li class="show_all_data1" id="' + id + '" onclick="swap_text(\'' + type + '\', this.id, \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">' + type + '</a></li><span id="'+type+'_publish_date" class="show_all_data2_"><small>'+$("#" + type_publish+'_publish_date_selected').text()+'</small></span>');

            }

            $("#" + type_publish+'_publish_date_selected').remove();

            if (flag == 'show_status') {
                var obj = {show_status: type, key_show_status: flag};
            } else if (flag == 'show_type') {
                var obj = {show_type: type, key_show_type: flag};
            } else if (flag == 'show_os_ver') {
                var obj = {show_os_ver: type, key_show_os_ver: flag};
            } else if (flag == 'show_app_ver') {
                var obj = {show_app_ver: type, key_show_app_ver: flag};
            } else if (flag == 'show_agent') {
                var obj = {show_agent: type, key_show_agent: flag};
            }

            $("#Remove_" + flag).html('<a href="javascript:;" style="margin-left: 10px;">Remove Others</a>');
            if ($("#" + flag + ' > li').size() == 0) {
                $("#Remove_" + flag).remove();
                if (flag == 'show_status') {
                    $('#status_close > li > a > span').css('color', '#828282');
                    $('#status_close > li > a > i').css('color', '#828282');
                    show_status = {};
                } else if (flag == 'show_type') {
                    $('#type_close > li > a > span').css('color', '#828282');
                    $('#type_close > li > a > i').css('color', '#828282');
                } else if (flag == 'show_os_ver') {
                    $('#os_ver_close > li > a > span').css('color', '#828282');
                    $('#os_ver_close > li > a > i').css('color', '#828282');
                } else if (flag == 'show_app_ver') {
                    $('#app_ver_close > li > a > span').css('color', '#828282');
                    $('#app_ver_close > li > a > i').css('color', '#828282');
                } else if (flag == 'show_agent') {
                    $('#agent_close > li > a > span').css('color', '#828282');
                    $('#agent_close > li > a > i').css('color', '#828282');
                }
                $("#All_" + flag).remove();
                $("#" + flag + "_list").prepend('<li class="show_all_data1" id="All_' + flag + '" onclick="swap_text(\'All\', this.id, \'' + flag + '\')"><a href="javascript:;" style="margin-left: 10px;">All</a></li>');

            }

            unset_data(obj);

        }



    }

    function unset_data(columns) {

        console.log(columns);

        $("#overlays").show();
        $.ajax({
            type: "POST",
            url: "search_support_messages.php",
            async: false,
            data: {action: "unset_data", columns: columns},
            success: function (data) {
                search_data();

            }
        });
    }


    $("#hide_communicatr").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').addClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "block");


    });



    $("#status_close").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#status_close > li').addClass('active');
        $('#status_close > li > ul.treeview-menu').css("display", "block");


    });

    $("#type_close").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#type_close > li').addClass('active');
        $('#type_close > li > ul.treeview-menu').css("display", "block");


    });

    $("#os_ver_close").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').addClass('active');
        $('#os_ver_close > li > ul.treeview-menu').css("display", "block");

    });

    $("#app_ver_close").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').addClass('active');
        $('#app_ver_close > li > ul.treeview-menu').css("display", "block");


    });

    $("#append_apps_detail").click(function () {
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').removeClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#append_apps_detail > li').addClass('active');
        $('#append_apps_detail > li > ul.treeview-menu').css("display", "block");

    });

    $("#agent_close").click(function () {
        $('#append_apps_detail > li').removeClass('active');
        $('#append_apps_detail li > ul.treeview-menu').css("display", "none");
        $('#type_close > li').removeClass('active');
        $('#type_close li > ul.treeview-menu').css("display", "none");
        $('#os_ver_close > li').removeClass('active');
        $('#os_ver_close li > ul.treeview-menu').css("display", "none");
        $('#status_close > li').removeClass('active');
        $('#status_close li > ul.treeview-menu').css("display", "none");
        $('#app_ver_close > li').removeClass('active');
        $('#app_ver_close li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#agent_close > li').addClass('active');
        $('#agent_close > li > ul.treeview-menu').css("display", "block");


    });
    if (!$('#Remove_show_os_ver').length) {
        // do something
        $('#show_app_ver_list > li').removeAttr('onclick');
    }


<?php
if (isset($_REQUEST['sel_app_id'])) {
    ?>
        clear_all_data();
        hide_app(<?php echo $_REQUEST['sel_app_id'] ?>);
        window.location = 'respond';
    <?php
}

if (count($get_distinct_app_version_selected) > 1) {
    ?>
        $('#All_show_app_ver').find('a').removeAttr("style");
        $('#All_show_app_ver').find('a').addClass("forcered");
        $('#All_show_app_ver').attr("onclick", "swap_text('All', this.id, 'show_app_ver')");
    <?php
}

for ($k = 0; $k < count($get_distinct_app_version_selected); $k++) {
    ?>


        $("#show_app_ver_list > li").each(function (index) {
            if ($(this).text() == "<?php echo $get_distinct_app_version_selected[$k]['version'] ?>") {
                if (!$(this).find('a').hasClass("forcered")) {
                    $(this).find('a').removeAttr("style");
                    $(this).find('a').addClass("forcered");
                    $(this).attr("onclick", "swap_text('" + $(this).text() + "',this.id, 'show_app_ver')");
                }

            }

        });
    <?php
}
?>


    var k;

    function hide_app(app_id) {



        var count = $('#app_count_data').val();
        $('#app_count_data').val(count - 1);
        $('#append_apps_detail > li > a > span').css('color', '#00CCCC');
        $('#append_apps_detail > li > a > i').css('color', '#00CCCC');
        $.ajax({
            type: "POST",
            url: "search_support_messages.php",
            async: false,
            data: {app_id: app_id, action: "hide_app"},
            beforeSend: function () {
                $("#overlays").show();

            },
            success: function (data) {
                search_data('');
                var da = JSON.parse(data);
                var obj = da.app_versions;

                if (da.app_versions.length > 1) {
                    $('#All_show_app_ver').find('a').removeAttr("style");
                    $('#All_show_app_ver').find('a').addClass("forcered");
                    $('#All_show_app_ver').attr("onclick", "swap_text('All', this.id, 'show_app_ver')");
                }
                for (k = 0; k < da.app_versions.length; k++)
                {
                    $("#show_app_ver_list > li").each(function (index) {
                        if ($(this).text() == obj[k]['version']) {
                            if (!$(this).find('a').hasClass("forcered")) {
                                $(this).find('a').removeAttr("style");
                                $(this).find('a').removeClass("unselected");
                                $(this).find('a').addClass("forcered");
                                $(this).attr("onclick", "swap_text('" + $(this).text() + "',this.id, 'show_app_ver')");
                            }

                        }

                    });
                }
                if (da.app_type == 'ios') {
                    var class_apply = 'iphone_i';
                } else {
                    var class_apply = 'android_i';
                }


                $('#append_apps_detail').append('<li class="app_sat" id="left_' + da.app_id + '"><i class="apps_i ' + class_apply + '" title=""></i> <span class="menu_t">' + da.app_name + '</span> <a href="javascript:;"  onclick="remove_app(\'' + da.app_id + '\')"><i class="apps_i fa close_i"></i></a></li>');

                $("#overlays").hide();
            }
        });
        if (count == 1) {
            $(".rowdata").append('<div class="col-lg-12"><div class="col-md-12 img-center" style="color:red;font-size:18px"><img src="img/no_apps_found.png" alt="" title="" /></div></div></div>');
        }

        $("#overlays").show();

        var cart = $('#left_' + app_id);

        var imgtodrag = $('#' + app_id + ' > a > div').find("img").eq(0);
        if (imgtodrag) {
            var imgclone = imgtodrag.clone()
                    .offset({
                        top: imgtodrag.offset().top,
                        left: imgtodrag.offset().left
                    })
                    .css({
                        'opacity': '0.5',
                        'position': 'absolute',
                        'height': '150px',
                        'border-radius': '10px',
                        'width': '150px',
                        'z-index': '100'
                    })
                    .appendTo($('body'))
                    .animate({
                        'top': cart.offset().top + 10,
                        'left': cart.offset().left + 10,
                        'width': 75,
                        'height': 75
                    }, 400, 'easeInOutExpo');

            setTimeout(function () {
                cart.effect("transfer", {
                    times: 2
                }, 200);
            }, 400);

            imgclone.animate({
                'width': 0,
                'height': 0
            }, function () {
                $(this).detach();
                $("#overlays").hide();
            });
        }



        $('#' + app_id).hide();



    }


    function remove_app(app_id, refresh, app_type) {

        var count = $('#app_count_data').val();
        $('#app_count_data').val(parseFloat(count) + 1);
        $('#left_' + app_id).remove();
        $('#' + app_id).show();

        $("#overlays").show();
        $("#overlays_left").show();
        $.ajax({
            type: "POST",
            url: "unset_apps.php",
            async: false,
            //dataType: "json",
            data: {app_id: app_id, refresh: refresh},
            success: function (data) {

                var json_data = JSON.parse(data);
                data = json_data['html'];

                var liarray = [];
                var liarrayselected = [];



                $("#show_app_ver > li").each(function (index) {
                    liarrayselected.push($(this).text());
                });

                for (var m = 0; m < liarrayselected.length; m++)
                {

                    if (jQuery.inArray(liarrayselected[m], json_data['selected_app_versions']) < 0)
                    {
                        var d = liarrayselected[m].replace(/\./g, "\\.");
                        remove_swap_text(liarrayselected[m], liarrayselected[m] + '_show_app_ver', 'show_app_ver');


                    }
                }


                $("#show_app_ver_list > li").each(function (index) {
                    liarray.push($(this).text());
                });

                for (var j = 0; j < liarray.length; j++)
                {

                    if (jQuery.inArray(liarray[j], json_data['selected_app_versions']) < 0)
                    {

                        var d = liarray[j].replace(/\./g, "\\.");
                     
                                $("#" + d + "_show_app_ver").removeAttr('onclick');
                                $("#" + d + "_show_app_ver").find('a').removeClass("forcered");
                                $("#" + d + "_show_app_ver").find('a').addClass("unselected");
                       

                    }


                }









                if (refresh == 'refresh') {
                    $(".rowdata > div.bk > div.col-lg-12 > div.img-center").empty();
                    $(".rowdata > div.col-lg-12 > div.img-center").empty();

                    $(".rowdata").append(data);
                }
                $("#overlays").hide();
            }
        });
        if ($('.ip').hasClass('active')) {
            var app_type = 'ios';
        } else if ($('.ad').hasClass('active')) {
            var app_type = 'android';
        } else if ($('.wn').hasClass('active')) {
            var app_type = 'windows';
        } else if ($('.all_apps').hasClass('active')) {
            var app_type = 'all';
        }
        var site_url = '<?php echo $url; ?>';
        $('#app_type').val(app_type);
        var q = encodeURIComponent($("#q").val());

        select_os(app_type, site_url, q);
        if ($('#app_count_data').val() == $('#total_app_count_data').val()) {
            $('#append_apps_detail > li > a > span').css('color', '#828282');
            $('#append_apps_detail > li > a > i').css('color', '#828282');
        }


        if (count == 0) {
            $(".rowdata > div.col-lg-12 > div.img-center").empty();
        }

        search_data('');
    }



    function clear_all_data() {
        $("#overlays").show();
        $.ajax({
            type: "POST",
            url: "search_support_messages.php",
            async: false,
            data: {action: "unset_session"},
            success: function (data) {
<?php
foreach ($_SESSION['app_id'] as $get_app_id):
    $res_app_id = $dclass->select("tblmember_apps.*", "tblmember_apps", " AND tblmember_apps.intid = '" . $get_app_id . "' ");
    ?>

                    remove_app('<?php echo $res_app_id[0]['intid'] ?>', 'refresh');
<?php endforeach; ?>
                $('#app_ver_close > li').removeClass('active');
                $('#app_ver_close li > ul.treeview-menu').css("display", "none");
                $('#type_close > li').removeClass('active');
                $('#type_close li > ul.treeview-menu').css("display", "none");
                $('#os_ver_close > li').removeClass('active');
                $('#os_ver_close li > ul.treeview-menu').css("display", "none");
                $('#status_close > li').removeClass('active');
                $('#status_close li > ul.treeview-menu').css("display", "none");
                $('#append_apps_detail > li').removeClass('active');
                $('#append_apps_detail > li > ul.treeview-menu').css("display", "none");
                $('#append_apps_detail > li > a > span').css('color', '#828282');
                $('#append_apps_detail > li > a > i').css('color', '#828282');
                $('#agent_close > li').removeClass('active');
                $('#agent_close li > ul.treeview-menu').css("display", "none");

                $("#All_show_status").remove();
                $("#All_show_type").remove();
                $("#All_show_os_ver").remove();
                $("#All_show_app_ver").remove();
                $("#All_show_agent").remove();

                //swap_text('Open', 'Open', 'show_status');
                remove_swap_text('Remove', 'Remove_show_status', 'show_status');
                remove_swap_text('Remove', 'Remove_show_type', 'show_type');
                remove_swap_text('Remove', 'Remove_show_os_ver', 'show_os_ver');
                remove_swap_text('Remove', 'Remove_show_app_ver', 'show_app_ver');
                remove_swap_text('Remove', 'Remove_show_agent', 'show_agent');

                $(".show_all_data1:hidden").show();

                $('#Open').hide();
                $('#append_apps_detail > li.app_sat').remove();
                $(".rowdata > div.col-lg-12 > div.img-center").empty();


                search_data('','');
                $("#overlays").hide();

            }
        });



    }

    search_data('','<?php echo $_REQUEST['msg'] ?>');
    
    $('html').css('overflow', 'inherit');

    $("#overlays").hide();

    function view_detail(id, image_path)
    {
        $.ajax({
            method: "POST",
            url: "view_image",
            data: {image_path: image_path},
            success: function (result) {
                $("#view-image" + id).html(result);

            }});
    }

</script>