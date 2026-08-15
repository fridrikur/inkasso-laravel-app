<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class bemaerkning extends Model
{
    protected $table = 'bemaerkning';

    protected $fillable = [
        'tekst',
        'forkortelse',
    ];

    protected $attributes = [
        'forkortelse' => 'N/A',
    ];


    public function sagerbemaerkning(): BelongsToMany
    {
        return $this->belongsToMany(Sager::class, 'sager_bemaerkning', 'bemaerkning_id', 'sag_id');
    }
}