<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokument extends Model
{
    protected $table = 'dokumenter';    
    protected $fillable = [
        'sag_id',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_date',
    ];

    protected $casts = [
        'uploaded_date' => 'datetime',
    ];

    public function sag()
    {
        return $this->belongsTo(Sager::class);
    }
}