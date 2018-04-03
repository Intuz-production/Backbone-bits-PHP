<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
$no_app_flag = 0;
//Check app records
if (!$gnrl->check_app($member_id)) {
    $no_app_flag = 1;
}

if ($url == 'apps' || $url == 'add-apps')
    $page_url = $url;
else {
    $page_url = '';
}

if (!strstr($url, 'support-')) {
    if ($sel_app_id)
        $addop = 'Update';
    else
        $addop = 'Add';
}else {
    $addop = '';
}

$header_title = ucwords(str_replace("-", " ", $url));


if ($url == 'add-apps' && $sel_app_id != '')
    $header_title = str_replace("Add", "Edit ", $header_title);



if (!$app_type) {
    if (isset($_SESSION['app_type'])) {
        $app_type = $_SESSION['app_type'];
        if ($app_type == 'ios') {
            $ios_class = 'active';
            $android_class = '';
        } else if ($app_type == 'android') {
            $android_class = 'active';
            $ios_class = '';
        } else {
            $all_class = "active";
        }
    } else {


        if ($url == 'apps') {
            $all_class = "active";
            $ios_class = '';
            $android_class = '';
        } else {
            $all_class = '';
            $android_class = '';
            $app_type = 'ios';
            $ios_class = 'active';
        }
    }
}

$slide_num = $gnrl->get_page_num($member_id, $feature_id, $sel_app_id, $app_type);


$get = http_build_query($_GET);

$qu_flag = 0;
$get = "";
$get_cnt = 0;
foreach ($_GET as $k => $value) {
    if ($get_cnt == 0)
        $op = '?';
    else
        $op = '';
    if (!$value) {
        $get .= $op . $k;
        $ad_key = $k;
    } else
        $get .= $op . "&" . $k . "=" . $value;

    $get_cnt++;
}


if ($url != 'add-apps')
    unset($_SESSION['pflag']);

$str = '';
if (isset($sel_app_id)) {
    $str = "&sel_app_id=" . $sel_app_id;
}

if ($url != 'promotr')
    $parent_app_name = $app_name;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
<?php
if ($url == 'help') {
    $nav_url = ucwords($url);
    ?>
        <ul class="breadcrumb_menu">
            <li><a href="apps"><i class="fa fa-fw fa-home"></i></a> /</li>
            <li><?php echo $nav_url; ?> /</li>
            <!--    <li>
            <?php if ($app_type == 'ios') {
                echo lcfirst(strtoupper($app_type));
            } else {
                echo ucwords($app_type);
            } ?>
                  /</li>-->
            <li><a href="javascript:void(0);" class="active"><?php echo $gnrl->subString($parent_app_name, 40); ?></a></li>
        </ul>
<?php } ?>
    <div class="header-middel-part">
        <h1> <span class="title"><?php echo $header_title; ?></span> </h1>




        <?php if ($url == 'add-apps' && $sel_app_id == '') { ?>
            <button class="btn btn-primary export add-app-manually-button <?php echo $app_class; ?> search__button" value="<?php echo $add_app_btn_value; ?>" type="button" style="" data-loading-text="Loading..."><?php echo $add_app_btn_value; ?></button>
<?php } ?>
<?php if ($url != 'apps' && $url != 'add-apps' && $url != 'analyzr-detail') { ?>
            <form id="changeappstatusfrm" method="POST" action="" >
                <input type="hidden" name="feature_status_id" id="feature_status_id" value="<?php echo $feature_status_id; ?>" >
                <input type="hidden" name="rfeature_status" id="rfeature_status" value="<?php echo $feature_status; ?>" >
                <h1>
                </h1>
            </form>
        <?php } ?>



        <?php if ($url == 'apps') { ?>
            <dd class="plus_icon fl"><a href="add-apps"><img src="img/plus_icon.png" alt="" /></a></dd>
<?php } ?>
<?php if ($no_app_flag == 0 && $url != 'add-apps' && $url != 'profile' && $url != 'agents' && $url != 'rights-management') { //check if any app exists  ?>

            <form id="selectappfrm" name="selectappfrm" method="POST" action="" class="col-xs-12 col-md-6 form-horizontal padding0" role="form" >
                <input type="hidden" name="app_type" id="app_type" value="<?php echo $app_type; ?>" >
                <input type="hidden" name="slide_num" id="slide_num" value="<?php echo $slide_num; ?>" >
                <div class="app-nav" > <div class="<?php if ($url == 'apps') {
                            echo 'fr';
                        } else {
                            echo 'fr';
                        } ?>" style="width:auto; padding:8px 0 0 0;">
                        <ul class="all-apps-icon">
    <?php if ($url == 'apps') { ?>
                                <li class="all_apps <?php echo $all_class; ?>" title="All Apps">&nbsp;</li>
    <?php } ?>
                            <li class="ip <?php echo $ios_class; ?> ">&nbsp;</li>
                            <li class="ad <?php echo $android_class; ?> ">&nbsp;</li>
                        </ul>
                    </div>
                </div>
        </div>
    </form>
<?php } ?>
</div>
</section>
<style type="text/css">
    .form-horizontals{width:76.4% !important;}
    .fr.header-right{ padding:0}
    .date_picker{margin: 8px 40px 0 0%;padding: 0; width:280px; float:right; }
    .input-group span.input-group-btn {position: absolute;right: 12px; top: 6px;}
    /*input.readonly[value="December 23, 2014 - January 22, 2015"]{ width:100%;}*/
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        .date_picker{margin: 8px 40px 0 0%;padding: 0;width:290px;}
        .input-group span.input-group-btn {position: absolute;right: 12px; top: 6px;}

    }

    @media(min-width:1200px) and (max-width:1400px) {.form-horizontal{width:60%;}}
    @media(width:1024px) {.form-horizontal{width:75%;}}
    @media(max-width:800px) {.date_picker, .sliderWrapper{ float:left} .fr.header-right{ padding:0} }
    @media(max-width:640px){.app-nav{ float:left; margin-left:23px}}

</style>