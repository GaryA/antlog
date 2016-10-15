-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2015 at 11:16 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Double elimination template' ;

--
-- Dumping data for table `aws_double_elim`
--

INSERT INTO `aws_double_elim` (`id`, `eventId`, `fightGroup`, `fightRound`, `fightBracket`, `fightNo`, `robot1Id`, `robot2Id`, `winnerId`, `loserId`, `winnerNextFight`, `loserNextFight`, `sequence`) VALUES
(1, 0, 1, 1, 'W', 1, 0, 0, -1, -1, 96, 64, -1),
(2, 0, 1, 1, 'W', 2, 0, 0, -1, -1, 97, 65, -1),
(3, 0, 1, 1, 'W', 3, 0, 0, -1, -1, 95, 63, -1),
(4, 0, 1, 1, 'W', 4, 0, 0, -1, -1, 96, 64, -1),
(5, 0, 1, 1, 'W', 5, 0, 0, -1, -1, 92, 60, -1),
(6, 0, 1, 1, 'W', 6, 0, 0, -1, -1, 93, 61, -1),
(7, 0, 1, 1, 'W', 7, 0, 0, -1, -1, 91, 59, -1),
(8, 0, 1, 1, 'W', 8, 0, 0, -1, -1, 92, 60, -1),
(9, 0, 2, 1, 'W', 1, 0, 0, -1, -1, 92, 60, -1),
(10, 0, 2, 1, 'W', 2, 0, 0, -1, -1, 93, 61, -1),
(11, 0, 2, 1, 'W', 3, 0, 0, -1, -1, 91, 59, -1),
(12, 0, 2, 1, 'W', 4, 0, 0, -1, -1, 92, 60, -1),
(13, 0, 2, 1, 'W', 5, 0, 0, -1, -1, 88, 56, -1),
(14, 0, 2, 1, 'W', 6, 0, 0, -1, -1, 89, 57, -1),
(15, 0, 2, 1, 'W', 7, 0, 0, -1, -1, 87, 55, -1),
(16, 0, 2, 1, 'W', 8, 0, 0, -1, -1, 88, 56, -1),
(17, 0, 3, 1, 'W', 1, 0, 0, -1, -1, 88, 56, -1),
(18, 0, 3, 1, 'W', 2, 0, 0, -1, -1, 89, 57, -1),
(19, 0, 3, 1, 'W', 3, 0, 0, -1, -1, 87, 55, -1),
(20, 0, 3, 1, 'W', 4, 0, 0, -1, -1, 88, 56, -1),
(21, 0, 3, 1, 'W', 5, 0, 0, -1, -1, 84, 52, -1),
(22, 0, 3, 1, 'W', 6, 0, 0, -1, -1, 85, 53, -1),
(23, 0, 3, 1, 'W', 7, 0, 0, -1, -1, 83, 51, -1),
(24, 0, 3, 1, 'W', 8, 0, 0, -1, -1, 84, 52, -1),
(25, 0, 4, 1, 'W', 1, 0, 0, -1, -1, 84, 52, -1),
(26, 0, 4, 1, 'W', 2, 0, 0, -1, -1, 85, 53, -1),
(27, 0, 4, 1, 'W', 3, 0, 0, -1, -1, 83, 51, -1),
(28, 0, 4, 1, 'W', 4, 0, 0, -1, -1, 84, 52, -1),
(29, 0, 4, 1, 'W', 5, 0, 0, -1, -1, 80, 48, -1),
(30, 0, 4, 1, 'W', 6, 0, 0, -1, -1, 81, 49, -1),
(31, 0, 4, 1, 'W', 7, 0, 0, -1, -1, 79, 47, -1),
(32, 0, 4, 1, 'W', 8, 0, 0, -1, -1, 80, 48, -1),
(33, 0, 5, 1, 'W', 1, 0, 0, -1, -1, 80, 48, -1),
(34, 0, 5, 1, 'W', 2, 0, 0, -1, -1, 81, 49, -1),
(35, 0, 5, 1, 'W', 3, 0, 0, -1, -1, 79, 47, -1),
(36, 0, 5, 1, 'W', 4, 0, 0, -1, -1, 80, 48, -1),
(37, 0, 5, 1, 'W', 5, 0, 0, -1, -1, 76, 44, -1),
(38, 0, 5, 1, 'W', 6, 0, 0, -1, -1, 77, 45, -1),
(39, 0, 5, 1, 'W', 7, 0, 0, -1, -1, 75, 43, -1),
(40, 0, 5, 1, 'W', 8, 0, 0, -1, -1, 76, 44, -1),
(41, 0, 6, 1, 'W', 1, 0, 0, -1, -1, 76, 44, -1),
(42, 0, 6, 1, 'W', 2, 0, 0, -1, -1, 77, 45, -1),
(43, 0, 6, 1, 'W', 3, 0, 0, -1, -1, 75, 43, -1),
(44, 0, 6, 1, 'W', 4, 0, 0, -1, -1, 76, 44, -1),
(45, 0, 6, 1, 'W', 5, 0, 0, -1, -1, 72, 40, -1),
(46, 0, 6, 1, 'W', 6, 0, 0, -1, -1, 73, 41, -1),
(47, 0, 6, 1, 'W', 7, 0, 0, -1, -1, 71, 39, -1),
(48, 0, 6, 1, 'W', 8, 0, 0, -1, -1, 72, 40, -1),
(49, 0, 7, 1, 'W', 1, 0, 0, -1, -1, 72, 40, -1),
(50, 0, 7, 1, 'W', 2, 0, 0, -1, -1, 73, 41, -1),
(51, 0, 7, 1, 'W', 3, 0, 0, -1, -1, 71, 39, -1),
(52, 0, 7, 1, 'W', 4, 0, 0, -1, -1, 72, 40, -1),
(53, 0, 7, 1, 'W', 5, 0, 0, -1, -1, 68, 36, -1),
(54, 0, 7, 1, 'W', 6, 0, 0, -1, -1, 69, 37, -1),
(55, 0, 7, 1, 'W', 7, 0, 0, -1, -1, 67, 35, -1),
(56, 0, 7, 1, 'W', 8, 0, 0, -1, -1, 68, 36, -1),
(57, 0, 8, 1, 'W', 1, 0, 0, -1, -1, 68, 36, -1),
(58, 0, 8, 1, 'W', 2, 0, 0, -1, -1, 69, 37, -1),
(59, 0, 8, 1, 'W', 3, 0, 0, -1, -1, 67, 35, -1),
(60, 0, 8, 1, 'W', 4, 0, 0, -1, -1, 68, 36, -1),
(61, 0, 8, 1, 'W', 5, 0, 0, -1, -1, 64, 32, -1),
(62, 0, 8, 1, 'W', 6, 0, 0, -1, -1, 65, 33, -1),
(63, 0, 8, 1, 'W', 7, 0, 0, -1, -1, 63, 31, -1),
(64, 0, 8, 1, 'W', 8, 0, 0, -1, -1, 64, 32, -1),
(65, 0, 1, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(66, 0, 1, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(67, 0, 1, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(68, 0, 1, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(69, 0, 2, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(70, 0, 2, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(71, 0, 2, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(72, 0, 2, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(73, 0, 3, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(74, 0, 3, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(75, 0, 3, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(76, 0, 3, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(77, 0, 4, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(78, 0, 4, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(79, 0, 4, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(80, 0, 4, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(81, 0, 5, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(82, 0, 5, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(83, 0, 5, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(84, 0, 5, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(85, 0, 6, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(86, 0, 6, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(87, 0, 6, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(88, 0, 6, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(89, 0, 7, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(90, 0, 7, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(91, 0, 7, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(92, 0, 7, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(93, 0, 8, 2, 'L', 1, -1, -1, -1, -1, 64, 0, -1),
(94, 0, 8, 2, 'L', 2, -1, -1, -1, -1, 64, 0, -1),
(95, 0, 8, 2, 'L', 3, -1, -1, -1, -1, 64, 0, -1),
(96, 0, 8, 2, 'L', 4, -1, -1, -1, -1, 64, 0, -1),
(97, 0, 1, 2, 'W', 1, -1, -1, -1, -1, 64, 33, -1),
(98, 0, 1, 2, 'W', 2, -1, -1, -1, -1, 64, 31, -1),
(99, 0, 1, 2, 'W', 3, -1, -1, -1, -1, 62, 33, -1),
(100, 0, 1, 2, 'W', 4, -1, -1, -1, -1, 62, 31, -1),
(101, 0, 2, 2, 'W', 1, -1, -1, -1, -1, 62, 33, -1),
(102, 0, 2, 2, 'W', 2, -1, -1, -1, -1, 62, 31, -1),
(103, 0, 2, 2, 'W', 3, -1, -1, -1, -1, 60, 33, -1),
(104, 0, 2, 2, 'W', 4, -1, -1, -1, -1, 60, 31, -1),
(105, 0, 3, 2, 'W', 1, -1, -1, -1, -1, 60, 33, -1),
(106, 0, 3, 2, 'W', 2, -1, -1, -1, -1, 60, 31, -1),
(107, 0, 3, 2, 'W', 3, -1, -1, -1, -1, 58, 33, -1),
(108, 0, 3, 2, 'W', 4, -1, -1, -1, -1, 58, 31, -1),
(109, 0, 4, 2, 'W', 1, -1, -1, -1, -1, 58, 33, -1),
(110, 0, 4, 2, 'W', 2, -1, -1, -1, -1, 58, 31, -1),
(111, 0, 4, 2, 'W', 3, -1, -1, -1, -1, 56, 33, -1),
(112, 0, 4, 2, 'W', 4, -1, -1, -1, -1, 56, 31, -1),
(113, 0, 5, 2, 'W', 1, -1, -1, -1, -1, 56, 33, -1),
(114, 0, 5, 2, 'W', 2, -1, -1, -1, -1, 56, 31, -1),
(115, 0, 5, 2, 'W', 3, -1, -1, -1, -1, 54, 33, -1),
(116, 0, 5, 2, 'W', 4, -1, -1, -1, -1, 54, 31, -1),
(117, 0, 6, 2, 'W', 1, -1, -1, -1, -1, 54, 33, -1),
(118, 0, 6, 2, 'W', 2, -1, -1, -1, -1, 54, 31, -1),
(119, 0, 6, 2, 'W', 3, -1, -1, -1, -1, 52, 33, -1),
(120, 0, 6, 2, 'W', 4, -1, -1, -1, -1, 52, 31, -1),
(121, 0, 7, 2, 'W', 1, -1, -1, -1, -1, 52, 33, -1),
(122, 0, 7, 2, 'W', 2, -1, -1, -1, -1, 52, 31, -1),
(123, 0, 7, 2, 'W', 3, -1, -1, -1, -1, 50, 33, -1),
(124, 0, 7, 2, 'W', 4, -1, -1, -1, -1, 50, 31, -1),
(125, 0, 8, 2, 'W', 1, -1, -1, -1, -1, 50, 33, -1),
(126, 0, 8, 2, 'W', 2, -1, -1, -1, -1, 50, 31, -1),
(127, 0, 8, 2, 'W', 3, -1, -1, -1, -1, 48, 33, -1),
(128, 0, 8, 2, 'W', 4, -1, -1, -1, -1, 48, 31, -1),
(129, 0, 1, 3, 'L', 1, -1, -1, -1, -1, 48, 0, -1),
(130, 0, 1, 3, 'L', 2, -1, -1, -1, -1, 48, 0, -1),
(131, 0, 1, 3, 'L', 3, -1, -1, -1, -1, 46, 0, -1),
(132, 0, 1, 3, 'L', 4, -1, -1, -1, -1, 46, 0, -1),
(133, 0, 2, 3, 'L', 1, -1, -1, -1, -1, 46, 0, -1),
(134, 0, 2, 3, 'L', 2, -1, -1, -1, -1, 46, 0, -1),
(135, 0, 2, 3, 'L', 3, -1, -1, -1, -1, 44, 0, -1),
(136, 0, 2, 3, 'L', 4, -1, -1, -1, -1, 44, 0, -1),
(137, 0, 3, 3, 'L', 1, -1, -1, -1, -1, 44, 0, -1),
(138, 0, 3, 3, 'L', 2, -1, -1, -1, -1, 44, 0, -1),
(139, 0, 3, 3, 'L', 3, -1, -1, -1, -1, 42, 0, -1),
(140, 0, 3, 3, 'L', 4, -1, -1, -1, -1, 42, 0, -1),
(141, 0, 4, 3, 'L', 1, -1, -1, -1, -1, 42, 0, -1),
(142, 0, 4, 3, 'L', 2, -1, -1, -1, -1, 42, 0, -1),
(143, 0, 4, 3, 'L', 3, -1, -1, -1, -1, 40, 0, -1),
(144, 0, 4, 3, 'L', 4, -1, -1, -1, -1, 40, 0, -1),
(145, 0, 5, 3, 'L', 1, -1, -1, -1, -1, 40, 0, -1),
(146, 0, 5, 3, 'L', 2, -1, -1, -1, -1, 40, 0, -1),
(147, 0, 5, 3, 'L', 3, -1, -1, -1, -1, 38, 0, -1),
(148, 0, 5, 3, 'L', 4, -1, -1, -1, -1, 38, 0, -1),
(149, 0, 6, 3, 'L', 1, -1, -1, -1, -1, 38, 0, -1),
(150, 0, 6, 3, 'L', 2, -1, -1, -1, -1, 38, 0, -1),
(151, 0, 6, 3, 'L', 3, -1, -1, -1, -1, 36, 0, -1),
(152, 0, 6, 3, 'L', 4, -1, -1, -1, -1, 36, 0, -1),
(153, 0, 7, 3, 'L', 1, -1, -1, -1, -1, 36, 0, -1),
(154, 0, 7, 3, 'L', 2, -1, -1, -1, -1, 36, 0, -1),
(155, 0, 7, 3, 'L', 3, -1, -1, -1, -1, 34, 0, -1),
(156, 0, 7, 3, 'L', 4, -1, -1, -1, -1, 34, 0, -1),
(157, 0, 8, 3, 'L', 1, -1, -1, -1, -1, 34, 0, -1),
(158, 0, 8, 3, 'L', 2, -1, -1, -1, -1, 34, 0, -1),
(159, 0, 8, 3, 'L', 3, -1, -1, -1, -1, 32, 0, -1),
(160, 0, 8, 3, 'L', 4, -1, -1, -1, -1, 32, 0, -1),
(161, 0, 1, 3, 'W', 1, -1, -1, -1, -1, 32, 40, -1),
(162, 0, 1, 3, 'W', 2, -1, -1, -1, -1, 31, 40, -1),
(163, 0, 2, 3, 'W', 1, -1, -1, -1, -1, 31, 40, -1),
(164, 0, 2, 3, 'W', 2, -1, -1, -1, -1, 30, 40, -1),
(165, 0, 3, 3, 'W', 1, -1, -1, -1, -1, 30, 40, -1),
(166, 0, 3, 3, 'W', 2, -1, -1, -1, -1, 29, 40, -1),
(167, 0, 4, 3, 'W', 1, -1, -1, -1, -1, 29, 40, -1),
(168, 0, 4, 3, 'W', 2, -1, -1, -1, -1, 28, 40, -1),
(169, 0, 5, 3, 'W', 1, -1, -1, -1, -1, 28, 40, -1),
(170, 0, 5, 3, 'W', 2, -1, -1, -1, -1, 27, 40, -1),
(171, 0, 6, 3, 'W', 1, -1, -1, -1, -1, 27, 40, -1),
(172, 0, 6, 3, 'W', 2, -1, -1, -1, -1, 26, 40, -1),
(173, 0, 7, 3, 'W', 1, -1, -1, -1, -1, 26, 40, -1),
(174, 0, 7, 3, 'W', 2, -1, -1, -1, -1, 25, 40, -1),
(175, 0, 8, 3, 'W', 1, -1, -1, -1, -1, 25, 40, -1),
(176, 0, 8, 3, 'W', 2, -1, -1, -1, -1, 24, 40, -1),
(177, 0, 1, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(178, 0, 1, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(179, 0, 2, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(180, 0, 2, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(181, 0, 3, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(182, 0, 3, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(183, 0, 4, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(184, 0, 4, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(185, 0, 5, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(186, 0, 5, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(187, 0, 6, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(188, 0, 6, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(189, 0, 7, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(190, 0, 7, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(191, 0, 8, 4, 'L', 1, -1, -1, -1, -1, 24, 0, -1),
(192, 0, 8, 4, 'L', 2, -1, -1, -1, -1, 24, 0, -1),
(193, 0, 1, 4, 'W', 1, -1, -1, -1, -1, 40, 32, -1),
(194, 0, 2, 4, 'W', 1, -1, -1, -1, -1, 40, 32, -1),
(195, 0, 3, 4, 'W', 1, -1, -1, -1, -1, 40, 32, -1),
(196, 0, 4, 4, 'W', 1, -1, -1, -1, -1, 40, 32, -1),
(197, 0, 5, 4, 'W', 1, -1, -1, -1, -1, 36, 32, -1),
(198, 0, 6, 4, 'W', 1, -1, -1, -1, -1, 36, 32, -1),
(199, 0, 7, 4, 'W', 1, -1, -1, -1, -1, 36, 32, -1),
(200, 0, 8, 4, 'W', 1, -1, -1, -1, -1, 36, 32, -1),
(201, 0, 1, 5, 'L', 1, -1, -1, -1, -1, 16, 0, -1),
(202, 0, 1, 5, 'L', 1, -1, -1, -1, -1, 15, 0, -1),
(203, 0, 2, 5, 'L', 1, -1, -1, -1, -1, 15, 0, -1),
(204, 0, 2, 5, 'L', 1, -1, -1, -1, -1, 14, 0, -1),
(205, 0, 3, 5, 'L', 1, -1, -1, -1, -1, 14, 0, -1),
(206, 0, 3, 5, 'L', 1, -1, -1, -1, -1, 13, 0, -1),
(207, 0, 4, 5, 'L', 1, -1, -1, -1, -1, 13, 0, -1),
(208, 0, 4, 5, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(209, 0, 5, 5, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(210, 0, 5, 5, 'L', 1, -1, -1, -1, -1, 11, 0, -1),
(211, 0, 6, 5, 'L', 1, -1, -1, -1, -1, 11, 0, -1),
(212, 0, 6, 5, 'L', 1, -1, -1, -1, -1, 10, 0, -1),
(213, 0, 7, 5, 'L', 1, -1, -1, -1, -1, 10, 0, -1),
(214, 0, 7, 5, 'L', 1, -1, -1, -1, -1, 9, 0, -1),
(215, 0, 8, 5, 'L', 1, -1, -1, -1, -1, 9, 0, -1),
(216, 0, 8, 5, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(217, 0, 1, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(218, 0, 2, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(219, 0, 3, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(220, 0, 4, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(221, 0, 5, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(222, 0, 6, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(223, 0, 7, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(224, 0, 8, 6, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(225, 0, 1, 7, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(226, 0, 2, 7, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(227, 0, 3, 7, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(228, 0, 4, 7, 'L', 1, -1, -1, -1, -1, 12, 0, -1),
(229, 0, 5, 7, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(230, 0, 6, 7, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(231, 0, 7, 7, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(232, 0, 8, 7, 'L', 1, -1, -1, -1, -1, 8, 0, -1),
(233, 0, 9, 8, 'W', 1, -1, -1, -1, -1, 8, 11, -1),
(234, 0, 9, 8, 'W', 2, -1, -1, -1, -1, 8, 12, -1),
(235, 0, 9, 8, 'W', 3, -1, -1, -1, -1, 6, 8, -1),
(236, 0, 9, 8, 'W', 4, -1, -1, -1, -1, 6, 9, -1),
(237, 0, 9, 8, 'L', 1, -1, -1, -1, -1, 6, 0, -1),
(238, 0, 9, 8, 'L', 2, -1, -1, -1, -1, 6, 0, -1),
(239, 0, 9, 8, 'L', 3, -1, -1, -1, -1, 6, 0, -1),
(240, 0, 9, 8, 'L', 4, -1, -1, -1, -1, 6, 0, -1),
(241, 0, 9, 9, 'W', 1, -1, -1, -1, -1, 6, 9, -1),
(242, 0, 9, 9, 'W', 2, -1, -1, -1, -1, 5, 9, -1),
(243, 0, 9, 9, 'L', 1, -1, -1, -1, -1, 5, 0, -1),
(244, 0, 9, 9, 'L', 2, -1, -1, -1, -1, 5, 0, -1),
(245, 0, 9, 9, 'L', 3, -1, -1, -1, -1, 3, 0, -1),
(246, 0, 9, 9, 'L', 4, -1, -1, -1, -1, 3, 0, -1),
(247, 0, 9, 10, 'W', 1, -1, -1, -1, -1, 7, 6, -1),
(248, 0, 9, 10, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(249, 0, 9, 10, 'L', 2, -1, -1, -1, -1, 2, 0, -1),
(250, 0, 9, 11, 'L', 1, -1, -1, -1, -1, 2, 0, -1),
(251, 0, 9, 11, 'L', 2, -1, -1, -1, -1, 1, 0, -1),
(252, 0, 9, 12, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(253, 0, 9, 13, 'L', 1, -1, -1, -1, -1, 1, 0, -1),
(254, 0, 10, 14, 'F', 1, -1, -1, -1, -1, 1, 1, -1),
(255, 0, 10, 15, 'F', 1, -1, -1, -1, -1, 0, 0, -1);

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
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntrantID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Entrants table' ;

--
-- Table structure for table `aws_event`
--

DROP TABLE IF EXISTS `aws_event`;
CREATE TABLE IF NOT EXISTS `aws_event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `eventDate` date NOT NULL,
  `state` enum('Complete','Running','Ready','Setup','Closed','Registration','Future') NOT NULL DEFAULT 'Registration',
  `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',
  `eventType` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'CONSTRAINT FOREIGN KEY (eventType) REFERENCES aws_event_type(id)',
  `num_groups` tinyint(4) NOT NULL DEFAULT '0',
  `offset` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `organiserId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (organiserId) REFERENCES aws_user(id)',
  `venue` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Events table' ;

--
-- Table structure for table `aws_event_type`
--

DROP TABLE IF EXISTS `aws_event_type`;
CREATE TABLE IF NOT EXISTS `aws_event_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='List of event types' ;

--
-- Dumping data for table `aws_event_type`
--

INSERT INTO `aws_event_type` (`id`, `name`) VALUES
(1, 'double_elim');

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
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FightID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Fights table' ;

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
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `RobotID_2` (`id`),
  KEY `RobotID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Robots table' ;

--
-- Table structure for table `aws_robot_class`
--

DROP TABLE IF EXISTS `aws_robot_class`;
CREATE TABLE IF NOT EXISTS `aws_robot_class` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='List of robot weight classes' ;

--
-- Dumping data for table `aws_robot_class`
--

INSERT INTO `aws_robot_class` (`id`, `name`) VALUES
(1, 'Nanoweight'),
(2, 'Fleaweight'),
(3, 'Antweight');

-- --------------------------------------------------------

--
-- Table structure for table `aws_robot_type`
--

DROP TABLE IF EXISTS `aws_robot_type`;
CREATE TABLE IF NOT EXISTS `aws_robot_type` (
  `id` smallint(6) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of robot types' ;

--
-- Dumping data for table `aws_robot_type`
--

INSERT INTO `aws_robot_type` (`id`, `name`) VALUES
(0, 'Roller'),
(1, 'Walker'),
(2, 'Cluster');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='User table' ;

--
-- Dumping data for table `aws_user`
--

INSERT INTO `aws_user` (`id`, `username`, `password_hash`, `auth_key`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `user_group`, `team_name`) VALUES
(1, 'admin', '$2y$13$PdkQ4FExnX4OBm51q0WOwuBK1HkXQiGoj9hdt3vHLuIQeoZBCtcsK', 'Ml836D_d49Ja3KiK6eSdrVe8R-nm2A_B', NULL, 'admin@admin.com', 10, 1429478521, 1429740286, 1, '');

--
-- Table structure for table `aws_lock`
--

DROP TABLE IF EXISTS `aws_lock`;
CREATE TABLE IF NOT EXISTS `aws_lock` (
  `id` int(11) NOT NULL,
  `lockState` tinyint(1),
  `lockUserId` int(10),
  `lockTime` INT(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Lock table' ;

--
-- Dumping data for table `aws_lock`
--

INSERT INTO `aws_lock` (`id`, `lockState`, `lockUserId`, `lockTime`) VALUES (1, 0, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

SELECT 'Antlog has been installed successfully!' AS '';