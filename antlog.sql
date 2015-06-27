-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2015 at 04:33 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Double elimination template' AUTO_INCREMENT=256 ;

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
  `group_num` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntrantID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_event`
--

DROP TABLE IF EXISTS `aws_event`;
CREATE TABLE IF NOT EXISTS `aws_event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `eventDate` date NOT NULL,
  `state` enum('Complete','Running','Setup','Registration','Future') NOT NULL DEFAULT 'Registration',
  `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  `eventType` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'CONSTRAINT FOREIGN KEY (eventType) REFERENCES aws_event_type(id)',
  `num_groups` tinyint(4) NOT NULL DEFAULT '0',
  `offset` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_event_type`
--

DROP TABLE IF EXISTS `aws_event_type`;
CREATE TABLE IF NOT EXISTS `aws_event_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Double elimination template' AUTO_INCREMENT=766 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_robot`
--

DROP TABLE IF EXISTS `aws_robot`;
CREATE TABLE IF NOT EXISTS `aws_robot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `teamId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (teamId) REFERENCES aws_user(id)',
  `classId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  `typeId` smallint(6) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (typeId) REFERENCES aws_robot_type(id)',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `RobotID_2` (`id`),
  KEY `RobotID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=175 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='List of robot weight classes' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `aws_robot_type`
--

DROP TABLE IF EXISTS `aws_robot_type`;
CREATE TABLE IF NOT EXISTS `aws_robot_type` (
  `id` smallint(6) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
