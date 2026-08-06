<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;
    protected $table = 'status';
    
    protected $fillable = ['tekst', 'forkortelse'];

    public function sagerStatus()
    {
        return $this->belongsToMany(Sager::class, 'sager_status', 'status_id','sag_id');
    }

}
