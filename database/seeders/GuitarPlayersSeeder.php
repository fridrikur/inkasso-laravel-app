<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class GuitarPlayersSeeder extends Seeder
{
    public function run()
    {
        $guitarPlayers = [
            'Jimi Hendrix',
            'Eric Clapton',
            'Jimmy Page',
            'Eddie Van Halen',
            'Carlos Santana',
            'B.B. King',
            'Stevie Ray Vaughan',
            'David Gilmour',
            'Slash',
            'Kirk Hammett',
            'Keith Richards',
            'Joe Satriani',
            'Steve Vai',
            'John Mayer',
            'Prince',
        ];

        foreach ($guitarPlayers as $player) {
            User::create([
                'name' => $player,
                'email' => strtolower(str_replace(' ', '.', $player)) . '@example.com',
                'password' => bcrypt('password'), // default password
            ]);
        }
    }
}
