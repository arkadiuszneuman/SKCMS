-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 04 Lut 2011, 10:08
-- Wersja serwera: 5.1.54
-- Wersja PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `skcms`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `skcms_articles`
--

CREATE TABLE IF NOT EXISTS `skcms_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL,
  `date` datetime NOT NULL,
  `note` text NOT NULL,
  `proporties` int(11) NOT NULL,
  `id_link` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=145 ;

--
-- Zrzut danych tabeli `skcms_articles`
--

INSERT INTO `skcms_articles` (`id`, `title`, `date`, `note`, `proporties`, `id_link`, `author`) VALUES
(144, 'Artykuł numer jeden.', '2011-01-17 23:14:20', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris augue odio, feugiat quis dictum sit amet, sodales at justo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Mauris augue est, ultrices id fringilla eget, rutrum nec elit. Phasellus eu tristique mauris. Donec condimentum laoreet blandit. Suspendisse bibendum fermentum malesuada. Nullam lacus urna, cursus quis dignissim quis, pretium eget risus. Suspendisse nec leo vitae tortor auctor condimentum at a risus. Aliquam eu urna nunc. Aliquam gravida porta nisi nec rhoncus. Vestibulum ut arcu ipsum, at venenatis sapien. Etiam non erat turpis, vel malesuada tortor. Sed lacus neque, hendrerit eu iaculis non, scelerisque nec nibh. Integer quis sapien ante. Morbi consequat, eros ut hendrerit luctus, odio risus malesuada odio, nec sodales orci diam interdum neque. Etiam libero lectus, rutrum vel vestibulum et, laoreet in felis.</p>', 0, 1, 'Admin');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `skcms_comments`
--

CREATE TABLE IF NOT EXISTS `skcms_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=125 ;

--
-- Zrzut danych tabeli `skcms_comments`
--

INSERT INTO `skcms_comments` (`id`, `article_id`, `user_id`, `name`, `note`, `date`) VALUES
(124, 144, 4, '', 'Bardzo dobry artykuł!', '2011-01-19 23:41:26');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `skcms_links`
--

CREATE TABLE IF NOT EXISTS `skcms_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(100) NOT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'kolejność wyświetlania linków',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `skcms_links`
--

INSERT INTO `skcms_links` (`id`, `link`, `order`) VALUES
(1, 'Aktualności', 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `skcms_preferences`
--

CREATE TABLE IF NOT EXISTS `skcms_preferences` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `title` varchar(120) NOT NULL,
  `descritpion` text NOT NULL,
  `options` text NOT NULL COMMENT 'Typ opcji (tak/nie, wł/wył, itp.)',
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `skcms_preferences`
--

INSERT INTO `skcms_preferences` (`id`, `name`, `title`, `descritpion`, `options`, `value`) VALUES
(1, 'homename', 'Nazwa strony', 'Nazwa strony, która będzie pokazywana na belce przeglądarki.', 'text', 'SKCMS - Zwierzęcy System Zarządzania Treścią'),
(2, 'homefooter', 'Stopka strony.', 'Treść stopki Twojej strony.', 'text', 'Copyright by SKCMS Team.'),
(3, 'siteauthor', 'Autor.', 'Autor strony.', 'text', 'SKCMS Team'),
(4, 'defaultstyle', 'Styl domyślny.', 'Domyślny styl Twojej strony.', 'text', 'mips');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `skcms_users`
--

CREATE TABLE IF NOT EXISTS `skcms_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `pass` varchar(33) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `privileges` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Zrzut danych tabeli `skcms_users`
--

INSERT INTO `skcms_users` (`id`, `login`, `pass`, `mail`, `privileges`) VALUES
(4, 'Admin', '21232f297a57a5a743894a0e4a801fc3', 'mrmoro@o2.pl', 127);
