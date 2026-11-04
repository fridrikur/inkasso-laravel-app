-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Vært: mysql219.curanet.dk
-- Genereringstid: 26. 01 2026 kl. 11:59:12
-- Serverversion: 8.0.44-35
-- PHP-version: 8.2.29

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
-- Struktur-dump for tabellen `breve`
--

CREATE TABLE `breve` (
  `id` int NOT NULL,
  `titel` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `brevpos` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emne` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tekst` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `beskyttet` tinyint NOT NULL,
  `brevtype` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `breve`
--

INSERT INTO `breve` (`id`, `titel`, `brevpos`, `emne`, `tekst`, `beskyttet`, `brevtype`) VALUES
(1, 'Rykker', '12', 'Gælden til {firmanavn}', '<p>På trods af tidligere påkravsskrivelser både fra 
kreditor og fra Dansk Konsulent Gruppe ApS, er gælden på kr. {restgaeld} endnu ikke 
betalt.<br><br>Indbetalingen imødeses derfor <strong>omgående</strong> til vor klientkonto i Sydbank - 
reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}<br><br><strong>Skulle det nævnte beløb være betalt 
inden for de sidste par dage skal der venligst ses bort fra denne skrivelse.</strong> </p><p><br 
data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><strong>Dansk Konsulent Gruppe 
ApS</strong><br>Opkrævningsafdelingen </p>', 0, 0),
(3, 'Påkrav blanco', '2', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
behandling - henvendelser om denne sag skal derfor indtil videre kun ske til Dansk Konsulent Gruppe 
ApS.\r\n<br><br>\r\n \r\n\r\n\r\n\r\n\r\nDet er blevet oplyst at kontrakten er opsagt til fuld indfrielse 
med kr. {statistik} på grund af manglende betalinger, hvilket dog kan undgås såfremt restancen kr. 
{restgaeld} bliver indbetalt omgående.<br><br>\r\n\r\nRestancen fremkommer således:\r\n<br><br>\r\nYdelse 
pr. 01.01.2024     kr.      2.000,00<br>Ydelse pr. 01.02.2024     kr.     2.000,00<br>Ydelse pr. 
01.03.2024     kr.      2.000,00<br>Inkassosalær                                       kr.     
{gebyr}\r\n<br><br>\r\nDu bedes derfor kontakte os OMGÅENDE således at der kan findes en løsning på denne 
sag så den ikke ender i fogedretten.<br><br>\r\nVælger du at indbetale restancebeløbet skal dette 
omgående ske til Sydbank - reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}\r\n<br><br>\r\nDin 
tilbagemelding afventes derfor hurtigst muligt på tlf. 22226862 eller 22226860 - spørg efter Per 
Mortensen eller Jonas Roest.\r\n<br><br>\r\n\r\nMed venlig hilsen\r\n<br>Dansk Konsulent Gruppe 
ApS\r\n<br> Opkrævnings- konsulentafdelingen', 0, 0),
(4, 'Påkrav købektr', '0', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
behandling - henvendelser om denne sag kan og skal derfor indtil videre kun ske til Dansk Konsulent 
Gruppe ApS. \r\nDa sagen er overgivet til Dansk Konsulent Gruppe ApS, kan {firmanavn} ikke længere 
kontaktes for betalingsaftaler.<br><br>\r\n\r\n<strong>Hjemtagelse af bilen</strong><br>\r\nDet er blevet 
oplyst at kontrakten er opsagt på grund af manglende betaling, hvorfor bilen omgående skal hjemtages til 
{firmanavn}. Du skal enten aflevere bilen selv, på et sted anvist af os, eller vi sørger for at den 
bliver afhentet omgående.<br><br>\r\n\r\n\r\n<strong>Mulighed for betaling</strong><br>\r\nDer kan være 
mulighed for, at du kan betale det du er bagud og kontrakten kan åbnes igen, men det kræver at du 
kontakter os omgående. Du skal dog betale både restancen og inkassosalæret.<br>\r\n\r\n<i>Til din 
orientering er restværdi af bilen på over kr. 100.001,00. Det betyder, at aftalen nu er pålagt 
inkassosalær (jf. bek nr. 719 af 03/06/2024) på kr. {gebyr}, som beregnes ud fra bilens 
restværdi.</i><br><br>\r\n\r\nRestancen på nuværende tidspunkt fremkommer således:<br>\r\n00-00-2000                  
kr. 1.000,00<br>\r\n00-00-2000                  kr. 1.000,00<br>\r\n00-00-2000                  kr. 
1.000,00<br>\r\n00-00-2000                  kr. 1.000,00<br>\r\nInkassosalær                  kr. 
{gebyr}<br>\r\n\r\n<strong>Total                      kr. {restgaeld}</strong><br><br>\r\n\r\nVælger du 
at indbetale restancebeløbet skal dette omgående ske til Sydbank - reg. nr. 6060 - konto 0001141611. Husk 
at angive reference nr. {sagsnr} <br>\r\n\r\n<i>Såfremt bilen ikke afleveres omgående eller der ikke sker 
betaling senest 10 dage fra dags dato, så kan sagen sendes til inddrivelse gennem fogedretten, jf. 
inkassolovens § 10.</i><br><br>\r\n\r\n<strong>Kontakt os nu</strong><br>\r\nDin tilbagemelding afventes 
hurtigst muligt:<br>\r\nCarl Erik Petersen på tlf. 4040 1499 eller Jonas Roest på tlf. 2222 6860 (oplys 
sagsnr. {sagsnr}).<br><br>\r\n\r\n\r\nMed venlig hilsen <br>\r\nDansk Konsulent Gruppe ApS 
<br>\r\nOpkrævnings- konsulentafdelingen\r\n', 0, 0),
(7, '08 Bil skal hjem (MAIL)', '48', 'Copy Paste til afsendelse af mail - Bil skal hjemtages', 
'{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS 
VENLIGST DET VEDHÆFTEDE BREV.</strong><br><br>Kontrakten er blevet opsagt på grund af 
misligholdelse.<br><br>\r\n{firmanavn} har sendt opsigelse af kontrakten til dig/jer, hvilken er blevet 
ignoreret. Derfor har {firmanavn} nu har overdraget sagsbehandlingen til DKG ApS.<br><br>\r\nEftersom 
restancen ikke er betalt, skal bilen OMGÅENDE afleveres eller afhentes.<br><br>\r\nKontakt mig derfor med 
det samme for nærmere aftale omkring dette.<br><br>\r\n\r\n<br></font><br><br><br>\r\n<br></font>', 0, 
0),
(8, 'Påkrav leasing', '1', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', '<p>I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
behandling - henvendelser om denne sag skal derfor indtil videre kun ske til Dansk Konsulent Gruppe ApS. 
<br><br>\r\n\r\n Det er blevet oplyst at kontrakten er opsagt på grund af manglende betalinger, hvilket 
dog kan undgås såfremt restancen kr. {restgaeld} bliver indbetalt omgående.\r\n<br><br>\r\n Restancen 
fremkommer således: <br><br>\r\n\r\n Ydelse pr. 01.01.2024    kr.   2.000,00<br>Ydelse pr. 01.02.2024    
kr.   2.000,00<br>Ydelse pr. 01.03.2024    kr.   2.000,00<br>Inkassosalær                  kr.   {gebyr} 
<br><br>\r\n\r\n Du bedes derfor kontakte os OMGÅENDE således at der kan findes en løsning på denne sag - 
enten ved din omgående betaling af restancebeløbet eller aflevering af det finansierede 
aktiv.\r\n</p><p>Vælger du at indbetale restancebeløbet skal dette omgående ske til Sydbank - reg. nr. 
6060 - konto 0001141611 - ref. nr. {sagsnr} <br><br>\r\n\r\n Din tilbagemelding afventes derfor hurtigst 
muligt på tlf. 22226862 eller 22226860 - spørg efter Per Mortensen eller Jonas Roest. <br><br>\r\n\r\n 
Med venlig hilsen <br>\r\nDansk Konsulent Gruppe ApS <br>\r\n Opkrævnings- konsulentafdelingen</p>', 0, 
0),
(52, 'Besøg', '11', 'Din gæld til {firmanavn}.', '<p>Du har endnu ikke betalt din gæld til {firmanavn} 
som seneste er opgjort til i alt <strong>kr. {restgaeld}<br><br></strong>Vi har derfor dags dato forsøgt 
at træffe dig på din adresse for en afklaring af sagen, men du var desværre ikke tilstede.<br><br>Kontakt 
os derfor omgående på tlf. 22226894 for nærmere aftale.<br><br>Betaling skal ske til Sydbank - reg. nr. 
6060 - konto 0001141611 - ref. nr. {sagsnr}.<br data-mce-bogus=\"1\"></p><p><br 
data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><strong>Dansk Konsulent Gruppe ApS</strong><strong><br 
data-mce-bogus=\"1\"></strong></p><p>Opkrævningsafdelingen<br data-mce-bogus=\"1\"></p><div 
id=\"mcepastebin\" style=\"left: -65535px; top: 196px; width: 10px; height: 10px; overflow: hidden; 
position: absolute; opacity: 0;\" contenteditable=\"true\" data-mce-bogus=\"all\"><p>Med venlig 
hilsen<br><strong>Dansk Konsulent Gruppe ApS</strong><br>Opkrævningsafdelingen </p></div>', 0, 0),
(11, 'Jonas uden bil', '6', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', '<p>Restancen er nu oppe på 
<strong>kr. {restgaeld}</strong>, hvilken endnu ikke er blevet betalt.<br><br>Undertegnede har dags dato 
forsøgt at træffe dig for en nærmere afklaring af dette, men du var desværre ikke 
tilstede.<br><br>Såfremt du har mulighed for at betale restancebeløbet og ikke ønsker at sagen skal 
fortsættes i fogedretten, <br>skal der omgående ske betaling til Sydbank reg. nr. 6060 - konto 0001141611 
ref. {sagsnr}.<br><br><br></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><span 
id=\"_mce_caret\" data-mce-bogus=\"true\"><strong>﻿Dansk Konsulent Gruppe ApS</strong><span 
id=\"_mce_caret\" data-mce-bogus=\"true\">﻿<br>Opkrævnings- konsulentafdelingen</span></span><br 
data-mce-bogus=\"1\"></p><p><br>Jonas Roest - tlf. 22226860<br data-mce-bogus=\"1\"></p><div 
id=\"mcepastebin\" style=\"left: -65535px; top: 349.99px; width: 10px; height: 10px; overflow: hidden; 
position: absolute; opacity: 0;\" contenteditable=\"true\" data-mce-bogus=\"all\">Med venlig 
hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- konsulentafdelingen</div>', 0, 0),
(12, 'Jonas', '5', '{ktr} med {firmanavn} <p>Vedr. {aktiv}', '<p>Restancen er nu oppe på <strong>kr. 
{restgaeld}</strong>, hvilken endnu ikke er blevet betalt.<br><br>Undertegnede har dags dato forsøgt at 
træffe dig for nærmere afklaring af dette, men du var desværre ikke tilstede.<br><br>Såfremt du har 
mulighed for at betale restancebeløbet og ønsker, at beholde bilen, skal der omgående ske betaling til 
vor klient konto i Sydbank: <br>Reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}.<br><br>Såfremt 
bilen skal afleveres er der vedlagt tilbagetagelsesdokument som skal underskrives og returneres til min 
mailadresse, <br> som pdf-fil eller til vor postadresse.<br><br>Registreringsattesten skal følge 
bilen.<br><br><br data-mce-bogus=\"1\"></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig 
hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- konsulentafdelingen</p><p>Jonas 
Roest - tlf. 22226860<br data-mce-bogus=\"1\"></p>', 0, 0),
(54, '07 Fakturainkasso', '46', 'Din gæld til {firmanavn} <p>Vedr. {aktiv} - faktura nr. {fakturanr}', 
'<p>Du er tidligere blevet mindet om din gæld til ovennævnte, men har endnu ikke betalt den. Kreditor har 
nu mistet tålmodigheden og ønsker en snarlig afklaring af sagen, hvorfor Dansk Konsulent Gruppe ApS er 
anmodet om bistand angående den manglende betaling.<br><br>Gælden incl. rykkergebyrer og renter er oplyst 
til kr. {hovedstol} hvortil skal lægges et inkassosalær på kr. {gebyr}</p><p> </p><p><strong>Gældsposten 
udgør således i alt kr. </strong><strong>{restgaeld}<br></strong><strong><br></strong><br>Såfremt beløbet 
ikke modtages inden 10 dage fra dags dato, forbeholder vi os ret til, uden yderligere varsel, at 
overdrage sagen til retten, hvorved der påføres dig yderligere omkostninger.<br><br>Den nemmeste måde for 
alle parter at løse denne sag på, vil derfor være, at du indbetaler det skyldige beløb til vor 
klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}.<br><br>Ved samtlige 
betalinger eller henvendelser omkring denne sag, skal der anføres navn, adresse samt 
journalnummer.<br><br>Skulle der være spørgsmål til sagen, kan vi dagligt træffes på tlf. 22226860 inden 
for normal åbningstid.<br><br>Ved samtlige indbetalinger eller henvendelser i denne sag, skal der anføres 
navn, adresse og journalnummer. </p><p><br data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><strong>Dansk 
Konsulent Gruppe ApS</strong><br>Opkrævningsafdelingen</p>', 0, 0),
(17, 'Fremsendelse', '15', 'Deres gæld til {firmanavn}', '<p> <br></p>', 0, 0),
(18, 'Per', '7', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', '<p>Restancen er nu oppe på <strong>kr. 
{restgaeld}</strong>, hvilken endnu ikke er blevet betalt.</p><p>Undertegnede har dags dato forsøgt at 
træffe dig for nærmere afklaring af dette, men du/I var desværre ikke tilstede.<br><br>Såfremt du/I har 
mulighed for at betale <strong>kr. {restgaeld}</strong> og ønsker at beholde bilen, skal der omgående ske 
betaling til vor klient konto i Sydbank: Reg. nr. 6060 - konto 0001141611 - ref. nr. 
{sagsnr}.<br><br>Såfremt bilen skal afleveres er der vedlagt tilbagetagelsesdokument som skal 
underskrives og returneres til min mailadresse som pdf-fil eller til vor 
postadresse.<br><br>Registreringsattesten skal følge bilen.</p>\r\n<br><br>\r\n<p><br 
data-mce-bogus=\"1\"></p><p>Med venlig hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> 
Opkrævnings- konsulentafdelingen</p><p>Per Mortensen<br>Tlf. 22226862<br>Mail: <a 
href=\"mailto:per@dkg-aps.dk\">per@dkg-aps.dk</a><br data-mce-bogus=\"1\"></p>', 0, 0),
(19, 'Påkrav - restance', '4', '{ktr} med {firmanavn} <p>Vedr. {aktiv}', '<p>I forbindelse med opsigelsen 
af ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
behandling -<strong> henvendelser om denne sag skal derfor indtil videre kun ske til Dansk Konsulent 
Gruppe ApS.<br><br></strong>Det er blevet oplyst, at din kontrakt er i restance med <strong>kr. 
{restgaeld}</strong> hvilket beløb for længst er forfalden til betaling - restancebeløbet består af 
manglende ydelser samt inkassosalær.<br><br>Du bedes derfor kontakte os omgående, således at der kan 
findes en løsning på sagen - enten ved din omgående betaling af restancebeløbet eller aflevering af det 
finansierede aktiv.<br><br>Er der tale om en finansiering på en købekontrakt, er der vedlagt en 
salgsfuldmagt som i så fald skal underskrives og returneres til vor mail som pdf-fil eller 
postadresse.<br><br>Bilens registreringsattest skal følge bilen.<br><br>Vælger du at indbetale 
restancebeløbet <strong>kr. {restgaeld}</strong> skal dette omgående ske til følgende konto i Sydbank - 
reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}.<br><br>Din tilbagemelding afventes derfor hurtigst 
muligt på tlf. 22226860 eller 22226862 - spørg efter Jonas Roest eller Per Mortensen.<br 
data-mce-bogus=\"1\"></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><strong>Dansk Konsulent 
Gruppe ApS</strong><br data-mce-bogus=\"1\"></p><p>Opkrævnings- konsulentafdelingen<br 
data-mce-bogus=\"1\"></p><div id=\"mcepastebin\" style=\"left: -65535px; top: 415.99px; width: 10px; 
height: 10px; overflow: hidden; position: absolute; opacity: 0;\" contenteditable=\"true\" 
data-mce-bogus=\"all\">Med venlig hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- 
konsulentafdelingen</div>', 0, 0),
(20, 'Kvittering', '14', '{ktr} med {firmanavn}<p>Vedr. {aktiv}.', '<p>For modtagelsen af ovennævnte bil 
kvitteres herved<br data-mce-bogus=\"1\"></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig 
hilsen<br><strong>Dansk Konsulent Gruppe ApS</strong><br data-mce-bogus=\"1\"></p><p>Opkrævnings- 
konsulentafdelingen<br data-mce-bogus=\"1\"></p><p></p><div id=\"mcepastebin\" style=\"left: -65535px; 
top: 64px; width: 10px; height: 10px; overflow: hidden; position: absolute; opacity: 0;\" 
contenteditable=\"true\" data-mce-bogus=\"all\">Med venlig hilsen<strong><br>Dansk Konsulent Gruppe 
ApS</strong><br> Opkrævnings- konsulentafdelingen</div>', 0, 0),
(50, 'Udl.leasing', '3', '{ktr} med {firmanavn} <p>Vedr. {aktiv}.', '<p>I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent<br>Gruppe ApS for videre 
behandling - henvendelser om denne sag skal derfor indtil videre kun ske til Dansk<br>Konsulent Gruppe 
ApS.<br><br>Kontrakten er udløbet og enten skulle den have været indfriet eller bilen 
afleveret.<br><br>Vælger du at beholde bilen, skal følgende beløb indbetales med det 
samme:<br>\r\n<br>Restværdi incl. renter                          kr.  {hovedstol}\r\n<br>Inkassosalær                                         
kr.   {gebyr}\r\n<br>\r\n<br><strong>I alt til betaling                                  kr.  
{restgaeld}</strong><br><br>Betaling skal ske til Sydbank - reg. nr. 6060 - konto 0001141611 - ref. nr. 
{sagsnr}.<br><br>Skal bilen afleveres, bedes du med det samme tage kontakt til vores kontor på tlf.  
22226860 (Jonas Roest)<br>eller 22226862 (Per Mortensen) for nærmere aftale.<br><br>Afhentning vil i så 
fald ske meget hurtigt.<br><br>Vi skal høre fra dig hurtigst muligt.<br></p><p><br 
data-mce-bogus=\"1\"></p><p>Med venlig hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> 
Opkrævnings- konsulentafdelingen</p>', 0, 0),
(57, 'SF Erhverv', '10', 'Fuldmagt smed {navn} <p>Vedr. {aktiv}.', '<div style=\"top: -170px; 
font-family: Times New Roman, Georgia, Serif; position: relative;\">\r\n        <p style=\"text-align: 
center;\" data-mce-style=\"text-align: center;\"><strong>SALGSFULDMAGT</strong><br>\r\n        
Erhverv</p>\r\n        <br><br><p><br></p><p>Kontrakt nr.: {sagsnr}\r\n        <br><br>Undertegnede 
{navn}, der i henhold til købekontrakt med {firmanavn} et beløb skyldigt, stort kr.  {statistik} i 
forbindelse med finansiering af køb af:\r\n        </p>\r\n        <table style=\"border: currentColor; 
border-image: none;\"><tbody><tr><td>Bilmærke</td><td style=\"border: 0px solid red; border-image: none; 
width: 150px;\"></td><td>{aktiv}</td></tr><tr><td>Stelnummer:</td><td style=\"border: 0px solid red; 
border-image: none; width: 150px;\"></td><td>{stelnr}</td></tr><tr><td>Kørt km:</td>\r\n        <td 
style=\"border: 0px solid red; border-image: none; width: 
150px;\"></td><td></td></tr></tbody></table><br>mod ejendomsforbehold i førnævnte bil, erklærer herved på 
tro og love:</p>\r\n        <div class=\"clearfix\"></div>\r\n        <div style=\"display: flex;\r\n        
flex-direction: row;\r\n        gap: 1px;\r\n        align-items: baseline;\">\r\n        <div 
style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n        <div style=\"width:67.333333%;\r\n        
display: flex;\">jeg/vi frivilligt har afleveret den købte bil til {firmanavn}, og jeg/vi giver\r\n        
samtidig {firmanavn} fuldmagt til at sælge bilen bedst muligt, eventuelt på auktion, således at 
salgsprovenuet anvendes til nedbringelse af {firmanavn} krav i henhold til købekontrakten,</div>\r\n        
</div>\r\n        <div style=\"display: flex;\r\n        flex-direction: row;\r\n        gap: 1px;\r\n        
align-items: baseline;\">\r\n        <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div><div 
style=\"width:67.333333%;\r\n        display: flex;\">jeg/vi således ikke længere har råd til at have 
bilen og således ej anskaffer anden bil i\r\n        forbindelse med nærværende,</div>\r\n        
</div>\r\n        <div style=\"display: flex;\r\n        \r\n        flex-direction: row;\r\n        gap: 
1px;\r\n        align-items: baseline;\">\r\n        <div style=\"padding-left: 6em;width: 
16.66666667%;\">at</div><div style=\"width:67.333333%;\r\n        display: flex;\">bilen på 
afleveringstidspunktet indeholder det samme udstyr m.v. som på købstidspunktet,\r\n        og at der 
således ikke er fjernet udstyr fra bilen,</div>\r\n        </div>\r\n        <div style=\"display: 
flex;\r\n        \r\n        flex-direction: row;\r\n        gap: 1px;\r\n        align-items: 
baseline;\">\r\n        <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div><div 
style=\"width:67.333333%;\r\n        display: flex\">der ikke er foretaget udlæg, pantsætning eller 
lignende af bilen, og</div>\r\n        </div>\r\n        <div style=\"display: flex;\r\n        \r\n        
flex-direction: row;\r\n        gap: 1px;\r\n        align-items: baseline;\">\r\n        <div 
style=\"padding-left: 6em;width: 16.66666667%;\">at</div><div style=\"width:67.333333%;\r\n        
display: flex;\">{firmanavn} på mine/vore vegne kan rekvirere en ny registreringsattest,\r\n        
såfremt den originale er bortkommet.</div></div>\r\n        <div style=\"display: flex;\r\n        \r\n        
flex-direction: row;\r\n        gap: 1px;\r\n        align-items: baseline;\">\r\n        <div 
style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n        <div class=\"pagebreak\"> </div>\r\n        
<div style=\"width:67.333333%;\r\n        display: flex;\">private ejendele ved aflevering/afhentning af 
køretøjet er fjernet. {firmanavn} bærer\r\n        ikke ansvaret for eventuelle efterladte effekter, og 
kan ikke gøres erstatningsan-svarlig herfor.</div></div><br>\r\n        \r\n        <p>Den udenretlige 
aflevering af bilen er ikke medvirkende til at dette sker til fuld og endelig afgørelse af 
mellemværendet, idet {firmanavn} jf. Kreditaftalelovens § 51, stk. 3 kan gøre krav på en evt. 
restfordring efter realisation af bilen.</p>\r\n        <p>Tilbagetagelse og salg sker under forudsætning 
af evt. udlægshavers godkendelse.<br><br></p>\r\n        \r\n        <div style=\"width:67.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;gap:30px;flex-direction: row;align-items: 
baseline;\">\r\n                    <div>\r\n                Sted  og dato</div>\r\n                <div>  
......................................................<br>{navn}</div><p></p>\r\n                
</div></div>', 0, 0),
(58, 'SF Købektr.', '8', 'Fuldmagt med {navn} <p>Vedr. {aktiv}.', '<div style=\"top: -200px; font-family: 
Times New Roman, Georgia, Serif;position: relative;\">\r\n        <p style=\"text-align: center;\" 
data-mce-style=\"text-align: center;\"><strong>SALGSFULDMAGT</strong><br>\r\n                
Forbrugerforhold</p><div style=\"font-family: Times New Roman, Georgia, Serif;\">\r\n                
<br><p></p><p>Kontrakt nr.: {sagsnr}\r\n                <br><br>Undertegnede {navn}, der i henhold til 
købekontrakt med {firmanavn} er et beløb skyldigt, stort kr.  {statistik} i forbindelse med finansiering 
af køb af:\r\n                </p>\r\n                <table style=\"border: currentColor; border-image: 
none;\"><tbody><tr><td>Bilmærke</td><td style=\"border: 0px solid red; border-image: none; width: 
150px;\"></td><td>{aktiv}</td></tr><tr><td>Stelnummer:</td><td style=\"border: 0px solid red; 
border-image: none; width: 150px;\"></td><td>{stelnr}</td></tr><tr><td>Kørt km:</td>\r\n                
<td style=\"border: 0px solid red; border-image: none; width: 
150px;\"></td><td></td></tr></tbody></table><br>mod ejendomsforbehold i førnævnte bil, erklærer herved på 
tro og love:<br><p></p>\r\n                <div class=\"clearfix\"></div>\r\n                <div 
style=\"display: flex;\r\n                    \r\n                    flex-direction: row;\r\n                    
gap: 1px;\r\n                    align-items: baseline;\">\r\n                <div style=\"padding-left: 
6em;width: 16.66666667%\">at</div>\r\n                <div style=\"width:85.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;\">jeg/vi frivilligt har afleveret den købte bil til 
{firmanavn}, og jeg/vi giver samtidig {firmanavn} fuldmagt til at sælge bilen bedst muligt, eventuelt på 
auktion, således at salgsprovenuet anvendes til nedbringelse af {firmanavn} krav i henhold til 
købekontrakten, </div>\r\n                </div>\r\n                <div style=\"display: flex;\r\n                    
\r\n                    flex-direction: row;\r\n                    gap: 1px;\r\n                    
align-items: baseline;\">\r\n                <div style=\"padding-left: 6em;width: 
16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                display: 
flex;\r\n                margin-bottom: 1em;\">jeg/vi således ikke længere har råd til at have bilen og 
således ej anskaffer anden bil i forbindelse med nærværende, </div>\r\n                </div>\r\n                
<div style=\"display: flex;\r\n                    \r\n                    flex-direction: row;\r\n                    
gap: 1px;\r\n                    align-items: baseline;\">\r\n                <div style=\"padding-left: 
6em;width: 16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;\">bilen på afleveringstidspunktet indeholder det 
samme udstyr m.v. som på købstidspunktet, og at der således ikke er fjernet udstyr fra bilen, </div>\r\n                
</div>\r\n                <div style=\"display: flex;\r\n                    \r\n                    
flex-direction: row;\r\n                    gap: 1px;\r\n                    align-items: 
baseline;\">\r\n                <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n                
<div style=\"width:67.333333%;\r\n                display: flex;\r\n                margin-bottom: 
1em;\">der ikke er foretaget udlæg, pantsætning eller lignende af bilen, og</div>\r\n                
</div>\r\n                <div style=\"display: flex;\r\n                    \r\n                    
flex-direction: row;\r\n                    gap: 1px;\r\n                    align-items: 
baseline;\">\r\n                <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n                
<div style=\"width:67.333333%;\r\n                display: flex;\r\n                margin-bottom: 
1em;\"> {firmanavn} på mine/vore vegne kan rekvirere en ny registreringsattest, såfremt den originale er 
bortkommet. </div>\r\n                </div>\r\n                <div style=\"display: flex;\r\n                    
\r\n                    flex-direction: row;\r\n                    gap: 1px;\r\n                    
align-items: baseline;\">\r\n                <div style=\"padding-left: 6em;width: 
16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                display: 
flex;\r\n                margin-bottom: 1em;\">private ejendele ved aflevering/afhentning af køretøjet er 
fjernet. {firmanavn}\r\n                bærer ikke ansvaret for eventuelle efterladte effekter, og kan 
ikke gøres erstatningsansvarlig herfor.<br></div>\r\n                </div>\r\n                
<p>Forudsat at ovennævnte oplysninger er korrekte og det købte på afleveringstidspunktet ikke er \r\n                
undergået en upåregnelig værdiforringelse sker afleveringen af bilen til fuld og endelig \r\n                
afgørelse af mellemværendet. </p>\r\n                <p>Tilbagetagelse og salg sker under forudsætning af 
evt. udlægshavers godkendelse.<br><br>\r\n                <div style=\"width:67.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;gap:30px;flex-direction: row;align-items: 
baseline;\">\r\n                    <div>\r\n                Sted  og dato</div>\r\n                <div>  
......................................................<br>{navn}</div><p></p>\r\n                
</div></div>', 0, 0),
(59, 'GDPR', '13', 'INFORMATION OM PERSONOPLYSNINGER', '<p>I forbindelse med, at vi behandler en 
inkassosag for {firmanavn}, indsamler og behandler vi løbende personoplysninger om dig.\r\n</p><p>\r\nDet 
samme vil gøre sig gældende under eventuelle retslige skridt, der må udspringe af 
inkassosagen.\r\n</p><p>\r\nDKG ApS er dataansvarlig for behandlingen af disse personoplysninger som vi 
modtager og/eller indsamler om dig, og vi henviser til ovenstående brevhoved, hvoraf vores 
kontaktoplysninger fremgår. Derudover henviser vi til vores hjemme www.dkg-aps.dk. 
\r\n</p><p>\r\n<strong>Hvilke oplysninger har vi fået, og hvor har vi disse 
fra?</strong>\r\n</p><p>\r\n{firmanavn} som er din kreditor har videresendt os oplysninger omkring dit 
navn, adresse og gældsforholdet. Derudover kan vi – såfremt dette skønnes nødvendigt – modtage oplysning 
om dit CPR-nummer, telefonnummer, e-mailadresse, økonomiske, arbejdsmæssige, sociale og/eller 
helbredsmæssige forhold, såfremt disse er relevante for sagen og såfremt {firmanavn} er bekendt med disse 
forhold. \r\n</p><p>\r\nVi vil tilsvarende modtage og/eller indsamle oplysninger fra andre, der har 
kendskab til disse forhold, f. eks din advokat og eller andre som repræsenterer dig i nærværende sag. 
\r\n</p><p>\r\nVi vil derudover foretage skridt til at indsamle oplysninger om dine økonomiske og 
personlige forhold fra offentlige kilder, herunder sociale medier på internettet, databaser mv., ligesom 
at du selv har mulighed for at give os personoplysninger skriftligt, pr. telefon og eller under 
eventuelle retsmøder, hvilke oplysninger vil blive registreret, behandlet og anvendt i nærværende sag. 
Det er frivilligt om du ønsker at afgive disse informationer om dig selv. \r\n</p><p>\r\nVi vil løbende 
holde os ajour med din adresse og dit navn fra offentligt tilgængelige databaser. 
\r\n</p><p>\r\n<strong>Hvad skal vi bruge disse oplysninger til?</strong>\r\n</p><p>\r\nVi skal bruge 
dine personoplysninger til at indkræve {firmanavn} tilgodehavende hos dig, og om nødvendigt vil vi 
foretage de i retsplejelovens foreskrevne tvangsinddrivelsesskridt, herunder reglerne om udlæg, 
tvangsauktion mv. \r\n</p><p>\r\nVi indhenter alene oplysninger omkring dit CPR-nummer og almindelige 
stamdata som navn og adresse for at kunne lokalisere og identificere dig sikkert.</p>\r\n<p>Under sagen 
kan der blive videregivet personoplysninger om dig til domstolene, kreditor, låsesmede, skat, din 
udlejer, kreditoplysningsbureauer mv. \r\n</p><p>\r\nVi har pligt til at foretage en afvejning af dine og 
{firmanavn}  interesser, når der sker behandling og indsamling af personoplysninger, hvilken interesse vi 
skønner der foreligger, idet det er i {firmanavn} interesse – din kreditor – at opnå betaling af sit 
tilgodehavende, hvorfor vi ikke behøver at indhente dit samtykke til behandling af personoplysninger med 
dette formål. \r\n</p><p><strong>\r\nOpbevaring, dine rettigheder og din 
klageadgang.</strong>\r\n</p><p>I hovedreglen vil dine oplysninger blive opbevaret i 5 år efter at en 
inkassosag er endeligt afsluttet, men dette afhænger bl.a. af følgende forhold, herunder om gælden er 
endeligt indfriet, om der er uenigheder om kravets størrelse, sagsbehandlingen og eller reglerne om 
forældelse. \r\n</p><p>I visse tilfælde har du ret til at få begrænset den behandling vi foretager af 
dine personoplysninger, således at vi fremover kun må og kan behandle dine personoplysninger, med dit 
samtykke, og/eller med henblik på at vi kan forfølge retskrav. Dette gælder ikke opbevaring af dine 
personoplysninger. </p><p>\r\nDu har ret til at få indsigt i de oplysninger vi har og behandler om dig. 
Derudover har du ret til at få berigtiget urigtige oplysninger, ligesom at du har ret til at få slettet 
oplysninger inden udgangen af 5 års perioden som anført ovenfor, hvis de oplysninger der ønskes slettet 
ingen betydning har for vores behandling af sagen. \r\n</p><p>Reglerne om dataportabilitet finder ikke 
anvendelse i nærværende sag, da betingelserne herfor ikke er opfyldt, herunder særligt henset til, at 
vores behandling af dine personoplysninger ikke er baseret på dit samtykke.</p><p>   \r\nDu kan klage 
over vores behandling af dine persondata til os og eller til datatilsynet, hvor du kan læse mere om 
behandlingen af persondata på www.datatilsynet.dk.</p>', 0, 0),
(88, 'SCB-Forbrugslån påkrav', '24', 'Din gæld til Santander Consumer Bank <p>Vedr. ubetalte ydelser på 
forbrugslån', 'I forbindelse med opsigelsen af ovennævnte kontrakt, er sagen efterfølgende blevet 
overgivet til Dansk Konsulent Gruppe ApS for videre behandling - henvendelser om denne sag skal derfor 
indtil videre kun ske til Dansk Konsulent Gruppe ApS.\r\n<br><br>\r\n \r\nDet er blevet oplyst at 
kontrakten er opsagt til fuld indfrielse med kr. {statistik} på grund af manglende betalinger, hvilket 
dog kan undgås såfremt restancen kr. {restgaeld} bliver indbetalt omgående.<br><br>\r\n<p>\r\nRestancen 
fremkommer således:\r\n<br><br>\r\nUbetalte ydelser inkl 1/12-2020:    kr. 
{hovedstol}<br>\r\nInkassosalær:                                                    kr. 
{gebyr}<br><br><br>\r\n\r\nDu bedes derfor kontakte os OMGÅENDE således at der kan findes en løsning på 
denne sag så den ikke ender i fogedretten.<br><br>\r\nVælger du at indbetale restancebeløbet skal dette 
omgående ske til Sydbank - reg. nr. 6060 - konto 0001995195 - ref. nr. {sagsnr}\r\n<br><br>\r\nDin 
tilbagemelding afventes derfor hurtigst muligt på tlf. 22226862 eller 22226860 - spørg efter Per 
Mortensen eller Jonas Roest.\r\n<br><br>\r\n\r\nMed venlig hilsen\r\n<br>Dansk Konsulent Gruppe 
ApS\r\n<br> Opkrævnings- konsulentafdelingen', 0, 0),
(90, '01 Købekontrakt (MAIL)', '35', 'Copy-paste til afsendelse af mail m. bil', 
'{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS 
VENLIGST DET VEDHÆFTEDE BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld} 
</strong>, som stammer fra manglende ydelser samt inkassosalær.<br><br>Aftalen er blevet opsagt, og 
{firmanavn} vil have ajourbetaling omgående for aftalen kan fortsættes, alternativt vil de sættes i 
besiddelse af bilen inden for de nærmeste dage.<br><br>Meddel venligst at restancen vil blive betalt med 
det samme i henhold til betalingsinformationen i påkravsskrivelsen, eller om bilen skal 
hjemtages.<br><br>Betaling skal ske til vores Klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611 - 
ref. {sagsnr}<br></font><br><br><br>\r\n<br></font>', 0, 0),
(92, 'Varsel om besøg', '51', '<h2>Varsel om besøg</h2>', '<b>Din gæld til Santander Consumer Bank </b> 
<br><b>vedr. {ktr}, sagsnr. {sagsnr} </b><br><br>\r\n\r\nI henhold til inkassolovens § 12 varsles du 
hermed om besøg på din adresse.<br><br>\r\n<b>Besøg er planlagt til dato ______________, mellem kl. 
08-19.</b> <br><br>\r\n<i>Vi skal i den forbindelse oplyse dig om, at du kan kontakte os på telefon 22 22 
68 94 og vi har kontor på Thorsvej 8, 4100 Ringsted</i>.<br><br>\r\nSåfremt vi ikke træffer dig, vil der 
ske uanmeldte besøg på din adresse.<br><br>\r\n\r\nRestancen er fortsat <b>kr. {restgaeld}</b>, som vi 
beder dig om at få betalt omgående. <br><br>\r\n\r\nIndbetaling af restancebeløbet skal ske til Sydbank - 
reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}<br><br>\r\n \r\nUnder alle omstændigheder bedes du 
kontakte os OMGÅENDE således at der kan findes en løsning på denne sag, så den ikke ender i fogedretten. 
<br><br>\r\n\r\nDin tilbagemelding afventes derfor hurtigst muligt på telefon 22 22 68 
94.<br><br><br><br>\r\n\r\nMed venlig hilsen\r\n<br><b>Dansk Konsulent Gruppe ApS</b>\r\n<br> 
Opkrævnings- konsulentafdelingen', 0, 0),
(93, 'Besøg Forbrugslån', '20', '<h2>Din gæld til Santander Consumer Bank<p>Vedr. Forbrugslån, nr. 
{sagsnr}</h2>', '<p>Restancen er stadig <strong>kr. {restgaeld}</strong>, som endnu ikke er blevet 
betalt.<br><br>\r\n\r\nVi har dags dato forsøgt at træffe dig for en nærmere afklaring af dette, men du 
var ikke tilstede.<br><br>\r\n\r\nSåfremt du har mulighed for at betale restancebeløbet og ikke ønsker at 
sagen skal fortsættes i fogedretten, skal der omgående ske betaling til Sydbank reg. nr. 6820 - konto 
0001995195. Husk at angive reference nr. {sagsnr}.<br><br>\r\n\r\nUnder alle omstændigheder bedes du 
kontakte os OMGÅENDE således at der kan findes en løsning på denne sag, så den ikke ender i 
fogedretten.<br><br>\r\n\r\nJonas Roest	tlf. 2222 6860	<br>\r\nPer Mortensen	tlf. 2222 
6862<br>\r\nCarl Erik Petersen	tlf. 4040 1499<br><br><br>\r\n\r\n\r\n</p><p><br 
data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><span id=\"_mce_caret\" 
data-mce-bogus=\"true\"><strong>﻿Dansk Konsulent Gruppe ApS</strong><span id=\"_mce_caret\" 
data-mce-bogus=\"true\">﻿<br>Opkrævnings- konsulentafdelingen</span></span><br 
data-mce-bogus=\"1\"></p><p><br><br data-mce-bogus=\"1\"></p><div id=\"mcepastebin\" style=\"left: 
-65535px; top: 349.99px; width: 1{statistik}px; height: 1{statistik}px; overflow: hidden; position: 
absolute; opacity: {statistik};\" contenteditable=\"true\" data-mce-bogus=\"all\">Med venlig 
hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- konsulentafdelingen</div>', 0, 0),
(94, 'test-fuldmagt-brev', '21', 'test-fuldmagt-brev', '<p>I forbindelse med opsigelsen af ovennævnte 
kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent\r\n        <br>Gruppe ApS for 
videre behandling - henvendelser om denne sag skal derfor indtil videre kun ske til Dansk\r\n        
<br>Konsulent Gruppe ApS.\r\n        <br><br>Kontrakten er udløbet og enten skulle den have været 
indfriet eller bilen afleveret.\r\n        <br><br>Vælger du at beholde bilen, skal følgende beløb 
indbetales med det samme:<br>\r\n        <table border=\"0\">\r\n            <tr><td>Restværdi incl. 
renter</td>\r\n                <td>kr. {hovedstol}</td>\r\n                </tr>\r\n                
<tr><td>DKG/Opsigelse</td>\r\n                <td>kr.  {gebyr}</td></tr>\r\n                <tr><td>I alt 
til betaling</td><td>kr. {restgaeld}</td></tr>\r\n        </table>\r\n        <br>                        
\r\n        <br><br>Betaling skal ske til Sydbank - reg. nr. 6060 - konto 0001141611 - ref. nr. 
{sagsnr}.\r\n        <br><br>Skal bilen afleveres, bedes du med det samme tage kontakt til vores kontor 
på tlf. 22226860 (Jonas Roest)\r\n        <br>eller 22226862 (Per Mortensen) for nærmere aftale.\r\n        
<br><br>Afhentning vil i så fald ske meget hurtigt.\r\n        <br><br>Vi skal høre fra dig hurtigst 
muligt.<br></p><p><br></p>\r\n        <p>Med venlig hilsen<strong><br>Dansk Konsulent Gruppe 
ApS</strong>\r\n        <br>Opkrævnings- konsulentafdelingen</p>', 0, 0),
(96, '05 Privatleasing, udløbet (MAIL)', '43', 'Copy-paste til afsendelse af mail - Påkrav udløbet 
leasing', '{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> 
<strong>LÆS VENLIGST DET VEDHÆFTEDE BREV.</strong><br><br>Kontrakten er blevet opsagt på grund af 
misligholdelse.<br><br>\r\n{firmanavn} har sendt opsigelse af kontrakten til dig/jer, hvilken er blevet 
ignoreret. Derfor har {firmanavn} nu har overdraget sagsbehandlingen til DKG ApS.<br><br>\r\nEftersom 
restancen ikke er betalt, skal bilen OMGÅENDE afleveres eller afhentes.<br><br>\r\nKontakt mig derfor med 
det samme for nærmere aftale omkring dette.<br><br>\r\n\r\n<br></font><br><br><br>\r\n<br></font>', 0, 
0),
(95, 'html-test', '22', 'fuldmagt', '<p>I forbindelse med opsigelsen af ovennævnte kontrakt, er sagen 
efterfølgende blevet overgivet til Dansk Konsulent\r\n        <br>Gruppe ApS for videre behandling - 
henvendelser om denne sag skal derfor indtil videre kun ske til Dansk\r\n        <br>Konsulent Gruppe 
ApS.\r\n        <br><br>Kontrakten er udløbet og enten skulle den have været indfriet eller bilen 
afleveret.\r\n        <br><br>Vælger du at beholde bilen, skal følgende beløb indbetales med det 
samme:<br>\r\n        <br>Restværdi incl. renter                        kr. {hovedstol}\r\n        
<br>DKG/Opsigelse                              kr.  {gebyr}\r\n        <br><br><strong>I alt til betaling                                 
kr. {restgaeld}</strong>\r\n        <br><br>Betaling skal ske til Sydbank - reg. nr. 6060 - konto 
0001141611 - ref. nr. {sagsnr}.\r\n        <br><br>Skal bilen afleveres, bedes du med det samme tage 
kontakt til vores kontor på tlf. 22226860 (Jonas Roest)\r\n        <br>eller 22226862 (Per Mortensen) for 
nærmere aftale.\r\n        <br><br>Afhentning vil i så fald ske meget hurtigt.\r\n        <br><br>Vi skal 
høre fra dig hurtigst muligt.<br></p><p><br></p>\r\n        <p>Med venlig hilsen<strong><br>Dansk 
Konsulent Gruppe ApS</strong>\r\n        <br>Opkrævnings- konsulentafdelingen</p>', 0, 0),
(98, 'Betalingsaftale', '16', 'Betalingsaftale', '{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - 
{aktiv} - {firmanavn}<br><br> \r\n\r\nHej ...<br><br>\r\nHermed opfølgende mail på vores 
aftale.<br><br>\r\n•	Du betaler omgående, kr. xxx.<br>\r\n•	Du betaler til den x/X-2025, kr. 
xxx.<br>\r\n•	Du betaler til den x/X-2025, kr. xxx.<br>\r\n•	Du betaler til den x/X-2025, kr. 
xxx.<br><br>\r\nI ovenstående beløb er afdrag for MÅNED ..... også medregnet.<br><br>\r\nBetaling skal 
ske til vores Klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611 - ref. {sagsnr}<br><br>\r\nHvis vi 
modtager betaling som aftalt, så vil kontrakten med {firmanavn} fortsætte efter den sidste 
betaling.<br><br>\r\nHvis du måtte have nogen spørgsmål, så ring eller skriv til mig.<br><br>\r\nBekræft 
venligst.<br><br>\r\n', 0, 0),
(101, 'CEP', '17', '{ktr} med {firmanavn} <p>Vedr. {aktiv}', '<p>Restancen er nu oppe på <strong>kr. 
{restgaeld}</strong>, hvilken endnu ikke er blevet betalt.<br><br>Undertegnede har dags dato forsøgt at 
træffe dig for nærmere afklaring af dette, men du var desværre ikke tilstede.<br><br>Såfremt du har 
mulighed for at betale restancebeløbet og ønsker, at beholde bilen, skal der omgående ske betaling til 
vor klient konto i Sydbank: Reg. nr. 6060 - konto 0001141611 - ref. nr. {sagsnr}.<br><br>Såfremt bilen 
skal afleveres er der vedlagt tilbagetagelsesdokument som skal underskrives og returneres til min 
mailadresse som pdf-fil eller til vor postadresse.<br><br>Registreringsattesten skal følge 
bilen.<br><br><br data-mce-bogus=\"1\"></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig 
hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- konsulentafdelingen</p><p>Carl 
Erik Petersen - tlf. 40401499<br data-mce-bogus=\"1\"></p>', 0, 0),
(103, 'CEP u. bil', '18', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', '<p>Restancen er nu oppe på 
<strong>kr. {restgaeld}</strong>, hvilken endnu ikke er blevet betalt.<br><br>Undertegnede har dags dato 
forsøgt at træffe dig for en nærmere afklaring af dette, men du var desværre ikke 
tilstede.<br><br>Såfremt du har mulighed for at betale restancebeløbet og ikke ønsker at sagen skal 
fortsættes i fogedretten, skal der omgående ske betaling til Sydbank reg. nr. 6060 - konto 0001141611 
ref. {sagsnr}.<br><br><br></p><p><br data-mce-bogus=\"1\"></p><p>Med venlig hilsen<br><span 
id=\"_mce_caret\" data-mce-bogus=\"true\"><strong>﻿Dansk Konsulent Gruppe ApS</strong><span 
id=\"_mce_caret\" data-mce-bogus=\"true\">﻿<br>Opkrævnings- konsulentafdelingen</span></span><br 
data-mce-bogus=\"1\"></p><p><br>Carl Erik Petersen - tlf. 40401499<br data-mce-bogus=\"1\"></p><div 
id=\"mcepastebin\" style=\"left: -65535px; top: 349.99px; width: 10px; height: 10px; overflow: hidden; 
position: absolute; opacity: 0;\" contenteditable=\"true\" data-mce-bogus=\"all\">Med venlig 
hilsen<strong><br>Dansk Konsulent Gruppe ApS</strong><br> Opkrævnings- konsulentafdelingen</div>', 0, 0),
(104, 'Mail vedr. besøg', '19', 'Mail vedr. besøg', 'Hej Per / Jonas / Carl 
Erik<br>\r\n\r\n\r\n<br><br><font face=\"Arial\">BESØG - {sagsnr} - {aktiv} - {firmanavn}<br><br> \r\nVil 
du/I aflægge KD besøg på adressen i ......, for nærmere afklaring inden sagen sendes videre til Fogedret 
mhp. udlæg i ejendom.<br><br>\r\n//<br><br>\r\n\r\nVil du/I aflægge KD besøg på adressen i ....., for 
nærmere afklaring inden sagen sendes videre til FR for hjemtagelse af bilen.\r\n\r\n<br><br>\r\nLige nu 
er restancen kr. {restgaeld} – KD vender ikke tilbage.\r\n<br><br>\r\nBetaling skal ske til Sydbank - 
reg. nr. 6060 - konto 0001141611 - ref. {sagsnr}\r\n<br><br>\r\nHvis du får fat i KD – og KD har mulighed 
for betaling – så skal det være straksoverførsel eller kontant afregning.\r\n<br><br>\r\nDine yderligere 
omkostninger må du pålægge ovenstående restance.\r\n<br><br>\r\nAlle data er i DB og KD er kopi på denne 
mail.', 0, 0),
(107, '02 Købekontrakt, udløbet (MAIL)', '37', 'Copy Paste til afsendelse af mail - Lånet er udløbet', 
'{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS 
VENLIGST DET VEDHÆFTEDE BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld} 
</strong>, som på nuværende tidspunkt er restgæld samt inkassosalær.<br><br>Aftalen er blevet opsagt, og 
{firmanavn} vil have betaling omgående.  Alternativt vil de sættes i besiddelse af bilen inden for de 
nærmeste dage.<br><br>Meddel venligst at der vil blive betalt med det samme i henhold til 
betalingsinformationen i påkravsskrivelsen, eller om bilen skal hjemtages.<br><br>Betaling skal ske til 
vores Klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611 - ref. 
{sagsnr}<br></font><br><br><br>\r\n<br></font>', 0, 0),
(108, 'Sag opgives', '26', 'Sag opgives', 'Sag opgives - {sagsnr} - {navn} <br><br>\r\n\r\nVi opgiver 
denne sag. <br><br>\r\n\r\nDet har ikke været muligt at få dialog med KD.<br>\r\n\r\nKD har oplyst at der 
ikke er betalingsevne og....<br><br>\r\nKD har tidligere betalt kr. x.xxx,xx direkte til DKG, som bliver 
overført til jer.<br><br>\r\n\r\nKD ejer stadig bilen {aktiv}  //  Bilen er solgt<br>\r\nDerudover ejer 
KD ejendom, som der kan tages udlæg i.<br><br>\r\n\r\nVi afslutter sagen her.', 0, 0),
(109, 'SMS-JR', '27', 'SMS', 'Vedr. {firmanavn} / {sagsnr} / {aktiv} - Restancen er nu på kr. 
{restgaeld}, Betaling til Sydbank, reg nr. 6060 konto 0001141611, ref. {sagsnr}. Afventer din omgående 
betaling. Med venlig hilsen DKG APS, Jonas, tlf. 22226860\r\n', 0, 0),
(106, 'Til mail - Grønafgift', '25', 'Til mail - Grønafgift', '{email}\r\n<br><br><font 
face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS VENLIGST DET VEDHÆFTEDE 
BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld} </strong>, som vedrører betaling 
af grønafgift samt inkassosalær.<br><br>{firmanavn} Santander Consumer Bank vil have omgående betaling, 
alternativt vil sagen blive fortsat i retten.<br><br>Meddel venligst at restancen vil blive betalt med 
det samme i henhold til betalingsinformationen i påkravsskrivelsen.<br><br>Betaling skal ske til Sydbank 
- reg. nr. 6060 - konto 0001141611 - ref. {sagsnr}<br></font><br><br></font>', 0, 0),
(111, 'SMS-CEP', '28', 'SMS-CEP', 'Vedr. {firmanavn}  / {sagsnr} / {aktiv} - Restancen er nu på kr. 
{restgaeld}, Betaling til Sydbank, reg nr. 6060 konto 0001141611, ref. {sagsnr}. Afventer din omgående 
betaling. Med venlig hilsen DKG APS, Carl Erik, tlf. 40401499\r\n', 0, 0),
(112, 'RYK 1', '29', 'RYK 1', '{email}<br><br>\r\n\r\nHej {navn}<br><br>\r\n\r\n<font 
face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn}<br><br> \r\n\r\nRestancen er nu oplyst til kr. 
{restgaeld}<br><br>\r\n\r\nBetalingen skal ske til Sydbank: reg nr. 6060 – konto nr. 0001141611 – oplys 
sags nr. {sagsnr}<br><br>\r\n\r\nVi har stadig ikke hørt noget, vil du vende tilbage 
hurtigt.<br><br>\r\n\r\nAlternativt bliver sagen overdraget til vores konsulenter, og du vil blive 
kontaktet på din adresse for en afklaring.\r\n\r\n', 0, 0),
(113, 'Afregning', '30', 'Afregning', 'Ajour betalt - kontraktnummer: {sagsnr}\r\n', 0, 0),
(118, 'Afhentning af bil', '32', 'Afhentning af bil', 'transport@dssmail.dk<br><br>\r\nEMNE : Afhentning 
af bil på vegne af {firmanavn} - Sjælland/FYN/Jylland (post nr)\r\n<br><br>\r\nHej Marc<br><br>\r\nVil I 
afhente nedenstående. DIN TEKST<br><br>\r\n{aktiv}\r\nSTEL NR: {stelnr}<br><br>\r\nAfhentningsadressen 
er: xxx<br><br>\r\nI kan aftale nærmere med NAVN på telefon nr. TELEFON<br><br>\r\nBilen skal til: 
FDM/Bilsalg<br><br>\r\nI kører for Santander - og sagen har aftale nr. {sagsnr}<br><br>\r\nVil I give 
besked, når den er afhentet.\r\n\r\n', 0, 0),
(116, 'SMS-CHR', '31', 'SMS-CHR', 'Vi har forgæves forsøgt at få fat i dig omkring manglende betaling af 
billån til {firmanavn} vedr. {aktiv}.\r\nManglende betaling er nu oplyst til kr. {restgaeld}. Beløb er 
forfalden til omgående betaling. \r\nBetaling skal ske til Sydbank reg nr.  6060 - konto nr. 0001141611 - 
oplys sags nr. {sagsnr}.\r\nVil du ringe os op, så vi kan finde en løsning mht. om du kan betale, eller 
ønsker sagen fortsættes via fogedretten. \r\nOgså sendt pr mail d.d. \r\nMVH DKG APS /Christina tlf 
22226892\r\n\r\n', 0, 0),
(117, '07 Fakturainkasso (MAIL)', '47', 'Mail/faktura', '{email}\r\n<br><br><font face=\"Arial\">{sagsnr} 
- {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS VENLIGST DET VEDHÆFTEDE 
BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld}</strong>, som stammer fra 
manglende betaling af Regning og inkassosalær.<br><br>\r\n\r\n{firmanavn} vil have betaling omgående, 
alternativt vil sagen blive fortsat i fogedretten.<br><br>\r\n\r\nMeddel venligst at dette vil blive 
betalt med det samme i henhold til betalingsinformationen i påkravsskrivelsen.<br><br>Betaling skal ske 
til Sydbank - reg. nr. 6060 - konto 0001141611 - ref. {sagsnr}<br></font><br><br></font>', 0, 0);
INSERT INTO `breve` (`id`, `titel`, `brevpos`, `emne`, `tekst`, `beskyttet`, `brevtype`) VALUES
(120, 'SF Købektr. Politi', '9', 'Fuldmagt med {navn} <p>Vedr. {aktiv}.', '<div style=\"top: -200px; 
font-family: Times New Roman, Georgia, Serif;position: relative;\">\r\n        <p style=\"text-align: 
center;\" data-mce-style=\"text-align: center;\"><strong>SALGSFULDMAGT</strong><br>\r\n                
Forbrugerforhold</p><div style=\"font-family: Times New Roman, Georgia, Serif;\">\r\n                
<br><p></p><p>Kontrakt nr.: {sagsnr}\r\n                <br><br>Undertegnede {navn}, der i henhold til 
købekontrakt med {firmanavn} er et beløb skyldigt, stort kr.  {statistik} i forbindelse med finansiering 
af køb af:\r\n                </p>\r\n                <table style=\"border: currentColor; border-image: 
none;\"><tbody><tr><td>Bilmærke</td><td style=\"border: 0px solid red; border-image: none; width: 
150px;\"></td><td>{aktiv}</td></tr><tr><td>Stelnummer:</td><td style=\"border: 0px solid red; 
border-image: none; width: 150px;\"></td><td>{stelnr}</td></tr><tr><td>Kørt km:</td>\r\n                
<td style=\"border: 0px solid red; border-image: none; width: 
150px;\"></td><td></td></tr></tbody></table><br>mod ejendomsforbehold i førnævnte bil, erklærer herved på 
tro og love:<br><p></p>\r\n                <div class=\"clearfix\"></div>\r\n                <div 
style=\"display: flex;\r\n                    \r\n                    flex-direction: row;\r\n                    
gap: 1px;\r\n                    align-items: baseline;\">\r\n                <div style=\"padding-left: 
6em;width: 16.66666667%\">at</div>\r\n                <div style=\"width:85.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;\">jeg/vi frivilligt har afleveret den købte bil til 
{firmanavn}, og jeg/vi giver samtidig {firmanavn} fuldmagt til at sælge bilen bedst muligt, eventuelt på 
auktion, således at salgsprovenuet anvendes til nedbringelse af {firmanavn} krav i henhold til 
købekontrakten, </div>\r\n                </div>\r\n                <div style=\"display: flex;\r\n                    
\r\n                    flex-direction: row;\r\n                    gap: 1px;\r\n                    
align-items: baseline;\">\r\n                <div style=\"padding-left: 6em;width: 
16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                display: 
flex;\r\n                margin-bottom: 1em;\">jeg/vi således ikke længere har råd til at have bilen og 
således ej anskaffer anden bil i forbindelse med nærværende, </div>\r\n                </div>\r\n                
<div style=\"display: flex;\r\n                    \r\n                    flex-direction: row;\r\n                    
gap: 1px;\r\n                    align-items: baseline;\">\r\n                <div style=\"padding-left: 
6em;width: 16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;\">bilen på afleveringstidspunktet indeholder det 
samme udstyr m.v. som på købstidspunktet, og at der således ikke er fjernet udstyr fra bilen, </div>\r\n                
</div>\r\n                <div style=\"display: flex;\r\n                    \r\n                    
flex-direction: row;\r\n                    gap: 1px;\r\n                    align-items: 
baseline;\">\r\n                <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n                
<div style=\"width:67.333333%;\r\n                display: flex;\r\n                margin-bottom: 
1em;\">der ikke er foretaget udlæg, pantsætning eller lignende af bilen, og</div>\r\n                
</div>\r\n                <div style=\"display: flex;\r\n                    \r\n                    
flex-direction: row;\r\n                    gap: 1px;\r\n                    align-items: 
baseline;\">\r\n                <div style=\"padding-left: 6em;width: 16.66666667%;\">at</div>\r\n                
<div style=\"width:67.333333%;\r\n                display: flex;\r\n                margin-bottom: 
1em;\"> {firmanavn} på mine/vore vegne kan rekvirere en ny registreringsattest, såfremt den originale er 
bortkommet. </div>\r\n                </div>\r\n                <div style=\"display: flex;\r\n                    
\r\n                    flex-direction: row;\r\n                    gap: 1px;\r\n                    
align-items: baseline;\">\r\n                <div style=\"padding-left: 6em;width: 
16.66666667%;\">at</div>\r\n                <div style=\"width:67.333333%;\r\n                display: 
flex;\r\n                margin-bottom: 1em;\">private ejendele ved aflevering/afhentning af køretøjet er 
fjernet. {firmanavn}\r\n                bærer ikke ansvaret for eventuelle efterladte effekter, og kan 
ikke gøres erstatningsansvarlig herfor.<br></div>\r\n                </div>\r\n                
<p>Forudsat at ovennævnte oplysninger er korrekte og det købte på afleveringstidspunktet ikke er \r\n                
undergået en upåregnelig værdiforringelse sker afleveringen af bilen til fuld og endelig \r\n                
afgørelse af mellemværendet. </p>\r\n                <p>Tilbagetagelse og salg sker under forudsætning af 
evt. udlægshavers godkendelse.<br><br>\r\n                <div style=\"width:67.333333%;\r\n                
display: flex;\r\n                margin-bottom: 1em;gap:30px;flex-direction: row;align-items: 
baseline;\">\r\n                    <div>\r\n                Sted  og dato</div>\r\n                <div>  
......................................................<br>{navn}</div><p></p>\r\n                
</div></div>', 0, 0),
(121, 'skub - skub - skub - skub - skub - skub - skub - skub - skub', '23', 'test ', 'test', 0, 0),
(122, '03 Leasing', '38', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
inkassobehandling - henvendelser om denne sag kan og skal derfor indtil videre kun ske til Dansk 
Konsulent Gruppe ApS. \r\nDa sagen er overgivet til Dansk Konsulent Gruppe ApS, kan {firmanavn} ikke 
længere kontaktes for betalingsaftaler.<br><br>\r\n\r\n<strong>Hjemtagelse af bilen</strong><br>\r\nDet 
er blevet oplyst at kontrakten er opsagt på grund af manglende betaling, hvorfor bilen omgående skal 
hjemtages til {firmanavn}. Du skal enten aflevere bilen selv, på et sted anvist af os, eller vi sørger 
for at den bliver afhentet omgående.<br><br>\r\n\r\n\r\n<strong>Mulighed for betaling</strong><br>\r\nDer 
kan være mulighed for, at du kan betale det du er bagud og kontrakten kan åbnes igen, men det kræver at 
du kontakter os omgående. Du skal dog betale både restancen og inkassosalæret.<br>\r\n\r\n<i>Til din 
orientering er restværdi af bilen på over kr. 100.001,00. Det betyder, at aftalen nu er pålagt 
inkassosalær (jf. bek nr. 719 af 03/06/2024) på kr. {gebyr}, som beregnes ud fra bilens 
restværdi.</i><br><br>\r\n\r\nRestancen på nuværende tidspunkt fremkommer således:<br>\r\n00-00-2000                  
kr. 1.000,00<br>\r\n00-00-2000                  kr. 1.000,00<br>\r\n00-00-2000                  kr. 
1.000,00<br>\r\n00-00-2000                  kr. 1.000,00<br>\r\nInkassosalær                kr. 
{gebyr}<br>\r\n\r\n<strong>Total                            kr. 
{restgaeld}</strong><br><br>\r\n\r\nVælger du at indbetale restancebeløbet skal dette omgående ske til 
Sydbank - reg. nr. 6060 - konto 0001141611. Husk at angive reference nr. {sagsnr} <br>\r\n\r\n<i>Såfremt 
bilen ikke afleveres omgående eller der ikke sker betaling senest 10 dage fra dags dato, så kan sagen 
sendes til inddrivelse gennem fogedretten, jf. inkassolovens § 10.</i><br><br>\r\n\r\n<strong>Kontakt os 
nu</strong><br>\r\nDin tilbagemelding afventes hurtigst muligt:<br>\r\nCarl Erik Petersen på tlf. 4040 
1499 eller Jonas Roest på tlf. 2222 6860 (oplys sagsnr. {sagsnr}).<br><br>\r\n\r\n\r\nMed venlig hilsen 
<br>\r\nDansk Konsulent Gruppe ApS <br>\r\nOpkrævnings- konsulentafdelingen\r\n', 0, 0),
(123, 'FAKTURA', '52', 'Kopi til', '{firmanavn}<br>\r\nPark Allé 295<br>\r\n2605 
Brøndby<br><br><br><br>\r\n\r\n<b>FAKTURA 
2025-xxxx</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DATO:  
<br><br><br>\r\n\r\n<b>Sagsnr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\nNavn&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inkassosalær</b><br>\r\n{sagsnr}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n\r\n{navn}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n\r\n{gebyr}<br><br><br>\r\n\r\nMoms 
udgør kr. xxxxxxxx<br><br><br>\r\n\r\nFakturabeløb er modregnet i debitors 
indbetaling.<br><br>\r\n\r\n<b>Kr. {hovedstol} er d.d. overført til reg. xxxxx</b><br><br>\r\n\r\nSagen 
er hermed afsluttet.<br><br><br><br>\r\n\r\nMed venlig hilsen\r\nDansk Konsulent Gruppe ApS\r\n', 0, 0),
(133, '04 Leasing udløbet', '40', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med udløb af 
ovennævnte leasingkontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for 
videre inkassobehandling - henvendelser om denne sag kan og skal derfor indtil videre kun ske til Dansk 
Konsulent Gruppe ApS og Santander kan ikke længere kontaktes for betalingsaftaler. 
<br><br>\r\n\r\nLeasingkontrakten er udløbet og enten skulle den have været indfriet eller bilen 
afleveret.<br><br>\r\n\r\nVælger du at beholde bilen, skal følgende restbeløb 
indbetales:<br><br>\r\n\r\nRestværdi, incl. moms                kr. {hovedstol}<br>\r\nInkassosalær                               
kr. {gebyr}<br><br>\r\n\r\n<strong>I alt til betaling                        kr. 
{restgaeld}</strong><br><br>\r\n\r\nObs! Hvis der er tale om flexleasingkontrakt, så er ovenstående UDEN 
afgift.<br>\r\nObs! Der kan være særlige regler for indfrielse af leasingbiler, som du skal være 
opmærksom på. Fx at der kan være krav om tilsyn af bilen samt at betaling kommer fra forhandler/erhvervs 
konto. Derudover skal en evt. betaling af restværdi ajourføres med Kreditor inden betaling. Derudover 
tages der forbehold for eventuelle grønne afgifter mv.<br><br>\r\n\r\nSåfremt bilen ikke afleveres 
omgående eller der ikke sker betaling senest 10 dage fra dags dato, så kan sagen sendes til inddrivelse 
gennem fogedretten.<br><br>\r\n\r\nBetaling skal ske til vores Klientkonto i Sydbank - Reg. nr. 6060 - 
Konto 0001141611 - ref. nr. {sagsnr}<br><br>\r\n\r\nSkal bilen afleveres tilbage, bedes du med det samme 
tage kontakt til os for nærmere aftale:\r\nJonas Roest på tlf. 2222 6860 eller Carl Petersen på tlf. 4040 
1499.<br><br>\r\n\r\nVi skal høre fra dig hurtigst muligt.', 0, 0),
(124, '05 Privatleasing, udløbet', '42', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med 
opsigelse/udløb af ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent 
Gruppe ApS for videre inkassobehandling - henvendelser om denne sag kan og skal derfor indtil videre kun 
ske til Dansk Konsulent Gruppe ApS. \r\n<br><br>\r\n\r\n<strong>Hjemtagelse af bilen</strong><br>\r\nDet 
er blevet oplyst at kontrakten er udløbet og du ikke har afleveret bilen som du skulle. Bilen skal 
omgående hjemtages til {firmanavn}. Du skal enten aflevere bilen selv, på et sted anvist af os, eller vi 
sørger for at den bliver afhentet omgående.<br><br>\r\n\r\nNærmeste afleveringssted i forhold til din 
bopæl er:<br>\r\nAutorola (Bilsalg.dk), Industriparken 34, 2750 Ballerup   (Du skal give os besked så 
snart den er afleveret)<br><br>\r\n\r\n\r\n<strong>Undgå konsulentbesøg</strong><br>\r\nSåfremt bilen 
ikke afleveres omgående, så kan Kreditor om 10 dage fra dags dato, sende sagen til inddrivelse gennem 
fogedretten, jf. inkassolovens § 10.\r\nFør dette vil der dog blive varslet og foretaget konsulentbesøg 
på din bopæl.<br><br>\r\n\r\n<strong>Kontakt os nu</strong><br>\r\nDin tilbagemelding afventes hurtigst 
muligt:<br>\r\nCarl Erik Petersen på tlf. 4040 1499 eller Jonas Roest på tlf. 2222 6860 (oplys sagsnr. 
{sagsnr}).<br><br><br><br>\r\n\r\nMed venlig hilsen <br>\r\nDansk Konsulent Gruppe ApS 
<br>\r\nOpkrævnings- konsulentafdelingen', 0, 0),
(125, 'Bil afhentet', '33', 'Bil afhentet', 'EMNE: Bil hjemtaget - {sagsnr} - {aktiv}<br> <br> 
\r\n\r\nSagsnr:  {sagsnr}        <br>      \r\nBil:  {aktiv}<br>\r\nDebitor:    
{navn}<br><br><br>\r\n\r\nBilen er hjemtaget.<br><br>\r\n\r\nVedlagt er underskrevet 
salgsfuldmagt.<br><br>\r\nOBS!<br>\r\nUdlæg / kunde bortrejst etc.<br><br>\r\nVi afregner og 
afslutter.<br><br>\r\n', 0, 0),
(126, '01 Købekontrakt', '34', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med opsigelsen af 
ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe ApS for videre 
behandling - henvendelser om denne sag kan og skal derfor indtil videre kun ske til Dansk Konsulent 
Gruppe ApS. \r\nDa sagen er overgivet til Dansk Konsulent Gruppe ApS, kan {firmanavn} ikke længere 
kontaktes for betalingsaftaler.<br><br>\r\n\r\nLåneaftalen er opsagt og der er krav om fuld indfrielse på 
grund af manglende betaling: <br>\r\n\r\n<strong>Indfrielsesbeløb dags dato: kr. {statistik}</strong>, 
heraf udgør inkassosalær kr. {gebyr}<br><br>\r\n\r\n\r\nDer er dog mulighed for at låneaftalen kan blive 
åbnet igen, men det kræver, at du omgående betaler de afdrag \r\ndu er bagud samt det pålagte 
inkassosalær (Den samlede restance). <br><br>\r\n\r\nRestancen på nuværende tidspunkt fremkommer 
således:<br>\r\n00-00-2025                  kr. 1.000,00<br>\r\n00-00-2025                  kr. 
1.000,00<br>\r\n00-00-2025                  kr. 1.000,00<br>\r\n00-00-2025                  kr. 
1.000,00<br>\r\n00-00-2025                  kr. 1.000,00<br>\r\n00-00-2025                  kr. 
1.000,00<br>\r\nInkassosalær                kr. {gebyr}<br>\r\n\r\n<strong>Total                            
kr. {restgaeld}</strong><br><br>\r\n\r\n<i>Såfremt bilen ikke afleveres omgående eller der ikke sker 
betaling af restancen senest 10 dage fra dato, så kan \r\nsagen sendes til inddrivelse gennem 
fogedretten. </i><br><br>\r\n\r\nSkal det finansierede aktiv afleveres eller afhentes, fremsendes 
salgsfuldmagt via E-Signatur, som skal \r\nunderskrives med MitID. <br><br>\r\n\r\nVælger du at indbetale 
restancebeløbet eller indfri lånet skal dette omgående ske til vores Klientkonto i Sydbank - reg. nr. 
6060 - konto 0001141611. Husk at angive reference nr. {sagsnr} <br><br>\r\n\r\n<strong>Kontakt os 
nu</strong><br>\r\nDu bedes derfor kontakte os OMGÅENDE således, at der kan findes en løsning på denne 
sag. \r\nJonas Roest på tlf. 2222 6860 eller Carl Erik Petersen på tlf. 4040 1499 (oplys sagsnr. 
{sagsnr}).<br><br>\r\n\r\n\r\nMed venlig hilsen <br>\r\nDansk Konsulent Gruppe ApS <br>\r\nOpkrævnings- 
konsulentafdelingen\r\n', 0, 0),
(128, '02 Købekontrakt, udløbet', '36', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med 
opsigelsen og udløb af ovennævnte kontrakt, er sagen efterfølgende blevet overgivet til Dansk Konsulent 
Gruppe ApS for videre inkassobehandling - henvendelser om denne sag kan og skal derfor indtil videre kun 
ske til Dansk Konsulent Gruppe ApS. \r\nDa sagen er overgivet til Dansk Konsulent Gruppe ApS, kan 
{firmanavn} ikke længere kontaktes for betalingsaftaler.<br><br>\r\n\r\nLåneaftalen er udløbet og står 
til fuld indfrielse: <br>\r\n\r\n<strong>Indfrielsesbeløb dags dato: kr. {statistik}</strong>, heraf 
udgør inkassosalær kr. {gebyr}<br><br>\r\n\r\nVælger du at indbetale beløbet skal dette omgående ske til 
vores Klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611. Husk at angive reference nr. {sagsnr} 
<br><br>\r\n\r\n\r\n<i>Såfremt bilen ikke afleveres omgående eller der ikke sker betaling  senest 10 dage 
fra dato, så kan \r\nsagen sendes til inddrivelse gennem fogedretten. </i><br><br>\r\n\r\nSkal det 
finansierede aktiv afleveres eller afhentes, fremsendes salgsfuldmagt via E-Signatur, som skal 
\r\nunderskrives med MitID. <br><br>\r\n\r\n<strong>Kontakt os nu</strong><br>\r\nDu bedes derfor 
kontakte os OMGÅENDE således, at der kan findes en løsning på denne sag. \r\nJonas Roest på tlf. 2222 
6860 eller Carl Erik Petersen på tlf. 4040 1499 (oplys sagsnr. {sagsnr}).<br><br>\r\n\r\n\r\nMed venlig 
hilsen <br>\r\nDansk Konsulent Gruppe ApS <br>\r\nOpkrævnings- konsulentafdelingen\r\n', 0, 0),
(129, '03 Leasing (MAIL)', '39', 'Copy-paste til afsendelse af mail m. bil', '{email}\r\n<br><br><font 
face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS VENLIGST DET VEDHÆFTEDE 
BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld} </strong>, som stammer fra 
manglende ydelser samt inkassosalær.<br><br>Aftalen er blevet opsagt, og {firmanavn} vil have 
ajourbetaling omgående for aftalen kan fortsættes, alternativt vil de sættes i besiddelse af bilen inden 
for de nærmeste dage.<br><br>Meddel venligst at restancen vil blive betalt med det samme i henhold til 
betalingsinformationen i påkravsskrivelsen, eller om bilen skal hjemtages.<br><br>Betaling skal ske til 
vores Klientkonto i Sydbank - reg. nr. 6060 - konto 0001141611 - ref. 
{sagsnr}<br></font><br><br><br>\r\n<br></font>', 0, 0),
(131, '06 Blanco, fuldt krav', '44', '{ktr} med {firmanavn}<p>Vedr. {aktiv}', 'I forbindelse med 
opsigelsen af ovennævnte låneaftale, er sagen efterfølgende blevet overgivet til Dansk Konsulent Gruppe 
ApS for videre inkassobehandling - henvendelser om denne sag kan og skal derfor indtil videre kun ske til 
Dansk Konsulent Gruppe ApS og Santander kan ikke længere kontaktes for betalingsaftaler. 
<br><br>\r\n\r\nLåneaftalen er opsagt og der er krav om fuld indfrielse på grund af manglende 
betaling:<br><br>\r\n\r\nRestgæld dags dato         kr. {hovedstol}<br>\r\nInkassosalær                   
kr. {gebyr}	<br><br>\r\n\r\n<strong>Indfrielsesbeløb           kr. 
{restgaeld}<br><br></strong>\r\n\r\nIndbetalinger skal ske til vores klientkonto i Sydbank - reg. nr. 
6060 - konto 0001141611. <br>\r\nHusk at angive reference nr. {sagsnr}.<br><br>\r\n\r\n<strong>Mulighed 
for betaling af restancen</strong><br>\r\nDer er dog mulighed for, at låneaftalen kan blive åbnet igen, 
men det kræver, at du betaler de afdrag du er bagud samt det pålagte inkassosalær. Du skal kontakte os 
omgående, hvis dette skal være en mulighed.<br><br>\r\n\r\n<strong>Kontakt os nu</strong><br>\r\nDu bedes 
kontakte os OMGÅENDE således, at der kan findes en løsning på denne sag - enten ved din betaling af 
indfrielsesbeløb eller restancen. <br><br>\r\n\r\nDin tilbagemelding afventes derfor hurtigst muligt: 
Christina Bang-Petersen på tlf. 2222 6892 eller \r\nCarl Erik Petersen på tlf. 4040 1499 (oplys sagsnr. 
{sagsnr}).<br><br>\r\n\r\n\r\nSåfremt vi ikke modtager din betaling senest 10 dage fra dato, så kan sagen 
sendes til inddrivelse gennem fogedretten.\r\n', 0, 0),
(132, '06 Blanco, fuldt krav (MAIL)', '45', 'Copy paste til mail - blanco fuldt krav', 
'{email}\r\n<br><br><font face=\"Arial\">{sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS 
VENLIGST DET VEDHÆFTEDE BREV.</strong><br><br>Vedhæftet påkrav på i alt <strong>kr. {restgaeld}</strong>, 
som stammer fra manglende betaling samt inkassosalær.<br><br>Låneaftalen er blevet opsagt, og {firmanavn} 
vil have betaling omgående, alternativt vil sagen blive fortsat i fogedretten.<br><br>\r\n\r\nMeddel 
venligst at beløbet vil blive betalt med det samme i henhold til betalingsinformationen i 
påkravsskrivelsen.<br><br>Betaling skal ske til vores Klientkonto i Sydbank - reg. nr. 6060 - konto 
0001141611 - ref. {sagsnr}<br></font><br><br><br>\r\n<br></font>', 0, 0),
(134, '04 Leasing udløbet (MAIL)', '41', 'Copy paste til mail', '{email}\r\n<br><br><font 
face=\"Arial\">UDLØBET LEASING - {sagsnr} - {aktiv} - {firmanavn} - HASTER<br><br> <strong>LÆS VENLIGST 
DET VEDHÆFTEDE BREV.</strong><br><br>Leasingkontrakten er udløbet og der skal ske omgående betaling. 
<br><br>Alternativt så skal bilen retur til {firmanavn} inden for de næste par dage. <br><br>Vedhæftet 
påkrav på i alt <strong>kr. {restgaeld} </strong><br><br>Betaling skal ske vores Klientkonto i til 
Sydbank - reg. nr. 6060 - konto 0001141611 - ref. {sagsnr}<br><br>Kontakt os omgående for nærmere 
afklaring.</font><br><br>\r\n<br></font>', 0, 0);

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `breve`
--
ALTER TABLE `breve`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `titel` (`titel`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `breve`
--
ALTER TABLE `breve`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

