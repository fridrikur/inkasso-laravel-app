<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sagsbehandler;
use App\Models\Konsulenter;
use App\Models\Kreditorer;

class SagsbehandlerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ["sbID" => "14", "kreditorID" => "17", "sagsbehandler" => "Anette Skarnvad", "email" => "", "tlf" => "0"],
            ["sbID" => "17", "kreditorID" => "5", "sagsbehandler" => "Louise", "email" => "", "tlf" => "0"],
            ["sbID" => "20", "kreditorID" => "29", "sagsbehandler" => "Sussi Skov", "email" => "", "tlf" => "0"],
            ["sbID" => "22", "kreditorID" => "3", "sagsbehandler" => "Sanne", "email" => "", "tlf" => "0"],
            ["sbID" => "25", "kreditorID" => "33", "sagsbehandler" => "Diverse", "email" => "", "tlf" => "0"],
            ["sbID" => "26", "kreditorID" => "19", "sagsbehandler" => "Diverse", "email" => "", "tlf" => "0"],
            ["sbID" => "28", "kreditorID" => "7", "sagsbehandler" => "Claus Laugaard", "email" => "", "tlf" => "0"],
            ["sbID" => "29", "kreditorID" => "11", "sagsbehandler" => "Gitte Ingstrup", "email" => "", "tlf" => "0"],
            ["sbID" => "30", "kreditorID" => "13", "sagsbehandler" => "Diverse", "email" => "", "tlf" => "0"],
            ["sbID" => "41", "kreditorID" => "0", "sagsbehandler" => "Carl Erik", "email" => "dkg@dkg-aps.dk", "tlf" => "22226860"],
            ["sbID" => "42", "kreditorID" => "0", "sagsbehandler" => "Per", "email" => "per@dkg-aps.dk", "tlf" => "22226862"],
            ["sbID" => "69", "kreditorID" => "34", "sagsbehandler" => "abc", "email" => "", "tlf" => "0"],
            ["sbID" => "70", "kreditorID" => "34", "sagsbehandler" => "sss", "email" => "", "tlf" => "0"],
            ["sbID" => "79", "kreditorID" => "11", "sagsbehandler" => "Margit Günther", "email" => "", "tlf" => "0"],
            ["sbID" => "80", "kreditorID" => "11", "sagsbehandler" => "Mickey Madsen", "email" => "", "tlf" => "0"],
            ["sbID" => "82", "kreditorID" => "17", "sagsbehandler" => "Vibeke Nielsen", "email" => "", "tlf" => "0"],
            ["sbID" => "83", "kreditorID" => "17", "sagsbehandler" => "Anette Reuter", "email" => "", "tlf" => "0"],
            ["sbID" => "85", "kreditorID" => "15", "sagsbehandler" => "Lotte Jacobsen", "email" => "", "tlf" => "0"],
            ["sbID" => "93", "kreditorID" => "10", "sagsbehandler" => "Kim Mithiof", "email" => "kim@mithiof.dk", "tlf" => "0"],
            ["sbID" => "94", "kreditorID" => "0", "sagsbehandler" => "Majbrit", "email" => "", "tlf" => "0"],
            ["sbID" => "96", "kreditorID" => "17", "sagsbehandler" => "Tina Køhl", "email" => "", "tlf" => "0"],
            ["sbID" => "98", "kreditorID" => "7", "sagsbehandler" => "Dennis Hørlykke", "email" => "", "tlf" => "0"],
            ["sbID" => "99", "kreditorID" => "111", "sagsbehandler" => "Kis Mortensen", "email" => "", "tlf" => "0"],
            ["sbID" => "102", "kreditorID" => "31", "sagsbehandler" => "Maiken Lass", "email" => "", "tlf" => "0"],
            ["sbID" => "103", "kreditorID" => "14", "sagsbehandler" => "Anne", "email" => "", "tlf" => "0"],
            ["sbID" => "104", "kreditorID" => "23", "sagsbehandler" => "Morten Petersen", "email" => "", "tlf" => "0"],
            ["sbID" => "105", "kreditorID" => "1", "sagsbehandler" => "Anita Mikkelsen", "email" => "", "tlf" => "0"],
            ["sbID" => "106", "kreditorID" => "16", "sagsbehandler" => "Dorte Skau", "email" => "", "tlf" => "0"],
            ["sbID" => "107", "kreditorID" => "2", "sagsbehandler" => "Camilla Røhl", "email" => "", "tlf" => "0"],
            ["sbID" => "109", "kreditorID" => "25", "sagsbehandler" => "Jesper Waterval", "email" => "", "tlf" => "0"],
            ["sbID" => "113", "kreditorID" => "30", "sagsbehandler" => "Jane Jensen", "email" => "", "tlf" => "0"],
            ["sbID" => "114", "kreditorID" => "32", "sagsbehandler" => "Morten Ringius Christensen", "email" => "", "tlf" => "0"],
            ["sbID" => "116", "kreditorID" => "0", "sagsbehandler" => "Annette", "email" => "sager@dkg-aps.dk", "tlf" => "22226894"],
            ["sbID" => "117", "kreditorID" => "5", "sagsbehandler" => "Louise", "email" => "", "tlf" => "0"],
            ["sbID" => "118", "kreditorID" => "6", "sagsbehandler" => "Pernille", "email" => "", "tlf" => "0"],
            ["sbID" => "120", "kreditorID" => "29", "sagsbehandler" => "Janus Pedersen", "email" => "jap@salonsupport.dk", "tlf" => "36161609"],
            ["sbID" => "133", "kreditorID" => "4", "sagsbehandler" => "Diverse", "email" => "", "tlf" => "0"],
            ["sbID" => "136", "kreditorID" => "0", "sagsbehandler" => "Forskellige DKG", "email" => "", "tlf" => "0"],
            ["sbID" => "137", "kreditorID" => "20", "sagsbehandler" => "Ole Laursen", "email" => "", "tlf" => "0"],
            ["sbID" => "141", "kreditorID" => "0", "sagsbehandler" => "Jonas", "email" => "dkg@dkg-aps.dk", "tlf" => "22226894"],
            ["sbID" => "149", "kreditorID" => "110", "sagsbehandler" => "Anita", "email" => "abaimi@almbrand.dk", "tlf" => "0"],
            ["sbID" => "150", "kreditorID" => "9", "sagsbehandler" => "Diverse - dkg", "email" => "", "tlf" => "0"],
            ["sbID" => "151", "kreditorID" => "8", "sagsbehandler" => "Diverse - DKG", "email" => "", "tlf" => "0"],
            ["sbID" => "152", "kreditorID" => "100", "sagsbehandler" => "Jonas", "email" => "dkg@dkg-aps.dk", "tlf" => "22226860"],
            ["sbID" => "153", "kreditorID" => "112", "sagsbehandler" => "Carl Erik Petersen", "email" => "cep@dkg-aps.dk", "tlf" => "40401499"],
            ["sbID" => "154", "kreditorID" => "33", "sagsbehandler" => "Torben Jeppesen", "email" => "info@maxgarage.dk", "tlf" => "40116167"],
            ["sbID" => "385", "kreditorID" => "50", "sagsbehandler" => "Morehouse-konto", "email" => "dkg@dkg-aps.dk", "tlf" => "0"],
            ["sbID" => "386", "kreditorID" => "8", "sagsbehandler" => "Marianne B. Petersen", "email" => "", "tlf" => "0"],
            ["sbID" => "387", "kreditorID" => "151", "sagsbehandler" => "Lotte Jacobsen", "email" => "lotte@bay-rev.dk", "tlf" => "57"],
            ["sbID" => "388", "kreditorID" => "15", "sagsbehandler" => "Diverse", "email" => "", "tlf" => "0"],
            ["sbID" => "389", "kreditorID" => "0", "sagsbehandler" => "Christina", "email" => "christina@dkg-aps.dk", "tlf" => "22226892"],
            ["sbID" => "391", "kreditorID" => "25", "sagsbehandler" => "Claus Pihl", "email" => "clp@stubbe.dk", "tlf" => "46330414"],
            ["sbID" => "393", "kreditorID" => "152", "sagsbehandler" => "Pernille L. Højstrøm", "email" => "pernille@bjaeverskovvvs.dk", "tlf" => "61135250"],
            ["sbID" => "394", "kreditorID" => "35", "sagsbehandler" => "Hanne Christensen", "email" => "hlc@ondriveleasing.dk", "tlf" => "61866980"],
            ["sbID" => "397", "kreditorID" => "55", "sagsbehandler" => "Kundeservice", "email" => "kunederservice@dkg-aps.dk", "tlf" => "22226860"],
            ["sbID" => "400", "kreditorID" => "1", "sagsbehandler" => "Stephanie", "email" => "stl@opendo.dk", "tlf" => "0"],
            ["sbID" => "401", "kreditorID" => "110", "sagsbehandler" => "Stephanie", "email" => "stl@opendo.dk", "tlf" => "0"],
            ["sbID" => "402", "kreditorID" => "55", "sagsbehandler" => "Kasper Lemke", "email" => "kasper.lemke@justdrive.today", "tlf" => "61108803"],
            ["sbID" => "411", "kreditorID" => "46", "sagsbehandler" => "Kundeservice", "email" => "Servicedk@ca-autobank.com", "tlf" => "43228955"],
            ["sbID" => "412", "kreditorID" => "60", "sagsbehandler" => "Diverse", "email" => "info@wormglas.dk", "tlf" => "33314053"],
            ["sbID" => "413", "kreditorID" => "70", "sagsbehandler" => "Kundeservice", "email" => "anita.mikkelsen@vanmossel.dk", "tlf" => "0"],
            ["sbID" => "414", "kreditorID" => "45", "sagsbehandler" => "Kundeservice", "email" => "servicedk@ca-autobank.com", "tlf" => "43228990"],
            ["sbID" => "415", "kreditorID" => "80", "sagsbehandler" => "Kundeservice", "email" => "info@lxflexleasing.dk", "tlf" => "75222211"],
            ["sbID" => "416", "kreditorID" => "40", "sagsbehandler" => "Kundeservice", "email" => "", "tlf" => "0"],
        ];

        $emailCounts = [];
        $usedTlf = [];

        foreach ($data as $row) {
            if (empty($row['sagsbehandler']) || $row['sagsbehandler'] === 'Ukendt sagsbehandler') {
                continue;
            }

            // 1. Håndter E-mail med dublet-tæller
            $baseEmail = (!empty($row['email']) && $row['email'] !== '0') 
                ? strtolower($row['email']) 
                : strtolower(str_replace(' ', '', $row['sagsbehandler'])) . '@dkg-aps.dk';

            if (!isset($emailCounts[$baseEmail])) {
                $emailCounts[$baseEmail] = 0;
                $finalEmail = $baseEmail;
            } else {
                $emailCounts[$baseEmail]++;
                $parts = explode('@', $baseEmail);
                $finalEmail = $parts[0] . '(' . $emailCounts[$baseEmail] . ')@' . $parts[1];
            }

            // 2. Håndter Telefonnummer: Hvis det mangler, er '0' eller allerede brugt, generer et unikt tlf-nummer
            $rawTlf = (!empty($row['tlf']) && $row['tlf'] !== '0') ? $row['tlf'] : null;

            if (!$rawTlf || in_array($rawTlf, $usedTlf)) {
                // Generer et unikt tlf-nummer for at undgå duplicate entry-fejl i databasen
                do {
                    $tlf = '20' . rand(100000, 999999);
                } while (in_array($tlf, $usedTlf));
            } else {
                $tlf = $rawTlf;
            }

            $usedTlf[] = $tlf;
            $mobil = '50' . rand(100000, 999999);

            if ((string)$row['kreditorID'] === '0') {
                Konsulenter::updateOrCreate(
                    ['navn' => $row['sagsbehandler']],
                    ['email' => $finalEmail, 'tlf' => $tlf, 'mobil' => $mobil]
                );
            } else {
                $sagsbehandler = Sagsbehandler::updateOrCreate(
                    ['navn' => $row['sagsbehandler']], 
                    ['email' => $finalEmail, 'tlf' => $tlf, 'mobil' => $mobil]
                );

                $kreditor = Kreditorer::where('lotusID', (string)$row['kreditorID'])->first();
                if ($kreditor) {
                    $kreditor->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);
                }
            }
        }
    }
}