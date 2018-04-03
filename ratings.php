<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php 
    require("config/configuration.php");
    $feature_id = 1; //1 for Ratings
    
    if(!$gnrl->checkMemLogin()){
	$gnrl->redirectTo("login?msg=logfirst");
    }
    
    $member_id = $_SESSION['custid'];
    
    if(isset($_SESSION['sel_app_id'])){
         $_REQUEST['sel_app_id'] = $_SESSION['sel_app_id'];
     }
   
    if(isset($_REQUEST['sel_app_id']) && $_REQUEST['sel_app_id'] != '' ){
        $sel_app_id= $_REQUEST['sel_app_id'];
       $res = $dclass->select("r.*,a.app_type,a.app_name,f.feature_status, f.intid as id","tblmember_apps a INNER JOIN tblapp_ratings r ON a.intid=r.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id"," AND a.intid='".$sel_app_id."' AND a.app_status='active' AND f.feature_id='".$feature_id."' AND f.transaction_id != '0' ");
         
    }else{
        $sel_app_id = '';
        $res = $dclass->select("r.*,a.app_type,a.app_name,f.feature_status, f.intid as id","tblmember_apps a INNER JOIN tblapp_ratings r ON a.intid=r.app_id INNER JOIN tblmember_app_features f ON a.intid=f.app_id"," AND a.member_id='".$member_id."' AND a.app_status='active' AND f.feature_id='".$feature_id."' AND f.transaction_id != '0' order by a.app_add_date LIMIT 1");
        $sel_app_id = $res[0]['app_id'];
        
    }
   if(count($res) > 0){
            $feature_status_id = $res[0]['id'];
            $feature_status = $res[0]['feature_status'];
            $app_type = $res[0]['app_type'];
            $app_name = $res[0]['app_name'];
            if($app_type == 'ios')
                $ios_class = 'active';
            else if($app_type == 'android')        
                $android_class = 'active';
            else if($app_type == 'windows')        
                $windows_class = 'active';
            $content_yn = $res[0]['content_yn'];
            $content_rate_short = $res[0]['content_rate_short'];
            $content_rate_long = $res[0]['content_rate_long'];
            $like_yes = $res[0]['like_yes'];
            $like_no = $res[0]['like_no'];
            $rate_this_app = $res[0]['rate_this_app'];
            $remind_later = $res[0]['remind_later'];
            $no_thanks = $res[0]['no_thanks'];
            $like_yes_bck = $res[0]['like_yes_bck'];
            $like_no_bck = $res[0]['like_no_bck'];
            $like_yes_but = $res[0]['like_yes_but'];
            $like_no_but = $res[0]['like_no_but'];
            $rate_this_app_bck = $res[0]['rate_this_app_bck'];
            $remind_later_bck = $res[0]['remind_later_bck'];
            $no_thanks_bck = $res[0]['no_thanks_bck'];
            $rate_this_app_but = $res[0]['rate_this_app_but'];
            $remind_later_but = $res[0]['remind_later_but'];
            $no_thanks_but = $res[0]['no_thanks_but'];
            $que_select_font = $res[0]['que_select_font'];
            $rate_select_font = $res[0]['rate_select_font'];
            $like_no_action = $res[0]['like_no_action'];
            $status = $res[0]['status'];
            if($status == 'save')
                $save_button_class = 'active';
            else if($status == 'publish')        
                $publish_button_class = 'active';
            else if($status == 'pause')        
                $pause_button_class = 'active';
   }
    
    include(INC."header.php");  ?>

<!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side"> 
  <!-- Content Header (Page header) -->
<?php include 'inc/app-navigate.php'; ?>
<?php if($no_app_flag == 0){ //check if any app exists ?>                        
  <!-- Main content -->
  <section class="content clear">
       <h2 class="fl"> Start with a simple question: </h2>
       <form id="saveupperfrm" method="POST" action="">
           <input type="hidden" id="rating_id" value="" >   
        <div class="promote_platform_top fr">
          <div class="fr">
          <button class="btn btn-primary save_all save <?php echo $save_button_class; ?>" type="button" value="save">Save</button>
          <button class="btn btn-primary save_all publish <?php echo $publish_button_class; ?>" type="button" value="publish"><?php echo SAVE_PUBLISH; ?></button>
          </div>
        </div>
       </form>
       <div class="clear"></div>
    <div class="mainbox"> 
        
      <!-- Small boxes (Stat box) -->
      <div class="row ">
        
        <div class="center_align">
        <div class="center_a">
        
        <div class="col-lg-5 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"><br />
            <h3>Preview</h3>
          </div>
           <form id="likefrm_preview" action="" method="post" >
          <div class="whitebox">
            <div class="inbox">
              <input type="text" id="like_question_preview" readonly="readonly" class="readonly" value="<?php echo $content_yn; ?>" />
              <div class="readonly">gdsgdfgdfgdfgdfgdfgdfgdf sddsf sdgsdg</div>
              <br />
              <span class="left">
                   <input id="like_no_preview"  type="button" readonly="readonly"class="buttongray readonly" value="<?php echo $like_no; ?>" style="background-color:#<?php echo $like_no_bck; ?>;color:#<?php echo $like_no_but; ?>;font-family:<?php echo $que_select_font; ?>">
                  <input id="like_yes_preview"  type="button" readonly="readonly" class="buttoncolor readonly" value="<?php echo $like_yes; ?>"  style="background-color:#<?php echo $like_yes_bck; ?>;color:#<?php echo $like_yes_but; ?>;font-family:<?php echo $que_select_font; ?>" >
              </span></div>
          </div>
           </form>     
        </div>
        <!-- ./col -->
        <div class="col-lg-2 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"> <img src="img/arrow1.png" alt="" class="arrow1" title=""/></div>
        </div>
        <!-- ./col -->
        <div class="col-lg-5 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"><br />
            <h3>Edit</h3>
          </div>
          <form id="likefrm" action="" method="post" >
          <div class="whitebox editbox">
            <div class="inbox">
              <input type="text" id="like_question" name="content_yn" value="<?php echo $content_yn; ?>" />
              <br />
              <span class="left">
              <input id="like_no" name="like_no" type="text"class="buttongray" value="<?php echo $like_no; ?>">
             <input id="like_yes" name="like_yes" type="text" class="buttoncolor" value="<?php echo $like_yes; ?>" >
              </span></div>
          </div>
          </form>
        </div> 
        <div class="cl"></div>
        </div>
        <div class="cl"></div>
        </div>
        
      </div>
      <!-- /.row -->
    
      <div class="row ">
        <div class="col-lg-12 col-xs-12">
          <div class="panel-group" id="accordion">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Other Options </a> </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                  <form id="queoptionsfrm" method="POST" class="form-horizontal" role="form">
                    <input type="hidden" id="like_no_bck_value" name="like_no_bck_value" value="<?php echo $like_no_bck; ?>" > 
                    <input type="hidden" id="like_no_but_value" name="like_no_but_value" value="<?php echo $like_no_but; ?>" > 
                    <input type="hidden" id="like_yes_bck_value" name="like_yes_bck_value" value="<?php echo $like_yes_bck; ?>" > 
                    <input type="hidden" id="like_yes_but_value" name="like_yes_but_value" value="<?php echo $like_yes_but; ?>" >  
                    <div class="row">
                    <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-6 col-xs-12">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'No' Button Background Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"><span id="like_no_bck" class="que_color colorpalate" style=" background-color:#<?php echo $like_no_bck; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 col-xs-12">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'No' Button Font Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"> <span id="like_no_but" class="que_color colorpalate" style=" background-color:#<?php echo $like_no_but; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                    <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-6 col-xs-12">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Yes' Button Background Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"><span id="like_yes_bck" class="que_color colorpalate" style="background-color:#<?php echo $like_yes_bck; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 col-xs-12">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Yes' Button Font Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"> <span id="like_yes_but" class="que_color colorpalate" style=" background-color:#<?php echo $like_yes_but; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                    <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-7 col-xs-12">
                        <div class="col-md-12">
                          <label for="inputEmail3" class="col-sm-7 control-label">Font Family</label>
                          <div class="col-sm-4">
                            <?php echo $gnrl->font_select('que_select_font',$que_select_font); ?>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <h2>People who answer <strong>Yes</strong> are asked to rate your app </h2>
    <div class="mainbox"> 
      
      <!-- Small boxes (Stat box) -->
      <div class="row ">
       <div class="center_align">
        <div class="center_a">
        <div class="col-lg-5 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"><br />
            <h3>Preview</h3>
          </div>
          <form id="ratefrm_preview" action="" method="post" >
          <div class="whitebox">
            <div class="inbox rate">
              <input type="text" id="content_rate_short_preview" readonly="readonly" class="readonly" value="<?php echo $content_rate_short; ?>" />
              <br />
              <span class="dec" id="content_rate_long_preview"><?php echo $content_rate_long; ?></span>
              <div class="left center">
               <input  id="rate_this_app_preview"  class="readonly buttoncolor wd" type="button" value="<?php echo $rate_this_app; ?>" style="background-color:#<?php echo $rate_this_app_bck; ?>;color:#<?php echo $rate_this_app_but; ?>;font-family:<?php echo $rate_select_font; ?>" >
                <input id="remind_later_preview" class="readonly buttongray wd5" type="button" value="<?php echo $remind_later; ?>" style="background-color:#<?php echo $remind_later_bck; ?>;color:#<?php echo $remind_later_but; ?>;font-family:<?php echo $rate_select_font; ?>" >
                <input id="no_thanks_preview" class="readonly buttongray wd5" type="button"  value="<?php echo $no_thanks; ?>" style="background-color:#<?php echo $no_thanks_bck; ?>;color:#<?php echo $no_thanks_but; ?>;font-family:<?php echo $rate_select_font; ?>">
              </div>
            </div>
          </div>   
          </form>
        </div>
        <!-- ./col -->
        <div class="col-lg-2 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"> <img src="img/arrow1.png" alt="" class="arrow1" title=""/></div>
        </div>
        <!-- ./col -->
        <div class="col-lg-5 col-xs-12 question_box"> 
          <!-- small box -->
          <div class="center"><br />
            <h3>Edit</h3>
          </div>
          <form id="ratefrm" action="" method="post"/>
          <div class="whitebox editbox">
            <div class="inbox rate">
              <input type="text" id="content_rate_short" name="content_rate_short" value="<?php echo $content_rate_short; ?>" />
              <br /> <br />
              <span class="dec">
              <textarea id="content_rate_long" name="content_rate_long" rows="3" class="body valid" aria-required="true" aria-invalid="false">
<?php echo $content_rate_long; ?></textarea>
              </span>
              <div class="left center">
                <input type="text" id="rate_this_app" name="rate_this_app" class="buttoncolor wd"  value="<?php echo $rate_this_app; ?>"  />
                 <input type="text"id="remind_later" name="remind_later" class="buttongray wd5"  value="<?php echo $remind_later; ?>"/>
                 <input type="text" id="no_thanks" name="no_thanks" class="buttongray wd5" value="<?php echo $no_thanks; ?>"  />
              </div>
            </div>
          </div>
        </div>
        
        
        </form>
        <!-- ./col --> 
        <!-- ./col --> 
      	<div class="cl"></div>
      </div>
      <div class="cl"></div>
      </div>
        <!-- ./col --> 
      </div>
      <!-- /.row -->
      <div class="row ">
        <div class="col-lg-12 col-xs-12">
          <div class="panel-group" id="accordion">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Other Options </a> </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                  <form id="rateoptionsfrm" method="POST" class="form-horizontal" role="form">
                    <input type="hidden" id="rate_this_app_bck_value" name="rate_this_app_bck_value" value="<?php echo $rate_this_app_bck; ?>" > 
                    <input type="hidden" id="rate_this_app_but_value" name="rate_this_app_but_value" value="<?php echo $rate_this_app_but; ?>" > 
                    <input type="hidden" id="remind_later_bck_value" name="remind_later_bck_value" value="<?php echo $remind_later_bck; ?>" > 
                    <input type="hidden" id="remind_later_but_value" name="remind_later_but_value" value="<?php echo $remind_later_but; ?>">  
                    <input type="hidden" id="no_thanks_bck_value" name="no_thanks_bck_value" value="<?php echo $no_thanks_bck; ?>" > 
                    <input type="hidden" id="no_thanks_but_value" name="no_thanks_but_value" value="<?php echo $no_thanks_but; ?>" > 
                    <div class="row">
                    <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Rate this app' Button Background Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"><span id="rate_this_app_bck" class="rate_color colorpalate" style="background-color:#<?php echo $rate_this_app_bck; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Rate this app' Button Font Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"> <span id="rate_this_app_but" class="rate_color colorpalate" style=" background-color:#<?php echo $rate_this_app_but; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                    	 <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Remind me later' Button Background Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"><span id="remind_later_bck" class="rate_color colorpalate"style=" background-color:#<?php echo $remind_later_bck; ?>" ></span></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'Remind me later' Button Font Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"> <span id="remind_later_but" class="rate_color colorpalate" style=" background-color:#<?php echo $remind_later_but; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                     <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'No thanks' Button Background Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"><span id="no_thanks_bck" class="rate_color colorpalate" style=" background-color:#<?php echo $no_thanks_bck; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="col-md-12">
                          <label for="inputPassword3" class="col-sm-8 control-label">'No thanks' Button Font Color</label>
                          <div class="col-sm-2">
                            <p class="form-control-static"> <span id="no_thanks_but" class="rate_color colorpalate" style=" background-color:#<?php echo $no_thanks_but; ?>"></span></p>
                          </div>
                        </div>
                      </div>
                      </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                     <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-7">
                        <div class="col-md-12">
                          <label for="inputEmail3" class="col-sm-7 control-label">Font Family</label>
                          <div class="col-sm-4">
                          <?php echo $gnrl->font_select('rate_select_font',$rate_select_font); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
 <h2>People who answer <strong>No</strong>  </h2>
 <div class="mainbox">
 		<form class="form-horizontal" role="form">  
   <div class="row">
   <div class="center_align">
       													 <div class="center_a">
                      <div class="col-sm-7">  <div class="form-group">
                          <label for="inputEmail3" class="col-sm-8 control-label">Select Action</label>
                          <div class="col-sm-4">
                            <select id="like_no_action" name="like_no_action" class="form-control">
                              <option value="support" <?php if($like_no_action == 'support'){ echo 'selected';} ?> >Support Request Form</option>
                              <option value="email" <?php if($like_no_action == 'email'){ echo 'selected';} ?>>Email</option>
                             
                            </select>
                          </div>
                          
                          <div class="form-group email_show" style="margin:15px 0 0 0; display:inline-block; width:100%;">
                          <label for="inputEmail3" class="col-sm-8 control-label">Email</label>
                          <div class="col-sm-4">
                            <input id="" class="form-control tutorial-input wd" type="text" value="" placeholder="Email" style="border-radius:0 !important;" name="Email">
                          </div>
                          </div>
                          
                          
                        </div> </div>
                        </div></form></div>
      <div class="clear height3"></div>
     <form id="savelowerfrm" method="POST" action="">
           <input type="hidden" id="rating_id" value="" >   
        <div class="col-md-12 promote_platform_top fr">
          <div class="fr">
          <button class="btn btn-primary save_all save <?php echo $save_button_class; ?>" type="button" value="save">Save</button>
          <button class="btn btn-primary save_all publish <?php echo $publish_button_class; ?>" type="button" value="publish"><?php echo SAVE_PUBLISH; ?></button>
         </div>
        </div>
       </form>
       <div class="clear"></div>
   
  </section>
  <!-- /.content --> 
<?php }else{ ?>
  <div align="center"> No App Found </div>
<?php } ?>
</aside>
<!-- /.right-side -->
</div>
<!-- ./wrapper -->
<?php include(INC."footer.php"); ?>
<link href="css/col-pick/colpick.css" rel="stylesheet" type="text/css" />
<script src="js/plugins/col-pick/colpick.js" type="text/javascript"></script>
 <script type="text/javascript">
     
 $(function() {
             //Function to save ratings     
            $('.save_all').on('click',function(e){
                var status = $(this).val(); 
               $('.save_all').removeClass('active');
               $('.'+status).addClass('active');
               
                var intid = $('#rating_id').val();
                 var form_data = $('form').serialize();
                var data = "ajax=1&action=save_ratings&intid="+'<?php echo $sel_app_id; ?>'+"&status="+status+"&"+form_data;
                
                 request = $.ajax({
					type: "POST",
					url: "ajax.php",
					data: data,
					dataType: 'json',
					cache: false,
					beforeSend: function(){
						$("#ovelays").show();
						$('#loading-image').show();
						$('.preload-bg').show();
					},
					success:function(data){
						$("#ovelays").hide();
						$('#loading-image').hide();
						$('.preload-bg').hide();
						
						if(data['output'] == 'S'){
							message(data['msg'],'success');
						}
						else if(data['output'] == 'F'){
							message(data['msg'],'error');	
						}
						
				
				   }
			});
             });
       
                
            
                $('#likefrm').validate({
                    onkeyup: function(element) {$(element).valid()},
                    rules: {
                        content_yn: "required",
                        like_yes : "required",
                        like_no : "required"
                    },
                    messages: {
                        content_yn: "",
                        like_yes : "",
                        like_no : ""
                    }
                });
                
                 $('#ratefrm').validate({
                    onkeyup: function(element) {$(element).valid()},
                    rules: {
                        content_rate_short: "required",
                        content_rate_long : "required",
                        rate_this_app : "required",
                        remind_later : "required",
                        no_thanks : "required"
                    },
                    messages: {
                        content_rate_short: "",
                        content_rate_long : "",
                        rate_this_app : "",
                        remind_later : "",
                        no_thanks : ""
                    }
                });
                
                //Preview question text
                $('#likefrm input[type="text"]').keyup(function(){
                   var preview_id = this.id+"_preview";
                   $('#'+ preview_id).val($(this).val());
                });
                
               //Preview question Background/Font color change 
               $('.que_color').colpick({
                    colorScheme:'dark',
                    layout:'hex',
                    color:'ff8800',
                    onSubmit:function(hsb,hex,rgb,el) {
                            $(el).css('background-color', '#'+hex);
                            $(el).colpickHide();
                            var preview_id = el.id.split("_")[0]+"_"+el.id.split("_")[1]+"_preview";
                            if(el.id.split("_")[2] == 'bck'){
                             $('#'+preview_id).css('background-color', '#'+hex);
                            }
                            
                            if(el.id.split("_")[2] == 'but'){
                                $('#'+preview_id).css('color', '#'+hex);
                            }
                            //alert(el.id+'_value');
                            $('#'+el.id+'_value').val(hex);
                            
                    }   
                })
                //Preview question font change
                $('#que_select_font').change(function(){
                    $('#likefrm_preview input').css("font-family", $(this).val());
                });
                
                //Preview rate
                $('#ratefrm input[type="text"], textarea').keyup(function(){
                   var preview_id = this.id+"_preview";
                   if(preview_id == 'content_rate_long_preview'){
                      $('#'+ preview_id).text($(this).val()); 
                   }else{
                    $('#'+ preview_id).val($(this).val());
                   }
                  
                });
                
                 //Preview rate Background/Font color change 
               $('.rate_color').colpick({
                    colorScheme:'dark',
                    layout:'rgbhex',
                    color:'ff8800',
                    onSubmit:function(hsb,hex,rgb,el) {
                            $(el).css('background-color', '#'+hex);
                            $(el).colpickHide();
                            var lng = el.id.split("_").length;
                            var preview_id = "";
                            for(i=0;i<(lng-1);i++){
                                  preview_id += el.id.split("_")[i]+"_";
                            }
                            preview_id= preview_id+"preview";
                            
                         
                            if(el.id.split("_")[2] == 'bck' || el.id.split("_")[3] == 'bck'){
                             $('#'+preview_id).css('background-color', '#'+hex);
                            }
                         
                            if(el.id.split("_")[2] == 'but' || el.id.split("_")[3] == 'but'){
                                $('#'+preview_id).css('color', '#'+hex);
                            }
                            //alert(el.id+'_value');
                            $('#'+el.id+'_value').val(hex);
                    }
                });
                
                
                 //Preview rate font change
                $('#rate_select_font').change(function(){
                    $('#ratefrm_preview input, #rate_long_msg_preview').css("font-family", $(this).val());
                });
                
            });
    </script>
        

</body></html>