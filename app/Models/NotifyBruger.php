<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifyBruger extends Model
{
    protected $table = 'notifybrugere';
    protected $fillable = ['brugerID'];
}