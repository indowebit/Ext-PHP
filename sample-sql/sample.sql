-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 30, 2011 at 02:54 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE IF NOT EXISTS `children` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `person_id`, `name`) VALUES
(1, 1, 'coba'),
(2, 1, 'test'),
(3, 3, 'coba'),
(4, 3, 'test'),
(5, 4, 'coba'),
(6, 4, 'test'),
(7, 5, 'coba'),
(8, 5, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `register_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_login` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `register_date`, `is_active`, `is_login`) VALUES
(4, 'ben update aja', '2011-04-13', 1, 1),
(5, 'bajul', '2011-04-18', 1, 1),
(7, 'ahaha', '2011-04-21', 0, 1),
(8, 'ahaha', '2011-04-21', 1, 0),
(9, 'ahaha', '2011-04-21', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `grafik`
--

CREATE TABLE IF NOT EXISTS `grafik` (
  `tahun` int(11) NOT NULL,
  `page_visit` int(11) NOT NULL,
  `page_view` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grafik`
--

INSERT INTO `grafik` (`tahun`, `page_visit`, `page_view`) VALUES
(2007, 1000, 2000),
(2008, 2000, 4000),
(2009, 3000, 4000),
(2010, 4000, 6000),
(2011, 2500, 4000);

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `birthday` date NOT NULL,
  `height` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `name`, `birthday`, `height`) VALUES
(4, 'nama 1', '2011-04-24', 0),
(5, 'nama 2', '1899-11-30', 123456);
