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
use App\Models\afslutning;
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
                    'lotusID' => $kData['lotusID'],
                ]
            );
        }

        // 3. OPRET SAGSBEHANDLERE OG KLYNGER KREDITORER TIL DEM
        $sagsbehandlereData = [
            ['navn' => 'Mette Frederiksen', 'email' => 'mette@inkasso.dk', 'tlf' => '30123456', 'mobil' => '50123456', 'kreditor_navn' => 'Danske Bank Inkasso'],
            ['navn' => 'Lars Løkke', 'email' => 'loekke@inkasso.dk', 'tlf' => '30654321', 'mobil' => '50654321', 'kreditor_navn' => 'Jyske Finans A/S'],
        ];

        foreach ($sagsbehandlereData as $data) {
            $sagsbehandler = Sagsbehandler::firstOrCreate(
                ['email' => $data['email']],
                ['navn' => $data['navn'], 'tlf' => $data['tlf'], 'mobil' => $data['mobil']]
            );

            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            if ($kreditor) {
                $kreditor->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);
                $kreditor->hovedsagsbehandler()->syncWithoutDetaching([$sagsbehandler->id]);
            }
        }

        // 4. OPRET DEBITORER, KONSULENTER OG DROPDOWN-STAMDATA (hvis tomme)
        if (Debitorer::count() === 0) {
            $danskeByer = [
                ['postnr' => 6400, 'by' => 'Sønderborg'],
                ['postnr' => 5000, 'by' => 'Odense C'],
                ['postnr' => 8000, 'by' => 'Aarhus C'],
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
                    'email' => strtolower(Str::slug($navn)) . rand(1, 999) . '@gmail.com',
                ]);
            }
        }

        if (Konsulenter::count() === 0) {
            $konsulentNavne = ['Anders Fogh', 'Helle Thorning', 'Poul Nyrup', 'Sven Auken'];
            foreach ($konsulentNavne as $index => $navn) {
                Konsulenter::create([
                    'navn' => $navn,
                    'email' => Str::slug($navn) . '.' . ($index + 1) . '@konsulent.dk',
                    'tlf' => '40' . rand(1000000, 9999999),
                    'mobil' => '50' . rand(1000000 + $index, 9999999), // Sikrer unikt mobilnummer
                ]);
            }
        }

        if (Status::count() === 0) {
            foreach ([
                ['tekst' => 'Modtaget', 'forkortelse' => 'MOD'],
                ['tekst' => 'Varsel sendt', 'forkortelse' => 'VAR'],
                ['tekst' => 'Afsluttet - Indbetalt', 'forkortelse' => 'IND'],
            ] as $status) {
                Status::create($status);
            }
        }

        if (KTR::count() === 0) {
            KTR::create(['tekst' => 'KTR-100 Konto i berod', 'forkortelse' => 'KTR100']);
        }

        if (bemaerkning::count() === 0) {
            bemaerkning::create(['tekst' => 'Debitor har lovet indbetaling fredag', 'forkortelse' => 'BEM1']);
        }

        if (afslutning::count() === 0) {
            afslutning::create(['tekst' => 'Fuld indfrielse', 'forkortelse' => 'AFSL1']);
        }

        if (udlaeg::count() === 0) {
            udlaeg::create(['tekst' => 'Ingen aktiver', 'forkortelse' => 'UDL1']);
        }

        // 5. NULSTIL OG OPRET SAGER OG PIVOT-TABELLER
        $kreditorer = Kreditorer::all();
        $debitorer = Debitorer::all();
        $sagsbehandlere = Sagsbehandler::all();
        $konsulenter = Konsulenter::all();
        $statuser = Status::all();
        $ktrListe = KTR::all();
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

        for ($i = 1; $i <= $totalSager; $i++) {
            $uniqueSagsnr = 100000 + $i;
            
            // Fordel datoer så de første 35 sager ligger inden for de seneste 45 dage (så grafen virker!)
            if ($i <= 35) {
                $modtagetDato = Carbon::now()->subDays(rand(1, 45));
                $isAfsluttet = (rand(1, 100) <= 20);
            } else {
                $modtagetDato = Carbon::now()->subDays(rand(60, 200));
                $isAfsluttet = (rand(1, 100) <= 50);
            }

            $ialt = round(rand(500000, 5000000) / 100, 2);
            $indbetalt = $isAfsluttet ? $ialt : round(rand(0, (int)($ialt / 2 * 100)) / 100, 2);
            $restgaeldDkg = max(0, $ialt - $indbetalt);

            $sag = Sager::create([
                'sagsnr' => $uniqueSagsnr,
                'modtaget' => $modtagetDato,
                'dato' => $modtagetDato,
                'ialt' => $ialt,
                'indbetalt' => $indbetalt,
                'restgaeld_dkg' => $restgaeldDkg,
                'restgaeld_kreditor' => $restgaeldDkg,
                'aktiv' => !$isAfsluttet,
                'fakturanr' => 'FAK-' . (2026000 + $i),
                'created_at' => $modtagetDato,
            ]);

            // Tilknyt relationer
            $sag->sagerkreditor()->attach($kreditorer->random()->id);
            $debitor = $shuffledDebitorer->isNotEmpty() ? $shuffledDebitorer->pop() : $debitorer->random();
            $sag->sagerdebitor()->attach($debitor->id);
            $sag->sagersagsbehandler()->attach($sagsbehandlere->random()->id);
            $sag->sagerkonsulent()->attach($konsulenter->random()->id);
            $sag->sagerStatus()->attach($statuser->random()->id);
            
            if ($ktrListe->isNotEmpty()) $sag->sagerKtr()->attach($ktrListe->random()->id);
            if ($bemaerkninger->isNotEmpty()) $sag->sagerBemaerkning()->attach($bemaerkninger->random()->id);
            if ($isAfsluttet && $afslutninger->isNotEmpty()) $sag->sagerAfslutning()->attach($afslutninger->random()->id);
            if ($udlaegListe->isNotEmpty()) $sag->sagerUdlaeg()->attach($udlaegListe->random()->id);
        }
    }
}