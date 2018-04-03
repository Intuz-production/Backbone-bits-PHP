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

<form id="addfaqfrm" action="" method="POST" >

    <input type="hidden" id="app_id" name="app_id" value="<?php echo $_GET['sel_app_id']; ?>" />
    <input type="hidden" id="member_id" name="member_id"  value="<?php echo $_GET['member_id']; ?>" />
    <input type="hidden" id="ver_id" name="ver_id"  value="<?php echo $_GET['ver_id']; ?>" />
    <div class="col-xs-12 col-md-12">
        <form role="form">
            <div class="box-body row">
                <div class="col-xs-12 col-md-9">
                    <div class="form-group">
                        <label for="exampleInputPassword1">Question</label>
                        <input type="text" placeholder="Enter Question Here" id="question" name="question"  value="" class="form-control">
                    </div>
                </div>
            </div>
            <div class="box-body pad textediter">
                <form>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Answer</label>
                        <textarea id="answer" name="answer" class="textarea" placeholder="Enter Answer Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                    </div>
                </form>
            </div>
        </form>
    </div>

    <div class="popap-footer col-xs-12 col-md-12">
        <button id="next1" type="button" class="btn btn-primary fl button_submit save_all" data-loading-text="Loading...">Save</button>
    </div>
    <div class="cl height10"></div>
</form>        
<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" defer="defer" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->
<script type="text/javascript">
    $(function () {

        $('#answer').wysihtml5({
            "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
            "emphasis": true, //Italics, bold, etc. Default true
            "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": false, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false //Button to change color of font  
        });


        $('#next1').on('click', function (e) {
            $('#noty_topCenter_layout_container').remove();
            var status = $(this).val();
            var intid = $('#intid').val();
            var form_data = $('#addfaqfrm').serialize();
            $('#addfaqfrm').valid();
            var go = 1;
            $('#addfaqfrm').find('input,select').each(function () {
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
                    data: "ajax=1&action=update_help_faq&script=add&" + form_data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        btn.button('loading');
                    },
                    success: function (data) {
                        btn.button('reset');

                        if (data['output'] == 'S') {
                            dlg1.toggle();
                            message(data['msg'], 'success');
                            search('', 'tblapp_faq', ['app_name'],<?php echo 0 ?>,<?php echo 5 ?>, '', 'help');
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
        $('#addfaqfrm').validate({
            onkeyup: function (element) {
                $(element).valid()
            },
            rules: {
                question: "required",
                answer: "required"
            },
            messages: {
                question: "",
                answer: ""
            }
        });

    });
</script>
</script>      
