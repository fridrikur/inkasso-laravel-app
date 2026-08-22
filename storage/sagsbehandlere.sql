-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Vært: mysql219.curanet.dk
-- Genereringstid: 21. 08 2026 kl. 17:00:32
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
(14, 17, 'Anette Skarnvad', '', 0, 0, 0),
(17, 5, 'Louise', '', 0, -1, 0),
(20, 29, 'Sussi Skov', '', 0, -1, 0),
(22, 3, 'Sanne', '', 0, 0, 0),
(25, 33, 'Diverse', '', 0, -1, 0),
(26, 19, 'Diverse', '', 0, 0, 0),
(28, 7, 'Claus Laugaard', '', 0, 0, 0),
(29, 11, 'Gitte Ingstrup', '', 0, 0, 0),
(30, 13, 'Diverse', '', 0, 0, 0),
(41, 0, 'Carl Erik', 'dkg@dkg-aps.dk', 22226860, 0, 0),
(42, 0, 'Per', 'per@dkg-aps.dk', 22226862, 0, 0),
(69, 34, 'abc', '', 0, 0, 0),
(70, 34, 'sss', '', 0, 0, 0),
(79, 11, 'Margit Günther', '', 0, 0, 0),
(80, 11, 'Mickey Madsen', '', 0, -1, 0),
(82, 17, 'Vibeke Nielsen', '', 0, 0, 0),
(83, 17, 'Anette Reuter', '', 0, 0, 0),
(85, 15, 'Lotte Jacobsen', '', 0, -1, 0),
(93, 10, 'Kim Mithiof', '0', 0, -1, 224),
(94, 0, 'Majbrit', '', 0, 0, 0),
(96, 17, 'Tina Køhl', '', 0, 0, 0),
(98, 7, 'Dennis Hørlykke', '', 0, 0, 0),
(99, 111, 'Kis Mortensen', '', 0, -1, 0),
(102, 31, 'Maiken Lass', '', 0, -1, 0),
(103, 14, 'Anne', '', 0, -1, 220),
(104, 23, 'Morten Petersen', '', 0, -1, 0),
(105, 1, 'Anita Mikkelsen', '', 0, -1, 0),
(106, 16, 'Dorte Skau', '', 0, 0, 0),
(107, 2, 'Camilla Røhl', '', 0, -1, 0),
(109, 25, 'Jesper Waterval', '', 0, 0, 0),
(113, 30, 'Jane Jensen', '', 0, -1, 0),
(114, 32, 'Morten Ringius Christensen', '', 0, 0, 0),
(116, 0, 'Annette', 'sager@dkg-aps.dk', 22226894, 0, 0),
(117, 5, 'Louise', '', 0, -1, 0),
(118, 6, 'Pernille', '', 0, -1, 187),
(120, 29, 'Janus Pedersen', 'jap@salonsupport.dk', 36161609, 0, 0),
(133, 4, 'Diverse', '', 0, -1, 163),
(136, 0, 'Forskellige DKG', '', 0, -1, 0),
(137, 20, 'Ole Laursen', '', 0, -1, 0),
(141, 0, 'Jonas', 'dkg@dkg-aps.dk', 22226894, 0, 0),
(149, 110, 'Anita', 'abaimi@almbrand.dk', 0, 0, 0),
(150, 9, 'Diverse - dkg', '', 0, -1, 196),
(151, 8, 'Diverse - DKG', '', 0, 0, 197),
(152, 100, 'Jonas', 'dkg@dkg-aps.dk', 22226860, -1, 0),
(153, 112, 'Carl Erik Petersen', 'cep@dkg-aps.dk', 40401499, -1, 0),
(154, 33, 'Torben Jeppesen', 'info@maxgarage.dk', 40116167, -1, 0),
(155, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(156, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(157, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(158, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(159, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(160, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(161, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(162, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(163, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(164, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(165, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(166, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(167, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(168, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(169, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(170, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(171, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(172, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(173, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(174, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(175, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(176, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(177, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(178, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(179, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(180, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(181, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(182, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(183, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(184, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(185, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(186, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(187, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(188, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(189, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(190, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(191, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(192, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(193, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(194, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(195, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(196, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(197, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(198, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(199, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(200, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(201, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(202, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(203, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(204, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(205, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(206, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(207, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(208, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(209, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(210, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(211, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(212, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(213, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(214, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(215, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(216, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(217, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(218, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(219, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(220, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(221, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(222, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(223, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(224, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(225, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(226, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(227, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(228, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(229, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(230, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(231, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(232, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(233, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(234, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(235, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(236, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(237, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(238, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(239, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(240, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(241, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(242, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(243, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(244, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(245, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(246, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(247, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(248, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(249, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(250, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(251, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(252, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(253, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(254, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(268, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(275, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(278, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(280, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(286, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(294, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(296, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(304, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(305, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(311, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(313, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(314, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(315, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(317, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(318, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(323, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(326, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(327, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(329, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(330, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(331, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(332, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(333, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(334, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(335, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(336, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(337, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(338, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(339, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(340, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(341, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(342, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(343, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(344, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(345, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(346, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(347, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(348, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(349, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(350, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(351, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(352, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(353, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(354, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(355, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(356, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(357, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(358, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(359, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(360, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(361, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(362, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(363, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(364, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(365, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(366, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(367, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(368, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(369, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(370, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(371, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(372, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(373, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(374, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(375, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(376, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(377, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(378, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(379, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(380, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(381, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(382, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(383, 44, 'Ukendt sagsbehandler', '0', 0, 0, 0),
(384, 44, 'SCB-sagsbehandler', 'dkg@dkg-aps.dk', 1010101, -1, 0),
(385, 50, 'Morehouse-konto', 'dkg@dkg-aps.dk', 0, -1, 0),
(386, 8, 'Marianne B. Petersen', '', 0, -1, 0),
(387, 151, 'Lotte Jacobsen', 'lotte@bay-rev.dk', 57, -1, 0),
(388, 15, 'Diverse', '', 0, 0, 0),
(389, 0, 'Christina', 'christina@dkg-aps.dk', 22226892, 0, 0),
(391, 25, 'Claus Pihl ', 'clp@stubbe.dk', 46330414, -1, 0),
(393, 152, 'Pernille L. Højstrøm', 'pernille@bjaeverskovvvs.dk', 61135250, -1, 0),
(394, 35, 'Hanne Christensen', 'hlc@ondriveleasing.dk', 61866980, -1, 0),
(397, 55, 'Kundeservice', 'kunederservice@dkg-aps.dk', 22226860, -1, 0),
(400, 1, 'Stephanie', 'stl@opendo.dk', 0, -1, 0),
(401, 110, 'Stephanie ', 'stl@opendo.dk', 0, -1, 0),
(402, 55, 'Kasper Lemke', 'kasper.lemke@justdrive.today', 61108803, 0, 0),
(403, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(404, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(405, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(406, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(407, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(408, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(409, 44, 'SCB-sagsbehandler', '0', 0, 0, 0),
(411, 46, 'Kundeservice', 'Servicedk@ca-autobank.com', 43228955, -1, 0),
(412, 60, 'Diverse', 'info@wormglas.dk', 33314053, -1, 0),
(413, 70, 'Kundeservice', 'anita.mikkelsen@vanmossel.dk', 0, -1, 0),
(414, 45, 'Kundeservice', 'servicedk@ca-autobank.com', 43228990, -1, 0),
(415, 80, 'Kundeservice', 'info@lxflexleasing.dk', 75222211, -1, 0),
(416, 40, 'Kundeservice', '', 0, -1, 0);

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
