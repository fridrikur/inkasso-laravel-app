<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function is2FaEnabled(): bool
    {
        return static::get('enable_2fa', '0') === '1';
    }

    public static function get2FaProvider(): string
    {
        return static::get('two_factor_provider', 'totp'); // 'totp' eller 'twilio'
    }
}