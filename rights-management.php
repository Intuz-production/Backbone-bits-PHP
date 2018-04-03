<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php
/*
 * This page for displaying the role wise rights
 */
require("config/configuration.php");
if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];
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
    case 'marketing':
        $access = 'profile, dashboard, apps, add-apps, help,  supportr, documentation';
}
//Show for technical by default
$access = 'profile, dashboard, apps, add-apps, help,  supportr, documentation';

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
    <br>
    <section id="account-settings">
        <div class="col-md-12">
            <div class="satting_top_title">
                <form id="account_settings" method="POST" action=""  enctype="multipart/form-data">
                    <input type="hidden" name="intid" id="intid" value="<?php echo $_GET['id']; ?>" >
                    <input type="hidden" name="script" id="script" value="<?php echo $_GET['script']; ?>" >
                    <!-- left -->
                    <div class="col-md-12 padding0" id="role_management"   >
                        <div class="height2"></div>
                        <div class="cl"></div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <div role="grid" class="dataTables_wrapper form-inline padding0" id="promote-table_wrapper" >
                                <table class="table table-bordered table-hover dataTable agents-table" id="role_table" >
                                    <thead>
                                        <tr role="row padding0">
                                            <th class="sorting">Features</th>
                                            <th class="sorting">
                                                &nbsp;Technical</th>
                                            <th class="sorting">
                                                &nbsp;Finance</th>
                                            <th class="sorting">
                                                &nbsp;Support</th>
                                            <th class="sorting">
                                                &nbsp;Marketing</th>
                                            <th class="sorting">
                                                &nbsp;Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                                        <?php
                                        $sqlr = "select * from tblrole where status='active'";
                                        $resr = $dclass->query($sqlr);

                                        while ($row = $dclass->fetchArray($resr)) {
                                            $num++;
                                            $sr = $num + $limitstart;

                                            if ($num % 2 == 0)
                                                $class = 'event';
                                            else
                                                $class = 'odd';

                                            ?>
                                            <tr class="<?php echo $class; ?>" id="row_<?php echo $row['intid']; ?>" >
                                                <td class=" sorting_1"><?php echo $row['feature']; ?></td>

                                                <td class="status"><?php
                                                    if ($row['technical'] == '1') {
                                                        echo '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
                                                    } else {
                                                        echo '<span class="inactive_icon" title="Inactive"></span>';
                                                    }
                                                    ?></td>
                                                <td class="status"><?php
                                                    if ($row['finance'] == '1') {
                                                        echo '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
                                                    } else {
                                                        echo '<span class="inactive_icon" title="Inactive"></span>';
                                                    }
                                                    ?></td>
                                                <td class="status"><?php
                                                    if ($row['support'] == '1') {
                                                        echo '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
                                                    } else {
                                                        echo '<span class="inactive_icon" title="Inactive"></span>';
                                                    }
                                                    ?></td>
                                                <td class="status"><?php
                                                    if ($row['marketing'] == '1') {
                                                        echo '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
                                                    } else {
                                                        echo '<span class="inactive_icon" title="Inactive"></span>';
                                                    }
                                                    ?></td>
                                                <td class="status"><?php
                    if ($row['admin'] == '1') {
                        echo '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
                    } else {
                        echo '<span class="inactive_icon" title="Inactive"></span>';
                    }
                    ?></td>
                                            </tr>
    <?php } ?>
                                    </tbody>
                                </table>
                                <div class="cl"></div>
                            </div>
                        </div>
                        <!-- /.box-body --> 
                    </div>

                </form>
                <div class="cl"></div>
            </div>
            <div class="cl height1"></div>
        </div>
    </section>
    <div class="cl height3"></div>
    <!-- /.content -->
</aside>
<!-- /.right-side --> 
</div>
<!-- ./wrapper -->
<?php include(INC . "footer.php"); ?>
<script src="js/setting-hover.js"></script>
<script type="text/javascript" src="js/process.js"></script>
<script type="text/javascript">

    //function to delete more app
    function delete_agent(id) {
        $.prompt("", {
            title: "Are you sure you want to delete this user?",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function (e, v, m, f) {
                if (v == false) {
                    //e.preventDefault(); 
                } else {
                    var data = "ajax=1&action=delete_user&limit=<?php echo $limit; ?>&intid=" + id;
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
                                location.href = '<?php echo $url . $get; ?>';
                            }
                            else if (data['output'] == 'F') {
                                message(data['msg'], 'error');
                            }
                        }
                    });

                }
            }
        });
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
        //alert(id);
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
                access = 'Profile, Dashboard, Apps, Add Apps, Help, Supportr, Documentation';
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

</script>

<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script> 
<script>
search('', 'tblmember', ['fname', 'lname', 'email', 'company'],<?php echo $limitstart ?>,<?php echo $limit ?>);
</script>
</body></html>