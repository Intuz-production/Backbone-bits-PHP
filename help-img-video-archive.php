<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 
    require("config/configuration.php");
    if(!$gnrl->checkMemLogin()){
	$gnrl->redirectTo("login?msg=logfirst");
    }
   $feature_id = 4; //help
   
   $member_id = $_SESSION['custid'];
    extract($_GET);
    
    include INC."header.php"; 
    
    include INC."left_sidebar.php";
    
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
<!-- ============================================================================== -->
<!-- Right Side Bar -->
<!-- ============================================================================== -->
<link href="css/video-js.css" rel="stylesheet" type="text/css" >
<aside class="right-side">
  <div class="right_sidebar">
    <div class="add-apps">
      <div class="col-xs-12 col-md-12"> 
  
      </div>
    </div>
    <div class="col-xs-12 col-md-12">
        <input type="hidden" name="sel_app_id" id="sel_app_id_ajax" value="<?php echo $sel_app_id ?>" />
      <div class="box"> 
        
        <div class="box-body table-responsive upgradr-table" id="promote-table_wrapper">
          <table id="example1" class="table table-bordered classtablenew table-striped">
            <thead>
              <tr>
                <th>Version</th>
                <th>Video</th>
                <th>Images</th>
                <th>FAQ</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody id="showdata">

             
            </tbody>
          </table>
           <div class='navigation' id="navview">

         </div>

        </div>
      </div>
    </div>
  </div>
</aside>


<!-- ================================================================================================================== --> 
<!-- Add Apps Popap --> 
<!-- ================================================================================================================== -->
<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="popap-header">
      <h3 class="fl">Add New Version</h3>
      <button class="action fr" data-dialog-close>&nbsp;</button>
    </div>
    <div class="popap-content" id="add-app-page"></div>
  </div>
</div>


<!--======================== mobile View Popep =========================-->

<div id="somedialog1" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content" id="movile_view" >
    <div class="popap-header">
     
      <button class="action fr" data-dialog-close>&nbsp;</button>
    </div>
    <div class="popap-content" id="add-app-page">
	
	
	</div>
  </div>
</div>


<?php include 'inc/footer.php'; ?>
<script type="text/javascript" src="js/video.js"></script> 
<script type="text/javascript" src="js/youtube.js"></script> 
<script type="text/javascript" src="js/vimeo.js"></script> 
<script>
    videojs.options.flash.swf = "js/video-js.swf";
  </script> 
<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script> 
<script type="text/javascript" src="js/process.js"></script>

<script type="text/javascript">
 
  //function to delete archive
      function delete_archive(id) {
                     $.prompt("", {
                         title: "Are you sure you want to delete this record?",
                         buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
                         submit: function(e, v, m, f) {

                             if (v == false) {
                             } else {

                                 var data = "ajax=1&action=delete_help_archive&limit=<?php echo $limit; ?>&intid=" + id;
                                 //alert(data); return false;
                                 request = $.ajax({
                                     type: "POST",
                                     url: "help-ajax.php",
                                     data: data,
                                     dataType: 'json',
                                     cache: false,
                                     beforeSend: function() {
         
                                        $("#ovelays").show();

                                     },
                                     success: function(data) {
                                        $("#ovelays").hide();


                                         if (data['output'] == 'S') {
                                             search('', 'tblapp_tutorial_settings', ['app_name'],<?php echo $limitstart ?>,<?php echo 5 ?>, '', '');
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
                 
    //copy description
    function copy_description(id){
        $("#copy-dynamic_"+id).zclip({
           path:"img/ZeroClipboard.swf",
           copy:function(){return $("#content_"+id).text();}
        });
        
    }
    
$(document).ready(function(){
     $(".plus_icon a.trigger").click(function(){
        $.ajax({url: "add-help-version?member_id=<?php echo $member_id; ?>&sel_app_id=<?php echo $sel_app_id; ?>", success: function(result){
        $("#add-app-page").html(result);
        }});
    });
    
     var limit = '<?php echo $limit; ?>';
        var str = '<?php echo $str; ?>';
        var total_pages = '<?php echo $total_pages; ?>';
        
        <?php if($_REQUEST['rd'] != ''){ ?>
             var rd = '<?php echo $_REQUEST['rd']; ?>';
        <?php }else{ ?>
            var rd = limit;
        <?php } ?>
        
        
       $('.selectedTxt').empty();
       $('.selectedTxt').html(rd);
        
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
            pagination: '#promote-table_wrapper .navigation',
            next: '.next-posts a',
            loader: '<div class="view_more_loader"><img src="img/ajax-loader.gif"/></div>',
            triggerPageThreshold: 0,
            trigger: 'View More',
            history: false,
            onRenderComplete: function() {
                remove_loader();
                
            },
            beforePageChange: function(curScrOffset, urlnext) {
                spliturl_paging(urlnext, ['app_name'], 'bef');
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

<script>
    search('', 'tblapp_tutorial_settings', ['app_name'],<?php echo $limitstart ?>,<?php echo 5 ?>, '', '');
</script>
