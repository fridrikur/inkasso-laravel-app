<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postnr extends Model
{
    protected $table = 'postnr';
    public $timestamps = false;
    protected $fillable = ['postnr', 'by'];
}
