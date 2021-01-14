-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 14, 2021 at 05:03 PM
-- Server version: 8.0.21
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ec`
--

-- --------------------------------------------------------

--
-- Table structure for table `ec_anti_email_spam`
--

DROP TABLE IF EXISTS `ec_anti_email_spam`;
CREATE TABLE IF NOT EXISTS `ec_anti_email_spam` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `USER_ID` int NOT NULL,
  `TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_anti_horde`
--

DROP TABLE IF EXISTS `ec_anti_horde`;
CREATE TABLE IF NOT EXISTS `ec_anti_horde` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ITEM_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `LIMIT_Q` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_avatar`
--

DROP TABLE IF EXISTS `ec_avatar`;
CREATE TABLE IF NOT EXISTS `ec_avatar` (
  `USER_ID` int NOT NULL,
  `BODY_ITEM_ID` int NOT NULL,
  `FACE_ITEM_ID` int NOT NULL,
  `HEAD_ITEM_ID` int NOT NULL,
  `TOOL_ITEM_ID` int NOT NULL,
  `MASK_ITEM_ID` int NOT NULL DEFAULT '-1',
  `EYES_ITEM_ID` int NOT NULL DEFAULT '0',
  `HAIR_ITEM_ID` int NOT NULL DEFAULT '0',
  `HEAD_2_ITEM_ID` int NOT NULL DEFAULT '0',
  `SHIRT_ITEM_ID` int NOT NULL DEFAULT '0',
  `TROU_ITEM_ID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_ban_logs`
--

DROP TABLE IF EXISTS `ec_ban_logs`;
CREATE TABLE IF NOT EXISTS `ec_ban_logs` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MOD_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `LENGTH` int NOT NULL,
  `REASON` varchar(999) NOT NULL,
  `START_TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_crate`
--

DROP TABLE IF EXISTS `ec_crate`;
CREATE TABLE IF NOT EXISTS `ec_crate` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ITEM_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `SERIAL` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_forums`
--

DROP TABLE IF EXISTS `ec_forums`;
CREATE TABLE IF NOT EXISTS `ec_forums` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `DESCRIPTION` varchar(999) NOT NULL,
  `THREADS` int NOT NULL,
  `POSTS` int NOT NULL,
  `LAST_USER` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_forum_posts`
--

DROP TABLE IF EXISTS `ec_forum_posts`;
CREATE TABLE IF NOT EXISTS `ec_forum_posts` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `BODY` varchar(2000) NOT NULL,
  `TABLE_ID` int NOT NULL,
  `THREAD_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_forum_threads`
--

DROP TABLE IF EXISTS `ec_forum_threads`;
CREATE TABLE IF NOT EXISTS `ec_forum_threads` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(30) NOT NULL DEFAULT 'NONE',
  `BODY` varchar(2000) NOT NULL DEFAULT 'NONE',
  `USER_ID` int NOT NULL,
  `LOCKED` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `PINNED` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `TABLE_ID` int NOT NULL,
  `TIME` int NOT NULL,
  `LAST_TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_friends`
--

DROP TABLE IF EXISTS `ec_friends`;
CREATE TABLE IF NOT EXISTS `ec_friends` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SENDER_ID` int NOT NULL,
  `RECEIVE_ID` int NOT NULL,
  `PENDING` enum('YES','NO') NOT NULL,
  `ACCEPTED` enum('YES','NO') NOT NULL,
  `DECLINED` enum('YES','NO') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_items`
--

DROP TABLE IF EXISTS `ec_items`;
CREATE TABLE IF NOT EXISTS `ec_items` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NAME` varchar(75) NOT NULL DEFAULT 'HAT.NAME.NEW',
  `DESCRIPTION` varchar(1000) NOT NULL DEFAULT 'HAT.DESCRIPTION.NEW',
  `GOLD_PRICE` int NOT NULL,
  `SILVER_PRICE` int NOT NULL,
  `OFFSALE` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `PREVIEW_IMG_URL` varchar(1000) NOT NULL DEFAULT '../EpicClubRebootMisc/IMGS/MAIN/Hat_Error.png',
  `AVATAR_IMG_URL` varchar(1000) NOT NULL DEFAULT '../EpicClubRebootMisc/IMGS/MAIN/Template_Blank.png',
  `RARE` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `STOCK` int NOT NULL DEFAULT '-1',
  `ORIGINAL_STOCK` int NOT NULL DEFAULT '-1',
  `SALES` int NOT NULL,
  `TIME` int NOT NULL DEFAULT '1500000000',
  `LAYER` enum('BODY','FACE','HEAD','TOOL','MASK','EYES','HAIR','HEAD_2','SHIRT','TROU') NOT NULL DEFAULT 'HEAD',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_item_comments`
--

DROP TABLE IF EXISTS `ec_item_comments`;
CREATE TABLE IF NOT EXISTS `ec_item_comments` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ITEM_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `COMMENT` varchar(255) NOT NULL,
  `TIME` int NOT NULL,
  `LIKES` int NOT NULL DEFAULT '0',
  `DISLIKES` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_membership`
--

DROP TABLE IF EXISTS `ec_membership`;
CREATE TABLE IF NOT EXISTS `ec_membership` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `USER_ID` int NOT NULL,
  `START_TIME` int NOT NULL,
  `END_TIME` int NOT NULL,
  `ACTIVE` enum('YES','NO') NOT NULL,
  `TYPE` enum('VIP','MEGA_VIP') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_messages`
--

DROP TABLE IF EXISTS `ec_messages`;
CREATE TABLE IF NOT EXISTS `ec_messages` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SENDER_ID` int NOT NULL,
  `RECEIVE_ID` int NOT NULL,
  `TITLE` varchar(30) NOT NULL,
  `BODY` varchar(2000) NOT NULL,
  `PAST_TEXT` varchar(8000) NOT NULL,
  `TIME` int NOT NULL,
  `SEEN` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_mod_logs`
--

DROP TABLE IF EXISTS `ec_mod_logs`;
CREATE TABLE IF NOT EXISTS `ec_mod_logs` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MOD_ID` int NOT NULL,
  `USER_ID` int NOT NULL,
  `ACTION` varchar(255) NOT NULL,
  `TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_mod_uploads`
--

DROP TABLE IF EXISTS `ec_mod_uploads`;
CREATE TABLE IF NOT EXISTS `ec_mod_uploads` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `AVATAR_IMG_URL` varchar(255) NOT NULL,
  `PREVIEW_IMG_URL` varchar(255) NOT NULL,
  `PENDING` enum('YES','NO') NOT NULL,
  `USER_ID` int NOT NULL,
  `RARE` enum('YES','NO') NOT NULL,
  `STOCK` int NOT NULL,
  `GOLD_PRICE` int NOT NULL,
  `SILVER_PRICE` int NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  `TIME` int NOT NULL,
  `LAYER` enum('BODY','FACE','MASK','HEAD','TOOL','HAIR','EYES','SHIRT','TROU','HEAD_2') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_recent_events`
--

DROP TABLE IF EXISTS `ec_recent_events`;
CREATE TABLE IF NOT EXISTS `ec_recent_events` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `USER_ID` int NOT NULL,
  `STATUS` varchar(255) NOT NULL,
  `TIME` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_reports`
--

DROP TABLE IF EXISTS `ec_reports`;
CREATE TABLE IF NOT EXISTS `ec_reports` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `REPORTER_ID` int NOT NULL,
  `REASON` varchar(500) NOT NULL,
  `VICTIM_ID` varchar(65) NOT NULL,
  `SEEN` enum('YES','NO') NOT NULL,
  `TIME` int NOT NULL,
  `MOD_SEEN_ID` int NOT NULL,
  `MOD_NOTE` varchar(500) NOT NULL DEFAULT 'no action',
  `TYPE` enum('FORUM','USER') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_reselling`
--

DROP TABLE IF EXISTS `ec_reselling`;
CREATE TABLE IF NOT EXISTS `ec_reselling` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `USER_ID` int NOT NULL,
  `ITEM_ID` int NOT NULL,
  `SERIAL` int NOT NULL,
  `PRICE` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_trades`
--

DROP TABLE IF EXISTS `ec_trades`;
CREATE TABLE IF NOT EXISTS `ec_trades` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TRADE_INFO` varchar(2000) NOT NULL,
  `SENDER_ID` int NOT NULL,
  `RECEIVER_ID` int NOT NULL,
  `STATUS` enum('PENDING','ACCEPTED','DECLINED') NOT NULL DEFAULT 'PENDING',
  `MONEY_SENDING` int NOT NULL,
  `MONEY_REQUEST` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_users`
--

DROP TABLE IF EXISTS `ec_users`;
CREATE TABLE IF NOT EXISTS `ec_users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(20) NOT NULL,
  `PASSWORD` varchar(999) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `GENDER` enum('M','F') NOT NULL,
  `GOLD` int NOT NULL DEFAULT '0',
  `SILVER` int NOT NULL DEFAULT '20',
  `POWER` enum('FOUNDER','CO-FOUNDER','ADMIN','MODERATOR','MEMBER') NOT NULL DEFAULT 'MEMBER',
  `VIP` enum('NONE','VIP','MEGA_VIP') NOT NULL DEFAULT 'NONE',
  `BANNED` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `BANNED_TILL` int NOT NULL,
  `JOINED` int NOT NULL,
  `FORUM_POSTS` int NOT NULL,
  `FORUM_SIG` varchar(255) NOT NULL,
  `LAST_ONLINE` int NOT NULL,
  `AVATAR_IMG_URL` varchar(255) NOT NULL DEFAULT 'imgs/default_avatar.png/',
  `IP` varchar(35) NOT NULL DEFAULT '0.0.0.0',
  `VERIFIED` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `BIO` varchar(999) NOT NULL DEFAULT 'Hi, I''m new here!',
  `STATUS` varchar(255) NOT NULL,
  `UNI_STRING` varchar(255) NOT NULL,
  `DAILY_COINS` int NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `USERNAME` (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ec_user_assets`
--

DROP TABLE IF EXISTS `ec_user_assets`;
CREATE TABLE IF NOT EXISTS `ec_user_assets` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `DESCRIPTION` varchar(500) NOT NULL,
  `CREATOR_ID` int NOT NULL,
  `TIME_UPLOADED` varchar(500) NOT NULL,
  `SILVER_PRICE` int NOT NULL,
  `AVATAR_IMG` varchar(500) NOT NULL,
  `STATUS` enum('ACCEPTED','PENDING','DECLINED') NOT NULL,
  `TYPE` varchar(10) NOT NULL,
  `STARS` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE IF NOT EXISTS `site_settings` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URL` varchar(999) NOT NULL DEFAULT 'localhost',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

DROP TABLE IF EXISTS `temp`;
CREATE TABLE IF NOT EXISTS `temp` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `IP` varchar(999) NOT NULL,
  `LOC` varchar(999) NOT NULL,
  `COUNTRY` varchar(999) NOT NULL,
  `PHONE` varchar(999) NOT NULL,
  `STATE` varchar(999) NOT NULL,
  `CITY` varchar(999) NOT NULL,
  `TIME` varchar(1000) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
