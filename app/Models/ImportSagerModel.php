<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportSagerModel extends Model
{
    use HasFactory;

    protected $table = 'import_sager';

    protected $fillable = [
        'import_id',
        'sag_id',
        'kreditor_id',
    ];

    public function import()
    {
        return $this->belongsTo(Imports::class, 'import_id');
    }

    public function sag()
    {
        return $this->belongsTo(Sager::class, 'sag_id');
    }

    public function kreditor()
    {
        return $this->belongsTo(Kreditorer::class, 'kreditor_id');
    }
}
