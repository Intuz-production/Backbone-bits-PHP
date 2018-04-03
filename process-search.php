<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php

require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];
foreach ($_REQUEST['columnName'] as $value) {
    //$columns .= $value . ', ';
    $columnlike .= $value . " Like '%" . $_REQUEST['keyword'] . "%' OR ";
}

$finalcolumnslike = substr($columnlike, 0, -3);

if ($_REQUEST['tablename'] == 'tblmember') {

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'];


    if (!empty($_REQUEST['keyword'])) {

        $chk1 = $dclass->select('*', $_REQUEST['tablename'], ' AND (' . $finalcolumnslike . ') AND parent_id='.$_SESSION['custid'].'   ');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        $chk = $dclass->select('*', $_REQUEST['tablename'], ' AND (' . $finalcolumnslike . ') AND parent_id='.$_SESSION['custid'].'  LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    } else {
        $chk1 = $dclass->select('*', $_REQUEST['tablename'], '  AND parent_id='.$_SESSION['custid'].' ');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");

        $chk = $dclass->select('*', $_REQUEST['tablename'], ' AND parent_id='.$_SESSION['custid'].'  LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    }

    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if(empty($chk)){
           $html .= "<tr><td colspan='5' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
        foreach ($chk as $key => $value) {

            if($value['logo'] != '' && is_file(USER_LOGO."/".$value['logo'])) {
         $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=".USER_LOGO."/".$value['logo'];
   }else{
       $logo_path = "img/s_user_img.png";
   }
            
            
            $html .= "<tr class='item slimScrollDiv' id=".$value['intid'].">
                <td class='agent_img'><img src='". $logo_path ."' alt='' height='80' width='80' /></td>
                <td>";
            $html .= '<div class="tital">'.$value['fname'] . " " . $value['lname'].'</div> <div class="role_s">'.ucfirst($value['role']).'</div>';
            $html .= "</td>";
            $html .= "<td>";
            $html .= $value['username'];
            $html .= "</td>
                <td>";
            $html .= $value['email'];
            $html .= "</td>
               
                <td>";
            if ($value['status'] == 'active') {
                $html .= '<span class="active_icon" title="Active"><i class="fa active"></i></span>';
            }else if($value['status'] == 'waiting'){
                $html .= '<span class="waiting_icon" title="Active"><i class="fa active"></i></span>';
            } else {
                $html .= '<span class="inactive_icon" title="Inactive"></span>';
            }
            $html .= "</td>";
            $html .= '
                
<td class="print-">
<div class="generate"> 
	
	<a onclick="editrow('.$value['intid'].');" href="javascript:;" class="print trigger" data-dialog="somedialog" >
	<i class="apps_i edit_icon_"  title=""></i>
                      <div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Edit</div>
                      </div>
                      </a> 
	 </div></td>';


            $html .= " </tr>";
            
$html .= '<tr id="display_'.$value['intid'].'"  style="display:none">
    <td colspan="7">
    
    <form id="account_settings_'.$value['intid'].'" method="POST" action=""  enctype="multipart/form-data">
      <input type="hidden" name="intid" id="intid" value="'. $value['intid'].'" >
      <input type="hidden" name="script" id="script" value="'. "edit".'" >
          <input type="hidden" name="role" id="role_'.$value['intid'].'" value="'.$value['role'].'" >
        <table style="width:100%;">
            <tr>
                <td  class="invoice__">';
  
  if($value['logo'] != '' && is_file(USER_LOGO."/".$value['logo'])) {
         $logo_path = "memimages.php?max_width=125&max_width=125&imgfile=".USER_LOGO."/".$value['logo'];
         $attra = 'onclick="getidimage('.$value['intid'].')"';
   }else{
       $logo_path = "";
       $attra = '';
						
   }
			
  
  $html .=  '
      <div id="file_logo" '.$attra.' class="mainlogo_'.$value['intid'].'">';
        if($logo_path != ''){ 
        $html .= '<span class="btn add-files fileinput-button"><img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>LOGO</span>
        <input type="file" id="logo_'.$value['intid'].'" name="logo">
        </span>
        <div class="upload_img" id="remove_logo_'.$value['intid'].'"> <div class="center-img"><img src="'. $logo_path.'" alt="" height="80" width="80" /></div>
          <div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div>
        </div>
        <input type="hidden" id="old_logo_'.$value['intid'].'" name="old_logo" value="'. $logo_path.'" >
        <input type="hidden" id="del_old_logo_'.$value['intid'].'" name="del_old_logo" value="" >';
         }else{ 
        $html .= '<span class="btn add-files fileinput-button"> <img class="add-logo_plus" src="img/upload_icon.png" alt=""> <span>LOGO</span>
            <!--<div class="upload_img" id="remove_logo_'.$value['intid'].'"> <img src="img/s_user_img.png" alt="" height="80" width="80" /></div>-->
        <input type="file" id="logo_'.$value['intid'].'" name="logo">
        </span>';
         } 
      $html .= '
          
</div>
</td>


    <td class="name-role">
    <!--<label for="inputPassword3" class="col-sm-12 control-label padding0">Name</label>-->
            <div class="col-sm-12 padding0">
              <input type="text" value="'. $value['fname'].'" id="fname" name="fname" placeholder="" class="form-control wd-50" >
                  <input type="text" value="'. $value['lname'].'" id="lname" name="lname" placeholder="" class="form-control wd-50" >
            </div>
			<div class="cl"></div>
			<div class="sliderWrapperFont" style="background-color: inherit;display: block;width: 85% !important;">
             <div id="select_role_slider_'.$value['intid'].'" style="margin: 0 !important;max-width: inherit !important; padding: 0 !important;">';
           $html .= $gnrl->role_select_slide($value['role'], $value['intid']);
           $html .= '</div>
        </div>
			
</td>

<td class="username_box">
<!--<label for="inputPassword3" class="col-sm-12 control-label padding0">User Name</label>-->
            <div class="col-sm-12 padding0">';
             $html .= '<input type="text" readonly value="'. $value['username'].'" id="username" name="username" placeholder="" class="form-control wd" >
            </div>
</td>

<td class="email_box">

<!--<label for="inputPassword3" class="col-sm-12 control-label padding0">Email Address</label>-->
            <div class="col-sm-12 padding0">';
             
             $html.= '<input type="text" value="'. $value['email'].'" name="email" placeholder="" class="form-control wd" id="email">
            </div>
			
</td>

<td class="status_box">';
          if($value['status']!='waiting'){   
$html .= '<div class="fl">

              <label class="i-switchs  i-switch-mds i-switch-mds-horizontal">
                <input id="status_'.$value['intid'].'" name="status" type="checkbox" value="active" ';
            if ($value['status'] == 'active') 
            {
                $html .= ' checked '; 
            }
                    $html .= '>
                <i></i> </label>
            </div>';
          }else{
              $html .= '<div class="waiting_icon"><input name="status" type="hidden" value="waiting" /></div>';
          }
$html .= '</td>
';
               
           $html .= '
    
    

               <td class="edit_box updata_">

<button name="save_settings" onclick="hideeditrow('.$value['intid'].');" value="Cancel" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary save_all agen_cancel ">
<div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Go Back</div>
                      </div>
</button>
<a href="javascript:;"  onclick="delete_agent(' . $value['intid'] . ');" class="pdf" id="hidedeletedata_' . $value['intid'] . '">
	<i class="apps_i remove_icon_" title="" ></i>
                      <div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Delete</div>
                      </div>
                      </a>
                      <button name="save_settings" onclick="update(\'account_settings_'.$value['intid'].'\',\'tblmember\')" value="Send" type="button" style=" margin:0 0px 0 0;" class="btn btn-primary agen_save save_all ">
<div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Save</div>
                      </div>

</button>
</td>

            </tr>
			
			
        </table>
        </form>
    </td>       
</tr>

<script type="text/javascript">
$(document).ready(function(){
  $(\'input\').iCheck({
    checkboxClass: \'icheckbox_square\',
    radioClass: \'iradio_square\',
    increaseArea: \'50%\' // optional
  });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
  $(\'input\').iCheck({
    checkboxClass: \'icheckbox_square-red\',
    radioClass: \'iradio_square-red\',
    increaseArea: \'50%\' // optional
  });
});
</script>
<script>

$(document).ready(function(){
    if (Modernizr.touch) {
        // show the close overlay button
        $(".close-overlay").removeClass("hidden");
        // handle the adding of hover class when clicked
        $(".img").click(function(e){
            if (!$(this).hasClass("hover")) {
                $(this).addClass("hover");
            }
        });
        // handle the closing of the overlay
        $(".close-overlay").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            if ($(this).closest(".img").hasClass("hover")) {
                $(this).closest(".img").removeClass("hover");
            }
        });
    } else {
        // handle the mouseenter functionality
        $(".img").mouseenter(function(){
            $(this).addClass("hover");
        })
        // handle the mouseleave functionality
        .mouseleave(function(){
            $(this).removeClass("hover");
        });
    }
				
				
});

function select_role(id,value){
               //alert(id);
               var split = id.split(\'_\');
               //alert(split[2]);
                $(\'#select_role_slider_\'+split[2]).children(\'div\').removeClass(\'active\');
                $(\'#\'+id).addClass(\'active\');
                $(\'#role_\'+split[2]).val(value);
    } 

$("#account_settings_'.$value['intid'].'").validate({
               onkeyup: function(element) {$(element).valid()},
                    rules: {
                        name: "required",
                        username: "required",        
                        email : {
                                required : true ,
                                email: true        
                        },
                       new_password_i: {
                                minlength : 5
                        },
                       
                       
                    },
                    messages: {
                        name: "",
                        email: "",
                        username: ""
                    }
           });
           



$("#logo_'.$value['intid'].'").change(function (){
    //alert(this);
var iclass = \'mainlogo_'.$value['intid'].'\';
var imageType = /image.*/;    
var fileInput = document.getElementById("logo_'.$value['intid'].'");
var file = fileInput.files[0]; 
if (file.type.match(imageType)) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(\'.\'+iclass).children(\'span\').after(\'<div class="preview_small_img" id="remove_preview_img_'.$value['intid'].'"><div class="center-img"><img src="\'+e.target.result+\'" height="118" width="118" ></div><div class="over_img"><a href="javascript:;"><i class="apps_i remove_icon_"></i></a></div></div>\');
                $("#app_type_error").remove();
                $(\'.\'+iclass).attr("onclick","getidimage_preview('.$value['intid'].')");
           }
                reader.readAsDataURL(this.files[0]);
          
        }
 }else{
    reset_field($("#logo_'.$value['intid'].'"));
    $("#app_type_error").remove();
    $("."+iclass).after(\'<div class="error_msg" id="app_type_error">File not supported!</div>\');
 }
});
';

if($value['status']=='active'){
    $html .= '
        $("#status_'.$value['intid'].'").iCheck(\'check\');';
}else{
    $html .= '
        $("#status_'.$value['intid'].'").iCheck(\'uncheck\');';
}
$html .= '
    
$(window).load(function(){
       
                 var fslider = $(\'#select_role_slider_'.$value['intid'].'\').bxSlider({
                               slideWidth:135,
                               minSlides: 5,
                               maxSlides: 9,
                               moveSlides: 1,
                               startSlide: parseInt(0),
                               slideMargin:10,
                               infiniteLoop: false,
                               hideControlOnEnd: true,
                               pager: false
                  });
              
                    $(".sliderWrapperFont").show(0, "swing", function(){fslider.reloadSlider();});
         
        });




</script>

';

            }
       }
        $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
}
else if ($_REQUEST['tablename'] == 'tbltransactions') { //INVOICE

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'];


    if (!empty($_REQUEST['keyword'])) {

        $chk1 = $dclass->select('t.*,p.pname as package_name,p.padditional_limit,p.pintval', "tbltransactions t inner join tblpackages p on t.package_id=p.intid", ' AND t.member_id=\''.$member_id.'\' order by t.intid desc');
        

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        
        $chk = $dclass->select('t.*,p.pname as package_name,p.padditional_limit,p.pintval', "tbltransactions t inner join tblpackages p on t.package_id=p.intid", ' AND t.member_id=\''.$member_id.'\' AND (' . $finalcolumnslike . ') order by t.intid desc  LIMIT '.$_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
        
    } else {
        
        
        
        
        $chk1 = $dclass->select('t.*,p.pname as package_name,p.padditional_limit,p.pintval', "tbltransactions t inner join tblpackages p on t.package_id=p.intid", ' AND t.member_id=\''.$member_id.'\' order by t.intid desc ');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");

        $chk = $dclass->select('t.*,p.pname as package_name,p.padditional_limit,p.pintval', "tbltransactions t inner join tblpackages p on t.package_id=p.intid", ' AND t.member_id=\''.$member_id.'\' order by t.intid desc  LIMIT '.$_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
    }

    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if(empty($chk)){
           $html .= "<tr><td colspan='5' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
        foreach ($chk as $key => $value) {

            if($value['period'] != ''){
                        $p = explode("|",$value['period']);
                       $from_year = date("Y",strtotime($p[0]));
                        $to_year = date("Y",strtotime($p[1]));
                        if($from_year != date("Y") || $to_year != date("Y")){
                              $from_period = date("M d, Y",strtotime($p[0]));
                              if(!empty($p[1])){
                              $to_period = ' <span>to</span> '.date("M d, Y",strtotime($p[1]));
                              }else{
                                  $to_period = '';
                              }
                        }else{
                              $from_period = date("M d",strtotime($p[0]));
                              
                              if(!empty($p[1])){
                              $to_period = ' <span>to</span> '.date("M d",strtotime($p[1]));  
                              }else{
                                  $to_period = '';
                              }
                              
                        }
                        
                    }
            
                    
            if($value['pintval'] == 'monthly')
                $intval = 'Month';
            else if($value['pintval'] == 'yearly')
                $intval = 'Year';
            
            $html .= "<tr class='item'>
                <td class='invoice-id'>";
            $html .= $value['invoice_id'];
            $html .= "</td>";
            $html .= "<td class='plan'>";
            $html .= $value['package_name'];
            $html .= "</td>
                <td class='period'>";
             if($value['package_id'] != 1){ 
                 $html .=  $from_period.$to_period;
                 }
                 else {
                     $html .=  'Till <strong>'.$value['padditional_limit'].'</strong> Actions';
                 }
            $html .= "</td>
                <td class='p-date'>";
            $html .= date("M d, Y",strtotime($value['dtadd']));
            $html .= "</td>";
            $html .= '<td class="total">
                        <span class="ret">'.
                            CUR.''.$gnrl->get_number_format(@$value['amount'],0,'.',',').'</span>';
            if($value['package_id'] != 1){ 
                    $html .= '<span class="month">';
            }
                            $html .= '</span></td>
                  <td class="print-"><div class="generate"> <a href="javascript:;" class="print trigger" data-dialog="somedialog" onclick="print_invoice('.$value['intid'].')"><i class="apps_i print"  title=""></i>
                      <div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Print</div>
                      </div>
                      </a> <a target="_blank" href="tcpdf/generate?id='. $value['intid'].'" class="pdf"><i class="apps_i pdf" title="" ></i>
                      <div class="popover top">
                        <div class="arrow"><!--<i class="fa fa-fw fa-caret-up"></i>--></div>
                        <div class="pdf-file">Generate PDF</div>
                      </div>
                      </a> </div></td>';

            $html .= " </tr>";
        }
       }
        $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
}




/****************************************************************/
			/******** Helpr FAQ ************/
/****************************************************************/


else if ($_REQUEST['tablename'] == 'tblapp_faq' && $_REQUEST['type'] == 'help') {

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'].'&app_id=' . $_REQUEST['app_id'];
    
    
  
    
    if (!empty($_REQUEST['keyword'])) {
        
       
        $keyword = mysql_real_escape_string(trim($_REQUEST['keyword']));
        $wh.= " AND (s.more_app_name like '%$keyword%')";



      $ssql0 = "SELECT * FROM tblapp_faq  WHERE 1  AND app_id='".$_REQUEST['app_id']."' AND ver_id = '".$_REQUEST['ver_id']."'  AND is_canned= 'N' $wh  ORDER BY intorder ASC";      
               $restepm0 = $dclass->query($ssql0);
                                 $nototal = $dclass->numRows($ssql0);
               $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");

      $ssql = "SELECT * FROM tblapp_faq  WHERE 1  AND app_id='".$_REQUEST['app_id']."' AND ver_id = '".$_REQUEST['ver_id']."' AND is_canned= 'N' $wh  ORDER BY intorder ASC Limit ".$_REQUEST['limitstart'].", ".$_REQUEST['limit']." ";  

        $sqltepm = $ssql;

        $restepm = $dclass->query($sqltepm);
        
    } else {
      
         $ssql0 = "SELECT * FROM tblapp_faq  WHERE 1 AND app_id='".$_REQUEST['app_id']."' AND ver_id = '".$_REQUEST['ver_id']."' AND is_canned= 'N' $wh  ORDER BY intorder ASC";    
        $restepm0 = $dclass->query($ssql0);
                          $nototal = $dclass->numRows($ssql0);
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        $ssql = "SELECT * FROM tblapp_faq  WHERE 1 AND app_id='".$_REQUEST['app_id']."' AND ver_id = '".$_REQUEST['ver_id']."' AND is_canned= 'N' $wh  ORDER BY intorder ASC Limit ".$_REQUEST['limitstart'].", ".$_REQUEST['limit']." ";  

        $sqltepm = $ssql;

     $restepm = $dclass->query($sqltepm);
                            
        
     
     
    }
 $status_record1 = "SELECT record_status FROM tblapp_tutorial_settings  WHERE 1 AND intid = '".$_REQUEST['ver_id']."'";
     $status_record = $dclass->query($status_record1);
     $res_record_stat = $dclass->fetchArray($status_record);
     
    
    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if($nototal==0){
           $html .= "<tr><td colspan='5' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
        $num=$_REQUEST['limitstart'];
        
        if($res_record_stat['record_status']!='running'){
            
            $style_dis = 'display:none';
        }
           while ($row = $dclass->fetchArray($restepm)) {
                    $num++;
                    $sr = $num + $_REQUEST['limitstart'];

                    if ($num % 2 == 0)
                        $class = 'event';
                    else
                        $class = 'odd';
                    
                    if($row['status'] == 'active')
                        $status_class = 'active_icon';
                    else {
                        $status_class = 'inactive_icon';
                    }
                  

           $html .= '<tr id="faq-list-'. $row['intid'] .'" class="tr-edit item">
		     
                         
		   <td class="faq_td_list">';
                           
           $html .='<h3> <span id="record_button_'.$row['intid'].'" class="'.$status_class.'" title="'.ucfirst($row['status']).'"></span>&nbsp;<span id="question_change_'.$row['intid'].'">'.$row['question'].'</span><div class="generate fr"> <a id="copy-dynamic_'.$row['intid'].'" href="javascript:;" onclick="copy_description('.$row['intid'].')"  style="visibility:hidden;" class="print copy_d"> <i title="" class="apps_i copy_i"></i>;
                        <div class="popover top">
                          <div class="arrow"></div>
                          <div class="pdf-file">Copy Description</div>
                        </div>
                        </a> <a href="javascript:;" onclick="inlineedit('.$row['intid'].')"  class="pdf preview_m" style="'.$style_dis.'"> <i title="" class="apps_i edit_icon"></i>
                        <div class="popover top">
                          <div class="arrow"></div>
                          <div class="pdf-file">Edit</div>
                        </div>
                        </a> 
                        <!---<a href="javascript:;" onclick="delete_faq('.$row['intid'].');" class="pdf delete_c"> <i title="" class="apps_i remove_icon"></i>
                        <div class="popover top">
                          <div class="arrow"></div>
                          <div class="pdf-file">Delete</div>
                        </div>
                        </a> -->
                        </div>
                        </h3>'; // Question and buttons
              $html .= '<p id="answer_change_'.$row['intid'].'">'.$row['answer'].'</p>'; //Answer
              $html .= '</td>';
              
              $html .= ' <td class="faq_td_edit"><form class="helpr-form" method="post" id="helpreditform_'.$row['intid'].'">
                              <input type="hidden"  name="intid" value="'.$row['intid'].'" />
                             <input type="hidden"  name="script" value="update" />
                             <input type="hidden"  name="action" value="update_help_faq" />
                            <input type="hidden" name="ajax" value="1" /><h3>
                    <div class="col-xs-12 col-md-3 padding0 fl status-i">
                      <dd class="fl status-text">Status</dd>
                      
                    </div>
                    <div class="generate fr inline_eidt">
																				<label class="i-switchs i-switch-mds i-switch-mds-horizontal">
                        <input id="status'.$row['intid'].'" name="status" type="checkbox" value="active"';
           if ($row['status'] == 'active') 
            {
                $html .= ' checked '; 
            }
                    $html .= '>';
             $html .=  '<i></i> 
													
													<div class="popover top">
                        <div class="arrow"></div>
                        <div class="pdf-file">Status</div>
                      </div>
													</label>
													
																				 <a id="copy-dynamic_'.$row['intid'].'" href="javascript:;" onclick="copy_description('.$row['intid'].')"  class="print copy_d"> <i title="" class="apps_i copy_i"></i>
                      <div class="popover top">
                        <div class="arrow"></div>
                        <div class="pdf-file">Copy Description</div>
                      </div>
                      </a> <a href="javascript:;" onclick="delete_faq('.$row['intid'].');" class="pdf delete_c"> <i title="" class="apps_i remove_icon"></i>
                      <div class="popover top">
                        <div class="arrow"></div>
                        <div class="pdf-file">Delete</div>
                      </div>
                      </a> 
																						
																						<a href="javascript:;" onclick="update_helpr_faq('.$row['intid'].')" class="pdf preview_m"> <i title="" class="apps_i edit_icon"></i>
                      <div class="popover top">
                        <div class="arrow"></div>
                        <div class="pdf-file">Save</div>
                      </div>
                      </a> 
																						</div>
                  </h3>
                  <div class="cl height10"></div>
                  <div class="form-group">
                    <input type="Title" class="form-control faq_textbox" value="'.$row['question'].'"  id="question_'.$row['intid'].'" name="question" placeholder="Enter Question Here">
                  </div>
                  <div class="form-group">
                    <textarea id="answer_'.$row['intid'].'" name="answer" class="faq_textarea" placeholder="Enter Answer Here">'.$row['answer'].' </textarea>
                  </div></form>
                </td>';
              
              $html .= '</tr>';
              
              $html .= '
                  
<script>
                    
              $("#answer_'.$row['intid'].'").wysihtml5({
                                            "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
                                            "emphasis": true, //Italics, bold, etc. Default true
                                            "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                                            "html": false, //Button which allows you to edit the generated HTML. Default false
                                            "link": false, //Button to insert a link. Default true
                                            "image": false, //Button to insert an image. Default true,
                                            "color": false //Button to change color of font  
            });

                        $("#helpreditform_'.$row['intid'].'").validate({
                               onkeyup: function(element) {$(element).valid()},
                                    rules: {
                                        question: "required",
                                        answer: "required"

                                    },
                                    messages: {
                                        question: "",
                                        answer: "",
                                    }
                           });
                        </script>';
              
       }
        
       }
        
    $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
    
    
}
else if ($_REQUEST['tablename'] == 'tblapp_tutorial_settings') {

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'];
    $app_id = $_REQUEST['app_id'];

    if (!empty($_REQUEST['keyword'])) {

        $chk1 = $dclass->select("s.*,v.video,v.video_name,v.video_type","tblapp_tutorial_settings s LEFT JOIN tblapp_tutorial_videos v ON s.intid=v.ver_id "," AND s.app_id='".$app_id."' AND  s.record_status='old' ORDER BY s.record_status DESC");


        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
       
        $chk = $dclass->select("s.*,v.video,v.video_name,v.video_type","tblapp_tutorial_settings s LEFT JOIN tblapp_tutorial_videos v ON s.intid=v.ver_id "," AND s.app_id='".$app_id."' AND (" .$finalcolumnslike . ")  AND s.record_status='old' ORDER BY s.record_status DESC  LIMIT ".$_REQUEST['limitstart'] . "," . $_REQUEST['limit']);
        
    } else {
        
        $chk1 = $dclass->select("s.*,v.video,v.video_name,v.video_type","tblapp_tutorial_settings s LEFT JOIN tblapp_tutorial_videos v ON s.intid=v.ver_id "," AND s.app_id='".$app_id."' AND  s.record_status='old' ORDER BY s.record_status DESC");

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        $chk = $dclass->select("s.*,v.video,v.video_name,v.video_type","tblapp_tutorial_settings s LEFT JOIN tblapp_tutorial_videos v ON s.intid=v.ver_id "," AND s.app_id='".$app_id."'  AND s.record_status='old' ORDER BY s.record_status DESC  LIMIT ".$_REQUEST['limitstart'] . "," . $_REQUEST['limit']);
    }
    
    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if(empty($chk)){
           $html .= "<tr><td colspan='6' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
        foreach ($chk as $key => $value) {

            $imgch = $dclass->select("title,image","tblapp_tutorial_images"," AND ver_id='".$value['intid']."'");
           
           
         
            $html .= "<tr class='item'>
                <td class='upgradr-id'>";
            $html .= $value['version'];
            $html .= "</td>";
            $html .= "<td class='plan'>";
            if($value['video']!= ''){ 
                $html .= '&nbsp;<a class="btn view_video" data-target="#helpr_video_'.$value['intid'].'"  href="help-img-video?sel_app_id='.$_REQUEST['app_id'].'&video_id='.$value['intid'].'&old">View</a>';
 
            }
            $html .= '</td><td class="title">
                <a class="btn view_video" data-target="#helpr_video_'.$value['intid'].'"  href="help-img-video?sel_app_id='.$_REQUEST['app_id'].'&video_id='.$value['intid'].'&old&sel=img">View</a>
';
           
            $html .= "</td>";
            
            $html .= '<td class="title">
                <a class="btn view_video" data-target="#helpr_video_'.$value['intid'].'"  href="help-img-video?sel_app_id='.$_REQUEST['app_id'].'&video_id='.$value['intid'].'&old&sel=faq">View</a>
';

           
            $html .= "</td>";
            
            $html .= '<td class="print-"><div class="generate"> ';
            
	  $html	.= '<a class="pdf delete_c" href="javascript:;"  onclick="delete_archive('.$value['intid'].')" >
					<i class="apps_i remove_icon" title=""></i>
                    <div class="popover top">
                      <div class="arrow"><i class="fa fa-fw fa-caret-up"></i></div>
                      <div class="pdf-file" >Delete</div>
                    </div>
                    </a> </div>
                    </td> ';
            
            $html .= " </tr>";
        }
       }
        $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
}


/****************************************************************/
			/******** Canned Response ************/
/****************************************************************/
else if ($_REQUEST['tablename'] == 'tblapp_faq') {

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'].'&app_id=' . $_REQUEST['app_id'];


    if($member_id==$_SESSION['agents_cust_id']){
        $append_data = '';
    }else{
        $append_data = 'AND member_id = \''.$_SESSION['agents_cust_id'].'\'';
    }
    
    
    if (!empty($_REQUEST['keyword'])) {

        $chk1 = $dclass->select('*', $_REQUEST['tablename'], ' AND (' . $finalcolumnslike . ') '.$append_data.' AND app_id=\''.$_REQUEST['app_id'].'\' AND is_canned=\'Y\' ');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        $chk = $dclass->select('*', $_REQUEST['tablename'], ' AND (' . $finalcolumnslike . ') '.$append_data.' AND app_id='.$_REQUEST['app_id'].' AND is_canned=\'Y\' LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    } else {
        $chk1 =  $dclass->select('*', $_REQUEST['tablename'], ' '.$append_data.' AND app_id='.$_REQUEST['app_id'].' AND is_canned=\'Y\' ');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");

        $chk = $dclass->select('*', $_REQUEST['tablename'], ' '.$append_data.' AND app_id='.$_REQUEST['app_id'].' AND is_canned=\'Y\' LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    }

    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if(empty($chk)){
           $html .= "<tr class='item'><td colspan='5' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
        foreach ($chk as $key => $value) {

            $html .= '<tr class="item">
                <td> 
                <div class="dragitems">
            
            <ul id="allfields" runat="server">';
            
if(strlen($value['question'])<=30)
  {
    $question_value = $value['question'];
  }
  else
  {
    $y=substr($value['question'],0,30) . '...';
    $question_value = $y;
  }  
  
  if(strlen($value['answer'])<=30)
  {
    $answer_value = $value['answer'];
  }
  else
  {
    $y=substr($value['answer'],0,30) . '...';
    $answer_value = $y;
  }  
            
                $html .= '<p><b><div class="canned_reponse_right" onclick="showdetail('. $value['intid'].')" id="'. $value['intid'].'"> '.$value['question'].' </div></b></p>';
                
                
                $html .= '<div class="drag_desc canned_reponse_right"   onclick="showdetail('. $value['intid'].')" id="node'. $value['intid'].'"> '.$value['answer'].'</div><div style="width:20px;float:left"><a href="javascript:;" onclick="showdetail('. $value['intid'].')" >
<i class="fa fa-fw fa-angle-right"></i></a>
                    </div>';
                
                
            $html .= '</ul>
        </div>
        </td>
        </tr>';
            $html .= '<script>
                    $(function() {
            $(".drag_desc").draggable({
                appendTo: "body",
                helper: "clone",
                cursor: "move",
                revert: "invalid"
            });
            initDroppable($("#message"));
            function initDroppable($elements) {
                $elements.droppable({
                    hoverClass: "textarea",
                    accept: ":not(.ui-sortable-helper)",
                    drop: function(event, ui) {
                        var $this = $(this);
                        $(\'#send_data\').attr(\'disabled\',false);
         $(\'.send_canned_and_save\').attr(\'disabled\',false);
         $(\'#send_close_data\').attr(\'disabled\',false);
                        var tempid = ui.draggable.text();
                        var dropText;
                        dropText =  tempid;
                        var droparea = document.getElementById(\'message\');
                        var range1 = droparea.selectionStart;
                        var range2 = droparea.selectionEnd;
                        var val = droparea.value;
                        var str1 = val.substring(0, range1);
                        var str3 = val.substring(range1, val.length);
                        droparea.value = str1 + dropText + str3;
                    }
                });
            }
        });
                    </script>';

            }
       }
        $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
}else if ($_REQUEST['tablename'] == 'tbl_access_log') {

    $url = "process-search";
    $str = 'keyword=' . $_REQUEST['keyword'] . '&tablename=' . $_REQUEST['tablename'] . '&limit=' . $_REQUEST['limit'];


    if (!empty($_REQUEST['keyword'])) {

        $chk1 = $dclass->select('agent_id, DATE(login_time) AS startdate, SUM(TIME_TO_SEC(TIMEDIFF(logout_time,login_time))) AS timediff', 'tbl_checkin_checkout', ' AND session_id = \'N\' AND parent_id = "'.$_SESSION['custid'].'" GROUP BY startdate,agent_id ORDER BY startdate DESC');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");
        
        $chk = $dclass->select('agent_id, DATE(login_time) AS startdate, SUM(TIME_TO_SEC(TIMEDIFF(logout_time,login_time))) AS timediff', 'tbl_checkin_checkout', ' AND session_id = \'N\' AND parent_id = "'.$_SESSION['custid'].'" GROUP BY startdate,agent_id ORDER BY startdate DESC  LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    } else {
        $chk1 = $dclass->select('agent_id, DATE(login_time) AS startdate, SUM(TIME_TO_SEC(TIMEDIFF(logout_time,login_time))) AS timediff', 'tbl_checkin_checkout', '  AND session_id = \'N\' AND parent_id = "'.$_SESSION['custid'].'" GROUP BY startdate,agent_id ORDER BY startdate DESC');

        $nototal = count($chk1);
        
        $pagen = new vmPageNav($nototal, $_REQUEST['limitstart'], $_REQUEST['limit'], 'process-search', "black");

        $chk = $dclass->select('agent_id, DATE(login_time) AS startdate, SUM(TIME_TO_SEC(TIMEDIFF(logout_time,login_time))) AS timediff', 'tbl_checkin_checkout', ' AND session_id = \'N\' AND parent_id = "'.$_SESSION['custid'].'" GROUP BY startdate,agent_id ORDER BY startdate DESC  LIMIT ' . $_REQUEST['limitstart'] . ',' . $_REQUEST['limit']);
        
    }

    
    $html = '';
    $html2 = '';

    if (@$_REQUEST['type'] != 'onp') {
       if(empty($chk)){
           $html .= "<tr><td colspan='5' class='no-record-found'><img title='' alt='' src='img/no_apps_found.png'></td></tr>";
       }else{
           $gettimezone = $dclass->select("timezone","tblmember"," AND intid ='".$_SESSION['custid']."'");
               date_default_timezone_set($gettimezone[0]['timezone']); 
           $i = $_REQUEST['limitstart'];
           $k=1;
           foreach($chk as $value_date){
               
               $getusername = $dclass->select("fname,lname","tblmember"," AND intid ='".$value_date['agent_id']."'");
              $html .= '<tr id="trhead_'.$i.'" class=\'item\' onclick="showdetailrow('.$i.')">
                  <td>'.$getusername[0]['fname'].' '.$getusername[0]['lname'].'</td>
                      <td>'.date("M d, Y",strtotime($value_date['startdate'])).'</td>
                          <td></td>
                          <td>'.gmdate("H:i:s",$value_date['timediff']).'</td>
                              <td colspan="2">Logs</td>
                      <tr>';
              $getdetailslog = $dclass->select("*","tbl_access_log"," AND agent_id ='".$value_date['agent_id']."' AND DATE(date) = '".$value_date['startdate']."' ORDER BY intid DESC ");
              $html .= '<tr class="trdetails" id="trdetail_'.$i.'" style="display:none" colspan="6"><td colspan="6"><table width="100%"colspan="6">';
              foreach($getdetailslog as $valuedetaillog){
                  
                 $html .=  '<tr class="trdetail" colspan="5">
									<td class="session_box"></td>																					
									<td class="users_box"></td>
									<td class="time_box">'.$valuedetaillog['ip_address'].'</td>
         <td class="time_box">'.date("H:i:s",strtotime($valuedetaillog['date'])).'</td>
         <td class="session_box">'.$valuedetaillog['message'].'</td>
									<td class="descriptions_box"></td>
									<td></td>                          
                      <tr>';
                  $k++;
              }
              $html .= '</table><td></tr>';
              $i++;
           }
         
       }
       
       $html .= '<script>
           function showdetailrow(id){
           //alert(id);
           $( \'#trdetail_\'+id ).toggle();
}
</script>';
        $html2 .= "<ul>";
        
        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    } else {
        $html2 .= "<ul>";

        if ($nototal > $_REQUEST['limitstart']) {
            #Custom function from front-paging class to create jquery infinite scroll plugin feasible link
            $html2 .= $pagen->getPagesLinks_Custom($str, $url);
        }
        $html2 .= "</ul>";
    }
}

$json = array('htmld' => $html, 'htmld2' => $html2);
echo json_encode($json);

?>
