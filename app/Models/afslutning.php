<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class afslutning extends Model
{
    protected $table = 'afslutning';

    protected $fillable = [
        'tekst',
        'forkortelse',
    ];

    public function sagerafslutning(): BelongsToM
    any
    {
        return $this->belongsToMany(Sager::class, 'sager_afslutning', 'afslutning_id', 'sag_id');
    }
}