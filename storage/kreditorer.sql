-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Vært: mysql219.curanet.dk
-- Genereringstid: 21. 08 2026 kl. 11:59:09
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
-- Struktur-dump for tabellen `kreditor`
--

CREATE TABLE `kreditor` (
  `id` int NOT NULL,
  `firmanavn` varchar(250) NOT NULL,
  `kreditorID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `kreditor`
--

INSERT INTO `kreditor` (`id`, `firmanavn`, `kreditorID`) VALUES
(1, 'Santander Consumer Bank', 4),
(2, 'PartnerLeasing A/S', 1),
(3, 'AL Finans A/S', 11),
(7, 'Nordania A/S', 17),
(8, 'Kjærgården Auto A/S', 31),
(20, 'Mithiof ApS', 10),
(23, 'VVS Installatør Kurt Jeppson ApS', 14),
(29, 'Arne Stubbe Automobiler A/S', 25),
(30, 'Diverse', 15),
(32, 'SalonSupport.dk', 29),
(34, 'Tandlæge Birgit Haargaard', 5),
(35, 'Centrumklinikken ApS', 6),
(37, 'Boligkontoret', 19),
(38, 'Jyske Finans A/S', 8),
(39, 'PartnerLeasing A/S  (gældspost)', 110),
(42, 'Santander Consumer Bank', 44),
(44, 'DKG ApS / Alm. Brand Leasing', 112),
(45, 'Max Garage ApS', 33),
(46, 'Morehouse A/S', 50),
(51, 'Bays Revisionskontor', 151),
(52, 'Bjæverskov & Ørslev VVS', 152),
(53, 'OnDrive Leasing A/S', 35),
(54, 'CA Auto Finance Danmark A/S', 45),
(55, 'JustDrive ApS', 55),
(56, 'Drivalia Lease Danmark A/S ', 46),
(57, 'GLARMESTERFIRMAET WORM A/S', 60),
(58, 'Van Mossel Automotive Group Denmark A/S ', 70),
(59, 'LX Flexleasing A/S', 80),
(60, 'Santander Consumer Bank', 40);

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `kreditor`
--
ALTER TABLE `kreditor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kreditorID` (`kreditorID`),
  ADD KEY `firmanavn` (`firmanavn`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `kreditor`
--
ALTER TABLE `kreditor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

