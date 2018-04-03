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
$feature_id = 4; //help
$member_id = $_SESSION['custid'];

extract($_GET);
$resda = $dclass->select("app_type", "tblmember_apps", " AND intid='" . $sel_app_id . "' ");

$_SESSION['app_type'] = $resda[0]['app_type'];
include INC . "header.php";

include INC . "left_sidebar.php";

if (isset($_GET['live']))
    $status = 'running';
else if (isset($_GET['prev'])) {
    $status = 'prev';
} else if (isset($_GET['old'])) {
    $status = 'old';
    $video_id_data = 'AND s.intid=' . $_GET['video_id'];
}

$app_name = $dclass->select("app_name", "tblmember_apps", " AND intid = '" . $sel_app_id . "'");

$res = $dclass->select("s.*,v.intid as video_id,v.video,v.video_name,v.video_type,v.live_date", "tblapp_tutorial_settings s LEFT JOIN tblapp_tutorial_videos v ON s.intid=v.ver_id ", " $video_id_data AND s.app_id='" . $sel_app_id . "' AND  s.record_status='" . $status . "' ORDER BY s.record_status DESC LIMIT 1");

if (count($res) > 0) {
    $intid = $res[0]['intid'];
    $animation_id = $res[0]['image_animation'];
    if ($res[0]['video_id'] != '') {
        $video_id = $res[0]['video_id'];
        $video_name = $res[0]['video_name'];
        $video = $res[0]['video'];
        $video_type = $res[0]['video_type'];

        if ($video_type == 'youtube')
            $youtube_video = $video;
        else if ($video_type == 'vimeo')
            $vimeo_video = $video;


        if ($video != '') {
            $v = explode(".", $value['video']);
            $ty = $v[1];
            if ($ty == 'mov') {
                $videoty = 'quicktime';
            }
            else
                $videoty = 'mp4';

            $video_path = SITE_URL . TUT_VIDEO . "/" . $video;
        }
        $live_date = date('m/d/Y', strtotime($res[0]['live_date']));
    }

    //GET IMAGES
    $resi = $dclass->select("intid as image_id, title,image", "tblapp_tutorial_images", " AND ver_id='" . $res[0]['intid'] . "' ORDER BY intorder ASC ");
}
?>
<!-- ============================================================================== -->
<!-- Right Side Bar -->
<!-- ============================================================================== -->
<link href="css/video-js.css" rel="stylesheet" type="text/css" >
<form id="editimgvideofrm" action="" method="POST"> 
    <aside class="right-side">
        <div class="right_sidebar">

            <input type="hidden" id="ver_id" name='ver_id' value="<?php echo $intid; ?>" />
            <input type="hidden" id="video_id" name='video_id' value="<?php echo $video_id; ?>" />
            <input type="hidden" id="app_id" name="app_id" value="<?php echo $sel_app_id; ?>" />
            <input type="hidden" id="action" name="action" value="save_tutorial" />
            <input type="hidden" id="ajax" name="ajax" value="1" />
            <input type="hidden" id="youtube_video" name="youtube_video" value="<?php echo $youtube_video; ?>" />
            <input type="hidden" id="vimeo_video" name="vimeo_video" value="<?php echo $vimeo_video; ?>"  />

            <input type="hidden" name="helpr_img_count" id="helpr_img_count" value="0"> 
            <?php
            if ($_REQUEST['sel'] == 'img') {
                $selected_img = 'class = "active"';
                $selected_img_act = 'active';
            } else if ($_REQUEST['sel'] == 'faq') {
                $selected_faq = 'class = "active"';
                $selected_faq_act = 'active';
            }
            else {
                $selected_vid = 'class = "active"';
                $selected_vid_act = 'active';
            }
            ?>
            <div class="add-apps">
                <div class="col-xs-12 col-md-12"> 
                    <ul class="nav nav-tabs helpr-img-v">
                        <li  <?php echo $selected_faq ?>><a id="faq_tab"  href="#Faq"  data-toggle="tab">FAQ</a></li>     
                        
                        <li id="video_tab"  <?php echo $selected_vid ?>><a href="#Video1"  data-toggle="tab">Video</a></li>
                        <li id="images_tab"  <?php echo $selected_img ?>><a href="#Images"  data-toggle="tab">Images</a></li>
                        <?php if (isset($_GET['live'])) { ?>  
                            <li class="fr plus_icon1 fl" style="margin-right:10px!important;" id="save_publish">																												
							<button type="button" class="btn btn-primary fr button_submit save_all">Save &amp; Publish</button>
							<a style="display: none;" id="save_faq" data-dialog1="somedialog1" class="trigger btn btn-primary fr button_submit" href="javascript:;">Add FAQ</a>
							
							</li>
                        <?php } ?>  
                    </ul>

                    <div class="cl height10"></div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="box">
                    <div class="helpr-img-video">
                        <div class="row">
                            <div class="col-md-12"> 
                                <!-- Custom Tabs -->
                                <div class="nav-tabs-custom">
                                    <div class="tab-content">
                                        <div class="tab-pane images_teb <?php echo $selected_faq_act ?>" id="Faq">
                                            <?php
//SET LIMIT FOR PAGING
                                            if (isset($_REQUEST['pageno'])) {
                                                $limit = $_REQUEST['pageno'];
                                            } else {
                                                $limit = 10;
                                            }
                                            $form = 'frm';

                                            if (isset($_REQUEST['limitstart'])) {
                                                $limitstart = $_REQUEST['limitstart'];
                                            } else {
                                                $limitstart = 0;
                                            }
                                            ?>

                                            <div class="col-xs-12 col-md-12">
                                                <input type="hidden" name="sel_app_id" id="sel_app_id_ajax" value="<?php echo $sel_app_id ?>" />
                                                <div class="box">
                                                    <div class="faq_list">
                                                        <table id="example1" class="table table-bordered classtablenew table-faq">
                                                            <tbody id="showdata">

                                                            </tbody>
                                                            <div class='navigation' id="navview">

                                                            </div>
                                                        </table>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane images_teb <?php echo $selected_img_act ?>" id="Images"> 

                                            <div class="small_font">Add one/more Help images here. You can use drag and drop to change sequence of saved images</div>
                                            <div class="animations_slider_aliment"> 
                                                <span>Animations</span>
                                                <div class="cl"></div>
                                                <input type="hidden" name="slide_anum" id="slide_anum" value="<?php echo $slide_anum; ?>" >
                                                <input type="hidden" name="animation_id" id="animation_id" value="<?php echo $animation_id; ?>" >
                                                <div class="sliderWrapperFont" style="display:none;">
                                                    <div id="select_animation_slider"> <?php echo $gnrl->tutorial_animation_slide($animation_id); ?> </div>
                                                </div>
                                                <div class="cl height3"></div>
                                            </div>
                                            <ul class="add-new-img">

                                                <?php
                                                if (count($resi) > 0) {
                                                    for ($j = 0; $j < count($resi); $j++) {
                                                        $image_id = $resi[$j]['image_id'];
                                                        $image = $resi[$j]['image'];
                                                        $image_title = $resi[$j]['title'];
                                                        $image_path = TUT_IMG . "/" . $resi[$j]['image'];
                                                        ?>      

                                                        <li id="select_app_logo_<?php echo $image_id; ?>">
                                                            <div class="center-img">
                                                                <img title="" alt="" src="<?php echo $image_path; ?>">
                                                                <div class="over_img over_img1"><a href="javascript:;" <?php if (isset($_GET['live'])) { ?> onClick="remove_logo(<?php echo $image_id; ?>);
                                                                                                   <?php } ?>"><?php if (isset($_GET['live'])) { ?><i class="apps_i trash_i" style="font-size:24px"></i><?php } ?></a></div>
                                                            </div>
                                                            <div class="img_title_name">
                                                                <input id="title_<?php echo $image_id; ?>" name="title_<?php echo $image_id; ?>" type="text" placeholder="Image Title" value="<?php echo $image_title; ?>" class="form-control valid">
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                                <?php if (isset($_GET['live'])) { ?>
                                                    <div class="add_new_helpr_img" id="sfile_logo_0"> 
                                                        <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt="" title=""> <span>Upload Tutorial Image</span>
                                                            <input type="file" name="new_img[]" id="new_img_0" class="form-control tutorial-input" placeholder="Logo-file">
                                                        </span>
                                                        <div class="img_title_name">
                                                            <input type="text" id="new_title_0" name="new_title[]" placeholder="Image Title" value="" class="form-control valid" >
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </ul>

                                        </div>
                                        <!-- /.tab-pane -->

                                        <div class="tab-pane video_teb <?php echo $selected_vid_act ?>" id="Video1"> 

                                            <div class="row">

                                                <div class="upload_video">
                                                    <div class="col-xs-12 col-md-2"></div>
                                                    <div class="col-xs-12 col-md-8">
                                                        <?php if ($video) { ?>
                                                            <?php if (isset($_GET['live'])) { ?>
                                                                <div class="fr">


                                                                    <div class="col-xs-12 col-md-4">
                                                                        <div class="hfile_logo" id="hfile_logo">
                                                                            <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/ger_up_img.png" alt="" title="">
                                                                                <input type="file" id="video" name="video" class="form-control tutorial-input" placeholder="Logo-file">
                                                                            </span>
                                                                        </div>
                                                                    </div>       
                                                                    <div class="col-xs-12 col-md-4"><a data-dialogy="somedialogy" href="javascript:;" <?php if ($video_type == 'youtube') { ?> class="active" <?php } ?>><img src="img/youtube_up_img.png " alt="" title=""></a></div>
                                                                    <div class="col-xs-12 col-md-4"><a data-dialogv="somedialogv"  href="javascript:;" <?php if ($video_type == 'vimeo') { ?> class="active" <?php } ?>><img src="img/vimeo_up_img.png " alt="" title=""></a></div>

                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>

                                                        <div class="video_up" id="video_show" >



                                                            <?php
                                                            if ($video) {
                                                                if ($video_type == 'file') {
                                                                    ?>

                                                                    <video class="video-js vjs-default-skin"  poster="img/video-screen.jpg"  width="840" height="500" id="home_video_html5_api" data-setup='{"controls": true, "autoplay": false, "preload": "auto" }' >
                                                                        <source src="<?php echo $video_path; ?>" type="<?php echo "video/" . $videoty; ?>" />
                                                                    </video>
                                                                <?php } else { ?>
                                                                    <video class="video-js vjs-default-skin"  poster="img/video-screen.jpg"  width="840" height="500" id="home_video_html5_api" data-setup='{ "techOrder": ["<?php echo $video_type; ?>"], "src": "<?php echo $video; ?>" }' >

                                                                    </video>

                                                                <?php } ?>
                                                            <?php } else { ?>    
                                                                <p>Upload Onboarding Video</p>
                                                                <div class="cl"></div>
                                                                <div class="upload_img">
                                                                    <div class="col-xs-12 col-md-4">
                                                                        <div class="hfile_logo" id="hfile_logo">
                                                                            <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/ger_up_img.png" alt="" title="">
                                                                                <input type="file" id="video" name="video" class="form-control tutorial-input" placeholder="Logo-file">
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-md-4"><a data-dialogy="somedialogy"  href="javascript:;"><img src="img/youtube_up_img.png " alt="" title=""></a></div>
                                                                    <div class="col-xs-12 col-md-4"><a data-dialogv="somedialogv"  href="javascript:;"><img src="img/vimeo_up_img.png " alt="" title=""></a></div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>


                                                        <div id="preview_videos_id" class="video_up" style="display:none" ></div>
                                                        <input type="hidden" name="video_type" id="video_type" value="<?php echo $video_type; ?>" />
                                                        <input type="hidden" id="old_video" name="old_video" value="<?php echo $video; ?>" >  



                                                    </div>
                                                    <div class="col-xs-12 col-md-2"></div>
                                                </div>

                                            </div>

                                            <div class="cl"></div>
                                            <div class="upload_video_date">

                                                <div class="row">
                                                    <div class="col-xs-12 col-md-2"></div>
                                                    <div class="col-xs-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Video Title</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" <?php if (!isset($_GET['live'])) { ?>readonly="true"<?php } ?> name="video_name" id="video_name" value="<?php echo $video_name; ?>">
                                                            </div>
                                                            <!-- /.input group --> 
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Live Date</label>
                                                            <div class="input-group" >
                                                                <input type="text"  name="live_date" id="live_date" value="<?php echo $live_date; ?>" <?php if (!isset($_GET['live'])) { ?>readonly="true"<?php } ?> class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask">
                                                                       <div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
                                                            </div>
                                                            <!-- /.input group --> 
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-md-2"></div>
                                                </div>


                                            </div>

                                        </div>
                                        <!-- /.tab-pane --> 
                                    </div>
                                    <!-- /.tab-content --> 
                                </div>
                                <!-- nav-tabs-custom --> 
                            </div>

                        </div>


                    </div>

                </div>
            </div>

        </div>
    </aside>

    <!-- ================================================================================================================== --> 
    <!-- Add Help Popap --> 
    <!-- ================================================================================================================== -->
    <div id="somedialog" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="popap-header">
                <h3 class="fl">Add New <?php //echo $app_name[0]['app_name'] ?>Version</h3>
                <button class="action fr" data-dialog-close>&nbsp;</button>
            </div>
            <div class="popap-content" id="add-app-page"></div>
        </div>
    </div>

    <div id="somedialog1" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="popap-header">
                <h3 class="fl">Add FAQ</h3>
                <button class="action fr" data-dialog-close>&nbsp;</button>
            </div>
            <div class="popap-content" id="add-faq-page"></div>
        </div>
    </div>


    <!-- ================================================================================================================== --> 
    <!-- Add Youtube Popap --> 
    <!-- ================================================================================================================== -->
    <div id="somedialogy" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="popap-header">
                <h3 class="fl">Enter Youtube URL</h3>
                <button  class="action fr" data-dialog-close>&nbsp;</button>
            </div>
            <div class="popap-content" id="add-youtube-page">


                <div class="box-body row">
                    <div class="col-xs-12 col-md-9">
                        <div class="form-group">
                            <label for="exampleInputPassword1">URL</label>
                            <input type="text" placeholder="" id="youtube_videop" name="youtube_videop"  value="<?php echo $youtube_video; ?>" class="form-control">
                        </div>
                    </div> <div class="popap-footer col-xs-12 col-md-12">
                        <button id="url_youtube" onclick="show_preview('youtube')" type="button" class="btn btn-primary fl button_submit" data-loading-text="Loading...">Save</button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================================================================== --> 
    <!-- Add Vimeo Popap --> 
    <!-- ================================================================================================================== -->
    <div id="somedialogv" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="popap-header">
                <h3 class="fl">Enter Vimeo URL</h3>
                <button class="action fr" data-dialog-close>&nbsp;</button>
            </div>
            <div class="popap-content" id="add-vimeo-page">
                <div class="box-body row">
                    <div class="col-xs-12 col-md-9">
                        <div class="form-group">
                            <label for="exampleInputPassword1">URL</label>
                            <input type="text" placeholder="" id="vimeo_videop" name="vimeo_videop"  value="<?php echo $vimeo_video; ?>" class="form-control">
                        </div>
                    </div> <div class="popap-footer col-xs-12 col-md-12">
                        <button id="url_vimeo" onclick="show_preview('vimeo')" type="button" class="btn btn-primary fl button_submit" data-loading-text="Loading...">Save</button>
                    </div>


                </div>
            </div>   
        </div>
    </div>
</form>  

<?php include 'inc/footer.php'; ?>
<script type="text/javascript" src="js/video.js"></script> 
<script type="text/javascript" src="js/youtube.js"></script> 
<script type="text/javascript" src="js/vimeo.js"></script> 
<script>
                                                                    videojs.options.flash.swf = "js/video-js.swf";
</script> 
<script type="text/javascript">
<?php if (!isset($_REQUEST['old'])): ?>
        (function() {

            var dlgtriggery = document.querySelector('[data-dialogy]');

            somedialogy = document.getElementById(dlgtriggery.getAttribute('data-dialogy'));

            dlgy = new DialogFx(somedialogy);

            dlgtriggery.addEventListener('click', dlgy.toggle.bind(dlgy));


            var dlgtrigger1 = document.querySelector('[data-dialog1]');

            somedialog1 = document.getElementById(dlgtrigger1.getAttribute('data-dialog1'));

            dlg1 = new DialogFx(somedialog1);

            dlgtrigger1.addEventListener('click', dlg1.toggle.bind(dlg1));


            var dlgtriggerv = document.querySelector('[data-dialogv]');

            somedialogv = document.getElementById(dlgtriggerv.getAttribute('data-dialogv'));

            dlgv = new DialogFx(somedialogv);

            dlgtriggerv.addEventListener('click', dlgv.toggle.bind(dlgv));


        })();
<?php endif; ?>
    //function to select fonts
    function select_tutorial_animation(id) {
        $('#select_animation_slider').children('div').removeClass('active');
        $('#tutorial_animation_' + id).addClass('active');
        $('#animation_id').val($('#tutorial_animation_' + id).html());
    }

//function to delete tutorial images
    function remove_logo(id) {
        $.prompt("", {
            title: "Are you sure you want to delete this help image record?",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function(e, v, m, f) {

                if (v == false) {
                } else {

                    var data = "ajax=1&action=delete_tutorial_image&intid=" + id;
                    request = $.ajax({
                        type: "POST",
                        url: "help-ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {

                        },
                        success: function(data) {
                            $('#select_app_logo_' + id).remove();
                        
                            if (data['output'] == 'S') {
                                message(data['msg'], 'success');
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


    //function to remove preview images
    function remove_manual_logo(id) {
        $('#preview_img_' + id).remove();
        $('#sfile_logo_' + id).remove();

    }
    $(window).load(function() {

        var fslider = $('#select_animation_slider').bxSlider({
            slideWidth: 135,
            minSlides: 5,
            maxSlides: 9,
            moveSlides: 1,
            startSlide: parseInt(0),
            slideMargin: 10,
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false,
            onSlideBefore: function() {
                var current = fslider.getCurrentSlide();
                $('#slide_anum').val(current);
            }
        });

        $(".sliderWrapperFont").show(0, "swing", function() {
            fslider.reloadSlider();
        });
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



    $(document).ready(function() {

        //Prevent popup buttons inside form scope to submit form
        $("button.action").on("click", function(e) {
            e.preventDefault();
        });

        $(".plus_icon a.trigger").click(function() {
            $.ajax({url: "add-help-version?member_id=<?php echo $member_id; ?>&sel_app_id=<?php echo $sel_app_id; ?>", success: function(result) {
                    $("#add-app-page").html(result);
                }});
        });

        $(".plus_icon1 a.trigger").click(function() {
            $.ajax({url: "add-faq?member_id=<?php echo $member_id; ?>&sel_app_id=<?php echo $sel_app_id; ?>&ver_id=<?php echo $intid ?>", success: function(result) {
                    $("#add-faq-page").html(result);
                }});
        });

        //Sort images
        $('.add-new-img').sortable({
            update: function(event, ui) {
                var data = $(this).sortable('serialize');
                // POST to server using $.post or $.ajax
                $.ajax({
                    data: data,
                    type: 'POST',
                    url: 'help-ajax.php?ajax=1&helpimgorder'
                });
            }
        });

        //PREVIEW Youtube
        $("#url_youtube").on("click", function() {
            var url = $('#youtube_videop').val();
            if (url != '') {
                dlgy.toggle();
<?php if ($video) { ?>
<?php } else { ?>
                    $(".file_name").remove();
                    $('.video_up').children(".upload_img").after('<div class="col-xs-12 col-md-4 file_name">' + url + '</div>');
<?php } ?>
                $('#youtube_video').val(url);
                $('#vimeo_videop').val('');
                $('#vimeo_video').val('');
                $("#video_type").val('youtube');
            }
        });

        //PREVIEW Vimeo
        $("#url_vimeo").on("click", function() {
            var url = $('#vimeo_videop').val();
            if (url != '') {
                dlgv.toggle();
<?php if ($video) { ?>
<?php } else { ?>
                    $(".file_name").remove();
                    $('.video_up').children(".upload_img").after('<div class="col-xs-12 col-md-4 file_name">' + url + '</div>');
<?php } ?>
                $('#vimeo_video').val(url);
                $('#youtube_videop').val('');
                $('#youtube_video').val('');
                $("#video_type").val('vimeo');
            }
        });

        //PREVIEW FILES
        $(document).on('change', 'input[type="file"]', function(e) {
            var id = this.id;

            if (id != 'video') {
                var new_id = id.split("_")[2];
                var fileInput = document.getElementById(id);

                var file = fileInput.files[0];
                var imageType = /image.*/;
                if (file.type.match(imageType)) {

                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#sfile_logo_' + new_id).children('span').after('<div id="preview_img_' + new_id + '" class="preview_small_img"><div class="center-img"><img src="' + e.target.result + '" style="height:auto;width:230px;" ><div class="over_img"><a href="javascript:;" onclick="remove_manual_logo(' + new_id + ')"><i class="apps_i trash_i" style="font-size:24px"></i></a></div></div></div>');
                        $('#app_type_error_' + new_id).remove();
                        $('#helpr_img_count').val(new_id);

                        //Add New helpr image
                        var inc_id = parseInt(new_id) + 1;
                        var html = '';
                        html += ' <div id="sfile_logo_' + inc_id + '" class="add_new_helpr_img"> <span class="btn add-files fileinput-button"> <img title="" alt="" src="img/upload_icon.png" class="add-logo_plus"> <span>Upload Tutorial Image</span>';
                        html += '<input type="file" placeholder="Logo-file" class="form-control tutorial-input" name="new_img[]" id="new_img_' + inc_id + '"> </span><div>';
                        html += '<div class="img_title_name"><input id="new_title_' + inc_id + '" name="new_title[]" class="form-control" type="text" placeholder="Image Title"></div>';
                        html += '</div>';

                        $(".add-new-img").append(html);

                    }
                    reader.readAsDataURL(file);
                } else {

                    $('#preview_img' + new_id).remove();
                    $('#app_type_error_' + new_id).remove();
                    $('#sfile_logo_0').after('<div class="helpr_file_error_msg" id="app_type_error_' + new_id + '">File not supported!</div>');


                }
            }
            else {
                var fileInput = document.getElementById(id);

                var file = fileInput.files[0];
                var imageType = /video.*/;
                if (file.type.match(imageType) && file.type == 'video/mp4') {
                    var reader = new FileReader();
                    reader.onload = function(e) {
<?php if ($video) { ?>
                            $('.video_up').html('<p>Upload Onboarding Video</p><div class="cl"></div><div class="upload_img preview_video3"><div class="col-xs-12 col-md-4 not_preview_video3">' + file.name + '</div></div>');
<?php } else { ?>
                            $(".file_name").remove();
                            $('.video_up').children(".upload_img").after('<div class="col-xs-12 col-md-4 file_name">' + file.name + '</div>');
<?php } ?>
                        $('#youtube_video').val('');
                        $('#vimeo_video').val('');
                        $("#video_type").val('file');
                    }
                    reader.readAsDataURL(file);
                } else {
                    alert(".mp4 video only");
                }
            }
        });




        //Datepicker
        $('#live_date').datepicker(
        {
<?php if (!$video) { ?>
            startDate: "today",
<?php } ?>
        autoclose: true,
        });
                //Add/Update Video/Images
                $(".save_all").on("click", function() {

            $('#noty_topCenter_layout_container').remove();

            if ($("#video_tab").hasClass("active")) {
                $('#editimgvideofrm').valid();
            } else {

            }
            var go = 1;
            if ($("#video_tab").hasClass("active")) {
                $('#editimgvideofrm').find('input,select').each(function() {
                    if ($(this).hasClass('error')) {
                        go = 0;
                        return false;
                    }
                });

            }
            if (go == 1) {
                $("#overlays").show();


                var form_data = $('#editimgvideofrm').serializefiles();
                var data = form_data;
                var btn = $(this);

                request = $.ajax({
                    type: "POST",
                    url: "help-ajax.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        btn.button('loading');

                    },
                    success: function(data) {
                        btn.button('reset');
                        $("#overlays").hide();

                        if (data['output'] == 'S') {

                            location.href = 'help-img-video?sel_app_id=<?php echo $sel_app_id; ?>&sel=faq&live';
                        } else if (data['output'] == 'SU') {
                            location.href = 'help-img-video?sel_app_id=<?php echo $sel_app_id; ?>&sel=faq&live';
                        }
                        else if (data['output'] == 'F') {
                            message(data['msg'], 'error');
                        }

                    }
                });
            } else {
                message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
            }


        });


        $("#version").focusout(function() {
            check_helpr_version($("#version").val(), $("#app_id").val());
        });




    });


    $('#editimgvideofrm').validate({
        onkeyup: function(element) {
            $(element).valid()
        },
        rules: {
            live_date: {
                required: true,
            },
            video_name: {
                required: true,
            },
        },
        messages: {
            video_name: {required: ""},
            live_date: {required: ""},
        }
    });
    function show_preview(flag) {

        if (flag == 'youtube') {
            var urlyoutube = $('#youtube_videop').val();
            var url = urlyoutube;
            var qs = url.substring(url.indexOf('?') + 1).split('&');
            for (var i = 0, result = {}; i < qs.length; i++) {
                qs[i] = qs[i].split('=');
                result[qs[i][0]] = decodeURIComponent(qs[i][1]);
            }
            $('#preview_videos_id').empty();
            $('#preview_videos_id').append('<div id="videoContainer"><iframe width="100%" height="" src="https://www.youtube.com/embed/' + result['v'] + '" frameborder="0" allowfullscreen></iframe></div>');
        } else if (flag == 'vimeo') {
            var urlvimeo = $('#vimeo_videop').val();
            var url = urlvimeo;
            var qs = url.split('/');
            $('#preview_videos_id').empty();
            $('#preview_videos_id').append('<div id="videoContainer"><iframe src="https://player.vimeo.com/video/' + qs[qs.length - 1] + '" width="100%" height="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>');
        }

        $('#video_show').hide();
        $('#preview_videos_id').show();


    }
</script> 
<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script> 
<script type="text/javascript" src="js/process.js"></script>

<script src="js/jquery.zclip.js"></script>
<script type="text/javascript">


    function inlineedit(id) {
        $('#faq-list-' + id).addClass('app-list_tr');
    }

    function hideeditrow(id) {
        $('#faq-list-' + id).removeClass('app-list_tr');
    }

    function update_helpr_faq(id) {

        $('#noty_topCenter_layout_container').remove();
        $('#helpreditform_' + id).valid();

        var go = 1;
        $('#helpreditform_' + id).find('input,select').each(function() {
            if ($(this).hasClass('error')) {
                go = 0;
                return false;
            }
        });


        if (go == 1) {


            $("#overlays").show();
            var status = 'publish';

            var data = $('#helpreditform_' + id).serialize();

            request = $.ajax({
                type: "POST",
                url: "help-ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function() {

                },
                success: function(data) {
                    $("#overlays").hide();

                    if (data['output'] == 'S') {
                        message(data['msg'], 'success');
                        $("#question_change_" + id).html(data["question"]);
                        $("#answer_change_" + id).html(data["answer"]);
                        if (data['record_icon'] == 'active_icon') {
                            $("#record_button_" + id).removeClass('inactive_icon').addClass('active_icon');
                            $("#record_button_" + id).attr('title', 'Active');

                        } else {
                            $("#record_button_" + id).removeClass('active_icon').addClass('inactive_icon');
                            $("#record_button_" + id).attr('title', 'Inactive');
                        }

                    } else if (data['output'] == 'SU') {
                        message(data['msg'], 'success');
                        $("#question_change_" + id).html(data["question"]);
                        $("#answer_change_" + id).next().empty();
                        $("#answer_change_" + id).html(data["answer"]);

                        if (data['record_icon'] == 'active_icon') {
                            $("#record_button_" + id).removeClass('inactive_icon').addClass('active_icon');
                            $("#record_button_" + id).attr('title', 'Active');

                        } else {
                            $("#record_button_" + id).removeClass('active_icon').addClass('inactive_icon');
                            $("#record_button_" + id).attr('title', 'InActive');
                        }

                    }
                    else if (data['output'] == 'F') {
                        message(data['msg'], 'error');
                    }
                    hideeditrow(id);
                }
            });
        } else {
            message('<?php echo $gnrl->getMessage('REQUIRED', $lang_id); ?>', 'error');
        }


    }

//copy description
    function copy_description(id) {
        $("#copy-dynamic_" + id).zclip({
            path: "img/ZeroClipboard.swf",
            copy: function() {
                return $("#answer_" + id).text();
            }
        });

    }

//Delete helpr faq
    function delete_faq(id) {
        $.prompt("", {
            title: "Are you sure you want to delete this faq?",
            buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
            submit: function(e, v, m, f) {

                if (v == false) {
                } else {

                    var data = "ajax=1&action=delete_faq&limit=<?php echo $limit; ?>&intid=" + id;
                    request = $.ajax({
                        type: "POST",
                        url: "help-ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {
                        },
                        success: function(data) {

                            if (data['output'] == 'S') {
                                $('#faq-list-' + id).remove();
                                message(data['msg'], 'success');
                                $('#answer_' + id).wysihtml5({
                                    "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
                                    "emphasis": true, //Italics, bold, etc. Default true
                                    "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                                    "html": false, //Button which allows you to edit the generated HTML. Default false
                                    "link": false, //Button to insert a link. Default true
                                    "image": false, //Button to insert an image. Default true,
                                    "color": false //Button to change color of font  
                                });
                                search('', 'tblapp_faq', ['app_name'],<?php echo $limitstart ?>,<?php echo 5 ?>, '', 'help');
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

    $(document).ready(function() {

        //view more
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
                pagination: '.faq_list .navigation',
                next: '.next-posts a',
                loader: '<div class="view_more_loader"><img src="img/ajax-loader.gif"/></div>',
                triggerPageThreshold: 0,
                trigger: 'View More',
                history: false,
                onRenderComplete: function() {
                    remove_loader();

                },
                beforePageChange: function(curScrOffset, urlnext) {
                    spliturl_paging(urlnext, ['app_name'], 'helpr');
                    remove_loader();
                    $("#overlay").show();
                },
                onPageChange: function(pageNum, pageUrl, scrollOffset) {
                    spliturl_paging(pageUrl, ['app_name'], 'onp');
                    remove_loader();
                    $("#overlay").show();
                }

            });
        }
    });
</script> 

<script type="text/javascript">

    $(document).ready(function() {
        $('.table-faq tr')
                .filter(':has(:checkbox:checked)')
                .addClass('selected')
                .end()
                .click(function(event) {
            $(this).toggleClass('selected');
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });



    });



</script> 

<script>
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

    $("#faq_tab").click(function() {
        $("#save_publish").show();
        $("#save_faq").show();
    });
    $("#images_tab").click(function() {
        $("#save_publish").show();
        $("#save_faq").hide();
    });
    $("#video_tab").click(function() {
        $("#save_publish").show();
        $("#save_faq").hide();
    });
    
    <?php if($_REQUEST['sel']=='faq'){ ?>
            $("#save_faq").show();
    <?php } ?>

    search('', 'tblapp_faq', ['app_name'],<?php echo $limitstart ?>,<?php echo 5 ?>, '', 'help');
</script>

