-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Vært: mysql219.curanet.dk
-- Genereringstid: 21. 08 2026 kl. 13:33:30
-- Serverversion: 8.0.46-37
-- PHP-version: 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `d1k2g3dbcom_db`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `sagsbehandlere`
--

CREATE TABLE `sagsbehandlere` (
  `sbID` int NOT NULL,
  `kreditorID` int NOT NULL COMMENT 'kreditor sagsbehandler?',
  `sagsbehandler` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tlf` int NOT NULL COMMENT 'admin sagsbehandler?',
  `hsb` tinyint NOT NULL,
  `brugerID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `sagsbehandlere`
--

INSERT INTO `sagsbehandlere` (`sbID`, `kreditorID`, `sagsbehandler`, `email`, `tlf`, `hsb`, `brugerID`) VALUES
(41, 0, 'Carl Erik', 'dkg@dkg-aps.dk', 22226860, 0, 0),
(42, 0, 'Per', 'per@dkg-aps.dk', 22226862, 0, 0),
(94, 0, 'Majbrit', '', 0, 0, 0),
(116, 0, 'Annette', 'sager@dkg-aps.dk', 22226894, 0, 0),
(136, 0, 'Forskellige DKG', '', 0, -1, 0),
(141, 0, 'Jonas', 'dkg@dkg-aps.dk', 22226894, 0, 0),
(389, 0, 'Christina', 'christina@dkg-aps.dk', 22226892, 0, 0);

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `sagsbehandlere`
--
ALTER TABLE `sagsbehandlere`
  ADD PRIMARY KEY (`sbID`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `sagsbehandlere`
--
ALTER TABLE `sagsbehandlere`
  MODIFY `sbID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=417;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
