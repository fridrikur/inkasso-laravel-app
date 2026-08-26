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

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Opret roller og basis admin-bruger
        $roles = ['Admin', 'Medarbejder', 'Kreditor'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@demo-inkasso.dk'],
            ['name' => 'Demo Administrator', 'password' => Hash::make('123456')]
        );
        if (! $adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        // 2. OPRET RENE DEMO-KREDITORER med lotusID
        $demoKreditorerData = [
            ['navn' => 'Nordic Finans A/S', 'lotusID' => 9991],
            ['navn' => 'Ekspres Lån & Kredit', 'lotusID' => 9992],
            ['navn' => 'Sjællandsk Inkasso', 'lotusID' => 9993],
            ['navn' => 'Jysk Fordringshåndtering', 'lotusID' => 9994],
        ];
        
        $kreditorer = collect();
        foreach ($demoKreditorerData as $data) {
            $kreditorer->push(
                Kreditorer::firstOrCreate(
                    ['navn' => $data['navn']],
                    ['lotusID' => $data['lotusID']]
                )
            );
        }

        // 3. OPRET RENE DEMO-SAGSBEHANDLERE
        $demoSagsbehandlereData = [
            ['navn' => 'Hans Demo-Hansen', 'email' => 'hans@demo-inkasso.dk', 'tlf' => '11223344', 'mobil' => '40112233'],
            ['navn' => 'Lone Test-Nielsen', 'email' => 'lone@demo-inkasso.dk', 'tlf' => '55667788', 'mobil' => '40556677'],
        ];

        $sagsbehandlere = collect();
        foreach ($demoSagsbehandlereData as $data) {
            $sbs = Sagsbehandler::firstOrCreate(
                ['email' => $data['email']],
                ['navn' => $data['navn'], 'tlf' => $data['tlf'], 'mobil' => $data['mobil']]
            );
            $sagsbehandlere->push($sbs);
        }

        foreach ($kreditorer as $kreditor) {
            $kreditor->sagsbehandlere()->syncWithoutDetaching($sagsbehandlere->pluck('id')->toArray());
        }

        // 4. OPRET RENE DEMO-DEBITORER
        $demoDebitorerNavne = [
            'Peter Testmand, Testvej 1, 6000 Kolding',
            'Mette Prøvepers, Eksempelvej 4, 6400 Sønderborg',
            'Carsten Eksempel, Fabriksvej 12, 6700 Esbjerg',
            'Hanne Prøversen, Bygade 9, 6200 Aabenraa'
        ];

        $debitorer = collect();
        foreach ($demoDebitorerNavne as $navn) {
            $debitorer->push(Debitorer::firstOrCreate(['navn' => $navn]));
        }

        // 5. FLERE VARIANTER AF DROPDOWNS / LOOKUPS
        $konsulent = Konsulenter::firstOrCreate(['navn' => 'Demo Konsulent A/S']);

        // Flere statuser
        $statuserData = [
            ['tekst' => 'Under behandling', 'forkortelse' => 'UB'],
            ['tekst' => 'Afventer debitor', 'forkortelse' => 'AD'],
            ['tekst' => 'Betalingsaftale indgået', 'forkortelse' => 'BA'],
            ['tekst' => 'Sendt til inkasso', 'forkortelse' => 'SI'],
        ];
        $statuser = collect();
        foreach ($statuserData as $s) {
            $statuser->push(Status::firstOrCreate(['tekst' => $s['tekst']], ['forkortelse' => $s['forkortelse']]));
        }

        // Flere KTR typer
        $ktrData = [
            ['tekst' => 'Standard KTR', 'forkortelse' => 'SKTR'],
            ['tekst' => 'Haste sak', 'forkortelse' => 'HKTR'],
            ['tekst' => 'Fจับelg krave', 'forkortelse' => 'FKTR'],
        ];
        $ktrListe = collect();
        foreach ($ktrData as $k) {
            $ktrListe->push(KTR::firstOrCreate(['tekst' => $k['tekst']], ['forkortelse' => $k['forkortelse']]));
        }

        // Flere afslutninger
        $afslutningData = [
            ['tekst' => 'Fuldt indbetalt', 'forkortelse' => 'FI'],
            ['tekst' => 'Afskrevet / Tabt', 'forkortelse' => 'AT'],
            ['tekst' => 'Overført til ordning', 'forkortelse' => 'OO'],
        ];
        $afslutninger = collect();
        foreach ($afslutningData as $a) {
            $afslutninger->push(afslutning::firstOrCreate(['tekst' => $a['tekst']], ['forkortelse' => $a['forkortelse']]));
        }

        // Flere udlæg
        $udlaegData = [
            ['tekst' => 'Retsafgift', 'forkortelse' => 'RA'],
            ['tekst' => 'Advokatomkostninger', 'forkortelse' => 'AO'],
            ['tekst' => 'Gebyrer og renter', 'forkortelse' => 'GR'],
        ];
        $udlaegListe = collect();
        foreach ($udlaegData as $u) {
            $udlaegListe->push(udlaeg::firstOrCreate(['tekst' => $u['tekst']], ['forkortelse' => $u['forkortelse']]));
        }

        // Flere bemærkninger
        $bemaerkningData = [
            ['tekst' => 'Debitor har lovet at betale i næste måned.', 'forkortelse' => 'B1'],
            ['tekst' => 'Rykkerbrev 2 afsendt uden respons.', 'forkortelse' => 'B2'],
            ['tekst' => 'Telefonisk kontakt oprettet med debitor.', 'forkortelse' => 'B3'],
        ];
        $bemaerkninger = collect();
        foreach ($bemaerkningData as $b) {
            $bemaerkninger->push(bemaerkning::firstOrCreate(['tekst' => $b['tekst']], ['forkortelse' => $b['forkortelse']]));
        }

        // 6. NULSTIL OG GENERER KUN DEMO-SAGER
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

        $totalSager = 20;

        for ($i = 1; $i <= $totalSager; $i++) {
            $uniqueSagsnr = 900000 + $i;
            $modtagetDato = Carbon::now()->subDays(rand(1, 30));
            $isAfsluttet = (rand(1, 100) <= 25);

            $ialt = round(rand(200000, 2000000) / 100, 2);
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
                'fakturanr' => 'DEMO-FAK-' . (2026000 + $i),
                'created_at' => $modtagetDato,
            ]);

            // Tilknyt tilfældige demo-relationer for at skabe variation
            $sag->sagerkreditor()->attach($kreditorer->random()->id);
            $sag->sagerdebitor()->attach($debitorer->random()->id);
            $sag->sagersagsbehandler()->attach($sagsbehandlere->random()->id);
            $sag->sagerkonsulent()->attach($konsulent->id);
            $sag->sagerStatus()->attach($statuser->random()->id);
            $sag->sagerKtr()->attach($ktrListe->random()->id);
            $sag->sagerBemaerkning()->attach($bemaerkninger->random()->id);
            
            if ($isAfsluttet) {
                $sag->sagerAfslutning()->attach($afslutninger->random()->id);
            }
            
            $sag->sagerUdlaeg()->attach($udlaegListe->random()->id);
        }
    }
}