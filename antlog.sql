-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2015 at 12:07 AM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `antlog`
--
CREATE DATABASE IF NOT EXISTS `antlog` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `antlog`;

-- --------------------------------------------------------

--
-- Table structure for table `aws_auth_assignment`
--

DROP TABLE IF EXISTS `aws_auth_assignment`;
CREATE TABLE IF NOT EXISTS `aws_auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aws_auth_item`
--

DROP TABLE IF EXISTS `aws_auth_item`;
CREATE TABLE IF NOT EXISTS `aws_auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aws_auth_item_child`
--

DROP TABLE IF EXISTS `aws_auth_item_child`;
CREATE TABLE IF NOT EXISTS `aws_auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aws_auth_rule`
--

DROP TABLE IF EXISTS `aws_auth_rule`;
CREATE TABLE IF NOT EXISTS `aws_auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aws_double_elim`
--

DROP TABLE IF EXISTS `aws_double_elim`;
CREATE TABLE IF NOT EXISTS `aws_double_elim` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',
  `fightGroup` int(11) NOT NULL,
  `fightRound` int(11) NOT NULL,
  `fightBracket` set('W','L','F') NOT NULL,
  `fightNo` int(11) NOT NULL,
  `robot1Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot1Id) REFERENCES aws_robot(id)',
  `robot2Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot2Id) REFERENCES aws_robot(id)',
  `winnerId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (winnerId) REFERENCES aws_robot(id)',
  `loserId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (loserId) REFERENCES aws_robot(id)',
  `winnerNextFight` int(10) unsigned NOT NULL,
  `loserNextFight` int(10) unsigned NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `FightID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Double elimination template' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_entrant`
--

DROP TABLE IF EXISTS `aws_entrant`;
CREATE TABLE IF NOT EXISTS `aws_entrant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',
  `robotId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (robotId) REFERENCES aws_robot(id)',
  `status` int(11) DEFAULT '-1',
  `finalFightId` int(11) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (finalFightId) REFERENCES aws_fights(id)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntrantID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_event`
--

DROP TABLE IF EXISTS `aws_event`;
CREATE TABLE IF NOT EXISTS `aws_event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `state` enum('Complete','Running','Setup','Registration') NOT NULL DEFAULT 'Registration',
  `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_fights`
--

DROP TABLE IF EXISTS `aws_fights`;
CREATE TABLE IF NOT EXISTS `aws_fights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',
  `fightGroup` int(11) NOT NULL,
  `fightRound` int(11) NOT NULL,
  `fightBracket` set('W','L','F') NOT NULL,
  `fightNo` int(11) NOT NULL,
  `robot1Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot1Id) REFERENCES aws_entrant(id)',
  `robot2Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot2Id) REFERENCES aws_entrant(id)',
  `winnerId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (winnerId) REFERENCES aws_entrant(id)',
  `loserId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (loserId) REFERENCES aws_entrant(id)',
  `winnerNextFight` int(10) unsigned NOT NULL,
  `loserNextFight` int(10) unsigned NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `FightID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Double elimination template' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_robot`
--

DROP TABLE IF EXISTS `aws_robot`;
CREATE TABLE IF NOT EXISTS `aws_robot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `teamId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (teamId) REFERENCES aws_user(id)',
  `classId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `RobotID_2` (`id`),
  KEY `RobotID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_robot_class`
--

DROP TABLE IF EXISTS `aws_robot_class`;
CREATE TABLE IF NOT EXISTS `aws_robot_class` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='List of robot weight classes' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_user`
--

DROP TABLE IF EXISTS `aws_user`;
CREATE TABLE IF NOT EXISTS `aws_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `user_group` smallint(6) NOT NULL DEFAULT '2',
  `team_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aws_auth_assignment`
--
ALTER TABLE `aws_auth_assignment`
  ADD CONSTRAINT `aws_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `aws_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `aws_auth_item`
--
ALTER TABLE `aws_auth_item`
  ADD CONSTRAINT `aws_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `aws_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `aws_auth_item_child`
--
ALTER TABLE `aws_auth_item_child`
  ADD CONSTRAINT `aws_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `aws_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aws_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `aws_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Dumping data for table `aws_double_elim`
--

INSERT INTO `aws_double_elim` (`id`, `eventId`, `fightGroup`, `fightRound`, `fightBracket`, `fightNo`, `robot1Id`, `robot2Id`, `winnerId`, `loserId`, `winnerNextFight`, `loserNextFight`, `sequence`) VALUES
(1, 0, 1, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(2, 0, 1, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(3, 0, 1, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(4, 0, 1, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(5, 0, 1, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(6, 0, 1, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(7, 0, 1, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(8, 0, 1, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(9, 0, 1, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(10, 0, 1, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(11, 0, 1, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(12, 0, 1, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(13, 0, 1, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(14, 0, 1, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(15, 0, 1, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(16, 0, 1, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(17, 0, 1, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(18, 0, 1, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(19, 0, 1, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(20, 0, 1, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(21, 0, 1, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(22, 0, 1, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(23, 0, 1, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(24, 0, 1, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(25, 0, 1, 4, 'W', 1, -1, -1, -1, -1, 208, 4, -1),
(26, 0, 1, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(27, 0, 1, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(28, 0, 1, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(29, 0, 1, 7, 'L', 1, -1, -1, -1, -1, 208, 0, -1),
(30, 0, 2, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(31, 0, 2, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(32, 0, 2, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(33, 0, 2, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(34, 0, 2, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(35, 0, 2, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(36, 0, 2, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(37, 0, 2, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(38, 0, 2, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(39, 0, 2, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(40, 0, 2, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(41, 0, 2, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(42, 0, 2, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(43, 0, 2, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(44, 0, 2, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(45, 0, 2, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(46, 0, 2, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(47, 0, 2, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(48, 0, 2, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(49, 0, 2, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(50, 0, 2, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(51, 0, 2, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(52, 0, 2, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(53, 0, 2, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(54, 0, 2, 4, 'W', 1, -1, -1, -1, -1, 181, 4, -1),
(55, 0, 2, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(56, 0, 2, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(57, 0, 2, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(58, 0, 2, 7, 'L', 1, -1, -1, -1, -1, 181, 0, -1),
(59, 0, 3, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(60, 0, 3, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(61, 0, 3, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(62, 0, 3, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(63, 0, 3, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(64, 0, 3, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(65, 0, 3, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(66, 0, 3, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(67, 0, 3, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(68, 0, 3, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(69, 0, 3, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(70, 0, 3, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(71, 0, 3, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(72, 0, 3, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(73, 0, 3, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(74, 0, 3, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(75, 0, 3, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(76, 0, 3, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(77, 0, 3, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(78, 0, 3, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(79, 0, 3, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(80, 0, 3, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(81, 0, 3, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(82, 0, 3, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(83, 0, 3, 4, 'W', 1, -1, -1, -1, -1, 151, 4, -1),
(84, 0, 3, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(85, 0, 3, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(86, 0, 3, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(87, 0, 3, 7, 'L', 1, -1, -1, -1, -1, 151, 0, -1),
(88, 0, 4, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(89, 0, 4, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(90, 0, 4, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(91, 0, 4, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(92, 0, 4, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(93, 0, 4, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(94, 0, 4, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(95, 0, 4, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(96, 0, 4, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(97, 0, 4, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(98, 0, 4, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(99, 0, 4, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(100, 0, 4, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(101, 0, 4, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(102, 0, 4, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(103, 0, 4, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(104, 0, 4, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(105, 0, 4, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(106, 0, 4, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(107, 0, 4, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(108, 0, 4, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(109, 0, 4, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(110, 0, 4, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(111, 0, 4, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(112, 0, 4, 4, 'W', 1, -1, -1, -1, -1, 124, 4, -1),
(113, 0, 4, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(114, 0, 4, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(115, 0, 4, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(116, 0, 4, 7, 'L', 1, -1, -1, -1, -1, 124, 0, -1),
(117, 0, 5, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(118, 0, 5, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(119, 0, 5, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(120, 0, 5, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(121, 0, 5, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(122, 0, 5, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(123, 0, 5, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(124, 0, 5, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(125, 0, 5, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(126, 0, 5, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(127, 0, 5, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(128, 0, 5, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(129, 0, 5, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(130, 0, 5, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(131, 0, 5, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(132, 0, 5, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(133, 0, 5, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(134, 0, 5, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(135, 0, 5, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(136, 0, 5, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(137, 0, 5, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(138, 0, 5, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(139, 0, 5, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(140, 0, 5, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(141, 0, 5, 4, 'W', 1, -1, -1, -1, -1, 92, 4, -1),
(142, 0, 5, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(143, 0, 5, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(144, 0, 5, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(145, 0, 5, 7, 'L', 1, -1, -1, -1, -1, 92, 0, -1),
(146, 0, 6, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(147, 0, 6, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(148, 0, 6, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(149, 0, 6, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(150, 0, 6, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(151, 0, 6, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(152, 0, 6, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(153, 0, 6, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(154, 0, 6, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(155, 0, 6, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(156, 0, 6, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(157, 0, 6, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(158, 0, 6, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(159, 0, 6, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(160, 0, 6, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(161, 0, 6, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(162, 0, 6, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(163, 0, 6, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(164, 0, 6, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(165, 0, 6, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(166, 0, 6, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(167, 0, 6, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(168, 0, 6, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(169, 0, 6, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(170, 0, 6, 4, 'W', 1, -1, -1, -1, -1, 65, 4, -1),
(171, 0, 6, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(172, 0, 6, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(173, 0, 6, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(174, 0, 6, 7, 'L', 1, -1, -1, -1, -1, 65, 0, -1),
(175, 0, 7, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(176, 0, 7, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(177, 0, 7, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(178, 0, 7, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(179, 0, 7, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(180, 0, 7, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(181, 0, 7, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(182, 0, 7, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(183, 0, 7, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(184, 0, 7, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(185, 0, 7, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(186, 0, 7, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(187, 0, 7, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(188, 0, 7, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(189, 0, 7, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(190, 0, 7, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(191, 0, 7, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(192, 0, 7, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(193, 0, 7, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(194, 0, 7, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(195, 0, 7, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(196, 0, 7, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(197, 0, 7, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(198, 0, 7, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(199, 0, 7, 4, 'W', 1, -1, -1, -1, -1, 35, 4, -1),
(200, 0, 7, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(201, 0, 7, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(202, 0, 7, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(203, 0, 7, 7, 'L', 1, -1, -1, -1, -1, 35, 0, -1),
(204, 0, 8, 1, 'W', 1, 0, 0, -1, -1, 12, 8, -1),
(205, 0, 8, 1, 'W', 2, 0, 0, -1, -1, 12, 8, -1),
(206, 0, 8, 1, 'W', 3, 0, 0, -1, -1, 12, 8, -1),
(207, 0, 8, 1, 'W', 4, 0, 0, -1, -1, 12, 8, -1),
(208, 0, 8, 1, 'W', 5, 0, 0, -1, -1, 8, 4, -1),
(209, 0, 8, 1, 'W', 6, 0, 0, -1, -1, 8, 4, -1),
(210, 0, 8, 1, 'W', 7, 0, 0, -1, -1, 8, 4, -1),
(211, 0, 8, 1, 'W', 8, 0, 0, -1, -1, 8, 4, -1),
(212, 0, 8, 2, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(213, 0, 8, 2, 'L', 2, -1, -1, -1, -1, 8, 0, -1),
(214, 0, 8, 2, 'L', 3, -1, -1, -1, -1, 8, 0, -1),
(215, 0, 8, 2, 'L', 4, -1, -1, -1, -1, 8, 0, -1),
(216, 0, 8, 2, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(217, 0, 8, 2, 'W', 2, -1, -1, -1, -1, 8, 4, -1),
(218, 0, 8, 2, 'W', 3, -1, -1, -1, -1, 6, 4, -1),
(219, 0, 8, 2, 'W', 4, -1, -1, -1, -1, 6, 4, -1),
(220, 0, 8, 3, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(221, 0, 8, 3, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(222, 0, 8, 3, 'L', 3, -1, -1, -1, -1, 4, 0, -1),
(223, 0, 8, 3, 'L', 4, -1, -1, -1, -1, 4, 0, -1),
(224, 0, 8, 3, 'W', 1, -1, -1, -1, -1, 4, 5, -1),
(225, 0, 8, 3, 'W', 2, -1, -1, -1, -1, 3, 5, -1),
(226, 0, 8, 4, 'L', 1, -1, -1, -1, -1, 3, 0, -1),
(227, 0, 8, 4, 'L', 2, -1, -1, -1, -1, 3, 0, -1),
(228, 0, 8, 4, 'W', 1, -1, -1, -1, -1, 8, 4, -1),
(229, 0, 8, 5, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(230, 0, 8, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(231, 0, 8, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(232, 0, 8, 7, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(233, 0, 9, 1, 'W', 1, -1, -1, -1, -1, 8, 11, -1),
(234, 0, 9, 1, 'W', 2, -1, -1, -1, -1, 8, 12, -1),
(235, 0, 9, 1, 'W', 3, -1, -1, -1, -1, 6, 8, -1),
(236, 0, 9, 1, 'W', 4, -1, -1, -1, -1, 6, 9, -1),
(237, 0, 9, 1, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(238, 0, 9, 1, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(239, 0, 9, 1, 'L', 3, -1, -1, -1, -1, 6, 0, -1),
(240, 0, 9, 1, 'L', 4, -1, -1, -1, -1, 6, 0, -1),
(241, 0, 9, 2, 'W', 1, -1, -1, -1, -1, 6, 9, -1),
(242, 0, 9, 2, 'W', 2, -1, -1, -1, -1, 5, 9, -1),
(243, 0, 9, 2, 'L', 1, -1, -1, -1, -1, 5, 0, -1),
(244, 0, 9, 2, 'L', 2, -1, -1, -1, -1, 5, 0, -1),
(245, 0, 9, 2, 'L', 3, -1, -1, -1, -1, 3, 0, -1),
(246, 0, 9, 2, 'L', 4, -1, -1, -1, -1, 3, 0, -1),
(247, 0, 9, 3, 'W', 1, -1, -1, -1, -1, 7, 6, -1),
(248, 0, 9, 3, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(249, 0, 9, 3, 'L', 2, -1, -1, -1, -1, 2, 0, -1),
(250, 0, 9, 4, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(251, 0, 9, 4, 'L', 2, -1, -1, -1, -1, 1, 0, -1),
(252, 0, 9, 5, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(253, 0, 9, 6, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(254, 0, 10, 1, 'F', 1, -1, -1, -1, -1, 0, 0, -1);

--
-- Dumping data for table `aws_robot_class`
--

INSERT INTO `aws_robot_class` (`id`, `name`) VALUES
(1, 'Antweight'),
(2, 'Fleaweight'),
(3, 'Nanoweight');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
