<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 🟢 1. Importér trait

class DialogMessage extends Model
{
    use HasFactory, SoftDeletes; // 🟢 2. Tilføj SoftDeletes her

    protected $fillable = [
        'dialog_id',
        'sender_id',
        'tekst', 
        'dato', 
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