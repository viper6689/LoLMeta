-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2015 at 07:07 PM
-- Server version: 5.5.42-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `rclsirzj_matchHistory`
--

-- --------------------------------------------------------

--
-- Table structure for table `league`
--

CREATE TABLE IF NOT EXISTS `league` (
  `playerOrTeamId` varchar(41) COLLATE latin1_general_ci DEFAULT NULL,
  `playerOrTeamName` varchar(30) COLLATE latin1_general_ci NOT NULL DEFAULT 'unknown',
  `queue` set('RANKED_SOLO_5x5','RANKED_TEAM_3x3','RANKED_TEAM_5x5') COLLATE latin1_general_ci NOT NULL DEFAULT 'RANKED_SOLO_5x5',
  `tier` set('CHALLENGER','MASTER','DIAMOND','PLATINUM','GOLD','SILVER','BRONZE') COLLATE latin1_general_ci NOT NULL DEFAULT 'CHALLENGER',
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL DEFAULT 'unknown',
  `division` set('V','IV','III','II','I') COLLATE latin1_general_ci NOT NULL DEFAULT 'V',
  `leaguePoints` tinyint(4) NOT NULL DEFAULT '0',
  `wins` smallint(5) unsigned NOT NULL DEFAULT '0',
  `losses` smallint(5) unsigned NOT NULL DEFAULT '0',
  `isHotStreak` tinyint(1) NOT NULL DEFAULT '0',
  `isFreshBlood` tinyint(1) NOT NULL DEFAULT '0',
  `isVeteran` tinyint(1) NOT NULL DEFAULT '0',
  `isInactive` tinyint(1) NOT NULL DEFAULT '0',
  `seriesWins` tinyint(1) unsigned DEFAULT NULL,
  `seriesLosses` tinyint(1) unsigned DEFAULT NULL,
  `seriesTarget` tinyint(1) unsigned DEFAULT NULL,
  `seriesProgress` set('NNN','WNN','LNN','WLN','LWN','NNNNN','WNNNN','LNNNN','WWNNN','WLNNN','LLNNN','LWNNN','WWLNN','WLLNN','WLWNN','LLWNN','LWWNN','LWLNN','WWLLN','WLWLN','WLLWN','LLWWN','LWLWN','LWWLN') COLLATE latin1_general_ci DEFAULT NULL,
  UNIQUE KEY `index` (`playerOrTeamId`,`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
