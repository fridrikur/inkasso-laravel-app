<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogMessage extends Model
{
    protected $fillable = [
        'dialog_id',
        'sender_id',
        'tekst',
        'dato',
        'read_at'
    ];

    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    public function dialog()
    {
        return $this->belongsTo(Dialog::class);
    }
}
