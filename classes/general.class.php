<?php
/* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */

class general {

    var $currency_symbol = "$";
    var $site_path = SITE_URL;
    var $mcrypt_iv = '12345678abcdefgh';
    var $salt = '123456789';

    function getimg($url) {
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'php';
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERAGENT, $useragent);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }

    function redirectTo($redirect_url) {
        echo "<script type=\"text/javascript\">location.href = \"{$redirect_url}\"</script>";
        die();
    }

    function dateDiff($dformat, $endDate, $beginDate) {
        $date_parts1 = explode($dformat, $beginDate);
        $date_parts2 = explode($dformat, $endDate);
        $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
        $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
        return $end_date - $start_date;
    }

    /* function added by todds for url checking */

    function addhttp($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }

    /* ends */

    function convertDateToDb($date, $sep) {
        $explode = explode($sep, $date);
        $yy = $explode[2];
        $dd = $explode[0];
        $mm = $explode[1];

        $newdate = $yy . $sep . $mm . $sep . $dd;
        return $newdate;
    }

    function convertDbToFormat($date, $sep) {
        $explode = explode($sep, $date);
        $yy = $explode[0];
        $mm = $explode[1];
        $dd = $explode[2];

        $newdate = $dd . $sep . $mm . $sep . $yy;
        return $newdate;
    }

    function createThumb($name, $filename, $new_w, $new_h, $path = "") {
        $wh = getimagesize($path . $name);
        if ($wh[0] < $new_w)
            $new_w = $wh[0];
        if ($wh[1] < $new_h)
            $new_h = $wh[1];
        $gd2 = 1;
        $system = explode(".", $name);

        if (preg_match("/jpg|jpeg|JPG|JPEG/", $system[1])) {
            $src_img = imagecreatefromjpeg($path . $name);
        }

        if (preg_match("/gif|GIF/", $system[1])) {
            $src_img = imagecreatefromgif($path . $name);
        }

        if (preg_match("/png|PNG/", $system[1])) {
            $src_img = imagecreatefrompng($path . $name);
        }

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_w * ($old_y / $old_x);
        }

        if ($old_x < $old_y) {
            $thumb_w = $new_h * ($old_x / $old_y);
            $thumb_h = $new_h;
        }

        if ($old_x == $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_h;
        }

        if ($gd2 == 1) {
            $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
            imagefill($dst_img, 0, 0, imagecolorallocate($dst_img, 255, 255, 255));
            imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        } else {
            $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        }

        if (preg_match("/gif|GIF/", $system[1])) {
            imagegif($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        } else {
            imagejpeg($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }

    function createThumb1($name, $filename, $new_w, $new_h, $path = "") {
        $wh = getimagesize($path . $name);

        if ($wh[0] < $new_w)
            $new_w = $wh[0];

        if ($wh[1] < $new_h)
            $new_h = $wh[1];

        $gd2 = 1;
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if (preg_match("/jpg|jpeg|JPG|JPEG/", $ext)) {
            $src_img = imagecreatefromjpeg($path . $name);
        }

        if (preg_match("/gif|GIF/", $ext)) {
            $src_img = imagecreatefromgif($path . $name);
        }

        if (preg_match("/png|PNG/", $ext)) {
            $src_img = imagecreatefrompng($path . $name);
        }

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_w * ($old_y / $old_x);
        }

        if ($old_x < $old_y) {
            $thumb_w = $new_h * ($old_x / $old_y);
            $thumb_h = $new_h;
        }

        if ($old_x == $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_h;
        }

        if ($gd2 == 1) {
            $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
            imagefill($dst_img, 0, 0, imagecolorallocate($dst_img, 255, 255, 255));
            imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        } else {
            $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        }

        if (preg_match("/gif|GIF/", $system[1])) {
            imagegif($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        } else {
            imagejpeg($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }

    ////////////////////// function to get the name field from the id field of a table ///////////////////////////////////

    function getName($id, $idValue, $table, $name) {
        $sqlSelect = "SELECT `" . $name . "` FROM `" . $table . "` WHERE `" . $id . "` = '" . $idValue . "'";
        $relSelect = mysql_query($sqlSelect);
        $nameValue = "";
        while ($row = mysql_fetch_array($relSelect)) {
            $nameValue = $row[$name];
        }
        return $nameValue;
    }

    function getNames($id, $idValue, $table, $name) {
        if ($name == '*') {
            $sqlSelect = "SELECT * FROM `" . $table . "` WHERE `" . $id . "` = '" . $idValue . "'";
            $relSelect = mysql_query($sqlSelect);
            $nameValue = array();
            while ($row = mysql_fetch_array($relSelect)) {
                $nameValue[] = $row;
            }
        } else {
            $sqlSelect = "SELECT `" . $name . "` FROM `" . $table . "` WHERE `" . $id . "` = '" . $idValue . "'";
            $relSelect = mysql_query($sqlSelect);
            $nameValue = array();
            while ($row = mysql_fetch_array($relSelect)) {
                $nameValue[] = $row[$name];
            }
        }
        return $nameValue;
    }

    //////////////////////////////// Check email ////////////////////////////////////////////

    function checkEmail($email) {
        // checks proper syntax
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email)) {

            return false;
        } else {
            return true;
        }
    }

    /* Date function added by todds */

    function custom_date($format, $timestamp) {
        return date($format, $timestamp);
    }

    function thumb($name, $filename, $new_w, $new_h, $path = "") {
        $wh = getimagesize($path . $name);
        if ($wh[0] < $new_w)
            $new_w = $wh[0];

        if ($wh[1] < $new_h)
            $new_h = $wh[1];

        $gd2 = 1;

        $system = explode(".", $name);
        if (count($system) > 2) {
            $system1 = $system[count($system) - 1];
        } else {
            $system1 = $system[1];
        }

        if (preg_match("/jpg|jpeg|JPG|JPEG/", $system1))
            $src_img = imagecreatefromjpeg($path . $name);

        if (preg_match("/gif|GIF/", $system1))
            $src_img = imagecreatefromgif($path . $name);

        if (preg_match("/png|PNG/", $system1))
            $src_img = imagecreatefrompng($path . $name);

        $old_x = imagesx($src_img);
        $old_y = imagesy($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_w * ($old_y / $old_x);
        }

        if ($old_x < $old_y) {
            $thumb_w = $new_h * ($old_x / $old_y);
            $thumb_h = $new_h;
        }

        if ($old_x == $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_h;
        }

        if ($gd2 == 1) {
            $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
            imagefill($dst_img, 0, 0, imagecolorallocate($dst_img, 255, 255, 255));
            imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        } else {
            $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        }

        if (preg_match("/gif|GIF/", $system1)) {
            imagegif($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        } else {
            imagejpeg($dst_img, $path . $filename);
            chmod($path . $filename, 0777);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function getSettings($field, $lang_id = '') {
        global $dclass;
        if ($lang_id) {
            $tbl_settings = 'tblmember_lang';
            $wh .= " AND lang_id='" . $lang_id . "' ";
        } else {
            $tbl_settings = 'tblsettings';
        }
        $sql = "SELECT value FROM $tbl_settings WHERE option_name='" . $field . "' $wh";
        $row = $dclass->fetchArray($dclass->query($sql));
        return $row['value'];
    }

    function getMember_Settings($field, $member_id) {
        global $dclass;

        $tbl_settings = 'tblmember_settings';
        $wh .= " AND member_id = '" . $member_id . "' AND option_name='" . $field . "'";
        $row = $dclass->fetchArray($dclass->query("SELECT value FROM $tbl_settings WHERE 1 $wh"));
        return $row['value'];
    }

    function getMessage($field, $lang_id = '') {
        global $dclass;
        if ($lang_id) {
            $wh .= " AND lang_id='" . $lang_id . "' ";
        } else {
            $wh .= " AND lang_id='1'";
        }
        $sql = "SELECT msg_value FROM tblmessage_lang WHERE 1 AND msg_name='" . $field . "'  $wh";
        $row = $dclass->fetchArray($dclass->query($sql));
        return $row['msg_value'];
    }

    function email_mime($email_from, $email_to, $email_cc = "", $email_bcc = "", $email_subject, $email_message, $email_format = "") {
        $smtp_yes = "";
        // Instantiate a new HTML Mime Mail object
        $mail = new htmlMimeMail();
        if ($smtp_yes) {
            
        }

        // Set the sender address
        $mail->setFrom($email_from);

        // Set the reply-to address
        $mail->setReturnPath($email_from);

        // Set the mail subject
        $mail->setSubject($email_subject);

        if ($email_bcc != "")
            $mail->setBcc($email_cc);

        if ($email_cc != "")
            $mail->setCc($email_cc);

        // Set the mail body text
        $email_message = str_replace("\n", "", $email_message);
        $email_message = str_replace("\r", "", $email_message);

        if ($email_format == "html") {
            $email_message = str_replace("{site_logo}", "<img src='" . $this->site_path . "/images/logo.jpg' />", $email_message);
            $mail->setHTML($email_message);
        } else {
            $mail->setText($email_message);
        }

        if (is_array($email_to)) {
            // Send the email!
            $mail->send($email_to, 'smtp');
        } else {
            // Send the email!
            $mail->send(array($email_to), 'smtp');
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function email($email_from, $email_to, $email_cc = "", $email_bcc = "", $email_subject, $email_message, $email_format = "") {

        if ($email_format == 'html') {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        }
        if ($email_from != '') {
            $headers .= 'From: Backbone bits<' . $email_from . '>' . "\r\n";
        }
        if ($email_cc != '') {
            $headers .= 'Cc: ' . $email_cc . "\r\n";
        }
        if ($email_bcc != '') {
            $headers .= 'Bcc: ' . $email_bcc . "\r\n";
        }

        $email_message_header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			    <head>
			        <title>#pageTitle#</title>
			        <style>body{font-family: Arial, Verdana, Tahoma, Helvetica, sans-serif;text-align:left;letter-spacing:0.03em;margin:0px;padding:0px;font-size:16px;padding:0px; line-height:25px;color:#515151;}div.button{color:#fff; font-size:25px}a.blue:link, .popup a.blue:visited, .popup a.blue:active, .blue{color:#A6DAE5;font-size:12px;font-weight:bold;}.dec, .dec li, .dec p, .dec p span, .dec div, .content ul li, .dec ul li{font-size:12px;text-align:justify;}.TwL-in{width:200px;}.welcome{font-weight:bold;color:#F67C1E;font-size:14px;text-transform:capitalize;}a:hover{color:#B3BB48;text-decoration:none;font-weight:bold;}.PB10{padding-bottom: 10px;}hr{color:#e7e7e7;border:0px;border-bottom:1px solid #e7e7e7;margin:0px;padding:0px}.footer{color:#7A7A7A;text-align:center;font-size:11px;}.LfHd{font-weight:bold;color:#F67C1E;font-size:11px;}.tblBr{border:1px solid #ECECEC;padding:6px 0px 6px 0px;}.ML22{margin-left: 10px;}.PL7{padding-left: 10px;}.left{float:left;}.right{float:right;}.PT2{padding-top:2px;}.PT8{padding-top:8px;}.PT20{padding-top:20px;}.PB6{padding-bottom: 6px;}.copyr, .copyr a{color:#B2B2B2;font-weight:normal;padding:0px 0px 0px 0px;}.pay{width:330px;float:left;}.payment{background:#E5E5E5;width:114px;padding: 4px 8px 4px 8px;line-height:16px;clear:both;}.clear{clear:both;}.borlight{border-bottom:#D6D6D6 1px solid;margin-bottom:3px;padding:0px;}.txtc{text-align:center;}.price{font-weight:bold;color:#4b4b4b;font-size:11px;float:left;padding-right: 50px;width:80px;text-align:right;}.hlight{color:#F67C1E;font-weight:normal;}.pay b{width:120px;text-align:right;display:block;float:left;padding-right:8px;}.pay span{float:left;padding-left:5px;width:200px;}.pay p{margin:3px 0px 3px 0px;clear:both;}.lighttext{color:#CCCCCC}.lighttext a{color:#56891A}.lighttext a:hover{color:#56891A}p{line-height: 25px;}a:link, a:visited, a:active{color:#00cccc;text-decoration: none;font-weight: bold;}a:hover{color:#666;text-decoration: none;font-weight: bold;}h4{margin: 0px 0 0 10px;font-size: 13px;font-weight: bold;color: #5E961D;}blockquote{margin: 0 0 0 10px;}h2{color: #000;font-size: 16px;font-weight: bold;margin: 0px 0px 5px 0px;}.copy{color:#CCC;}</style>
				    </head>
				    <body>
				        <table width="750" border="0" cellspacing="0" cellpadding="0" style=" margin:20px; ">
				        <tr>
				            <td align="center" style=" padding-bottom:30px" ><img src="' . $this->site_path . '/img/login-logo.png" height="100px" alt="' . BRAND . '" title="' . BRAND . '"/></td>
				        </tr>
				        <tr>
				            <td bgcolor="#ffffff">
				                <table width="100%" border="0" cellspacing="0" cellpadding="0">
				        <tr>
				            <td>
				                <table width="100%" border="0" cellspacing="10" cellpadding="0">
				        <tr>
				            <td colspan="2">
				                <hr/>
				                <br/>
				            </td>
				        </tr>
				        <tr>
				            <td>';


        $email_message = $email_message_header . $email_message;
        $email_message = str_replace("\n", "", $email_message);
        $email_message = str_replace("\r", "", $email_message);
        $email_message = stripslashes($email_message);

        $email_message_footer = '</td></tr></table></td></tr></table></td></tr>
			<tr>
			    <td bgcolor="#f2f2f2" align="center" style="color:gray" height="50">Copyright @' . date('Y') . ' <a href="http://bckbn.io" target="_blank">' . BRAND . '</a>. All Rights Reserved.</td>
			</tr>
			</table></body></html>';

        $email_message = $email_message . $email_message_footer;
        $resp = mail($email_to, $email_subject, $email_message, $headers);
        return $resp;
    }

    function email_communicatr($email_from, $email_to, $email_cc = "", $email_bcc = "", $email_subject, $email_message, $email_format = "") {

        if ($email_format == 'html') {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        }
        if ($email_from != '') {
            $headers .= 'From: Backbone bits<' . $email_from . '>' . "\r\n";
        }
        if ($email_cc != '') {
            $headers .= 'Cc: ' . $email_cc . "\r\n";
        }
        if ($email_bcc != '') {
            $headers .= 'Bcc: ' . $email_bcc . "\r\n";
        }

        $email_message_header = '<!doctype html>
			<html>
			<head>
				<meta name="viewport" content="width=device-width">
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<title>Really Simple HTML Email Template</title>
				<style>
					/* -------------------------------------
					GLOBAL
					------------------------------------- */
					* {
						font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
						font-size: 100%;
						line-height: 1.6em;
						margin: 0;
						padding: 0;
					}
					img {
						max-width: 600px;
						width: auto;
					}
					body {
						-webkit-font-smoothing: antialiased;
						height: 100%;
						-webkit-text-size-adjust: none;
						width: 100% !important;
					}
					/* -------------------------------------
					ELEMENTS
					------------------------------------- */
					a {
						color: #348eda;
					}
					.btn-primary {
						Margin-bottom: 10px;
						width: auto !important;
					}
					.btn-primary td {
						background-color: #348eda; 
						border-radius: 25px;
						font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
						font-size: 14px; 
						text-align: center;
						vertical-align: top; 
					}
					.btn-primary td a {
						background-color: #348eda;
						border: solid 1px #348eda;
						border-radius: 25px;
						border-width: 10px 20px;
						display: inline-block;
						color: #ffffff;
						cursor: pointer;
						font-weight: bold;
						line-height: 2;
						text-decoration: none;
					}
					.last {
						margin-bottom: 0;
					}
					.first {
						margin-top: 0;
					}
					.padding {
						padding: 10px 0;
					}
					/* -------------------------------------
					BODY
					------------------------------------- */
					table.body-wrap {
						padding: 20px;
						width: 100%;
					}
					table.body-wrap .container {
						border: 1px solid #f0f0f0;
					}
					/* -------------------------------------
					FOOTER
					------------------------------------- */
					table.footer-wrap {
						clear: both !important;
						width: 100%;  
					}
					.footer-wrap .container p {
						color: #666666;
						font-size: 12px;

					}
					table.footer-wrap a {
						color: #999999;
					}
					/* -------------------------------------
					TYPOGRAPHY
					------------------------------------- */
					h1, 
					h2, 
					h3 {
						color: #111111;
						font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
						font-weight: 200;
						line-height: 1.2em;
						margin: 40px 0 10px;
					}
					h1 {
						font-size: 36px;
					}
					h2 {
						font-size: 28px;
					}
					h3 {
						font-size: 22px;
					}
					p, 
					ul, 
					ol {
						font-size: 14px;
						font-weight: normal;
						margin-bottom: 10px;
					}
					ul li, 
					ol li {
						margin-left: 5px;
						list-style-position: inside;
					}
					/* ---------------------------------------------------
					RESPONSIVENESS
					------------------------------------------------------ */
					/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
					.container {
						clear: both !important;
						display: block !important;
						Margin: 0 auto !important;
						max-width: 600px !important;
					}
					/* Set the padding on the td rather than the div for Outlook compatibility */
					.body-wrap .container {
						padding: 20px;
					}
					/* This should also be a block element, so that it will fill 100% of the .container */
					.content {
						display: block;
						margin: 0 auto;
						max-width: 600px;
					}

					.content table {
						width: 100%;
					}
				</style>
			</head>

			<body bgcolor="#f6f6f6">
				<!-- body -->
				<table class="body-wrap" bgcolor="#f6f6f6">
					<tr>
						<td></td>
						<td class="container" bgcolor="#FFFFFF">
							<!-- content -->
							<div class="content">
								<table>
									<tr> <td align="center" style=" padding-bottom:30px"><img src="' . $this->site_path . '/img/login-logo.png" alt="' . BRAND . '" height="100px" title="' . BRAND . '"/></td></tr> 
									<tr>
										<td>';
        $email_message = $email_message_header . $email_message;
        $email_message = str_replace("\n", "", $email_message);
        $email_message = str_replace("\r", "", $email_message);
        $email_message = stripslashes($email_message);

        $email_message_footer = '</td>
										</tr>
									</table>
								</div>
								<!-- /content -->
							</td>
							<td></td>
						</tr>
					</table>

					<!-- footer -->
					<table class="footer-wrap">
						<tr>
							<td></td>
							<td class="container">
								<!-- content -->
								<div class="content">
									<table>
										<tr>
											<td align="center">
												<p>Copyright @' . date('Y') . ' <a href="http://bckbn.io" target="_blank">' . BRAND . '</a>. All Rights Reserved.
												</p>
											</td>
										</tr>
									</table>
								</div>
								<!-- /content -->
							</td>
							<td></td>
						</tr>
					</table>
					<!-- /footer -->
				</body>
				</html>';

        $email_message = $email_message . $email_message_footer;

        $resp = mail($email_to, $email_subject, $email_message, $headers);
        return $resp;
    }

    function printdate($date) {
        $date1 = explode("-", $date);
        $year = $date1[0];
        $month = $date1[1];
        $day = $date1[2];
        $day1 = explode(":", $day);
        $aday = $day1[0];
        $aday = substr($aday, 0, 3);
        $hr = $day1[0];
        $month_array = array("Jan", "Feb", "Mar", "Apr", "May", "June", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $month = $month_array[$month - 1];
        $newdate = $month . "-" . $aday . "-" . $year;
        return $newdate;
    }

    function imgWatermark($img_src, $watermark_src) {
        header('content-type: image/jpeg');
        $path = "";
        $imagesource = $path . $img_src;
        $watermarkPath = $watermark_src;
        $filetype = substr($imagesource, strlen($imagesource) - 4, 4);
        $filetype = strtolower($filetype);
        $watermarkType = substr($watermarkPath, strlen($watermarkPath) - 4, 4);
        $watermarkType = strtolower($watermarkType);

        if ($filetype == ".gif")
            $image = @imagecreatefromgif($imagesource);
        else
        if ($filetype == ".jpg" || $filetype == "jpeg")
            $image = @imagecreatefromjpeg($imagesource);
        else
        if ($filetype == ".png")
            $image = @imagecreatefrompng($imagesource);
        else
            die();

        if (!$image)
            die();

        if ($watermarkType == ".gif")
            $watermark = @imagecreatefromgif($watermarkPath);
        else
        if ($watermarkType == ".png")
            $watermark = @imagecreatefrompng($watermarkPath);
        else
            die();

        if (!$watermark)
            die();

        $imagewidth = imagesx($image);
        $imageheight = imagesy($image);
        $watermarkwidth = imagesx($watermark);
        $watermarkheight = imagesy($watermark);
        $startwidth = (($imagewidth - $watermarkwidth) / 2);
        $startheight = (($imageheight - $watermarkheight) / 2);
        imagecopy($image, $watermark, $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight);
        imagejpeg($image);
        imagedestroy($image);
        imagedestroy($watermark);
    }

    function subString($string, $length) {
        $string = strip_tags($string);
        if (strlen($string) > $length) {
            $string1 = substr($string, 0, $length);
        } else {
            $string1 = $string;
        }
        return $string1;
    }

    function subStringc($string, $length, $id = "") {
        $string = strip_tags($string);
        if (strlen($string) > $length) {
            $string1 = substr($string, 0, $length) . "...<a href='javascript:;' id='readmore" . $id . "' class='readm'>[Read More]</a>";
        } else {
            $string1 = $string;
        }
        return $string1;
    }

    function correctURL($url) {
        $test_arr = split("//", $url);
        if ($test_arr[0] == "http:" || $test_arr[0] == "https:")
            return $url;
        else
            return "http://" . $url;
    }

    ///////////////////////////////////////////////////////////////////////////////
    ////////////// funtion to fetch to content of the given url ////////////////////////////

    function fetchURL($address) {
        $host = $address;
        $contents = '';
        $handle = @fopen($host, "rb");
        if ($handle) {
            while (!@feof($handle)) {
                $contents .= @fread($handle, 8192);
            }
            @fclose($handle);
        }
        return $contents;
    }

    function removeChars($string) {
        $arra = array(" ", "@", "#", "?", "&", "&amp;");
        for ($i = 0; $i < count($arra); $i++) {
            if (strstr($string, $arra[$i])) {
                $string = str_replace($arra[$i], "", $string);
            }
        }
        return $string;
    }

    function back() {
        return "<a href='javascript:history.go(-1);'>Back</a>";
    }

    function makefilename($file_name) {
        $file_name = str_replace("&", "", $file_name);
        $file_name = str_replace("?", "", $file_name);
        $file_name = str_replace("/", "", $file_name);
        $file_name = str_replace(">", "", $file_name);
        $file_name = str_replace("<", "", $file_name);
        $file_name = str_replace("&", "", $file_name);
        $file_name = str_replace("#", "", $file_name);
        $file_name = str_replace(" ", "", $file_name);
        $file_name = stripslashes($file_name);
        return $file_name;
    }

    function checkLogin() {
        if (isset($_SESSION['adminid'])) {
            return 1;
        }
    }

    function checkMemLogin() {
        if (isset($_SESSION['custid'])) {
            return true;
        } else {
            return false;
        }
    }

    function checkuseraccess($member_id, $sel_app_id) {
        global $dclass;
        $data = $dclass->select("app_name", "tblmember_apps", " AND member_id = '" . $member_id . "' AND intid = '" . $sel_app_id . "' ");
        return count($data);
    }

    function getYouTubeURL($string) {
        $splitString = $string;
        $splitString = explode("=", $splitString);
        $videoID = $splitString[1];
        return $videoID;
    }

    function chklogin() {
        global $dclass;
        $data = $dclass->select("intid", "tblmembers", " AND intid = '" . $_SESSION['memberid'] . "' AND status = 'inactive' ");

        if (!isset($_SESSION['memberid'])) {
            @header("location:index.php");
        } else if (isset($_SESSION['memberid']) && count($data) > 0) {
            session_destroy();
            @header("location:index.php");
        }
    }

    function islogin() {
        if (!isset($_SESSION['memberid'])) {
            return false;
        } else {
            return true;
        }
    }

    function msbc_addslashes($str) {
        return addslashes($str);
    }

    function goto_page($url) {
        echo '<script language="javascript">document.location="' . $url . '";</script>';
        exit;
    }

    function msbc_mail($to, $subject, $vBody, $from, $format, $cc = "", $bcc = "") {
        if (strlen($format) == 0)
            $format = "text/html";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: " . $format . "; charset=iso-8859-1\r\n";

        /* additional headers */
        $headers .= "From: $from\r\n";
        if (strlen($cc) > 5)
            $headers .= "Cc: $cc\r\n";
        if (strlen($bcc) > 5)
            $headers .= "Bcc: $bcc\r\n";
        $state = @mail($to, $subject, $vBody, $headers);
        return $state;
    }

    function randomLoginToken($length, $id = '') {
        srand(date("s"));
        $possible_charactors = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $string = "";
        while (strlen($string) < $length) {
            $string .= substr($possible_charactors, rand() % strlen($possible_charactors), 1);
        }
        return($prefix . $id . $string);
    }

    function img_scale($source, $width, $height, $extra = 'border=0') {
        $w = $width;
        $h = $height;
        if (!file_exists($source)) {
            $src = '';
        }
        $size = @getimagesize($source);
        if ($w > $size[0] && $h > $size[1]) {
            $neww = $size[0]; //$w;
            $newh = $size[1]; //$h;
        } else if ($w < $size[0] && $h > $size[0]) {
            $diff = $size[0] - $w;
            $dec_per = (100 * $diff) / $size[0];
            $neww = $size[0] - (($size[0] * $dec_per) / 100);
            $newh = $size[1] - (($size[1] * $dec_per) / 100);
        } else if ($w > $size[0] && $h < $size[0]) {
            $diff = $size[1] - $h;
            $dec_per = (100 * $diff) / $size[1];
            $newh = $size[1] - (($size[1] * $dec_per) / 100);
            $neww = $size[0] - (($size[0] * $dec_per) / 100);
        } else {
            if (($size[0] - $w) >= ($size[1] - $h)) {
                $diff = $size[0] - $w;
                $dec_per = (100 * $diff) / $size[0];
                $neww = $size[0] - (($size[0] * $dec_per) / 100);
                $newh = $size[1] - (($size[1] * $dec_per) / 100);
                $cc = 'w';
            } else {
                $diff = $size[1] - $h;
                $dec_per = (100 * $diff) / $size[1];
                $newh = $size[1] - (($size[1] * $dec_per) / 100);
                $neww = $size[0] - (($size[0] * $dec_per) / 100);
                $cc = 'h';
            }
        }
        return "<img src='" . $source . "' width='" . $neww . "' height='" . $newh . "'  " . $extra . " >";
    }

    /**
     * @param :- photopath [ string ] - file tag name, vphoto [ string ] - upload filename,vphoto_name [ string ] - upload filename, prefix [ string ] - save image file name prefix.
     * @return  :- array - string array with message and fine name
     * @see  Purpose  :- to upload images.
     */
    function imageupload($photopath, $vphoto, $vphoto_name, $prefix) {
        $msg = '';
        if (is_file($vphoto) and ! empty($vphoto_name)) {
            // Remove Dots from File name
            $tmp = explode(".", $vphoto_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];
            $vlfname = $file . "." . $ext;

            if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png" || $ext == "JPG" || $ext == "JPEG" || $ext == "GIF" || $ext == "PNG") {
                $vphotofile = $prefix . "." . $ext;
                $ftppath1 = $photopath . $vphotofile;
                unlink($ftppath1);
                if (!copy($vphoto, $ftppath1)) {
                    $vphotofile = '';
                    $msg = rawurlencode("Photo Not Uploaded Successfully");
                } else {
                    $msg = rawurlencode("Image Uploaded Successfully !!");
                }
            } else {
                $vphotofile = '';
                $msg = "Photo Type Is Not Valid";
            }
        }
        $ret[0] = $vphotofile;
        $ret[1] = $msg;
        return $ret;
    }

    function pdfupload($photopath, $vphoto, $vphoto_name, $prefix) {
        $msg = '';
        if (is_file($vphoto) and ! empty($vphoto_name)) {
            // Remove Dots from File name
            $tmp = explode(".", $vphoto_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];
            $vlfname = $file . "." . $ext;

            if ($ext == "pdf" || $ext == "PDF") {
                $vphotofile = $prefix . "." . $ext;
                $ftppath1 = $photopath . $vphotofile;
                if (!copy($vphoto, $ftppath1)) {
                    $vphotofile = '';
                    $msg = rawurlencode("PDF Not Uploaded Successfully !!");
                } else {
                    $msg = rawurlencode("PDF Uploaded Successfully !!");
                }
            } else {
                $vphotofile = '';
                $msg = "PDF Type Is Not Valid !!!";
            }
        }
        $ret[0] = $vphotofile;
        $ret[1] = $msg;
        return $ret;
    }

    function add_html_editor_code() {
        ?><script type="text/javascript" src="ckeditor/ckeditor.js"></script>
        <script src="ckeditor/_samples/sample.js" type="text/javascript"></script>
        <link href="ckeditor/_samples/sample.css" rel="stylesheet" type="text/css"/><?php
    }

    function create_update_file_with_content($filename, $somecontent) {
        if (!$handle = fopen($filename, 'w+')) {
            return false;
        }
        if (fwrite($handle, $somecontent) === FALSE) {
            return false;
        }
        return true;
        fclose($handle);
    }

    function am_set_search_to_all_fields($tableName, $operator, $seachStr, $alias = '') {
        $query = "SELECT * FROM " . $tableName . " ";
        $result = mysql_query($query);
        $returnStr = '';
        for ($i = 0; $i < mysql_num_fields($result); ++$i) {
            $table = mysql_field_table($result, $i);
            $field = mysql_field_name($result, $i);
            if ($alias != '' && isset($alias)) {
                $returnStr .= $alias . '.' . $field . " like '%" . $seachStr . "%' " . $operator . " ";
            } else {
                $returnStr .= $field . " like '%" . $seachStr . "%' " . $operator . " ";
            }
        }
        if ($alias != '' && isset($alias)) {
            $returnStr .= $alias . '.' . $field . " like '%" . $seachStr . "%'  ";
        } else {
            $returnStr .= $field . " like '%" . $seachStr . "%'  ";
        }
        return $returnStr;
    }

    function create_list_from_table($table_name, $id_field, $text_field, $condition = '', $select_item = 0, $default_option = '', $select_field_list = ' * ') {
        $sql = "select " . $select_field_list . " from " . $table_name . " " . $condition . " ";
        $rs = mysql_query($sql);
        $return_var = $default_option;
        while ($row = mysql_fetch_array($rs)) {
            $return_var = $return_var . '<option value="' . $row[$id_field] . '" ' . ($row[$id_field] == $select_item ? ' selected ' : '' ) . '>' . $row[$text_field] . '</option>';
        }
        return $return_var;
    }

    function create_list_with_flag_from_table($type, $sep = "-", $table_name, $id_field, $text_field, $condition = '', $select_item = 0, $default_option = '', $select_field_list = ' * ') {
        $sql = "select " . $select_field_list . " from " . $table_name . " " . $condition . " ";
        $rs = mysql_query($sql);
        $return_var = $default_option;
        while ($row = mysql_fetch_array($rs)) {
            $return_var = $return_var . '<option value="' . $row[$id_field] . $sep . $type . '" ' . ($row[$id_field] . $sep . $type == $select_item ? ' selected ' : '' ) . '>' . $row[$text_field] . '</option>';
        }
        return $return_var;
    }

    function create_list_with_array($listArray, $selectValue) {
        $returnValue = '';
        foreach ($listArray as $value) {
            $returnValue .= '<option value="' . $value . '" ' . ($value == $selectValue ? ' selected ' : '' ) . ' >' . $value . '</option>';
        }
        return $returnValue;
    }

    function create_list_with_array2($listArray, $selectValue) {
        $returnValue = '';
        foreach ($listArray as $key => $value) {
            $returnValue .= '<option value="' . $key . '" ' . ($key == $selectValue ? ' selected ' : '' ) . ' >' . $value . '</option>';
        }
        return $returnValue;
    }

    function get_single_value_with_condition($table_name, $id_field, $text_field, $condition, $default_option = 'N/A', $select_field_list = ' * ') {
        $sql = "select " . $select_field_list . " from " . $table_name . " " . $condition . " ";
        $rs = mysql_query($sql);
        while ($row = mysql_fetch_array($rs)) {
            return '' . $row[$text_field] . '';
        }
        return $default_option;
    }

    function get_single_value_with_condition2($table_name, $id_field, $text_field, $condition = '', $select_item = 0, $default_option = '', $select_field_list = ' * ') {
        $sql = "select " . $select_field_list . " from " . $table_name . " " . $condition . " ";
        $rs = mysql_query($sql);
        while ($row = mysql_fetch_array($rs)) {
            return '' . $row[$text_field] . '';
        }
        return $default_option;
    }

    function add_space($count) {
        $return_var = "";
        for ($i = 1; $i <= ($count * 5); $i++) {
            $return_var = $return_var . "&nbsp;";
        }
        return $return_var;
    }

    function sort_type_check($var1, $var2) {
        if ($var1 == $var2) {
            if ($_REQUEST['sortType'] != 'desc')
                return '&sortType=desc';
            else {
                return '';
            }
        } else {
            return '';
        }
    }

    function sort_type_icon($var1, $var2) {
        if ($var1 == $var2) {
            if ($_REQUEST['sortType'] != 'desc')
                return '<span>&nbsp;<img src="images/arrow_up.gif" alt="up" title="up" border="0" /></span>';
            else
                return '<span>&nbsp;<img src="images/arrow_down.gif" alt="down" title="down" border="0" /></span>';
        }
        else {
            return '';
        }
    }

    function sort_type_check_icon($text, $qs, $field_name, $qs_field_name) {
        if ($field_name == $qs_field_name) {
            if ($_REQUEST['sortType'] != 'desc')
                return '
					<table cellpadding="0"  cellspacing="">
						<tr>
							<td rowspan="2">' . $text . '&nbsp;</td>
							<td valign="bottom"><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=asc"><img src="images/arrow_up.gif" alt="up" title="up" border="0" /></a></td>
						</tr>
						<tr>
							<td><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=desc"><img src="images/arrow_down.gif" alt="down" title="down" border="0" /></a></td>
						</tr>
					</table>';
            else {
                return '
					<table cellpadding="0"  cellspacing="">
						<tr>
							<td rowspan="2">' . $text . '&nbsp;</td>
							<td valign="bottom"><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=asc"><img src="images/arrow_up.gif" alt="up" title="up" border="0" /></a></td>
						</tr>
						<tr>
							<td><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=desc"><img src="images/arrow_down.gif" alt="down" title="down" border="0" /></a></td>
						</tr>
					</table>';
            }
        } else {
            return '
				<table cellpadding="0"  cellspacing="">
					<tr>
						<td rowspan="2">' . $text . '&nbsp;</td>
						<td valign="bottom"><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=asc"><img src="images/arrow_up.gif" alt="up" title="up" border="0" /></a></td>
					</tr>
					<tr>
						<td><a href="' . $sfl . '&sortBy=' . $field_name . '&sortType=desc"><img src="images/arrow_down.gif" alt="down" title="down" border="0" /></a></td>
					</tr>
				</table>';
        }
    }

    function create_list_from_table_custom($table_name, $id_field, $text_field1, $text_field2, $condition = '', $select_item = 0, $default_option = '', $select_field_list = ' * ') {
        $sql = "select " . $select_field_list . " from " . $table_name . " " . $condition . " ";

        $rs = mysql_query($sql);
        $return_var = $default_option;
        while ($row = mysql_fetch_array($rs)) {
            $return_var = $return_var . '<option value="' . $row[$id_field] . '" ' . ($row[$id_field] == $select_item ? ' selected ' : '' ) . '>' . $row[$text_field1] . '&nbsp;' . $row[$text_field2] . '&nbsp;(&nbsp;' . $row['memberEmail'] . '&nbsp;)&nbsp;' . '</option>';
        }
        return $return_var;
    }

    /* To get friendly url */

    function getURL() {
        $data = explode(".", basename($_SERVER['PHP_SELF']));
        return $data[0];
    }

    function getAnalytics() {
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '120.72.93.170' && $_SERVER['REMOTE_ADDR'] != '180.211.106.2' && $_SERVER['REMOTE_ADDR'] != '180.211.106.162' && $_SERVER['REMOTE_ADDR'] != '180.211.106.163') {
            echo $this->getSettings('sGoogleAnalyticsCode');
        }
    }

    function getMemImage($image) {
        if ($image == '') {
            $newimage = 'no-image.png';
        } else {
            $newimage = $image;
        }
        return $newimage;
    }

    function date_dropdown($year_limit = 0, $selected_date, $from) {
        //selected date is in year-month-date format.
        $str = explode("-", $selected_date);
        $selected_year = $str[0];
        $selected_month = $str[1];
        $selected_day = $str[2];

        /* months */
        $html_output .= '<label><select name="date_month" id="date_month" class="monthRequired wd70 addwrapper" tabindex="9" >' . "\n";
        $months = array("", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

        for ($month = 1; $month <= 12; $month++) {
            $cnt = $month;
            if (strlen($month) < 2) {
                $month = "0" . $month;
            }
            if ($selected_month == $month) {
                $selected = "selected";
            } else {
                $selected = "";
            }

            $html_output .= '<option value="' . $month . '" ' . $selected . ' >' . $var . $months[$cnt] . $var . '</option>' . "\n";
        }
        $html_output .= '</select></label>' . "\n";

        /* days */
        $html_output .= '<label><select name="date_day" id="date_day" class="dayRequired wd55 addwrapper" tabindex="10" >' . "\n";

        for ($day = 1; $day <= 31; $day++) {
            if ($selected_day == $day) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html_output .= '<option value=' . $day . ' ' . $selected . ' >' . $day . '</option>' . "\n";
        }
        $html_output .= '</select></label>' . "\n";

        /* years */
        $html_output .= '<label><select name="date_year" id="date_year" class="yearRequired wd75 addwrapper" tabindex="11" >' . "\n";

        for ($year = (date("Y") - $year_limit); $year >= 1950; $year--) {
            if ($selected_year == $year) {
                $selected = 'selected';
            } else {
                $selected = "";
            }
            $html_output .= '<option value=' . $year . ' ' . $selected . ' >' . $year . '</option>' . "\n";
        }
        $html_output .= '</select></label>' . "\n";

        return $html_output;
    }

    /*
     * Function to find latest date/time with server timezone.
     */

    function get_latest_time() {
        $now = @mysql_fetch_assoc(mysql_query("SELECT NOW() as cur_time"));
        return $now['cur_time'];
    }

    /*
     * Function to find latest date/time with local timezone.
     */

    function get_latest_local_time() {
        if (isset($_COOKIE['timezonediff'])) {
            $now = @mysql_fetch_assoc(mysql_query("SELECT (UTC_TIMESTAMP() - INTERVAL " . $_COOKIE['timezonediff'] . " MINUTE) as cur_time"));
            return $now['cur_time'];
        } else {
            session_destroy();
            @header("location:index.php?msg=del_browser");
        }
    }

    /*
     * Convert Date to UTC
     */

    function convertToUTC($rdate) {
        $the_date = strtotime($rdate);
        return $rdate;
    }

    /*
     * Convert Actual Date to UTC 
     */

    function convertDateToUTC($rdate) {
        $the_date = strtotime($rdate);
        date_default_timezone_set("UTC");
        return $rdate = date("Y-m-d H:i:s", $the_date);
    }

    /*
     * Function for creating resized image from the co-ordinates
     */

    function resizeImage($crop_image_name, $image, $width, $height, $start_width, $start_height, $scale, $ext) {
        if (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            $source = imagecreatefromjpeg($image);
            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagejpeg($newImage, $crop_image_name, 90);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        } else if (strtolower($ext) == 'gif') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $source = imagecreatefromgif($image);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            $trnprt_indx = imagecolortransparent($source);

            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color = imagecolorsforindex($source, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($newImage, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($newImage, $trnprt_indx);
            }

            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagegif($newImage, $crop_image_name, 90);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        } else if (strtolower($ext) == 'png') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $source = imagecreatefrompng($image);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            // Turn off transparency blending (temporarily)
            imagealphablending($newImage, false);
            // Create a new transparent color for image
            $color = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            // Completely fill the background of the new image with allocated color.
            imagefill($newImage, 0, 0, $color);
            // Restore transparency blending
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagepng($newImage, $crop_image_name);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        }
    }

    /*
     * Function for creating cropped image from the co-ordinates
     */

    function resizeCropImage($crop_image_name, $image, $width, $height, $start_width, $start_height, $scale, $ext) {
        if (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            $source = imagecreatefromjpeg($image);
            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagejpeg($newImage, $crop_image_name, 90);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        } else if (strtolower($ext) == 'gif') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $source = imagecreatefromgif($image);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            $trnprt_indx = imagecolortransparent($source);

            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color = imagecolorsforindex($source, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($newImage, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($newImage, $trnprt_indx);
            }

            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagegif($newImage, $crop_image_name, 90);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        } else if (strtolower($ext) == 'png') {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $source = imagecreatefrompng($image);
            $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
            // Turn off transparency blending (temporarily)
            imagealphablending($newImage, false);
            // Create a new transparent color for image
            $color = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            // Completely fill the background of the new image with allocated color.
            imagefill($newImage, 0, 0, $color);
            // Restore transparency blending
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
            imagepng($newImage, $crop_image_name);
            chmod($crop_image_name, 0777);
            return $crop_image_name;
        }
    }

    /*
     * Function to get image hight
     */

    function getimgHeight($image) {
        $sizes = getimagesize($image);
        $height = $sizes[1];
        return $height;
    }

    /*
     * Function to get image width
     */

    function getimgWidth($image) {
        $sizes = getimagesize($image);
        $width = $sizes[0];
        return $width;
    }

    function get_local_time($date) {
        if (isset($_COOKIE['timezonediff'])) {
            $sql = "SELECT ('$date' - INTERVAL " . $_COOKIE['timezonediff'] . " MINUTE) as cur_time";
            $now = @mysql_fetch_assoc(mysql_query($sql));
            return $now['cur_time'];
        } else {
            session_destroy();
            @header("location:index.php?msg=del_browser");
        }
    }

    function getSortingArrow($field) {
        $str = "";
        $sb = $_REQUEST['sb'];
        $st = $_REQUEST['st'];
        if ($field == $sb) {
            if ($st == 0) {
                $str = '<img src="images/up.png" alt="" />';
            } else if ($st == 1) {
                $str = '<img src="images/down.png" alt="" />';
            }
        }
        return $str;
    }

    /* nth level category function */

    function getLevelCateogry($parentId = '0', $level = '', $selected_value, $old_pid) {
        $sqlCountry1 = "SELECT tbl_category.intid, tbl_category.cName, tbl_category.cParentID 
		FROM tbl_category 
		WHERE tbl_category.cParentID = '" . $parentId . "' AND tbl_category.status = 'enabled' ORDER BY tbl_category.intid ASC
		";
        $relCountry1 = mysql_query($sqlCountry1);

        while ($rowCountry1 = mysql_fetch_array($relCountry1)) {

            if ($rowCountry1['cParentID'] == $old_pid && $rowCountry1['cParentID'] != 0) {
                $level = substr($level, 0, strlen($level) - 2);
                $old_pid = $rowCountry1['cParentID'];
            }
            if ($rowCountry1['cParentID'] == 0)
                $level = "";
            $option_value = $rowCountry1['intid'];
            $option_text = $level . $rowCountry1['cName'];

            if (in_array($option_value, $selected_value))
                $selection = "selected='selected'";
            else
                $selection = "";

            echo "<option value=\"$option_value\" $selection>$option_text</option>\n";
            $sqlLocation1 = "SELECT tbl_category.intid, tbl_category.cName, tbl_category.cParentID 
			FROM tbl_category
			WHERE tbl_category.cParentID = '" . $rowCountry1['cParentID'] . "' AND tbl_category.status = 'enabled' 
			ORDER BY tbl_category.cName ASC";
            $relLocation1 = mysql_query($sqlLocation1);
            $numLocation1 = mysql_num_rows($relLocation1);

            if (($old_pid <= $rowCountry1['cParentID']) || $old_pid == 0) {
                $level .= '- ';
                $old_pid = $rowCountry1['cParentID'];
            }
            if ($numLocation1 > 0) {
                $this->getLevelCateogry($rowCountry1['intid'], $level, $selected_value, $rowCountry1['cParentID']);
            }
        }
    }

    // DEBUG float $number , int $decimals = 0 , string $dec_point = '.' , string $thousands_sep = ',' 
    function get_number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    //Years difference between two dates
    function diff_years($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        return $years;
    }

    //Months difference between two dates
    function diff_months($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        return $months;
    }

    //Days difference between two dates
    function diff_days($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        return $days;
    }

    //Hours difference between two dates
    function diff_hours($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        return $hours;
    }

    //Minutes difference between two dates    
    function diff_minutes($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        return $minuts;
    }

    //Seconds difference between two dates 
    function diff_seconds($startdate, $enddate) {
        $diff = abs(strtotime($enddate) - strtotime($startdate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));
        return $seconds;
    }

    //Function to generate random password
    function generate_random_password($len = 6) {
        return substr(md5(rand() . rand()), 0, $len);
    }

    /* Function By Vince for query based CSV Export. */

    function CSVExport($query) {
        //Replace this line with what is appropriate for your DB abstraction layer
        $sql_csv = mysql_query($query) or die("Error: " . mysql_error());

        header("Content-type:text/octect-stream");
        header("Content-Disposition:attachment;filename=order-detail.csv");

        $column = mysql_num_fields($sql_csv);

        // Put the name of all fields
        for ($i = 0; $i < $column; $i++) {
            $l = mysql_field_name($sql_csv, $i);
            if ($l != 'transID') {
                $out .= '"' . $l . '",';
            }
        }
        $out .= "\n";
        echo $out;
        while ($row = mysql_fetch_assoc($sql_csv)) {
            $custname = ucwords($row['name']);
            $price = "AUD " . number_format($row['productPrice'], 2, '.', ' ');
            $shipamt = "AUD " . number_format($row['shippingAmount'], 2, '.', ' ');
            $subtot = $row['Total'] + $row['shippingAmount'];
            $totamt = "AUD " . number_format($subtot, 2, '.', ' ');
            $orddate = $this->ausDateFormat($row['addDate']);

            $atrsql = "select DISTINCT(tag.agGroupName),tpa.paName from tbl_product_attribute tpa LEFT JOIN tbl_attribute_group tag ON tpa.paGroupID=tag.intid where 1 AND tpa.intid IN(" . $row['Properties'] . ") AND tag.status='enabled' GROUP BY tag.intid";
            $attrsql_csv = mysql_query($atrsql);
            $attrcnt = mysql_num_rows($attrsql_csv);
            $attrstr = "";
            if ($attrcnt != 0) {
                while ($attrrow = mysql_fetch_assoc($attrsql_csv)) {
                    $attrstr .= $attrrow['agGroupName'] . '-' . $attrrow['paName'] . "\n";
                }
            }
            print '"' . stripslashes(implode('","', array($row['intid'], $row['orderID'], $custname, $orddate, $row['shippingID'], $row['trackingID'], $row['status'], $row['productName'], $row['pCode'], $attrstr, $row['productQuantity'], $price, $shipamt, $totamt))) . "\"\n";
        }
        exit;
    }

    /* function to display date in australian date format */

    function ausDateFormat($date) {
        date_default_timezone_set('Australia/Canberra');
        $dNewDate = strtotime($date);
        $convertdate = date('d/m/Y g:i A', $dNewDate);
        return $convertdate;
    }

    /* Function to find difference in minutes only */

    function get_time_difference($start, $end) {
        $uts['start'] = strtotime($start);
        $uts['end'] = strtotime($end);
        if ($uts['start'] !== -1 && $uts['end'] !== -1) {
            if ($uts['end'] >= $uts['start']) {
                $diff = $uts['end'] - $uts['start'];
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                $diff_minutes = ($hours * 60) + ($days * 24 * 60) + ($minutes);

                return($diff_minutes);
            }
            else {
                $diff = $uts['start'] - $uts['end'];
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                $diff_minutes = 0 - (($hours * 60) + ($days * 24 * 60) + ($minutes) );

                return($diff_minutes - 1);
            }
        }
        else {
            trigger_error("Invalid date/time data detected", E_USER_WARNING);
        }
        return( false );
    }

    /* combination of multiple arrays */

    function array_cartesian_product($arrays) {
        $result = array();
        $arrays = array_values($arrays);
        $sizeIn = sizeof($arrays);
        $size = $sizeIn > 0 ? 1 : 0;
        foreach ($arrays as $array)
            $size = $size * sizeof($array);
        for ($i = 0; $i < $size; $i ++) {
            $result[$i] = array();
            for ($j = 0; $j < $sizeIn; $j ++)
                array_push($result[$i], current($arrays[$j]));
            for ($j = ($sizeIn - 1); $j >= 0; $j --) {
                if (next($arrays[$j]))
                    break;
                elseif (isset($arrays[$j]))
                    reset($arrays[$j]);
            }
        }
        return $result;
    }

    // Function to extract numbers out of string
    function extract_numbers($string) {
        preg_match_all('/([\d]+)/', $string, $match);
        return $match[0];
    }

    //Used for invoice
    function generate_numbers($start, $count, $digits) {
        $result = array();
        for ($n = $start; $n < $start + $count; $n++) {

            $result = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }
        return $result;
    }

    //Function for AES-Complaint data encryption
    function encrypt($string) {
        $mcrypt_iv = $this->mcrypt_iv;
        $salt = $this->salt;
        $mcrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $salt, $string, MCRYPT_MODE_CBC, $mcrypt_iv);

        $encoded = base64_encode($mcrypted);

        return $encoded;
    }

    //Function for AES-Complaint data decryption
    function decrypt($hash) {

        $mcrypt_iv = $this->mcrypt_iv;
        $salt = $this->salt;

        $basedecoded = base64_decode($hash);

        $mcrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $salt, $basedecoded, MCRYPT_MODE_CBC);

        $decrypted = $mcrypted;
        $dec_s2 = strlen($decrypted);
        $padding = ord($decrypted[$dec_s2 - 1]);
        $decrypted = substr($decrypted, 0, -$padding);

        return $mcrypted;
    }

    //Function to check passcode
    function chk_passcode($passcode) {
        global $dclass;
        $chkp = $dclass->select("intid, status", "tbluser", " AND (passcode = '" . $passcode . "') ");
        if (count($chkp) > 0) {
            $passcode = $gnrl->generate_random_password(4);
            chk_passcode($passcode);
        } else {
            return $passcode;
        }
    }

    //get count
    function get_total_records($table) {
        global $dclass;
        $res = $dclass->select("1", $table, "");
        return count($res);
    }

    function generate_password($length = 10) {
        $random = "";
        srand((double) microtime() * 1000000);
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_list .= "abcdefghijklmnopqrstuvwxyz";
        $char_list .= "1234567890";

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($char_list, (rand() % (strlen($char_list))), 1);
        }
        return $random;
    }

    function addMessage($msg, $type) {
        $_SESSION['type'] = $type;
        $_SESSION['msg'] = $msg;
    }

    function check_feature_avail($member_id, $app_id, $feature_id) {
        global $dclass;
        $res = $dclass->select("1", "tblmember_app_features", " AND member_id='" . $member_id . "' AND app_id='" . $app_id . "' AND feature_id='" . $feature_id . "' ");
        if (count($res) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_app($member_id) {
        global $dclass;
        $res = $dclass->select("intid", "tblmember_apps", " AND member_id='" . $member_id . "' AND app_status='active' ");

        if (count($res) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_common_access($member_id) {
        global $dclass;
        $res = $dclass->select("intid", "tblmember_apps", " AND member_id='" . $member_id . "'  ");
        if (count($res) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_section_access($member_id, $feature_id) {
        global $dclass;
        $res = $dclass->select("intid", "tblmember_apps", " AND member_id='" . $member_id . "' AND app_status='active' ");
        $deny_cnt = 0;
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $chk = $this->check_feature_avail($member_id, $res[$i]['intid'], $feature_id);
                if (!$chk) {
                    $deny_cnt++;
                }
            }
            if ($deny_cnt == count($res)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    function check_access($member_id, $feature_id = '') {
        global $dclass;
        $res = $dclass->select("intid", "tblmember_apps a INNER JOIN tblmember_app_features f", " AND member_id='" . $member_id . "' ");
        $deny_cnt = 0;
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $chk = $this->check_feature_avail($member_id, $res[$i]['intid'], $feature_id);
                if (!$chk) {
                    $deny_cnt++;
                }
            }
            if ($deny_cnt == count($res)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    function font_select($select_tag_id, $sel_id = '') {
        global $dclass;
        $res = $dclass->select("fname", "tblfonts", "AND status='active' ");
        if (count($res) > 0) {
            $sel = "";
            $sel .= '<select class="form-control" id="' . $select_tag_id . '" name="' . $select_tag_id . '">';
            for ($i = 0; $i < count($res); $i++) {

                if ($res[$i]['fname'] == trim($sel_id))
                    $selected = 'selected="selected"';
                else {
                    $selected = '';
                }
                $sel .= '<option value="' . $res[$i]["fname"] . '" ' . $selected . '>' . $res[$i]["fname"] . '</option>';
            }
            $sel .= '</select>';

            return $sel;
        } else {
            return 'No fonts found';
        }
    }

    function tutorial_animation_slide($sel_id = '') {
        global $dclass;
        $res = $dclass->select("intid,name", "tbltutorial_animations", "AND status='active' ");
        if (count($res) > 0) {
            $sel = "";
            for ($i = 0; $i < count($res); $i++) {

                if ($res[$i]['name'] == trim($sel_id))
                    $selected = 'active';
                else if ($sel_id == '' && $i == 0) {
                    $selected = 'active';
                } else {
                    $selected = '';
                }
                $sel .= '<div class="slide ' . $selected . '" id="tutorial_animation_' . $res[$i]["intid"] . '" onclick="select_tutorial_animation(' . $res[$i]["intid"] . ')" >' . $res[$i]["name"] . '</div>';
            }
            return $sel;
        } else {
            return 'No fonts found';
        }
    }

    function plan_select_slide($select_tag_id, $sel_id = '') {
        global $dclass;
        $res = $dclass->select("*", "tblpackages", "AND status='active' ");
        if (count($res) > 0) {
            $sel = "";
            $desc = "";
            for ($i = 0; $i < count($res); $i++) {

                if ($res[$i]['intid'] == trim($sel_id)) {
                    $selected = 'active';
                    $desc_style = '';
                } else if ($sel_id == '' && $i == 0) {
                    $selected = 'active';
                    $desc_style = '';
                } else {
                    $selected = '';
                    $desc_style = 'style="display:none;"';
                }

                if ($res[$i]['pintval'] == 'yearly')
                    $pintval = 'Year';
                else if ($res[$i]['pintval'] == 'monthly')
                    $pintval = 'Month';


                $sel .= '<div class="slide ' . $selected . '" id="plan_' . $res[$i]["intid"] . '" onclick="select_plan(' . $res[$i]["intid"] . ')" >' . $res[$i]["pname"] . '</div>';

                if ($res[$i]["intid"] == 1)
                    $desc .= '<div class="smallfont" id="splan_' . $res[$i]["intid"] . '" ' . $desc_style . '><small>First ' . $this->get_number_format($res[$i]['plimit']) . ' actions free</small></div>';
                else
                    $desc .= '<div class="smallfont" id="splan_' . $res[$i]["intid"] . '" ' . $desc_style . '><small>' . CUR . $this->get_number_format($res[$i]['pcost']) . '/' . $pintval . ' with ' . $this->get_number_format($res[$i]['plimit']) . ' actions</small></div>';
            }
            return $sel . "|" . $desc;
        }
        else {
            return 'No fonts found';
        }
    }

    function role_select_slide($role, $sel_id = '') {
        $sel .= '<div class="slide ';
        if ($role == 'technical') {
            $sel .= 'active';
        }
        $sel .= '" id="role_technical_' . $sel_id . '" onclick="select_role(this.id,\'technical\')" >Technical</div>';
        $sel .= '<div class="slide ';
        if ($role == 'finance') {
            $sel .= 'active';
        }
        $sel .= '" id="role_finance_' . $sel_id . '" onclick="select_role(this.id,\'finance\')" >Finance</div>';
        $sel .= '<div class="slide ';
        if ($role == 'support') {
            $sel .= 'active';
        }
        $sel .= '" id="role_support_' . $sel_id . '" onclick="select_role(this.id,\'support\')" >Support</div>';
        $sel .= '<div class="slide ';
        if ($role == 'marketing') {
            $sel .= 'active';
        }
        $sel .= '" id="role_marketing_' . $sel_id . '" onclick="select_role(this.id,\'marketing\')" >Marketing</div>';

        $sel .= '<div class="slide ';
        if ($role == 'admin') {
            $sel .= 'active';
        }
        $sel .= '" id="role_admin_' . $sel_id . '" onclick="select_role(this.id,\'admin\')" >Admin</div>';
        return $sel;
    }

    function font_select_slide($select_tag_id, $sel_id = '') {
        global $dclass;
        $res = $dclass->select("intid,fname", "tblfonts", "AND status='active' ");
        if (count($res) > 0) {
            $sel = "";
            for ($i = 0; $i < count($res); $i++) {

                if ($res[$i]['fname'] == trim($sel_id))
                    $selected = 'active';
                else if ($sel_id == '' && $i == 0) {
                    $selected = 'active';
                } else {
                    $selected = '';
                }
                $sel .= '<div class="slide ' . $selected . '" id="font_' . $res[$i]["intid"] . '" onclick="select_font(' . $res[$i]["intid"] . ')" >' . $res[$i]["fname"] . '</div>';
            }
            return $sel;
        } else {
            return 'No fonts found';
        }
    }

    function font_select_agents($select_tag_id, $sel_id = '') {
        global $dclass;
        $res = $dclass->select("*", "tblmember", " AND parent_id = '" . $_SESSION['custid'] . "' AND role = 'support' AND status = 'active' ");
        if (count($res) > 0) {
            $sel = "";
            for ($i = 0; $i < count($res); $i++) {
                if ($res[$i]['intid'] == trim($sel_id))
                    $selected = 'active';
                else {
                    $selected = '';
                }
                $sel .= '<div class="slide ' . $selected . '" id="font_' . $res[$i]["intid"] . '" onclick="select_font(' . $res[$i]["intid"] . ')" >' . $res[$i]["fname"] . ' ' . $res[$i]["lname"] . '</div>';
            }
            return $sel;
        } else {
            return 'No fonts found';
        }
    }

    function all_apps_slide($member_id, $feature_id = '', $select_tag_id, $sel_id = '', $url = '', $app_type = '') {

        global $dclass;


        if ($url == 'apps' || $url == 'add-apps') {
            if ($app_type != '')
                $ap = "AND app_type='" . $app_type . "' ";

            $res = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' $ap  order by app_add_date");
        }else {
            if ($app_type != '')
                $ap = "AND a.app_type='" . $app_type . "' ";

            $fe = "AND f.feature_id='" . $feature_id . "'";

            $res = $dclass->select("a.*", "tblmember_apps a INNER JOIN tblmember_app_features f ON a.intid=f.app_id", " AND a.member_id='" . $member_id . "' $ap  $fe  order by a.app_add_date");
        }

        if (count($res) > 0) {
            $sel = "";
            $deny_cnt = 0;

            if ($sel_id == '')
                $default_select = 'active';
            else
                $default_select = '';
            $page = 0;
            for ($i = 0; $i < count($res); $i++) {
                if ($res[$i]['app_logo'] != '' && is_file(APP_LOGO . "/" . $res[$i]['app_logo']))
                    $app_img_path = APP_LOGO . "/" . $res[$i]['app_logo'];
                else
                    $app_img_path = "img/no_image_available.jpg";

                if ($res[$i]['intid'] == $sel_id)
                    $selected = 'active';
                else {
                    $selected = '';
                }


                $sel .= '<div class="slide ' . $selected . '" id="sl-' . $res[$i]["intid"] . '" onclick="select_app(' . $res[$i]["intid"] . ')"><img src="' . $app_img_path . '" alt="' . $res[$i]["app_name"] . '" title="' . $res[$i]["app_name"] . '"/></div>';
            }
            if ($deny_cnt == count($res))
                $sel = '';

            return $sel;
        }else {
            return 'No apps found';
        }
    }

    function get_page_num($member_id, $feature_id, $sel_id, $app_type) {
        global $dclass;
        $ap = "AND a.app_type='" . $app_type . "' ";

        $fe = "AND f.feature_id='" . $feature_id . "'";

        $res = $dclass->select("a.*", "tblmember_apps a INNER JOIN tblmember_app_features f ON a.intid=f.app_id", " AND a.member_id='" . $member_id . "' $ap  $fe  order by a.app_add_date");

        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                if ($res[$i]['intid'] == $sel_id) {
                    if ($i < 5)
                        $page = 0;
                    else
                        $page = ($i - 5) + 3;

                    break;
                }
            }
        }else {
            $page = 0;
        }

        return $page;
    }

    function all_apps_select($member_id, $feature_id = '', $select_tag_id, $sel_id = '', $url = '', $app_type = '') {

        global $dclass;


        if ($url == 'apps' || $url == 'add-apps') {
            if ($app_type != '')
                $ap = "AND app_type='" . $app_type . "' ";

            $res = $dclass->select("*", "tblmember_apps", " AND member_id='" . $member_id . "' $ap  AND app_status='active' order by app_add_date");
        }else {
            if ($app_type != '')
                $ap = "AND a.app_type='" . $app_type . "' ";
            $res = $dclass->select("a.*", "tblmember_apps a INNER JOIN tblmember_app_features f ON a.intid=f.app_id", " AND a.member_id='" . $member_id . "' $ap  AND f.feature_id='" . $feature_id . "'  order by a.app_add_date");
        }

        if (count($res) > 0) {
            $sel = "";
            $deny_cnt = 0;
            $sel .= '<select name="sel_app_id" class="sel_app_id my-dropdown form-control"  id="' . $select_tag_id . '" onchange="select_app()" style=" margin:-7px 0 0 0;">';

            if ($sel_id == '')
                $default_select = 'selected="selected"';
            else
                $default_select = '';

            if ($url == 'apps' || $url == 'add-apps') {
                $sel .= '<option value="" ' . $default_select . ' >Select App</option>';
            }
            if ($url == 'analytics') {
                $sel .= '<option value="" ' . $default_select . '>All</option>';
            }

            for ($i = 0; $i < count($res); $i++) {

                if ($res[$i]['intid'] == $sel_id)
                    $selected = 'selected="selected"';
                else {
                    $selected = '';
                }
                $sel .= '<option value="' . $res[$i]["intid"] . '" ' . $selected . '>' . $res[$i]["app_name"] . '</option>';
            }
            $sel .= '</select>';
            if ($deny_cnt == count($res))
                $sel = '';

            return $sel;
        }else {
            return 'No apps found';
        }
    }

    function get_usa_date($date, $default = "m/d/Y") {
        return date($default, strtotime($date));
    }

    function check_attachment($id) {
        global $dclass;
        $res = $dclass->select("1", "tblapp_support_attachment", " AND support_id='" . $id . "' ");
        if (count($res) > 0)
            return true;
        else
            return false;
    }

    function get_attachment($id, $table = "tblapp_support_attachment") {
        global $dclass;
        $res = $dclass->select("*", "tblapp_support_attachment", "  AND support_id='" . $id . "'");
        return $res;
    }

    function get_labels($app_id = '', $app_type = '') {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND a.intid='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND a.app_type='" . $app_type . "' ";
        }

        $apps = $dclass->select("a.app_name", "tblmember_apps a inner join tblmember_app_features f on a.intid=f.app_id", " $wh AND a.member_id='" . $_SESSION['custid'] . "'  AND a.app_status='active' AND f.feature_id='6' GROUP BY a.intid ");

        for ($i = 0; $i < count($apps); $i++) {
            extract($apps[$i]);
            $data[$i] = $this->subString($app_name, 15);
        }

        return $data;
    }

    function get_colors() {
        $colors = array('#0b62a4', '#D58665', '#37619d', '#2D9C2F', '#A87D8E', '#2D619C', '#2D9C2F');
        return $colors;
    }

    function get_last_days_json($app_id = '', $app_type = '', $table, $field = '', $count = 9) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $op = "";
                $from_date = 'CURDATE()';
                $fromr_date = 'CURDATE()';
            } else {
                $op = ",";
                $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
            }


            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            if ($table == 'tblapp_analytics') { //general analytics
                if ($field == '')
                    $field = 'app_display_count';

                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, s.dtadd as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  AND a.app_status='active' GROUP BY DAY(s.dtadd),s.app_id  ORDER BY DAY(s.dtadd) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, s.dtadd as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  AND a.app_status='active'  GROUP BY DAY(s.dtadd) ORDER BY DAY(s.dtadd) DESC ");
                }
            } else { //device wise analytics
                if ($field == '')
                    $field = 'intid';

                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, s.dtadd as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  AND a.app_status='active' GROUP BY DAY(s.dtadd),s.app_id  ORDER BY DAY(s.dtadd) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, s.dtadd as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  AND a.app_status='active'  GROUP BY DAY(s.dtadd) ORDER BY DAY(s.dtadd) DESC ");
                }
            }
            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }


        return $data;
    }

    function get_last_weeks_json($app_id = '', $app_type = '', $table, $field = '', $count = 8) {
        global $dclass;

        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }


        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL ' . $to_cnt . ' DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");

            $cnt = 0;
            $date_range = array();
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            if ($table == 'tblapp_analytics') {
                if ($field == '')
                    $field = 'app_display_count';
                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh GROUP BY WEEK(dt),s.app_id  ORDER BY WEEK(dt) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh  GROUP BY WEEK(dt) ORDER BY WEEK(dt) DESC ");
                }
            } else {
                if ($field == '')
                    $field = 'intid';
                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh GROUP BY WEEK(dt),s.app_id  ORDER BY WEEK(dt) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh  GROUP BY WEEK(dt) ORDER BY WEEK(dt) DESC ");
                }
            }
            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    function get_last_months_json($app_id = '', $app_type = '', $table, $field = '', $count = 12) {
        global $dclass;

        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }


        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = "DATE_FORMAT(CURDATE(), '%m-%Y')";
            } else {
                $from_date = "DATE_FORMAT((CURDATE() - INTERVAL $i MONTH), '%m-%Y')";
            }

            $para_from_date = date("m-Y", strtotime(date("Y-m") . "- $i month"));
            $to_date = 'CURDATE() - INTERVAL ' . ($i + 1) . ' MONTH';

            $dtres = $dclass->query("select " . $from_date . "  as dt");

            $cnt = 0;
            $date_range = array();
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            if ($table == 'tblapp_analytics') {
                if ($field == '')
                    $field = 'app_display_count';
                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, " . $from_date . " as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd, '%m-%Y') = '" . $para_from_date . "'  $wh  GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y'),s.app_id ORDER BY WEEK(dt) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, " . $from_date . " as dt, SUM(s." . $field . ") as download_count", "tblapp_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd, '%m-%Y') = '" . $para_from_date . "' $wh  GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y') ORDER BY WEEK(dt) DESC ");
                }
            } else {
                if ($field == '')
                    $field = 'intid';
                if ($app_id == '') {
                    $res = $dclass->select("a.app_name, " . $from_date . " as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd, '%m-%Y') = '" . $para_from_date . "' $wh  GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y'),s.app_id ORDER BY WEEK(dt) DESC ");
                } else {
                    $res = $dclass->select("a.app_name, " . $from_date . " as dt, count(s." . $field . ") as download_count", "tblapp_os_ver s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd, '%m-%Y') = '" . $para_from_date . "' $wh  GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y') ORDER BY WEEK(dt) DESC ");
                }
            }

            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //for os version donut charts
    function get_os_json($app_id = '', $app_type = '') {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        if ($app_id == '') {
            $res = $dclass->select("v.os_type, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.os_version ");
        } else {
            $res = $dclass->select("v.os_type, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.os_version ");
        }
        $data = array();
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $data[$i]['label'] = '' . strtoupper($res[$i]['os_type']) . " " . $res[$i]['os_version'];
                $data[$i]['value'] = intval($res[$i]['version_count']);
            }
        }
        return $data;
    }

    //for os version knob charts
    function get_os_count($app_id = '', $app_type = '') {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        if ($app_id == '') {
            $res = $dclass->select("v.os_type, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.os_version ");
        } else {
            $res = $dclass->select("v.os_type, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.os_version ");
        }
        $data = array();
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $data[$i]['label'] = '' . strtoupper($res[$i]['os_type']) . " " . $res[$i]['os_version'];
                $data[$i][$res[$i]['os_version']] = intval($res[$i]['version_count']);
            }
        }
        return $data;
    }

    //For geographical downloads
    function get_geo_json($app_id = '', $app_type = '') {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }

        if ($app_id == '') {
            $res = $dclass->select("count(v.intid) as download_count, c.countries_iso_code_2 as iso", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", " $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id ");
        } else {
            $res = $dclass->select("count(v.intid) as download_count, c.countries_iso_code_2 as iso", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "  $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id ");
        }
        $data = array();
        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                $data[$res[$i]['iso']] = intval($res[$i]['download_count']);
            }
        }
        return $data;
    }

    //for more apps visit days
    function get_more_apps_last_days_json($app_id = '', $app_type = '', $field, $count = 9) {
        global $dclass;

        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }


        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $op = "";
                $from_date = 'CURDATE()';
                $fromr_date = 'CURDATE()';
            } else {
                $op = ",";
                $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
            }


            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.app_name, s.dtadd as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  GROUP BY DAY(s.dtadd),s.app_id ORDER BY DAY(s.dtadd) DESC ");
            } else {
                $res = $dclass->select("a.app_name, s.dtadd as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd = " . $from_date . ") $wh  GROUP BY DAY(s.dtadd) ORDER BY DAY(s.dtadd) DESC ");
            }

            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //for more apps visit days
    function get_more_apps_last_weeks_json($app_id = '', $app_type = '', $field, $count = 8) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL ' . $to_cnt . ' DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");


            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh  GROUP BY WEEK(dt),s.app_id  ORDER BY WEEK(dt) DESC ");
            } else {
                $res = $dclass->select("a.app_name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ")  $wh  GROUP BY WEEK(dt) ORDER BY WEEK(dt) DESC ");
            }

            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //for more apps visit days
    function get_more_apps_last_months_json($app_id = '', $app_type, $field, $count = 8) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = "DATE_FORMAT(CURDATE(), '%m-%Y')";
            } else {
                $from_date = "DATE_FORMAT((CURDATE() - INTERVAL $i MONTH), '%m-%Y')";
            }

            $para_from_date = date("m-Y", strtotime(date("Y-m") . "- $i month"));

            $to_date = 'CURDATE() - INTERVAL ' . ($i + 1) . ' MONTH';

            $dtres = $dclass->query("select " . $from_date . " as dt");
            $cnt = 0;
            $date_range = array();
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            if ($app_id == '') {
                $res = $dclass->select("a.app_name, " . $from_date . " as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd,'%m-%Y')='" . $para_from_date . "' $wh GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y'),s.app_id ORDER BY MONTH(dt) DESC ");
            } else {
                $res = $dclass->select("a.app_name, " . $from_date . " as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmember_apps a on s.app_id=a.intid", " AND DATE_FORMAT(s.dtadd,'%m-%Y')='" . $para_from_date . "' $wh  GROUP BY DATE_FORMAT(s.dtadd,'%m-%Y') ORDER BY MONTH(dt) DESC ");
            }

            $data[$i]['period'] = $date_range;

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data[$i][$this->subString($res[$j]['app_name'], 15)] = intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //function to get total sessions/downloads/countries/osversion/promte apps app plus date wise or app wise total
    function get_total($app_id = '', $app_type, $date_left = '', $date_right = '', $table, $field, $cn_type, $date_type = '') {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        $grp_by = '';
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        } else {
            
        }
        if ($date_left != '') {
            if ($date_right != '') {
                $wh .= " AND (DATE_FORMAT(s.dtadd,'%Y-%m-%d') BETWEEN '" . $date_right . "'  AND '" . $date_left . "')";
                if ($app_id != '') {
                    if ($date_type == 'monthly') {
                        $grp_by = "GROUP BY month(dt)";
                    } else if ($date_type == 'weekly') {
                        $grp_by = "GROUP BY week(dt)";
                    } else {
                        $grp_by = "GROUP BY day(dt)";
                    }
                } else {
                    $grp_by = "GROUP BY week(dt),s.app_id";
                }
            } else {
                if ($date_type == 'monthly') {
                    $wh .= " AND DATE_FORMAT(s.dtadd,'%b %Y')='" . $date_left . "'";
                    if ($app_id != '') {
                        $grp_by = "GROUP BY DATE_FORMAT(dt,'%b %Y')";
                    } else {
                        $grp_by = "GROUP BY DATE_FORMAT(dt,'%b %Y'),s.app_id";
                    }
                } else
                    $wh .= " AND DATE_FORMAT(s.dtadd,'%Y-%m-%d')='" . $date_left . "'";
            }
        }else {
            if ($app_id != '')
                $grp_by = 'GROUP BY s.app_id';
            else
                $grp_by = 'GROUP BY s.member_id';
        }

        if ($app_id != '' && $table == 'tblmore_app_analytics') {
            $grp_by = 'GROUP BY s.app_id';
        } else if ($table == 'tblmore_app_analytics') {
            $grp_by = 'GROUP BY s.member_id';
        }

        if ($app_type != '') {

            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        $date_field = '';

        if ($date_type != '') {
            if ($date_type == 'monthly' || $date_type == 'daily') {
                $date_field = "s.dtadd as dt";
            } else {
                $date_field = "CONCAT($date_left,'-',$date_right) as dt";
            }
        }
        $res = $dclass->select("a.app_name,s.os_type, $date_field, $cn_type(s.$field) as count", "$table s inner join tblmember_apps a on s.app_id=a.intid", "   $wh  $grp_by ORDER BY day(dt) DESC ");

        if (count($res) > 0) {
            return $res[0]['count'];
        } else {
            return 0;
        }
    }

    //function to get total group by country/os version
    function get_total_group_by($app_id = '', $app_type, $table, $field, $cn_type, $for) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            if ($for == 'country') {
                $wh .= " AND s.app_id='" . $app_id . "' AND s.app_country_id != '0'";
                $grp_by = " GROUP BY  s.app_country_id";
                $ord_by = "order by s.app_country_id DESC ";
            } else {
                $wh .= " AND s.app_id='" . $app_id . "' ";
                $grp_by = " GROUP BY  s.os_version";
                $ord_by = "order by s.os_version DESC ";
            }
        } else {
            if ($for == 'country') {
                $wh .= " AND s.app_country_id != '0'";
                $grp_by = " GROUP BY  s.app_country_id";
                $ord_by = "order by s.app_country_id DESC ";
            } else {
                $grp_by = " GROUP BY  s.os_version";
                $ord_by = "order by s.os_version DESC ";
            }
        }

        if ($app_type != '') {

            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        $res = $dclass->select("count(*) as cnt", "(SELECT s.app_country_id, s.member_id, a.app_name, s.os_type, s.dtadd AS dt, $cn_type( s.$field ) AS count
   		FROM $table s INNER JOIN tblmember_apps a ON s.app_id = a.intid WHERE 1  $wh  $grp_by $ord_by) results");

        if (count($res) > 0) {
            return $res[0]['cnt'];
        } else {
            return 0;
        }
    }

    //get all dates
    function get_dates($type, $count = 9) {
        global $dclass;

        switch ($type) {
            case "daily":
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $op = "";
                        $from_date = 'CURDATE()';
                        $fromr_date = 'CURDATE()';
                    } else {
                        $op = ",";
                        $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                        $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
                    }


                    $dtres = $dclass->query("select " . $from_date . " as dt");

                    $cnt = 0;
                    while ($rs = $dclass->fetchArray($dtres)) {
                        $date_range[$i] = $rs['dt'];
                        $cnt++;
                    }
                }
                break;
            case "weekly":
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $from_date = 'CURDATE()';
                    } else {
                        $from_cnt = 7 * $i;
                        $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
                    }
                    $to_cnt = (7 * ($i + 1)) - 1;
                    $to_date = 'CURDATE() - INTERVAL ' . $to_cnt . ' DAY';

                    $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");

                    $cnt = 0;
                    while ($rs = $dclass->fetchArray($dtres)) {
                        $date_range[$i] = $rs['dt'];
                        $cnt++;
                    }
                }
                break;
            case "monthly":
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $from_date = 'CURDATE()';
                    } else {
                        $from_date = 'CURDATE() - INTERVAL ' . $i . ' MONTH';
                    }
                    $to_date = 'CURDATE() - INTERVAL ' . ($i + 1) . ' MONTH';
                    $sql = "select DATE_FORMAT(" . $from_date . ",'%b %Y') as dt";

                    $dtres = $dclass->query($sql);
                    $cnt = 0;
                    while ($rs = $dclass->fetchArray($dtres)) {
                        $date_range[$i] = $rs['dt'];
                        $cnt++;
                    }
                }
                break;
        }

        return $date_range;
    }

    function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    function delete_more_app($more_app_id) {
        global $dclass;
        $res = $dclass->select("more_app_custom_image as img", "tblapp_moreapp_rel", " AND more_app_id='" . $more_app_id . "' ");

        if (count($res) > 0) {
            for ($i = 0; $i < count($res); $i++) {
                unlink("files/more-apps/" . $res[$i]['img']);
                unlink("files/more-apps/thumbnails/" . $res[$i]['img']);
            }
            $dclass->delete("tblapp_moreapp_rel", " more_app_id='" . $more_app_id . "' ");
        }
    }

    //progress bar
    function get_progress($app_id = '', $app_type = '', $field, $count = 8) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL ' . $to_cnt . ' DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");
            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY WEEK(dt),s.more_app_id  ORDER BY WEEK(dt) DESC ");
            } else {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY WEEK(dt), s.more_app_id ORDER BY WEEK(dt) DESC ");
            }

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $data[$j]['day'] = $res[$j]['dt'];
                    $data[$j]['name'] = $res[$j]['name'];
                    $data[$j]['count'] = intval($res[$j]['download_count']);
                }
            }
        }

        return $data;
    }

    //for os version donut charts
    function get_os_json_days($app_id = '', $app_type = '', $count = 9) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $op = "";
                $from_date = 'CURDATE()';
                $fromr_date = 'CURDATE()';
            } else {
                $op = ",";
                $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
            }


            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("v.os_type, v.dtadd as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd = " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY DAY(dt), v.os_version ORDER BY DAY(dt) DESC ");
            } else {
                $res = $dclass->select("v.os_type, v.dtadd as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd = " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY DAY(dt), v.os_version ORDER BY DAY(dt) DESC ");
            }


            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $data[$j]['label'] = strtoupper($res[$j]['os_type']) . " " . $res[$j]['os_version'];
                    $data[$j]['value'] = intval($res[$j]['version_count']);
                }
            }
        }
        return $data;
    }

    //for os version donut charts
    function get_os_json_week($app_id = '', $app_type = '', $count = 8) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        // echo $app_type; exit;
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL ' . $to_cnt . ' DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");
            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("v.os_type, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY WEEK(dt), v.os_version ORDER BY WEEK(dt) DESC ");
            } else {
                $res = $dclass->select("v.os_type, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY WEEK(dt), v.os_version ORDER BY WEEK(dt) DESC ");
            }
            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $data[$j]['label'] = strtoupper($res[$j]['os_type']) . " " . $res[$j]['os_version'];
                    $data[$j]['value'] = intval($res[$j]['version_count']);
                }
            }
        }
        return $data;
    }

    //for os version donut charts
    function get_os_json_month($app_id = '', $app_type = '', $count = 12) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_date = 'CURDATE() - INTERVAL ' . $i . ' MONTH';
            }
            $to_date = 'CURDATE() - INTERVAL ' . ($i + 1) . ' MONTH';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");
            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("v.os_type, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY MONTH(dt), v.os_version ORDER BY MONTH(dt) DESC ");
            } else {
                $res = $dclass->select("v.os_type, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, count(v.intid) as version_count, v.os_version", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid", " AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") AND v.member_id='" . $_SESSION['custid'] . "' $wh GROUP BY MONTH(dt), v.os_version ORDER BY MONTH(dt) DESC ");
            }
            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $data[$j]['label'] = strtoupper($res[$j]['os_type']) . " " . $res[$j]['os_version'];
                    $data[$j]['value'] = intval($res[$j]['version_count']);
                }
            }
        }
        return $data;
    }

    //For geographical downloads
    function get_geo_json_days($app_id = '', $app_type = '', $count = 1) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $op = "";
                $from_date = 'CURDATE()';
                $fromr_date = 'CURDATE()';
            } else {
                $op = ",";
                $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
            }
            $to_date = 'CURDATE() - INTERVAL 9 DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("count(v.intid) as download_count, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, c.countries_iso_code_2 as iso,c.countries_name as country", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id, WEEK(dt) ORDER BY DAY(dt) DESC ");
            } else {
                $res = $dclass->select("count(v.intid) as download_count, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, c.countries_iso_code_2 as iso,c.countries_name as country", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id, WEEK(dt) ORDER BY DAY(dt) DESC");
            }
            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data["areas"][$res[$j]['iso']]['value'] = intval($res[$j]['download_count']);
                        $data["areas"][$res[$j]['iso']]['tooltip']["content"] = "<span style='font-weight:bold;'>" . $res[$j]['country'] . "</span> " . intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //For geographical downloads
    function get_geo_json_week($app_id = '', $app_type = '', $count = 1) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL 42 DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");


            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("count(v.intid) as download_count, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, c.countries_iso_code_2 as iso,c.countries_name as country", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id, WEEK(dt) ORDER BY WEEK(dt) DESC ");
            } else {
                $res = $dclass->select("count(v.intid) as download_count, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, c.countries_iso_code_2 as iso,c.countries_name as country", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id, WEEK(dt) ORDER BY WEEK(dt) DESC");
            }
            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data["areas"][$res[$j]['iso']]['value'] = intval($res[$j]['download_count']);
                        $data["areas"][$res[$j]['iso']]['tooltip']["content"] = "<span style='font-weight:bold;'>" . $res[$j]['country'] . "</span> " . intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

//For geographical downloads
    function get_geo_json_month($app_id = '', $app_type = '', $count = 1) {
        global $dclass;
        $wh = '';
        if ($app_id != '') {
            $wh .= " AND v.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND v.os_type='" . $app_type . "' ";
        }
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_date = 'CURDATE() - INTERVAL ' . $i . ' MONTH';
            }
            $to_date = 'CURDATE() - INTERVAL 12 MONTH';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");

            $cnt = 0;
            $date_range = array();
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            $res = $dclass->select("count(v.intid) as download_count, CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt, c.countries_iso_code_2 as iso,c.countries_name as country", "tblapp_os_ver v inner join tblmember_apps a on v.app_id=a.intid inner join tblcountries c on v.app_country_id=c.countries_id", "AND (v.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh AND v.member_id='" . $_SESSION['custid'] . "' GROUP BY v.app_country_id, MONTH(dt) ORDER BY MONTH(dt) DESC");

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    if ($res[$j]['dt'] == $date_range) {
                        $data["areas"][$res[$j]['iso']]['value'] = intval($res[$j]['download_count']);
                        $data["areas"][$res[$j]['iso']]['tooltip']["content"] = "<span style='font-weight:bold;'>" . $res[$j]['country'] . "</span> " . intval($res[$j]['download_count']);
                    }
                }
            }
        }
        return $data;
    }

    //progress bar
    function get_progress_days($app_id = '', $app_type = '', $field, $count = 1) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $op = "";
                $from_date = 'CURDATE()';
                $fromr_date = 'CURDATE()';
            } else {
                $op = ",";
                $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
                $fromr_date = 'CURDATE(), INTERVAL ' . $i . ' DAY';
            }


            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.more_app_name as name, s.dtadd as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd=" . $from_date . ") $wh GROUP BY DAY(dt),s.more_app_id  ORDER BY DAY(dt) DESC ");
            } else {
                $res = $dclass->select("a.more_app_name as name, s.dtadd as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd=" . $from_date . ") $wh GROUP BY DAY(dt),s.more_app_id  ORDER BY DAY(dt) DESC ");
            }

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $data[$j]['day'] = date("M d", strtotime($res[$j]['dt']));
                    $data[$j]['name'] = $res[$j]['name'];
                    $data[$j]['count'] = intval($res[$j]['download_count']);
                }
            }
        }

        return $data;
    }

    //progress bar
    function get_progress_week($app_id = '', $app_type = '', $field, $count = 8) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_cnt = 7 * $i;
                $from_date = 'CURDATE() - INTERVAL ' . $from_cnt . ' DAY';
            }
            $to_cnt = (7 * ($i + 1)) - 1;
            $to_date = 'CURDATE() - INTERVAL 42 DAY';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");
            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' | ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY WEEK(dt),s.more_app_id  ORDER BY WEEK(dt) DESC ");
            } else {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' | ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY WEEK(dt), s.more_app_id ORDER BY WEEK(dt) DESC ");
            }

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $dr = explode("|", $res[$j]['dt']);
                    $data[$j]['day'] = date("M d", strtotime($dr[0])) . " - " . date("M d", strtotime($dr[1]));
                    $data[$j]['name'] = $res[$j]['name'];
                    $data[$j]['count'] = intval($res[$j]['download_count']);
                }
            }
        }

        return $data;
    }

    function get_progress_month($app_id = '', $app_type = '', $field, $count = 1) {
        global $dclass;
        $wh = " AND s.member_id='" . $_SESSION['custid'] . "' ";
        if ($app_id != '') {
            $wh .= " AND s.app_id='" . $app_id . "' ";
        }

        if ($app_type != '') {
            $wh .= " AND s.os_type='" . $app_type . "' ";
        }

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $from_date = 'CURDATE()';
            } else {
                $from_date = 'CURDATE() - INTERVAL ' . $i . ' MONTH';
            }
            $to_date = 'CURDATE() - INTERVAL ' . ($i + 12) . ' MONTH';

            $dtres = $dclass->query("select CONCAT(" . $from_date . ", ' - ', " . $to_date . ") as dt");

            $cnt = 0;
            $date_range = array();
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }
            if ($app_id == '') {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' | ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY MONTH(dt),s.more_app_id  ORDER BY MONTH(dt) DESC ");
            } else {
                $res = $dclass->select("a.more_app_name as name, CONCAT(" . $from_date . ", ' | ', " . $to_date . ") as dt, SUM(s.$field) as download_count", "tblmore_app_analytics s inner join tblmore_apps a on s.more_app_id=a.intid", " AND (s.dtadd BETWEEN " . $to_date . "  AND " . $from_date . ") $wh GROUP BY MONTH(dt), s.more_app_id ORDER BY MONTH(dt) DESC ");
            }

            if (count($res) > 0) {
                for ($j = 0; $j < count($res); $j++) {
                    $dr = explode("|", $res[$j]['dt']);
                    //print_r($dr);exit;
                    $data[$j]['day'] = date("M Y", strtotime($dr[0])) . " - " . date("M Y", strtotime($dr[1]));
                    $data[$j]['name'] = $res[$j]['name'];
                    $data[$j]['count'] = intval($res[$j]['download_count']);
                }
            }
        }

        return $data;
    }

    //function to get package details
    function package_details($id_array) {
        global $dclass;

        $res = $dclass->select("*", "tblpackages", " AND intid=" . implode(' OR intid=', $id_array));

        return $res;
    }

    //function to get active, upcoming and other plan ids in assending order
    function get_plan_ids($intval, $next_id) {
        global $dclass;
        if ($intval != 'monthly' && $intval != 'yearly') {
            $intval = 'monthly';
        }

        if ($next_id == 2)
            $next_add_id = 3;
        if ($next_id == 3)
            $next_add_id = 2;

        if ($next_id == 4)
            $next_add_id = 5;
        if ($next_id == 5)
            $next_add_id = 4;

        $wh = '';
        if ($next_id != 1)
            $wh .= "AND intid != '" . $next_id . "' AND intid != '" . $next_add_id . "' ";
        else
            $wh .= " AND intid != '" . $next_id . "' ";




        $res = $dclass->select("intid", "tblpackages", " $wh AND pintval = '" . $intval . "' ");

        for ($i = 0; $i < count($res); $i++) {
            $ids[$i] = $res[$i]['intid'];
        }
        $ids[count($res)] = $next_id;

        if ($next_id != 1) {
            $ids[count($res) + 1] = 1;
        }

        sort($ids);
        return $ids;
    }

    //function to update/switch to upcoming plan
    function update_upcoming_plan() {
        global $dclass;
        $sql = "update tblmember set package_id=next_package_id, next_package_id=0 WHERE next_package_id != '0' AND customer_profile_id!=0 AND customer_profile_id!='' AND next_payment_date='" . date("Y-m-d") . "' ";

        $dclass->query($sql);
    }

    //Function to have upgrade vs later data
    function get_upgrade_later($type) {
        global $dclass;

        $member_id = $_SESSION['custid'];
        $data = array();
        $count = 15;
        $acnt = 0;
        for ($i = $count; $i > 0; $i--) {

            $op = "";
            $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';

            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {
                $date_range = $rs['dt'];
                $cnt++;
            }

            $res = $dclass->select("SUM(" . $type . ") as count, dtadd as dt", "tblmember_upgrade_stats", " AND dtadd = " . $from_date . " AND member_id='" . $member_id . "' GROUP BY DAY(dt) ORDER BY DAY(dt) DESC ");


            if (count($res) > 0) {

                for ($j = 0; $j < count($res); $j++) {
                    if ($acnt == 0) {
                        $op = "";
                    } else
                        $op = ",";
                    $data[$acnt] = array(strtotime($res[$j]['dt']) * 1000, $res[$j]['count']);
                }
                $acnt++;
            }
        }

        return json_encode($data);
    }

    //Function to have upgrade vs later data
    function get_helpr_daily_count() {
        global $dclass;

        $member_id = $_SESSION['custid'];
        $data = array();
        $count = 15;
        $acnt = 0;
        for ($i = $count; $i >= 0; $i--) {

            $op = "";
            $from_date = 'DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';

            $dtres = $dclass->query("select " . $from_date . " as dt");

            $cnt = 0;
            while ($rs = $dclass->fetchArray($dtres)) {

                $date_range = $rs['dt'];
                $cnt++;
            }

            $res = $dclass->select("SUM(video) as hlpr_video_count, SUM(image) as hlpr_image_count, SUM(faq) as hlpr_faq_count, dtadd as dt", "tblmember_helpr_stats", " AND dtadd = " . $from_date . " AND member_id='" . $member_id . "' GROUP BY DAY(dt) ORDER BY DAY(dt) DESC ");

            if (count($res) > 0) {

                for ($j = 0; $j < count($res); $j++) {
                    $data[$acnt]['period'] = $date_range;
                    $data[$acnt]['video'] = $this->get_number_format(intval($res[$j]['hlpr_video_count']), 0, '.', ',');
                    $data[$acnt]['image'] = $this->get_number_format(intval($res[$j]['hlpr_image_count']), 0, '.', ',');
                    $data[$acnt]['faq'] = $this->get_number_format(intval($res[$j]['hlpr_faq_count']), 0, '.', ',');
                }
                $acnt++;
            }
        }

        return $data;
    }

    function save_access_log($parent_id, $agent_id, $message, $logouttime) {
        global $dclass;
        if (!empty($logouttime)) {
            $logouttime = $logouttime;
        } else {
            $logouttime = date("Y-m-d H:i:s");
        }
        $ins = array("parent_id" => $parent_id, "agent_id" => $agent_id, "date" => $logouttime, "message" => $message, 'ip_address' => $_SERVER['REMOTE_ADDR']);
        $rel_id = $dclass->insert("tbl_access_log", $ins);
    }

    function save_checkin_checkout($parent_id, $agent_id, $message, $status, $login_session_id) {
        global $dclass;
        if ($status == 'login') {
            $login_time = date("Y-m-d H:i:s");
            $ins = array("parent_id" => $parent_id, "agent_id" => $agent_id, "date" => date("Y-m-d"), "login_time" => $login_time, "message" => $message, 'session_id' => 'Y');
            $rel_id = $dclass->insert("tbl_checkin_checkout", $ins);
            $_SESSION['login_session_id'] = $rel_id;
        } else if ($status == 'logout') {

            $logout_time = date("Y-m-d H:i:s");
            if (!empty($login_session_id)) {
                $sess_id = $login_session_id['intid'];
            } else {
                $sess_id = $_SESSION['login_session_id'];
            }


            $res_time = $dclass->select("*", "tbl_checkin_checkout", " AND intid = '" . $sess_id . "' AND session_id = 'Y' ");

            foreach ($res_time as $res_time_val) {
                if ($res_time_val['date'] == date('Y-m-d')) {
                    $up = array("logout_time" => $logout_time, "message_logout" => $message, 'session_id' => 'N');
                    $dclass->update("tbl_checkin_checkout", $up, " (intid='" . $sess_id . "' AND session_id = 'Y') ");
                    $affected_rows = mysql_affected_rows();
                    if ($affected_rows != 0) {
                        $this->save_access_log($res_time_val['parent_id'], $res_time_val['agent_id'], LOGOUT, $logout_time);
                    }
                } else {

                    $logout_time = date('Y-m-d H:i:s', strtotime($res_time_val['date'] . ' 23:59:59'));
                    $up = array("logout_time" => $logout_time, "message_logout" => $message, 'session_id' => 'N');
                    $dclass->update("tbl_checkin_checkout", $up, " (intid='" . $sess_id . "' AND session_id = 'Y') ");
                    $affected_rows = mysql_affected_rows();
                    if ($affected_rows != 0) {
                        $this->save_access_log($res_time_val['parent_id'], $res_time_val['agent_id'], LOGOUT, $logout_time);
                    }
                }
            }

            return $affected_rows;
        }
    }

    function add_more_app_to_new_app($more_app_id, $member_id, $app_type) {
        global $dclass;

        $chka = $dclass->select("a.intid", "tblmember_apps a inner join tblmember_app_features f on a.intid=f.app_id", " AND a.app_type='" . $app_type . "' AND f.member_id='" . $member_id . "'  AND f.feature_id='2' ");
        //echo $dclass->_sql;exit;
        //$mres = $dclass->select("app_id, more_app_id", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND more_app_id = '" . $more_app_id . "' AND more_app_id IN(select intid from tblmore_apps WHERE parent_app_id IN(select intid from tblmember_apps where app_type='".$app_type."')) GROUP BY more_app_id ");
        //echo $dclass->_sql;exit;
        if (count($chka) > 0) {
            //add cover or first image as default in case of adding more app for new promotr enabled apps
            $mires = $dclass->select("intid", "tblmore_app_images", " AND more_app_id='" . $more_app_id . "' order by status LIMIT 1 ");

            if (count($mires) > 0) {
                $more_app_img_id = $mires[0]['intid'];
            }

            for ($i = 0; $i < count($chka); $i++) {
                $app_id_for = $chka[$i]['intid'];
                //check to avoid same parent app and more app combo
                $chkr = $dclass->select("intid", "tblmore_apps", " AND member_id='" . $member_id . "' AND parent_app_id = '" . $app_id_for . "' AND intid='" . $more_app_id . "'  ");
                if (count($chkr) <= 0) {
                    //check to avoid duplicate more app records
                    $chkm = $dclass->select("intid", "tblapp_moreapp_rel", " AND member_id='" . $member_id . "' AND app_id = '" . $app_id_for . "' AND more_app_id='" . $more_app_id . "'  ");

                    if (count($chkm) <= 0) {
                        unset($insaa);
                        $insaa['member_id'] = $member_id;
                        $insaa['app_id'] = $app_id_for;
                        $insaa['more_app_id'] = $more_app_id;
                        $insaa['more_app_img_id'] = $more_app_img_id;
                        $rel_id = $dclass->insert("tblapp_moreapp_rel", $insaa);
                        $updor['intorder'] = $rel_id;
                        $dclass->update("tblapp_moreapp_rel", $updor, " intid = '" . $rel_id . "' ");
                    }
                    //echo $dclass->_sql;exit;
                }
            }
        }//check for promotr enabled over
    }

}
?>