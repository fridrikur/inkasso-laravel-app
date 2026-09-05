<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // 🟢 Husk denne import

class DialogMessage extends Model
{
    use HasFactory, SoftDeletes; // 🟢 Aktiver SoftDeletes trait her

    protected $table = 'dialog_messages';

    protected $fillable = [
        'dialog_id',
        'sender_id',
        'tekst',
        'dato',
        'read_at',
    ];

    protected $casts = [
        'dato' => 'datetime',
        'read_at' => 'datetime',
        'deleted_at' => 'datetime', // Sørg for at datoen for sletning castes korrekt
    ];

    protected static function booted()
    {
        parent::booted();

        // 🟢 Sørg for automatisk at tilføje/sikre deltageren i dialog_participants, når en besked oprettes
        static::created(function ($message) {
            if ($message->dialog_id && $message->sender_id) {
                $user = User::find($message->sender_id);
                $userType = 'sagsbehandler'; // Standard fallback

                if ($user) {
                    if ($user->hasRole('Kreditor')) {
                        $userType = 'kreditor';
                    } elseif ($user->hasRole('Konsulent')) {
                        $userType = 'konsulent';
                    }
                }

                // Opret deltageren, hvis vedkommende ikke allerede er tilknyttet tråden
                DialogParticipant::firstOrCreate([
                    'dialog_id' => $message->dialog_id,
                    'user_id'   => $message->sender_id,
                ], [
                    'user_type' => $userType,
                ]);
            }
        });
    }

    public function dialog()
    {
        return $this->belongsTo(Dialog::class, 'dialog_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}