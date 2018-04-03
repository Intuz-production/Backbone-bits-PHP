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
if (empty($_REQUEST['sel_app_id'])) {
    $gnrl->redirectTo("respond");
} else {
    $sel_app_id = $_REQUEST['sel_app_id'];
}
$member_id = $_SESSION['custid'];
$support_id = $_REQUEST['support_id'];

$request_user_text = $dclass->select('*, tblapp_support.intid', 'tblapp_support, tblmember_apps', ' AND  (tblapp_support.intid = ' . $_REQUEST['support_id'] . ' AND tblapp_support.request_id = 0) AND tblapp_support.app_id=tblmember_apps.intid order by tblapp_support.intid ASC');


$ask_for_review_check = $dclass->select('*', 'tbl_ask_for_review', " AND app_id = '" . $sel_app_id . "' AND support_id = '" . $support_id . "' ");


if ($request_user_text[0]['request_type'] == 'bug') {
    $class_image = 'request_i1';
    $text_name = 'Bug';
} else if ($request_user_text[0]['request_type'] == 'feedback') {
    $class_image = 'request_i2';
    $text_name = 'Feedback';
} else if ($request_user_text[0]['request_type'] == 'query') {
    $class_image = 'request_i3';
    $text_name = 'Query';
}
include(INC . "header.php");
include INC . "left_sidebar.php";
?>

<aside class="right-side">
    <div class="right_sidebar">
        <div class="add-apps">
            <div class="col-xs-12 col-md-12"> 
            </div>
            <div class="cl"></div>
            <div class="row">
                <input type="hidden" value="<?php echo $_REQUEST['sel_app_id'] ?>" id="sel_app_id_ajax" name="sel_app_id_ajax" />
                <div class="col-xs-12 col-md-12">
                    <div class="communicatr">
                        <div class="col-xs-12 col-md-12">
                            <div class="communicatr-detail">
                                <?php
                                if (!empty($request_user_text)) {
                                    ?>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-9 time__line_box">
                                            <div class="col-xs-12 col-md-12">
                                                <div class="col-xs-12 col-md-3">
                                                    <?php if (!empty($text_name)) { ?>
                                                        <i class="apps_i fl  <?php echo $class_image ?>"></i>&nbsp;&nbsp;
                                                    <?php } ?>
                                                    <div class="fl titel__name"> <?php echo $text_name ?></div>
                                                </div>
                                                <?php
                                                if ($request_user_text[0]['app_type'] == 'ios'):
                                                    $request_user_text[0]['app_type'] = lcfirst(strtoupper($request_user_text[0]['app_type']));
                                                endif;
                                                ?>
                                                <div class="col-xs-12 col-md-3 padding-t10"><i title="" class="apps_i ios_i fl"> </i>&nbsp; <?php echo $request_user_text[0]['app_type'] . ' ' . $request_user_text[0]['version'] . ' ' . $request_user_text[0]['device'] ?></div>
                                                <div class="col-xs-12 col-md-2 padding-t10">
                                                    <?php if (!empty($request_user_text[0]['app_version'])) { ?>
                                                        <i title="" class="apps_i ver_i fl"> </i>
                                                    <?php } ?>
                                                    <?php echo $request_user_text[0]['app_version'] ?></div>
                                                <div class="col-xs-12 col-md-2 padding-t10">
                                                    <?php if (!empty($request_user_text[0]['region'])) { ?>
                                                        <i class="apps_i location"></i>&nbsp;
                                                    <?php } ?>
    <?php echo $request_user_text[0]['region'] ?></div>
                                                <div class="col-xs-12 col-md-2 fr"> <a href="javascript:;" id="reply" class="reply_  <?php if ($request_user_text[0]['status'] == 'close') { ?>reopen_class<?php } ?>"><i title="" class="apps_i reply_i fl"> </i>&nbsp; <?php if ($request_user_text[0]['status'] == 'close') { ?>Re-open <?php } else { ?>Reply<?php } ?></a> </div>
                                            </div>
                                            <div class="cl"></div>
                                            <div class="row review_box none" style="display:none">
                                                <div class="review_box_1">
                                                    <div class="review_box_2">
                                                        <div class="col-xs-12 col-md-12">
                                                            <form id="addcannedtext"  method="POST" action=""  enctype="multipart/form-data">
                                                                <input type="hidden" name="member_id" id="member_id" value="<?php echo $_SESSION['agents_cust_id'] ?>">
                                                                <?php
                                                                if ($member_id == $_SESSION['agents_cust_id']) {
                                                                    $parent_id_data = 0;
                                                                } else {
                                                                    $parent_id_data = $member_id;
                                                                }
                                                                
                                                                
                                                                ?>
                                                                <input type="hidden" name="parent_id" id="parent_id" value="<?php echo $parent_id_data ?>">
                                                                <input type="hidden" name="app_id"id="app_id" value="<?php echo $request_user_text[0]['app_id'] ?>">
                                                                <input type="hidden" name="version"id="version" value="<?php echo $request_user_text[0]['version'] ?>">
                                                                <input type="hidden" name="app_version"id="app_version" value="<?php echo $request_user_text[0]['app_version'] ?>">
                                                                <input type="hidden" name="email"id="email" value="<?php echo $request_user_text[0]['email'] ?>">
                                                                <input type="hidden" name="request_id" id="request_id" value="<?php echo $_REQUEST['support_id'] ?>">
                                                                <input type="hidden" name="type" id="type" value="<?php echo $_SESSION['role'] ?>">
                                                                <input type="hidden" name="name" id="name" value="<?php echo $_SESSION['custname'] ?>">
                                                                <input type="hidden" name="dtadd" id="dtadd" value="<?php echo date('Y-m-d H:i:s') ?>">
                                                                
                                                                <?php 
                                                                    $mainstatus = "replied";
                                                                   if($request_user_text[0]['status'] == "close")
                                                                   {
                                                                        $mainstatus = "reopen";
                                                                   }
                                                                ?>
                                                                <input type="hidden" name="status"id="status" value="<?php echo $mainstatus;?>">
                                                                <div class="col-xs-12 col-md-8">
                                                                    <div class="form-group">
                                                                        <div class="dropitems" id="scrollto">
                                                                            <textarea id="message" ondrop="check_empty_message()" class="form-control" name="message" rows="3" placeholder="Enter ..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-12 col-md-4">
                                                                    <button onclick="add(this.form.id, 'tblapp_support')" class="btn btn-primary send" id="send_data" type="button">Send</button>
                                                                    <button onclick="save_as_canned(this.form.id)" class="btn btn-primary send send-canned send_canned_and_save trigger" type="button" data-dialog="test">Send & Add to Canned Response</button>
                                                                    <button onclick="change_status();
                                                                                    add(this.form.id, 'tblapp_support')" class="btn btn-primary send send-canned" id="send_close_data" type="button">Send & Close</button>
                                                                </div>


                                                                <div class="cl"></div>
                                                                <div class="form-group" id="select-app-img">
                                                                    <div class="add_new_img" id="new_img_div_0" >
                                                                        <div id="sfile_logo_0" class="file_logo"> <span class="btn add-files fileinput-button"> <img alt="" src="img/attachment_icon.png" class="add-logo_plus"> <span>Attach</span>
                                                                                <input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="app_logo[]" id="app_logo_0">
                                                                            </span>
                                                                            <div></div>
                                                                        </div>
                                                                        <div class="cl height1"></div>  
                                                                    </div>

   
                                                                </div>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.col --> 

                                            </div>
                                            <div class="timeline_scroll">




                                                <div class="row" id="support_data"> 

                                                    <!-- /.col --> 

                                                </div>
                                            </div>
                                            <div class="row"  style="border: 1px;color: black">
                                                <div class="col-xs-2"></div>
                                                <div class="col-xs-8" id="review_id" style="border: 1px #00a65a solid;background-color: lightgreen;opacity: 0.6;display: none"></div>
                                                <div class="col-xs-2"></div>
                                            </div>

                                            <div class="cl"></div>
                                        </div>
                                        <div class="col-xs-12 col-md-3">
                                            <div class="row c-responses">
                                                <div class="col-xs-12 col-md-12 c-responses-top">
                                                    <h3>Canned Responses</h3>
                                                    <span>Drag & drop on reply box to insert as response</span>
                                                    <div id="sb-search" class="sb-search">
                                                        <form>
                                                            <input class="sb-search-input" onkeyup="search(this.value, 'tblapp_faq', ['question', 'answer'], 0, 10, '', '')" placeholder="Enter your search term..." type="text" value="" name="keyword" id="keyword" style="display:none">
                                                            <input class="sb-search-submit" type="submit" value="">
                                                            <span class="sb-icon-search"> <a href="#"><i class="apps_i fa-search_i"></i></a> </span>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-12">

                                                </div>
                                                <div class="cl" style="height:10px;"></div>
                                                <div class="timeline_scroll_right">
                                                    <div class="col-xs-12 col-md-12">
                                                        <div class="box-body table-responsive" id="promote-table_wrapper">
                                                            <table id="example1" class="table table-bordered table-striped classtablenew agents-tables">
                                                                <thead>
                                                                </thead>
                                                                <tbody id="showdata">
                                                                </tbody>
                                                            </table>
                                                            <div class='navigation' id="navview"> </div>
                                                        </div>
                                                        <div id="tabledetailshow" style="display: none">
                                                            <div><a href="javascript:;" onclick="showtable()">Back</a></div>
                                                            <ul>
                                                                <li id="showquestion"></li>
                                                                <li id="showanswer"></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="alert no_data">No Data Found</div>
<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ask-review"><a href="javascript:;" onclick="show_message('<?php echo $_REQUEST['support_id'] ?>', '<?php echo $_REQUEST['sel_app_id'] ?>')">Ask for Review</a></div>
</aside>
<div id="test" class="dialog" style="width: 60%;padding-left: 5%;padding-right: 5%">
    <div class="dialog__overlay" style="width:105%"></div>
    <div class="dialog__content">
        <div class="popap-header">
            <h3 class="fl">Add as Faq or Canned Response</h3>
            <button class="action fr" data-dialog-close>&nbsp;</button>
        </div>
        <div class="popap-content" id="add-app-page"></div>
    </div>
</div>
<!-- ============================================================================== --> 
<!-- Footer --> 
<!-- ============================================================================== -->

<?php include 'inc/footer.php'; ?>
<script type="text/javascript" src="js/modernizr.min.js"></script> 
<script type="text/javascript" src="js/classie.js"></script> 
<script type="text/javascript" src="js/uisearch.js"></script> 
<script type="text/javascript" src="js/process.js"></script>
<link rel="stylesheet" type="text/css" href="css/component_search.css" />
<script>
        $("#message").change(function() {
            //getting search value
            var searchtext = $(this).val();
            if (searchtext) {
                //finding If content matches with searck keyword
                $('#send_data').attr('disabled', false);
                $('.send_canned_and_save').attr('disabled', false);
                $('#send_close_data').attr('disabled', false);
            } else {
                //if search keyword is empty then display all the lists
                $('#send_data').attr('disabled', true);
                $('.send_canned_and_save').attr('disabled', true);
                $('#send_close_data').attr('disabled', true);
            }
            return false;
        }).keyup(function() {
            $(this).change();
        });

        $('#send_data').attr('disabled', true);
        $('.send_canned_and_save').attr('disabled', true);
        $('#send_close_data').attr('disabled', true);

</script>
<script>

<?php if (!empty($ask_for_review_check)) { ?>
        $('.ask-review').addClass('disabled1');
<?php } ?>


    $("#hide_communicatr").click(function() {
        $('#hide_app_communicatr > li').removeClass('active');
        $('#hide_app_communicatr > li > ul.treeview-menu').css("display", "none");
        $('#hide_communicatr').addClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "block");


    });

    $("#hide_app_communicatr").click(function() {
        $('#hide_communicatr > li').removeClass('active');
        $('#hide_communicatr > ul.treeview-menu').css("display", "none");
        $('#hide_app_communicatr > li').addClass('active');
        $('#hide_app_communicatr > li > ul.treeview-menu').css("display", "block");


    });


    $(".timeline_scroll").slimScroll({
        height: '580px'
    });
    $(".timeline_scroll_right").slimScroll({
        height: '560px'
    });

    $(".scroll_remove_sticky").slimScroll({
        height: '600px'
    });
    $('#reply').click(function() {
        if ($('.review_box').css('display') == 'none') {
            $('#support_data').addClass('time__line__box');
            $('.reply_box').addClass('none');
            $("#reply").addClass("greencolorclass");
        } else {

            $('#support_data').removeClass('time__line__box');
            $('.reply_box').removeClass('none');
            $("#reply").removeClass("greencolorclass");
        }
        $('.review_box').slideToggle();
        $('#message').focus();

    });

    $('a.repaly_button').click(function() {
        if ($('.review_box').css('display') == 'none') {
            $('#support_data').addClass('time__line__box');
            $('.reply_box').addClass('none');

        } else {
            $('#support_data').removeClass('time__line__box');
            $('.reply_box').removeClass('none');
        }

        $('.review_box').slideToggle();
        $('#message').focus();
    });

    $('.closed_button').click(function() {

        if ($('.review_box').css('display') == 'none') {
            $('#support_data').addClass('time__line__box');
            $('.reply_box').addClass('none');
        } else {
            $('#support_data').removeClass('time__line__box');
            $('.reply_box').removeClass('none');
        }
        $('.review_box').slideToggle();
    });


    new UISearch(document.getElementById('sb-search'));
    (function() {
        var dlgtrigger1 = document.querySelector('[data-dialog]');
        somedialog1 = document.getElementById(dlgtrigger1.getAttribute('data-dialog'));
        dlg11 = new DialogFx(somedialog1);
        dlgtrigger1.addEventListener('click', dlg11.toggle.bind(dlg11));
    })();

</script>
<style type="text/css">
</style>
<script language="javascript" type="text/javascript">
    function show_message(support_id, sel_app_id) {
        $('#noty_topCenter_layout_container').remove();
        $.ajax({
            method: "POST",
            url: "process-add",
            beforeSend: function() {
                $("#overlays").show();
            },
            data: {table_name: "tbl_ask_for_review", app_id: '<?php echo $_REQUEST['sel_app_id'] ?>', support_id: '<?php echo $_REQUEST['support_id'] ?>', "parent_id": '<?php echo $_SESSION['custid'] ?>', "member_id": '<?php echo $_SESSION['agents_cust_id'] ?>', version: '<?php echo $request_user_text[0]['version'] ?>', app_version: '<?php echo $request_user_text[0]['app_version'] ?>', email: '<?php echo $request_user_text[0]['email'] ?>'},
            success: function(result) {
                var data = JSON.parse(result);
                //alert(data['output']);
                get_communicatr_details('<?php echo $_REQUEST['support_id'] ?>');
                if (data['output'] == 'S') {
                    $('.ask-review').addClass('disabled1');
                    message(data['msg'], 'success');
                } else if (data['output'] == 'F') {
                    message(data['msg'], 'error');
                }
                $("#overlays").hide();
            }});
    }

    function save_as_canned(form_id)
    {
        add(form_id, 'tblapp_support');
        var message = $("#message").val();
        $.ajax({
            method: "POST",
            url: "add_canned_response",
            beforeSend: function() {
                $("#add-app-page").html('');
            },
            data: {cache: false, message: message, sel_app_id: '<?php echo $_REQUEST['sel_app_id'] ?>', support_id: '<?php echo $_REQUEST['support_id'] ?>'},
            success: function(result) {
                $("#add-app-page").html(result);
            }});
        $("#overlays").hide();
    }
    document.addEventListener("drop", function(event) {
        alert(event.target.id);

        if (event.target.id == "message") {
        }
    });
    function check_empty_message() {
        document.addEventListener("drop", function(event) {
            alert(event.target.id);

            if (event.target.id == "message") {
            }
        });
    }

    function change_status() {

        $('#status').val('close');

    }
    function canned_status() {
        $('#save_canned_response').val('yes');
    }
    $('#addcannedtext').validate({
        onkeyup: function(element) {
            $(element).valid()
        },
        rules: {
            message: "required",
        },
        messages: {
            message: "",
        }
    });

    var ajaxRunning = false;

    $("body").ajaxStart(function()
    {
        ajaxRunning = true;
    }).ajaxStop(function()
    {
        ajaxRunning = false;
    });

    document.onscroll = function() {
        $.ias({
            container: '.classtablenew',
            scrollContainer: $('.classtablenew'),
            item: '.item',
            pagination: '#promote-table_wrapper .content',
            next: '.next-posts a',
            loader: '<div class="view_more_loader"><img src="img/ajax-loader.gif"/></div>',
            triggerPageThreshold: 0,
            trigger: 'View More',
            history: false,
            onRenderComplete: function() {
                remove_loader();

            },
            beforePageChange: function(curScrOffset, urlnext) {

                spliturl_paging(urlnext, ['message'], 'bef');
                remove_loader();

            },
            onPageChange: function(pageNum, pageUrl, scrollOffset) {
                spliturl_paging(pageUrl, ['message'], 'onp');
                remove_loader();
            }
        });
    }

    $(function() {
        $(".drag_desc").draggable({
            appendTo: "body",
            helper: "clone",
            cursor: "move",
            revert: "invalid"
        });
        initDroppable($("#message"));
        function initDroppable($elements) {
            $elements.droppable({
                hoverClass: "textarea",
                accept: ":not(.ui-sortable-helper)",
                drop: function(event, ui) {

                    var $this = $(this);
                    $('#send_data').attr('disabled', false);
                    $('.send_canned_and_save').attr('disabled', false);
                    $('#send_close_data').attr('disabled', false);
                    var tempid = ui.draggable.text();
                    var dropText;
                    dropText = tempid;
                    var droparea = document.getElementById('message');
                    var range1 = droparea.selectionStart;
                    var range2 = droparea.selectionEnd;
                    var val = droparea.value;
                    var str1 = val.substring(0, range1);
                    var str3 = val.substring(range1, val.length);
                    droparea.value = str1 + dropText + str3;

                }
            });
        }
    });

    $.fn.serializefiles = function() {
        var obj = $(this);
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        $.each($(obj).find("input[type='file']"), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        var params = $(obj).serializeArray();
        $.each(params, function(i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };

    function showdetail(id) {
        $('#showdata').hide();
        $('#showquestion').empty();
        $('#showanswer').empty();
        $('#tabledetailshow').toggle('slide', {direction: 'right'}, 600);
        $("#overlays").show();
        $.ajax({
            type: "POST",
            url: "get_ajax_faq_response.php",
            async: false,
            data: {intid: id},
            success: function(data) {
                data = JSON.parse(data);
                $('#showquestion').append('<b>' + data.question + '</b>');
                $('#showanswer').append(data.answer);
                $("#overlays").hide();
            }
        });
    }
    function showtable() {
        $('#tabledetailshow').hide();
        $('#showdata').show('slow');



    }
</script> 
<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script> 
<script>

    function remove_manual_logo(id) {
        $('#preview_img_' + id).remove();
        $('#new_img_div_' + id).remove();
    }

    $(document).on('change', 'input[type="file"]', function() {

        var id = this.id;
        var new_id = id.split("_")[2];
        var fileInput = document.getElementById(id);

        var file = fileInput.files[0];
        var imageType = /image.*/;
        var videoType = /video.*/;

        if (file.type.match(imageType)) {

            var reader = new FileReader();
            reader.onload = function(e) {

                $('#sfile_logo_' + new_id).children('span').after('<div id="preview_img_' + new_id + '" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" style="height:auto;width:100px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(' + new_id + ')"><i class="apps_i remove_icon_" title="Delete"></i></a></div></div>');
                $('#app_type_error_' + new_id).remove();
                $('#helpr_img_count').val(new_id);

                //Add New helpr image
                var inc_id = parseInt(new_id) + 1;
                var html = '';
                html += '<div class="add_new_img" id="new_img_div_' + inc_id + '" >';
                html += ' <div id="sfile_logo_' + inc_id + '" class="file_logo"> <span class="btn add-files fileinput-button"> <img alt="" src="img/attachment_icon.png" class="add-logo_plus"> <span>Attach</span>';
                html += '<input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="app_logo[]" id="app_logo_' + inc_id + '"> </span><div></div></div>';
                html += '<div class="cl height1"></div>';

                html += '</div>';

                $("#select-app-img").append(html);

            }
            reader.readAsDataURL(file);
        } else if (file.type.match(videoType)) {

            $('#sfile_logo_' + new_id).children('span').after('<div id="preview_img_' + new_id + '" class="preview_small_img"><div class="center-img"><img src="img/communicatr-detail-icon/mp4.png" style="height:auto;width:100px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(' + new_id + ')"><i class="apps_i remove_icon_" title="Delete"></i></a></div></div>');
            $('#app_type_error_' + new_id).remove();
            $('#helpr_img_count').val(new_id);

            //Add New helpr image
            var inc_id = parseInt(new_id) + 1;
            var html = '';
            html += '<div class="add_new_img" id="new_img_div_' + inc_id + '" >';
            html += ' <div id="sfile_logo_' + inc_id + '" class="file_logo"> <span class="btn add-files fileinput-button"> <img alt="" src="img/attachment_icon.png" class="add-logo_plus"> <span>Attach</span>';
            html += '<input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="app_logo[]" id="app_logo_' + inc_id + '"> </span><div></div></div>';
            html += '<div class="cl height1"></div>';

            html += '</div>';

            $("#select-app-img").append(html);

        } else {

            $('#sfile_logo_' + new_id).children('span').after('<div id="preview_img_' + new_id + '" class="preview_small_img"><div class="center-img"><img src="img/communicatr-detail-icon/other.png" style="height:auto;width:100px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(' + new_id + ')"><i class="apps_i remove_icon_" title="Delete"></i></a></div></div>');
            $('#app_type_error_' + new_id).remove();
            $('#helpr_img_count').val(new_id);

            //Add New helpr image
            var inc_id = parseInt(new_id) + 1;
            var html = '';
            html += '<div class="add_new_img" id="new_img_div_' + inc_id + '" >';
            html += ' <div id="sfile_logo_' + inc_id + '" class="file_logo"> <span class="btn add-files fileinput-button"> <img alt="" src="img/attachment_icon.png" class="add-logo_plus"> <span>Attach</span>';
            html += '<input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="app_logo[]" id="app_logo_' + inc_id + '"> </span><div></div></div>';
            html += '<div class="cl height1"></div>';

            html += '</div>';

            $("#select-app-img").append(html);
        }

    });

    search('', 'tblapp_faq', ['question', 'answer'],<?php echo '0' ?>,<?php echo '10' ?>);

    $(document).ready(function() {
        $('.review_box').hide();
        $('#keyword').show();
    });
    get_communicatr_details('<?php echo $_REQUEST['support_id'] ?>');
    $('html').css('overflow', 'hidden');


</script> 
<style>
    .disabled1 {
        z-index: 1000;    
        opacity: 0.6;
        pointer-events: none;
    }
</style>