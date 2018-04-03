<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
require("config/configuration.php");
if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

$member_id = $_SESSION['custid'];

if (isset($_SESSION['app_type'])) {
    $app_type = $_SESSION['app_type'];
} else {
    $app_type = 'ios';
}

if ($app_type == 'ios') {
    $placeholder = 'https://itunes.apple.com/us/app/appname';
    $app_id_placeholder = '284882215';
} else {
    $placeholder = 'http://play.google.com/store/apps/details?id=yourappbundleid';
    $app_id_placeholder = 'com.facebook.orca';
}

?><form id="addappautofrm" method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" id="action" name="action" value="<?php echo 'save_auto_apps'; ?>" />
    <input type="hidden" id="ajax" name="ajax" value="1" />
    <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >
    <input type="hidden" name="app_count" id="app_count" value="1" > 
    <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id; ?>">
    <input type="hidden" name="sel_unique_id" id="sel_unique_id" value="">
    <!--=============== Add App Automatically ======================= -->
    <div id="add-automatically">
        <div id="serch" class="serch_div">
            <div class="col-xs-12 col-md-8">
                <div class="form-group">
                    <label>Search with developer or app name</label>
                    <div class="ui-front input-group" style="width:100%;">
                        <input type="text" placeholder="e.g. Facebook" class="form-control ui-autocomplete-input" id="company_name" name="company_name" autocomplete="off" data-provide="typeahead" >
                    </div>
                    <!-- /.input group --> 
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div class="form-group">
                    <label>Platform</label>
                    <div class="input-group" style="width:100%;">
                        <ul class="all-apps-icon add-apps-os-icon" style="margin:0 0 0 -5px;">
                            <li class="ip <?php if ($app_type == '' || $app_type == 'ios') { ?> active <?php } ?>">&nbsp;</li>
                            <li class="ad <?php if ($app_type == 'android') { ?> active <?php } ?>">&nbsp;</li>
                        </ul>
                    </div>
                    <!-- /.input group --> 
                </div>
            </div>
            <div class="cl"></div>
            <div class="col-xs-12 col-md-12 or"> OR </div>
            <div class="col-xs-12 col-md-12 development__"><a href="javascript:;" class="currnt__" id="cur_devel">Add an app that is currently in development</a></div>
            <!-- Footer -->
            <div class="popap-footer">
                <button id="next1" class="btn btn-primary fr button_submit" >Next</button>
            </div>
        </div>
        <div id="result" class="result_div slimScrollDiv">
            <div id="chat-box" class="col-xs-12 col-md-12 apps_list">
                <div class="col-md-12 apps_listings" style="padding:0;">
                    <div class="row" id="apps_list">

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="popap-footer">
                <button id="next2" class="btn btn-primary fr button_submit" >Next</button>
                &nbsp; &nbsp;
                <button id="back1" class="btn btn-primary fr button_submit" >Back</button>
            </div>
        </div>
        <div id="app-add" class="result_div">
            <div class="col-xs-12 col-md-12" id="apps_preview">
                <!-- Display App preview here -->
            </div>
            <div class="popap-footer">
                <button id="next3" class="btn btn-primary fr button_submit" data-loading-text="Loading..." >Add Now</button>
                &nbsp; &nbsp;
                <button id="back2" class="btn btn-primary fr button_submit" >Back</button>
            </div>
        </div>
    </div>
</form>
<!--========================================================================================================= --> 
<!--=============== Add App manually ======================= --> 
<!--========================================================================================================= -->
<form id="addappfrm" method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" id="action" name="action" value="<?php echo 'save_app'; ?>" />
    <input type="hidden" id="ajax" name="ajax" value="1" />
    <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >
    <input type="hidden" name="app_count" id="app_count" value="1" > 
    <input type="hidden" name="member_id" id="member_id" value="<?php echo $member_id; ?>">
    <div id="add-manually">
        <div class="col-xs-12 col-md-12">
            <div class="add_apps_box">
                <div class="row">
                    <div class="col-xs-12 col-md-1 input-group fr"> <span class="label label-danger">In Development</span></div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="col-xs-12 col-md-12 manually_chan"> 
                        <div class="col-md-12 padding0 manually_chan2">
                            <div id="file_logo"> <span class="btn add-files fileinput-button"> <img width="30" class="add-logo_plus" src="img/upload_icon.png" alt="" title=""> <span>Upload LOGO</span>
                                    <input type="file" id="app_logo" name="app_logo" class="form-control tutorial-input" placeholder="Logo-file">
                                </span>

                            </div>
                        </div>
                        <div class="cl height2"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-5">
                    <div class="form-group">
                        <label>Display</label>
                        <div class="input-group">
                            <input type="text" placeholder="e.g. Facebook" class="form-control" value="" id="app_name" name="app_name">
                        </div>
                        <!-- /.input group --> 
                    </div>
                    <div class="form-group">
                        <label>App URL</label>
                        <div class="input-group">
                            <input type="text"  value="" id="app_url" name="app_url" class="form-control" placeholder="e.g. <?php echo $placeholder ?>">
                        </div>
                        <!-- /.input group --> 
                    </div>

                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="form-group">
                        <label>Bundle ID </label>
                        <div class="input-group">
                            <input type="text" value="" id="app_store_id" name="app_store_id" class="form-control" placeholder="e.g. com.companyname.appname">

                        </div>
                        <!-- /.input group --> 
                    </div>
                    <div class="form-group">
                        <label>App ID</label>
                        <div class="input-group">
                            <input type="text" value="" id="track_id" name="track_id" placeholder="e.g. <?php echo $app_id_placeholder ?>" class="form-control">
                        </div>
                        <!-- /.input group --> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-12 development__"><a href="#" class="currnt__" id="cur_devel2">Add automatically</a></div>
        <!-- Footer -->
        <div class="popap-footer">
            <button id="save_app" class="btn btn-primary fr button_submit" data-loading-text="Loading..." value="Save" >Save</button>
        </div>
    </div>
</form>

<script type="text/javascript">

    function remove_manual_logo(id) {
        if (id == '2') {
            $('#preview_more_img').remove();
            $('#more_app_store_logo').val($('#old_more_app_sel_new_img').val());
            $('#more_upload_img').remove();
            $("#select-app-img").children('li').first().addClass('active');
            var img = $("#select-app-img").children('li').children('img').attr('src');
            if ($('#more_inner_img').hasClass('upload_img')) {
                $('#more_inner_img').remove();
                $('#more_app_sel_img').val('');
                $('#more_app_sel_new_img').val(img);
            } else {
                $('#more_app_sel_img').val(img);
                $('#more_app_sel_new_img').val('');
            }


        } else if (id == '1') {
            $('#preview_img').remove();
            $('.preview_small_img').remove();
            $('#upload_img').remove();
        }
    }


    (function ($) {
        $.fn.serializefiles = function () {
            var obj = $(this);
            /* ADD FILE TO PARAM AJAX */
            var formData = new FormData();
            $.each($(obj).find("input[type='file']"), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            var params = $(obj).serializeArray();

            $.each(params, function (i, val) {
                formData.append(val.name, val.value);
            });
            return formData;
        };
    })(jQuery);

    $(document).ready(function () {
        if ($("#company_name").val() == '') {
            $("#next1").hide();
        }

        $("#next2").hide();

        $('#addappfrm').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                app_name: {
                    required: true,
                    maxlength: 100
                },
                app_store_id: "required",
                app_url: {
                    url: true
                }
            },
            messages: {
                app_name: {
                    required: "",
                    maxlength: "max 25 characters"
                },
                app_store_id: "",
                app_url: {
                    required: "",
                    url: ""
                }
            }
        });

        $("a#cur_devel").click(function (e) {
            $("#add-automatically").hide("");
            $("#add-manually").show("");
        });

        $("a#cur_devel2").click(function (e) {
            $("#add-automatically").show("");
            $("#add-manually").hide("");
            $('#company_name').focus();
            $('#company_name').val("");
            $("#next1").fadeOut();
        });

        $("button#next1").click(function (e) {
            e.preventDefault();
            if (globalvar == 1) {
                $("#serch").hide("");
                $("#app-add").show("");
            } else {
                return false;
            }
        });

        $("button#back1").click(function (e) {
            e.preventDefault();
            $("#serch").show("");
            $("#result").hide("");
            $('#company_name').focus();
            $("#next1").fadeOut();
        });

        $("button#next2").click(function (e) {
            e.preventDefault();
            $("#result").hide("");
            $("#app-add").show("");
        });

        $("button#back2").click(function (e) {
            e.preventDefault();
            $("#result").hide("");
            $('#company_name').focus();
            $("#next1").fadeOut();
            $("#app-add").hide("");
        });

        var globalvar = 0;
        //Function to add apps   

        $('#save_app').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
            $('#addappfrm').valid();

            var go = 1;
            $('#addappfrm').find('input').each(function () {
                if ($(this).hasClass('error')) {
                    go = 0;
                    return false;
                }
            });

            if (go == 1) {

                var status = $(this).val();
                $('#save_app').removeClass('active');
                $('.' + status).addClass('active');

                var btn = $(this);
                var data = $("#addappfrm").serializefiles();

                request = $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        btn.button('loading');

                    },
                    success: function (data) {
                        btn.button('reset');

                        if (data['output'] == 'S') {
                            message(data['msg'], 'success');
                            // Call dialogue box
                            dlg.toggle();
                            var q = '';
                            select_os(data['app_type'], 'apps', q);
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }
                    }
                });
                e.preventDefault();
            } else {
                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
            }
        });

        //Company name search autocomplete and add auto apps
        $("#company_name").autocomplete({
            appendTo: "#add-automatically",
            source: function (request, response) {
                var app_type = $('#app_type').val();

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: 'ajax.php?ajax=1&action=auto_search_company_apps&company_name=' + $('#company_name').val() + '&app_type=' + app_type,
                    success: function (data) {
                        $('.ui-helper-hidden-accessible').hide();
                        if ((Object.keys(data).length - 1) > 0) {
                            globalvar = 1;
                        } else {
                            globalvar = 0;
                        }
                        $('#company_name').removeClass('ui-autocomplete-loading');  // hide loading image
                        delete data.app_preview;
                        response($.map(data, function (item) {
                            return {
                                label: item.name,
                                value: item.id,
                                desc: item.link,
                                trackid: item.trackid
                            }
                        }));
                    },
                    error: function (data) {
                        $('#company_name').removeClass('ui-autocomplete-loading');
                        $("#next1").fadeOut();
                    }
                });
            },
            minLength: 2,
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            },
            focus: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
            },
            change: function () {
                if ($("#company_name").val() == '') {
                    $("#next1").fadeOut();
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $(this).attr('value', ui.item.label);
                $('#company_name').val(ui.item.label);
                $('#company_name').addClass('ui-autocomplete-loading');

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: 'ajax.php?ajax=1&action=auto_search_company_apps&flag=all&company_name=' + ui.item.label + '&app_store_id=' + ui.item.value + '&trackid=' + ui.item.trackid + '&app_type=' + $('#app_type').val(),
                    success: function (data) {
                        $('.ui-helper-hidden-accessible').hide();
                        $('#company_name').removeClass('ui-autocomplete-loading');  // hide loading image

                        //display all the data
                        $("#next1").fadeIn();
                        $('#apps_list').html(data['res']);
                        $('#apps_preview').html(data['app_preview']);
                        $('#app_count').val(data['total']);                                
                        $('input').iCheck({
                            checkboxClass: 'icheckbox_square-red',
                            radioClass: 'iradio_square-red',
                            increaseArea: '50%'
                        });

                        //save auto apps  
                        $('#next3').off('click').on('click', function (e) {
                            $('#noty_topCenter_layout_container').remove();
                            $('#addappautofrm').valid();

                            var go = 1;
                            $('#addappautofrm').find('input').each(function () {
                                if ($(this).hasClass('error')) {
                                    go = 0;
                                    return false;
                                }
                            });

                            if (go == 1) {
                                var wh = '';
                                if (script != 'add') {
                                    var intid = $('#intid').val();
                                    wh = "&intid=" + intid;
                                }

                                var btn = $(this);


                                var data = $("#addappautofrm").serializefiles();
                                data.append("status", "active");

                                request = $.ajax({
                                    type: "POST",
                                    url: "ajax.php",
                                    data: data,
                                    dataType: 'json',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    beforeSend: function () {
                                        btn.button('loading');

                                    },
                                    success: function (data) {
                                        btn.button('reset');
                                        if (data['output'] == 'S') {
                                            message(data['msg'], 'success');
                                            $("#no_record").val(data['no_record']);
                                            $("#no_ad_record").val(data['no_record']);
                                            // Call dialogue box
                                            dlg.toggle();
                                            var q = '';
                                            $('#q').val('');
                                            select_os(data['app_type'], 'apps', q);

                                            $('.all-apps-icon >li').removeClass('active');

                                            if (data['app_type'] == 'ios') {
                                                $('.ip').addClass('active');
                                            } else if (data['app_type'] == 'android') {
                                                $('.ad').addClass('active');
                                            } else {
                                                $('.all_apps').addClass('active');
                                            }
                                        } else if (data['output'] == 'F') {
                                            message(data['msg'], 'error');
                                        }
                                    }
                                });
                                e.preventDefault();
                            } else {
                                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
                            }
                        });
                    },
                    error: function (data) {
                        $('#company_name').removeClass('ui-autocomplete-loading');
                        $("#next1").fadeOut();
                    }
                });
            }
        });

        $("#company_name").focus();
        //change OS 
        $('.add-apps-os-icon >li').off('click').on('click', function () {
            $('.add-apps-os-icon >li').removeClass('active');
            $(this).addClass('active');
            if ($(this).hasClass('ip')) {
                var app_type = 'ios';
                $("#company_name").focus();
            } else if ($(this).hasClass('ad')) {
                var app_type = 'android';
                $("#add-automatically").hide("");
                $("#add-manually").show("");
                $('#cur_devel2').hide();
                $('.ad').addClass('active');
                $('.all_apps').removeClass('active');
                $('.ip').removeClass('active');
            } else if ($(this).hasClass('wn')) {
                var app_type = 'windows';
            } else if ($('.all_apps').hasClass('active')) {
                var app_type = 'all';
            }

            $('#addappautofrm').children('#app_type').val(app_type);
            $('#addappfrm').children('#app_type').val(app_type);

            $("#company_name").val('');
            $("#next1").fadeOut();

        });

        //on file change     
        $('input[type="file"]').on('change', function (e) {
            var id = this.id;
            var fileInput = document.getElementById(id);

            var file = fileInput.files[0];
            var imageType = /image.*/;
            if (file.type.match(imageType)) {

                var reader = new FileReader();
                reader.onload = function (e) {

                    if (id == 'app_logo') {
                        $('#file_logo').children('span').after('<div id="preview_img" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" height="255" width="255" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(1)"><i class="apps_i edit_icon"></i></a></div></div>');
                        $('#app_type_error').remove();
                    } else if (id == 'more_app_logo' || id == 'more_app_img') {
                        $('#sfile_logo').children('span').after('<div id="preview_more_img" class="preview_inner_img"><div class="center-img"><img id="more-img" src="' + reader.result + '" style="height:auto;width:230px;" ></div><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(2)" ><i class="apps_i edit_icon" style="font-size:24px"></i></a></div></div>');

                        $("#select-app-img").children('li').removeClass('active');
                        $('#more_app_sel_new_img').val(reader.result);
                        $('#more_app_type_error').remove();
                    }
                }
                reader.readAsDataURL(file);
            } else {
                if (id == 'app_logo') {
                    $('#preview_img').remove();
                    $('#app_type_error').remove();
                    $('#file_logo').after('<div class="error_msg" id="app_type_error">File not supported!</div>');

                } else if (id == 'more_app_logo' || id == 'more_app_img') {
                    $('#preview_more_img').remove();
                    $('#more_app_type_error').remove();
                    $('#sfile_logo').after('<div class="error_msg" id="more_app_type_error">File not supported!</div>');

                }
            }
        });

    });

    function get_app_preview(id) {
        $('#sel_unique_id').val(id);
        $("#" + id).show();
        $("#result").hide("");

        $("#app-add").hide("");
    }

    $(".apps_listings").slimScroll({
        height: '300px'
    });

    if ($('.ad').hasClass('active')) {
        var app_type = 'android';

        $("#add-automatically").hide("");
        $("#add-manually").show("");
        $('#cur_devel2').hide();
        $('.ad').addClass('active');
        $('.all_apps').removeClass('active');
        $('.ip').removeClass('active');
    }
</script>
<!-- datepicker -->