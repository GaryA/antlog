-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2018 at 10:42 PM
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
-- Table structure for table `aws_current_fight`
--

DROP TABLE IF EXISTS `aws_current_fight`;
CREATE TABLE IF NOT EXISTS `aws_current_fight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fightId` int(11) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (fightId) REFERENCES aws_fights(id)',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `robot1` varchar(100) DEFAULT NULL,
  `robot2` varchar(100) DEFAULT NULL,
  `team1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `team2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `aws_current_fight`
--

INSERT INTO `aws_current_fight` (`id`, `fightId`, `title`, `robot1`, `robot2`, `team1`, `team2`, `updated_at`) VALUES
(1, 0, NULL, NULL, NULL, NULL, NULL, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
