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

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `group`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `membership`
--

CREATE TABLE IF NOT EXISTS `membership` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  KEY `groupID` (`groupID`),
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
  `groupID` int(11) NOT NULL,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `description` tinytext COLLATE latin1_general_ci NOT NULL,
  `due_date` datetime NOT NULL,
  `created_byID` int(11) NOT NULL,
  PRIMARY KEY (`taskID`),
  KEY `groupID` (`groupID`),
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
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `surname` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `mail` varchar(90) COLLATE latin1_general_ci NOT NULL,
  `login` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `password` binary(20) NOT NULL,
  `position` tinyint(4) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `user`
--

