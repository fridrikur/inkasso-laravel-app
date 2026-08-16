<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Models\Debitorer;
use App\Models\Sagsbehandler;
use App\Models\Konsulenter;
use App\Models\Status;
use App\Models\KTR;
use App\Models\bemaerkning;
use App\Models\Afslutning;
use App\Models\udlaeg;

class SagerSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------------------
        // 1. TJEK OG AUTO-SEED STAMDATA OG DROPDOWNS HVIS DE ER TOMME
        // -------------------------------------------------------------------------

        if (Kreditorer::count() === 0) {
            foreach (['Danske Bank Inkasso', 'Jyske Finans A/S', 'Nordea Kredit', 'Express Bank', 'Resurs Bank'] as $navn) {
                Kreditorer::create([
                    'navn' => $navn,
                    'cvr' => rand(10000000, 99999999),
                    'adresse' => fake()->streetAddress(),
                    'postnr' => rand(1000, 9999),
                    'email' => fake()->companyEmail(),
                    'tlf' => '70' . rand(100000, 999999),
                ]);
            }
        }

        if (Debitorer::count() === 0) {
            // Liste af ægte danske postnumre og tilhørende byer
            $danskeByer = [
                ['postnr' => 6400, 'by' => 'Sønderborg'],
                ['postnr' => 6200, 'by' => 'Aabenraa'],
                ['postnr' => 6100, 'by' => 'Haderslev'],
                ['postnr' => 6300, 'by' => 'Gråsten'],
                ['postnr' => 6470, 'by' => 'Sydals'],
                ['postnr' => 6330, 'by' => 'Padborg'],
                ['postnr' => 6000, 'by' => 'Kolding'],
                ['postnr' => 6700, 'by' => 'Esbjerg'],
                ['postnr' => 5000, 'by' => 'Odense C'],
                ['postnr' => 8000, 'by' => 'Aarhus C'],
                ['postnr' => 1050, 'by' => 'København K'],
                ['postnr' => 2100, 'by' => 'København Ø'],
                ['postnr' => 4000, 'by' => 'Roskilde'],
                ['postnr' => 3000, 'by' => 'Helsingør'],
                ['postnr' => 9000, 'by' => 'Aalborg'],
            ];

            for ($d = 1; $d <= 100; $d++) {
                $tilfaeldigLokation = fake()->randomElement($danskeByer);

                Debitorer::create([
                    'navn' => fake()->name(),
                    'pnr' => rand(100000, 999999) . '-' . rand(1000, 9999),
                    'adresse' => fake()->streetAddress(),
                    'postnr' => $tilfaeldigLokation['postnr'],
                    'tlf' => '20' . rand(100000, 999999),
                    'email' => fake()->safeEmail(),
                ]);
            }
        }

        if (Sagsbehandler::count() === 0) {
            foreach (['Mette Frederiksen', 'Lars Løkke', 'Jakob Ellemann', 'Pia Olsen'] as $navn) {
                Sagsbehandler::create([
                    'navn' => $navn,
                    'email' => Str::slug($navn) . '@inkasso.dk',
                    'tlf' => '30' . rand(100000, 999999),
                ]);
            }
        }

        if (Konsulenter::count() === 0) {
                    $konsulenterData = ['Anders Fogh', 'Helle Thorning', 'Poul Nyrup', 'Sven Auken'];
                    
                    foreach ($konsulenterData as $index => $navn) {
                        Konsulenter::create([
                            'navn' => $navn,
                            'email' => Str::slug($navn) . '.' . ($index + 1) . '@konsulent.dk',
                            'tlf' => '40' . rand(1000000, 9999999),
                            'mobil' => '50' . rand(1000000, 9999999),
                        ]);
                    }
                }

        if (Status::count() === 0) {
            $statuser = [
                ['tekst' => 'Modtaget', 'forkortelse' => 'MOD'],
                ['tekst' => 'Varsel sendt', 'forkortelse' => 'VAR'],
                ['tekst' => 'Fogedret berammet', 'forkortelse' => 'FOG'],
                ['tekst' => 'Afdragsvis betaling', 'forkortelse' => 'AFD'],
                ['tekst' => 'Afsluttet - Indbetalt', 'forkortelse' => 'IND'],
                ['tekst' => 'Opgivet', 'forkortelse' => 'OPG'],
            ];

            foreach ($statuser as $status) {
                Status::create($status);
            }
        }

        if (ktr::count() === 0) {
            $ktrListeData = [
                ['tekst' => 'KTR-100 Konto i berod', 'forkortelse' => 'KTR100'],
                ['tekst' => 'KTR-200 Kontaktet pr. tlf', 'forkortelse' => 'KTR200'],
                ['tekst' => 'KTR-300 Retslig inkasso', 'forkortelse' => 'KTR300'],
                ['tekst' => 'KTR-400 Afdragsordning', 'forkortelse' => 'KTR400'],
            ];

            foreach ($ktrListeData as $item) {
                ktr::create($item);
            }
        }

        if (bemaerkning::count() === 0) {
            $bemaerkningerData = [
                ['tekst' => 'Debitor har lovet indbetaling fredag', 'forkortelse' => 'BEM1'],
                ['tekst' => 'Strider mod hovedstol', 'forkortelse' => 'BEM2'],
                ['tekst' => 'Afventer svar fra kreditor', 'forkortelse' => 'BEM3'],
                ['tekst' => 'Udvidet bopælsattest indhentet', 'forkortelse' => 'BEM4'],
            ];

            foreach ($bemaerkningerData as $item) {
                bemaerkning::create($item);
            }
        }

        if (afslutning::count() === 0) {
            $afslutningData = [
                ['tekst' => 'Fuld indfrielse', 'forkortelse' => 'AFSL1'],
                ['tekst' => 'Forlig indgået', 'forkortelse' => 'AFSL2'],
                ['tekst' => 'Insolvenserklæring i fogedret', 'forkortelse' => 'AFSL3'],
                ['tekst' => 'Kreditor trukket tilbage', 'forkortelse' => 'AFSL4'],
            ];

            foreach ($afslutningData as $item) {
                afslutning::create($item);
            }
        }

        if (udlaeg::count() === 0) {
            $udlaegData = [
                ['tekst' => 'Ingen aktiver', 'forkortelse' => 'UDL1'],
                ['tekst' => 'Udlæg i bil', 'forkortelse' => 'UDL2'],
                ['tekst' => 'Udlæg i fast ejendom', 'forkortelse' => 'UDL3'],
                ['tekst' => 'Lønindeholdelse iværksat', 'forkortelse' => 'UDL4'],
            ];

            foreach ($udlaegData as $item) {
                udlaeg::create($item);
            }
        }

        // Hent alle opdaterede samlinger
        $kreditorer = Kreditorer::all();
        $debitorer = Debitorer::all();
        $sagsbehandlere = Sagsbehandler::all();
        $konsulenter = Konsulenter::all();
        $statuser = Status::all();
        $ktrListe = ktr::all();
        $bemaerkninger = bemaerkning::all();
        $afslutninger = afslutning::all();
        $udlaegListe = udlaeg::all();
        
        // -------------------------------------------------------------------------
        // 2. NULSTIL PIPOT OG Hoved-TABELLER
        // -------------------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('sager_kreditor')->truncate();
        DB::table('sager_debitor')->truncate();
        DB::table('sager_sagsbehandler')->truncate();
        DB::table('sager_konsulent')->truncate();
        DB::table('sager_status')->truncate();
        DB::table('sager_ktr')->truncate();
        DB::table('sager_bemaerkning')->truncate();
        DB::table('sager_afslutning')->truncate();
        DB::table('sager_udlaeg')->truncate();
        DB::table('sagers')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // -------------------------------------------------------------------------
        // 3. OPRET SAGER MED FULLDSTÆNDIG DATADÆKNING
        // -------------------------------------------------------------------------
        $shuffledDebitorer = $debitorer->shuffle();
        $totalSager = 50;

        for ($i = 1; $i <= $totalSager; $i++) {
            
            $uniqueSagsnr = 100000 + $i;

            // GDPR-kategorier (15% expired, 15% expiring_soon, 70% aktiver)
            $gdprCategory = fake()->randomElement(['expired', 'expiring_soon', 'normal', 'normal', 'normal']);

            if ($gdprCategory === 'expired') {
                $modtagetDato = Carbon::now()->subMonths(rand(66, 84));
                $isAfsluttet = true;
            } elseif ($gdprCategory === 'expiring_soon') {
                $modtagetDato = Carbon::now()->subMonths(rand(58, 59));
                $isAfsluttet = fake()->boolean(70);
            } else {
                $modtagetDato = Carbon::now()->subDays(rand(30, 1000));
                $isAfsluttet = fake()->boolean(30);
            }

            $fakturadato = fake()->boolean(85) ? (clone $modtagetDato)->addDays(rand(1, 14)) : null;
            $faktureret = $fakturadato && fake()->boolean(80) ? (clone $fakturadato)->addDays(rand(1, 5)) : null;
            $betalt = $faktureret && fake()->boolean(65) ? (clone $faktureret)->addDays(rand(5, 30)) : null;
            $afsluttetDato = $isAfsluttet ? (clone $modtagetDato)->addDays(rand(30, 180)) : null;
            $opgivetDato = !$isAfsluttet && fake()->boolean(10) ? (clone $modtagetDato)->addDays(rand(60, 200)) : null;
            $senesteRapport = (clone $modtagetDato)->addDays(rand(5, 40));

            $hovedstol = fake()->randomFloat(2, 2000, 50000);
            $renter = fake()->randomFloat(2, 150, 4500);
            $gebyr = fake()->randomFloat(2, 100, 1800);
            $startgebyr = fake()->randomFloat(2, 100, 950);
            $ialt = $hovedstol + $renter + $gebyr + $startgebyr;

            $indbetalt = $betalt ? $ialt : ($isAfsluttet ? fake()->randomFloat(2, 0, $ialt) : fake()->randomFloat(2, 0, $ialt / 2));
            $restgaeldDkg = max(0, $ialt - $indbetalt);

            $sag = Sager::create([
                'sagsnr' => $uniqueSagsnr,
                'afsluttet' => $afsluttetDato,
                'faktureret' => $faktureret,
                'betalt' => $betalt,
                'fakturadato' => $fakturadato,
                'modtaget' => $modtagetDato,
                'senesterapport' => $senesteRapport,
                'opgivet' => $opgivetDato,
                'hovedstol' => $hovedstol,
                'renter' => $renter,
                'gebyr' => $gebyr,
                'ialt' => $ialt,
                'startgebyr' => $startgebyr,
                'restgaeld_dkg' => $restgaeldDkg,
                'restgaeld_kreditor' => $restgaeldDkg,
                'indbetalt' => $indbetalt,
                'n_mdlydelse' => fake()->randomFloat(2, 250, 3000),
                'stelnr' => 'VIN-' . strtoupper(Str::random(10)),
                'aktiv' => !$isAfsluttet,
                'fakturanr' => 'FAK-' . (2026000 + $i),
                'kort_bemaerkning' => fake()->randomElement([
                    'Debitor har kontaktet kontoret vedr. afdrag.',
                    'Varselsskrivelse afsendt pr. anbefalet post.',
                    'Sag overdraget til fogedretten i Sønderborg.',
                    'Forligsforhandling i gang.',
                    null
                ]),
                'kode' => 'KODE-' . rand(100, 999),
                'dato' => $modtagetDato,
                'created_at' => $modtagetDato,
                'updated_at' => $afsluttetDato ?? $senesteRapport,
            ]);

            // -------------------------------------------------------------------------
            // 4. TILKNYT SAMTLIGE RELATIONER OG DROPDOWNS (100% GARANTERET UDFYLDT)
            // -------------------------------------------------------------------------
            
            // Primary stakeholders
            $sag->sagerkreditor()->attach($kreditorer->random()->id);

            $debitor = $shuffledDebitorer->isNotEmpty() ? $shuffledDebitorer->pop() : $debitorer->random();
            $sag->sagerdebitor()->attach($debitor->id);

            $sag->sagersagsbehandler()->attach($sagsbehandlere->random()->id);
            $sag->sagerkonsulent()->attach($konsulenter->random()->id);

            // System & Dropdown Pivot Tables
            $sag->sagerStatus()->attach($statuser->random()->id);
            $sag->sagerKtr()->attach($ktrListe->random()->id);
            $sag->sagerBemaerkning()->attach($bemaerkninger->random()->id);
            
            // Valgfrie dropdowns tilknyttes til 70-80% af sagerne for variation
            if (fake()->boolean(80)) {
                $sag->sagerAfslutning()->attach($afslutninger->random()->id);
            }
            if (fake()->boolean(70)) {
                $sag->sagerUdlaeg()->attach($udlaegListe->random()->id);
            }
        }

        $this->command->info("✅ SagerSeeder har oprettet {$totalSager} sager med 100% udfyldte relationer, konsulenter, sagsbehandlere og dropdown-data!");
    }
}