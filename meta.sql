-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2015 at 07:08 PM
-- Server version: 5.5.42-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `rclsirzj_meta`
--

-- --------------------------------------------------------

--
-- Table structure for table `champs`
--

CREATE TABLE IF NOT EXISTS `champs` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `role` varchar(7) COLLATE latin1_general_ci NOT NULL,
  `rank` int(11) NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE IF NOT EXISTS `counters` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `champ` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `rating` float NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=180 ;
