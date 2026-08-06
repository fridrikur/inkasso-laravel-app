<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DialogParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'dialog_id',
        'user_type',  // konsulent / sagsbehandler / kreditor
        'user_id',
    ];

    public function dialog()
    {
        return $this->belongsTo(Dialog::class);
    }
}