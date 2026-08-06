<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imports extends Model
{
    use HasFactory;

    protected $table = 'imports';

    protected $fillable = [
        'file_name',
        'kreditor_id',
        'total_rows',
        'inserted_rows',
        'failed_rows',
    ];

    // Relationship to import_sager pivot
    public function importSager()
    {
        return $this->hasMany(ImportSager::class, 'import_id');
    }

    // Optional: relationship to Kreditor
    public function kreditor()
    {
        return $this->belongsTo(Kreditorer::class, 'kreditor_id');
    }
}
