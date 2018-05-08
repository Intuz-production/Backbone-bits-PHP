<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

@session_start();
@ob_start("ob_gzhandler");
@ob_gzhandler();
error_reporting(E_ALL);

$glob['dbhost'] = 'xxxxxxx';  // Databse Host Name
$glob['dbusername'] = 'xxxxxxx';  // Database Username
$glob['dbpassword'] = 'xxxxxxx';  // Database Password
$glob['dbdatabase'] = 'xxxxxxx'; // Database Name

define("BRAND", "Backbone bits");
define("TITLE", "Control Panel ::" . BRAND);
define("CUST_TITLE", "Admin ::" . BRAND);
define("SAVE", "Save");
define("SAVE_PUBLISH", "Save & Publish");

require_once('message.php');

define("INC", "inc/");
define('CUR', "$");
define("SITE_URL", "http://".$_SERVER['SERVER_NAME']."/");

$temp_url = parse_url($_SERVER['PHP_SELF']);
$dir_url = dirname($temp_url['path']);

if (strstr($dir_url, "server")) {
    require_once('../../classes/database.class.php');
    $dclass = new database();
    require_once('../../classes/general.class.php');
    $gnrl = new general();
    require_once('../../classes/front-paging.class.php');
    require_once('../../classes/MCrypt.php');
    $mcrypt = new MCrypt();
    define("USER_LOGO", "../files/user");
    define("MORE_APPS_IMG", "../files/more-apps");
    define("TUT_VIDEO", "../files/tutorial-videos");
    define("APP_LOGO", "../files/apps");
    define("FEATURE_LOGO", "../files/features");
    define("ANIMATION_PREVIEW", "../files/animations");
    define("TUT_IMG", "../files/tutorial-images");
} else if (strstr($dir_url, "services") || strstr($dir_url, "cron") || strstr($dir_url, "tcpdf")) {
    require_once('../classes/database.class.php');
    $dclass = new database();
    require_once('../classes/general.class.php');
    $gnrl = new general();
    require_once('../classes/front-paging.class.php');
    require_once('../classes/MCrypt.php');
    $mcrypt = new MCrypt();
    define("MORE_APPS_IMG", "../files/more-apps");
    define("TUT_VIDEO", "../files/tutorial-videos");
    define("APP_LOGO", "../files/apps");
    define("FEATURE_LOGO", "../files/features");
    define("ANIMATION_PREVIEW", "../files/animations");
    define("SUPPORT_IMG", "../files/support/attachment");
    define("COMPANY_LOGO", "../files/company");
    define("USER_LOGO", "../files/user");
    define("ANDROID_TEMP", "../android-api/temp");
    define("ANDROID_LOGO_TEMP", "../android-api/temp_logo");
    define("TUT_IMG", "../files/tutorial-images");
} else {
    require_once('./classes/database.class.php');
    $dclass = new database();
    require_once('./classes/general.class.php');
    $gnrl = new general();
    require_once('./classes/front-paging.class.php');
    $gnrl = new general();
    require_once('./classes/MCrypt.php');
    $mcrypt = new MCrypt();
    define("SUPPORT_IMG", "files/support/attachment");
    define("MORE_APPS_IMG", "files/more-apps");
    define("TUT_VIDEO", "files/tutorial-videos");
    define("APP_LOGO", "files/apps");
    define("COMPANY_LOGO", "files/company");
    define("ANIMATION_PREVIEW", "files/animations");
    define("FEATURE_LOGO", "files/features");
    define("USER_LOGO", "files/user");
    define("ANDROID_TEMP", "android-api/temp");
    define("ANDROID_LOGO_TEMP", "android-api/temp_logo");
    define("TUT_IMG", "files/tutorial-images");
}

// try to prevent sql injection
foreach ($_POST as $k => $v) {
    if (!is_array($v)) {
        
    } else {
        $_POST[$k] = $v;
    }
}
foreach ($_GET as $k => $v) {
    $_GET[$k] = mysql_real_escape_string($v);
}
foreach ($_REQUEST as $k => $v) {
    
}

function _p($str) {
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}

$basefilename = basename($_SERVER['PHP_SELF']);
define('REQUIRED', ' <span class="red">*</span>');
define('CURRENCY', '$');

//Google credentials for android api
define('GOOGLE_EMAIL', 'xxxxxxx@xxxx.xxx'); //Gmail Username
define('GOOGLE_PASSWD', 'xxxxxxxx');        // Gmail Password
define('ANDROID_DEVICEID', 'xxxxxxxxxxxxxxxx');

//---------Access Log---------------------//

define('LOGIN', 'Logged In');
define('LOGOUT', 'Logged Out');
define('APP_ADDED', 'Added an app');
define('APP_UPDATED', 'Updated an app');

define('HELPR_ADDED', 'Help version added');
define('HELPR_UPDATED', 'Help version updated');
define('HELPR_FAQ_ADDED', 'Help faq added');
define('HELPR_FAQ_UPDATED', 'Help faq updated');
define('HELPR_FAQ_DELETED', 'Help faq deleted');
define('HELPR_IMAGE_DELETED', 'Help image deleted');
define('HELPR_ARCHIVE_DELETED', 'Help archive deleted');
define('HELPR_VIDEO_UPDATED', 'Help video updated');
define('HELPR_VIDEO_ADDED', 'Help video added');
define('AGENT_ALLOCATED', 'Respond responsible added');
define('COMMUNICATR_ADDED', 'Replied by user');
define('COMMUNICATR_FAQ_ADDED', 'Faq added by user from respond');
define('ASK_FOR_REVIEW', 'Review sent');


if ($_SESSION['parent_id'] != 0)
    $_SESSION['custid'] = $_SESSION['parent_id'];
?>
