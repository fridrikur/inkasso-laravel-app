<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DialogMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'dialog_id',
        'sender_id',
        'tekst', // 🟢 VIGTIGT: Skal hedde 'tekst'
        'dato', // 🟢 Tilføjet
        'read_at',
    ];

    public function dialog()
    {
        return $this->belongsTo(Dialog::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}