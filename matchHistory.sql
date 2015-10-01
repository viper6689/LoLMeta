-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 01, 2015 at 05:15 PM
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
  `playerOrTeamId` varchar(41) COLLATE utf8_unicode_ci DEFAULT NULL,
  `playerOrTeamName` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `queue` set('RANKED_SOLO_5x5','RANKED_TEAM_3x3','RANKED_TEAM_5x5') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'RANKED_SOLO_5x5',
  `tier` set('CHALLENGER','MASTER','DIAMOND','PLATINUM','GOLD','SILVER','BRONZE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'CHALLENGER',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `division` set('V','IV','III','II','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'V',
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
  `seriesProgress` set('NNN','WNN','LNN','WLN','LWN','NNNNN','WNNNN','LNNNN','WWNNN','WLNNN','LLNNN','LWNNN','WWLNN','WLLNN','WLWNN','LLWNN','LWWNN','LWLNN','WWLLN','WLWLN','WLLWN','LLWWN','LWLWN','LWWLN') COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `index` (`playerOrTeamId`,`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match`
--

CREATE TABLE IF NOT EXISTS `match` (
  `matchId` int(10) unsigned NOT NULL DEFAULT '0',
  `queueType` set('RANKED_SOLO_5x5','RANKED_TEAM_3x3','RANKED_TEAM_5x5') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'RANKED_SOLO_5x5',
  `matchCreation` bigint(13) unsigned NOT NULL DEFAULT '0',
  `matchDuration` smallint(4) unsigned NOT NULL DEFAULT '0',
  `mapId` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `matchMode` set('CLASSIC','ODIN','ARAM','TUTORIAL','ONEFORALL','ASCENSION','FIRSTBLOOD','KINGPORO') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'CLASSIC',
  `matchType` set('CUSTOM_GAME','MATCHED_GAME','TUTORIAL_GAME') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MATCHED_GAME',
  `matchVersion` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00.00.0.000',
  `season` set('PRESEASON2015','SEASON2015','PRESEASON2016','SEASON2016') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PRESEASON2015',
  UNIQUE KEY `index` (`matchId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matchlist`
--

CREATE TABLE IF NOT EXISTS `matchlist` (
  `matchId` int(10) unsigned DEFAULT NULL,
  `summonerId` int(8) unsigned DEFAULT NULL,
  `queue` set('RANKED_SOLO_5x5','RANKED_TEAM_3x3','RANKED_TEAM_5x5') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'RANKED_SOLO_5x5',
  `champion` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lane` set('MID','MIDDLE','TOP','JUNGLE','BOT','BOTTOM') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TOP',
  `role` set('DUO','NONE','SOLO','DUO_CARRY','DUO_SUPPORT') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NONE',
  `timestamp` bigint(13) unsigned NOT NULL DEFAULT '0',
  `season` set('PRESEASON2015','SEASON2015','PRESEASON2016','SEASON2016') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PRESEASON2015',
  UNIQUE KEY `index` (`matchId`,`summonerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matchParticipants`
--

CREATE TABLE IF NOT EXISTS `matchParticipants` (
  `matchId` int(10) unsigned DEFAULT NULL,
  `summonerId` int(8) unsigned DEFAULT NULL,
  `winner` tinyint(1) NOT NULL DEFAULT '0',
  `championId` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kills` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deaths` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `assists` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `minionsKilled` smallint(4) unsigned NOT NULL DEFAULT '0',
  `champLevel` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `goldEarned` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `item0` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item1` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item2` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item3` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item4` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item5` smallint(4) unsigned NOT NULL DEFAULT '0',
  `item6` smallint(4) unsigned NOT NULL DEFAULT '0',
  `totalDamageDealtToChampions` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `totalDamageTaken` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `totalHeal` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `totalTimeCrowdControlDealt` smallint(4) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `index` (`matchId`,`summonerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
