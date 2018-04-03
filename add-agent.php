<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

error_reporting(0);
require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
?><div class="col-md-12">
    <div class="satting_top_title" style="padding:0;">
        <form id="account_settings" method="POST" action=""  enctype="multipart/form-data">
            <input type="hidden" name="intid" id="intid" value="<?php echo $_GET['id']; ?>" >
            <input type="hidden" name="script" id="script" value="<?php echo $_GET['script']; ?>" >
            <input type="hidden" name="role" id="role_1" value="technical" >
            <div class="col-xs-12 col-md-3" style="margin:22px 0 0 0;">
                <div id="file_logo" class="mainlogo">
                    <?php if ($logo != '') { ?>
                        <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/plus_icon.png" alt=""> <span>LOGO</span>
                            <input type="file" id="logoagent" name="logo">
                        </span>
                        <div class="upload_img preview_small_img" id="remove_logo"> 
                            <img src="<?php echo $logo_path; ?>" alt="" height="118" width="118" />
                            <div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div>
                        </div>
                    </div>
                    <input type="hidden" id="old_logo" name="old_logo" value="<?php echo $logo; ?>" >
                    <input type="hidden" id="del_old_logo" name="del_old_logo" value="" >
                <?php } else { ?>
                    <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/plus_icon.png" alt=""> <span>LOGO</span>
                        <input type="file" id="logoagent" name="logo">
                    </span>
                <?php } ?>
            </div>
    </div>
    <div class="col-xs-12 col-md-9" style="padding:0;"><?php
        if (!isset($_GET['role'])) {
            $role_txt = 'Role';
            $role_id = 'role_management';
            $role_aid = 'r';
        } else {
            $role_txt = 'User';
            $role_id = 'profile_management';
            $role_aid = 'p';
        }
        ?><div class="fr">
            <div class="fr"></div>
        </div>
        <div id="profile_management" <?php if (isset($_GET['role'])) { ?> style="display:none;" <?php } ?> >
            <div class="col-md-6 profile-l">
                <h3>Personal Info</h3>
                <div class="form-group">
                    <label for="inputPassword3" class="">First Name</label>
                    <div class="input-group">
                        <input type="text" value="" id="fname" name="fname" placeholder="" class="form-control wd" >              
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="none">&nbsp;</h3>
                <div class="form-group">
                    <label for="inputPassword3" class="">Last Name</label>
                    <div class="input-group">
                        <input type="text" value="" id="lname" name="lname" placeholder="" class="form-control wd" >
                    </div>
                </div>
            </div>
        </div>
        <div class="fl col-xs-12 col-md-12 emailid">
            <div class="form-group">
                <label for="inputPassword3" class="">Email Address</label>
                <div class="input-group">
                    <input type="text" value="" name="email" placeholder="" class="form-control wd" id="email">
                </div>
            </div>
        </div>
        <div class="fl col-xs-12 col-md-12 agnetroles">
            <h3>Roles</h3>
            <?php if ($_GET['script'] == 'edit') { ?>
                <div class="form-group">
                    <label for="inputPassword3" class="">Current Password</label>
                    <div class="input-group">
                        <input type="password" value="" name="password" placeholder="" class="form-control wd" id="password" autocomplete="off">
                    </div>
                </div>
            <?php } ?>
            <div class="form-group rol_td" style="display: inline-block;">
                <label>Select Role</label>
                <div class="sliderWrapperFont input-group " style="display:block;">
                    <div id="select_role_slider_1">
                        <?php echo $gnrl->role_select_slide('technical', 1); ?>
                    </div>
                </div>
            </div>
            <div class="cl"></div>
            <div class="fl col-xs-12 col-md-12 agent_save">
                <button name="save_settings" <?php if ($_REQUEST['script'] != 'add') { ?> onclick="update(this.form.id, 'tblmember')" <?php } else { ?> onclick="add(this.form.id, 'tblmember')" <?php } ?> value="Send" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all fr">Save Changes</button>
            </div>
            <div class="cl height20"></div>
        </div>
    </div>
</form>
<div class="cl"></div>
</div>
<div class="cl height1"></div>
</div>
<script>
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

        $('input').iCheck({
            checkboxClass: 'icheckbox_square',
            radioClass: 'iradio_square',
            increaseArea: '50%' // optional
        });
    });

    $("#logoagent").change(function () {
        var iclass = 'mainlogo';
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_img"><div class="center-img"><img src="' + e.target.result + '" height="118" width="118" ></div><div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div></div>');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('.mainlogo').on('click', function () {
        var img = $('#old_logo').val();
        $('#del_old_logo').val(img);
        $('#remove_logo').remove();
        $('#remove_preview_img').remove();
    });

    function select_role(id, value) {
        var split = id.split('_');
        $('#select_role_slider_1').children('div').removeClass('active');
        $('#' + id).addClass('active');
        $('#role_1').val(value);
    }
</script>
<script>
    $(document).ready(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '50%' // optional
        });
    });

    $(window).load(function () {
        var fslider = $('#select_role_slider_1').bxSlider({
            slideWidth: 135,
            minSlides: 5,
            maxSlides: 9,
            moveSlides: 1,
            startSlide: parseInt(0),
            slideMargin: 10,
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false
        });
        $(".sliderWrapperFont").show(0, "swing", function () {
            fslider.reloadSlider();
        });
    });

    $('#account_settings').validate({
        onkeyup: function (element) {
            $(element).valid()
        },
        rules: {
            name: "required",
            email: {
                required: true,
                email: true
            },

        },
        messages: {
            name: "",
            email: "",
        }
    });
</script>
<style type="text/css">
    .slide {
        width: auto !important;
        margin:0 8px 0 0px !important;
        padding: 6px 0;
        cursor: pointer;
        border-bottom: 2px solid #fff;
    }

    .form-group.rol_td .sliderWrapperFont.input-group  .slide{
        width: auto !important;
        margin:0 9px 0 0px !important;
        padding: 6px 0;
        cursor: pointer;
        border-bottom: 2px solid #fff;
    }

    .slide.active, .form-group.rol_td .sliderWrapperFont.input-group  .slide.active {
        border-bottom: 2px solid #00cccc;
    }
</style>
