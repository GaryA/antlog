-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2015 at 11:40 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `aws_entrant`
--

INSERT INTO `aws_entrant` (`id`, `eventId`, `robotId`, `status`, `finalFightId`, `group_num`) VALUES
(1, 1, 1, 2, 0, NULL),
(2, 1, 12, 2, 0, NULL),
(3, 1, 13, 2, 0, NULL),
(4, 1, 15, 2, 0, NULL),
(5, 1, 16, 2, 0, NULL),
(6, 1, 17, 2, 0, NULL),
(7, 1, 19, 2, 0, NULL),
(8, 1, 20, 2, 0, NULL),
(9, 1, 21, 2, 0, NULL),
(10, 1, 23, 2, 0, NULL),
(11, 1, 29, 2, 0, NULL),
(12, 1, 24, 2, 0, NULL),
(13, 1, 32, 2, 0, NULL),
(14, 1, 25, 2, 0, NULL),
(15, 1, 26, 2, 0, NULL),
(16, 1, 27, 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `aws_event`
--

DROP TABLE IF EXISTS `aws_event`;
CREATE TABLE IF NOT EXISTS `aws_event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `eventDate` date NOT NULL,
  `state` enum('Complete','Running','Setup','Registration','Future') NOT NULL DEFAULT 'Registration',
  `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  `offset` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `aws_event`
--

INSERT INTO `aws_event` (`id`, `name`, `eventDate`, `state`, `classId`, `offset`) VALUES
(1, 'Test AWS', '2015-05-09', 'Registration', 1, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Double elimination template' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
