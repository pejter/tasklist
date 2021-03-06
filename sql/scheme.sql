-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 03 Gru 2013, 22:53
-- Wersja serwera: 5.1.46
-- Wersja PHP: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `task`
--
CREATE DATABASE IF NOT EXISTS `tasklist`;
USE `tasklist`;
-- --------------------------------------------------------

--
-- Struktura tabeli dla  `group`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `groupsID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE latin2_general_ci NOT NULL,
  PRIMARY KEY (`groupsID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `group`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `membership`
--

CREATE TABLE IF NOT EXISTS `membership` (
  `membershipID` int(11) NOT NULL AUTO_INCREMENT,
  `groupsID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`membershipID`),
  KEY `groupsID` (`groupsID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Zrzut danych tabeli `membership`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `taskID` int(11) NOT NULL AUTO_INCREMENT,
  `groupsID` int(11) NOT NULL,
  `name` varchar(30) COLLATE latin2_general_ci NOT NULL,
  `description` tinytext COLLATE latin2_general_ci NOT NULL,
  `due_date` int(11) UNSIGNED NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`taskID`),
  KEY `groupsID` (`groupsID`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `task`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin2_general_ci,
  `surname` varchar(30) COLLATE latin2_general_ci,
  `mail` varchar(90) COLLATE latin1_general_ci NOT NULL,
  `login` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `password` char(32) NOT NULL,
  `position` tinyint(4) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `user`
--

