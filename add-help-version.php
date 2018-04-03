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

<form id="addhelprfrm" action="" method="POST" >

    <input type="hidden" id="app_id" name="app_id" value="<?php echo $_GET['sel_app_id']; ?>" />
    <input type="hidden" id="member_id" name="member_id"  value="<?php echo $_GET['member_id']; ?>" />
    <input type="hidden" id="faq_font_color" name="faq_font_color"  value="<?php echo 'd19147'; ?>" />
    <div class="col-xs-12 col-md-12">

        <div class="box-body row">
            <div class="col-xs-12 col-md-9">
                <div class="form-group">
                    <label for="exampleInputPassword1">Version</label>
                    <input type="text" placeholder="" id="version" name="version"  value="" class="form-control">
                </div>
            </div>

        </div>
    </div>      
    <div class="popap-footer col-xs-12 col-md-12">
        <button id="next1"  type="button" class="btn btn-primary fl button_submit save_all" data-loading-text="Loading...">Save</button>

<?php
$app_tutorial_settings = $dclass->select("intid", "tblapp_tutorial_settings", " AND app_id='" . $_GET['sel_app_id'] . "' AND record_status = 'running' ");
if (count($app_tutorial_settings) > 0) {
    ?>
            <button id="copy_previous" style="margin-left: 5px"  type="button" class="btn btn-primary fl button_submit save_all" data-loading-text="Loading...">Copy from previous version?</button>
            <div class="popap-footer"><p style="margin-left: -10px">Copy the complete content from previous version. You can edit them for current version if required.</p></div>
        <?php } ?>
    </div>
    <div class="cl height10"></div>      

</form>        

<script type="text/javascript">
    $(function () {
        $('#next1').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
            check_helpr_version($("#version").val(), $("#app_id").val());
            var form_data = $('#addhelprfrm').serialize();
            $('#addhelprfrm').valid();
            var go = 1;
            $('#addhelprfrm').find('input,select').each(function () {
                if ($(this).hasClass('error')) {
                    go = 0;
                    return false;
                }
            });

            if (go == 1) {
                var btn = $(this);
                request = $.ajax({
                    type: "POST",
                    url: "help-ajax.php",
                    data: "ajax=1&action=add_help_version&" + form_data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        btn.button('loading');
                    },
                    success: function (data) {
                        btn.button('reset');

                        if (data['output'] == 'S') {

                            //dlg.toggle();
                            location.href = '<?php echo 'help-img-video?sel_app_id=' . $_GET['sel_app_id']; ?>&sel=faq&live';
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }

                    }
                });
            } else {
            }
        });

        $('#copy_previous').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
            check_helpr_version($("#version").val(), $("#app_id").val());
            var form_data = $('#addhelprfrm').serialize();
            $('#addhelprfrm').valid();
            var go = 1;
            $('#addhelprfrm').find('input,select').each(function () {
                if ($(this).hasClass('error')) {
                    go = 0;
                    return false;

                }
            });

            if (go == 1) {
                var btn = $(this);
                request = $.ajax({
                    type: "POST",
                    url: "help-ajax.php",
                    data: "ajax=1&action=add_help_version_copy&" + form_data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        btn.button('loading');
                    },
                    success: function (data) {
                        btn.button('reset');

                        if (data['output'] == 'S') {

                            //dlg.toggle();
                            location.href = '<?php echo 'help-img-video?sel_app_id=' . $_GET['sel_app_id']; ?>&sel=faq&live';
                        } else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }

                    }
                });
            } else {
            }
        });
    });

    $(document).ready(function () {
        $('#addhelprfrm').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                version: "required"
            },
            messages: {
                version: ""
            }
        });

    });

    function check_helpr_version(version, app_id) {

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "ajax=1&action=check_help_version&version=" + version + "&app_id=" + app_id,
            dataType: 'json',
            async: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (data['output'] == 'S') {
                } else if (data['output'] == 'F') {
                    $("#version").val('');
                    message(data['msg'], 'error');
                }
            }
        });
    }
</script>      
