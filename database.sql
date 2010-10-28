-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas wygenerowania: 28 Paź 2010, 23:49
-- Wersja serwera: 5.1.49
-- Wersja PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `database`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `pass` varchar(33) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `privileges` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `admins`
--

INSERT INTO `admins` (`id`, `login`, `pass`, `name`, `mail`, `privileges`) VALUES
(4, 'sowa', ' 098f6bcd4621d373cade4e832627b4f6', 'Arek', 'mrmoro@o2.pl', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL,
  `date` datetime NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Zrzut danych tabeli `news`
--

INSERT INTO `news` (`id`, `title`, `date`, `note`) VALUES
(1, 'Test', '0000-00-00 00:00:00', ''),
(2, '', '2010-10-27 21:10:20', 'testestestes'),
(3, 'Test', '2010-10-27 21:10:42', 'testestestes'),
(4, 'Test', '2010-10-27 15:11:13', 'testestestes'),
(5, 'Test', '2010-10-27 15:17:29', 'testestestes'),
(6, 'Test', '2010-10-27 22:25:40', 'testestestes\n\n asdasd\n asdasdasdawdaa');
