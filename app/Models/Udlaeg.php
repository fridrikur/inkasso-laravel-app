<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class udlaeg extends Model
{
    protected $table = 'udlaeg';

    protected $fillable = [
        'tekst',
        'forkortelse',
    ];

    protected $attributes = [
        'forkortelse' => 'N/A',
    ];


    public function sagerudlaeg(): BelongsToMany
    {
        return $this->belongsToMany(Sager::class, 'sager_udlaeg', 'udlaeg_id', 'sag_id');
    }
}