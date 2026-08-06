<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KTR extends Model
{
    protected $table = 'ktr';

    protected $fillable = [
        'tekst',
        'forkortelse',
    ];

    public function sagerKTR(): BelongsToMany
    {
        return $this->belongsToMany(Sager::class, 'sager_ktr', 'ktr_id', 'sag_id');
    }
}