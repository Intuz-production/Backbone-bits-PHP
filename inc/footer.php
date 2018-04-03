<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

if ($member_id == $_SESSION['agents_cust_id']) {
    $append_data = 'AND tblapp_support.parent_id = \'' . $member_id . '\'';
} else {
    $append_data = 'AND tblapp_support.member_id = \'' . $_SESSION['agents_cust_id'] . '\'';
}

$app_name_distinct_data = $dclass->select('*, tblapp_support.intid as support_id, tblapp_support.request_id as request_id ', 'tblapp_support, tblmember_apps, tblmember_app_features', ' AND tblmember_app_features.app_id = tblmember_apps.intid AND tblmember_app_features.feature_status=\'running\' AND feature_id=\'3\' AND tblmember_apps.intid = tblapp_support.app_id  ' . $append_data . ' AND is_read = "N"  order by tblapp_support.dtadd desc');

if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'support'):
    ?><!-- Messages: style can be found in dropdown.less-->

    <div id="showmess"> </div>
    <script>
        setInterval(function () {
            $.ajax({
                type: "POST",
                url: "respond_notification_ajax.php",
                data: "",
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.count != 0) {
                        $('.messages-menu > a').html('<i class="fa fa-envelope"></i><span class="label label-success">' + data.count + '</span>');
                        $('.messages-menu').show();
                    }

                    $.each(data.data, function (key, element) {
                        if (element.request_type == 'bug') {
                            var classname = 'request_i1';
                        } else if (element.request_type == 'feedback') {
                            var classname = 'request_i2';
                        } else if (element.request_type == 'query') {
                            var classname = 'request_i3';
                        }
                        if (element.request_id == 0) {
                            var support_id = element.support_id;
                        } else {
                            var support_id = element.request_id;
                        }

                        var stringd = '<div class="messages_alert">' + '<a onclick="unread_message(\'' + support_id + '\')" href="respond-detail?support_id=' + support_id + '&sel_app_id=' + element.app_id + '">' + '<div class="fl left_icon"><i class="apps_i ' + classname + ' "></i></div>' + '<div class="fl right_messages">' +
                                '<h4><span>' + element.app_name + '</span></i></h4>' +
                                '<p>' + element.message + '</p>' + '</div>' +
                                '</a>' + '<i class="apps_i remove_icon fr" title="Delete" onclick="$(\'.messages_alert\').hide();"></div>';

                        $('#showmess').fadeIn().append(stringd);

                        var tostringdata = '<li><a onclick="unread_message(\'' + support_id + '\')" href="respond-detail?support_id=' + support_id + '&sel_app_id=' + element.app_id + '">' + '<div class="pull-left">' +
                                '<i class="apps_i ' + classname + '"></i></div><h4>' + element.app_name + '</h4><p>' + element.message + '</p></a></li>';
                        $('.menu').append(tostringdata);
                    });
                    $("#showmess").fadeOut(5000);
                }
            });
        }, 15000);
    </script>
<?php endif; ?>

<!-- edit popup -->
<div class="modal fade" id="DownloadSDK" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel" style="font-size:24px;">Download SDK</h4>
            </div>
            <div class="modal-body">
                <div class="ioss fl"><a href="https://github.com/Intuz-production/Backbone-bits-iOS"><i class="fa fa-fw fa-apple"></i> <br/>IOS</a></div>
                <div class="androids fr"><a href="https://github.com/Intuz-production/Backbone-bits-Android"> <i class="fa fa-fw fa-android"></i> <br/>Android</a></div>
            </div>
            <div class="cl height3"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="ContactUs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel" style="font-size:24px;">Contact Us</h4>
            </div>
            <div class="modal-body"> Have any question, comments, or feedback?<br/>
                Reach out to us anytime<br/>
                <br/>
                <textarea name="feedback" id="feedback" class="textarea" style="width:100%; height:100px; font-size: 14px; line-height: 18px; border: 1px solid rgb(221, 221, 221); padding: 10px;" placeholder="Place some text here"></textarea>
            </div>
            <div class="modal-footer"> 
                <button id="send_contact_email" class="btn btn-primary export" value="Send" type="button"style=" width:120px;">Send</button>
            </div>
        </div>
    </div>
</div>

<!-- END Popup -->

</div>

<!-- jQuery 2.0.2 --> 
<script src="js/jquery.min.js"></script> 
<!-- jQuery UI 1.10.3 --> 
<script src="js/jquery-ui-1.10.3.min.js" type="text/javascript"></script> 
<!-- Bootstrap --> 
<script src="js/bootstrap.min.js" type="text/javascript"></script>

<!-- daterangepicker --> 
<script src="js/plugins/daterangepicker/moment.js" type="text/javascript"></script> 
<script src="js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script> 

<!-- datepicker --> 
<script src="js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>

<script src="js/jquery-validate.js"></script> 
<script src="js/jquery.stylish-select.js" type="text/javascript"></script> 

<!-- Jquery noty for custom messages --> 
<script type="text/javascript" src="js/plugins/noty/jquery.noty.js"></script> 
<script type="text/javascript" src="js/plugins/noty/topCenter.js"></script> 
<script type="text/javascript" src="js/plugins/noty/default.js"></script> 

<!-- Jquery impromptu for custom prompts -->
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-impromptu/jquery-impromptu.css" />
<script type="text/javascript" src="js/plugins/jquery-impromptu/jquery-impromptu.js"></script> 

<!-- Jquery intro js for help tip -->
<link rel="stylesheet" media="all" type="text/css" href="css/introjs.min.css" />
<script type="text/javascript" src="js/intro.min.js"></script> 

<!-- Jquery ferro menu for circular icons -->
<link rel="stylesheet" media="all" type="text/css" href="css/jquery.ferro.ferroMenu.css" />
<script type="text/javascript" src="js/jquery.ferro.ferroMenu-1.2.3.min.js"></script> 
<script type="text/javascript">

    //reset file type fields
    function reset_field(e) {
        e.wrap('<form>').parent('form').trigger('reset');
        e.unwrap();
    }

    $(".right-side").click(function () {
        $('#bottommenu > li').removeClass('active');
        $('#bottommenu > li > ul.treeview-menu').css("display", "none");
        $('#topmenu > li').removeClass('active');
        $('#topmenu > li > ul.treeview-menu').css("display", "none");
        $('.treeview').removeClass('active');
        $('.sidebar-menu > .treeview-menu').slideUp('slow');
        $('.treeview-menu').removeClass('active');
    });

    function hide_overlay_left() {
        $('#bottommenu > li').removeClass('active');
        $('#topmenu > li').removeClass('active');
        $('.treeview-menu').slideUp('slow');
        $('.treeview').removeClass('active');
        $('.treeview-menu').removeClass('active');
        $('#overlays_left').fadeOut();
    }

    $(".treeview").click(function () {
<?php if ($_SESSION['role'] == 'finance') { ?>
            e.preventDefault();
<?php } ?>
        $("#overlays_left").fadeIn();
        $('.sidebar-menu > .treeview-menu').slideDown('slow');
    });

    function closebottommenu() {
        $("#overlays_left").fadeIn();
        $('#bottommenu > li').removeClass('active');
        $('#bottommenu > li > ul.treeview-menu').css("display", "none");
        $('#topmenu > li').addClass('active');
        $('#topmenu > li > ul.treeview-menu').slideDown('slow');
    }

    function closetopmenu() {
        $("#overlays_left").fadeIn();
        $('#topmenu > li').removeClass('active');
        $('#topmenu > li > ul.treeview-menu').css("display", "none");
        $('#bottommenu > li').addClass('active');
        $('#bottommenu > li > ul.treeview-menu').slideDown('slow');

    }

    function closebottom() {
        $('#DownloadSDK').modal('show');
        $('#DownloadSDK').addClass('in');
        $('#ContactUs').removeClass('in');
        $('#ContactUs').css("display", "none");
        $('#DownloadSDK').css("display", "block");
        $('#ContactUs').modal('hide');
    }

    function closetop() {
        $('#ContactUs').modal('show');
        $('#ContactUs').addClass('in');
        $('#DownloadSDK').removeClass('in');
        $('#ContactUs').css("display", "block");
        $('#DownloadSDK').modal('hide');
        $('#DownloadSDK').css("display", "none");
    }

    //server side page refresh
    function generate() {
        var n = noty({
            text: '<?php
                if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') {
                    echo $_SESSION['msg'];
                }
                ?>',
            type: '<?php
                if (isset($_SESSION['type'])) {
                    if ($_SESSION['type'] == 'succ' || $_SESSION['type'] == 'suc') {
                        echo "success";
                    } else {
                        echo "error";
                    }
                }
                ?>',
            dismissQueue: true,
            layout: 'topCenter',
            theme: 'defaultTheme',
            timeout: 1500
        });
    }

    //client side ajax
    function message(msg, status) {
        var n = noty({
            text: msg,
            type: status,
            dismissQueue: true,
            layout: 'topCenter',
            theme: 'defaultTheme',
            timeout: 1500
        });
    }

    $(document).ready(function () {
        var intro_flag = $("#intro_flag").val();
        //Initiate page guide if not displayed before
        if (intro_flag == 0) {
            <?php if ($url == 'dashboard') { ?>

                introJs().setOption('doneLabel', 'Next page').start().oncomplete(function () {

                    window.location.href = 'apps?list=apps&multipage=true';

                }).onbeforechange(function (targetElement) {
                })
            <?php } else { ?>
                introJs().start().oncomplete(function () {

                }).onbeforechange(function (targetElement) {
                })
            <?php } ?>

        }

        //Initiate page guide help tips on button click
        $("#pageguide").off("click").on("click", function (e) {
            <?php if ($url == 'dashboard') { ?>

                introJs().setOption('doneLabel', 'Next page').start().oncomplete(function () {

                    window.location.href = 'apps?list=apps&multipage=true';

                }).onbeforechange(function (targetElement) {
                })
            <?php } else { ?>
                introJs().start().oncomplete(function () {

                }).onbeforechange(function (targetElement) {
                })
            <?php } ?>

        });
        if (intro_flag == 1) {
        <?php if ($_GET['multipage'] == 'true') { ?>
                introJs().start();
        <?php } ?>
        }

        //change intro tip flag on skip  
        $(".introjs-skipbutton").on("click", function () {
            var data = "ajax=1&action=change_intro_flag&intid=<?php echo $member_id; ?>";
            request = $.ajax({
                type: "POST",
                url: "ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    if (data['output'] == 'S') {
                        $("#intro_flag").val('1');
                    }
                }
            });
        });

<?php
if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') {
    echo "generate()";
    unset($_SESSION['msg']);
    unset($_SESSION['type']);
}
?>
    });
</script> 
<script type="text/javascript">
    var page_url = '<?php echo $url; ?>';
    var script = $('#script').val();
    var listdata = '<?php echo $_REQUEST['list']; ?>';
<?php if ($url != 'unauthorize') { ?>
        function select_os(os_type, site_url, q) {
            var no_record = $("#no_record").val();
            var no_ad_record = $("#no_ad_record").val();
            if (!no_record)
                no_record = 0;

            if (!no_ad_record)
                no_ad_record = 0;

            var data = "ajax=1&action=select_os&app_type=" + os_type + "&member_id=" +<?php echo $member_id; ?> + "&q=" + q + "&site_url=" + site_url + "&list=<?php echo @$_REQUEST['list']; ?>" + "&sel_app_id=<?php echo @$_REQUEST['sel_app_id'] ?>&support_id=<?php echo @$_REQUEST['support_id']; ?>&no_record=" + no_record + "&no_ad_record=" + no_ad_record;

            if (site_url == 'apps') {
                $("#overlays").show();
            }
            request = $.ajax({
                type: "POST",
                url: "ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    if (site_url == 'apps') {
                        $("#overlays").hide();
                    }
                    if (data['output'] == 'S') {
                        if (page_url == 'apps') {
                            $('.row').empty().html(data['apps']);
                            $(".all-apps-icon").children('li').removeClass('active');
                            if (os_type == 'ios') {
                                $(".ip").addClass("active");
                            } else if (os_type == 'android') {
                                $(".ad").addClass("active");
                            } else if (os_type == 'all') {
                                $(".all_apps").addClass("active");
                            }
                        } else {
                            $('.rowdata').empty().html(data['apps']);
                        }
                    }
                }
            });
        }
<?php } ?>

    function select_app(id) {
        $('#sel_app_id').val(id);
        var form_data = $('#selectappfrm').serialize();
        request = $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "ajax=1&action=select_app&" + form_data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $("#ovelays").show();
                $('#loading-image').show();
                $('.preload-bg').show();
            },
            success: function (data) {
                $("#ovelays").hide();
                $('#loading-image').hide();
                $('.preload-bg').hide();

                if (data['output'] == 'S') {
                    location.href = '<?php echo $url; ?>?sel_app_id=' + data['sel_app_id'];
                }
            }
        });
    }

    $(document).ready(function () {
        $("#nav-overlays").on('click', function () {
            $.fn.ferroMenu.toggleMenu('#nav');
        });

        $("#nav").ferroMenu({drag: false, radius: 100});
<?php if ($url != 'dashboard') { ?>
            $(document).on("menuopened", function () {
                $("#nav-overlays").fadeIn();
            });

            $(document).on("menuclosed", function () {
                $("#nav-overlays").fadeOut();
            });
<?php } ?>

        //Contact us send feedback
        var sender_mail = '<?php echo $mdata[0]['email']; ?>';
        var sender_name = '<?php echo $mdata[0]['fname'] . " " . $mdata[0]['lname']; ?>';
        $('#send_contact_email').off('click').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();

            var go = 1;

            var feedback = $('#feedback').val();
            if (go == 1 && feedback != '') {
                $('#feedback').removeClass('error');
                var btn = $(this);
                var data = "ajax=1&action=send_contact&feedback=" + feedback + "&sender_email=" + sender_mail + "&sender_name=" + sender_name;
                request = $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        btn.button('loading');
                    },
                    success: function (data) {
                        btn.button('reset');

                        if (data['output'] == 'S') {
                            message(data['msg'], 'success');
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }
                        $('#ContactUs').modal('hide');
                    }
                });
            } else {
                $('#feedback').addClass('error');
                message('Fill up required fields', 'error');
            }
        });

        $('.all-apps-icon >li').off('click').on('click', function (e) {
            e.preventDefault();
            $('.all-apps-icon >li').removeClass('active');
            $(this).addClass('active');
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
            if (site_url != 'add-apps') {
                select_os(app_type, site_url, q);
            }
        });

        $('.analyzr_icon >li').off('click').on('click', function () {
            $('.analyzr_icon >li').removeClass('active');
            $(this).addClass('active');
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
            //alert(site_url);
            $('#app_type').val(app_type);
            var q = encodeURIComponent($("#q").val());
            if (site_url != 'add-apps') {
                select_os(app_type, site_url, q);
            }
        });

        //Styling dropdowns with jquery.stylish-select.js													
        $('.my-dropdown').sSelect();
        //set max height													
        $('.my-dropdownCountries').sSelect({ddMaxHeight: '300px'});
        //set value on click
        $('.setVal').click(function () {
            $('#my-dropdown5').getSetSSValue('4');
        });
        //get value on click
        $('.getVal').click(function () {
            alert('The value is: ' + $('.my-dropdown5').getSetSSValue());
        });
        //alert change event
        $('.my-dropdownChange').sSelect().change(function () {
            alert('changed')
        });
        //add options to select and update
        $('.addOptions').click(function () {
            $('.my-dropdown6').append('<option value="newOpt">New Option</option>').resetSS();
            return false;
        });

<?php if ($app_name != '') { ?>
            var selected_text = '<?php echo addslashes($app_name); ?>';
            $('.form-control-static').find('.selectedTxt').text(selected_text);
<?php } ?>
<?php if ($limit != '') { ?>
            $('.top-bax').find('.selectedTxt').text('<?php echo $limit; ?>');
<?php } else { ?>
            $('.top-bax').find('.selectedTxt').text('Page');
<?php } ?>

        if (page_url == 'add-apps' && script == 'add') {
            $('#addappfrm').find('.selectedTxt').text('In Development');
            $('.form-control-static').find('.selectedTxt').text('Select App');
        } else if (page_url == 'add-apps') {
            $('#addappfrm').find('.selectedTxt').text('<?php echo $server_status; ?>');
        }

        //Change feature status
        $('#changeappstatusfrm >h1>label.i-switchs').off('click').on('click', function (e) {
            var feature_status = $('#rfeature_status').val();
            if (feature_status == 'running')
                var status_msg = 'disable';
            else
                var status_msg = 'enable';

            $('#changeappstatusfrm').find('input:checkbox').iCheck('disable');

            $.prompt("", {
                title: "Are you sure you want to " + status_msg + " this feature?",
                buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
                submit: function (e, v, m, f) {
                    if (v == false) {
                    } else {
                        $('#changeappstatusfrm').find('input:checkbox').iCheck('enable');
                        if (feature_status == 'running') {
                            $('#feature_status').iCheck('uncheck');
                            $('#rfeature_status').val('pause');
                        } else {
                            $('#feature_status').iCheck('check');
                            $('#rfeature_status').val('running');
                        }
                        var form_data = $('#changeappstatusfrm').serialize();

                        request = $.ajax({
                            type: "POST",
                            url: "ajax.php",
                            data: "ajax=1&action=change_status&" + form_data,
                            dataType: 'json',
                            cache: false,
                            beforeSend: function () {
                                $("#ovelays").show();
                                $('#loading-image').show();
                                $('.preload-bg').show();
                            },
                            success: function (data) {
                                $("#ovelays").hide();
                                $('#loading-image').hide();
                                $('.preload-bg').hide();

                                if (data['output'] == 'S') {
                                    message(data['msg'], 'success');
                                } else if (data['output'] == 'F') {
                                    message(data['msg'], 'error');
                                }
                            }
                        });
                    }
                }
            });
        });


        //trigger add app other menu close patch
        $(".trigger").on('click', function () {
            var menu = $(".treeview-menu").first();
            var isActive = $('.treeview').hasClass('active');

            if (isActive) {
                menu.slideUp();
                $('.treeview').removeClass('active');
<?php if ($url != 'promotr' && $url != 'upgradr-settings' && $url != 'upgradr-archive' && $url != 'upgradr' && $url != 'upgradr-prev' && $url != 'upgradr-archive' && $url != 'help-faq-list' && $url != 'help-img-video' && $url != 'help-img-video-archive') { ?>
                    $('.treeview').children('a').trigger('click');
<?php } else if ($url == 'help-faq-list' || $url == 'help-img-video' || $url == 'help-img-video-archive') { ?>
                    $('.app_li').removeClass('active');
                    $('.all-apps_m').hide();
<?php } ?>
            }
        });
    });
</script> 
<script type="text/javascript">
    $(document).ready(function () {
<?php if ($url != 'apps') { ?>
            $('#q').on('keyup', function () {
                //alert('hi'); 
                var q = $(this).val();
                var app_type = '';
                if ($('.all-apps-icon >li.all_apps').hasClass('active')) {
                    app_type = '';
                } else if ($('.all-apps-icon >li.ip').hasClass('active')) {
                    app_type = 'ios';
                } else if ($('.all-apps-icon >li.ad').hasClass('active')) {
                    app_type = 'android';
                }
                $.ajax({dataType: "json", url: "ajax.php?ajax=1&action=search_app&q=" + q + "&member_id=<?php echo $member_id; ?>&site_url=<?php echo $url; ?>&list=<?php echo $_REQUEST['list']; ?>&support_id=<?php echo $_REQUEST['support_id']; ?>&app_type=" + app_type + "&sel_app_id=<?php echo $_REQUEST['sel_app_id'] ?>", success: function (data) {
                        $('.rowdata').empty().html(data['apps']);
                    }});
            });
<?php } ?>
    });
</script> 
<script type="text/javascript" src="js/plugins/iCheck/icheck.min.js"></script> 
<!-- AdminLTE App --> 
<script src="js/AdminLTE/app.js" type="text/javascript"></script> 

<!-- AdminLTE --> 
<script src="js/AdminLTE/demo.js" type="text/javascript"></script> 
<script src="js/custom.js" type="text/javascript"></script> 

<!-- Jquery bxslider for custom responsive sliders -->
<link rel="stylesheet" media="all" type="text/css" href="css/bxslider/jquery.bxslider.css" />
<script type="text/javascript" src="js/plugins/bxslider/jquery.bxslider.min.js"></script> 
<script>
    $(document).ready(function () {
        //show nav menus
        $("#nav").show();

        $('input').iCheck({
            checkboxClass: 'icheckbox_square',
            radioClass: 'iradio_square',
            increaseArea: '50%' // optional
        });
    });

    $(".leftside_listing_app").slimScroll({
        height: '285px'
    });
</script> 
<script>
    $(document).ready(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '50%' // optional
        });
    });
</script> 

<!-- JS for dynamic popup --> 
<script src="js/modernizr.min.js"></script>
<?php if ($url != 'dashboard' && $url != 'analyzr' && $url != 'analyzr-detail') { ?>
    <script src="js/classie.js"></script> 
    <script src="js/dialogFx.js"></script>
<?php } ?>
<?php if ($url != 'dashboard' && $url != 'analyzr' && $url != 'analyzr-detail' && $url != 'app-details' && $url != 'app-settings' && $url != 'notification' && $url != 'promotr-settings' && $url != 'upgrade-plan' && $url != 'invoice' && $url != 'respond-detail' && $url != 'unauthorize' && $url != 'access-log' && $url != 'respond') { ?>
    <script>
        (function () {
            var dlgtrigger = document.querySelector('[data-dialog]');

            somedialog = document.getElementById(dlgtrigger.getAttribute('data-dialog'));

            dlg = new DialogFx(somedialog);

            dlgtrigger.addEventListener('click', dlg.toggle.bind(dlg));
        })();
    </script>
<?php } ?>
<div style="display:none; opacity:0.5;  height:100%; width:100%; position:fixed; z-index:999; top:0px; left:0px;background-color:#fff;filter: alpha(opacity=50);" id="overlays">
    <div class="preload-bg">
        <div id='ajax_loader' style="position: fixed; left: 50%; top: 50%;"> <img src="img/ajax-loader-theme.gif"></img> </div>
        <div class="clear"></div>
    </div>
</div>
<div style="display:none; opacity:0.5;  height:100%; width:100%; position:fixed; z-index:1048; top:0px; left:0px;background-color:#000;filter: alpha(opacity=50);" id="overlays_left" onclick="hide_overlay_left()">
    <div class="preload-bg">
        <div id='ajax_loader' style="position: fixed; left: 50%; top: 50%;"> 
        </div>
        <div class="clear"></div>
    </div>
</div>
<div style="display:none; opacity:0.5;  height:100%; width:100%; position:fixed; z-index:1031; top:0px; left:0px;background-color:#000;filter: alpha(opacity=50);" id="nav-overlays"> </div>

<ul id="nav" class="ferro_menu_bottom" style="display:none;">
    <li><a class="documentation_" href="documentation/index.html" <?php if ($url != 'documentation') { ?> target="_blank" <?php } ?>><i class="menu_i document_"></i>
            <div class="popover top"><img src="img/document_text.png" alt="" /></div>
        </a></li>
    <li><a class="download_" href="javascript:void(0);" onclick="closebottom();"><i class="menu_i download_"></i>
            <div class="popover top"><img src="img/download_sdk_text.png" alt="" /></div>
        </a></li>
    <li><a class="contact_" href="javascript:void(0);" onclick="closetop();"><i class="menu_i contact_"></i>
            <div class="popover top"><img src="img/contact_text.png" alt="" /></div>
        </a></li>
    <li><a class="help_" id="pageguide" href="javascript:void(0);"><i class="menu_i help_"></i>
            <div class="popover top"><img src="img/help_text.png" alt="" /></div>
        </a></li>
</ul>

<!--================ Leftsitebar menu All page ======================= -->

<script type="text/javascript">

    $(document).ready(function () {

<?php if (!empty($_SESSION['app_id'])) { ?>
            $('#append_apps_detail > li > a > span').css('color', '#00CCCC');
            $('#append_apps_detail > li > a > i').css('color', '#00CCCC');
<?php } else { ?>
            $('#append_apps_detail > li > a > span').css('color', '#828282');
            $('#append_apps_detail > li > a > i').css('color', '#828282');
<?php } ?>
        $('li#allappss div.treeview a.add__').click(function () {
            if ($('ul.treeview-menu.popover.right').hasClass("leftmenubar")) {
                $('ul.treeview-menu.popover.right').addClass('showhidden');
                $('ul.treeview-menu.popover.right').removeClass('leftmenubar');
            } else {
                $('ul.treeview-menu.popover.right').removeClass('showhidden');
                $('ul.treeview-menu.popover.right').addClass('leftmenubar');
            }
        });

        $('li#topmenu div.treeview a.add__').click(function () {
            if ($('#popup_top').hasClass("leftmenubar_top")) {
                $('#popup_top').addClass('showhidden');
                $('#popup_top').removeClass('leftmenubar_top');
            } else {
                $('#popup_top').removeClass('showhidden');
                $('#popup_top').addClass('leftmenubar_top');
            }
        });

        $('ul#bottommenu div.treeview a.add__').click(function () {
            if ($('#popup_bottom').hasClass("leftmenubar_bottom")) {
                $('#popup_bottom').addClass('showhidden');
                $('#popup_bottom').removeClass('leftmenubar_bottom');
            } else {
                $('#popup_bottom').removeClass('showhidden');
                $('#popup_bottom').addClass('leftmenubar_bottom');
            }
        });

        $('#hide_communicatr div.treeview a.add__').click(function () {
            if ($('#popup_top').hasClass("leftmenubar_top")) {
                $('#popup_top').addClass('showhidden');
                $('#popup_top').removeClass('leftmenubar_top');
            } else {
                $('#popup_top').removeClass('showhidden');
                $('#popup_top').addClass('leftmenubar_top');
            }
        });

        $('#hide_app_communicatr a.add__').click(function () {
            if ($('#popup_bottom').hasClass("leftmenubar_bottom")) {
                $('#popup_bottom').addClass('showhidden');
                $('#popup_bottom').removeClass('leftmenubar_bottom');
            } else {
                $('#popup_bottom').removeClass('showhidden');
                $('#popup_bottom').addClass('leftmenubar_bottom');
            }
        });

        $("#overlays_left").click(function () {
            $('ul.treeview-menu.popover.right').removeClass('leftmenubar');
            $('#popup_top').removeClass('leftmenubar_top');
            $('#popup_bottom').removeClass('leftmenubar_bottom');
            $('ul.treeview-menu.popover.right').css("display", "none");
        });
    });
</script>