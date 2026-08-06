<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SagActivity extends Model
{
    protected $fillable = [
        'sag_id',
        'user_id',
        'last_viewed_at',
        'last_edited_at',
        'heartbeat_at',
        'is_editing',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
        'last_edited_at' => 'datetime',
        'heartbeat_at' => 'datetime',
        'is_editing' => 'boolean',
    ];

    public function sag()
    {
        return $this->belongsTo(Sager::class, 'sag_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}