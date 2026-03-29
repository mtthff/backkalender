-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:60027
-- Erstellungszeit: 29. Mrz 2026 um 10:46
-- Server-Version: 10.6.19-MariaDB
-- PHP-Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `backhaus_kalender`
--

--
-- Daten für Tabelle `backgruppen`
--

INSERT INTO `backgruppen` (`id`, `backgruppeName`, `type`, `passwort`, `mail`, `aktiv`) VALUES
(1, 'Männer II', '', '18Anta17', 'v.wachs@gmx.de', 1),
(2, 'Gina & Co.', '', 'ShahPhi2', 'gina.steegmueller@gmx.de', 1),
(3, 'Udo & Damen', '', 'shae0Eji', 'udo.keidel@gmx.net', 1),
(4, 'Beate', '', 'theem1Ae.', 'florahornemann@gmx.de', 1),
(5, 'Verena', '', 'Uw3ohpah', 'haerle.haerle@web.de', 1),
(6, 'Bernhard', '', 'Taex1Chu', 'bv.clauss@t-online.de', 1),
(7, 'Christels Backgäng', '', 'to7ooKar', 'christelmuench@gmx.de', 1),
(8, 'Uta', '', 'Baimah2u', 'servemuc@web.de', 1),
(9, 'Backknappen', '', 'ohng5ooT', 'reinhard.sinz@arcor.de', 1),
(10, 'Hahn im Korb', '', 'Fu1eijoc', 'adrian.kernen@googlemail.com', 1),
(11, 'Die Buhis', '', 'Foo6zooj', 'deborahbuk@alice-dsl.net', 1),
(12, 'Hefeschneckle', '', 'AhCai8en', 'sieweb@web.de', 1),
(13, 'Regina', '', 'jai6eLoo', 'maurerk@web.de', 1),
(14, 'sBackpack', '', 'aejeiW5c', 'cami.steenfatt@t-online.de', 1),
(15, 'Backgeister', '', 'chohTi5u', 'uschi.scharf@t-online.de', 1),
(16, 'Bernsteinbäcker', '', 'oophieR3', 'helmerfamily@t-online.de', 1),
(17, 'Blechbatscher', '', 'ughia5We', 'olaf.fischer@gmx.de', 1),
(18, 'Zimtsterne', '', 'yai4ooC8', 'a.knoeller@gmx.de', 1),
(19, 'Backfeen', '', 'uot5Hooh', 'dietmar.link1@gmx.de', 1),
(20, 'Die Wilden 3', '', 'hifjkr/(7eh4hejwmahm0aJe', 'bauka@web.de', 0),
(21, 'Backteufel', '', 'Cei0aede', 'wolftin@outlook.de', 1),
(22, 'Rührnix', '', 'ahGie4to', 'heidrun@benkmann.de', 1),
(23, 'Gallisches Dorf', '', 'FeiyuM1Z', 'isolde_bayer@yahoo.de', 1),
(24, 'Thekla', '', 'Re8lah0I', 'horst.langer@gmx.de', 1),
(25, 'BACKHAUS-AKTIVITÄTEN', 'vorstand', 'Ohee8oom', 'vorstand@backhaus-heumaden.de', 1),
(28, 'PROBEBACKEN', 'vorstand', 'saiJ1aiD', 'vorstand@backhaus-heumaden.de', 1),
(29, 'Pizzagäng', '', 'Gi4ahghohr', 'susann.deiana@arcor.de', 1),
(31, 'Plus Epsilon', '', '972904', 'Jan.Koellner@zeb.de', 1),
(33, 'Backwahn', '', 'GieNga3oov', 'ines.bohn@gmx.de', 1),
(34, 'Genussgruppe', '', 'Eili9fae', 'rose.alfred@web.de', 1),
(35, 'SONDERBACKEN', 'vorstand', 'Pah1Aewub0yo', 'vorstand@backhaus-heumaden.de', 1),
(36, 'Flotte Feudel', '', 'Mi2shoed', 'f.vuono@gmx.de', 1),
(37, 'Fanta5', '', 'helau15', 'markus@wilbs.net', 1),
(38, 'Holzofen-Allmende', '', 'ethee5Th', 'c-h@riseup.net', 1),
(39, 'Dörren', '', 'dörren', 'edith.alm@web.de', 1),
(40, 'Rudis Bäcker', '', 'Ohpai1oy', 'axel@weirauch.info', 1),
(41, 'BACKHAUS geschlossen', 'vorstand', 'hdHuFg8.rlf', 'vorstand@backhaus-heumaden.de', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
