-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2015 at 08:28 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `swiftquiz`
--
CREATE DATABASE IF NOT EXISTS `swiftquiz` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `swiftquiz`;

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `eid` bigint(20) NOT NULL,
  `pid` bigint(20) NOT NULL,
  `anscontent` text NOT NULL,
  `score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `etag`
--

CREATE TABLE IF NOT EXISTS `etag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eid` bigint(20) NOT NULL,
  `tagcontent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE IF NOT EXISTS `exam` (
  `eid` bigint(20) NOT NULL AUTO_INCREMENT,
  `adminid` bigint(20) NOT NULL,
  `uname` text NOT NULL,
  `title` text NOT NULL,
  `summary` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `timelimit` bigint(20) NOT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

CREATE TABLE IF NOT EXISTS `problems` (
  `pid` bigint(20) NOT NULL AUTO_INCREMENT,
  `eid` bigint(20) NOT NULL,
  `pcontent` text CHARACTER SET utf8 NOT NULL,
  `ansformat` text CHARACTER SET utf8 NOT NULL,
  `ansunique` tinyint(4) NOT NULL,
  `rightans` text CHARACTER SET utf8 NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uelink`
--

CREATE TABLE IF NOT EXISTS `uelink` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `answerip` varchar(20) NOT NULL,
  `answerdate` datetime NOT NULL,
  `duration` bigint(20) NOT NULL,
  `score` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `age` tinyint(4) NOT NULL,
  `sex` tinyint(4) NOT NULL,
  `email` varchar(50) NOT NULL,
  `lastlogintime` datetime NOT NULL,
  `lastloginip` varchar(24) NOT NULL,
  `regtime` datetime NOT NULL,
  `regip` varchar(24) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `username`, `password`, `age`, `sex`, `email`, `lastlogintime`, `lastloginip`, `regtime`, `regip`, `status`, `type`) VALUES
(27, 'admin', '12345678', 20, 0, 'admin@admin.com', '2015-09-05 12:28:50', '127.0.0.1', '2015-09-05 12:28:50', '127.0.0.1', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `utag`
--

CREATE TABLE IF NOT EXISTS `utag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `tagcontent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
