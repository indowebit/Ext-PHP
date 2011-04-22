-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 09, 2010 at 07:49 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--

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
-- Table structure for table `sppk`
--

CREATE TABLE IF NOT EXISTS `sppk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jenis` varchar(128) NOT NULL,
  `tanggal` date NOT NULL,
  `perihal` varchar(128) NOT NULL,
  `kode` varchar(128) NOT NULL,
  `tahun` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `sppk`
--

INSERT INTO `sppk` (`id`, `jenis`, `tanggal`, `perihal`, `kode`, `tahun`) VALUES
(1, 'a', '2010-03-17', 'test', '12', '2008'),
(2, 'b', '2010-03-18', 'a', '12', '2009'),
(10, 'test2', '2010-04-02', 'taur', '22', '2009'),
(9, 'CD', '2010-04-08', 'tewur', '07787', '2008'),
(8, 'abc', '2010-04-02', '123', 'ss', '2007'),
(7, 'a', '2010-03-18', 'test', '12', '2008');
