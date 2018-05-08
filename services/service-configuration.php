<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

@session_start();
error_reporting(1);
define("SITE_URL", "http://".$_SERVER['SERVER_NAME']."/");
define('INC', 'inc/');

$glob['dbhost'] = 'xxxxxxx';  // Databse Host Name
$glob['dbusername'] = 'xxxxxxx';  // Database Username
$glob['dbpassword'] = 'xxxxxxx';  // Database Password
$glob['dbdatabase'] = 'xxxxxxx'; // Database Name

define('KEY', '123456789');

$lang_id = 1;

if (strstr($_SERVER['PHP_SELF'], "fb") || strstr($_SERVER['PHP_SELF'], "admin") || strstr($_SERVER['PHP_SELF'], "services") || strstr($_SERVER['PHP_SELF'], "cron")) {
    require_once('../classes/database.class.php');
    $dclass = new database();
    require_once('../classes/general.class.php');
    $gnrl = new general();

    define("MORE_APPS_IMG", "files/more-apps");
    define("TUT_VIDEO", "files/tutorial-videos");
    define("APP_LOGO", "files/apps");
    define("COMPANY_LOGO", "files/company");
    define("ANIMATION_PREVIEW", "files/animations");
    define("TUT_IMG", "files/tutorial-images");
    define("SUPPORT_IMG", str_replace('/services', '', getcwd()) . '/files/support/attachment');
} else {
    require_once('./classes/database.class.php');
    $dclass = new database();
    require_once('./classes/general.class.php');
    $gnrl = new general();
    define("MORE_APPS_IMG", "files/more-apps");
    define("TUT_VIDEO", "files/tutorial-videos");
    define("APP_LOGO", "files/apps");
    define("COMPANY_LOGO", "files/company");
    define("ANIMATION_PREVIEW", "files/animations");
    define("TUT_IMG", "files/tutorial-images");
    define("SUPPORT_IMG", str_replace('/services', '', getcwd()) . '/files/support/attachment');
}

$basename = basename($_SERVER['PHP_SELF']);
define('REQUIRED', ' <span class="red">*</span>');
?>