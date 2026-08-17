<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
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
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. OPRET ROLLER OG BRUGERE
        $roles = ['Admin', 'Medarbejder', 'Kreditor'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $medarbejdere = [
            ['name' => 'Anna Hansen', 'email' => 'anna@d1k2g3db.com'],
            ['name' => 'Lars Jensen', 'email' => 'lars@d1k2g3db.com'],
        ];

        foreach ($medarbejdere as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('123456')]
            );
            if (! $user->hasRole('Medarbejder')) {
                $user->assignRole('Medarbejder');
            }
        }

        // 2. OPRET KREDITORER OG STAMDATA
        // 2. OPRET KREDITORER OG STAMDATA
        $kreditorDataList = [
            ['navn' => 'Danske Bank Inkasso', 'cvr' => 12345678, 'email' => 'danske@inkasso.dk', 'lotusID' => 'LOTUS-001'],
            ['navn' => 'Jyske Finans A/S', 'cvr' => 87654321, 'email' => 'jyske@finans.dk', 'lotusID' => 'LOTUS-002'],
            ['navn' => 'Nordea Kredit', 'cvr' => 11223344, 'email' => 'nordea@kredit.dk', 'lotusID' => 'LOTUS-003'],
            ['navn' => 'Express Bank', 'cvr' => 44332211, 'email' => 'express@bank.dk', 'lotusID' => 'LOTUS-004'],
            ['navn' => 'Resurs Bank', 'cvr' => 55667788, 'email' => 'resurs@bank.dk', 'lotusID' => 'LOTUS-005'],
        ];

        foreach ($kreditorDataList as $kData) {
            Kreditorer::firstOrCreate(
                ['navn' => $kData['navn']],
                [
                    'cvr' => $kData['cvr'],
                    'adresse' => 'Hovedgaden ' . rand(1, 50),
                    'postnr' => 6400,
                    'email' => $kData['email'],
                    'tlf' => '70' . rand(100000, 999999),
                    'lotusID' => $kData['lotusID'], // <--- Tilføjet her
                ]
            );
        }

        // 3. OPRET SAGSBEHANDLERE OG KLYNGER KREDITORER TIL DEM
        $sagsbehandlereData = [
            [
                'navn' => 'Mette Frederiksen',
                'email' => 'mette@inkasso.dk',
                'tlf' => '30123456',
                'mobil' => '50123456',
                'kreditor_navn' => 'Danske Bank Inkasso',
            ],
            [
                'navn' => 'Lars Løkke',
                'email' => 'loekke@inkasso.dk',
                'tlf' => '30654321',
                'mobil' => '50654321',
                'kreditor_navn' => 'Jyske Finans A/S',
            ],
        ];

        foreach ($sagsbehandlereData as $data) {
            $sagsbehandler = Sagsbehandler::firstOrCreate(
                ['email' => $data['email']],
                [
                    'navn' => $data['navn'],
                    'tlf' => $data['tlf'],
                    'mobil' => $data['mobil'],
                ]
            );

            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            if ($kreditor) {
                $kreditor->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);
                $kreditor->hovedsagsbehandler()->syncWithoutDetaching([$sagsbehandler->id]);
            }
        }

        // 4. OPRET KREDITOR-BRUGERE
        $kreditorUsersData = [
            ['name' => 'Danske Bank Bruger', 'email' => 'kreditor@danskebank.dk', 'kreditor_navn' => 'Danske Bank Inkasso'],
            ['name' => 'Jyske Finans Bruger', 'email' => 'kontakt@jyskefinans.dk', 'kreditor_navn' => 'Jyske Finans A/S'],
        ];

        foreach ($kreditorUsersData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('123456')]
            );
            if (! $user->hasRole('Kreditor')) {
                $user->assignRole('Kreditor');
            }
            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            if ($kreditor) {
                $kreditor->users()->syncWithoutDetaching([$user->id]);
            }
        }

        // 5. OPRET DEBITORER, KONSULENTER OG DROPDOWN-STAMDATA (hvis tomme)
        if (Debitorer::count() === 0) {
            $danskeByer = [
                ['postnr' => 6400, 'by' => 'Sønderborg'],
                ['postnr' => 6200, 'by' => 'Aabenraa'],
                ['postnr' => 6100, 'by' => 'Haderslev'],
                ['postnr' => 6000, 'by' => 'Kolding'],
                ['postnr' => 5000, 'by' => 'Odense C'],
                ['postnr' => 8000, 'by' => 'Aarhus C'],
                ['postnr' => 1050, 'by' => 'København K'],
            ];
            $tilfaeldigeNavne = ['Lars Jensen', 'Anna Hansen', 'Peter Nielsen', 'Mette Jensen', 'Henrik Poulsen'];
            $gader = ['Nørregade', 'Søndergade', 'Hovedgaden', 'Kirkegade', 'Bredgade'];

            for ($d = 1; $d <= 100; $d++) {
                $tilfaeldigLokation = $danskeByer[array_rand($danskeByer)];
                $navn = $tilfaeldigeNavne[array_rand($tilfaeldigeNavne)] . ' ' . rand(1, 99);

                Debitorer::create([
                    'navn' => $navn,
                    'pnr' => rand(100000, 999999) . '-' . rand(1000, 9999),
                    'adresse' => rand(1, 150) . ' ' . $gader[array_rand($gader)],
                    'postnr' => $tilfaeldigLokation['postnr'],
                    'tlf' => '20' . rand(100000, 999999),
                    'email' => strtolower(Str::slug($navn)) . '@gmail.com',
                ]);
            }
        }

        if (Konsulenter::count() === 0) {
            foreach (['Anders Fogh', 'Helle Thorning', 'Poul Nyrup', 'Sven Auken'] as $index => $navn) {
                Konsulenter::create([
                    'navn' => $navn,
                    'email' => Str::slug($navn) . '.' . ($index + 1) . '@konsulent.dk',
                    'tlf' => '40' . rand(1000000, 9999999),
                    'mobil' => '50' . rand(1000000, 9999999),
                ]);
            }
        }

        if (Status::count() === 0) {
            foreach ([
                ['tekst' => 'Modtaget', 'forkortelse' => 'MOD'],
                ['tekst' => 'Varsel sendt', 'forkortelse' => 'VAR'],
                ['tekst' => 'Fogedret berammet', 'forkortelse' => 'FOG'],
                ['tekst' => 'Afdragsvis betaling', 'forkortelse' => 'AFD'],
                ['tekst' => 'Afsluttet - Indbetalt', 'forkortelse' => 'IND'],
                ['tekst' => 'Opgivet', 'forkortelse' => 'OPG'],
            ] as $status) {
                Status::create($status);
            }
        }

        if (ktr::count() === 0) {
            foreach ([
                ['tekst' => 'KTR-100 Konto i berod', 'forkortelse' => 'KTR100'],
                ['tekst' => 'KTR-200 Kontaktet pr. tlf', 'forkortelse' => 'KTR200'],
                ['tekst' => 'KTR-300 Retslig inkasso', 'forkortelse' => 'KTR300'],
                ['tekst' => 'KTR-400 Afdragsordning', 'forkortelse' => 'KTR400'],
            ] as $item) {
                ktr::create($item);
            }
        }

        if (bemaerkning::count() === 0) {
            foreach ([
                ['tekst' => 'Debitor har lovet indbetaling fredag', 'forkortelse' => 'BEM1'],
                ['tekst' => 'Strider mod hovedstol', 'forkortelse' => 'BEM2'],
                ['tekst' => 'Afventer svar fra kreditor', 'forkortelse' => 'BEM3'],
                ['tekst' => 'Udvidet bopælsattest indhentet', 'forkortelse' => 'BEM4'],
            ] as $item) {
                bemaerkning::create($item);
            }
        }

        if (afslutning::count() === 0) {
            foreach ([
                ['tekst' => 'Fuld indfrielse', 'forkortelse' => 'AFSL1'],
                ['tekst' => 'Forlig indgået', 'forkortelse' => 'AFSL2'],
                ['tekst' => 'Insolvenserklæring i fogedret', 'forkortelse' => 'AFSL3'],
                ['tekst' => 'Kreditor trukket tilbage', 'forkortelse' => 'AFSL4'],
            ] as $item) {
                afslutning::create($item);
            }
        }

        if (udlaeg::count() === 0) {
            foreach ([
                ['tekst' => 'Ingen aktiver', 'forkortelse' => 'UDL1'],
                ['tekst' => 'Udlæg i bil', 'forkortelse' => 'UDL2'],
                ['tekst' => 'Udlæg i fast ejendom', 'forkortelse' => 'UDL3'],
                ['tekst' => 'Lønindeholdelse iværksat', 'forkortelse' => 'UDL4'],
            ] as $item) {
                udlaeg::create($item);
            }
        }

        // 6. NULSTIL OG OPRET SAGER OG PIVOT-TABELLER
        $kreditorer = Kreditorer::all();
        $debitorer = Debitorer::all();
        $sagsbehandlere = Sagsbehandler::all();
        $konsulenter = Konsulenter::all();
        $statuser = Status::all();
        $ktrListe = ktr::all();
        $bemaerkninger = bemaerkning::all();
        $afslutninger = afslutning::all();
        $udlaegListe = udlaeg::all();

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

        $shuffledDebitorer = $debitorer->shuffle();
        $totalSager = 50;
        $bemaerkningTekster = [
            'Debitor har kontaktet kontoret vedr. afdrag.',
            'Varselsskrivelse afsendt pr. anbefalet post.',
            'Sag overdraget til fogedretten i Sønderborg.',
            'Forligsforhandling i gang.',
            null
        ];

        for ($i = 1; $i <= $totalSager; $i++) {
            $uniqueSagsnr = 100000 + $i;
            $gdprKategoriValg = ['expired', 'expiring_soon', 'normal', 'normal', 'normal'];
            $gdprCategory = $gdprKategoriValg[array_rand($gdprKategoriValg)];

            if ($gdprCategory === 'expired') {
                $modtagetDato = Carbon::now()->subMonths(rand(66, 84));
                $isAfsluttet = true;
            } elseif ($gdprCategory === 'expiring_soon') {
                $modtagetDato = Carbon::now()->subMonths(rand(58, 59));
                $isAfsluttet = (rand(1, 100) <= 70);
            } else {
                $modtagetDato = Carbon::now()->subDays(rand(30, 1000));
                $isAfsluttet = (rand(1, 100) <= 30);
            }

            $fakturadato = (rand(1, 100) <= 85) ? (clone $modtagetDato)->addDays(rand(1, 14)) : null;
            $faktureret = $fakturadato && (rand(1, 100) <= 80) ? (clone $fakturadato)->addDays(rand(1, 5)) : null;
            $betalt = $faktureret && (rand(1, 100) <= 65) ? (clone $faktureret)->addDays(rand(5, 30)) : null;
            $afsluttetDato = $isAfsluttet ? (clone $modtagetDato)->addDays(rand(30, 180)) : null;
            $opgivetDato = !$isAfsluttet && (rand(1, 100) <= 10) ? (clone $modtagetDato)->addDays(rand(60, 200)) : null;
            $senesteRapport = (clone $modtagetDato)->addDays(rand(5, 40));

            $hovedstol = round(rand(200000, 5000000) / 100, 2);
            $renter = round(rand(15000, 450000) / 100, 2);
            $gebyr = round(rand(10000, 180000) / 100, 2);
            $startgebyr = round(rand(10000, 95000) / 100, 2);
            $ialt = $hovedstol + $renter + $gebyr + $startgebyr;

            $indbetalt = $betalt ? $ialt : ($isAfsluttet ? round(rand(0, (int)($ialt * 100)) / 100, 2) : round(rand(0, (int)(($ialt / 2) * 100)) / 100, 2));
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
                'n_mdlydelse' => round(rand(25000, 300000) / 100, 2),
                'stelnr' => 'VIN-' . strtoupper(Str::random(10)),
                'aktiv' => !$isAfsluttet,
                'fakturanr' => 'FAK-' . (2026000 + $i),
                'kort_bemaerkning' => $bemaerkningTekster[array_rand($bemaerkningTekster)],
                'kode' => 'KODE-' . rand(100, 999),
                'dato' => $modtagetDato,
                'created_at' => $modtagetDato,
                'updated_at' => $afsluttetDato ?? $senesteRapport,
            ]);

            // Tilknyt relationer
            $sag->sagerkreditor()->attach($kreditorer->random()->id);
            $debitor = $shuffledDebitorer->isNotEmpty() ? $shuffledDebitorer->pop() : $debitorer->random();
            $sag->sagerdebitor()->attach($debitor->id);
            $sag->sagersagsbehandler()->attach($sagsbehandlere->random()->id);
            $sag->sagerkonsulent()->attach($konsulenter->random()->id);
            $sag->sagerStatus()->attach($statuser->random()->id);
            $sag->sagerKtr()->attach($ktrListe->random()->id);
            $sag->sagerBemaerkning()->attach($bemaerkninger->random()->id);
            
            if (rand(1, 100) <= 80) {
                $sag->sagerAfslutning()->attach($afslutninger->random()->id);
            }
            if (rand(1, 100) <= 70) {
                $sag->sagerUdlaeg()->attach($udlaegListe->random()->id);
            }
        }
    }
}