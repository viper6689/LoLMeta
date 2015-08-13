-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2015 at 04:00 PM
-- Server version: 5.5.42-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `rclsirzj_lolmeta`
--

-- --------------------------------------------------------

--
-- Table structure for table `champs`
--

CREATE TABLE `champs` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `role` varchar(7) COLLATE latin1_general_ci NOT NULL,
  `rank` int(11) NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `champ` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `rating` float NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matchHistory`
--

CREATE TABLE `matchHistory` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `summonerID` int(11) NOT NULL,
  `matchID` int(11) unsigned NOT NULL,
  `queueType` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `creation` bigint(11) unsigned NOT NULL,
  `champ` int(11) NOT NULL,
  `lane` varchar(7) COLLATE latin1_general_ci NOT NULL,
  `duration` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `deaths` int(11) NOT NULL,
  `assists` int(11) NOT NULL,
  `win` int(11) NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `summonerInfo`
--

CREATE TABLE `summonerInfo` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `summonerID` int(11) NOT NULL,
  `name` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `league` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `division` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `points` int(11) NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teamInfo`
--

CREATE TABLE `teamInfo` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `teamID` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `league` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `division` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `points` int(11) NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teamMatchHistory`
--

CREATE TABLE `teamMatchHistory` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `matchID` int(11) unsigned NOT NULL,
  `mapID` int(11) NOT NULL,
  `creation` bigint(20) unsigned NOT NULL,
  `win` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `deaths` int(11) NOT NULL,
  `assists` int(11) NOT NULL,
  `opponent` varchar(30) COLLATE latin1_general_ci NOT NULL,
  KEY `index` (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
