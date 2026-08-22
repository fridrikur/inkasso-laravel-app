<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportTemplate extends Model
{
    protected $table = 'import_templates';

    protected $fillable = [
        'user_id',
        'name',
        'import_type',
        'mapping',
    ];

    protected $casts = [
        'mapping' => 'array', // Sikrer at JSON-feltet automatisk bliver konverteret til et array
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}