-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Vært: mysql219.curanet.dk
-- Genereringstid: 21. 08 2026 kl. 11:33:32
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
-- Struktur-dump for tabellen `brugere`
--

CREATE TABLE `brugere` (
  `brugerID` int NOT NULL,
  `brugernavn` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fornavn` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `efternavn` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tlf` int NOT NULL,
  `kodeord` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kreditorID` int NOT NULL,
  `admin` tinyint NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `logged_in` tinyint NOT NULL,
  `googlecode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `brugere`
--

INSERT INTO `brugere` (`brugerID`, `brugernavn`, `fornavn`, `efternavn`, `email`, `tlf`, `kodeord`, 
`kreditorID`, `admin`, `timestamp`, `ip`, `logged_in`, `googlecode`) VALUES
(1, 'admin', 'Fridikur', 'Ellefsen', 'fridrikur@gmail.com', 29609033, 
'$2a$12$Zy/Rn3iZocHALqO0.5.LKObLBH4rR/KWrnXTfE0eKdCkhcg1Vju8G', 0, -1, '0000-00-00 00:00:00', '', -1, 
'AKPIS2GH64A27ZB7'),
(82, 'SCB-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$3sxxLffcIWgKj31hL0fT5u3P2khu12sCFtlw.TUGfjIP/UaZU5mnm', 4, 0, '0000-00-00 00:00:00', '', -1, 
'RYXMFNDWJUKFKGND'),
(135, 'Louise_RTIC', 'Louise', 'tandlæge', 'kontor@ringsted-tandklinik.dk', 24448278, 
'$2y$10$3GpEfIgU3dIZLamEVQDMh.g6StMRKcMczKUS4Gt6J5MjzcJ1dhOra', 5, 0, '2026-07-06 19:22:18', '', 0, 
'AALFJ4HL2FRM4EYL'),
(163, 'Diverse-dkg', '', '', '', 0, 'wV1a/6$-@C_01l!!WXn5', 4, 0, '2021-05-21 09:00:21', '', 0, ''),
(167, 'Jeanette_Ekman-SCB', 'Jeanette', 'Ekman', 'jeanette.ekman@santanderconsumer.dk', 40766766, 
'$2y$10$MrbtBhWOKKp0Z6EgLmkE7O5hId51LLadraxqqntuc.fmcS5MIy8YW', 4, 0, '2025-03-05 07:54:05', '', 0, 
'E3SNPGI2SHM67UCF'),
(171, 'DKG-ALFINANS-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$3Ja386cv82KE21Z1FEOss.wd60fND/RDRrsNXjcTiwKFKn2jYZqGW', 11, 0, '2024-03-06 11:12:40', '', 0, 
'KWURCIJ3B4DYZWST'),
(187, 'Pernille_centrum', 'Pernille', ' Pernille', 'reception@centrumklinikken.dk', 57610086, 
'$2y$10$rMcGR76k9XW2GFDfyRB4GOgyi7J0whNeKc.l1aNZY5iRomD76JGju', 6, 0, '2022-03-29 07:10:27', '', 0, 
'GHYOMWGX4O7L27F5'),
(197, 'Diverse - DKG', '', '', '', 0, 'wNANe-6UHn&gt/*pc', 8, 0, '2021-05-21 08:51:05', '', 0, ''),
(202, 'dkg-alm-CarlE', '', '', '', 0, 'ABlt;3!Gn6iO{UVk=', 112, 0, '2021-05-21 09:29:57', '', 0, ''),
(208, 'OPENDO-DKG-LOGIN-1', 'Jonas Roest', 'Jonas Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$waytQR3k1kYhyLxIX2Y9nen6Z6UGoZNnISqAboiBltk/dO6QPslgy', 1, 0, '2024-12-03 08:18:30', '', 0, 
'BPQYH6TNN3BHVWOO'),
(214, 'admin-ce', 'Carl Erik', 'Petersen', 'cep@dkg-aps.dk', 40401499, 
'$2y$10$HRXAHe/LSI0Tjf/VQhIlo.XrbDTYcIQRpXbG39aeKaZwpp1wdQ/86', 0, -1, '2024-05-07 06:57:00', '', -1, 
'GGXMSGEGJPTFFLDK'),
(218, 'dkg-carl', 'Carl Erik', 'Petersen', 'cep@dkg-aps.dk', 40401499, 
'$2y$10$QKLE.8Mha0/WnlKAVwiwY.AqjuRfjoDzQHJIuKe/xNniO35diqD9S', 0, 0, '0000-00-00 00:00:00', '', -1, 
'NAXCRWQVCMIXWJ35'),
(219, 'dkg-majbrit', 'Majbrit', 'Petersen', 'mlp@dkg-aps.dk', 22226861, 
'$2y$10$SVKGRcRp7FruB9DFF/ziZuUPewuTB9KOqye4TiGBSFgM711nbJo8u', 0, 0, '0000-00-00 00:00:00', '', -1, 
'IKDC7BVW7O54RQMS'),
(220, 'Anne', '', '', '', 0, 'i3HKAE*SD5&amp;n$#', 14, 0, '2021-05-21 09:10:23', '', 0, ''),
(226, 'admin-jonas', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$1lzsiEhhRj2v42Ly9AXM1uCd.vID6BMPvyvtSRORrVXKiZCQRlmCG', 0, -1, '0000-00-00 00:00:00', '', -1, 
'UQRCCQRJPXQUJFUE'),
(227, 'administrator-dkg', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$9CrrCouc5eu7PThpx.DRieit0/8Y1jk./DhAbm6dE3/5Mmoubiak6', 0, -1, '0000-00-00 00:00:00', '', -1, 
'AFENZVPULPBI3IAR'),
(229, 'dkg-jonas', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$OlWn/KIA4oCo43hbL8/8teUensyGtPTfuo3qRbbGLkIoIWe8Tokg.', 0, 0, '0000-00-00 00:00:00', '', -1, 
'O7WU6L2475XYM3VD'),
(231, 'SanFransisco', 'slet', 'mig', 'fridrikur@icloud.com', 29609033, 
'$2y$10$rjHiRS57Vgmreb4CUBX3veTc/Z1jjMXvVcfV.rzZVeSEzOpbn/MBG', 1, 0, '0000-00-00 00:00:00', '2021-08-02 
04:43:56', -1, 'LJN5PGV354FIQV4N'),
(233, 'webudvikler (hellig)', 'Fridrikur', 'Ellefsen', 'fridrikur@gmail.com', 29609033, 
'$2y$10$rjHiRS57Vgmreb4CUBX3veTc/Z1jjMXvVcfV.rzZVeSEzOpbn/MBG', 0, 0, '0000-00-00 00:00:00', '2021-08-05 
12:05:59', -1, '6SXL3JMFZDILHJU3'),
(235, 'SCB44-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$OouNVIWINGZcB8hWqx6ss.jmxcNeR.5VevNnt1oNLR5tsKCd6h.RG', 44, 0, '2023-04-28 11:03:04', '2021-08-17 
16:06:06', 0, 'HJUZQIPGK4KCFORH'),
(238, 'OPENDO-DKG-LOGIN-110', 'JR', 'JR', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$wrVcOyjOPwNcq0ZXWIx1SeEPAES8mg6SIneSzbBR.0OmNvfTKXGx2', 110, 0, '2026-03-11 13:43:52', '2021-12-22 
12:36:23', 0, 'EYZ7NJOQVJIPDDR6'),
(246, 'Hammer', 'Hammer', 'Maskulin', 'fridrikur@gmail.com', 29609033, 
'$2y$10$c5MG7qvq9KliLxBdyAA8puOhVAt.GUmCu3wBu2dwfp8tpNfIh5dGS', 0, 0, '2023-05-05 15:55:12', '2023-05-02 
10:59:57', 0, '7A7AUHS6NGIWGBLE'),
(249, 'Amanda_Weber-SCB', 'Amanda', 'Weber ', 'Amanda.Weber@santanderconsumer.dk', 60818802, 
'$2y$10$EepmgiNkO827peYTtHFc9uxgcYqySKXhidAyCtM4/PU8Gn.gkWlc.', 4, 0, '2023-12-12 13:55:37', '2023-08-03 
14:11:58', 0, 'YWHEY5MRIKSISMCR'),
(250, 'dkg-christina', 'Christina Roest', 'Bang-Petersen', 'christina@dkg-aps.dk', 22226892, 
'$2y$10$akQ3IogGqnKQ.Jb1c6LcPu/1u0aztPJCAMPij3tI2QxCw1k9UQ3YK', 0, 0, '0000-00-00 00:00:00', '2023-10-03 
08:32:09', -1, 'SZMXYINTHV23BPKN'),
(252, 'frid-kred', '', '', '', 29609033, '$2y$10$rjHiRS57Vgmreb4CUBX3veTc/Z1jjMXvVcfV.rzZVeSEzOpbn/MBG', 
4, 0, '0000-00-00 00:00:00', '', -1, ''),
(254, 'AS-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$z5HCQS1uikn9swCR66B4C.RBJUlwirESmDmjGmS6QSc58/SENTz1.', 25, 0, '2025-02-17 08:39:12', '2024-05-15 
09:06:34', 0, 'DFYZECKX3YA2JNME'),
(255, 'ODL-HanneC', 'Hanne Liljehult ', 'Christensen ', 'hlc@ondriveleasing.dk', 61866980, 
'$2y$10$EcpvA.kxeh5tjnrxokaRwuxRGL7KmvCVvxaae8YsmZecf8Xz8KtDW', 35, 0, '0000-00-00 00:00:00', '2024-06-12 
13:27:23', -1, 'KJUNAQSNYH2FXGOG'),
(256, 'ODL-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$2KzB.YbUpNh8KI3h/2P0YevHL13Lpu1XCTfnS3metvU1415RoOiku', 35, 0, '0000-00-00 00:00:00', '2024-06-12 
13:28:00', -1, 'DVU7QTJDM4Z2LONG'),
(257, 'ODL-ThomasL', 'Thomas Lindgren', 'Mortensen', 'thm@ondriveleasing.dk', 30326267, 
'$2y$10$hV1KAUFHWvpjQgNpGx2DLeYaTDjVb8kdkBYR3Jf949ZEJMxyQgcwm', 35, 0, '2025-12-02 09:21:10', '2024-07-23 
09:10:56', 0, '4A7SECGZXIVNHIEZ'),
(258, 'CA-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$aJfnEqE5kqkjaCeWAxifWuHNavwAPncmQRhI32zqJ6HWzfhB31Ttm', 45, 0, '0000-00-00 00:00:00', '2024-09-03 
10:44:07', -1, '26OFABSUPVOSCKOS'),
(261, 'RTIC-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$xqk/kro64b5ALbjsofu0pe3e1c7YJeZ/.XrdCyBD0XlH2dvWI/bPu', 5, 0, '2025-05-20 10:50:21', '2024-09-16 
10:34:41', 0, 'RUBZU5QHFLJXFUNQ'),
(262, 'JustDrive-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$lgut2yHfIQMImWYZL9FieurDNmHmnvijd6AZ9FttWQ.PPvIvjonYa', 55, 0, '2025-02-18 07:40:11', '2024-09-16 
10:43:32', 0, 'UXCMLLSYTK6XKM3Z'),
(263, 'alfred', 'alfred', 'alfred', 'alfred@gmail.com', 12345678, 'Adr7Rv46VE.LE', 0, -1, '0000-00-00 
00:00:00', '', -1, ''),
(264, 'Stephanie_Lund-KD1', 'Stephanie', 'Lund ', 'stl@opendo.dk', 28724432, 
'$2y$10$QU/H75gua4G7rFfs2ZmxcO7T7hrLNZ9iq23BxNrr9EvRxs/cghFru', 1, 0, '0000-00-00 00:00:00', '2024-11-15 
09:57:31', 0, 'U2G3OAFW7ES6ZDOG'),
(265, 'Stephanie_Lund-KD110', 'Stephanie', 'Lund ', 'stl@opendo.dk', 28724432, 
'$2y$10$jmTVmdJy/oef2SdEUhrIPOMka2JcaBJspAGNUvJ/EjX8COCcMhSyO', 110, 0, '2026-06-09 11:53:15', '2024-11-15 
09:58:55', 0, 'NQXXJW3QNOKI4OLM'),
(267, 'Driv-Ricki', 'Ricki', 'Sørensen', 'ricki.sorensen@ca-autobank.com', 26359206, 
'$2y$10$Yt051KAb5EwqPlhHCSu2q.xzdbNd4cJwiGuntH2larXgil6vyLtU6', 46, 0, '0000-00-00 00:00:00', '2025-01-09 
10:33:26', -1, 'EUSJRTQRB2YFJFTD'),
(268, 'DRIV-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$P6uUcWF8/bHII75QpAuD5u6Uqc5GrU/vg.5KMd0SUtaNTjzn4STEy', 46, 0, '0000-00-00 00:00:00', '2025-01-09 
10:34:32', -1, 'N6CPILWW6DHYWOK3'),
(269, 'Driv-Lucas', 'Lucas', 'Jans', 'lucas.jans@external.ca-autobank.com', 61313231, 
'$2y$10$DN2W7cqJydaEJgIzoAeIJ.eRssmY75.MkOrX9DjAJDYFFlWWO8MxS', 46, 0, '2026-07-06 19:22:49', '2025-02-18 
08:30:44', 0, 'CK7HDWCZTNPQ3YM6'),
(272, 'Driv-Claes', 'Claes Egholm', 'Thykjær', 'claesegholm.thykjr@external.ca-autobank.com', 60453309, 
'$2y$10$gDcaqRf3S8XcKbOQfRzSFO6pog005fCHL3.JkrhcVnmExiv6.QmpO', 46, 0, '0000-00-00 00:00:00', '2025-02-18 
08:35:52', 0, 'QBJBLUHKODSWRFWB'),
(273, 'Driv-Nikoline', 'Nikoline', 'Hansen', 'nikoline.hansen@external.ca-autobank.com', 26613978, 
'$2y$10$Po0HKXUqeOcsOD77v8D/3euKPq3ErfK9guSGfjN06RC7AJjRcsCQy', 46, 0, '0000-00-00 00:00:00', '2025-02-18 
08:36:39', -1, 'B62V5KA3HEV5QVEC'),
(275, 'VM-Anita2026', 'Anita', 'Mikkelsen', 'anita.mikkelsen@vanmossel.dk', 60743338, 
'$2y$10$bS663zFJ1j43YiBr6yUK2.4hzdZHrkk/1GrtckQMQGr/i3YtUBfla', 70, 0, '0000-00-00 00:00:00', '2025-06-10 
10:08:44', -1, 'GPBR6MH5VMUQJPHE'),
(276, 'VM-DKG-LOGIN', 'Jonas', 'DKG', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$WUQImHnIwIBemW.csj5RSuS56dXR5lXUcM7y4WnLb.2Hshbr5XcRO', 70, 0, '0000-00-00 00:00:00', '2025-06-10 
10:09:25', -1, 'LB7TO2OYNVCS5AAY'),
(277, 'CA-bil-Ricki', 'Ricki Sørensen', 'Sørensen', 'ricki.sorensen@ca-autobank.com', 26359206, 
'$2y$10$RbU6kCEw95nSJNBllm0cUeoQb1p.TX1sbuxAQXeYPzEEKIJtbAnWO', 45, 0, '0000-00-00 00:00:00', '2025-09-10 
09:35:10', -1, 'T264NLWJAJPORSYP'),
(278, 'CA-bil-Lucas', 'Lucas ', 'Jans', 'lucas.jans@external.ca-autobank.com', 61313231, 
'$2y$10$cxedMz8coDW1EGh0jq5.AeetrhZdklr1qLZBKwGZ7GHOvH6NxXAgy', 45, 0, '0000-00-00 00:00:00', '2025-09-10 
09:35:45', -1, '76MTRX3SF6BP7QPK'),
(281, 'CA-bil-Claes', 'Claes Egholm', 'Thykjær', 'claesegholm.thykjr@external.ca-autobank.com', 60453309, 
'$2y$10$fMEjxTkhRmzbsrUxHpVhGO8QCOVbNFSZAgZjd5W6cBL.mZGeSYlOS', 45, 0, '0000-00-00 00:00:00', '2025-09-10 
09:38:15', 0, 'ZT6WWF7ULBF4P26X'),
(282, 'CA-bil-Nikoline', 'Nikoline', 'Hansen', 'nikoline.hansen@external.ca-autobank.com', 26613978, 
'$2y$10$VH1gASPpHQCkOX8NZ/0/e.Jsrn2mkyFa8oaRpWnZA9ca2vKqqwHi6', 45, 0, '0000-00-00 00:00:00', '2025-09-10 
09:39:33', -1, '4TBZWLXW2PS3FL2V'),
(284, 'dkg-Max', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$8yb.ZoDC80rbj4jLTABmW.nvXN7ANA5GIWVYfUnfPRej.ywRxgGF6', 33, 0, '0000-00-00 00:00:00', '2025-11-18 
08:53:13', 0, 'EBXTW5IERYY5F7SE'),
(285, 'CA-bil-Sandra', 'Sandra', 'Berg', 'sandra.berg@external.ca-autobank.com', 22316573, 
'$2y$10$R0xvmyiMpxjm8chwFX7tWujdVGFMWvCl6V1i/kxXRTnW0LuIn0HBi', 45, 0, '0000-00-00 00:00:00', '2025-11-18 
08:58:31', -1, 'UBJZ4G72KOVGSZ5D'),
(286, 'Driv-Sandra', 'Sandra', 'Berg', 'sandra.berg@external.ca-autobank.com', 22316573, 
'$2y$10$FbJyYOp//niRKFwEtUcXE.4a9RIibyly/fv0RjdoSdOZX86MtoExa', 46, 0, '2025-12-22 13:15:39', '2025-11-18 
09:00:21', 0, 'JPMNQLJPGURQR6EP'),
(287, 'Mithiof-DKG-jonas', 'jonas', 'roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$zitpshrs4VAtQZucyYwHL.8vGCOaDvMADqIWlmYCB6iosB5dLz7yG', 10, 0, '2025-12-02 09:26:57', '2025-11-19 
13:07:21', 0, '76Q62NBMVUXQXSUZ'),
(288, 'vvs-dkg-Jonas', 'jonas', 'roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$TpkiF4rnpRoTzHwaVUI7ceqdejWcvKE33jTuidTZePsHL0pJHB8S.', 152, 0, '0000-00-00 00:00:00', '2025-11-19 
13:09:13', 0, 'EHLALYTKUMMKPGKZ'),
(289, 'VM-Joachim2026', 'Joachim', 'Otto', 'joachim.otto@vanmossel.dk', 61632023, 
'$2y$10$yA..GvOHRLfYXAkcEC8.Juk1xNCj0g230tmNfhh/YA1w5H56QzBEW', 70, 0, '0000-00-00 00:00:00', '2026-03-11 
14:22:33', -1, '2YZRL2SOR5FCFPQ2'),
(290, 'LX-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$6boe3EVZhLU6KPrlihnjx.HefzyCuDRyo.wPY6gLoyy8S.csmv8aW', 80, 0, '0000-00-00 00:00:00', '2026-03-30 
22:32:30', -1, '7JZXEMD2HUA4IGQE'),
(291, 'LXF-AnneK2', 'Anne Cecilie Duus ', 'Kühl ', 'anne@lxflexleasing.dk', 50984728, 
'$2y$10$CCz6o2wGpviPF9rlnnFtx.rxhM1gkRWj2WzI4OUKLQv7Vw8r4U40m', 80, 0, '2026-05-28 08:45:17', '2026-03-30 
22:47:58', 0, 'U7TGHZ4PIAOBRLFK'),
(292, 'LXF-JensC2', 'Jens', 'Christensen', 'jens@lxflexleasing.dk', 24672211, 
'$2y$10$IqwKkZ8MSNkMBRpG8.l7Pe6Ys5s1F/SCbYbm9NEuG8c.MH4xlFQNm', 80, 0, '2026-04-14 07:38:36', '2026-03-30 
22:48:37', 0, 'WUJ52MSETE7BB6GJ'),
(294, 'LXF-MikaelL2', 'Mikael', 'Larsen', 'mikael@lxflexleasing.dk', 29661151, 
'$2y$10$xm27N55Mfaae1KLfyabQi.p1McxNgV5Awp.RLf1g1JYyixM/HCEx2', 80, 0, '0000-00-00 00:00:00', '2026-03-30 
22:50:09', 0, '6IU6ZYRSRLK2KXXY'),
(295, 'LXF-NicolaiA2', 'Nicolai Wisborg', 'Andersen', 'nicolai@lxflexleasing.dk', 61221171, 
'$2y$10$KYjf0m.j2ALkTejqVh3Hx.PgwI0JDU0ZUTjlMoHaxxoRvcJqbQnae', 80, 0, '2026-04-14 07:38:31', '2026-03-30 
22:50:43', 0, 'OQ6LELDBPJ77XED5'),
(296, 'LXF-UffeW2', 'Uffe', 'Wichmann', 'uffe@xflexleasing.dk', 30702749, 
'$2y$10$3J6aubpsnllTwiJqrixtUufqwWPFOAA4QKufmG1oW7kx.kgVYdkM.', 80, 0, '2026-05-28 08:45:13', '2026-03-30 
22:51:29', 0, 'JYETBMZDOSCHTG7Q'),
(297, 'VM-Mette2026', 'Mette', 'Hoffmann', 'Mette.hoffmann@vanmossel.dk', 30655748, 
'$2y$10$m3B7Q53CPV5JsEVcgFpmn.kauIjEqNMNPwfqMugR3pEnYLi3pHRLa', 70, 0, '0000-00-00 00:00:00', '2026-04-10 
11:37:20', -1, 'CDRPYAESWKSDGR6T'),
(298, 'CA-bil-Maiken', 'Maiken', 'Jørgensen', 'maiken.jorgensen@ca-autobank.com', 51926623, 
'$2y$10$7J.ID1I6XL1nfj.87JaZWepSHywvjqJVrTQdg9V5lfPciV3z0QBl6', 45, 0, '2026-04-14 07:36:47', '2026-04-13 
09:28:27', 0, 'YJGY2QO7A36SNOZG'),
(299, 'SCB-T-DKG-LOGIN', 'Jonas', 'Roest', 'dkg@dkg-aps.dk', 22226860, 
'$2y$10$rQS8pBHJ72h7XVww0UMyVOR/zVMoRBpCGrtzYO61U.Z8UAizge9u.', 40, 0, '0000-00-00 00:00:00', '2026-04-16 
10:19:39', 0, 'TWNDCXJS4WRASBCD'),
(300, 'LXF-JulieB2', 'Julie', 'Bjerregaard', 'Julie@lxflexleasing.dk', 24225183, 
'$2y$10$RbrkQ9XG2H7G4n.AuMEylOHqjP7U.alpkv1AJvMw4DiMZHgudLVeC', 80, 0, '0000-00-00 00:00:00', '2026-07-27 
10:22:06', -1, 'KSIMGR72WFFMP73Y'),
(301, 'fridrikur', 'fridrikur', 'ellefsen', 'fridrikur@hotmail.com', 29609033, 
'$2a$12$Zy/Rn3iZocHALqO0.5.LKObLBH4rR/KWrnXTfE0eKdCkhcg1Vju8G', 0, 0, '0000-00-00 00:00:00', '', -1, '');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `brugere`
--
ALTER TABLE `brugere`
  ADD PRIMARY KEY (`brugerID`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `brugere`
--
ALTER TABLE `brugere`
  MODIFY `brugerID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

