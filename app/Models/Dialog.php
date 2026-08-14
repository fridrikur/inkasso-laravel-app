<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dialog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sag_id',
        'type', // bogholderi, historik, klientinformation
    ];

    public function sag()
    {
        return $this->belongsTo(Sager::class, 'sag_id');
    }

    public function messages()
    {
        return $this->hasMany(DialogMessage::class);
    }

    public function participants()
    {
        return $this->hasMany(DialogParticipant::class);
    }

    public function unreadForUser($user)
    {
        if (! $user) {
            return 0;
        }

        // 1️⃣ BOGHOLDERI & HISTORIK: Kun for konsulenter (interne brugere)
        if (in_array($this->type, ['bogholderi', 'historik'])) {
            
            // Kreditorer/eksterne ser aldrig ulæste beskeder her
            if ($user->hasRole('Kreditor')) {
                return 0;
            }

            // Konsulenter ser alle ulæste beskeder i tråden, som de IKKE selv har skrevet
            return $this->messages()
                ->whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->count();
        }

        // 2️⃣ KLIENTINFORMATION: Konsulent <-> Sagsbehandler (Kreditor)
        if ($this->type === 'klientinformation') {

            if ($user->hasRole('Kreditor')) {
                // Sagsbehandler hos Kreditor ser ulæste beskeder fra interne konsulenter/admin
                return $this->messages()
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id)
                    ->whereHas('sender.roles', function ($q) {
                        $q->where('name', '!=', 'Kreditor');
                    })
                    ->count();
            }

            // Interne konsulenter ser ulæste beskeder fra Kreditor
            return $this->messages()
                ->whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereHas('sender.roles', function ($q) {
                    $q->where('name', 'Kreditor');
                })
                ->count();
        }

        return 0;
    }
}