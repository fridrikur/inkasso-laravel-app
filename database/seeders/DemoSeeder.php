<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        // 1. Kalder stamdata-seedere inkl. den nye DebitorSeeder
        $this->call([
            DropdownDataSeeder::class,
            KreditorSeeder::class,
            SagsbehandlerSeeder::class,
            LegacyUserSeeder::class,
            DebitorSeeder::class,
        ]);

        // 2. OPRET ROLLER OG BRUGERE
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

        // 3. OPRET SAGSBEHANDLERE OG KLYNGER KREDITORER TIL DEM
        $sagsbehandlereData = [
            ['navn' => 'Mette Frederiksen', 'email' => 'mette@inkasso.dk', 'tlf' => '30123456', 'mobil' => '50123456', 'kreditor_navn' => 'Santander Consumer Bank'],
            ['navn' => 'Lars Løkke', 'email' => 'loekke@inkasso.dk', 'tlf' => '30654321', 'mobil' => '50654321', 'kreditor_navn' => 'PartnerLeasing A/S'],
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

        // 4. NULSTIL OG OPRET SAGER OG PIVOT-TABELLER
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
            
            if ($konsulenter->isNotEmpty()) {
                $sag->sagerkonsulent()->attach($konsulenter->random()->id);
            }
            
            $sag->sagerStatus()->attach($statuser->random()->id);
            
            if ($ktrListe->isNotEmpty()) $sag->sagerKtr()->attach($ktrListe->random()->id);
            if ($bemaerkninger->isNotEmpty()) $sag->sagerBemaerkning()->attach($bemaerkninger->random()->id);
            if ($isAfsluttet && $afslutninger->isNotEmpty()) $sag->sagerAfslutning()->attach($afslutninger->random()->id);
            if ($udlaegListe->isNotEmpty()) $sag->sagerUdlaeg()->attach($udlaegListe->random()->id);
        }
    }
}