<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ 

require("config/configuration.php");
if(!$gnrl->checkMemLogin()){
   $gnrl->redirectTo("login?msg=logfirst");
}
 
$member_id = $_SESSION['custid']; 
$agent_cust_id = $_SESSION['agents_cust_id']; 
 
 //save user settings
if(isset($_POST['intid'])){
    extract($_POST);
    if($password == "" && $new_password != ''){
        $data['output'] = 'F';
        $type = 'err';
        $msg = $gnrl->getMessage("CURRENT_PASSWORD_REQ",$lang_id);
    }
    else {
        $t = explode(" ", $name);
        $up['fname'] = $t[0];
        $up['lname'] = $t[1];
        $up['email'] = $email;
        if($new_password != '')
            $up['password'] = md5($new_password);
           
        if($del_old_logo != ''){
            unlink(USER_LOGO."/".$old_logo);
            $up['logo'] = "";
        }
        
        if($_FILES['logo']['name']!=''){                
    		$filename = time().$gnrl->makefilename($_FILES['logo']['name']);
    		$des = USER_LOGO."/".$filename;	
    		if(move_uploaded_file($_FILES['logo']['tmp_name'],$des)){
    			$up['logo'] = $filename;
    		}
    	}
        $up['company'] = $company;
        
        if($del_old_company_logo != ''){
            unlink(COMPANY_LOGO."/".$del_old_company_logo);
            $up['company_logo'] = "";
        }
                    
        if($_FILES['company_logo']['name']!=''){
    		$filename = time().$gnrl->makefilename($_FILES['company_logo']['name']);
    		$des = COMPANY_LOGO."/".$filename;	
    		if(move_uploaded_file($_FILES['company_logo']['tmp_name'],$des)){
    			$up['company_logo'] = $filename;
    		}
    	}
        $upd['dtmod'] = date("Y-m-d h:i:s");
        
        $dclass->update("tblmember",$up," intid='".$intid."' ");
        $data['output'] = 'S';
        $type = 'succ';
        $msg = $gnrl->getMessage("SETTING_SUC",$lang_id); 
    }
    $_SESSION['msg'] = $msg;
    $_SESSION['type'] = $type;
    $gnrl->redirectTo($url); die();
}  
   
$res = $dclass->select("*","tblmember"," AND intid='".$agent_cust_id."' ");
$fname = $res[0]['fname'];
$lname = $res[0]['lname'];
$email = $res[0]['email'];
$password = $res[0]['password'];
$company = $res[0]['company'];
$username = $res[0]['username'];
$role = $res[0]['role'];
$status = $res[0]['status'];
$timezone = $res[0]['timezone'];
   
$logo = $res[0]['logo'];
if($logo != '' && is_file(USER_LOGO."/".$logo)) {
    $logo_path = SITE_URL.USER_LOGO."/".$logo;
}
else{
    $logo_path = "img/s_user_img.png";
}

$company_logo = $res[0]['company_logo'];
if($company_logo != '') 
    $company_logo_path = "memimages.php?max_width=125&max_width=125&imgfile=".COMPANY_LOGO."/".$company_logo;
        
$status = $res[0]['status'];

include(INC."header.php"); 
include INC."left_sidebar.php";

?><!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">  
    <!-- Main content -->
   
    <section id="account-settings">
        <div class="col-xs-12 col-md-12">
            <div class="satting_top_title">
                <form id="account_settings" method="POST" action=""  enctype="multipart/form-data">
                    <input type="hidden" name="intid" id="intid" value="<?php echo $agent_cust_id; ?>" >
                    <input type="hidden" name="username" id="username" value="<?php echo $username; ?>" >
                    <input type="hidden" name="role" id="role" value="<?php echo $role; ?>" >
                    <input type="hidden" name="status" id="status" value="<?php echo $status; ?>" >
                    <input type="hidden" name="url" id="url" value="<?php echo "profile"; ?>" >
          
                    <div class="promote_platform_top col-sm-12 padding0 pull-left">
                        <div class="fr">
                            <button name="save_settings" value="Send" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all " data-loading-text="Loading..." onclick="update(this.form.id,'tblmember','<?php echo  $url ?>')">Save Changes</button>
                        </div>
                    </div>
          
                    <!-- left -->
                    <div class="col-xs-12 col-md-6 profile-l">
                    <h3 class="padding-left-15">Personal Info</h3>
                    <div class="col-xs-12 col-md-12 profile_logo_img">
                      <div class="col-xs-12 col-md-3 padding0 Profile_logo">
                        <div id="file_logo" class="mainlogo">
                          <?php if($logo != ''){ ?>
                          <span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Photo</span>
                          <input type="file" id="logo" name="logo">
                          </span>
                          <div class="upload_img" id="remove_logo"> <img src="<?php echo $logo_path; ?>" alt=""  />
                            <div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_" title="Delete"></i></a></div>
                          </div>
                          <input type="hidden" id="old_logo" name="old_logo" value="<?php echo $logo; ?>" >
                          <input type="hidden" id="del_old_logo" name="del_old_logo" value="" >
                          <?php }else{ ?>
                          <span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>Upload Photo</span>
                          <input type="file" id="logo" name="logo">
                          </span>
                          <?php } ?>
                        </div>
                      </div>
			<div class="col-xs-12 col-md-9">
              <div class="form-group" style="float:left;">
              
              <div class="col-xs-12 col-md-6  padding-left-0">
                <label for="inputPassword3" >First Name</label>
                <div class="input-group">
                  <input type="text" value="<?php echo $fname; ?>" id="fname" name="fname" placeholder="" class="form-control wd" >
                </div>
                </div>
                
                <div class="col-xs-12 col-md-6  padding-left-0  padding-right-0">
                <label for="inputPassword3" >Last Name</label>
                <div class="input-group">
                  <input type="text" value="<?php echo $lname; ?>" id="lname" name="lname" placeholder="" class="form-control wd" >
                </div>
              </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3">Email Address</label>
                <div class="input-group">
                  <input type="text" value="<?php echo $email; ?>" name="email" placeholder="" class="form-control wd" id="email">
                </div>
              </div>
                                                                                                                    
											<div class="form-group">
                <label for="inputPassword3">Username</label>
                <div class="input-group">
                  <input type="text" value="<?php echo $username; ?>" name="username" placeholder="" class="form-control wd" id="username" disabled>
                </div>
	              </div>
														
											<?php
                                                                                      if($_SESSION['role']=='admin'){ 
											/**
												* Timezones list with GMT offset
												*
												* @return array
												* @link http://stackoverflow.com/a/9328760
												*/
											function tz_list() {
													$zones_array = array();
													$timestamp = time();
													foreach(timezone_identifiers_list() as $key => $zone) {
															date_default_timezone_set($zone);
															$zones_array[$key]['zone'] = $zone;
															$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
													}
													return $zones_array;
											}
											?>						
										
										<div class="form-group">
                <label for="inputPassword3">Time Zone</label>
                <div class="input-group">
                  
			<select name="timezone" class="form-control wd time_zoon">
    <option value="0">Please, select timezone</option>
    <?php foreach(tz_list() as $t) { ?>
      <option <?php if($timezone==$t['zone']){ ?>selected="selected"<?php } ?> value="<?php print $t['zone'] ?>">
        <?php print $t['diff_from_GMT'] . ' - ' . $t['zone'] ?>
      </option>
    <?php } ?>
  </select>
																		
                </div>
              </div>                                                                                                                    
                                                                                      <?php } ?>
              </div>
            </div>


          </div>
          
          <!-- Right Part -->
          
          <div class="col-xs-12 col-md-6">
            <h3>Password</h3>
            <div class="form-group">
              <label for="inputPassword3">Current Password</label>
              <div class="input-group">
                <input type="password" value="" name="password" placeholder="" class="form-control wd" id="password" autocomplete="off">
              </div>
            </div>
            <div class="form-group">
              <label for="inputPassword3" >New Password</label>
              <div class="input-group">
                <input type="password" value="" name="new_password"  placeholder="" class="form-control wd" id="new_password" autocomplete="off">
              </div>
            </div>
            <div class="form-group">
              <label for="inputPassword3" >Password Confirmation</label>
              <div class="input-group">
                <input type="password" value="" name="conf_new_password"  placeholder="" class="form-control wd" id="conf_new_password" autocomplete="off">
              </div>
            </div>
            <div class="cl height5"></div>
            <div class="cl height30"></div>
          </div>
          
          <div class="cl"></div>
        </form>
      </div>
    </div>
    <div class="cl height30"></div>
  </section>
  
  <!-- /.content --> 
</aside>
<!-- /.right-side -->
</div>
<!-- ./wrapper -->
<?php include(INC."footer.php"); ?>
<script src="js/setting-hover.js"></script> 
<script type="text/javascript">  
$(document).ready(function() {
    if (Modernizr.touch) {
        // show the close overlay button
        $(".close-overlay").removeClass("hidden");
        // handle the adding of hover class when clicked
        $(".img").click(function(e) {
            if (!$(this).hasClass("hover")) {
                $(this).addClass("hover");
            }
        });
        // handle the closing of the overlay
        $(".close-overlay").click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(this).closest(".img").hasClass("hover")) {
                $(this).closest(".img").removeClass("hover");
            }
        });
    } else {
        // handle the mouseenter functionality
        $(".img").mouseenter(function() {
            $(this).addClass("hover");
        })
        // handle the mouseleave functionality
        .mouseleave(function() {
            $(this).removeClass("hover");
        });
    }
});
</script> 
<script type="text/javascript">
function remove_logo(id) {
    var file_input_id = '';
    if (id == 'remove_preview_img') {
        file_input_id = 'logo';
    } else if (id == 'remove_preview_company_img') {
        file_input_id = 'company_logo';
    }
    $("#" + id).remove();
    reset_field($('#' + file_input_id));
}

$(function() {

    $("#logo").change(function() {
        var iclass = 'mainlogo';
        var imageType = /image.*/;
        var fileInput = document.getElementById("logo");
        var file = fileInput.files[0];
        if (file.type.match(imageType)) {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_img"  onclick="remove_logo(this.id)"><div class="center-img"><img src="' + e.target.result + '" height="" width="" ></div><div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div></div>');
                    $('#app_type_error').remove();
                }
                reader.readAsDataURL(this.files[0]);
            }
        } else {
            reset_field($('#logo'));
            $('#app_type_error').remove();
            $('.' + iclass).after('<div class="error_msg" id="app_type_error">File not supported!</div>');
        }


    });

    $("#company_logo").change(function() {

        var iclass = 'comp';
        var imageType = /image.*/;
        var fileInput = document.getElementById("company_logo");
        var file = fileInput.files[0];
        if (file.type.match(imageType)) {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.' + iclass).children('span').after('<div class="preview_small_img" id="remove_preview_company_img" onclick="remove_logo(this.id)"><div class="center-img"><img src="' + e.target.result + '" height="" width="" ></div><div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div></div>');
                    $('#app_type_error').remove();
                }
                reader.readAsDataURL(this.files[0]);
            }
        } else {
            reset_field($('#company_logo'));
            $('#app_type_error').remove();
            $('.' + iclass).after('<div class="error_msg" id="app_type_error">File not supported!</div>');
        }


    });


    $('#remove_comp_logo').on('click', function() {
        $.prompt("", {
            title: "Are you sure you want to delete this company logo?",
            buttons: {
                "Yes": true,
                "Cancel": false
            },
            submit: function(e, v, m, f) {

                if (v == false) {} else {
                    var type = 'company';
                    var data = "ajax=1&action=delete_image&intid=<?php echo $agent_cust_id; ?>&type=" + type;
                    $("#overlays").show();
                    request = $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {

                        },
                        success: function(data) {
                            var img = $('#old_company_logo').val();
                            $('#del_old_company_logo').val(img);
                            $('#remove_comp_logo').remove();
                            $('#remove_preview_company_img').remove();
                            $("#overlays").hide();
                        }

                    });
                }
            }

        });
    });

    $('#remove_logo').on('click', function() {
        $.prompt("", {
            title: "Are you sure you want to delete this profie logo?",
            buttons: {
                "Yes": true,
                "Cancel": false
            },
            submit: function(e, v, m, f) {

                if (v == false) {} else {
                    var type = 'user';
                    var data = "ajax=1&action=delete_image&intid=<?php echo $agent_cust_id; ?>&type=" + type;
                    $("#overlays").show();
                    request = $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {

                        },
                        success: function(data) {
                            var img = $('#old_logo').val();
                            $('#del_old_logo').val(img);
                            $('#remove_logo').remove();
                            $('#remove_preview_img').remove();
                            $("#overlays").hide();
                        }

                    });
                }

            }

        });

    });

    $('#account_settings').validate({
        onkeyup: function(element) {
            $(element).valid()
        },
        rules: {
            name: "required",
            email: {
                required: true,
                email: true
            },
            password: {
                minlength: 5
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
            email: ""
        }
    });

    $('#quick_support').validate({
        onkeyup: function(element) {
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

    </script> 
<script type="text/javascript" src="js/process.js"></script>
</body></html>