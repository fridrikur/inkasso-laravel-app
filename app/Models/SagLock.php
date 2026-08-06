<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Sager;

class SagLock extends Model
{
    protected $fillable = [
        'sag_id',
        'user_id',
        'currentsag_locked',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sag()
    {
        return $this->belongsTo(Sager::class);
    }
}