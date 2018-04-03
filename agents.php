<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
/*
 * Page for list all agents
 */
require("config/configuration.php");
if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

$member_id = $_SESSION['custid'];
if (isset($_GET['script']) && $_GET['id'] != '') {
    extract($_GET);
    $res = $dclass->select("*", "tblmember", " AND intid='" . $id . "' ");
    $username = $res[0]['username'];
    $name = $res[0]['fname'] . " " . $res[0]['lname'];
    $email = $res[0]['email'];
    $password = $res[0]['password'];
    $company = $res[0]['company'];
    $logo = $res[0]['logo'];
    $status = $res[0]['status'];
    $role = $res[0]['role'];

    if ($logo != '' && is_file(USER_LOGO . "/" . $logo)) {
        $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=" . USER_LOGO . "/" . $logo;
    } else {
        $logo_path = "img/s_user_img.png";
    }
    $company_logo = $res[0]['company_logo'];
    if ($company_logo != '')
        $company_logo_path = "memimages.php?max_width=75&max_width=75&imgfile=" . COMPANY_LOGO . "/" . $company_logo;

    $status = $res[0]['status'];

    switch ($role) {
        case "technical":
            $access = 'profile, dashboard, apps, add-apps, help,  supportr, documentation';
        case 'finance':
            $access = 'profile, dashboard, finance-report, documentation';
        case 'support':
            $access = 'profile, dashboard, supportr, documentation';
        case 'marketing':
            $access = 'profile, dashboard, documentation';
    }
} else {

    //Show for technical by default
    $access = 'profile, dashboard, apps, add-apps, help,  supportr, documentation';
}
$ge = $_POST;
if (is_array($ge) && !empty($ge)) {
    $str = http_build_query($ge, '', '&');
} else
    $str = '';
extract($_POST);

include(INC . "header.php");
include INC . "left_sidebar.php";

$url_add = $url;
$url_add .= "?script=" . $_GET['script'];

if ($_GET['script'] == 'edit' && $_GET['id'] != '') {
    $url_add .= "&id=" . $_GET['id'];
}
?>

<!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <?php if ($_GET['script'] != '') { ?>
        <section id="account-settings">
            <div class="col-md-12">
                <div class="satting_top_title">
                    <form id="account_settings" method="POST" action=""  enctype="multipart/form-data">
                        <input type="hidden" name="intid" id="intid" value="<?php echo $_GET['id']; ?>" >
                        <input type="hidden" name="script" id="script" value="<?php echo $_GET['script']; ?>" >
                        <div id="file_logo" class="mainlogo">
                            <?php if ($logo != '') { ?>
                                <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/plus_icon.png" alt=""> <span>LOGO</span>
                        <!--        <input type="file" id="logo" name="logo">-->
                                </span>
                                <div class="upload_img" id="remove_logo"> <img src="<?php echo $logo_path; ?>" alt="" height="118" width="118" />
                                    <div class="over_img"><a href="javascript:;"><i class="fa fa-fw fa-trash-o"></i></a></div>
                                </div>
                                <input type="hidden" id="old_logo" name="old_logo" value="<?php echo $logo; ?>" >
                                <input type="hidden" id="del_old_logo" name="del_old_logo" value="" >
                            <?php } else { ?>
                                <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/plus_icon.png" alt=""> <span>LOGO</span>
                        <!--        <input type="file" id="logo" name="logo">-->
                                </span>
                            <?php } ?>
                        </div>
                        <div class="add_apps_bottom" style="padding:0 !important;">
                            <div class="fr promote_platform_top">
                                <div class="fr">
                                    <button name="save_settings" <?php if ($_REQUEST['script'] != 'add') { ?> onclick="update(this.form.id, 'tblmember')" <?php } else { ?> onclick="add(this.form.id, 'tblmember')" <?php } ?> value="Send" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all ">Save Changes</button>
                                </div>
                            </div>
                        </div>
                        <div class="fl user_email_id">
                            <h2><?php echo ucwords($name); ?></h2>
                            <p><?php echo $email; ?></p>
                        </div>
                        <div class="cl height2"></div>
                        <?php
                        if (!isset($_GET['role'])) {
                            $role_txt = 'Role';
                            $role_id = 'role_management';
                            $role_aid = 'r';
                        } else {
                            $role_txt = 'User';
                            $role_id = 'profile_management';
                            $role_aid = 'p';
                        }
                        ?>
                        <div class="fr">
                            <div class="fr"> 
                            </div>
                        </div>
                        <!-- left -->
                        <div id="profile_management" <?php if (isset($_GET['role'])) { ?> style="display:none;" <?php } ?> >
                            <div class="col-md-6 profile-l">
                                <h3>Personal Info</h3>
                                <div class="cl height1"></div>
                                <div class="col-md-8 padding0">
                                    <label for="inputPassword3" class="col-sm-12 control-label padding0">Name</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="text" value="<?php echo $name; ?>" id="name" name="name" placeholder="" class="form-control wd" >
                                    </div>
                                </div>
                                <div class="cl height1"></div>
                                <div class="col-md-8 padding0">
                                    <label for="inputPassword3" class="col-sm-12 control-label padding0">User Name</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="text" value="<?php echo $username; ?>" id="username" name="username" placeholder="" class="form-control wd" >
                                    </div>
                                </div>
                                <div class="cl height1"></div>
                                <div class="col-md-8 padding0">
                                    <label for="inputPassword3" class="col-sm-12 control-label padding0">Email Address</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="text" value="<?php echo $email; ?>" name="email" placeholder="" class="form-control wd" id="email">
                                    </div>
                                </div>
                                <div class="cl height1"></div>
                            </div>
                            <div class="col-md-6">
                                <h3>Password</h3>
    <?php if ($_GET['script'] == 'edit') { ?>
                                    <div class="cl height1"></div>
                                    <div class="col-md-8 padding0">
                                        <label for="inputPassword3" class="col-sm-12 control-label padding0">Current Password</label>
                                        <div class="col-sm-12 padding0">
                                            <input type="password" value="" name="password" placeholder="" class="form-control wd" id="password" autocomplete="off">
                                        </div>
                                    </div>
    <?php } ?>
                                <div class="cl height1"></div>
                                <div class="col-md-8 padding0">
                                    <label for="inputPassword3" class="col-sm-12 control-label padding0">New Password</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="password" value="" name="new_password"  placeholder="" class="form-control wd" id="new_password" autocomplete="off">
                                    </div>
                                </div>
                                <div class="cl height1"></div>
                                <div class="col-md-8 padding0">
                                    <label for="inputPassword3" class="col-sm-12 control-label padding0">Password Confirmation</label>
                                    <div class="col-sm-12 padding0">
                                        <input type="password" value="" name="conf_new_password"  placeholder="" class="form-control wd" id="conf_new_password" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="inputPassword" style="padding:0 10px 0 0; color: #999;" class="control-label fl">
                                        <?php
                                        if ($status == 'active' || $_GET['script'] != 'edit') {
                                            echo 'Active';
                                        } else {
                                            echo 'Inactive';
                                        }
                                        ?>
                                    </label>
                                    <div class="cl"></div>
                                    <div class="fl">
                                        <label class="i-switchs  i-switch-mds i-switch-mds-horizontal">
                                            <i></i> </label>
                                    </div>
                                </div>

                            </div>
                            <br>

                            <div class="col-md-12">
                                <label>Select Role  <br><a target="_blank" href="rights-management?script=add">Check Role Details</a></label>
                                <div role="grid" class="dataTables_wrapper form-inline padding0" id="promote-table_wrapper" >

                                    <input type="radio" id="role1" name="role" value="technical" <?php
                                           if ($role == 'technical' || $_GET['script'] == 'add') {
                                               echo 'checked';
                                           }
                                           ?> >
                                    &nbsp;Technical
                                    <input type="radio" id="role2" name="role" value="finance" <?php
                                           if ($role == 'finance') {
                                               echo 'checked';
                                           }
                                           ?> >
                                    &nbsp;Finance
                                    <input type="radio" id="role3" name="role" value="support" <?php
                                           if ($role == 'support') {
                                               echo 'checked';
                                           }
                                           ?> >
                                    &nbsp;Support
                                    <input type="radio" id="role4" name="role" value="marketing" <?php
                                           if ($role == 'marketing') {
                                               echo 'checked';
                                           }
                                           ?> >
                                    &nbsp;Marketing

                                    <div class="cl"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="cl"></div>
                </div>
                <div class="cl height1"></div>
            </div>
        </section>
        <div class="cl height3"></div>
        <?php
    } else {

        //SET LIMIT FOR PAGING
        if (isset($_REQUEST['pageno'])) {
            $limit = $_REQUEST['pageno'];
        } else {
            $limit = $gnrl->getMember_Settings('sRecordperpage', $member_id);
        }
        $limit = 10;
        $form = 'frm';
        if (isset($_REQUEST['limitstart'])) {
            $limitstart = $_REQUEST['limitstart'];
        } else {
            $limitstart = 0;
        }
        if (isset($_REQUEST['button4']) || $_REQUEST['button4'] == "Search") {
            $limit = $gnrl->getMember_Settings('sRecordperpage', $member_id);
            $limitstart = 0;
        }
        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
            $keyword = mysql_real_escape_string(trim($_REQUEST['keyword']));
            $wh .= " AND (video_name like '%$keyword%')";
        }


        $ssql = "SELECT * FROM tblmember where 1 AND parent_id='" . $_SESSION['custid'] . "' $wh";

        $sortby = ($_REQUEST['sb'] == '') ? 'intid' : $_REQUEST['sb'];
        $sorttype = ($_REQUEST['st'] == '0') ? 'ASC' : 'DESC';

        $nototal = $dclass->numRows($ssql);
        $sqltepm = $ssql . " ORDER BY $sortby $sorttype LIMIT $limitstart,$limit";
        #DEBUG
        $restepm = $dclass->query($sqltepm);
        ?>
        <div class="right_sidebar">
            <div class="top-bax">
                <div class="col-md-3 fr">
                </div>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="box"> 
                    <!-- /.box-header -->
                    <div class="box-body table-responsive" id="promote-table_wrapper">
                        <table id="example1" class="table table-bordered table-striped classtablenew agents-tables">
                            <thead>
                                <tr>
                                    <th class="invoice__">&nbsp;</th>
                                    <th class="name-role">Full Name</th>
                                    <th class="username_box">User Name</th>
                                    <th class="email_box">Email</th>
                                    <th class="status_box">Status</th>
                                    <th class="edit_box">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="showdata">

                            </tbody>

                        </table>
                        <div class='navigation' id="navview">
                        </div>
                    </div>
                </div>
                <!-- /.box-body --> 
            </div>
        </div>
        <div id="somedialog" class="dialog">
            <div class="dialog__overlay"></div>
            <div class="dialog__content">
                <div class="popap-header">
                    <h3 class="fl">Add Agent</h3>
                    <button class="action fr" data-dialog-close>&nbsp;</button>
                </div>
                <div class="popap-content" id="add-app-page"></div>
            </div>
        </div>
<?php } ?>
    <!-- /.content -->
</aside>
<!-- /.right-side --> 
</div>
<!-- ./wrapper -->
<?php include(INC . "footer.php"); ?>
<script src="js/setting-hover.js"></script>
<script type="text/javascript" src="js/process.js"></script>

<style type="text/css">

    .slide.active {
        border-bottom: 1px solid #00cccc;
    }
    .bx-viewport{ background:none !important;}

</style>

<script type="text/javascript">
    function select_role(id, value) {
        //alert(id);
        var split = id.split('_');
        //alert(split[2]);
        $('#select_role_slider_' + split[2]).children('div').removeClass('active');
        $('#' + id).addClass('active');
        $('#role_' + split[2]).val(value);
    }

    $(document).ready(function () {
        if (Modernizr.touch) {
            // show the close overlay button
            $(".close-overlay").removeClass("hidden");
            // handle the adding of hover class when clicked
            $(".img").click(function (e) {
                if (!$(this).hasClass("hover")) {
                    $(this).addClass("hover");
                }
            });
            // handle the closing of the overlay
            $(".close-overlay").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                if ($(this).closest(".img").hasClass("hover")) {
                    $(this).closest(".img").removeClass("hover");
                }
            });
        } else {
            // handle the mouseenter functionality
            $(".img").mouseenter(function () {
                $(this).addClass("hover");
            })
                    // handle the mouseleave functionality
                    .mouseleave(function () {
                        $(this).removeClass("hover");
                    });
        }


    });
</script> 
<script type="text/javascript">

    function change_tab(id) {
        if (id == 'r') {
            $('.change_tab').attr('id', 'p');
            $('.change_tab').html('Agent Management');
            $('#profile_management').hide();
            $('#role_management').show();
        }

        if (id == 'p') {
            $('.change_tab').attr('id', 'r');
            $('.change_tab').html('Role Management');
            $('#role_management').hide();
            $('#profile_management').show();
        }
    }

    $(function () {
        $("#role").change(function () {
            var val = $(this).val();

            var access = '';
            if (val == 'technical') {
                access = 'Profile, Dashboard, Apps, Add Apps, Help,  Supportr, Documentation';
            } else if (val == 'finance') {
                access = 'Profile, Dashboard, Finance-report, Documentation';
            } else if (val == 'support') {
                access = 'Profile, Dashboard, Supportr, Documentation';
            } else if (val == 'marketing') {
                access = 'Profile, Dashboard, Documentation';
            }

            $("#access_list").html('Access to ' + access);

        });

<?php if ($_GET['script'] == 'add' || ($_GET['script'] == 'edit' && $status == 'active')) { ?>
            $("#status").iCheck('check');
<?php } else { ?>
            $("#status").iCheck('uncheck');
<?php } ?>

        $("#logo").change(function () {
            var iclass = 'mainlogo';
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_img"><div class="center-img"><img src="' + e.target.result + '" height="118" width="118" ></div><div class="over_img"><a href="javascript:;"><i class="fa fa-fw fa-trash-o"></i></a></div></div>');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        $("#company_logo").change(function () {
            var iclass = 'comp';
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_company_img"><div class="center-img"><img src="' + e.target.result + '" height="118" width="118" ></div><div class="over_img"><a href="javascript:;"><i class="fa fa-fw fa-trash-o"></i></a></div></div>');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        $('.comp').on('click', function () {
            var img = $('#old_company_logo').val();
            $('#del_old_company_logo').val(img);
            $('#remove_comp_logo').remove();
            $('#remove_preview_company_img').remove();

        });

        $('.mainlogo').on('click', function () {
            var img = $('#old_logo').val();
            $('#del_old_logo').val(img);
            $('#remove_logo').remove();
            $('#remove_preview_img').remove();

        });

        $('#account_settings').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                name: "required",
                username: "required",
                email: {
                    required: true,
                    email: true
                },
                new_password: {
                    minlength: 5
                },
                conf_new_password: {
                    minlength: 5,
                    equalTo: "#new_password"
                }

            },
            messages: {
                name: "",
                email: "",
                username: ""
            }
        });

        $('#quick_support').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                subject: "required",
                content: {
                    required: true
                }

            },
            messages: {
                subject: "",
                content: ""
            }
        });
        var ajaxRunning = false;

        $("body").ajaxStart(function ()
        {

            ajaxRunning = true;
        }).ajaxStop(function ()
        {
            ajaxRunning = false;
        });
        document.onscroll = function () {
            $.ias({
                container: '.classtablenew',
                scrollContainer: $('.classtablenew'),
                item: '.item',
                pagination: '#promote-table_wrapper .navigation',
                next: '.next-posts a',
                loader: '<div class="view_more_loader"><img src="img/ajax-loader.gif"/></div>',
                triggerPageThreshold: 0,
                trigger: 'View More',
                history: false,
                onRenderComplete: function () {
                    remove_loader();

                },
                beforePageChange: function (curScrOffset, urlnext) {

                    spliturl_paging(urlnext, ['fname', 'lname', 'email', 'company'], 'bef');
                    remove_loader();

                },
                onPageChange: function (pageNum, pageUrl, scrollOffset) {

                    spliturl_paging(pageUrl, ['fname', 'lname', 'email', 'company'], 'onp');

                    remove_loader();

                }

            });
        }
    });
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

    function editrow(id) {

        $('#display_' + id).show();

        $('#' + id).hide();

    }
    function hideeditrow(id) {
        $('#display_' + id).hide();
        $('#' + id).show();
    }
    function getidimage(id) {
        $.prompt("", {
            title: "Are you sure you want to delete this agent logo?",
            buttons: {"Yes": true, "Cancel": false},
            submit: function (e, v, m, f) {
                if (v == false) {
                } else {
                    var type = 'user';
                    var data = "ajax=1&action=delete_image&intid=" + id + "&type=" + type;
                    $("#overlays").show();
                    request = $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        beforeSend: function () {

                        },
                        success: function (data) {
                            $("#overlays").hide();
                            var img = $('#old_logo_' + id).val();
                            $('#del_old_logo_' + id).val(img);
                            $('#remove_logo_' + id).remove();
                            $('#remove_preview_img_' + id).remove();
                            $('.mainlogo_' + id).attr("onclick", "getidimage_preview(" + id + ")");
                        }

                    });
                }
            }

        });

    }
    function getidimage_preview(id) {
        var img = $('#old_logo_' + id).val();
        $('#del_old_logo_' + id).val(img);
        $('#remove_logo_' + id).remove();
        $('#remove_preview_img_' + id).remove();
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".plus_icon a.trigger").click(function () {
            $.ajax({url: "add-agent?script=add", success: function (result) {
                    $("#add-app-page").html(result);
                }});
        });
    });

</script>
<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script> 
<script>      search('', 'tblmember', ['fname', 'lname', 'email', 'company'],<?php echo $limitstart ?>,<?php echo $limit ?>);
</script>
</body></html>