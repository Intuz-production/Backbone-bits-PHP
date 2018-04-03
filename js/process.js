var url = 'http://localhost/App-tools/PHP/cladmin/';

function update(id, table_name, url) {

    $('#noty_topCenter_layout_container').remove();
    var data = $('#' + id).serializefiles();
    data.append("table_name", table_name);
    var valid = $('#' + id).valid();

    if (valid != false) {
        $.ajax({
            type: "POST",
            url: "process-update.php?url=" + url,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                if (url == 'profile') {
                    $('.save_all').text('Loading..');
                    $('.save_all').attr('disabled', 'disabled');
                }
            },
            success: function(data) {
                $('#noty_topCenter_layout_container').remove();
                if ((table_name == 'tblmember') && url != 'profile') {
                    $("#display_" + data['intid']).hide();
                    $("#" + data['intid']).empty();
                    $("#" + data['intid']).append(data['html']);
                    $("#" + data['intid']).show();

                    if (data['output'] == 'S') {
                        message(data['msg'], 'success');
                        if (data['logo'] == 'Y') {
                            $('.mainlogo_' + data['intid']).attr('onclick', 'getidimage(' + data["intid"] + ')');
                        }
                    } else if (data['output'] == 'F') {
                        message(data['msg'], 'error');
                    }
                } else if (url == 'profile') {
                    if (data['name'] == '') {
                        $('.dropdown-toggle > span').html(data['name'] + '<i class="fa fa-fw fa-angle-down"></i>');
                        $('div.user_email_id > h2').text(data['name']);
                        $('div.user_email_id > p').text(data['email']);
                    }

                    if (data['output'] == 'S') {
                        var intid = data['intid'];
                        if (data['logo'] == 'Y') {
                            $('.mainlogo').children('div').removeClass("preview_small_img").addClass("upload_img");
                            $('.mainlogo').children('div').removeAttr('id').attr('id', "remove_logo");
                            $('.mainlogo').children('div').removeAttr('onclick');

                            $('#remove_logo').on('click', function() {

                                $.prompt("", {
                                    title: "Are you sure you want to delete this profie logo?",
                                    buttons: {
                                        "Yes": true,
                                        "Cancel": false
                                    },
                                    submit: function(e, v, m, f) {
                                        if (v == false) {} else {
                                            var type = 'user';
                                            var data = "ajax=1&action=delete_image&intid=" + intid + "&type=" + type;
                                            $("#overlays").show();
                                            request = $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
                                                beforeSend: function() {

                                                },
                                                success: function(data) {
                                                    var img = $('#old_logo').val();
                                                    $('#del_old_logo').val(img);
                                                    $('#remove_logo').remove();
                                                    $('#remove_preview_img').remove();
                                                    $("#overlays").hide();
                                                }
                                            });
                                        }
                                    }
                                });
                            });
                        }
                        if (data['company_logo'] == 'Y') {
                            $('.comp').children('div').removeClass("preview_small_img").addClass("upload_img");
                            $('.comp').children('div').removeAttr('id').attr('id', "remove_comp_logo");
                            $('.comp').children('div').removeAttr('onclick');

                            $('#remove_comp_logo').on('click', function() {

                                $.prompt("", {
                                    title: "Are you sure you want to delete this company logo?",
                                    buttons: {
                                        "Yes": true,
                                        "Cancel": false
                                    },
                                    submit: function(e, v, m, f) {
                                        if (v == false) {} else {
                                            var type = 'company';
                                            var data = "ajax=1&action=delete_image&intid=" + intid + "&type=" + type;
                                            $("#overlays").show();
                                            request = $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
                                                beforeSend: function() {

                                                },
                                                success: function(data) {
                                                    var img = $('#old_company_logo').val();
                                                    $('#del_old_company_logo').val(img);
                                                    $('#remove_comp_logo').remove();
                                                    $('#remove_preview_company_img').remove();
                                                    $("#overlays").hide();
                                                }
                                            });
                                        }
                                    }
                                });
                            });
                        }
                        $('.save_all').text('Save Changes');
                        $('.save_all').removeAttr('disabled');
                        message(data['msg'], 'success');
                    } else if (data['output'] == 'F') {
                        $('.save_all').text('Save Changes');
                        $('.save_all').removeAttr('disabled', 'disabled');
                        message(data['msg'], 'error');
                    }
                }
            }
        });
    }
}

function add(id, table_name) {
    var data = $('#' + id).serializefiles();

    data.append("table_name", table_name);
    if (table_name == 'tblapp_support') {
        var sendstatus = $('#status').val();
        var support_id = $('#request_id').val();
    }
    if (table_name == 'tblapp_faq') {
        var support_id = $('#support_id').val();
        var canned = $('#canned').val();

        if (canned == 'Y') {
            data.append("is_canned", canned);
        } else {
            data.append("is_canned", canned);
        }
    }
    var valid = $('#' + id).valid();

    if (valid != false) {
        $.ajax({
            type: "POST",
            url: "process-add.php",
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                if (table_name == 'tblmember') {
                    $('.save_all').text('Loading..');
                    $('.save_all').attr('disabled', 'disabled');

                }
                if (table_name == 'tblapp_support') {
                    $("#overlays").show();
                    $('#send_data').attr('disabled', 'disabled');
                    $('#send_close_data').attr('disabled', 'disabled');
                    $('.send_canned_and_save').attr('disabled', 'disabled');
                }
                if (table_name == 'tblapp_faq') {
                    $("#overlays").show();
                }
            },
            success: function(data) {

                if (table_name == 'tblmember') {
                    search('', 'tblmember', ['fname', 'lname', 'email', 'company'], 0, 10);
                    $('.save_all').removeAttr('disabled');
                    dlg.toggle();
                }
                if (table_name == 'tblapp_support') {
                    $('#select-app-img').empty();

                    if (sendstatus == 'close') {
                        $('#status').val('reopen');
                        $('.greencolorclass').html('<i class="apps_i reply_i fl" title=""> </i>Re-open');
                    } 
                    else {
                        $('.greencolorclass').html('<i class="apps_i reply_i fl" title=""> </i>Reply');
                        $('#status').val('replied');
                    }

                    var html = '';
                    html += '<div class="add_new_img" id="new_img_div_0" >';
                    html += ' <div id="sfile_logo_0" class="file_logo"> <span class="btn add-files fileinput-button"> <img alt="" src="img/attachment_icon.png" class="add-logo_plus"> <span>Attach</span>';
                    html += '<input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="app_logo[]" id="app_logo_0"> </span><div></div></div>';
                    html += '<div class="cl height1"></div>';
                    html += '</div>';

                    $("#select-app-img").append(html);

                    get_communicatr_details(support_id);
                    $('#message').val('');
                    search('', 'tblapp_faq', ['question', 'answer'], 0, 10);
                    $('.send_canned_and_save').text('Send & Add to Canned Response');
                    $('#send_data').text('Send');
                    $('#send_close_data').text('Send & Close');
                    $("#overlays").hide();
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
                }
                if (table_name == 'tblapp_faq') {
                    $('#status').val('replied');
                    $('#question').val('');
                    search('', 'tblapp_faq', ['question', 'answer'], 0, 10);

                    $("#overlays").hide();
                    dlg11.toggle(dlg11);
                }

                if (data['output'] == 'S') {
                    message(data['msg'], 'success');
                } else if (data['output'] == 'F') {
                    message(data['msg'], 'error');
                }
            }
        });
    }
}


function search(keyword, tablename, columnName, limitstart, limit, noempty, type) {
    if (noempty != 'noempty') {
        $('.classtablenew tbody').empty();
        $('#navview').empty();

        $("#overlays").show();

    }
    if (tablename == 'tblmember_apps' || tablename == 'tblapp_faq' || tablename == 'tblapp_whatsnew' || tablename == 'tblapp_tutorial_settings') {
        var app_id = $('#sel_app_id_ajax').val();
    }
    if (tablename == 'tblapp_faq') {
        var ver_id = $('#ver_id').val();
    }
    $.ajax({
        type: "POST",
        url: "process-search.php",
        async: false,
        data: {
            keyword: keyword,
            tablename: tablename,
            columnName: columnName,
            limitstart: limitstart,
            limit: limit,
            type: type,
            app_id: app_id,
            ver_id: ver_id
        },
        success: function(data) {
            $("#overlays").hide();

            if (tablename == 'tblmember') {
                var dat = JSON.parse(data);
                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
            } else if (tablename == 'tbl_access_log') {
                var dat = JSON.parse(data);
                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
            } else if (tablename == 'tbltransactions') {
                var dat = JSON.parse(data);
                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
            } else if (tablename == 'tblapp_tutorial_settings') { //Helpr Archive
                var dat = JSON.parse(data);
                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
            } else if (tablename == 'tblapp_faq') { //Helpr + Respond
                var dat = JSON.parse(data);
                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);

                //For applying icheck for data above
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-red',
                    radioClass: 'iradio_square-red',
                    increaseArea: '50%' // optional
                });
            } else if (tablename == 'tblmember_apps') {
                var dat = JSON.parse(data);

                $('#navview').empty();
                $('#showdata').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);

                //For applying icheck for data above
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-red',
                    radioClass: 'iradio_square-red',
                    increaseArea: '50%' // optional
                });

                //Get image preview
                $('input[type="file"]').on('change', function(e) {
                    var id = this.id;
                    var name = $(this).attr('name');

                    if (name == 'more_app_logo') {
                        var idr = id.split("more_app_logo_")[1];
                    } else {
                        var idr = id.split("more_app_img_")[1];
                    }
                    var fileInput = document.getElementById(id);

                    var file = fileInput.files[0];
                    var imageType = /image.*/;
                    if (file.type.match(imageType)) {

                        var reader = new FileReader();
                        reader.onload = function(e) {

                            $('#preview_more_img_' + idr).remove();
                            $('#sfile_logo_' + idr).children('span').after('<div id="preview_more_img_' + idr + '" class="preview_inner_img"><div class="center-img"><img  id="more-img" src="' + reader.result + '" height="255" width="255" ></div><div id="remove_more_img" class="over_img">\n\
                                                        <a href="javascript:;" onclick="remove_manual_logo(2,' + idr + ')" ><i class="apps_i remove_icon_" style="font-size:24px"></i></a></div></div>');

                            $('#sfile_logo_' + idr).addClass('active');
                            $('#sfile_logo_' + idr).children('div:last').addClass('active');
                            $('#sfile_logo_' + idr).attr("onclick", "select_manual_logo(this.id," + idr + ")");
                            $("#select-app-img_" + idr).children('li').removeClass('active');

                            if (name == 'more_app_logo') {

                                $('#more_app_sel_new_img_' + idr).val(reader.result);
                                $('#more_app_sel_img_' + idr).val('');
                            }
                        }
                        reader.readAsDataURL(file);
                    } else {
                        $('#preview_more_img_' + idr).remove();
                        $('#more_app_type_error').remove();
                        $('#sfile_logo_' + idr).after('<div class="error_msg" id="more_app_type_error">File not supported!</div>');
                    }

                });

            }
        }
    });
}

function delete_agent(id) {
    $.prompt("", {
        title: "Are you sure you want to delete this user?",
        buttons: {
            "Yes, I'm Ready": true,
            "No, Lets Wait": false
        },
        submit: function(e, v, m, f) {

            if (v == false) {
            } else {

                var data = "ajax=1&action=delete_user&intid=" + id;
                request = $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                    },
                    success: function(data) {

                        if (data['output'] == 'S') {
                            $('#display_' + id).remove();
                            message(data['msg'], 'success');

                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }
                    }
                });
            }
        }
    });
}


function delete_log(id) {
    $.prompt("", {
        title: "Are you sure you want to delete this log?",
        buttons: {
            "Yes, I'm Ready": true,
            "No, Lets Wait": false
        },
        submit: function(e, v, m, f) {

            if (v == false) {
            } else {
                var data = "ajax=1&action=delete_log&intid=" + id;
                request = $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                    },
                    success: function(data) {
                        if (data['output'] == 'S') {
                            $('#' + id).remove();
                            message(data['msg'], 'success');
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }


                    }
                });

            }
        }
    });
}


function spliturl_paging(url, columns, type) {
    var spliturl = url.split('&');
    var limitstart = spliturl[0].split('=');
    var keyword = spliturl[1].split('=');
    var tablename = spliturl[2].split('=');
    var limit = spliturl[3].split('=');
    search(keyword[1], tablename[1], columns, limitstart[1], limit[1], 'noempty', type);
}

function get_communicatr_details(support_id) {
    $.ajax({
        type: "POST",
        url: "respond-detail-ajax.php",
        async: false,
        data: {
            support_id: support_id
        },
        success: function(data) {
            $("#overlays").hide();
            $('#support_data').empty();
            $('#support_data').append(data);
        }
    });
}