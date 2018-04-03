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
?>
<div class="col-md-12">
    <div class="satting_top_title" style="padding:0;">
        <form id="account_settings" method="POST" action=""  enctype="multipart/form-data">
            <?php
            if ($member_id == $_SESSION['agents_cust_id']) {
                $parent_id_data = 0;
            } else {
                $parent_id_data = $member_id;
            }
            ?>
            <input type="hidden"  name="parent_id"  placeholder="" class="form-control" id="parent_id" value="<?php echo $parent_id_data ?>">
            <input type="hidden"  name="member_id"  placeholder="" class="form-control" id="member_id" value="<?php echo $_SESSION['agents_cust_id'] ?>">
            <input type="hidden"  name="app_id"  placeholder="" class="form-control" id="app_id" value="<?php echo $_REQUEST['sel_app_id'] ?>">
            <input type="hidden"  name="support_id"  placeholder="" class="form-control" id="support_id" value="<?php echo $_REQUEST['support_id'] ?>">
            <input type="hidden"  name="status"  placeholder="" class="form-control" id="status" value="active">
            <input type="hidden"  name="dtadd"  placeholder="" class="form-control" id="dtadd" value="<?php echo date('Y-m-d H:i:s') ?>">
            <input type="hidden"  name="answer"  placeholder="" class="form-control" id="answer" value="<?php echo $_REQUEST['message'] ?>">
            <div class="col-xs-12" style="padding:0px;">

                <div class="form-group">
                    <label for="question" class="">Title</label>
                    <div class="input-group">
                        <input type="text" value="" name="question"  placeholder="" class="form-control" id="question">
                    </div>
                </div>

                <div class="pull-left">

                    <input id="canned" name="canned" value="Y" type="hidden" >
                    <button name="save_canned"  onclick="change_status_canned('Y');add(this.form.id, 'tblapp_faq');" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all fr">Save as Canned Response</button>

                </div>

                <div class="pull-right">
                    <button name="save_faq"  onclick="change_status_canned('N');add(this.form.id, 'tblapp_faq');"  type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all fr">Save as FAQ</button>

                </div>

                <div class="cl"></div>
            </div>
            <div class="cl height20"></div>
    </div>
</form>
</div>
<div class="cl"></div>
</div>
<div class="cl height1"></div>
</div>

<div style="display:none; opacity:0.5;  height:100%; width:100%; position:fixed; z-index:999; top:0px; left:0px;background-color:#fff;filter: alpha(opacity=50);" id="overlays">
    <div class="preload-bg">
        <div id='ajax_loader' style="position: fixed; left: 50%; top: 50%;">
            <img src="img/ajax-loader-theme.gif"></img>
        </div>
        <div class="clear"></div>
    </div>
</div>

<script>

    function change_status_canned(status) {
        if (status == 'Y') {
            $('#canned').val('Y');
        } else {
            $('#canned').val('N');
        }
    }

    $('#account_settings').validate({
        onkeyup: function (element) {
            $(element).valid()
        },
        rules: {
            question: "required",
            answer: "required",
            is_canned: "required"

        },
        messages: {
            question: "",
            answer: "Please add text first",
            is_canned: "",
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
