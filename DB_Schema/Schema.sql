-- MySQL dump 10.13  Distrib 5.6.33, for debian-linux-gnu (x86_64)
--
-- Host: DBHOST    Database: DBNAME
-- ------------------------------------------------------
-- Server version	5.6.38

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_access_log`
--

DROP TABLE IF EXISTS `tbl_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_access_log` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_ask_for_review`
--

DROP TABLE IF EXISTS `tbl_ask_for_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ask_for_review` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `support_id` int(11) NOT NULL,
  `dtadd` datetime NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_checkin_checkout`
--

DROP TABLE IF EXISTS `tbl_checkin_checkout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_checkin_checkout` (
  `intid` double NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime NOT NULL,
  `message` text NOT NULL,
  `message_logout` varchar(100) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_support_agent_allocate`
--

DROP TABLE IF EXISTS `tbl_support_agent_allocate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_support_agent_allocate` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `os_type` varchar(10) NOT NULL,
  `pem_file` varchar(255) NOT NULL,
  `pem_prod_file` varchar(255) NOT NULL,
  `passphrase` varchar(255) NOT NULL,
  `passphrase_prod` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `project_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbladmin`
--

DROP TABLE IF EXISTS `tbladmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbladmin` (
  `intid` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `varfname` varchar(255) NOT NULL DEFAULT '',
  `varlname` varchar(255) NOT NULL DEFAULT '',
  `vaemail` varchar(255) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `dtreg` date NOT NULL DEFAULT '0000-00-00',
  `admintype` enum('main','org') NOT NULL,
  `phone` varchar(30) NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblalertlog`
--

DROP TABLE IF EXISTS `tblalertlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblalertlog` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `alert_id` tinytext NOT NULL COMMENT '<id> of XML',
  `dtadd` datetime NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_analytics`
--

DROP TABLE IF EXISTS `tblapp_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_analytics` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `os_type` enum('ios','android','windows') NOT NULL,
  `app_display_count` bigint(11) NOT NULL DEFAULT '0',
  `app_like_count` bigint(11) NOT NULL DEFAULT '0',
  `app_dislike_count` bigint(11) NOT NULL DEFAULT '0',
  `app_update_count` bigint(11) NOT NULL DEFAULT '0',
  `app_no_update_count` bigint(11) NOT NULL DEFAULT '0',
  `app_support_count` bigint(11) NOT NULL DEFAULT '0',
  `app_resolved_count` bigint(11) NOT NULL DEFAULT '0',
  `rating_prompt_count` bigint(11) NOT NULL DEFAULT '0',
  `upgrade_prompt_count` bigint(11) NOT NULL DEFAULT '0',
  `video_prompt_count` bigint(11) NOT NULL DEFAULT '0',
  `promote_prompt_count` bigint(11) NOT NULL DEFAULT '0',
  `rate_count` bigint(11) NOT NULL DEFAULT '0',
  `remind_count` bigint(11) NOT NULL DEFAULT '0',
  `no_thanks_count` bigint(11) NOT NULL DEFAULT '0',
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_faq`
--

DROP TABLE IF EXISTS `tblapp_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_faq` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `ver_id` int(11) NOT NULL,
  `question` tinytext NOT NULL,
  `answer` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `intorder` int(11) NOT NULL DEFAULT '0',
  `dtadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_canned` varchar(1) NOT NULL COMMENT 'Y-canned, N-faq',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_moreapp_rel`
--

DROP TABLE IF EXISTS `tblapp_moreapp_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_moreapp_rel` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `more_app_id` int(11) NOT NULL,
  `more_app_img_id` int(11) NOT NULL DEFAULT '0',
  `more_app_custom_image` tinytext,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `intorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_os_ver`
--

DROP TABLE IF EXISTS `tblapp_os_ver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_os_ver` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `device_id` tinytext NOT NULL,
  `app_country_id` int(11) NOT NULL DEFAULT '223',
  `os_type` enum('ios','android','windows') NOT NULL,
  `os_version` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `model_number` varchar(255) NOT NULL,
  `os_download_count` int(11) NOT NULL,
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_ratings`
--

DROP TABLE IF EXISTS `tblapp_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_ratings` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL COMMENT 'member_apps intid',
  `content_yn` text NOT NULL COMMENT 'Content for yes or no',
  `content_rate_short` text COMMENT 'Content for encouraging rating',
  `content_rate_long` text,
  `content_feedback` text COMMENT 'Content for feedback for no',
  `like_yes` varchar(100) NOT NULL,
  `like_no` varchar(255) NOT NULL,
  `rate_this_app` varchar(255) NOT NULL,
  `remind_later` varchar(255) NOT NULL,
  `remind_count` int(11) NOT NULL DEFAULT '3',
  `no_thanks` varchar(255) NOT NULL,
  `like_yes_bck` varchar(100) NOT NULL,
  `like_no_bck` varchar(100) NOT NULL,
  `like_yes_but` varchar(100) NOT NULL,
  `like_no_but` varchar(100) NOT NULL,
  `rate_this_app_bck` varchar(100) NOT NULL,
  `remind_later_bck` varchar(100) NOT NULL,
  `no_thanks_bck` varchar(100) NOT NULL,
  `rate_this_app_but` varchar(100) NOT NULL,
  `remind_later_but` varchar(100) NOT NULL,
  `no_thanks_but` varchar(100) NOT NULL,
  `que_select_font` varchar(100) NOT NULL,
  `rate_select_font` varchar(100) NOT NULL,
  `like_no_action` enum('support','email') NOT NULL,
  `like_no_email` varchar(255) DEFAULT NULL,
  `animation` int(11) NOT NULL,
  `dtadd` datetime NOT NULL,
  `dtmod` datetime NOT NULL,
  `status` enum('save','publish') NOT NULL,
  `app_status` enum('pause','running') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_support`
--

DROP TABLE IF EXISTS `tblapp_support`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_support` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL DEFAULT '0',
  `type` enum('user','admin','agent') NOT NULL,
  `request_type` enum('feedback','bug','change','query') NOT NULL DEFAULT 'feedback',
  `priority` enum('high','medium','low') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `region` varchar(255) NOT NULL DEFAULT 'LA, USA',
  `os` varchar(100) NOT NULL DEFAULT 'ios',
  `version` varchar(100) NOT NULL DEFAULT '1.1',
  `device` varchar(255) NOT NULL,
  `device_id` varchar(500) NOT NULL,
  `device_token` varchar(500) NOT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('due','replied','close','review','reopen') NOT NULL,
  `is_read` varchar(1) NOT NULL COMMENT 'N-unread,Y-read',
  `is_read_mobile` varchar(1) NOT NULL DEFAULT 'N',
  `is_notification` varchar(1) NOT NULL DEFAULT 'N',
  `is_archive` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1= archieve',
  `is_live` tinyint(1) NOT NULL DEFAULT '1',
  `app_version` varchar(255) NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_support_attachment`
--

DROP TABLE IF EXISTS `tblapp_support_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_support_attachment` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `support_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_support_config`
--

DROP TABLE IF EXISTS `tblapp_support_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_support_config` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `name_title` varchar(255) NOT NULL DEFAULT 'Name',
  `email_title` varchar(255) NOT NULL DEFAULT 'Email',
  `subject_title` varchar(255) NOT NULL,
  `subject_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `message_title` varchar(255) NOT NULL DEFAULT 'Message',
  `phone_title` varchar(255) NOT NULL,
  `phone_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `dtadd` datetime NOT NULL,
  `feature_status` enum('pause','running') DEFAULT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_tutorial_images`
--

DROP TABLE IF EXISTS `tblapp_tutorial_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_tutorial_images` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `ver_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `intorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_tutorial_settings`
--

DROP TABLE IF EXISTS `tblapp_tutorial_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_tutorial_settings` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `version` varchar(100) NOT NULL,
  `display_first` enum('video','image','faq') NOT NULL DEFAULT 'video',
  `status` enum('save','publish') NOT NULL DEFAULT 'publish',
  `video_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `image_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `image_animation` varchar(255) NOT NULL DEFAULT 'Swipe left to right',
  `faq_status` enum('active','inactive') DEFAULT 'active',
  `faq_font_color` varchar(50) NOT NULL,
  `record_status` enum('running','prev','old') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_tutorial_videos`
--

DROP TABLE IF EXISTS `tblapp_tutorial_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_tutorial_videos` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `ver_id` int(11) NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `video` tinytext NOT NULL,
  `video_type` enum('file','youtube','vimeo') DEFAULT NULL,
  `live_date` date DEFAULT NULL,
  `pause_date` date DEFAULT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('save','publish') DEFAULT 'publish',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblapp_whatsnew`
--

DROP TABLE IF EXISTS `tblapp_whatsnew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblapp_whatsnew` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `view_type` varchar(10) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `short_content` varchar(255) NOT NULL,
  `show_app_logo` enum('yes','no') NOT NULL DEFAULT 'yes',
  `content_upgrade_but` varchar(100) NOT NULL,
  `content_upgrade_but_alert` varchar(100) NOT NULL,
  `content_no_thanks_but` varchar(100) NOT NULL,
  `content_no_thanks_but_alert` varchar(100) NOT NULL,
  `upgrade_but_bck` varchar(100) NOT NULL,
  `upgrade_but_font` varchar(100) NOT NULL,
  `no_thanks_but_bck` varchar(100) NOT NULL,
  `no_thanks_but_font` varchar(100) NOT NULL,
  `font_family` varchar(100) NOT NULL,
  `bck_color` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `upgrade_date` date DEFAULT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('save','publish') NOT NULL,
  `record_status` enum('old','prev','running') NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblcountries`
--

DROP TABLE IF EXISTS `tblcountries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblcountries` (
  `countries_id` int(11) NOT NULL AUTO_INCREMENT,
  `countries_name` varchar(64) NOT NULL DEFAULT '',
  `countries_iso_code_2` char(2) NOT NULL DEFAULT '',
  `countries_iso_code_3` char(3) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`countries_id`),
  KEY `idx_countries_name_zen` (`countries_name`),
  KEY `idx_address_format_id_zen` (`status`),
  KEY `idx_iso_2_zen` (`countries_iso_code_2`),
  KEY `idx_iso_3_zen` (`countries_iso_code_3`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblfeatures`
--

DROP TABLE IF EXISTS `tblfeatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblfeatures` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `fename` varchar(255) NOT NULL,
  `felogo` varchar(255) NOT NULL,
  `fedesc` text,
  `fedoc` longtext,
  `status` enum('active','inactive') NOT NULL,
  `dtadd` datetime NOT NULL,
  `dtmod` datetime NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblfeatures` (`intid`, `fename`, `felogo`, `fedesc`, `fedoc`, `status`, `dtadd`, `dtmod`) VALUES
(3, 'Respond', '', NULL, NULL, 'active', NOW(), '0000-00-00 00:00:00'),
(4, 'Help', '', NULL, NULL, 'active', NOW(), '0000-00-00 00:00:00');

--
-- Table structure for table `tblfonts`
--

DROP TABLE IF EXISTS `tblfonts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblfonts` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) NOT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmaster_more_apps`
--

DROP TABLE IF EXISTS `tblmaster_more_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmaster_more_apps` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `os_type` enum('ios','android','windows') NOT NULL,
  `more_app_name` varchar(255) NOT NULL,
  `more_app_lnk` tinytext NOT NULL,
  `dtadd` datetime NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember`
--

DROP TABLE IF EXISTS `tblmember`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `package_id` int(11) NOT NULL,
  `package_type` enum('subscription','topup') NOT NULL,
  `customer_profile_id` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Customer profile id from braintree',
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `company_logo` varchar(100) DEFAULT NULL,
  `dtadd` datetime NOT NULL,
  `dtmod` datetime NOT NULL,
  `status` enum('pending','active','inactive','banned','waiting') NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `role` enum('admin','technical','finance','support','marketing') NOT NULL DEFAULT 'admin',
  `payment_status` enum('trial','paid','unpaid','trial-unpaid') NOT NULL,
  `next_package_id` int(11) NOT NULL DEFAULT '0',
  `next_payment_date` date NOT NULL,
  `cc_status` int(11) NOT NULL DEFAULT '0' COMMENT 'Valid credit card added to braintree or not 0/1',
  `total_actions` bigint(11) NOT NULL DEFAULT '0' COMMENT 'used actions',
  `total_payment` float(18,2) NOT NULL,
  `free_action_limit_once` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if free action limit reached 0/1',
  `intro` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 for no intro 1 for close intro',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblmember` (`intid`, `lang_id`, `parent_id`, `package_id`, `package_type`, `customer_profile_id`, `fname`, `lname`, `username`, `password`, `email`, `phone`, `logo`, `company`, `company_logo`, `dtadd`, `dtmod`, `status`, `timezone`, `role`, `payment_status`, `next_package_id`, `next_payment_date`, `cc_status`, `total_actions`, `total_payment`, `free_action_limit_once`, `intro`) VALUES (NULL, '1', '0', '2', 'subscription', '0', 'Admin', NULL, 'admin', '0192023a7bbd73250516f069df18b500', 'admin@gmail.com', '1234567890', NULL, NULL, NULL, NOW(), '', 'active', '', 'admin', 'trial', '0', '', '0', '0', '', '0', '1');

--
-- Table structure for table `tblmember_app_features`
--

DROP TABLE IF EXISTS `tblmember_app_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_app_features` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `pf_id` int(11) NOT NULL COMMENT 'package feature relation',
  `payment_type` enum('monthly','yearly') NOT NULL,
  `payment_cost` float(18,2) NOT NULL,
  `feature_status` enum('pause','running') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember_apps`
--

DROP TABLE IF EXISTS `tblmember_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_apps` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `or_app_name` varchar(255) NOT NULL,
  `app_name` tinytext NOT NULL,
  `app_type` enum('ios','android','windows') NOT NULL,
  `app_logo` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) NOT NULL,
  `app_key` tinytext NOT NULL,
  `app_store_id` varchar(100) NOT NULL,
  `app_url` tinytext NOT NULL,
  `track_id` varchar(100) DEFAULT '0',
  `app_add_date` datetime NOT NULL,
  `app_mod_date` datetime NOT NULL,
  `app_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `server_status` enum('dev','prod') NOT NULL,
  `total_payment` float(18,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('trial','paid','unpaid','part-paid','deprecated') NOT NULL DEFAULT 'deprecated',
  `current_billing_end_date` date DEFAULT NULL,
  `current_payment` float(18,2) DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `support_notify` enum('on','off') NOT NULL DEFAULT 'on',
  `review_message` varchar(10000) DEFAULT '\r\nDear %ticket_creator_name%,\r\n\r\nThank you for choosing our app %app_name%. I hope we achieved our goal in making your experience memorable by providing outstanding service.\r\n\r\nWe strive to craft the app best in its class. Therefore, your comments are very important to us. Please take a moment of your time to rate the app on the store %appstore_url%, so we maintain our exceptional services.\r\n\r\nSincerely,\r\n%app_company_name%',
  `notification` varchar(10) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember_features`
--

DROP TABLE IF EXISTS `tblmember_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_features` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `feature_status` enum('pause','running') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblmember_features` (`intid`, `member_id`, `feature_id`, `feature_status`) VALUES (1, '1', '3', 'running');
INSERT INTO `tblmember_features` (`intid`, `member_id`, `feature_id`, `feature_status`) VALUES (2, '1', '4', 'running');

--
-- Table structure for table `tblmember_helpr_stats`
--

DROP TABLE IF EXISTS `tblmember_helpr_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_helpr_stats` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `video` int(11) NOT NULL,
  `image` int(11) NOT NULL,
  `faq` int(11) NOT NULL,
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember_settings`
--

DROP TABLE IF EXISTS `tblmember_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_settings` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `info` text,
  `serialize` enum('0','1') NOT NULL,
  `option_group` varchar(50) NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember_stats`
--

DROP TABLE IF EXISTS `tblmember_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_stats` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `more_app_id` int(11) NOT NULL DEFAULT '0' COMMENT 'app more app rel intid',
  `actions` int(11) NOT NULL,
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmember_upgrade_stats`
--

DROP TABLE IF EXISTS `tblmember_upgrade_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmember_upgrade_stats` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `upgrade` int(11) NOT NULL,
  `later` int(11) NOT NULL,
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmessage_lang`
--

DROP TABLE IF EXISTS `tblmessage_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmessage_lang` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `msg_name` varchar(255) NOT NULL,
  `msg_value` text NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (1,1,'RATING_CONFIG_SUC','Ratings configuration for your app is build successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (2,1,'NO_RATING_CONFIG','Ratings configuration for your app is not found. Please configure from Backbone admin');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (3,1,'FEATURE_NOT_AVAILABLE','This feature is not available to use. Please purchase or contact App-Manager administrator to enable.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (4,1,'MORE_APPS_CONFIG_SUC','Promotr configuration for your app is build successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (5,1,'NO_MORE_APPS_CONFIG','Promotr configuration or record for your app is not found. Please configure from Backbone admin');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (6,1,'TVIDEO_CONFIG_SUC','Help configuration for your app is build successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (7,1,'NO_TVIDEO_CONFIG','Help configuration for your app is not found. Please configure from  admin');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (8,1,'WHATS_NEW_CONFIG_SUC','Whats New Content for your app is build successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (9,1,'NO_WHATS_NEW_CONFIG','Whats New Content for your app is not found. Please configure from Backbone admin');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (10,1,'SUPPORT_GET_SUC','Support replies from administrator');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (11,1,'NO_SUPPORT_GET','No Support reply from administrator');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (12,1,'SUPPORT_CONFIG_SUC','Support configuration for your app build successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (13,1,'NO_SUPPORT_CONFIG','Support configuration for your app is not found. Please configure from Backbone admin');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (14,1,'SUPPORT_SAVE_SUC','Support request saved successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (15,1,'SUPPORT_SAVE_FAIL','Support request failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (16,1,'STATS_SAVE_SUC','Analyzr saved successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (17,1,'STATS_SAVE_FAIL','Analyzr failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (18,1,'NO_STATS_CONFIG','Analyzr configuration for your app is not found. Please configure from Backbone admin.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (19,1,'USER_EXISTS','Username already exists. Try another.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (20,1,'REG_SUC','Registration successful. Welcome to the Backbon');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (21,1,'REG_FAIL','Registration failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (22,1,'FEATURE_SAVED_OR_PAUSED','Feature not published yet. Please check your Backbone configuration.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (23,1,'RATING_PUBLISH_UPDATE','Updated ratings for your app published');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (24,1,'RATING_SAVE_UPDATE','Updated ratings for your app saved');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (25,1,'RATING_PAUSE_UPDATE','Updated ratings for your app paused');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (26,1,'UPGRADE_PUBLISH_UPDATE','Upgradr live version updated');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (27,1,'UPGRADE_SAVE_UPDATE','Upgrade prompt saved ');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (28,1,'NO_ACTIVE_RECORD','Your feature is published but no active record found');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (29,1,'PROMOTE_PUBLISH_UPDATE','Your changes have been saved and published');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (30,1,'PROMOTE_SAVE_UPDATE','Your changes have been saved');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (31,1,'PROMOTE_PAUSE_UPDATE','Promotion apps paused. You need to publish it again to make it live.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (32,1,'PROMOTE_PUBLISH_ADD','New promotion app added');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (33,1,'PROMOTE_SAVE_ADD','Your changes have been saved');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (34,1,'PROMOTE_PAUSE_ADD','Promotion app paused. You need to publish it again to make it live.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (35,1,'FEATURE_STATUS_UPD','Feature status has been updated');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (36,1,'EMAIL_EXISTS','Email already registered. Try another.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (37,1,'LOGIN_INVALID','Invalid username or password.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (38,1,'INVALID_ACCESS','You have no right to access the feature');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (39,1,'PASS_SENT','Password sent to your email address.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (40,1,'EMAIL_NOT_EXIST','This email has not been registered yet.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (41,1,'EMAIL_SIGNUP_NOT_REGISTERED','Email signed up but not registered yet. Check your email for verification link to register.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (42,1,'REG_VER_SUC','Verification email sent with a link to complete registration process.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (43,1,'REG_VER_FAIL','Verification email sending failed. Try again.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (44,1,'APP_EXIST','This app already added in your list.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (45,1,'APP_INACTIVE','All current app services are inactive at the moment. please contact your app administrator.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (46,1,'DEVICE_EXIST','Device already exists');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (47,1,'MORE_APP_EXIST','More app already exists. Enter new app name and/or link.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (48,1,'FILE_REQUIRED_TRANSFER_FAILED','File required or transfer failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (49,1,'PROMOTE_PUBLISH_ADD_FAIL','Promotion app saving failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (50,1,'DELETE_MORE_APPS','Promotional app deleted successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (51,1,'DELETE_MORE_APPS_FAIL','Promotional app deletion failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (52,1,'APP_ADD','New App added successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (53,1,'APP_UPD','App updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (54,1,'APP_SAVE_FAIL','App saving failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (55,1,'FILE_UPLOAD_FAIL','File upload failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (56,1,'REQUIRED','Please complete all required fields or resolve errors');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (57,1,'CURRENT_PASSWORD_REQ','Current password is required');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (58,1,'SETTING_SUC','Settings updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (59,1,'ALL_FEATURES_DISABLED_APP_ADD_FAILED','All app features disabled. No App added');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (60,1,'CONTACT_FEEDBACK_SENT','Your feedback has been sent');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (61,1,'APP_ID_REQUIRED','App ID is required to fetch latest info for IOS platform');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (62,1,'ALL_FEATURES_DISABLED_APP_UPD_FAILED','All app features disabled. App update failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (63,1,'NEW_UPGRADE_PUBLISH_UPDATE','New Upgradr live version added');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (64,1,'DELETE_OLD_UPGRADE_PROMPT','Old upgradr deleted successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (65,1,'DELETE_TUT_IMG','Help image deleted successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (66,1,'HELPR_VALIDATE_FAIL','Fill up required fields in at least one of the help sections');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (67,1,'HELPR_WRONG_VIDEO_TYPE','Select .mp4 video only');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (68,1,'HELPR_MAKE_VIDEO_LIVE','Selected help video is live now');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (69,1,'HELPR_MAKE_VIDEO_LIVE_FAIL','Selected help video making live failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (70,1,'HELPR_UPDATE','Help sections updated successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (71,1,'NO_TVIDEO_DATA','No Help data');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (72,1,'NO_LIVE_UPGRADR','No live upgradr record exist');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (73,1,'HELPR_WRONG_IMG_TYPE','Select images only');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (74,1,'FAQ_REQUIRED','Fill required FAQ fields');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (75,1,'FAQ_ADD','FAQ added successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (76,1,'FAQ_UPDATE','FAQ updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (77,1,'SEL_PROMOTE_UPDATE','Selected Promotr app updated');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (78,1,'AGENT_UPD','Agent updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (79,1,'AGENT_ADD','Agent added successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (80,1,'AGENT_FAIL','Agent addition failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (81,1,'DELETE_USER_SUC','Deleted agent successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (82,1,'DELETE_USER_FAIL','Deleting agent failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (83,1,'AGENT_UPD','Agent updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (84,1,'AGENT_ADD','Agent added successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (85,1,'AGENT_FAIL','Agent addition failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (86,1,'PAYMENT_UPD_SUC','Payment details updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (87,1,'ACTION_LIMIT_REACHED','your action limits have reached.Please pay additional top up for your selected plan');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (88,1,'TRIAL_EXP','Trial Period Expired. Please pay as per your subscription package.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (89,1,'REGULAR_PAYMENT_DUE','Regular payment due. Please pay to proceed further.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (90,1,'ADDITIONAL_PAYMENT_DUE','Your action limits for the plan has reached payment due. Please pay for additional plan to proceed further.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (91,1,'UPGRADE_PLAN_SUCCESS','Your upcoming plan saved successfully from next billing cycle.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (92,1,'UPGRADE_PLAN_FAIL','Payment failed. Your plan upgrade failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (93,1,'NO_MSTATS_CONFIG','Member stats not found for your Backbone configuration.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (94,1,'PROMOTER_SETTINGS_UPD','Promotr settings updated');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (95,1,'UPGRADE_PLAN_SUCCESS_BUZZ','Your plan upgraded successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (96,1,'ACTION_STATS_SAVE_SUC','Action count received successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (97,1,'ACTION_STATS_SAVE_FAIL','Action count save failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (98,1,'PROFILE_SUC','Profile updated successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (99,1,'SEC_DISABLE_GEN_SETTINGS','Enable this section from general settings');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (100,1,'LOGIN_NOT_ALLOWED','Login not allowed at this time. Contact your administrator');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (101,1,'CANNED_SUCC',' Message saved as canned response successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (102,1,'CANNED_ERR','Message failed to send and save as canned response');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (103,1,'UPGRADE_SETTINGS_UPDATE','Upgradr live version settings updated');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (104,1,'UPGRADR_STATS_SAVE_SUC','Upgradr analytics saved successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (105,1,'UPGRADR_STATS_SAVE_FAIL','Upgradr analytics saving failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (106,1,'SUPPORT_SUCC','Message and mail sent successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (107,1,'SUPPORT_ERR','Error in sending message');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (108,1,'MESS_FAQ_SUCC','Message saved as FAQ successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (109,1,'MESS_FAQ_ERR','Error in saving as FAQ');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (110,1,'ASK_REVIEW_SUCC','Review request and mail sent successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (111,1,'ASK_REVIEW_SUCC','Error in sending review');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (112,1,'REVIEW_SENT_ERR','Review already sent to the user');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (113,1,'HELPR_VER_INSERT','Help New version added');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (114,1,'HELPR_VER_INSERT_FAIL','Help New version addition failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (115,1,'DELETE_FAQ','FAQ deleted successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (116,1,'DELETE_FAQ_FAIL','FAQ deletion failed.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (117,1,'HELPR_STATS_SAVE_SUC','Help stats saved successfully');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (118,1,'HELPR_STATS_SAVE_FAIL','Help stats saving failed');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (119,1,'SUCC_ACCESS_LOG_DELETE','Access log deleted successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (120,1,'FAIL_ACCESS_LOG_DELETE','Fail to delete access log.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (121,1,'ALREADY_ACTIVATED','Your account is already activated.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (122,1,'CURR_PASS_NOT_MATCH','Current password does not match.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (123,1,'SUCC_ACTIVE_USER','Active user inserted successfully.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (124,1,'ALREADY_ADDED_ACTIVE_USER','Active user already inserted.');
INSERT INTO `tblmessage_lang` (`intid`,`lang_id`,`msg_name`,`msg_value`) VALUES (125,1,'KEY_NOT_AVAILABLE','App key is not valid');

--
-- Table structure for table `tblmore_app_analytics`
--

DROP TABLE IF EXISTS `tblmore_app_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmore_app_analytics` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `more_app_id` int(11) NOT NULL,
  `os_type` enum('ios','android','windows') NOT NULL,
  `app_visit_count` bigint(11) NOT NULL,
  `app_display_count` bigint(11) NOT NULL,
  `dtadd` date NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmore_app_images`
--

DROP TABLE IF EXISTS `tblmore_app_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmore_app_images` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `more_app_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `source` enum('store','custom') NOT NULL,
  `status` enum('cover','other') NOT NULL,
  `cover_app_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmore_app_settings`
--

DROP TABLE IF EXISTS `tblmore_app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmore_app_settings` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `logo_flag` enum('yes','no') NOT NULL,
  `title_text` varchar(100) NOT NULL,
  `font_color` varchar(100) NOT NULL,
  `bck_color` varchar(100) NOT NULL,
  `font_family` varchar(100) NOT NULL,
  `animation_id` int(11) NOT NULL,
  `status` enum('save','publish') NOT NULL,
  `app_status` enum('pause','running') NOT NULL DEFAULT 'running',
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblmore_apps`
--

DROP TABLE IF EXISTS `tblmore_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblmore_apps` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `parent_app_id` int(11) NOT NULL,
  `more_app_name` tinytext NOT NULL,
  `more_app_lnk` tinytext NOT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblpackage_features`
--

DROP TABLE IF EXISTS `tblpackage_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblpackage_features` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('active','inactive','deleted') NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblpackages`
--

DROP TABLE IF EXISTS `tblpackages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblpackages` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(255) NOT NULL,
  `ptype` enum('fixed','recurring') NOT NULL,
  `pcost` float(18,2) NOT NULL,
  `plimit` bigint(11) NOT NULL,
  `padditional_limit` bigint(11) NOT NULL,
  `padditional_limit_cost` float(18,2) NOT NULL,
  `pintval` enum('monthly','yearly') DEFAULT NULL COMMENT 'x or 0(infinte) months',
  `pdate` datetime NOT NULL,
  `status` enum('active','inactive','deleted') NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblpackages` (`intid`, `pname`, `ptype`, `pcost`, `plimit`, `padditional_limit`, `padditional_limit_cost`, `pintval`, `pdate`, `status`) VALUES (NULL, 'Buzz', 'fixed', '0.00', '1000', '1000', '10.00', NULL, '2014-09-19 00:00:00', 'active'),
  (NULL, 'Shout', 'recurring', '0.00', '1200000', '1200000', '10.00', NULL, '2014-09-19 00:00:00', 'active');

--
-- Table structure for table `tblplan_log`
--

DROP TABLE IF EXISTS `tblplan_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblplan_log` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `dtadd` datetime NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblrole`
--

DROP TABLE IF EXISTS `tblrole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblrole` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `feature` varchar(255) NOT NULL,
  `admin` tinyint(4) NOT NULL,
  `technical` tinyint(4) NOT NULL,
  `finance` tinyint(4) NOT NULL,
  `support` tinyint(4) NOT NULL,
  `marketing` tinyint(4) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (1,'Add Users',1,0,0,0,0,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (2,'Rolls Management',1,0,0,0,0,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (3,'View/Edit Profile',1,1,1,1,1,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (4,'Dashboard',1,1,1,1,1,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (5,'Add/Edit Apps',1,1,0,0,0,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (6,'Help',1,1,0,0,0,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (9,'Respond',1,0,0,1,0,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (12,'Documentation',1,1,1,1,1,'active');
INSERT INTO `tblrole` (`intid`,`feature`,`admin`,`technical`,`finance`,`support`,`marketing`,`status`) VALUES (13,'Activity Log',1,0,0,0,0,'active');

--
-- Table structure for table `tblsettings`
--

DROP TABLE IF EXISTS `tblsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblsettings` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `info` text,
  `serialize` enum('0','1') NOT NULL DEFAULT '0',
  `option_group` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (1,'recordperpage','10',NULL,'0','general');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (2,'google_analytics_code','',NULL,'0','general');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (3,'title','Backbone bits',NULL,'0','seo');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (4,'s_keyword','Backbone bits',NULL,'0','seo');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (5,'s_metadesc','Backbone bits',NULL,'0','seo');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (6,'app_name','Backbone bits',NULL,'0','title');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (7,'width','940',NULL,'0','slide');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (8,'height','350',NULL,'0','slide');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (9,'facebook','',NULL,'0','social');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (10,'twitter','',NULL,'0','social');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (11,'linkedin','',NULL,'0','social');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (12,'login','',NULL,'0','social');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (13,'varemailrecive','test@test.com',NULL,'0','general');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (14,'varsenderemial','test@test.com',NULL,'0','general');
INSERT INTO `tblsettings` (`intid`,`option_name`,`value`,`info`,`serialize`,`option_group`) VALUES (15,'google','',NULL,'0','social');

--
-- Table structure for table `tbltransactions`
--

DROP TABLE IF EXISTS `tbltransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbltransactions` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(100) DEFAULT NULL,
  `trans_id` varchar(50) NOT NULL,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL DEFAULT '0',
  `package_type` enum('subscription','topup') NOT NULL COMMENT 'subscription or top up',
  `amount` double(10,2) NOT NULL,
  `status` enum('cr','db') NOT NULL,
  `period` varchar(100) NOT NULL,
  `dtadd` datetime NOT NULL,
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbltutorial_animations`
--

DROP TABLE IF EXISTS `tbltutorial_animations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbltutorial_animations` (
  `intid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dtadd` datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tbltutorial_animations` (`intid`,`name`,`dtadd`,`status`) VALUES (1,'Swipe left to right',NOW(),'active');
INSERT INTO `tbltutorial_animations` (`intid`,`name`,`dtadd`,`status`) VALUES (2,'Swipe right to left',NOW(),'active');
INSERT INTO `tbltutorial_animations` (`intid`,`name`,`dtadd`,`status`) VALUES (3,'Swipe top to bottom',NOW(),'active');
INSERT INTO `tbltutorial_animations` (`intid`,`name`,`dtadd`,`status`) VALUES (4,'Swipe bottom to top',NOW(),'active');